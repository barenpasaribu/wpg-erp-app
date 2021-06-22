<?php

defined('BASEPATH') or exit('No direct  script access allowed');

class Job extends CI_Controller {


    private $filename = "import_data";

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Constant_model');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('job_model');
        $this->load->library('pagination');

    }

    public function index()
    {
        $this->load->view('technoilahi', 'refresh');
    }

     public function data_tampil()
    {
        $data['job'] = $this->job_model->get()->result_array();
        $this->load->view('data_job', $data);
    }

     public function cetak_barcode()
    {
        $pcode = $this->input->get('pcode');

        $ckPcodeData = $this->Constant_model->getDataOneColumn('job', 'JOBCODE', $pcode);

        if (count($ckPcodeData) == 1) {
            $data['pcode'] = $pcode;
            $this->load->view('print_job', $data);
        } else {
            
            redirect(base_url().'barang/data_tampil');
        }
    }

    public function tambah_data()
    {
        $this->load->view('tambah_job');
    }

    public function import_data()
    {
          $data = array(); // Buat variabel $data sebagai array
        
        if(isset($_POST['lihat'])){ // Jika user menekan tombol Preview pada form
            // lakukan upload file dengan memanggil function upload yang ada di SiswaModel.php
            $upload = $this->job_model->upload_file($this->filename);
            
            if($upload['result'] == "success"){ // Jika proses upload sukses
                // Load plugin PHPExcel nya
                include APPPATH.'third_party/PHPExcel/PHPExcel.php';
                
                $csvreader = PHPExcel_IOFactory::createReader('CSV');
                $loadcsv = $csvreader->load('csv/'.$this->filename.'.csv'); // Load file yang tadi diupload ke folder csv
                $sheet = $loadcsv->getActiveSheet()->getRowIterator();
                
                $data['sheet'] = $sheet; 
            }else{ // Jika proses upload gagal
                $data['upload_error'] = $upload['error']; // Ambil pesan error uploadnya untuk dikirim ke file form dan ditampilkan
            }
        }

        $this->load->view('import_job', $data);
    }

    public function import_kode()
    {
       // Load plugin PHPExcel nya
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        
        $csvreader = PHPExcel_IOFactory::createReader('CSV');
        $loadcsv = $csvreader->load('csv/'.$this->filename.'.csv'); // Load file yang tadi diupload ke folder csv
        $sheet = $loadcsv->getActiveSheet()->getRowIterator();
        
        // Buat sebuah variabel array untuk menampung array data yg akan kita insert ke database
        $data = [];
        
        $numrow = 1;
        foreach($sheet as $row){
            // Cek $numrow apakah lebih dari 1
            // Artinya karena baris pertama adalah nama-nama kolom
            // Jadi dilewat saja, tidak usah diimport
            if($numrow > 1){
                // START -->
                // Skrip untuk mengambil value nya
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
                
                $get = array(); // Valuenya akan di simpan kedalam array,dimulai dari index ke 0
                foreach ($cellIterator as $cell) {
                    array_push($get, $cell->getValue()); // Menambahkan value ke variabel array $get
                }
                // <-- END
                
            $JOBCODE = $get[0];
                
                // Kita push (add) array data ke variabel data
                array_push($data, [
                    'JOBCODE' =>$JOBCODE,
                ]);
            }
            
            $numrow++; // Tambah 1 setiap kali looping
        }

        // Panggil fungsi insert_multiple yg telah kita buat sebelumnya di model
        $this->job_model->insert_multiple($data);
        
        redirect("job/data_tampil"); // Redirect ke halaman awal (ke controller siswa fungsi index)
    }

    public function edit_job()
    {

    }

    public function input_data()
    {
        $data['JOBCODE'] = $this->input->post('JOBCODE');
        $hasil = $this->job_model->tambah($data);
            if ($hasil == 0) {
            
            redirect('job/data_tampil');
        } 
    }


    public function ubah_job()
    {

    }
}