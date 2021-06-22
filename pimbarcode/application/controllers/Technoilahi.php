<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Technoilahi extends CI_Controller
{
    /**
    |//////////////////////////////////////////// TECHNOILAHI CORPORATION \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
    | Note : 
    |   Nama Produk     => Sisir (Sistem Management Kasir) 
    |   Kode Produk     => 0045
    |   Licensi         => Dapat Di Jual Belikan Kembali Dengan Cara Online, Artinya Tidak Bisa Di Iklankan Via Online
    |   Harga Produk    => 350.000.00 Rupiah
    |
    |   Apabila Anda ingin Memperjual Belika Via Offline, Alangkah baiknya Untuk menyeimbangkan harga dengan harga
    |   yang kami jual di online, yakni sebesar 250-350 Ribu rupiah. Hal itu agar tidak menjadi pembentrokan harga
    |   dan anda tidak harus menanggung malu apabila seketika orang tahu harga sesungguhnya dari aplikasi ini.
    |
    |   Anda Bisa saja Menjual Aplikasi ini Secara Online, Dengan catatan menjadi affliasi kami, setiap penjualan
    |   anda akan mendapatkan keuntungan 50% dari penjualan anda.
    |
    |___________________________________________________________________________________________________________________
     */



    
    //////////////////////////////////// Controller Kepala | Memanggil Function Model dll \\\\\\\\\\\\\\\\\\\\\\\\\\\\


    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_model');
        $this->load->library('form_validation');
        $this->load->library('pagination');
        $this->load->helper('form');
        $this->load->helper('url');
    }



    ///////////////////////////////////////////////// Controller Bagian Dashboard \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

    public function index()
    {
        
        $this->load->view('dashboard');
    }
}