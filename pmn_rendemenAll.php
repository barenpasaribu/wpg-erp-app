<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src="js/pmn_tbsrendemen.js"></script>' . "\r\n";
include 'master_mainMenu.php';

OPEN_BOX('', '<b>PERHITUNGAN RENDEMEN</b>');

//
echo '<fieldset><legend>Tanggal Rendemen</legend>
   Tanggal<input type="date" id="paradate" name="filterdate" onchange="replaceDate()"'. $enable .' required />
	   <input id="tempdate" name="date" style="display: none;" '. $enable .' value="'. $paratgl.'" />
	   Organisasi
	   <!--input id="idPabrikTahun" style="display: none;" value="'.substr($_SESSION['empl']['namalokasitugas'], 0 ,3) .'" /-->
 	   <select id="idPabrikTahun" name="selectOrg" style="width:150px; height:26px;" disabled>';
$sPabrik="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
			where tipe='PABRIK' AND kodeorganisasi LIKE '%".substr($_SESSION['empl']['namalokasitugas'], 0 ,3)."%'";
$qPabrik=mysql_query($sPabrik) or die(mysql_error());
while($rPabrik=mysql_fetch_assoc($qPabrik)){
	echo "<option value=".$rPabrik['kodeorganisasi']." selected>".$rPabrik['namaorganisasi']."</option>";
}
echo '</select>
		<button class="mybutton" onclick="getTransaksi()" '. $enable .' >Cari</button>';
echo $cancelBut;
echo '<button class="mybutton" onclick="showtableAll()" style="margin-left: 305px;"> Lihat Data</button>';
echo '</fieldset>';
//
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     ' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div style="background: $ffffff;">' . "\r\n" . ' <table class=sortable cellspacing=1 border=3 id=container>' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
