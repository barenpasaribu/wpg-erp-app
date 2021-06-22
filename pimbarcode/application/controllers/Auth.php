<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
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


    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->model('Auth_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('url');
    }

    public function index()
    {
            $this->load->view('login');
    }
     public function daftar()
    {
            $this->load->view('daftar_user');
    }
function saveuser(){
        if($_POST){
            $nik = $_POST['nik'];
            $sn = $_POST['sn'];
            $upassword = $_POST['upassword'];
            $password = $_POST['password'];
            $pass = md5($password);
            if($upassword  == $password){


            $hasilkar = $this->Auth_model->GetKaryawan(" where EMPCODE ='$nik'");
            $hasil = $this->Auth_model->GetUser(" where USERID ='$nik'");
                if ($hasilkar->num_rows() == 0) {
                    $url=isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : ''; 
                echo "<script>alert('NIK tidak ada di database !')</script>";
                redirect($url,'refresh');
                }else{

              
            if ($hasil->num_rows() > 0) {
           $url=isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : ''; 
                echo "<script>alert('NIK Sudah Terdaftar !')</script>";
                redirect($url,'refresh');
            }else{
                $hsl = $this->Auth_model->GetDevice(" where DEVICE_SN ='$sn'");
                     if ($hsl->num_rows() > 0) {

                echo "<script>alert('Serial number sudah ada di database !')</script>";
               redirect($url,'refresh');
            }else{
                $tampung = $this->Auth_model->GetKaryawan(" where EMPCODE ='$nik'")->result_array();
                $nik = $tampung[0]['EMPCODE'];
                $ocid = $tampung[0]['UNITID'];
                $data = array(
                    'USERID' => $nik,
                    'USERTYPE' => 'KRN',
                    'PASSWORD' => $pass,
                    'OC_ID' => $ocid,
                    'LASTUSER' => 'admin',
                    );
                $result = $this->Auth_model->Simpan('user', $data);
                 $dt = array(
                    'DEVICE_OC' => $ocid,
                    'DEVICE_SN' => $sn,
                    'DEVICE_ISREPLACE' => '0',
                    'DEVICE_DESC' => '',
                    'LASTUSER' => 'admin', 
                    );
                $rsult = $this->Auth_model->Simpan('device', $dt);
                
                if($result == 1){
                     $url=isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : ''; 
                echo "<script>alert('Berhasil Silahkan Login Di Android App !')</script>";
                redirect($url,'refresh');
                }else{
                     $url=isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : ''; 
                echo "<script>alert('Gagal Daftar !')</script>";
                redirect($url,'refresh');
                }
            }
            }
              }
          }else{
                $url=isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : ''; 
                echo "<script>alert('Masukan pasword konfirmasi tidak sama, mohon ulangi !')</script>";
                redirect($url,'refresh');
          }
        }else{
            echo('GAGAL!');
        }
    }
    public function login()
    {
        if (isset($_POST['sp_login'])) {
            $data = array(
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
            );

            $em = $this->input->post('email');
            $ps = $this->input->post('password');

            if (empty($em)) {
                $this->set_flashdata('alert_msg', array('failure', 'Login', 'Mohon Masukkan Email Anda!'));
                redirect(base_url());
            } elseif (empty($ps)) {
                $this->set_flashdata('alert_msg', array('failure', 'Login', 'Mohon Masukkan Email Anda!'));
                redirect(base_url());
            } else {
                $result = $this->Auth_model->verifyLogIn($data);

                if ($result['valid']) {
                    $user_id = $result['user_id'];
                    $user_email = $result['user_email'];
                    $role_id = $result['role_id'];
                    $out_id = $result['outlet_id'];

                    $userdata = array(
                        
                        'user_id' => $user_id,
                        'user_email' => $user_email,
                        'user_role' => $role_id,
                        'user_outlet' => $out_id,
                    );

                    $this->set_userdata($userdata);

                    redirect(base_url().'technoilahi', 'refresh');
                } else {
                    $this->set_flashdata('alert_msg', array('failure', 'Login', 'Maaf Email Atau Password Anda Tidak Terdaftar'));
                    redirect(base_url());
                }
            }
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    // Function to get the client IP address
    public function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}
