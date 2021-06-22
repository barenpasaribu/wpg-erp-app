<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n";
$where = "`tipe`='HOLDING'";
$optOrg = getOrgBelow($dbname, $_SESSION['empl']['kodeorganisasi'], false, 'kebunonly');
$els = [];
$els[] = [makeElement('tahun', 'label', $_SESSION['lang']['tahun']), makeElement('tahun', 'textnum', date(Y), ['style' => 'width:200px', 'maxlength' => '16', 'onkeypress' => 'return tanpa_kutip(event)'])];
$els[] = [makeElement('kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('kodeorg', 'select', '', ['style' => 'width:300px'], $optOrg)];
$els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'textnum', '0', ['style' => 'width:200px', 'maxlength' => '80', 'onkeypress' => 'return tanpa_kutip(event)'])];
$param = '##tahun##kodeorg##revisi';
$container = 'printContainer';
$els['btn'] = [makeElement('preview', 'btn', 'Preview', ['onclick' => "zPreview('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')"]).makeElement('printPdf', 'btn', 'PDF', ['onclick' => "zPdf('keu_slave_2laporanAnggaranKebun_print','".$param."','".$container."')"]).makeElement('printExcel', 'btn', 'Excel', ['onclick' => 'excelBudKebun()'])];
echo "<div style='margin-bottom:30px'>";
echo genElTitle('Laporan Anggaran Traksi', $els);
echo "</div><fieldset style='clear:both'><legend><b>Print Area</b></legend><div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'></div></fieldset>";
CLOSE_BOX();
echo close_body();

?>