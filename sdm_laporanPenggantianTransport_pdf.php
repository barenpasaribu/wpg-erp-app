<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$notransaksi = $_GET['notransaksi'];
$str = 'select a.*,sum(b.jlhbbm) as bbm,c.namakaryawan from '.$dbname.".sdm_penggantiantransport a\r\n      left join ".$dbname.".sdm_penggantiantransportdt b \r\n\t  on a.notransaksi=b.notransaksi\r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t   where a.notransaksi='".$notransaksi."'\r\n\t  group by notransaksi";
$res = mysql_query($str);

class PDF extends FPDF
{
    public function Header()
    {
        global $notransaksi;
        $this->SetFont('Arial', 'B', 8);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(190, 5, strtoupper($_SESSION['lang']['penggantiantransport']), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 5, '', '', 0, 'L');
        $this->Cell(2, 5, '', '', 0, 'L');
        $this->Cell(100, 5, '', '', 0, 'L');
        $this->Cell(15, 5, $_SESSION['lang']['print'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, date('d-m-Y H:i'), '', 1, 'L');
        $this->Cell(35, 5, $_SESSION['lang']['notransaksi'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $notransaksi, '', 0, 'L');
        $this->Cell(15, 5, 'User', '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
        $this->SetFont('Arial', '', 6);
        $this->Cell(5, 5, 'No.', 1, 0, 'C');
        $this->Cell(10, 5, $_SESSION['lang']['periode'], 1, 0, 'C');
        $this->Cell(10, 5, $_SESSION['lang']['py'], 1, 0, 'C');
        $this->Cell(35, 5, $_SESSION['lang']['karyawan'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['totalbiaya'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['dibayar'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['transport'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['perawatan'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['toll'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['lain'], 1, 0, 'C');
        $this->Cell(35, 5, $_SESSION['lang']['keterangan'], 1, 1, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(5, 5, $no, 0, 0, 'C');
    $pdf->Cell(10, 5, substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4), 0, 0, 'C');
    $pdf->Cell(10, 5, $bar->alokasi, 0, 0, 'L');
    $pdf->Cell(35, 5, $bar->namakaryawan, 0, 0, 'L');
    $pdf->Cell(15, 5, number_format($bar->totalklaim, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->dibayar, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->trans, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->perawatan, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->toll, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->bylain, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(35, 5, $bar->keterangan, 0, 1, 'L');
}
$pdf->Ln();
$pdf->Cell(25, 5, 'DEDAIL BBM:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 6);
$pdf->Cell(5, 5, 'No.', 1, 0, 'C');
$pdf->Cell(15, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C');
$pdf->Cell(20, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
$pdf->Cell(20, 5, $_SESSION['lang']['total'], 1, 1, 'C');
$str = 'select * from  '.$dbname.".sdm_penggantiantransportdt where notransaksi='".$notransaksi."'";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(5, 5, $no, 0, 0, 'R');
    $pdf->Cell(15, 5, tanggalnormal($bar->tanggal), 0, 0, 'C');
    $pdf->Cell(20, 5, number_format($bar->jlhbbm, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(20, 5, number_format($bar->hargatotal, 2, ',', '.'), 0, 1, 'R');
    $tbbm += $bar->jlhbbm;
    $tharga += $bar->hargatotal;
}
$pdf->Cell(5, 5, '', 0, 0, 'R');
$pdf->Cell(15, 5, 'TOTAL', 'T', 0, 'C');
$pdf->Cell(20, 5, number_format($tbbm, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(20, 5, number_format($tharga, 2, ',', '.'), 'T', 1, 'R');
$pdf->Output();

?>