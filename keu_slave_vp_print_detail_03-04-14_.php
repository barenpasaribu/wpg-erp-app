<?php


include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/fpdf.php';
include_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$param = $_GET;
$cols = array();
$whereH = 'novp=\'' . $param['novp'] . '\'';
$queryH = selectQuery($dbname, 'keu_vpht', '*', $whereH);
$resH = fetchData($queryH);
$dataH = $resH[0];
$tipe = substr($resH[0]['nopo'], 0, 2);
$col1 = 'noakun,jumlah';
$cols = array('noakun', 'jumlah');
$where = 'novp=\'' . $param['novp'] . '\'';
$query = selectQuery($dbname, 'keu_vpdt', $col1, $where);
$resD = fetchData($query);
$dataD = array(
	'debet'  => array(),
	'kredit' => array()
	);

if (empty($resD)) {
	exit('Data Empty');
}

$total = 0;
$totalReal = 0;

foreach ($resD as $row) {
	$totalReal += $row['jumlah'];

	if (0 <= $row['jumlah']) {
		$dataD['debet'][] = $row;
		$total += $row['jumlah'];
	}
	else {
		$dataD['kredit'][] = $row;
	}
}

if ($totalReal != 0) {
	exit('Data Detail belum balance');
}

switch ($tipe) {
case 'SJ':
	$qSupp = 'select a.expeditor,b.namasupplier from ' . $dbname . '.log_suratjalanht a' . "\r\n\t\t\t" . 'left join ' . $dbname . '.log_5supplier b' . "\r\n\t\t\t" . 'on a.expeditor = b.supplierid' . "\r\n\t\t\t" . 'where a.nosj = \'' . $resH[0]['nopo'] . '\'';
	break;

case 'KS':
	$qSupp = 'select a.shipper,b.namasupplier from ' . $dbname . '.log_konosemenht a' . "\r\n\t\t\t" . 'left join ' . $dbname . '.log_5supplier b' . "\r\n\t\t\t" . 'on a.shipper = b.supplierid' . "\r\n\t\t\t" . 'where a.nokonosemen = \'' . $resH[0]['nopo'] . '\'';
	break;
}

$tmp = (, $resH[0]['nopo']);

if (1 < count($tmp)) {
	$qSupp = 'select a.kodesupplier,b.namasupplier from ' . $dbname . '.log_poht a' . "\r\n\t\t\t\t" . 'left join ' . $dbname . '.log_5supplier b' . "\r\n\t\t\t\t" . 'on a.kodesupplier = b.supplierid' . "\r\n\t\t\t\t" . 'where a.nopo = \'' . $resH[0]['nopo'] . '\'';
}
else {
	$qSupp = 'select a.koderekanan,b.namasupplier from ' . $dbname . '.log_spkht a' . "\r\n\t\t\t\t" . 'left join ' . $dbname . '.log_5supplier b' . "\r\n\t\t\t\t" . 'on a.koderekanan = b.supplierid' . "\r\n\t\t\t\t" . 'where a.notransaksi = \'' . $resH[0]['nopo'] . '\'';
}

