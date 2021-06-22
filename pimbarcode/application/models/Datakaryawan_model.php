<?php

Class Datakaryawan_model extends CI_Model 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get()
	{
		return $this->db->get('employee');
	}

	 public function upload_file($filename){
        $this->load->library('upload'); 
        $config['upload_path'] = './csv/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '2048';
        $config['overwrite'] = true;
        $config['file_name'] = $filename;
    
        $this->upload->initialize($config); // Load konfigurasi uploadnya
        if($this->upload->do_upload('file')){ // Lakukan upload dan Cek jika proses upload berhasil
            // Jika berhasil :
            $return = array('result' => 'success', 'file' => $this->upload->data(), 'error' => '');
            return $return;
        }else{
            // Jika gagal :
            $return = array('result' => 'failed', 'file' => '', 'error' => $this->upload->display_errors());
            return $return;
        }
    }

     public function insert_multiple($data){
        $this->db->insert_batch('employee', $data);
    }
    
	public function tambah($data)
	{
		$this->db->insert('employee', $data);
	}
}