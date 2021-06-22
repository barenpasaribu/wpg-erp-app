<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$param = $_POST;

if (isset($_GET['proses']) != '') {
	if ($_GET['proses'] == 'excel') {
		$param = $_GET;
	}
	else {
		$param['proses'] = $_GET['proses'];
	}
}

$optNmakun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optNmbarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optKlmpKbrg = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$nmTipe = array(0 => 'Koreksi', 1 => 'Masuk', 2 => 'Pengembalian pengeluaran(retur)', 3 => 'Penerimaan Mutasi', 5 => 'Pengeluaran', 6 => 'Pengembalian penerimaan', 7 => 'Pengeluaran mutasi');

if ($param['unitId'] != '') {
	$whr .= ' and left(kodegudang,4)=\'' . $param['unitId'] . '\'';
}

if (($param['prdIdDr'] != '') || ($param['prdIdSmp'] != '')) {
	if ($param['ptId'] == '') {
		exit('error: ' . $_SESSION['lang']['pt'] . ' tidak boleh kosong');
	}

	$whrd .= 'and left(tanggal,7) between \'' . $param['prdIdDr'] . '\' and \'' . $param['prdIdSmp'] . '\'';
	$dert = 'select TIMESTAMPDIFF(MONTH,\'' . $param['prdIdDr'] . '-01\',\'' . $param['prdIdSmp'] . '-01\') as difdrt ' . "\r\n" . '           from ' . $dbname . '.log_transaksiht where kodept=\'' . $param['ptId'] . '\'';

	#exit(mysql_error($conn));
	($qert = mysql_query($dert)) || true;
	$rdert = mysql_fetch_assoc($qert);

	if ((0 <= $rdert['difdrt']) && ($rdert['difdrt'] <= 6)) {
		$whr .= 'and left(tanggal,7) between \'' . $param['prdIdDr'] . '\' and \'' . $param['prdIdSmp'] . '\'';
	}
	else {
		exit('error: Periode Salah atau lebih dari 6 bulan');
	}
}