$resSupp = fetchData($qSupp);
$pdf = new fpdf('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell((60 / 100) * $width, $height, $_SESSION['org']['namaorganisasi'], 0, 0, 'L', 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell((20 / 100) * $width, $height, $_SESSION['lang']['novp'], 0, 0, 'R', 1);
$pdf->Cell((20 / 100) * $width, $height, ': ' . $dataH['novp'], 0, 0, 'L', 1);
$pdf->Ln();
$pdf->Cell((80 / 100) * $width, $height, $_SESSION['lang']['tanggal'], 0, 0, 'R', 1);
$pdf->Cell((20 / 100) * $width, $height, ': ' . tanggalnormal($dataH['tanggal']), 0, 0, 'L', 1);
$pdf->Ln(30);
$pdf->Cell((40 / 100) * $width, $height, 'C R E D I T O R', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . $resSupp[0]['namasupplier'], 0, 1, 'L', 1);
$pdf->Cell((40 / 100) * $width, $height, 'PURCHASED ORDER NO', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . $dataH['nopo'], 0, 1, 'L', 1);
$pdf->Cell((40 / 100) * $width, $height, 'DATE OF INVOICE RECIEVED', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . tanggalnormal($dataH['tanggalterima']), 0, 1, 'L', 1);
$pdf->Cell((40 / 100) * $width, $height, 'DATE OF PAYMENT', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . tanggalnormal($dataH['tanggalbayar']), 0, 1, 'L', 1);
$pdf->Cell((40 / 100) * $width, $height, 'EXPLANATION', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . $dataH['penjelasan'], 0, 1, 'L', 1);
$pdf->Ln();
$wrmt = 'nopo=\'' . $dataH['nopo'] . '\'';
$mtUang = makeOption($dbname, 'log_poht', 'nopo,matauang', $wrmt);
$keNm = makeOption($dbname, 'setup_matauang', 'kode,matauang');
$keSimbol = makeOption($dbname, 'setup_matauang', 'kode,simbol');
$mataUang = $keNm[$mtUang[$dataH['nopo']]];
$simbolmataUang = $keSimbol[$mtUang[$dataH['nopo']]];
if (($mataUang != '') || ($mataUang != NULL)) {
	$mataUang = $mataUang;
	$simbolmataUang = $simbolmataUang;
}
else {
	$mataUang = 'Rupiah';
	$simbolmataUang = 'Rp';
}

$pdf->Cell((40 / 100) * $width, $height, 'TOTAL AMOUNT (' . $mataUang . ')', 0, 0, 'L', 1);
$pdf->Cell((60 / 100) * $width, $height, ': ' . $simbolmataUang . ' ' . number_format($total, 0), 0, 1, 'L', 1);
$pdf->Ln(30);
$height = 11;
$pdf->Cell((60 / 100) * $width, $height, 'VOUCHER PAYABLE SYSTEM', 0, 1, 'C', 1);
$pdf->Cell((60 / 100) * $width, $height, '', 0, 0, 'C', 1);
$pdf->Cell((40 / 100) * $width, $height, 'PREPARED BY :', 0, 1, 'L', 1);
$pdf->Cell((30 / 100) * $width, $height, 'Account Code', 0, 0, 'L', 1);
$pdf->Cell((28 / 100) * $width, $height, 'Amount', 0, 1, 'R', 1);
$pdf->Cell((60 / 100) * $width, $height, '', 0, 0, 'C', 1);
$pdf->Cell((40 / 100) * $width, $height, 'VERIFIED BY :', 0, 1, 'L', 1);
$pdf->Cell((30 / 100) * $width, $height, $dataD['debet'][0]['noakun'], 0, 0, 'L', 1);
$pdf->Cell((28 / 100) * $width, $height, number_format($dataD['debet'][0]['jumlah'], 0), 0, 1, 'R', 1);

if (isset($dataD['debet'][1])) {
	$onDebet = true;
	$pdf->Cell((30 / 100) * $width, $height, $dataD['debet'][1]['noakun'], 0, 0, 'L', 1);
	$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['debet'][1]['jumlah']), 0), 0, 0, 'R', 1);
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 0, 'L', 1);
}
else {
	$onDebet = false;
	$currCredit = 0;
	$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][0]['noakun'], 0, 0, 'L', 1);
	$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][0]['jumlah']), 0), 0, 0, 'R', 1);
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 0, 'L', 1);
}

$pdf->Cell((40 / 100) * $width, $height, 'APPROVED BY :', 0, 1, 'L', 1);

if ($onDebet) {
	if (isset($dataD['debet'][2])) {
		$onDebet = true;
		$pdf->Cell((30 / 100) * $width, $height, $dataD['debet'][2]['noakun'], 0, 0, 'L', 1);
		$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['debet'][2]['jumlah']), 0), 0, 0, 'R', 1);
		$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
	}
	else {
		$onDebet = false;
		$currCredit = 0;
		$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
		$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][0]['noakun'], 0, 0, 'L', 1);
		$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][0]['jumlah']), 0), 0, 0, 'R', 1);
		$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
	}
}
else if (isset($dataD['kredit'][1])) {
	++$currCredit;
	$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][1]['noakun'], 0, 0, 'L', 1);
	$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][1]['jumlah']), 0), 0, 0, 'R', 1);
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
}
else {
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
}

