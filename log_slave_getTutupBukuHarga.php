<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$pt = $_POST['pt'];
	$gudang = $pt;
	$user = $_SESSION['standard']['userid'];
	$period = $_POST['period'];
	$str = 'select tutupbuku,tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi ' . "\r\n" . '                      where periode=\'' . $period . '\'' . "\r\n\t\t" . '      and kodeorg like \''.$gudang.'%\' AND tutupbuku=0' ;
	$res = mysql_query($str);
	$awal = '';
	$akhir = '';
	$periode = 'benar';
	
	if (0 < mysql_num_rows($res)) {
		$periode = 'benar';

		while ($bar = mysql_fetch_object($res)) {
			$awal = str_replace('-', '', $bar->tanggalmulai);
			$akhir = str_replace('-', '', $bar->tanggalsampai);
		}
	}
	else {
		$periode = 'salah';
	}

	if ($periode == 'salah') {
		echo ' Error: Transaction period not defined';
	}
	else {
		$str = 'select distinct a.kodeorg,a.kodegudang,a.kodebarang,b.namabarang,b.satuan, hargarata from ' . $dbname . '.log_5saldobulanan a left join' . "\r\n" . '   ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang ' . "\r\n" . ' where a.kodeorg=\'' . $gudang . '\' and a.periode=\'' . $period . '\'';
		
		$res = mysql_query($str);
		$r = mysql_num_rows($res);

		if (0 < $r) {
			echo '<button class=mybutton onclick=saveSaldoHarga(' . $r . ');>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n" . '                                 <button style=\'display:none;\' onclick=lanjut(); id=lanjut>Lanjut</button>' . "\r\n" . '                                 <table class=sortable cellspacing=1 border=0>' . "\r\n" . '                                 <thead>' . "\r\n" . '                                           <tr class=rowheader>' . "\r\n" . '                                             <td>No</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['tanggalmulai'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['tanggalsampai'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['transaksigudang'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '                                                 <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '
																 <td> Harga Rata-Rata </td>' . "\r\n" . '
																 </tr>' . "\r\n" . '                                         </thead>' . "\r\n" . '                                         <tbody>' . "\r\n" . '                                        ';
			$no = 0;

			while ($bar = mysql_fetch_object($res)) {
				$no += 1;
				echo '<tr class=rowcontent id=row' . $no . '>' . "\r\n" . '                                             <td>' . $no . '</td>' . "\r\n" . '                                                 <td id=period' . $no . '>' . $period . '</td>' . "\r\n" . '                                                 <td id=start' . $no . '>' . $awal . '</td>' . "\r\n" . '                                                 <td id=end' . $no . '>' . $akhir . '</td>' . "\r\n" . '                                                 <td id=pt' . $no . '>' . $bar->kodegudang . '</td>' . "\r\n" . '                                                 <td id=kodebarang' . $no . '>' . $bar->kodebarang . '</td>' . "\r\n" . '                                                 <td>' . $bar->namabarang . '</td>' . "\r\n" . '                                                 <td>' . $bar->satuan . '</td>' . "\r\n" . '
																	<td align=right>' . $bar->hargarata . '</td>' . "\r\n" .'</tr>';
			}

			echo '</tbody><tfoot></tfoot></table>';
		}
		else {
			echo 'No data';
		}
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
