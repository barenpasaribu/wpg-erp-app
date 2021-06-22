<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['unit'];
$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];
$tanggal1 = explode('-', $tgl1);
$tanggal2 = explode('-', $tgl2);
$date1 = $tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
$tanggalterakhir = date(t, strtotime($date1));
$tanggal = [];
if ($tanggal1[1] < $tanggal2[1]) {
    for ($i = $tanggal1[0]; $i <= $tanggalterakhir; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii] = $tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
    }
    for ($i = 1; $i <= $tanggal2[0]; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal2[2].'-'.$tanggal2[1].'-'.$ii] = $tanggal2[2].'-'.$tanggal2[1].'-'.$ii;
    }
} else {
    for ($i = $tanggal1[0]; $i <= $tanggal2[0]; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii] = $tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
    }
}

$sdakar = 'select karyawanid, namakaryawan, tipekaryawan, subbagian from '.$dbname.'.datakaryawan';
$qdakar = mysql_query($sdakar) ;
while ($rdakar = mysql_fetch_assoc($qdakar)) {
    $dakar[$rdakar['karyawanid']]['karyawanid'] = $rdakar['karyawanid'];
    $dakar[$rdakar['karyawanid']]['namakaryawan'] = $rdakar['namakaryawan'];
    $dakar[$rdakar['karyawanid']]['tipekaryawan'] = $rdakar['tipekaryawan'];
    $dakar[$rdakar['karyawanid']]['subbagian'] = $rdakar['subbagian'];
}
$stikar = 'select id, tipe from '.$dbname.'.sdm_5tipekaryawan';
$qtikar = mysql_query($stikar) ;
while ($rtikar = mysql_fetch_assoc($qtikar)) {
    $tikar[$rtikar['id']] = $rtikar['tipe'];
}
if ('' === $unit) {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty, a.karyawanid  from ".$dbname.".kebun_prestasi_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal,a.karyawanid';
} else {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty, a.karyawanid  from ".$dbname.".kebun_prestasi_vw a\r\n        where unit = '".$unit."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal, a.karyawanid';
}

$jumlahhari = count($tanggal);
$res = mysql_query($str);
$dzArr = [];
if (mysql_num_rows($res) < 1) {
    $jukol = $jumlahhari * 2 + 5;
    echo $_SESSION['lang']['tidakditemukan'];
    exit();
}

