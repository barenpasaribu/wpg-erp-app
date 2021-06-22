<?php

defined('BASEPATH') or exit('No direct  script access allowed');

class Kodeblok extends CI_Controller {


	private $filename = "import_data";

	public function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Constant_model');
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->model('Kodeblok_model');
		$this->load->library('pagination');

	}

	public function index()
	{
		$this->load->view('technoilahi', 'refresh');
	}

	 public function data_tampil()
    {
    	$data['field'] = $this->db->query("SELECT distinct FIELDNO,field.* FROM field")->result_array();
        $this->load->view('data_kode', $data);
    }

     public function cetak_barcode()
    {
        $pcode = $this->input->get('pcode');

        $ckPcodeData = $this->Constant_model->getDataOneColumn('field', 'FIELDNO', $pcode);

        if (count($ckPcodeData) == 1) {
            $data['pcode'] = $pcode;
            $this->load->view('print_blok', $data);
        } else {
            
            redirect(base_url().'barang/data_tampil');
        }
    }

	

	

	
}