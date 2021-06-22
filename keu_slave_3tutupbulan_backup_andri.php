<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$period= $_POST['periode'];
$kodeorg=$_POST['kodeorg'];


$tmpPeriod = explode('-', $period);
if (empty($param)) {
    $tmpPeriod = explode('-', $_GET['periode']);
    $period=$_GET['periode'];
    $kodeorg=$_GET['kodeorg'];
}


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

if ('' == $akunCLM || '' == $akunCLY || '' == $akunRAT) {
    if ('EN' == $_SESSION['language']) {
        exit(' Error: Annual income account data, account  retained earnings and account limits profits / losses not yet listed on the parameters of the journal');
    }
    exit(' Error: data akun laba tahunan, akun laba ditahan dan batas akun laba/rugi belum terdaftar pada parameter jurnal');
}

$str = 'select tanggalmulai,tanggalsampai from '.$dbname.".setup_periodeakuntansi where \r\n      periode='".$period."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
$currstart = '';
$currend = '';
while ($bar = mysql_fetch_object($res)) {
    $currstart = $bar->tanggalmulai;
    $currend = $bar->tanggalsampai;
}
if ('' == $currstart || '' == $currend) {
    exit('Error: Accounting period is not normal to '.$_SESSION['empl']['lokasitugas']);
}

$str = 'select notransaksi,tanggal,jumlah from '.$dbname.".keu_kasbankht where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo " There are Cash/Bank transaction that has not been posted:\n";
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
if (0 < mysql_num_rows($res)) {
    while ($bar = mysql_fetch_object($res)) {
        $stm .= 'Gudang:'.$bar->kodegudang.'->No.>'.$bar->notransaksi.'->'.$bar->tanggal.'<br>';
    }
    echo "Error: Warehouse transaction that has not been posted\r<br>".$stm;
    exit();
}

$scekMut = 'select * from '.$dbname.".log_transaksiht where kodegudang like '".$_SESSION['empl']['lokasitugas']."%'\r\n              and tanggal between '".$currstart."' and '".$currend."' and tipetransaksi=7 \r\n              and (notransaksireferensi is null or notransaksireferensi='') order by notransaksi asc";
$qcekMut = mysql_query($scekMut);
if (0 < mysql_num_rows($qcekMut)) {
    echo "Error: Still no receipt of goods mutation that has not been done:\n";
    while ($rcekMut = mysql_fetch_object($qcekMut)) {
        echo $rcekMut->notransaksi.':'.tanggalnormal($rcekMut->tanggal)."\n";
    }
    exit('error');
}

$str = 'select notransaksi,tanggal from '.$dbname.".kebun_aktifitas where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n          and tanggal between '".$currstart."' and '".$currend."' and jurnal=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo " There still estate transaction that has not been posted:\n";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo $no.'. No '.$bar->notransaksi.':'.tanggalnormal($bar->tanggal)."\n";
    }
    exit('Error');
}

$str = 'select notransaksi,tanggal from '.$dbname.".vhc_runht where kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n          and tanggal between '".$currstart."' and '".$currend."' and posting=0";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    echo " There still Vehicle Run transaction that has not been posted:\n";
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        echo $no.'. No '.$bar->notransaksi.':'.tanggalnormal($bar->tanggal)."\n";
    }
    exit('Error');
}

$str = 'select sum(debet)-sum(kredit) as saldo FROM '.$dbname.".keu_jurnalsum_vw where  periode ='".$period."' \r\n          and kodeorg='".$_SESSION['empl']['lokasitugas']."' AND noakun like '4%'";
$res = mysql_query($str);
$transit = 0;
if (0 < mysql_num_rows($res)) {
    while ($bar = mysql_fetch_object($res)) {
        $transit = abs($bar->saldo);
    }
}

if (10 < $transit && '' != $transit) {
    exit(' Error: Transit account has not been allocated correctly, remains:'.$transit);
}

