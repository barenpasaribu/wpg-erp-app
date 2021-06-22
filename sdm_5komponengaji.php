<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n<p align=\"left\"><u><b><font face=\"Arial\" size=\"5\" color=\"#000080\">Komponen Gaji</font></b></u></p>\r\n";
$optBin = ['Pengurang', 'Penambah'];
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('idkomponen', 'label', $_SESSION['lang']['idkomponen']), makeElement('idkomponen', 'textnum', '', ['style' => 'width:200px', 'maxlength' => '11'])];
$els[] = [makeElement('namakomponen', 'label', $_SESSION['lang']['namakomponen']), makeElement('namakomponen', 'text', '', ['style' => 'width:200px', 'maxlength' => '45'])];
$els[] = [makeElement('tipe', 'label', $_SESSION['lang']['tipe']), makeElement('tipe', 'select', '', ['style' => 'width:300px'], $optBin)];
$els[] = [makeElement('sumber', 'label', $_SESSION['lang']['sumber']), makeElement('sumber', 'text', '', ['style' => 'width:200px', 'maxlength' => '40'])];
$fieldStr = '##idkomponen##namakomponen##tipe##sumber';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'sdm_5komponengaji', '##idkomponen')];
echo genElement($els);
echo "</div><div style='height:200px;overflow:auto'>";
echo masterTable($dbname, 'sdm_5komponengaji', '*', [], [], null, [], null, 'idkomponen');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>