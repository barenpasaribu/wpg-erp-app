<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';

if (isTransactionPeriod()) {
	$txtcari = $_POST['txtcari'];
	$gudang = $_POST['gudang'];
	$pemilikbarang = $_POST['pemilikbarang'];
	$str = 'select a.kodebarang,a.namabarang,a.satuan from' . "\r\n\t\t" . '      ' . $dbname . '.log_5masterbarang a where a.namabarang like \'%' . $txtcari . '%\' or kodebarang like \'%' . $txtcari . '%\'';
	$res = mysql_query($str);

	if (mysql_num_rows($res) < 1) {
		echo 'Error: ' . $_SESSION['lang']['tidakditemukan'];
	}
	else {
		echo '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t" . '     <thead>' . "\r\n\t\t\t" . '      <tr class=rowheader>' . "\r\n\t\t\t\t" . '      <td>No</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n\t\t\t\t" . '  </tr>' . "\r\n\t\t" . '     </thead>' . "\r\n\t\t\t" . ' <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$saldoqty = 0;
			$str1 = 'select saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n\t\t\t\t" . '       and kodegudang=\'' . $gudang . '\'';
			$res1 = mysql_query($str1);

			while ($bar1 = mysql_fetch_object($res1)) {
				$saldoqty = $bar1->saldoqty;
			}

			$qtynotpostedin = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n\t\t\t\t\t" . '   and a.tipetransaksi<5' . "\r\n\t\t\t\t\t" . '   and a.kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t\t\t" . '   and a.post=0' . "\r\n\t\t\t\t\t" . '   group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotpostedin = $bar2->jumlah;
			}

			if ($qtynotpostedin == '' or NULL) {
				$qtynotpostedin = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $pemilikbarang . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n\t\t\t\t\t" . '   and a.tipetransaksi>4' . "\r\n\t\t\t\t\t" . '   and a.kodegudang=\'' . $gudang . '\'' . "\r\n\t\t\t\t\t" . '   and a.post=0' . "\r\n\t\t\t\t\t" . '   group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotposted = $bar2->jumlah;
			}

			if ($qtynotposted == '') {
				$qtynotposted = 0;
			}

			//$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;
			$saldoqty = $saldoqty ;

			if ($saldoqty == 0) {
				echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '   <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n\t\t\t" . '  </tr>';
			}
			else {
				echo '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="loadField(\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t\t" . '   <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t" . '  <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n\t\t\t" . '      </tr>';
			}
		}

		echo "\r\n\t\t\t\t" . ' </tbody>' . "\r\n\t\t\t\t" . ' <tfoot></tfoot>' . "\r\n\t\t\t\t" . ' </table>';
	}
}
else {
	echo ' Error: Transaction Period missing';
}

?>
