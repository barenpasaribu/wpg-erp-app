<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/kebun_5bjr.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");
$optMonth = optionMonth('I', 'long');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:200px', 'onchange' => 'blokInfo()'], $optOrg)];
$els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '4', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('umr', 'label', $_SESSION['lang']['umr']), makeElement('umr', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$fieldStr = '##kodeorg##tahun##umr';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'sdm_5umr', '##kodeorg##tahuntanam', null, 'kodeorg##tahun')];
echo genElTitle($_SESSION['lang']['umr'], $els);
echo "<div style='max-height:200px;overflow:auto;clear:both'>";
echo masterTable($dbname, 'sdm_5umr', '*', [], [], null, [], ['sep' => 'and', ['kodeorg' => $_SESSION['empl']['lokasitugas']]], 'kodeorg##tahun');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>