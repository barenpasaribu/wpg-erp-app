<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<!-- Includes -->\r\n<script language=javascript1.2 src='js/zTools.js'></script>\r\n<script language=javascript1.2 src='js/keu_3posting.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";

//$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optOrg=[ $_SESSION['empl']['lokasitugas'] => $_SESSION['empl']['namalokasitugas']];

$bulantahun = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
$optPeriod = [$bulantahun => $bulantahun];
/*if ('EN' == $_SESSION['language']) {
    $optJenisData = ['gaji' => 'Salaries From General Cost', 'gajiharilibur' => 'UnAllocated Salaries of Plant Labour', 'potongan' => 'Deduction Journal', 'alokasi' => 'Vehicle Running Allocation - (Traksi)', 'depresiasi' => 'Depresiation'];
} else {
*/   
if($_SESSION['empl']['tipelokasitugas']=='HOLDING'){

 $optJenisData = ['gaji' => 'Alokasi Gaji Karyawan', 'alokasi' => 'Alokasi Traksi Kendaraan- (Traksi)', 'depresiasi' => 'Depresiasi'];

/*} else if($_SESSION['empl']['tipelokasitugas']=='KEBUN') {

 $optJenisData = ['gaji1' => 'Alokasi Gaji Kebun', 'bengkel' => 'Alokasi Traksi Workshop - (Traksi)', 'alokasi' => 'Alokasi Traksi Kendaraan- (Traksi)', 'depresiasi' => 'Depresiasi'];
*/
}else{
	$optJenisData = ['gaji' => 'Alokasi Gaji Karyawan', 'bengkel' => 'Alokasi Traksi Workshop - (Traksi)', 'alokasi' => 'Alokasi Traksi Kendaraan- (Traksi)', 'depresiasi' => 'Depresiasi'];
}


//}

$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg'], ['style' => 'width:300px']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('periode', 'label', $_SESSION['lang']['periode'], ['style' => 'width:300px']), makeElement('periode', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els[] = [makeElement('jenisData', 'label', $_SESSION['lang']['jenisbiaya'], ['style' => 'width:300px']), makeElement('jenisData', 'select', '', ['style' => 'width:300px'], $optJenisData)];
$els['btn'] = [makeElement('btnList', 'button', $_SESSION['lang']['list'], ['onclick' => 'listPosting()'])];
include 'master_mainMenu.php';
OPEN_BOX();
echo genElTitle($_SESSION['lang']['peosesakhirbulan'], $els);
CLOSE_BOX();
OPEN_BOX();
echo makeFieldset($_SESSION['lang']['list'], 'listPosting', null, true);
CLOSE_BOX();
close_body();

?>