<?php
    
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    date_default_timezone_set('Asia/Jakarta');

    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);
    
    $kodeorg = $_GET['kodeorg'];
    $tanggal = $_GET['periode'];
    $method = $_GET['method'];    

    $border = "border='1'";
    $border2 = "border='0'";
    $bgcolor = 'bgcolor=#FFFFFF';

    function getNamaSupplier($kodeSupplier){
        $query = "SELECT * FROM log_5supplier where supplierid = '".$kodeSupplier."'";
        $queryAct = fetchData($query);

        return $queryAct[0]['namasupplier'];
    }

    // pengaturan general
    $queryGetPengaturanGeneral = "SELECT * FROM pabrik_5general where kodeorg='".$kodeorg."'";
    $dataPengaturanAdmin = fetchData($queryGetPengaturanGeneral);

    $generalG1 = null;
    $generalG2 = null;
    $generalG3 = null;
    $generalL1 = null;
    $generalL2 = null;
    $generalL3 = null;
    $generalLR = null;
    $kapasitasTerpasang = 1;
    $OEE = null;
    $HK = 1;
    $JO = 1;
    $stream = NULL;

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
        if ($value['code'] == "KP") {
            $kapasitasTerpasang = $value['nilai'];
        }
        if ($value['code'] == "HK") {
            $HK = $value['nilai'];
        }
        if ($value['code'] == "JO") {
            $JO = $value['nilai'];
        }
    }

    // get produksi harian
    $queryGetDataPabrikProduksi = "SELECT * FROM pabrik_produksi where kodeorg='".$_GET['kodeorg']."' AND tanggal='".$tanggal."'";
    $dataPabrikProduksi = fetchData($queryGetDataPabrikProduksi);
    
    // get produksi harian sebelum
    $queryGetDataPabrikProduksiSebelum = "  SELECT * FROM pabrik_produksi 
                                            where 
                                                kodeorg = '".$_GET['kodeorg']."' 
                                            AND 
                                                tanggal < '".$tanggal."'
                                            
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

    // data
    // get produksi harian sebulan
    $queryGetDataPabrikProduksiBulanan = " SELECT 
                                    sum(tbs_sisa_kemarin) as tbs_sisa_kemarin,
                                    sum(tbs_masuk_bruto) as tbs_masuk_bruto,
                                    sum(tbs_masuk_netto) as tbs_masuk_netto,
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
                                    sum(rendemen_cpo_before) as rendemen_cpo_before,
                                    sum(rendemen_pk_before) as rendemen_pk_before,
                                    sum(rendemen_cpo_after) as rendemen_cpo_after,
                                    sum(rendemen_pk_after) as rendemen_pk_after,
                                    sum(total_jam_operasi) as total_jam_operasi
                                    FROM pabrik_produksi 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($tanggal,0,8)."01'
                                    AND
                                    tanggal <= '".$tanggal."'
                                    )
                                    ";
    $dataPabrikProduksiBulanan = fetchData($queryGetDataPabrikProduksiBulanan);

    // if ($dataPabrikProduksiBulanan[0]['jumlah_hari_olah'] <= $HK) {
    //     $HK = $dataPabrikProduksiBulanan[0]['jumlah_hari_olah'];
    // }

    // get produksi harian setahun
    $queryGetDataPabrikProduksiTahunan = " SELECT 
                                    sum(tbs_sisa_kemarin) as tbs_sisa_kemarin,
                                    sum(tbs_masuk_bruto) as tbs_masuk_bruto,
                                    sum(tbs_masuk_netto) as tbs_masuk_netto,
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
                                    sum(rendemen_cpo_before) as rendemen_cpo_before,
                                    sum(rendemen_pk_before) as rendemen_pk_before,
                                    sum(rendemen_cpo_after) as rendemen_cpo_after,
                                    sum(rendemen_pk_after) as rendemen_pk_after,
                                    sum(total_jam_operasi) as total_jam_operasi
                                    FROM pabrik_produksi 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($tanggal,0,5)."01-01'
                                    AND
                                    tanggal <= '".$tanggal."'
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
                                    tanggal >= '".substr($tanggal,0,8)."01'
                                    AND
                                    tanggal <= '".$tanggal."'
                                    )
                                    ";
    $dataPabrikProduksiBulananAll = fetchData($queryGetDataPabrikProduksiBulananAll);
    
    $queryGetDataPabrikProduksiTahunanAll = "   SELECT 
                                                *
                                                FROM pabrik_produksi 
                                                WHERE 
                                                kodeorg='".$_GET['kodeorg']."' 
                                                AND 
                                                (
                                                tanggal >= '".substr($tanggal,0,5)."01-01'
                                                AND
                                                tanggal <= '".$tanggal."'
                                                )
                                                ";
    $dataPabrikProduksiTahunanAll = fetchData($queryGetDataPabrikProduksiTahunanAll);

    // Bulanan
    $jumlahKadarAirCPO = 0;
    $jumlahFfaCPO = 0;
    $jumlahKadarKotoranCPO = 0;
    $jumlahMICPO = 0;
    $jumlahKadarAirPK = 0;
    $jumlahKadarKotoranPK = 0;
    $jumlahIntiPecahPK = 0;
    $i = 1;
    foreach ($dataPabrikProduksiBulananAll as $key => $value) {
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
    }
    
    // Tahunan
    $jumlahKadarAirCPOTahunan = 0;
    $jumlahFfaCPOTahunan = 0;
    $jumlahKadarKotoranCPOTahunan = 0;
    $jumlahMICPOTahunan = 0;
    $jumlahKadarAirPKTahunan = 0;
    $jumlahKadarKotoranPKTahunan = 0;
    $jumlahIntiPecahPKTahunan = 0;
    
    $i = 1;
    foreach ($dataPabrikProduksiTahunanAll as $key => $value) {
        $hasilProductKadarAirCPOTahunan = $value['cpo_kadar_air'] * $value['cpo_produksi'];
        $jumlahKadarAirCPOTahunan += $hasilProductKadarAirCPOTahunan;
        
        $hasilProductFfaCPOTahunan = $value['cpo_ffa'] * $value['cpo_produksi'];
        $jumlahFfaCPOTahunan += $hasilProductFfaCPOTahunan;
        
        $hasilProductKadarKotoranCPOTahunan = $value['cpo_kotoran'] * $value['cpo_produksi'];
        $jumlahKadarKotoranCPOTahunan += $hasilProductKadarKotoranCPOTahunan;
        
        $hasilProductMICPOTahunan = ($value['cpo_kadar_air'] + $value['cpo_kotoran']) * $value['cpo_produksi'];
        $jumlahMICPOTahunan += $hasilProductMICPOTahunan;
        
        $hasilProductKadarAirPKTahunan = $value['kernel_kadar_air'] * $value['kernel_produksi'];
        $jumlahKadarAirPKTahunan += $hasilProductKadarAirPKTahunan;
        
        $hasilProductKadarKotoranPKTahunan = $value['kernel_kotoran'] * $value['kernel_produksi'];
        $jumlahKadarKotoranPKTahunan += $hasilProductKadarKotoranPKTahunan;
        
        $hasilProductIntiPecahPKTahunan = $value['kernel_inti_pecah'] * $value['kernel_produksi'];
        $jumlahIntiPecahPKTahunan += $hasilProductIntiPecahPKTahunan;
    }

    // get pengolahan pabrik
    $queryDataPengolahanPabrik = '  SELECT * FROM pabrik_pengolahan
            WHERE 
            kodeorg="'.$_GET['kodeorg'].'" 
            AND
            tanggal = "'.$tanggal.'" 
            AND
            posting = 1';
    $dataPengolahanPabrik = fetchData($queryDataPengolahanPabrik);

    $queryGetDataPengolahanPabrikBulanan = " SELECT 
                                    sum(total_jam_shift_1) as total_jam_shift_1,
                                    sum(total_jam_shift_2) as total_jam_shift_2,
                                    sum(jam_idle_shift_1) as jam_idle_shift_1,
                                    sum(jam_idle_shift_2) as jam_idle_shift_2,
                                    sum(jam_stagnasi) as jam_stagnasi,
                                    sum(lori_olah_shift_1) as lori_olah_shift_1,
                                    sum(lori_olah_shift_2) as lori_olah_shift_2,
                                    sum(lori_olah_shift_3) as lori_olah_shift_3,
                                    sum(rata_rata_lori) as rata_rata_lori,
                                    sum(total_jam_shift) as total_jam_shift,
                                    sum(total_jam_press) as total_jam_press,
                                    sum(total_jam_operasi) as total_jam_operasi,
                                    sum(total_jam_idle) as total_jam_idle
                                    
                                    FROM pabrik_pengolahan 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($tanggal,0,8)."01'
                                    AND
                                    tanggal <= '".$tanggal."'
                                    )
                                    ";
    $dataPengolahanPabrikBulanan = fetchData($queryGetDataPengolahanPabrikBulanan);

    $queryGetDataPengolahanPabrikTahunan = " SELECT 
                                    sum(total_jam_shift_1) as total_jam_shift_1,
                                    sum(total_jam_shift_2) as total_jam_shift_2,
                                    sum(jam_idle_shift_1) as jam_idle_shift_1,
                                    sum(jam_idle_shift_2) as jam_idle_shift_2,
                                    sum(jam_stagnasi) as jam_stagnasi,
                                    sum(lori_olah_shift_1) as lori_olah_shift_1,
                                    sum(lori_olah_shift_2) as lori_olah_shift_2,
                                    sum(lori_olah_shift_3) as lori_olah_shift_3,
                                    sum(rata_rata_lori) as rata_rata_lori,
                                    sum(total_jam_shift) as total_jam_shift,
                                    sum(total_jam_press) as total_jam_press,
                                    sum(total_jam_operasi) as total_jam_operasi,
                                    sum(total_jam_idle) as total_jam_idle
                                    
                                    FROM pabrik_pengolahan 
                                    WHERE 
                                    kodeorg='".$_GET['kodeorg']."' 
                                    AND 
                                    (
                                    tanggal >= '".substr($tanggal,0,8)."01'
                                    AND
                                    tanggal <= '".$tanggal."'
                                    )
                                    ";
    $dataPengolahanPabrikTahunan = fetchData($queryGetDataPengolahanPabrikTahunan);


    $queryORG = " SELECT 
                                    namaorganisasi
                                    
                                    FROM organisasi
                                    WHERE 
                                    kodeorganisasi='".$_GET['kodeorg']."' 
                                    
                                    ";
    $dataORG = fetchData($queryORG);
    /*
        Tabel Main
    */
    $stream .= "    <table cellspacing='1' class='sortable'  ".$border2.'>';
    $stream .= "    <thead class=rowheader> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center colspan='8'>".$dataORG[0]['namaorganisasi']."</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr > ";
    $stream .= "            <td align=center colspan='8'>LAPORAN HARIAN PABRIK</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center colspan='8'>PER TANGGAL ".$tanggal."</td>";
    $stream .= "        </tr> ";
    $stream .= "     </thead></table> ";
    $stream .= "    <table cellspacing='1' class='sortable'  ".$border.'>';
    $stream .= "    <thead class=rowheader> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center rowspan='3' ".$bgcolor."></td>";
    $stream .= "            <td align=center rowspan='3' ".$bgcolor.">SATUAN</td>";
    $stream .= "            <td align=center colspan='3' ".$bgcolor.">BULAN INI</td>";
    $stream .= "            <td align=center colspan='2' ".$bgcolor.">S.D. BULAN INI</td>";
    $stream .= "            <td align=center rowspan='3' ".$bgcolor.">ANG. SETAHUN</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center colspan='2' ".$bgcolor.">REAL</td>";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor.">ANG.</td>";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor.">REAL.</td>";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor.">ANG.</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center ".$bgcolor.">H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "        </tr> ";
    $stream .= "    </thead> ";
    $stream .= "    <tbody> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">PENGOLAHAN TBS</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">STOCK AWAL</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa_kemarin'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa_kemarin'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa_kemarin'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">PENERIMAAN TBS</td>";
    $stream .= "        </tr> ";

    if(substr($_SESSION['empl']['lokasitugas'],0,3) == "SSP" || substr($_SESSION['empl']['lokasitugas'],0,3) == "LSP"){
        $queryGetKlSupplier = "SELECT * FROM log_5klsupplier WHERE isTBS = 1 AND kelompok like '%".substr($_SESSION['empl']['lokasitugas'],0,3)."' ";
    }else{
        $queryGetKlSupplier = "SELECT * FROM log_5klsupplier WHERE isTBS = 1";
    }
    
    $dataKlSupplier = fetchData($queryGetKlSupplier);

    $jumlahTBSMasukALLSupplier = 0;
    $jumlahTBSMasukALLSupplierBulanan = 0;
    $jumlahTBSMasukALLSupplierTahunan = 0;
    // print_r(substr($tanggal,0,8));
    // echo "<br>";
    // print_r(substr($tanggal,0,5));
    // die();
    foreach ($dataKlSupplier as $keyDKS => $valueDKS) {
        // get timbangan harian
        $queryGetDataPabrikTimbangan = "SELECT sum(beratmasuk - beratkeluar) as jumlah FROM pabrik_timbangan 
        WHERE 
        notransaksi LIKE 'M%'
        AND
        millcode='".$_GET['kodeorg']."' 
        AND
        kodecustomer LIKE '".$valueDKS['kode']."%'
        AND 
        (
        tanggal >= '".$tanggal." 00:00:00'
        AND
        tanggal <= '".$tanggal." 23:59:59'
        )
        ";
        $dataPabrikTimbangan = fetchData($queryGetDataPabrikTimbangan);
        
        // get timbangan bulanan
        $queryGetDataPabrikTimbanganBulanan = "SELECT sum(beratmasuk - beratkeluar) as jumlah FROM pabrik_timbangan 
        WHERE 
        notransaksi LIKE 'M%'
        AND
        millcode='".$_GET['kodeorg']."' 
        AND
        kodecustomer LIKE '".$valueDKS['kode']."%'
        AND 
        (
        tanggal >= '".substr($tanggal,0,8)."01 00:00:00'
        AND
        tanggal <= '".$tanggal." 23:59:59'
        )
        ";
        $dataPabrikTimbanganBulanan = fetchData($queryGetDataPabrikTimbanganBulanan);

        // get timbangan tahunan
        $queryGetDataPabrikTimbanganTahunan = "SELECT sum(beratmasuk - beratkeluar) as jumlah FROM pabrik_timbangan 
        WHERE 
        notransaksi LIKE 'M%'
        AND
        millcode='".$_GET['kodeorg']."' 
        AND
        kodecustomer LIKE '".$valueDKS['kode']."%'
        AND 
        (
        tanggal >= '".substr($tanggal,0,5)."01-01 00:00:00'
        AND
        tanggal <= '".$tanggal." 23:59:59'
        )
        ";
        $dataPabrikTimbanganTahunan = fetchData($queryGetDataPabrikTimbanganTahunan);



        $jumlahTBSMasukKlSupplier = 0;
        $jumlahTBSMasukKlSupplierBulanan = 0;
        $jumlahTBSMasukKlSupplierTahunan = 0;
        if (!empty($dataPabrikTimbangan[0]['jumlah'])) {
            $jumlahTBSMasukKlSupplier = $dataPabrikTimbangan[0]['jumlah'];
        }
        if (!empty($dataPabrikTimbanganBulanan[0]['jumlah'])) {
            $jumlahTBSMasukKlSupplierBulanan = $dataPabrikTimbanganBulanan[0]['jumlah'];
        }
        if (!empty($dataPabrikTimbanganTahunan[0]['jumlah'])) {
            $jumlahTBSMasukKlSupplierTahunan = $dataPabrikTimbanganTahunan[0]['jumlah'];
        }
        if ($jumlahTBSMasukKlSupplier > 0) {
            $stream .= "        <tr> ";
            $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'>".$valueDKS['kelompok']."</td>";
            $stream .= "            <td align=center ".$bgcolor.">KG</td>";
            $stream .= "            <td align=center ".$bgcolor.">".round($jumlahTBSMasukKlSupplier,0)."</td>";
            $stream .= "            <td align=center ".$bgcolor.">".round($jumlahTBSMasukKlSupplierBulanan,0)."</td>";
            $stream .= "            <td align=center ".$bgcolor.">0</td>";
            $stream .= "            <td align=center ".$bgcolor.">".round($jumlahTBSMasukKlSupplierTahunan,0)."</td>";
            $stream .= "            <td align=center ".$bgcolor.">0</td>";
            $stream .= "            <td align=center ".$bgcolor.">0</td>";
            $stream .= "        </tr> ";
        }
        $jumlahTBSMasukALLSupplier += $jumlahTBSMasukKlSupplier;
        $jumlahTBSMasukALLSupplierBulanan += $jumlahTBSMasukKlSupplierBulanan;
        $jumlahTBSMasukALLSupplierTahunan += $jumlahTBSMasukKlSupplierTahunan;
    }
    
    // if ($jumlahTBSMasukALLSupplier > 0) {
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor.">JUMLAH TBS MASUK</td>";
        $stream .= "            <td align=center ".$bgcolor.">KG</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_masuk_bruto'],0)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['tbs_masuk_bruto'],0)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">0</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['tbs_masuk_bruto'],0)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">0</td>";
        $stream .= "            <td align=center ".$bgcolor.">0</td>";
        $stream .= "        </tr> ";
    // }
    

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH PERSEDIAAN TBS</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa_kemarin'] + $dataPabrikProduksi[0]['tbs_masuk_bruto'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round(($dataPabrikProduksi[0]['tbs_sisa_kemarin'] ) + ($dataPabrikProduksiBulanan[0]['tbs_masuk_bruto']),0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['tbs_sisa_kemarin'] + $dataPabrikProduksiTahunan[0]['tbs_masuk_bruto'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">POTONGAN SORTASI</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_potongan'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['tbs_potongan'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['tbs_potongan'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">TBS DIOLAH</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - TBS OLAH BEFORE</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_diolah'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['tbs_diolah'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['tbs_diolah'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - TBS OLAH AFTER</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_after_grading'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['tbs_after_grading'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['tbs_after_grading'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">STOCK AKHIR</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['tbs_sisa'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">PRODUKSI</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">CRUDE PALM OIL (CPO) </td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['cpo_produksi'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['cpo_produksi'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['cpo_produksi'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">RENDEMEN CPO</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RENDEMEN BEFORE</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rendemen_cpo_before'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['cpo_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['cpo_produksi'] / $dataPabrikProduksiTahunan[0]['tbs_diolah'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RENDEMEN AFTER</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rendemen_cpo_after'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['cpo_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_after_grading'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['cpo_produksi'] / $dataPabrikProduksiTahunan[0]['tbs_after_grading'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">MUTU PRODUKSI CPO</td>";
    $stream .= "        </tr> ";

    $sumProductFfaCPO = round($jumlahFfaCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $sumProductFfaCPOTahunan = round($jumlahFfaCPOTahunan / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - ALB</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['cpo_ffa'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductFfaCPO."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductFfaCPOTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $sumProductKadarAirCPO = round($jumlahKadarAirCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $sumProductKadarAirCPOTahunan = round($jumlahKadarAirCPOTahunan / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - KADAR AIR</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['cpo_kadar_air'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarAirCPO."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarAirCPOTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $sumProductKadarKotoranCPO = round($jumlahKadarKotoranCPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],3);
    $sumProductKadarKotoranCPOTahunan = round($jumlahKadarKotoranCPOTahunan / $dataPabrikProduksiTahunan[0]['cpo_produksi'],3);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - KADAR KOTORAN</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['cpo_kotoran'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($sumProductKadarKotoranCPO,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($sumProductKadarKotoranCPOTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $mANDb = $dataPabrikProduksi[0]['cpo_kadar_air'] + $dataPabrikProduksi[0]['cpo_kotoran'];
    $sumProductMICPO = round($jumlahMICPO / $dataPabrikProduksiBulanan[0]['cpo_produksi'],2);
    $sumProductMICPOTahunan = round($jumlahMICPOTahunan / $dataPabrikProduksiTahunan[0]['cpo_produksi'],2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - M&I</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($mANDb,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductMICPO."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductMICPOTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">PALM KERNEL (PK)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kernel_produksi'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['kernel_produksi'],0) ."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['kernel_produksi'],0) ."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">RENDEMEN PK</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RENDEMEN BEFORE</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rendemen_pk_before'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['kernel_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['kernel_produksi'] / $dataPabrikProduksiTahunan[0]['tbs_diolah'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RENDEMEN AFTER</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rendemen_pk_after'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['kernel_produksi'] / $dataPabrikProduksiBulanan[0]['tbs_after_grading'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['kernel_produksi'] / $dataPabrikProduksiTahunan[0]['tbs_after_grading'] * 100,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">MUTU PRODUKSI PK</td>";
    $stream .= "        </tr> ";
    
    $sumProductKadarAirPK = round($jumlahKadarAirPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);
    $sumProductKadarAirPKTahunan = round($jumlahKadarAirPKTahunan / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - KADAR AIR</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kernel_kadar_air'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarAirPK."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarAirPKTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $sumProductKadarKotoranPK = round($jumlahKadarKotoranPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);    
    $sumProductKadarKotoranPKTahunan = round($jumlahKadarKotoranPKTahunan / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2);    
      
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - KADAR KOTORAN</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kernel_kotoran'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarKotoranPK."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductKadarKotoranPKTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
 
    $sumProductIntiPecahPK = round($jumlahIntiPecahPK / $dataPabrikProduksiBulanan[0]['kernel_produksi'],2);
    $sumProductIntiPecahPKTahunan = round($jumlahIntiPecahPKTahunan / $dataPabrikProduksiTahunan[0]['kernel_produksi'],2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - INTI PECAH</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kernel_inti_pecah'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductIntiPecahPK."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$sumProductIntiPecahPKTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">PENGIRIMAN</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">CRUDE PALM OIL (CPO)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_despatch_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_despatch_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_despatch_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">RETURN CPO</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_return_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_return_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_return_cpo'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">PALM KERNEL (PK)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_despatch_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_despatch_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_despatch_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">RETURN PK</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_return_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_return_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_return_pk'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JANJANG KOSONG (EFB)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_janjang_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_janjang_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_janjang_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">LIMBAH CAIR (POME)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_limbah_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_limbah_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_limbah_kosong'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">SOLID DECANTER</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_solid_decnter'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_solid_decnter'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_solid_decnter'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">ABU JANJANG (BUNCH ASH)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_abu_janjang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_abu_janjang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_abu_janjang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">CANGKANG (SHELL)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_cangkang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_cangkang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_cangkang'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">FIBRE</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['pengiriman_fibre'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['pengiriman_fibre'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['pengiriman_fibre'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">UTILISASI PABRIK</td>";
    $stream .= "        </tr> ";

    $totalJamShift = $dataPengolahanPabrik[0]['total_jam_shift_1'] + $dataPengolahanPabrik[0]['total_jam_shift_2'];
    $totalJamShiftBulanan = $dataPengolahanPabrikBulanan[0]['total_jam_shift_1'] + $dataPengolahanPabrikBulanan[0]['total_jam_shift_2'];

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JAM SHIFT</td>";
    $stream .= "            <td align=center ".$bgcolor.">JAM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrik[0]['total_jam_shift']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrikBulanan[0]['total_jam_shift']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrikTahunan[0]['total_jam_shift']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JAM OLAH</td>";
    $stream .= "            <td align=center ".$bgcolor.">JAM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['total_jam_operasi']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['total_jam_operasi'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrikTahunan[0]['total_jam_operasi']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - JAM TIDAK PRODUKTIF</td>";
    $stream .= "            <td align=center ".$bgcolor.">JAM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPengolahanPabrik[0]['total_jam_idle'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPengolahanPabrikBulanan[0]['total_jam_idle'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrikTahunan[0]['total_jam_idle']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - JAM DOWNTIME</td>";
    $stream .= "            <td align=center ".$bgcolor.">JAM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPengolahanPabrik[0]['jam_stagnasi'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPengolahanPabrikBulanan[0]['jam_stagnasi'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPengolahanPabrikTahunan[0]['jam_stagnasi']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH HARI OLAH</td>";
    $stream .= "            <td align=center ".$bgcolor.">HARI</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['jumlah_hari_olah']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiBulanan[0]['jumlah_hari_olah']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiTahunan[0]['jumlah_hari_olah']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
 
    $kapasitasOlahBulanan = $dataPabrikProduksiBulanan[0]['tbs_diolah'] / $dataPabrikProduksiBulanan[0]['total_jam_operasi'];
    $kapasitasOlahTahunan = $dataPabrikProduksiTahunan[0]['tbs_diolah'] / $dataPabrikProduksiTahunan[0]['total_jam_operasi'];
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">KAPASITAS OLAH</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kapasitas_olah'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($kapasitasOlahBulanan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($kapasitasOlahTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> "; 

    $utilitasKapasitasSDHI = ($kapasitasOlahBulanan / $kapasitasTerpasang) * 100;
    $utilitasKapasitasSDBI = ($kapasitasOlahTahunan / $kapasitasTerpasang) * 100;
    // print_r($dataPabrikProduksi[0]['kapasitas_olah']);
    // die();
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - UTILISASI KAPASITAS</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['utilitas_kapasitas'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($utilitasKapasitasSDHI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($utilitasKapasitasSDBI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    // print_r($dataPabrikProduksiBulanan[0]['tbs_diolah']);
    // die();
    // print_r(($dataPabrikProduksiTahunan[0]['tbs_diolah'] / ($kapasitasTerpasang * $HK * $JO))*100);
    // die();
    $utilityFactorCommercialSDHI = round((($dataPabrikProduksiBulanan[0]['tbs_diolah']) / ($kapasitasTerpasang * $HK * $JO))*100,2);
    $utilityFactorCommercialSDBI = round((($dataPabrikProduksiTahunan[0]['tbs_diolah']) / ($kapasitasTerpasang * $HK * $JO))*100,2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">UTILITY FACTOR COMMERCIAL</td>";
    $stream .= "            <td align=center ".$bgcolor.">%</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['utility_factor_commercial'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$utilityFactorCommercialSDHI ."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$utilityFactorCommercialSDBI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $jumlahLori = $dataPengolahanPabrik[0]['lori_olah_shift_1'] + $dataPengolahanPabrik[0]['lori_olah_shift_2'] + $dataPengolahanPabrik[0]['lori_olah_shift_3'];
    $jumlahLoriBulanan = $dataPengolahanPabrikBulanan[0]['lori_olah_shift_1'] + $dataPengolahanPabrikBulanan[0]['lori_olah_shift_2'] + $dataPengolahanPabrikBulanan[0]['lori_olah_shift_3'];
    $jumlahLoriTahunan = $dataPengolahanPabrikTahunan[0]['lori_olah_shift_1'] + $dataPengolahanPabrikTahunan[0]['lori_olah_shift_2'] + $dataPengolahanPabrikTahunan[0]['lori_olah_shift_3'];
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">LORI / STERILISER OLAH</td>";
    $stream .= "            <td align=center ".$bgcolor.">UNIT</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahLori."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahLoriBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahLoriTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $muatanLoriSteriserSDHI = round($dataPabrikProduksiBulanan[0]['tbs_diolah'] / $jumlahLoriBulanan,0);
    $muatanLoriSteriserSDBI = round($dataPabrikProduksiTahunan[0]['tbs_diolah'] / $jumlahLoriTahunan,0);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - MUATAN LORI / STERILISER</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG/UNIT</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPengolahanPabrik[0]['rata_rata_lori'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$muatanLoriSteriserSDHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$muatanLoriSteriserSDBI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JAM PRESS</td>";
    $stream .= "            <td align=center ".$bgcolor.">HM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['total_jam_press'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['total_jam_press'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['total_jam_press'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
 
    $kapasitasPressSDHI = round($dataPabrikProduksiBulanan[0]['tbs_diolah'] / $dataPabrikProduksiBulanan[0]['total_jam_press'],0);
    $kapasitasPressSDBI = round($dataPabrikProduksiTahunan[0]['tbs_diolah'] / $dataPabrikProduksiTahunan[0]['total_jam_press'],0);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - KAPASITAS PRESS</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG/HM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['kapasitas_press'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kapasitasPressSDHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kapasitasPressSDBI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=8 ".$bgcolor.">PEMAKAIAN KALSIUM</td>";
    $stream .= "        </tr> ";
   
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">CALSIUM</td>";
    $stream .= "            <td align=center ".$bgcolor.">JAM</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['caco3'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiBulanan[0]['caco3'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksiTahunan[0]['caco3'],0)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";
    
    $rasioCalsiumTBSBulanan = round($dataPabrikProduksiBulanan[0]['caco3'] / $dataPabrikProduksiBulanan[0]['tbs_diolah'] * 1000,2);
    $rasioCalsiumTBSTahunan = round($dataPabrikProduksiTahunan[0]['caco3'] / $dataPabrikProduksiTahunan[0]['tbs_diolah'] * 1000,2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RASIO CALSIUM TERHADAP TBS</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG/TON TBS</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rasio_kalsium_tbs'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$rasioCalsiumTBSBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$rasioCalsiumTBSTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

    $rasioCalsiumPKBulanan = round($dataPabrikProduksiBulanan[0]['caco3'] / $dataPabrikProduksiBulanan[0]['kernel_produksi'] * 1000,2);
    $rasioCalsiumPKTahunan = round($dataPabrikProduksiTahunan[0]['caco3'] / $dataPabrikProduksiTahunan[0]['kernel_produksi'] * 1000,2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> - RASIO CALSIUM TERHADAP PK</td>";
    $stream .= "            <td align=center ".$bgcolor.">KG/TON PK</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($dataPabrikProduksi[0]['rasio_kalsium_pk'],2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$rasioCalsiumPKBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$rasioCalsiumPKTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";

      $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor." style='padding-left: 15px;'> INFORMASI CUACA</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['informasi_cuaca']."</td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "            <td align=center ".$bgcolor."></td>";
    $stream .= "        </tr> ";
    $stream .= "    </tbody> ";
    $stream .= "</table> ";
    
    $stream .= "        <br><br>";

    /*
        TABLE 2 - KELENGKAPAN LOSSES
    */

    $stream .= "    <table cellspacing='1' class='sortable'  ".$border.'>';
    $stream .= "    <thead class=rowheader> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor."></td>";
    $stream .= "            <td align=center colspan='4' ".$bgcolor.">LOSSES TERHADAP SAMPLE (%)</td>";
    $stream .= "            <td align=center colspan='4' ".$bgcolor.">LOSSES TERHADAP TBS (%)</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center ".$bgcolor.">STD</td>";
    $stream .= "            <td align=center ".$bgcolor.">H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D B.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">STD</td>";
    $stream .= "            <td align=center ".$bgcolor.">H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D B.I</td>";
    $stream .= "        </tr> ";
    $stream .= "    </thead> ";
    $stream .= "    <tbody> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=9 ".$bgcolor.">LOSSES</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=9 ".$bgcolor.">OIL LOSSES</td>";
    $stream .= "        </tr> ";

    // LOSES HARIAN
    $getTransaksiLosesCPO = "   SELECT 
                                    b.id, b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai 
                                FROM 
                                    pabrik_kelengkapanloses a
                                JOIN 
                                    pabrik_5kelengkapanloses b
                                ON 
                                    a.id = b.id
                                WHERE 
                                    b.losses_to_tbs > 0
                                AND
                                    b.produk = 'CPO'
                                AND
                                    a.kodeorg = '".$kodeorg."'
                                AND
                                    a.tanggal = '".$tanggal."' 
                                ";
                            
    $dataTransaksiLosesCPO = fetchData($getTransaksiLosesCPO);
    // END LOSES HARIAN

    $jumlahLosesToTBS = 0;
    $jumlahLosesToTBSHI = 0;
    $jumlahLosesToTBSSDHI = 0;
    $jumlahLosesToTBSSDBI = 0;
    foreach ($dataTransaksiLosesCPO as $key => $value) {
        // TBS HI
        $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
        $dataMasterLoses = fetchData($getMasterLoses);
        $losesTBSHI = $value['nilai'];
        if (!empty($dataMasterLoses[0])) {
            if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
            }
            if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
            }
            if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
            }
        }
        // END TBS HI
              
        $queryGetSumSDHI = "SELECT 
                                sum(nilai)/COUNT(*) AS nilai
                            FROM
                                pabrik_kelengkapanloses
                            WHERE
                            (
                                tanggal >= '".substr($tanggal,0,5)."01-01' 
                                AND     
                                tanggal <= '".$tanggal."' 
                            )
                            AND
                            id = '".$value['id']."'
                            ";
        $dataSumSDHI = fetchData($queryGetSumSDHI);
        $losesSampleSDHI = 0;
        if (!empty($dataSumSDHI[0])) {
            $losesSampleSDHI = $dataSumSDHI[0]['nilai'];
        }

        $queryGetSumSDBI = "SELECT 
                                sum(nilai)/COUNT(*) AS nilai
                            FROM
                                pabrik_kelengkapanloses
                            WHERE
                            (
                                tanggal >= '".substr($tanggal,0,8)."01' 
                                AND     
                                tanggal <= '".$tanggal."' 
                            )
                            AND
                            id = '".$value['id']."'
                            ";
        $dataSumSDBI = fetchData($queryGetSumSDBI);
        $losesSampleSDBI = 0;
        if (!empty($dataSumSDBI[0])) {
            $losesSampleSDBI = $dataSumSDBI[0]['nilai'];
        }
        
        // TBS SD HI
        $dataTemp2=0;
        foreach ($dataPabrikProduksiBulananAll as $keyPBAll => $valuePBAll) {     
            $queryGetLoses = "SELECT * FROM pabrik_kelengkapanloses WHERE id='".$value['id']."' and tanggal='".$valuePBAll['tanggal']."' ";
            $dataGetLoses = fetchData($queryGetLoses);
            $dataTemp = 0;
            if ($dataGetLoses[0]['nilai'] > 0) {
                $dataTemp = $dataGetLoses[0]['nilai'];
                $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
                $dataMasterLoses = fetchData($getMasterLoses);
                if (!empty($dataMasterLoses[0])) {
                    if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
                    }
                }
            }
            $dataTemp = $dataTemp * $valuePBAll['tbs_diolah'];
            $dataTemp2 = $dataTemp2 + $dataTemp;
            
        }
        $losesTBSSDHI = $dataTemp2 / $dataPabrikProduksiBulanan[0]['tbs_diolah'];

        // TBS SD BI
        $dataTemp2PPLT=0;
        foreach ($dataPabrikProduksiTahunanAll as $keyPPLT => $valuePPLT) {     
            $queryGetLoses = "SELECT * FROM pabrik_kelengkapanloses WHERE id='".$value['id']."' and tanggal='".$valuePPLT['tanggal']."' ";
            $dataGetLoses = fetchData($queryGetLoses);
            $dataTempPPLT = 0;
            if ($dataGetLoses[0]['nilai'] > 0) {
                $dataTempPPLT = $dataGetLoses[0]['nilai'];
                $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
                $dataMasterLoses = fetchData($getMasterLoses);
                if (!empty($dataMasterLoses[0])) {
                    if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
                    }
                }
            }
            $dataTempPPLT = $dataTempPPLT * $valuePPLT['tbs_diolah'];
            $dataTemp2PPLT = $dataTemp2PPLT + $dataTempPPLT;
            
        }
        $losesTBSSDBI = $dataTemp2PPLT / $dataPabrikProduksiTahunan[0]['tbs_diolah'];

        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - ".$value['namaitem']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['standard'].$value['standardtoffb']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['nilai']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesSampleSDHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesSampleSDBI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['losses_to_tbs'].$value['satuan']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSSDHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSSDBI,2)."</td>";
        $stream .= "        </tr> ";
         
        $jumlahLosesToTBS += $value['losses_to_tbs'];
        $jumlahLosesToTBSHI += round($losesTBSHI,2);
        $jumlahLosesToTBSSDHI += round($losesTBSSDHI,2);
        $jumlahLosesToTBSSDBI += round($losesTBSSDBI,2);
    }
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH OIL LOSSES</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahLosesToTBS."%"."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahLosesToTBSHI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahLosesToTBSSDHI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahLosesToTBSSDBI,2)."</td>";
    $stream .= "        </tr> ";

    $oilExtractionSTDToTBS = round(($OEE / (($OEE + $jumlahLosesToTBS) / 100)),2);
    $oilExtractionHiToTBS = round($dataPabrikProduksi[0]['rendemen_cpo_after'] / (($dataPabrikProduksi[0]['rendemen_cpo_after'] + $jumlahLosesToTBS) / 100),2);
    $oilExtractionSDHIToTBS = round($dataPabrikProduksiBulanan[0]['rendemen_cpo_after'] / (($dataPabrikProduksiBulanan[0]['rendemen_cpo_after'] + $jumlahLosesToTBSSDHI) / 100),2);
    $oilExtractionSDBIToTBS = round($dataPabrikProduksiTahunan[0]['rendemen_cpo_after'] / (($dataPabrikProduksiTahunan[0]['rendemen_cpo_after'] + $jumlahLosesToTBSSDBI) / 100),2);
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">OIL EXTRACTION EFFICIENCY</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$oilExtractionSTDToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$oilExtractionHiToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$oilExtractionSDHIToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$oilExtractionSDBIToTBS."</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=9 ".$bgcolor.">KERNEL LOSSES</td>";
    $stream .= "        </tr> ";

    // LOSES HARIAN
    $getTransaksiLosesPK = "    SELECT 
                                    b.id, b.produk, b.namaitem, b.standard, b.standardtoffb, b.losses_to_tbs, b.satuan, b.linked_to, a.nilai 
                                FROM 
                                    pabrik_kelengkapanloses a
                                JOIN 
                                    pabrik_5kelengkapanloses b ON a.id = b.id
                                WHERE 
                                    b.losses_to_tbs > 0
                                AND
                                    b.produk = 'KERNEL'
                                AND
                                    a.kodeorg = '".$kodeorg."'
                                AND
                                    a.tanggal = '".$tanggal."' 
                                ";
    $getTransaksiLosesPK = fetchData($getTransaksiLosesPK);
    // END LOSES HARIAN
    $jumlahPKLosesToTBS = 0;
    $jumlahPKLosesToTBSHI = 0;
    $jumlahPKLosesToTBSSDHI = 0;
    $jumlahPKLosesToTBSSDBI = 0;
    foreach ($getTransaksiLosesPK as $key => $value) {
        // TBS HI
        $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
        $dataMasterLoses = fetchData($getMasterLoses);
        $losesTBSHI = $value['nilai'];
        if (!empty($dataMasterLoses[0])) {
            if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
            }
            if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
            }
            if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                $losesTBSHI = $losesTBSHI * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
            }
        }
        // END TBS HI


        $queryGetSumSDHI = "SELECT 
                                sum(nilai)/COUNT(*) AS nilai
                            FROM
                                pabrik_kelengkapanloses
                            WHERE
                            (
                                tanggal >= '".substr($tanggal,0,5)."01-01' 
                                AND     
                                tanggal <= '".$tanggal."' 
                            )
                            AND
                            id = '".$value['id']."'
                            ";
                            
        $dataSumSDHI = fetchData($queryGetSumSDHI);
        $losesSampleSDHI = 0;
        if (!empty($dataSumSDHI[0])) {
            $losesSampleSDHI = $dataSumSDHI[0]['nilai'];
        }

        $queryGetSumSDBI = "SELECT 
                                sum(nilai)/COUNT(*) AS nilai
                            FROM
                                pabrik_kelengkapanloses
                            WHERE
                            (
                                tanggal >= '".substr($tanggal,0,8)."01' 
                                AND     
                                tanggal <= '".$tanggal."' 
                            )
                            AND
                            id = '".$value['id']."'
                            ";
        $dataSumSDBI = fetchData($queryGetSumSDBI);
        $losesSampleSDBI = 0;
        if (!empty($dataSumSDBI[0])) {
            $losesSampleSDBI = $dataSumSDBI[0]['nilai'];
        }
        
        // TBS SD HI
        $dataTemp2=0;
        foreach ($dataPabrikProduksiBulananAll as $keyPBAll => $valuePBAll) {     
            $queryGetLoses = "SELECT * FROM pabrik_kelengkapanloses WHERE id='".$value['id']."' and tanggal='".$valuePBAll['tanggal']."' ";
            $dataGetLoses = fetchData($queryGetLoses);
            $dataTemp = 0;
            
            if (!empty($dataGetLoses[0]['nilai'])) {
                $dataTemp = $dataGetLoses[0]['nilai'];
                $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
                $dataMasterLoses = fetchData($getMasterLoses);
                if (!empty($dataMasterLoses[0])) {
                    if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                        $dataTemp = $dataTemp * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
                    }
                }
            }
            $dataTemp = $dataTemp * $valuePBAll['tbs_diolah'];
            $dataTemp2 = $dataTemp2 + $dataTemp;
            
        }
        $losesTBSSDHI = $dataTemp2 / $dataPabrikProduksiBulanan[0]['tbs_diolah'];

        // TBS SD BI
        $dataTemp2PPLT=0;
        foreach ($dataPabrikProduksiTahunanAll as $keyPPLT => $valuePPLT) {     
            $queryGetLoses = "SELECT * FROM pabrik_kelengkapanloses WHERE id='".$value['id']."' and tanggal='".$valuePPLT['tanggal']."' ";
            $dataGetLoses = fetchData($queryGetLoses);
            $dataTempPPLT = 0;
            if (!empty($dataGetLoses[0]['nilai'])) {
                $dataTempPPLT = $dataGetLoses[0]['nilai'];
                $getMasterLoses = " SELECT namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                            FROM pabrik_5kelengkapanloses
                            WHERE 
                            kodeorg = '".$kodeorg."'
                            AND 
                            linked_to = '".$value['linked_to']."'
                            ";
                $dataMasterLoses = fetchData($getMasterLoses);
                if (!empty($dataMasterLoses[0])) {
                    if ($dataMasterLoses[0]['faktor_konversi_1'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_1'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_2'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_2'] / 100;
                    }
                    if ($dataMasterLoses[0]['faktor_konversi_3'] > 0) {
                        $dataTempPPLT = $dataTempPPLT * $dataMasterLoses[0]['faktor_konversi_3'] / 100;
                    }
                }
            }
            $dataTempPPLT = $dataTempPPLT * $valuePPLT['tbs_diolah'];
            $dataTemp2PPLT = $dataTemp2PPLT + $dataTempPPLT;
            
        }
        $losesTBSSDBI = $dataTemp2PPLT / $dataPabrikProduksiTahunan[0]['tbs_diolah'];
        
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - ".$value['namaitem']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['standard'].$value['standardtoffb']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($value['nilai'],2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesSampleSDHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesSampleSDBI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($value['losses_to_tbs'],3) .$value['satuan'] ."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSSDHI,2)."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".round($losesTBSSDBI,2)."</td>";
        $stream .= "        </tr> ";
         
        $jumlahPKLosesToTBS += $value['losses_to_tbs'];
        $jumlahPKLosesToTBSHI += round($losesTBSHI,2);
        $jumlahPKLosesToTBSSDHI += round($losesTBSSDHI,2);
        $jumlahPKLosesToTBSSDBI += round($losesTBSSDBI,2);
    }
 
    

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH KERNEL LOSSES</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahPKLosesToTBS,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahPKLosesToTBSHI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahPKLosesToTBSSDHI,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahPKLosesToTBSSDBI,2)."</td>";
    $stream .= "        </tr> ";
    
    $kernelExtractionSTDToTBS = round(($dataPabrikProduksi[0]['rendemen_pk_after'] / (($dataPabrikProduksi[0]['rendemen_pk_after'] + $jumlahPKLosesToTBS) / 100)),2);
    $kernelExtractionHiToTBS = round($dataPabrikProduksi[0]['rendemen_pk_after'] / (($dataPabrikProduksi[0]['rendemen_pk_after'] + $jumlahPKLosesToTBSHI) / 100),2);
    $kernelExtractionSDHIToTBS = round($dataPabrikProduksiBulanan[0]['rendemen_pk_after'] / (($dataPabrikProduksiBulanan[0]['rendemen_pk_after'] + $jumlahPKLosesToTBSSDHI) / 100),2);
    $kernelExtractionSDBIToTBS = round($dataPabrikProduksiTahunan[0]['rendemen_pk_after'] / (($dataPabrikProduksiTahunan[0]['rendemen_pk_after'] + $jumlahPKLosesToTBSSDBI) / 100),2);
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">KERNEL EXTRACTION EFFICIENCY</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kernelExtractionSTDToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kernelExtractionHiToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kernelExtractionSDHIToTBS."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$kernelExtractionSDBIToTBS."</td>";
    $stream .= "        </tr> ";

    $stream .= "    </tbody> ";
    $stream .= "</table> ";
    $stream .= "        <br><br>";

    /*
        TABLE 3 - SOLAR
    */

    $stream .= "    <table cellspacing='1' class='sortable'  ".$border.'>';
    $stream .= "    <thead class=rowheader> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor."></td>";
    $stream .= "            <td align=center colspan='2' ".$bgcolor.">HM TRAKSI  H. I</td>";
    $stream .= "            <td align=center colspan='3' ".$bgcolor.">JUMLAH HM TRAKSI</td>";
    $stream .= "            <td align=center colspan='3' ".$bgcolor.">KONSUMSI BBM (LITER)</td>";
    $stream .= "            <td align=center colspan='2' ".$bgcolor.">RASIO BBM (LITER/HM)</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center ".$bgcolor.">AWAL</td>";
    $stream .= "            <td align=center ".$bgcolor.">AKHIR</td>";
    $stream .= "            <td align=center ".$bgcolor.">H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D B.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D B.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D H.I</td>";
    $stream .= "            <td align=center ".$bgcolor.">S.D B.I</td>";
    $stream .= "        </tr> ";
    $stream .= "    </thead> ";
    $stream .= "    <tbody> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=11 ".$bgcolor.">PEMAKAIAN GENSET</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - GENSET 1</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulanan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - GENSET 2</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulanan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalG3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - GENSET 3</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulanan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL1.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - LOADER 1</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulanan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulanan,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL2.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulananL2 = 0;
    $$jumlahHIHMBulanan = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulananL2 += $value2['jumlah'];
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - LOADER 2</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulananL2,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulananL2,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalL3.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulananL3 = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulananL3 += $value2['jumlah'];
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - LOADER 3</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulananL3,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulananL3,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";

    // ======== START ===========

    // tahunan
    $queryGetSolarTahunan = '  SELECT * FROM vhc_runht
            WHERE
                kodevhc = "'.$generalLR.'"
            AND 
                kodeorg="'.$kodeorg.'" 
            AND
            (
                tanggal >= "'.substr($tanggal,0,5).'01-01"
                AND
                tanggal <= "'.$tanggal.'"
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
                tanggal >= "'.substr($tanggal,0,8).'01"
                AND
                tanggal <= "'.$tanggal.'"
            )
            AND
                posting = 1';
    $dataSolarBulanan = fetchData($queryGetSolarBulanan);
    $jumlahBBMBulanan = 0;
    $$jumlahHIHMBulananLR = 0;
    foreach ($dataSolarBulanan as $key => $value) {
        $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
        $dataHM = fetchData($queryGetHM);
        foreach ($dataHM as $key2 => $value2) {
            $jumlahHIHMBulananLR += $value2['jumlah'];
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
                tanggal = "'.$tanggal.'" 
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
    if (empty($hmAwalGensetHI)) {
        $hmAwalGensetHI = 0;
    }
    if (empty($hmAkhirGensetHI)) {
        $hmAkhirGensetHI = 0;
    }

    // ======== END ===========
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> - LOADER RENTAL</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAwalGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$hmAkhirGensetHI."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHM,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMBulananLR,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahHIHMTahunan,2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBM."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMBulanan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahBBMTahunan."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMBulanan/round($jumlahHIHMBulananLR,2),2)."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".round($jumlahBBMTahunan/round($jumlahHIHMTahunan,2),2)."</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH PEMAKAIAN</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "            <td align=center ".$bgcolor.">0</td>";
    $stream .= "        </tr> ";


    

    $stream .= "    </tbody> ";
    $stream .= "</table> ";
    $stream .= "        <br><br>";

    /*
        TABLE 4 - STOCK BY PRODUCTS
    */

    $stream .= "    <table cellspacing='1' class='sortable'  ".$border.'>';
    $stream .= "    <thead class=rowheader> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor."></td>";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor.">STOCK AWAL (KG)</td>";
    $stream .= "            <td align=center rowspan='2' ".$bgcolor.">STOCK AKHIR (KG)</td>";
    $stream .= "            <td align=center colspan='3' ".$bgcolor.">KWALITAS STOCK HARI INI</td>";
    $stream .= "        </tr> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=center ".$bgcolor.">ALB (%)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KADAR AIR (%)</td>";
    $stream .= "            <td align=center ".$bgcolor.">KADAR KOTORAN (%)</td>";
    $stream .= "        </tr> ";
    $stream .= "    </thead> ";
    $stream .= "    <tbody> ";
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=6 ".$bgcolor.">STOCK PRODUKSI</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=6 ".$bgcolor.">CPO</td>";
    $stream .= "        </tr> ";

    $queryGetSoundingCPO = "   SELECT kuantitas, kodetangki, cpoffa, cpokdair, cpokdkot FROM pabrik_masukkeluartangki 
                            WHERE 
                            kuantitas > 0
                            AND
                            kodeorg = '".$kodeorg."'
                            AND
                            posting = 1
                            AND
                            (
                            tanggal >= '".$tanggal." 00:00:00'
                            AND
                            tanggal <= '".$tanggal." 23:59:59'
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
                                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                                ORDER BY
                                    tanggal DESC
                                limit 1
                                ";
        $dataStockAwalSoundingCPO = fetchData($queryGetStockAwalSoundingCPO);

        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> ".$value['kodetangki']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$dataStockAwalSoundingCPO[0]['kuantitas']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['kuantitas']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['cpoffa']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['cpokdair']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['cpokdkot']."</td>";
        $stream .= "        </tr> ";
        $jumlahStockSoundingCPO += $value['kuantitas'];
        $jumlahStockAwalSoundingCPO += $dataStockAwalSoundingCPO[0]['kuantitas'];
    }

    

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH STOCK CPO</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahStockAwalSoundingCPO."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahStockSoundingCPO."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";

    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left colspan=6 ".$bgcolor.">KERNEL</td>";
    $stream .= "        </tr> ";

    $queryGetSoundingPK = "   SELECT kernelquantity, kodetangki, kernelffa, kernelkdair, kernelkdkot FROM pabrik_masukkeluartangki 
                            WHERE 
                            kernelquantity > 0
                            AND
                            kodeorg = '".$kodeorg."'
                            AND
                            posting = 1
                            AND
                            (
                            tanggal >= '".$tanggal." 00:00:00'
                            AND
                            tanggal <= '".$tanggal." 23:59:59'
                            )";
    $dataSoundingPK = fetchData($queryGetSoundingPK);
    $jumlahStockSoundingPK = 0;
    $jumlahStockAwalSoundingPK = 0;
    foreach ($dataSoundingPK as $key => $value) {
        $queryGetStockAwalSoundingPK = "  SELECT kernelquantity, kodetangki, kernelffa, kernelkdair, kernelkdkot FROM pabrik_masukkeluartangki 
                                WHERE 
                                kodetangki = '".$value['kodetangki']."'
                                AND
                                kodeorg = '".$kodeorg."'
                                AND
                                posting = 1
                                AND 
                                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                                ORDER BY
                                    tanggal DESC
                                limit 1
                                ";
        $dataStockAwalSoundingPK = fetchData($queryGetStockAwalSoundingPK);
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor."  style='padding-left: 15px;'> ".$value['kodetangki']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$dataStockAwalSoundingPK[0]['kernelquantity']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['kernelquantity']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['kernelffa']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['kernelkdair']."</td>";
        $stream .= "            <td align=center ".$bgcolor.">".$value['kernelkdkot']."</td>";
        $stream .= "        </tr> ";
        $jumlahStockSoundingPK += $value['kernelquantity'];
        $jumlahStockAwalSoundingPK += $dataStockAwalSoundingPK[0]['kernelquantity'];
    }

    

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JUMLAH STOCK KERNEL</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahStockAwalSoundingPK."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$jumlahStockSoundingPK."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">JANJANG KOSONG (EMPTY FRUIT BUNCH)</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiSebelum[0]['stock_product_janjang_kosong']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['stock_product_janjang_kosong']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";

    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">LIMBAH CAIR (POME)</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiSebelum[0]['stock_product_limbar_cair']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['stock_product_limbar_cair']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">CANGKANG (SHELL)</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiSebelum[0]['stock_product_cangkang']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['stock_product_cangkang']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">FIBRE</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiSebelum[0]['stock_product_fibre']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['stock_product_fibre']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";
    
    $stream .= "        <tr> ";
    $stream .= "            <td align=left ".$bgcolor.">ABU INCENERATOR</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksiSebelum[0]['stock_product_abu_incenerator']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">".$dataPabrikProduksi[0]['stock_product_abu_incenerator']."</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "            <td align=center ".$bgcolor.">-</td>";
    $stream .= "        </tr> ";

    $stream .= "    </tbody> ";
    $stream .= "</table> ";
    $stream .= "        <br>";

    $stream .= "<table cellspacing='1' >";
    $stream .= "    <tbody> ";
    if ($dataPabrikProduksi[0]['catatan1'] != NULL) {
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor.">Catatan 1</td>";
        $stream .= "            <td align=left colspan=5 ".$bgcolor.">".$dataPabrikProduksi[0]['catatan1']."</td>";
        $stream .= "        </tr> ";
    }
    if ($dataPabrikProduksi[0]['catatan2'] != NULL) {
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor.">Catatan 2</td>";
        $stream .= "            <td align=left colspan=5 ".$bgcolor.">".$dataPabrikProduksi[0]['catatan2']."</td>";
        $stream .= "        </tr> ";
    }
    if ($dataPabrikProduksi[0]['catatan3'] != NULL) {
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor.">Catatan 3</td>";
        $stream .= "            <td align=left colspan=5 ".$bgcolor.">".$dataPabrikProduksi[0]['catatan3']."</td>";
        $stream .= "        </tr> ";
    }
    if ($dataPabrikProduksi[0]['catatan4'] != NULL) {
        $stream .= "        <tr> ";
        $stream .= "            <td align=left ".$bgcolor.">Catatan 4</td>";
        $stream .= "            <td align=left colspan=5 ".$bgcolor.">".$dataPabrikProduksi[0]['catatan4']."</td>";
        $stream .= "        </tr> ";
    }  
    $stream .= "    </tbody> ";
    $stream .= "</table> ";
    $stream .= "        <br>";


    switch ($method) {
        case 'preview':
            print_r($stream);
            break;
        case 'excel':
            $stream .= 'Print Time : '.date('H:i:s, d/m/Y').'<br>By : '.$_SESSION['empl']['name'];
            $tglSkrg = date('Ymd');
            $nop_ = 'Laporan_Produksi_Harian_'.tanggalnormal($tanggal);
            if (0 < strlen($stream)) {
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ('.' !== $file && '..' !== $file) {
                            @unlink('tempExcel/'.$file);
                        }
                    }
                    closedir($handle);
                }

                $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
                if (!fwrite($handle, $stream)) {
                    echo "  <script language=javascript1.2>
                                parent.window.alert('Can't convert to excel format');
                            </script>";
                    exit();
                }

                echo "  <script language=javascript1.2>
                            window.location='tempExcel/".$nop_.".xls';
                        </script>";
                closedir($handle);
            }

            break;
    }
?>