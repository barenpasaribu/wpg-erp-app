<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$lksiTgs = $_SESSION['empl']['lokasitugas'];
('' == $_POST['unitId'] ? ($kdOrg = $_GET['unitId']) : ($kdOrg = $_POST['unitId']));
('' == $_POST['tglDari'] ? ($tglDari = tanggalsystem($_GET['tglDari'])) : ($tglDari = tanggalsystem($_POST['tglDari'])));
('' == $_POST['tglSmp'] ? ($tglSmp = tanggalsystem($_GET['tglSmp'])) : ($tglSmp = tanggalsystem($_POST['tglSmp'])));
('' == $_GET['proses'] ? ($proses = $_POST['proses']) : ($proses = $_GET['proses']));
$optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$optTipeId = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
if ('' == $kdOrg) {
    exit('Error: Working unit required');
}

if ('' == $tglDari || '' == $tglSmp) {
    exit('Error: Date required');
}

$test = dates_inbetween($tglDari, $tglSmp);
$jmlhHari = count($test);
$sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
$qAbsen = mysql_query($sAbsen);
$sData = 'select distinct sum(umr+insentif) as gaji,nik from '.$dbname.".kebun_kehadiran as a left join\r\n        ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where b.kodeorg = '".$kdOrg."'   and jurnal=1\r\n        and b.tanggal between '".$tglDari."' and '".$tglSmp."'  group by nik";
$qData = mysql_query($sData);
while ($rData = mysql_fetch_assoc($qData)) {
    $dtGaji[$rData['nik']] += $rData['gaji'];
    $dtKary[] = $rData['nik'];
}
$sData2 = 'select distinct nik,absensi,tanggal from '.$dbname.".kebun_kehadiran as a left join\r\n        ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where b.kodeorg = '".$kdOrg."' and jurnal=1\r\n        and b.tanggal between '".$tglDari."' and '".$tglSmp."'";
$qData2 = mysql_query($sData2);
while ($rData2 = mysql_fetch_assoc($qData2)) {
    $dtAbsens[$rData2['nik']][$rData2['tanggal']] = $rData2['absensi'];
}
$allDataKary = count($dtKary);
if (0 == $allDataKary) {
    exit('Error: Not found');
}

$brd = 0;
if ('excel' == $proses) {
    $bgDt = 'bgcolor=#DEDEDE align=center';
    $brd = 1;
}

