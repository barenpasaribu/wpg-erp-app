<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo ' ' . "\r\n" . '<script language=javascript1.2 src="js/generic.js"></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/log_2kalkulasi_stock.js\'></script>' . "\r\n" . '<link rel=stylesheet type=\'text/css\' href=\'style/generic.css\'>' . "\r\n";
$pilih = $_POST['pilih'];

if ($pilih == '') {
	$pilih = $_GET['pilih'];
}

$barang = $_POST['barang'];

if ($barang == '') {
	$barang = $_GET['barang'];
}

$periode = $_POST['periode'];

if ($periode == '') {
	$periode = $_GET['periode'];
}

$unit = $_POST['unit'];

if ($unit == '') {
	$unit = $_GET['unit'];
}

$excel = $_POST['excel'];

if ($excel == '') {
	$excel = $_GET['excel'];
}

$qwe = explode('-', $periode);
$bulan = $qwe[1];
$tahun = $qwe[0];

if ($bulan == 1) {
	$bulanlalu = 12;
	$tahunlalu = $tahun - 1;
}
else {
	$bulanlalu = $bulan - 1;
	$tahunlalu = $tahun;
}

if (strlen($bulanlalu) == 1) {
	$bulanlalu = '0' . $bulanlalu;
}

$periodelalu = $tahunlalu . '-' . $bulanlalu;
$sData = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang' . "\r\n" . '    where kodebarang like \'' . $barang . '%\'';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$namabarang[$rData['kodebarang']] = $rData['namabarang'];
	$satuan[$rData['kodebarang']] = $rData['satuan'];
}

$sData = 'select kode, kelompok, kelompokbiaya from ' . $dbname . '.log_5klbarang';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$namabarang[$rData['kode']] = $rData['kelompok'];
	$satuan[$rData['kode']] = $rData['kelompokbiaya'];
}

$sData = 'select namasupplier, supplierid from ' . $dbname . '.log_5supplier' . "\r\n" . '    ';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$namasupplier[$rData['supplierid']] = $rData['namasupplier'];
}

$sData = 'select a.kodevhc, b.namajenisvhc from ' . $dbname . '.vhc_5master a' . "\r\n" . '    left join ' . $dbname . '.vhc_5jenisvhc b on a.jenisvhc=b.jenisvhc' . "\r\n" . '    ';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$artimesin[$rData['kodevhc']] = $rData['namajenisvhc'];
}

$sData = 'select kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '    ';
$qData = mysql_query($sData);

