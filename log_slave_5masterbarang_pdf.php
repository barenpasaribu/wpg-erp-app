<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['kolom'];
$where = str_replace("\\\'", "\\'", $_GET['kondisi']);
$klbarang = $_GET['klbarang'];
$where1 = 'kode =' . $klbarang;
$query1 = selectQuery($dbname, 'log_5klbarang', 'kelompok', $where1);
$nmklbarang = fetchData($query1);
$query = selectQuery($dbname, $table, $column, $where);
$result = fetchData($query);

foreach ($nmklbarang as $data) {
	$nama = $data['kelompok'];
}

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
		global $where;
		global $nama;
		$width = $this->w - $this->lMargin - $this->rMargin;
		$height = 15;
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(20, $height, $_SESSION['org']['namaorganisasi'], '', 1, 'L');
		$this->SetFont('Arial', 'B', 12);
		$this->Cell($width, $height, strtoupper($_SESSION['lang']['daftarbarang']), '', 1, 'C');
		$this->SetFont('Arial', 'B', 8);
		$this->Cell(415, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(5, $height, ':', '', 0, 'L');
		$this->Cell(40, $height, date('d-m-Y H:i'), '', 1, 'L');
		$this->Cell(415, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(8, $height, ':', '', 0, 'L');
		$this->Cell(15, $height, $this->PageNo(), '', 1, 'L');
		$this->Cell(100, $height, $nama, '', 0, 'L');
		$this->Cell(315, $height, ' ', '', 0, 'R');
		$this->Cell(40, $height, $_SESSION['lang']['user'], '', 0, 'L');
		$this->Cell(8, $height, ':', '', 0, 'L');
		$this->Cell(20, $height, $_SESSION['standard']['username'], '', 1, 'L');
		$this->Ln();
		$this->Cell(60, 1.5 * $height, $_SESSION['lang']['kode'], 'TBLR', 0, 'C');
		$this->Cell(400, 1.5 * $height, $_SESSION['lang']['namabarang'], 'TBLR', 0, 'C');
		$this->Cell(80, 1.5 * $height, $_SESSION['lang']['satuan'], 'TBLR', 0, 'C');
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
	$pdf->Cell(60, $height, $data['kodebarang'], '', 0, 'L');
	$pdf->Cell(400, $height, $data['namabarang'], '', 0, 'L');
	$pdf->Cell(80, $height, $data['satuan'], '', 0, 'L');
	$pdf->Ln();
}

$pdf->Output();

?>
