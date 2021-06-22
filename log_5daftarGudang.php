<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src=js/kelompok_barang.js></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', '<b>' . $_SESSION['lang']['daftargudang'] . '</b>');
echo '<table class=sortable cellspacing=1 border-0>' . "\r\n" . '     <thead>' . "\r\n\t" . '   <tr class=rowheader>' . "\r\n\t" . '     <td>No.</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['orgcode'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['orgname'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['parent'] . '</td>' . "\r\n\t\t" . ' <td>' . $_SESSION['lang']['alamat'] . '</td>' . "\r\n\t" . '   </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody>';
 

$res = mysql_query(getQuery("gudang"));
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo ' <tr class=rowcontent>' . "\r\n\t" . '     <td>' . $no . '</td>' .
		' <td>' . $bar->kodeorganisasi . '</td>' .
		' <td>' . $bar->namaorganisasi . '</td>' .
		' <td>' . $bar->induk . '</td>' .
		' <td>' . $bar->alamat . ', ' . $bar->wilayahkota . ', ' . $bar->negara . ', ' . $bar->kodepos . '</td>' .
		 '   </tr>';
}

echo '</tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>' . "\r\n\t" . ' ';
CLOSE_BOX();
echo close_body();

?>
