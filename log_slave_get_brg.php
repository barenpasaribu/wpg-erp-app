<?php


require_once 'master_validation.php';
require_once 'config/connection.php';

if (isset($_POST['txtfind']) != '') {
	$txtfind = $_POST['txtfind'];
	$str = 'select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' or kodebarang like \'%' . $txtfind . '%\' ';

	if ($res = mysql_query($str)) {
		echo "\r\n" . '          <fieldset>' . "\r\n" . '        <legend>Result</legend>' . "\r\n" . '        <div style="overflow:auto; height:300px;" >' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n\t\t\t\t" . ' <thead>' . "\r\n\t\t\t\t" . ' <tr class=rowheader>' . "\r\n\t\t\t\t" . ' <td class=firsttd>' . "\r\n\t\t\t\t" . ' No.' . "\r\n\t\t\t\t" . ' </td>' . "\r\n\t\t\t\t" . ' <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t" . ' <td>' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n\t\t\t\t" . ' </tr>' . "\r\n\t\t\t\t" . ' </thead>' . "\r\n\t\t\t\t" . ' <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			$saldoqty = 0;
			$str1 = 'select sum(saldoqty) as saldoqty from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $bar->kodebarang . '\'' . "\r\n\t\t\t\t" . '       and kodeorg=\'' . $_SESSION['empl']['kodeorganisasi'] . '\'';
			$res1 = mysql_query($str1);

			while ($bar1 = mysql_fetch_object($res1)) {
				$saldoqty = $bar1->saldoqty;
			}

			$qtynotpostedin = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n\t\t\t\t\t" . '   and a.tipetransaksi<5' . "\r\n\t\t\t\t\t" . '   and a.post=0' . "\r\n\t\t\t\t\t" . '   group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotpostedin = $bar2->jumlah;
			}

			if ($qtynotpostedin == '') {
				$qtynotpostedin = 0;
			}

			$qtynotposted = 0;
			$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt' . "\r\n" . '                       b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['kodeorganisasi'] . '\' and b.kodebarang=\'' . $bar->kodebarang . '\' ' . "\r\n\t\t\t\t\t" . '   and a.tipetransaksi>4' . "\r\n\t\t\t\t\t" . '   and a.post=0' . "\r\n\t\t\t\t\t" . '   group by kodebarang';
			$res2 = mysql_query($str2);

			while ($bar2 = mysql_fetch_object($res2)) {
				$qtynotposted = $bar2->jumlah;
			}

			if ($qtynotposted == '') {
				$qtynotposted = 0;
			}

			$saldoqty = ($saldoqty + $qtynotpostedin) - $qtynotposted;

			if ($bar->inactive == 1) {
				echo '<tr bgcolor=\'red\' style=\'cursor:pointer;\'  title=\'Inactive\' >';
				$bar->namabarang = $bar->namabarang . ' [Inactive]';
			}
			else {
				echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setBrg(\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\')" title=\'Click\' >';
			}

			echo ' <td class=firsttd>' . $no . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t\t\t" . '  <td align=right>' . number_format($saldoqty, 2, ',', '.') . '</td>' . "\r\n\t\t\t\t\t" . ' </tr>';
		}

		echo '</tbody>' . "\r\n\t\t\t\t" . '  <tfoot>' . "\r\n\t\t\t\t" . '  </tfoot>' . "\r\n\t\t\t\t" . '  </table></div></fieldset>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}
else {
	$txtfind2 = $_POST['txtfind2'];
	$str = 'select * from ' . $dbname . '.keu_anggaran where kodeanggaran like \'%' . $txtfind2 . '%\' or keterangan like \'%' . $txtfind2 . '%\' or kodeorg  like \'%' . $txtfind2 . '%\'';

	if ($res = mysql_query($str)) {
		echo "\r\n" . '        <fieldset>' . "\r\n" . '        <legend>Result</legend>' . "\r\n" . '        <div style="overflow:auto; height:300px;">' . "\r\n" . '        <table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n\t\t" . ' <td class=firsttd>' . "\r\n\t\t" . ' No.' . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . ' <td>Kode Anggaran</td>' . "\r\n\t\t" . ' <td>Keterangan Anggaran</td>' . "\r\n\t\t" . ' <td>Tipe Anggaran</td>' . "\r\n\t\t" . ' </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody>';
		$no = 0;

		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setAngrn(\'' . $bar->kodeanggaran . '\',\'' . $bar->tipeanggaran . '\')" title=\'Click\' >' . "\r\n\t\t" . '      <td class=firsttd>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->kodeanggaran . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->keterangan . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->tipeanggaran . '</td>' . "\r\n\t\t\t" . ' </tr>';
		}

		echo '</tbody>' . "\r\n\t" . '      <tfoot>' . "\r\n\t\t" . '  </tfoot>' . "\r\n\t\t" . '  </table></div></fieldset>';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}
}

?>
