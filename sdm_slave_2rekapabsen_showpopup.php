<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/sdm_2rekapabsen.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$karyawanid = $_GET['karyawanid'];
$namakaryawan = $_GET['namakaryawan'];
$tanggal = $_GET['tanggal'];
$notransaksi = $_GET['notransaksi'];
$namakaryawan = substr($namakaryawan, 0, -2);
$notransaksi = substr($notransaksi, 0, -2);
$qwe = explode('__', $notransaksi);
$qwe2 = explode('__', $namakaryawan);
foreach ($qwe2 as $kyu2) {
    $namakar .= $kyu2.' ';
}
if ('excel' != $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td>Karyawan</td>\r\n          <td>No. Transaksi</td>\r\n          <td>Tanggal</td>";
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
foreach ($qwe as $kyu) {
    $stream .= '<tr class=rowcontent>';
    $stream .= '<td align=left>'.$namakar.'</td>';
    $stream .= '<td align=left>'.$kyu.'</td>';
    $stream .= '<td align=center>'.$tanggal.'</td>';
    $stream .= '</tr>';
}
$stream .= '</tbody></table>';
echo $stream;

?>