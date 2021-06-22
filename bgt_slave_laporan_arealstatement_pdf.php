<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$tahun = $_GET['tahun'];
$kebun = $_GET['kebun'];

if ($tahun == '') {
	echo 'WARNING: silakan mengisi tahun.';
	exit();
}

if ($kebun == '') {
	echo 'WARNING: silakan mengisi kebun.';
	exit();
}

$isidata = array();
$str = 'select sum(hathnini) as hathnini,sum(hanonproduktif) as hanonproduktif,sum(pokokproduksi) as pokokproduksi,' . "\r\n" . '      thntnm,substr(kodeblok,1,6) as afdeling,statusblok,sum(pokokthnini) as pokokthnini from ' . $dbname . '.bgt_blok where' . "\r\n" . '      substr(kodeblok,1,4)=\'' . $kebun . '\' and tahunbudget = \'' . $tahun . '\' and statusblok != \'BBT\' group by substr(kodeblok,1,6),thntnm,statusblok' . "\r\n" . '      order by substr(kodeblok,1,6),thntnm';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	if (($bar->thntnm + 3) < $tahun) {
		if ($bar->statusblok != 'CADANGAN') {
			$isidata[$bar->thntnm . $bar->statusblok] += $bar->afdeling;
			$totalrowdata[$bar->thntnm . $bar->statusblok] += total;
			$totalcolumndata[$bar->afdeling . $bar->statusblok] += total;
			$total += $bar->statusblok;
			$rowdata0[$bar->thntnm . $bar->statusblok] = $bar->thntnm;
		}
	}
	else if ($bar->statusblok != 'CADANGAN') {
		if ($bar->statusblok == 'TB') {
			$bar->statusblok = 'TBM';
		}

		$isidata1[$bar->thntnm . $bar->statusblok] += $bar->afdeling;
		$totalrowdata1[$bar->thntnm . $bar->statusblok] += total;
		$totalcolumndata1[$bar->afdeling . $bar->statusblok] += total;
		$total1 += $bar->statusblok;
		$rowdata1[$bar->thntnm . $bar->statusblok] = $bar->thntnm;
	}

	if ($bar->statusblok == 'CADANGAN') {
		$bar->hanonproduktif = $bar->hathnini;
	}

	$unplanted += $bar->afdeling;
	$totalunplanted += $bar->hanonproduktif;
	$kadaster += $bar->afdeling;
	$totalkadaster += $bar->hathnini + $bar->hanonproduktif;
	$isidata2[$bar->thntnm] += $bar->afdeling;
	$totalrowdata2[$bar->thntnm] += total;
	$totalcolumndata2[$bar->afdeling] += total;
	$total2 += $bar->pokokthnini;
	$pkkProduktif[$bar->thntnm] += $bar->afdeling;
	$totPkkProduktif += $bar->pokokproduksi;
	$totPerthnPkk[$bar->thntnm] += total;
	$totAfdPkkProduktif[$bar->afdeling] += total;
	$headerdata[$bar->afdeling] = $bar->afdeling;
	$rowdata[$bar->thntnm] = $bar->thntnm;
}

0 < count($headerdata) ? sort($headerdata) : false;
0 < count($rowdata) ? sort($rowdata) : false;
0 < count($rowdata0) ? sort($rowdata0) : false;
0 < count($rowdata1) ? sort($rowdata1) : false;
$jumlahafdeling = 0;

if (!empty($headerdata)) {
	foreach ($headerdata as $baris1) {
		$jumlahafdeling += 1;
	}
}

$jumlahrow = 0;

