<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n<p align=\"left\"><u><b><font face=\"Arial\" size=\"5\" color=\"#000080\">Basis Pemeliharaan</font></b></u></p>\r\n";
$optTopografi = makeOption($dbname, 'setup_topografi', 'topografi,keterangan');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('topografi', 'label', $_SESSION['lang']['topografi']), makeElement('topografi', 'select', '', ['style' => 'width:100px'], $optTopografi)];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('batasbawah', 'label', $_SESSION['lang']['batasbawah']), makeElement('batasbawah', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('batasatas', 'label', $_SESSION['lang']['batasatas']), makeElement('batasatas', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('basisboronglaki', 'label', $_SESSION['lang']['basisboronglaki']), makeElement('basisboronglaki', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('basisborongperempuan', 'label', $_SESSION['lang']['basisborongperempuan']), makeElement('basisborongperempuan', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('basistugaslaki', 'label', $_SESSION['lang']['basistugaslaki']), makeElement('basistugaslaki', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('basistugasperempuan', 'label', $_SESSION['lang']['basistugasperempuan']), makeElement('basistugasperempuan', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('satuan', 'label', $_SESSION['lang']['satuan']), makeElement('satuan', 'text', '', ['style' => 'width:50px', 'maxlength' => '3', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('nilaipremi', 'label', $_SESSION['lang']['nilaipremi']), makeElement('nilaipremi', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$fieldStr = '##topografi##keterangan##batasbawah##batasatas##basisboronglaki##basisborongperempuan';
$fieldStr .= '##basistugaslaki##basistugasperempuan##satuan##nilaipremi';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'kebun_5basispemeliharaan', '##topografi', null, 'topografi')];
echo genElement($els);
echo "</div><div style='height:200px;overflow:auto'>";
echo masterTable($dbname, 'kebun_5basispemeliharaan', '*', [], [], null, [], null, 'topografi');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>