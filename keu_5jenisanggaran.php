<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";
$where = "`tipe`='HOLDING' or `tipe`='PT'";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $where, '0');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeanggaran', 'label', $_SESSION['lang']['kodeanggaran']), makeElement('kodeanggaran', 'text', '', ['style' => 'width:100px', 'maxlength' => '10', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('namaanggaran', 'label', $_SESSION['lang']['namaanggaran']), makeElement('namaanggaran', 'text', '', ['style' => 'width:250px', 'maxlength' => '45', 'onkeypress' => 'return tanpa_kutip(event)'])];
$fieldStr = '##kodeanggaran##namaanggaran';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'keu_5jenisanggaran', '##kodeanggaran')];
echo genElTitle('Jenis Anggaran', $els);
echo "</div><div style='clear:both;float:left'>";
echo masterTable($dbname, 'keu_5jenisanggaran');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>