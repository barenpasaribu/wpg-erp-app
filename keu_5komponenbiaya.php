<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n";
$optKlpBy = ['Karyawan' => 'Karyawan', 'Mesin' => 'Mesin', 'Material' => 'Material', 'Transport' => 'Transport', 'Kontrak' => 'Kontrak', 'Supervisi' => 'Supervisi'];
$whereOrg = "tipe='HOLDING'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '1');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:250px'], $optOrg)];
$els[] = [makeElement('kelompokbiaya', 'label', $_SESSION['lang']['kelompokbiaya']), makeElement('kelompokbiaya', 'select', '', ['style' => 'width:300px'], $optKlpBy)];
$els[] = [makeElement('kodebiaya', 'label', $_SESSION['lang']['kodebiaya']), makeElement('kodebiaya', 'text', '', ['style' => 'width:50px', 'maxlength' => '3'])];
$els[] = [makeElement('keteranganbiaya', 'label', $_SESSION['lang']['keterangan']), makeElement('keteranganbiaya', 'text', '', ['style' => 'width:250px', 'maxlength' => '40'])];
$fieldStr = '##kodeorg##kelompokbiaya##kodebiaya##keteranganbiaya';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'keu_5komponenbiaya', '##kodebiaya')];
echo genElTitle('Komponen Biaya', $els);
echo "</div><div style='clear:both;float:left'>";
echo masterTable($dbname, 'keu_5komponenbiaya', '*', [], [], null, [], null, 'kodebiaya');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>