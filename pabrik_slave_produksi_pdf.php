<?php
    include_once 'master_validation.php';
    include_once 'lib/eagrolib.php';
    include_once 'lib/zLib.php';
    include_once 'lib/formTable.php';
    include_once 'lib/zPdfMaster.php';

    $kodeorg = $_GET['kodeorg'];
    $tanggal = $_GET['tanggal'];
    $proses = $_GET['proses'];

    // Get Data pabrik_produksi
    $queryGetPabrikProduksi = " SELECT * FROM pabrik_produksi 
                                WHERE 
                                kodeorg='".$kodeorg."'
                                AND
                                tanggal='".$tanggal."'"
                                ;
    $dataPabrikProduksi = fetchData($queryGetPabrikProduksi);

    switch ($proses) {
        case 'pdf':
            $pdf = new zPdfMaster('L', 'pt', 'A4');
            $pdf->_noThead = true;
            $pdf->setAttr1($title, $align, $length, []);
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            // 785
            $height = 18;
            $pdf->AddPage();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, "Kode Organisasi".' : '.$dataPabrikProduksi[0]['kodeorg'], 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Tanggal".' : '.tanggalnormal($dataPabrikProduksi[0]['tanggal']), 0, 1, 'L', 1);

            // TBS
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "TBS", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(110, $height, "TBS Sisa Kemarin", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "TBS Masuk (Bruto)", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "TBS (Potongan)", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "TBS Masuk (Netto)", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "TBS diolah", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, "TBS diolah After Grading", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "TBS (Sisa)", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['tbs_sisa_kemarin'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['tbs_masuk_bruto'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['tbs_potongan'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['tbs_masuk_netto'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['tbs_diolah'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, $dataPabrikProduksi[0]['tbs_after_grading'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['tbs_sisa'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Lori
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Lori", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Lori Olah", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Lori Dalam Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "Restan Depan Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(130, $height, "Restan dibelakang Rebusan", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, "Estimasi di Peron", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Total Lori", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Rata-rata Lori", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['lori_olah'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['lori_dalam_rebusan'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['restan_depan_rebusan'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(130, $height, $dataPabrikProduksi[0]['restan_dibelakang_rebusan'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(95, $height, $dataPabrikProduksi[0]['estimasi_di_peron'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['total_lori'] . " Unit.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['lori_rata_rata'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Rendemen
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Rendemen", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(150, $height, "Rendemen CPO Before Grading", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, "Rendemen CPO After Grading", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Rendemen PK Before", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Rendemen PK After", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(150, $height, $dataPabrikProduksi[0]['rendemen_cpo_before'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, $dataPabrikProduksi[0]['rendemen_cpo_after'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['rendemen_pk_before'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['rendemen_pk_after'] . "%.", 1, 0, 'C', 1);
            $pdf->Ln();

            // CPO
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "CPO", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(80, $height, "Opening Stock", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Produksi CPO", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Closing Stock ", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Kotoran", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Kadar Air", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "FFa", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Dobi", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['cpo_opening_stock'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_produksi'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['cpo_closing_stock'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_kotoran'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_kadar_air'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_ffa'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_dobi'] . "%.", 1, 0, 'C', 1);
            $pdf->Ln();
            
            // Oil Loses to FFB
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Oil Loses to FFB", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "USB", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Empty Bunch", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "Fibre Cyclone", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, "Nut from Polishingdrum", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Effluent", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['cpo_usb'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['cpo_empty_bunch'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['cpo_fibre_cyclone'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, $dataPabrikProduksi[0]['cpo_nut_from_polishingdrum'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikProduksi[0]['cpo_effluent'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Kernel
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Kernel", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "Opening Stok", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Produksi Kernel", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "Closing Stok", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, "Kotoran", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Kadar Air", 1, 0, 'C', 1);
            $pdf->Cell(115, $height, "Inti Pecah", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['kernel_opening_stock'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['kernel_produksi'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['kernel_closing_stock'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, $dataPabrikProduksi[0]['kernel_kotoran'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['kernel_kadar_air'] . "%.", 1, 0, 'C', 1);
            $pdf->Cell(115, $height, $dataPabrikProduksi[0]['kernel_inti_pecah'] . "%.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Kernel loses
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Kernel Loses", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(120, $height, "USB", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Fibre Cyclone", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "LTDS 1", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, "LTDS 2", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Claybath", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(120, $height, $dataPabrikProduksi[0]['kernel_loses_usb'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['kernel_loses_fibre_cyclone'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['kernel_loses_ltds_1'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(120, $height, $dataPabrikProduksi[0]['kernel_loses_ltds_2'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikProduksi[0]['kernel_loses_claybath'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Pengiriman
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pengiriman", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Despatch (CPO)	", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Return CPO", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Despatch (PK)", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Return PK", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Janjang Kosong", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Limbah Cair", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Solid Decnter", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Abu Janjang", 1, 0, 'C', 1);
            $pdf->Cell(75, $height, "Cangkang	", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, "Fibre", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['pengiriman_despatch_cpo'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikProduksi[0]['pengiriman_return_cpo'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['pengiriman_despatch_pk'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikProduksi[0]['pengiriman_return_pk'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['pengiriman_janjang_kosong'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['pengiriman_limbah_kosong'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['pengiriman_solid_decnter'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['pengiriman_abu_janjang'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(75, $height, $dataPabrikProduksi[0]['pengiriman_cangkang'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(60, $height, $dataPabrikProduksi[0]['pengiriman_fibre'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Utilisasi Pabrik
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Utilisasi Pabrik", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Jumlah hari olah", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Kapasitas olah", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "Utilitas Kapasitas", 1, 0, 'C', 1);
            $pdf->Cell(130, $height, "Utilitas Factor Commercial", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['jumlah_hari_olah'] . " Hari.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['kapasitas_olah'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['utilitas_kapasitas'] . " Kg/Jam.", 1, 0, 'C', 1);
            $pdf->Cell(130, $height, $dataPabrikProduksi[0]['utility_factor_commercial'], 1, 0, 'C', 1);
            $pdf->Ln();

            // Pemakaian Kalsium
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pemakaian Kalsium", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "CaCO3", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, "Rasio Kalsium terhadap TBS", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, "Rasio Kalsium terhadap PK", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['caco3'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(150, $height, $dataPabrikProduksi[0]['rasio_kalsium_tbs'], 1, 0, 'C', 1);
            $pdf->Cell(150, $height, $dataPabrikProduksi[0]['rasio_kalsium_pk'], 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->Ln(); 

            // Press
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Press", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(70, $height, "Jam Press", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Kapasitas Press", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['total_jam_press'] . " Jam.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['kapasitas_press'] . " Kg/Jam.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Stock by Products
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Stock by Products", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Janjang Kosong", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Limbah Cair (POME)", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, "Cangkang (Shell)", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, "Fibre", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Abu Incenerator	", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['stock_product_janjang_kosong'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['stock_product_limbar_cair'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(110, $height, $dataPabrikProduksi[0]['stock_product_cangkang'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(70, $height, $dataPabrikProduksi[0]['stock_product_fibre'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['stock_product_abu_incenerator'] . " Kg.", 1, 0, 'C', 1);
            $pdf->Ln();

            // Pemakaian Solar Genset
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pemakaian Solar Genset", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Solar Genset 1", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Solar Genset 2", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Solar Genset 3", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Solar Genset", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Genset 1", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Genset 2", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Genset 3", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Total HM Genset", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Rasio", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_genset_1'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_genset_2'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_genset_3'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['total_solar_genset'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_genset_1'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_genset_2'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_genset_3'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['total_hm_genset'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['rasio_total_solar_genset_hm_total_genset'] . " HM/Liter.", 1, 0, 'C', 1);
            $pdf->Ln();
            
            // Pemakaian Solar Loader
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell($width, $height, $titleDetail, 0, 1, 'L', 1);
            $pdf->Cell($width, $height, "Pemakaian Solar Loader", 0, 1, 'L', 1);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(90, $height, "Solar Loader 1", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Solar Loader 2", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, "Solar Loader 3", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, "Total Solar Loader", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Loader 1", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Loader 2", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "HM Loader 3", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Total HM Loader", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, "Rasio", 1, 0, 'C', 1);
            $pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_loader_1'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_loader_2'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(90, $height, $dataPabrikProduksi[0]['solar_loader_3'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(100, $height, $dataPabrikProduksi[0]['total_solar_loader'] . " Liter.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_loader_1'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_loader_2'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['hm_loader_3'] . " HM.", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['total_hm_loader'] . " HM", 1, 0, 'C', 1);
            $pdf->Cell(80, $height, $dataPabrikProduksi[0]['rasio_total_solar_loader_hm_total_loader'] . " Liter/HM.", 1, 0, 'C', 1);
            $pdf->Ln();

            

            $pdf->Ln();
            $pdf->Output();
            break;
        case 'excel':
            break;
        default:
            break;
    }

?>