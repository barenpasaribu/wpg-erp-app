<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
$periode = $_GET['periode'];
$tipe = $_GET['tipe'];
$username = $_GET['username'];
if ('' == $username) {
    $username = $_SESSION['standard']['username'];
} else {
    $username = $username;
}

$str = 'select id,name from '.$dbname.'.sdm_ho_component order by id';
$res = mysql_query($str);
$head = 'Payroll Periode: '.$periode.', Operator: '.$periode."\r\n        <table border=1>\r\n       <thead><tr>\r\n\t   \t<td class=firsttd>No.</td>\r\n\t\t<td align=center>No.Karyawan</td>\r\n\t\t<td align=center>Nama.Karyawan</td>\r\n\t\t<td align=center>Dept.</td>\r\n\t\t<td align=center>Likasi.Tugas</td>\r\n\t\t<td align=center>Periode<br>Gaji</td>\r\n\t\t<td align=center>Bank</td>\r\n\t\t<td align=center>Bank.A/C</td>\r\n\t\t<td align=center>Total T.H.P<br>(Rp.)</td>";
$arrUrut = [];
$arrVal = [];
while ($bar = mysql_fetch_object($res)) {
    array_push($arrUrut, $bar->id);
    $head .= '<td align=center>'.str_replace(' ', '.', $bar->name).'</td>';
}
$head .= '</tr></thead><tbody>';
$str1 = 'select distinct e.karyawanid,e.name,e.bank,e.bankaccount from '.$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji m\r\n       where e.operator='".$username."'\r\n\t   and e.karyawanid=m.karyawanid and periodegaji='".$periode."' and e.karyawanid not in (0999999999,0888888888)\r\n       order by e.name";
$res1 = mysql_query($str1);
$no = 0;
$grandTotal = 0;
while ($bar1 = mysql_fetch_array($res1)) {
    ++$no;
    $total = 0;
    $stg = 'select a.lokasitugas,a.bagian from '.$dbname.".datakaryawan a\r\n         where karyawanid=".$bar1[0];
    $reg = mysql_query($stg, $conn);
    $emplloc = '';
    $dept = '';
    while ($barg = mysql_fetch_object($reg)) {
        $dept = $barg->bagian;
        $emplloc = $barg->lokasitugas;
    }
    for ($z = 0; $z < count($arrUrut); ++$z) {
        $arrVal[$z] = 0;
    }
    $str2 = 'select a.idkomponen,a.jumlah,b.id as komponen, case b.plus when 0 then -1 else b.plus end as pengali,b.name as nakomp from '.$dbname.'.sdm_gaji a, '.$dbname.'.sdm_ho_component b where a.idkomponen=b.id and a.karyawanid='.$bar1[0]." and a.periodegaji='".$periode."' order by a.idkomponen";
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        if (1 != $bar2->pengali) {
            $jumlah = -1 * $bar2->jumlah;
        } else {
            $jumlah = $bar2->jumlah;
        }

        for ($z = 0; $z < count($arrUrut); ++$z) {
            if ($arrUrut[$z] == $bar2->idkomponen) {
                $arrVal[$z] = $jumlah;
            }
        }
        $total += $jumlah;
    }
    $head .= "<tr>\r\n        <td class=firsttd>".$no."</td>\r\n\t\t<td>'".$bar1[0]."</td>\r\n\t\t<td>".$bar1[1]."</td>\r\n\t\t<td>".$dept."</td>\r\n\t\t<td>".$emplloc."</td>\r\n\t\t<td align=center>".substr($periode, 5, 2).'-'.substr($periode, 0, 4)."</td>\r\n\t\t<td>".$bar1[2]."</td>\r\n\t\t<td>'".$bar1[3]."</td>\r\n\t\t<td align=right><b>".number_format($total, 2, '.', '').'</b></td>';
    for ($c = 0; $c < count($arrVal); ++$c) {
        $head .= '<td align=right>'.number_format($arrVal[$c], 2, '.', '').'</td>';
    }
    $head .= '</tr>';
    $grandTotal += $total;
}
$head .= "</tbody><tfoot>\r\n        <tr><td colspan=8>Grand Total</td>\r\n\t\t<td align=right>".number_format($grandTotal, 2, '.', '')."</td>\r\n\t\t<td clspan=".count($arrVal)."></td>\r\n\t\t</tfoot></table>";
$stream = $head;
$nop_ = 'payroll_'.$periode.'_'.$username;
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>