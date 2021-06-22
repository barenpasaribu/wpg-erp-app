<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tmpPeriod = explode('-', $param['periode']);
$tahunbulan = implode('', $tmpPeriod);
$proses = $_GET['proses'];
$stl = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where jurnalid='CLM'";
$rel = mysql_query($stl);
$akunCLM = '';
while ($bal = mysql_fetch_object($rel)) {
    $akunCLM = $bal->noakundebet;
}
$stl = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where jurnalid='CLY'";
$rel = mysql_query($stl);
$akunCLY = '';
while ($bal = mysql_fetch_object($rel)) {
    $akunCLY = $bal->noakundebet;
}
$stl = 'select noakundebet from '.$dbname.".keu_5parameterjurnal where jurnalid='RAT'";
$rel = mysql_query($stl);
$akunRAT = '';
while ($bal = mysql_fetch_object($rel)) {
    $akunRAT = $bal->noakundebet;
}
if ('' === $akunCLM || '' === $akunCLY || '' === $akunRAT) {
    exit(' Error: data akun laba tahunan, akun laba ditahan dan batas akun laba/rugi belum terdaftar pada parameter jurnal');
}

$str = 'select tanggalmulai,tanggalsampai from '.$dbname.".setup_periodeakuntansi where \r\n      periode='".$param['periode']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
$currstart = '';
$currend = '';
while ($bar = mysql_fetch_object($res)) {
    $currstart = $bar->tanggalmulai;
    $currend = $bar->tanggalsampai;
}
if ('' === $currstart || '' === $currend) {
    exit('Error: Accounting period is not normal on '.$_SESSION['empl']['lokasitugas']);
}

$str = 'select notransaksi,tanggal,jumlah from '.$dbname.".keu_kasbankht where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "There are Cash/Bank transaction that has not been posted:\n";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo $no.'. No '.$bar->notransaksi.':'.tanggalnormal($bar->tanggal).'->Rp. '.number_format($bar->jumlah, 0)."\n";
    }
    exit('Error');
}

$str = 'select notransaksi,tanggal,jumlahrealisasi from '.$dbname.".log_baspk where kodeblok like '".$_SESSION['empl']['lokasitugas']."%'\r\n          and tanggal between '".$currstart."' and '".$currend."' and statusjurnal=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "There are Contract Realization transaction that has not been posted:\n";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo $no.'. No '.$bar->notransaksi.':'.tanggalnormal($bar->tanggal).'->Rp. '.number_format($bar->jumlahrealisasi, 0)."\n";
    }
    exit('Error');
}

$str = 'select nojurnal,tanggal,debet,kredit from '.$dbname.".keu_jurnal_tidak_balance_vw where kodeorg = '".$_SESSION['empl']['lokasitugas']."'\r\n          and tanggal between '".$currstart."' and '".$currend."'\r\n          and nojurnal not like '%/CLSM/%'";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo "There is still yet balanced Journal:\n";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo $no.'. No '.$bar->nojurnal.':'.tanggalnormal($bar->tanggal).'->(D)Rp. '.number_format($bar->debet, 0).':(K)Rp. '.number_format($bar->kredit, 0)."\n";
    }
    exit('Error');
}

$str = 'select notransaksi,tanggal, kodegudang from '.$dbname.".log_transaksiht where post=0 and kodegudang like '".$_SESSION['empl']['lokasitugas']."%'\r\n            and tanggal between '".$currstart."' and '".$currend."'";
$res = mysql_query($str);
$stm = '';
if (0 < mysql_numrows($res)) {
    while ($bar = mysql_fetch_object($res)) {
        $stm .= 'Gudang:'.$bar->kodegudang.'->No.>'.$bat->notransaksi.'->'.$bar->tanggal.'<br>';
    }
    echo "Error: Warehouse transaction that has not been posted\r<br>".$stm;
}

