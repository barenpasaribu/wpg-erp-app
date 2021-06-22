<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$param = $_POST;
$queryH = selectQuery($dbname, 'kebun_aktifitas', '*', "notransaksi='".$param['notransaksi']."'");
$dataH = fetchData($queryH);
$tgl = str_replace('-', '', $dataH[0]['tanggal']);
if ($tgl < $_SESSION['org']['period']['start']) {
    exit('Error:Tanggal diluar periode aktif');
}

$queryD = selectQuery($dbname, 'kebun_prestasi', '*', "notransaksi='".$param['notransaksi']."'");
$dataD = fetchData($queryD);
$queryAbs = selectQuery($dbname, 'kebun_kehadiran', 'nik,jhk,umr,insentif', "notransaksi='".$param['notransaksi']."'");
$dataAbs = fetchData($queryAbs);
$error0 = '';
if (1 === $dataH[0]['jurnal']) {
    $error0 .= $_SESSION['lang']['errisposted'];
}

if ('' !== $error0) {
    echo "Data Error :\n".$error0;
    exit();
}

$error1 = '';
if (0 === count($dataH)) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}

if (0 === count($dataD)) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}

if ('' !== $error1) {
    echo "Data Error :\n".$error1;
    exit();
}

$costRawat = 0;
$costRawatDetail = [];
$totalHk = 0;
$nikKary = '';
if (!empty($dataAbs)) {
    foreach ($dataAbs as $row) {
        if ('' !== $nikKary) {
            $nikKary .= ',';
        }

        $nikKary .= $row['nik'];
    }
    $karyList = makeOption($dbname, 'datakaryawan', 'karyawanid,lokasitugas', 'karyawanid in ('.$nikKary.')');
    foreach ($dataAbs as $row) {
        $costRawat += $row['jhk'] * $row['umr'] + $row['insentif'];
        $totalHk += $row['jhk'];
        if (isset($costRawatDetail[$karyList[$row['nik']]])) {
            $costRawatDetail[$karyList[$row['nik']]] += $row['jhk'] * $row['umr'] + $row['insentif'];
        } else {
            $costRawatDetail[$karyList[$row['nik']]] = $row['jhk'] * $row['umr'] + $row['insentif'];
        }
    }
}

$totalHk = round($totalHk, 2);
$dataD[0]['jumlahhk'] = round($dataD[0]['jumlahhk'], 2);
$qwe = $totalHk - $dataD[0]['jumlahhk'];
if (empty($dataAbs) || $totalHk !== $dataD[0]['jumlahhk']) {
    echo 'Warning : HK Prestasi belum teralokasi dengan lengkap '.$qwe.'.';
    exit();
}

$kodeJurnal = 'M0';
$queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
$resParam = fetchData($queryParam);
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
$tmpKonter = fetchData($queryJ);
$konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
$tmpNoJurnal = explode('/', $param['notransaksi']);
$nojurnal = $tmpNoJurnal[0].'/'.$tmpNoJurnal[1].'/'.$kodeJurnal.'/'.$konter;
$strt = "SELECT a.kodegudang\r\nFROM ".$dbname.".`kebun_pakaimaterial` a\r\nWHERE a.notransaksi='".$dataH[0]['notransaksi']."'";
$res = mysql_query($strt);
$tanggalzx = explode('-', $dataH[0]['tanggal']);
$tanggalqq = $tanggalzx[0].$tanggalzx[1].$tanggalzx[2];
$kodenyagudang = '';
while ($prm = mysql_fetch_object($res)) {
    $kodenyagudang = $prm->kodegudang;
}
if ('' !== $kodenyagudang && ($tanggalqq < $_SESSION['gudang'][$kodenyagudang]['start'] || $_SESSION['gudang'][$kodenyagudang]['end'] < $tanggalqq)) {
    exit('error: periode aktif gudang tidak sama dengan kebun.');
}

