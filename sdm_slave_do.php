<style type="text/css">
<!--
.stylex {font-size: 9px; font-family: Arial, Helvetica, sans-serif; }
-->
</style>
<style>
table {
  border-collapse: collapse;
  border: 1px solid black;
}
</style>
<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
require_once 'lib/tglindo.php';

require_once 'lib/zLib.php';
include_once 'lib/zMysql.php';
include_once 'lib/terbilang.php';
include_once 'lib/spesialCharacter.php';

$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];

$alamatpabrik = makeOption($dbname, 'organisasi', 'kodeorganisasi,alamat');
$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$nmsupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$mtUang = makeOption($dbname, 'setup_matauang', 'kode,simbol');

$i = 'select * from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $_GET['column'] . '\' ';
($n = mysql_query($i)) || true;
$d = mysql_fetch_assoc($n);

if ($d['ppn'] == '0') {

	$ppn = 'tidak termasuk PPN 10%';

}

else {

	$ppn = 'termasuk PPN '.$d['ppn'].'% ';

}

$isiKualitas = explode(' ', $d['kualitas']);
$ffa = $isiKualitas[0];
$mi = $isiKualitas[0];

$tglTtd = explode('-', $d['tanggalkontrak']);

$nmBlnTtd = numToMonth($tglTtd[1], 'I', 'long');
$tglisiTtd = $tglTtd[2] . ' ' . date('F', strtotime($tglTtd[0])) . ' ' . $tglTtd[0];

$o = 'select * from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $d['koderekanan'] . '\'';
$p = mysql_query($o);
$q = mysql_fetch_assoc($p);


$x = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
$y = mysql_query($x);
$z = mysql_fetch_assoc($y);


$alamat=$q['alamat'];
$costumer=$q['namacustomer'];
$nomerkontrak=$d['nokontrak'];
$kwantitas=number_format($d['kuantitaskontrak']);
$satuan=$d['satuan'];
$namaorganisasi=$z['namaorganisasi'];
$nodo=$d['nodo'];
$pabrik=$d['kodept']."M";
$penyerahan=tgl_indo($d['tanggalkirim'])." - ".tgl_indo($d['sdtanggal']);
$alamatnya=$alamatpabrik[$pabrik];
$transporter=$nmsupp[$d['transporter']];
$nominal=terbilang($d['kuantitaskontrak'],1)." Kilogram";
$tanggal=tgl_indo(date("Y-m-d"));

    $stream .= "<table>";    
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3 height=50><u><strong>$namaorganisasi</strong></u></td>";
    $stream .= "<td align=Right colspan=3><u><strong>No. DO :</strong> $nodo</u></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=center colspan=6><u><strong>D E L I V E R Y  &nbsp;&nbsp;&nbsp;O R D E R</strong></u></td>";  
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=center colspan=6>ORDER PEMBELIAN</td>";  
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3>Kepada Yth.</td>";
    $stream .= "<td align=lEFT colspan=3>Untuk diserahkan kepada :</td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3><b>$namaorganisasi</b></td>";
    $stream .= "<td align=lEFT colspan=3><b>$costumer</b></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3>Pabrik Minyak Kelapa Sawit</td>";
    $stream .= "<td align=lEFT colspan=3></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3>$alamatnya</td>";
    $stream .= "<td align=lEFT colspan=3></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=lEFT colspan=6></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=6>UP. : <u> Bapak Mill Manager </u></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=6></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=6>";
    $stream .= "<table border=1 cellspacing=0 cellpadding=5 >";
    $stream .= "<tr>";
    $stream .= "<td colspan=6>";    
            $stream .= "<table>";    
            $stream .= "<tr>";
            $stream .= "<td colspan=6>Dengan hormat,</td>"; 
            $stream .= "</tr>";

            $stream .= "<tr>";
            $stream .= "<td colspan=6>Kepada pembawa D/O ini, kami harap Bapak dapat memberikan barang-barang berikut ini</td>"; 
            $stream .= "</tr>";
            $stream .= "<tr>";
            $stream .= "<td colspan=6>dalam keadaan baik dan cukup.</td>"; 
            $stream .= "</tr>";


            $stream .= "<tr>";
            $stream .= "<td>Nomor Kontrak</td>";
            $stream .= "<td>:</td>";
            $stream .= "<td colspan=4> <u> $nomerkontrak </u> </td>"; 
            $stream .= "</tr>";
            
            $stream .= "<tr>";
            $stream .= "<td>Quantity (Kg)</td>";
            $stream .= "<td>:</td>";
            $stream .= "<td colspan=4> <u> $kwantitas ( $nominal ) </u> </td>"; 
            $stream .= "</tr>";

            $stream .= "<tr>";
            $stream .= "<td>Jadwal Penyerahan</td>";
            $stream .= "<td>:</td>";
            $stream .= "<td colspan=4> <u> $penyerahan </u> </td>"; 
            $stream .= "</tr>";

            $stream .= "<tr>";
            $stream .= "<td>Masa Berlaku D/O</td>";
            $stream .= "<td>:</td>";
            $stream .= "<td colspan=4> <u> $penyerahan </u> </td>"; 
            $stream .= "</tr>";

            $stream .= "<tr>";
            $stream .= "<td>Nama Pengangkutan</td>";
            $stream .= "<td>:</td>";
            $stream .= "<td colspan=4> <u> $transporter </u> </td>"; 
            $stream .= "</tr>";

            $stream .= "<tr>";
            $stream .= "<td colspan=6>Atas perhatiannya diucapkan terima kasih.</td>"; 
            $stream .= "</tr>";

            $stream .= "</table>";
    $stream .= "</td>";
    $stream .= "</tr>";
    $stream .= "</table>";

    $stream .= "</td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=6></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3 rowspan=4>";

