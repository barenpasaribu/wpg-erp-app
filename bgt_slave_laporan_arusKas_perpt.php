<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['thnBudget4'] == '' ? $thnBudget = $_GET['thnBudget4'] : $thnBudget = $_POST['thnBudget4'];
$_POST['kdUnit4'] == '' ? $kdUnit = $_GET['kdUnit4'] : $kdUnit = $_POST['kdUnit4'];
if (($thnBudget == '') || ($kdUnit == '')) {
	exit('Error:Field Inputan Tidak Boleh Kosong');
}

$thn = $thnBudget - 1;
$thn = $thn . '12';
$sSum = 'select distinct sum( awal12 ) AS awal12, noakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' ' . "\r\n" . '      and kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\')' . "\r\n" . '      and (substr(noakun,1,1)=\'5\' or substr(noakun,1,1)=\'9\') and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

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
$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun,unit from ' . $dbname . '.bgt_summary_biaya_vw ' . "\r\n" . '    where tahunbudget=\'' . $thnBudget . '\' and  substr(noakun,1,1)=\'5\' or substr(noakun,1,2)=\'91\' and noakun!=0 and' . "\r\n" . '    unit in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\')' . "\r\n" . '    group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon[$rSum2['dnoakun']] += $rSum2['unit'];
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

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' and ' . "\r\n" . '           kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\')' . "\r\n" . '           and (substr(noakun,1,3)>\'125\' and substr(noakun,1,3)<\'129\') and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSumPeng = mysql_query($sSumPeng)) || true;

while ($rSumPeng = mysql_fetch_assoc($qSumPeng)) {
	if (strlen($rSumPeng['dnoakun']) == 3) {
		$totalDes += $rSumPeng['dnoakun'];
	}
}

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' and ' . "\r\n" . '           kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\')' . "\r\n" . '           and substr(noakun,1,1)=\'6\' and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSumPeng = mysql_query($sSumPeng)) || true;

while ($rSumPeng = mysql_fetch_assoc($qSumPeng)) {
	if (strlen($rSumPeng['dnoakun']) == 3) {
		$totalDes += $rSumPeng['dnoakun'];
	}
}

$sSumPeng = 'select distinct sum( awal12 ) AS awal12, substr(noakun,1,3) as dnoakun from ' . $dbname . '.keu_saldobulanan where periode=\'' . $thn . '\' and ' . "\r\n" . '           kodeorg in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\')' . "\r\n" . '           and substr(noakun,1,1)=\'7\' or substr(noakun,1,1)=\'8\' and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

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
$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun,unit from ' . $dbname . '.bgt_summary_biaya_vw where ' . "\r\n" . '        tahunbudget=\'' . $thnBudget . '\' and  unit in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and' . "\r\n" . '        (substr(noakun,1,3)>\'125\' and substr(noakun,1,3)<\'129\') and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon2[$rSum2['unit']] += $rSum2['dnoakun'];
	$byLangsung += $rSum2['setahun'];
}

$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, unit from ' . $dbname . '.bgt_summary_biaya_vw where ' . "\r\n" . '        tahunbudget=\'' . $thnBudget . '\' and  unit in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and' . "\r\n" . '        substr(noakun,1,1)=\'6\' and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan2 = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon3[$rSum2['dnoakun']] += $rSum2['unit'];
	$byLangsung += $rSum2['setahun'];
}

$sSum2 = 'select distinct substring(noakun, 1, 3) AS dnoakun,sum(setahun) as setahun, unit from ' . $dbname . '.bgt_summary_biaya_vw where ' . "\r\n" . '        tahunbudget=\'' . $thnBudget . '\' and  unit in (select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\') and' . "\r\n" . '        substr(noakun,1,1)=\'7\' or substr(noakun,1,1)=\'8\' and noakun!=0 group by substr(noakun,1,3) order by substr(noakun,1,3) asc';

#exit(mysql_error($conn));
($qSum2 = mysql_query($sSum2)) || true;
$rupiahBulan2 = array();

while ($rSum2 = mysql_fetch_assoc($qSum2)) {
	$rupAkunStaon5[$rSum2['dnoakun']] += $rSum2['unit'];
	$byTdkLangsung += $rSum2['setahun'];
}

$sUnit = 'select distinct kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kdUnit . '\'';

#exit(mysql_error());
($qUnit = mysql_query($sUnit)) || true;

while ($rUnit = mysql_fetch_assoc($qUnit)) {
	$listUnit[] = $rUnit['kodeorganisasi'];
}