$dataRes['header'] = [];
$dataRes['detail'] = [];
$dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => 'M0', 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
$i = 0;
$whereKeg = '';
foreach ($dataD as $row) {
    if (0 === $i) {
        $whereKeg .= "kodekegiatan='".$row['kodekegiatan']."'";
    } else {
        $whereKeg .= " or kodekegiatan='".$row['kodekegiatan']."'";
    }

    ++$i;
}
$queryKeg = selectQuery($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,noakun', $whereKeg);
$tmpRes = fetchData($queryKeg);
$resKeg = [];
foreach ($tmpRes as $row) {
    $resKeg[$row['kodekegiatan']]['nama'] = $row['namakegiatan'];
    $resKeg[$row['kodekegiatan']]['akun'] = $row['noakun'];
}
$noUrut = 1;
$totalJumlah = 0;
$kodeblok = '';
$kodekegiatan = '';
foreach ($dataD as $row) {
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $resKeg[$row['kodekegiatan']]['akun'], 'keterangan' => 'Pemeliharaan '.$resKeg[$row['kodekegiatan']]['nama'], 'jumlah' => $costRawat, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($row['kodeorg'], 0, 4), 'kodekegiatan' => $row['kodekegiatan'], 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $row['kodeorg'], 'revisi' => '0'];
    $totalJumlah += $costRawat;
    ++$noUrut;
    $kodeblok = $row['kodeorg'];
    $kodekegiatan = $row['kodekegiatan'];
}
$kebunList = '';
foreach ($costRawatDetail as $kebun => $cost) {
    if ('' !== $kebunList) {
        $kebunList .= ',';
    }

    $kebunList .= "'".$kebun."'";
}
$whereAkun = "jenis='intra' and kodeorg in (".$kebunList.')';
$queryAkun = selectQuery($dbname, 'keu_5caco', 'kodeorg,akunpiutang,akunhutang', $whereAkun);
$akunIntraco = fetchData($queryAkun);
$optAkunIntra = [];
foreach ($akunIntraco as $row) {
    $optAkunIntra[$row['kodeorg']] = ['piutang' => $row['akunpiutang'], 'hutang' => $row['akunhutang']];
}
foreach ($costRawatDetail as $kebun => $cost) {
    if ($_SESSION['empl']['lokasitugas'] === $kebun) {
        $noAkun = $resParam[0]['noakunkredit'];
        $keterangan = 'Pemeliharaan '.$dataH[0]['tipetransaksi'];
    } else {
        if (!isset($optAkunIntra[$kebun])) {
            echo 'Gagal, Intraco Account for division '.$kebun.' not exist. Please set intraco account for division '.$kebun;
            exit();
        }

        $noAkun = $optAkunIntra[$kebun]['hutang'];
        $keterangan = 'Pemeliharaan '.$dataH[0]['tipetransaksi'].' oleh '.$kebun;
    }

    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $noAkun, 'keterangan' => $keterangan, 'jumlah' => $cost * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => '', 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    ++$noUrut;
}
$dataRes['header']['totaldebet'] = $totalJumlah;
$dataRes['header']['totalkredit'] = $totalJumlah;
$errorDB = '';
$queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
if (!mysql_query($queryH)) {
    $errorDB .= 'Header :'.mysql_error()."\n".$queryH;
}

if ('' === $errorDB) {
    foreach ($dataRes['detail'] as $key => $dataDet) {
        $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
        if (!mysql_query($queryD)) {
            $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
        }
    }
    $queryToJ = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 1], "notransaksi='".$dataH[0]['notransaksi']."'");
    if (!mysql_query($queryToJ)) {
        $errorDB .= 'Posting Mark Error :'.mysql_error()."\n";
    }

    $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if (!mysql_query($queryKonter)) {
        $errorDB .= 'Update Counter Error :'.mysql_error()."\n";
    }
}

if ('' !== $errorDB) {
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
    $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if (!mysql_query($queryRB)) {
        $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
    }

    if (!mysql_query($queryRB2)) {
        $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
    }

    if (!mysql_query($queryRBKonter)) {
        $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
    }

    echo "DB Error :\n".$errorDB;
    exit();
}

jurnalIntraco($dbname, $param, $costRawatDetail, $optAkunIntra, $kodekegiatan, $resKeg[$kodekegiatan]['akun'], $resKeg[$kodekegiatan]['nama'], $dataRes);
$str = 'select * from '.$dbname.".log_transaksiht where notransaksireferensi='".$param['notransaksi']."'";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    exit(' Error: Posting ulang kegiatan berhasil, namun untuk material pada kegiatan tsb tidak dapat di posting ulang');
}

