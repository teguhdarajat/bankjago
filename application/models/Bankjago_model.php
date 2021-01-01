<?php

class Bankjago_model extends CI_Model{
    
    public function get_data_nasabah($username = null){
        if ($username  === null){            
            return $this->db->get('nasabah')->result_array();
        }
        else{            
            return $this->db->get_where('nasabah', ['username' => $username])->result_array();
        } 
    }

    public function data_rekening($rekening){        
        return $this->db->get_where('nasabah', ['no_rekening' => $rekening])->result_array();
    }

    public function login_check($username, $password){
        return $this->db->get_where('nasabah', ['username' => $username, 'password' => $password])->result_array();
    }

    public function register($data){
        $hasil =  $this->db->insert('nasabah',$data);
        return $hasil?$this->db->insert_id():false;
    }

    public function update_saldo($uang, $rekening){
        $this->db->where('no_rekening', $rekening);
        $this->db->update('nasabah', $uang);
        $this->db->trans_complete();    
        if ($this->db->affected_rows() == '1') {
            return true;
        } 
        else {
            return false;
        }
    }

    public function insert_histori($data) {
        $this->db->set('timestamp', 'NOW()', FALSE);
        $this->db->insert('histori', $data);
        // return $hasil?$this->db->insert_id():false;
    }

    public function get_histori($username) {
        $this->db->select('*');
        $this->db->from('histori');
        $this->db->where('nasabah', $username);        
        $this->db->order_by('timestamp', 'desc');
        $this->db->limit(5);
        return $this->db->get()->result_array();
    }
}


?>