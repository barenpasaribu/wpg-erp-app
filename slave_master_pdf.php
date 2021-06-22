<?php



session_start();
include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$query = selectQuery($dbname, $table);
$result = fetchData($query);
$header = [];
foreach ($result[0] as $key => $row) {
    if ('EN' === $_SESSION['language'] && '' !== $_SESSION['lang'][strtolower($key)]) {
        $header[] = $_SESSION['lang'][strtolower($key)];
    } else {
        $header[] = $key;
    }
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
        $this->Cell($width, $height, 'Tabel : '.$table, '', 1, 'L');
        $this->Ln();
        foreach ($header as $hName) {
            $this->Cell($width / count($header), $height, ucfirst($hName), 'TBLR', 0, 'L');
        }
        $this->Ln();
    }
}

$pdf = new masterpdf('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 15;
$pdf->SetFont('Arial', '', 8);
$pdf->AddPage();
foreach ($result as $row) {
    foreach ($row as $data) {
        $pdf->Cell($width / count($header), $height, $data, '', 0, 'L');
    }
    $pdf->Ln();
}
$pdf->Output();

?>