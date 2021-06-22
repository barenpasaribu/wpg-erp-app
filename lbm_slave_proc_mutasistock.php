<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$unit = $_POST['unit'];

if ($unit == '') {
	$unit = $_GET['unit'];
}

$tahun = $_POST['tahun'];

if ($tahun == '') {
	$tahun = $_GET['tahun'];
}

$excel = $_POST['proses'];

if ($excel == '') {
	$excel = $_GET['proses'];
}

$kelompok = $_POST['kelompok'];

if ($kelompok == '') {
	$kelompok = $_GET['kelompok'];
}

$pilih = $_POST['pilih'];

if ($pilih == '') {
	$pilih = $_GET['pilih'];
}

$mayor = $_POST['mayor'];

if ($mayor == '') {
	$mayor = $_GET['mayor'];
}

$urut = $_POST['urut'];

if ($urut == '') {
	$urut = $_GET['urut'];
}

$asc = $_POST['asc'];

if ($asc == '') {
	$asc = $_GET['asc'];
}

if ($pilih == 'volume') {
	if ($mayor == 'mayor') {
		echo 'warning: ' . "\n" . 'silakan pilih Display : Nilai ' . "\n" . 'untuk pilihan Per Mayor';
		exit();
	}
}

$tahunlalu = $tahun - 1;
if (($unit == '') || ($tahun == '')) {
	echo 'Warning: silakan memilih gudang.';
	exit();
}

$sData = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$nabar[$rData['kodebarang']] = $rData['namabarang'];
	$satbar[$rData['kodebarang']] = $rData['satuan'];
}

if ($mayor == 'mayor') {
	$sData = 'select kode, kelompok, kelompokbiaya from ' . $dbname . '.log_5klbarang';
	$qData = mysql_query($sData);

	while ($rData = mysql_fetch_assoc($qData)) {
		$nabar[$rData['kode']] = $rData['kelompok'];
		$satbar[$rData['kode']] = $rData['kelompokbiaya'];
	}
}

$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where kodegudang = \'' . $unit . '\' order by tanggal';

if ($unit == 'sumatera') {
	$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where (kodegudang LIKE \'MRKE%\' OR kodegudang LIKE \'SKSE%\' OR kodegudang LIKE \'SOGM%\' OR kodegudang LIKE \'SSRO%\' OR kodegudang LIKE \'WKNE%\' OR kodegudang LIKE \'SOGE%\' OR kodegudang LIKE \'SENE%\') order by tanggal';
}

if ($unit == 'kalimantan') {
	$sData = 'select substr(tanggal,6,2) as bulan from ' . $dbname . '.log_transaksiht where (kodegudang LIKE \'SBME%\' OR kodegudang LIKE \'SBNE%\' OR kodegudang LIKE \'SMLE%\' OR kodegudang LIKE \'SMTE%\' OR kodegudang LIKE \'SSGE%\' OR kodegudang LIKE \'STLE%\') order by tanggal';
}

$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$bulan = +$rData['bulan'];
}

$buattes = '';
$resData = array();
$kodebarang_ = 'kodebarang,';
$saldoawalqty_ = 'saldoawalqty,';
$qtymasuk_ = 'qtymasuk,';
$qtykeluar_ = 'qtykeluar,';
$saldoakhirqty_ = 'saldoakhirqty,';
$hargarata_ = 'hargarata';
$groupbykodebarang_ = 'group by kodebarang';
if (($unit == 'sumatera') || 'kalimantan') {
	$saldoawalqty_ = 'sum(saldoawalqty) as saldoawalqty,';
	$qtymasuk_ = 'sum(qtymasuk) as qtymasuk,';
	$qtykeluar_ = 'sum(qtykeluar) as qtykeluar,';
	$saldoakhirqty_ = 'sum(saldoakhirqty) as saldoakhirqty,';
	$hargarata_ = 'avg(hargarata) as hargarata';
}

if ($mayor == 'mayor') {
	$kodebarang_ = 'substr(kodebarang,1,3) as kodebarang,';
	$saldoawalqty_ = 'sum(saldoawalqty*hargarata) as saldoawalqty,';
	$qtymasuk_ = 'sum(qtymasuk*hargarata) as qtymasuk,';
	$qtykeluar_ = 'sum(qtykeluar*hargarata) as qtykeluar,';
	$saldoakhirqty_ = 'sum(saldoakhirqty*hargarata) as saldoakhirqty';
	$hargarata_ = '';
	$groupbykodebarang_ = 'group by substr(kodebarang,1,3)';
}

$i = 1;