$nomor = [];
$str = 'select distinct kodegudang from '.$dbname.".kebun_pakaimaterial where notransaksi='".$param['notransaksi']."' and kodegudang!=''";
$resc = mysql_query($str);
while ($bar1 = mysql_fetch_object($resc)) {
    $gudang = $bar1->kodegudang;
    $num = 1;
    $str = 'select max(notransaksi) as notransaksi from '.$dbname.".log_transaksiht where tipetransaksi=5 and kodegudang='".$gudang."' and\r\n            tanggal>=".$_SESSION['gudang'][$gudang]['start'].' and tanggal<='.$_SESSION['gudang'][$gudang]['end']." and notransaksireferensi!=''\r\n                  order by notransaksi desc limit 1";
    if ($res = mysql_query($str)) {
        while ($bar = mysql_fetch_object($res)) {
            $num = $bar->notransaksi;
            if ('' !== $num) {
                $num = substr($num, 7, 4);
                $num = 1 + (int) $num;
                $num = str_pad($num, 4, '0', STR_PAD_LEFT);
            } else {
                $num = '0001';
            }
        }
    }

    $nomor[$bar1->kodegudang] = date('Ym').'M'.$num;
    $strd = 'select periode from '.$dbname.".setup_periodeakuntansi where kodeorg='".$bar1->kodegudang."' and tutupbuku=0";
    $resd = mysql_query($strd);
    while ($bard = mysql_fetch_object($resd)) {
        $periode[$bar1->kodegudang] = $bard->periode;
    }
}
$brg = [];
$gud = [];
$str = 'select a.*,b.namabarang,b.satuan from '.$dbname.".kebun_pakaimaterial a\r\n          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
$resa = mysql_query($str);
while ($barf = mysql_fetch_object($resa)) {
    $stru = 'select saldoakhirqty,hargarata,nilaisaldoakhir,qtykeluar,qtykeluarxharga from '.$dbname.".log_5saldobulanan where kodegudang='".$barf->kodegudang."'\r\n                and kodebarang='".$barf->kodebarang."' and periode='".$periode[$barf->kodegudang]."'";
    $resu = mysql_query($stru);
    $saldo[$barf->kodegudang][$barf->kodebarang] = 0;
    $harga[$barf->kodegudang][$barf->kodebarang] = 0;
    while ($baru = mysql_fetch_object($resu)) {
        $saldo[$barf->kodegudang][$barf->kodebarang] = $baru->saldoakhirqty;
        $harga[$barf->kodegudang][$barf->kodebarang] = $baru->hargarata;
        $xkeluar[$barf->kodegudang][$barf->kodebarang] = $baru->qtykeluarxharga;
        $qtykeluar[$barf->kodegudang][$barf->kodebarang] = $baru->qtykeluar;
        $nilaisaldoakhir[$barf->kodegudang][$barf->kodebarang] = $baru->nilaisaldoakhir;
    }
    $brg[] = $barf->kodebarang;
    $gud[] = $barf->kodegudang;
}
$akunbarang = [];
$stk = 'select kode,noakun from '.$dbname.".log_5klbarang where noakun!=''";
$rek = mysql_query($stk);
while ($bak = mysql_fetch_object($rek)) {
    $akunbarang[$bak->kode] = $bak->noakun;
}
$kodeJurnal1 = 'INVK1';
$queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal1."'");
$tmpKonter1 = fetchData($queryJ);
if (0 === count($tmpKonter1)) {
    $where = "nojurnal='".$nojurnal."'";
    $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
    $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
    $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
    if (!mysql_query($queryRB)) {
        $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
    }

    if (!mysql_query($queryRB2)) {
        $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
    }

    if (!mysql_query($queryRBKonter)) {
        $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
    }

    exit('Error: Kelompok jurnal untuk '.$kodeJurnal1.' belum ada dan '.$errorDB);
}

