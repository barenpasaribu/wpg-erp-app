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
        $dissa = '';
        if (2 == $bar->statuspersetujuan) {
            $stpersetujuan = $_SESSION['lang']['ditolak'];
            $dissa = ' disabled ';
        } else {
            if (1 == $bar->statuspersetujuan) {
                $stpersetujuan = $_SESSION['lang']['disetujui'];
                $dissa = '';
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
                $dissa = '';
            } else {
                $sthrd = $_SESSION['lang']['wait_approve'];
                $dissa = ' disabled ';
            }
        }

        if (1 == $bar->lunas) {
            $dissa = ' disabled ';
        }

        echo "<tr class=rowcontent>\r\n\t  <td>".$no."</td>\r\n\t  <td>".$bar->notransaksi."</td>\r\n\t  <td>".$namakaryawan."</td>\r\n\t  <td>".tanggalnormal($bar->tanggalbuat)."</td>\r\n\t  <td>".$bar->tujuan1." ".$bar->tujuan2." ".$bar->tujuan3." ".$bar->tujuanlain."</td>\r\n\t  <td>".$stpersetujuan."</td>\r\n\t  <td>".$sthrd."</td>\r\n\t  <td align=right>".number_format($bar->uangmuka, 2, ',', '.')."</td>\t\r\n\t\t  <td align=right><img src='images/puzz.png' style='cursor:pointer;' title='click to get value' onclick=\"document.getElementById('bayar".$no."').value='".number_format($bar->uangmuka, 2, '.', ',')."'\">\r\n\t\t                  <input ".$dissa.' type=text id=bayar'.$no." class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=12 onblur=change_number(this) size=12 value='".number_format($bar->dibayar, 2, '.', ',')."'></td>\r\n\t\t  <td align=right><input ".$dissa.' type=text id=tglbayar'.$no." class=myinputtext onkeypress=\"return false;\" maxlength=10  size=10 onmouseover=setCalendar(this) value='".tanggalnormal($bar->tglbayar)."'></td>\r\n\t\t  <td><img src='images/save.png' title='Save' class=resicon onclick=saveBayarPJD('".$no."','".$bar->notransaksi."')>\r\n\t\t      <img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewPJD('".$bar->notransaksi."',event);\"> \r\n\t\t  </td>\r\n\r\n\t  </tr>";
    }
    echo "<tr><td colspan=11 align=center>\r\n       ".($page * $limit + 1).' to '.($page + 1) * $limit.' Of '.$jlhbrs."\r\n\t   <br>\r\n       <button class=mybutton onclick=cariPJD(".($page - 1).');>'.$_SESSION['lang']['pref']."</button>\r\n\t   <button class=mybutton onclick=cariPJD(".($page + 1).');>'.$_SESSION['lang']['lanjut']."</button>\r\n\t   </td>\r\n\t   </tr>";

?>