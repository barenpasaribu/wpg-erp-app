<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
('' == $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
('' == $_POST['periode'] ? ($periodeGaji = $_GET['periode']) : ($periodeGaji = $_POST['periode']));
$thn = explode('-', $periodeGaji);
('' == $_POST['tipeKary'] ? ($tipeKary = $_GET['tipeKary']) : ($tipeKary = $_POST['tipeKary']));
('' == $_GET['sistemGaji'] ? ($sistemGaji = $_POST['sistemGaji']) : ($sistemGaji = $_GET['sistemGaji']));
$namakar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$tpKar = makeOption($dbname, 'datakaryawan', 'karyawanid,tipekaryawan');
$nmTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$tglLahir = makeOption($dbname, 'datakaryawan', 'karyawanid,tanggallahir');
$dNik = makeOption($dbname, 'datakaryawan', 'karyawanid,nik');
$dJamsos = makeOption($dbname, 'datakaryawan', 'karyawanid,jms');
$wdtIbu = "hubungankeluarga='Ibu'";
$dtIbu = makeOption($dbname, 'sdm_karyawankeluarga', 'karyawanid,nama', $wdtIbu);
if ('' == $kdOrg) {
    $kdOrg = $lksiTgs;
}

if ('' != $tipeKary) {
    $where .= "  and tipekaryawan='".$tipeKary."'";
} else {
    $where .= " and tipekaryawan NOT IN ('5','6')";
}

if ('' != $sistemGaji) {
    $where .= " and sistemgaji='".$sistemGaji."'";
    $addTmbh = " and sistemgaji='".$sistemGaji."'";
}

$sGapok = 'select sum(jumlah) as gapok,karyawanid from '.$dbname.".sdm_5gajipokok \r\n         WHERE  idkomponen in (1,2,30,31) and tahun='".$thn[0]."' group by karyawanid order by karyawanid asc";
$qGapok = mysql_query($sGapok) || exit(mysql_error($sGapok));
while ($rGapok = mysql_fetch_assoc($qGapok)) {
    $dtGapok[$rGapok['karyawanid']] = $rGapok['gapok'];
}
$sJams = 'select distinct a.karyawanid,jumlah, b.lokasitugas,b.tipekaryawan,b.noktp from '.$dbname.".sdm_gaji a \r\n        left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n        where a.idkomponen='3' and a.kodeorg like '%".$kdOrg."%' and periodegaji='".$periodeGaji."'  ".$where.'';
$qJams = mysql_query($sJams);
while ($rJams = mysql_fetch_assoc($qJams)) {
    $dtJams[$rJams['karyawanid']] = $rJams['jumlah'];
    $data[$rJams['karyawanid']] = $rJams['karyawanid'];
    $resData[$rGapok['karyawanid']] = $kar['karyawanid'];
    $datalok[$rJams['karyawanid']] = $rJams['lokasitugas'];
    $datatk[$rJams['karyawanid']] = $rJams['tipekaryawan'];
    $datanoktp[$rJams['karyawanid']] = $rJams['noktp'];
}
$cekDt = count($data);
if (0 == $cekDt) {
    exit('Error:Not Found');
}

switch ($proses) {
    case 'preview':
        if ('' == $periodeGaji) {
            exit('Error:Period required');
        }

        $tab .= "<table cellspacing='1' border='0' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td>No</td>\r\n\t <td>".$_SESSION['lang']['nik']."</td>\r\n\t<td>".$_SESSION['lang']['nama']."</td>\r\n\t<td>".$_SESSION['lang']['noktp']."</td>\r\n\t<td>".$_SESSION['lang']['nokpj']."</td>\r\n\t\r\n\t\r\n\t<td>Nama Ibu</td>\r\n\t<td>".$_SESSION['lang']['lokasitugas']."</td>\r\n\t<td>".$_SESSION['lang']['tanggallahir']."</td>\r\n        <td>".$_SESSION['lang']['tipekaryawan']."</td>\r\n       \r\n        \r\n        <td>".$_SESSION['lang']['gaji']."</td>\r\n        <td>".$_SESSION['lang']['potongan'].' '.$_SESSION['lang']['karyawan']."</td>\r\n        <td>".$_SESSION['lang']['perusahaan']."</td>\r\n\t\t</tr></thead>\r\n\t<tbody>";
        foreach ($data as $brsData) {
            ++$no;
            $sDtip = 'select distinct tipekaryawan from '.$dbname.".datakaryawan where karyawanid='".$brsData."'";
            $qDtip = mysql_query($sDtip);
            $rDtip = mysql_fetch_assoc($qDtip);
            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n\t\t\t<td>".$dNik[$brsData]."</td>\r\n            <td>".$namakar[$brsData]."</td>\r\n\t\t\t<td>".$datanoktp[$brsData]."</td>\r\n\t\t\t<td>".$dJamsos[$brsData]."</td>\r\n\t\t\t\r\n\t\t\t<td>".$dtIbu[$brsData]."</td>\r\n            <td>".$datalok[$brsData]."</td>\r\n            <td>".$tglLahir[$brsData]."</td>  \r\n            <td>".$nmTipe[$datatk[$brsData]]."</td>\r\n            \r\n            \r\n            <td align=right>".number_format($dtGapok[$brsData], 2)."</td>\r\n            <td align=right>".number_format($dtJams[$brsData], 2)."</td>\r\n            <td align=right>".number_format(($dtGapok[$brsData] * 6.54) / 100, 2)."</td>\r\n\t\t\t\r\n\t\t\t\r\n\t\t\t\r\n            </tr>";
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'pdf':
        if ('' == $periodeGaji) {
            exit('Error:Period required');
        }

        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
            if ('' != $kdOrg) {
                $where = " lokasitugas!=''";
            } else {
                exit('Error:Working unit required');
            }
        } else {
            $kdOrg = $_SESSION['empl']['lokasitugas'];
            $where = " lokasitugas!=''";
        }

        if ('' != $tipeKary) {
            $where .= " and tipekaryawan='".$tipeKary."'";
        }

        if ('' != $sistemGaji) {
            $where .= " and sistemgaji='".$sistemGaji."'";
            $addTmbh = " and sistemgaji='".$sistemGaji."'";
        }

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
        global $periodeGaji;
        global $kdOrg;
        global $tglLahir;
        global $jmlHari;
        global $namakar;
        global $tipeKary;
        global $sistemGaji;
        global $nmTipe;
        global $dNik;
        global $dJamsos;
        global $addTmbh;
        global $resData;
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
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['dafJams'].' '.$sistemGaji, '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['dafJams']), '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.$periodeGaji, '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 6);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(20 / 100 * $width, $height, $_SESSION['lang']['namakaryawan'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tanggallahir'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['tipekaryawan'], 1, 0, 'C', 1);
        $this->Cell(6 / 100 * $width, $height, $_SESSION['lang']['nik'], 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nokpj'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['gaji'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, $_SESSION['lang']['potongan'].' '.$_SESSION['lang']['karyawan'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['perusahaan'], 1, 0, 'C', 1);
        $this->Cell(12 / 100 * $width, $height, 'Nama Ibu', 1, 1, 'C', 1);
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
        $subtot = [];
        foreach ($data as $brsData) {
            ++$no;
            $sDtip = 'select distinct tipekaryawan from '.$dbname.".datakaryawan where karyawanid='".$brsData."'";
            $qDtip = mysql_query($sDtip);
            $rDtip = mysql_fetch_assoc($qDtip);
            $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'L', 1);
            $pdf->Cell(20 / 100 * $width, $height, $namakar[$brsData], 1, 0, 'L', 1);
            $pdf->Cell(10 / 100 * $width, $height, tanggalnormal($tglLahir[$brsData]), 1, 0, 'C', 1);
            $pdf->Cell(10 / 100 * $width, $height, $nmTipe[$tpKar[$brsData]], 1, 0, 'L', 1);
            $pdf->Cell(6 / 100 * $width, $height, $dNik[$brsData], 1, 0, 'L', 1);
            $pdf->Cell(15 / 100 * $width, $height, $dJamsos[$brsData], 1, 0, 'L', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format($dtGapok[$brsData], 2), 1, 0, 'R', 1);
            $pdf->Cell(12 / 100 * $width, $height, number_format($dtJams[$brsData], 2), 1, 0, 'R', 1);
            $pdf->Cell(8 / 100 * $width, $height, number_format(($dtGapok[$brsData] * 6.54) / 100, 2), 1, 0, 'R', 1);
            $pdf->Cell(12 / 100 * $width, $height, $dtIbu[$brsData], 1, 1, 'C', 1);
        }
        $pdf->Output();

        break;
    case 'excel':
        $periodeGaji = $_GET['periode'];
        $tipeKary = $_GET['tipeKary'];
        $sistemGaji = $_GET['sistemGaji'];
        if ('' == $periodeGaji) {
            exit('Error:Period required');
        }

        $tab .= "<table cellspacing='1' border='1' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td bgcolor=#DEDEDE align=center>No</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggallahir']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nik']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nokpj']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['gaji']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['potongan'].' '.$_SESSION['lang']['karyawan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['perusahaan']."</td></tr></thead>\r\n\t<tbody>";
        foreach ($data as $brsData) {
            ++$no;
            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$namakar[$brsData]."</td>\r\n            <td>".$tglLahir[$brsData]."</td>  \r\n            <td>".$nmTipe[$rDtip['tipekaryawan']]."</td>\r\n            <td>".$dNik[$brsData]."</td>\r\n            <td>".$dJamsos[$brsData]."</td>\r\n            <td align=right>".number_format($dtGapok[$brsData], 2)."</td>\r\n            <td align=right>".number_format($dtJams[$brsData], 2)."</td>\r\n            <td align=right>".number_format(($dtGapok[$brsData] * 6.54) / 100, 2)."</td>\r\n            </tr>";
        }
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'daftar_jamsostek_'.$kdOrg;
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
                echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
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

?>