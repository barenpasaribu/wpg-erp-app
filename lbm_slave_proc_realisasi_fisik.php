<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$_POST['kdPt'] == '' ? $kdPt = $_GET['kdPt'] : $kdPt = $_POST['kdPt'];
$_POST['periode'] == '' ? $periode = $_GET['periode'] : $periode = $_POST['periode'];
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
$_POST['regDt'] == '' ? $regDt = $_GET['regDt'] : $regDt = $_POST['regDt'];
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

$whr = ' and kodeorg in (' . $dtPete . ')';

if ($kdPt != '') {
	$whr = ' and kodeorg=\'' . $kdPt . '\'';
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

$arr = '##periode##judul##kdPt##regDt';
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
	$sTot = 'select distinct sum(jumlahpesan) as total  from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset order by kodetipe) ' . "\r\n" . '       and tanggal like \'' . $periode . '%\' ' . $whr . '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totKapi += $rTot['total'];
	}

	$sTot = 'select distinct sum(jumlahpesan) as total,matauang from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where  substr(kodebarang,1,1) not in (\'8\',\'9\') ' . "\r\n" . '       and tanggal like \'' . $periode . '%\'  ' . $whr . '';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totNonKapi += $rTot['total'];
	}

	$sTot = 'select distinct sum(jumlahpesan) as total from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where substr(kodebarang,1,3) in (select distinct kelompokbarang from ' . $dbname . '.sdm_5tipeasset order by kodetipe)' . "\r\n" . '       ' . $whr . ' and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totKapiSmp += $rTot['total'];
	}

	$sTot = 'select distinct sum(jumlahpesan) as total from ' . "\r\n" . '       ' . $dbname . '.log_po_vw where substr(kodebarang,1,1) not in (\'8\',\'9\') ' . $whr . "\r\n" . '       and substr(tanggal,1,7) between \'' . $tahun . '-01\' and \'' . $periode . '\'';

	exit(mysql_error($sTot));
	($qTot = mysql_query($sTot)) || true;

	while ($rTot = mysql_fetch_assoc($qTot)) {
		$totNonKapiSmp += $rTot['total'];
	}

	strlen($bulan) < 1 ? $bln = '0' . $bulan : $bln = $bulan;
	$sBgt = 'select distinct sum(k' . $bln . ') as total from ' . "\r\n" . '      ' . $dbname . '.bgt_kapital_vw where tahunbudget=\'' . $tahun . '\' ' . $whrKapt . '';

	#exit(mysql_error($conn));
	($qBgt = mysql_query($sBgt)) || true;
	$rBgt = mysql_fetch_assoc($qBgt);
	$bgtKapital = 0;
	$sBgt = 'select distinct sum(fis' . $bln . ') as total from ' . "\r\n" . '      ' . $dbname . '.bgt_budget_detail where tahunbudget=\'' . $tahun . '\'' . "\r\n" . '      and substr(kodebudget,1,1)=\'M\' ' . $whrbgt . '';

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
	$bgtKapSmp = 0;
	$addstr = '(';
	$W = 1;

	while ($W <= intval($bulan)) {
		if ($W < 10) {
			$jack = 'fis0' . $W;
		}
		else {
			$jack = 'fis' . $W;
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
	$annualKap = 0;
	$aresta = 'SELECT sum(jumlah) as total FROM ' . $dbname . '.bgt_budget_detail' . "\r\n" . '         WHERE substr(kodebudget,1,1)=\'M\' and tahunbudget = \'' . $tahun . '\' ' . $whrbgt . '';

	#exit(mysql_error($conn));
	($query = mysql_query($aresta)) || true;
	$res = mysql_fetch_assoc($query);
	$annualNonKap = $res['total'];
	$lnkKapital = 'style=\'cursor:pointer\' onclick=getDetailKap2(\'' . $arr . '\')';
	$lnkNonKap = 'style=\'cursor:pointer\' onclick=getDetailNonKap2(\'' . $arr . '\')';
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
	$nop_ = 'totalPembelianFis_' . $dte;

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
	$sUnit = 'select distinct kodeunit from ' . $dbname . '.bgt_regional_assignment where regional=\'' . $_POST['regional'] . '\'';

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
