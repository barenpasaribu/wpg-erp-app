<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_pindahPeriodeGudang.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/log_rekalgudang.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['bentuksaldoawal'] . ':</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (tipe=\'GUDANG\' ' . "\r\n" . '            and induk in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\'))' . "\r\n" . '            or ( kodeorganisasi like \'' . $_SESSION['empl']['lokasitugas'] . '%\' and tipe like \'GUDANG%\')' . "\r\n" . '            order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n\t" . '  ' . "\r\n\t" . ' </fieldset>';
	$frm[0] .= '<fieldset><legend>'.$_SESSION['lang']['daftarproses']."</legend>\r\n          <div id=infoDisplay>\r\n\r\n\t\t  </div>\r\n         ";
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
