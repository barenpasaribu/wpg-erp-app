<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$optPt = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
$tmpUnit = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "induk='".$_SESSION['org']['kodeorganisasi']."'");
$optUnit = ['' => $_SESSION['lang']['all']];
foreach ($tmpUnit as $key => $row) {
    $optUnit[$key] = $row;
}
$optRev[''] = $_SESSION['lang']['all'];
for ($i = 0; $i <= 5; ++$i) {
    $optRev[$i] = $i;
}
$tmpBulan = $_SESSION['org']['period']['bulan'];
$tmpTahun = $_SESSION['org']['period']['tahun'];
$tmp2Bulan = $tmpBulan + 1;
if (12 < $tmp2Bulan) {
    $tmp2Bulan = 1;
    $tmp2Tahun = $tmpTahun;
} else {
    $tmp2Tahun = $tmpTahun - 1;
}

$optPeriod = [];
for ($i = 0; $i < 12; ++$i) {
    if ($tmp2Bulan < 10) {
        $tmp2Bulan = '0'.$tmp2Bulan;
    }

    $optPeriod[$tmp2Bulan.'-'.$tmp2Tahun] = $tmp2Bulan.'-'.$tmp2Tahun;
    ++$tmp2Bulan;
    if (12 < $tmp2Bulan) {
        $tmp2Bulan = 1;
        ++$tmp2Tahun;
    }
}
$els = [];
$els[] = [makeElement('pt', 'label', $_SESSION['lang']['pt']), makeElement('pt', 'select', '', ['style' => 'width:300px'], $optPt)];
$els[] = [makeElement('unit', 'label', $_SESSION['lang']['unit']), makeElement('unit', 'select', '', ['style' => 'width:300px'], $optUnit)];
$els[] = [makeElement('periode', 'label', $_SESSION['lang']['periode']), makeElement('periode', 'select', '', ['style' => 'width:300px'], $optPeriod)];
$els[] = [makeElement('revisi', 'label', $_SESSION['lang']['revisi']), makeElement('revisi', 'select', 'all', ['style' => 'width:300px'], $optRev)];
$param = '##pt##unit##periode##revisi';
$container = 'printArea';
$els['btn'] = [makeElement('btnPreview', 'btn', 'Preview', ['onclick' => "zPreview('keu_slave_2summaryJMemorial','".$param."','".$container."')"]).makeElement('btnPDF', 'btn', 'PDF', ['onclick' => "zPdf('keu_slave_2summaryJMemorial','".$param."','".$container."')"]).makeElement('btnExcel', 'btn', 'Excel', ['onclick' => "zExcel(event,'keu_slave_2summaryJMemorial.php','".$param."','".$container."')"])];
echo open_body();
echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"style/zTable.css\">\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
echo genElTitle('Summary Memorial Journal', $els);
echo "<fieldset style='clear:left'><legend><b>Print Area</b></legend>";
echo "<div id='".$container."' style='overflow:auto;height:60%'></div></fieldset>";
CLOSE_BOX();
echo close_body();

?>