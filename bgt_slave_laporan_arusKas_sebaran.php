<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['thnBudget3'] == '' ? $thnBudget = $_GET['thnBudget3'] : $thnBudget = $_POST['thnBudget3'];
$_POST['kdPt3'] == '' ? $kdPt = $_GET['kdPt3'] : $kdPt = $_POST['kdPt3'];
$_POST['kdUnit3'] == '' ? $kdUnit = $_GET['kdUnit3'] : $kdUnit = $_POST['kdUnit3'];

if ($thnBudget == '') {
	exit('Error:Tahun Budget Tidak Boleh Kosong');
}

$thn = $thnBudget - 1;
$thn = $thn . '12';

if (($kdPt != '') && ($kdUnit == '')) {
	$where = ' and kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\')';
	$where2 = ' and unit in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdPt . '\')';
}

if ($kdUnit != '') {
	$where = ' and kodeorg =\'' . $kdUnit . '\'';
	$where2 = ' and unit =\'' . $kdUnit . '\'';
}

$sSum = 'select distinct sum( awal12 ) AS awal12, noakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' and (substr(noakun,1,1)=\'5\' or substr(noakun,1,1)=\'9\') ' . "\r\n" . '       and noakun!=0 ' . $where . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum = mysql_query($sSum)) || true;

while ($rSum = mysql_fetch_assoc($qSum)) {
	$totalDes += $rSum['noakun'];
}

$sNoakun = 'select distinct noakun from ' . $dbname . '.keu_5akun where substr(noakun,1,1)=\'5\' or substr(noakun,1,2)=\'91\' order by noakun asc';

#exit(mysql_error());
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	if (($rNoakun['noakun'] != '5') && ($rNoakun['noakun'] != '91')) {
		$noAkun[] = $rNoakun['noakun'];
	}
}

$rupiahBulanPen = array();
$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, sum(rp01)  as rp01, sum(rp02) as rp02, sum(rp03) as rp03,' . "\r\n" . '    sum(rp04) as rp04, sum(rp05) as rp05, sum(rp06) as rp06, sum(rp07) as rp07, sum(rp08) as rp08, sum(rp09) as rp09, sum(rp10) as rp10, ' . "\r\n" . '    sum(rp11) as rp11, sum(rp12) as rp12 from ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    where tahunbudget=\'' . $thnBudget . '\' and  substr(noakun,1,1)=\'5\' or substr(noakun,1,2)=\'91\' and noakun!=0 ' . $where2 . "\r\n" . '    group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon += $rSum2['dnoakun'];
	$arAwal = 1;

	while ($arAwal < 13) {
		if (($rNoakun['noakun'] != '5') && ($rNoakun['noakun'] != '91')) {
			if (strlen($arAwal) < 2) {
				$dtke = '0' . $arAwal;
			}
			else {
				$dtke = $arAwal;
			}

			$rupiahBulanPen[$rSum2['dnoakun']][$arAwal] = $rSum2['rp' . $dtke];
		}

		++$arAwal;
	}
}

$sNoakun = 'select distinct substr(noakun,1,3) as noakun,namaakun from ' . $dbname . '.keu_5akun where substr(noakun,1,3)>\'125\' and ' . "\r\n" . '          substr(noakun,1,3)<\'129\' group by substr(noakun,1,3) order by noakun asc';

#exit(mysql_error());
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	$dtNoakun[] = $rNoakun['noakun'];
	$nmAkun[$rNoakun['noakun']] = $rNoakun['namaakun'];
}

$sNoakun = 'select distinct substr(noakun,1,3) as noakun,namaakun from ' . $dbname . '.keu_5akun where substr(noakun,1,1)=\'6\' ' . "\r\n" . '          group by substr(noakun,1,3) order by noakun asc';

#exit(mysql_error());
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	if (strlen($rNoakun['noakun']) == 3) {
		$dtNoakun2[] = $rNoakun['noakun'];
		$nmAkun2[$rNoakun['noakun']] = $rNoakun['namaakun'];
	}
}

$sNoakun = 'select distinct substr(noakun,1,3) as noakun,namaakun from ' . $dbname . '.keu_5akun where substr(noakun,1,1)=\'7\' or substr(noakun,1,1)=\'8\' ' . "\r\n" . '          group by substr(noakun,1,3) order by noakun asc';

#exit(mysql_error());
($qNoakun = mysql_query($sNoakun)) || true;

