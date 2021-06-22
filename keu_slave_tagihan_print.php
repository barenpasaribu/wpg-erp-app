<?php


include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
include_once 'lib/devLibrary.php';
$proses = $_GET['proses'];
$param = $_POST;
$str = 'select karyawanid, namakaryawan from ' . $dbname . '.datakaryawan' . "\r\n" . '    where karyawanid in (select distinct updateby from ' . $dbname . '.keu_tagihanht)';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$nama[$bar->karyawanid] = $bar->namakaryawan;
}

$str = 'select periode, tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi where kodeorg=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tutupbuku = \'0\'';

#exit(mysql_error($conn));
($query = mysql_query($str)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$periodeaktif = $res['periode'];
	$periodemulai = $res['tanggalmulai'];
	$periodesampai = $res['tanggalsampai'];
}

$where = 'tanggal >= \'' . $periodemulai . '\' and tanggal <= \'' . $periodesampai . '\'';
$cols = 'noinvoice,kodeorg,tanggal,updateby,nopo,keterangan,nilaiinvoice';
$colArr = explode(',', $cols);
$order = 'tanggal desc';
$query = selectQuery($dbname, 'keu_tagihanht', $cols, $where, $order);
$data = fetchData($query);

foreach ($data as $key => $row) {
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);
	$data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'], 2);
	$data[$key]['updateby'] = $nama[$row['updateby']];
}

$title = 'INVOICE IN';
$align = explode(',', 'L,L,L,L,L,L,R');
$length = explode(',', '15,10,8,14,18,18,12');

switch ($proses) {
case 'pdf':
	$pdf = new zPdfMaster('L', 'pt', 'A4');
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
