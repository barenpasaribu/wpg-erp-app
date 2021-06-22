<?php

    include_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/formTable.php';
    include_once 'lib/zPdfMaster.php';
    include_once 'lib/terbilang.php';
    $proses = $_GET['proses'];
    $param = $_GET;
    $cols = [];
    $where = 'karyawanid='.$param['karyawanid'];
    $cols = '*';
    $query = selectQuery($dbname, 'sdm_pesangonht', $cols, $where);
    $data = fetchData($query);
    $dataH = $data[0];
    $queryD = selectQuery($dbname, 'sdm_pesangondt', $cols, $where, 'no asc');
    $dataD = fetchData($queryD);
    // if (empty($dataD)) {
    //     echo 'Data Empty';
    //     exit();
    // }

    $whereKary = 'karyawanid='.$param['karyawanid'];
    $qKary = selectQuery($dbname, 'datakaryawan', '*', $whereKary);
    $resKary = fetchData($qKary);
    $infoKary = $resKary[0];
    $namaKary = $resKary[0]['namakaryawan'];
    $nikKary = $resKary[0]['nik'];
    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$infoKary['lokasitugas']."'");
    $optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan', "kodejabatan='".$infoKary['kodejabatan']."'");
    $tgl = explode('-', $dataH['tanggal']);
    $tglStr = date('j F Y', mktime(0, 0, 0, $tgl[1], $tgl[2], $tgl[0]));
    $tglMasuk = explode('-', $infoKary['tanggalmasuk']);
    $tglMasukStr = date('j F Y', mktime(0, 0, 0, $tglMasuk[1], $tglMasuk[2], $tglMasuk[0]));
    switch ($proses) {
        case 'pdf':
            $pdf = new zPdfMaster('P', 'pt', 'A4');
            $pdf->_kopOnly = true;
            $pdf->_logoOrg = strtolower($_SESSION['org']['kodeorganisasi']);
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 15;
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 14);
			$posY = $pdf->GetY;
			$posY = $height+130;
			$pdf-> SetY($posY);
            $pdf->Cell($width, $height, 'Perhitungan Penyelesaian Gaji/Uang Jasa', 0, 1, 'C');
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell($width, $height, 'Periode '.$tglStr, 0, 1, 'C');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell($width, $height, 'Nomor : '.$dataH['nodok'], 0, 1, 'C');
            $pdf->Ln($height * 0.5);
            $pdf->Cell(20 / 100 * $width, $height, 'NIK', 0, 0, 'L');
            $pdf->Cell(30 / 100 * $width, $height, ': '.$nikKary, 0, 1, 'L');
            $pdf->Cell(20 / 100 * $width, $height, 'Nama', 0, 0, 'L');
            $pdf->Cell(30 / 100 * $width, $height, ': '.$namaKary, 0, 1, 'L');
            $pdf->Cell(20 / 100 * $width, $height, 'Unit Kerja', 0, 0, 'L');
            $pdf->Cell(30 / 100 * $width, $height, ': '.$optOrg[$infoKary['lokasitugas']], 0, 1, 'L');
            $pdf->Cell(20 / 100 * $width, $height, 'Masuk Kerja', 0, 0, 'L');
            $pdf->Cell(30 / 100 * $width, $height, ': '.$tglMasukStr, 0, 1, 'L');
            $pdf->Ln($height);
            $pdf->SetFont('Arial', 'U', 9);
            $pdf->Cell(30 / 100 * $width, $height, 'Perincian Besarnya Uang Penyelesaian :', 0, 1, 'L');
            $pdf->Ln($height);
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(30 / 100 * $width, $height, '1. Uang Penggantian Hak', 0, 1, 'L');
            $pdf->Ln($height * 0.5);
            $pdf->SetFont('Arial', '', 9);
            $subTotal = 0;
            foreach ($dataD as $row) {
                if ('uang ganti' == $row['tipe']) {
                    $pdf->Cell(35 / 100 * $width, $height, '- '.$row['narasi'], 0, 0, 'L');
                    $pdf->Cell(5 / 100 * $width, $height, $row['pengali'], 0, 0, 'R');
                    $pdf->Cell(15 / 100 * $width, $height, 'X', 0, 0, 'C');
                    $pdf->Cell(15 / 100 * $width, $height, number_format($row['rp'], 2), 0, 0, 'R');
                    $pdf->Cell(15 / 100 * $width, $height, ' = Rp.', 0, 0, 'L');
                    $pdf->Cell(15 / 100 * $width, $height, number_format($row['total'], 0), 0, 0, 'R');
                    $pdf->Ln();
                    $subTotal += $row['total'];
                }
            }
            $pdf->Ln();
			$n=2;
            if($dataH['pesangon']<>0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Pesangon', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['pesangon'], 0), 0, 1, 'R');
            $n++;
			}
			if($dataH['penghargaan']<>0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Penghargaan', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['penghargaan'], 0), 'B', 1, 'R');
            $n++;
			}
			if($dataH['pengganti']<>0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Mengundurkan diri', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['pengganti'], 0), 'B', 1, 'R');
            $n++;
			}
			if($dataH['perusahaan'] <> 0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Diberhentikan Perusahaan', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['perusahaan'], 0), 'B', 1, 'R');
            $n++;
			}
			if($dataH['kesalahanbiasa'] <> 0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Kesalahan Biasa', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['kesalahanbiasa'], 0), 'B', 1, 'R');
            $n++;
			}
			if($dataH['kesalahanbesar'] <> 0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Kesalahan Berat', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['kesalahanbesar'], 0), 'B', 1, 'R');
            $n++;
			}
			if($dataH['uangpisah'] <> 0){
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Uang Pisah', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['uangpisah'], 0), 'B', 1, 'R');
			$n++;
            }
			$pdf->Ln($height * 0.5);

            $subTotal += $dataH['pesangon'] + $dataH['penghargaan'] + $dataH['pengganti'] + $dataH['perusahaan'] + $dataH['kesalahanbiasa'] + $dataH['kesalahanbesar'] + $dataH['uangpisah'];
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(80 / 100 * $width, $height, 'Sub Total', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(20 / 100 * $width, $height, number_format($subTotal, 0), 0, 1, 'R');
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(80 / 100 * $width, $height, 'PPh', 0, 0, 'R');
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(20 / 100 * $width, $height, number_format($dataH['pph'], 0), 0, 1, 'R');
            $pdf->Ln($height);
            $pdf->SetFont('Arial', 'BU', 10);
            $pdf->Cell(85 / 100 * $width, $height, $n.'. Potongan', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 9);
            foreach ($dataD as $row) {
                if ('potongan' == $row['tipe']) {
                    $pdf->Cell(40 / 100 * $width, $height, '', 0, 0, 'L');
                    $pdf->Cell(30 / 100 * $width, $height, $row['narasi'], 0, 0, 'L');
                    $pdf->Cell(15 / 100 * $width, $height, ' = Rp.', 0, 0, 'L');
                    $pdf->Cell(15 / 100 * $width, $height, number_format($row['total'], 0), 'B', 0, 'R');
                    $pdf->Ln();
                    $subTotal -= $row['total'];
                }
            }
            $pdf->Ln($height * 0.5);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40 / 100 * $width, $height, '', 0, 0, 'L');
            $pdf->Cell(30 / 100 * $width, $height, 'Diterima', 0, 0, 'R');
            $pdf->Cell(15 / 100 * $width, $height, ' = Rp.', 0, 0, 'L');
            $pdf->Cell(15 / 100 * $width, $height, number_format($dataH['total'], 0), 0, 0, 'R');
            $pdf->Ln($height * 2.5);
            $pdf->Cell(20 / 100 * $width, $height, 'T o t a l :', 0, 0, 'L');
            $pdf->Cell(5 / 100 * $width, $height, 'Rp.', 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(20 / 100 * $width, $height, number_format($dataH['total'], 0), 0, 0, 'R');
            $pdf->Ln($height * 2.5);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(10 / 100 * $width, $height, 'Terbilang :', 0, 0, 'L');
            $pdf->MultiCell(90 / 100 * $width, $height, '# '.terbilang($dataH['total'], 0).' Rupiah #', 0, 'L');
            $pdf->Ln($height * 3);
            $pdf->Cell(60 / 100 * $width, $height, 'Diterima Oleh,', 0, 0, 'L');
            $pdf->Cell(40 / 100 * $width, $height, 'Disetujui,', 0, 0, 'L');           
            $pdf->Ln($height * 4);
            $pdf->Cell(60 / 100 * $width, $height, $namaKary, 0, 0, 'L');
            $pdf->Cell(40 / 100 * $width, $height, '', 0, 0, 'L');
            $pdf->Ln();
            $pdf->Cell(60 / 100 * $width, $height*0.8, "(".$optJabatan[$infoKary['kodejabatan']].")", 0, 0, 'L');
            $pdf->Cell(40 / 100 * $width, $height, '', 0, 0, 'L');
            $pdf->Output();

        break;
        default:
        break;
    }

?>