while ($rNoakun = mysql_fetch_assoc($qNoakun)) {
	if (strlen($rNoakun['noakun']) == 3) {
		$dtNoakun3[] = $rNoakun['noakun'];
		$nmAkun3[$rNoakun['noakun']] = $rNoakun['namaakun'];
	}
}

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' ' . "\r\n" . '           and (substr(noakun,1,3)>\'125\' and substr(noakun,1,3)<\'129\') and noakun!=0 ' . $where . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSumPeng = mysql_query($sSumPeng)) || true;

while ($rSumPeng = mysql_fetch_assoc($qSumPeng)) {
	if (strlen($rSumPeng['dnoakun']) == 3) {
		$totalDes += $rSumPeng['dnoakun'];
	}
}

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' ' . "\r\n" . '           and substr(noakun,1,1)=\'6\' and noakun!=0 ' . $where . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSumPeng = mysql_query($sSumPeng)) || true;

while ($rSumPeng = mysql_fetch_assoc($qSumPeng)) {
	if (strlen($rSumPeng['dnoakun']) == 3) {
		$totalDes += $rSumPeng['dnoakun'];
	}
}

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' ' . "\r\n" . '           and substr(noakun,1,1)=\'7\' or substr(noakun,1,1)=\'8\' and noakun!=0 ' . $where . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSumPeng = mysql_query($sSumPeng)) || true;

while ($rSumPeng = mysql_fetch_assoc($qSumPeng)) {
	if (strlen($rSumPeng['dnoakun']) == 3) {
		$totalDes += $rSumPeng['dnoakun'];
	}
}

$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKegiatan = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optBrng = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, sum(rp01)  as rp01, sum(rp02) as rp02, sum(rp03) as rp03,' . "\r\n" . '        sum(rp04) as rp04, sum(rp05) as rp05, sum(rp06) as rp06, sum(rp07) as rp07, sum(rp08) as rp08, sum(rp09) as rp09, sum(rp10) as rp10, ' . "\r\n" . '        sum(rp11) as rp11, sum(rp12) as rp12 from ' . $dbname . '.bgt_summary_biaya_vw where tahunbudget=\'' . $thnBudget . '\' and  ' . "\r\n" . '        (substr(noakun,1,3)>\'125\' and substr(noakun,1,3)<\'129\') and noakun!=0 ' . $where2 . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon += $rSum2['dnoakun'];
	$arAwal = 1;

	while ($arAwal < 13) {
		if (strlen($arAwal) < 2) {
			$dtke = '0' . $arAwal;
		}
		else {
			$dtke = $arAwal;
		}

		$rupiahBulan[$rSum2['dnoakun']][$arAwal] = $rSum2['rp' . $dtke];
		$byLangsung += $rSum2['rp' . $dtke];
		++$arAwal;
	}
}

$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, sum(rp01)  as rp01, sum(rp02) as rp02, sum(rp03) as rp03,' . "\r\n" . '        sum(rp04) as rp04, sum(rp05) as rp05, sum(rp06) as rp06, sum(rp07) as rp07, sum(rp08) as rp08, sum(rp09) as rp09, sum(rp10) as rp10, ' . "\r\n" . '        sum(rp11) as rp11, sum(rp12) as rp12 from ' . $dbname . '.bgt_summary_biaya_vw where tahunbudget=\'' . $thnBudget . '\' and  ' . "\r\n" . '        substr(noakun,1,1)=\'6\' and noakun!=0 ' . $where2 . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan2 = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon += $rSum2['dnoakun'];
	$arAwal = 1;

	while ($arAwal < 13) {
		if (strlen($rSum2['dnoakun']) == 3) {
			if (strlen($arAwal) < 2) {
				$dtke = '0' . $arAwal;
			}
			else {
				$dtke = $arAwal;
			}

			$rupiahBulan[$rSum2['dnoakun']][$arAwal] = $rSum2['rp' . $dtke];
			$byLangsung += $rSum2['rp' . $dtke];
		}

		++$arAwal;
	}
}

