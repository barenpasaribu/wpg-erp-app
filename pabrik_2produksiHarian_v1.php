<?php

    require_once 'master_validation.php';
    include 'lib/eagrolib.php';
    include 'master_mainMenu.php';

    echo open_body();
    echo "<script language=javascript1.2 src='js/pabrik_2produksi.js'></script>";

    $str = 'select 
                kodeorganisasi 
            from 
                '.$dbname.".organisasi 
            where 
                tipe='PABRIK'
            AND
            kodeorganisasi LIKE '".$_SESSION['empl']['lokasitugas']."%'
            order by 
                kodeorganisasi";
    $res = mysql_query($str);

    $optpabrik = '<option value=>Pilih Pabrik</option>';
    while ($bar = mysql_fetch_object($res)) {
        $optpabrik .= "<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi.'</option>';
    }
    $sPeriode = 'select distinct substring(tanggal,1,7) as periode from '.$dbname.'.pabrik_produksi order by tanggal desc ';
    $qPeriode = mysql_query($sPeriode);
    while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
        $optper .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
    }

    OPEN_BOX('', '<b>'.$_SESSION['lang']['rprodksiPabrik'].' :</b>');
    echo "  <fieldset>
                ".$_SESSION['lang']['kodeorganisasi'].':
                <select id=kodeorg>'.$optpabrik."</select>
                Dari
                <input autocomplete=off type=text class=myinputtext id=tanggal_awal onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                Sampai
                <input autocomplete=off type=text class=myinputtext id=tanggal_akhir onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\"> 
                <button class=mybutton onclick=getLaporan()>Cari</button>
            ";
    CLOSE_BOX();
    OPEN_BOX('', '');
    echo "<div id=container style='width:100%;height:500px overflow:scroll'>\r\n\r\n     </div>";
    CLOSE_BOX();
    close_body();

?>