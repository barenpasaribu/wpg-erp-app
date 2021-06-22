<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
require_once 'lib/eagrolib.php';
$tab = $_GET['tab'];
$tahunbudget0 = $_GET['tahunbudget0'];
$regional0 = $_GET['regional0'];
$kelompokbarang0 = $_GET['kelompokbarang0'];
$str = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang' . "\r\n" . '    ';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namabarang[$bar->kodebarang] = $bar->namabarang;
	$satuanbarang[$bar->kodebarang] = $bar->satuan;
}

if ($tahunbudget0 == '') {
	echo 'WARNING: silakan mengisi tahunbudget.';
	exit();
}

if ($regional0 == '') {
	echo 'WARNING: silakan mengisi regional.';
	exit();
}

$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '                    order by kode ' . "\r\n" . '                    ';
$artikelompok[''] = $_SESSION['lang']['all'];
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$artikelompok[$bar->kode] = $bar->kelompok;
}
class PDF extends FPDF
{
	public function Header()
	{
		global $regional0;
		global $tahunbudget0;
		global $kelompokbarang0;
		global $artikelompok;
		global $dbname;
		global $isidata;
		global $lebarno;
		global $lebarkode;
		global $lebarnama;
		global $lebarsatuan;
		global $lebarharga;
		global $lebarlalu;
		global $tab;
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
		$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['budgetyear'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((57 / 100) * $width, $height, $tahunbudget0, '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, 'Printed By', '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['empl']['name'], '', 1, 'L');
		$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['regional'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((57 / 100) * $width, $height, $regional0, '', 0, 'L');
		$this->Cell(((10 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 1, 'L');

		if ($tab == '1') {
			$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['kelompokbarang'], '', 0, 'L');
		}

		if ($tab == '2') {
			$this->Cell(((15 / 100) * $width) - 5, $height, $_SESSION['lang']['caribarang'], '', 0, 'L');
		}

		$this->Cell(5, $height, ':', '', 0, 'L');

		if ($tab == '1') {
			$this->Cell((60 / 100) * $width, $height, trim($kelompokbarang0 . ' ' . $artikelompok[$kelompokbarang0]), '', 1, 'L');
		}

		if ($tab == '2') {
			$this->Cell((60 / 100) * $width, $height, trim($kelompokbarang0), '', 1, 'L');
		}

		if ($tab == '1') {
			$title = 'Laporan Harga Barang per ' . $_SESSION['lang']['kelompokbarang'];
		}

		if ($tab == '2') {
			$title = 'Laporan Harga Barang per ' . $_SESSION['lang']['caribarang'];
		}

		$this->Ln();
		$this->SetFont('Arial', 'U', 10);
		$this->Cell($width, $height, $title, 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', '', 9);
		$this->SetFillColor(220, 220, 220);
		$this->Cell(($lebarno / 100) * $width, $height, substr($_SESSION['lang']['nomor'], 0, 2), 1, 0, 'C', 1);
		$this->Cell(($lebarkode / 100) * $width, $height, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);
		$this->Cell(($lebarnama / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
		$this->Cell(($lebarsatuan / 100) * $width, $height, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
		$this->Cell(($lebarharga / 100) * $width, $height, $_SESSION['lang']['hargabudget'], 1, 0, 'C', 1);
		$this->Cell(($lebarlalu / 100) * $width, $height, $_SESSION['lang']['hargatahunlalu'], 1, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}


$lebarno = 8;
$lebarkode = 13;
$lebarnama = 39;
$lebarsatuan = 10;
$lebarharga = 15;
$lebarlalu = 15;
$pdf = new PDF('P', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);

if ($tab == '1') {
	$str = 'select * from ' . $dbname . '.bgt_masterbarang' . "\r\n" . '        where closed = 1 and tahunbudget = \'' . $tahunbudget0 . '\' and regional = \'' . $regional0 . '\' and kodebarang like \'' . $kelompokbarang0 . '%\'';
}

if ($tab == '2') {
	$str = 'select a.* from ' . $dbname . '.bgt_masterbarang a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '        where a.closed = 1 and a.tahunbudget = \'' . $tahunbudget0 . '\' and a.regional = \'' . $regional0 . '\' and b.namabarang like \'%' . $kelompokbarang0 . '%\'';
}

$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	$pdf->Cell(($lebarno / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell(($lebarkode / 100) * $width, $height, $bar->kodebarang, 1, 0, 'C', 1);
	$pdf->Cell(($lebarnama / 100) * $width, $height, $namabarang[$bar->kodebarang], 1, 0, 'L', 1);
	$pdf->Cell(($lebarsatuan / 100) * $width, $height, $satuanbarang[$bar->kodebarang], 1, 0, 'L', 1);
	$pdf->Cell(($lebarharga / 100) * $width, $height, number_format($bar->hargasatuan, 2), 1, 0, 'R', 1);
	$pdf->Cell(($lebarlalu / 100) * $width, $height, number_format($bar->hargalalu, 2), 1, 1, 'R', 1);
}

if ($no == 0) {
	echo 'Data tidak ada atau belum ditutup.';
	exit();
}

$pdf->Output();

?>
