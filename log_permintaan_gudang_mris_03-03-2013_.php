<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zFunction.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/log_permintaan_gudang_mris.js\'></script>' . "\r\n\r\n";
include 'master_mainMenu.php';

if (isTransactionPeriod()) {
	OPEN_BOX('', '<b>' . $_SESSION['lang']['permintaangudang'] . ' :</b>');
	$frm[0] = '';
	$frm[1] = '';
	echo '<fieldset><legend>';
	echo ' <b>' . $_SESSION['lang']['periode'] . ': <span id=displayperiod>' . tanggalnormal($_SESSION['org']['period']['start']) . ' - ' . tanggalnormal($_SESSION['org']['period']['end']) . '</span></b>';
	echo '</legend>';

	if (($_SESSION['empl']['tipelokasitugas'] == 'KANWIL') && (substr($_SESSION['empl']['subbagian'], -2) != 'PK')) {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'GUDANG\'' . "\r\n" . '       and left(induk,4) in(select kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_SESSION['empl']['regional'] . '\')' . "\r\n" . '       order by namaorganisasi desc';
	}
	else {
		$str = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '       where (left(induk,4)=\'' . $_SESSION['empl']['lokasitugas'] . '\' or kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\') ' . "\r\n" . '      and tipe=\'GUDANGTEMP\'';
	}

	$res = mysql_query($str);
	$optsloc = '<option value=\'\'></option>';

	while ($bar = mysql_fetch_object($res)) {
		$optsloc .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
	}

	echo '<fieldset>' . "\r\n" . '     <legend>' . "\r\n\t" . ' ' . $_SESSION['lang']['daftargudang'] . "\r\n" . '     </legend>' . "\r\n\t" . '  ' . $_SESSION['lang']['pilihgudang'] . ': <select id=sloc onchange=getPT(this.options[this.selectedIndex].value)>' . $optsloc . '</select>' . "\r\n\t" . '   ' . $_SESSION['lang']['ptpemilikbarang'] . '<select id=pemilikbarang style=\'width:200px;\'>' . "\r\n\t" . '   <option value=\'\'></option>' . "\r\n\t" . '   </select>' . "\r\n\t" . '   <button onclick=setSloc(\'simpan\') class=mybutton id=btnsloc>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . '   <button onclick=setSloc(\'ganti\') class=mybutton>' . $_SESSION['lang']['ganti'] . '</button>' . "\t" . '  ' . "\r\n\t" . ' </fieldset>';

	foreach ($_SESSION['gudang'] as $key => $val) {
		echo '<input type=hidden id=\'' . $key . '_start\' value=\'' . $_SESSION['gudang'][$key]['start'] . '\'>' . "\r\n\t" . '     <input type=hidden id=\'' . $key . '_end\' value=\'' . $_SESSION['gudang'][$key]['end'] . '\'>' . "\r\n\t\t";
	}

	$optlokasitujuan = '<option value=\'\'></option>';
	$optlokasitujuan .= ambilUnitPembebananBarang('', $_SESSION['empl']['lokasitugas']);
	$optsubunit = '<option value=\'\'></option>';
	$optKegiatan = '<option value=\'\'></option>';
	$strf = 'select kodekegiatan,kelompok,namakegiatan from ' . $dbname . '.setup_kegiatan order by kelompok,namakegiatan';
	$resf = mysql_query($strf);

	while ($barf = mysql_fetch_object($resf)) {
		$optKegiatan .= '<option value=\'' . $barf->kodekegiatan . '\'>[' . $barf->kelompok . ']-' . $barf->namakegiatan . '</option>';
	}

	$optionm = '<option value=\'\'></option>';
	$str = 'select * from ' . $dbname . '.vhc_5master ' . "\t" . ' order by kodetraksi,kodevhc';
	$res = mysql_query($str);

	while ($bar1 = mysql_fetch_object($res)) {
		$str = 'select namajenisvhc from ' . $dbname . '.vhc_5jenisvhc where jenisvhc=\'' . $bar1->jenisvhc . '\'';
		$res1 = mysql_query($str);
		$namabarang = '';

		while ($bar = mysql_fetch_object($res1)) {
			$namabarang = $bar->namajenisvhc;
		}

		$optionm .= '<option value=\'' . $bar1->kodevhc . '\'>' . $bar1->kodetraksi . ' : ' . $bar1->kodevhc . ' - ' . $namabarang . '</option>';
	}

	$frm .= 0;
	$frm .= 0;
	$frm .= 1;
	$hfrm[0] = $_SESSION['lang']['pengeluaranbarang'];
	$hfrm[1] = $_SESSION['lang']['list'];
	drawTab('FRM', $hfrm, $frm, 200, 900);
}
else {
	echo ' Error: Transaction Period missing';
}

CLOSE_BOX();
close_body();

?>
