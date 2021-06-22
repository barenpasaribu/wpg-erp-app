<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "<script language=javascript1.2 src='js/keu_2agingPiutang.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>DAFTAR USIA PIUTANG</b>');
$kodeorg=substr($_SESSION['empl']['lokasitugas'], 0,3);
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n      where tipe='PT' and kodeorganisasi='".$kodeorg."'  order by namaorganisasi desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optpt .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
//$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi\r\n\t\twhere (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'\r\n\t\tor tipe='HOLDING')  and induk!='' and kodeorganisasi like '".$kodeorg."%'";
//$res = mysql_query($str);
$optgudang = "<option value=''>".$_SESSION['lang']['all'].'</option>';
//$optper = "<option value=''>".$_SESSION['lang']['all'].'</option>';
//while ($bar = mysql_fetch_object($res)) {
//    $optgudang .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
//}
$str = 'select DISTINCT(kodecustomer), namacustomer from '.$dbname.".aging_piutang_vw where kodeorg like '".$kodeorg."%' order by namacustomer";
$res = mysql_query($str);
$optcustomer = "<option value='all'>".$_SESSION['lang']['all'].'</option>';
while ($bar = mysql_fetch_object($res)) {
    $optcustomer .= "<option value='".$bar->kodecustomer."'>".$bar->namacustomer.'</option>';
}

echo "<fieldset>\r\n     <legend>DAFTAR USIA PIUTANG</legend>\r\n\t ".$_SESSION['lang']['pt'].' : '."<select id=pt style='width:200px;'>".$optpt."</select>\r\n\t <select id=gudang style='width:150px;' hidden>".$optgudang."</select>\r\n  Customer <select id=kodecustomer style='width:150px;'>".$optcustomer."</select> <input type=\"text\" value=\"".($tanggalpivot = date('d-m-Y')."\" class=\"myinputtext\" id=\"tanggalpivot\" name=\"tanggalpivot\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />\r\n\t <button class=mybutton onclick=getUsiaHutang()>".$_SESSION['lang']['proses']."</button>\r\n\t </fieldset>");
CLOSE_BOX();

OPEN_BOX('', 'Result:');
echo 	"<span id=printPanel style='display:none;'>
			<img onclick=fisikKeExcel(event,'keu_laporanUsiaPiutang_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'>
			<img onclick=fisikKePDF(event,'keu_laporanUsiaPiutang_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
		</span>
		<div style='width:100%;height:50%;overflow:scroll;'>
			<table class=sortable cellspacing=1 border=0>
				<thead>
				  <tr>
					<td rowspan=2 align=center width=50>".$_SESSION['lang']['nourut']."</td>
					<td rowspan=2 align=center width=50>".$_SESSION['lang']['tanggal']."</td>
					<td rowspan=2 align=center width=200>".$_SESSION['lang']['noinvoice'].'<br>'.$_SESSION['lang']['namasupplier']."</td>
					<td rowspan=2 align=center width=75>".$_SESSION['lang']['jatuhtempo']."</td>
					<td rowspan=2 align=center width=75>No.  DO / Kontrak</td>
					<td rowspan=2 align=center width=75>Nilai Kontrak</td>
					<td rowspan=2 align=center width=75>".$_SESSION['lang']['nilaiinvoice']."</td>
					<td rowspan=2 align=center width=100>".$_SESSION['lang']['belumjatuhtempo']."</td>
					<td align=center colspan=4 width=400>".$_SESSION['lang']['sudahjatuhtempo']."</td>
					<td rowspan=2 align=center width=100>".$_SESSION['lang']['dibayar']."</td>
					<td rowspan=2 align=center width=50>".$_SESSION['lang']['jmlh_hari_outstanding']."</td>
				  </tr>
				  <tr>
				  	<td align=center width=50>1-30 ".$_SESSION['lang']['hari']."</td>
				  	<td align=center width=50>31-60 ".$_SESSION['lang']['hari']."</td>
				  	<td align=center width=50>61-90 ".$_SESSION['lang']['hari']."</td>
				  	<td align=center width=50>over 90 ".$_SESSION['lang']['hari']."</td>
				  </tr>
				</thead>
				<tbody id=container>
				</tbody><tfoot></tfoot>
			</table>
		</div>";

CLOSE_BOX();

close_body();
// \r\n\t\t\t<script>getUsiaHutang()</script>\r\n\t\t 
?>