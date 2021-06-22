<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zMaster.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n  \r\n<p align=\"left\"><u><b><font face=\"Arial\" size=\"5\" color=\"#000080\">Penalti</font></b></u></p>\r\n";
$optKode = getEnum($dbname, 'kebun_5penalty', 'kodedenda');
$optUom = getEnum($dbname, 'kebun_5penalty', 'satuan');
$optBudidaya = makeOption($dbname, 'kebun_5budidaya', 'kode,budidaya');
echo "<div style='margin-bottom:30px'>";
$els = [];
$els[] = [makeElement('budidaya', 'label', $_SESSION['lang']['budidaya']), makeElement('budidaya', 'select', '', ['style' => 'width:300px'], $optBudidaya)];
$els[] = [makeElement('kodedenda', 'label', $_SESSION['lang']['kodedenda']), makeElement('kodedenda', 'select', '', ['style' => 'width:300px'], $optKode)];
$els[] = [makeElement('keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('keterangan', 'text', '', ['style' => 'width:200px', 'maxlength' => '50', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('satuan', 'label', $_SESSION['lang']['satuan']), makeElement('satuan', 'select', '', ['style' => 'width:300px'], $optUom)];
$els[] = [makeElement('dendapemanen', 'label', $_SESSION['lang']['dendapemanen']), makeElement('dendapemanen', 'text', '', ['style' => 'width:200px', 'maxlength' => '10', 'onkeypress' => 'return angka_doang(event)'])];
$els[] = [makeElement('dendasupervisi', 'label', $_SESSION['lang']['dendasupervisi']), makeElement('dendasupervisi', 'check', '0', ['style' => 'width:300px'])];
$els[] = [makeElement('tglberlaku', 'label', $_SESSION['lang']['tglberlaku']), makeElement('tglberlaku', 'text', '', ['style' => 'width:200px', 'readonly' => 'readonly', 'onmousemove' => 'setCalendar(this.id)'])];
$fieldStr = '##budidaya##kodedenda##keterangan##satuan##dendapemanen##dendasupervisi##tglberlaku';
$fieldArr = explode('##', substr($fieldStr, 2, strlen($fieldStr) - 2));
$els['btn'] = [genFormBtn($fieldStr, 'kebun_5penalty', '##budidaya##kodedenda')];
echo genElement($els);
echo "</div><div style='height:200px;overflow:auto'>";
echo masterTable($dbname, 'kebun_5penalty', '*', [], [], null, [], null, 'budidaya##kodedenda');
echo '</div>';
CLOSE_BOX();
echo close_body();

?>