<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$periode = $_GET['periode'];
$kodeorg = $_GET['kodeorg'];
$rs = $_GET['rs'];
$optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
if ('' == $periode) {
    $periode = date('Y');
}

$str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama from '.$dbname.".sdm_pengobatanht a left join\r\n    ".$dbname.".sdm_5rs b on a.rs=b.id \r\n    left join ".$dbname.".sdm_5diagnosa d\r\n    on a.diagnosa=d.id\r\n    left join ".$dbname.".datakaryawan c\r\n    on a.karyawanid=c.karyawanid\r\n        left join ".$dbname.".sdm_karyawankeluarga f\r\n        on a.ygsakit=f.nomor\r\n    where a.periode like '".$periode."%'\r\n    and c.lokasitugas like '".$kodeorg."%'\r\n    and b.namars like '".$rs."%'\r\n    order by a.updatetime desc, a.tanggal desc";
$stream = 'Laporan Klaim Pengobatan Periode '.$periode.' '.$kodeorg." \r\n    <table border=1>\r\n    <thead>\r\n    <tr>\r\n        <td bgcolor=#dedede>No</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['periode']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['tanggal']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['pasien']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['dibayar']."</td>\r\n         <td bgcolor=#dedede>".$_SESSION['lang']['perusahaan']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['karyawan']."</td>\r\n        <td bgcolor=#dedede>Jamsostek</td>            \r\n        <td bgcolor=#dedede>".$_SESSION['lang']['diagnosa']."</td>\r\n        <td bgcolor=#dedede>".$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pasien = '';
    $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n          where nomor=".$bar->ygsakit;
    $resu = mysql_query($stru);
    while ($baru = mysql_fetch_object($resu)) {
        $pasien = $baru->hubungankeluarga;
    }
    if ('' == $pasien) {
        $pasien = 'AsIs';
    }

    $stream .= "<tr>\r\n        <td>".$no."</td>\r\n        <td>".$bar->notransaksi."</td>\r\n        <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n        <td>".tanggalnormal($bar->tanggal)."</td>\r\n        <td>".$bar->loktug."</td>\r\n        <td>".$bar->namakaryawan."</td>\r\n        <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n        <td>".$pasien."</td>\r\n        <td>".$bar->nama."</td>\r\n        <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n        <td>".$bar->kodebiaya."</td>\r\n        <td align=right>".$bar->totalklaim."</td>\r\n        <td align=right>".$bar->jlhbayar."</td>\r\n        <td align=right>".$bar->bebanperusahaan."</td>\r\n        <td align=right>".$bar->bebankaryawan."</td>\r\n        <td align=right>".$bar->bebanjamsostek."</td>            \r\n        <td>".$bar->ketdiag."</td>\r\n        <td>".$bar->keterangan."</td>\r\n    </tr>";
}
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
$nop_ = 'LaporanKlaimPengobatan-'.$periode.$kodeorg.'_'.$method.'_';
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