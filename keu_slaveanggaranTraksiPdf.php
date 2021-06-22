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
		global $thnAnggaran;
		global $kdVhc;
		global $kdOrg;
		global $tkdOperasi;
		global $jmlhHariOperasi;
		global $meter;
		$sql = 'select * from ' . $dbname . '.' . $_GET['table'] . ' where kodevhc=\'' . $kdVhc . '\' and orgdata=\'' . $kdOrg . '\' and tahun=\'' . $thnAnggaran . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_assoc($query);
		$tkdOperasi = $res['jlhharitdkoperasi'];
		$jmlhHariOperasi = $res['jlhharioperasi'];
		$meter = $res['merterperhari'];
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
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['anggaranTraksi'], '', 0, 'L');
		$this->Ln();
		$this->SetFont('Arial', '', 8);
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['tahunanggaran'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, $kdVhc, '', 0, 'L');
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['jmlhHariOperasi'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $jmlhHariOperasi, 0, 0, 'L');
		$this->Ln();
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['pemakaianHmKm'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, $meter, '', 0, 'L');
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['jmlhHariTdkOpr'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $tkdOperasi, 0, 0, 'L');
		$this->Ln();
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, date('d-m-Y H:i:s'), '', 0, 'L');
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['user'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((15 / 100) * $width, $height, $_SESSION['standard']['username'], 0, 0, 'L');
		$this->Ln();
		$this->Cell(((20 / 100) * $width) - 5, $height, $_SESSION['lang']['kodeorg'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell((45 / 100) * $width, $height, $kdOrg, '', 0, 'L');
		$this->Ln();
		$this->Ln();
		$this->SetFont('Arial', 'U', 12);
		$this->Cell($width, $height, $_SESSION['lang']['anggaranTraksiDetail'], 0, 1, 'C');
		$this->Ln();
		$this->SetFont('Arial', 'B', 9);
		$this->SetFillColor(220, 220, 220);
		$this->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
		$this->Cell((25 / 100) * $width, $height, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
		$this->Cell((8 / 100) * $width, $height, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
		$this->Cell((10 / 100) * $width, $height, $_SESSION['lang']['grnd_total'], 1, 1, 'C', 1);
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
$thnAnggaran = $dt[0];
$kdVhc = $dt[1];
$kdOrg = $dt[2];
$where = $_GET['cond'];
$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$sDet = 'select * from ' . $dbname . '.keu_anggaranvhcdt where tahun=\'' . $thnAnggaran . '\' and kodevhc=\'' . $kdVhc . '\'';

#exit(mysql_error());
($qDet = mysql_query($sDet)) || true;

while ($rDet = mysql_fetch_assoc($qDet)) {
	$no += 1;
	$sCust = 'select namabarang,satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $rDet['kodebarang'] . '\'';

	#exit(mysql_error($conn));
	($qCust = mysql_query($sCust)) || true;
	$rCust = mysql_fetch_assoc($qCust);
	$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell((25 / 100) * $width, $height, $rCust['namabarang'], 1, 0, 'L', 1);
	$pdf->Cell((8 / 100) * $width, $height, number_format($rDet['jumlah'], 2) . ' ' . $rCust['satuan'], 1, 0, 'R', 1);
	$pdf->Cell((10 / 100) * $width, $height, number_format($rDet['hargatotal'], 2), 1, 1, 'R', 1);
}

$pdf->Ln();
$pdf->SetFont('Arial', 'U', 12);
$pdf->Cell($width, $height, $_SESSION['lang']['anggaranTraksiAlokasi'], 0, 1, 'C');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((3 / 100) * $width, $height, 'No', 1, 0, 'C', 1);
$pdf->Cell((12 / 100) * $width, $height, $_SESSION['lang']['kodeorg'], 1, 0, 'C', 1);
$pdf->Cell((15 / 100) * $width, $height, $_SESSION['lang']['jmlhMeter'], 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'Jan', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'FEB', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'MAR', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'APR', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'MEI', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'JUN', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'JUL', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'AGU', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'SEP', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'OKT', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'NOV', 1, 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'Des', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);
$sDetBuged = 'select * from ' . $dbname . '.keu_anggaranalokasivhc where tahun=\'' . $thnAnggaran . '\' and kodevhc=\'' . $kdVhc . '\'';

#exit(mysql_error());
($qDetBudged = mysql_query($sDetBuged)) || true;

while ($rDetBugdeg = mysql_fetch_assoc($qDetBudged)) {
	$no += 1;
	$pdf->Cell((3 / 100) * $width, $height, $no, 1, 0, 'C', 1);
	$pdf->Cell((12 / 100) * $width, $height, $rDetBugdeg['kodeorg'], 1, 0, 'C', 1);
	$pdf->Cell((15 / 100) * $width, $height, $rDetBugdeg['jlhmeter'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['jan'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['feb'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['mar'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['apr'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['mei'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['jun'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['jul'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['agu'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['sep'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['okt'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['nov'], 1, 0, 'C', 1);
	$pdf->Cell((5 / 100) * $width, $height, $rDetBugdeg['des'], 1, 1, 'C', 1);
}

$pdf->Output();

?>
