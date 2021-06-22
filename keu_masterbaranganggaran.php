<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n";
$whereOrg = "tipe='PT'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '1');
$optMatauang = makeOption($dbname, 'setup_matauang', 'kode,matauang', null, '1');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('kodebarang', 'label', $_SESSION['lang']['kodebarang']), makeElement('kodebarang', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'readonly' => 'readonly']).makeElement('searchBarang', 'btn', $_SESSION['lang']['find'], ['onclick' => "getInv(event,'kodebarang')"])];
$els[] = [makeElement('kode', 'label', $_SESSION['lang']['kode']), makeElement('kode', 'text', '', ['style' => 'width:100px', 'maxlength' => '10'])];
$els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'textnum', '', ['style' => 'width:100px', 'maxlength' => '4'])];
$els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'textnum', '', ['style' => 'width:100px', 'maxlength' => '2'])];
$els[] = [makeElement('matauang', 'label', $_SESSION['lang']['matauang']), makeElement('matauang', 'select', '', ['style' => 'width:100px'], $optMatauang)];
$els[] = [makeElement('hargasatuan', 'label', $_SESSION['lang']['hargasatuan']), makeElement('hargasatuan', 'textnum', '', ['style' => 'width:100px', 'maxlength' => '10'])];
$fieldStr = '##kodeorg##kodebarang##kode##tahun##revisi##matauang##hargasatuan';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'log_5masterbaranganggaran', '##kodeorg##kodebarang##kode##tahun##revisi')];
echo genElTitle('Master Barang Anggaran', $els);
echo '</div>';
$cols = ['kodeorg', 'kodebarang', 'kode', 'tahun', 'revisi', 'matauang', 'hargasatuan'];
echo "<div style='clear:both;float:left'>";
echo masterTable($dbname, 'log_5masterbaranganggaran', $cols, [], [], [], [], null, 'kodeorg##kodebarang##kode##tahun##revisi##searchBarang');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>