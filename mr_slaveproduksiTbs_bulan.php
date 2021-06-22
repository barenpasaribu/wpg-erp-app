<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/devLibrary.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$_POST['proses'] == '' ? $proses = $_GET['proses'] : $proses = $_POST['proses'];
$optNmorg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$_POST['kdPt'] == '' ? $kdPt = $_GET['kdPt'] : $kdPt = $_POST['kdPt'];
$_POST['kdUnit'] == '' ? $kdUnit = $_GET['kdUnit'] : $kdUnit = $_POST['kdUnit'];
$_POST['periodeDt'] == '' ? $periodeDt = $_GET['periodeDt'] : $periodeDt = $_POST['periodeDt'];
$thnbgt = explode('-', $periodeDt);

if ($periodeDt == '') {
	exit('Error:Tahun Tidak Boleh Kosong');
}

$nmpt = $unit = $_SESSION['lang']['all'];

if ($kdUnit != '') {
	$whreblok = 'kodeorg like \'' . $kdUnit . '%\'';
	$whre = 'kodeblok like \'' . $kdUnit . '%\'';
	$whrakt = 'and blok like \'' . $kdUnit . '%\'';
	$unit = $optNmorg[$kdUnit];
}
else if ($kdPt != '') {
	$nmpt = $optNmorg[$kdPt];
	$whreblok = ' substr(kodeorg,1,4) in (';
	$whre = ' substr(kodeblok,1,4) in (';
	$whrakt = ' and substr(blok,1,4) in (';
	$sKod = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk =\'' . $kdPt . '\' and tipe=\'KEBUN\'';

	#exit(mysql_error($conn));
	($qKod = mysql_query($sKod)) || true;
	$rTot = mysql_num_rows($qKod);

	while ($rKod = mysql_fetch_assoc($qKod)) {
		$nord += 1;
		$whreblok .= '\'' . $rKod['kodeorganisasi'] . '\'';
		$whre .= '\'' . $rKod['kodeorganisasi'] . '\'';
		$whrakt .= '\'' . $rKod['kodeorganisasi'] . '\'';

		if ($nord < $rTot) {
			$whreblok .= ',';
			$whre .= ',';
			$whrakt .= ',';
		}
	}

	$whreblok .= ')';
	$whre .= ')';
	$whrakt .= ')';
}
else if ($kdPt == '') {
	$whreblok = ' substr(kodeorg,1,4) in (';
	$whre = ' substr(kodeblok,1,4) in (';
	$whrakt = ' and substr(blok,1,4) in (';
	$sKod = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi  where tipe=\'KEBUN\'';

	#exit(mysql_error($conn));
	($qKod = mysql_query($sKod)) || true;
	$rTot = mysql_num_rows($qKod);

	while ($rKod = mysql_fetch_assoc($qKod)) {
		$nord += 1;
		$whreblok .= '\'' . $rKod['kodeorganisasi'] . '\'';
		$whre .= '\'' . $rKod['kodeorganisasi'] . '\'';
		$whrakt .= '\'' . $rKod['kodeorganisasi'] . '\'';

		if ($nord < $rTot) {
			$whreblok .= ',';
			$whre .= ',';
			$whrakt .= ',';
		}
	}

	$whreblok .= ')';
	$whre .= ')';
	$whrakt .= ')';
}

$optBulan['01'] = $_SESSION['lang']['jan'];
$optBulan['02'] = $_SESSION['lang']['peb'];
$optBulan['03'] = $_SESSION['lang']['mar'];
$optBulan['04'] = $_SESSION['lang']['apr'];
$optBulan['05'] = $_SESSION['lang']['mei'];
$optBulan['06'] = $_SESSION['lang']['jun'];
$optBulan['07'] = $_SESSION['lang']['jul'];
$optBulan['08'] = $_SESSION['lang']['agt'];
$optBulan['09'] = $_SESSION['lang']['sep'];
$optBulan['10'] = $_SESSION['lang']['okt'];
$optBulan['11'] = $_SESSION['lang']['nov'];
$optBulan['12'] = $_SESSION['lang']['dec'];

$totAktual=[];
$totBudget=[];
$subTotAkt=[];
$subTotBgt=[];
$totThnAkt=[];
$totThnBgt=[];
$TotAktualThn=[];
$TotBudgetThn=[];

