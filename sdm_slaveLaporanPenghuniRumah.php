<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$str = ' select a.* from '.$dbname.".sdm_perumahanht a where kodeorg='".$kodeorg."'";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $jlh = 0;
    $str1 = 'select count(karyawanid) as jlh from '.$dbname.".sdm_penghunirumah\r\n\t        where kodeorg='".$kodeorg."' and blok='".$bar->blok."'\r\n\t\t\tand norumah='".$bar->norumah."'";
    $res1 = mysql_query($str1);
    while ($bar1 = mysql_fetch_object($res1)) {
        $jlh = $bar1->jlh;
    }
    echo "<tr class=rowcontent>\r\n\t\t <td>".$no."</td>\r\n\t\t <td>".$kodeorg."</td>\r\n\t\t <td>".$bar->kompleks."</td>\r\n\t\t <td>".$bar->blok."</td>\r\n\t\t <td>".$bar->norumah."</td>\r\n\t\t <td>".$bar->tipe."</td>\r\n\t\t <td align=right>".$jlh."</td>\r\n\t\t <td>\r\n\t\t <img src=images/zoom.png class=resicon onclick=showTenant('".$kodeorg."','".$bar->blok."','".$bar->norumah."',event)>\r\n\t\t </td>\r\n\t\t </tr>";
}

?>