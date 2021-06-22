<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=js/keu_laporan.js></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['laporanjurnal']).'</b>');
$str = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".keu_jurnaldt\r\n      order by periode desc";
$res = mysql_query($str);
$optper = "<option value=''>".$_SESSION['lang']['sekarang'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n      where tipe='PT'\r\n\t  order by namaorganisasi desc";
$res = mysql_query($str);
$optpt = '';
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select distinct a.kodeorg,b.namaorganisasi from '.$dbname.".setup_periodeakuntansi a\r\n      left join ".$dbname.".organisasi b\r\n\t  on a.kodeorg=b.kodeorganisasi\r\n      where b.tipe='GUDANG'\r\n\t  order by namaorganisasi desc";
$res = mysql_query($str);
$optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
}
echo "<fieldset>\r\n     <legend>".$_SESSION['lang']['laporanjurnal']."</legend>\r\n\t ".$_SESSION['lang']['pt']."<select id=pt style='width:200px;' onchange=hideById('printPanel')>".$optpt."</select>\r\n\t ".$_SESSION['lang']['periode']."<select id=periode onchange=hideById('printPanel')>".$optper."</select>\r\n\t <button class=mybutton onclick=getLaporanJurnal()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=fisikKeExcel(event,'keu_laporanJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=fisikKePDF(event,'keu_laporanJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>    \r\n\t <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center>No.</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t  <td align=center>".$_SESSION['lang']['uraian']."</td>\r\n\t\t\t  <td align=center width=120>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td align=center width=120>".$_SESSION['lang']['kredit']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();

?>