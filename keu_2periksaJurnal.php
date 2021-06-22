<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=\"js/keu_laporan.js\"></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n    where CHAR_LENGTH(kodeorganisasi)=4\r\n    ";
} else {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n    where CHAR_LENGTH(kodeorganisasi)=4 and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%'\r\n    ";
}

$res = mysql_query($str);
$optunit = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optunit .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select distinct periode from '.$dbname.".setup_periodeakuntansi\r\n      order by periode desc\r\n      ";
$res = mysql_query($str);
$optperiode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optperiode .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
}
echo "<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanperiksajurnal'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td><td><select id=unit style='width:200px;' onchange=ambilJurnal()>";
echo $optunit;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td><td><select id=periode style='width:200px;' onchange=ambilJurnal()>";
echo $optperiode;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['nojurnal'].' '.$_SESSION['lang']['dari'];
echo "</label></td><td><select id=jurnaldari style='width:200px;' onchange=hideById('printPanel')><option value=\"\"></option></select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['nojurnal'].' '.$_SESSION['lang']['sampai'];
echo "</label></td><td><select id=jurnalsampai style='width:200px;' onchange=hideById('printPanel')><option value=\"\"></option></select></td></tr>\r\n\r\n<tr height=\"20\"><td colspan=\"2\"><button class=mybutton onclick=getLaporanPeriksaJurnal()>";
echo $_SESSION['lang']['proses'];
echo "</button></td></tr>\r\n\r\n\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=periksajurnalKeExcel(event,'keu_slave_2periksaJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=periksajurnalKePDF(event,'keu_slave_2periksaJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>    \r\n\t <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['kredit']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['selisih']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();
exit();

?>