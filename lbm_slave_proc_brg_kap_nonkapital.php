<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['tipe'] == '' ? $tipe = $_GET['tipe'] : $tipe = $_POST['tipe'];
$_POST['kdPt'] == '' ? $kdPt = $_GET['kdPt'] : $kdPt = $_POST['kdPt'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['regDt'] == '' ? $regDt = $_GET['regDt'] : $regDt = $_POST['regDt'];
$_POST['smbrData'] == '' ? $smbrData = $_GET['smbrData'] : $smbrData = $_POST['smbrData'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$bulan = $qwe[1];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if (($proses == 'excel') || ($proses == 'preview')) {
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}
}

if ($regDt != '') {
	$whrtd = 'regional=\'' . $regDt . '\'';

	if ($regDt == 'SUMSEL') {
		$whrtd = ' regional in (\'SUMSEL\',\'LAMPUNG\')';
	}

	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where ' . $whrtd . '';
}
else {
	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment order by kodeunit';
}

$arte = '';
$ader = 0;

#exit(mysql_error($conn));
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$ader += 1;

	if ($ader == 1) {
		$arte .= '\'' . $rUnit['kodeunit'] . '\'';
	}
	else {
		$arte .= ',\'' . $rUnit['kodeunit'] . '\'';
	}
}

$whrbgt = ' and substr(kodeorg,1,4) in (' . $arte . ')';
$whrKapt = ' and substr(kodeunit,1,4) in (' . $arte . ')';
$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in (' . $arte . ')';

#exit(mysql_error($conn));
($qPt = mysql_query($sPt)) || true;

while ($rPt = mysql_fetch_assoc($qPt)) {
	$ert += 1;

	if ($ert == 1) {
		$dtPete .= '\'' . $rPt['induk'] . '\'';
	}
	else {
		$dtPete .= ',\'' . $rPt['induk'] . '\'';
	}
}

$whr .= ' and kodeorg in (' . $dtPete . ')';

if ($kdPt != '') {
	$whr .= ' and kodeorg=\'' . $kdPt . '\'';
	$sBgt = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\'';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;

	while ($rBgt = mysql_fetch_assoc($qBgt)) {
		$ater += 1;

		if ($ater == 1) {
			$aretd = '\'' . $rBgt['kodeorganisasi'] . '\'';
		}
		else {
			$aretd .= ',\'' . $rBgt['kodeorganisasi'] . '\'';
		}
	}

	$whrbgt = ' and substr(kodeorg,1,4) in (' . $aretd . ')';
	$whrKapt = ' and substr(kodeunit,1,4) in (' . $aretd . ')';
}

