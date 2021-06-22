<?php
    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'lib/zMysql.php';
    echo open_body();
    include 'master_mainMenu.php';
    echo "<script language=javascript src=js/sdm_pembayaranPJD.js></script>\r\n";
    //OPEN_BOX('', $_SESSION['lang']['pembayaranclaim']);
    OPEN_BOX('', "Pembayaran Uang Muka");
    echo "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\r\n\t  <button class=mybutton onclick=cariPJD(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['notransaksi']."</td>\r\n\t  <td>".$_SESSION['lang']['karyawan']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalsurat']."</td>\r\n\t  <td>".$_SESSION['lang']['tujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['approval_status']."</td>\r\n\t  <td>".$_SESSION['lang']['hrd']."</td>\r\n\t  <td>".$_SESSION['lang']['uangmuka']."</td>\r\n\t  <td>".$_SESSION['lang']['dibayar']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggalbayar']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>";
    $limit = 20;
    $page = 0;
    $notransaksi = '';
    if (isset($_POST['tex'])) {
        $notransaksi .= " and notransaksi like '%".$_POST['tex']."%' ";
    }

    $lokasitugas = substr($_SESSION['empl']['lokasitugas'], 0, 4);
    $str = 'select count(*) as jlhbrs from '.$dbname.".sdm_pjdinasht \r\n        where\r\n\t\tkodeorg='".$lokasitugas."'\r\n\t\t".$notransaksi."\r\n\t\torder by jlhbrs desc";
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
    $str = 'select * from '.$dbname.".sdm_pjdinasht \r\n        where\r\n        kodeorg='".$lokasitugas."'\r\n\t\t".$notransaksi."\r\n\t\torder by tanggalbuat desc  limit ".$offset.',20';
    $res = mysql_query($str);
    $no = $page * $limit;
    // print_r($str);
    // die();
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
        $dissa = null;
        
        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
            $dissa = ' disabled ';
        } else {
            if (1 == $bar->statuspersetujuan) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
                $dissa = null;
            } else {
                $stpersetujuan = $_SESSION['lang']['wait_approve'];
                $dissa = ' disabled ';
            }
        }

        if (2 == $bar->statushrd) {
            $sthrd = $_SESSION['lang']['ditolak'];
            $dissa = ' disabled ';
        } else {
            if (1 == $bar->statushrd) {
                $sthrd = $_SESSION['lang']['disetujui'];
                $dissa = null;
            } else {
                $sthrd = $_SESSION['lang']['wait_approve'];
                $dissa = ' disabled ';
            }
        }

        if (1 == $bar->lunas) {
            $dissa = ' disabled ';
        }
        
        //modif last request, kalo udah dibayar gak bisa di edit lagi
        if ((real)$bar->dibayar > 0) {
            $dissa = ' disabled ';
        }
        
        if ($bar->tglbayar != "0000-00-00") {
            $dissa = ' disabled ';
        }

        echo "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td>".$sthrd."</td>\r\n\t  <td align=right>".number_format($bar->uangmuka, 2, ',', '.')."</td>\t\r\n\t\t  <td align=right>";
        if( empty($dissa) ){
            echo "<img src='images/puzz.png' id='btnGet' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value='".number_format($bar->uangmuka, 2, '.', ',')."'\">\r\n\t\t     ";             
        }
        
        echo "<input ".$dissa.' type=text id=bayar'.$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12 value='".number_format($bar->dibayar, 2, '.', ',')."'></td>\r\n\t\t  <td align=right><input ".$dissa.' type=text id=tglbayar'.$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".tanggalnormal($bar->tglbayar)."'></td>\r\n\t\t  <td>";
        if( empty($dissa)){
            echo "<img src='images/save.png' id='btnSave' title='Save' class=resicon onclick=saveBayarPJD('".$no."','".$bar->notransaksi."')>\r\n\t\t      ";
        }	
        echo "<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n\t\t  </td>\r\n\r\n\t  </tr>";
    }
    echo "  <tr>
                <td colspan=11 align=center>
                    ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."
                    <br>
                    <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>
                    <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>
                </td>
            </tr>";
    echo "</tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>";
    drawTab('FRM', $hfrm, $frm, 100, 900);
    CLOSE_BOX();
    echo close_body('');

?>