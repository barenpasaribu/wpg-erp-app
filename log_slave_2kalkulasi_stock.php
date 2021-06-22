<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$unit = $_POST['unit'];

if ($unit == '') {
	$unit = $_GET['unit'];
}

$tahun = $_POST['tahun'];

if ($tahun == '') {
	$tahun = $_GET['tahun'];
}

$excel = $_POST['excel'];

if ($excel == '') {
	$excel = $_GET['excel'];
}

$kelompok = $_POST['kelompok'];

if ($kelompok == '') {
	$kelompok = $_GET['kelompok'];
}

$kodebarang = $_POST['kodebarang'];

if ($kodebarang == '') {
	$kodebarang = $_GET['kodebarang'];
}

$pilih = $_POST['pilih'];

if ($pilih == '') {
	$pilih = $_GET['pilih'];
}

$tahunlalu = $tahun - 1;
if (($unit == '') || ($tahun == '')) {
	echo 'Warning: Period is missing.';
	exit();
}

$sData = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang';


$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$nabar[$rData['kodebarang']] = $rData['namabarang'];
	$satbar[$rData['kodebarang']] = $rData['satuan'];
}

$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where left(tanggal,4)=\'' . $tahun . '\' and kodegudang = \'' . $unit . '\' order by tanggal';

if ($unit == 'sumatera') {
	$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where tanggal like \'' . $tahun . '%\' and kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') order by tanggal';
}

if ($unit == 'kalimantan') {
	$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where tanggal like \'' . $tahun . '%\' and kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') order by tanggal';
}

$qData = mysql_query($sData);
while ($rData = mysql_fetch_assoc($qData)) {
	$bulan = +$rData['bulan'];
}

$buattes = '';
$resData = array();
$sData = 'select kodebarang, sum(saldoakhirqty) as saldo from ' . $dbname . '.log_5saldobulanan where kodegudang = \'' . $unit . '\' ' . $buattes . "\r\n" . 'and periode = \'' . $tahunlalu . '-12\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';

if ($unit == 'sumatera') {
	$sData = 'select kodebarang, sum(saldoakhirqty) as saldo from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $tahunlalu . '-12\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
}

if ($unit == 'kalimantan') {
	$sData = 'select kodebarang, sum(saldoakhirqty) as saldo from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $tahunlalu . '-12\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
}

$qData = mysql_query($sData);
$per = $tahun . '-01';
$awal = $per . 'A';

while ($rData = mysql_fetch_assoc($qData)) {
	$resData[$rData['kodebarang']][kobar] = $rData['kodebarang'];
	$resData[$rData['kodebarang']][sallu] = $rData['saldo'];
	$resData[$rData['kodebarang']][salak] = $rData['saldo'];
	$resData[$rData['kodebarang']][$awal] = $rData['saldo'];
}
$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang = \'' . $unit . '\' ' . $buattes . "\r\n" . 'and periode = \'' . $tahun . '-01\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';

if ($unit == 'sumatera') {
	$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $tahun . '-01\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';
}

if ($unit == 'kalimantan') {
	$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $tahun . '-01\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';
}

$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$harga = $tahun . '-01H';
	$resData[$rData['kodebarang']][$harga] = $rData['hargarata'];
}

$i = 1;