$arr = '##periode##judul##kdPt##regDt##smbrData';
$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan[10] = $_SESSION['lang']['okt'];
$optBulan[11] = $_SESSION['lang']['nov'];
$optBulan[12] = $_SESSION['lang']['dec'];
if (($proses == 'excel') || ($proses == 'preview')) {
	$dft = 'statuspo=3';

	if ($smbrData != '3') {
		$dft = 'statuspo in (\'2\',\'3\')';
	}

	$sTot = 'select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where ' . $dft . ' and hargasatuan!=1 and substr(kodebarang,1,1)=\'9\' ' . "\r\n" . '       and tanggal like \'' . $periode . '%\' ' . $whr . '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totKapi = $rTot['total'];
	}

	$sTot = 'select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from ' . "\r\n" . '       ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo ' . "\r\n" . '       where ' . $dft . ' and hargasatuan!=1  and  left(kodebarang,1)=\'9\' ' . "\r\n" . '       and left(tanggal,7) = \'' . $periode . '\'  ' . $whr . ' ' . 'group by a.nopo,kodebarang order by nopo asc';
	$nopor = '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		if ($nopor != $rTot['nopo']) {
			$srow = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $rTot['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qrow = mysql_query($srow)) || true;
			$rrow = mysql_num_rows($qrow);
			$pembagi = $rrow;
			$nopor = $rTot['nopo'];
		}

		@$drt3 = $rTot['total'] / $pembagi;
		$totPpnKapi = $totPpnKapi + $drt3;
		$drt3 = 0;
	}

	$totKapi = $totKapi + $totPpnKapi;
	$drt3 = 0;
	$sTot = 'select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where ' . $dft . ' and hargasatuan!=1 and  substr(kodebarang,1,1) not in (\'8\',\'9\') ' . "\r\n" . '       and tanggal like \'' . $periode . '%\'  ' . $whr . '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totNonKapi = $rTot['total'];
	}

	$nopor = '';
	$sTot = 'select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from ' . "\r\n" . '       ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo ' . "\r\n" . '       where ' . $dft . ' and hargasatuan!=1 and  substr(kodebarang,1,1) not in (\'8\',\'9\') ' . "\r\n" . '       and left(tanggal,7) = \'' . $periode . '\'  ' . $whr . ' ' . 'group by a.nopo,kodebarang order by nopo asc';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		if ($nopor != $rTot['nopo']) {
			$srow = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $rTot['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qrow = mysql_query($srow)) || true;
			$rrow = mysql_num_rows($qrow);
			$pembagi = $rrow;
			$nopor = $rTot['nopo'];
		}

		@$drt3 = $rTot['total'] / $pembagi;
		$totPpnNonKapi = $totPpnNonKapi + $drt3;
		$drt3 = 0;
	}

	$totNonKapi = $totNonKapi + $totPpnNonKapi;
	$sTot = 'select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where ' . $dft . ' and hargasatuan!=1  and substr(kodebarang,1,1)=\'9\'  ' . $whr . "\r\n" . '       and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totKapiSmp = $rTot['total'];
	}

	$sTot = 'select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from ' . "\r\n" . '       ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo' . ' where ' . $dft . ' and hargasatuan!=1 and substr(kodebarang,1,1)=\'9\'  ' . $whr . "\r\n" . '       and left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'' . 'group by a.nopo,kodebarang order by nopo asc';
	$nopor = '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		if ($nopor != $rTot['nopo']) {
			$srow = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $rTot['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qrow = mysql_query($srow)) || true;
			$rrow = mysql_num_rows($qrow);
			$pembagi = $rrow;
			$nopor = $rTot['nopo'];
		}

		@$drt += $rTot['total'] / $pembagi;
		$totPpnKapiSmp = $totPpnKapiSmp + $drt;
		$drt = 0;
	}

	$totKapiSmp = $totKapiSmp + $totPpnKapiSmp;
	$sTot = 'select distinct sum((hargasatuan*kurs)*jumlahpesan) as total,matauang from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where ' . $dft . ' and hargasatuan!=1 and substr(kodebarang,1,1) not in (\'8\',\'9\') ' . $whr . "\r\n" . '       and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totNonKapiSmp = $rTot['total'];
	}

	$sTot = 'select distinct sum(ppn*kurs) as total,kodebarang,a.nopo from ' . "\r\n" . '       ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo' . ' where ' . $dft . ' and hargasatuan!=1 and substr(kodebarang,1,1) not in (\'8\',\'9\')  ' . $whr . "\r\n" . '       and left(tanggal,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'' . 'group by a.nopo,kodebarang order by nopo asc';
	$nopor = '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		if ($nopor != $rTot['nopo']) {
			$srow = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $rTot['nopo'] . '\'';

			#exit(mysql_error($conn));
			($qrow = mysql_query($srow)) || true;
			$rrow = mysql_num_rows($qrow);
			$pembagi = $rrow;
			$nopor = $rTot['nopo'];
		}

		@$drt += $rTot['total'] / $pembagi;
		$totPpnNonKapiSmp = $totPpnNonKapiSmp + $drt;
		$drt = 0;
	}

	$totNonKapiSmp = $totNonKapiSmp + $totPpnNonKapiSmp;
	strlen($bulan) < 1 ? $bln = '0' . $bulan : $bln = $bulan;
	$sBgt = 'select distinct sum(k' . $bln . ') as total from ' . "\r\n" . '      ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' ' . $whrKapt . '';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;
	$rBgt = mysql_fetch_assoc($qBgt);
	$bgtKapital = $rBgt['total'];
	$sBgt = 'select distinct sum(rp' . $bln . ') as total from ' . "\r\n" . '      ' . $dbname . '.bgt_budget_detail where tahunbudget=\'' . $tahun . '\'' . "\r\n" . '      and substr(kodebudget,1,1)=\'M\' ' . $whrbgt . '';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;
	$rBgt = mysql_fetch_assoc($qBgt);
	$bgtNonKapital = $rBgt['total'];
	$addstr = '(';
	$W = 1;

	while ($W <= intval($bulan)) {
		if ($W < 10) {
			$jack = 'k0' . $W;
		}
		else {
			$jack = 'k' . $W;
		}

		if ($W < intval($bulan)) {
			$addstr .= $jack . '+';
		}
		else {
			$addstr .= $jack;
		}

		++$W;
	}

	$addstr .= ')';
	$aresta = 'SELECT sum(' . $addstr . ') as total FROM ' . $dbname . '.bgt_kapital_vw' . "\r\n" . '        WHERE tahunbudget = \'' . $tahun . '\' ' . $whrKapt . '';

	#exit(mysql_error($conn));
	($query = mysql_query($aresta)) || true;
	$res = mysql_fetch_assoc($query);
	$bgtKapSmp = $res['total'];
	$addstr = '(';
	$W = 1;

	while ($W <= intval($bulan)) {
		if ($W < 10) {
			$jack = 'rp0' . $W;
		}
		else {
			$jack = 'rp' . $W;
		}

		if ($W < intval($bulan)) {
			$addstr .= $jack . '+';
		}
		else {
			$addstr .= $jack;
		}

		++$W;
	}

	$addstr .= ')';
	$aresta = 'SELECT sum(' . $addstr . ') as total FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '         WHERE substr(kodebudget,1,1)=\'M\' and tahunbudget = \'' . $tahun . '\' ' . $whrbgt . '';

	#exit(mysql_error($conn));
	($query = mysql_query($aresta)) || true;
	$res = mysql_fetch_assoc($query);
	$bgtNonKap = $res['total'];
	$aresta = 'SELECT sum(harga) as total FROM ' . $dbname . '.bgt_kapital_vw' . "\r\n" . '        WHERE tahunbudget = \'' . $tahun . '\' ' . $whrKapt . '';

	#exit(mysql_error($conn));
	($query = mysql_query($aresta)) || true;
	$res = mysql_fetch_assoc($query);
	$annualKap = $res['total'];
	$aresta = 'SELECT sum(rupiah) as total FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '         WHERE substr(kodebudget,1,1)=\'M\' and tahunbudget = \'' . $tahun . '\' ' . $whrbgt . '';

	#exit(mysql_error($conn));
	($query = mysql_query($aresta)) || true;
	$res = mysql_fetch_assoc($query);
	$annualNonKap = $res['total'];
	$lnkKapital = 'style=\'cursor:pointer\' onclick=getDetailKap(\'' . $arr . '\')';
	$lnkNonKap = 'style=\'cursor:pointer\' onclick=getDetailNonKap(\'' . $arr . '\')';
	$bg = '';
	$brdr = 0;

	if ($proses == 'excel') {
		$bg = ' bgcolor=#DEDEDE';
		$brdr = 1;
		$tab .= '<table border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=4 align=left><font size=3>' . $judul . '</font></td>' . "\r\n" . '        <td colspan=3 align=right>' . $_SESSION['lang']['bulan'] . ' : ' . $optBulan[$bulan] . ' ' . $tahun . '</td>' . "\r\n" . '     </tr>    ' . "\r\n" . '</table>';
	}

	if ($proses != 'excel') {
		$tab .= $judul;
	}

	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable style=\'width:100%;\'>' . "\r\n" . '    <thead class=rowheader>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>Kelompok</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['bulanini'] . '</td>' . "\r\n" . '    <td align=center colspan=3 ' . $bg . '>' . $_SESSION['lang']['sdbulanini'] . '</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>ANNUAL BUDGET</td>' . "\r\n" . '    <td align=center rowspan=2 ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['realisasi'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>' . $_SESSION['lang']['anggaran'] . '</td>' . "\r\n" . '    <td align=center ' . $bg . '>%</td>' . "\r\n" . '    </tr>' . "\r\n" . '    </thead>' . "\r\n" . '    <tbody>' . "\r\n";
	$tab .= '<tr class=rowcontent ' . $lnkKapital . '>';
	$tab .= '<td>KAPITAL</td>';
	$tab .= '<td align=right>' . number_format($totKapi, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($bgtKapital, 0) . '</td>';
	@$persenBlnini = ($totKapi / $bgtKapital) * 100;
	$tab .= '<td align=right>' . number_format($persenBlnini, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totKapiSmp, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($bgtKapSmp, 0) . '</td>';
	@$persenSmpBlnini = ($totKapiSmp / $bgtKapSmp) * 100;
	$tab .= '<td align=right>' . number_format($persenSmpBlnini, 0) . '</td>';
	@$persenAnnual = ($totKapiSmp / $annualKap) * 100;
	$tab .= '<td align=right>' . number_format($annualKap, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($persenAnnual, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent ' . $lnkNonKap . '>';
	$tab .= '<td>NON KAPITAL</td>';
	$tab .= '<td align=right>' . number_format($totNonKapi, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($bgtNonKapital, 0) . '</td>';
	@$prsnBlnini = ($totNonKapi / $bgtNonKapital) * 100;
	$tab .= '<td align=right>' . number_format($prsnBlnini, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totNonKapiSmp, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($bgtNonKap, 0) . '</td>';
	@$prsnSmpBlnini = ($totNonKapiSmp / $bgtNonKap) * 100;
	$tab .= '<td align=right>' . number_format($prsnSmpBlnini, 0) . '</td>';
	@$prsnAnnual = ($totNonKapiSmp / $annualNonKap) * 100;
	$tab .= '<td align=right>' . number_format($annualNonKap, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($prsnAnnual, 0) . '</td>';
	$tab .= '</tr>';
	$grReal = $totKapi + $totNonKapi;
	$grBudget = $bgtKapital + $bgtNonKapital;
	$grPersen = ($grReal / $grBudget) * 100;
	$grSmpBgt = $bgtKapSmp + $bgtNonKap;
	$grSmp = $totKapiSmp + $totNonKapiSmp;
	$grPersenSmp = ($grSmp / $grSmpBgt) * 100;
	$grAnnual = $annualKap + $annualNonKap;
	$grPersenAnn = ($grSmp / $grAnnual) * 100;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>GRAND TOTAL</td>';
	$tab .= '<td align=right>' . number_format($grReal, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grBudget, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grPersen, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grSmp, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grSmpBgt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grPersenSmp, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grAnnual, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($grPersenAnn, 0) . '</td>';
	$tab .= '</tr>';
	$tab .= '</tbody></table>';
}

switch ($proses) {
case 'preview':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	echo $tab;
	break;

case 'excel':
	if ($periode == '') {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('His');
	$nop_ = 'totalPembelian_' . $dte;

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
			echo '<script language=javascript1.2>' . "\r\n" . '            parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '            </script>';
			exit();
		}
		else {
			echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '            </script>';
		}

		closedir($handle);
	}

	break;

case 'getDetPt':
	$arte = '';
	$optPt = '';
	$ader = 0;

	if ($_POST['regional'] != '') {
		$whret = 'regional=\'' . $_POST['regional'] . '\'';

		if ($_POST['regional'] == 'SUMSEL') {
			$whret = 'regional in (\'' . $_POST['regional'] . '\',\'LAMPUNG\')';
		}

		$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where ' . $whret . ' order by kodeunit asc';
	}
	else {
		$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment order by kodeunit asc';
	}

	#exit(mysql_error($conn));
	($qUnit = mysql_query($sUnit)) || true;

	while ($rUnit = mysql_fetch_assoc($qUnit)) {
		$ader += 1;

		if ($ader == 1) {
			$arte .= '\'' . $rUnit['kodeunit'] . '\'';
		}
		else {
			$arte .= ',\'' . $rUnit['kodeunit'] . '\'';
		}
	}

	$optPt = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi in (' . $arte . ')';

	#exit(mysql_error($conn));
	($qPt = mysql_query($sPt)) || true;

	while ($rPt = mysql_fetch_assoc($qPt)) {
		$optPt .= '<option value=\'' . $rPt['induk'] . '\'>' . $optNm[$rPt['induk']] . '</option>';
	}

	echo $optPt;
	break;
}

?>
