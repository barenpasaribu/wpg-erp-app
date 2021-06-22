<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$pt = $_GET['pt'];
$unit = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'INCOME STATEMENT';
$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$lebarkiri = 40;
$lebarisi = 18;
$lebarkanan = 25;
if ('' === $unit) {
    $tampilunit = $_SESSION['lang']['all'];
} else {
    $tampilunit = $unit;
}

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        global $periode;
        global $tampilunit;
        global $lebarkiri;
        global $lebarisi;
        global $lebarkanan;
        $this->SetFont('Arial', 'B', 8);
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        $this->Cell(300, 3, strtoupper($_SESSION['lang']['laporanrugilabaperiodik']), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln();
        $this->Cell(230, 3, $namapt, '', 0, 'L');
        $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
        $this->Cell(230, 3, 'UNIT:'.$tampilunit, '', 0, 'L');
        $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
        $this->Cell(230, 3, 'Periode:'.$periode, '', 0, 'L');
        $this->Cell(15, 3, 'User', '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Ln();
        $this->Cell($lebarkiri, 5, '', 'B', 0, 'L');
        $this->Cell($lebarisi, 5, numToMonth(1, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(2, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(3, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(4, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(5, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(6, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(7, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(8, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(9, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(10, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(11, 'E'), 'B', 0, 'R');
        $this->Cell($lebarisi, 5, numToMonth(12, 'E'), 'B', 0, 'R');
        $this->Cell($lebarkanan, 5, 'YTD', 'B', 1, 'R');
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$tnow2[] = 0;
$ttill2 = 0;
$tnow3[] = 0;
$ttill3 = 0;
while ($bar = mysql_fetch_object($res)) {
    if ('Header' === $bar->tipe) {
        if ('ID' === $_SESSION['language']) {
            $stream .= '<tr class=rowcontent><td colspan=16><b>'.$bar->keterangandisplay.'</b></td></tr>';
        } else {
            $stream .= '<tr class=rowcontent><td colspan=16><b>'.$bar->keterangandisplay1.'</b></td></tr>';
        }
    } else {
        $akum = 0;
        for ($i = 1; $i <= 12; ++$i) {
            if (1 === strlen($i)) {
                $ii = '0'.$i;
            } else {
                $ii = $i;
            }

            $st13 = 'select sum(debet'.$ii.') - sum(kredit'.$ii.") as sekarang\r\n                       from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n                       and '".$bar->noakunsampai."' and  periode like '".$periode."%' and ".$where;
            $res13 = mysql_query($st13);
            $jlhsekarang[$ii] = 0;
            while ($ba13 = mysql_fetch_object($res13)) {
                $jlhsekarang[$ii] = $ba13->sekarang;
                $akum += $ba13->sekarang;
            }
            $tnow201[$ii] += $jlhsekarang[$ii];
            $tnow301[$ii] += $jlhsekarang[$ii];
        }
        $ttill2 += $akum;
        $ttill3 += $akum;
        if ('Total' === $bar->tipe) {
            if ('' === $bar->noakundari || '' === $bar->noakunsampai) {
                if ('2' === $bar->variableoutput) {
                    $akum = $ttill2;
                    $ttill2 = 0;
                    for ($i = 1; $i <= 12; ++$i) {
                        if (1 === strlen($i)) {
                            $ii = '0'.$i;
                        } else {
                            $ii = $i;
                        }

                        $jlhsekarang[$ii] = $tnow201[$ii];
                        $tnow201[$ii] = 0;
                    }
                }

                if ('3' === $bar->variableoutput) {
                    $akum = $ttill3;
                    $ttill3 = 0;
                    for ($i = 1; $i <= 12; ++$i) {
                        if (1 === strlen($i)) {
                            $ii = '0'.$i;
                        } else {
                            $ii = $i;
                        }

                        $jlhsekarang[$ii] = $tnow301[$ii];
                        $tnow301[$ii] = 0;
                    }
                }
            }

            $pdf->SetFont('Arial', 'B', 7);
            if ('ID' === $_SESSION['language']) {
                $pdf->Cell($lebarkiri, 5, substr($bar->keterangandisplay, 0, 25), '', 0, 'L');
            } else {
                $pdf->Cell($lebarkiri, 5, substr($bar->keterangandisplay1, 0, 25), '', 0, 'L');
            }

            for ($i = 1; $i <= 12; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $pdf->Cell($lebarisi, 5, number_format($jlhsekarang[$ii]), '', 0, 'R');
            }
            $pdf->Cell($lebarkanan, 5, number_format($akum), '', 1, 'R');
            $pdf->Ln();
        } else {
            if (0 === $jlhsekarang && 0 === $akum) {
                continue;
            }

            $pdf->SetFont('Arial', '', 7);
            if ('ID' === $_SESSION['language']) {
                $pdf->Cell($lebarkiri, 5, substr($bar->keterangandisplay, 0, 25), '', 0, 'L');
            } else {
                $pdf->Cell($lebarkiri, 5, substr($bar->keterangandisplay1, 0, 25), '', 0, 'L');
            }

            for ($i = 1; $i <= 12; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $pdf->Cell($lebarisi, 5, number_format($jlhsekarang[$ii]), '', 0, 'R');
            }
            $pdf->Cell($lebarkanan, 5, number_format($akum), '', 1, 'R');
        }
    }
}
$pdf->Output();

?>