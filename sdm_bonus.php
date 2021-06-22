<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript1.2 src='js/sdm_bonus.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optPeriod = makeOption($dbname, 'sdm_5periodegaji', 'periode,periode', "kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");
$arrData = '##periodegaji##jenis##jnsGaji';
$els = [];
$teks1 = 'Periode THR/Bonus';
//$els[] = [makeElement('periodegaji', 'label', $_SESSION['lang']['periodebonus']), makeElement('periodegaji', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els[] = [makeElement('periodegaji', 'label', $teks1), makeElement('periodegaji', 'select', '', ['style' => 'width:300px'], $optPeriod)];
//$els[] = [makeElement('jenis', 'label', $_SESSION['lang']['jenis']), makeElement('jenis', 'select', '', ['style' => 'width:300px'], [28 => 'THR', 26 => 'Bonus'])];

$id_komponen_thr = 0;
$id_komponen_bonus = 0;
$str = "select * from sdm_ho_component where name in ('thr','bonus')";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if(strtolower(trim($bar->name)) == "thr" ){
		$id_komponen_thr = $bar->id;		
	}
	if(strtolower(trim($bar->name)) == "bonus" ){
		$id_komponen_bonus = $bar->id;
	}
}
$teksagama= 'Agama';
$islam= 'Islam';
$nasrani= 'Kristen-Katolik-Protestan';
$other= 'Hindu-Budha-Konghucu-Lainnya';
$els[] = [makeElement('jenis', 'label', $_SESSION['lang']['jenis']), makeElement('jenis', 'select', '', ['style' => 'width:300px'], [$id_komponen_thr => 'THR', $id_komponen_bonus => 'Bonus'])];
$els[] = [makeElement('jnsGaji', 'label', $_SESSION['lang']['sistemgaji']), makeElement('jnsGaji', 'select', '', ['style' => 'width:300px'], ['Bulanan' => $_SESSION['lang']['bulanan'], 'Harian' => $_SESSION['lang']['harian']])];
$els[] = [makeElement('agama', 'label', $teksagama), makeElement('agama', 'select', '', ['style' => 'width:300px'], ['Islam' => $islam, 'Kristen-Katolik-Protestan' => $nasrani,'Hindu-Budha-Konghucu-Lainnya' => $other])];
$els[] = [makeElement('tanggal', 'label', $_SESSION['lang']['tanggal']), makeElement('tanggal', 'date', '', ['style' => 'width:200px', 'maxlength' => '20'])];
$els[] = [makeElement('tahun', 'label', 'Basis Gaji'), makeElement('tahun', 'textnum', '', ['style' => 'width:50px', 'maxlength' => '20'])];
$els['btn'] = [makeElement('listBtn', 'btn', $_SESSION['lang']['list'], ['onclick' => 'list()']).makeElement('cancelBtn', 'btn', $_SESSION['lang']['cancel'], ['onclick' => 'cancel()', 'disabled' => 'disabled']).makeElement('excelBtn', 'btn', 'Excel', ['onclick' => "zExcel(event,'sdm_slave_bonus.php','".$arrData."')"])];
$form = '';
$teks2= 'THR/Bonus';
//$form .= "<h3 align='left'>".$_SESSION['lang']['bonus'].'</h3>';
$form .= "<h3 align='left'>".$teks2.'</h3>';
$form .= genElementMultiDim($_SESSION['lang']['form'], $els, 1);
OPEN_BOX();
echo $form;
CLOSE_BOX();
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'], 'listPosting', null, true);
CLOSE_BOX();
echo close_body();

?>