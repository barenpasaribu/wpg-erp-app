<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/log_permintaan_gudang_mris.js'></script>\r\n\r\n";
include 'master_mainMenu.php';
if (isTransactionPeriod()) {
    OPEN_BOX('', '<b>'.$_SESSION['lang']['permintaangudang'].' MRIS :</b>');
    $frm[0] = '';
    $frm[1] = '';
    echo '<fieldset><legend>';
    echo ' <b>'.$_SESSION['lang']['periode'].': <span id=displayperiod>'.tanggalnormal($_SESSION['org']['period']['start']).' - '.tanggalnormal($_SESSION['org']['period']['end']).'</span></b>';
    echo '</legend>';
//    $str = "select namaorganisasi,kodeorganisasi from $dbname.organisasi ".
//        "where tipe like 'GUDANG%' and ".
//        "left(kodeorganisasi,4) in(select kodeunit from $dbname.bgt_regional_assignment ".
//        "where regional='".$_SESSION['empl']['regional']."') and ".
//        "kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi desc";
//    $str = getQuery("gudang");
//    $res = mysql_query($str);
//    $optsloc = "<option value=''></option>";
//    while ($bar = mysql_fetch_object($res)) {
//        $optsloc .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
//    }
    $optsloc = makeOption2(getQuery("gudang"),
        array("valuefield"=>'',"captionfield"=> $_SESSION['lang']['pilihgudang'] ),
        array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
        );
    echo "<fieldset>\r\n     <legend>\r\n\t ".$_SESSION['lang']['daftargudang']."\r\n     </legend>\r\n\t  ".$_SESSION['lang']['pilihgudang'].': <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>'.$optsloc."</select>\r\n\t   ".$_SESSION['lang']['ptpemilikbarang']."<select id=pemilikbarang style='width:200px;'>\r\n\t   <option value=''></option>\r\n\t   </select>\r\n\t   <button onclick=setSloc('simpan') class=mybutton id=btnsloc>".$_SESSION['lang']['save']."</button>\r\n\t   <button onclick=setSloc('ganti') class=mybutton>".$_SESSION['lang']['ganti']."</button>\t  \r\n\t </fieldset>";
    foreach ($_SESSION['gudang'] as $key => $val) {
        echo "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\r\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\r\n\t\t";
    }
    $optlokasitujuan = "<option value=''></option>";
    $optlokasitujuan .= ambilUnitPembebananBarang('', $_SESSION['empl']['lokasitugas']);
    $optsubunit = "<option value=''></option>";
    $optKegiatan = "<option value=''></option>";
    $strf = 'select kodekegiatan,kelompok,namakegiatan from '.$dbname.'.setup_kegiatan order by kelompok,namakegiatan';
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $optKegiatan .= "<option value='".$barf->kodekegiatan."'>[".$barf->kelompok.']-'.$barf->namakegiatan.'</option>';
    }
    $optionm = "<option value=''></option>";
    $str = 'select * from '.$dbname.".vhc_5master \t order by kodetraksi,kodevhc";
    $res = mysql_query($str);
    while ($bar1 = mysql_fetch_object($res)) {
        $str = 'select namajenisvhc from '.$dbname.".vhc_5jenisvhc where jenisvhc='".$bar1->jenisvhc."'";
        $res1 = mysql_query($str);
        $namabarang = '';
        while ($bar = mysql_fetch_object($res1)) {
            $namabarang = $bar->namajenisvhc;
        }
        $optionm .= "<option value='".$bar1->kodevhc."'>".$bar1->kodevhc.' - '.$namabarang.'-'.$bar1->kodetraksi.'</option>';
    }
    $frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['header'].'</legend>';
    $frm[0] .= "<table cellspacing=1 border=0>\r\n     <tr>\r\n\t\t<td>".$_SESSION['lang']['momordok']."</td>\r\n\t\t<td><input type=text id=nodok size=25 disabled class=myinputtext></td>\t \r\n\t    <td>".$_SESSION['lang']['tanggal']."</td><td>\r\n\t\t     <input type=text class=myinputtext id=tanggal size=12 onmousemove=setCalendar(this.id) maxlength=10 onkeypress=\"return false;\" value='".date('d-m-Y')."'>\r\n\t\t</td>\r\n\t </tr>\r\n\t <tr>\r\n\t <td>".$_SESSION['lang']['untukunit']."</td><td><select id=untukunit onchange=loadSubunit(this.options[this.selectedIndex].value,'','') style='width:200px;'>".$optlokasitujuan."</select></td>\r\n\t <td>".$_SESSION['lang']['subunit']."</td><td><select id=subunit onchange=loadBlock(this.options[this.selectedIndex].value,'')>".$optsubUnit."</select>\r\n \t    <input type=hidden value='insert' id=method>\r\n\t </td>\r\n\t </tr>\r\n\t <tr>\r\n\t <!--td>".$_SESSION['lang']['penerima'].'</td><td><select id=penerima style=width:200px>'.$optsubUnit."</select></td-->\r\n\t <td>".$_SESSION['lang']['penerima']."</td><td><select id=penerima style=width:200px></select></td>\r\n\t <td>".$_SESSION['lang']['note']."</td><td><input type=text id=catatan class=myinputtext onkeypress=\"return tanpa_kutip(event);\" size=40 maslength=80></td>\r\n\t </tr>\r\n\r\n\t </table>\r\n    </fieldset>\r\n    <fieldset>\r\n\t   <legend>".$_SESSION['lang']['detail']."</legend>\r\n\t   <div id=container>\r\n\t   <table class=sortable cellspacing=1 border=0>\r\n\t\t   <thead>\r\n\t\t   <tr class=rowheader>\r\n\t\t    <td>Kode.Barang</td>\r\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['blok']."/".$_SESSION['lang']['mesin']."</td>\r\n\t\t\t<td>Kendaraan & AB</td>\r\n\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td>\r\n\t\t\t</tr>\r\n\t\t   </thead>\r\n\t\t\t   <tbody>\r\n\t\t\t\t   <tr class=rowcontent>\r\n\t\t\t\t    <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\r\n\t\t\t\t\t<td><input type=text size=45 maxlength=100 id=namabarang class=myinputtext readonly onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\r\n\t\t\t\t\t<td><input type=text size=5 maxlength=5 id=satuan class=myinputtext  onkeypress=\"return false;\" onclick=\"showWindowBarang('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['namabarang']."',event);\"></td>\r\n\t\t\t\t\t<td><input type=text size=8 maxlength=10 id=qty value=0 class=myinputtextnumber onkeypress=\"return angka_doang(event);\"></td>\r\n\t\t\t\t\t<td><select id=blok style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'BLOK')></select></td>\r\n\t\t\t\t\t<td><select id=mesin style='width:100px;' onchange=getKegiatan(this.options[this.selectedIndex].value,'TRAKSI')>".$optionm."</select></td>\r\n\t\t\t\t\t<td><select id=kegiatan style='width:100px;'>".$optKegiatan."</select></td>\r\n\t\t \t\t   </tr>\t\t\t   \r\n\t\t\t   </tbody>\r\n\t\t   <tfoot>\r\n\t\t   </tfoot>\r\n\t   </table>\r\n\t   </div>\r\n\t   <button onclick=saveItemBast() class=mybutton>".$_SESSION['lang']['save']."</button>\r\n\t   <button onclick=nextItem() class=mybutton>".$_SESSION['lang']['cancel']."</button>\t\r\n\t   <button onclick=bastBaru() class=mybutton>".$_SESSION['lang']['done']."</button>\t \r\n\t </fieldset>\r\n\r\n    <fieldset>\r\n\t   <legend>".$_SESSION['lang']['datatersimpan']."</legend>\r\n\t   <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t\t   <thead>\r\n\t\t   <tr class=rowheader>\r\n\t\t   <td>No</td>\r\n\t\t    <td>".$_SESSION['lang']['kodebarang']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['namabarang']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['satuan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['jumlah']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['untukunit']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['kodeblok']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['kegiatan']."</td>\r\n\t\t\t<td>".$_SESSION['lang']['kodenopol']."</td>\r\n\t\t\t<td></td>\r\n \t\t   </tr>\r\n\t\t   </thead>\r\n\t\t\t   <tbody id=bastcontainer>\t\t\t   \r\n\t\t\t   </tbody>\r\n\t\t   <tfoot>\r\n\t\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\r\n\t \t \r\n\t ";
    $frm[1] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\r\n\t  <button class=mybutton onclick=cariBast()>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sloc']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['untukunit']."</td>\t  \t \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td>".$_SESSION['lang']['posted']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";
    $hfrm[0] = $_SESSION['lang']['pengeluaranbarang'];
    $hfrm[1] = $_SESSION['lang']['list'];
    drawTab('FRM', $hfrm, $frm, 200, 900);
} else {
    echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