$konter1 = addZero($tmpKonter1[0]['nokounter'] + 1, 3);
$tmpNoJurnal = explode('/', $param['notransaksi']);
$nojurnal1 = $tmpNoJurnal[0].'/'.$tmpNoJurnal[1].'/'.$kodeJurnal1.'/'.$konter1;
$dataResMat['header'] = [];
$dataResMat['detail'] = [];
$dataResMat['header'] = ['nojurnal' => $nojurnal1, 'kodejurnal' => 'M0', 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '1', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
$str = 'select a.*,b.namabarang,b.satuan from '.$dbname.".kebun_pakaimaterial a\r\n          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
$resx = mysql_query($str);
$noUrut = 1;
$totalJumlah = 0;
$errAkunBarang = '';
$namabarang = '';
while ($bab = mysql_fetch_object($resx)) {
    $namabarang = substr($bab->namabarang, 0, 25).' '.$bab->kwantitas.' '.$bab->satuan;
    if ('' === $harga[$bab->kodegudang][$bab->kodebarang] || 0 === $harga[$bab->kodegudang][$bab->kodebarang]) {
        $errAkunBarang .= ' Error: Belum ada harga rata-rata barang '.$bab->kodebarang;

        break;
    }

    if (isset($akunbarang[substr($bab->kodebarang, 0, 3)]) && '' !== $akunbarang[substr($bab->kodebarang, 0, 3)]) {
        $dataResMat['detail'][] = ['nojurnal' => $nojurnal1, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $akunbarang[substr($bab->kodebarang, 0, 3)], 'keterangan' => 'Material BKM '.$dataH[0]['notransaksi'].' '.$namabarang, 'jumlah' => $harga[$bab->kodegudang][$bab->kodebarang] * $bab->kwantitas * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => '', 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => $bab->kodebarang, 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $totalJumlah += $harga[$bab->kodegudang][$bab->kodebarang] * $bab->kwantitas;
    } else {
        $errAkunBarang .= ' Error: Belum ada akun untuk barang '.$bab->kodebarang;

        break;
    }
}
$dataResMat['detail'][] = ['nojurnal' => $nojurnal1, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $resKeg[$kodekegiatan]['akun'], 'keterangan' => 'Material BKM '.$dataH[0]['notransaksi'], 'jumlah' => $totalJumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => substr($kodeblok, 0, 4), 'kodekegiatan' => $kodekegiatan, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => $kodeblok, 'revisi' => '0'];
if ('' !== $namabarang) {
    $dataResMat['header']['totaldebet'] = $totalJumlah;
    $dataResMat['header']['totalkredit'] = $totalJumlah;
    if ('' !== $errAkunBarang) {
        echo $errAkunBarang;
        $where = "nojurnal='".$nojurnal."'";
        $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
        $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
        $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if (!mysql_query($queryRB)) {
            $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
        }

        if (!mysql_query($queryRB2)) {
            $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
        }

        if (!mysql_query($queryRBKonter)) {
            $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
        }

        echo "DB Error :\n".$errorDB;
        exit();
    }

    $errorDBX = '';
    $queryH = insertQuery($dbname, 'keu_jurnalht', $dataResMat['header']);
    if (!mysql_query($queryH)) {
        $errorDBX .= ' Error Header jurnal material:'.mysql_error()."\n".$queryH;
    }

    if ('' === $errorDBX) {
        foreach ($dataResMat['detail'] as $key => $dataDet) {
            $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
            if (!mysql_query($queryD)) {
                $errorDBX .= 'Error Detail jurnal material '.$key.' :'.mysql_error()."\n";
                $where = "nojurnal='".$nojurnal1."'";
                $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                if (!mysql_query($queryRB)) {
                    $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
                }

                echo $errorDBX;
                $where = "nojurnal='".$nojurnal."'";
                $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
                $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                if (!mysql_query($queryRB)) {
                    $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
                }

                if (!mysql_query($queryRB2)) {
                    $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
                }

                if (!mysql_query($queryRBKonter)) {
                    $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
                }

                echo "DB Error :\n".$errorDB;
                exit();
            }

            $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter1[0]['nokounter'] + 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal1."'");
            if (!mysql_query($queryKonter)) {
                $errorDB .= 'Update Counter jurnal material Error :'.mysql_error()."\n";
            }
        }
        $awlheader = 1;
        $str = 'select a.*,b.namabarang,b.satuan from '.$dbname.".kebun_pakaimaterial a\r\n          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
        $resp = mysql_query($str);
        $dataMat['header'] = [];
        $dataMat['detail'] = [];
        while ($bar = mysql_fetch_object($resp)) {
            if (1 === $awlheader) {
                $num = $nomor[$bar->kodegudang].'-GI-'.$bar->kodegudang;
                $dataMat['header'][] = ['tipetransaksi' => '5', 'notransaksi' => $num, 'nomirs' => '', 'tanggal' => $dataH[0]['tanggal'], 'kodept' => $_SESSION['empl']['kodeorganisasi'], 'untukpt' => $_SESSION['empl']['kodeorganisasi'], 'nopo' => '', 'nosj' => '', 'keterangan' => 'Material BKM ', 'statusjurnal' => '1', 'kodegudang' => $bar->kodegudang, 'user' => $_SESSION['standard']['userid'], 'namapenerima' => '0', 'mengetahui' => $_SESSION['standard']['userid'], 'idsupplier' => '', 'nofaktur' => '', 'post' => '1', 'postedby' => $_SESSION['standard']['userid'], 'untukunit' => $_SESSION['empl']['lokasitugas'], 'notransaksireferensi' => $param['notransaksi'], 'gudangx' => '', 'lastupdate' => date('Y-m-d H:i:s')];
            }

            $dataMat['detail'][] = ['notransaksi' => $num, 'kodebarang' => $bar->kodebarang, 'satuan' => $bar->satuan, 'jumlah' => $bar->kwantitas, 'jumlahlalu' => $saldo[$bar->kodegudang][$bar->kodebarang], 'hargasatuan' => '0', 'kodeblok' => $kodeblok, 'waktutransaksi' => date('Y-m-d H:i:s'), 'updateby' => $_SESSION['standard']['userid'], 'kodekegiatan' => $kodekegiatan, 'kodemesin' => '', 'statussaldo' => 1, 'hargarata' => $harga[$bar->kodegudang][$bar->kodebarang], 'nomirs' => '', 'nopo' => ''];
            ++$awlheader;
        }
        $str = 'select a.*,b.namabarang,b.satuan from '.$dbname.".kebun_pakaimaterial a\r\n          left join ".$dbname.".log_5masterbarang b on a.kodebarang=b.kodebarang\r\n          where a.notransaksi='".$param['notransaksi']."' and a.kodegudang!=''";
        $resku = mysql_query($str);
        $errsaldo = '';
        while ($barf = mysql_fetch_object($resku)) {
            if ($barf->kwantitas <= $saldo[$barf->kodegudang][$barf->kodebarang]) {
                if (0 < $harga[$barf->kodegudang][$barf->kodebarang]) {
                    $jumlah[$barf->kodegudang][$barf->kodebarang] = $barf->kwantitas;
                } else {
                    $errsaldo = ' Error: Tidak cukup saldo untuk barang '.$barf->kodebarang.' pada gudang '.$barf->kodegudang.' pada periode '.$periode[$barf->kodegudang];

                    break;
                }
            } else {
                $errsaldo = ' Error: Tidak cukup saldo untuk barang '.$barf->kodebarang.' pada gudang '.$barf->kodegudang.' pada periode '.$periode[$barf->kodegudang];

                break;
            }
        }
        if ('' !== $errsaldo) {
            $where = "nojurnal='".$nojurnal1."'";
            $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
            if (!mysql_query($queryRB)) {
                $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
            }

            echo $errsaldo;
            $where = "nojurnal='".$nojurnal."'";
            $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
            $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
            $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if (!mysql_query($queryRB)) {
                $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
            }

            if (!mysql_query($queryRB2)) {
                $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
            }

            if (!mysql_query($queryRBKonter)) {
                $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
            }

            echo "DB Error :\n".$errorDB;
            exit();
        }

        $errorY = '';
        $errorX = '';
        foreach ($dataMat['header'] as $key => $dataX) {
            $queryD = insertQuery($dbname, 'log_transaksiht', $dataX);
            if (!mysql_query($queryD)) {
                $errorX = ' Error insert header material :'.$queryD.':'.mysql_error()."\n";
            }
        }
        if ('' === $errorX) {
            foreach ($dataMat['detail'] as $key => $dataY) {
                $queryD = insertQuery($dbname, 'log_transaksidt', $dataY);
                if (!mysql_query($queryD)) {
                    $errorY = ' Error insert detail material :'.$queryD.':'.mysql_error()."\n";
                }
            }
            if ('' === $errorY) {
                $errSal = '';
                foreach ($gud as $keygud => $valgud) {
                    foreach ($brg as $keybrg => $valbrg) {
                        $sth = 'update '.$dbname.'.log_5saldobulanan set saldoakhirqty='.($saldo[$valgud][$valbrg] - $jumlah[$valgud][$valbrg]).",\r\n                                nilaisaldoakhir=".($nilaisaldoakhir[$valgud][$valbrg] - $jumlah[$valgud][$valbrg] * $harga[$valgud][$valbrg]).",\r\n                                qtykeluar=".($qtykeluar[$valgud][$valbrg] + $jumlah[$valgud][$valbrg]).",\r\n                                qtykeluarxharga=".($qtykeluar[$valgud][$valbrg] + $jumlah[$valgud][$valbrg]) * $harga[$valgud][$valbrg]."\r\n                                where periode='".$periode[$valgud]."' and kodegudang='".$valgud."' and kodebarang='".$valbrg."'";
                        if (!mysql_query($sth)) {
                            $errSal .= ' Error update saldo bulanan'.addslashes(mysql_error($conn)).$sth;
                        }

                        $stup = 'update '.$dbname.'.kebun_pakaimaterial set hargasatuan='.$harga[$valgud][$valbrg]." where kodegudang='".$valgud."'\r\n                                   and kodebarang='".$valbrg."' and notransaksi='".$param['notransaksi']."'";
                        mysql_query($stup);
                    }
                }
                if ('' === $errSal) {
                    foreach ($gud as $keygud => $valgud) {
                        foreach ($brg as $keybrg => $valbrg) {
                            $strg = 'update '.$dbname.'.log_5masterbarangdt set saldoqty='.($saldo[$valgud][$valbrg] - $jumlah[$valgud][$valbrg]).",\r\n                                hargalastout=".$harga[$valgud][$valbrg]." where kodegudang='".$valgud."' and kodebarang='".$valbrg."'";
                            mysql_query($strg);
                        }
                    }
                } else {
                    foreach ($gud as $keygud => $valgud) {
                        foreach ($brg as $keybrg => $valbrg) {
                            $sth = 'update '.$dbname.'.log_5saldobulanan set saldoakhirqty='.$saldo[$valgud][$valbrg].",\r\n                                nilaisaldoakhir=".$nilaisaldoakhir[$valgud][$valbrg].",\r\n                                qtykeluar=".$qtykeluar[$gb][$valbrg].',qtykeluarxharga='.$xkeluar[$valgud][$valbrg]."\r\n                                where periode='".$periode[$valgud]."' and kodegudang='".$valgud."' and kodebarang='".$valbrg."'";
                            mysql_query($sth);
                        }
                    }
                    $where = "nojurnal='".$nojurnal1."'";
                    $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                    if (!mysql_query($queryRB)) {
                        $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
                    }

                    foreach ($dataMat['header'] as $key => $dataX) {
                        $queryD = ' delete from '.$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                        mysql_query($queryD);
                    }
                    echo $errSal;
                    $where = "nojurnal='".$nojurnal."'";
                    $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                    $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
                    $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                    if (!mysql_query($queryRB)) {
                        $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
                    }

                    if (!mysql_query($queryRB2)) {
                        $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
                    }

                    if (!mysql_query($queryRBKonter)) {
                        $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
                    }

                    echo "DB Error :\n".$errorDB;
                    exit();
                }
            } else {
                $where = "nojurnal='".$nojurnal1."'";
                $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                if (!mysql_query($queryRB)) {
                    $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
                }

                foreach ($dataMat['header'] as $key => $dataX) {
                    $queryD = ' delete from '.$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                    mysql_query($queryD);
                }
                echo $errorY;
                $where = "nojurnal='".$nojurnal."'";
                $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
                $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
                $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
                if (!mysql_query($queryRB)) {
                    $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
                }

                if (!mysql_query($queryRB2)) {
                    $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
                }

                if (!mysql_query($queryRBKonter)) {
                    $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
                }

                echo "DB Error :\n".$errorDB;
                exit();
            }
        } else {
            $where = "nojurnal='".$nojurnal1."'";
            $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
            if (!mysql_query($queryRB)) {
                $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
            }

            foreach ($dataMat['header'] as $key => $dataX) {
                $queryD = ' delete from '.$dbname.".log_transaksiht where notransaksi='".$dataX['notransaksi']."'";
                mysql_query($queryD);
            }
            echo $errorX;
            $where = "nojurnal='".$nojurnal."'";
            $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
            $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
            $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if (!mysql_query($queryRB)) {
                $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
            }

            if (!mysql_query($queryRB2)) {
                $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
            }

            if (!mysql_query($queryRBKonter)) {
                $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
            }

            echo "DB Error :\n".$errorDB;
            exit();
        }
    } else {
        $where = "nojurnal='".$nojurnal1."'";
        $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
        if (!mysql_query($queryRB)) {
            $errorDB .= 'Rollback jurnal material Error :'.mysql_error()."\n";
        }

        echo $errorDBX;
        $where = "nojurnal='".$nojurnal."'";
        $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
        $queryRB2 = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 0], "notransaksi='".$dataH[0]['notransaksi']."'");
        $queryRBKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
        if (!mysql_query($queryRB)) {
            $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n".$queryRB;
        }

        if (!mysql_query($queryRB2)) {
            $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n".$queryRB2;
        }

        if (!mysql_query($queryRBKonter)) {
            $errorDB .= 'Rollback Counter Error :'.mysql_error()."\n".$queryRBKonter;
        }

        echo "DB Error :\n".$errorDB;
        exit();
    }
}

