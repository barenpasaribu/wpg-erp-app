<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$ulat = $_POST['ulat'];
$kret = $_POST['kret'];
$maxu = $_POST['maxu'];
$minu = $_POST['minu'];
$method = $_POST['method'];
$arrNm = ['jlhdarnatrima' => 'Darna Trima', 'jlhsetothosea' => 'Setothosea Asigna', 'jlhsetothosea' => 'Setora Nitens', 'jlhulatkantong' => 'Ulat Kantong'];
$optUlat = "<option value='jlhdarnatrima'>Darna Trima</option>";
$optUlat .= "<option value='jlhsetothosea'>Setothosea Asigna</option>";
$optUlat .= "<option value='jlhsetoranitens'>Setora Nitens</option>";
$optUlat .= "<option value='jlhulatkantong'>Ulat Kantong</option>";
echo "\r\n";
switch ($method) {
    case 'insert':
        $i = 'insert into '.$dbname.".kebun_qc_5ulatapi (ulat,kret,maxu,minu,updateby)\r\n\t\tvalues ('".$ulat."','".$kret."','".$maxu."','".$minu."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".kebun_5dendapengawas set nama='".$nama."',jabatan='".$jabatan."',denda='".$denda."',updateby='".$_SESSION['standard']['userid']."'\r\n\t\t where kode='".$kode."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t <td align=center>No</td>\r\n\t\t\t \t <td align=center>Ulat</td>\r\n\t\t\t\t <td align=center>Kreteria</td>\r\n\t\t\t\t <td align=center>Minimal</td>\r\n\t\t\t\t <td align=center>Maksimal</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_qc_5ulatapi';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_qc_5ulatapi  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$arrNm[$d['ulat']].'</td>';
            echo '<td align=left>'.$d['kret'].'</td>';
            echo '<td align=left>'.$d['maxu'].'</td>';
            echo '<td align=left>'.$d['minu'].'</td>';
            echo "<td align=center>\r\n\t\t\t\t\r\n\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['ulat']."','".$d['kret']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_qc_5ulatapi where ulat='".$ulat."' and kret='".$kret."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>