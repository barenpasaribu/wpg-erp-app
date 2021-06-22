<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript1.2 src='js/kebun_5premipanen.js'></script>";
OPEN_BOX();
$optReg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//$sreg = 'select distinct regional from '.$dbname.".bgt_regional_assignment where kodeunit='".$_SESSION['empl']['lokasitugas']."' ";
$sreg = "select distinct regional from ".$dbname.".bgt_regional_assignment ";
$sreg .= "union select distinct kodeorganisasi from organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and length(kodeorganisasi)=4 and tipe='kebun'";
$sreg .= "union select distinct kodeorganisasi from organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='blok' and detail=1";
$qreg = mysql_query($sreg) ;
while ($rreg = mysql_fetch_assoc($qreg)) {
    $optReg .= "<option value='".$rreg['regional']."'>".$rreg['regional'].'</option>';
    $regDt = $rreg['regional'];
}
$arrDt = [1 => '1', 501 => '501', 1001 => '1001'];
foreach ($arrDt as $kei => $fal) {
    $optdenda .= "<option value='".$kei."'>".$fal.'</option>';
}
echo "<fieldset style='float:left;'>";
if ('ID' === $_SESSION['language']) {
    echo '<legend>Premi Panen Bulanan</legend>';
} else {
    echo '<legend>Premi Monthly Harvesting</legend>';
}

$blnOpt1 = '';
$blnOpt2 = '';
$arrBln1=array(
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "July",
    "August",
    "September",
    "October",
    "November",
    "Desember"
);
//echoMessage(" bulan",$arrBln1);
for ($i=1;$i<=12;$i++){
    $i < 10 ? $cch = '0'.$i : $cch = $i;
    if ($i==1) {$blnOpt1.="<option value='$i' selected>".$arrBln1[$i-1]."</option>";} else {
    $blnOpt1.="<option value='$i'>".$arrBln1[$i-1]."</option>";}

    if ($i==12) {$blnOpt2.="<option value='$i' selected>".$arrBln1[$i-1]."</option>";} else {
        $blnOpt2.="<option value='$i'>".$arrBln1[$i-1]."</option>";}
}

echo "<table border=0 cellpadding=1 cellspacing=1>" .
"<tr>" .
"<input type=hidden id=dataid value=''><td>".$_SESSION['lang']['regional']."</td><td>:</td>" .
"<td><select id=kodeorg style=width:150px>".$optReg."</select></td></tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['tahuntanam']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=4 id=tahuntanam onkeypress=return angka_doang(event);  class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>Bulan Awal</td>" .
"    <td>:</td>" .
"    <td><select id=bulanawal style=width:150px>".$blnOpt1."</select></td>" .
"</tr>" .
"<tr>" .
"    <td>Bulan Akhir</td>" .
"    <td>:</td>" .
"    <td><select id=bulanakhir style=width:150px>".$blnOpt2."</select></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['basiskg']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=8 id=hasil onkeypress=return angka_doang(event); value='' class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['hslpanen']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=8 id=hslpanen style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['lebihbasis2']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=8 id=lebihbasis onkeypress=return angka_doang(event); value='' class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"<td>".$_SESSION['lang']['rp']."</td>" .
"<td>:</td>" .
"<td><input type=text maxlength=8 id=rupiah onkeypress=return angka_doang(event);  class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['premirajin']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=8 id=premirajin onkeypress=return angka_doang(event);  class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['premihadir']."</td>" .
"    <td>:</td>" .
"    <td><input type=text maxlength=8 id=premihadir onkeypress=return angka_doang(event);  class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"    <td>".$_SESSION['lang']['brondolanperkg2']."</td>" .
"   <td>:</td>" .
"   <td><input type=text maxlength=8 id=brondolanperkg onkeypress=return angka_doang(event);  class=myinputtextnumber style='width:150px;'></td>" .
"</tr>" .
"<tr>" .
"<td colspan=3 align=center><button class=mybutton onclick=simpan()>".$_SESSION['lang']['save']."</button>".
"<button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button></td>".
"</tr></table></fieldset><input type=hidden id=method value='insert'>";
CLOSE_BOX();
echo "";
OPEN_BOX();
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend><div id=container> <script>loadData()</script></div></fieldset>";
CLOSE_BOX();
echo close_body();

?>