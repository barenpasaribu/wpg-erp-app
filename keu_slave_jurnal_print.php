<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/biReport.php';
include_once 'lib/zPdfMaster.php';
$level = $_GET['level'];
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}

if ('pdf' === $mode) {
    $param = $_GET;
    unset($param['mode'], $param['level']);
} else {
    $param = $_POST;
}

unset($param['nojurnal_0_until']);
foreach ($param as $key => $row) {
    if ('nojurnal' === substr($key, 0, 8)) {
        $nojurnal = $row;
    }
}
switch ($level) {
    case '0':
        break;
    case '1':
        $query = "SELECT b.noreferensi,a.nojurnal,a.noakun,a.keterangan,a.jumlah,a.nodok \r\n            FROM ".$dbname.".keu_jurnaldt a\r\n            LEFT JOIN ".$dbname.".keu_jurnalht b on a.nojurnal=b.nojurnal\r\n            WHERE a.nojurnal = '".$nojurnal."'";
        $data = fetchData($query);
        if ('EN' === $_SESSION['language']) {
            $kegiatan = 'SELECT noakun, namaakun1 as namaakun FROM '.$dbname.'.keu_5akun';
        } else {
            $kegiatan = 'SELECT noakun, namaakun FROM '.$dbname.'.keu_5akun';
        }

        $query = mysql_query($kegiatan);
        while ($res = mysql_fetch_assoc($query)) {
            $kamusakun[$res['noakun']] = $res['namaakun'];
        }
        $total = ['debet' => 0, 'kredit' => 0];
        foreach ($data as $row) {
            if ($row['jumlah'] < 0) {
                $total['kredit'] += $row['jumlah'] * -1;
            } else {
                $total['debet'] += $row['jumlah'];
            }
        }

        break;
}
switch ($mode) {
    case 'pdf':
        $colsNew = 'noakun,namaakun,keterangan,debet,kredit,nodok';
        $colPdf = explode(',', $colsNew);
        $title = $_SESSION['lang']['nojurnal'].': '.$row['nojurnal'];
        $title .= ' '.$_SESSION['lang']['noreferensi'].': '.$row['noreferensi'];
        $align = explode(',', 'L,L,L,R,R,L');
        $length = explode(',', '10,20,45,15,15,15');
        $pdf = new zPdfMaster('P', 'pt', 'A4');
        $pdf->SetFont('Arial', '', 8);
        $pdf->setAttr1($title, $align, $length, $colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();

        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell($length[0] / 100 * $width, $height, "No Akun", 1, 0, 'C',1);
        $pdf->Cell($length[2] / 100 * $width, $height, "Keterangan", 1, 0, 'C',1);
        $pdf->Cell($length[3] / 100 * $width, $height, "Debet", 1, 0, 'C',1);
        $pdf->Cell($length[4] / 100 * $width, $height, "Kredit", 1, 0, 'C',1);
        $pdf->Cell($length[5] / 100 * $width, $height, "No Dokumen",  1, 0, 'C',1);
            $pdf->Ln();

        $pdf->SetFillColor(255, 255, 255);
        foreach ($data as $row) {
            $i = 0;
            $pdf->Cell($length[0] / 100 * $width, $height, $row['noakun'], 1, 0, $align[0]);
            $pdf->Cell($length[2] / 100 * $width, $height, substr($row['keterangan'], 0, 35), 1, 0, $align[2]);
            if ($row['jumlah'] < 0) {
                $pdf->Cell($length[3] / 100 * $width, $height, 0, 1, 0, $align[3]);
                $pdf->Cell($length[4] / 100 * $width, $height, number_format($row['jumlah'] * -1,2), 1, 0, $align[4]);
            } else {
                $pdf->Cell($length[3] / 100 * $width, $height, number_format($row['jumlah'],2), 1, 0, $align[3]);
                $pdf->Cell($length[4] / 100 * $width, $height, 0, 1, 0, $align[4]);
            }

            $pdf->Cell($length[5] / 100 * $width, $height, substr($row['nodok'], 0, 32), 1, 0, $align[5]);
            $pdf->Ln();
        }
        $lenTotal = $length[0] + $length[2];
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($lenTotal / 100 * $width, $height, 'TOTAL', 1, 0, 'C');
        $pdf->Cell($length[3] / 100 * $width, $height, number_format($total['debet'],2), 1, 0, 'R');
        $pdf->Cell($length[4] / 100 * $width, $height, number_format($total['kredit'],2), 1, 0, 'R');
        $pdf->Cell($length[5] / 100 * $width, $height, '', 1, 0, 'R');
        $pdf->Ln();
		$pdf->Ln();

       		$pdf->SetFillColor(220, 220, 220);
       
           $pdf->Cell(180, $height, 'Dibuat Oleh', 1, 0, 'C', 1);
           $pdf->Cell(180, $height, 'Diperiksa Oleh', 1, 0, 'C', 1);
           $pdf->Cell(180, $height, 'Disetujui Oleh', 1, 0, 'C', 1);
           $pdf->Ln();
        
           $pdf->SetFillColor(255, 255, 255);

           for ($i = 0; $i < 3; ++$i) {
               $pdf->Cell(180, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(180, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(180, $height, '', 'LR', 0, 'C', 1);
               $pdf->Ln();
           }
           $pdf->Cell(180, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(180, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(180, $height, '', 'BLR', 0, 'C', 1);
        

       


        $pdf->SetFont('Arial', '', 8);
        $pdf->Output();

        break;
}

?>