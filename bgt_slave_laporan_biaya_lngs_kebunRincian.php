<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['kdUnitRincian'] == '' ? $kodeOrg = $_GET['kdUnitRincian'] : $kodeOrg = $_POST['kdUnitRincian'];
$_POST['thnBudgetRincian'] == '' ? $thnBudget = $_GET['thnBudgetRincian'] : $thnBudget = $_POST['thnBudgetRincian'];
$_POST['noakun'] == '' ? $noakun = $_GET['noakun'] : $noakun = $_POST['noakun'];
$where = ' kodeunit=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\' and thntnm in (select distinct thntnm from ' . $dbname . '.bgt_budget_kebun_perakun_vw where  kodeorg=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\'  ) ';
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');
$optBrng = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE align=center';
	$brdr = 1;
	$tab = '<table>' . "\r\n" . ' <tr><td colspan=5 align=left><font size=5>' . strtoupper($_SESSION['lang']['lapLangsung']) . '</font></td></tr> ' . "\r\n" . ' <tr><td colspan=5 align=left>' . $optNm[$kodeOrg] . '</td></tr>   ' . "\r\n" . ' <tr><td>' . $_SESSION['lang']['budgetyear'] . '</td><td colspan=2 align=left>' . $thnBudget . '</td></tr>   ' . "\r\n" . ' </table>';
}
else {
	$bg = ' ';
	$brdr = 0;
}

if (($kodeOrg == '') || ($thnBudget == '')) {
	exit('Error:Field Tidak Boleh Kosong');
}

$tab .= 'Note:Fis<i>xx</i>=Dritribusi dari kolom Jumlah<br>' . "\r\n" . '                <table cellpadding=1 cellspacing=1 border=' . $brdr . ' class=sortable><thead>';
$tab .= '<tr class=rowheader>';
$tab .= '<td ' . $bg . '>No.</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['afdeling'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['blok'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['tahuntanam'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['kodebudget'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['kodekegiatan'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['volume'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['satuan'] . ' ' . $_SESSION['lang']['volume'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['rpperthn'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['namabarang'] . '</td>';
$tab .= '<td ' . $bg . '>Jlh/Thn</td>';
$tab .= '<td ' . $bg . '>Sat. Jlh</td>';
$awal = 1;

while ($awal < 13) {
	if ($awal < 10) {
		$awal = '0' . $awal;
	}

	$tab .= '<td ' . $bg . '>Rp.' . $awal . '</td>';
	$tab .= '<td ' . $bg . '>Fis.' . $awal . '</td>';
	++$awal;
}

$tab .= '<td ' . $bg . '>' . $_SESSION['lang']['luas'] . '</td>';
$tab .= '<td ' . $bg . '>Jlh.Pkk</td>';
$tab .= '<td ' . $bg . '>Pkk.produktif</td>';
$tab .= '</tr></thead><tbody>';
$sData = 'select distinct * from ' . $dbname . '.bgt_budget_kebun_perblok_vw where substr(kodeorg,1,4)=\'' . $kodeOrg . '\' and tahunbudget=\'' . $thnBudget . '\' order by substr(kodeorg,1,6) asc';

#exit(mysql_error($conn));
($qData = mysql_query($sData)) || true;

while ($rData = mysql_fetch_assoc($qData)) {
	$no += 1;
	$tab .= '<tr class=rowcontent>';
	$tab .= '<td>' . $no . '</td>';
	$tab .= '<td>' . substr($rData['kodeorg'], 0, 6) . '</td>';
	$tab .= '<td>' . $rData['kodeorg'] . '</td>';
	$tab .= '<td>' . $rData['thntnm'] . '</td>';
	$tab .= '<td>' . $rData['kodebudget'] . '</td>';
	$tab .= '<td>' . $rData['kegiatan'] . '</td>';
	$tab .= '<td>' . $optKegiatan[$rData['kegiatan']] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['volume'], 2) . '</td>';
	$tab .= '<td>' . $rData['satuanv'] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['rupiah'], 2) . '</td>';
	$tab .= '<td>' . $rData['kodebarang'] . '</td>';
	$tab .= '<td>' . $optBrng[$rData['kodebarang']] . '</td>';
	$tab .= '<td align=right>' . number_format($rData['jumlah'], 2) . '</td>';
	$tab .= '<td>' . $rData['satuanj'] . '</td>';
	$awal = 1;

	while ($awal < 13) {
		if ($awal < 10) {
			$awal = '0' . $awal;
		}

		$tab .= '<td align=right>' . number_format($rData['rp' . $awal], 2) . '</td>';
		$tab .= '<td align=right>' . number_format($rData['fis' . $awal], 2) . '</td>';
		++$awal;
	}

	$tab .= '<td align=right>' . number_format($rData['luas'], 2) . '</td>';
	$tab .= '<td align=right>' . number_format($rData['jlhpokok'], 0) . '</td>';
	$tab .= '<td align=right>' . number_format($rData['pokokproduktif'], 0) . '</td></tr>';
}

