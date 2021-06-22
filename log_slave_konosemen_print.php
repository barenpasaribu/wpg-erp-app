<?php


include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_POST;
$where = NULL;
$cols = 'nokonosemen,kodept,tanggal,tanggalberangkat';
$colArr = explode(',', $cols);
$query = selectQuery($dbname, 'log_konosemenht', $cols, $where, 'nokonosemen desc');
$data = fetchData($query);
$title = $_SESSION['lang']['konosemen'];
$align = explode(',', 'L,L,L,L,L');
$length = explode(',', '20,20,20,20,20');

switch ($proses) {
case 'pdf':
	$pdf = new zPdfMaster('P', 'pt', 'A4');
	$pdf->setAttr1($title, $align, $length, $colArr);
	$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
	$height = 15;
	$pdf->AddPage();
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetFont('Arial', '', 9);

	foreach ($data as $key => $row) {
		$i = 0;

		foreach ($row as $cont) {
			$pdf->Cell(($length[$i] / 100) * $width, $height, $cont, 1, 0, $align[$i], 1);
			++$i;
		}

		$pdf->Ln();
	}

	$pdf->Output();
	break;

case 'excel':
	break;
}

?>