while ($i <= $bulan) {
	if (strlen($i) == 1) {
		$ii = '0' . $i;
	}
	else {
		$ii = $i;
	}

	$per = $tahun . '-' . $ii;
	$j = $i + 1;

	if (strlen($j) == 1) {
		$jj = '0' . $j;
	}
	else {
		$jj = $j;
	}

	$perj = $tahun . '-' . $jj;
	$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang = \'' . $unit . '\' ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GR%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';

	if ($unit == 'sumatera') {
		$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GR%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
	}

	if ($unit == 'kalimantan') {
		$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GR%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
	}

	$qData = mysql_query($sData);

	while ($rData = mysql_fetch_assoc($qData)) {
		$resData[$rData['kodebarang']][kobar] = $rData['kodebarang'];
		$terima = $per . 'R';
		$resData[$rData['kodebarang']][$terima] = $rData['saldo'];

		$resData[$rData['kodebarang']] += totem;
		$resData[$rData['kodebarang']] += salak;
		$sama = $per . 'S';
		$resData[$rData['kodebarang']][$sama] = $resData[$rData['kodebarang']][salak];
	}

	$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang = \'' . $unit . '\' ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GI%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';

	if ($unit == 'sumatera') {
		$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GI%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
	}

	if ($unit == 'kalimantan') {
		$sData = 'select kodebarang, sum(jumlah) as saldo from ' . $dbname . '.log_transaksi_vw where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') ' . $buattes . "\r\n" . '    and tanggal like \'' . $per . '-%\' and notransaksi like \'%GI%\' and post = \'1\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang';
	}

	$qData = mysql_query($sData);

	while ($rData = mysql_fetch_assoc($qData)) {
		$resData[$rData['kodebarang']][kobar] = $rData['kodebarang'];
		$kasih = $per . 'I';
		$resData[$rData['kodebarang']][$kasih] = $rData['saldo'];
		$resData[$rData['kodebarang']] += tokel;
		$resData[$rData['kodebarang']] -= salak;
		$sama = $per . 'S';
		$resData[$rData['kodebarang']][$sama] = $resData[$rData['kodebarang']][salak];
	}

	$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang = \'' . $unit . '\' ' . $buattes . "\r\n" . '    and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';

	if ($unit == 'sumatera') {
		$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';
	}

	if ($unit == 'kalimantan') {
		$sData = 'select kodebarang, sum(saldoakhirqty) as saldo, (sum(saldoakhirqty*hargarata)/sum(saldoakhirqty)) as hargarata, periode from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') ' . $buattes . "\r\n" . '    and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' group by kodebarang order by periode';
	}

	$qData = mysql_query($sData);

	while ($rData = mysql_fetch_assoc($qData)) {
		$resData[$rData['kodebarang']]['kobar'] = $rData['kodebarang'];
		$resData[$rData['kodebarang']][$per] = $rData['saldo'];
		$resData[$rData['kodebarang']]['salaw'] = $rData['saldo'];
		$sama = $per . 'S';
		$resData[$rData['kodebarang']][$sama] = $resData[$rData['kodebarang']][salak];
		$awal = $perj . 'A';
		$resData[$rData['kodebarang']][$awal] = $rData['saldo'];
		$harga = $per . 'H';
		$resData[$rData['kodebarang']][$harga] = $rData['hargarata'];
		$hargaj = $perj . 'H';
		$resData[$rData['kodebarang']][$hargaj] = $rData['hargarata'];
	}

	++$i;
}

$sData = 'select kodebarang, saldoqty as saldo from ' . $dbname . '.log_5masterbarangdt where kodegudang = \'' . $unit . '\' and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' ' . $buattes . '';

if ($unit == 'sumatera') {
	$sData = 'select kodebarang, sum(saldoqty) as saldo from ' . $dbname . '.log_5masterbarangdt where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' ' . $buattes . ' group by kodebarang';
}

if ($unit == 'kalimantan') {
	$sData = 'select kodebarang, sum(saldoqty) as saldo from ' . $dbname . '.log_5masterbarangdt where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') and kodebarang like \'' . $kelompok . '%\' and kodebarang like \'' . $kodebarang . '%\' ' . $buattes . ' group by kodebarang';
}

$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$resData[$rData['kodebarang']]['salakpondoh'] = $rData['saldo'];
}

if (!empty($resData)) {
	ksort($resData);
}

$no = 0;
$tab = '';

