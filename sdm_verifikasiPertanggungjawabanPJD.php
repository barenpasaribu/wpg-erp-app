<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    echo open_body();
    echo "\r\n<script language=javascript1.2 src=js/sdm_verPertanggungjawabanPJD.js></script>\r\n";
    include 'master_mainMenu.php';
    OPEN_BOX('', $_SESSION['lang']['verifikasi']);
    $frm[1] .= "<fieldset>
                    <legend>".$_SESSION['lang']['form']."</legend>
                    <table>
                        <tr>
                            <td>".$_SESSION['lang']['notransaksi']."</td>
                            <td>
                                <input type=text class=myinputtext id=notransaksi disabled value=''>
                            </td>
                        </tr>
                    </table>
                    <fieldset>
                        <legend>".$_SESSION['lang']['datatersimpan']."</legend>
                        <table class=sortable cellspacing=1>
                            <thead>
                                <tr>
                                    <td>No.</td>
                                    <td>".$_SESSION['lang']['tanggal']."</td>
                                    <td>".$_SESSION['lang']['jenisbiaya']."</td>
                                    <td>".$_SESSION['lang']['keterangan']."</td>
                                    <td>".$_SESSION['lang']['jumlah']."</td>
                                    <td>".$_SESSION['lang']['disetujui']."</td>
                                </tr>
                            </thead>
                            <tbody id=innercontainer></tbody>
                            <tfoot></tfoot>
                        </table>
                        <button class=mybutton onclick=selesai()>".$_SESSION['lang']['done']."</button>
                        <button class=mybutton onclick=batalkan()>".$_SESSION['lang']['cancel']."</button>
                    </fieldset>
                </fieldset>";
    $frm[0] = " <fieldset>
                    <legend>".$_SESSION['lang']['list']."</legend>
                    <fieldset><legend></legend>
                        ".$_SESSION['lang']['cari_transaksi']."
                        <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>
                        <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>
                    </fieldset>
                    <table class=sortable cellspacing=1 border=0>
                        <thead>
                            <tr class=rowheader>
                                <td>No.</td>
                                <td>".$_SESSION['lang']['notransaksi']."</td>
                                <td>".$_SESSION['lang']['karyawan']."</td>
                                <td>".$_SESSION['lang']['tanggalsurat']."</td>
                                <td>".$_SESSION['lang']['tujuan']."</td>
                                <td>".$_SESSION['lang']['uangmuka']."</td>
                                <td>Digunakan</td>
                                <td>Disetujui</td>
                                <td></td>
                            </tr>
                        </head>
                        <tbody id=containerlist>";
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= " and notransaksi like '%".$_POST['tex']."%' ";
    }

    $str = 'select count(*) as jlhbrs 
            from 
                '.$dbname.".sdm_pjdinasht 
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
    $str = 'select * 
            from 
                '.$dbname.".sdm_pjdinasht 
            where
                kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'
            and 
                (statuspersetujuan=1 or statuspersetujuan2=1) 
            and 
                statushrd=1
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
        $frm[0] .= "<tr class=rowcontent>
                        <td>".$no."</td>
                        <td>".$bar->notransaksi."</td>
                        <td>".$namakaryawan."</td>
                        <td>".tanggalnormal($bar->tanggalbuat)."</td>
                        <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>
                        <td align=right>".number_format($bar->dibayar, 2, '.', ',')."</td>
                        <td align=right>".number_format($usage, 2, '.', ',')."</td>
                        <td align=right>".number_format($usagehrd, 2, '.', ',')."</td>
                        <td align=center>
                        <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n       ".$add."\r\n\t  </td>\r\n\t  </tr>";
    }
    $frm[0] .= "    <tr>
                        <td colspan=11 align=center>
                            ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs." <br>
                            <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>
                            <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>
                        </td>
                    </tr>";
    $frm[0] .= "</tbody>
                <tfoot></tfoot>
            </table>
        </fieldset>";
    $hfrm[1] = $_SESSION['lang']['form'];
    $hfrm[0] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body();

?>