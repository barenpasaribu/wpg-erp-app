<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_postingGudang.js\'></script>' . "\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['konfirmasitransaksi'] . ':</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ':<span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</pre></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe in (\'GUDANGTEMP\',\'GUDANG\')' . "\r\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '       order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . "\r\n" . '       or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') and tipe in (\'GUDANGTEMP\',\'GUDANG\') order by namaorganisasi desc';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc>' . $optsloc . '</select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\r\n" . ' ' . "\t" . ' </fieldset>';
	$frm[0] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  <fieldset><legend></legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtunpost size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\r\n\t  <button class=mybutton onclick=cariUnconfirmed(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sloc']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['pt']."</td>\r\n\t  <td>".$_SESSION['lang']['nopo']."</td>\t\r\n\t  <td>".$_SESSION['lang']['supplier']."</td> \r\n\t  <td>".$_SESSION['lang']['asaltujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['noreferensi']."</td>\t\t\t  \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=unconfirmaedlist>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";

	foreach ($_SESSION['gudang'] as $key => $val) {
		$frm[0] .= "<input type=hidden id='".$key."_start' value='".$_SESSION['gudang'][$key]['start']."'>\r\n\t     <input type=hidden id='".$key."_end' value='".$_SESSION['gudang'][$key]['end']."'>\r\n\t\t";
	}

	$frm[1] .= "<fieldset>\r\n\t   <legend>".$_SESSION['lang']['list']."</legend>\r\n\t  ".$_SESSION['lang']['cari_transaksi']."\r\n\t  <input type=text id=txtbabp size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=12>\r\n\t  <button class=mybutton onclick=cariDokumen(0)>".$_SESSION['lang']['find']."</button>\r\n\t  </fieldset>\r\n\t  <table class=sortable cellspacing=1 border=0>\r\n      <thead>\r\n\t  <tr class=rowheader>\r\n\t  <td>No.</td>\r\n\t  <td>".$_SESSION['lang']['sloc']."</td>\r\n\t  <td>".$_SESSION['lang']['tipe']."</td>\r\n\t  <td>".$_SESSION['lang']['momordok']."</td>\r\n\t  <td>".$_SESSION['lang']['tanggal']."</td>\r\n\t  <td>".$_SESSION['lang']['pt']."</td>\r\n\t  <td>".$_SESSION['lang']['nopo']."</td>\t\r\n\t  <td>".$_SESSION['lang']['supplier']."</td> \r\n\t  <td>".$_SESSION['lang']['asaltujuan']."</td>\r\n\t  <td>".$_SESSION['lang']['noreferensi']."</td>\t\t  \r\n\t  <td>".$_SESSION['lang']['dbuat_oleh']."</td>\r\n\t  <td>".$_SESSION['lang']['posted']."</td>\r\n\t  <td></td>\r\n\t  </tr>\r\n\t  </head>\r\n\t   <tbody id=containerlist>\r\n\t   </tbody>\r\n\t   <tfoot>\r\n\t   </tfoot>\r\n\t   </table>\r\n\t </fieldset>\t \r\n\t ";
	$hfrm[0] = $_SESSION['lang']['belumposting'];
	$hfrm[1] = $_SESSION['lang']['daftartransaksi'];
	drawTab('FRM', $hfrm, $frm, 150, 1000);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
