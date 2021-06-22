<?php

include_once 'lib/eagrolib.php';
include_once 'lib/devLibrary.php';
require_once 'master_validation.php';
require_once 'config/connection.php';
if (!empty($_POST)) {
	$code = $_POST['code'];
	$option = $_POST['option'];
} else {
	$code = $_GET['code'];
	$option = $_GET['option'];
}
$sta = "select * from $dbname.organisasi where kodeorganisasi='$code'";
$re = mysql_query($sta);

$data = [];

if ($option=='save') {
	$post = $_POST;
	$update = "update organisasi set ".
		//"kodeorganisasi='".$_POST['kodeorganisasi']."',".
		"namaorganisasi='".$_POST['namaorganisasi']."',".
		"tipe='".$_POST['tipe']."',".
		"alamat='".$_POST['alamat']."',".
		"telepon='".$_POST['telepon']."',".
		"wilayahkota='".$_POST['wilayahkota']."',".
		"kodepos='".$_POST['kodepos']."',".
		"negara='".$_POST['negara']."',".
		"alokasi='".$_POST['alokasi']."',".
		"noakun='".$_POST['noakun']."'".
		"where kodeorganisasi='".$_POST['kodeorganisasi']."'";
	if (!mysql_query($update)) {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	if (0 < mysql_num_rows($re)) {
		while ($be = mysql_fetch_object($re)) {
			$data[] = array("id" => "kodeorganisasi", "caption" => 'Kode Organisasi', "value" => $be->kodeorganisasi, "reference" => array());
			$data[] = array("id" => "namaorganisasi", "caption" => 'Nama Organisasi', "value" => $be->namaorganisasi, "reference" => array());
			$data[] = array("id" => "tipe", "caption" => 'Tipe', "value" => $be->tipe, "reference" => array());
			$data[] = array("id" => "alamat", "caption" => 'Alamat', "value" => $be->alamat, "reference" => array());
			$data[] = array("id" => "telepon", "caption" => 'Telp', "value" => $be->telepon, "reference" => array());
			$data[] = array("id" => "wilayahkota", "caption" => 'Wilayah Kota', "value" => $be->wilayahkota, "reference" => array());
			$data[] = array("id" => "kodepos", "caption" => 'Kode Pos', "value" => $be->kodepos, "reference" => array());
			$data[] = array("id" => "negara", "caption" => 'Negara', "value" => $be->negara, "reference" => array());

			$ref = [];
			$sta = 'select * from ' . $dbname . '.organisasi where tipe=\'PT\' order by namaorganisasi';
			$re = mysql_query($sta);
			while ($be1 = mysql_fetch_object($re)) {
				$ref[] = array("value" => $be1->kodeorganisasi, "caption" => $be1->kodeorganisasi . ' - ' . $be1->namaorganisasi);
			}

			$data[] = array("id" => "alokasi", "caption" => 'Alokasi', "value" => $be->alokasi, "reference" => $ref);

			$sta = "select noakun,namaakun from " . $dbname . ".keu_5akun where detail=1 order by noakun";
			$re = mysql_query($sta);
			$ref = [];
			while ($be1 = mysql_fetch_object($re)) {
				$ref[] = array("value" => $be1->noakun, "caption" => $be1->noakun . ' - ' . $be1->namaakun);
			}

			$data[] = array("id" => "noakun", "caption" => 'No Akun', "value" => $be->noakun, "reference" => $ref);
		}
	} else {
		echo '-1';
	}
	echo json_encode($data);
}
?>
