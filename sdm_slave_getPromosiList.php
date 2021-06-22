<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_riwayatjabatan where nomorsk like '%".$notransaksi."%'\r\n\t\tand left(nomorsk,4)='".$_SESSION['empl']['lokasitugas']."'\r\n\t\torder by jlhbrs desc";
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
    $str = 'select * from '.$dbname.".sdm_riwayatjabatan where nomorsk like '%".$notransaksi."%'\r\n        and left(nomorsk,4)='".$_SESSION['empl']['lokasitugas']."'\r\n\t\torder by nomorsk desc limit ".$offset.',20';
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
        $namapembuat = '';
        $stry = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->updateby;
        $resy = mysql_query($stry);
        while ($bary = mysql_fetch_object($resy)) {
            $namapembuat = $bary->namakaryawan;
        }
        echo "  <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".$bar->nomorsk."</td>
                    <td>".$namakaryawan."</td>
                    <td>".tanggalnormal($bar->tanggalsk)."</td>
                    <td>".$bar->tipesk."</td>
                    <td>".$namapembuat."</td>
                    <td align=center>
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewSK('".$bar->nomorsk."',event);\"> 
                         &nbsp 
                        <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editSK('".$bar->nomorsk."','".$bar->karyawanid."');\">
                        &nbsp 
                        <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delSK('".$bar->nomorsk."','".$bar->karyawanid."');\">
                    </td>
                </tr>";
    }
    echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariSK(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariSK(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>