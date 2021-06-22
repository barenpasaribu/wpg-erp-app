<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
require_once 'config/connection.php';
$periode = $_POST['periode'];
$tipe = $_POST['tipe'];
$username = $_POST['username'];
if ('' == $username) {
    $username = $_SESSION['standard']['username'];
} else {
    $username = $username;
}

$str = 'select id,name from '.$dbname.'.sdm_ho_component order by id';
$res = mysql_query($str);
$head = "<table cellspacing=1 class=data border=0 width=1400px>\r\n       <thead><tr chass=rowheader>\r\n\t   \t<td class=firsttd>No.</td>\r\n\t\t<td align=center>No.Karyawan</td>\r\n\t\t<td align=center>Nama.Karyawan</td>\r\n\t\t<td align=center>Periode<br>Gaji</td>\r\n\t\t<td align=center>Bank</td>\r\n\t\t<td align=center>Bank.A/C</td>\r\n\t\t<td align=center>Total T.H.P<br>(Rp.)</td>";
$arrUrut = [];
$arrVal = [];
while ($bar = mysql_fetch_object($res)) {
    array_push($arrUrut, $bar->id);
    $head .= '<td align=center>'.str_replace(' ', '<br>', $bar->name).'</td>';
}
$head .= '</tr></thead><tbody>';
$str1 = 'select distinct e.karyawanid,e.name,e.bank,e.bankaccount from '.$dbname.'.sdm_ho_employee e,'.$dbname.".sdm_gaji m\r\n       where e.operator='".$username."'\r\n\t   and e.karyawanid=m.karyawanid and periodegaji='".$periode."' and e.karyawanid not in (0999999999,0888888888)\r\n       order by e.name";
$res1 = mysql_query($str1);
$no = 0;
$grandTotal = 0;
while ($bar1 = mysql_fetch_array($res1)) {
    ++$no;
    $total = 0;
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
    $head .= "<tr class=rowcontent>\r\n        <td class=firsttd rowspan=2>".$no."</td>\r\n\t\t<td rowspan=2>".$bar1[0]."</td>\r\n\t\t<td rowspan=2>".$bar1[1]."</td>\r\n\t\t<td rowspan=2 align=center>".substr($periode, 5, 2).'-'.substr($periode, 0, 4)."</td>\r\n\t\t<td rowspan=2>".$bar1[2]."</td>\r\n\t\t<td rowspan=2>".$bar1[3]."</td>\r\n\t\t<td align=right class=firsttd ><b>".number_format($total, 2, '.', ',').'</b></td>';
    for ($c = 0; $c < count($arrVal); ++$c) {
        $head .= '<td align=right>'.number_format($arrVal[$c], 2, '.', ',').'</td>';
    }
    $head .= '</tr>';
    $terbilang = '-';
    $str3 = 'select terbilang from '.$dbname.".sdm_ho_payrollterbilang\r\n        where userid=".$bar1[0]." and `type`='".$tipe."'\r\n\t\tand periode='".$periode."' limit 1";
    $res3 = mysql_query($str3);
    while ($bar3 = mysql_fetch_object($res3)) {
        $terbilang = $bar3->terbilang;
    }
    $head .= "<tr><td bgcolor=#ffffff colspan='".(count($arrVal) + 1)."'>".$terbilang.'</td></tr>';
    $grandTotal += $total;
}
$head .= "</tbody><tfoot>\r\n        <tr><td colspan=6>Grand Total</td>\r\n\t\t<td align=right>".number_format($grandTotal, 2, '.', ',')."</td>\r\n\t\t<td clspan=".count($arrVal)."></td>\r\n\t\t</tfoot></table>";
echo $head;

?>