$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, sum(rp01)  as rp01, sum(rp02) as rp02, sum(rp03) as rp03,' . "\r\n" . '        sum(rp04) as rp04, sum(rp05) as rp05, sum(rp06) as rp06, sum(rp07) as rp07, sum(rp08) as rp08, sum(rp09) as rp09, sum(rp10) as rp10, ' . "\r\n" . '        sum(rp11) as rp11, sum(rp12) as rp12  from ' . $dbname . '.bgt_summary_biaya_vw where tahunbudget=\'' . $thnBudget . '\' and  ' . "\r\n" . '        substr(noakun,1,1)=\'7\' or substr(noakun,1,1)=\'8\' and noakun!=0 ' . $where2 . ' group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan3 = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon += $rSum2['dnoakun'];
	$arAwal = 1;

	while ($arAwal < 13) {
		if (strlen($rSum2['dnoakun']) == 3) {
			if (strlen($arAwal) < 2) {
				$dtke = '0' . $arAwal;
			}
			else {
				$dtke = $arAwal;
			}

			$rupiahBulan[$rSum2['dnoakun']][$arAwal] = $rSum2['rp' . $dtke];
			$byTdkLangsung += $rSum2['rp' . $dtke];
		}

		++$arAwal;
	}
}

$rup1 = count($rupiahBulan);
$dtCekAja = count($dtNoakun);
$dtCekAja2 = count($dtNoakun2);
$dtCekAja3 = count($dtNoakun3);
if (($rup1 == 0) || ($dtCekAja2 == 0) || ($dtCekAja3 == 0) || ($dtCekAja == 0)) {
	exit('Error:Data Kosong');
}

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab = '<table>' . "\r\n" . ' <tr><td colspan=5 align=left><font size=5>' . strtoupper($_SESSION['lang']['lapProyArusKas']) . ' ' . $thnBudget . ' ' . $_SESSION['lang']['per'] . ' ' . $_SESSION['lang']['bulan'] . '</font></td></tr> ' . "\r\n" . ' <tr><td colspan=5 align=left>' . $optNm[$kodeOrg] . '</td></tr>   ' . "\r\n" . ' <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td colspan=2 align=left>' . $thnBudget . '</td></tr>   ' . "\r\n" . ' </table>';
}
else {
	$bg = ' ';
	$brdr = 0;
}

