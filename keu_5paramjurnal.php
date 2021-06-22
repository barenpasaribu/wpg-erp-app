<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n";
$where = '`detail`=1';
$optAkun = ['' => ''];
$tmpAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $where, '2');
foreach ($tmpAkun as $key => $row) {
    $optAkun[$key] = $row;
}
$whereOrg = "tipe='HOLDING' and length(kodeorganisasi)=3";
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', $whereOrg, '1');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:250px'], $optOrg)];
$els[] = [makeElement('kodeaplikasi', 'label', $_SESSION['lang']['kodeaplikasi']), makeElement('kodeaplikasi', 'text', '', ['style' => 'width:50px', 'maxlength' => '5'])];
$els[] = [makeElement('jurnalid', 'label', $_SESSION['lang']['jurnalid']), makeElement('jurnalid', 'text', '', ['style' => 'width:250px', 'maxlength' => '6'])];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '50'])];
$els[] = [makeElement('noakundebet', 'label', $_SESSION['lang']['noakundebet']), makeElement('noakundebet', 'select', '', ['style' => 'width:250px'], $optAkun)];
$els[] = [makeElement('sampaidebet', 'label', $_SESSION['lang']['sampaidebet']), makeElement('sampaidebet', 'select', '', ['style' => 'width:250px'], $optAkun)];
$els[] = [makeElement('noakunkredit', 'label', $_SESSION['lang']['noakunkredit']), makeElement('noakunkredit', 'select', '', ['style' => 'width:250px'], $optAkun)];
$els[] = [makeElement('sampaikredit', 'label', $_SESSION['lang']['sampaikredit']), makeElement('sampaikredit', 'select', '', ['style' => 'width:250px'], $optAkun)];
$els[] = [makeElement('aktif', 'label', $_SESSION['lang']['aktif']), makeElement('aktif', 'check')];
$fieldStr = '##kodeorg##kodeaplikasi##jurnalid##keterangan##noakundebet##sampaidebet##noakunkredit##sampaikredit##aktif';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'keu_5parameterjurnal', '##kodeorg##kodeaplikasi##jurnalid', null, null, true)];
echo genElTitle('Parameter Jurnal', $els);
echo "</div><div style='clear:both;float:left'>";
echo masterTable($dbname, 'keu_5parameterjurnal', $fieldArr, [], [], null, [], null, 'kodeorg##kodeaplikasi##jurnalid');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>