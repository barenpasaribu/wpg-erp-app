<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' == $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' == $_POST['periodegaji'] ? ($periode = $_GET['periodegaji']) : ($periode = $_POST['periodegaji']));
('' == $_POST['period'] ? ($period = $_GET['period']) : ($period = $_POST['period']));
('' == $_POST['jenis'] ? ($jenis = $_GET['jenis']) : ($jenis = $_POST['jenis']));
('' == $_POST['jnsGaji'] ? ($jnsGaji = $_GET['jnsGaji']) : ($jnsGaji = $_POST['jnsGaji']));
('' == $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
$tahunlalu = $periode - 1;
$tgltahunlalu = $tahunlalu.'-12-31';
$re = [28 => 'THR', 26 => 'Bonus'];
$optTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$sPeriode = 'select distinct periodegaji from '.$dbname.".sdm_gaji \r\n           where substr(periodegaji,1,4)='".$periode."' and kodeorg='".$kdOrg."' and idkomponen='".$jenis."'";
$qPeriode = mysql_query($sPeriode);
$rPeriode = mysql_fetch_assoc($qPeriode);
$sSlip = "select distinct a.*,COALESCE(ROUND(DATEDIFF('".$tgltahunlalu."',b.tanggalmasuk)/365.25,3),0) as masakerja,b.norekeningbank,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian from \r\n               ".$dbname.".sdm_gaji_vw a  \r\n               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid \r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan \r\n               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode\r\n               where b.sistemgaji='".$jnsGaji."' and a.periodegaji='".$rPeriode['periodegaji']."' and b.lokasitugas='".$kdOrg."' and idkomponen='".$jenis."'";
$sGaji2 = 'select karyawanid,idkomponen,sum(jumlah) as jumlah from '.$dbname.".sdm_5gajipokok where\r\n    idkomponen in (select distinct id from ".$dbname.".sdm_ho_component where plus=1) and tahun='".$tahunlalu."'\r\n        group by karyawanid";
$rGaji2 = mysql_query($sGaji2);
while ($bar = mysql_fetch_object($rGaji2)) {
    $kamusgaji[$bar->karyawanid] = $bar->jumlah;
}
$sPengali = 'select kodeorg, karyawanid, pengali from '.$dbname.".sdm_gaji where\r\n    periodegaji='".$rPeriode['periodegaji']."' and idkomponen='".$jenis."' and kodeorg='".$kdOrg."'";
$rPengali = mysql_query($sPengali);
while ($bar = mysql_fetch_object($rPengali)) {
    $kamuspengali[$bar->karyawanid] = $bar->pengali;
}
switch ($proses) {
    case 'preview':
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr><td>No</td><td>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['norekeningbank'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['subbagian'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tmk'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['masakerja'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['bagian'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['tipekaryawan'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['statuspajak'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['functionname'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['gaji'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['pengali'].'</td>';
        $tab .= '<td>'.$_SESSION['lang']['jumlah'].' Bonus/THR</td></tr></thead><tbody>';
        $qSlip = mysql_query($sSlip);
        $rRow = mysql_num_rows($qSlip);
        if (0 == $rRow) {
            exit('Error:Not found');
        }

        while ($rSlip = mysql_fetch_assoc($qSlip)) {
            ++$no;
            $tab .= '<tr class=rowcontent><td>'.$no.'</td><td>'.$rSlip['namakaryawan'].'</td>';
            $tab .= '<td>'.$rSlip['norekeningbank'].'</td>';
            $tab .= '<td>'.$rSlip['nik'].'</td>';
            $tab .= '<td>'.$rSlip['subbagian'].'</td>';
            $tab .= '<td>'.$rSlip['tanggalmasuk'].'</td>';
            $tab .= '<td align=right>'.number_format($rSlip['masakerja'], 3).'</td>';
            $tab .= '<td>'.$rSlip['bagian'].'</td>';
            $tab .= '<td>'.$optTipe[$rSlip['tipekaryawan']].'</td>';
            $tab .= '<td>'.$rSlip['statuspajak'].'</td>';
            $tab .= '<td>'.$rSlip['namajabatan'].'</td>';
            $tab .= '<td align=right>'.number_format($kamusgaji[$rSlip['karyawanid']], 0).'</td>';
            $tab .= '<td align=right>'.number_format($kamuspengali[$rSlip['karyawanid']], 3).'</td>';
            $tab .= '<td align=right>'.number_format($rSlip['jumlah'], 0).'</td></tr>';
        }
        $tab .= '</tbody></table>';
        echo $tab;

        break;
    case 'pdf':

class PDF extends FPDF
{
    public $col = 0;
    public $dbname;

    public function SetCol($col)
    {
        $this->col = $col;
        $x = 10 + $col * 100;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    public function AcceptPageBreak()
    {
        if ($this->col < 1) {
            $this->SetCol($this->col + 1);
            $this->SetY(10);

            return false;
        }

        $this->SetCol(0);

        return true;
    }

    public function Header()
    {
        $this->lMargin = 5;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 5);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 5);
        $bln = explode('-', $perod);
        $idBln = (int) ($bln[1]);
        $qSlip = mysql_query($sSlip);
        $rRow = mysql_num_rows($qSlip);
        if (0 == $rRow) {
            exit('Error:Not found');
        }

        while ($rSlip = mysql_fetch_assoc($qSlip)) {
            if ('' == $rSlip['subbagian'] || null == $rSlip['subbagian']) {
                $rSlip['subbagian'] = $rSlip['kodeorg'];
            }

            $pdf->Image('images/logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
            $pdf->SetX($pdf->getX() + 10);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(75, 6, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(71, 4, $_SESSION['lang']['slipGaji'].': '.$rPeriode['periodegaji'], 'T', 0, 'L');
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell(25, 4, 'Printed on: '.date('d-m-Y: H:i:s'), 'T', 1, 'R');
            $pdf->SetFont('Arial', '', 6);
            $pdf->Cell(15, 4, $_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'], 0, 0, 'L');
            $pdf->Cell(35, 4, ': '.$rSlip['nik'].'/'.tanggalnormal($rSlip['tanggalmasuk']), 0, 0, 'L');
            $pdf->Cell(18, 4, $_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian'], 0, 0, 'L');
            $pdf->Cell(28, 4, ': '.$rSlip['subbagian'].' / '.$rSlip['bagian'], 0, 1, 'L');
            $pdf->Cell(15, 4, $_SESSION['lang']['namakaryawan'].':', 0, 0, 'L');
            $pdf->Cell(35, 4, ': '.$rSlip['namakaryawan'], 0, 0, 'L');
            $pdf->Cell(18, 3, $_SESSION['lang']['jabatan'], 0, 0, 'L');
            $pdf->Cell(28, 4, ':'.$rSlip['namajabatan'], 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(23, 4, $re[$jenis], 0, 0, 'L');
            $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
            $pdf->Cell(18, 4, number_format($rSlip['jumlah'], 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(47, 4, '', 0, 1, 'L');
            $terbilang = $rSlip['jumlah'];
            $blng = terbilang($terbilang, 2).' rupiah';
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(23, 4, 'Terbilang', 0, 0, 'L');
            $pdf->Cell(5, 4, ':', 0, 0, 'L');
            $pdf->MultiCell(58, 4, $blng, 0, 'L');
            $pdf->SetFont('Arial', 'I', 5);
            $pdf->Cell(96, 4, 'Note: This is computer generated system, signature is not required', 'T', 1, 'L');
            $pdf->SetFont('Arial', '', 6);
            $pdf->Ln(10);
            if (235 < $pdf->GetY() && $pdf->col < 1) {
                $pdf->AcceptPageBreak();
            }

            if (235 < $pdf->GetY() && 0 < $pdf->col) {
                $r = 275 - $pdf->GetY();
                $pdf->Cell(80, $r, '', 0, 1, 'L');
            }

            $pdf->cell(-1, 3, '', 0, 0, 'L');
        }
        $pdf->Output();

        break;
    case 'excel':
        $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead>';
        $tab .= '<tr><td  bgcolor=#DEDEDE align=center>No</td><td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['namakaryawan'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['norekeningbank'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['subbagian'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['nik'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['tmk'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['masakerja'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['bagian'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['tipekaryawan'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['statuspajak'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['functionname'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['gaji'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['pengali'].'</td>';
        $tab .= '<td  bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jumlah'].' Bonus/THR</td></tr></thead><tbody>';
        $sSlip = "select distinct a.*,COALESCE(ROUND(DATEDIFF('".$tgltahunlalu."',b.tanggalmasuk)/365.25,3),0) as masakerja,b.norekeningbank,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,b.subbagian from \r\n               ".$dbname.".sdm_gaji_vw a  \r\n               left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid \r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan \r\n               left join ".$dbname.".sdm_5departemen d on b.bagian=d.kode\r\n               where b.sistemgaji='".$jnsGaji."' and a.periodegaji='".$rPeriode['periodegaji']."' and b.lokasitugas='".$kdOrg."' and idkomponen='".$jenis."'";
        $qSlip = mysql_query($sSlip);
        $rRow = mysql_num_rows($qSlip);
        if (0 == $rRow) {
            exit('Error:Not Found');
        }

        while ($rSlip = mysql_fetch_assoc($qSlip)) {
            ++$no;
            $tab .= '<tr class=rowcontent><td>'.$no.'</td><td>'.$rSlip['namakaryawan'].'</td>';
            $tab .= '<td>'.$rSlip['norekeningbank'].'</td>';
            $tab .= '<td>'.$rSlip['nik'].'</td>';
            $tab .= '<td>'.$rSlip['subbagian'].'</td>';
            $tab .= '<td>'.$rSlip['tanggalmasuk'].'</td>';
            $tab .= '<td align=right>'.number_format($rSlip['masakerja'], 3).'</td>';
            $tab .= '<td>'.$rSlip['bagian'].'</td>';
            $tab .= '<td>'.$optTipe[$rSlip['tipekaryawan']].'</td>';
            $tab .= '<td>'.$rSlip['statuspajak'].'</td>';
            $tab .= '<td>'.$rSlip['namajabatan'].'</td>';
            $tab .= '<td align=right align=right>'.number_format($kamusgaji[$rSlip['karyawanid']], 0).'</td>';
            $tab .= '<td align=right align=right>'.number_format($kamuspengali[$rSlip['karyawanid']], 3).'</td>';
            $tab .= '<td align=right  bgcolor=#DEDEDE align=right>'.number_format($rSlip['jumlah'], 0).'</td></tr>';
        }
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'daftarBonusThr_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                            window.location='tempExcel/".$nop_.".xls.gz';\r\n                            </script>";

        break;
    case 'getPeriode':
        $optPeriode = "<option value''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select periode from '.$dbname.".sdm_5periodegaji where kodeorg='".substr($idAfd, 1, 4)."' and jenisgaji='B'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.$rPeriode['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    default:
        break;
}

?>