while ($bar = mysql_fetch_object($res)) {
    $dzArr[$bar->karyawanid][$bar->tanggal] = $bar->tanggal;
    $dzArr[$bar->karyawanid]['karyawanid'] = $bar->karyawanid;
    $dzArr[$bar->karyawanid][$bar->tanggal.'j'] = $bar->berat;
    $dzArr[$bar->karyawanid][$bar->tanggal.'k'] = $bar->jjg;
}
if (!empty($dzArr)) {
    foreach ($dzArr as $c => $key) {
        $sort_kodeorg[] = $key['karyawanid'];
    }
    array_multisort($sort_kodeorg, SORT_ASC, $dzArr);
}

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $pt;
        global $unit;
        global $tgl1;
        global $tgl2;
        global $tanggal;
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
        $qAlamat = mysql_query($sAlmat) ;
        $rAlamat = mysql_fetch_assoc($qAlamat);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 11;
        if ('SSP' === $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' === $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' === $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' === $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $rAlamat['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $rAlamat['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$rAlamat['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 11);
        $this->Cell($width, $height, $_SESSION['lang']['laporanpanen'].' per '.$_SESSION['lang']['tanggal'], 0, 1, 'C');
        $this->Cell($width, $height, $_SESSION['lang']['periode'].':'.$tgl1.' S/d '.$tgl2.' '.$_SESSION['lang']['unit'].':'.(('' !== $gudang ? $gudang : $_SESSION['lang']['all'])), 0, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(2 / 100 * $width, $height, '', TRL, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['karyawan'].':', TRL, 0, 'C', 1);
        foreach ($tanggal as $tang) {
            $ting = explode('-', $tang);
            $qwe = date('D', strtotime($tang));
            if ('Sun' === $qwe) {
                $this->SetTextColor(255, 0, 0);
            }

            $this->Cell(2.84 / 100 * $width, $height, $ting[2], 1, 0, 'C', 1);
            $this->SetTextColor(0, 0, 0);
        }
        $this->Cell(4 / 100 * $width, $height, 'Total', 1, 0, 'C', 1);
        $this->Ln();
        $this->Cell(2 / 100 * $width, $height, 'No', RL, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['nama'], RL, 0, 'C', 1);
        foreach ($tanggal as $tang) {
            $ting = explode('-', $tang);
            $qwe = date('D', strtotime($tang));
            if ('Sun' === $qwe) {
                $this->SetTextColor(255, 0, 0);
            }

            $this->Cell(2.84 / 100 * $width, $height, 'jjg', TRL, 0, 'C', 1);
            $this->SetTextColor(0, 0, 0);
        }
        $this->Cell(4 / 100 * $width, $height, 'jjg', TRL, 0, 'C', 1);
        $this->Ln();
        $this->Cell(2 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['tipe'], BRL, 0, 'C', 1);
        foreach ($tanggal as $tang) {
            $ting = explode('-', $tang);
            $qwe = date('D', strtotime($tang));
            if ('Sun' === $qwe) {
                $this->SetTextColor(255, 0, 0);
            }

            $this->Cell(2.84 / 100 * $width, $height, 'kg', BRL, 0, 'C', 1);
            $this->SetTextColor(0, 0, 0);
        }
        $this->Cell(4 / 100 * $width, $height, 'kg', BRL, 0, 'C', 1);
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'pt', 'A4');
$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
$height = 11;
$pdf->AddPage();
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 6);
$no = 0;
foreach ($dzArr as $arey) {
    ++$no;
    $pdf->Cell(2 / 100 * $width, $height, $no, TRL, 0, 'R', 1);
    $pdf->Cell(6 / 100 * $width, $height, $dakar[$arey['karyawanid']]['namakaryawan'], TRL, 0, 'L', 1);
    $totalj = 0;
    foreach ($tanggal as $tang) {
        $qwe = date('D', strtotime($tang));
        if ('Sun' === $qwe) {
            $pdf->SetTextColor(255, 0, 0);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->Cell(2.84 / 100 * $width, $height, number_format($arey[$tang.'j']), TRL, 0, 'R', 1);
        $total[$tang.'j'] += $arey[$tang.'j'];
        $totalj += $arey[$tang.'j'];
    }
    $pdf->Cell(4 / 100 * $width, $height, number_format($totalj), TRL, 0, 'R', 1);
    $pdf->Ln();
    $pdf->Cell(2 / 100 * $width, $height, '', BRL, 0, 'C', 1);
    $pdf->Cell(6 / 100 * $width, $height, $dakar[$arey['karyawanid']]['subbagian'].' - '.$tikar[$dakar[$arey['karyawanid']]['tipekaryawan']], BRL, 0, 'L', 1);
    $totalk = 0;
    foreach ($tanggal as $tang) {
        $qwe = date('D', strtotime($tang));
        if ('Sun' === $qwe) {
            $pdf->SetTextColor(255, 0, 0);
        } else {
            $pdf->SetTextColor(0, 0, 0);
        }

        $pdf->Cell(2.84 / 100 * $width, $height, number_format($arey[$tang.'k']), BRL, 0, 'R', 1);
        $total[$tang.'k'] += $arey[$tang.'k'];
        $totalk += $arey[$tang.'k'];
    }
    $pdf->Cell(4 / 100 * $width, $height, number_format($totalk), BRL, 0, 'R', 1);
    $pdf->Ln();
}
$pdf->Cell(8 / 100 * $width, $height, '', TRL, 0, 'C', 1);
$totalj = 0;
foreach ($tanggal as $tang) {
    $pdf->Cell(2.84 / 100 * $width, $height, number_format($total[$tang.'j']), TRL, 0, 'R', 1);
    $totalj += $total[$tang.'j'];
}
$pdf->Cell(4 / 100 * $width, $height, number_format($totalj), TRL, 0, 'R', 1);
$pdf->Ln();
$pdf->Cell(8 / 100 * $width, $height, 'Total', BRL, 0, 'C', 1);
$totalk = 0;
foreach ($tanggal as $tang) {
    $pdf->Cell(2.84 / 100 * $width, $height, number_format($total[$tang.'k']), BRL, 0, 'R', 1);
    $totalk += $total[$tang.'k'];
}
$pdf->Cell(4 / 100 * $width, $height, number_format($totalk), BRL, 0, 'R', 1);
$pdf->Ln();
$pdf->Output();

?>