<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_POST;
$query = 'select b.namakaryawan,a.periodegaji,a.masakerja,a.total from '.$dbname.'.sdm_pesangonht a'.' left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid';
$data = fetchData($query);
$title = $_SESSION['lang']['pesangon'];
$align = explode(',', 'L,L,C,R');
$length = explode(',', '25,20,15,35');
switch ($proses) {
    case 'pdf':
        $colArr = [$_SESSION['lang']['namakaryawan'], $_SESSION['lang']['periodegaji'], $_SESSION['lang']['masakerja'], $_SESSION['lang']['total']];
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->setAttr1($title, $align, $length, $colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        foreach ($data as $key => $row) {
            $i = 0;
            foreach ($row as $attr => $cont) {
                if ('total' == $attr) {
                    $cont = number_format($cont, 2);
                }

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