$tab .= '</tbody></table>';

switch ($proses) {
case 'preview':
	echo '<table cellspacing=1 cellpadding=1 border=0>';
	echo '<tr><td>' . $_SESSION['lang']['kebun'] . '</td><td>:</td><td>' . $optNm[$kodeOrg] . '</td>' . "\r\n" . '                 <td>' . $_SESSION['lang']['budgetyear'] . '</td><td>:</td><td>' . $thnBudget . '</td></tr>';
	echo '</table>';
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br />By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'lapKebunByLngsng_rincian_' . $dte;
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
			global $dbname;
			global $optAkun;
			global $optKegiatan;
			global $totRupiahKegiatan;
			global $totRupiah;
			global $ttlLuastbm;
			global $arrLang;
			global $rSum;
			global $kodeOrg;
			global $thnBudget;
			global $awal;
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
			$this->SetFont('Arial', 'B', 5);
			$this->Cell(100, 10, $_SESSION['lang']['total'], 1, 1, 'C', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(100, 10, 'TM=' . number_format($ttlLuastm, 2) . ' TBM=' . number_format($ttlLuastbm, 2) . ' Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(50, 10, number_format($rSum['ton'], 2), 1, 0, 'R', 1);
			$this->Cell(50, 10, 'Kg', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			@$hslBagi = $rSum['ton'] / 1000 / $ttlLuastm;
			$this->Cell(50, 10, number_format($hslBagi, 2), 1, 0, 'R', 1);
			$this->Cell(50, 10, 'Ton/Ha', 1, 1, 'L', 1);
			$xPertama = $this->GetX();
			$this->SetX($xPertama + 208);
			$this->Cell(50, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
			$this->Cell(50, 10, 'RP/Ha', 1, 1, 'L', 1);
			$br = 308;
			$ypertama = $this->GetY();
			$dtLang = 0;

			while ($dtLang <= 4) {
				$this->SetY($ypertama - 50);
				$xPertama = $this->GetX();
				$this->SetX($xPertama + $br);
				$this->Cell(100, 40, $arrLang[$dtLang], 1, 1, 'C', 1);
				$xPertama = $this->GetX();
				$this->SetX($xPertama + $br);
				$this->Cell(50, 10, $_SESSION['lang']['total'], 1, 0, 'R', 1);
				$this->Cell(50, 10, 'RP/Ha', 1, 1, 'L', 1);
				$br += 100;
				++$dtLang;
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
	$awal = 0;

	foreach ($listNoakun as $barisNoakun) {
		$new2 = substr($barisNoakun, 0, 3);

		if (($ktKrgng2 != '') && ($ktKrgng2 != $new2)) {
			$pdf->SetFont('Arial', 'B', 5);
			$xPertama = $pdf->GetX();
			$pdf->SetX($xPertama);

			if (substr($ktKrgng2, 0, 1) != '6') {
				if ($ktKrgng2 == '126') {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TBM', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastbm;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
				}
				else if ($ktKrgng2 == '128') {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' BIBITAN', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = 0;
					$ttlLuastbm = 0;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
				}
			}
			else {
				$ktKrgng2 = substr($ktKrgng2, 0, 1);
				$sTotal = 'select distinct kegiatan from ' . $dbname . '. bgt_budget_kegiatan_vw  where  afdeling like \'' . $kodeOrg . '%\' and tahunbudget=\'' . $thnBudget . '\' and substring(kegiatan,1,1)=\'6\'';

				exit(mysql_error($sTotal));
				($qTotal = mysql_query($sTotal)) || true;
				$rTotal = mysql_num_rows($qTotal);
				$awal += 1;

				if ($awal == $rTotal) {
					$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TM', 1, 0, 'R', 1);
					$xPertama = $pdf->GetX();
					$pdf->SetX($xPertama);
					@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastm;
					$ttlLuastbm = $ttlLuastm;
					$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
					$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
					$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
					$awal = 0;
				}
			}
		}

		@$kegHa = $totRupiahKegiatan[$thnBudget][$barisNoakun] / $ttlLuastbm;
		@$kegSdm = $dataKeg[$thnBudget][$barisNoakun][sdm] / $ttlLuastbm;
		@$kegMat = $dataKeg[$thnBudget][$barisNoakun][mat] / $ttlLuastbm;
		@$kegTool = $dataKeg[$thnBudget][$barisNoakun][tool] / $ttlLuastbm;
		@$kegVhc = $dataKeg[$thnBudget][$barisNoakun][vhc] / $ttlLuastbm;
		@$kegKntrak = $dataKeg[$thnBudget][$barisNoakun][kntrk] / $ttlLuastbm;
		$pdf->Cell(58, $height, $barisNoakun, 1, 0, 'L', 1);
		$pdf->Cell(150, $height, $optKegiatan[$barisNoakun], 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($totRupiahKegiatan[$thnBudget][$barisNoakun], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegHa, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][sdm], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegSdm, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][mat], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegMat, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][tool], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegTool, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][vhc], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegVhc, 2), 1, 0, 'L', 1);
		$pdf->Cell(50, 10, number_format($dataKeg[$thnBudget][$barisNoakun][kntrk], 2), 1, 0, 'R', 1);
		$pdf->Cell(50, 10, number_format($kegKntrak, 2), 1, 1, 'L', 1);

		if (substr($barisNoakun, 0, 1) == '6') {
			$ktKrgng2 = substr($barisNoakun, 0, 1);
		}
		else {
			$ktKrgng2 = substr($barisNoakun, 0, 3);
		}

		$grnTotKeg2 += $totRupiahKegiatan[$thnBudget][$barisNoakun];
		$grnTotKegha2 += $kegHa;
		$grnTotKegSdm2 += $dataKeg[$thnBudget][$barisNoakun][sdm];
		$grnTotKeghaSdm2 += $kegSdm;
		$grnTotKegMat2 += $dataKeg[$thnBudget][$barisNoakun][mat];
		$grnTotKeghaMat2 += $kegMat;
		$grnTotKegTool2 += $dataKeg[$thnBudget][$barisNoakun][tool];
		$grnTotKeghaTool2 += $kegTool;
		$grnTotKegVhc2 += $dataKeg[$thnBudget][$barisNoakun][vhc];
		$grnTotKeghaVhc2 += $kegVhc;
		$grnTotKegKntrak2 += $dataKeg[$thnBudget][$barisNoakun][kntrk];
		$grnTotKeghaKntrak2 += $kegKntrak;
	}

	if ($ktKrgng2 == '126') {
		$ttlLuastbm = $ttlLuastbm;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TBM', 1, 0, 'R', 1);
	}
	else if ($ktKrgng2 == '128') {
		$ttlLuastbm = 0;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' BIBITAN', 1, 0, 'R', 1);
	}
	else if ($ktKrgng2 == '6') {
		$ttlLuastbm = $ttlLuastm;
		$pdf->Cell(208, $height, $_SESSION['lang']['total'] . ' TM', 1, 0, 'R', 1);
	}

	$xPertama = $pdf->GetX();
	$pdf->SetX($xPertama);
	@$hsilBagi = $totRupiah[$thnBudget][$ktKrgng2] / $ttlLuastbm;
	$pdf->Cell(50, 10, number_format($totRupiah[$thnBudget][$ktKrgng2], 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($hsilBagi, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][sdm], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][sdm] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][mat], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][mat] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][tool], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][tool] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][vhc], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][vhc] / $ttlLuastbm, 2), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($totalKplaSDM[$thnBudget][$ktKrgng2][kntrk], 2), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format(@$totalKplaSDM[$thnBudget][$ktKrgng2][kntrk] / $ttlLuastbm, 2), 1, 1, 'L', 1);
	$pdf->Cell(208, $height, $_SESSION['lang']['grnd_total'], 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeg2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegha2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegSdm2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaSdm2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegMat2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaMat2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegTool2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaTool2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegVhc2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaVhc2, 0), 1, 0, 'L', 1);
	$pdf->Cell(50, 10, number_format($grnTotKegKntrak2, 0), 1, 0, 'R', 1);
	$pdf->Cell(50, 10, number_format($grnTotKeghaKntrak2, 0), 1, 1, 'L', 1);
	$pdf->Output();
	break;
}

?>
