<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$pt = $_GET['pt'];
$unit = $_GET['gudang'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$revisi = $_GET['revisi'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$tahunlalu = $tahun - 1;
$bulan = $qwe[1];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'BALANCE SHEET';
$periodesaldo = str_replace('-', '', $periode);
if ('akhir' === $periode1) {
    $periodPRF = substr($periodesaldo, 0, 4).'01';
} else {
    $periodPRF = $tahunlalu.$bulan;
}

if ('akhir' === $periode1) {
    $periodPRF2 = substr($periodesaldo, 0, 4).'-01';
} else {
    $periodPRF2 = $tahunlalu.'-'.$bulan;
}

if ('akhir' === $periode1) {
    $kolomPRF = 'awal01';
} else {
    $kolomPRF = 'awal'.date('m', $t);
}

$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2) + 1, 15, substr($periodesaldo, 0, 4));
$periodCUR = date('Ym', $t);
$periodCUR2 = substr($periodesaldo, 0, 4).'-'.substr($periodesaldo, 4, 2);
$kolomCUR = 'awal'.date('m', $t);
$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2), 15, substr($periodesaldo, 0, 4));
$captionCUR = date('M-Y', $t);
$t = mktime(0, 0, 0, 12, 15, substr($periodesaldo, 0, 4) - 1);
$t1 = mktime(0, 0, 0, $bulan, 15, substr($periodesaldo, 0, 4) - 1);
if ('akhir' === $periode1) {
    $captionPRF = date('M-Y', $t);
} else {
    $captionPRF = $captionPRF = date('M-Y', $t1);
}

if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $dzArr[$bar->nourut]['nourut'] = $bar->nourut;
    $dzArr[$bar->nourut]['tampil'] = $bar->variableoutput;
    $dzArr[$bar->nourut]['tipe'] = $bar->tipe;
    if ('ID' === $_SESSION['language']) {
        $dzArr[$bar->nourut]['keterangan'] = $bar->keterangandisplay;
    } else {
        $dzArr[$bar->nourut]['keterangan'] = $bar->keterangandisplay1;
    }

    $dzArr[$bar->nourut]['noakundari'] = $bar->noakundari;
    $dzArr[$bar->nourut]['noakunsampai'] = $bar->noakunsampai;
}

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        global $periode;
        global $periode1;
        global $revisi;
        global $unit;
        global $captionCUR;
        global $captionPRF;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20, 3, $namapt, '', 1, 'L');
        $this->Cell(20, 3, 'UNIT:'.$unit, '', 1, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        $this->Cell(190, 3, strtoupper($_SESSION['lang']['neraca']), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln();
        $this->Cell(150, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, date('d-m-Y H:i'), 0, 1, 'L');
        $this->Cell(150, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, $_SESSION['lang']['page'], '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $this->PageNo(), '', 1, 'L');
        $this->Cell(150, 3, ' ', '', 0, 'R');
        $this->Cell(15, 3, 'User', '', 0, 'L');
        $this->Cell(2, 3, ':', '', 0, 'L');
        $this->Cell(35, 3, $_SESSION['standard']['username'], '', 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->Line(10, 36, 200, 36);
        $this->Ln();
        $this->Cell(110, 5, '', '', 0, 'L');
        $this->Cell(30, 5, $captionCUR, 'B', 0, 'R');
        $this->Cell(30, 5, $captionPRF, 'B', 1, 'R');
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

if (!empty($dzArr)) {
    foreach ($dzArr as $data) {
        $st12 = 'select (sum('.$kolomPRF.")+sum(debet".$bulan.")-sum(kredit".$bulan.")) as kemarin\r\n        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' \r\n        and '".$data['noakunsampai']."' and (periode='".$periodPRF."') and ".$where;
        $res12 = mysql_query($st12);
        $jlhlalu = 0;
        while ($ba12 = mysql_fetch_object($res12)) {
            $jlhlalu = $ba12->kemarin;
        }
        $dzArr[$data['nourut']]['jumlahlalu'] = $jlhlalu;
        if (0 === $revisi) {
            $st12 = 'select (sum('.$kolomCUR.")+sum(debet".$bulan.")-sum(kredit".$bulan.")) as sekarang\r\n            from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' \r\n            and '".$data['noakunsampai']."' and (periode='".$periodCUR."') and ".$where;
            $res12 = mysql_query($st12);
            $jlhsekarang = 0;
            while ($ba12 = mysql_fetch_object($res12)) {
                $jlhsekarang = $ba12->sekarang;
            }
            $dzArr[$data['nourut']]['jumlahsekarang'] = $jlhsekarang;
        }
    }
}

if (0 < $revisi) {
    $st12 = "select noakun, sum(jumlah) as jumlah\r\n        from ".$dbname.".keu_jurnaldt_vw where periode between '".$periodPRF2."' \r\n        and '".$periodCUR2."' and ".$where." and revisi <= '".$revisi."' group by noakun";
    $res12 = mysql_query($st12);
    $jlhsekarang = 0;
    while ($ba12 = mysql_fetch_object($res12)) {
        if (!empty($dzArr)) {
            foreach ($dzArr as $data) {
                if ($data['noakundari'] <= $ba12->noakun && $ba12->noakun <= $data['noakunsampai']) {
                    $dzArr[$data['nourut']]['jumlahtemp'] += $ba12->jumlah;
                    $dzArr[$data['nourut']]['jumlahsekarang'] = $dzArr[$data['nourut']]['jumlahlalu'] + $dzArr[$data['nourut']]['jumlahtemp'];
                }
            }
        }
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
if (!empty($dzArr)) {
    foreach ($dzArr as $data) {
        if ('Header' === $data['tipe']) {
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10, 5, '', '', 0, 'C');
            $pdf->Cell(100, 5, $data['keterangan'], '', 0, 'L');
            $pdf->Cell(30, 5, '', '', 0, 'C');
            $pdf->Cell(30, 5, '', '', 1, 'C');
        } else {
            if ('Total' === $bar->tipe) {
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(10, 5, '', '', 0, 'C');
                $pdf->Cell(5, 5, '', '', 0, 'L');
                $pdf->Cell(95, 5, $data['keterangan'], '', 0, 'L');
                if ($data['jumlahsekarang'] < 0) {
                    $pdf->SetTextColor(240, 53, 61);
                    $pdf->Cell(30, 5, number_format($data['jumlahsekarang'] * -1), 'T', 0, 'R');
                } else {
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(30, 5, number_format($data['jumlahsekarang']), 'T', 0, 'R');
                }

                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(30, 5, number_format($data['jumlahlalu']), 'T', 1, 'R');
                $pdf->Ln();
            } else {
                if (0 === $data['jumlahsekarang'] && 0 === $data['jumlahlalu']) {
                    continue;
                }

                $pdf->Cell(10, 5, '', '', 0, 'C');
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(10, 5, '', '', 0, 'L');
                $pdf->Cell(90, 5, $data['keterangan'], '', 0, 'L');
                if ($data['jumlahsekarang'] < 0) {
                    $pdf->SetTextColor(240, 53, 61);
                    $pdf->Cell(30, 5, number_format($data['jumlahsekarang'] * -1), '', 0, 'R');
                } else {
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(30, 5, number_format($data['jumlahsekarang']), '', 0, 'R');
                }

                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(30, 5, number_format($data['jumlahlalu']), '', 1, 'R');
            }
        }
    }
}

$pdf->Output();

?>