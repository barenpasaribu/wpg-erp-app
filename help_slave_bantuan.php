<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
switch ($proses) {
    case 'loaddata':
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $sCount = 'select count(*) as jmlhrow from '.$dbname.'.guidance order by kode asc';
        $qCount = mysql_query($sCount) || exit(mysql_error($conns));
        while ($rCount = mysql_fetch_object($qCount)) {
            $jlhbrs = $rCount->jmlhrow;
        }
        $offset = $page * $limit;
        if ($jlhbrs < $offset) {
            --$page;
        }

        $offset = $page * $limit;
        $no = $offset;
        $sShow = 'select * from '.$dbname.'.guidance order by kode asc,tentang,modul,isi limit '.$offset.','.$limit.' ';
        $qShow = mysql_query($sShow) || exit(mysql_error($conns));
        while ($row = mysql_fetch_assoc($qShow)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n            <td id='no'>".$no."</td>\r\n            <td id='index_".$row['kode']."' value='".$row['kode']."' align='center'>".$row['kode']."</td>\r\n            <td id='modul_".$row['kode']."' value='".$row['modul']."'>".$row['modul']."</td>\r\n            <td id='tentang_".$row['kode']."' value='".$row['tentang']."'>".$row['tentang']."</td>\r\n            <td><img onclick=\"detailHelp(event,'".str_replace(' ', '', $row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>";
        }
        echo "\r\n        </tr><tr class=rowheader><td colspan=5 align=center>\r\n        ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."<br />\r\n        <button class=mybutton onclick=cariBast(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n        <button class=mybutton onclick=cariBast(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n        </td>\r\n        </tr>";

        break;
    case 'cariindex':
        $indexfind = $_POST['cariindex'];
        $str = 'select * from '.$dbname.".guidance where (kode like '%".$indexfind."%') or (tentang like '%".$indexfind."%') or (modul like '%".$indexfind."%')  ";
        if ($res = mysql_query($str)) {
            $no = 0;
            while ($bar = mysql_fetch_object($res)) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                    <td id='no'>".$no."</td>\r\n                    <td id='index_".$bar->kode."' value='".$bar->kode."' align='center'>".$bar->kode."</td>\r\n                    <td id='modul_".$bar->kode."' value='".$bar->modul."'>".$bar->modul."</td>\r\n                    <td id='tentang_".$bar->kode."' value='".$bar->tentang."'>".$bar->tentang."</td>\r\n                    <td><img onclick=\"detailHelp(event,'".str_replace(' ', '', $bar->kode)."','".$bar->modul."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>\r\n                    </tr>";
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    default:
        break;
}

?>