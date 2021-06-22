<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_laporanRealisasiSPK.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['realisasispk']) . '</b>');
OPEN_BOX('', '<b>REALISASI SPK</b>');
$str = 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '      where length(kodeorganisasi)=4 order by namaorganisasi desc';
$res = mysql_query($str);
//$optunit = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
//$optunit = '<option value=\'\'></option>';
//
//while ($bar = mysql_fetch_object($res)) {
//	$optunit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
//}
$optunit= makeOption2(getQuery("lokasitugas"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['realisasispk'] . '</legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['unit'] . '<select id=unit style=\'width:150px;\'>' . $optunit . '</select>' . "\r\n\t" . ' ' . $_SESSION['lang']['tgldari'] . ' <input type="text" class="myinputtext" id="tglAwal" name="tglAwal" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:100px;" />' . "\r\n" . '         ' . $_SESSION['lang']['tglsmp'] . ' <input type="text" class="myinputtext" id="tglAkhir" name="tglAkhir" onmousemove="setCalendar(this.id)" onkeypress="return false;"  maxlength="10" style="width:100px;" />' . "\r\n\t" . ' <button class=mybutton onclick=getBiayaTotalPerKendaraan()>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=biayaLaporanRealisasiKeExcel(event,\'log_slave_laporanRealisasiSPK.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' </span>    ' . "\r\n" . '      <div id=container>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
