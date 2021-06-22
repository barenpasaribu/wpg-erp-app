<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kode = $_POST['kode'];
$jmlhPorsi = $_POST['jmlhPorsi'];
$method = $_POST['method'];
switch ($method) {
    case 'update':
        $str = 'update '.$dbname.".it_presentasenilai set jumlah='".$jmlhPorsi."',\r\n\t       where kode='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $sCek = 'select distinct * from '.$dbname.".it_presentasenilai where kode='".$kode."'";
        $qCek = mysql_query($sCek) || exit(mysql_error($conns));
        $rRow = mysql_num_rows($qCek);
        if (0 < $rRow) {
            $sdel = 'delete from '.$dbname.".it_presentasenilai where kode='".$kode."'";
            if (mysql_query($sdel)) {
                $sCek = 'select distinct sum(jumlah) as jumlah from '.$dbname.'.it_presentasenilai ';
                $qCek = mysql_query($sCek) || exit(mysql_error($conns));
                $rRow = mysql_fetch_assoc($qCek);
                $jmlhCek = $rRow['jumlah'] + $jmlhPorsi;
                if (100 < $jmlhCek) {
                    $jmlhPorsi = 100 - $rRow['jumlah'];
                }

                $str = 'insert into '.$dbname.".it_presentasenilai (kode,jumlah)\r\n\t      values('".$kode."','".$jmlhPorsi."')";
                if (mysql_query($str)) {
                } else {
                    echo ' Gagal,'.addslashes(mysql_error($conn));
                }
            }
        } else {
            $sCek = 'select distinct sum(jumlah) as jumlah from '.$dbname.'.it_presentasenilai ';
            $qCek = mysql_query($sCek) || exit(mysql_error($conns));
            $rRow = mysql_fetch_assoc($qCek);
            $jmlhCek = $rRow['jumlah'] + $jmlhPorsi;
            if (100 < $jmlhCek) {
                $jmlhPorsi = 100 - $rRow['jumlah'];
            }

            $str = 'insert into '.$dbname.".it_presentasenilai (kode,jumlah)\r\n\t      values('".$kode."','".$jmlhPorsi."')";
            if (mysql_query($str)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        }

        break;
    case 'delete':
        $str = 'delete from '.$dbname.".it_presentasenilai\r\n\twhere kode='".$kode."'";
        if (mysql_query($str)) {
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        $str1 = 'select * from '.$dbname.'.it_presentasenilai order by kode asc';
        if ($res1 = mysql_query($str1)) {
            echo "<table class=sortable cellspacing=1 border=0 style='width:350px;'>\r\n\t     <thead>\r\n\t\t <tr class=rowheader>\r\n                 <td style='width:150px;'>".$_SESSION['lang']['kodeabs']."</td>\r\n                 <td>".$_SESSION['lang']['jumlah']."</td>\r\n                 <td style='width:70px;'>*</td></tr>\r\n\t\t </thead>\r\n\t\t <tbody>";
            while ($bar1 = mysql_fetch_object($res1)) {
                echo "<tr class=rowcontent>\r\n                     <td align=left>".$bar1->kode."</td>\r\n                     <td>".$bar1->jumlah."</td>\r\n                     <td align=center><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$bar1->kode."','".$bar1->jumlah."');\"> </td></tr>";
            }
            echo "\t \r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\r\n\t\t </table>";
        }

        break;
    default:
        break;
}

?>