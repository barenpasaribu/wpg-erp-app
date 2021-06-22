<?php


class PDF extends FPDF
{
	public function Header()
	{
		global $tahun;
		global $departemen;
		global $dbname;
		$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $_SESSION['org']['kodeorganisasi'] . '\'');
		$orgData = fetchData($query);
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 5;

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

		$this->Image($path, 1, 1, 50);
		$this->SetFont('Arial', 'B', 9);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(50);
		$this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
		$this->SetX(50);
		$this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
		$this->SetX(50);
		$this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
		$this->Line($this->lMargin, $this->tMargin + ($height * 4), $this->lMargin + $width, $this->tMargin + ($height * 4));
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(290, 3, strtoupper($_SESSION['lang']['budgetdepartemen']), 0, 1, 'C');
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial', '', 9);
		$this->Cell(220, 3, ' ', '', 0, 'R');
		$this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(2, 3, ':', '', 0, 'L');
		$this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
		$this->Cell(220, 3, $_SESSION['lang']['departemen'] . ' : ' . $departemen, '', 0, 'L');
		$this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(2, 3, ':', '', 0, 'L');
		$this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
		$this->Cell(220, 3, $_SESSION['lang']['tahun'] . ' : ' . $tahun, '', 0, 'L');
		$this->Cell(15, 3, $_SESSION['lang']['user'], '', 0, 'L');
		$this->Cell(2, 3, ': ', '', 0, 'L');
		$this->Cell(35, 3, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial', '', 7);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(10, 10, substr($_SESSION['lang']['nomor'], 0, 2), 1, 0, 'C', 1);
		$this->Cell(35, 10, $_SESSION['lang']['namaakun'], 1, 0, 'C', 1);
		$this->Cell(25, 10, $_SESSION['lang']['keterangan'], 1, 0, 'C', 1);
		$this->Cell(15, 10, $_SESSION['lang']['alokasibiaya'], 1, 0, 'C', 1);
		$this->Cell(15, 10, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
		$this->Cell(180, 5, $_SESSION['lang']['distribusi'], 1, 1, 'C', 1);
		$this->Cell(100, 5, '', 0, 0, 'C');
		$this->Cell(15, 5, substr($_SESSION['lang']['jan'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['peb'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['mar'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['apr'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['mei'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['jun'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['jul'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['agt'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['sep'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['okt'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['nov'], 0, 3), 1, 0, 'C', 1);
		$this->Cell(15, 5, substr($_SESSION['lang']['dec'], 0, 3), 1, 1, 'C', 1);
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
$tahun = $_GET['tahun'];
$departemen = $_GET['departemen'];
$qwe = explode('-', $tanggal1);
$periode = $qwe[2] . $qwe[1];
$bulan = $qwe[1];
$qwe = explode('-', $tanggal1);
$tanggal1 = $qwe[2] . '-' . $qwe[1] . '-' . $qwe[0];
$qwe = explode('-', $tanggal2);
$tanggal2 = $qwe[2] . '-' . $qwe[1] . '-' . $qwe[0];
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$str = 'select noakun,namaakun from ' . $dbname . '.keu_5akun' . "\r\n" . '                    where detail=1  order by noakun' . "\r\n" . '                    ';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$noakun[$bar->noakun] = $bar->namaakun;
}

$str = 'select * from ' . $dbname . '.bgt_dept where departemen = \'' . $departemen . '\' and tahunbudget = \'' . $tahun . '\' order by noakun, alokasibiaya';
$no = 0;
$jumlahan = $d01an = $d02an = $d03an = $d04an = $d05an = $d06an = $d07an = $d08an = $d09an = $d10an = $d11an = $d12an = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$pdf->Cell(10, 5, $no, 1, 0, 'C');
	$pdf->Cell(35, 5, $noakun[$bar->noakun], 1, 0, 'L');
	$pdf->Cell(25, 5, $bar->keterangan, 1, 0, 'L');
	$pdf->Cell(15, 5, $bar->alokasibiaya, 1, 0, 'L');
	$pdf->Cell(15, 5, number_format($bar->jumlah), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d01), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d02), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d03), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d04), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d05), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d06), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d07), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d08), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d09), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d10), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d11), 1, 0, 'R');
	$pdf->Cell(15, 5, number_format($bar->d12), 1, 1, 'R');
	$jumlahan += $bar->jumlah;
	$d01an += $bar->d01;
	$d02an += $bar->d02;
	$d03an += $bar->d03;
	$d04an += $bar->d04;
	$d05an += $bar->d05;
	$d06an += $bar->d06;
	$d07an += $bar->d07;
	$d08an += $bar->d08;
	$d09an += $bar->d09;
	$d10an += $bar->d10;
	$d11an += $bar->d11;
	$d12an += $bar->d12;
}

$pdf->SetFillColor(220, 220, 220);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(85, 5, $_SESSION['lang']['total'], 1, 0, 'C', 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(15, 5, number_format($jumlahan), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d01an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d02an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d03an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d04an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d05an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d06an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d07an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d08an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d09an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d10an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d11an), 1, 0, 'R', 1);
$pdf->Cell(15, 5, number_format($d12an), 1, 1, 'R', 1);
$pdf->Output();

?>
