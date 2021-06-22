<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdeOrg = (isset($_POST['kdeOrg']) ? $_POST['kdeOrg'] : '');
if ('' == $kdeOrg) {
    $kdeOrg = $_GET['kdeOrg'];
}

$kdOrg = (isset($_POST['kdOrg']) ? $_POST['kdOrg'] : '');
if ('' == $kdOrg) {
    $kdOrg = (isset($_GET['kdOrg']) ? $_GET['kdOrg'] : '');
}

$tgl1 = (isset($_POST['tgl1']) ? tanggalsystem($_POST['tgl1']) : '');
$tgl2 = (isset($_POST['tgl2']) ? tanggalsystem($_POST['tgl2']) : '');
if ('' == $tgl1) {
    $tgl1 = (isset($_GET['tgl1']) ? tanggalsystem($_GET['tgl1']) : '');
}

if ('' == $tgl2) {
    $tgl2 = (isset($_GET['tgl2']) ? tanggalsystem($_GET['tgl2']) : '');
}

$tgl_1 = (isset($_POST['tgl_1']) ? tanggalsystem($_POST['tgl_1']) : '');
$tgl_2 = (isset($_POST['tgl_2']) ? tanggalsystem($_POST['tgl_2']) : '');
if ('' == $tgl_1) {
    $tgl_1 = tanggalsystem($_GET['tgl_1']);
}

if ('' == $tgl_2) {
    $tgl_2 = tanggalsystem($_GET['tgl_2']);
}

$periode = (isset($_POST['period']) ? $_POST['period'] : '');
if ('' == $periode) {
    $periode = $_GET['period'];
}

$periodeGaji = $periode;
$periode = explode('-', $periode);
$kdUnit = (isset($_POST['kdUnit']) ? $_POST['kdUnit'] : '');
$pilihan = (isset($_POST['pilihan']) ? $_POST['pilihan'] : '');
if ('' == $pilihan) {
    $pilihan = (isset($_GET['pilihan']) ? $_GET['pilihan'] : '');
}

$pilihan2 = (isset($_POST['pilihan_2']) ? $_POST['pilihan_2'] : '');
if ('' == $pilihan2) {
    $pilihan2 = $_GET['pilihan_2'];
}

$pilihan3 = (isset($_POST['pilihan_3']) ? $_POST['pilihan_3'] : '');
if ('' == $pilihan3) {
    $pilihan3 = $_GET['pilihan_3'];
}

if (!$kdOrg) {
    $kdOrg = $_SESSION['empl']['lokasitugas'];
}

$optBagian = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optNikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$optDtJbtnr = makeOption($dbname, 'datakaryawan', 'karyawanid,kodejabatan');
$optDtBag = makeOption($dbname, 'datakaryawan', 'karyawanid,bagian');
$optDtTipe = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optDtSub = makeOption($dbname, 'datakaryawan', 'karyawanid,subbagian');
$arrTpLmbr = ['Normal', 'Minggu', 'Hari libur bukan minggu', 'Hari raya'];
if ('' != $tgl_1 && '' != $tgl_2) {
    $tgl1 = $tgl_1;
    $tgl2 = $tgl_2;
}

$test = dates_inbetween($tgl1, $tgl2);
$sOrg = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where kodeorganisasi ='".$kdeOrg."' ";
$qOrg = mysql_query($sOrg);
while ($rOrg = mysql_fetch_assoc($qOrg)) {
    $nmOrg = $rOrg['namaorganisasi'];
}
if (!$nmOrg) {
    $nmOrg = $kdOrg;
}

if ('' != $kdeOrg) {
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
        $where = " and lokasitugas = '".$kdeOrg."'";
        $where2 = " and substr(kodeorg,1,4)='".$kdeOrg."'";
    } else {
        if (4 < strlen($kdeOrg)) {
            $where = " and subbagian='".$kdeOrg."'";
            $where2 = " and kodeorg='".$kdeOrg."'";
        } else {
            $where = " and lokasitugas='".$kdeOrg."'";
            $where2 = " and substr(kodeorg,1,4)='".$kdeOrg."'";
        }
    }
} else {
    $kodeOrg = $_SESSION['empl']['lokasitugas'];
    $where = " and lokasitugas='".$kodeOrg."'";
}

if ('semua' == $pilihan2) {
    $where3 = '';
} else {
    if ('bulanan' == $pilihan2) {
        $where3 = " and a.sistemgaji = 'Bulanan' ";
    } else {
        if ('harian' == $pilihan2) {
            $where3 = " and a.sistemgaji = 'Harian' ";
        }
    }
}

if ('semua' == $pilihan3) {
    $where4 = '';
} else {
    $where4 = " and a.bagian = '".$pilihan3."' ";
}

$bgclr = ' ';
$brdr = 0;
if ($proses == 'excel') {
    $bgclr = ' bgcolor=#DEDEDE align=center';
    $brdr = 1;
}

