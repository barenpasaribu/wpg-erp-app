<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
$proses = $_GET['proses'];
$_POST['thnBudget'] == '' ? $thnBudget = $_GET['thnBudget'] : $thnBudget = $_POST['thnBudget'];
$_POST['kdWS'] == '' ? $kdWS = $_GET['kdWS'] : $kdWS = $_POST['kdWS'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optNmbrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optAk = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', 'level=5');
$optKg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$str = 'select namakaryawan,karyawanid from ' . $dbname . '.datakaryawan where karyawanid=' . $_SESSION['standard']['userid'] . '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namakar[$bar->karyawanid] = $bar->namakaryawan;
}

$where = ' kodeorg like \'' . $kdWS . '%\' and tahunbudget=\'' . $thnBudget . '\'';

if ($_GET['proses'] == 'excel') {
	$bg = ' bgcolor=#DEDEDE';
	$brdr = 1;
	$tab .= '<table>' . "\r\n" . '             <tr><td colspan=4 align=left>' . $optNm[$kdWS] . '</td></tr>   ' . "\r\n" . '             <tr><td colspan=4>' . $_SESSION['lang']['budget'] . $_SESSION['lang']['detail'] . ' ' . $kdWS . ' ' . $_SESSION['lang']['budgetyear'] . ': ' . $thnBudget . '</td></tr>   ' . "\r\n" . '             </table>';
}
else {
	$bg = '';
	$brdr = 0;
}

$sDetail = 'select * from ' . $dbname . '.bgt_budget_detail where ' . $where . ' order by kodeorg, tipebudget, kodebudget, noakun, kegiatan, kodebarang, kodevhc';

#exit(mysql_error($conn));
($qDetail = mysql_query($sDetail)) || true;
$brscek = mysql_num_rows($qDetail);