if ($proses != 'PDF') {
	$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable width=100%><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td   valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td   valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['namaakun'] . '</td>';
	$tab .= '<td    align=center ' . $bg . '>' . $_SESSION['lang']['catatan'] . '</td>';

	foreach ($arrBln as $listBln) {
		$tab .= '<td  align=center ' . $bg . '>' . $listBln . '</td>';
	}

	$tab .= '<td  align=center ' . $bg . '>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '</tr>';
	$tab .= '</thead><tbody>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=left  colspan=3 >' . $_SESSION['lang']['aktivitaspenerimaan'] . '</td>';
	$tab .= '<td   align=right >&nbsp;</td>';
	$tab .= '<td   colspan=12 align=center >&nbsp;</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center  >&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=left  colspan=3 >' . $_SESSION['lang']['penerimaankas'] . '</td>';
	$tab .= '<td   colspan=12 align=center >&nbsp;</td>';
	$tab .= '</tr>';

	foreach ($noAkun as $listNoakun) {
		if ($stNoakun != substr($listNoakun, 0, 1)) {
			$brsDt = 1;
		}

		if ($brsDt == 1) {
			$brsDt = 0;
			$stNoakun = substr($listNoakun, 0, 1);
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td   valign=\'middle\' align=center  >' . $stNoakun . '</td>';
			$tab .= '<td   colspan=3 valign=\'middle\' align=left  >' . $optKegiatan[$stNoakun] . '</td>';
			$tab .= '<td   colspan=12 align=center  >&nbsp</td>';
			$tab .= '</tr>';
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td   valign=\'middle\' align=center  >' . $listNoakun . '</td>';
		$tab .= '<td   valign=\'middle\' align=left  >' . $optKegiatan[$listNoakun] . '</td>';
		$tab .= '<td   align=center >&nbsp</td>';
		$ter = 1;

		while ($ter < 13) {
			$tab .= '<td  align=right>' . number_format($rupiahBulanPen[$listNoakun][$ter], 2) . '</td>';
			$totalSemua += $listNoakun;
			++$ter;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$listNoakun], 2) . '</td>';
		$tab .= '</tr>';
		$grTotal += $totalDes[$listNoakun];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center  >&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=right  colspan=2 >' . $_SESSION['lang']['totalPenerimaan'] . '</td>';
	$tab .= '<td   align=right >' . number_format($grTotal, 2) . '</td>';
	$tab .= '<td   colspan=12 align=center >&nbsp;</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=left  colspan=3 >' . $_SESSION['lang']['aktivitaspengeluaran'] . '</td>';
	$tab .= '<td   align=right >&nbsp;</td>';
	$tab .= '<td   colspan=12 align=center >&nbsp;</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center>&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=left colspan=3 >' . $_SESSION['lang']['biayalangsung'] . '</td>';
	$tab .= '<td   colspan=12 align=center>&nbsp;</td>';
	$tab .= '</tr>';

	foreach ($dtNoakun as $barisNoakun) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td   valign=\'middle\' align=center >' . $barisNoakun . '</td>';
		$tab .= '<td   valign=\'middle\' align=left >' . $nmAkun[$barisNoakun] . '</td>';
		$tab .= '<td   align=center>&nbsp;</td>';
		$ter = 1;

		while ($ter < 13) {
			$tab .= '<td  align=right>' . number_format($rupiahBulan[$barisNoakun][$ter], 2) . '</td>';
			$totalSemua += $barisNoakun;
			$totLangsung += $ter;
			++$ter;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$barisNoakun], 2) . '</td>';
		$tab .= '</tr>';
		$totLanSma += $totalSemua[$barisNoakun];
	}

	foreach ($dtNoakun2 as $barisNoakun2) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td   valign=\'middle\' align=center>' . $barisNoakun2 . '</td>';
		$tab .= '<td   valign=\'middle\' align=left>' . $nmAkun2[$barisNoakun2] . '</td>';
		$tab .= '<td   align=center>&nbsp;</td>';
		$ter = 1;

		while ($ter < 13) {
			$tab .= '<td  align=right>' . number_format($rupiahBulan2[$barisNoakun2][$ter], 2) . '</td>';
			$totalSemua += $barisNoakun2;
			$totLangsung += $ter;
			++$ter;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$barisNoakun2], 2) . '</td>';
		$tab .= '</tr>';
		$totLanSma += $totalSemua[$barisNoakun2];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center colspan=3>&nbsp;</td>';
	$ter = 1;

	while ($ter < 13) {
		$tab .= '<td  align=right>' . number_format($totLangsung[$ter], 2) . '</td>';
		$totSema += $ter;
		++$ter;
	}

	$tab .= '<td  align=right>' . number_format($totLanSma, 2) . '</td>';
	$tab .= '</tr>';
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center >&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=left colspan=3 >' . $_SESSION['lang']['biayataklangsung'] . '</td>';
	$tab .= '<td   colspan=12 align=center>&nbsp;</td>';
	$tab .= '</tr>';

	foreach ($dtNoakun3 as $barisNoakun3) {
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td   valign=\'middle\' align=center >' . $barisNoakun3 . '</td>';
		$tab .= '<td   valign=\'middle\' align=left >' . $nmAkun3[$barisNoakun3] . '</td>';
		$tab .= '<td   align=center>&nbsp;</td>';
		$ter = 1;

		while ($ter < 13) {
			$tab .= '<td  align=right>' . number_format($rupiahBulan[$barisNoakun3][$ter], 2) . '</td>';
			$totalSemua += $barisNoakun3;
			$totTdkLangsung += $ter;
			++$ter;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$barisNoakun3], 2) . '</td>';
		$tab .= '</tr>';
		$totTlSma += $totalSemua[$barisNoakun3];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center colspan=3>&nbsp;</td>';
	$ter = 1;

	while ($ter < 13) {
		$tab .= '<td  align=right>' . number_format($totTdkLangsung[$ter], 2) . '</td>';
		$totSema += $ter;
		++$ter;
	}

	$tab .= '<td  align=right>' . number_format($totTlSma, 2) . '</td>';
	$tab .= '</tr>';
	$totKeluar = $totLanSma + $totTlSma;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center  >&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=right colspan=2 >' . $_SESSION['lang']['totalaktivitaskeluar'] . '</td>';
	$ter = 1;

	while ($ter < 13) {
		$tab .= '<td  align=right>' . number_format($totSema[$ter], 2) . '</td>';
		++$ter;
	}

	$tab .= '<td  align=right>' . number_format($totKeluar, 2) . '</td>';
	$tab .= '</tr>';
	$tab .= '</tbody></table>';
}

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lapProyeksiKas_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                        </script>';
	break;
}

?>
