<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_GET['kodeorg'];
$thnbudget = $_GET['thnbudget'];
$lebarno = 3;
$lebarnoakun = 7;
$lebarnamaakun = 14;
$lebarrp = 7;
$lebarkg = 5;
$lebarbulan = 5;
$str = 'select sum(kgcpo) as cpo,sum(kgkernel) as kernel,sum(kgolah)  as tbs from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode = \'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$prd = $bar->cpo + $bar->kernel;
	$totTbs = $bar->tbs;
	$totCpo = $bar->cpo;
	$totKer = $bar->kernel;
}
class PDF extends FPDF
{
	public function Header()
	{
		global $kodeorg;
		global $thnbudget;
		global $lebarno;
		global $lebarnoakun;
		global $lebarnamaakun;
		global $lebarrp;
		global $lebarkg;
		global $lebarbulan;
		global $dbname;
		global $prd;
		global $totTbs;
		global $totCpo;
		global $totKer;
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
		$this->SetFont('Arial', 'B', 7);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(100);
		$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
		$this->SetX(100);
		$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
		$this->SetX(100);
		$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
		$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
		$this->Ln();
		$this->SetFont('Arial', '', 7);
		$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['budgetyear'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((57 / 100) * $width, $height, $thnbudget, '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((57 / 100) * $width, $height, $kodeorg, '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$title = 'Budget Biaya tidak langsung PKS ' . $kodeorg . ' tahun budget: ' . $thnbudget;
		$this->Ln();
		$this->SetFont('Arial', 'U', 7);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 7);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebarno / 100) * $width, $height, $_SESSION['lang']['nourut'], 1, 0, 'C', 1);
		$this->Cell(($lebarnoakun / 100) * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
		$this->Cell(($lebarnamaakun / 100) * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
		$this->Cell(($lebarrp / 100) * $width, $height, $_SESSION['lang']['jumlahrp'], 1, 0, 'C', 1);
		$this->Cell(($lebarkg / 100) * $width, $height, $_SESSION['lang']['rpperoil'], 1, 0, 'C', 1);
		$this->Cell(($lebarkg / 100) * $width, $height, $_SESSION['lang']['rppertbs'], 1, 0, 'C', 1);
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			if ($i != 12) {
				$this->Cell(($lebarbulan / 100) * $width, $height, $ii . '(Rp)', 1, 0, 'C', 1);
			}
			else {
				$this->Cell(($lebarbulan / 100) * $width, $height, $ii . '(Rp)', 1, 1, 'C', 1);
			}

			++$i;
		}
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
$pdf->SetFont('Arial', '', 6);
$str = 'select a.*,b.namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg=\'' . $kodeorg . '\'';
$res = mysql_query($str);
$no = 0;
$rpperha = 0;

if ($bar = mysql_fetch_object($res)) {
	@$rpperha = $bar->rupiah / $prd;
	@$rptbs = $bar->rupiah / $totTbs;
	$no += 1;
	$pdf->Cell(($lebarno / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebarnoakun / 100) * $width, $height, $bar->noakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebarnamaakun / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebarrp / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebarkg / 100) * $width, $height, number_format($rpperha, 2, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebarkg / 100) * $width, $height, number_format($rptbs, 2, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	if (strlen($i) == 1) {
		$ii = 'rp0' . $i;
	}
	else {
		$ii = 'rp' . $i;
	}

	if ($i != 12) {
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
	}
	else {
		$pdf->Cell(($lebarbulan / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
	}

	++$i;
}

$pdf->Output();

?>
