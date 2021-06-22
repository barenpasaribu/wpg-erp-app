<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/sdm_2alokasigaji.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['alokasigaji']).'</b>');
if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select distinct kodeorganisasi, namaorganisasi from '.$dbname.".organisasi\r\n      where length(kodeorganisasi) = 4\r\n\t  order by namaorganisasi desc";
} else {
    $str = 'select distinct kodeorganisasi, namaorganisasi from '.$dbname.".organisasi\r\n      where length(kodeorganisasi) = 4 and kodeorganisasi = '".$_SESSION['empl']['lokasitugas']."'\r\n\t  order by namaorganisasi desc";
}

$res = mysql_query($str);
$optunit = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optunit .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['alokasigaji']."</legend>\r\n\t ".$_SESSION['lang']['unit']."<select id=unit style='width:150px;' onchange=ambilPeriode2(this.options[this.selectedIndex].value)>".$optunit."</select>\r\n\t ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n\t <button class=mybutton onclick=getAlokasiGaji()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=alokasiGajiKeExcel(event,'sdm_laporanAlokasiGaji_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t </span>    \r\n\t <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center>No.</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['noreferensi']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();

?>