if ('HO' != substr($_SESSION['empl']['lokasitugas'], 2, 2)) {
    $str = 'select nojurnal FROM '.$dbname.".keu_jurnalht where  tanggal like '".$period."%'\r\n              and (nojurnal like '%".$_SESSION['empl']['lokasitugas']."/KBNB%' or nojurnal like '%".$_SESSION['empl']['lokasitugas']."/KNTB%' or nojurnal like '%".$_SESSION['empl']['lokasitugas']."/KROB%' or nojurnal like '%".$_SESSION['empl']['lokasitugas']."/PKSB%' or nojurnal like '%".$_SESSION['empl']['lokasitugas']."/KBN1%')";
    $res = mysql_query($str);
    if (0 < mysql_num_rows($res)) {
    } else {
        #exit(' Error: Proses Gaji has not been processed. ');
    }
}

switch ($proses) {
    case 'tutupBuku':
        if (12 == $tmpPeriod[1]) {
            $bulanLanjut = 1;
            $tahunLanjut = $tmpPeriod[0] + 1;
        } else {
            $bulanLanjut = $tmpPeriod[1] + 1;
            $tahunLanjut = $tmpPeriod[0];
        }


        $jmlHari = cal_days_in_month(CAL_GREGORIAN, $bulanLanjut, $tahunLanjut);
        $tglAwal = $tahunLanjut.'-'.addZero($bulanLanjut, 2).'-01';
        $tglAkhir = $tahunLanjut.'-'.addZero($bulanLanjut, 2).'-'.addZero($jmlHari, 2);


        $pt = getPT($dbname, $kodeorg);
        if (false == $pt) {
            $pt = getHolding($dbname, $kodeorg);
        }

        $tgl = $tmpPeriod[0].$tmpPeriod[1].cal_days_in_month(CAL_GREGORIAN, $tmpPeriod[1], $tmpPeriod[0]);
        $kodejurnal = 'CLSM';
        $nojurnal = $tgl.'/'.$kodeorg.'/'.$kodejurnal.'/999';
        $qCek = selectQuery($dbname, 'keu_jurnalht', '*', "nojurnal='".$nojurnal."'");
       // exit();
        $resCek = fetchData($qCek);
//        if (!empty($resCek)) {
//            echo ' Error : This period has been closed(Before).';
//            exit();
//        }

        $query = 'select count(*) as x from '.$dbname.".keu_jurnaldt_vw where \r\n                   tanggal between '".$currstart."' and '".$currend."' and substr(nojurnal,10,4)='".$kodeorg."'";
        $res = mysql_query($query);
        //echo $query."<br>";
        //die();
        if (0 == mysql_num_rows($res)) {
            echo 'Warning : No data found for this unit';
            exit();
        }

        $query = selectQuery($dbname, 'keu_jurnaldt_vw', 'substr(nojurnal,10,4) as kodeorg,sum(jumlah) as jumlah', "substr(nojurnal,10,4)='".$kodeorg."' and tanggal between '".$currstart."' and '".$currend."'\r\n             and noakun>='".$akunRAT."'").'group by substr(nojurnal,10,4)';
        $data = fetchData($query);

        $noakun = $akunCLM;
        if (0 < $data[0]['jumlah']) {
            $debetH = $data[0]['jumlah'];
            $kreditH = 0;
        } else {
            $debetH = 0;
            $kreditH = $data[0]['jumlah'];
        }
		$debetH = 0;
		$kreditH = 0;
		$data[0]['jumlah'] = 0;
        $dataRes['header'] = ['nojurnal' => $nojurnal,
            'kodejurnal' => $kodejurnal,
            'tanggal' => $tgl,
            'tanggalentry' => date('Ymd'),
            'posting' => '0',
            'totaldebet' => $debetH,
            'totalkredit' => $kreditH,
            'amountkoreksi' => '0',
            'noreferensi' => 'TUTUP/'.$kodeorg.'/'.$tahunbulan,
            'autojurnal' => '1',
            'matauang' => 'IDR',
            'kurs' => '1',
            'revisi' => '0'];
        $noUrut = 1;
        $dataRes['detail'][] = ['nojurnal' => $nojurnal,
            'tanggal' => $tgl,
            'nourut' => $noUrut,
            'noakun' => $noakun,
            'keterangan' => 'Tutup Bulan '.$tahunbulan.' Unit '.$kodeorg,
            'jumlah' => $data[0]['jumlah'],
            'matauang' => 'IDR',
            'kurs' => '1',
            'kodeorg' => $kodeorg,
            'kodekegiatan' => '',
            'kodeasset' => '',
            'kodebarang' => '',
            'nik' => '',
            'kodecustomer' => '',
            'kodesupplier' => '',
            'noreferensi' => '',
            'noaruskas' => '',
            'kodevhc' => '',
            'nodok' => '',
            'kodeblok' => '',
            'revisi' => '0'];
        ++$noUrut;

        $headErr = '';
        $insHead = insertQuery($dbname, 'keu_jurnalht', $dataRes['header']);

        if (!mysql_query($insHead)) {
            $headErr .= 'Insert Header Error : '.mysql_error()."\n".$insHead;
        }

        if ('' == $headErr) {
            $detailErr = '';

            foreach ($dataRes['detail'] as $row) {
                $insDet = insertQuery($dbname, 'keu_jurnaldt', $row);
                if (!mysql_query($insDet)) {
                    $detailErr .= 'Insert Detail Error : '.mysql_error()."\n";

                    break;
                }
            }
            if ('' == $detailErr) {
//                createSaldoAwal($period, $tahunLanjut.'-'.addZero($bulanLanjut, 2), $kodeorg);
                createSaldoAwal('201901', '201902', $kodeorg);
                $queryUpd = updateQuery($dbname, 'setup_periodeakuntansi', ['tutupbuku' => 1], "kodeorg='".$kodeorg."' and periode='".$period."'");
                if (!mysql_query($queryUpd)) {
                    echo 'Error Update : '.mysql_error();
                    exit();
                }

                $dataIns = ['kodeorg' => $kodeorg, 'periode' => $tahunLanjut.'-'.addZero($bulanLanjut, 2), 'tanggalmulai' => $tglAwal, 'tanggalsampai' => $tglAkhir, 'tutupbuku' => 0];
                $queryIns = insertQuery($dbname, 'setup_periodeakuntansi', $dataIns);

                echo '1';
                if (!mysql_query($queryIns)) {
                    echo 'Error Insert : '.mysql_error();
                    $queryRB = updateQuery($dbname, 'setup_periodeakuntansi', ['tutupbuku' => 0], "kodeorg='".$kodeorg."' and periode='".$period."'");
                    if (!mysql_query($queryRB)) {
                        echo 'Error Rollback Update : '.mysql_error();
                        exit();
                    }
                } else {
                    $str = 'delete from '.$dbname.".keu_setup_watu_tutup where periode='".$period."'. and kodeorg='".$kodeorg."'";
                    mysql_query($str);
                    $str = 'insert into '.$dbname.".keu_setup_watu_tutup(kodeorg,periode,username) values(\r\n                                  '".$kodeorg."','".$period."','".$_SESSION['standard']['username']."')";
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
    $indexd=substr($dariperiode, 4, 2);
    $indexk=substr($dariperiode, 4, 2);
    $str = 'select awal'.$index.',noakun from '.$dbname.".keu_saldobulanan where periode='".str_replace('-', '', $dariperiode)."' and kodeorg='".$kodeorg."'";

    echoMessage('str ',$str);
    echoMessage('index= ',$index);
    echoMessage('dariperiode ',$dariperiode);
    echoMessage('keperiode ',$keperiode);
    echoMessage('kodeorg ',$kodeorg);

    $res = mysql_query($str);
    while ($bar = mysql_fetch_array($res)) {
        $sawal[$bar[1]] = $bar[0];
        $mtdebet[$bar[1]] = 0;
        $mtkredit[$bar[1]] = 0;
        $salak[$bar[1]] = $bar[0];
    }
    echoMessage('awal ',$sawal);
    $str = 'select  debet'.$index.',kredit'.$index.',noakun from '.$dbname.".keu_saldobulanan where periode='".$dariperiode."' and kodeorg='".$kodeorg."'";
    $res2 = mysql_query($str);
    echoMessage('str  ',$str);
    $saldo_=array();
    while ($bar = mysql_fetch_array($res2)) {
        $mtdebet[$bar[2]] = $bar[0];
        $mtkredit[$bar[2]] = $bar[1];
        $salak[$bar[2]] = ($mtdebet[$bar[2]] + $sawal[$bar[2]]) - $mtkredit[$bar[2]];
         $saldo_[$bar[2]]= array(
            "awal"=>$sawal[$bar[2]],
            "debet"=>$bar[0],
            "kredit"=>$bar[1],
            "akumulasi"=>$salak[$bar[2]]
    );
    }
//    echoMessage('saldo_ ',$saldo_);
    $str = 'select noakun from '.$dbname.'.keu_5akun where length(noakun)=7';
    $res = mysql_query($str);
    $temp = '';
    while ($bar = mysql_fetch_object($res)) {
        if ('' != $sawal[$bar->noakun]) {
            if ('' == $mtdebet[$bar->noakun]) {
                $mtdebet[$bar->noakun] = 0;
            }

            if ('' == $mtkredit[$bar->noakun]) {
                $mtkredit[$bar->noakun] = 0;
            }

            $temp = 'update '.$dbname.".keu_saldobulanan \r\n                set debet".substr($dariperiode, 5, 2).'='.$mtdebet[$bar->noakun].",\r\n                kredit".substr($dariperiode, 5, 2).'='.$mtkredit[$bar->noakun]."\r\n                where periode='".str_replace('-', '', $dariperiode)."'\r\n                and kodeorg='".$kodeorg."' and noakun='".$bar->noakun."';";
            if (!mysql_query($temp)) {
                exit('Error update mutasi bulanan '.mysql_error($conn).'\n'.$temp);
            }
        } else {
            if ('' != $sawal[$bar->noakun] || '' != $mtdebet[$bar->noakun] || '' != $mtkredit[$bar->noakun]) {
                if ('' == $mtdebet[$bar->noakun]) {
                    $mtdebet[$bar->noakun] = 0;
                }

                if ('' == $mtkredit[$bar->noakun]) {
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
            if ('' != $salak[$key]) {
                $temp = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,".
                    "awal".$index.")values('".
                    "$kodeorg','".str_replace('-', '', $keperiode)."','".$key."',".$salak[$key].')';
                echoMessage('temp ',$temp);
                if ('01' != substr($keperiode, 4, 2)) {
                    if (!mysql_query($temp)) {
                        exit('Error insert saldo awal '.mysql_error($conn).':'.$temp);
                    }
                } else {
                    if ($key < $akunRAT) {
                        if ($key == $akunCLY) {
                            $saldoditahan += $salak[$key];
                        } else {
                            if ($key == $akunCLM) {
                                $saldoditahan += $salak[$key];
                                $salak[$key] = 0;
                            }

                            $temp1 = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n awal".$index.")values('"
                                .$kodeorg."','".str_replace('-', '', $keperiode)."','".$key."',".$salak[$key].')';
                            echoMessage('temp1  ',$temp1 );
                            echoMessage('temp1  ',$temp1 );
                            if (!mysql_query($temp1)) {
                                exit('Error insert saldo awal '.mysql_error($conn));
                            }
                        }
                    }
                }
            }
        }
        if ('01' == $index) {
            $temp2 = 'insert into  '.$dbname.".keu_saldobulanan (kodeorg,periode,noakun,\r\n awal".substr($keperiode, 5, 2).")values\r\n           ('".
                $kodeorg."','".str_replace('-', '', $keperiode)."','".$akunCLY."',".$saldoditahan.')';
            echoMessage('temp2  ',$temp2 );
            if (!mysql_query($temp2)) {
                exit('Error insert laba ditahan pada saldo awal '.mysql_error($conn));
            }
        }
    }
}

?>