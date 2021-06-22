<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
('' === $_POST['kdUnit'] ? ($kdUnit = $_GET['kdUnit']) : ($kdUnit = $_POST['kdUnit']));
('' === $_POST['periodeData'] ? ($periodeData = $_GET['periodeData']) : ($periodeData = $_POST['periodeData']));
$per = explode('-', $periodeId);
$perod = $per[2].'-'.$per[1].'-'.$per[0];
$dtPeriod = $per[2].'-'.$per[1];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optSup = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optBibit = makeOption($dbname, 'bibitan_batch', 'batch,jenisbibit');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'tipekaryawan=5');
$where = " kodeunit='".$kodeOrg."' and tahunbudget='".$thnBudget."'";
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
$optNmkeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan');
$optSatkeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,satuan');
if ('' === $periodeData || '' === $kdUnit) {
    exit('Error:Unit Dan Periode Tidak Boleh Kosong!!');
}

$sData = 'select distinct * from '.$dbname.".bibitan_mutasi where kodetransaksi='PNB'\r\n       and kodeorg like '%MN%' and kodeorg like '".$kdUnit."%' and substr(tanggal,1,7)='".$periodeData."'";
$qData = mysql_query($sData) ;
$row = mysql_num_rows($qData);
if (0 === $row) {
    exit('Error:Data Kosong');
}

$brd = 0;
$bgcolordt = '';
if ('excel' === $proses) {
    $bgcolordt = 'bgcolor=#DEDEDE';
    $brd = 1;
}

$tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable>';
$tab .= '<thead><tr>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['batch'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['tanggal'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['kodeorganisasi'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['nopol'].'</td>';
$tab .= '<td rowspan=2 '.$bgcolordt.'>RIT KE</td>';
$tab .= '<td rowspan=2 align=center  '.$bgcolordt.'>'.$_SESSION['lang']['supir'].'</td>';
$tab .= '<td rowspan=2 align=center  '.$bgcolordt.'>'.$_SESSION['lang']['customerlist'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['kegiatan'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['satuan'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['nosj'].'</td>';
$tab .= '<td align=center colspan=2 '.$bgcolordt.'>'.$_SESSION['lang']['lokasi'].'</td>';
$tab .= '<td align=center colspan=2 '.$bgcolordt.'>'.$_SESSION['lang']['pengiriman'].'</td>';
$tab .= '<td rowspan=2 align=center '.$bgcolordt.'>'.$_SESSION['lang']['detail'].' '.$_SESSION['lang']['lokasi'].'</td>';
$tab .= '<td align=center rowspan=2 '.$bgcolordt.'>'.$_SESSION['lang']['asisten'].'</td>';
$tab .= '</tr>';
$tab .= '<tr><td '.$bgcolordt.'>'.$_SESSION['lang']['afdeling'].'</td>';
$tab .= '<td '.$bgcolordt.'>'.$_SESSION['lang']['blok'].'</td>';
$tab .= '<td '.$bgcolordt.'>'.$_SESSION['lang']['jenisbibit'].'</td>';
$tab .= '<td '.$bgcolordt.'>'.$_SESSION['lang']['jumlah'].' (PK)</td></tr>';
$tab .= '</thead><tbody>';
while ($rData = mysql_fetch_assoc($qData)) {
    if (0 !== $rData['intex']) {
        $nmPelanggan = $optNm[$rData['pelanggan']];
    } else {
        $nmPelanggan = $optSup[$rData['pelanggan']];
    }

    $afd = substr($rData['afdeling'], 0, 6);
    $blk = substr($rData['afdeling'], 6, 4);
    $tab .= '<tr class=rowcontent>';
    $tab .= '<td>'.$rData['batch'].'</td>';
    $tab .= '<td>'.$rData['tanggal'].'</td>';
    $tab .= '<td>'.$rData['kodeorg'].'</td>';
    $tab .= '<td>'.$rData['kodevhc'].'</td>';
    $tab .= '<td align=right>'.$rData['rit'].'</td>';
    $tab .= '<td>'.$rData['sopir'].'</td>';
    $tab .= '<td>'.$nmPelanggan.'</td>';
    $tab .= '<td>'.$rData['jenistanam'].'</td>';
    $tab .= '<td>PKK</td>';
    $tab .= '<td>'.$rData['keterangan'].'</td>';
    $tab .= '<td>'.$afd.'</td>';
    $tab .= '<td>'.$blk.'</td>';
    $tab .= '<td>'.$optBibit[$rData['batch']].'</td>';
    $tab .= '<td>'.number_format(abs($rData['jumlah'])).'</td>';
    $tab .= '<td>'.$rData['lokasipengiriman'].'</td>';
    $tab .= '<td>'.$optNmKary[$rData['penanggungjawab']].'</td>';
    $tab .= '</tr>';
    $totalan += $rData['jumlah'];
}
$tab .= '<tr class=rowcontent><td colspan=13>'.$_SESSION['lang']['total'].'</td>';
$tab .= '<td align=right>'.number_format(abs($totalan)).'</td><td colspan=2></td></tr>';
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('Hms');
        $nop_ = 'lapAntarBibit_'.$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n            window.location='tempExcel/".$nop_.".xls.gz';\r\n            </script>";

        break;
    case 'pdf':

class PDF extends FPDF
{
    public function Header()
    {
        global $periodeId;
        global $dataAfd;
        global $kdUnit;
        global $optNm;
        global $dbname;
        global $where;
        global $optSatuan;
        global $optNmBrg;
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 20;
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(250, $height, strtoupper('LAPORAN RESTAN'), 0, 0, 'L');
        $this->Cell(270, $height, $_SESSION['lang']['bulan'].' : '.substr(tanggalnormal($periodeId), 1, 7), 0, 1, 'R');
        $tinggiAkr = $this->GetY();
        $ksamping = $this->GetX();
        $this->SetY($tinggiAkr);
        $this->SetX($ksamping);
        $this->Cell($width, $height, $_SESSION['lang']['unit'].' : '.$optNm[$kdUnit], 0, 1, 'L');
        $this->SetFillColor(220, 220, 220);
        $this->Cell(55, $height, 'Tanggal', TLR, 0, 'C', 1);
        $this->Cell(55, $height, $_SESSION['lang']['blok'], TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Panen', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Kirim', TLR, 0, 'C', 1);
        $this->Cell(90, $height, 'Restan', TLR, 1, 'C', 1);
        $this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
        $this->Cell(55, $height, ' ', BLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Janjang', TBLR, 0, 'C', 1);
        $this->Cell(45, $height, 'Kg', TBLR, 1, 'C', 1);
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
        $pdf->SetFont('Arial', '', 7);
        $sData = 'select distinct * from '.$dbname.'.kebun_restan where '.$where.' order by tanggal asc';
        $qData = mysql_query($sData) ;
        $row = mysql_num_rows($qData);
        if (0 !== $row) {
            while ($rdata = mysql_fetch_assoc($qData)) {
                $sKg = 'select distinct bjr from '.$dbname.".kebun_5bjr where kodeorg='".$rdata['kodeorg']."'";
                $qKg = mysql_query($sKg) ;
                $rKg = mysql_fetch_assoc($qKg);
                $panenKg = $rdata['jjgpanen'] * $rKg['bjr'];
                $KirimKg = $rdata['jjgkirim'] * $rKg['bjr'];
                $resJjg = $rdata['jjgpanen'] - $rdata['jjgkirim'];
                $resKg = $resJjg * $rKg['bjr'];
                $pdf->Cell(55, $height, tanggalnormal($rdata['tanggal']), TBLR, 0, 'C', 1);
                $pdf->Cell(55, $height, $rdata['kodeorg'], TBLR, 0, 'C', 1);
                $pdf->Cell(45, $height, $rdata['jjgpanen'], TBLR, 0, 'R', 1);
                $pdf->Cell(45, $height, $panenKg, TBLR, 0, 'R', 1);
                $pdf->Cell(45, $height, $rdata['jjgkirim'], TBLR, 0, 'R', 1);
                $pdf->Cell(45, $height, $KirimKg, TBLR, 0, 'R', 1);
                $pdf->Cell(45, $height, $resJjg, TBLR, 0, 'R', 1);
                $pdf->Cell(45, $height, $resKg, TBLR, 1, 'R', 1);
            }
        } else {
            $pdf->Cell(380, $height, $_SESSION['lang']['dataempty'], TBLR, 1, 'C', 1);
        }

        $pdf->Output();

        break;
    default:
        break;
}

?>