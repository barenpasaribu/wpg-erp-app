<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_POST;
$cols = 'notransaksi,tanggal,divisi,koderekanan';
$cols2 = 'notransaksi,tanggal,afdeling,koderekanan';
if ('TRAKSI' === $_SESSION['empl']['tipelokasitugas'] || 'HOLDING' === $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' === $_SESSION['empl']['tipelokasitugas']) {
    // $where = 'length(kodeorg)=4';
    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
} else {
    $where = "kodeorg='".$_SESSION['empl']['lokasitugas']."'";
}

$colArr = explode(',', $cols2);
// $query = selectQuery($dbname, 'log_spkht', $cols, $where);
$query = "SELECT a.notransaksi, a.tanggal, a.divisi, b.namasupplier AS koderekanan from `wpg_erp_trial`.`log_spkht` a
JOIN log_5supplier b
ON a.koderekanan = b.supplierid
where ".$where."";

$data = fetchData($query);
$title = $_SESSION['lang']['spk'];
$align = explode(',', 'L,L,L,L');
$length = explode(',', '5,28,12,15,40');
switch ($proses) {
    case 'pdf':
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($length[0] / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'No Transaksi', 1, 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Tanggal', 1, 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Sub Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Nama Supplier', 1, 0, 'C', 1);
        $pdf->Ln();
        $j = 1;
        foreach ($data as $key => $row) {
            $i = 1;
            $pdf->Cell($length[0] / 100 * $width, $height, $j, 1, 0, $align[$i], 1);
            $j++; 
            foreach ($row as $cont) {
                $pdf->Cell($length[$i] / 100 * $width, $height, $cont, 1, 0, $align[$i], 1);
                ++$i;
            }
            $pdf->Ln();
        }
        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}

?>