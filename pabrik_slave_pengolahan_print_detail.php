<?php

    include_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/formTable.php';
    include_once 'lib/zPdfMaster.php';
    include_once 'lib/awanLib.php';

    $proses = $_GET['proses'];
    $param = $_GET;
    $nopengolahan = $_GET['nopengolahan'];

    // Get Data pabrik_pengolahan
    $queryGetPabrikPengolahan = "SELECT * FROM pabrik_pengolahan WHERE nopengolahan=".$nopengolahan;
    $dataPabrikPengolahan = fetchData($queryGetPabrikPengolahan);

    // Get Data pabrik_pengolahanmesin
    $queryGetPabrikPengolahanMesin = "SELECT * FROM pabrik_pengolahan_mesin WHERE pabrik_pengolahan_id=".$nopengolahan;
    $dataPabrikPengolahanMesin = fetchData($queryGetPabrikPengolahanMesin);

    // echo "<pre>";
    // print_r($dataPabrikPengolahan);
    // print_r($dataPabrikPengolahanMesin);
    // die();

    
    
    switch ($proses) {
        case 'pdf':
            $pdf = new zPdfMaster('L', 'pt', 'A4');
            $pdf->_noThead = true;
            $pdf->setAttr1($title, $align, $length, []);
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            // 785
            $height = 15;
            $pdf->AddPage();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $_SESSION['lang']['nopengolahan'].' : '.$param['nopengolahan'], 0, 1, 'L', 1);
            $pdf->Cell($width, $height, $_SESSION['lang']['kodeorg'].' : '.$dataPabrikPengolahan[0]['kodeorg'], 0, 1, 'L', 1);
            $pdf->Cell($width, $height, $_SESSION['lang']['tanggal'].' : '.tanggalnormal($dataPabrikPengolahan[0]['tanggal']), 0, 1, 'L', 1);
            $pdf->Cell($width, $height, 'Jumlah Hari Olah : '.$dataPabrikPengolahan[0]['status_olah'], 0, 1, 'L', 1);
            $pdf->Cell($width, $height, 'Total Jam Stagnasi : '.$dataPabrikPengolahan[0]['jam_stagnasi'], 0, 1, 'L', 1);
            $pdf->Cell($width, $height, 'Total Jam Idle : '.$dataPabrikPengolahan[0]['total_jam_idle'], 0, 1, 'L', 1);
            
            // if ($dataPabrikPengolahan[0]['posting'] == 1) {
            //     $status = "Belum di Posting"
            // }else{
            //     $status = "Sudah di Posting"
            // }
            // $pdf->Cell($width, $height, 'Status : '.$status, 0, 1, 'L', 1);
            $pdf->Ln();
            
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pengolahan Pabrik Shift 1", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "Mandor", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Asisten", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Jam Start Operasi", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Jam Stop Operasi", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Total Jam Operasi", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Jam Start Shift", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Jam Stop Shift", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Jam Shift", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Jam Press", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_1'])) > 11) {
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(70, $height, substr(getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_1']),0,20), 1, 0, '', 1);
                $pdf->SetFont('Arial', '', 9);
            }else {
                $pdf->Cell(70, $height, substr(getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_1']),0,20), 1, 0, 'C', 1);
            }

            if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_1'])) > 11) {
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_1']), 1, 0, 'C', 1);
                $pdf->SetFont('Arial', '', 9);
            }else {
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_1']), 1, 0, 'C', 1);
            }
            
            
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_start_operasi_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_stop_operasi_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['total_jam_operasi_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_start_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_stop_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_press_shift_1'], 1, 0, 'C', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pengolahan Pabrik Shift 2", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "Mandor", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Asisten", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Jam Start Operasi", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Jam Stop Operasi", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Total Jam Operasi", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Jam Start Shift", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Jam Stop Shift", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Jam Shift", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Jam Press", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_2'])) > 11) {
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_2']), 1, 0, 'C', 1);
                $pdf->SetFont('Arial', '', 9);
            }else {
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_2']), 1, 0, 'C', 1);
            }

            if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_2'])) > 11) {
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_2']), 1, 0, 'C', 1);
                $pdf->SetFont('Arial', '', 9);
            }else {
                $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_2']), 1, 0, 'C', 1);
            }
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_start_operasi_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_stop_operasi_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['total_jam_operasi_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_start_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_stop_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_press_shift_2'], 1, 0, 'C', 1);
            $pdf->Ln();

            if ($dataPabrikPengolahan[0]['total_jam_shift_3'] > 0) {
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
                $pdf->Cell($width, $height, "Pengolahan Pabrik Shift 3", 0, 1, 'L', 1);
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(70, $height, "Mandor", 1, 0, 'C', 1);
                $pdf->Cell(70, $height, "Asisten", 1, 0, 'C', 1);
                $pdf->Cell(95, $height, "Jam Start Operasi", 1, 0, 'C', 1);
                $pdf->Cell(95, $height, "Jam Stop Operasi", 1, 0, 'C', 1);
                $pdf->Cell(95, $height, "Total Jam Operasi", 1, 0, 'C', 1);
                $pdf->Cell(80, $height, "Jam Start Shift", 1, 0, 'C', 1);
                $pdf->Cell(80, $height, "Jam Stop Shift", 1, 0, 'C', 1);
                $pdf->Cell(100, $height, "Total Jam Shift", 1, 0, 'C', 1);
                $pdf->Cell(100, $height, "Total Jam Press", 1, 0, 'C', 1);
                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('Arial', '', 9);
                if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_3'])) > 11) {
                    $pdf->SetFont('Arial', '', 6);
                    $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_3']), 1, 0, 'C', 1);
                    $pdf->SetFont('Arial', '', 9);
                }else {
                    $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['mandor_shift_3']), 1, 0, 'C', 1);
                }
    
                if (strlen (getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_3'])) > 11) {
                    $pdf->SetFont('Arial', '', 6);
                    $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_3']), 1, 0, 'C', 1);
                    $pdf->SetFont('Arial', '', 9);
                }else {
                    $pdf->Cell(70, $height, getNamaKaryawan($dataPabrikPengolahan[0]['asisten_shift_3']), 1, 0, 'C', 1);
                }
                
                $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_start_operasi_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['jam_stop_operasi_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['total_jam_operasi_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_start_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['jam_stop_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_shift_3'], 1, 0, 'C', 1);
                $pdf->Cell(100, $height, $dataPabrikPengolahan[0]['total_jam_press_shift_3'], 1, 0, 'C', 1);
                $pdf->Ln();
            }

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Lori", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(80, $height, "Lori Olah Shift 1", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Lori Olah Shift 2", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Lori Olah Shift 3", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Lori Dalam Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Lori Depan Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, "Lori Dibelakang Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Estimasi di Peron", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Total Lori", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Rata-rata Lori", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['lori_olah_shift_1'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['lori_olah_shift_2'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['lori_olah_shift_3'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['lori_dalam_rebusan'], 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['restan_depan_rebusan'], 1, 0, 'C', 1);
            $pdf->Cell(120, $height, $dataPabrikPengolahan[0]['restan_dibelakang_rebusan'], 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikPengolahan[0]['estimasi_di_peron'], 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikPengolahan[0]['total_lori'], 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikPengolahan[0]['rata_rata_lori'], 1, 0, 'C', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "TBS", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "TBS Sisa Kemarin", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "TBS Masuk (Bruto)", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Total TBS", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Sortasi", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "TBS Masuk (Netto)", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "TBS Diolah", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "TBS Diolah After", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "TBS Sisa", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikPengolahan[0]['tbs_sisa_kemarin']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikPengolahan[0]['tbs_masuk_bruto']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['total_tbs']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['tbs_potongan']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikPengolahan[0]['tbs_masuk_netto']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['tbs_diolah']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['tbs_diolah_after']."Kg", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikPengolahan[0]['tbs_sisa']."Kg", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->Ln();

            if (!empty($dataPabrikPengolahanMesin)) {
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
                $pdf->Cell($width, $height, "Pengolahan Pabrik Mesin", 0, 1, 'L', 1);
                $pdf->SetFillColor(220, 220, 220);

                $pdf->Cell(40, $height, "Shift", 1, 0, 'C', 1);
                $pdf->Cell(60, $height, "Station", 1, 0, 'C', 1);
                $pdf->Cell(80, $height, "Mesin", 1, 0, 'C', 1);
                $pdf->Cell(95, $height, "Jam Mulai Stagnasi", 1, 0, 'C', 1);
                $pdf->Cell(100, $height, "Jam Selesai Stagnasi", 1, 0, 'C', 1);
                $pdf->Cell(95, $height, "Total Jam Stagnasi", 1, 0, 'C', 1);
                $pdf->Cell(80, $height, "Down Status", 1, 0, 'C', 1);
                $pdf->Cell(140, $height, "Keterangan", 1, 0, 'C', 1);
                $pdf->Ln();
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('Arial', '', 9);
                foreach ($dataPabrikPengolahanMesin as $key => $value) {
                    $pdf->Cell(40, $height, $value['shift'], 1, 0, 'C', 1);
                    $pdf->Cell(60, $height, $value['station'], 1, 0, 'C', 1);
                    $pdf->Cell(80, $height, $value['engine'], 1, 0, 'C', 1);
                    $pdf->Cell(95, $height, $value['start_time_stagnasi'], 1, 0, 'C', 1);
                    $pdf->Cell(100, $height, $value['stop_time_stagnasi'], 1, 0, 'C', 1);
                    $pdf->Cell(95, $height, $value['total_stagnasi'], 1, 0, 'C', 1);
                    $pdf->Cell(80, $height, $value['down_status'], 1, 0, 'C', 1);
                    $pdf->Cell(140, $height, $value['description'], 1, 0, 'C', 1);
                    $pdf->Ln();
                }
                $pdf->Ln();
            }

            $pdf->Ln();
            $pdf->Output();
            break;
        case 'excel':
            break;
        default:
            break;
    }

?>