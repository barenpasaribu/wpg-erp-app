<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    require_once 'config/connection.php';
    $limit = 20;
    $page = 0;
    $notransaksi = '';
    if (isset($_POST['tex'])) {
        $notransaksi .= " and notransaksi like '%".$_POST['tex']."%' ";
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht 
            where
                kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'
            and 
                (statuspersetujuan=1 or statuspersetujuan2=1) and statushrd=1
            and
                isBatal = 0
            ".$notransaksi."
            order by 
            jlhbrs desc";
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
                kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'
            and 
                (statuspersetujuan=1 or statuspersetujuan2=1) 
            and 
                statushrd = 1
            and
                isBatal = 0
            ".$notransaksi."
            order by 
                tanggalbuat desc  
            limit 
                ".$offset.',20';
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
        if (0 == $bar->statuspertanggungjawaban) {
            $add .= "&nbsp <img src=images/application/application_edit.png class=resicon  title='FollowUp' onclick=\"editPPJD('".$bar->notransaksi."');\">\r\n         ";
        }

        if (2 == $bar->statuspertanggungjawaban) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspertanggungjawaban) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan = $_SESSION['lang']['wait_approve'];
            }
        }

        $str1 = 'select sum(jumlah) as jumlah, sum(jumlahhrd) as jumlahhrd from '.$dbname.".sdm_pjdinasdt\r\n         where notransaksi='".$bar->notransaksi."'";
        $res1 = mysql_query($str1);
        $usage = 0;
        while ($bar1 = mysql_fetch_object($res1)) {
            $usage = $bar1->jumlah;
            $usagehrd = $bar1->jumlahhrd;
        }
        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar->notransaksi."</td>
                    <td>".$namakaryawan."</td>
                    <td>".tanggalnormal($bar->tanggalbuat)."</td>
                    <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>
                    <td align=right>".number_format($bar->dibayar, 2, '.', ',')."</td>
                    <td align=right>".number_format($usage, 2, '.', ',')."</td>
                    <td align=right>".number_format($usagehrd, 2, '.', ',')."</td>
                    <td align=center>
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\">".$add."\r\n\t  
                    </td>
                </tr>";
    }
    echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>