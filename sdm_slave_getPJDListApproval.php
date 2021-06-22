<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'config/connection.php';
$limit = 20;
$page = 0;
if (isset($_POST['tex'])) {
    $notransaksi = " and notransaksi like '%".$_POST['tex']."%' ";
} else {
    $notransaksi = '';
}

$str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where\r\n\t\t(persetujuan=".$_SESSION['standard']['userid']."\r\n\t\tor hrd=".$_SESSION['standard']['userid'].")\r\n\t\t".$notransaksi."\r\n\t\torder by jlhbrs desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jlhbrs = $bar->jlhbrs;
}
if (isset($_POST['page'])) {
    $page = $_POST['page'];
    if ($page < 0) {
        $page = 0;
    }
}

$offset = $page * $limit;
$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n\t\t(persetujuan=".$_SESSION['standard']['userid']."\r\n\t\tor hrd=".$_SESSION['standard']['userid'].")\r\n\t\t".$notransaksi."\r\n\t\torder by tanggalbuat desc  limit ".$offset.',20';
$res = mysql_query($str);
$no = $page * $limit;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    if ($bar->persetujuan == $_SESSION['standard']['userid']) {
        $per = 'persetujuan';
    } else {
        $per = 'hrd';
    }

    $namakaryawan = '';
    $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
    $resx = mysql_query($strx);
    while ($barx = mysql_fetch_object($resx)) {
        $namakaryawan = $barx->namakaryawan;
    }
    $add = '';
    if (0 == $bar->statuspersetujuan && 'persetujuan' == $per) {
        $add .= "&nbsp <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\">\r\n\t\t       &nbsp <img src=images/onebit_33 class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\">\r\n         ";
    }

    if (0 == $bar->statushrd && 'hrd' == $per) {
        $add .= "&nbsp <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\">\r\n\t\t       &nbsp <img src=images/onebit_33 class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\">\r\n         ";
    }

    if (2 == $bar->statuspersetujuan) {
        $stpersetujuan = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['disetujui'];
        } else {
            $stpersetujuan = $_SESSION['lang']['wait_approve'];
        }
    }

    if (2 == $bar->statushrd) {
        $sthrd = $_SESSION['lang']['ditolak'];
    } else {
        if (1 == $bar->statushrd) {
            $sthrd = $_SESSION['lang']['disetujui'];
        } else {
            $sthrd = $_SESSION['lang']['wait_approve'];
        }
    }

    echo "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td>".$sthrd."</td>\t\r\n\t  <td align=center>\r\n\t     <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n       ".$add."\r\n\t  </td>\r\n\t  </tr>";
}
echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>