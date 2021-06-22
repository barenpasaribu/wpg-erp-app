<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
echo "<script language=javascript src=js/keu_anggaran.js></script>\r\n<script language=javascript src=js/zSearch.js></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n<p align=\"left\"><u><b><font face=\"Arial\" size=\"3\" color=\"#000080\">Anggaran</font></b></u></p>\r\n";
if (!isset($_SESSION['empl']['lokasitugas'])) {
    echo $_SESSION['lang']['errorkaryawan'];
    CLOSE_BOX();
    echo close_body();
    exit();
}

$headControl = "<img id='addHeaderId' title='Tambah Header' src='images/plus.png'"."style='width:20px;height:20px;cursor:pointer' onclick='addHeader(event)' />&nbsp;";
$headControl .= "<img id='editHeaderId' title='Lihat Daftar Header' src='images/edit.png'"."style='width:20px;height:20px;cursor:pointer' onclick='showHeadList(event)' />";
$els = [];
$els[] = [makeElement('main_kodeorg', 'label', $_SESSION['lang']['kodeorg']), makeElement('main_kodeorg', 'text', '', ['style' => 'width:70px', 'disabled' => 'disabled']).'&nbsp;'.makeElement('main_nameorg', 'text', '', ['style' => 'width:200px', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_kodeanggaran', 'label', $_SESSION['lang']['kodeanggaran']), makeElement('main_kodeanggaran', 'text', '', ['style' => 'width:70px', 'maxlength' => '10', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_keterangan', 'label', $_SESSION['lang']['keterangan']), makeElement('main_keterangan', 'text', '', ['style' => 'width:250px', 'maxlength' => '50', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_tipeanggaran', 'label', $_SESSION['lang']['tipeanggaran']), makeElement('main_tipeanggaran', 'text', '', ['style' => 'width:70px', 'maxlength' => '10', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_tahun', 'label', $_SESSION['lang']['tahun']), makeElement('main_tahun', 'text', '', ['style' => 'width:70px', 'maxlength' => '4', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_matauang', 'label', $_SESSION['lang']['matauang']), makeElement('main_matauang', 'text', '', ['style' => 'width:70px', 'maxlength' => '3', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_jumlah', 'label', $_SESSION['lang']['jumlah']), makeElement('main_jumlah', 'textnum', '', ['style' => 'width:70px', 'maxlength' => '10', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_revisi', 'label', $_SESSION['lang']['revisi']), makeElement('main_revisi', 'textnum', '', ['style' => 'width:70px', 'maxlength' => '2', 'disabled' => 'disabled'])];
$els[] = [makeElement('main_tutup', 'label', $_SESSION['lang']['tutup']), makeElement('main_tutup', 'check', '', ['disabled' => 'disabled'])];
$header = [$_SESSION['lang']['kodebagian'], $_SESSION['lang']['kodekegiatan'], $_SESSION['lang']['kelompok'], $_SESSION['lang']['revisi'], $_SESSION['lang']['kodebarang'], 'Z'];
$data = [];
$tables = makeTable('listDetail', 'bodyDetail', $header, $data, [], true, 'detail_tr');
echo '<div>';
echo $headControl;
echo '</div>';
$container = "<fieldset style='float:left;clear:both'>"."<legend><b>Header</b></legend><div id='headContainer'>";
$container .= genElement($els);
$container .= '</div></fieldset>';
$container .= "<fieldset style='float:left;clear:both'>"."<legend><b>Detail</b></legend><div id='detailContainer'></div></fieldset>";
echo $container;
CLOSE_BOX();
echo close_body();

?>