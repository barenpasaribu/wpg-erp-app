<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$periode = $_POST['periode'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdOrg'];
if (!$periode) {
    $periode = $_GET['periode'];
}

if (!$kdOrg) {
    $kdOrg = $_GET['kdOrg'];
}

if (!$kdOrg) {
    $kdOrg = $_SESSION['empl']['lokasitugas'];
}

('' == $_POST['tpKary'] ? ($tpKary = $_GET['tpKary']) : ($tpKary = $_POST['tpKary']));
$optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optOrg = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi ='".$kdOrg."' ";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $nmOrg = $rOrg['namaorganisasi'];
}
if (!$nmOrg) {
    $nmOrg = $kdOrg;
}

if ('' == $kdOrg) {
    if ('HOLDING' != $_SESSION['empl']['tipelokasitugas']) {
        $kodeOrg = $_SESSION['empl']['lokasitugas'];
        $where .= " and a.kodeorg like '%".$kodeOrg."%'";
        $where2 .= " and b.lokasitugas like '%".$kodeOrg."%'";
    } else {
        $sKebun = "select kodeorganisasi from organisasi where tipe in ('KEBUN','PABRIK','KANWIL','TRAKSI') ";
        $qKebun = mysql_query($sKebun);
        while ($rKebun = mysql_fetch_assoc($qKebun)) {
            $kodeOrg = "'".$rKebun['kodeorganisasi']."'";
            $kodeOrg .= ",'".$rKebun['kodeorganisasi']."'";
        }
        $where .= ' and a.kodeorg in('.$kodeOrg.')';
        $where2 .= ' and b.lokasitugas in('.$kodeOrg.')';
    }
} else {
    if (strlen($kdOrg) < 5) {
        $where = " and a.kodeorg = '".$kdOrg."'";
        $where2 = " and b.lokasitugas like '".$kdOrg."%'";
    } else {
        $where = " and a.kodeorg = '".$kdOrg."'";
        $where2 = " and b.subbagian like '".$kdOrg."%'";
    }
}

if ('' != $tpKary) {
    $where2 .= " and b.tipekaryawan='".$tpKary."'";
    $where .= " and b.tipekaryawan='".$tpKary."'";
} else {
    $where2 .= ' and b.tipekaryawan in (select distinct id from '.$dbname.'.sdm_5tipekaryawan where id!=0) ';
    $where .= ' and b.tipekaryawan in (select distinct id from '.$dbname.'.sdm_5tipekaryawan where id!=0)';
}