if (!empty($rowdata)) {
	foreach ($rowdata as $baris2) {
		$jumlahrow += 1;
	}
}
else {
	echo 'Data tidak tersedia.';
	exit();
}
class PDF extends FPDF
{
	public function Header()
	{
		global $tahun;
		global $kebun;
		global $dbname;
		global $headerdata;
		global $isidata;
		global $jumlahafdeling;
		global $statTbm;
		$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
		$orgData = fetchData($query);
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;

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
		$this->SetFont('Arial', '', 8);
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['budgetyear'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((70 / 100) * $width, $height, $tahun, '', 0, 'L');
		$this->Cell(((7 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((70 / 100) * $width, $height, $kebun, '', 0, 'L');
		$this->Cell(((7 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$title = $_SESSION['lang']['arealstatement'];
		$this->Ln();
		$this->SetFont('Arial', 'U', 12);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 10);
		$this->SetFillColor(220, 220, 220);
		$this->Cell((15 / 100) * $width, $height, $_SESSION['lang']['uraian'], 1, 0, 'C', 1);
		$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['tahuntanam'], 1, 0, 'C', 1);
		$this->Cell((10 / 100) * $width * $jumlahafdeling, $height, $_SESSION['lang']['afdeling'], 1, 0, 'C', 1);
		$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
		$this->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
		$this->Cell((10 / 100) * $width, $height, '', LRB, 0, 'C', 1);

		if (!empty($headerdata)) {
			foreach ($headerdata as $baris) {
				$this->Cell((10 / 100) * $width, $height, $baris, 1, 0, 'C', 1);
			}
		}

		$this->Cell((10 / 100) * $width, $height, '', LRB, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}


$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 10);
$statTm = 'TM';
$countdown = $jumlahrow;

if (!empty($rowdata0)) {
	foreach ($rowdata0 as $tt) {
		if ($tt != 0) {
			if ($countdown == $jumlahrow) {
				$pdf->Cell((15 / 100) * $width, $height, 'A. Luas Areal TM (ha)', LRT, 0, 'L', 1);
			}
			else {
				$pdf->Cell((15 / 100) * $width, $height, '', LR, 0, 'C', 1);
			}

			$pdf->Cell((10 / 100) * $width, $height, $tt, 1, 0, 'C', 1);

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					$pdf->Cell((10 / 100) * $width, $height, number_format($isidata[$tt . $statTm][$af], 2), 1, 0, 'R', 1);
					$totalplanted_tm += $af;
				}
			}

			$pdf->Cell((10 / 100) * $width, $height, number_format($totalrowdata[$tt . $statTm][total], 2), 1, 1, 'R', 1);
			$countdown -= 1;
		}
	}
}

if (!empty($rowdata0)) {
	$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->Cell((10 / 100) * $width, $height, 'Subtotal', 1, 0, 'C', 1);

	if (!empty($headerdata)) {
		foreach ($headerdata as $af) {
			$pdf->Cell((10 / 100) * $width, $height, number_format($totalcolumndata[$af . $statTm][total], 2), 1, 0, 'R', 1);
		}
	}

	$pdf->Cell((10 / 100) * $width, $height, number_format($total[$statTm], 2), 1, 1, 'R', 1);
	$pdf->SetFillColor(255, 255, 255);
}

$statTbm = 'TBM';
$countdown = $jumlahrow;

if (!empty($rowdata1)) {
	foreach ($rowdata1 as $tt) {
		if ($tt != 0) {
			if ($countdown == $jumlahrow) {
				$pdf->Cell((15 / 100) * $width, $height, 'B. Luas Areal (ha)', LRT, 0, 'L', 1);
			}
			else {
				$pdf->Cell((15 / 100) * $width, $height, '', LR, 0, 'C', 1);
			}

			$pdf->Cell((10 / 100) * $width, $height, $tt, 1, 0, 'C', 1);

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					$pdf->Cell((10 / 100) * $width, $height, number_format($isidata1[$tt . $statTbm][$af], 2), 1, 0, 'R', 1);
					$totalplanted_tbm += $af;
				}
			}

			$pdf->Cell((10 / 100) * $width, $height, number_format($totalrowdata1[$tt . $statTbm][total], 2), 1, 1, 'R', 1);
			$countdown -= 1;
		}
	}
}

if (!empty($rowdata1)) {
	$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
	$pdf->SetFillColor(220, 220, 220);
	$pdf->Cell((10 / 100) * $width, $height, 'Subtotal', 1, 0, 'C', 1);

	if (!empty($headerdata)) {
		foreach ($headerdata as $af) {
			$pdf->Cell((10 / 100) * $width, $height, number_format($totalcolumndata1[$af . $statTbm][total], 2), 1, 0, 'R', 1);
		}
	}

	$pdf->Cell((10 / 100) * $width, $height, number_format($total1[$statTbm], 2), 1, 1, 'R', 1);
	$pdf->SetFillColor(255, 255, 255);
}

$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((10 / 100) * $width, $height, 'TOTAL PLANTED', 1, 0, 'C', 1);

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$tp = $totalplanted_tbm[$af] + $totalplanted_tm[$af];
		$pdf->Cell((10 / 100) * $width, $height, number_format($tp, 2), 1, 0, 'R', 1);
	}
}

