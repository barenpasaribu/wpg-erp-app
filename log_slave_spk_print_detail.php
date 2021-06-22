<?php
include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
$proses = $_GET['proses'];
$param = $_GET;
$notransaksi=$param['notransaksi'];
$strht= "SELECT b.namaorganisasi as unit, c.namasupplier as supplier, keterangan, useridapprove, tglapprove, useridposting, tglposting  FROM log_spkht a INNER JOIN organisasi b ON a.kodeorg=b.kodeorganisasi inner join log_5supplier c on a.koderekanan=c.supplierid WHERE a.notransaksi='".$notransaksi."'";  
$queryht=mysql_query($strht);
$dataheader=mysql_fetch_assoc($queryht);

$str= "SELECT d.hk, d.hasilkerjajumlah, d.satuan, d.jumlahrp, b.namaorganisasi as unit, c.namaorganisasi as subunit, namakegiatan FROM log_spkdt d inner join log_spkht a ON d.notransaksi=a.notransaksi INNER JOIN organisasi b ON a.kodeorg=b.kodeorganisasi inner join organisasi c on a.divisi=c.kodeorganisasi left join setup_kegiatan x ON d.kodekegiatan=x.kodekegiatan WHERE d.notransaksi='".$notransaksi."'"; 
 
$query=mysql_query($str);

$optKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$align = explode(',', 'L,L,L,R,L,R');
$length = explode(',', '17,17,25,10,12,7,12');

$userapprove=addZero($dataheader['useridapprove'], 10);
$userposting=addZero($dataheader['useridposting'], 10);

$title = $_SESSION['lang']['spk'];
$titleDetail = [''];
switch ($proses) {
    case 'pdf':
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->_noThead = true;
        $pdf->setAttr1($title, $align, $length, []);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(0.6*$width, $height, 'No Kontrak : '.$param['notransaksi'], 0, 0, 'L', 1);
        $pdf->Cell(0.4*$width, $height,  'Approve : '.$optKar[$userapprove].' '.tanggalnormal($dataheader['tglapprove']) ,'LRT', 1, 'L', 1);
        $pdf->Cell(0.6*$width, $height, $_SESSION['lang']['kodeorg'].' : '.$dataheader['unit'], 0, 0, 'L', 1);
        $pdf->Cell(0.4*$width, $height,  'Posting : '.$optKar[$userposting].' '.tanggalnormal($dataheader['tglposting']) , 'LRB', 1, 'L', 1);
        $pdf->Cell($width, $height, $_SESSION['lang']['koderekanan'].' : '.$dataheader['supplier'], 0, 1, 'L', 1);
        $pdf->Ln();
        $pdf->Cell($length[0] / 100 * $width, $height, 'Project', 0, 0, 'L', 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($width, $height, ' : ' .$dataheader['keterangan'] , 0, 1, 'L', 1);
        $pdf->SetFillColor(220, 220, 220);
        $i = 0;
        $pdf->Cell($length[0] / 100 * $width, $height, 'Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, 'Sub Unit', 1, 0, 'C', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, 'Kegiatan', 1, 0, 'C', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, 'Hari Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, 'Volume Kerja', 1, 0, 'C', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, 'Satuan', 1, 0, 'C', 1);
        $pdf->Cell($length[6] / 100 * $width, $height, 'Jumlah (Rp.)', 1, 0, 'C', 1);
        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 8);
    
        while($data=mysql_fetch_array($query)){
        $pdf->Cell($length[0] / 100 * $width, $height, $data['unit'], 1, 0, 'L', 1);
        $pdf->Cell($length[1] / 100 * $width, $height, $data['subunit'], 1, 0, 'L', 1);
        $pdf->Cell($length[2] / 100 * $width, $height, $data['namakegiatan'], 1, 0, 'L', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($data['hk']), 1, 0, 'R', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($data['hasilkerjajumlah']), 1, 0, 'R', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, $data['satuan'], 1, 0, 'L', 1);
        $pdf->Cell($length[6] / 100 * $width, $height, number_format($data['jumlahrp']), 1, 0, 'R', 1);
        $pdf->Ln();
        $tothk+=$data['hk'];
        $tothasil+=$data['hasilkerjajumlah'];
        $totjumlahrp+=$data['jumlahrp'];

        }

        $pdf->Cell(($length[0]+$length[1]+$length[2]) / 100 * $width, $height, 'T O T A L', 1, 0, 'L', 1);
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($tothk), 1, 0, 'R', 1);
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($tothasil), 1, 0, 'R', 1);
        $pdf->Cell($length[5] / 100 * $width, $height, $data['satuan'], 1, 0, 'L', 1);
        $pdf->Cell($length[6] / 100 * $width, $height, number_format($totjumlahrp), 1, 0, 'R', 1);

        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, $height, 'Dibuat Oleh ('.date("d-m-Y").')', 'LTR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Pemborong','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Diperiksa','TR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Disetujui','TR',0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, 'Dibayar','TR', 0, 'C', 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);
        $pdf->Cell(1 / 5 * $width, 50, '', 'LR', 0, $align[4], 0);

        $pdf->Ln();

        $pdf->Cell(1 / 5 * $width, $height, '', 'LBR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);
        $pdf->Cell(1 / 5 * $width, $height, '', 'BR', 0, 'C', 0);

        $pdf->Output();

        break;
    case 'excel':
        break;
    default:
        break;
}



?>
