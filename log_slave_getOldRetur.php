<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$nomorlama = $_POST['nomorlama'];
$kodebarang = $_POST['kodebarang'];
$kodegudang = $_POST['kodegudang'];
$kodeblok = $_POST['kodeblok'];
$str = 'select a.tipetransaksi,a.kodept,a.untukpt,a.untukunit,b.jumlah,b.satuan,b.hargasatuan ' . "\r\n" . '        from ' . $dbname . '.log_transaksidt b left join' . "\r\n" . '        ' . $dbname . '.log_transaksiht a on. a.notransaksi=b.notransaksi' . "\r\n" . '        where a.tipetransaksi=5 and b.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '        and a.notransaksi=\'' . $nomorlama . '\'' . "\r\n" . '        and a.notransaksi like \'%' . $kodegudang . '%\'' . "\r\n" . '        and b.kodeblok=\'' . $kodeblok . '\' limit 1';
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	while ($bar = mysql_fetch_object($res)) {
		$namabarang = '';
		$strf = 'select namabarang from ' . $dbname . '.log_5masterbarang' . "\r\n" . '            where kodebarang=\'' . $kodebarang . '\'';
		$resf = mysql_query($strf);

		while ($barf = mysql_fetch_object($resf)) {
			$namabarang = $barf->namabarang;
		}

		$stam = 'select sum(jumlah) as jum from ' . $dbname . '.log_transaksi_vw where notransaksireferensi=\'' . $nomorlama . '\'' . "\r\n" . '                            and kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\'' . "\r\n" . '                            and tipetransaksi=2 and kodeblok=\'' . $kodeblok . '\'';
		$jam = 0;
		$rem = mysql_query($stam);

		while ($bam = mysql_fetch_object($rem)) {
			$jam = $bam->jum;
		}

		$sis = $bar->jumlah - $jam;
		echo '<?xml version=\'1.0\' ?>' . "\r\n" . '                <oldoc>' . "\r\n" . '                    <jumlah>' . $sis . '</jumlah>' . "\r\n" . '                    <satuan>' . ($bar->satuan != '' ? $bar->satuan : '*') . '</satuan>' . "\r\n" . '                    <namabarang>' . ($namabarang != '' ? $namabarang : '*') . '</namabarang>' . "\r\n" . '                    <hargasatuan>' . ($bar->hargasatuan != '' ? $bar->hargasatuan : '*') . '</hargasatuan>' . "\r\n" . '                <kodept>' . ($bar->kodept != '' ? $bar->kodept : '*') . '</kodept>' . "\r\n" . '                    <untukpt>' . ($bar->untukpt != '' ? $bar->untukpt : '*') . '</untukpt>' . "\r\n" . '                    <untukunit>' . ($bar->untukunit != '' ? $bar->untukunit : '*') . '</untukunit>' . "\r\n" . '                </oldoc>';
	}
}
else {
	echo ' Gagal,Previous transaction not found';
}

?>