$strJ = 'select * from '.$dbname.'.sdm_5jabatan';
$resJ = mysql_query($strJ, $conn);
while ($barJ = mysql_fetch_object($resJ)) {
    $jab[$barJ->kodejabatan] = $barJ->namajabatan;
}
$sPeople = "SELECT b.namakaryawan as namakaryawan, b.kodejabatan as kodejabatan,b.tipekaryawan,b.subbagian,b.kodecatu\r\n                  , a.karyawanid as karyawanid,  jumlahhk, catuperhk, totalcatu,b.bagian\r\n                          FROM ".$dbname.".sdm_catu a\r\n                          LEFT JOIN ".$dbname.".datakaryawan b on a.karyawanid = b.karyawanid \r\n                          WHERE a.periodegaji='".$periode."' ".$where." \r\n                          ORDER BY a.karyawanid";
$query = mysql_query($sPeople);
while ($res = mysql_fetch_assoc($query)) {
    $dzArr[$res['karyawanid']][id] = $res['karyawanid'];
    $dzArr[$res['karyawanid']][id] = $res['karyawanid'];
    $dzArr[$res['karyawanid']][nm] = $res['namakaryawan'];
    $dzArr[$res['karyawanid']][jb] = $jab[$res['kodejabatan']];
    $dzArr[$res['karyawanid']][tk] = $optTipe[$res['tipekaryawan']];
    $dzArr[$res['karyawanid']][sb] = $res['bagian'];
    $dzArr[$res['karyawanid']][kc] = $res['kodecatu'];
    $dzArr[$res['karyawanid']][hk] = $res['jumlahhk'];
    $dzArr[$res['karyawanid']][jc] = $res['catuperhk'];
    $dzArr[$res['karyawanid']][tc] = $res['totalcatu'];
}
$cekdt = count($dzArr);
switch ($proses) {
    case 'preview':
        echo "<table cellspacing='1' border='0' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td>No</td>\r\n        <td>".$_SESSION['lang']['nama']."</td>\r\n        <td>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['bagian']."</td>\r\n        <td >Natura Code</td>\r\n        <td>".$_SESSION['lang']['jumlahhk']."</td>\r\n        <td colspan=2>".$_SESSION['lang']['jumlah'].' Natura/'.$_SESSION['lang']['hk']."</td>\r\n        <td colspan=2>".$_SESSION['lang']['jumlah'].' '.$_SESSION['lang']['diterima']."</td>\r\n        <td colspan=4>Tanda Terima</td>\r\n        </tr></thead>\r\n        <tbody>\r\n        ";
        $no = 0;
        if (0 != $cekdt) {
            foreach ($dzArr as $qwe) {
                ++$no;
                echo '<tr class=rowcontent><td>'.$no."</td>\r\n                <td>".$qwe['nm']."</td>\r\n                <td>".$qwe['tk']."</td>\r\n                <td>".$qwe['jb']."</td>\r\n                <td>".$optOrg[$qwe['sb']].'</td> <td>'.$qwe['kc'].'</td>';
                if (0 != $qwe['hk']) {
                    echo '<td align=right>'.number_format($qwe['hk']).'</td>';
                } else {
                    echo '<td align=right></td>';
                }

                if (0 != $qwe['jc']) {
                    echo '<td align=right>'.number_format($qwe['jc'], 2).'</td>';
                } else {
                    echo '<td align=right></td>';
                }

                echo '<td align=left>KG</td>';
                if (0 != $qwe['tc']) {
                    echo '<td align=right>'.number_format($qwe['tc'], 2).'</td>';
                } else {
                    echo '<td align=right></td>';
                }

                echo '<td align=left>KG</td>';
                if ($no % 2) {
                    echo '<td align=left colspan=2>'.$no.'.</td>';
                    echo '<td align=left colspan=2>&nbsp;</td>';
                } else {
                    echo '<td align=left  colspan=2>&nbsp;</td>';
                    echo '<td align=left  colspan=2>'.$no.'.</td>';
                }

                echo '</tr>';
            }
        } else {
            echo '<tr class=rowcontent><td colspan=15>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        echo '</tbody></table>';

        break;
    case 'excel':
        $stream .= "\r\n        <table border='0'>\r\n          <tr>\r\n          <td colspan='12' align=center>".strtoupper($_SESSION['lang']['laporanCatu']).' '.$nmOrg."</td></tr>\r\n        <tr>\r\n          <td colspan='12' align=center>".strtoupper($_SESSION['lang']['periode']).' : '.$periode." </td></tr>\r\n          <tr><td colspan='12'>&nbsp;</td></tr>\r\n        </table>";
        $stream .= "<table border='1'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n\r\n        <td bgcolor=#DEDEDE align=center>No</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['subbagian']."</td>\r\n        <td bgcolor=#DEDEDE align=center>Kode Catu</td>\r\n        <td bgcolor=#DEDEDE align=center>Jumlah HK</td>\r\n        <td bgcolor=#DEDEDE align=center colspan=2>Jatah Catu</td>\r\n        <td bgcolor=#DEDEDE align=center colspan=2>Jumlah Yang Diterima</td>\r\n        <td bgcolor=#DEDEDE align=center colspan=4>Tanda Terima</td></tr></thead>\r\n        <tbody>\r\n    ";
        $no = 0;
        if (0 != $cekdt) {
            foreach ($dzArr as $qwe) {
                ++$no;
                $stream .= '<tr class=rowcontent><td>'.$no."</td>\r\n                <td>".$qwe['nm']."</td>\r\n                <td>".$qwe['tk']."</td>\r\n                <td>".$qwe['jb']."</td>\r\n                <td>".$optOrg[$qwe['sb']].'</td> <td>'.$qwe['kc'].'</td>';
                if (0 != $qwe['hk']) {
                    $stream .= '<td align=right>'.number_format($qwe['hk'], 2).'</td>';
                } else {
                    $stream .= '<td align=right></td>';
                }

                if (0 != $qwe['jc']) {
                    $stream .= '<td align=right>'.number_format($qwe['jc'], 2).'</td>';
                } else {
                    $stream .= '<td align=right></td>';
                }

                $stream .= '<td align=left>KG</td>';
                if (0 != $qwe['tc']) {
                    $stream .= '<td align=right>'.number_format($qwe['tc'], 2).'</td>';
                } else {
                    $stream .= '<td align=right></td>';
                }

                $stream .= '<td align=left>KG</td>';
                if ($no % 2) {
                    $stream .= '<td align=left colspan=2>'.$no.'.</td>';
                    $stream .= '<td align=left colspan=2>&nbsp;</td>';
                } else {
                    $stream .= '<td align=left  colspan=2>&nbsp;</td>';
                    $stream .= '<td align=left  colspan=2>'.$no.'.</td>';
                }

                $stream .= '</tr>';
            }
        } else {
            $stream .= '<tr class=rowcontent><td colspan=15>'.$_SESSION['lang']['dataempty'].'</td></tr>';
        }

        $stream .= '</tbody>';
        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'LaporanPremi'.$periode.'__'.$kdOrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
    case 'pdf':

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
        global $periode;
        global $kdOrg;
        global $nmOrg;
        global $tanggalMulai;
        global $tanggalSampai;
        global $where;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 10;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['laporanPremi'], '', 0, 'L');
        $this->Ln();
        $this->Cell($width, $height, strtoupper('Laporan Premi Karyawan: '.$nmOrg), '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.$tanggalMulai.' s/d '.$tanggalSampai, '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', '', 5);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
        $this->Cell(13 / 100 * $width, $height, $_SESSION['lang']['jabatan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Hasil PNN', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Norma', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Premi PNN', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Premi PRW', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Premi Trak', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Pngawsan', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Kemandoran PNN', 1, 0, 'L', 1);
        $this->Cell(7 / 100 * $width, $height, 'Pnalty PNN', 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, 'Pnalty Trak', 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, 'Total (Rp)', 1, 0, 'C', 1);
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
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $no = 0;
        foreach ($dzArr as $qwe) {
            if (0 < $qwe['tt']) {
                ++$no;
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', l);
                $pdf->Cell(15 / 100 * $width, $height, $qwe['nm'], 1, 0, 'L', 1);
                $pdf->Cell(13 / 100 * $width, $height, $qwe['jb'], 1, 0, 'L', 1);
                if (0 != $qwe['hk']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['hk']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['nr']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['nr']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['up']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['up']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['in']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['in']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['pr']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['pr']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['jm']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['jm']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['km']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['km']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['rp']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['rp']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                if (0 != $qwe['pe']) {
                    $pdf->Cell(7 / 100 * $width, $height, number_format($qwe['pe']), 1, 0, 'R', 1);
                } else {
                    $pdf->Cell(7 / 100 * $width, $height, '', 1, 0, 'R', 1);
                }

                $pdf->Cell(8 / 100 * $width, $height, number_format($qwe['tt']), 1, 0, 'R', 1);
                $pdf->Ln();
            }
        }
        $pdf->Cell(31 / 100 * $width, $height, 'TOTAL', 1, 0, 'C', l);
        $pdf->Cell(7 / 100 * $width, $height, number_format($thk), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tnr), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tup), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tin), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tpr), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tjm), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tkm), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($trp), 1, 0, 'R', 1);
        $pdf->Cell(7 / 100 * $width, $height, number_format($tpe), 1, 0, 'R', 1);
        $pdf->Cell(8 / 100 * $width, $height, number_format($ttt), 1, 0, 'R', 1);
        $pdf->Ln();
        $pdf->Output();

        break;
    default:
        break;
}

?>