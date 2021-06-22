<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$afdeling = $_POST['afdeling'];
$where1 = '(tipe=\'BLOK\' or tipe=\'BIBITAN\') and induk=\'' . $afdeling . '\'';
$query = selectQuery($dbname, 'organisasi', 'kodeorganisasi', $where1);
$data = fetchData($query);
$where2 = array();

foreach ($data as $key => $row) {
	$where2[] = array('kodeorg' => $row['kodeorganisasi']);
}

if (count($where2) < 1) {
	exit('Error:Tidak ada data');
}

$where2['sep'] = 'OR';
$fieldStr = '##kodeorg##bloklama##tahuntanam##luasareaproduktif##luasareanonproduktif' .
	'##jumlahpokok##statusblok##mulaipanen##kodetanah' .
	'##klasifikasitanah##topografi##intiplasma##jenisbibit##tanggalpengakuan' .
	'##cadangan##okupasi##rendahan##sungai##rumah##kantor##pabrik##jalan##kolam##umum';
/*	
	'##jumlahpokoksisipan1##tahunjumlahpokoksisipan1##jumlahpokoksisipan2##tahunjumlahpokoksisipan2'.
	'##jumlahpokoksisipan3##tahunjumlahpokoksisipan3##jumlahpokokabnormal';
*/

$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));

$head = array();
$head[0]['name'] = $_SESSION['lang']['kodeorg'];
$head[1]['name'] = $_SESSION['lang']['bloklama'];
$head[2]['name'] = $_SESSION['lang']['tahuntanam'];
$head[3]['name'] = $_SESSION['lang']['luasareaproduktif'];
$head[4]['name'] = $_SESSION['lang']['luasareanonproduktif'];
$head[5]['name'] = $_SESSION['lang']['jumlahpokok'];
$head[6]['name'] = $_SESSION['lang']['statusblok'];
$head[7]['name'] = $_SESSION['lang']['bulanmulaipanen'];
$head[7]['span'] = '2';
$head[8]['name'] = $_SESSION['lang']['kodetanah'];
$head[9]['name'] = $_SESSION['lang']['klasifikasitanah'];
$head[10]['name'] = $_SESSION['lang']['topografi'];
$head[11]['name'] = $_SESSION['lang']['intiplasma'];
$head[12]['name'] = $_SESSION['lang']['jenisbibit'];
$head[13]['name'] = $_SESSION['lang']['tanggal'];
$head[14]['name'] = $_SESSION['lang']['cadangan'];
$head[15]['name'] = $_SESSION['lang']['okupasi'];
$head[16]['name'] = $_SESSION['lang']['rendahan'];
$head[17]['name'] = $_SESSION['lang']['sungai'];
$head[18]['name'] = $_SESSION['lang']['rumah'];
$head[19]['name'] = $_SESSION['lang']['kantor'];
$head[20]['name'] = $_SESSION['lang']['pabrik'];
$head[21]['name'] = $_SESSION['lang']['jalan'];
$head[22]['name'] = $_SESSION['lang']['kolam'];
$head[23]['name'] = $_SESSION['lang']['umum'];

/*
$head[24]['name'] = 'Jml Sisip 1';
$head[25]['name'] = 'Thn Sisip 1';
$head[26]['name'] = 'Jml Sisip 2';
$head[27]['name'] = 'Thn Sisip 2';
$head[28]['name'] = 'Jml Sisip 3';
$head[29]['name'] = 'Thn Sisip 3';
$head[30]['name'] = 'Jml Abnormal';
*/

$conSetting = array();
$conSetting['luasareaproduktif']['type'] = 'currency';
$conSetting['luasareanonproduktif']['type'] = 'currency';
$conSetting['jumlahpokok']['type'] = 'numeric';
$conSetting['bulanmulaipanen']['type'] = 'month';
$conSetting['cadangan']['type'] = 'numeric';
$conSetting['okupasi']['type'] = 'numeric';
$conSetting['rendahan']['type'] = 'numeric';
$conSetting['sungai']['type'] = 'numeric';
$conSetting['rumah']['type'] = 'numeric';
$conSetting['kantor']['type'] = 'numeric';
$conSetting['pabrik']['type'] = 'numeric';
$conSetting['jalan']['type'] = 'numeric';
$conSetting['kolam']['type'] = 'numeric';
$conSetting['umum']['type'] = 'numeric';

$master = masterTableBlok($dbname, 'setup_blok', 1, $fieldArr, $head, $conSetting, $where2, array(), 'setup_slave_blok_pdf');

try {
	echo $master;
}
catch (Exception $e) {
	echo 'Create Table Error';
}

?>
