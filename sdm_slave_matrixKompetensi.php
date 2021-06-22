<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
if ('' == $kamar) {
    $kamar = $_GET['kamar'];
    $jabatan = $_GET['jabatan'];
}

$sJabat = 'select distinct * from '.$dbname.'.sdm_5jabatan where 1';
$qJabat = mysql_query($sJabat);
while ($rJabat = mysql_fetch_assoc($qJabat)) {
    $kamusJabat[$rJabat['kodejabatan']] = $rJabat['namajabatan'];
}
if ('pdf' == $kamar) {
    class PDF extends FPDF
    {
        public function Header()
        {
            global $jabatan;
            global $kriteria;
            $this->SetFont('Arial', 'B', 11);
            $this->Cell(190, 6, strtoupper('Matrix '.$_SESSION['lang']['kompetensi']), 0, 1, 'C');
            $this->Ln();
        }
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(50, 6, $_SESSION['lang']['jabatan'], 1, 0, 'L');
    $pdf->Cell(30, 6, $_SESSION['lang']['jenis'], 1, 0, 'L');
    $pdf->Cell(30, 6, 'Item', 1, 0, 'L');
    $pdf->Cell(70, 6, $_SESSION['lang']['perilaku'], 1, 0, 'L');
    $pdf->Ln();
    $str = 'select * from '.$dbname.".sdm_5matrikkompetensi where kodejabatan = '".$jabatan."'\r\n    order by kodejabatan, jenis, item, prilaku";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $pdf->Cell(50, 6, $kamusJabat[$bar->kodejabatan], 0, 0, 'L');
        $pdf->Cell(30, 6, $bar->jenis, 0, 0, 'L');
        $pdf->Cell(30, 6, $bar->item, 0, 0, 'L');
        $pdf->MultiCell(70, 6, $bar->prilaku, 0, 'L', false);
    }
    $pdf->Output();
}

?>