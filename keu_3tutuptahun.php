<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<!-- Includes -->\r\n<script language=javascript1.2 src=js/zTools.js></script>\r\n<script language=javascript1.2 src=js/keu_3tutuptahun.js></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$tahun = $_SESSION['org']['period']['tahun'] - 1;
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'textnum', $tahun, ['style' => 'width:200px', 'disabled' => 'disabled'])];
$els['btn'] = [makeElement('btnPost', 'button', $_SESSION['lang']['posting'], ['onclick' => 'postingData()'])];
include 'master_mainMenu.php';
OPEN_BOX();
echo genElTitle('Alokasi Biaya Umum', $els);
CLOSE_BOX();
close_body();

?>