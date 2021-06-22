<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$kode = $_POST['kode'];
$nama = $_POST['nama'];
$jabatan = $_POST['jabatan'];
$denda = $_POST['denda'];
$method = $_POST['method'];
echo "\r\n";
switch ($method) {
    case 'insert':
        $i = 'insert into '.$dbname.".kebun_5dendapengawas (kode,nama,jabatan,denda,updateby)\r\n\t\tvalues ('".$kode."','".$nama."','".$jabatan."','".$denda."','".$_SESSION['standard']['userid']."')";
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
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t \t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kode']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nama']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['jabatan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['rp'].' '.$_SESSION['lang']['denda']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.'.kebun_5dendapengawas';
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.'.kebun_5dendapengawas order by kode asc  limit '.$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['kode'].'</td>';
            echo '<td align=left>'.$d['nama'].'</td>';
            echo '<td align=left>'.$d['jabatan'].'</td>';
            echo '<td align=right>'.number_format($d['denda']).'</td>';
            echo "<td align=center>\r\n\t\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['kode']."','".$d['nama']."','".$d['jabatan']."','".$d['denda']."');\">\r\n\t\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kode']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_5dendapengawas where kode='".$kode."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>