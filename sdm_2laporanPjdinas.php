<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src='js/sdm_2laporanPjdinas.js'></script>\r\n";
    OPEN_BOX('', $_SESSION['lang']['perjalanandinas']);
    $frm[0] = "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\r\n\t  <td>".$_SESSION['lang']['hrd']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
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
            order by tanggalbuat desc,notransaksi desc  limit ".$offset.',20';
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

        if ($bar->isBatal == 1) {
            $statuskolom = "<td colspan=2 align=center>".$bar->keterangan_batal."</td>";
        } else {
            $statuskolom = "<td>".$stpersetujuan."</td>
            <td>".$sthrd."</td>";
        }
        

        $frm[1] .= "    <tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$bar->notransaksi."</td>
                            <td>".$namakaryawan."</td>
                            <td>".tanggalnormal($bar->tanggalbuat)."</td>
                            <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain." "."</td>
                            ".$statuskolom."
                            <td align=center>
                                <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> 
                                ".$add."
                            </td>\r\n\t  </tr>";
    }
    $frm[1] .= "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";
    $frm[1] .= "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
    $hfrm[0] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');

?>