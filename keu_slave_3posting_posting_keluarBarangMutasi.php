<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$queryH1 = selectQuery($dbname, 'log_transaksiht', '*', "notransaksi='".$param['notransaksi']."'");
$dataH1 = fetchData($queryH1);
$query1 = selectQuery($dbname, 'log_transaksi_vw', '*', "notransaksi='".$param['notransaksi']."' and statussaldo=1");
$data1 = fetchData($query1);
$queryH2 = selectQuery($dbname, 'log_transaksiht', '*', "notransaksi='".$dataH1[0]['notransaksireferensi']."'");
$dataH2 = fetchData($queryH2);
$query2 = selectQuery($dbname, 'log_transaksi_vw', '*', "notransaksi='".$dataH1[0]['notransaksireferensi']."' and statussaldo=1");
$data2 = fetchData($query2);
if (empty($dataH2)) {
    echo 'Error : Data Transaksi tidak ada data transaksi referensi';
    exit();
}

$whereBarang = 'kode in (';
$whereKeg = 'kodekegiatan in (';
foreach ($data1 as $key => $row) {
    if (0 === $key) {
        $whereBarang .= "'".substr($row['kodebarang'], 0, 3)."'";
        $whereKeg .= "'".$row['kodekegiatan']."'";
    } else {
        $whereBarang .= ",'".substr($row['kodebarang'], 0, 3)."'";
        $whereKeg .= ",'".$row['kodekegiatan']."'";
    }
}
foreach ($data2 as $key => $row) {
    $whereBarang .= ",'".substr($row['kodebarang'], 0, 3)."'";
    $whereKeg .= ",'".$row['kodekegiatan']."'";
}
$whereBarang .= ')';
$whereKeg .= ')';
$optBarang = makeOption($dbname, 'log_5klbarang', 'kode,noakun', $whereBarang);
$optKeg = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,noakun', $whereKeg);
$interCo = ['SOGE' => '1120300', 'MRKE' => '1120301', 'SBNE' => '1120302', 'WKNE' => '1120303', 'MJHO' => '1120304', 'SSRO' => '1120305', 'SENE' => '1120306', 'SOGM' => '1120307', 'SKNE' => '1120308', 'SKSE' => '1120309'];
$kodejurnal1 = 'INVK1';
$queryC1 = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal1."'");
$tmpC1 = fetchData($queryC1);
if (empty($tmpC1)) {
    echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
    exit();
}