while ($rData = mysql_fetch_assoc($qData)) {
	$artimesin[$rData['kodeorganisasi']] = $rData['namaorganisasi'];
	$namasupplier[$rData['kodeorganisasi']] = $rData['namaorganisasi'];
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

if ($excel == '') {
	echo '<span id=printPanelz>' . "\r\n" . '        <img onclick="parent.getDetailGudangKeExcel(event,\'log_slave_2kalkulasi_stock_detail.php\',\'' . $pilih . '\',\'' . $barang . '\',\'' . $periode . '\')" src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '     </span>';
}

if ($pilih == 'GR') {
	$pilihan = 'In';
}
else {
	$pilihan = 'out';
}

$gudangnya = 'kodegudang = \'' . $unit . '\'';

if ($unit == 'sumatera') {
	$gudangnya = '(kodegudang LIKE \'MRKE%\' OR kodegudang LIKE \'SKSE%\' OR kodegudang LIKE \'SOGM%\' OR kodegudang LIKE \'SSRO%\' OR kodegudang LIKE \'WKNE%\' OR kodegudang LIKE \'SOGE%\' OR kodegudang LIKE \'SENE%\')';
}

if ($unit == 'kalimantan') {
	$gudangnya = '(kodegudang LIKE \'SBME%\' OR kodegudang LIKE \'SBNE%\' OR kodegudang LIKE \'SMLE%\' OR kodegudang LIKE \'SMTE%\' OR kodegudang LIKE \'SSGE%\' OR kodegudang LIKE \'STLE%\')';
}

$qwe = 'select notransaksi, kodebarang, satuan, jumlah, hargasatuan, hargarata, notransaksireferensi, gudangx,' . "\r\n" . '        kodeblok, kodemesin, tanggal, kodegudang, untukunit, idsupplier, nopo, hartot ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw ' . "\r\n" . '        where ' . $gudangnya . ' and tanggal like \'' . $periode . '%\' ' . "\r\n" . '            and notransaksi like \'%' . $pilih . '%\' and post = \'1\' and kodebarang like \'' . $barang . '%\' ' . "\r\n" . '        order by kodemesin, kodeblok';

#exit(mysql_error($conn));
($query = mysql_query($qwe)) || true;

while ($rData = mysql_fetch_assoc($query)) {
	if ($pilih == 'GR') {
		if ($rData['idsupplier'] != '') {
			$subsumber = $namasupplier[$rData['idsupplier']];
		}
		else {
			$subsumber = $artimesin[$rData['gudangx']];
		}

		if ($rData['idsupplier'] != '') {
			$subsumber2 = $namasupplier[$rData['idsupplier']] . ' ' . $rData['nopo'];
		}
		else {
			$subsumber2 = $artimesin[$rData['gudangx']];
		}

		$sub[$subsumber]['client'] = $subsumber;
		$sub2[$subsumber2]['client'] = $subsumber2;
	}

	if ($pilih == 'GI') {
		if ($rData['kodemesin'] != '') {
			$subsumber = $artimesin[$rData['kodemesin']];
		}
		else if ($rData['kodeblok'] != '') {
			$subsumber = $artimesin[substr($rData['kodeblok'], 0, 6)];
		}
		else {
			$subsumber = $artimesin[substr($rData['notransaksireferensi'], -6)];
		}

		if ($rData['kodemesin'] != '') {
			$subsumber2 = $artimesin[$rData['kodemesin']] . ' ' . $rData['kodemesin'];
		}
		else if ($rData['kodeblok'] != '') {
			$subsumber2 = $artimesin[$rData['kodeblok']];
		}
		else {
			$subsumber2 = $artimesin[substr($rData['notransaksireferensi'], -6)];
		}

		$sub[$subsumber]['client'] = $subsumber;
		$sub2[$subsumber2]['client'] = $subsumber2;
	}

	$sub[$subsumber] += 'jumlah';
	$sub2[$subsumber2] += 'jumlah';
	$satuannya = $rData['satuan'];
}

$tab .= 'Transaction : ' . $pilihan . ' ' . $namabarang[$barang] . ' ' . $periode . ' ' . $unit . '</br>';
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead><tr class=rowtitle>';

if ($pilih == 'GR') {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['sumber'] . '</td>';
}
else {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['tujuan'] . '</td>';
}

$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['jumlah'] . '</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>';
$tab .= '</tr></trhead><tbody>';
$subtotal = 0;

if (!empty($sub)) {
	foreach ($sub as $subnya) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=left>' . $subnya['client'] . '</td>';
		$tab .= '<td align=right>' . number_format($subnya['jumlah'], 2) . '</td>';
		$tab .= '<td align=left>' . $satuannya . '</td>';
		$tab .= '</tr>';
		$subtotal += $subnya['jumlah'];
	}
}

$tab .= '<tr>';
$tab .= '<td align=center>Total</td>';
$tab .= '<td align=right>' . number_format($subtotal, 2) . '</td>';
$tab .= '<td align=left>' . $satuannya . '</td>';
$tab .= '</tr>';
$tab .= '</tbody><tfoot></tfoot></table><br>';
$tab .= 'Sub Detail Transaction : ' . $pilihan . ' ' . $namabarang[$barang] . ' ' . $periode . ' ' . $unit . '</br>';
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead><tr class=rowtitle>';

if ($pilih == 'GR') {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['sumber'] . '</td>';
}
else {
	$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['tujuan'] . '</td>';
}

$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['jumlah'] . '</td>';
$tab .= '<td align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>';
$tab .= '</tr></trhead><tbody>';
$subtotal = 0;

if (!empty($sub2)) {
	foreach ($sub2 as $subnya2) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=left>' . $subnya2['client'] . '</td>';
		$tab .= '<td align=right>' . number_format($subnya2['jumlah'], 2) . '</td>';
		$tab .= '<td align=left>' . $satuannya . '</td>';
		$tab .= '</tr>';
		$subtotal += $subnya2['jumlah'];
	}
}

