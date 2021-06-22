<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$regional = $_POST['regional'];
$kodekegiatan = $_POST['kodekegiatan'];
$volume = $_POST['volume'];
$rupiah = $_POST['rupiah'];
$tipe = $_POST['tipe'];
$jumlahhari = $_POST['jumlahhari'];
$method = $_POST['method'];
$optTipe = ['D' => 'Dump Truck', 'F' => 'Fuso'];
$nmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
echo "\r\n";
switch ($method) {
    case 'update':
        $i = 'UPDATE  '.$dbname.".`kebun_5premimuat` SET  \r\n\t\t\t`rupiah` =  '".$rupiah."',\r\n\t\t\t`tipe` =  '".$tipe."',\r\n\t\t\t`jumlahhari` =  '".$jumlahhari."',\r\n\t\t\t`updateby` =  '".$_SESSION['standard']['userid']."' WHERE  `kodekegiatan` ='".$kodekegiatan."'  '' AND  `volume` ='".$volume."' ";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'insert':
        $i = 'insert into '.$dbname.".kebun_5premimuat (regional,kodekegiatan,volume,rupiah,tipe,jumlahhari,updateby)\r\n\t\tvalues ('".$regional."','".$kodekegiatan."','".$volume."','".$rupiah."','".$tipe."','".$jumlahhari."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kodekegiatan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['namakegiatan']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['volume']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['rupiah']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['tipe']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['jumlahhari']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
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
        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".kebun_5premimuat  where regional='".$_SESSION['empl']['regional']."'   ";
        $query2 = mysql_query($ql2) ;
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".kebun_5premimuat where regional='".$_SESSION['empl']['regional']."'  limit ".$offset.','.$limit.'';
        $n = mysql_query($i) ;
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['regional'].'</td>';
            echo '<td align=left>'.$d['kodekegiatan'].'</td>';
            echo '<td align=left>'.$nmKeg[$d['kodekegiatan']].'</td>';
            echo '<td align=right>'.$d['volume'].'</td>';
            echo '<td align=right>'.number_format($d['rupiah']).'</td>';
            echo '<td align=right>'.$optTipe[$d['tipe']].'</td>';
            echo '<td align=right>'.$d['jumlahhari'].'</td>';
            echo '<td align=left>'.$nmKar[$d['updateby']].'</td>';
            echo "<td align=center>\r\n\t\t\t<img src=images/application/application_edit.png class=resicon  caption='Edit' \r\n\t\t\tonclick=\"edit('".$d['regional']."','".$d['kodekegiatan']."','".$d['volume']."','".$d['rupiah']."','".$d['tipe']."','".$d['jumlahhari']."');\">\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodekegiatan']."','".$d['volume']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".kebun_5premimuat where kodekegiatan='".$kodekegiatan."' and volume='".$volume."' ";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>