if ($excel == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
}
else {
	$bg = '';
	$brdr = 0;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'><thead><tr>' . "\r\n" . '<td rowspan=1 align=center ' . $bg . '>No.</td>';
$tab .= '<td align=left ' . $bg . '>' . STRTOUPPER($_SESSION['lang']['kodebarang']) . '<br>' . STRTOUPPER($_SESSION['lang']['namabarang']) . '<br>' . STRTOUPPER($_SESSION['lang']['satuan']) . '</td>';
$tab .= '<td align=left ' . $bg . '></td>';
$i = 1;

while ($i <= $bulan) {
	if (strlen($i) == 1) {
		$ii = '0' . $i;
	}
	else {
		$ii = $i;
	}

	$per = $tahun . '-' . $ii;
	$tab .= '<td align=center ' . $bg . '>' . $per . '</td>';
	++$i;
}

$tab .= '<td align=center ' . $bg . '>' . $tahun . '</td>';
$tab .= '</tr>';
$tab .= '</thead><tbody>';

if (!empty($resData)) {
	foreach ($resData as $ar) {
		$no += 1;
		$tab .= '<tr class=rowcontent>' . "\r\n" . '    <td rowspan=5 align=center>' . $no . '</td>';
		$tab .= '<td rowspan=5 align=left valign=center>' . $ar[kobar] . '<br>' . $nabar[$ar[kobar]] . '<br>' . $satbar[$ar[kobar]] . '</td>';
		$tab .= '<td nowrap bgcolor=\'\' align=left valign=bottom>' . $_SESSION['lang']['saldoawal'] . '</td>';
		$i = 1;

		while ($i <= $bulan) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			$per = $tahun . '-' . $ii;
			$terima = $per . 'R';
			$kasih = $per . 'I';
			$sama = $per . 'S';
			$awal = $per . 'A';
			$harga = $per . 'H';
			$tampilan = $ar[$awal];

			if ($pilih == 'nilai') {
				$tampilan = $ar[$awal] * $ar[$harga];
			}

			$tab .= '<td bgcolor=\'\' align=right>' . number_format($tampilan) . '</td>';
			++$i;
		}

		$tampilan = $ar[sallu];

		if ($pilih == 'nilai') {
			$tampilan = $ar[sallu] * $ar[$harga];
		}

		$tab .= '<td bgcolor=\'\' align=right><b>' . number_format($tampilan) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'AAFFAA\' align=left valign=bottom>' . $_SESSION['lang']['masuk'] . '</td>';
		$ar[totalnilai] = 0;
		$i = 1;

		while ($i <= $bulan) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			$per = $tahun . '-' . $ii;
			$terima = $per . 'R';
			$kasih = $per . 'I';
			$sama = $per . 'S';
			$awal = $per . 'A';
			$harga = $per . 'H';
			$tampilan = $ar[$terima];

			if ($pilih == 'nilai') {
				$tampilan = $ar[$terima] * $ar[$harga];
			}

			$tab .= '<td bgcolor=\'AAFFAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GR\',\'' . $ar[kobar] . '\',\'' . $per . '\',event) title=' . $_SESSION['lang']['klikdetail'] . '>' . number_format($tampilan) . '</td>';
			$ar += totalharga;
			$ar += totalbarang;
			$ar += totalnilai;
			++$i;
		}

		$tampilan = $ar[totem];

		if ($pilih == 'nilai') {
			$tampilan = $ar[totalharga];
		}

		$tab .= '<td bgcolor=\'AAFFAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GR\',\'' . $ar[kobar] . '\',\'' . $per . '\',event) title=' . $_SESSION['lang']['klikdetail'] . '><b>' . number_format($tampilan) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'FFAAAA\' align=left valign=bottom>' . $_SESSION['lang']['keluar'] . '</td>';
		$ar[totalnilai] = 0;
		$i = 1;

		while ($i <= $bulan) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			$per = $tahun . '-' . $ii;
			$terima = $per . 'R';
			$kasih = $per . 'I';
			$sama = $per . 'S';
			$awal = $per . 'A';
			$harga = $per . 'H';
			$tampilan = $ar[$kasih];

			if ($pilih == 'nilai') {
				$tampilan = $ar[$kasih] * $ar[$harga];
			}

			$tab .= '<td bgcolor=\'FFAAAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GI\',\'' . $ar[kobar] . '\',\'' . $per . '\',event) title=' . $_SESSION['lang']['klikdetail'] . '>' . number_format($tampilan) . '</td>';
			$ar += totalharga;
			$ar += totalbarang;
			$ar += totalnilai;
			++$i;
		}

		$tampilan = $ar[tokel];

		if ($pilih == 'nilai') {
			$tampilan = $ar[totalnilai];
		}

		$tab .= '<td bgcolor=\'FFAAAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GI\',\'' . $ar[kobar] . '\',\'' . $tahun . '\',event) title=' . $_SESSION['lang']['klikdetail'] . '><b>' . number_format($tampilan) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'\' align=left valign=bottom>' . $_SESSION['lang']['saldoakhir'] . '</td>';
		$i = 1;

		while ($i <= $bulan) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			$per = $tahun . '-' . $ii;
			$terima = $per . 'R';
			$kasih = $per . 'I';
			$sama = $per . 'S';
			$awal = $per . 'A';
			$harga = $per . 'H';
			$tampilan = $ar[$sama];

			if ($pilih == 'nilai') {
				$tampilan = $ar[$sama] * $ar[$harga];
			}

			$tab .= '<td bgcolor=\'\' align=right>' . number_format($tampilan) . '</td>';
			++$i;
		}

		$tampilan = $ar[salakpondoh];

		if ($pilih == 'nilai') {
			$tampilan = $ar[salakpondoh] * $ar[$harga];
		}

		$tab .= '<td bgcolor=\'\' align=right><b>' . number_format($tampilan) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$totalharga = 0;
		$tab .= '<td nowrap bgcolor=\'AAAAFF\' align=left valign=bottom>' . $_SESSION['lang']['harga'] . '</td>';
		$i = 1;

		while ($i <= $bulan) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			$per = $tahun . '-' . $ii;
			$terima = $per . 'R';
			$kasih = $per . 'I';
			$sama = $per . 'S';
			$awal = $per . 'A';
			$harga = $per . 'H';
			$tab .= '<td bgcolor=\'AAAAFF\' align=right>' . number_format($ar[$harga]) . '</td>';
			$totalharga += $ar[$harga];
			$ar[hargaterakhir] = $ar[$harga];
			++$i;
		}

		$k = $bulan - 1;

		if (strlen($k) == 1) {
			$kk = '0' . $k;
		}
		else {
			$kk = $k;
		}

		$perk = $tahun . '-' . $kk;
		$hargak = $perk . 'H';
		@$ar[hargarata] = $ar[totalharga] / $ar[totalbarang];

		if ($ar[totalbarang] == 0) {
			$ar[hargarata] = $ar[$hargak];
		}

		$tab .= '<td bgcolor=\'AAAAFF\' align=right><b>' . number_format($ar[hargarata]) . '</b></td>';
		$tab .= '</tr>';
	}
}
else {
	$qwe = 4 + $bulan;
	$tab .= '<tr class=rowcontent><td colspan=' . $qwe . '>Data Empty.</td></tr>';
}

$tab .= '</tbody><tfoot></tfoot></table>';

if ($excel != 'excel') {
	echo $tab;
}
else {
	if ($unit == 'sumatera') {
		$unit = 'Sumatera(MRKE, SKSE, SOGM, SSRO, WKNE)';
	}

	if ($unit == 'kalimantan') {
		$unit = 'Kalimantan(SBME, SBNE, SMLE, SMTE, SSGE, STLE)';
	}

	$nop_ = 'MutasiStock_' . $unit . $tahun;

	if (0 < strlen($tab)) {
		if ($handle = opendir('tempExcel')) {
			while (false !== $file = readdir($handle)) {
				if (($file != '.') && ($file != '..')) {
					@unlink('tempExcel/' . $file);
				}
			}

			closedir($handle);
		}

		$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

		if (!fwrite($handle, $tab)) {
			echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
		}

		closedir($handle);
	}
}

?>
