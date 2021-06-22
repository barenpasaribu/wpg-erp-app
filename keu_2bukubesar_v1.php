<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src=\"js/keu_laporan.js\"></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$str = 'select distinct periode as periode from '.$dbname.".setup_periodeakuntansi\r\n      order by periode desc";
$res = mysql_query($str);
$optper = '';
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
/*
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n              where tipe='PT'\r\n                  order by namaorganisasi desc";
} else {
*/    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n              where tipe='PT' and kodeorganisasi = '".$_SESSION['empl']['kodeorganisasi']."'\r\n                  order by namaorganisasi desc";
/*
}
*/
$res = mysql_query($str);
$optpt = '';
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$optgudang = '';

/*
if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
    $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL' or tipe='TRAKSI'\r\n                        or tipe='HOLDING')  and induk!=''\r\n                        ";
    $optgudang .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
} else {
    if ('KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                        where induk='".$_SESSION['empl']['kodeorganisasi']."' and length(kodeorganisasi)=4 and kodeorganisasi not like '%HO'";
    } else {
 */
        $str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'  and induk!=''";
        if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {
            $optgudang .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
		}

 /*   }
}
*/
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
$str = 'select noakun,namaakun from '.$dbname.".keu_5akun\r\n                        where level = '5'\r\n  OR level = '7'                      order by noakun\r\n                        ";
$res = mysql_query($str);
$optakun = "<option value=''></option>";
while ($bar = mysql_fetch_object($res)) {
    $optakun .= "<option value='".$bar->noakun."'>".$bar->noakun.' - '.$bar->namaakun.'</option>';
}
$qwe = '01-'.date('m-Y');
echo "<fieldset style=\"float: left;\">\r\n<legend><b>";
echo $_SESSION['lang']['laporanbukubesar'];
echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";
echo $_SESSION['lang']['pt'];
echo "</label></td><td><select id=pt style='width:200px;'  onchange=ambilAnakBB(this.options[this.selectedIndex].value)>";
echo $optpt;
echo "</select></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['unit'];
echo "</label></td><td><select id=gudang style='width:200px;' onchange=hideById('printPanel')>";
echo $optgudang;
echo "</select></td></tr>\r\n\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalmulai'];
echo '</label></td><td><input type="text" class="myinputtext" id="tgl1" name="tgl1" onchange="cekTanggal1(this.value);" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:150px;" value="';
echo $qwe;
echo "\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['tanggalsampai'];
echo "</label></td><td><input type=\"text\" class=\"myinputtext\" id=\"tgl2\" name=\"tgl2\" onchange=\"cekTanggal2(this.value);\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:150px;\" /></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['noakundari'];
echo "</label></td><td><select id=akundari style='width:200px;' onchange=ambilAkun2(this.options[this.selectedIndex].value)>";
echo $optakun;
echo "</select></td></tr>\r\n<tr><td><label>";
echo $_SESSION['lang']['noakunsampai'];
echo "</label></td><td><select id=akunsampai style='width:200px;' onchange=hideById('printPanel')><option value=\"\"></option></select></td></tr>\r\n\r\n<!--<tr height=\"20\"><td colspan=\"2\">&nbsp;</td></tr>-->\r\n<tr height=\"20\"><td colspan=\"2\"> <button class=mybutton onclick=getLaporanBukuBesarv1()>";
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
echo "<span id=printPanel style='display:none;'>\r\n     <img onclick=jurnalv1KeExcel(event,'keu_laporanBukuBesarv1_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n\t <img onclick=jurnalv1KePDF(event,'keu_laporanBukuBesarv1_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n\t </span>    \r\n         <div style='width:99%;display:fixed;'>\r\n       <table class=sortable cellspacing=1 border=0 >\r\n\t     <thead>\r\n\t\t    <tr>\r\n\t\t\t  <td align=center style='width:40px;'>".$_SESSION['lang']['nomor']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['nojurnal']."</td>\r\n\t\t\t  <td align=center style='width:80px;'>".$_SESSION['lang']['tanggal']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['noakun']."</td>\r\n\t\t\t  <td align=center style='width:250px;'>".$_SESSION['lang']['keterangan']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['debet']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['kredit']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['saldo']."</td>\r\n\t\t\t  <td align=center style='width:50px;'>".$_SESSION['lang']['kodeorg']."</td>\r\n\t\t\t  <td align=center style='width:100px;'>".$_SESSION['lang']['kodeblok']."</td>\r\n\t\t\t  <td align=center style='width:40px;'>".$_SESSION['lang']['tahuntanam']."</td>\r\n\t\t\t</tr>  \r\n\t\t </thead>\r\n\t\t <tbody>\r\n\t\t </tbody>\r\n\t\t <tfoot>\r\n\t\t </tfoot>\t\t \r\n\t   </table>\r\n     </div>\r\n     <div style='width:100%;height:50%;overflow:auto;'>\r\n           <table class=sortable cellspacing=1 border=0 >\r\n                 <thead>\r\n                      <tr>\r\n                     </tr>  \r\n                     </thead>\r\n                     <tbody id=container>\r\n                     </tbody>\r\n                     <tfoot>\r\n                     </tfoot>\t\t \r\n               </table>\r\n         </div>";
CLOSE_BOX();
close_body();

?>