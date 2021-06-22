<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_prosesAkhirBulan.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	// OPEN_BOX('', '<b>' . $_SESSION['lang']['kalkulasihargarata'] . ':</b>');
	OPEN_BOX('', '<b>PERHITUNGAN HARGA AKHIR BULAN:</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>' . $_SESSION['lang']['infoakhirbulangudang'] . '</legend>';

/*	if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\' order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tipe in (\'GUDANG\',\'GUDANGTEMP\') order by namaorganisasi desc';
	}
*/
	$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['induklokasitugas'] . '\'';

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	$optper = '';
	$x = 0;

	while ($x < 13) {
		$y = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
		$optper .= '<option value=\'' . date('Y-m', $y) . '\'>' . date('m-Y', $y) . '</option>';
		++$x;
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['pt'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['ptpemilikbarang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\r\n" . '           <select id=periode>' . $optper . '</select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n\t" . '  ' . "\r\n\t" . ' </fieldset>';
	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['info']."</legend>\r\n          <div id=infoDisplay>\r\n\t\t  </div>\r\n         ";
    foreach ($_SESSION['gudang'] as $key => $val) {
        $frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\r\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\r\n\t\t";
    }
    $frm[0] .= '</fieldset>';
	$hfrm[0] = $_SESSION['lang']['daftarproses'];
	drawTab('FRM', $hfrm, $frm, 100, 900);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
