<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "<script language=javascript1.2 src=\"js/generic.js\"></script>\r\n<script language=javascript1.2 src=\"js/kebun_2pemeliharaan.js\"></script>\r\n<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$kodekegiatan = $_GET['kodekegiatan'];
$kodeorg = $_GET['kodeorg'];
$bulan = $_GET['bulan'];
$type = $_GET['type'];
$str = "select kodekegiatan, namakegiatan, satuan\r\n        from ".$dbname.".setup_kegiatan where kodekegiatan='".$kodekegiatan."'\r\n        ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namaKeg = $bar->namakegiatan;
    $satuKeg = $bar->satuan;
}
$str = "select kodebarang, namabarang, satuan\r\n        from ".$dbname.".log_5masterbarang\r\n        ";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namabarang[$bar->kodebarang] = $bar->namabarang;
    $satuanbarang[$bar->kodebarang] = $bar->satuan;
}
if ('excel' !== $_GET['type']) {
    $stream = '<table class=sortable border=0 cellspacing=1>';
} else {
    $stream = '<table class=sortable border=1 cellspacing=1>';
}

$stream .= "\r\n      <thead>\r\n        <tr class=rowcontent>\r\n          <td align=center>".$_SESSION['lang']['notransaksi']."</td>\r\n          <td align=center>".$_SESSION['lang']['namakegiatan']."</td>\r\n          <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n          <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n          <td align=center>".$_SESSION['lang']['jhk']."</td>\r\n          <td align=center>".$_SESSION['lang']['hasilkerjad']."</td>\r\n          <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n          <td align=center>Output</td>\r\n          <td align=center>".$_SESSION['lang']['namabarang']."</td>\r\n          <td align=center>".$_SESSION['lang']['jumlah']."</td>\r\n          <td align=center>".$_SESSION['lang']['satuan']."</td>\r\n          ";
$stream .= "</tr>\r\n      </thead>\r\n      <tbody>";
$str = "select a.notransaksi, a.hasilkerja, a.jumlahhk, a.tanggal, a.jumlahhk, a.hasilkerja,\r\n        b.kodebarang, b.kwantitas\r\n        from ".$dbname.".kebun_perawatan_vw a\r\n        left join ".$dbname.".kebun_pakaimaterial b on a.notransaksi=b.notransaksi\r\n        where a.kodekegiatan = '".$kodekegiatan."' and a.kodeorg = '".$kodeorg."' and a.tanggal like '".$bulan."%'";
$notem = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $qwebar = '';
    if ($bar->kwantitas) {
        $qwebar = number_format($bar->kwantitas, 3);
    }

    if ($notem !== $bar->notransaksi) {
        $notem = $bar->notransaksi;
        $oput = $bar->hasilkerja / $bar->jumlahhk;
        $stream .= "<tr class=rowcontent>\r\n                <td>".$bar->notransaksi."</td>    \r\n                <td>".$namaKeg."</td>     \r\n                <td>".$kodeorg."</td>    \r\n                <td>".tanggalnormal($bar->tanggal)."</td>    \r\n                <td align=right>".number_format($bar->jumlahhk, 2)."</td>    \r\n                <td align=right>".number_format($bar->hasilkerja, 2)."</td>    \r\n                <td>".$satuKeg."</td>    \r\n                <td align=right>".number_format($oput, 2).' '.$satuKeg."/HK</td> \r\n                <td>".$namabarang[$bar->kodebarang]."</td>    \r\n                <td align=right>".$qwebar."</td>    \r\n                <td>".$satuanbarang[$bar->kodebarang].'</td>';
        $jumlahhk += $bar->jumlahhk;
        $hasilkerja += $bar->hasilkerja;
    } else {
        $stream .= "<tr class=rowcontent>\r\n                <td></td>    \r\n                <td></td>    \r\n                <td></td>    \r\n                <td></td>    \r\n                <td align=right></td>    \r\n                <td align=right></td>    \r\n                <td></td>    \r\n                <td align=right></td>\r\n                <td>".$namabarang[$bar->kodebarang]."</td>    \r\n                <td align=right>".$qwebar."</td>    \r\n                <td>".$satuanbarang[$bar->kodebarang].'</td>';
    }

    $stream .= '</tr>';
}
$oput = $hasilkerja / $jumlahhk;
$stream .= "<tr class=rowcontent>\r\n            <td colspan=4 align=center>Total</td>    \r\n            <td align=right>".number_format($jumlahhk, 2)."</td>    \r\n            <td align=right>".number_format($hasilkerja, 2)."</td>    \r\n            <td>".$satuKeg."</td>    \r\n            <td align=right>".number_format($oput, 2).' '.$satuKeg."/HK</td>    \r\n            <td colspan=3></td>    \r\n        </tr>";
$stream .= '</tbody></table>';
if ('excel' === $_GET['type']) {
    $nop_ = 'Detail_pemeliharaan1_'.$kodekegiatan.'_'.$kodeorg.'_'.$bulan;
    if (0 < strlen($stream)) {
        if ($handle = opendir('tempExcel')) {
            while (false !== ($file = readdir($handle))) {
                if ('.' !== $file && '..' !== $file) {
                    @unlink('tempExcel/'.$file);
                }
            }
            closedir($handle);
        }

        $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
        if (!fwrite($handle, $stream)) {
            echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
            exit();
        }

        echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
        closedir($handle);
    }
} else {
    echo $stream;
}

?>