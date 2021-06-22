<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$divisi = $_POST['divisi'];
$kodeblok = $_POST['kodeblok'];
$regional = $_POST['regional'];
$jarak = $_POST['jarak'];
$divisiSch = $_POST['divisiSch'];
$method = $_POST['method'];
$nmOrg = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$nmid = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$nmen = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1');
echo "\r\n";
switch ($method) {
    case 'getBlok':
        $optBlok = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $i = 'select kodeorg from '.$dbname.".setup_blok where kodeorg like '%".$divisi."%'";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            $optBlok .= "<option value='".$d['kodeorg']."'>".$d['kodeorg'].'</option>';
        }
        echo $optBlok;

        break;
    case 'insert':
        $i = 'insert into '.$dbname.".vhc_5jarakblok (regional,kodeorg,kodeblok,jarak,updateby)\r\n\t\tvalues ('".$regional."','".$divisi."','".$kodeblok."','".$jarak."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'loadData':
        echo "\r\n\t<div id=container>\r\n\t\t<table class=sortable cellspacing=1 border=0>\r\n\t     <thead>\r\n\t\t\t <tr class=rowheader>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['nourut']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['regional']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['divisi']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['jarak']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['updateby']."</td>\r\n\t\t\t\t <td align=center>".$_SESSION['lang']['action']."</td>\r\n\t\t\t </tr>\r\n\t\t</thead>\r\n\t\t<tbody>";
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
        if ('' !== $divisiSch) {
            $divisiSch = "and kodeblok like '%".$divisiSch."%'";
        } else {
            $divisiSch = "and kodeblok!='' ";
        }

        $ql2 = 'select count(*) as jmlhrow from '.$dbname.".vhc_5jarakblok  where regional='".$_SESSION['empl']['regional']."'  ".$divisiSch.'  ';
        $query2 = mysql_query($ql2);
        while ($jsl = mysql_fetch_object($query2)) {
            $jlhbrs = $jsl->jmlhrow;
        }
        $i = 'select * from '.$dbname.".vhc_5jarakblok where regional='".$_SESSION['empl']['regional']."' ".$divisiSch.'  limit '.$offset.','.$limit.'';
        $n = mysql_query($i);
        $no = $maxdisplay;
        while ($d = mysql_fetch_assoc($n)) {
            ++$no;
            echo '<tr class=rowcontent>';
            echo '<td align=center>'.$no.'</td>';
            echo '<td align=left>'.$d['regional'].'</td>';
            echo '<td align=right>'.$d['kodeorg'].'</td>';
            echo '<td align=right>'.$d['kodeblok'].'</td>';
            echo '<td align=right>'.number_format($d['jarak']).'</td>';
            echo '<td align=left>'.$nmOrg[$d['updateby']].'</td>';
            echo "<td align=center>\r\n\t\t\t<img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"del('".$d['kodeblok']."');\"></td>";
            echo '</tr>';
        }
        echo "\r\n\t\t<tr class=rowheader><td colspan=18 align=center>\r\n\t\t".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n\t\t<button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t\t<button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t\t</td>\r\n\t\t</tr>";
        echo '</tbody></table>';

        break;
    case 'delete':
        $i = 'delete from '.$dbname.".vhc_5jarakblok where kodeblok='".$kodeblok."' ";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>