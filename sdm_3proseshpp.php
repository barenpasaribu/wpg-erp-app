<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$str = 'select periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'\r\n          order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optPeriod[$bar->periode] = $bar->periode;
}
$els = [];
$els[] = [makeElement('periodegaji', 'label', $_SESSION['lang']['periodeakuntansi']), makeElement('periodegaji', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els['btn'] = [makeElement('listBtn', 'btn', $_SESSION['lang']['list'], ['onclick' => 'list()'])];
$form = '';
$form .= "<h3 align='left'>Proses Perhitungan HPP</h3>";
$form .= genElementMultiDim($_SESSION['lang']['form'], $els, 1);
$form .= "<fieldset style='float:left;clear:left;'><legend><b>Tampilan</b></legend><div id='listContainer'></div></fieldset>";
echo open_body();
echo "<script languange=javascript1.2 src='js/sdm_proseshpp.js'></script>\r\n<link rel=stylesheet type=text/css href='style/zTable.css'>\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo $form;
CLOSE_BOX();
echo close_body();

?>