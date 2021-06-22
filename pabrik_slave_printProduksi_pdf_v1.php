<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/zLib.php';
    include_once 'lib/eagrolib.php';
    $tampil = $_GET['tampil'];
    $pabrik = $_GET['pabrik'];
    $periode = $_GET['periode'];
    $tanggal = $_GET['tanggal'];
    $kodeorg = $_GET['kodeorg'];

    function getNamaSupplier($kodeSupplier){
        $query = "SELECT * FROM log_5supplier where supplierid = '".$kodeSupplier."'";
        $queryAct = fetchData($query);

        return $queryAct[0]['namasupplier'];
    }

    // pengaturan general
    $queryGetPengaturanGeneral = "SELECT  * FROM pabrik_5general where kodeorg='".$kodeorg."'";
    $dataPengaturanAdmin = fetchData($queryGetPengaturanGeneral);

    $generalG1 = null;
    $generalG2 = null;
    $generalG3 = null;
    $generalL1 = null;
    $generalL2 = null;
    $generalL3 = null;
    $generalLR = null;
    $OEE = null;

    foreach ($dataPengaturanAdmin as $key => $value) {
        if ($value['code'] == "SL1") {
            $generalL1 = $value['nilai'];
        }
        if ($value['code'] == "SL2") {
            $generalL2 = $value['nilai'];
        }
        if ($value['code'] == "SL3") {
            $generalL3 = $value['nilai'];
        }
        if ($value['code'] == "SLR") {
            $generalLR = $value['nilai'];
        }
        if ($value['code'] == "SG1") {
            $generalG1 = $value['nilai'];
        }
        if ($value['code'] == "SG2") {
            $generalG2 = $value['nilai'];
        }
        if ($value['code'] == "SG3") {
            $generalG3 = $value['nilai'];
        }
        if ($value['code'] == "OEE") {
            $OEE = $value['nilai'];
        }
    }

    // get produksi harian
    $queryGetDataPabrikProduksi = "SELECT * FROM pabrik_produksi where kodeorg='".$_GET['kodeorg']."' AND tanggal='".$_GET['tanggal']."'";
    $dataPabrikProduksi = fetchData($queryGetDataPabrikProduksi);
    
    // get produksi harian sebelum
    $queryGetDataPabrikProduksiSebelum = "  SELECT * FROM pabrik_produksi 
                                            where 
                                                kodeorg = '".$_GET['kodeorg']."' 
                                            AND 
                                                tanggal < '".$_GET['tanggal']."'
                                            
                                            ORDER BY
                                                tanggal DESC
                                            LIMIT 
                                                1
                                                ";
    $dataPabrikProduksiSebelum = fetchData($queryGetDataPabrikProduksiSebelum);



    $pabrik = $dataPabrikProduksi[0]['kodeorg'];
    $periode = $dataPabrikProduksi[0]['tanggal'];

    $queryGetNama = "SELECT namakaryawan FROM datakaryawan WHERE karyawanid = '".$_SESSION['empl']['karyawanid']."'";
    $dataNamaKaryawan = fetchData($queryGetNama);
    $nama = $dataNamaKaryawan[0]['namakaryawan'];


    
    class PDF extends FPDF
    {
        // ukuran dari 470
        public function Header()
        {
            global $namapt;
            global $periode;
            global $pabrik;
            global $nama;
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(20, 5, $namapt, '', 1, 'L');
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(200, 5, strtoupper($_SESSION['lang']['rprodksiPabrik']), 0, 1, 'C');
            $this->Cell(200, 5, 'Tanggal : '.tanggalnormal($periode), 0, 1, 'C');
            $this->Cell(200, 5, $_SESSION['lang']['pabrik'].' : '.$pabrik, 0, 1, 'C');
            $this->SetFont('Arial', '', 8);
            $this->Cell(170, 5, $_SESSION['lang']['tanggal'], 0, 0, 'R');
            $this->Cell(2, 5, ':', '', 0, 'L');
            $this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
            $this->Cell(170, 5, $_SESSION['lang']['page'], '', 0, 'R');
            $this->Cell(2, 5, ':', '', 0, 'L');
            $this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
            $this->Cell(170, 3, 'Pembuat', '', 0, 'R');
            $this->Cell(2, 3, ':', '', 0, 'L');
            $this->Cell(35, 3, $nama, '', 1, 'L');
            $this->Ln();
        }
    }

    
    
    // get produksi harian sebulan
    $queryGetDataPabrikProduksiBulanan = " SELECT 
                                    sum(tbs_sisa_kemarin) as tbs_sisa_kemarin,
                                    sum(tbs_sisa) as tbs_sisa,
                                    sum(tbs_diolah) as tbs_diolah,
                                    sum(tbs_potongan) as tbs_potongan,
                                    sum(tbs_after_grading) as tbs_after_grading,
                                    sum(cpo_produksi) as cpo_produksi,
                                    sum(kernel_produksi) as kernel_produksi,
                                    sum(pengiriman_despatch_cpo) as pengiriman_despatch_cpo,
                                    sum(pengiriman_return_cpo) as pengiriman_return_cpo,
                                    sum(pengiriman_despatch_pk) as pengiriman_despatch_pk,
                                    sum(pengiriman_return_pk) as pengiriman_return_pk,
                                    sum(pengiriman_janjang_kosong) as pengiriman_janjang_kosong,
                                    sum(pengiriman_limbah_kosong) as pengiriman_limbah_kosong,
                                    sum(pengiriman_solid_decnter) as pengiriman_solid_decnter,
                                    sum(pengiriman_abu_janjang) as pengiriman_abu_janjang,
                                    sum(pengiriman_cangkang) as pengiriman_cangkang,
                                    sum(pengiriman_fibre) as pengiriman_fibre,
                                    sum(caco3) as caco3,
                                    sum(jumlah_hari_olah) as jumlah_hari_olah,
                                    sum(kapasitas_olah) as kapasitas_olah,
                                    sum(utilitas_kapasitas) as utilitas_kapasitas,
                                    sum(utility_factor_commercial) as utility_factor_commercial,
                                    sum(total_jam_press) as total_jam_press,
                                    sum(kapasitas_press) as kapasitas_press,
                                    sum(rasio_kalsium_tbs) as rasio_kalsium_tbs,
                                    sum(rasio_kalsium_pk) as rasio_kalsium_pk,
                                    sum(solar_genset_1) as solar_genset_1,
                                    sum(solar_genset_2) as solar_genset_2,
                                    sum(solar_genset_3) as solar_genset_3,
                                    sum(solar_loader_1) as solar_loader_1,
                                    sum(solar_loader_2) as solar_loader_2,
                                    sum(solar_loader_3) as solar_loader_3,
                                    sum(lori_rata_rata) as lori_rata_rata,
                                    
                                    sum(total_jam_operasi) as total_jam_operasi
                                    FROM pabrik_produksi 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($_GET['tanggal'],0,8)."01'
                                    AND
                                    tanggal <= '".$_GET['tanggal']."'
                                    )
                                    ";
    $dataPabrikProduksiBulanan = fetchData($queryGetDataPabrikProduksiBulanan);

    // get produksi harian setahun
    $queryGetDataPabrikProduksiTahunan = " SELECT 
                                    sum(cpo_produksi) as cpo_produksi,
                                    sum(kernel_produksi) as kernel_produksi
                                    FROM pabrik_produksi 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($_GET['tanggal'],0,5)."01-01'
                                    AND
                                    tanggal <= '".$_GET['tanggal']."'
                                    )
                                    ";
    $dataPabrikProduksiTahunan = fetchData($queryGetDataPabrikProduksiTahunan);

    // get produksi harian sebulan all
    $queryGetDataPabrikProduksiBulananAll = " SELECT 
                                    *
                                    FROM pabrik_produksi 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($_GET['tanggal'],0,8)."01'
                                    AND
                                    tanggal <= '".$_GET['tanggal']."'
                                    )
                                    ";
    $dataPabrikProduksiBulananAll = fetchData($queryGetDataPabrikProduksiBulananAll);
    $jumlahRendemenCpoBefore = 0;
    $jumlahRendemenCpoAfter = 0;
    $jumlahFfaCPO = 0;
    $jumlahKadarAirCPO = 0;
    $jumlahKadarKotoranCPO = 0;
    $jumlahUtilitasKapasitas = 0;
    $i = 1;
    foreach ($dataPabrikProduksiBulananAll as $key => $value) {
        $hasilProductRendemenCpoBefore = $value['rendemen_cpo_before'] * $value['cpo_produksi'];
        $jumlahRendemenCpoBefore += $hasilProductRendemenCpoBefore;
        
        
        $hasilProductRendemenCpoAfter = $value['rendemen_cpo_after'] * $value['cpo_produksi'];
        $jumlahRendemenCpoAfter += $hasilProductRendemenCpoAfter;
        
        $hasilProductKadarAirCPO = $value['cpo_kadar_air'] * $value['cpo_produksi'];
        $jumlahKadarAirCPO += $hasilProductKadarAirCPO;
        
        $hasilProductFfaCPO = $value['cpo_ffa'] * $value['cpo_produksi'];
        $jumlahFfaCPO += $hasilProductFfaCPO;
        
        $hasilProductKadarKotoranCPO = $value['cpo_kotoran'] * $value['cpo_produksi'];
        $jumlahKadarKotoranCPO += $hasilProductKadarKotoranCPO;
        
        $hasilProductMICPO = ($value['cpo_kadar_air'] + $value['cpo_kotoran']) * $value['cpo_produksi'];
        $jumlahMICPO += $hasilProductMICPO;
        
        $hasilProductKadarAirPK = $value['kernel_kadar_air'] * $value['kernel_produksi'];
        $jumlahKadarAirPK += $hasilProductKadarAirPK;
        
        $hasilProductKadarKotoranPK = $value['kernel_kotoran'] * $value['kernel_produksi'];
        $jumlahKadarKotoranPK += $hasilProductKadarKotoranPK;
        
        $hasilProductIntiPecahPK = $value['kernel_inti_pecah'] * $value['kernel_produksi'];
        $jumlahIntiPecahPK += $hasilProductIntiPecahPK;

        $hasilProductUtilitasKapasitas = $value['utilitas_kapasitas'] * $value['cpo_produksi'];
        $jumlahUtilitasKapasitas += $hasilProductUtilitasKapasitas;
        
        $hasilProductFactorCommercial = $value['utility_factor_commercial'] * $value['cpo_produksi'];
        $jumlahFactorCommercial += $hasilProductFactorCommercial;
        
        
        $hasilProductMuatanLoriSteriser = $value['lori_rata_rata'] * $value['cpo_produksi'];
        $jumlahMuatanLoriSteriser += $hasilProductMuatanLoriSteriser;
        
        $hasilProductKapasitasPress = $value['total_jam_press'] * $value['cpo_produksi'];
        $jumlahKapasitasPress += $hasilProductKapasitasPress;
        


    }
    
                          
    // get timbangan
    $queryGetDataPabrikTimbangan = "SELECT * FROM pabrik_timbangan 
                                    WHERE 
                                    notransaksi LIKE 'M%'
                                    AND
                                    millcode='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                                    AND
                                    tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                                    )
                                    ";
    $dataPabrikTimbangan = fetchData($queryGetDataPabrikTimbangan);

    // get pengolahan pabrik
    $queryDataPengolahanPabrik = '  SELECT * FROM pabrik_pengolahan
            WHERE 
            kodeorg="'.$_GET['kodeorg'].'" 
            AND
            tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
            posting = 1';
    $dataPengolahanPabrik = fetchData($queryDataPengolahanPabrik);

    $queryGetDataPengolahanPabrikBulanan = " SELECT 
                                    sum(total_jam_shift_1) as total_jam_shift_1,
                                    sum(total_jam_shift_2) as total_jam_shift_2,
                                    sum(jam_idle) as jam_idle,
                                    sum(jam_idle_shift_2) as jam_idle_shift_2,
                                    sum(jam_stagnasi) as jam_stagnasi,
                                    sum(lori_olah_shift_1) as lori_olah_shift_1,
                                    sum(lori_olah_shift_2) as lori_olah_shift_2,
                                    sum(rata_rata_lori) as rata_rata_lori
                                    
                                    FROM pabrik_pengolahan 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($_GET['tanggal'],0,8)."01' - INTERVAL 1 DAY
                                    AND
                                    tanggal <= '".$_GET['tanggal']."' - INTERVAL 1 DAY
                                    )
                                    ";
    $dataPengolahanPabrikBulanan = fetchData($queryGetDataPengolahanPabrikBulanan);
    
    $ukuranFontJudul = 10;
    $ukuranFontParagraf = 7;

    $ukuranKolomKosong = 55;
    $ukuranKolomSatuan = 18;

    $ukuranHI = 20;
    $ukuranSDHI = 20;
    $ukuranKolomAngBulanIni = 20;

    $ukuranKolomRealSDBulanIni = 15;
    $ukuranKolomAngSDBulanIni = 15;

    $ukuranKolomAngSetahun = 34;

    $ukuranKolomRealBulanIni = $ukuranHI + $ukuranSDHI;
    $ukuranKolomBulanIni = $ukuranKolomRealBulanIni + $ukuranKolomAngBulanIni ;

    $ukuranKolomSDBulanIni = $ukuranKolomAngSDBulanIni + $ukuranKolomRealSDBulanIni;   
    
    $ukuranKolomKosongSatuan = $ukuranKolomKosong + $ukuranKolomSatuan ;

    $ukuranTotal = $ukuranKolomKosong + $ukuranKolomSatuan + $ukuranKolomBulanIni + $ukuranKolomSDBulanIni + $ukuranKolomAngSetahun;
    
    $pdf = new PDF('P', 'mm', 'LEGAL');
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ukuranKolomKosong, 15, ' ', 1, 0, 'C');
    $pdf->Cell($ukuranKolomSatuan, 15, "SATUAN", 1, 0, 'C');
    $pdf->Cell($ukuranKolomBulanIni, 5, "BULAN INI", 1, 0, 'C');
    $pdf->Cell($ukuranKolomSDBulanIni, 5, "S.D.  BULAN INI", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 15, "ANG. SETAHUN", 1, 0, 'C');
    $pdf->Cell(0, 5, "", 0, 1, 'C');
    
    $pdf->Cell($ukuranKolomKosongSatuan, 5, "", 0, 0, 'C');
    $pdf->Cell($ukuranKolomRealBulanIni, 5, "REAL", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 10, "ANG.", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 10, "REAL.", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 10, "ANG.", 1, 0, 'C');
    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->Cell($ukuranKolomKosongSatuan, 5, "", 0, 0, 'C');
    $pdf->Cell($ukuranHI, 5, "H.I", 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, "S.D. H.I", 1, 0, 'C');
    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($ukuranTotal, 5, "PENGOLAHAN TBS", 1, 1, 'L');

    $pdf->Cell($ukuranKolomKosong, 5, "STOCK AWAL", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['tbs_sisa_kemarin'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksi[0]['tbs_sisa_kemarin'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    // $pdf->Cell($ukuranTotal, 5, "PENERIMAAN TBS", 1, 1, 'L');
    
    $jumlahPersediaanTBS = 0;
    $jumlahPersediaanTBS = $jumlahPersediaanTBS+$dataPabrikProduksi[0]['tbs_sisa_kemarin'];
    foreach ($dataPabrikTimbangan as $keyPabrikTimbangan => $valuePabrikTimbangan) {
        // $pdf->Cell($ukuranKolomKosong, 5, "     - ".getNamaSupplier($valuePabrikTimbangan['kodecustomer']), 1, 0, 'L');
        // $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
        // $pdf->Cell($ukuranHI, 5, $valuePabrikTimbangan['beratnormal'], 1, 0, 'C');
        // $pdf->Cell($ukuranSDHI, 5, "", 1, 0, 'C');
        // $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
        // $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
        // $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
        // $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');
        $jumlahPersediaanTBS = $jumlahPersediaanTBS + $valuePabrikTimbangan['beratnormal'];
    }
    

    $pdf->Cell($ukuranKolomKosong, 5, "JUMLAH PERSEDIAAN TBS", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $jumlahPersediaanTBS, 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "POTONGAN SORTASI", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['tbs_potongan'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['tbs_potongan'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "TBS DIOLAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."TBS OLAH Before", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['tbs_diolah'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['tbs_diolah'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."TBS OLAH After", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['tbs_after_grading'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['tbs_after_grading'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "STOCK AKHIR", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['tbs_sisa'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksi[0]['tbs_sisa'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "PRODUKSI", 1, 1, 'L');

    $pdf->Cell($ukuranKolomKosong, 5, "CRUDE PALM OIL (CPO)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['cpo_produksi'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['cpo_produksi'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "RENDEMEN CPO", 1, 1, 'L');
    $sumProductRendemenCPOBefore = $jumlahRendemenCpoBefore / $dataPabrikProduksiBulanan[0]['tbs_diolah'];
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."Rendemen Before", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rendemen_cpo_before'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($dataPabrikProduksiBulanan[0]['cpo_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 100,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $sumProductRendemenCPOAfter = round($jumlahRendemenCpoAfter / $dataPabrikProduksiBulanan[0]['tbs_after_grading'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."Rendemen After", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rendemen_cpo_after'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($dataPabrikProduksiBulanan[0]['cpo_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_after_grading'] * 100,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "MUTU PRODUKSI CPO", 1, 1, 'L');
    $sumProductFfaCPO = round($jumlahFfaCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."ALB", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['cpo_ffa'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductFfaCPO, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $sumProductKadarAirCPO = round($jumlahKadarAirCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."KADAR AIR", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['cpo_kadar_air'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductKadarAirCPO, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $sumProductKadarKotoranCPO = round($jumlahKadarKotoranCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],3);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."KADAR KOTORAN", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['cpo_kotoran'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductKadarKotoranCPO, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $mANDb = $dataPabrikProduksi[0]['cpo_kadar_air'] + $dataPabrikProduksi[0]['cpo_kotoran'];
    $sumProductMICPO = round($jumlahMICPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],3);

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."M&I", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $mANDb, 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductMICPO, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "PALM KERNEL (PK)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kernel_produksi'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['kernel_produksi'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "RENDEMEN PK", 1, 1, 'L');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."Rendemen Before", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rendemen_pk_before'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($dataPabrikProduksiBulanan[0]['kernel_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 100,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."Rendemen After", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rendemen_pk_after'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($dataPabrikProduksiBulanan[0]['kernel_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_after_grading'] * 100,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "MUTU PRODUKSI PK", 1, 1, 'L');

    // $pdf->Cell(100, 5, "     - "."ALB", 1, 0, 'L');
    // $pdf->Cell(40, 5, "%", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['rendemen_pk_before'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');
    $sumProductKadarAirPK = round($jumlahKadarAirPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."KADAR AIR", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kernel_kadar_air'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductKadarAirPK, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $sumProductKadarKotoranPK = round($jumlahKadarKotoranPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);    
    $sumProductIntiPecahPK = round($jumlahIntiPecahPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);    
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."KADAR KOTORAN", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kernel_kotoran'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductKadarKotoranPK, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');
    
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."INTI PECAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kernel_inti_pecah'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $sumProductIntiPecahPK, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "JANJANG KOSONG (EFB)", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_janjang_kosong'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "LIMBAH CAIR (POME)", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_limbar_cair'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "SOLID DECANTER", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_janjang_kosong'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "ABU JANJANG (BUNCH ASH)", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_abu_incenerator'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "CANGKANG (SHELL)", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_cangkang'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    // $pdf->Cell(100, 5, "FIBRE", 1, 0, 'L');
    // $pdf->Cell(40, 5, "KG", 1, 0, 'C');
    // $pdf->Cell(20, 5, $dataPabrikProduksi[0]['stock_product_fibre'], 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(20, 5, "", 1, 0, 'C');
    // $pdf->Cell(90, 5, "", 1, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ukuranTotal, 5, "PENGIRIMAN", 1, 1, 'L');
    $pdf->SetFont('Arial', '', 8);

    $pdf->Cell($ukuranKolomKosong, 5, "CRUDE PALM OIL (CPO)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_despatch_cpo'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_despatch_cpo'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "Return CPO", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_return_cpo'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_return_cpo'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "PALM KERNEL (PK)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_despatch_pk'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_despatch_pk'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "Return PK", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_return_pk'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_return_pk'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "JANJANG KOSONG (EFB)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_janjang_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_janjang_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "LIMBAH CAIR (POME)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_limbah_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_limbah_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "SOLID DECANTER", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_solid_decnter'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_solid_decnter'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "ABU JANJANG (BUNCH ASH)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan,5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_abu_janjang'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_abu_janjang'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "CANGKANG (SHELL)", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_cangkang'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_cangkang'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "FIBRE", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['pengiriman_fibre'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['pengiriman_fibre'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "UTILISASI PABRIK", 1, 1, 'L');

    $totalJamShift = $dataPengolahanPabrik[0]['total_jam_shift_1'] + $dataPengolahanPabrik[0]['total_jam_shift_2'];

    $pdf->Cell($ukuranKolomKosong, 5, "JAM SHIFT", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "JAM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $totalJamShift, 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPengolahanPabrikBulanan[0]['total_jam_shift_1'] + $dataPengolahanPabrikBulanan[0]['total_jam_shift_2'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "JAM OLAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "JAM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['total_jam_operasi'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['total_jam_operasi'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $totalJamIdle = $dataPengolahanPabrik[0]['jam_idle'] + $dataPengolahanPabrik[0]['jam_idle_shift_2'];
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."JAM TIDAK PRODUKTIF", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "JAM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $totalJamIdle, 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPengolahanPabrikBulanan[0]['jam_idle'] + $dataPengolahanPabrikBulanan[0]['jam_idle_shift_2'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."JAM DOWNTIME", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "JAM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPengolahanPabrik[0]['jam_stagnasi'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPengolahanPabrikBulanan[0]['jam_stagnasi'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "JUMLAH HARI OLAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "HARI", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['jumlah_hari_olah'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['jumlah_hari_olah'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    // rumus KAPASITAS OLAH 
    $kapasitasOlahSDHI = $dataPabrikProduksiBulanan[0]['tbs_diolah'] / $dataPabrikProduksiBulanan[0]['total_jam_operasi'];
    $pdf->Cell($ukuranKolomKosong, 5, "KAPASITAS OLAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG/JAM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kapasitas_olah'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $kapasitasOlahSDHI, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $utilitasKapasitasSDHI = $jumlahUtilitasKapasitas / $dataPabrikProduksiBulanan[0]['cpo_produksi'];
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."UTILISASI KAPASITAS", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['utilitas_kapasitas'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($utilitasKapasitasSDHI,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $utilityFactorCommercialSDHI = round($jumlahFactorCommercial / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "UTILITY FACTOR COMMERCIAL", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "%", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['utility_factor_commercial'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $utilityFactorCommercialSDHI, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $jumlahLori = $dataPengolahanPabrik[0]['lori_olah_shift_1'] + $dataPengolahanPabrik[0]['lori_olah_shift_2'];
    $pdf->Cell($ukuranKolomKosong, 5, "LORI / STERILISER OLAH", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "UNIT", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $jumlahLori, 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPengolahanPabrikBulanan[0]['lori_olah_shift_1'] + $dataPengolahanPabrikBulanan[0]['lori_olah_shift_2'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $muatanLoriSteriserSDHI = round($jumlahMuatanLoriSteriser / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."MUATAN LORI / STERILISER", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG/UNIT", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPengolahanPabrik[0]['rata_rata_lori'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $muatanLoriSteriserSDHI, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranKolomKosong, 5, "JAM PRESS", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "HM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['total_jam_press'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['total_jam_press'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $kapasitasPressSDHI = round($jumlahKapasitasPress / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."KAPASITAS PRESS", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG/HM", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['kapasitas_press'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, round($kapasitasPressSDHI,2), 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Cell($ukuranTotal, 5, "PEMAKAIAN KALSIUM", 1, 1, 'L');

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."CALSIUM", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['caco3'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $dataPabrikProduksiBulanan[0]['caco3'], 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $rasioCalsiumTBSBulanan = round($dataPabrikProduksiBulanan[0]['caco3'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 1000,2);

    $pdf->Cell($ukuranKolomKosong, 5, "     - "."RASIO CALSIUM TERHADAP TBS", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG/KG TBS", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rasio_kalsium_tbs'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $rasioCalsiumTBSBulanan, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $rasioCalsiumPKBulanan = round($dataPabrikProduksiBulanan[0]['caco3'] / $dataPabrikProduksiBulanan[0]['kernel_produksi'] * 1000,2);
    $pdf->Cell($ukuranKolomKosong, 5, "     - "."RASIO CALSIUM TERHADAP PK", 1, 0, 'L');
    $pdf->Cell($ukuranKolomSatuan, 5, "KG/KG PK", 1, 0, 'C');
    $pdf->Cell($ukuranHI, 5, $dataPabrikProduksi[0]['rasio_kalsium_pk'], 1, 0, 'C');
    $pdf->Cell($ukuranSDHI, 5, $rasioCalsiumPKBulanan, 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomRealSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSDBulanIni, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuranKolomAngSetahun, 5, "", 1, 1, 'C');

    $pdf->Ln();





    // ISIAN 2 (330)

    $ukuran2Kosong = 52;

    $ukuran2LTSSTD = 15;
    $ukuran2LTSHI = 17.5;
    $ukuran2LTSSDHI = 20;
    $ukuran2LTSSDBI = 20;

    $ukuran2LTS = $ukuran2LTSSTD + $ukuran2LTSHI + $ukuran2LTSSDHI + $ukuran2LTSSDBI;
    $ukuran2LTT = $ukuran2LTSSTD + $ukuran2LTSHI + $ukuran2LTSSDHI + $ukuran2LTSSDBI;

    $ukuran2full = $ukuran2Kosong + $ukuran2LTS + $ukuran2LTT;
    
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell($ukuran2Kosong, 10, ' ', 1, 0, 'C');
    $pdf->Cell($ukuran2LTS, 5, "LOSSES TERHADAP SAMPLE (%)", 1, 0, 'C');
    $pdf->Cell($ukuran2LTT, 5, "LOSSES TERHADAP TBS (%)", 1, 0, 'C');
    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->Cell($ukuran2Kosong, 5, "", 0, 0, 'C');

    $pdf->Cell($ukuran2LTSSTD, 5, "STD", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "HI.", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "S.D.  H. I.", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "S.D.  B. I.", 1, 0, 'C');

    $pdf->Cell($ukuran2LTSSTD, 5, "STD", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "HI.", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "S.D.  H. I.", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "S.D.  B. I.", 1, 0, 'C');

    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ukuran2full, 5, "LOSSES", 1, 1, 'L');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($ukuran2full, 5, "OIL LOSSES (".tanggalnormal($dataPabrikProduksi[0]['cpo_loses_tanggal']).")", 1, 1, 'L');
    
    // HARIAN
    $getTransaksiLosesCPO = "   SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                                JOIN pabrik_5kelengkapanloses b
                                ON a.id = b.id
                                WHERE 
                                b.losses_to_tbs > 0
                                AND
                                b.produk = 'CPO'
                                AND
                                a.kodeorg = '".$kodeorg."'
                                AND
                                a.tanggal = '".$dataPabrikProduksi[0]['cpo_loses_tanggal']."'
                                ";
                            
    $dataTransaksiLosesCPO = fetchData($getTransaksiLosesCPO);

    // TANGGAL LOSES SETAHUN
    $queryGetTanggalLosesTahunan = "  SELECT 
                                                cpo_loses_tanggal, kernel_loses_tanggal,
                                                cpo_usb, cpo_empty_bunch, cpo_fibre_cyclone, cpo_nut_from_polishingdrum, cpo_effluent,
                                                kernel_loses_usb, kernel_loses_fibre_cyclone, kernel_loses_ltds_1, kernel_loses_ltds_2, kernel_loses_claybath,
                                                kernel_produksi, cpo_produksi
                                            FROM 
                                                pabrik_produksi 
                                            WHERE 
                                                kodeorg='".$_GET['kodeorg']."' 
                                            AND 
                                                (
                                                tanggal >= '".substr($_GET['tanggal'],0,5)."01-01'
                                                AND
                                                tanggal <= '".$_GET['tanggal']."'
                                                )
                                            ";
    $dataTanggalLosesTahunan = fetchData($queryGetTanggalLosesTahunan);
    
    // TANGGAL LOSES SEBULAN
    $queryGetTanggalLosesBulanan = "  SELECT 
                                                cpo_loses_tanggal, kernel_loses_tanggal,
                                                cpo_usb, cpo_empty_bunch, cpo_fibre_cyclone, cpo_nut_from_polishingdrum, cpo_effluent,
                                                kernel_loses_usb, kernel_loses_fibre_cyclone, kernel_loses_ltds_1, kernel_loses_ltds_2, kernel_loses_claybath,
                                                kernel_produksi, cpo_produksi
                                            FROM 
                                                pabrik_produksi 
                                            WHERE 
                                                kodeorg='".$_GET['kodeorg']."' 
                                            AND 
                                                (
                                                tanggal >= '".substr($_GET['tanggal'],0,8)."01'
                                                AND
                                                tanggal <= '".$_GET['tanggal']."'
                                                )
                                            ";
    $dataTanggalLosesBulanan = fetchData($queryGetTanggalLosesBulanan);

    $tanggalLosesCPO = null;
    $tanggalLosesPK = null;
    $runNumber = 0;
    foreach ($dataTanggalLosesBulanan as $key => $value) {
        $tanggalLosesCPO[$runNumber] = $value['cpo_loses_tanggal'];
        $tanggalLosesPK[$runNumber] = $value['kernel_loses_tanggal'];
        $runNumber++;
    }
    
    $jumlahLosesToTBS = 0;
    $jumlahLosesToTBSHI = 0;
    $jumlahLosesToTBSSDHI = 0;
    $jumlahLosesToTBSSDBI = 0;
    foreach ($dataTransaksiLosesCPO as $key => $value) {
        // LOSES TERHADAP TBS
        $dataTerhadapTBSSDHI[$value['linked_to']] = 0;
        $dataTerhadapTBSSDBI[$value['linked_to']] = 0;
        
        // LOSSES TERHADAP SAMPLE
        $dataTerhadapSampleSDHI[$value['linked_to']] = 0;
        $dataTerhadapSampleSDBI[$value['linked_to']] = 0;
        
        $temp = 0;
        foreach ($dataTanggalLosesBulanan as $key3 => $value3) {
            // UNTUK SAMPLE
            // GET LOSES TANGGAL
            $getTransaksiLosesCPOB = "   SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                                        JOIN pabrik_5kelengkapanloses b
                                        ON a.id = b.id
                                        WHERE 
                                        b.losses_to_tbs > 0
                                        AND
                                        b.produk = 'CPO'
                                        AND
                                        a.kodeorg = '".$kodeorg."'
                                        AND
                                        a.tanggal = '".$value3['cpo_loses_tanggal']."'
                                        ";
                        
            $dataTransaksiLosesCPOB = fetchData($getTransaksiLosesCPOB); 
            foreach ($dataTransaksiLosesCPOB as $key5 => $value5) {
                $temp4 = $value5['nilai'] * $value3['cpo_produksi'];
                $dataTerhadapSampleSDHI[$value5['namaitem']] += $temp4;
            }
            // UNTUK TBS
            $temp = $value3[$value['linked_to']] * $value3['cpo_produksi'];
            $dataTerhadapTBSSDHI[$value['linked_to']] += $temp;
        }
        
        $temp2 = 0;
        foreach ($dataTanggalLosesTahunan as $key4 => $value4) {
            // GET LOSES TANGGAL
            $getTransaksiLosesCPOC = "   SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                                        JOIN pabrik_5kelengkapanloses b
                                        ON a.id = b.id
                                        WHERE 
                                        b.losses_to_tbs > 0
                                        AND
                                        b.produk = 'CPO'
                                        AND
                                        a.kodeorg = '".$kodeorg."'
                                        AND
                                        a.tanggal = '".$value4['cpo_loses_tanggal']."'
                                        ";
                        
            $dataTransaksiLosesCPOC = fetchData($getTransaksiLosesCPOC); 
            foreach ($dataTransaksiLosesCPOC as $key6 => $value6) {
                $temp4 = $value6['nilai'] * $value4['cpo_produksi'];
                $dataTerhadapSampleSDBI[$value6['namaitem']] += $temp4;
            }
            $temp2 = $value4[$value['linked_to']] * $value4['cpo_produksi'];
            $dataTerhadapTBSSDBI[$value['linked_to']] += $temp2;
        }

        
        
        $pdf->Cell($ukuran2Kosong, 5, "        -  ".$value['namaitem'], 1, 0, 'L');
        $pdf->Cell($ukuran2LTSSTD, 5, $value['standard'].$value['standardtoffb'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSHI, 5, $value['nilai'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDHI, 5, round($dataTerhadapSampleSDHI[$value['namaitem']] / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDBI, 5, round($dataTerhadapSampleSDBI[$value['namaitem']] / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSTD, 5, $value['losses_to_tbs'].$value['satuan'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSHI, 5, $dataPabrikProduksi[0][$value['linked_to']], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDHI, 5, round($dataTerhadapTBSSDHI[$value['linked_to']] / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDBI, 5, round($dataTerhadapTBSSDBI[$value['linked_to']] / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2), 1, 1, 'C');
        $jumlahLosesToTBS += $value['losses_to_tbs'];
        $jumlahLosesToTBSHI += $dataPabrikProduksi[0][$value['linked_to']];
        $jumlahLosesToTBSSDHI += round($dataTerhadapTBSSDHI[$value['linked_to']] / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
        $jumlahLosesToTBSSDBI += round($dataTerhadapTBSSDBI[$value['linked_to']] / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2);
    }

    $pdf->Cell($ukuran2Kosong, 5, "JUMLAH OIL LOSSES", 1, 0, 'L');
    $pdf->Cell($ukuran2LTSSTD, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSTD, 5, $jumlahLosesToTBS.$value['satuan'], 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, $jumlahLosesToTBSHI, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, $jumlahLosesToTBSSDHI, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, $jumlahLosesToTBSSDBI, 1, 1, 'C');

    $oilExtractionHiToTBS = round($dataPabrikProduksi[0]['rendemen_cpo_after'] / (($dataPabrikProduksi[0]['rendemen_cpo_after'] + $jumlahLosesToTBS) / 100),2);
    $oilExtractionSTDToTBS = round(($OEE / (($OEE + $jumlahLosesToTBS) / 100)),2);
    $pdf->Cell($ukuran2Kosong, 5, "OIL EXTRACTION EFFICIENCY", 1, 0, 'L');
    $pdf->Cell($ukuran2LTSSTD, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSTD, 5, $oilExtractionSTDToTBS, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, $oilExtractionHiToTBS, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "?", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "?", 1, 1, 'C');

    

    $pdf->Cell($ukuran2Kosong, 5, "KERNEL LOSSES (".tanggalnormal($dataPabrikProduksi[0]['kernel_loses_tanggal']).")", 1, 0, 'L');
    $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');

    $getTransaksiLosesPK = "  SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                            JOIN pabrik_5kelengkapanloses b
                            ON a.id = b.id
                            WHERE 
                            b.losses_to_tbs > 0
                            AND
                            b.produk = 'KERNEL'
                            AND
                            a.kodeorg = '".$kodeorg."'
                            AND
                            a.tanggal = '".$dataPabrikProduksi[0]['kernel_loses_tanggal']."'
                            ";
    $getTransaksiLosesPK = fetchData($getTransaksiLosesPK);

    $jumlahLosesPK = 0;
    $jumlahLosesPKHI = 0;
    $jumlahLosesPKSDHI = 0;
    $jumlahLosesPKSDBI = 0;
    foreach ($getTransaksiLosesPK as $key => $value) {
        // LOSES TERHADAP TBS
        $dataTerhadapTBSSDHIPK[$value['linked_to']] = 0;
        $dataTerhadapTBSSDBIPK[$value['linked_to']] = 0;
        
        // LOSSES TERHADAP SAMPLE
        $dataTerhadapSampleSDHIPK[$value['namaitem']] = 0;
        $dataTerhadapSampleSDBIPK[$value['namaitem']] = 0;
        
        $temp = 0;
        foreach ($dataTanggalLosesBulanan as $key3 => $value3) {
            // UNTUK SAMPLE
            // GET LOSES TANGGAL
            $getTransaksiLosesCPOB = "   SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                                        JOIN pabrik_5kelengkapanloses b
                                        ON a.id = b.id
                                        WHERE 
                                        b.losses_to_tbs > 0
                                        AND
                                        b.produk = 'KERNEL'
                                        AND
                                        a.kodeorg = '".$kodeorg."'
                                        AND
                                        a.tanggal = '".$value3['cpo_loses_tanggal']."'
                                        ";
                        
            $dataTransaksiLosesCPOB = fetchData($getTransaksiLosesCPOB); 
            // echo $value3['kernel_produksi'];
            // echo "<br>";
            // pre($dataTransaksiLosesCPOB);
            // echo "<br>";
            foreach ($dataTransaksiLosesCPOB as $key5 => $value5) {
                $temp4 = $value5['nilai'] * $value3['kernel_produksi'];
                // echo $value5['namaitem']." - ".$temp4."<br>";
                $dataTerhadapSampleSDHIPK[$value5['namaitem']] += $temp4;
                // pre($dataTerhadapSampleSDHIPK);
            }
        //     pre($dataTerhadapSampleSDHI);
        // die();
            // UNTUK TBS
            $temp = $value3[$value['linked_to']] * $value3['kernel_produksi'];
            $dataTerhadapTBSSDHIPK[$value['linked_to']] += $temp;
        }
        // pre($dataTerhadapSampleSDHIPK);
        // die();
        
        $temp2 = 0;
        foreach ($dataTanggalLosesTahunan as $key4 => $value4) {
            // GET LOSES TANGGAL
            $getTransaksiLosesCPOC = "   SELECT b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai FROM pabrik_kelengkapanloses a
                                        JOIN pabrik_5kelengkapanloses b
                                        ON a.id = b.id
                                        WHERE 
                                        b.losses_to_tbs > 0
                                        AND
                                        b.produk = 'KERNEL'
                                        AND
                                        a.kodeorg = '".$kodeorg."'
                                        AND
                                        a.tanggal = '".$value4['cpo_loses_tanggal']."'
                                        ";
                        
            $dataTransaksiLosesCPOC = fetchData($getTransaksiLosesCPOC); 
            foreach ($dataTransaksiLosesCPOC as $key6 => $value6) {
                $temp4 = $value6['nilai'] * $value4['kernel_produksi'];
                $dataTerhadapSampleSDBIPK[$value6['namaitem']] += $temp4;
            }
            $temp2 = $value4[$value['linked_to']] * $value4['kernel_produksi'];
            $dataTerhadapTBSSDBIPK[$value['linked_to']] += $temp2;
        }
        
        $pdf->Cell($ukuran2Kosong, 5, "        -  ".$value['namaitem'], 1, 0, 'L');
        $pdf->Cell($ukuran2LTSSTD, 5, $value['standard'].$value['standardtoffb'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSHI, 5, $value['nilai'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDHI, 5, round($dataTerhadapSampleSDHIPK[$value['namaitem']] / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDBI, 5, round($dataTerhadapSampleSDBIPK[$value['namaitem']] / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSTD, 5, $value['losses_to_tbs'].$value['satuan'], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSHI, 5, $dataPabrikProduksi[0][$value['linked_to']], 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDHI, 5, round($dataTerhadapTBSSDHIPK[$value['linked_to']] / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2), 1, 0, 'C');
        $pdf->Cell($ukuran2LTSSDBI, 5, round($dataTerhadapTBSSDBIPK[$value['linked_to']] / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2), 1, 1, 'C');
        
        $jumlahLosesPK += $value['losses_to_tbs'];
        $jumlahLosesPKHI += round($dataTerhadapTBSSDHIPK[$value['linked_to']] / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);
        $jumlahLosesPKsdHI += round($dataTerhadapTBSSDBIPK[$value['linked_to']] / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2);
    }

    $pdf->Cell($ukuran2Kosong, 5, "JUMLAH KERNEL LOSSES", 1, 0, 'L');
    $pdf->Cell($ukuran2LTSSTD, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSTD, 5, $jumlahLosesPK, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, $jumlahLosesPKHI, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, $jumlahLosesPKHI, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, $jumlahLosesPKsdHI, 1, 1, 'C');
    
    $kernelExtractionHiToTBS = round($dataPabrikProduksi[0]['rendemen_pk_after'] / (($dataPabrikProduksi[0]['rendemen_pk_after'] + $jumlahLosesPK) / 100),2);
    $kernelExtractionSTDToTBS = round(($OEE / (($OEE + $jumlahLosesPK) / 100)),2);
    $pdf->Cell($ukuran2Kosong, 5, "KERNEL EXTRACTION EFFICIENCY", 1, 0, 'L');
    $pdf->Cell($ukuran2LTSSTD, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSTD, 5, $kernelExtractionSTDToTBS, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSHI, 5, $kernelExtractionHiToTBS, 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDHI, 5, "?", 1, 0, 'C');
    $pdf->Cell($ukuran2LTSSDBI, 5, "?", 1, 1, 'C');

    // $pdf->Cell($ukuran2Kosong, 5, "CONTROL OIL LOSSES", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');
    

    // $pdf->Cell($ukuran2Kosong, 5, "        -  "."PRESS FIBRE", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');

    // $pdf->Cell($ukuran2Kosong, 5, "        -  "."SLUDGE SEPARATOR", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');

    // $pdf->Cell($ukuran2Kosong, 5, "        -  "."COND. IN-RECOVERY", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');

    // $pdf->Cell($ukuran2Kosong, 5, "        -  "."COND. EX-RECOVERY", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');

    // $pdf->Cell($ukuran2Kosong, 5, "        -  "."DECANTER H. PHASE", 1, 0, 'L');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSTD, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDHI, 5, "", 1, 0, 'C');
    // $pdf->Cell($ukuran2LTSSDBI, 5, "", 1, 1, 'C');
    
    $pdf->Ln();

    $ukuran3kosong = 40;
    
    $ukuran3Awal = 15;
    $ukuran3Akhir = 15;
    $ukuran3HMGHI = $ukuran3Awal + $ukuran3Akhir;
    
    $ukuran3JHMGHI = 15;
    $ukuran3JHMGSDHI = 15;
    $ukuran3JHMGSDBI = 15;

    $ukuran3JHMG = $ukuran3JHMGHI + $ukuran3JHMGSDHI + $ukuran3JHMGSDBI;

    $ukuran3KBHI = 15;
    $ukuran3KBSDHI = 16.5;
    $ukuran3KBSDBI = 16.5;
    $ukuran3KonsumsiBbm = $ukuran3KBHI + $ukuran3KBSDHI + $ukuran3KBSDBI;

    $ukuran3RBSDHI = 17;
    $ukuran3RBSDBI = 17;
    $ukuran3RB = $ukuran3RBSDHI + $ukuran3RBSDBI;

    $ukuran3Full = $ukuran3kosong + $ukuran3HMGHI + $ukuran3JHMG + $ukuran3KonsumsiBbm + $ukuran3RB;

    // ISIAN 3 (330)
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($ukuran3kosong, 10, ' ', 1, 0, 'C');
    $pdf->Cell($ukuran3HMGHI, 5, "HM GENSET  H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMG, 5, "JUMLAH HM GENSET", 1, 0, 'C');
    $pdf->Cell($ukuran3KonsumsiBbm, 5, "KONSUMSI BBM (LITER)", 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($ukuran3RB, 5, "RASIO BBM (LITER/HM)", 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->Cell($ukuran3kosong, 5, "", 0, 0, 'C');

    $pdf->Cell($ukuran3Awal, 5, "AWAL", 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, "AKHIR", 1, 0, 'C');

    $pdf->Cell($ukuran3JHMGHI, 5, "H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, "S.D.  H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, "S.D.  B. I", 1, 0, 'C');

    $pdf->Cell($ukuran3KBHI, 5, "H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, "S.D.  H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, "S.D.  B. I", 1, 0, 'C');

    $pdf->Cell($ukuran3RBSDHI, 5, "S.D.  H. I", 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, "S.D.  B. I", 1, 0, 'C');

    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->Cell($ukuran3Full, 5, 'PEMAKAIAN GENSET', 1, 1, 'L');
    $pdf->SetFont('Arial', '', 8);

    
    
    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        1.  "."GENSET NO.1", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        2.  "."GENSET NO.2", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        3.  "."GENSET NO.3", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        4.  "."Loader 1", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        5.  "."Loader 2", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========

    $pdf->Cell($ukuran3kosong, 5, "        6.  "."Loader 3", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalLR.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarTahunan = fetchData($queryGetSolarTahunan);
    $jumlahBBMTahunan = 0;
    $jumlahHIHMTahunan = 0;
    foreach ($dataSolarTahunan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMTahunan += $value2['jumlah'];
        }
        $jumlahBBMTahunan += $value['jlhbbm'];
    }
    
    // bulanan
    $queryGetSolarBulanan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalLR.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,8).'01" - INTERVAL 1 DAY
                AND
                tanggal <= "'.$tanggal.'" - INTERVAL 1 DAY
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulanan += $value2['jumlah'];
        }
        $jumlahBBMBulanan += $value['jlhbbm'];
    }

    // harian
    $queryGetSolar = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalLR.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
                tanggal = "'.$tanggal.'" - INTERVAL 1 DAY 
            AND
                posting = 1';
    $dataSolar = fetchData($queryGetSolar);
    $jumlahBBM = 0;
    $arrayAwal = null;
    $arrayAkhir = null;
    $hmAwalGensetHI = 0;
    $hmAkhirGensetHI = 0;
    $runNumber = 0;
    $jumlahHIHM = 0;
    foreach ($dataSolar as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $arrayAwal[$runNumber] = $value2['kmhmawal'];
            $arrayAkhir[$runNumber] = $value2['kmhmakhir'];
            $jumlahHIHM += $value2['jumlah'];
            $runNumber++;
        }
        $jumlahBBM += $value['jlhbbm'];
        
    }
    $hmAwalGensetHI = min($arrayAwal);
    $hmAkhirGensetHI = max($arrayAkhir);

    // ======== END ===========
    
    $pdf->Cell($ukuran3kosong, 5, "        7.  "."Loader Rental", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, $hmAwalGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, $hmAkhirGensetHI, 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, round($jumlahHIHM,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, round($jumlahHIHMBulanan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, round($jumlahHIHMTahunan,2), 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, $jumlahBBM, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, $jumlahBBMBulanan, 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, $jumlahBBMTahunan, 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2), 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2), 1, 1, 'C');

    $pdf->Cell($ukuran3kosong, 5, "Jumlah Pemakaian", 1, 0, 'L');
    $pdf->Cell($ukuran3Awal, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3Akhir, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3JHMGSDBI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3KBHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3KBSDBI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDHI, 5, "", 1, 0, 'C');
    $pdf->Cell($ukuran3RBSDBI, 5, "", 1, 1, 'C');



    $pdf->Ln();


    $ukuran4Kosong = 60;
    $ukuran4StockAwal = 28;
    $ukuran4Kirim = 0;
    $ukuran4Produksi = 0;
    $ukuran4StockAkhir = 28;

    $ukuran4Space = $ukuran4Kosong + $ukuran4StockAwal + $ukuran4Kirim + $ukuran4Produksi + $ukuran4StockAkhir ;

    $ukuran4Alb = 20;
    $ukuran4KadarAir = 30;
    $ukuran4KadarKotoran = 30;

    $ukuran4KwalitasStockHI = $ukuran4Alb + $ukuran4KadarAir + $ukuran4KadarKotoran;

    $ukuran4Full = $ukuran4Kosong + $ukuran4StockAwal + $ukuran4Kirim + $ukuran4Produksi + $ukuran4StockAkhir + $ukuran4KwalitasStockHI;

    // ISIAN 4 (330) sounding
    
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell($ukuran4Kosong, 10, ' ', 1, 0, 'C');
    $pdf->Cell($ukuran4StockAwal, 10, 'STOCK AWAL (KG)', 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 10, 'STOCK AKHIR (KG)', 1, 0, 'C');
    $pdf->Cell($ukuran4KwalitasStockHI, 5, 'KWALITAS STOCK HARI INI', 1, 0, 'C');
    $pdf->Cell(0, 5, "", 0, 1, 'C');

    $pdf->Cell($ukuran4Space, 5, "", 0, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "ALB (%)", 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 5);
    $pdf->Cell($ukuran4KadarAir, 5, "KADAR AIR (%)", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "KADAR KOTORAN (%)", 1, 1, 'C');
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ukuran4Full, 5, 'STOCK PRODUKSI', 1, 1, 'L');
    $pdf->Cell($ukuran4Full, 5, 'CPO', 1, 1, 'L');

    $pdf->SetFont('Arial', '', 8);
    $queryGetSoundingCPO = "   SELECT kuantitas, kodetangki, cpoffa, cpokdair, cpokdkot FROM pabrik_masukkeluartangki 
                            WHERE 
                            kuantitas > 0
                            AND
                            kodeorg = '".$kodeorg."'
                            AND
                            posting = 1
                            AND
                            (
                            tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                            AND
                            tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                            )";
    $dataSoundingCPO = fetchData($queryGetSoundingCPO);
    $jumlahStockSoundingCPO = 0;
    $jumlahStockAwalSoundingCPO = 0;
    foreach ($dataSoundingCPO as $key => $value) {
        $queryGetStockAwalSoundingCPO = "  SELECT kuantitas, kodetangki, cpoffa, cpokdair, cpokdkot FROM pabrik_masukkeluartangki 
                                WHERE 
                                kodetangki = '".$value['kodetangki']."'
                                AND
                                kodeorg = '".$kodeorg."'
                                AND
                                posting = 1
                                AND 
                                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 2 DAY
                                ORDER BY
                                    tanggal DESC
                                limit 1
                                ";
        $dataStockAwalSoundingCPO = fetchData($queryGetStockAwalSoundingCPO);

        $pdf->Cell($ukuran4Kosong, 5, "     ".$value['kodetangki']."   "."", 1, 0, 'L');
        $pdf->Cell($ukuran4StockAwal, 5, $dataStockAwalSoundingCPO[0]['kuantitas'], 1, 0, 'C');
        $pdf->Cell($ukuran4StockAkhir, 5, $value['kuantitas'], 1, 0, 'C');
        $pdf->Cell($ukuran4Alb, 5, $value['cpoffa'], 1, 0, 'C');
        $pdf->Cell($ukuran4KadarAir, 5, $value['cpokdair'], 1, 0, 'C');
        $pdf->Cell($ukuran4KadarKotoran, 5, $value['cpokdkot'], 1, 1, 'C');
        $jumlahStockSoundingCPO += $value['kuantitas'];
        $jumlahStockAwalSoundingCPO += $dataStockAwalSoundingCPO[0]['kuantitas'];
    }
    
    $pdf->Cell($ukuran4Kosong, 5, "JUMLAH STOCK CPO", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $jumlahStockAwalSoundingCPO, 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $jumlahStockSoundingCPO, 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "-", 1, 1, 'C');

    $pdf->SetFont('Arial', '', 8);
    $queryGetSoundingPK = "   SELECT kernelquantity, kodetangki, cpoffa, cpokdair, cpokdkot FROM pabrik_masukkeluartangki 
                            WHERE 
                            kernelquantity > 0
                            AND
                            kodeorg = '".$kodeorg."'
                            AND
                            posting = 1
                            AND
                            (
                            tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                            AND
                            tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                            )";
    $dataSoundingPK = fetchData($queryGetSoundingPK);
    $jumlahStockSoundingPK = 0;
    $jumlahStockAwalSoundingPK = 0;
    foreach ($dataSoundingPK as $key => $value) {
        $queryGetStockAwalSoundingPK = "  SELECT kernelquantity, kodetangki, cpoffa, cpokdair, cpokdkot FROM pabrik_masukkeluartangki 
                                WHERE 
                                kodetangki = '".$value['kodetangki']."'
                                AND
                                kodeorg = '".$kodeorg."'
                                AND
                                posting = 1
                                AND 
                                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 2 DAY
                                ORDER BY
                                    tanggal DESC
                                limit 1
                                ";
        $dataStockAwalSoundingPK = fetchData($queryGetStockAwalSoundingPK);
        $pdf->Cell($ukuran4Kosong, 5, "     ".$value['kodetangki']."   "."", 1, 0, 'L');
        $pdf->Cell($ukuran4StockAwal, 5, $dataStockAwalSoundingPK[0]['kernelquantity'], 1, 0, 'C');
        $pdf->Cell($ukuran4StockAkhir, 5, $value['kernelquantity'], 1, 0, 'C');
        $pdf->Cell($ukuran4Alb, 5, $value['cpoffa'], 1, 0, 'C');
        $pdf->Cell($ukuran4KadarAir, 5, $value['cpokdair'], 1, 0, 'C');
        $pdf->Cell($ukuran4KadarKotoran, 5, $value['cpokdkot'], 1, 1, 'C');
        $jumlahStockSoundingPK += $value['kernelquantity'];
        $jumlahStockAwalSoundingPK += $dataStockAwalSoundingPK[0]['kernelquantity'];
    }
    
    $pdf->Cell($ukuran4Kosong, 5, "JUMLAH STOCK KERNEL", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $jumlahStockAwalSoundingPK, 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $jumlahStockSoundingPK, 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "-", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "-", 1, 1, 'C');

    
    $pdf->Cell($ukuran4Kosong, 5, "JANJANG KOSONG (EMPTY FRUIT BUNCH)", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $dataPabrikProduksiSebelum[0]['stock_product_janjang_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $dataPabrikProduksi[0]['stock_product_janjang_kosong'], 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "0", 1, 1, 'C');

    $pdf->Cell($ukuran4Kosong, 5, "LIMBAH CAIR (POME)", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $dataPabrikProduksiSebelum[0]['stock_product_limbar_cair'], 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $dataPabrikProduksi[0]['stock_product_limbar_cair'], 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "0", 1, 1, 'C');

    $pdf->Cell($ukuran4Kosong, 5, "CANGKANG (SHELL)", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $dataPabrikProduksiSebelum[0]['stock_product_cangkang'], 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $dataPabrikProduksi[0]['stock_product_cangkang'], 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "0", 1, 1, 'C');

    $pdf->Cell($ukuran4Kosong, 5, "FIBRE", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $dataPabrikProduksiSebelum[0]['stock_product_fibre'], 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $dataPabrikProduksi[0]['stock_product_fibre'], 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "0", 1, 1, 'C');

    
    $pdf->Cell($ukuran4Kosong, 5, "ABU INCENERATOR", 1, 0, 'L');
    $pdf->Cell($ukuran4StockAwal, 5, $dataPabrikProduksiSebelum[0]['stock_product_abu_incenerator'], 1, 0, 'C');
    $pdf->Cell($ukuran4StockAkhir, 5, $dataPabrikProduksi[0]['stock_product_abu_incenerator'], 1, 0, 'C');
    $pdf->Cell($ukuran4Alb, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarAir, 5, "0", 1, 0, 'C');
    $pdf->Cell($ukuran4KadarKotoran, 5, "0", 1, 1, 'C');


    

    $pdf->Output();

?>