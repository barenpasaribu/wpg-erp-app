<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=\"js/keu_laporan.js\"></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$str = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".keu_jurnaldt\r\n      order by periode desc";
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n              where tipe='PT'\r\n                  order by namaorganisasi desc";
$res = mysql_query($str);
$optpt = '';
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n                        or tipe='HOLDING')  and induk!=''\r\n                        ";
$res = mysql_query($str);
$optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n                        where level = '5'\r\n                        order by noakun\r\n                        ";
$res = mysql_query($str);
$optakun = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optakun .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';
}
echo "<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['catatanneraca'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['periode'];
echo "</label></td><td><select id=periode style='width:200px;' onchange=hideById('printPanel')>";
echo $optper;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['pt'];
echo "</label></td><td><select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>";
echo $optpt;
echo "</select></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['noakundari'];
echo "</label></td><td><select id=akundari style='width:200px;' onchange=ambilAkun2(this.options[this.selectedIndex].value)>";
echo $optakun;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['noakunsampai'];
echo "</label></td><td><select id=akunsampai style='width:200px;' onchange=hideById('printPanel')><option value=\"\"></option></select></td></tr>\r\n\r\n<!--<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>-->\r\n<tr height=\"20\"><td colspan=\"2\"> <button class=mybutton onclick=getLaporanCatatanNeraca()>";
echo $_SESSION['lang']['proses'];
echo "</button></td></tr>\r\n\r\n<!--<tr><td colspan=\"2\"><button onclick=\"zPreview('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button><button onclick=\"zPdf('sdm_slave_2rekapabsen','";
echo $arr;
echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">PDF</button><button onclick=\"zExcel(event,'sdm_slave_2rekapabsen.php','";
echo $arr;
echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button><button onclick=\"Clear1()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";
echo $_SESSION['lang']['cancel'];
echo "</button></td></tr>-->\r\n\r\n</table>\r\n</fieldset>\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=catatanNeracaKeExcel(event,'keu_laporancatatanNeraca_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=catatanNeracaKePDF(event,'keu_laporancatatanNeraca_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>    \r\n\t <div style='width:1180;display:fixed;'>\r\n       <table class=sortable cellspacing=1 border=0 width=1160px>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center style='width:50px'>".$_SESSION['lang']['nomor']."</td>\r\n\t\t\t  <td align=center style='width:80px'>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td align=center style='width:330px'>".$_SESSION['lang']['namaakun']."</td>\r\n\t\t\t  <td align=center style='width:100px'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t  <td align=center style='width:150px'>".$_SESSION['lang']['saldoawal']."</td>\r\n\t\t\t  <td align=center style='width:150px'>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td align=center style='width:150px'>".$_SESSION['lang']['kredit']."</td>\r\n\t\t\t  <td align=center style='width:150px'>".$_SESSION['lang']['saldoakhir']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>\r\n<div style='width:1180px;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=1160px>\r\n\t     <thead>\r\n\t\t    <tr>\r\n\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody id=container>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>";
CLOSE_BOX();
close_body();

?>