if ($proses == 'excel' || $proses == 'preview' || $proses == 'pdf') {
    $sAbsensi = 'select distinct count(absensi) as jmlhhadir,absensi,karyawanid from '.$dbname.".sdm_absensidt_vw where nilaihk='H' and\r\n                   tanggal between '".$tgl_1."' and '".$tgl_2."' and substring(kodeorg,1,4)='".substr($kdeOrg, 0, 4)."' group by karyawanid,absensi";
    $qAbsensi = mysql_query($sAbsensi);
    while ($rAbsensi = mysql_fetch_assoc($qAbsensi)) {
        $jmlhHadir[$rAbsensi['karyawanid']] = $rAbsensi['jmlhhadir'];
    }
    $sKehadiran = 'select count(absensi) as jmlhhadir,karyawanid from '.$dbname.".kebun_kehadiran_vw \r\n                     where tanggal between  '".$tgl_1."' and '".$tgl_2."' and substring(unit,1,4)='".substr($kdeOrg, 0, 4)."'";
    $qKehadiran = mysql_query($sKehadiran);
    $jmlhHadir = [];
    while ($rKehadiran = mysql_fetch_assoc($qKehadiran)) {
        if (isset($jmlhHadir[$rKehadiran['karyawanid']])) {
            $jmlhHadir[$rKehadiran['karyawanid']] += $rKehadiran['jmlhhadir'];
        } else {
            $jmlhHadir[$rKehadiran['karyawanid']] = $rKehadiran['jmlhhadir'];
        }
    }
    $sPrestasi = 'select count(a.nik) as jmlhhadir,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                    where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kdeOrg, 0, 4)."' and b.tanggal between '".$tgl_1."' and '".$tgl_2."'\r\n                    group by a.nik";
    $qPrestasi = mysql_query($sPrestasi);
    while ($rPrestasi = mysql_fetch_assoc($qPrestasi)) {
        if (isset($jmlhHadir[$rPrestasi['nik']])) {
            $jmlhHadir[$rPrestasi['nik']] += $rPrestasi['jmlhhadir'];
        } else {
            $jmlhHadir[$rPrestasi['nik']] = $rPrestasi['jmlhhadir'];
        }
    }
    $sGetLembur = 'select jamaktual, jamlembur,tipelembur from '.$dbname.".sdm_5lembur where kodeorg = '".substr($kdeOrg, 0, 4)."'";
    $rGetLembur = fetchData($sGetLembur);
    foreach ($rGetLembur as $row => $kar) {
        $GetLembur[$kar['tipelembur'].$kar['jamaktual']] = $kar['jamlembur'];
    }
    $sLembur = 'select  uangkelebihanjam,a.karyawanid,jamaktual,tipelembur from '.$dbname.".sdm_lemburdt b\r\n                  LEFT JOIN ".$dbname.".datakaryawan a on a.karyawanid = b.karyawanid\r\n                  WHERE b.tanggal between  '".$tgl_1."' and '".$tgl_2."' ".$where2.' '.$where3.' '.$where4.' order by namakaryawan asc ';
    $qLembur = mysql_query($sLembur);
    $dtKaryawan = [];
    while ($rLembur = mysql_fetch_assoc($qLembur)) {
        if (isset($jlhJmLembur[$rLembur['karyawanid']])) {
            $jlhJmLembur[$rLembur['karyawanid']] = $GetLembur[$rLembur['tipelembur'].$rLembur['jamaktual']];
        } else {
            $jlhJmLembur[$rLembur['karyawanid']] += $GetLembur[$rLembur['tipelembur'].$rLembur['jamaktual']];
        }

        if (isset($jlhJamLemburKali[$rLembur['karyawanid']])) {
            $jlhJamLemburKali[$rLembur['karyawanid']] += $rLembur['jamaktual'];
        } else {
            $jlhJamLemburKali[$rLembur['karyawanid']] = $rLembur['jamaktual'];
        }

        if (isset($jlhUang[$rLembur['karyawanid']])) {
            $jlhUang[$rLembur['karyawanid']] += $rLembur['uangkelebihanjam'];
        } else {
            $jlhUang[$rLembur['karyawanid']] = $rLembur['uangkelebihanjam'];
        }

        $dtKaryawan[$rLembur['karyawanid']] = $rLembur['karyawanid'];
    }
    $tab = "<table cellspacing='1' border='".$brdr."' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td ".$bgclr.">No.</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['nik']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['nama']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['subbagian']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['bagian']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['jabatan']."</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['total'].' '.$_SESSION['lang']['absensi']."</td>\r\n\t\t<td ".$bgclr.'>'.$_SESSION['lang']['totLembur']." Actual</td>\r\n        <td ".$bgclr.'>'.$_SESSION['lang']['totLembur'].'</td>';
    if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2, 2) || 'HO' == substr($_SESSION['empl']['lokasitugas'], -2, 2)) {
        $tab .= '<td '.$bgclr.'>'.$_SESSION['lang']['jumlah'].' (Rp)</td>';
    }

    $tab .= '</tr><thead><tbody>';
    $no = 0;
    foreach ($dtKaryawan as $dtKary) {
        ++$no;
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$no.'</td>';
        $tab .= '<td>'.$optNikKar[$dtKary].'</td>';
        $tab .= '<td>'.$optNmKar[$dtKary].'</td>';
        $tab .= '<td>'.$optDtSub[$dtKary].'</td>';
        $tab .= '<td>'.$optTipe[$optDtTipe[$dtKary]].'</td>';
        if (isset($optBagian[$optDtBag[$dtKary]])) {
            $tab .= '<td>'.$optBagian[$optDtBag[$dtKary]].'</td>';
        } else {
            $tab .= '<td></td>';
        }

        $tab .= '<td>'.$optJabatan[$optDtJbtnr[$dtKary]].'</td>';
        if (isset($jmlhHadir[$dtKary])) {
            $tab .= '<td align=right>'.$jmlhHadir[$dtKary].'</td>';
        } else {
            $tab .= '<td></td>';
        }

        $tab .= '<td align=right>'.$jlhJamLemburKali[$dtKary].'</td>';
        $tab .= '<td align=right>'.$jlhJmLembur[$dtKary].'</td>';
        if ('KANWIL' == $_SESSION['empl']['tipelokasitugas'] || 'HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            $tab .= '<td align=right>'.number_format($jlhUang[$dtKary], 0).'</td>';
        }

        $tab .= '</tr>';
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        if ('' == $periodeGaji) {
            echo 'warning: Periode tidak boleh kosong';
            exit();
        }

        echo $tab;

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
        global $period;
        global $periode;
        global $kdOrg;
        global $kdeOrg;
        global $tgl1;
        global $tgl2;
        global $where;
        global $jmlHari;
        global $test;
        global $nmOrg;
        global $pilihan;
        global $pilihan2;
        $jmlHari = $jmlHari * 1.5;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
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
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['laporanLembur'].'  Total/'.$_SESSION['lang']['karyawan'].' (option '.$pilihan.') '.$pilihan2, '', 0, 'L');
        $this->Ln();
        $this->Cell($width, $height, strtoupper('Overtime Recapitulation').' : '.$nmOrg, '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2), '', 0, 'C');
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 'TLR', 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nama'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tipekaryawan'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['bagian'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['jabatan'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['total'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['totLembur'], 'TLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['totLembur'], 'TLR', 0, 'C', 1);
        if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2, 2) || 'HO' == substr($_SESSION['empl']['lokasitugas'], -2, 2)) {
            $this->Cell(10 / 100 * $width, $height, '', 'TLR', 1, 'C', 1);
        } else {
            $this->Ln();
        }

        $this->Cell(3 / 100 * $width, $height, ' ', 'BLR', 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, ' ', 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, ' ', 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, ' ', 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, ' ', 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['absensi'], 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, 'Actual', 'BLR', 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, '', 'BLR', 0, 'C', 1);
        if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2, 2) || 'HO' == substr($_SESSION['empl']['lokasitugas'], -2, 2)) {
            $this->Cell(10 / 100 * $width, $height, '(Rupiah)', 'BLR', 1, 'C', 1);
        } else {
            $this->Ln();
        }
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
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 6);
        $n3o = 0;
        foreach ($dtKaryawan as $dtKary) {
            ++$n3o;
            $pdf->Cell(3 / 100 * $width, $height, $n3o, 'TBLR', 0, 'C', 1);
            $pdf->Cell(15 / 100 * $width, $height, $optNmKar[$dtKary], 'TBLR', 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $optTipe[$optDtTipe[$dtKary]], 'TBLR', 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, (isset($optBagian[$optDtBag[$dtKary]]) ? $optBagian[$optDtBag[$dtKary]] : ''), 'TBLR', 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, $optJabatan[$optDtJbtnr[$dtKary]], 'TBLR', 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, (isset($jmlhHadir[$dtKary]) ? $jmlhHadir[$dtKary] : ''), 'TBLR', 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, $jlhJamLemburKali[$dtKary], 'TBLR', 0, 'R', 1);
            $pdf->Cell(10 / 100 * $width, $height, $jlhJmLembur[$dtKary], 'TBLR', 0, 'R', 1);
            if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2, 2) || 'HO' == substr($_SESSION['empl']['lokasitugas'], -2, 2)) {
                $pdf->Cell(10 / 100 * $width, $height, number_format($jlhUang[$dtKary], 0), 'TBLR', 1, 'R', 1);
            } else {
                $pdf->Ln();
            }
        }
        $pdf->Output();

        break;
    case 'excel':
        $wktu = date('Hms');
        $nop_ = 'RekapLembur_total_per_orang_'.$wktu.'__'.$kdeOrg;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getTgl':
        $add = '';
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
            }
        }

        if ('bulanan' == $pilihan2) {
            $add = " and jenisgaji='B'";
        }

        if ('harian' == $pilihan2) {
            $add = " and jenisgaji='H'";
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where \r\n            kodeorg='".substr($kdUnit, 0, 4)."' and periode='".$tanggal."' ".$add.'';
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
    case 'getPeriode':
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji  where kodeorg='".$kdOrg."'";
        $optPeriode = "<option value''>".$_SESSION['lang']['pilihdata'].'</option>';
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    default:
        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);

    return $dates_array;
}

?>