//    $stream .= "<table border=1 cellspacing=0 cellpadding=2 >";
//    $stream .= "<tr>";
//    $stream .= "<td colspan=2>";    
        $stream .= "<table>";
        $stream .= "<tr>";
        $stream .= "<td colspan=3 align=center><span class=stylex><strong><u> PERHATIAN : </u></strong></span></td>";
        $stream .= "</tr>";
        $stream .= "<tr>";
        $stream .= "<td colspan=3><span class=stylex>1. Segala kekurangan atau kerusakan barang yang keluar</span></td>";
        $stream .= "</tr>";
        $stream .= "<tr>";
        $stream .= "<td colspan=3><span class=stylex>dari gudang menjadi tanggungan penerima barang.</span></td>";
        $stream .= "</tr>";
        $stream .= "<tr>";
        $stream .= "<td colspan=3><span class=stylex>2. Segala resiko atas kehilangan D/O ini tidak ditanggung.</span></td>";
        $stream .= "</tr>";
        $stream .= "</table>";
//    $stream .= "</td>";
//    $stream .= "</tr>";
//    $stream .= "</table>";
    $stream .= "</td>";
    $stream .= "<td align=left>Diterbitkan di </td>";
    $stream .= "<td align=lEFT colspan=2>: <u> Pekanbaru <u></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=left>Tanggal </td>";
    $stream .= "<td align=lEFT colspan=2>: <u>$tanggal<u></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=center>Dibuat Oleh :</td>";
    $stream .= "<td align=center colspan=2>Disetujui Oleh :</td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Right></td>";
    $stream .= "<td align=lEFT colspan=2></td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=6 height=20>&nbsp</td>";
    $stream .= "</tr>";
    $stream .= "<tr>";
    $stream .= "<td align=Left colspan=3></td>";
    $stream .= "<td align=center>(_______________)</td>";
    $stream .= "<td align=center colspan=2>(_______________)</td>";
    $stream .= "</tr>";


//echo $stream;


$wktu = date('Hms');
$nop_ = 'DO_Penjualan'.$wktu.'__'.date('Y');

    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');

    gzwrite($gztralala, $stream);

    gzclose($gztralala);

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";

?>