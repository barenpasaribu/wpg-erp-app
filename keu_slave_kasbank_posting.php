<?php
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo "\r\n";
$param = $_POST;
$kegiatan = 'SELECT * FROM '.$dbname.". setup_parameterappl WHERE kodeaplikasi = 'TX'";
$query = mysql_query($kegiatan);
while ($res = mysql_fetch_assoc($query)) {
    $excludeacc[$res['nilai']] = $res['nilai'];
}

//$queryH = selectQuery($dbname, 'keu_kasbankht', '*', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."' limit 1");
$queryH = selectQuery($dbname, 'keu_kasbankht', '*', "notransaksi='".$param['notransaksi']."' ");//and tipetransaksi='".$param['tipetransaksi']."' limit 1");
#echo $queryH;
echo "<br>";
$dataH = fetchData($queryH);
//$queryD = selectQuery($dbname, 'keu_kasbankdt', '*', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'");
$queryD = selectQuery($dbname, 'keu_kasbankdt', '*', "notransaksi='".$param['notransaksi']."' ");// and tipetransaksi='".$param['tipetransaksi']."'");
#echo $queryD;
$dataD = fetchData($queryD);
$tmpJml = 0; //total jumlah pembayaran atas transaksi penagihan atau tagihan
foreach ($dataD as $row) {
    $tmpJml += $row['jumlah'];
}
if (number_format($tmpJml,2) != number_format($dataH[0]['jumlah'],2)) {
    echo "Warning : Amount on header difference to the amount in detail\nPosting Failed, Header=".$dataH[0]['jumlah']." , Detail=".$tmpJml;
    exit();
}

$error0 = '';
if (1 == $dataH[0]['posting']) {
    $error0 .= $_SESSION['lang']['errisposted'];
}

if ('' != $error0) {
    echo "Data Error :\n".$error0;
    exit();
}
$orgvalue=$_SESSION['empl']['lokasitugas'];
$sqlPara="select * from setup_periodeakuntansi where kodeorg LIKE '".$orgvalue."' AND tutupbuku=0 GROUP BY periode";

$sqlExe=mysql_query($sqlPara);
while($paradata = mysql_fetch_object($sqlExe)){
	$perstart = $paradata->tanggalmulai;
	$perakhir = $paradata->tanggalsampai;
}
$tglpostinput = tanggalsystem($param['tglpost']);
$tglpostinput = substr($param['tglpost'], 6, 4).'-'.substr($param['tglpost'], 3, 2).'-'.substr($param['tglpost'], 0, 2);
if(strtotime($tglpostinput) < strtotime($perstart)){
	echo 'warning: '.$tglpostinput .' tanggal posting diluar periode '. $perstart;
	exit('Error:Date beyond active period');
}elseif(strtotime($tglpostinput) > strtotime($perakhir)){
	echo 'warning: '.$tglpostinput .' tanggal posting diluar periode '.$perakhir;
	exit('Error:Date beyond active period');
}
/* $dataH[0]['tanggal'] = tanggaldgnbar($param['tglpost']);
$tgl = str_replace('-', '', $dataH[0]['tanggal']);
if ($tgl < $_SESSION['org']['period']['start']) {
	echo "warning: tgl=".$param['tglpost']." , start=".$_SESSION['org']['period']['start'];
    exit('Error:Date beyond active period');
} */

$error1 = '';
if (0 == count($dataH)) {
    $error1 .= $_SESSION['lang']['errheadernotexist']."\n";
}

if (0 == count($dataD)) {
    $error1 .= $_SESSION['lang']['errdetailnotexist']."\n";
}

if ('' != $error1) {
    echo "Data Error :\n".$error1;
    exit();
}

$scekakun = 'select * from '.$dbname.".keu_kasbankdt where noakun='' and notransaksi='".$param['notransaksi']."'";
$qcekakun = mysql_query($scekakun);
$rcekakun = mysql_num_rows($qcekakun);
//$rcekakun = 999;
if (0 < $rcekakun) {
    exit('warning: ada data dengan '.$_SESSION['lang']['noakun'].' yang kosong');
}

$data = ['nobayar' => $param['nobayar']];
$scek = 'select * from '.$dbname.".keu_kasbankht where nobayar='".$param['nobayar']."'";
$qcek = mysql_query($scek);
$rcek = mysql_num_rows($qcek);

