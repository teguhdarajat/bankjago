<?php 

use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Transaction extends REST_Controller 
{
    public function __construct() {
        parent::__construct();
        $this->load->model('Bankjago_model', 'bankjago');
    }

    public function transfer_post(){        
        $penyetor = $this->post('penyetor');
        $penerima = $this->post('penerima');
        $uang_transfer = $this->post('uang');
        $kategori = $this->post('kategori');

        $data_penyetor = $this->bankjago->get_data_nasabah($penyetor);
        $data_penerima = $this->bankjago->data_rekening($penerima);
        $uang_penyetor = 0;
        $uang_penerima = 0;
        $rekening_penerima;
        $rekening_penyetor;

        foreach ($data_penerima as $data){
            $uang_penerima = intval($data['saldo']);
            $nama_penerima = $data['nama'];        
            $rekening_penerima = $data['no_rekening'];                     
        }

        foreach ($data_penyetor as $data){
            $uang_penyetor = intval($data['saldo']);
            $rekening_penyetor = $data['no_rekening'];
            $nama_penyetor = $data['nama'];   
            $username = $data['username'];                     
        }        
        
        if ($data_penerima && $data_penyetor) { 
            $uang_penerima += $uang_transfer;
            $uang_penyetor -= $uang_transfer;

            $update_penerima['saldo'] = $uang_penerima;
            $update_penyetor['saldo'] = $uang_penyetor;

            $transaksi_penerima = $this->bankjago->update_saldo($update_penerima, $rekening_penerima);
            $transaksi_penyetor = $this->bankjago->update_saldo($update_penyetor, $rekening_penyetor);

            if($transaksi_penerima && $transaksi_penerima){
                $data_transaksi['penyetor'] = $nama_penyetor;
                $data_transaksi['penerima'] = $nama_penerima;
                $data_transaksi['jumlah_transfer'] = $uang_transfer;
                $data_transaksi['message'] = 'Transaksi berhasil';

                $data_histori['nasabah'] = $username;
                $data_histori['jumlah_transaksi'] = $uang_transfer;
                $data_histori['kategori'] = $kategori;
                $data_histori['penerima'] = $nama_penerima;                
                $histori = $this->bankjago->insert_histori($data_histori);

                $this->response([
                    'status' => true,
                    'data' => [$data_transaksi]
                ], REST_Controller::HTTP_OK);
                
            }
            else {
                $data_transaksi['penyetor'] = $nama_penyetor;
                $data_transaksi['penerima'] = $nama_penerima;
                $data_transaksi['jumlah_transfer'] = $uang_transfer;
                $data_transaksi['message'] = 'Transaksi Gagal';
                $this->response([
                    'status' => false,
                    'data' => [$data_transaksi]
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
        else if(!$data_penerima && $data_penyetor){
            $this->response([
                'status' => false,
                'message' => 'nomor rekening '.$penerima.' tidak terdaftar'
            ], REST_Controller::HTTP_NOT_FOUND);        
        }

    }
    
    public function histori_get() {
        $nasabah = $this->get('nasabah');
        $data = $this->bankjago->get_histori($nasabah);

        if($data) {
            $this->response([
                'status' => true,
                'data' => $data
            ], REST_Controller::HTTP_OK);
        }
    }
}

?>