$ttp = $total1[$statTbm] + $total[$statTm];
$pdf->Cell((10 / 100) * $width, $height, number_format($ttp, 2), 1, 1, 'R', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
$pdf->Cell((10 / 100) * $width, $height, 'Unplanted', 1, 0, 'C', 1);

if (!empty($unplanted)) {
	foreach ($unplanted as $af) {
		$pdf->Cell((10 / 100) * $width, $height, number_format($af, 2), 1, 0, 'R', 1);
	}
}

$pdf->Cell((10 / 100) * $width, $height, number_format($totalunplanted, 2), 1, 1, 'R', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((10 / 100) * $width, $height, 'GRAND TOTAL ', 1, 0, 'C', 1);

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$gt = $totalplanted_tbm[$af] + $totalplanted_tm[$af] + $unplanted[$af];
		$pdf->Cell((10 / 100) * $width, $height, number_format($gt, 2), 1, 0, 'R', 1);
	}

	$tgt = $ttp + $totalunplanted;
	$pdf->Cell((10 / 100) * $width, $height, number_format($tgt, 2), 1, 1, 'R', 1);
	$stream .= '</tr>';
}

$countdown = $jumlahrow;

if (!empty($rowdata)) {
	foreach ($rowdata as $tt) {
		if ($tt != 0) {
			$pdf->SetFont('Arial', '', 8);

			if ($countdown == $jumlahrow) {
				$pdf->Cell((15 / 100) * $width, $height, 'C. Populasi (pkk)', LRT, 0, 'L', 1);
			}
			else {
				$pdf->Cell((15 / 100) * $width, $height, '', LR, 0, 'C', 1);
			}

			$pdf->SetFont('Arial', '', 6);
			$pdf->Cell((10 / 100) * $width, $height, $tt, 1, 0, 'C', 1);

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					$pdf->Cell((5 / 100) * $width, $height, number_format($isidata2[$tt][$af], 2), 1, 0, 'R', 1);
					$pdf->Cell((5 / 100) * $width, $height, number_format($pkkProduktif[$tt][$af], 2), 1, 0, 'R', 1);
				}
			}

			$pdf->Cell((5 / 100) * $width, $height, number_format($totalrowdata2[$tt][total], 2), 1, 0, 'R', 1);
			$pdf->Cell((5 / 100) * $width, $height, number_format($totAfdPkkProduktif[$tt][total], 2), 1, 1, 'R', 1);
			$countdown -= 1;
		}
	}
}

$pdf->Cell((15 / 100) * $width, $height, '', LRB, 0, 'C', 1);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((10 / 100) * $width, $height, 'Total Areal', 1, 0, 'C', 1);

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$pdf->Cell((5 / 100) * $width, $height, number_format($totalcolumndata2[$af][total], 2), 1, 0, 'R', 1);
		$pdf->Cell((5 / 100) * $width, $height, number_format($totAfdPkkProduktif[$af][total], 2), 1, 0, 'R', 1);
	}
}

