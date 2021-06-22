<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
include_once 'lib/zLib.php';
echo "\r\n<script language=javascript1.2 src='js/pabrik_pengapalanModo.js'></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src='js/iReport.js'></script>\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n\r\n\r\n";
$a = 'select notransaksi from '.$dbname.".pabrik_timbangan where notransaksi like 'H01M%' order by notransaksi desc limit 1";
$b = mysql_query($a);
$c = mysql_fetch_assoc($b);
$noLama = substr($c['notransaksi'], 4, 4);
if ('' === $noLama) {
    $noLama = 1;
} else {
    ++$noLama;
}

$nomor = addZero($noLama, 4);
$notranBaru = 'H01M'.$nomor;
$optNoKontrak = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$d = 'select kuantitaskontrak,selisih,nokontrak from '.$dbname.".pabrik_kontrakjual_vs_timbangan where kodept='HIP' and selisih is NULL or selisih>0 ";
$e = mysql_query($d);
while ($f = mysql_fetch_assoc($e)) {
    if ('' === $f['selisih'] || null === $f['selisih']) {
        $sisa = number_format($f['kuantitaskontrak']);
    } else {
        $sisa = number_format($f['selisih']);
    }

    $optNoKontrak .= "<option value='".$f['nokontrak']."'>".$f['nokontrak'].' - Sisa Belum Terkirim : '.$sisa.'</option>';
}
$optCust = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optBarang = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$optTransp = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$g = 'select supplierid,namasupplier from '.$dbname.".log_5supplier where supplierid like 'K002%'";
$h = mysql_query($g);
while ($i = mysql_fetch_assoc($h)) {
    $optTransp .= "<option value='".$i['supplierid']."'>".$i['namasupplier'].'</option>';
}
$optPer = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
$i = 'select distinct LEFT( tanggal, 7 ) as tanggal from '.$dbname.'.pabrik_timbangan limit 5';
$n = mysql_query($i);
while ($d = mysql_fetch_assoc($n)) {
    $optPer .= "<option value='".$d['tanggal']."'>".$d['tanggal'].'</option>';
}
$tgl = date('d-m-Y');
$arrExcel = '##perSch##notranSch##nokontrakSch';
echo "\r\n\r\n";
OPEN_BOX('', 'Pengapalan');
echo "<fieldset>";
echo '<legend><b>'.$_SESSION['lang']['form'].'</b></legend>';
echo '<table border=0 cellpadding=1 cellspacing=1>';
echo "\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['notransaksi']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=10 disabled  value='".$notranBaru."' id=notran onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['tanggal']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text class=myinputtext  id=tgl value=".$tgl." onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style=\"width:200px;\"/></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['pabrik']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=4 disabled value='H01M' id=kodeorg onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>\r\n\t\t\t\t\t</tr> \r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['NoKontrak']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=nokontrak onchange=getCust() style=\"width:200px;\">".$optNoKontrak."</select></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['nodo']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=50   id=nodo onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['nmcust']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=kdCust style=\"width:200px;\">".$optCust."</select></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['kodebarang']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=kdbarang style=\"width:200px;\">".$optBarang."</select></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['kodekapal']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text maxlength=50 id=kdKapal onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\"></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['transporter']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><select id=transp style=\"width:200px;\">".$optTransp."</select></td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>".$_SESSION['lang']['beratnormal']."</td> \r\n\t\t\t\t\t\t<td>:</td>\r\n\t\t\t\t\t\t<td><input type=text value=0 id=berat onkeypress=\"return angka_doang(event);\"  class=myinputtext style=\"width:200px;\"> Kg</td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td>\r\n\t\t\t\t\t\t\t<button class=mybutton onclick=simpan()>Simpan</button>\r\n\t\t\t\t\t\t\t<button class=mybutton onclick=cancel()>Hapus</button>\r\n\t\t\t\t\t\t</td>\r\n\t\t\t\t\t<tr>\r\n\t\t\t</table></fieldset><input type=hidden id=method value='insert'>";
echo "<fieldset>\r\n\t\t<legend>".$_SESSION['lang']['list']."</legend>\r\n\t\t".$_SESSION['lang']['periode'].' : <select id=perSch style="width:100px;" onchange=loadData()>'.$optPer."</select>\r\n\t\t".$_SESSION['lang']['notransaksi']." : <input type=text id=notranSch onblur=loadData() onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\">\r\n\t\t".$_SESSION['lang']['NoKontrak']." : <input type=text id=nokontrakSch onblur=loadData() onkeypress=\"return_tanpa_kutip(event);\"  class=myinputtext style=\"width:200px;\">\r\n\r\n\t\t\r\n\t\t<img onclick=iExcel(event,'pabrik_slave_pengapalanModo.php','".$arrExcel."') src=images/excel.jpg class=resicon title='excel'> \r\n\t\t\r\n\t\t\r\n\t\t\r\n\t\t<div id=container> \r\n\t\t\t<script>loadData()</script>\r\n\t\t</div>\r\n\t</fieldset>";
CLOSE_BOX();
echo close_body();

?>