switch ($proses) {
    case 'tutupBuku':
        if (12 === $tmpPeriod[1]) {
            $bulanLanjut = 1;
            $tahunLanjut = $tmpPeriod[0] + 1;
        } else {
            $bulanLanjut = $tmpPeriod[1] + 1;
            $tahunLanjut = $tmpPeriod[0];
        }

        $jmlHari = cal_days_in_month(CAL_GREGORIAN, $bulanLanjut, $tahunLanjut);
        $tglAwal = $tahunLanjut.'-'.addZero($bulanLanjut, 2).'-01';
        $tglAkhir = $tahunLanjut.'-'.addZero($bulanLanjut, 2).'-'.addZero($jmlHari, 2);
        $pt = getPT($dbname, $param['kodeorg']);
        if (false === $pt) {
            $pt = getHolding($dbname, $param['kodeorg']);
        }

        $tgl = $tmpPeriod[0].$tmpPeriod[1].cal_days_in_month(CAL_GREGORIAN, $tmpPeriod[1], $tmpPeriod[0]);
        $kodejurnal = 'CLSM';
        $nojurnal = $tgl.'/'.$param['kodeorg'].'/'.$kodejurnal.'/999';
        $str = 'delete from '.$dbname.".keu_jurnalht where nojurnal='".$nojurnal."'";
        mysql_query($str);
        $query = 'select count(*) as x from '.$dbname.".keu_jurnaldt_vw where \r\n                   tanggal between '".$currstart."' and '".$currend."' and substr(nojurnal,10,4)='".$param['kodeorg']."'";
        $res = mysql_query($query);
        if (0 === mysql_num_rows($res)) {
            echo 'Warning : No data found for this unit';
            exit();
        }

        $query = selectQuery($dbname, 'keu_jurnaldt_vw', 'substr(nojurnal,10,4) as kodeorg,sum(jumlah) as jumlah', "substr(nojurnal,10,4)='".$param['kodeorg']."' and tanggal between '".$currstart."' and '".$currend."'\r\n             and noakun>='".$akunRAT."'").'group by substr(nojurnal,10,4)';
        $data = fetchData($query);
        $noakun = $akunCLM;
        if (0 < $data[0]['jumlah']) {
            $debetH = $data[0]['jumlah'];
            $kreditH = 0;
        } else {
            $debetH = 0;
            $kreditH = $data[0]['jumlah'];
        }

        $dataRes['header'] = ['nojurnal' => $nojurnal, 'kodejurnal' => $kodejurnal, 'tanggal' => $tgl, 'tanggalentry' => date('Ymd'), 'posting' => '0', 'totaldebet' => $debetH, 'totalkredit' => $kreditH, 'amountkoreksi' => '0', 'noreferensi' => 'TUTUP/'.$param['kodeorg'].'/'.$tahunbulan, 'autojurnal' => '1', 'matauang' => 'IDR', 'kurs' => '1', 'revisi' => '0'];
        $noUrut = 1;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal, 'tanggal' => $tgl, 'nourut' => $noUrut, 'noakun' => $noakun, 'keterangan' => 'Tutup Bulan '.$tahunbulan.' Unit '.$param['kodeorg'], 'jumlah' => $data[0]['jumlah'], 'matauang' => 'IDR', 'kurs' => '1', 'kodeorg' => $param['kodeorg'], 'kodekegiatan' => '', 'kodeasset' => '', 'kodebarang' => '', 'nik' => '', 'kodecustomer' => '', 'kodesupplier' => '', 'noreferensi' => '', 'noaruskas' => '', 'kodevhc' => '', 'nodok' => '', 'kodeblok' => '', 'revisi' => '0'];
        ++$noUrut;
        $headErr = '';
        $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);
        if (!mysql_query($insHead)) {
            $headErr .= 'Insert Header Error : '.mysql_error()."\n";
        }

        if ('' === $headErr) {
            $detailErr = '';
            foreach ($dataRes['detail'] as $row) {
                $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                if (!mysql_query($insDet)) {
                    $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

                    break;
                }
            }
            if ('' === $detailErr) {
                createSaldoAwal($param['periode'], $tahunLanjut.'-'.addZero($bulanLanjut, 2), $param['kodeorg']);
                $str = 'delete from '.$dbname.".setup_periodeakuntansi where kodeorg='".$param['kodeorg']."' \r\n                             and periode like '".$tahunLanjut."%'";
                mysql_query($str);
                $queryUpd = updateQuery($dbname, 'setup_periodeakuntansi', ['tutupbuku' => 1], "kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'");
                if (!mysql_query($queryUpd)) {
                    echo 'Error Update : '.mysql_error();
                    exit();
                }

                $dataIns = ['kodeorg' => $param['kodeorg'], 'periode' => $tahunLanjut.'-'.addZero($bulanLanjut, 2), 'tanggalmulai' => $tglAwal, 'tanggalsampai' => $tglAkhir, 'tutupbuku' => 0];
                $queryIns = insertQuery($dbname, 'setup_periodeakuntansi', $dataIns);
                echo '1';
                if (!mysql_query($queryIns)) {
                    echo 'Error Insert : '.mysql_error();
                    $queryRB = updateQuery($dbname, 'setup_periodeakuntansi', ['tutupbuku' => 0], "kodeorg='".$param['kodeorg']."' and periode='".$param['periode']."'");
                    if (!mysql_query($queryRB)) {
                        echo 'Error Rollback Update : '.mysql_error();
                        exit();
                    }

                    $str = 'delete from '.$dbname.".keu_setup_watu_tutup where periode='".$param['periode']."'. and kodeorg='".$param['kodeorg']."'";
                    mysql_query($str);
                    $str = 'insert into '.$dbname.".keu_setup_watu_tutup(kodeorg,periode,username) values(\r\n                                  '".$param['kodeorg']."','".$param['periode']."','".$_SESSION['standard']['username']."')";
                    mysql_query($str);
                }
            } else {
                echo $detailErr;
                $RBDet = deleteQuery($dbname, 'keu_jurnalht', "nojurnal='".$nojurnal."'");
                if (!mysql_query($RBDet)) {
                    echo 'Rollback Delete Header Error : '.mysql_error();
                    exit();
                }
            }

            break;
        }

            echo $headErr;
            exit();
}
function createSaldoAwal($dariperiode, $keperiode, $kodeorg)
{
    global $conn;
    global $dbname;
    global $akunRAT;
    global $akunCLM;
    global $akunCLY;
    $sawal = [];
    $mtdebet = [];
    $mtkredit = [];
    $salak = [];
    $str = 'select awal'.substr($dariperiode, 5, 2).',noakun from '.$dbname.".keu_saldobulanan\r\n          where periode='".str_replace('-', '', $dariperiode)."' and kodeorg='".$kodeorg."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_array($res)) {
        $sawal[$bar[1]] = $bar[0];
        $mtdebet[$bar[1]] = 0;
        $mtkredit[$bar[1]] = 0;
        $salak[$bar[1]] = $bar[0];
    }
    $str = 'select debet,kredit,noakun from '.$dbname.".keu_jurnalsum_vw \r\n          where periode='".$dariperiode."' and kodeorg='".$kodeorg."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $mtdebet[$bar->noakun] = $bar->debet;
        $mtkredit[$bar->noakun] = $bar->kredit;
        $salak[$bar->noakun] = ($mtdebet[$bar->noakun] + $sawal[$bar->noakun]) - $mtkredit[$bar->noakun];
    }
    $str = 'select noakun from '.$dbname.'.keu_5akun where length(noakun)=7';
    $res = mysql_query($str);
    $temp = '';
    while ($bar = mysql_fetch_object($res)) {
        if ('' !== $sawal[$bar->noakun]) {
            if ('' === $mtdebet[$bar->noakun]) {
                $mtdebet[$bar->noakun] = 0;
            }

            if ('' === $mtkredit[$bar->noakun]) {
                $mtkredit[$bar->noakun] = 0;
            }

            $temp = 'update '.$dbname.".keu_saldobulanan \r\n                set debet".substr($dariperiode, 5, 2).'='.$mtdebet[$bar->noakun].",\r\n                kredit".substr($dariperiode, 5, 2).'='.$mtkredit[$bar->noakun]."\r\n                where periode='".str_replace('-', '', $dariperiode)."'\r\n                and kodeorg='".$kodeorg."' and noakun='".$bar->noakun."';";
            if (!mysql_query($temp)) {
                exit('Error update mutasi bulanan '.mysql_error($conn));
            }
        } else {
            if ('' !== $sawal[$bar->noakun] || '' !== $mtdebet[$bar->noakun] || '' !== $mtkredit[$bar->noakun]) {
                if ('' === $mtdebet[$bar->noakun]) {
                    $mtdebet[$bar->noakun] = 0;
                }

                if ('' === $mtkredit[$bar->noakun]) {
                    $mtkredit[$bar->noakun] = 0;
                }

                $temp = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n                  awal".substr($dariperiode, 5, 2).',debet'.substr($dariperiode, 5, 2).",\r\n                  kredit".substr($dariperiode, 5, 2).")values('".$kodeorg."','".str_replace('-', '', $dariperiode)."','".$bar->noakun."',0,".$mtdebet[$bar->noakun].','.$mtkredit[$bar->noakun].');';
                if (!mysql_query($temp)) {
                    exit('Error insert mutasi bulanan '.mysql_error($conn));
                }
            }
        }
    }
    $str = 'delete from '.$dbname.".keu_saldobulanan where periode='".str_replace('-', '', $keperiode)."'\r\n          and kodeorg='".$kodeorg."';";
    if (mysql_query($str)) {
        $saldoditahan = 0;
        foreach ($salak as $key => $val) {
            if ('' !== $salak[$key]) {
                $temp = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n                      awal".substr($keperiode, 5, 2).")values('".$kodeorg."','".str_replace('-', '', $keperiode)."','".$key."',".$salak[$key].')';
                if ('01' !== substr($keperiode, 5, 2)) {
                    if (!mysql_query($temp)) {
                        exit('Error insert saldo awal '.mysql_error($conn));
                    }
                } else {
                    if ($key < $akunRAT) {
                        if ($key === $akunCLY) {
                            $saldoditahan += $salak[$key];
                        } else {
                            if ($key === $akunCLM) {
                                $saldoditahan += $salak[$key];
                                $salak[$key] = 0;
                            }

                            $temp1 = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n                                  awal".substr($keperiode, 5, 2).")values('".$kodeorg."','".str_replace('-', '', $keperiode)."','".$key."',".$salak[$key].')';
                            if (!mysql_query($temp1)) {
                                exit('Error insert saldo awal '.mysql_error($conn));
                            }
                        }
                    }
                }
            }
        }
        if ('01' === substr($keperiode, 5, 2)) {
            $temp2 = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n          awal".substr($keperiode, 5, 2).")values\r\n           ('".$kodeorg."','".str_replace('-', '', $keperiode)."','".$akunCLY."',".$saldoditahan.')';
            if (!mysql_query($temp2)) {
                exit('Error insert laba ditahan pada saldo awal '.mysql_error($conn));
            }
        }
    }
}

?>