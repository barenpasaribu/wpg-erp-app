<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$kodeorg = $_GET['kodeorg'];
$thnbudget = $_GET['thnbudget'];
$jenis = $_GET['jenis'];
$lebar1 = 3;
$lebar2 = 7;
$lebar3 = 12;
$lebar4 = 7;
$lebar5 = 4;
$lebar6 = 5.5;
$str = 'select sum(kgolah) as kgolah,sum(kgcpo) as kgcpo,sum(kgkernel) as kgkernel from ' . $dbname . '.bgt_produksi_pks_vw ' . "\r\n" . '      where tahunbudget=' . $thnbudget . ' and millcode=\'' . $kodeorg . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$kgolah = $bar->kgolah;
	$kgcpo = $bar->kgcpo;
	$kgkernel = $bar->kgkernel;
}
class PDF extends FPDF
{
	public function Header()
	{
		global $kodeorg;
		global $thnbudget;
		global $lebar1;
		global $lebar2;
		global $lebar3;
		global $lebar4;
		global $lebar5;
		global $lebar6;
		global $dbname;
		global $kgolah;
		global $jenis;
		global $kgoil;
		global $kgkernel;
		global $kgcpo;
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
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['produksi'] . ' PP', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((8 / 100) * $width, $height, number_format(($kgoil / 1000) . 'Ton', 0, '.', ','), '', 0, 'R');
		$this->Cell((52 / 100) * $width, $height, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['produksi'] . ' CPO', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((8 / 100) * $width, $height, number_format(($kgcpo / 1000) . 'Ton', 0, '.', ','), '', 0, 'R');
		$this->Cell((52 / 100) * $width, '', '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['produksi'] . ' KER', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((8 / 100) * $width, $height, number_format(($kgkernel / 1000) . 'Ton', 0, '.', ','), '', 1, 'R');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'TBS', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((8 / 100) * $width, $height, number_format(($kgolah / 1000) . 'Ton', 0, '.', ','), '', 0, 'R');
		$title = 'BUDGET BIAYA ' . $jenis . ' PKS ' . $kodeorg . '  ' . $thnbudget;
		$this->Ln();
		$this->SetFont('Arial', 'U', 10);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 9);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebar1 / 100) * $width, $height, $_SESSION['lang']['nourut'], 1, 0, 'C', 1);
		$this->Cell(($lebar2 / 100) * $width, $height, $_SESSION['lang']['noakun'], 1, 0, 'C', 1);
		$this->Cell(($lebar3 / 100) * $width, $height, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
		$this->Cell(($lebar4 / 100) * $width, $height, $_SESSION['lang']['jumlahrp'], 1, 0, 'C', 1);
		$this->Cell(($lebar5 / 100) * $width, $height, $_SESSION['lang']['rpperkg'], 1, 0, 'C', 1);
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == 1) {
				$ii = '0' . $i;
			}
			else {
				$ii = $i;
			}

			if ($i != 12) {
				$this->Cell(($lebar6 / 100) * $width, $height, $ii . '(Rp)', 1, 0, 'C', 1);
			}
			else {
				$this->Cell(($lebar6 / 100) * $width, $height, $ii . '(Rp)', 1, 1, 'C', 1);
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


$kgoil = $kgcpo + $kgkernel;
$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 7);
$adq = 'a.noakun, sum(a.rupiah) as rupiah,sum(a.rp01) as rp01,' . "\r\n" . '      sum(a.rp02) as rp02,sum(a.rp03) as rp03,' . "\r\n" . '      sum(a.rp04) as rp04,sum(a.rp05) as rp05,' . "\r\n" . '      sum(a.rp06) as rp06,sum(a.rp07) as rp07,' . "\r\n" . '      sum(a.rp08) as rp08,sum(a.rp09) as rp09,' . "\r\n" . '      sum(a.rp10) as rp10,sum(a.rp11) as rp11,' . "\r\n" . '      sum(a.rp12) as rp12';

if ($jenis == 'UMUM') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget=\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'MILL\'     ' . "\r\n" . '      group by a.noakun';
}
else if ($jenis == 'LANGSUNG') {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where a.kodebudget<>\'UMUM\' and tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'MILL\'      ' . "\r\n" . '      group by a.noakun';
}
else {
	$str = 'select ' . $adq . ',b.namaakun as namaakun from ' . $dbname . '.bgt_budget_detail a left join' . "\r\n" . '      ' . $dbname . '.keu_5akun b on a.noakun=b.noakun' . "\r\n" . '      where  tahunbudget=' . $thnbudget . ' and a.kodeorg like \'' . $kodeorg . '%\'' . "\r\n" . '      and tipebudget=\'MILL\'      ' . "\r\n" . '      group by a.noakun';
}

$res = mysql_query($str);
$no = 0;
$rpperha = 0;
$ttrp = 0;

while ($bar = mysql_fetch_object($res)) {
	@$rpperkg = $bar->rupiah / $kgoil;
	$no += 1;
	$pdf->Cell(($lebar1 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebar2 / 100) * $width, $height, $bar->noakun, 1, 0, 'C', 1);
	$pdf->Cell(($lebar3 / 100) * $width, $height, $bar->namaakun, 1, 0, 'L', 1);
	$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($bar->rupiah, 0, '.', ','), 1, 0, 'R', 1);
	$pdf->Cell(($lebar5 / 100) * $width, $height, number_format($rpperkg, 3, '.', ','), 1, 0, 'R', 1);
	$i = 1;

	while ($i <= 12) {
		if (strlen($i) == 1) {
			$ii = 'rp0' . $i;
		}
		else {
			$ii = 'rp' . $i;
		}

		if ($i != 12) {
			$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 0, 'R', 1);
		}
		else {
			$pdf->Cell(($lebar6 / 100) * $width, $height, number_format($bar->{$ii}, 0, '.', ','), 1, 1, 'R', 1);
		}

		++$i;
	}

	$tt01 += $bar->rp01;
	$tt02 += $bar->rp02;
	$tt03 += $bar->rp03;
	$tt04 += $bar->rp04;
	$tt05 += $bar->rp05;
	$tt06 += $bar->rp06;
	$tt07 += $bar->rp07;
	$tt08 += $bar->rp08;
	$tt09 += $bar->rp09;
	$tt10 += $bar->rp10;
	$tt11 += $bar->rp11;
	$tt12 += $bar->rp12;
	$ttrp += $bar->rupiah;
}

@$ttrpperproduksi = $ttrp / $kgoil;
$pdf->Cell((($lebar1 + $lebar2 + $lebar3) / 100) * $width, $height, 'Total', 1, 0, 'C', 1);
$pdf->Cell(($lebar4 / 100) * $width, $height, number_format($ttrp, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar5 / 100) * $width, $height, @number_format($ttrpperproduksi, 3, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt01, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt02, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt03, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt04, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt05, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt06, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt07, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt08, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt09, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt10, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt11, 0, '.', ','), 1, 0, 'R', 1);
$pdf->Cell(($lebar6 / 100) * $width, $height, @number_format($tt12, 0, '.', ','), 1, 1, 'R', 1);
$pdf->Output();

?>
