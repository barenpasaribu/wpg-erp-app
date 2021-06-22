<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';

    $proses = $_POST['proses'];

    $kodeBarangCPO = 40000001; // CPO
    $kodeBarangPK = 40000002; // KERNEL
    $kodeBarangTBS = 40000003; // TBS
    $kodeBarangCangkang = 40000004; // CANGKANG
    $kodeBarangFiber = 40000005; // FIBER
    $kodeBarangAbuJanjang = 40000006; // ABU JANJANG

    switch ($proses) {
        // untuk pengopreasian pabrik
        case 'getDataTimbanganPengolahanPabrik':
            $kodeorg = $_POST['kodeorg'];
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $i = '  select sum(beratmasuk-beratkeluar) as tbs_bruto, sum(kgpotsortasi) as tbs_potongan
                    from '.$dbname.".pabrik_timbangan 
                    where 
                    kodebarang = '".$kodeBarangTBS."'
                    AND
                    tanggal LIKE '".$tanggal."%' 
                    AND
                    millcode = '".$kodeorg."'
                    ";
            $data = fetchData($i);
            echo json_encode($data[0]);
            break;
        case 'getTBSSisaKemarinPengolahanPabrik':
            $kodeorg = $_POST['kodeorg'];
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $queryGetProduksi = "   select tbs_sisa
                                    from pabrik_pengolahan
                                    WHERE
                                    tanggal = '".$tanggal."' - INTERVAL 1 DAY
                                    AND
                                    kodeorg = '".$kodeorg."'
                                    ";

            $data = fetchData($queryGetProduksi);
            echo json_encode($data[0]);
            break;
        case 'getSortasiTidakOlah':
            $kodeorg = $_POST['kodeorg'];
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $queryGetProduksi = "   SELECT status_olah, tbs_potongan
                                    FROM pabrik_pengolahan
                                    WHERE
                                    tanggal = '".$tanggal."' - INTERVAL 1 DAY
                                    AND
                                    kodeorg = '".$kodeorg."'
                                    AND
                                    posting = 1
                                    ";

            $data = fetchData($queryGetProduksi);

            $sortasi = 0;
            if ($data[0]['status_olah'] == 0) {
                if (empty($data[0]['tbs_potongan'])) {
                    $sortasi = 0;
                }else{
                    $sortasi = $data[0]['tbs_potongan'];
                }
                
            }else{
                $sortasi = 0;
            }

            $hasil = [
                'sortasi' => $sortasi
            ];
            echo json_encode($hasil);
            break;
        
        case 'getTimbanganByBarang':
            $kodeorg = $_POST['kodeorg'];
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodebarang = "40000003";
            $queryGetTimbangan = '  select sum(beratbersih) as jumlah 
                                    from '.$dbname.".pabrik_timbangan 
                                    WHERE 
                                    kodebarang = '".$kodebarang."'
                                    AND
                                    tanggal LIKE '".$tanggal."%' 
                                    AND
                                    millcode = '".$kodeorg."'
                                    ";

            $data = fetchData($queryGetTimbangan);
            echo json_encode($data[0]);
            break;
        
        
        // untuk pabrik produksi harian
            
        case 'getProduksiHarianTerakhir':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $i = '  SELECT 
                        *
                    FROM 
                        pabrik_produksi 
                    WHERE 
                        kodeorg="'.$kodeorg.'" 
                    AND 
                        tanggal < "'.$tanggal.'"
                    ORDER BY 
                        tanggal 
                    DESC LIMIT 1';
            $data = fetchData($i);
            echo json_encode($data[0]);

            break;
        case 'getDataPengolahan':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $i = 'select * from pabrik_pengolahan
            WHERE 
            kodeorg="'.$kodeorg.'" 
            AND
            tanggal = "'.$tanggal.'" 
            AND
            posting = 1';
            $data = fetchData($i);
            if(empty($data[0])){
                echo json_encode("awan");
            }else{
                echo json_encode($data[0]);
            }            

            break;
        case 'calcUtilitasKapasitas':
    
            $kodeorg = $_POST['kodeorg'];
            $kapasitasOlah = round($_POST['kapasitas_olah']);
    
            // pengaturan general
            $queryGetPengaturanGeneral = "SELECT  * FROM pabrik_5general where kodeorg='".$kodeorg."'";
            $dataPengaturanAdmin = fetchData($queryGetPengaturanGeneral);
    
            $kapasitasTerpasang = 1;
    
            foreach ($dataPengaturanAdmin as $key => $value) {
                if ($value['code'] == "KP") {
                    $kapasitasTerpasang = $value['nilai'];
                }
            }
            if($kapasitasOlah == 0){
                $hasil = 0;
            }else{
                $hasil = ($kapasitasOlah / $kapasitasTerpasang)*100;
            }
    
            echo round($hasil,2);
    
            break;
        case 'calcUtilitasFactorCormecial':
            $kodeorg = $_POST['kodeorg'];
            $tbsOlah = round($_POST['tbs_diolah']);

            // pengaturan general
            $queryGetPengaturanGeneral = "SELECT  * FROM pabrik_5general where kodeorg='".$kodeorg."'";
            $dataPengaturanAdmin = fetchData($queryGetPengaturanGeneral);

            $kapasitasTerpasang = 1;
            $jamOlah = 1;

            foreach ($dataPengaturanAdmin as $key => $value) {
                if ($value['code'] == "KP") {
                    $kapasitasTerpasang = $value['nilai'];
                }
                if ($value['code'] == "JO") {
                    $jamOlah = $value['nilai'];
                }
            }
            // tbs olah / kapasitas terpasang * jolah
            $hasil = ($tbsOlah / ($kapasitasTerpasang * $jamOlah))*100;

            echo round($hasil,2);

            break;
        case 'getSolar': 
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
    
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
            }
    
            $i = 'select * from vhc_runht
            WHERE 
            kodeorg="'.$kodeorg.'" 
            AND
            tanggal = "'.$tanggal.'" 
            AND
            posting = 1';
            $data = fetchData($i);
            
            
            $genset1 = $genset2 = $genset3 = $loader1 = $loader2 = $loader3 = $loaderr = 0;
    
            $hmgenset1 = $hmgenset2 = $hmgenset3 = $hmloader1 = $hmloader2 = $hmloader3 = $hmloaderr = 0;
            $hmgenset1akhir = $hmgenset2akhir = $hmgenset3akhir = $hmloader1akhir = $hmloader2akhir = $hmloader3akhir = $hmloaderrakhir = 0;
            foreach ($data as $key => $value) {
                $queryGetHM = "SELECT * FROM vhc_rundt WHERE notransaksi ='".$value['notransaksi']."'";
                $queryActHM = fetchData($queryGetHM);
                
                $hmjumlah = 0;
                foreach ($queryActHM as $key2 => $value2) {
                    $hmjumlah = $hmjumlah + $value2['jumlah'];
                }
    
                if ($value['kodevhc'] == $generalG1) {
                    $genset1 += $value['jlhbbm'];
                    $hmgenset1 = $hmgenset1 + $hmjumlah;
                }
                if ($value['kodevhc'] == $generalG2) {
                    $genset2 += $value['jlhbbm'];
                    $hmgenset2 = $hmgenset2 + $hmjumlah;
                }
                if ($value['kodevhc'] == $generalG3) {
                    $genset3 += $value['jlhbbm'];
                    $hmgenset3 = $hmgenset3 + $hmjumlah;
                }
    
                if ($value['kodevhc'] == $generalL1) {
                    $loader1 += $value['jlhbbm'];
                    $hmloader1 = $hmloader1 + $hmjumlah;
                }
                if ($value['kodevhc'] == $generalL2) {
                    $loader2 += $value['jlhbbm'];
                    $hmloader2 = $hmloader2 + $hmjumlah;
                }
                if ($value['kodevhc'] == $generalL3) {
                    $loader3 += $value['jlhbbm'];
                    $hmloader3 = $hmloader3 + $hmjumlah;
                }
                if ($value['kodevhc'] == $generalLR) {
                    $loaderr += $value['jlhbbm'];
                    $hmloaderr = $hmloaderr + $hmjumlah;
                }
            }
    
            $dataJson['genset1'] = $genset1;
            $dataJson['genset2'] = $genset2;
            $dataJson['genset3'] = $genset3;
            $dataJson['loader1'] = $loader1;
            $dataJson['loader2'] = $loader2;
            $dataJson['loader3'] = $loader3;
            $dataJson['loaderr'] = $loaderr;
    
            
            $dataJson['hmgenset1'] = round($hmgenset1,2);
            $dataJson['hmgenset2'] = round($hmgenset2,2);
            $dataJson['hmgenset3'] = round($hmgenset3,2);
            $dataJson['hmloader1'] = round($hmloader1,2);
            $dataJson['hmloader2'] = round($hmloader2,2);
            $dataJson['hmloader3'] = round($hmloader3,2);
            $dataJson['hmloaderr'] = round($hmloaderr,2);
    
    
    
            echo json_encode($dataJson);
    
            break;
        case 'getHM':
            $notransaksi = $_POST['notransaksi'];
            $i = 'select (kmhmakhir - kmhmawal) as jumlah from vhc_rundt
            WHERE 
            notransaksi="'.$notransaksi.'" '
            ;
            $data = fetchData($i);
            echo json_encode($data[0]);

            break;
        case 'getLoses':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];

            $getTransaksiLoses = "  SELECT b.produk, b.namaitem, a.nilai FROM pabrik_kelengkapanloses a
                    JOIN pabrik_5kelengkapanloses b
                    ON a.id = b.id
                    WHERE 
                    a.kodeorg = '".$kodeorg."'
                    AND
                    a.tanggal = '".$tanggal."' 
                    ";
            $dataTransaksi = fetchData($getTransaksiLoses);
            
            $getMasterLoses = " SELECT produk, namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                                FROM pabrik_5kelengkapanloses
                                WHERE 
                                kodeorg = '".$kodeorg."'
                                -- AND
                                -- (
                                --     faktor_konversi_1 > 0
                                --     OR
                                --     faktor_konversi_2 > 0
                                --     OR
                                --     faktor_konversi_3 > 0
                                -- )
                                ";
            $dataMaster = fetchData($getMasterLoses);
            foreach ($dataTransaksi as $key => $value) {
                $hasil = 0;
                foreach ($dataMaster as $key2 => $value2) {
                    if ($value['produk'] == $value2['produk'] && $value['namaitem'] == $value2['namaitem']) {
                        $hasil = $value['nilai'];
                        if ($value2['faktor_konversi_1'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_1'] / 100;
                        }
                        if ($value2['faktor_konversi_2'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_2'] / 100;
                        }
                        if ($value2['faktor_konversi_3'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_3'] / 100;
                        }
                    }
                }
                $dataTransaksi[$key]['hasil'] = $hasil;
            }

            echo json_encode($dataTransaksi);

            break;
        case 'getLosesReal':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];

            $getTransaksiLoses = "  SELECT b.produk, b.namaitem, a.nilai FROM pabrik_kelengkapanloses a
                    JOIN pabrik_5kelengkapanloses b
                    ON a.id = b.id
                    WHERE 
                    a.kodeorg = '".$kodeorg."'
                    AND
                    a.tanggal = '".$tanggal."'
                    ";
            $dataTransaksi = fetchData($getTransaksiLoses);
            
            $getMasterLoses = " SELECT produk, namaitem, faktor_konversi_1, faktor_konversi_2, faktor_konversi_3 
                                FROM pabrik_5kelengkapanloses
                                WHERE 
                                kodeorg = '".$kodeorg."'
                                ";
            $dataMaster = fetchData($getMasterLoses);
            foreach ($dataTransaksi as $key => $value) {
                $hasil = 0;
                foreach ($dataMaster as $key2 => $value2) {
                    if ($value['produk'] == $value2['produk'] && $value['namaitem'] == $value2['namaitem']) {
                        $hasil = $value['nilai'];
                        if ($value2['faktor_konversi_1'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_1'] / 100;
                        }
                        if ($value2['faktor_konversi_2'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_2'] / 100;
                        }
                        if ($value2['faktor_konversi_3'] > 0) {
                            $hasil = $hasil * $value2['faktor_konversi_3'] / 100;
                        }
                    }
                }
                $dataTransaksi[$key]['hasil'] = $hasil;
            }

            echo json_encode($dataTransaksi);

            break;
        case 'getPengiriman':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $kodebarang1 = $kodeBarangCPO; // CPO
            $kodebarang2 = $kodeBarangPK; // KERNEL
            $kodebarang3 = $kodeBarangCangkang; // CANGKANG
            $kodebarang4 = $kodeBarangFiber; // FIBER
            $kodebarang5 = $kodeBarangAbuJanjang; // ABU JANJANG

            $cpoQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang1."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $cpoHasil = fetchData($cpoQuery);

            $kernelQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang2."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $kernelHasil = fetchData($kernelQuery);
                        // print_r($kernelQuery);
                        // die();
            $cangkangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE
                    notransaksi LIKE 'K%'
                    AND 
                    kodebarang = '".$kodebarang3."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $cangkangHasil = fetchData($cangkangQuery);

            $fiberQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang4."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $fiberHasil = fetchData($fiberQuery);

            $abuJanjangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                        WHERE 
                        notransaksi LIKE 'K%'
                        AND
                        kodebarang = '".$kodebarang5."'
                        AND
                        millcode = '".$kodeorg."'
                        AND
                        (
                        tanggal >= '".$tanggal." 00:00:00'
                        AND
                        tanggal <= '".$tanggal." 23:59:59'
                        )";
            $abuJanjangHasil = fetchData($abuJanjangQuery);

            $hasil1 = 0;
            $hasil2 = 0;
            $hasil3 = 0;
            $hasil4 = 0;
            $hasil5 = 0;
            if($cpoHasil[0]['jumlah'] != NULL){
                $hasil1 = $cpoHasil[0]['jumlah'];
            }
            if($kernelHasil[0]['jumlah'] != NULL){
                $hasil2 = $kernelHasil[0]['jumlah'];
            }
            if($cangkangHasil[0]['jumlah'] != NULL){
                $hasil3 = $cangkangHasil[0]['jumlah'];
            }
            if($fiberHasil[0]['jumlah'] != NULL){
                $hasil4 = $fiberHasil[0]['jumlah'];
            }
            if($abuJanjangHasil[0]['jumlah'] != NULL){
                $hasil5 = $abuJanjangHasil[0]['jumlah'];
            }

            $hasil = [
                'cpo' => $hasil1,
                'kernel' => $hasil2,
                'cangkang' => $hasil3,
                'fiber' => $hasil4,
                'abu_janjang' => $hasil5
            ];
            echo json_encode($hasil);
            break;
        case 'getPengirimanHI':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $kodebarang1 = $kodeBarangCPO; // CPO
            $kodebarang2 = $kodeBarangPK; // KERNEL
            $kodebarang3 = $kodeBarangCangkang; // CANGKANG
            $kodebarang4 = $kodeBarangFiber; // FIBER
            $kodebarang5 = $kodeBarangAbuJanjang; // ABU JANJANG

            $cpoQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang1."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $cpoHasil = fetchData($cpoQuery);

            $kernelQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang2."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $kernelHasil = fetchData($kernelQuery);
            $cangkangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE
                    notransaksi LIKE 'K%'
                    AND 
                    kodebarang = '".$kodebarang3."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $cangkangHasil = fetchData($cangkangQuery);

            $fiberQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang4."'
                    AND
                    millcode = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    AND
                    tanggal <= '".$tanggal." 23:59:59'
                    )";
            $fiberHasil = fetchData($fiberQuery);

            $abuJanjangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                        WHERE 
                        notransaksi LIKE 'K%'
                        AND
                        kodebarang = '".$kodebarang5."'
                        AND
                        millcode = '".$kodeorg."'
                        AND
                        (
                        tanggal >= '".$tanggal." 00:00:00'
                        AND
                        tanggal <= '".$tanggal." 23:59:59'
                        )";
            $abuJanjangHasil = fetchData($abuJanjangQuery);

            $hasil1 = 0;
            $hasil2 = 0;
            $hasil3 = 0;
            $hasil4 = 0;
            $hasil5 = 0;
            if($cpoHasil[0]['jumlah'] != NULL){
                $hasil1 = $cpoHasil[0]['jumlah'];
            }
            if($kernelHasil[0]['jumlah'] != NULL){
                $hasil2 = $kernelHasil[0]['jumlah'];
            }
            if($cangkangHasil[0]['jumlah'] != NULL){
                $hasil3 = $cangkangHasil[0]['jumlah'];
            }
            if($fiberHasil[0]['jumlah'] != NULL){
                $hasil4 = $fiberHasil[0]['jumlah'];
            }
            if($abuJanjangHasil[0]['jumlah'] != NULL){
                $hasil5 = $abuJanjangHasil[0]['jumlah'];
            }

            $hasil = [
                'cpo' => $hasil1,
                'kernel' => $hasil2,
                'cangkang' => $hasil3,
                'fiber' => $hasil4,
                'abu_janjang' => $hasil5
            ];
            echo json_encode($hasil);
            break;
        case 'getReturn':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $kodebarang1 = $kodeBarangCPO; // CPO
            $kodebarang2 = $kodeBarangPK; // KERNEL

            // select nospb from pabrik_timbangan where tipe=3 and nospb in
	// (select notransaksi from pabrik_timbangan where tipe =2)   

            $cpoQuery = "  SELECT SUM(beratmasuk-beratkeluar) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'M%'
                    AND 
                        nospb in (select notransaksi from pabrik_timbangan where tipe = 2 )
                    AND
                        kodebarang = '".$kodebarang1."'
                    AND
                        millcode = '".$kodeorg."'
                    AND
                    (
                        tanggal >= '".$tanggal." 00:00:00'
                        AND
                        tanggal <= '".$tanggal." 23:59:59'
                    )";
            $cpoHasil = fetchData($cpoQuery);

            $kernelQuery = "  SELECT SUM(beratmasuk-beratkeluar) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                        notransaksi LIKE 'M%'
                    AND 
                        nospb in (select notransaksi from pabrik_timbangan where tipe = 2 )
                    AND
                        kodebarang = '".$kodebarang2."'
                    AND
                        millcode = '".$kodeorg."'
                    AND
                    (
                        tanggal >= '".$tanggal." 00:00:00'
                    AND
                        tanggal <= '".$tanggal." 23:59:59'
                    )";
            $kernelHasil = fetchData($kernelQuery);

            $hasil1 = 0;
            $hasil2 = 0;

            if($cpoHasil[0]['jumlah'] != NULL){
                $hasil1 = $cpoHasil[0]['jumlah'];
            }
            if($kernelHasil[0]['jumlah'] != NULL){
                $hasil2 = $kernelHasil[0]['jumlah'];
            }

            $hasil = [
                'return_cpo' => $hasil1,
                'return_pk' => $hasil2
            ];
            echo json_encode($hasil);
            break;
        case 'getCPOSounding':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $i = "  SELECT 
                        SUM(kuantitas) AS jumlah_cpo_sounding FROM pabrik_masukkeluartangki 
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
                    
            $x = fetchData($i);
            echo json_encode($x[0]);
            break;
        case 'getPKSounding':
            $tanggal = tanggalsystem($_POST['tanggal']);
            $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
            $kodeorg = $_POST['kodeorg'];
            $i = "  SELECT SUM(kernelquantity) AS jumlah_pk_sounding FROM pabrik_masukkeluartangki 
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
            $x = fetchData($i);
            echo json_encode($x[0]);
            break;
        case 'quickPosting':
            $queryQuickPosting = "  update 
                        pabrik_kelengkapanloses 
                    set 
                        posting = 1 
                    where 
                        kodeorg = '".$_SESSION['empl']['lokasitugas']."' 
                    and 
                        tanggal='".$_POST['tanggal']."'
                ";
                
            mysql_query($queryQuickPosting);
            if (mysql_affected_rows() > 0) {
                echo "Quick Posting berhasil";
            }else{
                echo "Quick Posting gagal";
            }
            break;
        case 'quickPostingSounding':
            $queryQuickPosting = "  update 
                        pabrik_masukkeluartangki 
                    set 
                        posting = 1 
                    where 
                        kodeorg = '".$_SESSION['empl']['lokasitugas']."' 
                    and 
                        tanggal like'".$_POST['tanggal']."%'
                ";
                
            mysql_query($queryQuickPosting);
            if (mysql_affected_rows() > 0) {
                echo "Quick Posting berhasil";
            }else{
                echo "Quick Posting gagal";
            }
            break;
    }



?>