function jurnalIntraco($dbname, $param, $costRawatDetail, $optAkunIntra, $kodeKeg, $akunKeg, $nameKeg, $dataRes)
{
    $dataIntraco = [];
    $i = 0;
    foreach ($costRawatDetail as $kebun => $cost) {
        if ($kebun !== $_SESSION['empl']['lokasitugas']) {
            $dataIntraco[$kebun]['header'] = $dataRes['header'];
            $kodeJurnal = 'M0';
            $queryParam = selectQuery($dbname, 'keu_5parameterjurnal', 'noakunkredit', "kodeaplikasi='KBN' and jurnalid='".$kodeJurnal."'");
            $resParam = fetchData($queryParam);
            $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            $tmpKonter = fetchData($queryJ);
            $konter = addZero($tmpKonter[0]['nokounter'] + 1 + $i, 3);
            $tmpNoJurnal = explode('/', $param['notransaksi']);
            $nojurnal = $tmpNoJurnal[0].'/'.$kebun.'/'.$kodeJurnal.'/'.$konter;
            $dataIntraco[$kebun]['header']['nojurnal'] = $nojurnal;
            $dataIntraco[$kebun]['header']['totaldebet'] = $cost;
            $dataIntraco[$kebun]['header']['totalkredit'] = $cost;
            $dataIntraco[$kebun]['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataIntraco[$kebun]['header']['tanggal'], 'nourut' => 1, 'noakun' => $optAkunIntra[$kebun]['piutang'], 'keterangan' => 'Pemeliharaan '.$nameKeg.' '.$_SESSION['empl']['lokasitugas'], 'jumlah' => $cost, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kebun, 'kodekegiatan' => $kodeKeg, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            $dataIntraco[$kebun]['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataIntraco[$kebun]['header']['tanggal'], 'nourut' => 2, 'noakun' => $akunKeg, 'keterangan' => 'Pemeliharaan '.$nameKeg.' '.$_SESSION['empl']['lokasitugas'], 'jumlah' => $cost * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $kebun, 'kodekegiatan' => $kodeKeg, 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
            ++$i;
        }
    }
    $errorDB = '';
    foreach ($dataIntraco as $dataRes) {
        $queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
        if (!mysql_query($queryH)) {
            $errorDB .= 'Header :'.mysql_error()."\n".$queryH;
        }

        if ('' === $errorDB) {
            foreach ($dataRes['detail'] as $key => $dataDet) {
                $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
                if (!mysql_query($queryD)) {
                    $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
                }
            }
            $queryToJ = updateQuery($dbname, 'kebun_aktifitas', ['jurnal' => 1], "notransaksi='".$param['notransaksi']."'");
            if (!mysql_query($queryToJ)) {
                $errorDB .= 'Posting Mark Error :'.mysql_error()."\n";
            }

            $queryKonter = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + $i], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$kodeJurnal."'");
            if (!mysql_query($queryKonter)) {
                $errorDB .= 'Update Counter Error :'.mysql_error()."\n";
            }
        }
    }
    echo $errorDB;
}

?>