$konter1 = addZero($tmpC1[0]['nokounter'] + 1, 3);
$tanggalJ1 = tanggalsystem(tanggalnormal($data1[0]['tanggal']));
$nojurnal1 = $tanggalJ1.'/'.substr($data1[0]['notransaksi'], 14, 4).'/'.$kodejurnal1.'/'.$konter1;
$dataRes1['header'] = ['nojurnal' => $nojurnal1, 'kodejurnal' => $kodejurnal1, 'tanggal' => $data1[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $param['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
$noUrut = 1;
foreach ($data1 as $row) {
    $dataRes1['detail'][] = ['nojurnal' => $nojurnal1, 'tanggal' => $row['tanggal'], 'nourut' => $noUrut, 'noakun' => $interCo[substr($row['notransaksi'], 14, 4)], 'keterangan' => $dataH1[0]['keterangan'], 'jumlah' => $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
    $dataRes1['detail'][] = ['nojurnal' => $nojurnal1, 'tanggal' => $row['tanggal'], 'nourut' => $noUrut, 'noakun' => $optBarang[substr($row['kodebarang'], 0, 3)], 'keterangan' => $dataH1[0]['keterangan'], 'jumlah' => -1 * $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
}
$kodejurnal2 = 'INVM1';
$queryC2 = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal2."'");
$tmpC2 = fetchData($queryC2);
if (empty($tmpC2)) {
    echo 'Warning : Kode Jurnal belum disetting untuk PT anda';
    exit();
}

$konter2 = addZero($tmpC2[0]['nokounter'] + 1, 3);
$tanggalJ2 = tanggalsystem(tanggalnormal($data2[0]['tanggal']));
$nojurnal2 = $tanggalJ2.'/'.substr($data2[0]['notransaksi'], 14, 4).'/'.$kodejurnal2.'/'.$konter2;
$dataRes2['header'] = ['nojurnal' => $nojurnal2, 'kodejurnal' => $kodejurnal2, 'tanggal' => $data2[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $data2[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1'];
$noUrut = 1;
foreach ($data2 as $row) {
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $data[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $optKeg[$row['kodekegiatan']], 'keterangan' => $dataH[0]['keterangan'], 'jumlah' => $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => $row['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $data[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $interCo[substr($row['notransaksi'], 14, 4)], 'keterangan' => $dataH2[0]['keterangan'], 'jumlah' => -1 * $row['hartot'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodegudang'], 0, 4), 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $row['kodebarang'], 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeblok']];
    ++$noUrut;
}
$headErr = '';
$insHead1 = insertQuery($dbname, 'keu_jurnalht', $dataRes1['header']);
if (!mysql_query($insHead1)) {
    $headErr .= 'Insert Header 1 Error : '.mysql_error()."\n";
}

$insHead2 = insertQuery($dbname, 'keu_jurnalht', $dataRes2['header']);
if (!mysql_query($insHead2)) {
    $headErr .= 'Insert Header 2 Error : '.mysql_error()."\n";
}

if ('' === $headErr) {
    $detailErr = '';
    foreach ($dataRes1['detail'] as $row) {
        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
        if (!mysql_query($insDet)) {
            $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

            break;
        }
    }
    foreach ($dataRes2['detail'] as $row) {
        $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
        if (!mysql_query($insDet)) {
            $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

            break;
        }
    }
    if ('' === $detailErr) {
        $updErr = '';
        $updJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal1."'");
        $updJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter2], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal2."'");
        if (!mysql_query($updJurnal1)) {
            $updErr .= 'Update Kode Jurnal Error : '.mysql_error()."\n";
        }

        if (!mysql_query($updJurnal2)) {
            $updErr .= 'Update Kode Jurnal Error : '.mysql_error()."\n";
        }

        if ('' !== $updErr) {
            echo $updErr;
            $RBErr = '';
            $RBDet1 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal1."'");
            if (!mysql_query($RBDet1)) {
                $RBErr .= 'Rollback Delete Header 1 Error : '.mysql_error()."\n";
                exit();
            }

            $RBDet2 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal2."'");
            if (!mysql_query($RBDet2)) {
                $RBErr .= 'Rollback Delete Header 2 Error : '.mysql_error()."\n";
                exit();
            }

            $RBJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter1 - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal1."'");
            if (!mysql_query($RBJurnal1)) {
                $RBErr .= 'Rollback Update Jurnal 1 Error : '.mysql_error()."\n";
            }

            $RBJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter2 - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal2."'");
            if (!mysql_query($RBJurnal2)) {
                $RBErr .= 'Rollback Update Jurnal 2 Error : '.mysql_error()."\n";
            }

            if ('' !== $RBErr) {
                echo $RBErr;
            }

            exit();
        }

        $updTransErr = '';
        $updTrans1 = updateQuery($dbname, 'log_transaksiht', ['statusjurnal' => 1], "notransaksi='".$param['notransaksi']."'");
        $updTrans2 = updateQuery($dbname, 'log_transaksiht', ['statusjurnal' => 1], "notransaksi='".$data2[0]['notransaksi']."'");
        if (!mysql_query($updTrans1)) {
            $updTransErr .= 'Update Transaksi 1 Error : '.mysql_error()."\n";
        }

        if (!mysql_query($updTrans2)) {
            $updTransErr .= 'Update Transaksi 2 Error : '.mysql_error()."\n";
        }

        if ('' !== $updTransErr) {
            echo $updTransErr;
            $RBErr = '';
            $RBDet1 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal1."'");
            if (!mysql_query($RBDet1)) {
                $RBErr .= 'Rollback Delete Header 1 Error : '.mysql_error()."\n";
                exit();
            }

            $RBDet2 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal2."'");
            if (!mysql_query($RBDet2)) {
                $RBErr .= 'Rollback Delete Header 2 Error : '.mysql_error()."\n";
                exit();
            }

            $RBJurnal1 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter1 - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal1."'");
            if (!mysql_query($RBJurnal1)) {
                $RBErr .= 'Rollback Update Jurnal 1 Error : '.mysql_error()."\n";
            }

            $RBJurnal2 = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konter2 - 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodejurnal2."'");
            if (!mysql_query($RBJurnal2)) {
                $RBErr .= 'Rollback Update Jurnal 2 Error : '.mysql_error()."\n";
            }

            $RBTrans1 = updateQuery($dbname, 'log_transaksiht', ['statusjurnal' => 0], "notransaksi='".$param['notransaksi']."'");
            $RBTrans2 = updateQuery($dbname, 'log_transaksiht', ['statusjurnal' => 0], "notransaksi='".$data2[0]['notransaksi']."'");
            if (!mysql_query($RBTrans1)) {
                $RBErr .= 'Rollback Update Transaksi 1 Error : '.mysql_error()."\n";
            }

            if (!mysql_query($RBTrans2)) {
                $RBErr .= 'Rollback Update Transaksi 2 Error : '.mysql_error()."\n";
            }

            if ('' !== $RBErr) {
                echo $RBErr;
            }

            exit();
        }

        echo '1';
    } else {
        echo $detailErr;
        $RBErr = '';
        $RBDet1 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal1."'");
        if (!mysql_query($RBDet1)) {
            $RBErr .= 'Rollback Delete Header 1 Error : '.mysql_error()."\n";
            exit();
        }

        $RBDet2 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal2."'");
        if (!mysql_query($RBDet2)) {
            $RBErr .= 'Rollback Delete Header 2 Error : '.mysql_error()."\n";
            exit();
        }

        if ('' !== $RBErr) {
            echo $RBErr;
        }
    }
} else {
    echo $headErr;
    $RBErr = '';
    $RBDet1 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal1."'");
    if (!mysql_query($RBDet1)) {
        $RBErr .= 'Rollback Delete Header 1 Error : '.mysql_error()."\n";
        exit();
    }

    $RBDet2 = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal2."'");
    if (!mysql_query($RBDet2)) {
        $RBErr .= 'Rollback Delete Header 2 Error : '.mysql_error()."\n";
        exit();
    }

    if ('' !== $RBErr) {
        echo $RBErr;
    }

    exit();
}

?>