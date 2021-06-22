<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$param['jumlahrealisasi'] = str_replace(',', '', $param['jumlahrealisasi']);
$pt = getPT($dbname, $param['kodeorg']);
if (false == $pt) {
    $pt = getHolding($dbname, $param['kodeorg']);
}

$tgl = tanggalsystem($param['tanggal']);
if ($tgl < $_SESSION['org']['period']['start']) {
    exit('Error:Tanggal diluar periode aktif');
}

// Cek SPK nya sudah posting belum - FA 202001
$squery="select * from log_spkht where notransaksi='".$param['notransaksi']."'";
$hasil = mysql_query($squery);
$res = mysql_fetch_array($hasil); 
if ($res['posting'] == 0 || $res['useridposting'] < 1){
	echo "Warning: SPK belum diposting";
	exit();
};			
// --------------------------------------------

// Cek kodevhc untuk vhcrunning  - HP 202010
$squery1="select * from organisasi where kodeorganisasi='".$param['blokalokasi']."'";
$hasil1 = mysql_query($squery1);
$res1 = mysql_fetch_assoc($hasil1); 

if ($res1['tipe'] == "STENGINE"){
    $kodevhc=$res1['namaorganisasi'];
}else{
    $kodevhc = '';
}          
// --------------------------------------------

$query = selectQuery($dbname, 'log_baspk', '*', "notransaksi='".$param['notransaksi']."' and kodeblok='".$param['blokalokasi']."' and kodekegiatan='".$param['kodekegiatan']."' and tanggal='".$tgl."' and blokspkdt='".$param['kodeblok']."' ");
$data = fetchData($query);
$error0 = '';
if (1 == $data[0]['statusjurnal']) {
    $error0 .= $_SESSION['lang']['errisposted'];
}

if ('' != $error0) {
    echo "Data Error :\n".$error0;
    exit();
}

$error1 = '';
if (0 == count($data)) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}

if ('' != $error1) {
    echo "Data Error :\n".$error1;
    exit();
}

$kodeJurnal = 'SPK1';
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,noakun', "kodekegiatan='".$param['kodekegiatan']."'");
$optSupp = makeOption($dbname, 'log_5klsupplier', 'kode,noakun', "kode='".substr($param['koderekanan'], 0, 4)."'");
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."' ");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$nojurnal = $tgl.'/'.$param['kodeorg'].'/'.$kodeJurnal.'/'.$konter;
if (5 < strlen($param['blokalokasi'])) {
    $blok = $param['blokalokasi'];
} else {
    $blok = '';
}

$kodeasset = '';
if ('AK' == substr($param['blokalokasi'], 0, 2) || 'PB' == substr($param['blokalokasi'], 0, 2)) {
    $tipeasset = substr($param['blokalokasi'], 3, 3);
    $tipeasset = str_replace('0', '', $tipeasset);
    $str = 'select akunak from '.$dbname.".sdm_5tipeasset where kodetipe='".$tipeasset."'";
    $res = mysql_query($str);
    if (mysql_num_rows($res) < 1) {
        exit(' Error: Akun aktiva dalam konstruksi untuk '.$tipeasset.' beum disetting dari keuangan->setup->tipeasset');
    }

    while ($bar = mysql_fetch_object($res)) {
        if ('' == $bar->akunak) {
            exit(' Error: Akun aktiva dalam konstruksi untuk '.$tipeasset.' beum disetting dari keuangan->setup->tipeasset');
        }

        $kodeasset = $param['blokalokasi'];
        $blok = '';
        $optKeg[$param['kodekegiatan']] = $bar->akunak;
    }
}

$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodeJurnal, 'tanggal' => $tgl, 'tanggalentry' => date('Ymd'), 'posting' => 0, 'totaldebet' => $param['jumlahrealisasi'], 'totalkredit' => -1 * $param['jumlahrealisasi'], 'amountkoreksi' => '0', 'noreferensi' => $param['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
$noUrut = 1;
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tgl, 'nourut' => $noUrut, 'noakun' => $optKeg[$param['kodekegiatan']], 'keterangan' => 'Realisasi SPK '.$param['kodeorg'].'/'.$param['notransaksi'], 'jumlah' => $param['jumlahrealisasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $param['kodeorg'], 'kodekegiatan' => $param['kodekegiatan'], 'kodeasset' => $kodeasset, 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $param['notransaksi'], 'noaruskas' => '', 'kodevhc' => $kodevhc, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
++$noUrut;
$dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tgl, 'nourut' => $noUrut, 'noakun' => $optSupp[substr($param['koderekanan'], 0, 4)], 'keterangan' => 'Realisasi SPK '.$param['kodeorg'].'/'.$param['notransaksi'], 'jumlah' => -1 * $param['jumlahrealisasi'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $param['kodeorg'], 'kodekegiatan' => $param['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => $param['koderekanan'], 'noreferensi' => $param['notransaksi'], 'noaruskas' => '', 'kodevhc' => $kodevhc, 'nodok' => '', 'kodeblok' => $blok, 'revisi' => '0'];
++$noUrut;
$dataRes['header']['totaldebet'] = $param['jumlahrealisasi'];
$dataRes['header']['totalkredit'] = $param['jumlahrealisasi'];
$headErr = '';
$insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
if (!mysql_query($insHead)) {
    $headErr .= 'Insert Header Error : '.mysql_error()."\n";
}

if ('' == $headErr) {
    $detailErr = '';
    foreach ($dataRes['detail'] as $row) {
        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
        if (!mysql_query($insDet)) {
            $detailErr .= 'Insert Detail Error : '.mysql_error()."\n".$insDet;

            break;
        }
    }
    if ('' == $detailErr) {
        $updJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if (!mysql_query($updJurnal)) {
            echo 'Update Kode Jurnal Error : '.mysql_error()."\n";
            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
            if (!mysql_query($RBDet)) {
                echo 'Rollback Delete Header Error : '.mysql_error()."\n";
                exit();
            }

            exit();
        }

        $updTrans = updateQuery($dbname, 'log_baspk', ['statusjurnal' => 1], "notransaksi='".$param['notransaksi']."' and kodeblok='".$param['blokalokasi']."' and kodekegiatan='".$param['kodekegiatan']."' and tanggal='".$tgl."'");
        saveLog($updTrans);
        if (!mysql_query($updTrans)) {
            echo 'Update Status Jurnal Error : '.mysql_error()."\n";
            $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
            if (!mysql_query($RBDet)) {
                echo 'Rollback Delete Header Error : '.mysql_error()."\n";
                exit();
            }

            $RBJurnal = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if (!mysql_query($RBJurnal)) {
                echo 'Rollback Update Jurnal Error : '.mysql_error()."\n";
                exit();
            }

            exit();
        }

        echo '1';
    } else {
        echo $detailErr;
        $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
        if (!mysql_query($RBDet)) {
            echo 'Rollback Delete Header Error : '.mysql_error();
            exit();
        }
    }
} else {
    echo $headErr;
    exit();
}

?>
