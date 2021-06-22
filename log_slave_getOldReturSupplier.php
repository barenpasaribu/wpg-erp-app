<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$nomorlama = $_POST['nomorlama'];
$kodebarang = $_POST['kodebarang'];
$kodegudang = $_POST['kodegudang'];
$str = 'select a.tipetransaksi,a.kodept,a.untukpt,a.untukunit,b.jumlah,b.satuan,b.hargasatuan,a.nopo,c.namasupplier,c.supplierid ' . "\r\n" . '        from ' . $dbname . '.log_transaksidt b ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht a on. a.notransaksi=b.notransaksi' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid    ' . "\r\n" . '        where a.tipetransaksi=1 and b.kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '        and a.notransaksi=\'' . $nomorlama . '\'' . "\r\n" . '        and a.notransaksi like \'%' . $kodegudang . '%\'' . "\r\n" . '        limit 1';
$res = mysql_query($str);

if (0 < mysql_num_rows($res)) {
	while ($bar = mysql_fetch_object($res)) {
		$namabarang = '';
		$strf = 'select namabarang from ' . $dbname . '.log_5masterbarang' . "\r\n" . '                where kodebarang=\'' . $kodebarang . '\'';
		$resf = mysql_query($strf);

		while ($barf = mysql_fetch_object($resf)) {
			$namabarang = $barf->namabarang;
		}

		$stam = 'select sum(jumlah) as jum from ' . $dbname . '.log_transaksi_vw where nopo=\'' . $bar->nopo . '\'' . "\r\n" . '                        and kodebarang=\'' . $kodebarang . '\' and kodegudang=\'' . $kodegudang . '\' and notransaksireferensi = \'' . $nomorlama . '\'' . "\r\n" . '                        and tipetransaksi=6';
		$jam = 0;
		$rem = mysql_query($stam);

		while ($bam = mysql_fetch_object($rem)) {
			$jam = $bam->jum;
		}

		$sis = $bar->jumlah - $jam;
		echo '<?xml version=\'1.0\' ?>' . "\r\n" . '                <oldoc>' . "\r\n" . '                            <jumlah>' . $sis . '</jumlah>' . "\r\n" . '                            <satuan>' . ($bar->satuan != '' ? $bar->satuan : '*') . '</satuan>' . "\r\n" . '                            <namabarang>' . ($namabarang != '' ? $namabarang : '*') . '</namabarang>' . "\r\n" . '                            <hargasatuan>' . ($bar->hargasatuan != '' ? $bar->hargasatuan : '*') . '</hargasatuan>' . "\r\n" . '                            <kodept>' . ($bar->kodept != '' ? $bar->kodept : '*') . '</kodept>' . "\r\n" . '                            <untukpt>' . ($bar->untukpt != '' ? $bar->untukpt : '*') . '</untukpt>' . "\r\n" . '                            <untukunit>' . ($bar->untukunit != '' ? $bar->untukunit : '*') . '</untukunit>' . "\r\n" . '                            <nopo>' . ($bar->nopo != '' ? $bar->nopo : '*') . '</nopo>' . "\r\n" . '                            <namasupplier>' . ($bar->namasupplier != '' ? $bar->namasupplier : '*') . '</namasupplier>   ' . "\r\n" . '                            <kodesupplier>' . ($bar->supplierid != '' ? $bar->supplierid : '*') . '</kodesupplier>    ' . "\r\n" . '                        </oldoc>';
	}
}
else {
	echo ' Gagal,Previous transaction not found';
}

?>
