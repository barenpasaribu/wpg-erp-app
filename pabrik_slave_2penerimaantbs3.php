<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$kdPabrik = $_POST['kdPabrik__3'];
$kdUnit = $_POST['kdUnit__3'];
$periode = $_POST['periode__3'];
switch ($proses) {
    case 'preview':
        if ('' === $periode) {
            echo 'Warning: Period required';
            exit();
        }

        if ('' === $kdUnit) {
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$isi['kodeorg']][$tanggal] += $isi['beratbersih'];
                    $kolom[$isi['kodeorg']] = $isi['kodeorg'];
                }
            }
        } else {
            $qweri = 'select kodeorganisasi from '.$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
            $datanya = mysql_query($qweri);
            while ($isi = mysql_fetch_assoc($datanya)) {
                $kolom[$isi['kodeorganisasi']] = $isi['kodeorganisasi'];
            }
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $qwe = explode('/', $isi['nospb']);
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$qwe[1]][$tanggal] += $isi['beratbersih'];
                }
            }
        }

        $anyDate = $periode.'-25';
        list($yr, $mn, $dt) = preg_split('/-/D', $anyDate);
        $timeStamp = mktime(0, 0, 0, $mn, 1, $yr);
        list($y, $m, $t) = preg_split('/-/D', date('Y-m-t', $timeStamp));
        $lastDayTimeStamp = mktime(0, 0, 0, $m, $t, $y);
        $lastDate = date('d', $lastDayTimeStamp);
        sort($kolom);
        echo $_SESSION['lang']['rPenerimaanTbs'].'/'.$_SESSION['lang']['afdeling'].'/'.$_SESSION['lang']['bulan'].' (berat sebelum potongan sortasi/before grading deduction)';
        if (empty($isinya)) {
            echo '<br><br>Tidak ada data.';
        } else {
            echo "<table cellspacing=1 border=0 class=sortable>\r\n        <thead class=rowheader>\r\n        <tr>\r\n            <td align=center>".$_SESSION['lang']['tanggal'].'</td>';
            foreach ($kolom as $kol) {
                echo '<td align=center>'.$kol.'</td>';
            }
            echo '<td align=center>'.$_SESSION['lang']['total']."</td></tr>\r\n        </thead>\r\n        <tbody>";
            $asd = explode('-', $periode);
            list($tahun, $bulan) = $asd;
            $totalnya = [];
            for ($i = 1; $i <= $lastDate; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $hari = date('D', mktime(0, 0, 0, $bulan, $i, $tahun));
                if ('Sun' === $hari) {
                    $class = 'class=rowheader';
                } else {
                    $class = 'class=rowcontent';
                }

                echo '<tr '.$class.'><td align=center>'.$ii.'</td>';
                $total = 0;
                foreach ($kolom as $kol) {
                    $bgwarna = '';
                    $scek = 'select distinct * from '.$dbname.".kebun_spb_vw where\r\n                       left(blok,6)='".$kol."' and tanggal='".$periode.'-'.$ii."' \r\n                       and substr(nospb,9,6)<>left(blok,6)";
                    $qcek = mysql_query($scek);
                    $rcek = mysql_num_rows($qcek);
                    if (1 === $rcek) {
                        $bgwarna = "bgcolor=yellow title='Ada Buah Dari Afd Lain'";
                    }

                    echo '<td align=right '.$bgwarna.'>'.number_format($isinya[$kol][$ii]).'</td>';
                    $totalnya[$kol] += $isinya[$kol][$ii];
                    $total += $isinya[$kol][$ii];
                }
                echo '<td align=right>'.number_format($total).'</td>';
                echo '</tr>';
            }
            echo '<tr class=rowheader><td align=center>'.$_SESSION['lang']['total'].'</td>';
            $total = 0;
            foreach ($kolom as $kol) {
                echo '<td align=right>'.number_format($totalnya[$kol]).'</td>';
                $total += $totalnya[$kol];
            }
            echo '<td align=right>'.number_format($total).'</td>';
            echo '</tr></tbody></table>';
        }

        break;
    case 'pdf':
        $kdPabrik = $_GET['kdPabrik__3'];
        $kdUnit = $_GET['kdUnit__3'];
        $periode = $_GET['periode__3'];

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        global $kdPabrik;
        global $kdUnit;
        global $periode;
        $sAlmat = 'select namaorganisasi,alamat,telepon from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
        $qAlamat = mysql_query($sAlmat);
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
        $this->Cell($width, $height, $_SESSION['lang']['rPenerimaanTbs'].' / '.$_SESSION['lang']['afdeling'].' / '.$_SESSION['lang']['bulan'], 0, 1, 'C');
        $this->Cell($width, $height, $kdPabrik.' '.$periode, 0, 1, 'C');
        $this->SetFont('Arial', 'B', 7);
        if ('' === $kdUnit) {
            $kdUnitz = $_SESSION['lang']['all'];
        } else {
            $kdUnitz = $kdUnit;
        }

        $this->Cell(50, $height, $_SESSION['lang']['unit'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(350, $height, $kdUnitz, '', 0, 'L');
        $this->Cell(50, $height, 'Printed By', '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(350, $height, $_SESSION['empl']['name'], '', 1, 'L');
        $this->Cell(50, $height, '', '', 0, 'L');
        $this->Cell(5, $height, '', '', 0, 'L');
        $this->Cell(350, $height, '', '', 0, 'L');
        $this->Cell(50, $height, $_SESSION['lang']['tanggal'], '', 0, 'L');
        $this->Cell(5, $height, ':', '', 0, 'L');
        $this->Cell(350, $height, date('d-m-Y H:i:s'), '', 1, 'L');
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        if ('' === $kdUnit) {
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $kolom[$isi['kodeorg']] = $isi['kodeorg'];
                }
            }
        } else {
            $qweri = 'select kodeorganisasi from '.$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
            $datanya = mysql_query($qweri);
            while ($isi = mysql_fetch_assoc($datanya)) {
                $kolom[$isi['kodeorganisasi']] = $isi['kodeorganisasi'];
            }
        }

        sort($kolom);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
        foreach ($kolom as $kol) {
            $this->Cell(10 / 100 * $width, $height, $kol, 1, 0, 'C', 1);
        }
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'pt', 'A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        if ('' === $periode) {
            echo 'Warning: Period required';
            exit();
        }

        if ('' === $kdUnit) {
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$isi['kodeorg']][$tanggal] += $isi['beratbersih'];
                    $kolom[$isi['kodeorg']] = $isi['kodeorg'];
                }
            }
        } else {
            $qweri = 'select kodeorganisasi from '.$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
            $datanya = mysql_query($qweri);
            while ($isi = mysql_fetch_assoc($datanya)) {
                $kolom[$isi['kodeorganisasi']] = $isi['kodeorganisasi'];
            }
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $qwe = explode('/', $isi['nospb']);
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$qwe[1]][$tanggal] += $isi['beratbersih'];
                }
            }
        }

        $anyDate = $periode.'-25';
        list($yr, $mn, $dt) = preg_split('/-/D', $anyDate);
        $timeStamp = mktime(0, 0, 0, $mn, 1, $yr);
        list($y, $m, $t) = preg_split('/-/D', date('Y-m-t', $timeStamp));
        $lastDayTimeStamp = mktime(0, 0, 0, $m, $t, $y);
        $lastDate = date('d', $lastDayTimeStamp);
        sort($kolom);
        if (empty($isinya)) {
            echo '<br><br>Tidak ada data.';
        } else {
            $asd = explode('-', $periode);
            list($tahun, $bulan) = $asd;
            $totalnya = [];
            for ($i = 1; $i <= $lastDate; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $hari = date('D', mktime(0, 0, 0, $bulan, $i, $tahun));
                if ('Sun' === $hari) {
                    $pdf->SetFillColor(255, 192, 192);
                }

                $pdf->Cell(10 / 100 * $width, $height, $ii, 1, 0, 'C', 1);
                $total = 0;
                foreach ($kolom as $kol) {
                    $pdf->Cell(10 / 100 * $width, $height, number_format($isinya[$kol][$ii]), 1, 0, 'R', 1);
                    $totalnya[$kol] += $isinya[$kol][$ii];
                    $total += $isinya[$kol][$ii];
                }
                $pdf->Cell(10 / 100 * $width, $height, number_format($total), 1, 0, 'R', 1);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Ln();
            }
            $pdf->Cell(10 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 0, 'C', 1);
            $total = 0;
            foreach ($kolom as $kol) {
                $pdf->Cell(10 / 100 * $width, $height, number_format($totalnya[$kol]), 1, 0, 'R', 1);
                $total += $totalnya[$kol];
            }
            $pdf->Cell(10 / 100 * $width, $height, number_format($total), 1, 0, 'R', 1);
            $pdf->Ln();
        }

        $pdf->Cell(100, 10, 'Belum termasuk potongan sortasi / not include grading deduction', 0, 0, 'L', 1);
        $pdf->Output();

        break;
    case 'excel':
        $kdPabrik = $_GET['kdPabrik__3'];
        $kdUnit = $_GET['kdUnit__3'];
        $periode = $_GET['periode__3'];
        if ('' === $periode) {
            echo 'Warning: Period required.';
            exit();
        }

        $tab = $_SESSION['lang']['rPenerimaanTbs'].'/'.$_SESSION['lang']['afdeling'].'/'.$_SESSION['lang']['bulan']." (berat sebelum potongan sortasi)<br>\r\n        Periode: ".$periode;
        if ($kdPabrik) {
            $tab .= '<br>Pabrik: '.$kdPabrik;
        }

        if ($kdUnit) {
            $tab .= '<br>Unit: '.$kdUnit;
        }

        if ('' === $kdUnit) {
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$isi['kodeorg']][$tanggal] += $isi['beratbersih'];
                    $kolom[$isi['kodeorg']] = $isi['kodeorg'];
                }
            }
        } else {
            $qweri = 'select kodeorganisasi from '.$dbname.".organisasi where induk like '%".$kdUnit."%' and tipe = 'AFDELING'";
            $datanya = mysql_query($qweri);
            while ($isi = mysql_fetch_assoc($datanya)) {
                $kolom[$isi['kodeorganisasi']] = $isi['kodeorganisasi'];
            }
            $qweri = 'select tanggal, kodeorg, nospb, (beratbersih-kgpotsortasi) as beratbersih from '.$dbname.".pabrik_timbangan where kodeorg != '' and kodeorg like '%".$kdUnit."%' and millcode like '%".$kdPabrik."%' and tanggal like '".$periode."%'";
            $datanya = mysql_query($qweri);
            $barisnya = mysql_num_rows($datanya);
            if (0 < $barisnya) {
                while ($isi = mysql_fetch_assoc($datanya)) {
                    $qwe = explode('/', $isi['nospb']);
                    $tanggal = substr($isi['tanggal'], 8, 2);
                    $isinya[$qwe[1]][$tanggal] += $isi['beratbersih'];
                }
            }
        }

        $anyDate = $periode.'-25';
        list($yr, $mn, $dt) = preg_split('/-/D', $anyDate);
        $timeStamp = mktime(0, 0, 0, $mn, 1, $yr);
        list($y, $m, $t) = preg_split('/-/D', date('Y-m-t', $timeStamp));
        $lastDayTimeStamp = mktime(0, 0, 0, $m, $t, $y);
        $lastDate = date('d', $lastDayTimeStamp);
        sort($kolom);
        if (empty($isinya)) {
            $tab .= '<br><br>Tidak ada data.';
        } else {
            $tab .= "<table cellspacing=1 border=1 class=sortable>\r\n        <thead>\r\n        <tr bgcolor=#dedede>\r\n            <td align=center>".$_SESSION['lang']['tanggal'].'</td>';
            foreach ($kolom as $kol) {
                $tab .= '<td align=center>'.$kol.'</td>';
            }
            $tab .= '<td align=center>'.$_SESSION['lang']['total']."</td></tr>\r\n        </thead>\r\n        <tbody>";
            $asd = explode('-', $periode);
            list($tahun, $bulan) = $asd;
            $totalnya = [];
            for ($i = 1; $i <= $lastDate; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $hari = date('D', mktime(0, 0, 0, $bulan, $i, $tahun));
                if ('Sun' === $hari) {
                    $bgcolor = 'bgcolor=#FFAAAA';
                } else {
                    $bgcolor = '';
                }

                $tab .= '<tr '.$bgcolor.'><td align=center>'.$ii.'</td>';
                $total = 0;
                foreach ($kolom as $kol) {
                    $scek = 'select distinct * from '.$dbname.".kebun_spb_vw where\r\n                       left(blok,6)='".$kol."' and tanggal='".$periode.'-'.$ii."' \r\n                       and substr(nospb,9,6)<>left(blok,6)";
                    $qcek = mysql_query($scek);
                    $rcek = mysql_num_rows($qcek);
                    if (1 === $rcek) {
                        $bgwarna = 'bgcolor=yellow';
                    }

                    $tab .= '<td align=right '.$bgwarna.'>'.number_format($isinya[$kol][$ii]).'</td>';
                    $totalnya[$kol] += $isinya[$kol][$ii];
                    $total += $isinya[$kol][$ii];
                }
                $tab .= '<td align=right>'.number_format($total).'</td>';
                $tab .= '</tr>';
            }
            $tab .= '<tr bgcolor=#dedede><td align=center>'.$_SESSION['lang']['total'].'</td>';
            $total = 0;
            foreach ($kolom as $kol) {
                $tab .= '<td align=right>'.number_format($totalnya[$kol]).'</td>';
                $total += $totalnya[$kol];
            }
            $tab .= '<td align=right>'.number_format($total).'</td>';
            $tab .= '</tr>';
            $tab .= '</tbody></table>';
        }

        $tab .= '</tbody></table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $qwe = date('YmdHms');
        $nop_ = 'LaporanPenerimaanTbs3'.$tglSkrg.'__'.$qwe;
        if (0 < strlen($tab)) {
            $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
            gzwrite($gztralala, $tab);
            gzclose($gztralala);
            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";
        }

        break;
    default:
        break;
}

?>