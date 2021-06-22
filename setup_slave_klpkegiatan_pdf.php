<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$order = 'noakun, kodeklp';
$query = selectQuery($dbname, $table, '*', $where, $order);
$result = fetchData($query);
$header = array();

foreach ($result[0] as $key => $row) {
	$header[] = $key;
}
class masterpdf extends FPDF
{
	public function Header()
	{
		global $table;
		global $header;
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, $height, $_SESSION['org']['namaorganisasi'], '', 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell($width, $height, strtoupper($_SESSION['lang']['daftarkelompokkegiatan']), '', 1, 'C');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(415, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell(40, $height, date('d-m-Y H:i'), '', 1, 'L');
		$this->Cell(415, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(8, $height, ':', '', 0, 'L');
		$this->Cell(15, $height, $this->PageNo(), '', 1, 'L');
		$this->Cell(415, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['user'], '', 0, 'L');
		$this->Cell(8, $height, ':', '', 0, 'L');
		$this->Cell(20, $height, $_SESSION['standard']['username'], '', 1, 'L');
		$this->Ln();
		$this->Cell(60, 1.5 * $height, $_SESSION['lang']['kode'], 'TBLR', 0, 'C');
		$this->Cell(400, 1.5 * $height, $_SESSION['lang']['namakelompokkegiatan'], 'TBLR', 0, 'C');
		$this->Cell(80, 1.5 * $height, $_SESSION['lang']['nomorperkiraan'], 'TBLR', 0, 'C');
		$this->Ln();
		$this->Ln();
	}
}


$pdf = new masterpdf('P', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->SetFont('Arial', '', 8);
$pdf->AddPage();

foreach ($result as $data) {
	$pdf->Cell(60, $height, $data['kodeklp'], '', 0, 'L');

	if ($_SESSION['language'] == 'EN') {
		$pdf->Cell(400, $height, $data['namakelompok1'], '', 0, 'L');
	}
	else {
		$pdf->Cell(400, $height, $data['namakelompok'], '', 0, 'L');
	}

	$pdf->Cell(80, $height, $data['noakun'], '', 0, 'L');
	$pdf->Ln();
}

$pdf->Output();

?>