if ($rcek < 1) {
//    $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
    $where = "notransaksi='".$param['notransaksi']."' ";//and tipetransaksi='".$param['tipetransaksi']."'";
    $query = updateQuery($dbname, 'keu_kasbankht', $data, $where);
    if (!mysql_query($query)) {
        echo 'DB Error Update Transaction : '.mysql_error();
    }
	
	// DZ - 20190209
	if ($param['tipetransaksi']== 'K'){
		$tabname = 'keu_tagihanht'; 
	} else {
		$tabname = 'keu_penagihanht';
	}
	
    $data = ['tanggalposting' => tanggalsystem($param['tglpost'])];
//    $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
    $where = "notransaksi='".$param['notransaksi']."' ";//and tipetransaksi='".$param['tipetransaksi']."'";
    $query = updateQuery($dbname, 'keu_kasbankht', $data, $where);
    if (!mysql_query($query)) {
        echo 'DB Error Update Transaction : '.mysql_error();
    }

    if (1 == $dataH[0]['hutangunit']) {
        $pembayarhutang = $param['kodeorg'];
        $pemilikhutang = $dataH[0]['pemilikhutang'];
        $periodepembayar = makeOption($dbname, 'setup_periodeakuntansi', 'kodeorg,periode', "kodeorg = '".$pembayarhutang."' and tutupbuku = 0");
        $periodepemilik = makeOption($dbname, 'setup_periodeakuntansi', 'kodeorg,periode', "kodeorg = '".$pemilikhutang."' and tutupbuku = 0");
        if ($periodepembayar[$pembayarhutang] != $periodepemilik[$pemilikhutang]) {
            echo 'Warning : '.$_SESSION['lang']['periodeakuntansi']." do not match.\n".$pembayarhutang.' : '.$periodepembayar[$pembayarhutang]."\n".$pemilikhutang.' : '.$periodepemilik[$pemilikhutang];
            exit();
        }

        $noakunhutang = $dataH[0]['noakunhutang'];
        $kodejurnal = 'M';
        $tanggal = $dataH[0]['tanggal'];
        $tanggal = tanggalnormal($tanggal);
        $tanggal = tanggalsystem($tanggal);
        $whereNomilhut = "kodeorganisasi='".$pemilikhutang."'";
        $query = selectQuery($dbname, 'organisasi', 'induk', $whereNomilhut);
        $noKon = fetchData($query);
        $indukpemilikhutang = $noKon[0]['induk'];
        $whereNoyarhut = "kodeorganisasi='".$param['kodeorg']."'";
        $query = selectQuery($dbname, 'organisasi', 'induk', $whereNoyarhut);
        $noKon = fetchData($query);
        $indukpembayarhutang = $noKon[0]['induk'];
        if ($indukpemilikhutang == $indukpembayarhutang) {
            $jenisinduk = 'intra';
        } else {
            $jenisinduk = 'inter';
        }

        $whereNoindukph = "kodekelompok='".$kodejurnal."' and kodeorg='".$indukpemilikhutang."'";
        $query = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', $whereNoindukph);
        $noKon = fetchData($query);
        $tmpC = $noKon[0]['nokounter'];
        ++$tmpC;
        $konteroto = addZero($tmpC, 3);
        $nojuroto = $tanggal.'/'.$pemilikhutang.'/'.$kodejurnal.'/'.$konteroto;
        $whereNocaco = "jenis='".$jenisinduk."' and kodeorg='".$pemilikhutang."'";
        $query = selectQuery($dbname, 'keu_5caco', 'akunpiutang', $whereNocaco);
        $noKon = fetchData($query);
        $noakuncaco = $noKon[0]['akunpiutang'];
        $whereNocacol = "jenis='".$jenisinduk."' and kodeorg='".$pembayarhutang."'";
        $query = selectQuery($dbname, 'keu_5caco', 'akunpiutang', $whereNocacol);
        $noKon = fetchData($query);
        $noakuncacol = $noKon[0]['akunpiutang'];
    }

    $dataRes['header'] = [];
    $dataRes['detail'] = [];
    $dataResoto['header'] = [];
    $dataResoto['detail'] = [];
    $queryJ = selectQuery($dbname, 'keu_5kelompokjurnal', 'nokounter', "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
    $tmpKonter = fetchData($queryJ);
    $konter = addZero($tmpKonter[0]['nokounter'] + 1, 3);
    $nojurnal = str_replace('-', '', $dataH[0]['tanggal']).'/'.$dataH[0]['kodeorg'].'/'.$dataD[0]['kode'].'/'.$konter;
    $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $dataD[0]['kode'], 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
    $dataResoto['header'] = ['nojurnal' => $nojuroto, 'kodejurnal' => $kodejurnal, 'tanggal' => $dataH[0]['tanggal'], 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => '0', 'totalkredit' => '0', 'amountkoreksi' => '0', 'noreferensi' => $pembayarhutang.$dataH[0]['notransaksi'], 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
    $noUrut = 1;
    $totalJumlah = 0;
    foreach ($dataD as $row) {
        if ('M' == substr($row['kode'], 1, 1)) {
            $jumlah = $row['jumlah'] * -1;
        } else {
            $jumlah = $row['jumlah'];
        }

        $dKurs = 1;
        $dMtUang = 'IDR';
        if ('IDR' != $row['matauang']) {
            $dKurs = $row['kurs'];
            $jumlah = $jumlah * $dKurs;
        }

        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $row['noakun'], 'keterangan' => $row['keterangan2'], 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $row['kodeorg'], 'kodekegiatan' => $row['kodekegiatan'], 'kodeasset' => $row['kodeasset'], 'kodebarang' => $row['kodebarang'], 'nik' => $row['nik'], 'kodecustomer' => $row['kodecustomer'], 'kodesupplier' => $row['kodesupplier'], 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => $row['noaruskas'], 'kodevhc' => $row['kodevhc'], 'nodok' => $row['nodok'], 'kodeblok' => $row['orgalokasi'], 'revisi' => '0'];
        $totalJumlah += $jumlah;
        ++$noUrut;
    }
    $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $dataH[0]['noakun'], 'keterangan' => $dataH[0]['keterangan'], 'jumlah' => $totalJumlah * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $dataH[0]['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    $noUrut = 1;
    $totalJumlahOto = 0;
    foreach ($dataD as $row) {
        $ok = true;
        if (!empty($excludeacc)) {
            foreach ($excludeacc as $acc) {
                if (substr($row['noakun'], 0, 3) == $acc) {
                    $ok = false;
                }
            }
        }

        if (0 == $row['hutangunit1']) {
            $ok = false;
        }

        if ($ok) {
            if ('M' == substr($row['kode'], 1, 1)) {
                $jumlah = $row['jumlah'] * -1;
            } else {
                $jumlah = $row['jumlah'];
            }

            $dKurs = 1;
            $dMtUang = 'IDR';
            if ('IDR' != $row['matauang']) {
                $dKurs = $row['kurs'];
                $jumlah = $jumlah * $dKurs;
            }

            $dataResoto['detail'][] = ['nojurnal' => $nojuroto, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $noakunhutang, 'keterangan' => $row['keterangan2'], 'jumlah' => $jumlah, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pemilikhutang, 'kodekegiatan' => $row['kodekegiatan'], 'kodeasset' => $row['kodeasset'], 'kodebarang' => $row['kodebarang'], 'nik' => $row['nik'], 'kodecustomer' => $row['kodecustomer'], 'kodesupplier' => $row['kodesupplier'], 'noreferensi' => $pembayarhutang.$dataH[0]['notransaksi'], 'noaruskas' => $row['noaruskas'], 'kodevhc' => $row['kodevhc'], 'nodok' => $row['nodok'], 'kodeblok' => $row['orgalokasi'], 'revisi' => '0'];
            $totalJumlahOto += $jumlah;
            ++$noUrut;
        }
    }
    $dataResoto['detail'][] = ['nojurnal' => $nojuroto, 'tanggal' => $dataH[0]['tanggal'], 'nourut' => $noUrut, 'noakun' => $noakuncacol, 'keterangan' => $dataH[0]['keterangan'], 'jumlah' => $totalJumlahOto * -1, 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $pemilikhutang, 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => $pembayarhutang.$dataH[0]['notransaksi'], 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
    $dataRes['header']['totaldebet'] = $totalJumlah;
    $dataRes['header']['totalkredit'] = $totalJumlah * -1;
    $dataResoto['header']['totaldebet'] = $totalJumlahOto;
    $dataResoto['header']['totalkredit'] = $totalJumlahOto * -1;
    $errorDB = '';
    $queryH = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
    if (!mysql_query($queryH)) {
        $errorDB .= 'Header :'.mysql_error()."\n";
    }

    if (1 == $dataH[0]['hutangunit']) {
        $queryH = insertQuery($dbname, 'keu_jurnalht', $dataResoto['header']);
        if (!mysql_query($queryH)) {
            $errorDB .= 'Header :'.mysql_error()."\n";
        }
    }

    if ('' == $errorDB) {
        foreach ($dataRes['detail'] as $key => $dataDet) {
            $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
            if (!mysql_query($queryD)) {
                $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
            }
        }
        $queryJ = selectQuery($dbname, 'keu_kasbankht', 'posting', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."'");
        $isJ = fetchData($queryJ);
        if (1 == $isJ[0]['posting']) {
            $errorDB .= 'Data changed by other user';
        } else {
            $queryToJ = updateQuery($dbname, 'keu_kasbankht', ['posting' => 1], "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."'");
            if (!mysql_query($queryToJ)) {
                $errorDB .= 'Posting Flag Error :'.mysql_error()."\n";
            } else {
				// DZ - 20190209
				$xJml = 0; //jumlah pembayaran atas transaksi penagihan atau tagihan
				$xKet1 = '';
				$xdata = '';
				$xwhere = '';
				$xqueryD = selectQuery($dbname, 'keu_kasbankdt', '*', "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."'");
				$xdataD = fetchData($xqueryD);
				foreach ($xdataD as $xrow) {
					$xJml = $xrow['jumlah'];
					$xKet1 = $xrow['keterangan1'];
					
					$xqueryD1 = selectQuery($dbname, $tabname, '*', "noinvoice='".$xKet1."'");
					$xdataD1 = fetchData($xqueryD1);
					foreach ($xdataD1 as $xrow1) {
						$xJml = $xJml + $xrow1['terbayar'];
					}
					$xdata = ['terbayar' => $xJml];
					$queryToJ = updateQuery($dbname, $tabname, $xdata, "noinvoice='".$xKet1."'");
					if (!mysql_query($queryToJ)) {
						$errorDB .= 'Posting Flag Error :'.mysql_error()."\n";
					}
				}
			}
        }
    }

    if (1 == $dataH[0]['hutangunit'] && '' == $errorDB) {
        foreach ($dataResoto['detail'] as $key => $dataDet) {
            $queryD = insertQuery($dbname, 'keu_jurnaldt', $dataDet);
            if (!mysql_query($queryD)) {
                $errorDB .= 'Detail '.$key.' :'.mysql_error()."\n";
            }
        }
    }

    if ('' != $errorDB) {
        $where = "nojurnal='".$nojurnal."'";
        $queryRB = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$where;
        $queryRB2 = updateQuery($dbname, 'keu_kasbankht', ['posting' => 0], "notransaksi='".$dataH[0]['notransaksi']."' and kodeorg='".$dataH[0]['kodeorg']."'");
        if (!mysql_query($queryRB)) {
            $errorDB .= 'Rollback 1 Error :'.mysql_error()."\n";
        }

        if (!mysql_query($queryRB2)) {
            $errorDB .= 'Rollback 2 Error :'.mysql_error()."\n";
        }

        if (1 == $dataH[0]['hutangunit']) {
            $whereoto = "nojurnal='".$nojuroto."'";
            $queryRBoto = 'delete from `'.$dbname.'`.`keu_jurnalht` where '.$whereoto;
            if (!mysql_query($queryRBoto)) {
                $errorDB .= 'Rollback 3 Error :'.mysql_error()."\n";
            }
        }

        echo "DB Error :\n".$errorDB;
        exit();
    }

    $queryJ = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter'] + 1], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
    $errCounter = '';
    if (!mysql_query($queryJ)) {
        $errCounter .= 'Update Counter Parameter Jurnal Error :'.mysql_error()."\n";
    }

    if ('' != $errCounter) {
        $queryJRB = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $tmpKonter[0]['nokounter']], "kodeorg='".$_SESSION['org']['kodeorganisasi']."' and kodekelompok='".$dataD[0]['kode']."'");
        $errCounter = '';
        if (!mysql_query($queryJRB)) {
            $errorJRB .= 'Rollback Parameter Jurnal Error :'.mysql_error()."\n";
        }

        echo "DB Error :\n".$errorJRB;
        exit();
    }

    if (1 == $dataH[0]['hutangunit']) {
        $queryJ = updateQuery($dbname, 'keu_5kelompokjurnal', ['nokounter' => $konteroto], "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
        $errCounter = '';
        if (!mysql_query($queryJ)) {
            $errCounter .= 'Update Counter Parameter Jurnal Error :'.mysql_error()."\n";
        }

        if ('' != $errCounter) {
            $queryJRB = updateQuery($dbname, 'keu_5kelompokjurnal', [$noKon[0]['nokounter']], "kodeorg='".$indukpemilikhutang."' and kodekelompok='".$kodejurnal."'");
            $errCounter = '';
            if (!mysql_query($queryJRB)) {
                $errorJRB .= 'Rollback Parameter Jurnal Error :'.mysql_error()."\n";
            }

            echo "DB Error :\n".$errorJRB;
            exit();
        }
    }
}else{
    exit('warning: '.$_SESSION['lang']['nobayar'].' sudah tersimpan di notransaksi yang lain');
}

?>