$tab .= "<table cellspacing='1' border='".$brd."' class='sortable'>\r\n    <thead class=rowheader>\r\n    <tr>\r\n    <td ".$bgDt.">No</td>\r\n    <td ".$bgDt.'>'.$_SESSION['lang']['nama']."</td>\r\n    <td ".$bgDt.'>'.$_SESSION['lang']['tipekaryawan']."</td>\r\n    <td ".$bgDt.'>'.$_SESSION['lang']['gaji'].'</td>';
foreach ($test as $ar => $isi) {
    $qwe = date('D', strtotime($isi));
    $tab .= '<td width=5px  '.$bgDt.'>';
    if ('Sun' == $qwe) {
        $tab .= '<font color=red>'.substr($isi, 8, 2).'</font>';
    } else {
        $tab .= substr($isi, 8, 2);
    }

    $tab .= '</td>';
}
while ($rKet = mysql_fetch_assoc($qAbsen)) {
    $klmpkAbsn[] = $rKet;
    $tab .= '<td width=10px  '.$bgDt.'>'.$rKet['kodeabsen'].'</td>';
}
$cold = count($klmpkAbsn);
$colspandt = $cold + $jmlhHari + 1;
$tab .= "\r\n    <td ".$bgDt.">Total</td>\r\n    </tr></thead>\r\n    <tbody>";
foreach ($dtKary as $lstKary) {
    ++$no;
    $tab .= '<tr class=rowcontent>';
    $tab .= '<td>'.$no.'</td>';
    $tab .= '<td>'.$optNmKar[$lstKary].'</td>';
    $tab .= '<td>'.$optTipe[$optTipeId[$lstKary]].'</td>';
    $tab .= '<td align=right>'.number_format($dtGaji[$lstKary], 0).'</td>';
    $totGaji += $dtGaji[$lstKary];
    foreach ($test as $barisTgl => $isiTgl) {
        $tab .= '<td>'.$dtAbsens[$lstKary][$isiTgl].'</td>';
        ++$brt[$lstKary][$dtAbsens[$lstKary][$isiTgl]];
    }
    foreach ($klmpkAbsn as $brsKet => $hslKet) {
        $tab .= '<td width=5px>'.$brt[$lstKary][$hslKet['kodeabsen']].'</td>';
        $subtot[$lstKary]['total'] += $brt[$lstKary][$hslKet['kodeabsen']];
        $totPerAbsen[$hslKet['kodeabsen']] += $brt[$lstKary][$hslKet['kodeabsen']];
    }
    $tab .= '<td width=5px align=right>'.$subtot[$lstKary]['total'].'</td>';
    $subtot['total'] = 0;
    $tab .= '</tr>';
}
$tab .= '<tr class=rowcontent><td colspan=3>Total</td><td align=right>'.number_format($totGaji, 0).'</td>';
$tab .= '<td colspan='.$colspandt.'>&nbsp;</td>';
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
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
        global $jmlHari;
        global $test;
        global $klmpkAbsn;
        global $tglDari;
        global $tglSmp;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
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
        $this->Cell($width, $height, strtoupper('Payroll Remise I'), '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['tanggal']).' :'.tanggalnormal($tglDari).' '.$_SESSION['lang']['sampai'].' '.tanggalnormal($tglSmp), '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(13 / 100 * $width, $height, $_SESSION['lang']['namakaryawan'], 1, 0, 'C', 1);
        $this->Cell(7 / 100 * $width, $height, $_SESSION['lang']['tipekaryawan'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['gaji'], 1, 0, 'C', 1);
        foreach ($test as $ar => $isi) {
            $this->Cell(1.5 / 100 * $width, $height, substr($isi, 8, 2), 1, 0, 'C', 1);
            $akhirX = $this->GetX();
        }
        $this->SetY($this->GetY());
        $this->SetX($akhirX);
        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        while ($rAbsen = mysql_fetch_assoc($qAbsen)) {
            $this->Cell(2 / 100 * $width, $height, $rAbsen['kodeabsen'], 1, 0, 'C', 1);
        }
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
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
        $pdf->SetFont('Arial', '', 7);
        $subtot = [];
        foreach ($dtKary as $lstKary) {
            ++$noe;
            $pdf->Cell(3 / 100 * $width, $height, $noe, 1, 0, 'C', 1);
            $pdf->Cell(13 / 100 * $width, $height, $optNmKar[$lstKary], 1, 0, 'L', 1);
            $pdf->Cell(7 / 100 * $width, $height, $optTipe[$optTipeId[$lstKary]], 1, 0, 'C', 1);
            $pdf->Cell(10 / 100 * $width, $height, number_format($dtGaji[$lstKary], 0), 1, 0, 'R', 1);
            $totGaji += $dtGaji[$lstKary];
            foreach ($test as $barisTgl => $isiTgl) {
                $pdf->Cell(1.5 / 100 * $width, $height, $dtAbsens[$lstKary][$isiTgl], 1, 0, 'C', 1);
                $akhirX = $pdf->GetX();
            }
            foreach ($klmpkAbsn as $brsKet => $hslKet) {
                $pdf->Cell(2 / 100 * $width, $height, $brt[$lstKary][$hslKet['kodeabsen']], 1, 0, 'C', 1);
                $subtot[$lstKary]['total'] += $brt[$lstKary][$hslKet['kodeabsen']];
            }
            $pdf->Cell(5 / 100 * $width, $height, $subtot[$lstKary]['total'], 1, 1, 'R', 1);
        }
        $pdf->Output();

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dt = date('His');
        $nop_ = 'daftar_remise_'.$dt;
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
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
    case 'getTgl':
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
            }
        }

        if ('' == $kdUnit) {
            $kdUnit = $_SESSION['empl']['lokasitugas'];
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit, 0, 4)."' and periode='".$tanggal."' ";
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
    case 'getKry':
        $optKry = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if (4 < strlen($kdeOrg)) {
            $where = " subbagian='".$kdeOrg."'";
        } else {
            $where = " lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null)";
        }

        $sKry = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
        }
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
        }
        echo $optKry.'###'.$optPeriode;

        break;
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$kdUnit."'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
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