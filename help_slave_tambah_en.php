<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$proses = $_POST['proses'];
switch ($proses) {
    case 'insert':
        $tentang = $_POST['tentang'];
        $modul = $_POST['modul'];
        $isi = $_POST['isi'];
        $html = $_POST['html'];
        $rrr = '';
        if ('' === $tentang) {
            $rrr .= ' Tentang, ';
        }

        if ('' === $modul) {
            $rrr .= ' Modul,';
        }

        if ('' === $isi) {
            $rrr .= ' Isi';
        }

        if ('' !== $rrr) {
            echo 'error: Silakan mengisi '.$rrr.'.';
            exit();
        }

        $sCek = 'select * from '.$dbname.".guidance_english where kode='".$_POST['index']."'";
        $qCek = mysql_query($sCek);
        $cek = mysql_num_rows($qCek);
        if ($cek < 1) {
            $index = 0;
            $simpan = 'INSERT INTO '.$dbname.".guidance_english(kode,tentang,modul,isi,tujuan)\r\n        VALUES ('".$index."','".$tentang."','".$modul."','".$isi."','".$html."')";
            if ($hasil = mysql_query($simpan)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        } else {
            $update = 'UPDATE '.$dbname.".guidance_english\r\n        SET tentang='".$tentang."', modul='".$modul."',isi='".$isi."',tujuan='".$html."'\r\n        WHERE kode='".$_POST['index']."'";
            if (!mysql_query($update)) {
                echo 'DB Error ht : '.mysql_error();
            } else {
                echo 'Done.';
            }
        }

        break;
    case 'loaddata':
        $limit = 10;
        $page = 0;
        if (isset($_POST['page'])) {
            $page = $_POST['page'];
            if ($page < 0) {
                $page = 0;
            }
        }

        $sCount = 'select count(*) as jmlhrow from '.$dbname.'.guidance_english order by `kode` asc';
        $qCount = mysql_query($sCount) || exit(mysql_error($conns));
        while ($rCount = mysql_fetch_object($qCount)) {
            $jmlbrs = $rCount->jmlhrow;
        }
        $offset = $page * $limit;
        if ($jmlbrs < $offset) {
            --$page;
        }

        $offset = $page * $limit;
        $no = $offset;
        $sShow = 'select * from '.$dbname.'.guidance_english order by kode asc,kode,tentang,modul,isi limit '.$offset.','.$limit.' ';
        $qShow = mysql_query($sShow) || exit(mysql_error($conns));
        while ($row = mysql_fetch_assoc($qShow)) {
            ++$no;
            echo "<tr class=rowcontent>\r\n        <td id='no'>".$no."</td>\r\n        <td id='index_".$row['kode']."' value='".$row['kode']."' align='center'>".$row['kode']."</td>\r\n        <td id='modul_".$row['kode']."' value='".$row['modul']."'>".$row['modul']."</td>\r\n        <td id='tentang_".$row['kode']."' value='".$row['tentang']."'>".$row['tentang']."</td>\r\n        <td>\r\n        <img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow_en('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(["\r", "\n"], '\\n', $row['isi'])."','".$row['tujuan']."');\" >\r\n        <img onclick=\"detailHelp_en(event,'".str_replace(' ', '', $row['kode'])."','".$row['modul']."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\">\r\n        <img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData_en('".$row['kode']."','".$row['tentang']."','".$row['modul']."','".str_replace(["\r", "\n"], '\\n', $row['isi'])."');\" ></td>";
        }
        echo "\r\n    </tr><tr class=rowheader><td colspan=5 align=center>\r\n    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jmlbrs."<br />\r\n    <button class=mybutton onclick=cariBast_en(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n    <button class=mybutton onclick=cariBast_en(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n    </td>\r\n    </tr>";

        break;
    case 'cariindex':
        $indexfind = $_POST['cariindex'];
        $sCari = 'select * from '.$dbname.".guidance_english where (kode like '%".$indexfind."%') or (tentang like '%".$indexfind."%') or (modul like '%".$indexfind."%')  ";
        if ($qCari = mysql_query($sCari)) {
            $no = 0;
            while ($bar = mysql_fetch_object($qCari)) {
                ++$no;
                echo "<tr class=rowcontent>\r\n                <td id='no'>".$no."</td>\r\n                <td id='index_".$bar->kode."' value='".$bar->kode."'>".$bar->kode."</td>\r\n                <td id='modul_".$bar->kode."' value='".$bar->modul."'>".$bar->modul."</td>\r\n                <td id='tentang_".$bar->kode."' value='".$bar->tentang."'>".$bar->tentang."</td>\r\n                <td><img src=images/edit.png class=resicon  title='Edit' onclick=\"editRow_en('".$bar->kode."','".$bar->tentang."','".$bar->modul."','".str_replace(["\r", "\n"], '\\n', $bar->isi)."','".$bar->tujuan."');\" ></td>\r\n                <td><img onclick=\"detailHelp_en(event,'".str_replace(' ', '', $bar->kode)."','".$bar->modul."');\" title=\"Detail Help\" class=\"resicon\" src=\"images/zoom.png\"></td>\r\n                <td><img src=images/delete1.jpg class=resicon  title='Delete' onclick=\"delData_en('".$bar->kode."','".$bar->tentang."','".$bar->modul."','".str_replace(["\r", "\n"], '', $bar->isi)."');\" ></td>\r\n                </tr>";
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'deletedata':
        $index = $_POST['index'];
        $modul = $_POST['modul'];
        $tentang = $_POST['tentang'];
        $isi = $_POST['isi'];
        $where = "modul = '".$modul."' and tentang = '".$tentang."' and isi = '".$isi."'";
        $sDel = 'delete from '.$dbname.'.guidance_english where '.$where." and kode = '".$index."'";
        if (mysql_query($sDel)) {
            echo '';
        } else {
            echo 'DB Error : '.mysql_error($conn);
        }

        break;
    default:
        break;
}

?>