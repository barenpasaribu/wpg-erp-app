<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$org_code = $_POST['code_org'];
$code_block = $_POST['kode_blok'];
$no_rmh = $_POST['rmh_no'];
$method = $_POST['method'];

switch ($method) {
case 'get_blok':
	$optOrg = '';
	$sql = 'select blok from ' . $dbname . '.sdm_perumahanht where kodeorg=\'' . $org_code . '\' group by blok';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$optOrg .= '<option value=></option>';

	while ($res = mysql_fetch_assoc($query)) {
		$optOrg .= '<option value=' . $res['blok'] . '>' . $res['blok'] . '</option>';
	}

	echo $optOrg;
	break;

case 'get_kary':
	$optKary = '';
	$skary = 'select karyawanid,namakaryawan,lokasitugas,subbagian from ' . $dbname . '.datakaryawan where lokasitugas=\'' . $org_code . '\'';

	#exit(mysql_error());
	($qkary = mysql_query($skary)) || true;

	while ($rkary = mysql_fetch_assoc($qkary)) {
		if (($rkary['subbagian'] == '0') || is_null($rkary['subbagian'])) {
			$rkary['lokasitugas'] = $rkary['lokasitugas'];
		}
		else {
			$rkary['lokasitugas'] = $rkary['subbagian'];
		}

		$optKary .= '<option value=' . $rkary['karyawanid'] . '>' . $rkary['namakaryawan'] . '&nbsp;[' . $rkary['karyawanid'] . ']&nbsp;[' . $rkary['lokasitugas'] . ']</option>';
	}

	echo $optKary;
	break;

case 'get_normh':
	$optNormh = '';

	if (($no_rmh != 0) && ($code_block != 0)) {
		$where .= ' kodeorg=\'' . $org_code . '\' and blok=\'' . $code_block . '\' and norumah=\'' . $no_rmh . '\'';
	}
	else if ($code_block != '') {
		$where .= ' kodeorg=\'' . $org_code . '\' and blok=\'' . $code_block . '\'';
	}

	$sql = 'select norumah from ' . $dbname . '.sdm_perumahanht where' . $where;

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$optNormh .= '<option value=' . $res['norumah'] . '>' . $res['norumah'] . '</option>';
	}

	echo $optNormh;
	break;
}

?>
