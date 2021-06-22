<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';

$proses = $_POST['proses'];

switch ($proses) {
    case 'gettbs':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $i = '  select sum(beratbersih) - sum(kgpotsortasi) as tbsmasuk 
                from '.$dbname.".pabrik_timbangan where tanggal LIKE '".$tanggal."%' ";

        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $tbs = $d['tbsmasuk'];
        if ($tbs == '') {
            $tbs = 0;
        }
        echo $tbs;
        break;
    case 'getTBSSisaKemarin':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = 'select sisahariini from pabrik_produksi where kodeorg="'.$kodeorg.'" ORDER BY tanggal DESC LIMIT 1';

        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $tbsSisa = $d['sisahariini'];
        if ($tbsSisa == '' || $tbsSisa < 1) {
            $tbsSisa = 0;
        }
        echo $tbsSisa;
        break;
    case 'getTBSDiOlah':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = 'select tbsdiolah from pabrik_pengolahan where kodeorg="'.$kodeorg.'" and tanggal = "'.$tanggal.'"';
        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $tbsDiOlah = $d['tbsdiolah'];
        if ($tbsDiOlah == '' || $tbsDiOlah < 1) {
            $tbsDiOlah = 0;
        }
        echo $tbsDiOlah;
        break;
    case 'getCPOLoses':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = "  SELECT a.nilai, b.namaitem FROM pabrik_kelengkapanloses a
                JOIN pabrik_5kelengkapanloses b
                ON a.id = b.id
                WHERE 
                a.kodeorg = '".$kodeorg."'
                AND
                b.produk = 'CPO'
                AND
                a.tanggal = '".$tanggal."'";
        $x = fetchData($i);
        // $n = mysql_query($i);
        // $d = mysql_fetch_assoc($n);
        echo json_encode($x);
        break;
    case 'getKernelLoses':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = "  SELECT a.nilai, b.namaitem FROM pabrik_kelengkapanloses a
                JOIN pabrik_5kelengkapanloses b
                ON a.id = b.id
                WHERE 
                a.kodeorg = '".$kodeorg."'
                AND
                b.produk = 'KERNEL'
                AND
                a.tanggal = '".$tanggal."'";
        $x = fetchData($i);
        // $n = mysql_query($i);
        // $d = mysql_fetch_assoc($n);
        echo json_encode($x);
        break;
    case 'getCPOSounding':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = "  SELECT SUM(kuantitas) AS jumlah_cpo_sounding_hari_ini FROM pabrik_masukkeluartangki 
                WHERE 
                kuantitas > 0
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $x = fetchData($i);
        echo json_encode($x[0]);
        break;
    case 'getKERNELSounding':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $i = "  SELECT SUM(kernelquantity) AS jumlah_kernel_sounding_hari_ini FROM pabrik_masukkeluartangki 
                WHERE 
                kernelquantity > 0
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $x = fetchData($i);
        echo json_encode($x[0]);
        break;
    case 'getCPOPengirimanKemarin':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $kodebarang = 40000001;
        $i = "  SELECT SUM(beratbersih) AS jumlah_cpo_pengiriman_kemarin FROM pabrik_timbangan 
                WHERE 
                notransaksi LIKE 'K%'
                AND
                kodebarang = '".$kodebarang."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                OR
                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                )";
        $x = fetchData($i);
        echo json_encode($x[0]);
        break;
    case 'getKERNELPengirimanKemarin':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $kodebarang = 40000002;
        $i = "  SELECT SUM(beratbersih) AS jumlah_kernel_pengiriman_kemarin FROM pabrik_timbangan 
                WHERE 
                notransaksi LIKE 'K%'
                AND
                kodebarang = '".$kodebarang."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00' - INTERVAL 1 DAY
                OR
                tanggal <= '".$tanggal." 23:59:59' - INTERVAL 1 DAY
                )";
        $x = fetchData($i);
        echo json_encode($x[0]);
        break;
    case 'getPengirimanHariIni':
        $tanggal = tanggalsystem($_POST['tanggal']);
        $tanggal = substr($tanggal,0,4)."-".substr($tanggal,4,2)."-".substr($tanggal,6,2);
        $kodeorg = $_POST['kodeorg'];
        $kodebarang1 = 40000001; // CPO
        $kodebarang2 = 40000002; // KERNEL
        $kodebarang3 = 40000004; // CANGKANG
        $kodebarang4 = 40000005; // FIBER
        $kodebarang5 = 40000006; // ABU JANJANG

        $cpoQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                WHERE 
                notransaksi LIKE 'K%'
                AND
                kodebarang = '".$kodebarang1."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $cpoHasil = fetchData($cpoQuery);

        $kernelQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                WHERE 
                notransaksi LIKE 'K%'
                AND
                kodebarang = '".$kodebarang2."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $kernelHasil = fetchData($kernelQuery);

        $cangkangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                WHERE
                notransaksi LIKE 'K%'
                AND 
                kodebarang = '".$kodebarang3."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $cangkangHasil = fetchData($cangkangQuery);

        $fiberQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                WHERE 
                notransaksi LIKE 'K%'
                AND
                kodebarang = '".$kodebarang4."'
                AND
                kodeorg = '".$kodeorg."'
                AND
                (
                tanggal >= '".$tanggal." 00:00:00'
                OR
                tanggal <= '".$tanggal." 23:59:59'
                )";
        $fiberHasil = fetchData($fiberQuery);

        $abuJanjangQuery = "  SELECT SUM(beratbersih) AS jumlah FROM pabrik_timbangan 
                    WHERE 
                    notransaksi LIKE 'K%'
                    AND
                    kodebarang = '".$kodebarang5."'
                    AND
                    kodeorg = '".$kodeorg."'
                    AND
                    (
                    tanggal >= '".$tanggal." 00:00:00'
                    OR
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
}



?>