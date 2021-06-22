<?php


include_once 'master_validation.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zLib.php';
$proses = $_GET['proses'];
$param = $_GET;
$where = 'nokonosemen=\'' . $param['nokonosemen'] . '\'';
$cols = 'kodept,kodebarang,jenis,jumlah,jumlahditerima,satuanpo,nopo,nopp';
$colArr = explode(',', $cols);
$query = selectQuery($dbname, 'log_konosemendt', $cols, $where, 'nokonosemen desc');
$data = fetchData($query);
$resData = $data;
$queryH = selectQuery($dbname, 'log_konosemenht', '*', $where, 'nokonosemen desc');
$dataH = fetchData($queryH);
$dataH = $dataH[0];
$qFranco = selectQuery($dbname, 'setup_franco', '*', 'id_franco=\'' . $dataH['franco'] . '\'');
$resFranco = fetchData($qFranco);
$franco = $resFranco[0];
$qShip = selectQuery($dbname, 'log_5supplier', '*', 'supplierid=\'' . $dataH['shipper'] . '\'');
$resShip = fetchData($qShip);
$shipper = $resShip[0];
$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$pdf = new FPDF('P', 'mm', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 5;
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell($width, 5, $optOrg[$dataH['kodept']], 0, 1, 'C');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell($width, 5, 'KONOSEMEN : ' . $param['nokonosemen'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell($width, 3, $franco['franco_name'], 0, 1, 'C');
$pdf->SetFont('Arial', '', 7);
$pdf->SetFillColor(255, 255, 255);
$pdf->Ln();
$height = 4;
$pdf->Cell((40 / 100) * $width, $height, 'Nama Kapal', 0, 0, 'L');
$pdf->Cell((60 / 100) * $width, $height, ': ' . $shipper['namasupplier'], 0, 0, 'L');
$pdf->Ln();
$pdf->Cell((40 / 100) * $width, $height, 'Tgl Berangkat', 0, 0, 'L');
$pdf->Cell((60 / 100) * $width, $height, ': ' . tanggalnormal($dataH['tanggalberangkat']), 0, 0, 'L');
$pdf->Ln();
$pdf->Cell((40 / 100) * $width, $height, 'Tujuan', 0, 0, 'L');
$pdf->Cell((60 / 100) * $width, $height, ': ' . $franco['franco_name'], 0, 0, 'L');
$pdf->Ln();
$pdf->Cell((40 / 100) * $width, $height, 'Asal Barang', 0, 0, 'L');
$pdf->Cell((60 / 100) * $width, $height, ': ' . $dataH['asalbarang'], 0, 0, 'L');
$pdf->Ln($height * 2);
$height = 5;
$yAkhir = $pdf->GetY();
$pdf->Line($pdf->lMargin, $yAkhir - 0.5, $width + $pdf->lMargin, $yAkhir - 0.69999999999999996);
$pdf->SetFillColor(220, 220, 220);
$pdf->Cell((5 / 100) * $width, $height, 'NO', 'TB', 0, 'C', 1);
$pdf->Cell((30 / 100) * $width, $height, 'MATERIAL NAME& SPECIFICATION', 'TB', 0, 'C', 1);
$pdf->Cell((5 / 100) * $width, $height, 'UNIT', 'TB', 0, 'C', 1);
$pdf->Cell((10 / 100) * $width, $height, 'DIKIRIM', 'TB', 0, 'C', 1);
$pdf->Cell((10 / 100) * $width, $height, 'DITERIMA', 'TB', 0, 'C', 1);
$pdf->Cell((20 / 100) * $width, $height, 'NO PO', 'TB', 0, 'C', 1);
$pdf->Cell((20 / 100) * $width, $height, 'NO PP', 'TB', 0, 'C', 1);
$pdf->Ln();
$pdf->SetFillColor(255, 255, 255);

foreach ($data as $key => $row) {
	$pdf->Cell((5 / 100) * $width, $height, $key + 1, 0, 0, 'R', 1);

	if (isset($optBarang[$row['kodebarang']])) {
		$pdf->Cell((30 / 100) * $width, $height, $optBarang[$row['kodebarang']], 0, 0, 'L', 1);
	}
	else {
		$pdf->Cell((30 / 100) * $width, $height, $row['kodebarang'], 0, 0, 'L', 1);
	}

	$pdf->Cell((5 / 100) * $width, $height, $row['satuanpo'], 0, 0, 'C', 1);
	$pdf->Cell((10 / 100) * $width, $height, $row['jumlah'], 0, 0, 'C', 1);
	$pdf->Cell((10 / 100) * $width, $height, $row['jumlahditerima'], 0, 0, 'C', 1);
	$pdf->Cell((20 / 100) * $width, $height, $row['nopo'], 0, 0, 'C', 1);
	$pdf->Cell((20 / 100) * $width, $height, $row['nopp'], 0, 0, 'C', 1);
	$pdf->Ln();
}

$yAkhir = $pdf->GetY();
$pdf->Line($pdf->lMargin, $yAkhir, $pdf->lMargin + $width, $yAkhir);
$pdf->Line($pdf->lMargin, $yAkhir + 0.5, $pdf->lMargin + $width, $yAkhir + 0.5);
$pdf->ln();
$pdf->Cell(15, $height, 'Dikirim', 0, 0, 'L');
$pdf->SetX(125);
$pdf->Cell(15, $height, 'Yang Menerima', 0, 0, 'L');
$pdf->Ln(10);
$pdf->Cell(15, $height, '', 0, 0, 'L');
$pdf->SetX(50);
$pdf->SetX(125);
$pdf->SetX(175);
$pdf->Cell(15, $height, "\t", 0, 1, 'L');

if (isset($optKary[$dataH['pengirim']])) {
	$pdf->Cell(15, $height, $optKary[$dataH['pengirim']], 0, 0, 'L');
}

$pdf->SetX(125);

if (isset($optKary[$dataH['penerima']])) {
	$pdf->Cell(15, $height, $optKary[$dataH['penerima']], 0, 0, 'L');
}

$pdf->Ln();
$yAkhir = $pdf->GetY();
$pdf->Line($pdf->lMargin, $yAkhir, $pdf->lMargin + $width, $yAkhir);
$pdf->Line($pdf->lMargin, $yAkhir + 0.29999999999999999, $pdf->lMargin + $width, $yAkhir + 0.29999999999999999);
$pdf->Output();

?>
