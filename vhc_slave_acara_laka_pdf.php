<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$tmp = explode(',', $_GET['column']);
$notransaksi = $tmp[0];
$karyawan = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
    }
}

$a1 = 'select * from '.$dbname.".vhc_balaka where notransaksi='".$notransaksi."'";
$b1 = mysql_query($a1);
$c1 = mysql_fetch_assoc($b1);
$pdf = new PDF('P', 'mm', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 7;
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(100 / 100 * $width, $height, 'BERITA ACARA KECELAKAAN UNIT', 0, 1, 'C', 1);
$pdf->Ln(15);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(35, $height, 'NO BERITA ACARA', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $c1['notransaksi'], 0, 1, 'L');
$pdf->Cell(35, $height, 'TANGGAL', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $c1['tanggal'], 0, 1, 'L');
$pdf->Cell(35, $height, 'KODE KENDARAAN', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $c1['kodealat'], 0, 1, 'L');
$pdf->Cell(35, $height, 'OPERATOR', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $karyawan[$c1['operator']], 0, 1, 'L');
$pdf->Cell(35, $height, 'SECURITY', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $karyawan[$c1['security']], 0, 1, 'L');
$pdf->Cell(35, $height, 'MEKANIK', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $karyawan[$c1['mekanik']], 0, 1, 'L');
$pdf->Cell(35, $height, 'MANAGER UNIT', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $karyawan[$c1['managerunit']], 0, 1, 'L');
$pdf->Cell(35, $height, 'KA. WORKSHOP', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $karyawan[$c1['kaworkshop']], 0, 1, 'L');
$pdf->Cell(35, $height, 'KRONOLOGIS KEJADIAN', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $c1['kronologis'], 0, 1, 'L');
$pdf->Cell(35, $height, 'AKIBAT KEJADIAN', 0, 0, 'L');
$pdf->Cell(5, $height, ':', 0, 0, 'L');
$pdf->Cell(1, $height, $c1['akibatkejadian'], 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(55, $height, '     SECURITY', 0, 0, 'L');
$pdf->Cell(50, $height, 'OPERATOR', 0, 0, 'L');
$pdf->Cell(50, $height, 'MEKANIK', 0, 0, 'L');
$pdf->Cell(50, $height, 'MANAGER', 0, 1, 'L');
$pdf->Ln(5);
$pdf->Cell(55, $height, '     --------', 0, 0, 'L');
$pdf->Cell(50, $height, '--------', 0, 0, 'L');
$pdf->Cell(50, $height, '-------', 0, 0, 'L');
$pdf->Cell(50, $height, '-------', 0, 0, 'L');
$pdf->Output();

?>