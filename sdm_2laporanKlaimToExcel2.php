<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$optNmRwt = makeOption($dbname, 'sdm_5jenisbiayapengobatan', 'kode,nama');
$periode = $_GET['periode'];
$kodeorg = $_GET['kodeorg'];
if ('' == $periode) {
    $periode = date('Y');
}

$str2 = "select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,\r\n    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya\r\n    from ".$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".datakaryawan d\r\n\t  on a.karyawanid=d.karyawanid \r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n\t  where a.periode like '".$periode."%'\r\n\t  and e.lokasitugas='".$kodeorg."'\r\n        group by a.karyawanid,kodebiaya order by klaim desc";
$res2 = mysql_query($str2);
while ($bar2 = mysql_fetch_object($res2)) {
    $kdBiaya[$bar2->kodebiaya] = $bar2->kodebiaya;
    $idKary[$bar2->karyawanid] = $bar2->karyawanid;
    $jmlhRp[$bar2->karyawanid.$bar2->kodebiaya] = $bar2->klaim;
    $umurKary[$bar2->karyawanid] = $bar2->umur;
    $kdGol[$bar2->karyawanid] = $bar2->kodegolongan;
    $nmKary[$bar2->karyawanid] = $bar2->namakaryawan;
    $lksiKary[$bar2->karyawanid] = $bar2->lokasitugas;
}
$stream = 'Laporan Ranking Biaya/Karyawan '.$periode.' '.$kodeorg."\r\n<table border=1>\r\n<thead>\r\n<tr>\r\n    <td bgcolor=#dedede>Rank</td>\r\n    <td bgcolor=#dedede>".$_SESSION['lang']['namakaryawan']."</td>\r\n    <td bgcolor=#dedede>".$_SESSION['lang']['kodegolongan']."</td>\r\n    <td bgcolor=#dedede>".$_SESSION['lang']['umur']." (yrs)</td>    \r\n    <td bgcolor=#dedede>".$_SESSION['lang']['lokasitugas'].'</td>';
foreach ($kdBiaya as $lsBy) {
    $stream .= '<td bgcolor=#dedede>'.$optNmRwt[$lsBy].'</td>';
}
$stream .= '<td bgcolor=#dedede>'.$_SESSION['lang']['total']."</td>\r\n</tr>\r\n</thead>\r\n<tbody>";
$res2 = mysql_query($str2);
$no = 0;
foreach ($idKary as $lstKary) {
    ++$no;
    $stream .= "<tr class=rowcontent>\r\n            <td>".$no.'</td>';
    $stream .= '<td>'.$nmKary[$lstKary]."</td>\r\n         <td>".$kdGol[$lstKary]."</td>\r\n            <td align=right>".$umurKary[$lstKary]."</td>\r\n            <td>".$lksiKary[$lstKary].'</td>';
    foreach ($kdBiaya as $lsBy) {
        $stream .= '<td align=right>'.number_format($jmlhRp[$lstKary.$lsBy]).'</td>';
        $total[$lstKary] += $jmlhRp[$lstKary.$lsBy];
        $totPerBy[$lsBy] += $jmlhRp[$lstKary.$lsBy];
    }
    $stream .= '<td>'.$_SESSION['lang']['total'].'</td>';
    $stream .= '</tr>';
}
$stream .= "<tr class=rowcontent>\r\n              <td></td>\r\n               <td colspan=3 align=right>".$_SESSION['lang']['total'].'</td>';
foreach ($kdBiaya as $lsBy) {
    $stream .= '<td align=right>'.number_format($totPerBy[$lsBy]).'</td>';
    $totBy += $totPerBy[$lsBy];
}
$stream .= '<td>'.number_format($totBy).'</td>';
$stream .= "</tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
$nop_ = 'LaporanRankingBiayaperKaryawan-'.$periode.$kodeorg;
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