<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/log_returkegudang.js'></script>\n";
include 'master_mainMenu.php';
if (isTransactionPeriod()) {
    OPEN_BOX('', '<b>'.$_SESSION['lang']['retur'].' (Ke Gudang):</b>');
    $frm[0] = '';
    $frm[1] = '';
    echo '<fieldset><legend>';
    echo ' <b>'.$_SESSION['lang']['periode'].': <span id=displayperiod>'.tanggalnormal($_SESSION['org']['period']['start']).' - '.tanggalnormal($_SESSION['org']['period']['end']).'</span></b>';
    echo '</legend>';
    if ('KANWIL' === $_SESSION['empl']['tipelokasitugas'] && 'PK' !== substr($_SESSION['empl']['subbagian'], -2)) {
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe in ('GUDANG','GUDANGTEMP')\n       and left(induk,4) in(select kodeunit from ".$dbname.".bgt_regional_assignment where regional='".$_SESSION['empl']['regional']."')\n       order by namaorganisasi desc";
    } else {
        $str = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where (left(induk,4)='".$_SESSION['empl']['lokasitugas']."' or kodeorganisasi='".$_SESSION['empl']['lokasitugas']."') and tipe in ('GUDANG','GUDANGTEMP') order by namaorganisasi desc";
    }

    $res = mysql_query($str);
    $optsloc = "<option value=''></option>";
    while ($bar = mysql_fetch_object($res)) {
        $optsloc .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
    }
    echo "<fieldset>\n     <legend>\n\t ".$_SESSION['lang']['daftargudang']."\n     </legend>\n\t  ".$_SESSION['lang']['pilihgudang'].': <select id=sloc>'.$optsloc."</select>\n\t   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>\n\t   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>\n\t  \n\t </fieldset>";
    $frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[0] .= "<table cellspacing=1 border=0>\n     <tr>\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \n\t    <td>".$_SESSION['lang']['tanggalretur']."</td><td>\n\t\t     <input type=text class=myinputtext id=tanggal size=25 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\n\t\t</td>\n\t </tr>\n\t </table>\n\t <fieldset><legend>".$_SESSION['lang']['dokumenlama']."</legend>\n\t <table>\n\t <tr>\n\t <td>".$_SESSION['lang']['nomorlama']."</td><td><input type=text id=nomorlama class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\"></td>\n\t <td>".$_SESSION['lang']['kodebarang']."</td><td><input type=text id=kodebarang class=myinputtext size=25 maxength=11 onkeypress=\"return angka_doang(event);\">\n         <td>".$_SESSION['lang']['kodeblok']."</td><td><input type=text id=kodeblok class=myinputtext size=25 maxength=11 onkeypress=\"return tanpa_kutip_dan_sepasi(event);\">    \n\t       <button class=mybutton onclick=Fverify()>".$_SESSION['lang']['cek']."</button>\n\t </td>\n\t </tr>\n\t <tr>\n\t <td>".$_SESSION['lang']['namabarang']."</td><td><input type=text id=namabarang class=myinputtext size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled></td>\n\t <td>".$_SESSION['lang']['jumlah']."</td><td><input type=text id=jlhlama class=myinputtextnumber size=25 maxength=25 onkeypress=\"return tanpa_kutip(event);\" disabled>\n\t <input type=text id=satuan size=6 disabled class=myinputtext>\n\t </td>\n\t </tr>\n\t </table>\n\t </fieldset>\n\t <fieldset><legend>".$_SESSION['lang']['jumlahkembali']."</legend>\n\t ".$_SESSION['lang']['jumlahkembali'].": <input type=text id=jlhretur disabled value=0 class=myinputtextnumber size=10 maxlength=6 onkeypress=\"return tanpa_kutip(event);\">\n\t <input type=hidden id=hargasatuan value='0'>\n\t <input type=hidden id=kodept value=''>\n\t <input type=hidden id=untukunit value=''>\n\t <input type=hidden id=untukpt value=''>\n\t ".$_SESSION['lang']['keterangan']."\n\t <input type=text id=keterangan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=25 maxlength=80>\n\t <button id=savebutton class=mybutton onclick=simpanRetur() disabled>".$_SESSION['lang']['save']."</button>\n\t <button id=savebutton class=mybutton onclick=window.location.reload()>".$_SESSION['lang']['cancel']."</button>\n\t </fieldset>\n\t ";
    foreach ($_SESSION['gudang'] as $key => $val) {
        $frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\n\t\t";
    }
    $frm[0] .= "</fieldset>\n\t ";
    $frm[1] .= "<fieldset>\n\t   <legend>".$_SESSION['lang']['list']."</legend>\n\t  <fieldset><legend></legend>\n\t  ".$_SESSION['lang']['cari_transaksi']."\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=9>\n\t  <button class=mybutton onclick=cariBapb()>".$_SESSION['lang']['find']."</button>\n\t  </fieldset>\n\t  <table class=sortable cellspacing=1 border=0>\n      <thead>\n\t  <tr class=rowheader>\n\t  <td>No.</td>\n\t  <td>".$_SESSION['lang']['sloc']."</td>\n\t  <td>".$_SESSION['lang']['tipe']."</td>\n\t  <td>".$_SESSION['lang']['momordok']."</td>\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\n\t  <td>".$_SESSION['lang']['pt']."</td>\n\t  <td>".$_SESSION['lang']['nopo']."</td>\t\n\t  <td>".$_SESSION['lang']['dari']."</td> \n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\n\t  <td>".$_SESSION['lang']['posted']."</td>\n\t  <td></td>\n\t  </tr>\n\t  </head>\n\t   <tbody id=containerlist>\n\t   </tbody>\n\t   <tfoot>\n\t   </tfoot>\n\t   </table>\n\t </fieldset>\t \n\t ";
    $hfrm[0] = $_SESSION['lang']['retur'];
    $hfrm[1] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 100, 900);
} else {
    echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
