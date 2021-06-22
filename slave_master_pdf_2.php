<?php



include_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$query = selectQuery($dbname, $table);
$result = fetchData($query);
$header = [];
foreach ($result[0] as $key => $row) {
    $header[] = $key;
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
    if ($row['karyawanid']) {
        $sDt = 'select namakaryawan,karyawanid from '.$dbname.".datakaryawan  where karyawanid='".$row['karyawanid']."'";
        $qDt = mysql_query($sDt);
        $rDt = mysql_fetch_assoc($qDt);
        if ($rDt['karyawanid'] === $row['karyawanid']) {
            $data1 = $rDt['namakaryawan'];
        }
    }

    if ($row['karyawanid']) {
        $sDt = 'select namakaryawan,karyawanid,lokasitugas from '.$dbname.".datakaryawan  where karyawanid='".$row['karyawanid']."'";
        $qDt = mysql_query($sDt);
        $rDt = mysql_fetch_assoc($qDt);
        if ($rDt['karyawanid'] === $row['karyawanid']) {
            $data1 = $rDt['namakaryawan'].'['.$rDt['lokasitugas'].']';
        }
    }

    $pdf->Cell($width / count($header), $height, $data1, '', 0, 'L');
    $pdf->Cell($width / count($header), $height, $row['idkomponen'], '', 0, 'L');
    $pdf->Cell($width / count($header), $height, $row['jumlah'], '', 0, 'L');
    $pdf->Ln();
}
$pdf->Output();

?>