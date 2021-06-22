<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo "<script language=javascript1.2 src='js/kebun_panen.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['laporanpanen']).'</b>');
$str = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.".kebun_aktifitas\r\n      where tipetransaksi = 'PNN' order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optper .= "<option value='".$bar->periode."'>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4).'</option>';
}
//$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n      where tipe='PT'\r\n          order by namaorganisasi desc";
//$res = mysql_query($str);
//$optpt = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
//while ($bar = mysql_fetch_object($res)) {
//    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
//}

$optpt=makeOption2(getQuery("pt"),
    array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
    array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n                where tipe='KEBUN'";
$res = mysql_query($str);
$optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
$optpil = "<option value='fisik'>".$_SESSION['lang']['fisik'].'</option>';
$optpil .= "<option value='lokasi'>".$_SESSION['lang']['lokasi'].'</option>';
$frm[0] .= "<fieldset>\r\n     <legend>".$_SESSION['lang']['laporanpanen'].' '.$_SESSION['lang']['detail']."</legend>\r\n         ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;' onchange=getKbn()>".$optpt."</select>\r\n         ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel')>".$optgudang."</select>\r\n         ".$_SESSION['lang']['tanggal']." : \r\n          <input type=text class=myinputtext id=tgl1 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> -\r\n          <input type=text class=myinputtext id=tgl2 onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>\r\n          <button class=mybutton onclick=getLaporanPanen()>".$_SESSION['lang']['proses']."</button>\r\n         </fieldset>";
$frm[0] .= "<span id=printPanel style='display:none;'>\r\n     <img onclick=fisikKeExcel(event,'kebun_laporanPanen_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n         <img onclick=fisikKePDF(event,'kebun_laporanPanen_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n         </span>    \r\n         <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100%>\r\n             <thead>\r\n                    <tr>\r\n                          <td align=center width=50>No.</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['tanggal']."</td>\r\n                          <td align=center>".$_SESSION['lang']['afdeling']."</td>\r\n                          <td align=center>".$_SESSION['lang']['lokasi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['tahuntanam']."</td>    \r\n                          <td align=center>".$_SESSION['lang']['janjang']."</td>\r\n                          <td align=center>".$_SESSION['lang']['hasilkerjad']." (Kg)</td>    \r\n                          <td align=center>".$_SESSION['lang']['upahkerja']."</td>\r\n                          <td align=center>".$_SESSION['lang']['upahpremi']."</td>\r\n                          <td align=center>".$_SESSION['lang']['jumlahhk']."</td>\r\n                          <td align=center width=150>".$_SESSION['lang']['rupiahpenalty']."</td>\r\n                        </tr>  \r\n                 </thead>\r\n                 <tbody id=container>\r\n                 </tbody>\r\n                 <tfoot>\r\n                 </tfoot>\t\t \r\n           </table>\r\n     </div>";
$frm[1] .= '<fieldset><legend>'.$_SESSION['lang']['laporanpanen'].' per '.$_SESSION['lang']['tanggal'].'</legend>';
$frm[1] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>\r\n<select id=pt_1 name=pt_1 style='width:200px;' onchange=getKbn_1()>".$optpt."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>\r\n<select id=unit_1 name=unit_1 style=width:150px; onchange=bersih_1()>".$optgudang."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=tgl1_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - \r\n<input type=text class=myinputtext id=tgl2_1 onchange=bersih_1() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>\r\n</td></tr>\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=getLaporanPanen_1() >".$_SESSION['lang']['proses']."</button>\r\n<input type=hidden name=hidden_1 id=hidden_1 value=hiddenvalue1 />\r\n\r\n</td></tr>\r\n</table>";
$frm[1] .= '</fieldset>';
$frm[1] .= "<span id=printPanel_1 style='display:none;'>\r\n     <img onclick=laporanKeExcel_1(event,'kebun_laporanPanen_tanggal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n         <img onclick=laporanKePDF_1(event,'kebun_laporanPanen_tanggal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n         </span>    \r\n         <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100% id=container_1>\r\n           </table>\r\n     </div>";
$frm[2] .= '<fieldset><legend>'.$_SESSION['lang']['laporanpanen'].' per '.$_SESSION['lang']['orang'].'</legend>';
$frm[2] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>\r\n<select id=pt_2 name=pt_2 style='width:200px;' onchange=getKbn_2()>".$optpt."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>\r\n<select id=unit_2 name=unit_2 style=width:150px; onchange=bersih_2()>".$optgudang."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=tgl1_2 onchange=bersih_2() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - \r\n<input type=text class=myinputtext id=tgl2_2 onchange=bersih_2() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['pilih']."</td><td>:</td><td>\r\n<select id=pil_2 name=pil_2 style='width:100px;'>".$optpil."</select></select>\r\n</td></tr>\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=getLaporanPanen_2() >".$_SESSION['lang']['proses']."</button>\r\n<input type=hidden name=hidden_2 id=hidden_2 value=hiddenvalue2 />\r\n\r\n</td></tr>\r\n</table>";
$frm[2] .= '</fieldset>';
$frm[2] .= "<span id=printPanel_2 style='display:none;'>\r\n     <img onclick=laporanKeExcel_2(event,'kebun_laporanPanen_orang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n         <img onclick=laporanKePDF_2(event,'kebun_laporanPanen_orang_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>\r\n         </span>    \r\n         <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100% id=container_2>\r\n           </table>\r\n     </div>";
$frm[3] .= '<fieldset><legend>'.$_SESSION['lang']['laporanpanen'].' SPB vs WB </legend>';
$frm[3] .= "<table cellspacing=1 border=0>\r\n<tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>\r\n<select id=pt_3 name=pt_3 style='width:200px;' onchange=getKbn_3()>".$optpt."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>\r\n<select id=unit_3 name=unit_3 style=width:150px; onchange=bersih_3()>".$optgudang."</select></select>\r\n</td></tr>\r\n<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>\r\n<input type=text class=myinputtext id=tgl1_3 onchange=bersih_3() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10> - \r\n<input type=text class=myinputtext id=tgl2_3 onchange=bersih_3() onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>\r\n</td></tr>\r\n<tr><td colspan=3>\r\n<button class=mybutton onclick=getLaporanPanen_3() >".$_SESSION['lang']['proses']."</button>\r\n<input type=hidden name=hidden_3 id=hidden_3 value=hiddenvalue2 />\r\n\r\n</td></tr>\r\n</table>";
$frm[3] .= '</fieldset>';
$frm[3] .= "<span id=printPanel_3 style='display:none;'>\r\n     <img onclick=laporanKeExcel_3(event,'kebun_laporanPanen_spbwb_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> \r\n         </span>    \r\n         <div style='width:100%;height:50%;overflow:scroll;'>\r\n       <table class=sortable cellspacing=1 border=0 width=100% id=container_3>\r\n           </table>\r\n     </div>";
$hfrm[0] = $_SESSION['lang']['laporanpanen'].' '.$_SESSION['lang']['detail'];
$hfrm[1] = $_SESSION['lang']['laporanpanen'].' per '.$_SESSION['lang']['tanggal'];
$hfrm[2] = $_SESSION['lang']['laporanpanen'].' per '.$_SESSION['lang']['orang'];
$hfrm[3] = $_SESSION['lang']['laporanpanen'].' SPB vs WB';
drawTab('FRM', $hfrm, $frm, 200, 900);
close_body();

?>