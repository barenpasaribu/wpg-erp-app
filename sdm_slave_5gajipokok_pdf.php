<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/biReport.php';
include_once 'lib/zPdfMaster.php';
include_once 'lib/terbilang.php';
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "lokasitugas='".$_SESSION['empl']['lokasitugas']."' and tipekaryawan in(1,2,3,4,5)");
$optComp = makeOption($dbname, 'sdm_ho_component', 'id,name', "type='basic'");
$cols = 'tahun,karyawanid,idkomponen,jumlah';
$where = $_GET['cond'];
$query = selectQuery($dbname, 'sdm_5gajipokok', $cols, $where);
$data = fetchData($query);
$dataShow = $data;
foreach ($dataShow as $key => $row) {
    $dataShow[$key]['karyawanid'] = $optKary[$row['karyawanid']];
    $dataShow[$key]['idkomponen'] = $optComp[$row['idkomponen']];
}
$title = $_SESSION['lang']['gajipokok'];
$colArr = explode(',', $cols);
$align = explode(',', 'L,L,L,R');
$length = explode(',', '10,30,30,20');
$pdf = new zPdfMaster('P', 'pt', 'A4');
$pdf->_noThead = true;
$pdf->setAttr1($title, $align, $length, []);
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetFillColor(220, 220, 220);
$i = 0;
foreach ($colArr as $column) {
    $pdf->Cell($length[$i] / 100 * $width, $height, $_SESSION['lang'][$column], 1, 0, 'C', 1);
    ++$i;
}
$pdf->Ln();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
foreach ($dataShow as $key => $row) {
    $i = 0;
    foreach ($row as $cont) {
        $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
        ++$i;
    }
    $pdf->Ln();
}
$pdf->Ln();
$pdf->Output();

?>