while ($i <= 12) {
	if (strlen($i) == 1) {
		$ii = '0' . $i;
	}
	else {
		$ii = $i;
	}

	$per = $tahun . '-' . $ii;
	$sData = 'SELECT ' . $kodebarang_ . ' ' . $saldoawalqty_ . ' ' . $qtymasuk_ . ' ' . $qtykeluar_ . ' ' . $saldoakhirqty_ . ' ' . $hargarata_ . ' FROM ' . $dbname . '.log_5saldobulanan' . "\r\n" . '        WHERE kodegudang = \'' . $unit . '\' ' . $buattes . ' and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' ' . $groupbykodebarang_ . ' order by periode';

	if ($unit == 'sumatera') {
		$sData = 'SELECT ' . $kodebarang_ . ' ' . $saldoawalqty_ . ' ' . $qtymasuk_ . ' ' . $qtykeluar_ . ' ' . $saldoakhirqty_ . ' ' . $hargarata_ . ' FROM ' . $dbname . '.log_5saldobulanan' . "\r\n" . '        WHERE (kodegudang LIKE \'MRKE%\' OR kodegudang LIKE \'SKSE%\' OR kodegudang LIKE \'SOGM%\' OR kodegudang LIKE \'SSRO%\' OR kodegudang LIKE \'WKNE%\' OR kodegudang LIKE \'SOGE%\' OR kodegudang LIKE \'SENE%\') ' . $buattes . ' and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' ' . $groupbykodebarang_ . ' order by periode';
	}

	if ($unit == 'kalimantan') {
		$sData = 'SELECT ' . $kodebarang_ . ' ' . $saldoawalqty_ . ' ' . $qtymasuk_ . ' ' . $qtykeluar_ . ' ' . $saldoakhirqty_ . ' ' . $hargarata_ . ' FROM ' . $dbname . '.log_5saldobulanan' . "\r\n" . '        WHERE (kodegudang LIKE \'SBME%\' OR kodegudang LIKE \'SBNE%\' OR kodegudang LIKE \'SMLE%\' OR kodegudang LIKE \'SMTE%\' OR kodegudang LIKE \'SSGE%\' OR kodegudang LIKE \'STLE%\') ' . $buattes . ' and periode = \'' . $per . '\' and kodebarang like \'' . $kelompok . '%\' ' . $groupbykodebarang_ . ' order by periode';
	}

	$qData = mysql_query($sData);

	while ($rData = mysql_fetch_assoc($qData)) {
		if ($i == 1) {
			$resData[$rData['kodebarang']][sallu] = $rData['saldoawalqty'];

			if ($pilih == 'nilai') {
				if ($mayor == '') {
					$resData[$rData['kodebarang']][sallu] = $rData['saldoawalqty'] * $rData['hargarata'];
				}
			}
		}

		$awal = $per . 'A';
		$terima = $per . 'R';
		$kasih = $per . 'I';
		$saldo = $per . 'S';
		$harga = $per . 'H';
		$resData[$rData['kodebarang']][kobar] = $rData['kodebarang'];
		$resData[$rData['kodebarang']][$awal] = $rData['saldoawalqty'];

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']][$awal] = $rData['saldoawalqty'] * $rData['hargarata'];
			}
		}

		$resData[$rData['kodebarang']][$terima] = $rData['qtymasuk'];

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']][$terima] = $rData['qtymasuk'] * $rData['hargarata'];
			}
		}

		$resData[$rData['kodebarang']][$kasih] = $rData['qtykeluar'];

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']][$kasih] = $rData['qtykeluar'] * $rData['hargarata'];
			}
		}

		$resData[$rData['kodebarang']][$saldo] = $rData['saldoakhirqty'];

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']][$saldo] = $rData['saldoakhirqty'] * $rData['hargarata'];
			}
		}

		$resData[$rData['kodebarang']] += totem;

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']] += totem;
			}
		}

		$resData[$rData['kodebarang']] += tokel;

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']] += tokel;
			}
		}

		$resData[$rData['kodebarang']][salak] = $rData['saldoakhirqty'];

		if ($pilih == 'nilai') {
			if ($mayor == '') {
				$resData[$rData['kodebarang']][salak] = $rData['saldoakhirqty'] * $rData['hargarata'];
			}
		}

		$resData[$rData['kodebarang']][$harga] = $rData['hargarata'];

		if ($rData['hargarata'] != 0) {
			$resData[$rData['kodebarang']][hargaterakhir] = $rData['hargarata'];
		}
	}

	++$i;
}

if (!empty($resData)) {
	foreach ($resData as $c => $key) {
		if ($urut == 'kodebarang') {
			$sort_masuk[] = $key[kobar];
		}

		if ($urut == 'awal') {
			$sort_masuk[] = $key[sallu];
		}

		if ($urut == 'masuk') {
			$sort_masuk[] = $key[totem];
		}

		if ($urut == 'keluar') {
			$sort_masuk[] = $key[tokel];
		}

		if ($urut == 'akhir') {
			$sort_masuk[] = $key[salak];
		}

		if ($urut == 'harga') {
			$sort_masuk[] = $key[hargaterakhir];

			if ($mayor == 'mayor') {
				$sort_masuk[] = $key[kobar];
			}
		}
	}
}

if ($asc == 'asc') {
	if (!empty($resData)) {
		array_multisort($sort_masuk, SORT_ASC, $resData);
	}
}
else if (!empty($resData)) {
	array_multisort($sort_masuk, SORT_DESC, $resData);
}

