<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$notransaksi = $_POST['notransaksi'];
	$gudang = $_POST['gudang'];
	$jlhbaris = 0;
	$str = 'select a.tipetransaksi,a.notransaksi,a.tanggal,a.kodept,a.kodegudang,' . "\r\n" . '         b.kodebarang,b.satuan,b.jumlah   ' . "\r\n" . '         from ' . $dbname . '.log_transaksiht a ' . "\r\n" . '         left join ' . $dbname . '.log_transaksidt b on' . "\r\n\t\t" . ' a.notransaksi=b.notransaksi' . "\r\n\t\t" . ' where a.notransaksi=\'' . $notransaksi . '\'' . "\r\n" . '        and a.tipetransaksi =7';
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '        <thead>' . "\r\n\t\t" . '   <tr>' . "\r\n\t\t" . '      <td>No.</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['kodept'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['kodeorgpengirim'] . '</td>' . "\r\n\t\t\t" . '  <td>' . $_SESSION['lang']['penerima'] . '</td>' . "\r\n\t\t" . '   </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody>  ' . "\t" . '  ' . "\r\n\t\t\t" . '  ';
	$no = 0;
	$res = mysql_query($str);
	$jlhbaris = mysql_num_rows($res);

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$stru = 'select namabarang from ' . $dbname . '.log_5masterbarang ' . "\r\n\t" . '      where kodebarang=\'' . $bar->kodebarang . '\'';
		$resu = mysql_query($stru);
		$namabarang = '';

		while ($baru = mysql_fetch_object($resu)) {
			$namabarang = $baru->namabarang;
		}

		echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n\t" . '  <td>' . $no . '</td>' . "\r\n\t" . '  <td id=notransaksi' . $no . '>' . $bar->notransaksi . '</td>' . "\r\n\t" . '  <td>' . $bar->tipetransaksi . '</td>' . "\r\n\t" . '  <td id=kodebarang' . $no . '>' . $bar->kodebarang . '</td>' . "\t" . '  ' . "\r\n\t" . '  <td>' . $namabarang . '</td>' . "\r\n\t" . '  <td id=satuan' . $no . '>' . $bar->satuan . '</td>' . "\r\n\t" . '  <td id=jumlah' . $no . '>' . $bar->jumlah . '</td>' . "\r\n\t" . '  <td id=kodept' . $no . '>' . $bar->kodept . '</td>' . "\t\t\t" . '  ' . "\r\n\t" . '  <td id=asalgudang' . $no . '>' . $bar->kodegudang . '</td>' . "\r\n\t" . '  <td id=gudang' . $no . '>' . $gudang . '</td>' . "\r\n\t" . '  </tr>';
	}

	echo '</tbody><tfoot></tfoot></table>' . "\r\n" . '  ' . "\t" . '   <button onclick=mulaiSimpan(' . $jlhbaris . ') class=mybutton>' . $_SESSION['lang']['save'] . '</button>' . "\r\n" . '  ';
}
else {
	echo ' Error: Transaction Period missing';
}

?>
