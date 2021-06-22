<?php
require_once('master_validation.php');
include('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

showerror();
#pre($_POST); exit();
$Bagian = isset($_POST['Departemen']) ? $_POST['Departemen'] : '';
$Proses = isset($_POST['Proses']) ? $_POST['Proses'] : null;
switch($Proses)
{
	case'KaryawanByDepartemen':
		# Ambil option untuk karyawan
		$sKaryawan = "select * from ".$dbname.".datakaryawan 
		where lokasitugas like '".$_SESSION['empl']['lokasitugas']."' and
		bagian like '%".$Bagian."%' order by namakaryawan asc";    
		$rkaryawan = fetchData($sKaryawan);
		if(!isset($rkaryawan) || empty($rkaryawan)) {
			$optkaryawan = "<option>Tidak Ada Karyawan</option>";
		} else {
			$optkaryawan = "<option value='Bag-".$Bagian."'>Semua Karyawan ".$Bagian."</option>";
		}

		foreach($rkaryawan as $row => $kar)
		{
			$optkaryawan.="<option value='".$kar['karyawanid']."'>".$kar['namakaryawan']."</option>";
		}
		echo $optkaryawan;
		break;
	default:
    break;
}