$subTotLuas=[];
$subTotLuasBgt=[];
$sbTotLs=[];
$sbTotLsBgt=[];
$subTotAktual=[];
$subTotBudget=[];
$horTotAktual=[];
$horTotBudget=[];


$grndTotThnAkt=[];
$grndTotThnAkt2=[];
$grndTotThnBgt=[];
$grndTotThnBgt2=[];
$grndLuas=0;
$grndLuasBgt=0;

$granTotAktual=0;
$granTotBudet=0;


if ($proses != 'getData') {
	$sLuasMrh = "select sum(luasareaproduktif) as luas,substr(kodeorg,1,6) as afd,tahuntanam from $dbname.setup_blok ".
		"where " . $whreblok . " and statusblok='TM' ".
		"group by substr(kodeorg,1,6),tahuntanam order by substr(kodeorg,1,6),tahuntanam asc";

	#exit(mysql_error($conn));
	($qLuasMrh = mysql_query($sLuasMrh)) || true;

	while ($rLuasMrh = mysql_fetch_assoc($qLuasMrh)) {
		$sPt = "select distinct induk from $dbname.organisasi ".
			"where kodeorganisasi='" . substr($rLuasMrh['afd'], 0, 4) . "'";

		#exit(mysql_error($conn));
		($qPt = mysql_query($sPt)) || true;
		$rPt = mysql_fetch_assoc($qPt);
		$dtPt[$rLuasMrh['afd']] = $rPt['induk'];
		$dtLuas[$rLuasMrh['tahuntanam'] . $rLuasMrh['afd']] = $rLuasMrh['luas'];
		$dtAfdeling[$rLuasMrh['afd']] = $rLuasMrh['afd'];
		$dtThnTnm[$rLuasMrh['afd'] . $rLuasMrh['tahuntanam']] = $rLuasMrh['tahuntanam'];
		$amThnTnm[$rLuasMrh['tahuntanam']] = $rLuasMrh['tahuntanam'];
		$dtThnAfd[$rLuasMrh['tahuntanam'] . $rLuasMrh['afd']] = $rLuasMrh['afd'];
	}

	$sLuasBgt = "select sum(hathnini) as luasbgt,thntnm,substr(kodeblok,1,6) as afd from $dbname.bgt_blok ".
		"where  " . $whre . " and tahunbudget=" . $thnbgt[0] . " and statusblok='TM' ".
		"group by substr(kodeblok,1,6),thntnm order by substr(kodeblok,1,6),thntnm asc";
	#exit(mysql_error($conn));
	($qLuasBgt = mysql_query($sLuasBgt)) || true;

	while ($rLuasBgt = mysql_fetch_assoc($qLuasBgt)) {
		if ($rLuasBgt['luasbgt'] != 0) {
			$dtLuasBgt[$rLuasBgt['thntnm'] . $rLuasBgt['afd']] = $rLuasBgt['luasbgt'];
			$dtAfdeling[$rLuasBgt['afd']] = $rLuasBgt['afd'];
			$dtThnTnm[$rLuasBgt['thntnm'] . $rLuasBgt['afd']] = $rLuasBgt['thntnm'];
			$dtThnAfd[$rLuasBgt['thntnm'] . $rLuasBgt['afd']] = $rLuasBgt['afd'];
			$amThnTnm[$rLuasBgt['thntnm']] = $rLuasBgt['thntnm'];
		}
	}

	$asr5 = 1;

	while ($asr5 <= 12) {
		if ($asr5 < 10) {
			if ($asr5 == 1) {
				$field5 = 'sum(kg0' . $asr5 . ') as kg01';
			}
			else {
				$field5 .= ',sum(kg0' . $asr5 . ') as kg0' . $asr5 . '';
			}
		}
		else {
			$field5 .= ',sum(kg' . $asr5 . ') as kg' . $asr5 . '';
		}

		++$asr5;
	}

	$sBrtBgt = "select distinct $field5 ,substr(kodeblok,1,6) as afd,thntnm,sum(kgsetahun) as kgsetahun from $dbname.bgt_produksi_kbn_kg_vw ".
		"where ". $whre . " and tahunbudget='" . $periodeDt . "'   ".
		"group by substr(kodeblok,1,6),thntnm";

	#exit(mysql_error($conn));
	($qBrtBgt = mysql_query($sBrtBgt)) || true;

	while ($rBrtBgt = mysql_fetch_assoc($qBrtBgt)) {
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['01'] = $rBrtBgt['kg01'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['02'] = $rBrtBgt['kg02'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['03'] = $rBrtBgt['kg03'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['04'] = $rBrtBgt['kg04'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['05'] = $rBrtBgt['kg05'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['06'] = $rBrtBgt['kg06'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['07'] = $rBrtBgt['kg07'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['08'] = $rBrtBgt['kg08'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['09'] = $rBrtBgt['kg09'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['10'] = $rBrtBgt['kg10'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['11'] = $rBrtBgt['kg11'];
		$brtBgt[$rBrtBgt['thntnm'] . $rBrtBgt['afd']]['12'] = $rBrtBgt['kg12'];
		$bgtThnan[$rBrtBgt['thntnm'] . $rBrtBgt['afd']] = $rBrtBgt['kgsetahun'];
	}

	$sAktual = "select sum(kgwbtanpabrondolan) as brtaktual,substr(blok,1,6) as afd,tahuntanam,substr(periode,6,2) as bln from $dbname.kebun_spb_vs_rencana_blok_vw ".
		"where substr(periode,1,4)='" . $periodeDt ."' " . $whrakt .
		" group by substr(blok,1,6),tahuntanam,periode";

	#exit(mysql_error($conn));
	($qAktual = mysql_query($sAktual)) || true;

	while ($rAktual = mysql_fetch_assoc($qAktual)) {
		$brtAktual[$rAktual['tahuntanam'] . $rAktual['afd']][$rAktual['bln']] = $rAktual['brtaktual'];
	}

	$brd = 0;
	$agdDt = '';
	$lrt = 0;
	if (($proses == 'excel') || ($proses == 'pdf')) {
		$brd = 1;
		$bgcoloraja = 'bgcolor=#DEDEDE ';
		$tab .= '<table border=0>';
		$tab .= '<tr><td colspan=13>' . $_SESSION['lang']['pt'] . ' :[ ' . $nmpt . ' ] ' . $_SESSION['lang']['unit'] . ' : [ ' . $unit . ' ]</td></tr>';
		$tab .= '<tr><td colspan=13 align=center>LAPORAN PRODUKSI s.d. ' . $thnbgt[1] . '-' . $thnbgt[0] . ' </td></tr>';
		$tab .= '<tr><td colspan=13>' . $_SESSION['lang']['satuan'] . ' : Kg TBS  </td></tr>';
		$tab .= '<tr><td colspan=13>' . $_SESSION['lang']['periode'] . ' : ' . $thnbgt[1] . '-' . $thnbgt[0] . '  </td></tr>';
		$tab .= '</table>';
		$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brd . ' class=sortable>';
		$tab .= '<thead><tr class=rowheader>';
		$tab .= '<td rowspan=2 align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['tahuntanam'] . '</td>';
		$tab .= '<td rowspan=2 align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['afdeling'] . '</td>';
		$tab .= '<td colspan=2  align=center ' . $bgcoloraja . '>LUAS TM (Ha)</td>';
		$der = 1;

		while ($der <= 12) {
			$red = $der;

			if ($der < 10) {
				$red = '0' . $der;
			}

			$tab .= '<td colspan=2  align=center ' . $bgcoloraja . '>' . $optBulan[$red] . '</td>';
			++$der;
		}

		$tab .= '<td colspan=2  align=center ' . $bgcoloraja . '>' . $_SESSION['lang']['total'] . '</td></tr>';
		$tab .= '<tr>';
		$der2 = 1;

		while ($der2 <= 14) {
			$tab .= '<td align=center ' . $bgcoloraja . '>Aktual</td>';
			$tab .= '<td align=center ' . $bgcoloraja . '>Budget</td>';
			++$der2;
		}
		$tab .= '</tr></thead><tbody>';
		foreach ($amThnTnm as $rthntnm) {
			foreach ($dtAfdeling as $lstAfdeling) {
				$index = $rthntnm.$lstAfdeling ;
				if ($dtThnAfd[$index] != '') {
					$subTotLuas[$rthntnm] += $dtLuas[$index];//$lstAfdeling;
					$subTotLuasBgt[$rthntnm] += $dtLuasBgt[$index];//$lstAfdeling;
					$sbTotLs[$rthntnm] +=$dtLuas[$index];//$lstAfdeling;
					$sbTotLsBgt[$rthntnm]+= $subTotLuasBgt[$index];//$lstAfdeling;

					$tab .= '<tr class=rowcontent>';
					$tab .= '<td style=width:\'30px\'>' . $rthntnm . '</td>';
					$tab .= '<td style=width:\'30px\' align=center>' . $dtThnAfd[$index] . '</td>';
					$tab .= '<td style=width:\'30px\' align=right>' . number_format($dtLuas[$index], 2) . '</td>';
					$tab .= '<td style=width:\'30px\' align=right>' . number_format($dtLuasBgt[$index], 2) . '</td>';
					foreach ($optBulan as $ltBLn => $dtBln) {
						$tab .= '<td align=right style=width:\'30px\'>' . number_format($brtAktual[$index][$ltBLn], 2) . '</td>';
						$tab .= '<td align=right style=width:\'30px\'>' . number_format($brtBgt[$index][$ltBLn], 2) . '</td>';
						$totAktual[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$lstAfdeling . $rthntnm;
						$totBudget[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$lstAfdeling . $rthntnm;
						$subTotAkt[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$ltBLn;
						$subTotBgt[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$ltBLn;
						$totThnAkt[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$ltBLn;
						$totThnBgt[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$ltBLn;
						$TotAktualThn[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];
						$TotBudgetThn[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];

					}
					$tab .= '<td align=right style=width:\'30px\'>' . number_format($totAktual[$rthntnm ], 2) . '</td>';
					$tab .= '<td align=right style=width:\'30px\'>' . number_format($totBudget[$rthntnm], 2) . '</td>';
				}
			}

			if ($rthntnm != $agdDt) {
				$agdDt = $lstAfdeling;
				$tab .= '<tr class=rowcontent>';
				$tab .= '<td colspan=2><b>Sub ' . $_SESSION['lang']['total'] . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($subTotLuas[$rthntnm], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($subTotLuasBgt[$rthntnm], 2) . '</b></td>';


				foreach ($optBulan as $ltBLn => $dtBln) {
					$tab .= '<td align=right><b>' . number_format($totAktual[$rthntnm][$ltBLn], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($totBudget[$rthntnm][$ltBLn], 2) . '</b></td>';

					$horTotAktual[$rthntnm]+=$totAktual[$rthntnm][$ltBLn];
					$horTotBudget[$rthntnm]+=$totBudget[$rthntnm][$ltBLn];
				}

				$tab .= '<td align=right><b>' . number_format($horTotAktual[$rthntnm], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($horTotBudget[$rthntnm], 2) . '</b></td>';
				$tab .= '</tr>';
				$totAktual=[];//[$rthntnm][$ltBLn]  += 0;//$lstAfdeling . $rthntnm;
				$totBudget=[];//[$rthntnm][$ltBLn]  +=0;//$lstAfdeling . $rthntnm;
			}
		}

		asort($amThnTnm);
		$barisdtr = count($amThnTnm);
		$afd = true;
		$brc = true;
		$grndTotThnAkt=[];
		$grndTotThnAkt2=[];
		$grndTotThnBgt=[];
		$grndTotThnBgt2=[];
		$grndLuas=0;
		$grndLuasBgt=0;
		foreach ($amThnTnm as $rthntnm) {
			$tab .= '<tr  class=rowcontent>';

			if ($afd == true) {
				$tab .= '<td><b>' . $_SESSION['lang']['total'] . '</b></td>';
				$afd = false;
			}
			else if ($brc == true) {
				$tab .= '<td  rowspan=' . ($barisdtr - 1) . '>&nbsp;</td>';
				$brc = false;
			}

			$tab .= '<td><b>' . $rthntnm . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($sbTotLs[$rthntnm], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($sbTotLsBgt[$rthntnm], 2) . '</b></td>';

			foreach ($optBulan as $ltBLn => $dtBln) {
				$tab .= '<td align=right><b>' . number_format($totThnAkt[$rthntnm][$ltBLn], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($totThnBgt[$rthntnm][$ltBLn], 2) . '</b></td>';
				$grndTotThnAkt[$ltBLn] += $totThnAkt[$rthntnm][$ltBLn];
				$grndTotThnBgt[$ltBLn] += $totThnBgt[$rthntnm][$ltBLn];
			}
			$tab .= '<td align=right><b>' . number_format($horTotAktual[$rthntnm], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($horTotBudget[$rthntnm], 2) . '</b></td>';
			$tab .= '</tr>';
			$grndLuas += $sbTotLs[$rthntnm];
			$grndLuasBgt += $sbTotLsBgt[$rthntnm];
		}

		$tab .= '<tr  class=rowcontent>';
		$tab .= '<td colspan=2><b>Grand ' . $_SESSION['lang']['total'] . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($grndLuas, 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($grndLuasBgt, 2) . '</b></td>';

		$granTotAktual=0;
		$granTotBudet=0;
		foreach ($optBulan as $ltBLn => $dtBln) {
			$tab .= '<td align=right><b>' . number_format($grndTotThnAkt[$ltBLn], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($grndTotThnBgt[$ltBLn], 2) . '</b></td>';
			$granTotAktual+=$grndTotThnAkt[$ltBLn];
			$granTotBudet+=$grndTotThnBgt[$ltBLn];
		}

		$tab .= '<td align=right><b>' . number_format($granTotAktual, 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($granTotBudet, 2) . '</b></td>';
		$tab .= '</tr>';
		$tab .= '</tbody></table>';
	}
}

switch ($proses) {
	case 'getData':
		if ($kdPt != '') {
			$sOrg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '                       where induk=\'' . $kdPt . '\' and tipe=\'KEBUN\' order by namaorganisasi asc';
		}
		else {
			$sOrg = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi ' . "\r\n" . '                       where tipe=\'KEBUN\' order by namaorganisasi asc';
		}

		$optorg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

		#exit(mysql_error());
		($qOrg = mysql_query($sOrg)) || true;

		while ($rOrg = mysql_fetch_assoc($qOrg)) {
			$optorg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
		}

		echo $optorg;
		break;

	case 'preview':
		foreach ($amThnTnm as $rthntnm) {
			foreach ($dtAfdeling as $lstAfdeling) {
				$index = $rthntnm.$lstAfdeling ;
				if ($dtThnAfd[$index] != '') {
					$subTotLuas[$rthntnm] += $dtLuas[$index];//$lstAfdeling;
					$subTotLuasBgt[$rthntnm] += $dtLuasBgt[$index];//$lstAfdeling;
					$sbTotLs[$rthntnm] +=$dtLuas[$index];//$lstAfdeling;
					$sbTotLsBgt[$rthntnm]+= $subTotLuasBgt[$index];//$lstAfdeling;

					$tab .= '<tr class=rowcontent>';
					$tab .= '<td style=width:\'30px\'>' . $rthntnm . '</td>';
					$tab .= '<td style=width:\'30px\' align=center>' . $dtThnAfd[$index] . '</td>';
					$tab .= '<td style=width:\'30px\' align=right>' . number_format($dtLuas[$index], 2) . '</td>';
					$tab .= '<td style=width:\'30px\' align=right>' . number_format($dtLuasBgt[$index], 2) . '</td>';
					foreach ($optBulan as $ltBLn => $dtBln) {
						$tab .= '<td align=right style=width:\'30px\'>' . number_format($brtAktual[$index][$ltBLn], 2) . '</td>';
						$tab .= '<td align=right style=width:\'30px\'>' . number_format($brtBgt[$index][$ltBLn], 2) . '</td>';
						$totAktual[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$index;
						$totBudget[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$index;
						$subTotAkt[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$ltBLn;
						$subTotBgt[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$ltBLn;
						$totThnAkt[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];//$ltBLn;
						$totThnBgt[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];//$ltBLn;
						$TotAktualThn[$rthntnm][$ltBLn] += $brtAktual[$index][$ltBLn];
						$TotBudgetThn[$rthntnm][$ltBLn] += $brtBgt[$index][$ltBLn];

					}
					$tab .= '<td align=right style=width:\'30px\'>' . number_format($totAktual[$rthntnm ], 2) . '</td>';
					$tab .= '<td align=right style=width:\'30px\'>' . number_format($totBudget[$rthntnm], 2) . '</td>';
				}
			}

			if ($rthntnm != $agdDt) {
				$agdDt = $rthntnm;
				$tab .= '<tr class=rowcontent>';
				$tab .= '<td colspan=2><b>Sub ' . $_SESSION['lang']['total'] . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($subTotLuas[$rthntnm], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($subTotLuasBgt[$rthntnm], 2) . '</b></td>';


				foreach ($optBulan as $ltBLn => $dtBln) {
					$tab .= '<td align=right><b>' . number_format($totAktual[$rthntnm][$ltBLn], 2) . '</b></td>';
					$tab .= '<td align=right><b>' . number_format($totBudget[$rthntnm][$ltBLn], 2) . '</b></td>';

					$horTotAktual[$rthntnm]+=$totAktual[$rthntnm][$ltBLn];
					$horTotBudget[$rthntnm]+=$totBudget[$rthntnm][$ltBLn];
				}

				$tab .= '<td align=right><b>' . number_format($horTotAktual[$rthntnm], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($horTotBudget[$rthntnm], 2) . '</b></td>';
				$tab .= '</tr>';
				$totAktual=[];//[$rthntnm][$ltBLn]  += 0;//$index;
				$totBudget=[];//[$rthntnm][$ltBLn]  +=0;//$index;
			}
		}

		asort($amThnTnm);
		$barisdtr = count($amThnTnm);
		$afd = true;
		$brc = true;
		foreach ($amThnTnm as $rthntnm) {
			$tab .= '<tr  class=rowcontent>';

			if ($afd == true) {
				$tab .= '<td><b>' . $_SESSION['lang']['total'] . '</b></td>';
				$afd = false;
			}
			else if ($brc == true) {
				$tab .= '<td  rowspan=' . ($barisdtr - 1) . '>&nbsp;</td>';
				$brc = false;
			}

			$tab .= '<td><b>' . $rthntnm . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($sbTotLs[$rthntnm], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($sbTotLsBgt[$rthntnm], 2) . '</b></td>';

			foreach ($optBulan as $ltBLn => $dtBln) {
				$tab .= '<td align=right><b>' . number_format($totThnAkt[$rthntnm][$ltBLn], 2) . '</b></td>';
				$tab .= '<td align=right><b>' . number_format($totThnBgt[$rthntnm][$ltBLn], 2) . '</b></td>';
				$grndTotThnAkt[$ltBLn] += $totThnAkt[$rthntnm][$ltBLn];
				$grndTotThnBgt[$ltBLn] += $totThnBgt[$rthntnm][$ltBLn];
			}
			$tab .= '<td align=right><b>' . number_format($horTotAktual[$rthntnm], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($horTotBudget[$rthntnm], 2) . '</b></td>';
			$tab .= '</tr>';
			$grndLuas += $sbTotLs[$rthntnm];
			$grndLuasBgt += $sbTotLsBgt[$rthntnm];
		}

		$tab .= '<tr  class=rowcontent>';
		$tab .= '<td colspan=2><b>Grand ' . $_SESSION['lang']['total'] . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($grndLuas, 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($grndLuasBgt, 2) . '</b></td>';

		foreach ($optBulan as $ltBLn => $dtBln) {
			$tab .= '<td align=right><b>' . number_format($grndTotThnAkt[$ltBLn], 2) . '</b></td>';
			$tab .= '<td align=right><b>' . number_format($grndTotThnBgt[$ltBLn], 2) . '</b></td>';
			$granTotAktual+=$grndTotThnAkt[$ltBLn];
			$granTotBudet+=$grndTotThnBgt[$ltBLn];
		}

		$tab .= '<td align=right><b>' . number_format($granTotAktual, 2) . '</b></td>';
		$tab .= '<td align=right><b>' . number_format($granTotBudet, 2) . '</b></td>';
		$tab .= '</tr>';
		echo $tab;
		break;
	case 'excel':
		$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
		$dte = date('Hms');
		$nop_ = 'produksiTbsBulanan__' . $kdUnit . '__' . $dte;
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $tab);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '            </script>';
		break;

	case 'pdf':
		class PDF extends FPDF
		{
			public function Header()
			{
				global $periodeDt;
				global $kdUnit;
				global $unit;
				global $dbname;
				global $nmpt;
				global $kdPt;
				global $optBulan;
				$this->SetFont('Arial', 'B', 8);
				$this->Cell($width, $height, strtoupper('LAPORAN PRODUKSI BULANAN TAHUN ' . $periodeDt), 0, 1, 'L');
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['pt'] . ' : ' . $nmpt, 0, 1, 'L');
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $unit, 0, 1, 'L');
				$this->Cell(790, $height, ' ', 0, 1, 'R');
				$height = 12;
				$this->SetFillColor(220, 220, 220);
				$this->SetFont('Arial', 'B', 4);
				$tinggiAkr = $this->GetY();
				$ksamping = $this->GetX();
				$this->SetY($tinggiAkr + 20);
				$this->SetX($ksamping);
				$this->Cell(30, $height, $_SESSION['lang']['afdeling'], TLR, 0, 'C', 1);
				$this->Cell(30, $height, $_SESSION['lang']['tahuntanam'], TLR, 0, 'C', 1);
				$this->SetFont('Arial', 'B', 5);
				$this->Cell(60, $height, 'LUAS TM (Ha)', TLR, 0, 'C', 1);
				$der3 = 1;

				while ($der3 <= 12) {
					$red = $der3;

					if ($der3 < 10) {
						$red = '0' . $der3;
					}

					$this->Cell(50, $height, $optBulan[$red], TBLR, 0, 'C', 1);
					++$der3;
				}

				$this->Cell(60, $height, $_SESSION['lang']['total'], TBLR, 1, 'C', 1);
				$this->Cell(30, $height, ' ', BLR, 0, 'C', 1);
				$this->Cell(30, $height, ' ', BLR, 0, 'C', 1);
				$this->Cell(30, $height, 'Aktual', TBLR, 0, 'C', 1);
				$this->Cell(30, $height, 'Budget', TBLR, 0, 'C', 1);
				$der3 = 1;

				while ($der3 <= 12) {
					$this->Cell(25, $height, 'Aktual', TBLR, 0, 'C', 1);
					$this->Cell(25, $height, 'Budget', TBLR, 0, 'C', 1);
					++$der3;
				}

				$this->Cell(30, $height, 'Aktual', TBLR, 0, 'C', 1);
				$this->Cell(30, $height, 'Budget', TBLR, 1, 'C', 1);
			}

			public function Footer()
			{
				$this->SetY(-15);
				$this->SetFont('Arial', 'I', 8);
				$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
			}
		}

		$pdf = new PDF('L', 'pt', 'Legal');
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
		$height = 12;
		$tnggi = $jmlHari * $height;
		$pdf->AddPage();
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('Arial', '', 4);
		$i = 0;

		foreach ($amThnTnm as $rthntnm) {
			foreach ($dtAfdeling as $lstAfdeling) {
				$index = $rthntnm.$lstAfdeling ;
				if ($dtThnAfd[$index] != '') {
					$pdf->Cell(30, $height, $lstAfdeling, TBLR, 0, 'C', 1);
					$pdf->Cell(30, $height, $dtThnAfd[$index], TBLR, 0, 'C', 1);
					$pdf->Cell(30, $height, number_format($dtLuas[$index], 2), TBLR, 0, 'R', 1);
					$pdf->Cell(30, $height, number_format($dtLuasBgt[$index], 2), TBLR, 0, 'R', 1);

					foreach ($optBulan as $ltBLn => $dtBln) {
						$pdf->Cell(25, $height, number_format($brtAktual[$index][$ltBLn], 2), TBLR, 0, 'R', 1);
						$pdf->Cell(25, $height, number_format($brtAktual[$index][$ltBLn], 2), TBLR, 0, 'R', 1);
					}

					$pdf->Cell(30, $height, number_format($totAktual[$index], 2), TBLR, 0, 'R', 1);
					$pdf->Cell(30, $height, number_format($totBudget[$index], 2), TBLR, 1, 'R', 1);
				}
			}

			if ($rthntnm != $agdDt) {
				$agdDt = $rthntnm;
				$pdf->Cell(60, $height, 'Sub ' . $_SESSION['lang']['total'], TBLR, 0, 'C', 1);
				$pdf->Cell(30, $height, number_format($subTotLuas[$rthntnm], 2), TBLR, 0, 'R', 1);
				$pdf->Cell(30, $height, number_format($subTotLuasBgt[$rthntnm], 2), TBLR, 0, 'R', 1);

				foreach ($optBulan as $ltBLn => $dtBln) {
					$pdf->Cell(25, $height, number_format($subTotAkt[$rthntnm][$ltBLn], 2), TBLR, 0, 'R', 1);
					$pdf->Cell(25, $height, number_format($subTotBgt[$rthntnm][$ltBLn], 2), TBLR, 0, 'R', 1);
					$horTotAktual[$rthntnm]+=$totAktual[$rthntnm][$ltBLn];
					$horTotBudget[$rthntnm]+=$totBudget[$rthntnm][$ltBLn];
				}

				$pdf->Cell(30, $height, number_format($horTotAktual[$rthntnm], 2), TBLR, 0, 'R', 1);
				$pdf->Cell(30, $height, number_format($horTotBudget[$rthntnm], 2), TBLR, 1, 'R', 1);
			}
		}

		$rwDt = count($amThnTnm);
		$erer = true;
		$dterr = true;

		foreach ($amThnTnm as $rthntnm) {
			if ($erer == true) {
				$pdf->Cell(30, $height, $_SESSION['lang']['total'], TBLR, 0, 'C', 1);
				$erer = false;
			}
			else {
				$pdf->Cell(30, $height, ' ', TBLR, 0, 'C', 1);
			}

			$pdf->Cell(30, $height, $rthntnm, TBLR, 0, 'C', 1);
			$pdf->Cell(30, $height, number_format($sbTotLs[$rthntnm], 2), TBLR, 0, 'R', 1);
			$pdf->Cell(30, $height, number_format($sbTotLsBgt[$rthntnm], 2), TBLR, 0, 'R', 1);
			$pdf->SetFont('Arial', '', 3);

			foreach ($optBulan as $ltBLn => $dtBln) {
				$pdf->Cell(25, $height, number_format($totThnAkt[$rthntnm][$ltBLn], 2), TBLR, 0, 'R', 1);
				$pdf->Cell(25, $height, number_format($totThnBgt[$rthntnm][$ltBLn], 2), TBLR, 0, 'R', 1);
				$grndTotThnAkt[$ltBLn] += $totThnAkt[$rthntnm][$ltBLn];
				$grndTotThnBgt[$ltBLn] += $totThnBgt[$rthntnm][$ltBLn];
			}

			$pdf->SetFont('Arial', '', 4);
			$pdf->Cell(30, $height, number_format($TotAktualThn[$rthntnm], 2), TBLR, 0, 'R', 1);
			$pdf->Cell(30, $height, number_format($TotBudgetThn[$rthntnm], 2), TBLR, 1, 'R', 1);
			$grndLuas += $sbTotLs[$rthntnm];
			$grndLuasBgt += $sbTotLsBgt[$rthntnm];
		}

		$pdf->Cell(60, $height, 'Grand ' . $_SESSION['lang']['total'], TBLR, 0, 'C', 1);
		$pdf->Cell(30, $height, number_format($grndLuas, 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($grndLuasBgt, 2), TBLR, 0, 'R', 1);
		$pdf->SetFont('Arial', '', 3);

		$granTotAktual=0;
		$granTotBudet=0;
		foreach ($optBulan as $ltBLn => $dtBln) {
			$pdf->Cell(25, $height, number_format($grndTotThnAkt[$ltBLn], 2), TBLR, 0, 'R', 1);
			$pdf->Cell(25, $height, number_format($grndTotThnBgt[$ltBLn], 2), TBLR, 0, 'R', 1);
			$granTotAktual+=$grndTotThnAkt[$ltBLn];
			$granTotBudet+=$grndTotThnBgt[$ltBLn];
		}

		$pdf->Cell(30, $height, number_format($granTotAktual, 2), TBLR, 0, 'R', 1);
		$pdf->Cell(30, $height, number_format($granTotBudet, 2), TBLR, 1, 'R', 1);
		$pdf->Output();
		break;
}

?>
