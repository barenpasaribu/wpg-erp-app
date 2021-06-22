<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$thnBudget = $_POST['thnBudget'];
$kdGol = $_POST['kdGol'];
$region = $_POST['region'];
$tktPes = $_POST['tktPes'];
$tksi = $_POST['tksi'];
$airport = $_POST['airport'];
$visa = $_POST['visa'];
$byaLain = $_POST['byaLain'];
$where = " tahunbudget='".$thnBudget."' and golongan='".$kdGol."' and tujuan='".$region."'";
switch ($method) {
    case 'insert':
        if ('' == $thnBudget || '' == $kdGol || '' == $region) {
            echo 'warning:Field tidak boleh kosong';
            exit();
        }

        $sCek = 'select tahunbudget from '.$dbname.'.sdm_5transportpjd where '.$where.'';
        $qCek = mysql_query($sCek);
        $rCek = mysql_num_rows($qCek);
        if (0 < $rCek) {
            echo 'warning:Data sudah ada';
            exit();
        }

        ('' == $tktPes ? ($tktPes = 0) : ($tktPes = $tktPes));
        ('' == $airport ? ($airport = 0) : ($airport = $airport));
        ('' == $visa ? ($visa = 0) : ($visa = $visa));
        ('' == $byaLain ? ($byaLain = 0) : ($byaLain = $byaLain));
        $sIns = 'insert into '.$dbname.".sdm_5transportpjd (tahunbudget, golongan, tujuan, ticket, taxiboat, airporttax, visa, bylain) values \r\n                        ('".$thnBudget."','".$kdGol."','".$region."','".$tktPes."','".$tksi."','".$airport."','".$visa."','".$byaLain."')";
        if (!mysql_query($sIns)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'loadData':
        $limit = 20;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $no = 0;
        if ('' != $thnBudget) {
            $addKond .= " and tahunbudget='".$thnBudget."'";
        }

        if ('' != $kdGol) {
            $addKond .= " and golongan='".$kdGol."'";
        }

        if ('' != $region) {
            $addKond .= " and tujuan='".$region."'";
        }

        $sql2 = 'SELECT count(*) as jmlhrow FROM '.$dbname.".sdm_5transportpjd where tahunbudget!='' ".$addKond.' order by tahunbudget desc ';
        $query2 = mysql_query($sql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        if (0 != $jlhbrs) {
            $str = 'select * from '.$dbname.".sdm_5transportpjd where tahunbudget!='' ".$addKond.' order by tahunbudget desc limit '.$offset.','.$limit.'';
            $res = mysql_query($str);
            while ($bar = mysql_fetch_assoc($res)) {
                ++$no;
                echo "<tr class=rowcontent>\r\n\t\t<td>".$no."</td>\r\n\t\t<td>".$bar['tahunbudget']."</td>\r\n\t\t<td>".$bar['golongan']."</td>\r\n\t\t<td>".$bar['tujuan']."</td>\r\n\t\t<td align=right>".number_format($bar['ticket'], 2)."</td>\r\n                <td align=right>".number_format($bar['taxiboat'], 2)."</td>\r\n\t\t<td align=right>".number_format($bar['airporttax'], 2)."</td>\r\n                <td align=right>".number_format($bar['visa'], 2)."</td>\r\n                <td align=right>".number_format($bar['bylain'], 2)."</td>\r\n                \r\n\t\t<td>\r\n\t\t\t  <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar['tahunbudget']."','".$bar['golongan']."','".$bar['tujuan']."');\"> \r\n\t\t\t  <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".$bar['tahunbudget']."','".$bar['golongan']."','".$bar['tujuan']."');\">\r\n\t\t  </td>\r\n\t\t</tr>";
            }
            echo "\r\n                <tr><td colspan=10 align=center>\r\n                ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n                <button class=mybutton onclick=cariPage(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n                <button class=mybutton onclick=cariPage(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n                </td>\r\n                </tr>";
        } else {
            echo '<tr class=rowcontent><td colspan=10>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        break;
    case 'update':
        if ('' == $thnBudget || '' == $kdGol || '' == $region) {
            echo 'warning:Field tidak boleh kosong';
            exit();
        }

        ('' == $tktPes ? ($tktPes = 0) : ($tktPes = $tktPes));
        ('' == $airport ? ($airport = 0) : ($airport = $airport));
        ('' == $visa ? ($visa = 0) : ($visa = $visa));
        ('' == $byaLain ? ($byaLain = 0) : ($byaLain = $byaLain));
        $sUpd = 'update '.$dbname.".sdm_5transportpjd set `ticket`='".$tktPes."',`taxiboat`='".$tksi."',`airporttax`='".$airport."',`visa`='".$visa."',`bylain`='".$byaLain."' where ".$where.'';
        if (!mysql_query($sUpd)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'delData':
        $sDel = 'delete from '.$dbname.'.sdm_5transportpjd  where '.$where.'';
        if (!mysql_query($sDel)) {
            echo 'Gagal'.mysql_error($conn);
        }

        break;
    case 'getData':
        $sDt = 'select * from '.$dbname.'.sdm_5transportpjd where '.$where.'';
        $qDt = mysql_query($sDt);
        $rDet = mysql_fetch_assoc($qDt);
        echo $rDet['tahunbudget'].'###'.$rDet['golongan'].'###'.$rDet['tujuan'].'###'.$rDet['ticket'].'###'.$rDet['taxiboat'].'###'.$rDet['airporttax'].'###'.$rDet['visa'].'###'.$rDet['bylain'];

        break;
    default:
        break;
}

?>