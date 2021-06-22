<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    $str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5";
    $res = mysql_query($str);
    $optKar = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    }
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht 
            where 
                notransaksi like '%".$notransaksi."%'
            and
                notransaksi like '%".$_SESSION['empl']['lokasitugas']."'
            order by jlhbrs desc";
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
    $str = 'select * from '.$dbname.".sdm_pjdinasht 
            where 
                notransaksi like '%".$notransaksi."%'
            and
                notransaksi like '%".$_SESSION['empl']['lokasitugas']."'
            order by tanggalbuat desc,notransaksi desc limit ".$offset.',20';
    $res = mysql_query($str);
    $no = $page * $limit;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $namakaryawan = '';
        $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
        $resx = mysql_query($strx);
        while ($barx = mysql_fetch_object($resx)) {
            $namakaryawan = $barx->namakaryawan;
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

        echo "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td>".$sthrd."</td>\t\r\n\t  <td align=center>\r\n\t     <img src='images/pdf.jpg' class='resicon'  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n\t  </td>\r\n\t  </tr>";
    }
    echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>