if ($brscek != 0) {
	if (($kdWS == '') || ($thnBudget == '')) {
		exit('Error:Field Tidak Boleh Kosong');
	}

	$tab .= '<table cellspacing=1 cellpadding=1 border=' . $brdr . ' class=sortable><thead>';
	$tab .= '<tr class=rowheader>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>No</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['budgetyear'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kodeorg'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['tipeBudget'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kodebudget'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['noakun'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['namaakun'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kegiatan'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['namakegiatan'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['rotasi'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['volume'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['namabarang'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['regional'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jumlah'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['satuan'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['kodevhc'] . '</td>';
	$tab .= '<td rowspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jumlahsetahun'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jan'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['peb'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['mar'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['apr'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['mei'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jun'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['jul'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['agt'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['sep'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['okt'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['nov'] . '</td>';
	$tab .= '<td colspan=2 align=center ' . $bg . '>' . $_SESSION['lang']['dec'] . '</td></tr><tr>';
	$x = 1;

	while ($x < 13) {
		$tab .= '<td align=center ' . $bg . '>Rp.</td>';
		$tab .= '<td align=center ' . $bg . '>Fis</td>';
		++$x;
	}

	$tab .= '</tr></thead><tbody>';

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $thnBudget . '</td>';
		$tab .= '<td>' . $rDetail['kodeorg'] . '</td>';
		$tab .= '<td>' . $rDetail['tipebudget'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebudget'] . '</td>';
		$tab .= '<td>' . $rDetail['noakun'] . '</td>';
		$tab .= '<td>' . $optAk[$rDetail['noakun']] . '</td>';
		$tab .= '<td>' . $rDetail['kegiatan'] . '</td>';
		$tab .= '<td>' . $optKg[$rDetail['kegiatan']] . '</td>';
		$tab .= '<td align=right>' . $rDetail['rotasi'] . '</td>';

		if ($rDetail['volume'] != '') {
			$tab .= '<td align=right>' . number_format($rDetail['volume'], 2) . '</td>';
		}
		else {
			$tab .= '<td align=right></td>';
		}

		$tab .= '<td>' . $rDetail['satuanv'] . '</td>';
		$tab .= '<td>' . $rDetail['kodebarang'] . '</td>';
		$tab .= '<td>' . $optNmbrg[$rDetail['kodebarang']] . '</td>';
		$tab .= '<td>' . $rDetail['regional'] . '</td>';

		if ($rDetail['jumlah'] != 0) {
			$tab .= '<td align=right>' . number_format($rDetail['jumlah'], 2) . '</td>';
		}
		else {
			$tab .= '<td align=right></td>';
		}

		$tab .= '<td>' . $rDetail['satuanj'] . '</td>';
		$tab .= '<td>' . $rDetail['kodevhc'] . '</td>';
		$tab .= '<td align=right>' . number_format($rDetail['rupiah'], 2) . '</td>';
		$x = 1;

		while ($x < 13) {
			if (strlen($x) == 1) {
				$rprp = 'rp0' . $x;
				$fisfis = 'fis0' . $x;
			}
			else {
				$rprp = 'rp' . $x;
				$fisfis = 'fis' . $x;
			}

			if ($rDetail[$rprp] != 0) {
				$tab .= '<td align=right>' . number_format($rDetail[$rprp], 2) . '</td>';
			}
			else {
				$tab .= '<td align=right></td>';
			}

			if ($rDetail[$fisfis] != 0) {
				$tab .= '<td align=right>' . number_format($rDetail[$fisfis], 2) . '</td>';
			}
			else {
				$tab .= '<td align=right></td>';
			}

			$tot += $rprp;
			++$x;
		}

		$tab .= '</tr>';
		$totRp += $rDetail['rupiah'];
	}

	$tab .= '</tbody><thead><tr class=rowheader>';
	$tab .= '<td align=center align=right colspan=15 ' . $bg . '>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right ' . $bg . '>' . number_format($totRp, 2) . '</td>';
	$x = 1;

	while ($x < 13) {
		if (strlen($x) == 1) {
			$rprp = 'rp0' . $x;
			$fisfis = 'fis0' . $x;
		}
		else {
			$rprp = 'rp' . $x;
			$fisfis = 'fis' . $x;
		}

		$tab .= '<td align=right ' . $bg . '>' . number_format($tot[$rprp], 2) . '</td>';
		$tab .= '<td align=right ' . $bg . '></td>';
		++$x;
	}

	$tab .= '</tr>';
	$tab .= '</thead></table>';
}
else {
	exit('Error:Data Kosong');
}

switch ($proses) {
case 'preview':
	echo $tab;
	break;

case 'excel':
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHis');
	$nop_ = 'laporanBudgetDetail_' . $kdWS . '_' . $thnBudget . '___' . $dte;
	$stream = $tab;

	if (0 < strlen($stream)) {
		$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
		gzwrite($gztralala, $stream);
		gzclose($gztralala);
		echo '<script language=javascript1.2>' . "\r\n" . '                    window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                    </script>';
	}

	break;

case 'pdf':
	if ($thnBudget == '') {
		echo 'warning : a';
		exit();
	}
	else if ($kdWS == '') {
		echo 'warning : b';
		exit();
	}
	class PDF extends FPDF
	{
		public function Header()
		{
			global $thnBudget;
			global $kdWs;
			global $kdWS;
			global $totRp;
			global $conn;
			global $dbname;
			global $align;
			global $length;
			global $colArr;
			global $title;
			global $total;
			global $optKar;
			global $namakar;
			global $optNm;
			$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
			$orgData = fetchData($query);
			$width = $this->w - $this->lMargin - $this->rMargin;
			$height = 20;

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
			$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
			$this->SetX(100);
			$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
			$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->Cell(((20 / 100) * $width) - 5, $height, 'Biaya Bengkel', '', 0, 'L');
			$this->Ln();
			$this->SetFont('Arial', '', 10);
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Printed By : ' . $namakar[$_SESSION['standard']['userid']], '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Tanggal By : ' . date('d-m-Y'), '', 0, 'R');
			$this->Ln();
			$this->Cell(((100 / 100) * $width) - 5, $height, 'Time By : ' . date('h:i:s'), '', 0, 'R');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 12);
			$this->Cell($width, $height, strtoupper('Biaya ' . $optNm[$kdWS]), '', 0, 'C');
			$this->Ln();
			$this->Cell($width, $height, strtoupper('Tahun ' . $thnBudget), '', 0, 'C');
			$this->Ln();
			$this->Ln();
			$this->SetFont('Arial', 'B', 10);
			$this->SetFillColor(220, 220, 220);
			$this->Cell((2 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['workshop'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['kodeanggaran'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['volume'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
			$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
			$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['rp'], 1, 1, 'C', 1);
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
	$height = 20;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 7);
	$no = 0;
	$sql = 'select * from ' . $dbname . '.bgt_budget where tipebudget=\'WS\' and ' . $where . ' ';

	#exit(mysql_error());
	($qDet = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($qDet)) {
		$no += 1;
		$pdf->SetFontSize(10);
		$pdf->Cell((2 / 100) * $width, $height, $no, 1, 0, 'C', 1);
		$pdf->Cell((8 / 100) * $width, $height, $kdWS, 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, $res['kodebudget'], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, $optAk[$res['noakun']], 1, 0, 'L', 1);
		$pdf->Cell((15 / 100) * $width, $height, $optNmbrg[$res['kodebarang']], 1, 0, 'L', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['volume'], 2), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, $res['satuanv'], 1, 0, 'R', 1);
		$pdf->Cell((15 / 100) * $width, $height, number_format($res['jumlah'], 2), 1, 0, 'R', 1);
		$pdf->Cell((8 / 100) * $width, $height, $res['satuanj'], 1, 0, 'R', 1);
		$pdf->Cell((10 / 100) * $width, $height, number_format($res['rupiah'], 2), 1, 0, 'R', 1);
		$pdf->Ln();
	}

	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->Cell((91 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
	$pdf->SetFont('Arial', '', 10);
	$pdf->Cell((10 / 100) * $width, $height, number_format($totRp, 2), 1, 1, 'R', 1);
	$pdf->Output();
	break;
}

?>