$pdf->Cell((5 / 100) * $width, $height, number_format($total2, 2), 1, 0, 'R', 1);
$pdf->Cell((5 / 100) * $width, $height, number_format($totPkkProduktif, 2), 1, 1, 'R', 1);
$pdf->SetFillColor(255, 255, 255);
$pdf->Output();
exit();
$stream = '';
$stream .= '<table class=sortable cellspacing=1 border=1 width=100%>' . "\r\n" . '     <thead>' . "\r\n" . '        <tr class=rowtitle>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['uraian'] . '</td>' . "\r\n" . '            <td rowspan=2 align=center>' . $_SESSION['lang']['tahuntanam'] . '</td>' . "\r\n" . '            <td colspan=' . $jumlahafdeling . ' align=center>Data per Afdeling</td>';
$stream .= '<td rowspan=2 align=center>' . $_SESSION['lang']['total'] . '</td>' . "\r\n" . '        </tr>';

if (!empty($headerdata)) {
	foreach ($headerdata as $baris) {
		$stream .= '<td align=center>' . $baris . '</td>';
	}
}

$stream .= '</thead>' . "\r\n" . '    <tbody>';
$countdown = $jumlahrow;

if (!empty($rowdata)) {
	foreach ($rowdata as $tt) {
		if ($tt != 0) {
			$stream .= '<tr class=rowcontent>';

			if ($countdown == $jumlahrow) {
				$stream .= '<td align=left>A. Luas Areal (ha)</td>';
			}
			else {
				$stream .= '<td align=center>&nbsp;</td>';
			}

			$stream .= '<td align=center>' . $tt . '</td>';

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					$stream .= '<td align=right>' . number_format($isidata[$tt][$af], 2) . '</td>';
				}
			}

			$stream .= '<td align=right>' . number_format($totalrowdata[$tt][total], 2) . '</td>';
			$stream .= '</tr>';
			$countdown -= 1;
		}
	}
}

$stream .= '<tr class=rowcontent>';
$stream .= '<td align=center>&nbsp;</td>';
$stream .= '<td align=center>Total Areal</td>';

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$stream .= '<td align=right>' . number_format($totalcolumndata[$af][total], 2) . '</td>';
	}
}

$stream .= '<td align=right>' . number_format($total, 2) . '</td>';
$stream .= '</tr>';
$countdown = $jumlahrow;

if (!empty($rowdata)) {
	foreach ($rowdata as $tt) {
		if ($tt != 0) {
			$stream .= '<tr class=rowcontent>';

			if ($countdown == $jumlahrow) {
				$stream .= '<td align=left>B. Populasi Tanaman (pkk)</td>';
			}
			else {
				$stream .= '<td align=center>&nbsp;</td>';
			}

			$stream .= '<td align=center>' . $tt . '</td>';

			if (!empty($headerdata)) {
				foreach ($headerdata as $af) {
					$stream .= '<td align=right>' . number_format($isidata2[$tt][$af]) . '</td>';
				}
			}

			$stream .= '<td align=right>' . number_format($totalrowdata2[$tt][total]) . '</td>';
			$stream .= '</tr>';
			$countdown -= 1;
		}
	}
}

$stream .= '<tr class=rowcontent>';
$stream .= '<td align=center>&nbsp;</td>';
$stream .= '<td align=center>Total Pokok</td>';

if (!empty($headerdata)) {
	foreach ($headerdata as $af) {
		$stream .= '<td align=right>' . number_format($totalcolumndata2[$af][total]) . '</td>';
	}
}

$stream .= '<td align=right>' . number_format($total2) . '</td>';
$stream .= '</tr>';
$stream .= '    </tbody>' . "\r\n" . '         <tfoot>' . "\r\n" . '         </tfoot>' . "\t\t" . ' ' . "\r\n" . '   </table>';
$qwe = date('YmdHms');
$nop_ = 'bgt_arealstatement' . $tahun . ' ' . $kebun;

if (0 < strlen($stream)) {
	if ($handle = opendir('tempExcel')) {
		while (false !== $file = readdir($handle)) {
			if (($file != '.') && ($file != '..')) {
				@unlink('tempExcel/' . $file);
			}
		}

		closedir($handle);
	}

	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

	if (!fwrite($handle, $stream)) {
		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
		exit();
	}
	else {
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
	}

	closedir($handle);
}

?>
