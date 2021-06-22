\<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo "<!-- Includes -->\r\n<script language=javascript1.2 src=js/zTools.js></script>\r\n<script language=javascript1.2 src=js/keu_3tutupbulan.js></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$bulantahun = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
$optPeriod = [$bulantahun => $bulantahun];
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('periode', 'label', $_SESSION['lang']['periode']), makeElement('periode', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els['btn'] = [makeElement('btnList', 'button', $_SESSION['lang']['tutupbuku'], ['onclick' => 'tutupBuku()'])];
include 'master_mainMenu.php';
OPEN_BOX();
echo genElTitle($_SESSION['lang']['tutupbulan'], $els);
CLOSE_BOX();
close_body();

?>