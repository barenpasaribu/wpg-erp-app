<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src='js/sdm_persetujuanPJD.js'></script>\r\n";
    OPEN_BOX('', $_SESSION['lang']['persetujuanpjdinas']);
    echo "  <fieldset>
                <legend>".$_SESSION['lang']['list']."</legend>
                    <fieldset>
                        <legend></legend>
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
                                <td>".$_SESSION['lang']['approval_status']." 1</td>
                                <td>".$_SESSION['lang']['approval_status']." 2</td>
                                <td>".$_SESSION['lang']['hrd']."</td>
                                <td></td>
                            </tr>
                        </head>
                        <tbody id=containerlist>";
    $limit = 20;
    $page = 0;
    if (isset($_POST['tex'])) {
        $notransaksi .= $_POST['tex'];
    }

    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where\r\n\t\tpersetujuan=".$_SESSION['standard']['userid']."\r\n\t\tor hrd=".$_SESSION['standard']['userid']."\r\n\t\torder by jlhbrs desc";
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
    $str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n        persetujuan=".$_SESSION['standard']['userid']."\r\n\t\tor persetujuan2=".$_SESSION['standard']['userid']."\r\n\t\tor hrd=".$_SESSION['standard']['userid']."\r\n\t\torder by tanggalbuat desc  limit ".$offset.',20';
    $res = mysql_query($str);
    $no = $page * $limit;

    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        if ($bar->persetujuan == $_SESSION['standard']['userid']) {
            $per = 'persetujuan';
        } else {
            if ($bar->persetujuan2 == $_SESSION['standard']['userid']) {
                $per = 'persetujuan2';
            } else {
                $per = 'hrd';
            }
        }

        $namakaryawan = '';
        $strx = 'select namakaryawan from '.$dbname.'.datakaryawan where karyawanid='.$bar->karyawanid;
        $resx = mysql_query($strx);
        
        while ($barx = mysql_fetch_object($resx)) {
            $namakaryawan = $barx->namakaryawan;
        }

        $add = '';
        if (0 == $bar->statuspersetujuan && 'persetujuan' == $per) {
            $add .= "&nbsp 
                    <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\"> &nbsp 
                    <img src=images/onebit_33.png class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\"> &nbsp 
                    ";
        }

        if (0 == $bar->statuspersetujuan2 && 'persetujuan2' == $per) {
            $add .= "&nbsp 
                    <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\"> &nbsp 
                    <img src=images/onebit_33.png class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\"> &nbsp 
                    ";
        }

        if (0 == $bar->statushrd && 'hrd' == $per) {
            $add .= "&nbsp 
                    <img src=images/onebit_34.png class=resicon  title='".$_SESSION['lang']['disetujui']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',1,'".$per."');\"> &nbsp 
                    <img src=images/onebit_33.png class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"approvePJD('".$bar->notransaksi."','".$bar->karyawanid."',2,'".$per."');\"> &nbsp 
                    ";
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

        if (2 == $bar->statuspersetujuan2) {
            $stpersetujuan2 = $_SESSION['lang']['ditolak'];
        } else {
            if (1 == $bar->statuspersetujuan2) {
                $stpersetujuan2 = $_SESSION['lang']['disetujui'];
            } else {
                $stpersetujuan2 = $_SESSION['lang']['wait_approve'];
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

        echo "              <tr class=rowcontent>";
        echo "                  <td>".$no."</td>";
        echo "                  <td>".$bar->notransaksi."</td>";
        echo "                  <td>".$namakaryawan."</td>";
        echo "                  <td>".tanggalnormal($bar->tanggalbuat)."</td>";
        echo "                  <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>";
        if ($bar->isBatal == 1) {
        echo "                  <td colspan=3 align=center>".$bar->keterangan_batal."</td>";
        } else {
        echo "                  <td>".$stpersetujuan."</td>";
        echo "                  <td>".$stpersetujuan2."</td>";
        echo "                  <td>".$sthrd."</td>";
        }
        
        
        echo "                  <td align=center>";
        echo "                      <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\">";
        echo "                      ".$add." &nbsp";
        if ($bar->isBatal == 0 && $bar->statuspertanggungjawaban == 0 && $bar->statuspersetujuan == 1 && $bar->statuspersetujuan2 == 1 && $bar->statushrd == 1) {
        echo "                      <img src='images/application/application_delete.png' class='resicon'  title='Batalkan Perjalanan Dinas' onclick=\"batalPJD('".$bar->notransaksi."');\" /> ";
        }
        echo "                      <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJDPDF('".$bar->notransaksi."',event);\">";
        echo "                  </td>
                            </tr>";
    }
    echo "                  <tr>
                                <td colspan=11 align=center>
                                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs." <br>
                                    <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>
                                    <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>
                                </td>
                            </tr>";
    echo "              </tbody>
                        <tfoot></tfoot>
                    </table>
                </fieldset>";
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');

?>