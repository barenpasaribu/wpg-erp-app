<?php



include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_POST;
$str = 'select periode, tanggalmulai, tanggalsampai from '.$dbname.".setup_periodeakuntansi where kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku = '0'";
$query = mysql_query($str);
while ($res = mysql_fetch_assoc($query)) {
    $periodeaktif = $res['periode'];
    $periodemulai = $res['tanggalmulai'];
    $periodesampai = $res['tanggalsampai'];
}
$where = "kodeorg='".$_SESSION['empl']['lokasitugas']."' and tanggal >= '".$periodemulai."' and tanggal <= '".$periodesampai."'";
$cols = 'notransaksi,tanggal,noakun,tipetransaksi,jumlah,posting';
$colArr = explode(',', $cols);
$query = selectQuery($dbname, 'keu_kasbankht', $cols, $where, 'tanggal desc, notransaksi desc');
$data = fetchData($query);
$title = 'Cash/Bank Tansaction';
$align = explode(',', 'L,L,L,L,R,L');
$length = explode(',', '20,20,15,20,15,10');
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