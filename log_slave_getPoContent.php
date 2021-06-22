<?php


function createForm($nopo, $notransaksi = '')
{
	global $dbname;
	global $conn;
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '             <thead>' . "\r\n" . '                 <tr class=rowheader>' . "\r\n" . '                   <td>No.</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['sudahditerima'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['kuantitaspo'] . '</td>' . "\t\t" . '   ' . "\r\n" . '                   <td>' . $_SESSION['lang']['diterima'] . '</td>' . "\r\n" . '                   <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                   <td></td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </thead><tbody>' . "\r\n" . '                 ';
	$no = 0;
	$str = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $nopo . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$qtypo = $bar->jumlahpesan;
		$jumlah = $qtypo;
		$namabarang = '';
		$satuan = '';
		$str2 = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';
		$res2 = mysql_query($str2);

		while ($bar1 = mysql_fetch_object($res2)) {
			$namabarang = $bar1->namabarang;
			$satuan = $bar1->satuan;
		}

		if ($satuan != $bar->satuan) {
			$str1 = 'select jumlah from ' . $dbname . '.log_5stkonversi ' . "\r\n" . '                               where darisatuan=\'' . $satuan . '\' and satuankonversi=\'' . $bar->satuan . '\'' . "\r\n" . '                               and kodebarang=\'' . $bar->kodebarang . '\'';
			$res3 = mysql_query($str1);

			while ($bar2 = mysql_fetch_object($res3)) {
				$jumlah = round($qtypo / $bar2->jumlah);
			}
		}

		$jumlahlalu = 0;
		$sddt = '';
		$jumlahedit = 0;
		$strh = 'select jumlah from ' . $dbname . '.log_transaksidt where ' . "\r\n" . '                notransaksi=\'' . $notransaksi . '\'' . "\r\n" . '                        and kodebarang=\'' . $bar->kodebarang . '\'';
		$resh = mysql_query($strh);

		while ($barh = mysql_fetch_object($resh)) {
			$jumlahedit = $barh->jumlah;
		}

		if ($notransaksi != '') {
			$sddt = ' and a.notransaksi!=\'' . $notransaksi . '\' ';
		}

		$strx = 'select sum(a.jumlah) as jumlah,a.kodebarang as kodebarang ' . "\r\n" . '            from ' . $dbname . '.log_transaksidt a,' . "\r\n" . '                 ' . $dbname . '.log_transaksiht b' . "\r\n" . '                   where a.notransaksi=b.notransaksi ' . "\r\n" . '                   and b.nopo=\'' . $nopo . '\' ' . "\r\n" . '               and a.kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n" . '                   ' . $sddt . "\r\n" . '                   group by kodebarang';
		$resx = mysql_query($strx);

		while ($barx = mysql_fetch_object($resx)) {
			$jumlahlalu = $barx->jumlah;
		}

		if ($notransaksi != '') {
			$sisa = $jumlahedit;
		}
		else {
			$sisa = $jumlah - $jumlahlalu;
		}

		if (($notransaksi != '') && ($jumlahedit == 0)) {
			$disab = 'disabled';
		}
		else if ($sisa <= 0) {
			$disab = 'disabled';
		}
		else {
			$disab = '';
		}

		$xyz = $jumlah - $jumlahlalu;
		echo '<tr class=rowcontent>' . "\r\n" . '                   <td>' . $no . '</td>' . "\r\n" . '                   <td>' . $bar->kodebarang . '</td>' . "\r\n" . '                   <td>' . $namabarang . '</td>' . "\r\n" . '                   <td id=\'sat' . $bar->kodebarang . '\'>' . $satuan . '</td>' . "\r\n" . '                   <td align=right>' . number_format($jumlahlalu, 2, '.', ',') . '</td>' . "\r\n" . '                   <td align=right>' . number_format($jumlah, 2, '.', ',') . '</td>' . "\r\n" . '                   <td><input type=text ' . $disab . ' class=myinputtextnumber id=\'qty' . $bar->kodebarang . '\' onkeypress="return angka_doang(event);" value=\'' . $sisa . '\' size=7 maxlength=12 onblur=cekButton(this,\'btn' . $bar->kodebarang . '\')></td>' . "\r\n" . '                   <td>' . $bar->catatan . '</td>' . "\r\n" . '                   <td><button class=mybutton id=\'btn' . $bar->kodebarang . '\' onclick=saveItemPo(\'' . $bar->kodebarang . '\',' . $xyz . ') ' . $disab . '>' . $_SESSION['lang']['save'] . '</button>';
	}

	$optmengetahui = '<option value=\'\'></option>';
	$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where lokasitugas=\'' . $_SESSION['empl']['lokasitugas'] . '\' or lokasitugas=\'' . $_SESSION['org']['induk'] . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$optmengetahui .= '<option value=\'' . $bar->karyawanid . '\'>' . $bar->namakaryawan . '</option>';
	}

	echo '</tbody>' . "\r\n" . '             <tfoot>' . "\r\n" . '                 <tr>' . "\r\n" . '                   <td colspan=8 align=center>' . "\r\n" . '                   <button onclick=selesaiBapb() class=mybutton>' . $_SESSION['lang']['done'] . '</button>' . "\r\n" . '                   </td>' . "\r\n" . '                 </tr>' . "\r\n" . '                 </tfoot>' . "\r\n" . '                 </table>' . "\r\n" . '                 ';
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$nopo = $_POST['nopo'];
	$gudang = $_POST['gudang'];
	$datatype = $_POST['tipedata'];
	$str = 'select induk from ' . $dbname . '.organisasi where kodeorganisasi = \'' . substr($gudang, 0, 4) . '\'';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$ptgudang = $bar->induk;
	}

	$str = 'select kodeorg,stat_release from ' . $dbname . '.log_poht where nopo = \'' . $nopo . '\'';
	$res = mysql_query($str);
	$bar = mysql_fetch_object($res);
	$ptPO = $bar->kodeorg;
	$statReleasePO = $bar->stat_release;

	if (($ptgudang != $ptPO) && ($ptgudang != '')) {
		echo 'warning: belongs to other company (storage:' . $ptgudang . ' << PO:' . $ptPO . ')';
		exit();
		$exit = true;
	}

	if ($statReleasePO == 0) {
		exit('error: This Nopo : ' . $nopo . ' need release, please contact purchasing dept' . $bar->stat_release);
	}

	$statuspo = 'x';
	$str = 'select statuspo,kodesupplier from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';
	$res = mysql_query($str);

	if (0 < mysql_num_rows($res)) {
		while ($bar = mysql_fetch_object($res)) {
			$statuspo = $bar->statuspo;
			$kodesupplier = $bar->kodesupplier;
		}

		if (0 < $statuspo) {
			$str = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';
			$res = mysql_query($str);

			if ($datatype == 'supplier') {
				echo $kodesupplier;
			}
			else if ($datatype == 'data') {
				createForm($nopo);
			}
			else if ($datatype == 'edit') {
				$notransaksi = $_POST['notransaksi'];
				createForm($nopo, $notransaksi);
			}
		}
		else {
			echo ' Error: Purchase order no.' . $nopo . '. not released';
		}
	}
	else {
		echo ' Error: Purchase order no.' . $nopo . '. not found';
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
