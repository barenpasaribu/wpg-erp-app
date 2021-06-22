<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$param = $_POST;
$method = $_POST['method'];
$periodeAkutansi = $_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan'];
switch ($method) {
    case 'insert':
        $i = 'insert into '.$dbname.".keu_5kursbulanan (periode,matauang,kurs,updateby,lastupdate)\r\n\t\tvalues ('".$param['periodeDt']."','".$param['mtUang']."','".$param['krsDt']."','".$_SESSION['standard']['userid']."','".date('Y-m-d H:i:s')."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".keu_5kursbulanan set kurs='".$param['krsDt']."',matauang='".$param['mtUang']."',updateby='".$_SESSION['standard']['userid']."',lastupdate='".date('Y-m-d H:i:s')."'\r\n\t\t where periode='".$param['periodeDtold']."' and matauang='".$param['mtUangold']."' ";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t\t <td align=center>No.</td>\r\n                                 <td align=center>".$_SESSION['lang']['periode']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['matauang']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kurs']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
        if ('' !== $_POST['periode']) {
            $whr .= " and periode like '%".$_POST['periode']."%'";
        }

        if ('' !== $_POST['mtUang']) {
            $whr .= " and matauang='".$_POST['mtUang']."'";
        }

        $limit = 15;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $offset = $page * $limit;
        $maxdisplay = $page * $limit;
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".keu_5kursbulanan  \r\n                       where periode!='' ".$whr.' order by periode asc ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".keu_5kursbulanan \r\n                    where periode!='' ".$whr.' order by periode asc limit '.$offset.','.$limit.'';
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td>'.$d['periode'].'</td>';
            echo '<td>'.$d['matauang'].'</td>';
            echo '<td align=right>'.number_format($d['kurs'], 2).'</td>';
            if ($d['periode'] === $periodeAkutansi) {
                echo "<td align=center>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"edit('".$d['periode']."','".$d['matauang']."','".$d['kurs']."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['periode']."','".$d['matauang']."');\"></td>";
            } else {
                echo '<td colspan=2>&nbsp</td>';
            }

            echo '</tr>';
        }
        echo '</tbody>';
        echo "<tfoot>\r\n\t\t<tr class=rowheader><td colspan=5 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=loadData(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=loadData(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr></tfoot></table>";

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".keu_5kursbulanan where periode='".$param['periodeDt']."' and matauang='".$param['mtUang']."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>