<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
if($pt==""){
    $pt = substr($_SESSION['empl']['lokasitugas'], 0,3);
}
$gudang = $_GET['gudang'];
$supplier = $_GET['supplier'];
$tanggalpivot = $_GET['tanggalpivot'];

$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'Seluruhnya';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
if ('' !== $gudang) {
    $whr .= " and substr(novp,2,4) = '".$gudang."' ";
} else {
    if ('' !== $pt) {
        $whr .= " and kodeorg = '".$pt."'";
    }
}
if ('' !== $supplier) {
    $whr .= " and kodesupplier = '".$supplier."'";
}

$str = 'select distinct(namasupplier) as namasupplier from '.$dbname.".aging_sch_vw\r\n      where tanggal > '2012-12-31' and (nilaiinvoice > dibayar or dibayar is NULL)\r\n      ".$whr.' order by namasupplier asc';

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        global $tanggalpivot;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 3, $namapt.' per '.$tanggalpivot, '', 1, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(280, 3, strtoupper($_SESSION['lang']['usiahutang']), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Cell(225, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
        $this->Cell(225, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
        $this->Cell(225, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, 'User', '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        $this->Cell(16, 5, $_SESSION['lang']['nourut'], LTR, 0, 'C');
        $this->Cell(55, 5, $_SESSION['lang']['noinvoice'], 1, 0, 'L');
        $this->Cell(25, 5, 'Contract Value', LTR, 0, 'C');
        $this->Cell(25, 5, 'Invoice Value', LTR, 0, 'C');
        $this->Cell(25, 5, $_SESSION['lang']['belumjatuhtempo'], LTR, 0, 'C');
        $this->Cell(100, 5, $_SESSION['lang']['sudahjatuhtempo'], 1, 0, 'C');
        $this->Cell(25, 5, $_SESSION['lang']['dibayar'], LTR, 0, 'C');
        $this->Ln();
        $this->Cell(16, 5, $_SESSION['lang']['tanggal'], LBR, 0, 'C');
        $this->Cell(55, 5, $_SESSION['lang']['namasupplier'], 1, 0, 'L');
        $this->Cell(25, 5, 'PO/Contract No.', LBR, 0, 'C');
        $this->Cell(25, 5, $_SESSION['lang']['tgljatuhtempo'], LBR, 0, 'C');
        $this->Cell(25, 5, '', LBR, 0, 'C');
        $this->Cell(25, 5, '1-30 '.$_SESSION['lang']['hari'], 1, 0, 'C');
        $this->Cell(25, 5, '31-60 '.$_SESSION['lang']['hari'], 1, 0, 'C');
        $this->Cell(25, 5, '61-90 '.$_SESSION['lang']['hari'], 1, 0, 'C');
        $this->Cell(25, 5, 'over 90 '.$_SESSION['lang']['hari'], 1, 0, 'C');
        $this->Cell(25, 5, 'Outstanding', LBR, 0, 'C');
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$res = mysql_query($str);
$no = 0;
if (@mysql_num_rows($res) < 1) {
    echo $_SESSION['lang']['tidakditemukan'];
} else {
    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $grantotal0 = $grantotal30 = $grandtotal60 = $grantotal90 = $grantotal100 = $grantotaldibayar = 0;
    $totalinvoice = 0;
    while ($bar = mysql_fetch_object($res)) {
        $subTotInvoice = 0;
        $total0 = 0;
        $total30 = 0;
        $total60 = 0;
        $total90 = 0;
        $total100 = 0;
        $i = 'select * from '.$dbname.".aging_sch_vw where tanggal > '2012-12-31' and (nilaiinvoice > dibayar or dibayar is NULL) and \r\n\t\t\t\t\t\tnamasupplier='".$bar->namasupplier."' order by tanggal asc";
        $n = mysql_query($i);
        while ($d = mysql_fetch_assoc($n)) {
            $namasupplier = $d['namasupplier'];
            if ('' === $namasupplier) {
                $namasupplier = '&nbsp;';
            }

            $noinvoice = $d['noinvoice'];
            $tanggal = $d['tanggal'];
            $jatuhtempo = $d['jatuhtempo'];
            $nopokontrak = $d['nopo'];
            $nilaipo = $d['kurs'] * $d['nilaipo'];
            $nilaikontrak = $d['kurs'] * $d['nilaikontrak'];
            $nilaiinvoice = $d['kurs'] * $d['nilaiinvoice'];
            $dibayar = $d['kurs'] * $d['dibayar'];
            $sisainvoice = $nilaiinvoice - $dibayar;
            $nilaipokontrak = $nilaipo;
            if (0 < $nilaikontrak) {
                $nilaipokontrak = $nilaikontrak;
            }

            $date1 = tanggalbiasa($tanggalpivot);
            if ('0000-00-00' === $jatuhtempo) {
                $jatuhtempo = $date1;
            }

            $diff = strtotime($jatuhtempo) - strtotime($date1);
            $outstd = floor($diff / (60 * 60 * 24));
            $flag0 = $flag30 = $flag60 = $flag90 = $flag100 = 0;
            if (0 !== $outstd) {
                $outstd *= -1;
            }

            if ($outstd <= 0) {
                $flag0 = 1;
            }

            if (1 <= $outstd && $outstd <= 30) {
                $flag30 = 1;
            }

            if (31 <= $outstd && $outstd <= 60) {
                $flag60 = 1;
            }

            if (61 <= $outstd && $outstd <= 90) {
                $flag90 = 1;
            }

            if (90 < $outstd) {
                $flag100 = 1;
            }

            if (1 === $flag0) {
                $total0 += $sisainvoice;
            }

            if (1 === $flag30) {
                $total30 += $sisainvoice;
            }

            if (1 === $flag60) {
                $total60 += $sisainvoice;
            }

            if (1 === $flag90) {
                $total90 += $sisainvoice;
            }

            if (1 === $flag100) {
                $total100 += $sisainvoice;
            }

            $subtotaldibayar += $dibayar;
            if ('0000-00-00' === $jatuhtempo) {
                $outstd = '';
                $jatuhtempo = '';
            } else {
                $jatuhtempo = tanggalnormal($jatuhtempo);
            }

            ++$no;
            $pdf->Cell(16, 5, $no, LTR, 0, 'L');
            $pdf->Cell(55, 5, $noinvoice, LTR, 0, 'L');
            $pdf->Cell(25, 5, number_format($nilaipokontrak, 2), LTR, 0, 'R');
            $pdf->Cell(25, 5, number_format($nilaiinvoice, 2), LTR, 0, 'R');
            $dummy = '';
            if (1 === $flag0) {
                $dummy = number_format($sisainvoice, 2);
            }

            $pdf->Cell(25, 5, $dummy, LTR, 0, 'R');
            $dummy = '';
            if (1 === $flag30) {
                $dummy = number_format($sisainvoice, 2);
            }

            $pdf->Cell(25, 5, $dummy, LTR, 0, 'R');
            $dummy = '';
            if (1 === $flag60) {
                $dummy = number_format($sisainvoice, 2);
            }

            $pdf->Cell(25, 5, $dummy, LTR, 0, 'R');
            $dummy = '';
            if (1 === $flag90) {
                $dummy = number_format($sisainvoice, 2);
            }

            $pdf->Cell(25, 5, $dummy, LTR, 0, 'R');
            $dummy = '';
            if (1 === $flag100) {
                $dummy = number_format($sisainvoice, 2);
            }

            $pdf->Cell(25, 5, $dummy, LTR, 0, 'R');
            $dummy = '';
            $pdf->Cell(25, 5, number_format($dibayar, 2, '.', ','), LTR, 1, 'R');
            $pdf->Cell(16, 5, tanggalbiasa2($tanggal), LBR, 0, 'L');
            $pdf->Cell(55, 5, $namasupplier, LBR, 0, 'L');
            $pdf->Cell(25, 5, $nopokontrak, LBR, 0, 'L');
            $pdf->Cell(25, 5, $jatuhtempo, LBR, 0, 'R');
            $pdf->Cell(25, 5, '', LBR, 0, 'R');
            $pdf->Cell(25, 5, '', LBR, 0, 'R');
            $pdf->Cell(25, 5, '', LBR, 0, 'R');
            $pdf->Cell(25, 5, '', LBR, 0, 'R');
            $pdf->Cell(25, 5, '', LBR, 0, 'R');
            $pdf->Cell(25, 5, $outstd.' Hari', LBR, 1, 'R');
            $subTotInvoice += $nilaiinvoice;
            $subtotaldibayar += $dibayar;
        }
        $pdf->Cell(96, 5, 'TOTAL PER SUPPLIER', 1, 0, 'C');
        $dummy = number_format($subTotInvoice, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $dummy = number_format($total0, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $dummy = number_format($total30, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $dummy = number_format($total60, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $dummy = number_format($total90, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $dummy = number_format($total100, 2);
        $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
        $pdf->Cell(25, 5, number_format($totaldibayar, 2, '.', ','), 1, 1, 'R');
        $totalinvoice += $subTotInvoice;
        $grantotaldibayar += $subtotaldibayar;
        $grantotal0 += $total0;
        $grantotal30 += $total30;
        $grantotal60 += $total60;
        $grantotal90 += $total90;
        $grantotal100 += $total00;
    }
    $pdf->Cell(96, 5, 'TOTAL', 1, 0, 'C');
    $dummy = number_format($totalinvoice, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $dummy = number_format($grantotal0, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $dummy = number_format($grantotal30, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $dummy = number_format($grantotal60, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $dummy = number_format($grantotal90, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $dummy = number_format($total100, 2);
    $pdf->Cell(25, 5, $dummy, 1, 0, 'R');
    $pdf->Cell(25, 5, number_format($grantotaldibayar, 2, '.', ','), 1, 0, 'R');
    $pdf->Output();
}

function tanggalbiasa($_q)
{
    $_q = str_replace('-', '', $_q);

    return substr($_q, 4, 4).'-'.substr($_q, 2, 2).'-'.substr($_q, 0, 2);
}

function tanggalbiasa2($_q)
{
    $_q = str_replace('-', '', $_q);

    return substr($_q, 6, 2).'-'.substr($_q, 4, 2).'-'.substr($_q, 0, 4);
}

?>