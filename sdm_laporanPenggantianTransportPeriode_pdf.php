<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$periode = $_GET['periode'];
if ('' == $periode) {
    $periode = date('Y-m');
}

$str = 'select a.*,sum(b.jlhbbm) as bbm,sum(b.hargatotal) as harga,c.namakaryawan from '.$dbname.".sdm_penggantiantransport a\r\n      left join ".$dbname.".sdm_penggantiantransportdt b \r\n\t  on a.notransaksi=b.notransaksi\r\n\t  left join ".$dbname.".datakaryawan c\r\n\t  on a.karyawanid=c.karyawanid\r\n\t   where periode='".$periode."' and \r\n\t  kodeorg='".substr($_SESSION['empl']['lokasitugas'], 0, 4)."'\r\n\t  group by notransaksi";
$res = mysql_query($str);

class PDF extends FPDF
{
    public function Header()
    {
        global $periode;
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
        $this->Cell(35, 5, $_SESSION['lang']['periode'], '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(100, 5, $periode, '', 0, 'L');
        $this->Cell(15, 5, 'User', '', 0, 'L');
        $this->Cell(2, 5, ':', '', 0, 'L');
        $this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
        $this->SetFont('Arial', '', 6);
        $this->Cell(5, 5, 'No.', 1, 0, 'C');
        $this->Cell(20, 5, $_SESSION['lang']['notransaksi'], 1, 0, 'C');
        $this->Cell(10, 5, $_SESSION['lang']['py'], 1, 0, 'C');
        $this->Cell(35, 5, $_SESSION['lang']['karyawan'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['totalbiaya'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['dibayar'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['transport'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['perawatan'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['toll'], 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['lain'], 1, 0, 'C');
        $this->Cell(10, 5, 'BBM', 1, 0, 'C');
        $this->Cell(15, 5, $_SESSION['lang']['harga'], 1, 1, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->Cell(5, 5, $no, 0, 0, 'C');
    $pdf->Cell(20, 5, $bar->notransaksi, 0, 0, 'C');
    $pdf->Cell(10, 5, $bar->alokasi, 0, 0, 'L');
    $pdf->Cell(35, 5, $bar->namakaryawan, 0, 0, 'L');
    $pdf->Cell(15, 5, number_format($bar->totalklaim, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->dibayar, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->trans, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->perawatan, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->toll, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->bylain, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(10, 5, number_format($bar->bbm, 2, ',', '.'), 0, 0, 'R');
    $pdf->Cell(15, 5, number_format($bar->harga, 2, ',', '.'), 0, 1, 'R');
    $tcl += $bar->totalklaim;
    $tbyr += $bar->dibayar;
    $ttrans += $bar->trans;
    $tprw += $bar->perawatan;
    $ttol += $bar->toll;
    $tbyl += $bar->bylain;
    $tbbm += $bar->bbm;
    $tharga += $bar->harga;
}
$pdf->Cell(5, 5, '', 0, 0, 'C');
$pdf->Cell(65, 5, $_SESSION['lang']['total'], 'T', 0, 'C');
$pdf->Cell(15, 5, number_format($tcl, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($tbyr, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($ttrans, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($tprw, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($ttol, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($tbyl, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(10, 5, number_format($tbbm, 2, ',', '.'), 'T', 0, 'R');
$pdf->Cell(15, 5, number_format($tharga, 2, ',', '.'), 'T', 1, 'R');
$pdf->Output();

?>