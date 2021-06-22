<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$periode = $_GET['periode'];
$optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$stry = 'select nomor,nama,hubungankeluarga,tanggungan from '.$dbname.'.sdm_karyawankeluarga';
$res = mysql_query($stry);
while ($bar = mysql_fetch_object($res)) {
    $nama[$bar->nomor] = $bar->nama;
    $hubungan[$bar->nomor] = $bar->hubungankeluarga;
    $tanggungan[$bar->nomor] = $bar->tanggungan;
}
$stream = 'Laporan Rekap Pengobatan Periode '.$periode."\r\n    <table border=1>\r\n    <thead>\r\n    <tr>\r\n        <td bgcolor=#dedede rowspan=2>No</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['lokasitugas']."</td>            \r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['tanggal']."</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td bgcolor=#dedede colspan=3 align=center>".$_SESSION['lang']['dibayar']."</td>  \r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['total']."</td>     \r\n        <td bgcolor=#dedede rowspan=2>".$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    <tr>\r\n            <td bgcolor=#dedede>".$_SESSION['lang']['internal']."</td>\r\n            <td bgcolor=#dedede>Providers</td>\r\n            <td bgcolor=#dedede>".$_SESSION['lang']['klaim']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
$str = 'select a.*,b.namars,c.namakaryawan,c.lokasitugas,c.kodejabatan from '.$dbname.'.sdm_pengobatanht a left join '.$dbname.".sdm_5rs b on a.rs=b.id\r\n    left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid where periode='".$periode."'";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pasien = '';
    if ('0' != $bar->ygsakit) {
        $pasien = $nama[$bar->ygsakit];
    } else {
        $pasien = $bar->namakaryawan;
    }

    if (0 == $bar->klaimoleh) {
        $claim = $bar->jlhbayar;
        $tclaim += $claim;
    }

    if (1 == $bar->klaimoleh) {
        $prov = $bar->jlhbayar;
        $tprov += $prov;
    }

    if (2 == $bar->klaimoleh) {
        $int = $bar->jlhbayar;
        $tint += $int;
    }

    $stream .= "<tr>\r\n        <td>".$no."</td>\r\n        <td>".$bar->namakaryawan."</td>\r\n        <td>".$bar->lokasitugas."</td>\r\n        <td>".tanggalnormal($bar->tanggal)."</td>\r\n        <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n        <td>".$pasien.'['.$hubungan[$bar->ygsakit]."]</td>\r\n        <td>".$bar->namars."</td>\r\n        <td align=right>".number_format($bar->totalklaim, 0)."</td>\r\n            \r\n        <td align=right>".number_format($int, 0)."</td>    \r\n         <td align=right>".number_format($prov, 0)."</td>   \r\n        <td align=right>".number_format($claim, 0)."</td>\r\n        <td align=right>".number_format($bar->jlhbayar, 0)."</td>    \r\n        \r\n       <td>".$bar->keterangan."</td>\r\n    </tr>";
    $tklaim += $bar->totalklaim;
    $tbayar += $bar->jlhbayar;
}
$stream .= "<tr>\r\n        <td colspan=7>TOTAL</td>\r\n        <td align=right>".number_format($tklaim, 0)."</td>\r\n            \r\n        <td align=right>".number_format($tint, 0)."</td>    \r\n         <td align=right>".number_format($tprov, 0)."</td>   \r\n        <td align=right>".number_format($tclaim, 0)."</td>\r\n        <td align=right>".number_format($tbayar, 0)."</td>    \r\n        \r\n       <td></td>\r\n    </tr>";
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
$nop_ = 'LaporanRekapPengobatan-'.$periode;
if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
                @unlink('tempExcel/'.$file);
            }
        }
        closedir($handle);
    }

    $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
    if (!fwrite($handle, $stream)) {
        echo "<script language=javascript1.2>\r\n            parent.window.alert('Cant convert to excel format');\r\n            </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls';\r\n            </script>";
    closedir($handle);
}

?>