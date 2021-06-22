<?php


class PDF extends FPDF
{
	public function Header()
	{
		global $conn;
		global $dbname;
		global $align;
		global $length;
		global $colArr;
		global $title;
		global $periode;
		global $kdOrg;
		$sql = 'select * from ' . $dbname . '.' . $_GET['table'] . ' where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);
		$query = selectQuery($dbname, 'organisasi', 'alamat,telepon', 'kodeorganisasi=\'' . $res['kodeorg'] . '\'');
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
		$this->SetFont('Arial', 'B', 12);
		$this->Ln();
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['rencanaJual'], '', 0, 'L');
		$this->Ln();
		$this->SetFont('Arial', '', 8);
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['periode'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, $periode, '', 0, 'L');
		$this->Ln();
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, $kdOrg, '', 0, 'L');
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial', 'U', 12);
		$this->Cell($width, $height, strtoupper($_SESSION['lang']['rencanaJualdetail']), 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', 'B', 9);
		$this->SetFillColor(220, 220, 220);
		$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
		$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
		$this->Cell((25 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
		$this->Cell((25 / 100) * $width, $height, $_SESSION['lang']['kodecustomer'], 1, 0, 'C', 1);
		$this->Cell((20 / 100) * $width, $height, $_SESSION['lang']['almt_kirim'], 1, 0, 'C', 1);
		$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['volume'] . ' (Kg)', 1, 1, 'C', 1);
	}

	public function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$dt = explode(',', $column);
$periode = $dt[0];
$kdOrg = $dt[1];
$where = $_GET['cond'];
$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$sDet = 'select * from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\' order by `tanggal` desc';

#exit(mysql_error());
($qDet = mysql_query($sDet)) || true;

while ($rDet = mysql_fetch_assoc($qDet)) {
	$no += 1;
	$sBrg = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rDet['kodebarang'] . '\'';

	#exit(mysql_error($conn));
	($qBrg = mysql_query($sBrg)) || true;
	$rBrg = mysql_fetch_assoc($qBrg);
	$sCust = 'select namacustomer from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $rDet['pembeli'] . '\'';

	#exit(mysql_error());
	($qCust = mysql_query($sCust)) || true;
	$rCust = mysql_fetch_assoc($qCust);
	$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell((8 / 100) * $width, $height, tanggalnormal($rDet['tanggal']), 1, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $rBrg['namabarang'], 1, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $rCust['namacustomer'], 1, 0, 'L', 1);
	$pdf->Cell((20 / 100) * $width, $height, $rDet['lokasipengiriman'], 1, 0, 'L', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($rDet['jumlah'], 2), 1, 1, 'R', 1);
}

$pdf->Output();

?>
