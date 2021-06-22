<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    echo "\r\n<script language=javascript1.2 src=js/sdm_pertanggungjawabanPJD.js></script>\r\n";
    include 'master_mainMenu.php';
    OPEN_BOX('', $_SESSION['lang']['pertanggungjawabandinas']);
    $frm[2] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJDUraian(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['uangmuka']."</td>\r\n\t  <td>".$_SESSION['lang']['digunakan']."</td>\t  \r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\t  \r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= " and notransaksi like '%".$_POST['tex']."%' and notransaksi like '%".$_SESSION['empl']['lokasitugas']."'  ";
    }else{
        $notransaksi .= " and notransaksi like '%".$_SESSION['empl']['lokasitugas']."'  ";
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht 
    where statuspertanggungjawaban=1 ".$notransaksi."\r\n\t\torder by jlhbrs desc";

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
    where statuspertanggungjawaban=1 ".$notransaksi."\r\n\t\torder by tanggalbuat desc  limit ".$offset.',20';

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
        if (2 == $bar->statuspertanggungjawaban) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspertanggungjawaban) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan = $_SESSION['lang']['wait_approve'];
            }
        }

        $str1 = 'select sum(jumlah) as jumlah from '.$dbname.".sdm_pjdinasdt\r\n         where notransaksi='".$bar->notransaksi."'";
        $res1 = mysql_query($str1);
        $usage = 0;
        while ($bar1 = mysql_fetch_object($res1)) {
            $usage = $bar1->jumlah;
        }
        $frm[2] .= "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>\r\n\t  <td align=right>".number_format($bar->dibayar, 2, '.', ',')."</td>\r\n\t  <td align=right>".number_format($usage, 2, '.', ',')."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td align=center>\r\n\t     <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Cost)' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n\t\t <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']." (Task Result Description)' onclick=\"previewPJDUraian('".$bar->notransaksi."',event);\"> \r\n       ".$add."\r\n\t  </td>\r\n\t  </tr>";
    }
    $frm[2] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJDUraian(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJDUraian(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
    $frm[2] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
    echo $frm[2];
    CLOSE_BOX();
    echo close_body();

?>