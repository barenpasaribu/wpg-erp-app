<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$revisi = $_GET['revisi'];
if ('' !== $gudang) {
    $str = 'select a.*,b.namaakun from '.$dbname.".keu_jurnaldt_vw a\r\n        left join ".$dbname.".keu_5akun b\r\n        on a.noakun=b.noakun\r\n        where a.tanggal between '".$periode."-01' and LAST_DAY('".$periode1."-15')\r\n        and a.kodeorg='".$gudang."'\r\n        and a.nojurnal NOT LIKE '%CLSM%'\r\n        and a.revisi<='".$revisi."'\r\n        order by a.nojurnal \r\n        ";
} else {
    $str = 'select a.*,b.namaakun from '.$dbname.".keu_jurnaldt_vw a\r\n        left join ".$dbname.".keu_5akun b\r\n        on a.noakun=b.noakun\r\n        where a.tanggal between '".$periode."-01' and LAST_DAY('".$periode1."-15')\r\n        and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' \r\n        and a.nojurnal NOT LIKE '%CLSM%'\r\n        and a.revisi<='".$revisi."'\r\n        and length(kodeorganisasi)=4)                    \r\n        order by a.nojurnal \r\n        ";
}

$aresta = 'SELECT kodeorg, tahuntanam FROM '.$dbname.".setup_blok\r\n    ";
$query = mysql_query($aresta);
while ($res = mysql_fetch_assoc($query)) {
    $tahuntanam[$res['kodeorg']] = $res['tahuntanam'];
}
if ('' === $periode) {
    $periode = substr($_SESSION['org']['period']['start'], 0, 7);
}

class PDF extends FPDF
{
    public function Header()
    {
        global $pt;
        global $gudang;
        global $periode;
        global $periode1;
        global $revisi;
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(190, 3, strtoupper($_SESSION['lang']['laporanjurnal']), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(155, 3, 'UNIT:'.$pt.':'.$gudang.':'.$periode.'-'.$periode1.'', 0, 0, 'L');
        $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
        $this->Cell(155, 3, $_SESSION['lang']['revisi'].':'.$revisi, '', 0, 'L');
        $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
        $this->Cell(155, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, 'User', '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 6);
        $this->Cell(5, 5, 'No.', 1, 0, 'C');
        $this->Cell(24, 5, $_SESSION['lang']['nojurnal'], 1, 0, 'C');
        $this->Cell(16, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C');
        $this->Cell(14, 5, $_SESSION['lang']['noakun'], 1, 0, 'C');
        $this->Cell(40, 5, $_SESSION['lang']['namaakun'], 1, 0, 'C');
        $this->Cell(44, 5, $_SESSION['lang']['uraian'], 1, 0, 'C');
        $this->Cell(25, 5, $_SESSION['lang']['debet'], 1, 0, 'C');
        $this->Cell(25, 5, $_SESSION['lang']['kredit'], 1, 0, 'C');
        $this->Ln();
        $this->Ln();
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$salakqty = 0;
$masukqty = 0;
$keluarqty = 0;
$sawalQTY = 0;
$sdebet = $skredit = 0;
$res = mysql_query($str);
$no = 0;
if (mysql_num_rows($res) < 1) {
    echo $_SESSION['lang']['tidakditemukan'];
} else {
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $tanggal = $bar->tanggal;
        $noakun = $bar->noakun;
        $nojurnal = $bar->nojurnal;
        $keterangan = $bar->keterangan;
        $namaakun = $bar->namaakun;
        $jumlah = $bar->jumlah;
        if (0 <= $jumlah) {
            $debet = $jumlah;
            $kredit = 0;
        } else {
            $debet = 0;
            $kredit = $jumlah * -1;
        }

        $pdf->Cell(5, 3, $no, 0, 0, 'C');
        $pdf->Cell(24, 3, $nojurnal, 0, 0, 'L');
        $pdf->Cell(18, 3, tanggalnormal($tanggal), 0, 0, 'C');
        $pdf->Cell(12, 3, $noakun, 0, 0, 'L');
        $pdf->Cell(40, 3, $namaakun, 0, 0, 'L');
        $pdf->Cell(44, 3, $keterangan, 0, 0, 'L');
        $pdf->Cell(25, 3, number_format($debet, 2, '.', ','), 0, 0, 'R');
        $pdf->Cell(25, 3, number_format($kredit, 2, '.', ','), 0, 1, 'R');
        $sdebet += $debet;
        $skredit += $kredit;
    }
    $pdf->Cell(143, 2, ' ', 0, 0, 'L');
    $pdf->Cell(25, 2, '-------------------------', 0, 0, 'R');
    $pdf->Cell(25, 2, '-------------------------', 0, 1, 'R');
    $pdf->Cell(143, 3, 'T O T A L   : ', 0, 0, 'R');
    $pdf->Cell(25, 3, number_format($sdebet, 2, '.', ','), 0, 0, 'R');
    $pdf->Cell(25, 3, number_format($skredit, 2, '.', ','), 0, 1, 'R');
    $pdf->Cell(143, 2, ' ', 0, 0, 'L');
    $pdf->Cell(25, 2, '-------------------------', 0, 0, 'R');
    $pdf->Cell(25, 2, '-------------------------', 0, 1, 'R');
    $pdf->Output();
}

?>