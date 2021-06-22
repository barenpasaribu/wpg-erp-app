<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/pabrik_2pengolahan.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$kodeorg = $_GET['kodeorg'];
$tanggal = $_GET['tanggal'];
$barang = $_GET['barang'];
if ('excel' !== $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
} else {
    $stream = '<table class=sortable border=1 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>No</td>\r\n          <td>".$_SESSION['lang']['tanggal']."</td>\r\n          <td>".$_SESSION['lang']['NoKontrak']."</td>\r\n          <td>".$_SESSION['lang']['material']."</td>\r\n          <td>".$_SESSION['lang']['kuantitas']."</td>\r\n          <td>".$_SESSION['lang']['kendaraan']."</td>\r\n          <td>".$_SESSION['lang']['Pembeli'].'</td>';
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
$str = 'select nokontrak, koderekanan from '.$dbname.'.pmn_kontrakjual';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kontrak[$bar->nokontrak] = $bar->koderekanan;
}
$str = 'select kodecustomer,namacustomer from '.$dbname.'.pmn_4customer';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $kustom[$bar->kodecustomer] = $bar->namacustomer;
}
$str = 'select tanggal, nokontrak, kodebarang, beratbersih, nokendaraan from '.$dbname.".pabrik_timbangan\r\n              where millcode = '".$kodeorg."' and tanggal like '".$tanggal."%' and kodebarang = '".$barang."'";
$res = mysql_query($str);
$no = 0;
$total = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ('40000001' === $bar->kodebarang) {
        $barang = 'CPO';
    }

    if ('40000002' === $bar->kodebarang) {
        $barang = 'Kernel';
    }

    $total += $bar->beratbersih;
    $stream .= "<tr class=rowcontent>\r\n           <td align=right>".$no."</td>\r\n           <td align=left>".$bar->tanggal."</td>    \r\n           <td align=left>".$bar->nokontrak."</td>               \r\n           <td align=left>".$barang."</td>               \r\n           <td align=right>".number_format($bar->beratbersih, 0)."</td>               \r\n           <td align=left>".$bar->nokendaraan."</td>               \r\n           <td align=left>".$kustom[$kontrak[$bar->nokontrak]].'</td>';
    $stream .= '</tr>';
}
$stream .= "<tr class=rowcontent>\r\n           <td align=center colspan=4>Total</td>               \r\n           <td align=right>".number_format($total, 0)."</td>               \r\n           <td align=center colspan=2></td>";
$stream .= '</tr>';
$stream .= '</tbody></table>';
echo $stream;

?>