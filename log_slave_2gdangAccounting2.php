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
$optNmSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$drpt = 'kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $param['ptId2'] . '\' and tipe!=\'HOLDING\')';
if (($param['prdIdDr2'] != '') || ($param['prdIdSmp2'] != '')) {
	$whrd .= 'and left(tanggal,7) between \'' . $param['prdIdDr2'] . '\' and \'' . $param['prdIdSmp2'] . '\'';
	$dert = 'select TIMESTAMPDIFF(MONTH,\'' . $param['prdIdDr2'] . '-01\',\'' . $param['prdIdSmp2'] . '-01\') as difdrt ' . "\r\n" . '           from ' . $dbname . '.keu_jurnaldt ' . "\r\n" . '           where ' . $drpt . '';

	#exit(mysql_error($conn));
	($qert = mysql_query($dert)) || true;
	$rdert = mysql_fetch_assoc($qert);

	if ((0 <= $rdert['difdrt']) && ($rdert['difdrt'] <= 6)) {
		$whr .= 'and left(tanggal,7) between \'' . $param['prdIdDr2'] . '\' and \'' . $param['prdIdSmp2'] . '\'';
	}
	else {
		exit('error: Periode Salah atau lebih dari 6 bulan');
	}
}

if ($param['proses'] != 'getUnit') {
	if ($param['unitId2'] != '') {
		$whr .= ' and kodeorg=\'' . $param['unitId2'] . '\'';
	}

	if ($param['ptId2'] == '') {
		exit('error: ' . $_SESSION['lang']['pt'] . ' tidak boleh kosong');
	}
	else {
		$drpt = 'kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $param['ptId2'] . '\' and tipe!=\'HOLDING\')';
	}

	$snoakun = 'select distinct noakun,kode from ' . $dbname . '.log_5klbarang ' . "\r\n" . '               where (noakun!=\'\' and noakun is not null) order by kode asc';

	#exit(mysql_error($conn));
	($qnoakun = mysql_query($snoakun)) || true;

	while ($rnoakun = mysql_fetch_assoc($qnoakun)) {
		$lstNoakun[$rnoakun['kode']] = $rnoakun['noakun'];
	}

	$sddr = 'select left(kodebarang,3) as klmpk,count(nojurnal) as jmlhrow' . "\r\n" . '           from ' . $dbname . '.`keu_jurnaldt` ' . "\r\n" . '           where ' . $drpt . ' ' . $whr . '  and (nojurnal like \'%INVK%\' and jumlah<0) or (nojurnal like \'%INVM%\' and jumlah>0)' . "\r\n" . '           group by left(kodebarang,3) order by kodebarang asc';

	#exit(mysql_error($conn));
	($qddr = mysql_query($sddr)) || true;

	while ($rddr = mysql_fetch_assoc($qddr)) {
		$rowKlmkBrg[$rddr['klmpk']] = $rddr['jmlhrow'];
	}

	$sddr = 'select kodebarang as klmpk,count(nojurnal) as jmlhrow' . "\r\n" . '           from ' . $dbname . '.`keu_jurnaldt` ' . "\r\n" . '           where ' . $drpt . ' ' . $whr . '  and (nojurnal like \'%INVK%\' and jumlah<0) or (nojurnal like \'%INVM%\' and jumlah>0)' . "\r\n" . '           group by kodebarang order by kodebarang asc';

	#exit(mysql_error($conn));
	($qddr = mysql_query($sddr)) || true;

	while ($rddr = mysql_fetch_assoc($qddr)) {
		$rowBrg[$rddr['klmpk']] = $rddr['jmlhrow'];
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
	$tab .= '<td>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['rp'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['namasupplier'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['nodok'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodevhc'] . '</td>';
	$tab .= '<td>' . $_SESSION['lang']['kodeblok'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['keterangan'] . '</td> ' . "\r\n" . '           </tr><tbody>';
	$sdt = 'select left(kodebarang,3) as klmpk,kodebarang,nojurnal,noreferensi,`jumlah` as uang,kodevhc,kodeblok,kodesupplier,noakun,keterangan,nodok' . "\r\n" . '           from ' . $dbname . '.`keu_jurnaldt` ' . "\r\n" . '           where ' . $drpt . ' ' . $whr . '  and (nojurnal like \'%INVK%\' and jumlah<0) or (nojurnal like \'%INVM%\' and jumlah>0)' . "\r\n" . '           order by kodebarang asc';

	#exit(mysql_error($conn));
	($qdt = mysql_query($sdt)) || true;

	while ($rdt = mysql_fetch_assoc($qdt)) {
		if ($klmpkbrg != $rdt['klmpk']) {
			$klmpkbrg = $rdt['klmpk'];
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td>' . $klmpkbrg . '</td>';
			$tab .= '<td>' . $optKlmpKbrg[$klmpkbrg] . '</td>';
			$tab .= '<td>' . $lstNoakun[$klmpkbrg] . '</td>';
			$tab .= '<td>' . $optNmakun[$lstNoakun[$klmpkbrg]] . '</td>';
			$tab .= '<td colspan=7>&nbsp;</td>';
			$tab .= '</tr>';
			$rertklm = $rowKlmkBrg[$klmpkbrg];
			$subtRps += $klmpkbrg;
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
		$tab .= '<td>' . $rdt['nojurnal'] . '</td>';
		$tab .= '<td>' . $rdt['noreferensi'] . '</td>';
		$tab .= '<td>' . $rdt['noakun'] . '</td>';
		$tab .= '<td align=right>' . number_format($rdt['uang'], 2) . '</td>';
		$tab .= '<td>' . $optNmSup[$rdt['kodesupplier']] . '</td>';
		$tab .= '<td>' . strtoupper($rdt['nodok']) . '</td>';
		$tab .= '<td>' . $rdt['kodevhc'] . '</td>';
		$tab .= '<td>' . $rdt['kodeblok'] . '</td>' . "\r\n" . '                           <td>' . $rdt['keterangan'] . '</td></tr>';

		if ($kdbrg != $rdt['kodebarang']) {
			$aret = 1;
			$kdbrg = $rdt['kodebarang'];
			$subtRp += $kdbrg;
			$rert = $rowBrg[$kdbrg];
		}
		else {
			$aret += 1;
			$subtRp += $kdbrg;
		}

		if ($rert == $aret) {
			$tab .= '<tr class=rowcontent>';
			$tab .= '<td colspan=5 align=right>' . $_SESSION['lang']['subtotal'] . ' ' . $optNmbarang[$kdbrg] . '</td>';
			$tab .= '<td  align=right>' . number_format($subtRp[$kdbrg], 2) . '</td>';
			$tab .= '<td colspan=5>&nbsp;</td>';
			$tab .= '</tr>';
		}

		if ($rertklm == $ad) {
			$tab .= '<tr bgcolor=orange>';
			$tab .= '<td  align=right>' . $_SESSION['lang']['subtotal'] . '</td>';
			$tab .= '<td  align=right>' . $klmpkbrg . '</td>';
			$tab .= '<td  align=right>' . $optKlmpKbrg[$klmpkbrg] . '</td>';
			$tab .= '<td  align=right>' . $lstNoakun[$klmpkbrg] . '</td>';
			$tab .= '<td  align=right>' . $optNmakun[$lstNoakun[$klmpkbrg]] . '</td>';
			$tab .= '<td  align=right>' . number_format($subtRps[$klmpkbrg], 2) . '</td>';
			$tab .= '<td colspan=5>&nbsp;</td>';
			$tab .= '</tr>';
		}
	}

	$tab .= '</tbody></table>';
}

switch ($param['proses']) {
case 'getUnit':
	$optUnit2 = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sunit = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '                where induk=\'' . $param['ptId2'] . '\' order by namaorganisasi asc';

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
	$nop_ = 'laptransaksiGudangAccv_' . $thisDate;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '        </script>';
	break;
}

?>
