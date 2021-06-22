<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
echo "<link rel=stylesheet type='text/css' href='style/generic.css'>\r\n";
$noakun = $_GET['noakun'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$lmperiode = $_GET['lmperiode'];
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$revisi = $_GET['revisi'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$gudang."'";
$namagudang = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namagudang = strtoupper($bar->namaorganisasi);
}
if ('' === $gudang && '' === $pt) {
    $str = "select nojurnal,jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok,kodebarang \r\n        from ".$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."' and noakun='".$noakun."' and revisi <= '".$revisi."'";
} else {
    if ('' === $gudang && '' !== $pt) {
        $str = "select nojurnal,jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok,kodebarang \r\n        from ".$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg in(select kodeorganisasi \r\n        from ".$dbname.".organisasi where induk='".$pt."' and length(kodeorganisasi)=4)\r\n        and noakun='".$noakun."' and revisi <= '".$revisi."'";
    } else {
        $str = "select nojurnal,jumlah,keterangan,tanggal,noreferensi,kodevhc,nik,kodeblok,kodebarang \r\n        from ".$dbname.".keu_jurnaldt_vw\r\n        where periode>='".$periode."' and periode<='".$periode1."' and kodeorg ='".$gudang."'\r\n        and noakun='".$noakun."' and revisi <= '".$revisi."'";
    }
}

echo "<fieldset><legend>Print Excel</legend>\r\n     <img onclick=\"parent.detailKeExcel(event,'keu_slave_getBBDetail.php?type=excel&noakun=".$noakun.'&periode='.$periode.'&periode1='.$periode1.'&lmperiode='.$lmperiode.'&pt='.$pt.'&gudang='.$gudang.'&revisi='.$revisi."')\" src=images/excel.jpg class=resicon title='MS.Excel'>\r\n     </fieldset>";
if ('excel' === $_GET['type']) {
    $border = 1;
} else {
    $border = 0;
}

$stream = '<table class=sortable border='.$border." cellspacing=1>\r\n    <thead>\r\n    <tr class=rowcontent>\r\n        <td>No</td>\r\n        <td>No.Transaksi</td>\r\n        <td>Tanggal</td>\r\n        <td>No.Akun</td>\r\n        <td>Keterangan</td>\r\n        <td>Debet</td>\r\n        <td>Kredit</td>\r\n        <td>Karyawan</td>\r\n        <td>Mesin</td>\r\n        <td>Blok</td><td>Barang</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
$res = mysql_query($str);
$no = 0;
$tdebet = 0;
$tkredit = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $debet = 0;
    $kredit = 0;
    if (0 < $bar->jumlah) {
        $debet = $bar->jumlah;
    } else {
        $kredit = $bar->jumlah * -1;
    }

    $noref = $bar->noreferensi;
    if ('' === trim($noref)) {
        $noref = $bar->nojurnal;
    }

    if ('excel' === $_GET['type']) {
        $tampiltanggal = $bar->tanggal;
    } else {
        $tampiltanggal = tanggalnormal($bar->tanggal);
    }

    $stream .= "<tr class=rowcontent>\r\n           <td>".$no."</td>\r\n           <td>".$noref."</td>               \r\n           <td>".$tampiltanggal."</td>    \r\n           <td>".$noakun."</td>    \r\n           <td>".$bar->keterangan."</td>\r\n           <td align=right>".number_format($debet,2)."</td>\r\n           <td align=right>".number_format($kredit,2)."</td>  \r\n           <td align=right>".$bar->karyawanid."</td>\r\n           <td align=right>".$bar->kodevhc."</td>\r\n           <td align=right>".$bar->kodeblok."</td><td align=right>".$bar->kodebarang."</td>  \r\n        </tr>";
    $tdebet += $debet;
    $tkredit += $kredit;
}
$stream .= "<tr class=rowcontent>\r\n    <td colspan=5>TOTAL</td>\r\n    <td align=right>".number_format($tdebet,2)."</td>\r\n    <td align=right>".number_format($tkredit,2)."</td>\r\n    <td></td>\r\n    <td></td>\r\n    <td></td>\r\n</tr>";
$stream .= '</tbody><tfoot></tfoot></table>';
if ('excel' === $_GET['type']) {
    $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
    $nop_ = 'Detail_jurnal_'.$_GET['gudang'].'_'.$_GET['periode'];
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