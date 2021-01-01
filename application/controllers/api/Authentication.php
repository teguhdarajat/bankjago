<?php 

use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Authentication extends REST_Controller 
{
    public function __construct() {
        parent::__construct();
        $this->load->model('Bankjago_model', 'bankjago');
    }

    
}

?>