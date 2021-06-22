<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$periode = $_GET['periode'];
$kodeorg = $_GET['kodeorg'];
if ('' == $periode) {
    $periode = date('Y');
}

$str3 = 'select  sum(a.jlhbayar) as klaim,a.periode,a.kodebiaya,c.nama from '.$dbname.".sdm_pengobatanht a \r\n        left join ".$dbname.".sdm_5jenisbiayapengobatan c\r\n        on a.kodebiaya=c.kode\r\n        left join ".$dbname.".datakaryawan b \r\n        on a.karyawanid=b.karyawanid\r\n              where a.periode like '".$periode."%'\r\n              and b.lokasitugas like '".$kodeorg."%'\r\n        group by kodebiaya,periode order by periode\r\n    ";
$res3 = mysql_query($str3);
$no = 0;
while ($bar3 = mysql_fetch_object($res3)) {
    $kode[$bar3->kodebiaya][$bar3->periode] = $bar3->klaim;
    $kodex[$bar3->kodebiaya]['nama'] = $bar3->nama;
}
$stream .= "Biaya Pengobatan per jenis perawatan\r\n     <table border=1>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>No</td>\r\n        <td>".$_SESSION['lang']['kodeorg']."</td>\r\n        <td>".$_SESSION['lang']['tahun']."</td>            \r\n        <td>Treatment Type</td>\r\n        <td  align=center>Jan</td>\r\n        <td  align=center>Feb</td>\r\n        <td  align=center>Mar</td>\r\n        <td  align=center>Apr</td>\r\n        <td  align=center>Mei</td>\r\n        <td  align=center>Jun</td>\r\n        <td  align=center>Jul</td>\r\n        <td  align=center>Aug</td>\r\n        <td  align=center>Sep</td>\r\n        <td  align=center>Oct</td>\r\n        <td  align=center>Nov</td>\r\n        <td  align=center>Dec</td>\r\n        <td>".$_SESSION['lang']['total']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
foreach ($kodex as $key => $val) {
    ++$no;
    $total = $kode[$key][$periode.'-12'] + $kode[$key][$periode.'-11'] + $kode[$key][$periode.'-10'] + $kode[$key][$periode.'-09'] + $kode[$key][$periode.'-08'] + $kode[$key][$periode.'-07'] + $kode[$key][$periode.'-06'] + $kode[$key][$periode.'-05'] + $kode[$key][$periode.'-04'] + $kode[$key][$periode.'-03'] + $kode[$key][$periode.'-02'] + $kode[$key][$periode.'-01'];
    $gt += $total;
    $stream .= "<tr>\r\n            <td>".$no."</td>\r\n            <td>".$kodeorg."</td>\r\n            <td>".$periode."</td>    \r\n            <td>".$kodex[$key]['nama']."</td>                \r\n            <td align=right>".number_format($kode[$key][$periode.'-01'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-02'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-03'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-04'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-05'])."</td> \r\n            <td align=right>".number_format($kode[$key][$periode.'-06'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-07'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-08'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-09'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-10'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-11'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-12'])."</td>\r\n            <td align=right>".number_format($total)."</td>    \r\n        </tr>";
    $t01 += $kode[$key][$periode.'-01'];
    $t02 += $kode[$key][$periode.'-02'];
    $t03 += $kode[$key][$periode.'-03'];
    $t04 += $kode[$key][$periode.'-04'];
    $t05 += $kode[$key][$periode.'-05'];
    $t06 += $kode[$key][$periode.'-06'];
    $t07 += $kode[$key][$periode.'-07'];
    $t08 += $kode[$key][$periode.'-08'];
    $t09 += $kode[$key][$periode.'-09'];
    $t10 += $kode[$key][$periode.'-10'];
    $t11 += $kode[$key][$periode.'-11'];
    $t12 += $kode[$key][$periode.'-12'];
}
$stream .= "<tr class=rowcontent>\r\n            <td colspan=4>Total</td>                \r\n            <td align=right>".number_format($t01)."</td>\r\n            <td align=right>".number_format($t02)."</td>\r\n            <td align=right>".number_format($t03)."</td>\r\n             <td align=right>".number_format($t04)."</td>\r\n             <td align=right>".number_format($t05)."</td>\r\n             <td align=right>".number_format($t06)."</td>\r\n             <td align=right>".number_format($t07)."</td>\r\n             <td align=right>".number_format($t08)."</td>\r\n             <td align=right>".number_format($t09)."</td>\r\n             <td align=right>".number_format($t10)."</td>\r\n             <td align=right>".number_format($t11)."</td>\r\n             <td align=right>".number_format($t12)."</td>     \r\n            <td align=right>".number_format($gt)."</td>    \r\n        </tr>";
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table></div>\r\n    </fieldset>";
$nop_ = 'Biaya pengobatan Per jenis Pengobatan-'.$periode.$kodeorg;
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Cant convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>