$tab .= '<tr>';
$tab .= '<td align=center>Total</td>';
$tab .= '<td align=right>' . number_format($subtotal, 2) . '</td>';
$tab .= '<td align=left>' . $satuannya . '</td>';
$tab .= '</tr>';
$tab .= '</tbody><tfoot></tfoot></table><br>';
$tab .= 'Detail Transaksi : ' . $pilihan . ' ' . $namabarang[$barang] . ' ' . $periode . ' ' . $unit . '</br>';
$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead><tr class=rowtitle>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['notransaksi'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['harga'] . '</td>' . "\r\n" . '        <td rowspan=1 align=center ' . $bg . '>' . $_SESSION['lang']['subtotal'] . '</td>';

if ($pilih == 'GR') {
	$tab .= '<td rowspan=1 align=center ' . $bg . ' colspan=2>' . $_SESSION['lang']['dari'] . '</td>';
}
else {
	$tab .= '<td rowspan=1 align=center ' . $bg . ' colspan=2>' . $_SESSION['lang']['tujuan'] . '</td>';
}

$tab .= '</tr></trhead><tbody>';
$gudangnya = 'kodegudang = \'' . $unit . '\'';

if ($unit == 'sumatera') {
	$gudangnya = '(kodegudang LIKE \'MRKE%\' OR kodegudang LIKE \'SKSE%\' OR kodegudang LIKE \'SOGM%\' OR kodegudang LIKE \'SSRO%\' OR kodegudang LIKE \'WKNE%\' OR kodegudang LIKE \'SOGE%\' OR kodegudang LIKE \'SENE%\')';
}

if ($unit == 'kalimantan') {
	$gudangnya = '(kodegudang LIKE \'SBME%\' OR kodegudang LIKE \'SBNE%\' OR kodegudang LIKE \'SMLE%\' OR kodegudang LIKE \'SMTE%\' OR kodegudang LIKE \'SSGE%\' OR kodegudang LIKE \'STLE%\')';
}

$qwe = 'select notransaksi, kodebarang, satuan, jumlah, hargasatuan, hargarata, notransaksireferensi, gudangx,' . "\r\n" . '        kodeblok, kodemesin, tanggal, kodegudang, untukunit, idsupplier, nopo, hartot ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw ' . "\r\n" . '        where ' . $gudangnya . ' and tanggal like \'' . $periode . '%\' ' . "\r\n" . '            and notransaksi like \'%' . $pilih . '%\' and post = \'1\' and kodebarang like \'' . $barang . '%\' ' . "\r\n" . '        order by tanggal, notransaksi';

#exit(mysql_error($conn));
($query = mysql_query($qwe)) || true;

while ($rData = mysql_fetch_assoc($query)) {
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td align=left>' . $rData['tanggal'] . '</td>';
	$tab .= '<td align=left>' . $rData['notransaksi'] . '</td>';
	$tab .= '<td align=left>' . $namabarang[$rData['kodebarang']] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['jumlah'], 2) . '</td>';
	$tab .= '<td align=left>' . $rData['satuan'] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['hargarata']) . '</td>';
	$subtotal = $rData['jumlah'] * $rData['hargarata'];
	$tab .= '<td align=right>' . number_format($subtotal) . '</td>';

	if ($pilih == 'GR') {
		$tab .= '<td align=left>' . substr($rData['gudangx'], 0, 4) . ' ' . $namasupplier[$rData['idsupplier']] . '</td>';
		$tab .= '<td align=left>' . $artimesin[substr($rData['notransaksireferensi'], -6)] . ' ' . $rData['nopo'] . '</td>';
	}
	else {
		$tab .= '<td align=left>' . substr($rData['gudangx'], 0, 4) . ' ' . $rData['untukunit'] . '</td>';

		if ($rData['kodemesin'] != '') {
			$untuk = $artimesin[$rData['kodemesin']] . ' ' . $rData['kodemesin'];
		}
		else if ($rData['kodeblok'] != '') {
			$untuk = $artimesin[$rData['kodeblok']];
		}
		else {
			$untuk = $artimesin[substr($rData['notransaksireferensi'], -6)];
		}

		$tab .= '<td align=left>' . $untuk . '</td>';
	}

	$totalduit += $subtotal;
	$total += $rData['jumlah'];
	$tab .= '</tr>';
}

$tab .= '<tr>';
$tab .= '<td align=center colspan=2>Total</td>';
$tab .= '<td></td>';
$tab .= '<td align=right>' . number_format($total, 2) . '</td>';
$tab .= '<td align=left>' . $satuan[$barang] . '</td>';
$tab .= '<td></td>';
$tab .= '<td align=right>' . number_format($totalduit) . '</td>';
$tab .= '<td align=left colspan=2></td>';
$tab .= '</tr>';
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

	$nop_ = 'MutasiStock_Detail' . $unit . $periode . $barang;

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
