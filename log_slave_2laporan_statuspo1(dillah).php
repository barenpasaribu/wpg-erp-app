<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

('' === $_POST['tgl1'] ? ($tgl1 = $_GET['tgl1']) : ($tgl1 = $_POST['tgl1']));
('' === $_POST['tgl2'] ? ($tgl2 = $_GET['tgl2']) : ($tgl2 = $_POST['tgl2']));
('' === $_POST['status1'] ? ($status1 = $_GET['status1']) : ($status1 = $_POST['status1']));
('' === $_POST['pt1'] ? ($pt1 = $_GET['pt1']) : ($pt1 = $_POST['pt1']));
('' === $_POST['terima1'] ? ($terima1 = $_GET['terima1']) : ($terima1 = $_POST['terima1']));
$tanggal1 = putertanggal($tgl1);
$tanggal2 = putertanggal($tgl2);
$brdr = 0;
$bgcoloraja = '';
if ('excel' === $proses) {
    $bgcoloraja = 'bgcolor=#DEDEDE ';
    $brdr = 1;
}

$sPo = 'select kodebarang, namabarang from '.$dbname.".log_5masterbarang\r\n        where 1";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $namabarang[$rPo['kodebarang']] = $rPo['namabarang'];
}
$sPo = 'select supplierid, namasupplier from '.$dbname.".log_5supplier\r\n        where 1";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $namasupplier[$rPo['supplierid']] = $rPo['namasupplier'];
}
$tab .= '<table cellspacing=1 cellpadding=1 border='.$brdr." class=sortable>\r\n    <thead class=rowheader>";
$tab .= '<tr>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['nopo'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['tanggal'].' PO</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['kodebarang'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['namabarang'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['matauang'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['kurs'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['harga'].' '.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jumlah'].' PO</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['harga'].' '.$_SESSION['lang']['total'].'</td>';
$tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['namasupplier'].'</td>';
$tab .= '<td '.$bgcoloraja.' colspan=5>'.$_SESSION['lang']['pembayaran'].'</td>';
$tab .= '<td '.$bgcoloraja.' colspan=4>'.$_SESSION['lang']['penerimaan'].'</td>';
$tab .= '</tr>';
$tab .= '<tr>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['syaratPem'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['tanggal'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['noinvoice'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['jatuhtempo'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['tanggalbayar'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['bapb'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['tanggal'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['jumlah'].'</td>';
$tab .= '<td '.$bgcoloraja.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '</tr>';
$sPo = 'select a.keterangan1, sum(a.jumlah*a.kurs) as jumlah, b.tanggal, b.cgttu from '.$dbname.".keu_kasbankdt a\r\n        left join ".$dbname.".keu_kasbankht b on b.notransaksi = a.notransaksi\r\n        where a.keterangan1 != '' group by a.keterangan1";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $databayar[$rPo['keterangan1']]['noinvoice'] = $rPo['keterangan1'];
    $databayar[$rPo['keterangan1']]['tanggal'] = $rPo['tanggal'];
    $databayar[$rPo['keterangan1']]['cgttu'] = $rPo['cgttu'];
}
$sPo = 'select notransaksi, kodebarang, satuan, jumlah, tanggal, kodegudang, nopo from '.$dbname.".log_transaksi_vw\r\n        where notransaksi like '%GR%' and nopo!= '' ";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $kunci = strtoupper($rPo['nopo']).$rPo['kodebarang'];
    $dataterima[$kunci]['notransaksi'] = $rPo['notransaksi'];
    $dataterima[$kunci]['tanggal'] = $rPo['tanggal'];
    $dataterima[$kunci]['jumlah'] += $rPo['jumlah'];
    $dataterima[$kunci]['satuan'] = $rPo['satuan'];
}
$sPo = 'select * from '.$dbname.".keu_tagihanht\r\n        where 1";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $datatagih[$rPo['nopo']]['nopo'] = $rPo['nopo'];
    $datatagih[$rPo['nopo']]['noinvoice'] = $rPo['noinvoice'];
    $datatagih[$rPo['nopo']]['jatuhtempo'] = $rPo['jatuhtempo'];
    $datatagih[$rPo['nopo']]['tanggal'] = $rPo['tanggal'];
}
$sPo = "select * from $dbname.log_po_vw where (1=1) ";
    if ($tanggal1!='' && $tanggal2!='') {
        $sPo.= " and tanggal between '".$tanggal1."' and '".$tanggal2."' ";
    }
    if ($status1!='') {
        $sPo.= "  and lokalpusat = $status1 ";
    }
if ($pt1!='') {
    $sPo.= "  and kodeorg like '%$pt1%' ";
}
if ($terima1!='') {
    $sPo.= "   and statuspo = $terima1 ";
}
$sPo.= " order by nopo, kodebarang";
$qPo = mysql_query($sPo);
while ($rPo = mysql_fetch_assoc($qPo)) {
    $kunci = strtoupper($rPo['nopo']).$rPo['kodebarang'];
    $data[$kunci]['kodebarang'] = $rPo['kodebarang'];
    $data[$kunci]['nopo'] = $rPo['nopo'];
    $data[$kunci]['tanggal'] = $rPo['tanggal'];
    $data[$kunci]['jumlahpesan'] = $rPo['jumlahpesan'];
    $data[$kunci]['satuan'] = $rPo['satuan'];
    $data[$kunci]['matauang'] = $rPo['matauang'];
    $data[$kunci]['kurs'] = $rPo['kurs'];
    $data[$kunci]['hargasatuan'] = $rPo['hargasatuan'];
    $data[$kunci]['totalharga'] = $rPo['hargasatuan'] * $rPo['jumlahpesan'];
    $data[$kunci]['kodesupplier'] = $rPo['kodesupplier'];
}
$tab .= '</thead><tbody>';
if (!empty($data)) {
    foreach ($data as $d) {
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$d['nopo'].'</td>';
        $tab .= '<td>'.putertanggal($d['tanggal']).'</td>';
        $tab .= '<td>'.$d['kodebarang'].'</td>';
        $tab .= '<td>'.$namabarang[$d['kodebarang']].'</td>';
        $tab .= '<td>'.$d['matauang'].'</td>';
        $tab .= '<td align=right>'.number_format($d['kurs']).'</td>';
        $tab .= '<td align=right>'.number_format($d['hargasatuan']).'</td>';
        $tab .= '<td align=right>'.number_format($d['jumlahpesan']).'</td>';
        $tab .= '<td>'.$d['satuan'].'</td>';
        $tab .= '<td align=right>'.number_format($d['totalharga']).'</td>';
        $tab .= '<td>'.$namasupplier[$d['kodesupplier']].'</td>';
        $tab .= '<td>'.$databayar[$datatagih[$d['nopo']]['noinvoice']]['cgttu'].'</td>';
        $tab .= '<td>'.putertanggal($datatagih[$d['nopo']]['tanggal']).'</td>';
        $tab .= '<td>'.$datatagih[$d['nopo']]['noinvoice'].'</td>';
        $tab .= '<td>'.putertanggal($datatagih[$d['nopo']]['jatuhtempo']).'</td>';
        $tab .= '<td>'.putertanggal($databayar[$datatagih[$d['nopo']]['noinvoice']]['tanggal']).'</td>';
        $kunci = $d['nopo'].$d['kodebarang'];
        $tab .= '<td>'.$dataterima[$kunci]['notransaksi'].'</td>';
        $tab .= '<td>'.putertanggal($dataterima[$kunci]['tanggal']).'</td>';
        $tab .= '<td align=right>'.number_format($dataterima[$kunci]['jumlah']).'</td>';
        $tab .= '<td>'.$dataterima[$kunci]['satuan'].'</td>';
        $tab .= '</tr>';
    }
} else {
    $tab .= '<tr class=rowcontent>';
    $tab .= '<td colspan=26>'.$_SESSION['lang']['dataempty'].'</td>';
    $tab .= '</tr>';
}

$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'daftrpo1_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";

        break;
    default:
        break;
}
function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);
    $asd = $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
    if ('' === $tanggal) {
        $asd = '';
    }

    return $asd;
}

?>