if ($onDebet) {
	if (isset($dataD['debet'][3])) {
		$onDebet = true;
		$pdf->Cell((30 / 100) * $width, $height, $dataD['debet'][3]['noakun'], 0, 0, 'L', 1);
		$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['debet'][3]['jumlah']), 0), 0, 0, 'R', 1);
		$pdf->Cell((2 / 100) * $width, $height, '', 0, 0, 'L', 1);
	}
	else {
		$onDebet = false;
		$currCredit = 0;
		$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
		$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][0]['noakun'], 0, 0, 'L', 1);
		$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][0]['jumlah']), 0), 0, 0, 'R', 1);
		$pdf->Cell((2 / 100) * $width, $height, '', 0, 0, 'L', 1);
	}
}
else if (isset($dataD['kredit'][$currCredit + 1])) {
	++$currCredit;
	$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][$currCredit]['noakun'], 0, 0, 'L', 1);
	$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][$currCredit]['jumlah']), 0), 0, 0, 'R', 1);
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 0, 'L', 1);
}
else {
	$pdf->Cell((60 / 100) * $width, $height, '', 0, 0, 'C', 1);
}

$pdf->Cell((40 / 100) * $width, $height, 'POSTED BY :', 0, 1, 'L', 1);

if ($onDebet) {
	if (isset($dataD['debet'][4])) {
		$tmpDebet = count($dataD['debet']);
		$i = 4;

		while ($i < $tmpDebet) {
			$pdf->Cell((30 / 100) * $width, $height, $dataD['debet'][$i]['noakun'], 0, 0, 'L', 1);
			$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['debet'][$i]['jumlah']), 0), 0, 0, 'R', 1);
			$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
			++$i;
		}
	}

	$currCredit = 0;
}
else {
	++$currCredit;
}

$tmpKredit = count($dataD['kredit']);
$i = $currCredit;

while ($i < $tmpKredit) {
	$pdf->Cell((5 / 100) * $width, $height, '', 0, 0, 'L', 1);
	$pdf->Cell((25 / 100) * $width, $height, $dataD['kredit'][$i]['noakun'], 0, 0, 'L', 1);
	$pdf->Cell((28 / 100) * $width, $height, number_format(abs($dataD['kredit'][$i]['jumlah']), 0), 0, 0, 'R', 1);
	$pdf->Cell((2 / 100) * $width, $height, '', 0, 1, 'L', 1);
	++$i;
}

$totalRow = count($resD);

if (5 < $totalRow) {
	$addHeight = ($totalRow - 5) * 11;
}
else {
	$addHeight = 0;
}

$pdf->Rect($pdf->lMargin - 10, $pdf->tMargin - 10, $width + 20, 300 + $addHeight);
$pdf->Rect($pdf->lMargin - 7, $pdf->tMargin - 7, $width + 14, 180);
$pdf->Rect($pdf->lMargin - 7, ($pdf->tMargin - 7) + 183, (60 / 100) * $width, 17);
$pdf->Rect($pdf->lMargin - 7, ($pdf->tMargin - 7) + 203, (30 / 100) * $width, 20);
$pdf->Rect(($pdf->lMargin - 4) + ((30 / 100) * $width), ($pdf->tMargin - 7) + 203, ((30 / 100) * $width) - 3, 20);
$pdf->Rect($pdf->lMargin - 7, ($pdf->tMargin - 7) + 226, (30 / 100) * $width, 67 + $addHeight);
$pdf->Rect(($pdf->lMargin - 4) + ((30 / 100) * $width), ($pdf->tMargin - 7) + 226, ((30 / 100) * $width) - 3, 67 + $addHeight);
$pdf->Rect(($pdf->lMargin - 7) + ((60 / 100) * $width), ($pdf->tMargin - 7) + 183, ((40 / 100) * $width) + 14, 110 + $addHeight);
$pdf->Output();

?>