if ($param['proses'] != 'getUnit') {
	$snoakun = 'select distinct noakun,kode from ' . $dbname . '.log_5klbarang ' . "\r\n" . '               where (noakun!=\'\' and noakun is not null) order by kode asc';

	#exit(mysql_error($conn));
	($qnoakun = mysql_query($snoakun)) || true;

	while ($rnoakun = mysql_fetch_assoc($qnoakun)) {
		$lstNoakun[$rnoakun['kode']] = $rnoakun['noakun'];
	}

	$sjurnal = 'select distinct nojurnal,noreferensi from ' . $dbname . '.keu_jurnalht where kodejurnal like \'INV%\' ' . $whrd . '';

	#exit(mysql_error($conn));
	($qjurnal = mysql_query($sjurnal)) || true;

	while ($rjurnal = mysql_fetch_assoc($qjurnal)) {
		$nojurnal[$rjurnal['noreferensi']] = $rjurnal['nojurnal'];
	}

	$sdt3 = 'select left(kodebarang,3) as klmpk,count(notransaksi) as jmlh' . "\r\n" . '           from ' . $dbname . '.`log_transaksi_vw` ' . "\r\n" . '           where `kodept`=\'' . $param['ptId'] . '\' and post=1 ' . $whr . '  and left(kodebarang,1) not in(\'8\',\'9\')' . "\r\n" . '           group by left(kodebarang,3) order by kodebarang,kodegudang asc';

	#exit(mysql_error($conn));
	($qdt3 = mysql_query($sdt3)) || true;

	while ($rertklm = mysql_fetch_assoc($qdt3)) {
		$jmlhRow[$rertklm['klmpk']] = $rertklm['jmlh'];
	}

	$sdt3 = 'select kodebarang as klmpk,count(notransaksi) as jmlh' . "\r\n" . '           from ' . $dbname . '.`log_transaksi_vw` ' . "\r\n" . '           where `kodept`=\'' . $param['ptId'] . '\' and post=1 ' . $whr . '  and left(kodebarang,1) not in(\'8\',\'9\')' . "\r\n" . '           group by kodebarang order by kodebarang,kodegudang asc';

	#exit(mysql_error($conn));
	($qdt3 = mysql_query($sdt3)) || true;

	while ($rertklm = mysql_fetch_assoc($qdt3)) {
		$jmlhRowBrg[$rertklm['klmpk']] = $rertklm['jmlh'];
	}

	$bgex = '';
	$brd = 0;

	if ($param['proses'] == 'excel') {
		$bgex = ' bgcolor=#DEDEDE align=center';
		$brd = 1;
	}

	$tab = '<table cellpadding=1 cellspacing=1 border=' . $brd . ' class=sortable>';
	$tab .= '<thead><tr ' . $bgex . '>';
	$tab .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['nojurnal'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['noreferensi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['tipetransaksi'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodevhc'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td></tr><tbody>';
	$sdt = 'select left(kodebarang,3) as klmpk,kodebarang,notransaksi,tipetransaksi,`hartot` as uang,jumlah,satuan,kodemesin,kodeblok,kodept' . "\r\n" . '           from ' . $dbname . '.`log_transaksi_vw` ' . "\r\n" . '           where `kodept`=\'' . $param['ptId'] . '\' and post=1 ' . $whr . '  ' . "\r\n" . '           and left(kodebarang,1) not in (\'8\',\'9\')' . "\r\n" . '           order by kodebarang,kodegudang asc';

	#exit(mysql_error($conn));
	($qdt = mysql_query($sdt)) || true;

	while ($rdt = mysql_fetch_assoc($qdt)) {
		if ($klmpkbrg != substr($rdt['kodebarang'], 0, 3)) {
			$klmpkbrg = substr($rdt['kodebarang'], 0, 3);
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $klmpkbrg . '</td>';
			$tab .= '<td>' . $optKlmpKbrg[$klmpkbrg] . '</td>';
			$tab .= '<td>' . $lstNoakun[$klmpkbrg] . '</td>';
			$tab .= '<td>' . $optNmakun[$lstNoakun[$klmpkbrg]] . '</td>';
			$tab .= '<td colspan=6>&nbsp;</td>';
			$tab .= '</tr>';
			$rowKlmpk = $jmlhRow[$klmpkbrg];
			$subtRps += $klmpkbrg;
			$subtJmlhs += $klmpkbrg;
			$ad = 1;
		}
		else {
			$subtRps += $klmpkbrg;
			$subtJmlhs += $klmpkbrg;
			$ad += 1;
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $rdt['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmbarang[$rdt['kodebarang']] . '</td>';
		$tab .= '<td>' . $nojurnal[$rdt['notransaksi']] . '</td>';
		$tab .= '<td>' . $rdt['notransaksi'] . '</td>';
		$tab .= '<td>' . $nmTipe[$rdt['tipetransaksi']] . '</td>';
		$tab .= '<td align=right>' . number_format($rdt['uang'], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($rdt['jumlah'], 2) . '</td>';
		$tab .= '<td>' . $rdt['satuan'] . '</td>';
		$tab .= '<td>' . $rdt['kodemesin'] . '</td>';
		$tab .= '<td>' . $rdt['kodeblok'] . '</td></tr>';

		if ($kdbrg != $rdt['kodebarang']) {
			$aret = 1;
			$kdbrg = $rdt['kodebarang'];
			$subtRp += $kdbrg;
			$subtJmlh += $kdbrg;
			$rert = $jmlhRowBrg[$kdbrg];
		}
		else {
			$aret += 1;
			$subtRp += $kdbrg;
			$subtJmlh += $kdbrg;
		}

		if ($rert == $aret) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td colspan=5 align=right>' . $_SESSION['lang']['subtotal'] . ' ' . $optNmbarang[$kdbrg] . '</td>';
			$tab .= '<td  align=right>' . number_format($subtRp[$kdbrg], 2) . '</td>';
			$tab .= '<td  align=right>' . number_format($subtJmlh[$kdbrg], 2) . '</td>';
			$tab .= '<td colspan=3>&nbsp;</td>';
			$tab .= '</tr>';
		}

		if ($rowKlmpk == $ad) {
			$tab .= '<tr bgcolor=orange>';
			$tab .= '<td colspan=5 align=right>' . $_SESSION['lang']['subtotal'] . ' ' . $optKlmpKbrg[$klmpkbrg] . '</td>';
			$tab .= '<td  align=right>' . number_format($subtRps[$klmpkbrg], 2) . '</td>';
			$tab .= '<td  align=right>' . number_format($subtJmlhs[$klmpkbrg], 2) . '</td>';
			$tab .= '<td colspan=3>&nbsp;</td>';
			$tab .= '</tr>';
		}
	}

	$tab .= '</tbody></table>';
}

switch ($param['proses']) {
case 'getUnit':
	$optUnit2 = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sunit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '                where induk=\'' . $param['kdPt'] . '\' order by namaorganisasi asc';

	#exit(mysql_error($conn));
	($qunit = mysql_query($sunit)) || true;

	while ($runit = mysql_fetch_assoc($qunit)) {
		$optUnit2 .= '<option value=\'' . $runit['kodeorganisasi'] . '\'>' . $runit['namaorganisasi'] . '</option>';
	}

	echo $optUnit2;
	break;

case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= '</table>Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$thisDate = date('YmdHms');
	$nop_ = 'laptransaksiGudang_' . $thisDate;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
	break;
}

?>
