<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    $str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and karyawanid <>".$_SESSION['standard']['userid']." and kodegolongan in ('2A','2B','2C','2D','2E','2F','2G','2H') order by namakaryawan";
    $res = mysql_query($str);
    $optKar = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    }
    $str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and kodegolongan in ('1A','1B','1C','1D','1E','1F','1G','1H') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    $res = mysql_query($str);
    $optKar2 = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKar2 .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    }
    $str = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan\r\n      where tipekaryawan=5 and bagian in ('HO_HRGA','RO_HRGA') and karyawanid <>".$_SESSION['standard']['userid'].' order by namakaryawan';
    $res = mysql_query($str);
    $optKarHrd = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optKarHrd .= "<option value='".$bar->karyawanid."'>".$bar->namakaryawan.'</option>';
    }
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht
            where notransaksi like '%".$notransaksi."%'
            and karyawanid=".$_SESSION['standard']['userid']."
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
    //$str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where notransaksi like '%".$notransaksi."%'\r\n        and karyawanid=".$_SESSION['standard']['userid']."\r\n\t\torder by tanggalbuat desc limit ".$offset.',20';
    $str = 'select * from '.$dbname.".sdm_pjdinasht 
            where notransaksi like '%".$notransaksi."%'
            and 
            (
                karyawanid=".$_SESSION['standard']['userid']."
            or 
                created_by=".$_SESSION['standard']['userid']."
            )
            order by tanggalbuat desc limit ".$offset.',20';
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
        $add = '';
        if (0 == $bar->statuspersetujuan && 0 == $bar->statushrd) {
            $add .= "&nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n\t\t &nbsp <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editPJD('".$bar->notransaksi."','".$bar->karyawanid."');\">\r\n         ";
        }

        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspersetujuan) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan = $_SESSION['lang']['wait_approve'];
                $stpersetujuan .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan','".$bar->notransaksi."')>".$optKar.'</select>';
            }
        }

        if (2 == $bar->statuspersetujuan2) {
            $stpersetujuan2 = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspersetujuan2) {
                $stpersetujuan2 = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan2 = $_SESSION['lang']['wait_approve'];
                $stpersetujuan2 .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select  style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'persetujuan2','".$bar->notransaksi."')>".$optKar2.'</select>';
            }
        }

        if (2 == $bar->statushrd) {
            $sthrd = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statushrd) {
                $sthrd = $_SESSION['lang']['disetujui'];
            } else {
                $sthrd = $_SESSION['lang']['wait_approve'];
                $sthrd .= '<br> &nbsp '.$_SESSION['lang']['ganti'].":<select   style='width:100px;' onchange=ganti(this.options[this.selectedIndex].value,'hrd','".$bar->notransaksi."')>".$optKarHrd.'</select>';
            }
        }

        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan2 = '';
            $sthrd = '';
        }

        if ($bar->isBatal == 1) {
            $kolomstatus = "<td colspan=3 align=center>Perjalanan dinas dibatalkan</td>";
        } else {
            $kolomstatus = "<td>".$stpersetujuan."</td>
            <td>".$stpersetujuan2."</td>
            <td>".$sthrd."</td>";
        }

        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar->notransaksi."</td>
                    <td>".$namakaryawan."</td>
                    <td>".tanggalnormal($bar->tanggalbuat)."</td>
                    <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain." "."</td>
                    ".$kolomstatus."
                    <td align=center>
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
                        ".$add."
                    </td>
                </tr>";
    }
    echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>