$no = 0;
$tab = '';

if ($excel == 'excel') {
	if ($urut == 'kodebarang') {
		$tampilurut = 'Kode Barang';
	}

	if ($urut == 'awal') {
		$tampilurut = 'Saldo Awal';
	}

	if ($urut == 'masuk') {
		$tampilurut = 'Penerimaan';
	}

	if ($urut == 'keluar') {
		$tampilurut = 'Pengeluaran';
	}

	if ($urut == 'akhir') {
		$tampilurut = 'Saldo Akhir';
	}

	if ($urut == 'harga') {
		$tampilurut = 'Harga';
	}

	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= 'Mutasi Stock ' . $unit . ' ' . $tahun . '<br>';
	$tab .= 'Opsi: ' . $kelompok . ' ' . $mayor . ' ' . $pilih . '<br>';
	$tab .= 'Urut: ' . $tampilurut . ' ' . $asc;
}
else {
	$bg = '';
	$brdr = 0;
}

$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'><thead><tr>' . "\r\n" . '<td rowspan=1 align=center ' . $bg . '>No.</td>';
$tab .= '<td align=left ' . $bg . '>' . STRTOUPPER($_SESSION['lang']['kodebarang']) . '<br>' . STRTOUPPER($_SESSION['lang']['namabarang']) . '<br>' . STRTOUPPER($_SESSION['lang']['satuan']) . '</td>';
$tab .= '<td align=left ' . $bg . '></td>';
$i = 1;

while ($i <= 12) {
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
		$tab .= '<tr class=rowcontent>' . "\r\n" . '        <td rowspan=5 align=center>' . $no . '</td>';
		$tab .= '<td rowspan=5 align=left valign=center>' . $ar[kobar] . '<br>' . $nabar[$ar[kobar]] . '<br>' . $satbar[$ar[kobar]] . '</td>';
		$tab .= '<td nowrap bgcolor=\'\' align=left valign=bottom>Saldo Awal</td>';
		$i = 1;

		while ($i <= 12) {
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
			$tab .= '<td bgcolor=\'\' align=right>' . number_format($ar[$awal]) . '</td>';
			++$i;
		}

		$tab .= '<td bgcolor=\'\' align=right><b>' . number_format($ar[sallu]) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'AAFFAA\' align=left valign=bottom>Masuk</td>';
		$i = 1;

		while ($i <= 12) {
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
			$tab .= '<td bgcolor=\'AAFFAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GR\',\'' . $ar[kobar] . '\',\'' . $per . '\',event) title=\'Klik untuk melihat detail.\'>' . number_format($ar[$terima]) . '</td>';
			++$i;
		}

		$tab .= '<td bgcolor=\'AAFFAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GR\',\'' . $ar[kobar] . '\',\'' . $tahun . '\',event) title=\'Klik untuk melihat detail.\'><b>' . number_format($ar[totem]) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'FFAAAA\' align=left valign=bottom>Keluar</td>';
		$i = 1;

		while ($i <= 12) {
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
			$tab .= '<td bgcolor=\'FFAAAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GI\',\'' . $ar[kobar] . '\',\'' . $per . '\',event) title=\'Klik untuk melihat detail.\'>' . number_format($ar[$kasih]) . '</td>';
			++$i;
		}

		$tab .= '<td bgcolor=\'FFAAAA\' align=right style=\'cursor:pointer;\' onclick=getDetailGudang(\'GI\',\'' . $ar[kobar] . '\',\'' . $tahun . '\',event) title=\'Klik untuk melihat detail.\'><b>' . number_format($ar[tokel]) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'\' align=left valign=bottom>Saldo Akhir</td>';
		$i = 1;

		while ($i <= 12) {
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
			$tab .= '<td bgcolor=\'\' align=right>' . number_format($ar[$sama]) . '</td>';
			++$i;
		}

		$tab .= '<td bgcolor=\'\' align=right><b>' . number_format($ar[salak]) . '</b></td>';
		$tab .= '</tr><tr class=rowcontent>';
		$tab .= '<td nowrap bgcolor=\'AAAAFF\' align=left valign=bottom>Harga</td>';
		$i = 1;

		while ($i <= 12) {
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
			++$i;
		}

		if ($mayor == 'true') {
			$tab .= '<td bgcolor=\'AAAAFF\' align=right><b></b></td>';
		}
		else {
			$tab .= '<td bgcolor=\'AAAAFF\' align=right><b>' . number_format($ar[hargaterakhir]) . '</b></td>';
		}

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
		$unit = 'Sumatera(MRKE, SKSE, SOGM, SSRO, WKNE, SOGE, SENE)';
	}

	if ($unit == 'kalimantan') {
		$unit = 'Kalimantan(SBME, SBNE, SMLE, SMTE, SSGE, STLE)';
	}

	$nop_ = 'MutasiStock_' . $unit . $tahun . $kelompok . $pilih . $mayor . $urut . $asc;

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
