<?php

defined('BASEPATH') or exit('No direct  script access allowed');

class Kodevhc extends CI_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Constant_model');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Kodevhc_model');
		$this->load->library('pagination');

	}

	public function index()
	{
		$this->load->view('technoilahi', 'refresh');
	}

	 public function data_tampil()
    {
    	$data['kendaraan'] = $this->Kodevhc_model->get()->result_array();
        $this->load->view('data_kodevhc', $data);
    }

     public function cetak_barcode()
    {
        $pcode = $this->input->get('pcode');

        $ckPcodeData = $this->Constant_model->getDataOneColumn('kendaraan', 'KENDNO', $pcode);

        if (count($ckPcodeData) == 1) {
            $data['pcode'] = $pcode;
            $this->load->view('print_kendaraan', $data);
        } else {
            
            redirect(base_url().'kodevhc/data_tampil');
        }
    }

	
}