$dtCekAja = count($dtNoakun);
$dtCekAja2 = count($dtNoakun2);
$dtCekAja3 = count($dtNoakun3);
if (($dtCekAja2 == 0) || ($dtCekAja3 == 0) || ($dtCekAja == 0)) {
	exit('Error:Data Kosong');
}

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab = '<table>' . "\r\n" . ' <tr><td colspan=5 align=left><font size=5>' . strtoupper($_SESSION['lang']['lapProyArusKas']) . ' ' . $thnBudget . ' ' . $_SESSION['lang']['per'] . ' ' . $_SESSION['lang']['pt'] . '</font></td></tr> ' . "\r\n" . ' <tr><td colspan=5 align=left>' . $optNm[$kodeOrg] . '</td></tr>   ' . "\r\n" . ' <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td colspan=2 align=left>' . $thnBudget . '</td></tr>   ' . "\r\n" . ' </table>';
}
else {
	$bg = ' ';
	$brdr = 0;
}

if ($proses != 'PDF') {
	$tab .= '<table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable width=100%><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td   valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td   valign=\'middle\' align=center ' . $bg . ' >' . $_SESSION['lang']['namaakun'] . '</td>';
	$tab .= '<td    align=center ' . $bg . '>' . $_SESSION['lang']['catatan'] . '</td>';

	foreach ($listUnit as $listBln) {
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
			$tab .= '<td   colspan=13 align=center  >&nbsp</td>';
			$tab .= '</tr>';
		}

		$tab .= '<tr class=rowcontent>';
		$tab .= '<td   valign=\'middle\' align=center  >' . $listNoakun . '</td>';
		$tab .= '<td   valign=\'middle\' align=left  >' . $optKegiatan[$listNoakun] . '</td>';
		$tab .= '<td   align=center >&nbsp</td>';

		foreach ($listUnit as $listBln) {
			$tab .= '<td  align=right>' . number_format($rupAkunStaon[$listNoakun][$listBln], 2) . '</td>';
			$totalSemua += $listNoakun;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$listNoakun], 2) . '</td>';
		$tab .= '</tr>';
		$grTotal += $totalSemua[$listNoakun];
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

		foreach ($listUnit as $listBln) {
			$tab .= '<td  align=right>' . number_format($rupAkunStaon2[$barisNoakun][$listBln], 2) . '</td>';
			$totalSemua += $barisNoakun;
			$totLangsung += $listBln;
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

		foreach ($listUnit as $listBln) {
			$tab .= '<td  align=right>' . number_format($rupAkunStaon3[$barisNoakun2][$listBln], 2) . '</td>';
			$totalSemua += $barisNoakun2;
			$totLangsung += $listBln;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$barisNoakun2], 2) . '</td>';
		$tab .= '</tr>';
		$totLanSma += $totalSemua[$barisNoakun2];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center>&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=left>&nbsp;</td>';
	$tab .= '<td   align=center>&nbsp;</td>';

	foreach ($listUnit as $listBln) {
		$tab .= '<td  align=right>' . number_format($totLangsung[$listBln], 2) . '</td>';
		$totSema += $listBln;
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

		foreach ($listUnit as $listBln) {
			$tab .= '<td  align=right>' . number_format($rupAkunStaon5[$barisNoakun3][$listBln], 2) . '</td>';
			$totalSemua += $barisNoakun3;
			$totTdkLangsung += $listBln;
		}

		$tab .= '<td  align=right>' . number_format($totalSemua[$barisNoakun3], 2) . '</td>';
		$tab .= '</tr>';
		$totTlSma += $totalSemua[$barisNoakun3];
	}

	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center>&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=left>&nbsp;</td>';
	$tab .= '<td   align=center>&nbsp;</td>';

	foreach ($listUnit as $listBln) {
		$tab .= '<td  align=right>' . number_format($totTdkLangsung[$listBln], 2) . '</td>';
		$totSema += $listBln;
	}

	$tab .= '<td  align=right>' . number_format($totTlSma, 2) . '</td>';
	$tab .= '</tr>';
	$totKeluar = $totLanSma + $totTlSma;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td   valign=\'middle\' align=center  >&nbsp;</td>';
	$tab .= '<td   valign=\'middle\' align=right colspan=2 >' . $_SESSION['lang']['totalaktivitaskeluar'] . '</td>';

	foreach ($listUnit as $listBln) {
		$tab .= '<td  align=right>' . number_format($totSema[$listBln], 2) . '</td>';
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
	$nop_ = 'lapProyeksiKasPerPt_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                        window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                        </script>';
	break;

case 'pdf':
	if (($kodeOrg == '') || ($thnBudget == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $dtJjg;
			global $dtThnBudget;
			global $dtNoakun;
			global $dtJmlhKg;
			global $brsThnBudget;
			global $dtJmlhLuastm;
			global $dtJmlhLuastbm;
			global $totKg;
			global $totJjg;
			global $ttlLuastm;
			global $ttlLuastbm;
			global $ttlLuas;
			global $dbname;
			global $barisNoakun;
			global $kodeOrg;
			global $totRupiah;
			global $rSum;
			global $lstRupiah;
			global $thnBudget;
			global $hasilBagi;
			global $hasilBagi2;
			global $optNm;
			$sAlmat = 'select namaorganisasi,alamat,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'';

			#exit(mysql_error());
			($qAlamat = mysql_query($sAlmat)) || true;
			$rAlamat = mysql_fetch_assoc($qAlamat);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 10;

			if ($_SESSION['org']['kodeorganisasi'] == 'SSP') {
				$path = 'images/SSP_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {
				$path = 'images/MI_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {
				$path = 'images/HS_logo.jpg';
			}
			else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {
				$path = 'images/BM_logo.jpg';
			}

			$this->Image($path, $this->lMargin, $this->tMargin, 70);
			$this->SetFont('Arial', 'B', 9);
			$this->SetFillColor(255, 255, 255);
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $rAlamat['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 11);
			$this->Cell($width, $height, strtoupper($_SESSION['lang']['lapLangsung']), 0, 1, 'C');
			$this->Ln();
			$this->Cell($width, $height, $_SESSION['lang']['unit'] . ' : ' . $optNm[$kodeOrg], 0, 1, 'C');
			$this->SetFont('Arial', '', 8);
			$this->Cell(850, $height, $_SESSION['lang']['tanggal'], 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, date('d-m-Y H:i'), 0, 1, 'R');
			$this->Cell(850, $height, $_SESSION['lang']['page'], 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, $this->PageNo(), 0, 1, 'R');
			$this->Cell(850, $height, 'User', 0, 0, 'R');
			$this->Cell(10, $height, ':', '', 0, 0, 'R');
			$this->Cell(70, $height, $_SESSION['standard']['username'], 0, 1, 'R');
			$this->Ln();
			$this->Ln();
			$height = 50;
			$this->SetFillColor(220, 220, 220);
			$this->SetFont('Arial', 'B', 7);
			$this->Cell(58, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
			$this->Cell(150, $height, $_SESSION['lang']['namakegiatan'], 1, 0, 'C', 1);
			$this->SetFont('Arial', 'B', 4.5);
			$this->Cell(80, 10, $_SESSION['lang']['total'], 1, 1, 'C', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(80, 10, 'TM=' . number_format($ttlLuastm, 2) . ' TBM=' . number_format($ttlLuastbm, 2) . ' Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(40, 10, number_format($rSum['ton'], 2), 1, 0, 'R', 1);
			$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			@$tnha = $rSum['ton'] / 1000 / $ttlLuastm;
			$this->Cell(40, 10, number_format($tnha, 2), 1, 0, 'R', 1);
			$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
			$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
			$br = 288;

			foreach ($dtThnBudget as $listThn) {
				$no += 1;

				if ($no == 1) {
					$ypertama = $this->GetY();
					$this->SetY($ypertama - 50);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, $listThn, 1, 1, 'C', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, 'TM=' . number_format($dtJmlhLuastm[$thnBudget][$listThn], 2) . 'TBM=' . number_format($dtJmlhLuastbm[$thnBudget][$listThn], 2) . ' Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, number_format($dtJmlhKg[$thnBudget][$listThn], 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					@$tnha = $dtJmlhKg[$thnBudget][$listThn] / 1000 / $dtJmlhLuastm[$thnBudget][$listThn];
					$this->Cell(40, 10, number_format($tnha, 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
					$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
				}
				else {
					$ypertama = $this->GetY();
					$this->SetY($ypertama - 50);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, $listThn, 1, 1, 'C', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(80, 10, 'TM=' . number_format($dtJmlhLuastm[$thnBudget][$listThn], 2) . ' TBM=' . number_format($dtJmlhLuastbm[$thnBudget][$listThn], 2) . ' Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, number_format($dtJmlhKg[$thnBudget][$listThn], 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Kg', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					@$tnha = $dtJmlhKg[$thnBudget][$listThn] / 1000 / $dtJmlhLuastm[$thnBudget][$listThn];
					$this->Cell(40, 10, number_format($tnha, 2), 1, 0, 'R', 1);
					$this->Cell(40, 10, 'Ton/Ha', 1, 1, 'L', 1);
					$xPertama = $this->GetX();
					$this->SetX($xPertama + $br);
					$this->Cell(40, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
					$this->Cell(40, 10, 'RP/Ha', 1, 1, 'L', 1);
				}

				$br += 80;
			}
		}

		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', 'I', 8);
			$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		}
	}


	$pdf = new PDF('L', 'pt', 'LEGAL');
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 10;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', 'B', 5);
	$totThn = count($dtThnBudget);
	$totAkun = count($dtNoakun);
	$totalRptm = '';
	$totalbagitm = '';
	$gttm = '';
	$bagitm = '';
	$totalRptbm = '';
	$totalbagitbm = '';
	$gttbm = '';
	$bagitbm = '';
	$gtbbt = '';
	$totalRpbbt = '';
	$ard = 1;
	$totThn = count($dtThnBudget);
	$totAkun = count($dtNoakun);
	$totalRptm = '';
	$totalbagitm = '';
	$gttm = '';
	$bagitm = '';
	$totalRptbm = '';
	$totalbagitbm = '';
	$gttbm = '';
	$bagitbm = '';
	$gtbbt = '';
	$totalRpbbt = '';
	$ard = 1;

	foreach ($dtNoakun as $barisNoakun) {
		$drAwal += 1;

		if ($ktKrgng != substr($barisNoakun, 0, 3)) {
			$brs = 1;

			if ($drAwal != 1) {
				$yPertama = $pdf->GetY();
				$pdf->SetY($yPertama);
			}
		}

		if ($brs == 1) {
			$colTotal = 80 + (80 * $totThn);
			$ktKrgng = substr($barisNoakun, 0, 3);

			if (substr($barisNoakun, 0, 3) == '126') {
				@$hasilBagiRup[$ktKrgng] = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuastbm;
				$pdf->SetFont('Arial', 'B', 5);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TBM', 1, 0, 'L', 1);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(40, 10, number_format($dtNoakunRup2[$thnBudget][$ktKrgng], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagiRup[$ktKrgng], 0), 1, 0, 'R', 1);
			}
			else if (substr($barisNoakun, 0, 3) == '128') {
				@$hasilBagiRup[$ktKrgng] = 0;
				$pdf->SetFont('Arial', 'B', 5);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' BIBITAN', 1, 0, 'L', 1);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(40, 10, number_format($dtNoakunRup2[$thnBudget][$ktKrgng], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagiRup[$ktKrgng], 0), 1, 0, 'R', 1);
			}
			else if (substr($barisNoakun, 0, 1) == '6') {
				$ktKrgng = substr($ktKrgng, 0, 1);
				@$hasilBagiRup[$ktKrgng] = $dtNoakunRup2[$thnBudget][$ktKrgng] / $ttlLuastm;
				$pdf->SetFont('Arial', 'B', 5);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TM', 1, 0, 'L', 1);
				$xPertama = $pdf->GetX();
				$pdf->SetX($xPertama);
				$pdf->Cell(40, 10, number_format($dtNoakunRup2[$thnBudget][$ktKrgng], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagiRup[$ktKrgng], 0), 1, 0, 'R', 1);
			}

			foreach ($dtThnBudget as $lstThaTnm) {
				if (substr($barisNoakun, 0, 3) == '126') {
					@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = $dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng] / $ttlLuastbm;
				}
				else if (substr($barisNoakun, 0, 3) == '128') {
					@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = 0;
				}
				else if (substr($barisNoakun, 0, 1) == '6') {
					$ktKrgng = substr($ktKrgng, 0, 1);
					@$hslBagi[$thnBudget][$lstThaTnm][$ktKrgng] = $dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng] / $ttlLuastm;
				}

				if ($ard < $totThn) {
					$ard += 1;
					$pdf->Cell(40, 10, number_format($dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng], 0), 1, 0, 'R', 1);
					$pdf->Cell(40, 10, number_format($hslBagi[$thnBudget][$lstThaTnm][$ktKrgng], 0), 1, 0, 'R', 1);
				}
				else {
					$ard = 1;
					$pdf->Cell(40, 10, number_format($dtNoakunRup[$thnBudget][$lstThaTnm][$ktKrgng], 0), 1, 0, 'R', 1);
					$pdf->Cell(40, 10, number_format($hslBagi[$thnBudget][$lstThaTnm][$ktKrgng], 0), 1, 1, 'R', 1);
				}
			}

			$brs = 0;
			$awal = 1;
		}

		$pdf->SetFont('Arial', '', 5);
		$yAkhir = $pdf->GetY();
		$xPertama = $pdf->GetX();
		$pdf->SetY($yAkhir);
		$pdf->SetX($xPertama);
		$pdf->Cell(58, $height, $barisNoakun, 1, 0, 'L', 1);
		$pdf->Cell(150, $height, $optKegiatan[$barisNoakun], 1, 0, 'L', 1);

		if (substr($barisNoakun, 0, 3 == '126')) {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastbm;
		}
		else if (substr($barisNoakun, 0, 3 == '128')) {
			@$hasilBagi = 0;
		}
		else if (substr($barisNoakun, 0, 1 == '6')) {
			@$hasilBagi[$barisNoakun] = $totRupiah[$thnBudget][$barisNoakun] / $ttlLuastm;
		}

		$pdf->Cell(40, 10, number_format($totRupiah[$thnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
		$pdf->Cell(40, 10, number_format($hasilBagi[$barisNoakun], 0), 1, 0, 'R', 1);
		$grndTotal += $totRupiah[$thnBudget][$barisNoakun];
		$grndTotalHsil += $hasilBagi[$barisNoakun];
		$yAkhir = $pdf->GetY();
		$xPertama = $pdf->GetX();
		$pdf->SetY($yAkhir);
		$pdf->SetX($xPertama);
		$rd = 1;

		foreach ($dtThnBudget as $brsThnBudget) {
			if (substr($barisNoakun, 0, 3) == '126') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastbm[$thnBudget][$brsThnBudget];
				$totalRptbm += $brsThnBudget;
				$totalbagitbm += $brsThnBudget;
				$gttbm += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitbm = $gttbm / $ttlLuastbm;
			}
			else if (substr($barisNoakun, 0, 3) == '128') {
				$hasilBagi2 = 0;
				$totalRpbbt += $brsThnBudget;
				$totalbagbbt += $brsThnBudget;
				$gtbbt += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitbm = 0;
			}
			else if (substr($barisNoakun, 0, 1) == '6') {
				@$hasilBagi2[$brsThnBudget] = $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun] / $dtJmlhLuastm[$thnBudget][$brsThnBudget];
				$totalRptm += $brsThnBudget;
				$totalbagitm += $brsThnBudget;
				$gttm += $lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun];
				$bagitm = $gttm / $ttlLuastm;
			}

			if ($rd < $totThn) {
				$pdf->Cell(40, 10, number_format($lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagi2[$brsThnBudget], 0), 1, 0, 'R', 1);
			}
			else {
				$pdf->Cell(40, 10, number_format($lstRupiah[$thnBudget][$brsThnBudget][$barisNoakun], 0), 1, 0, 'R', 1);
				$pdf->Cell(40, 10, number_format($hasilBagi2[$brsThnBudget], 0), 1, 1, 'R', 1);
			}

			$rd += 1;
		}
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'BBT', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gtbbt, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format(0, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRpbbt[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format(0, 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRpbbt[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format(0, 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'TBM', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gttbm, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($bagitbm, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRptbm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitbm[$brsThnBudget], 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRptbm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitbm[$brsThnBudget], 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Cell(208, $height, $_SESSION['lang']['total'] . 'TM', 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($gttm, 0), 1, 0, 'R', 1);
	$pdf->Cell(40, 10, number_format($bagitm, 0), 1, 0, 'R', 1);
	$yAkhir = $pdf->GetY();
	$xPertama = $pdf->GetX();
	$pdf->SetY($yAkhir);
	$pdf->SetX($xPertama);
	$drd = 1;

	foreach ($dtThnBudget as $brsThnBudget) {
		if ($drd < $totThn) {
			$pdf->Cell(40, 10, number_format($totalRptm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitm[$brsThnBudget], 0), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(40, 10, number_format($totalRptm[$brsThnBudget], 0), 1, 0, 'R', 1);
			$pdf->Cell(40, 10, number_format($totalbagitm[$brsThnBudget], 0), 1, 1, 'R', 1);
		}

		$drd += 1;
	}

	$pdf->Output();
	break;
}

?>
