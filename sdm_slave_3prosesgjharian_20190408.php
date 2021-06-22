<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
include_once 'config/connection.php';

$cekuser = "select count(*) FROM sdm_ho_payroll_user a JOIN user b ON a.uname = b.namauser WHERE b.karyawanid = '".$_SESSION['standard']['userid']."' LIMIT 1";
$qcekuser = mysql_query($cekuser);
if (mysql_num_rows($qcekuser) > 0) {
    $proses = $_GET['proses'];
    $param = $_POST;
    $id = [];
    $namakar = [];
    $kdjabatan = [];
    $tipekar = [];
    $namatipe = [];
    $kdgol = [];
    $sCekPeriode = 'select distinct * from '.$dbname.".sdm_5periodegaji where periode='".$param['periodegaji']."'\r\n              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='H'";
    $qCekPeriode = mysql_query($sCekPeriode);
    if (0 < mysql_num_rows($qCekPeriode)) {
        $aktif2 = false;
    } else {
        $aktif2 = true;
    }

    if (!$aktif2) {
        exit(' Payroll period has been closed');
    }

    $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$param['periodegaji']."' and\r\n             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
    $res = mysql_query($str);
    if (0 < mysql_num_rows($res)) {
        $aktif = false;
    } else {
        $aktif = true;
    }

    if (!$aktif) {
        exit('Accounting period has been closed');
    }

    $qPeriod = selectQuery($dbname, 'sdm_5periodegaji', 'tanggalmulai,tanggalsampai', "periode='".$param['periodegaji']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='H'");
    $resPeriod = fetchData($qPeriod);
    $tanggal1 = $resPeriod[0]['tanggalmulai'];
    $tanggal2 = $resPeriod[0]['tanggalsampai'];
    $str = 'delete from '.$dbname.".kebun_aktifitas where notransaksi like '%//%'";
    mysql_query($str);
    $query1 = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,jms,statuspajak,npwp,kodejabatan,tipekaryawan,kodegolongan,idmedical', "tipekaryawan in(1,2,3,4,6) and lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (tanggalkeluar>='".$tanggal1."' or tanggalkeluar is NULL) and alokasi=0 and sistemgaji='Harian' and ( tanggalmasuk<='".$tanggal2."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null) and karyawanid not in (0999999999,0888888888) order by karyawanid");
    $absRes = fetchData($query1);
    if (empty($absRes)) {
        echo 'Error : No Presence(Kehadiran) On this Payroll Period';
        exit();
    }

    $id = [];
    foreach ($absRes as $row => $kar) {
        $id[$kar['karyawanid']][] = $kar['karyawanid'];
        $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
        $kdjabatan[$kar['karyawanid']] = $kar['kodejabatan'];
        $kdgol[$kar['karyawanid']] = $kar['kodegolongan'];
        $tipekar[$kar['karyawanid']] = $kar['tipekaryawan'];
        $nojms[$kar['karyawanid']] = trim($kar['jms']);
        $nobpjskes[$kar['karyawanid']] = trim($kar['idmedical']);
        $statuspajak[$kar['karyawanid']] = trim($kar['statuspajak']);
        $npwp[$kar['karyawanid']] = trim($kar['npwp']);
    }
    $strgjh = 'select a.karyawanid,sum(jumlah)/25 as gjperhari from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid where a.tahun='.substr($tanggal1, 0, 4)." and b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL) and b.alokasi in(0) and a.idkomponen in(1,2,3,4,15,21,29,30,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51) and sistemgaji='Harian' and b.karyawanid not in (0999999999,0888888888) group by a.karyawanid ";
    $resgjh = fetchData($strgjh);
    foreach ($resgjh as $idx => $val) {
        $gajiperhari[$val['karyawanid']] = $val['gjperhari'];
    }
    $strgjh = 'select  count(*) as jlh,b.karyawanid from '.$dbname.".sdm_hktdkdibayar_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in (1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian'\r\n               group by a.karyawanid";
    $tdkdibayar = [];
    $resgjh = fetchData($strgjh);
    foreach ($resgjh as $idx => $val) {
        $tdkdibayar[$val['karyawanid']] = $gajiperhari[$val['karyawanid']] * $val['jlh'];
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => 20, 'jumlah' => $tdkdibayar[$val['karyawanid']], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
    }
    $str = 'select tipe from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
    $res = mysql_query($str);
    $tip = '';
    while ($bar = mysql_fetch_object($res)) {
        $tip = $bar->tipe;
    }
    if ('KANWIL' == $tip || 'PABRIK' == $tip) {
        $str1 = 'select distinct a.*,b.namakaryawan,b.tipekaryawan,b.bagian from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid left join '.$dbname.'.kebun_kehadiran_vw c on b.karyawanid=c.karyawanid where a.tahun='.substr($tanggal2, 0, 4)." and b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0 and b.sistemgaji='Harian' or c.tanggal>='".$tanggal1."' and c.tanggal<='".$tanggal2."' order by b.karyawanid,idkomponen";
    } else {
        $str1 = 'select distinct a.*,b.namakaryawan,b.tipekaryawan,b.bagian from '.$dbname.'.sdm_5gajipokok a left join '.$dbname.'.datakaryawan b on a.karyawanid=b.karyawanid left join '.$dbname.'.kebun_kehadiran_vw c on b.karyawanid=c.karyawanid where a.tahun='.substr($tanggal2, 0, 4)." and b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0 and b.sistemgaji='Harian' or c.tanggal>='".$tanggal1."' and c.tanggal<='".$tanggal2."' order by b.karyawanid,idkomponen";
    }

    $res1 = fetchData($str1);
    $query6 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan'");
    $query7 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan'");
    $jmsRes = fetchData($query6);
    $bpjspt = fetchData($query7);
    $persenJms = $jmsRes[0]['value'] / 100;
    $persenjhtpt = $bpjspt[0]['jhtpt'] / 100;
    $persenjppt = $bpjspt[0]['jppt'] / 100;
    $persenjkkpt = $bpjspt[0]['jkkpt'] / 100;
    $persenjkmpt = $bpjspt[0]['jkmpt'] / 100;
    $persenbpjspt = $bpjspt[0]['bpjspt'] / 100;
    $persenjhtkar = $jmsRes[0]['jhtkar'] / 100;
    $persenjpkar = $jmsRes[0]['jpkar'] / 100;
    $persenbpjskar = $jmsRes[0]['bpjskar'] / 100;
    $jabPersen = 0;
    $jabMax = 0;
    $str = 'select persen,max from '.$dbname.'.sdm_ho_pph21jabatan';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $jabPersen = $bar->persen / 100;
        $jabMax = $bar->max * 12;
        $jabMax2 = $bar->max;
    }
    $tjms = [];
    $tipekaryawan = [];
    $bagiankaryawan = [];
    foreach ($res1 as $idx => $val) {
        if ($id[$val['karyawanid']][0] == $val['karyawanid']) {
            if ('1' == $val['tipekaryawan']) {
                $tipekaryawan[$val['karyawanid']] = 'BHL';
            } else {
                if ('2' == $val['tipekaryawan']) {
                    $tipekaryawan[$val['karyawanid']] = 'ORGANIK';
                } else {
                    if ('3' == $val['tipekaryawan']) {
                        $tipekaryawan[$val['karyawanid']] = 'SKU';
                    } else {
                        if ('4' == $val['tipekaryawan']) {
                            $tipekaryawan[$val['karyawanid']] = 'SKUP';
                        } else {
                            if ('6' == $val['tipekaryawan']) {
                                $tipekaryawan[$val['karyawanid']] = 'PKWT';
                            } else {
                                $tipekaryawan[$val['karyawanid']] = 'ERROR';
                            }
                        }
                    }
                }
            }

            if ('RO_INFR' == $val['bagian']) {
                $bagiankaryawan[$val['karyawanid']] = 'RO_INFR';
            }

            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => $val['idkomponen'], 'jumlah' => $val['jumlah'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            if ((1 == $val['idkomponen'] || 2 == $val['idkomponen'] || 3 == $val['idkomponen'] || 4 == $val['idkomponen'] || 15 == $val['idkomponen'] || 17 == $val['idkomponen'] || 21 == $val['idkomponen'] || 22 == $val['idkomponen'] || 23 == $val['idkomponen'] || 29 == $val['idkomponen'] || 30 == $val['idkomponen'] || 32 == $val['idkomponen'] || 33 == $val['idkomponen'] || 35 == $val['idkomponen'] || 36 == $val['idkomponen'] || 37 == $val['idkomponen'] || 38 == $val['idkomponen'] || 39 == $val['idkomponen'] || 40 == $val['idkomponen'] || 41 == $val['idkomponen'] || 42 == $val['idkomponen'] || 43 == $val['idkomponen'] || 44 == $val['idkomponen'] || 45 == $val['idkomponen'] || 46 == $val['idkomponen'] || 47 == $val['idkomponen'] || 48 == $val['idkomponen'] || 49 == $val['idkomponen'] || 50 == $val['idkomponen'] || 51 == $val['idkomponen'] || 54 == $val['idkomponen'] || 58 == $val['idkomponen'] || 59 == $val['idkomponen'] || 60 == $val['idkomponen'] || 61 == $val['idkomponen'] || 62 == $val['idkomponen'] || 63 == $val['idkomponen'] || 65 == $val['idkomponen']) && '' != $nojms[$val['karyawanid']]) {
                $tjms[$val['karyawanid']] += $val['jumlah'];
            }
        }
    }
    foreach ($tjms as $key => $nilai) {
        if ('BHL' == $tipekaryawan[$key] || 'ORGANIK' == $tipekaryawan[$key] || 'SKU' == $tipekaryawan[$key] || 'SKUP' == $tipekaryawan[$key] || 'PKWT' == $tipekaryawan[$key]) {
            if ('E' == substr($_SESSION['empl']['lokasitugas'], -1)) {
                $querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
                $angkapersenlokres = fetchData($querypersenlokres);
                $jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
            }

            if ('HO' == substr($_SESSION['empl']['lokasitugas'], -2)) {
                $querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='HO'");
                $angkapersenlokres = fetchData($querypersenlokres);
                $jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
            }

            if ('M' == substr($_SESSION['empl']['lokasitugas'], -1)) {
                $querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='M'");
                $angkapersenlokres = fetchData($querypersenlokres);
                $jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
            }

            if ('RO' == substr($_SESSION['empl']['lokasitugas'], -2)) {
                $querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='RO'");
                $angkapersenlokres = fetchData($querypersenlokres);
                $jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
            }

            if ('RO_INFR' == $bagiankaryawan[$key]) {
                $querypersenlokres = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='perusahaan' and lokasiresiko='E'");
                $angkapersenlokres = fetchData($querypersenlokres);
                $jkkptpersen = $angkapersenlokres[0]['jkkpt'] / 100;
            }

            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 6, 'jumlah' => round($nilai * $jkkptpersen), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 7, 'jumlah' => round($nilai * $persenjkmpt), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            $nilaibatasmax3 = 8000000;
            if ($nilaibatasmax3 < $nilai) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $nilaibatasmax3 * $persenbpjspt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            } else {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 57, 'jumlah' => $nilai * $persenbpjspt, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => $nilai * $persenjhtkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            $nilaibatasmax = 7703500;
            if ($nilaibatasmax < $nilai) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilaibatasmax * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            } else {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilai * $persenjpkar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            $nilaibatasmax3 = 10000000;
            if ($nilaibatasmax3 < $nilai) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $jabMax2, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            } else {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => round(($nilai + $nilai * $persenjkkpt + $nilai * $persenjkmpt + $nilai * $persenbpjspt) * $jabPersen, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('' != $nobpjskes[$key]) {
                $nilaibatasmax2 = 8000000;
                if ($nilaibatasmax2 < $nilai) {
                    $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilaibatasmax2 * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
                } else {
                    $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => $nilai * $persenbpjskar, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
                }
            } else {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 8, 'jumlah' => 0, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }
        }
    }
    $where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."')";
    $query2 = 'select a.karyawanid,sum(a.uangkelebihanjam) as lembur from '.$dbname.".sdm_lemburdt a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0)\r\n               and sistemgaji='Harian'\r\n               and ".$where2.' group by a.karyawanid';
    $lbrRes = fetchData($query2);
    foreach ($lbrRes as $idx => $row) {
        if (isset($id[$row['karyawanid']])) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 17, 'jumlah' => $row['lembur'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
        }
    }
    $where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
    $query3 = 'select a.nik as karyawanid,sum(jumlahpotongan) as potongan, a.tipepotongan as tipepotongan from '.$dbname.".sdm_potongandt a left join\r\n              ".$dbname.".datakaryawan b on a.nik=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,5,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0)\r\n               and sistemgaji='Harian'\r\n               and ".$where3.' group by a.nik';
    $potRes = fetchData($query3);
    foreach ($potRes as $idx => $row) {
        if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
            if ('25' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 25, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('26' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 26, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('27' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 27, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('20' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 20, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('19' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 19, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('64' == $row['tipepotongan']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 64, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }
        }
    }


    $where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
    $query4 = 'select a.karyawanid,a.bulanan,a.jenis from '.$dbname.".sdm_angsuran a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0)\r\n               and a.active=1\r\n               and sistemgaji='Harian'\r\n               and ".$where4;
    $angRes = fetchData($query4);
    foreach ($angRes as $idx => $row) {
        if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => $row['jenis'], 'jumlah' => $row['bulanan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
        }
    }
    $where3a = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
    $query3a = 'select a.karyawanid as karyawanid,a.jumlah as potongan, a.idkomponen as idkomponen from '.$dbname.".sdm_gaji a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0)\r\n               and b.sistemgaji='Harian'\r\n               and ".$where3a.' ';
    $angResa = fetchData($query3a);
    foreach ($angResa as $idx => $row) {
        if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
            if ('63' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 63, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('23' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 23, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('58' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 58, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('59' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 59, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('61' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 61, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('65' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 65, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('60' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 60, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('21' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 21, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('61' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 61, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('12' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 12, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('62' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 62, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('22' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 22, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('54' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 54, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }

            if ('67' == $row['idkomponen']) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 67, 'jumlah' => $row['potongan'], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }
        }
    }
    $stru1 = 'select distinct(tanggal) from '.$dbname.".kebun_kehadiran_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian' order by tanggal";
    $resu1 = mysql_query($stru1);
    $stru2 = 'select distinct(tanggal) from '.$dbname.".kebun_prestasi_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian' order by tanggal";
    $resu2 = mysql_query($stru2);
    $stru3 = "select distinct(tanggal)\r\n           from ".$dbname.".vhc_runhk_vw a left join\r\n          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n           where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n           and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n           and posting=0 and sistemgaji='Harian' order by tanggal";
    $resu3 = mysql_query($stru3);
    if (0 < mysql_num_rows($resu1) || 0 < mysql_num_rows($resu2) || 0 < mysql_num_rows($resu3)) {
        echo 'Masih ada data yang belum di posting/There still unconfirmed transaction:';
        echo "<table class=sortable border=0 cellspacing=1>\r\n            <thead><tr class=rowheader>\r\n            <td>".$_SESSION['lang']['jenis']."</td>\r\n            <td>".$_SESSION['lang']['tanggal']."</td>\r\n            </tr></thead><tbody>";
        while ($bar = mysql_fetch_object($resu1)) {
            echo '<tr class=rowcontent><td>Perawatan Kebun</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
        }
        while ($bar = mysql_fetch_object($resu2)) {
            echo '<tr class=rowcontent><td>Panen</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
        }
        while ($bar = mysql_fetch_object($resu3)) {
            echo '<tr class=rowcontent><td>Traksi Pekerjaan</td><td>'.tanggalnormal($bar->tanggal).'</td></tr>';
        }
        echo '</tbody><tfoot></tfoot></table>';
        exit();
    }

    $premi = [];
    $penalty = [];
    $penaltykehadiran = [];
    $query5 = 'select a.karyawanid,sum(a.insentif) as premi from '.$dbname.".kebun_kehadiran_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian'\r\n               group by a.karyawanid";
    $premRes = fetchData($query5);
    foreach ($premRes as $idx => $val) {
        if (0 < $val['premi']) {
            $premi[$val['karyawanid']] = $val['premi'];
        }
    }
    $query6 = "select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty\r\n               from ".$dbname.".kebun_prestasi_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian'\r\n               group by a.karyawanid";
    $premRes1 = fetchData($query6);
    foreach ($premRes1 as $idx => $val) {
        if (0 < $val['premi']) {
            if (isset($premi[$val['karyawanid']])) {
                $premi[$val['karyawanid']] += $val['premi'];
            } else {
                $premi[$val['karyawanid']] = $val['premi'];
            }
        }

        if (0 < $val['penalty']) {
            $penalty[$val['karyawanid']] = $val['penalty'];
        }
    }
    $query7 = "select a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty\r\n               from ".$dbname.".vhc_runhk_vw a left join\r\n              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Harian'\r\n               group by a.idkaryawan";
    $premRes2 = fetchData($query7);
    foreach ($premRes2 as $idx => $val) {
        if (0 < $val['premi']) {
            if (isset($premi[$val['karyawanid']])) {
                $premi[$val['karyawanid']] += $val['premi'];
            } else {
                $premi[$val['karyawanid']] = $val['premi'];
            }
        }

        if (0 < $val['penalty']) {
            if (isset($penalty[$val['karyawanid']])) {
                $penalty[$val['karyawanid']] += $val['penalty'];
            } else {
                $penalty[$val['karyawanid']] = $val['penalty'];
            }
        }
    }
    $query8 = "select sum(a.premiinput) as premi,a.karyawanid,a.tanggal\r\n               from ".$dbname.".kebun_premikemandoran a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n\t\t\t    and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n                and b.sistemgaji='Harian' and a.posting=1\r\n               group by a.karyawanid";
    $premRes2 = fetchData($query8);
    foreach ($premRes2 as $idx => $val) {
        if (0 < $val['premi']) {
            if (isset($premi[$val['karyawanid']])) {
                $premi[$val['karyawanid']] += $val['premi'];
            } else {
                $premi[$val['karyawanid']] = $val['premi'];
            }
        }
    }
    $stkh = 'select a.karyawanid,sum(a.premi+a.insentif) as premi from '.$dbname.".sdm_absensidt a\r\n                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."' and (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0 and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian' and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
    $reskh = mysql_query($stkh);
    while ($barky = mysql_fetch_object($reskh)) {
        if (isset($premi[$barky->karyawanid])) {
            $premi[$barky->karyawanid] += $barky->premi;
        } else {
            $premi[$barky->karyawanid] = $barky->premi;
        }
    }
    $stkh1 = 'select a.karyawanid,a.rupiahpremi  from '.$dbname.".kebun_premipanen a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.tipekaryawan in(1,2,3,4,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and a.periode like  '%".$param['periodegaji']."%' and sistemgaji='Harian' group by a.karyawanid";
    $reskh1 = mysql_query($stkh1);
    while ($barky = mysql_fetch_object($reskh1)) {
        if (isset($premi[$barky->karyawanid])) {
            $premi[$barky->karyawanid] += $barky->rupiahpremi;
        } else {
            $premi[$barky->karyawanid] = $barky->rupiahpremi;
        }
    }
    foreach ($premi as $idx => $row) {
        if (0 < $row) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 16, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
        }
    }
    foreach ($penalty as $idx => $row) {
        if (0 < $row) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 26, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
        }

    }


    $stkh = 'select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran from '.$dbname.".sdm_absensidt a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.tipekaryawan in(1,2,3,4,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Harian'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
    $reskh = mysql_query($stkh);
    while ($barkh = mysql_fetch_object($reskh)) {
        if (0 < $barkh->penaltykehadiran) {
            $penaltykehadiran[$barkh->karyawanid] = $barkh->penaltykehadiran;
        }
    }

    foreach ($penaltykehadiran as $idx => $row) {
        if (0 < $row) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 64, 'jumlah' => $row, 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
        }
    }

    $strx = "select id as komponen, case plus when 0 then -1 else plus end as pengali,name as nakomp\r\n              FROM ".$dbname.'.sdm_ho_component';
    $comRes = fetchData($strx);
    $comp = [];
    $nakomp = [];
    foreach ($comRes as $idx => $row) {
        $comp[$row['komponen']] = $row['pengali'];
        $nakomp[$row['komponen']] = $row['nakomp'];
    }
    $ptkp = [];
    $str = 'select id,value from '.$dbname.'.sdm_ho_pph21_ptkp';
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $ptkp[$bar->id] = $bar->value;
    }
    $pphtarif = [];
    $pphpercent = [];
    $str = 'select level,percent,upto from '.$dbname.'.sdm_ho_pph21_kontribusi order by level';
    $res = mysql_query($str);
    $urut = 0;
    while ($bar = mysql_fetch_object($res)) {
        $pphtarif[$urut] = $bar->upto;
        $pphpercent[$urut] = $bar->percent / 100;
        ++$urut;
    }
    foreach ($id as $key => $val) {
        $penghasilan[$val[0]] = 0;
        $penghasilanbruto[$val[0]] = 0;
        foreach ($readyData as $dat => $bar) {
            if ($val[0] == $bar['karyawanid']) {
                if (1 == $comp[$bar['idkomponen']] || 5 == $bar['idkomponen'] || 9 == $bar['idkomponen'] || 66 == $bar['idkomponen']) {
                    $penghasilan[$val[0]] += floor($comp[$bar['idkomponen']] * $bar['jumlah']);
                }

                if (1 == $comp[$bar['idkomponen']] || 5 == $comp[$bar['idkomponen']] || 9 == $comp[$bar['idkomponen']] || 66 == $comp[$bar['idkomponen']]) {
                    $penghasilanbruto[$val[0]] += $comp[$bar['idkomponen']] * $bar['jumlah'];
                }
            }
        }
    }
    foreach ($id as $key => $val) {
        $jhtkarypersen[$val[0]] = 0;
        $jpkarypersen[$val[0]] = 0;
        $gapoktunj[$val[0]] = 0;
        $pph21[$val[0]] = 0;
        $gapok[$val[0]] = 0;
        $tunjgol[$val[0]] = 0;
        $tunjab[$val[0]] = 0;
        $tunjnat[$val[0]] = 0;
        $tunjmasker[$val[0]] = 0;
        $totuptetap[$val[0]] = 0;
        $tunjharian[$val[0]] = 0;
        $totgross[$val[0]] = 0;
        $jkk[$val[0]] = 0;
        $jkm[$val[0]] = 0;
        $bpjspt[$val[0]] = 0;
        $totgajibruto[$val[0]] = 0;
        $biayajab[$val[0]] = 0;
        $gjnettosebulan[$val[0]] = 0;
        $gjnettosetahun[$val[0]] = 0;
        $ptkp[$val[0]] = 0;
        $pkp[$val[0]] = 0;
        $thpbruto[$val[0]] = 0;
        $potongankoperasi[$val[0]] = 0;
        $potonganvop[$val[0]] = 0;
        $potonganmotor[$val[0]] = 0;
        $potonganlaptop[$val[0]] = 0;
        $potongandenda[$val[0]] = 0;
        $potonganbpjskes[$val[0]] = 0;
        $thpnetto[$val[0]] = 0;
        $tunjlembur[$val[0]] = 0;
        $tunjtidaktetap[$val[0]] = 0;
        $byjb[$val[0]] = 0;
        $byjbq[$val[0]] = 0;
        $pphSetahun[$val[0]] = 0;
        $tunjpremi[$val[0]] = 0;
        $potdendapanen[$val[0]] = 0;
        $a = 0;
        foreach ($readyData as $dat => $bar) {

            if ($val[0] == $bar['karyawanid']) {

                if (6 == $comp[$bar['idkomponen']] || 6 == $bar['idkomponen']) {
                    $jkk[$val[0]] += $bar['jumlah'];
                }

                if (7 == $comp[$bar['idkomponen']] || 7 == $bar['idkomponen']) {
                    $jkm[$val[0]] += $bar['jumlah'];
                }

                if (57 == $comp[$bar['idkomponen']] || 57 == $bar['idkomponen']) {
                    $bpjspt[$val[0]] += $bar['jumlah'];
                }

                if (5 == $comp[$bar['idkomponen']] || 5 == $bar['idkomponen']) {
                    $jhtkarypersen[$val[0]] += $bar['jumlah'];
                }

                if (9 == $comp[$bar['idkomponen']] || 9 == $bar['idkomponen']) {
                    $jpkarypersen[$val[0]] += $bar['jumlah'];
                }

                if (17 == $comp[$bar['idkomponen']] || 17 == $bar['idkomponen']) {
                    $tunjlembur[$val[0]] = $bar['jumlah'];
                }

                if (4 == $comp[$bar['idkomponen']] || 4 == $bar['idkomponen']) {
                    $tunjnat[$val[0]] += $bar['jumlah'];
                }

                if (15 == $comp[$bar['idkomponen']] || 15 == $bar['idkomponen']) {
                    $tunjmasker[$val[0]] += $bar['jumlah'];
                }

                if (66 == $comp[$bar['idkomponen']] || 66 == $bar['idkomponen']) {

                    $nilaibatasmax3 = 10000000;
                    $byjb[$val[0]] += $bar['jumlah'];
                    if ($nilaibatasmax3 < $penghasilanbruto[$val[0]]) {
                        $biayajab[$val[0]] = $jabMax2;
                        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $biayajab[$val[0]], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
                    } else {
                        $biayajab[$val[0]] = floor($penghasilanbruto[$val[0]] * 0.05);
                        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 66, 'jumlah' => $biayajab[$val[0]], 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
                    }
                }

                if (25 == $comp[$bar['idkomponen']] || 25 == $bar['idkomponen']) {
                    $potongankoperasi[$val[0]] += $bar['jumlah'];
                }

                if (52 == $comp[$bar['idkomponen']] || 52 == $bar['idkomponen']) {
                    $potonganvop[$val[0]] += $bar['jumlah'];
                }

                if (10 == $comp[$bar['idkomponen']] || 10 == $bar['idkomponen']) {
                    $potonganmotor[$val[0]] += $bar['jumlah'];
                }

                if (11 == $comp[$bar['idkomponen']] || 11 == $bar['idkomponen']) {
                    $potonganlaptop[$val[0]] += $bar['jumlah'];
                }

                if (27 == $comp[$bar['idkomponen']] || 27 == $bar['idkomponen'] || 64 == $comp[$bar['idkomponen']] || 64 == $bar['idkomponen']) {
                    $potongandenda[$val[0]] += $bar['jumlah'];
                }

                if (8 == $comp[$bar['idkomponen']] || 8 == $bar['idkomponen']) {
                    $potonganbpjskes[$val[0]] += $bar['jumlah'];
                }

                if (26 == $comp[$bar['idkomponen']] || 26 == $bar['idkomponen']) {
                    $potdendapanen[$val[0]] += $bar['jumlah'];
                }

                if (29 == $comp[$bar['idkomponen']] || 29 == $bar['idkomponen'] || 30 == $comp[$bar['idkomponen']] || 30 == $bar['idkomponen'] || 32 == $comp[$bar['idkomponen']] || 32 == $bar['idkomponen'] || 33 == $comp[$bar['idkomponen']] || 33 == $bar['idkomponen']) {
                    $gapoktunj[$val[0]] += $bar['jumlah'];
                }

                if (24 == $comp[$bar['idkomponen']] || 24 == $bar['idkomponen']) {
                    $pph21[$val[0]] += $bar['jumlah'];
                }

                if (1 == $bar['idkomponen']) {
                    $gapok[$val[0]] += $bar['jumlah'];
                }

                if (3 == $comp[$bar['idkomponen']] || 3 == $bar['idkomponen'] || 35 == $comp[$bar['idkomponen']] || 35 == $bar['idkomponen'] || 36 == $comp[$bar['idkomponen']] || 36 == $bar['idkomponen'] || 37 == $comp[$bar['idkomponen']] || 37 == $bar['idkomponen'] || 38 == $comp[$bar['idkomponen']] || 38 == $bar['idkomponen'] || 39 == $comp[$bar['idkomponen']] || 39 == $bar['idkomponen'] || 40 == $comp[$bar['idkomponen']] || 40 == $bar['idkomponen'] || 40 == $comp[$bar['idkomponen']] || 41 == $bar['idkomponen'] || 38 == $comp[$bar['idkomponen']] || 41 == $bar['idkomponen'] || 42 == $comp[$bar['idkomponen']] || 42 == $bar['idkomponen'] || 43 == $comp[$bar['idkomponen']] || 43 == $bar['idkomponen'] || 44 == $comp[$bar['idkomponen']] || 44 == $bar['idkomponen'] || 45 == $comp[$bar['idkomponen']] || 45 == $bar['idkomponen'] || 46 == $comp[$bar['idkomponen']] || 46 == $bar['idkomponen'] || 47 == $comp[$bar['idkomponen']] || 47 == $bar['idkomponen'] || 48 == $comp[$bar['idkomponen']] || 48 == $bar['idkomponen'] || 49 == $comp[$bar['idkomponen']] || 49 == $bar['idkomponen'] || 50 == $comp[$bar['idkomponen']] || 50 == $bar['idkomponen'] || 51 == $comp[$bar['idkomponen']] || 51 == $bar['idkomponen']) {
                    $tunjgol[$val[0]] += $bar['jumlah'];
                }

                if (16 == $comp[$bar['idkomponen']] || 16 == $bar['idkomponen']) {
                    $tunjpremi[$val[0]] += $bar['jumlah'];
                }

                if (63 == $comp[$bar['idkomponen']] || 63 == $bar['idkomponen']) {
                    $tunjkom[$val[0]] += $bar['jumlah'];
                }

                if (58 == $comp[$bar['idkomponen']] || 58 == $bar['idkomponen']) {
                    $tunjlok[$val[0]] += $bar['jumlah'];
                }

                if (59 == $comp[$bar['idkomponen']] || 59 == $bar['idkomponen']) {
                    $tunjprt[$val[0]] += $bar['jumlah'];
                }

                if (61 == $comp[$bar['idkomponen']] || 61 == $bar['idkomponen']) {
                    $tunjbbm[$val[0]] += $bar['jumlah'];
                }

                if (65 == $comp[$bar['idkomponen']] || 65 == $bar['idkomponen']) {
                    $tunjair[$val[0]] += $bar['jumlah'];
                }

                if (60 == $comp[$bar['idkomponen']] || 60 == $bar['idkomponen']) {
                    $tunjsprpart[$val[0]] += $bar['jumlah'];
                }

                if (21 == $comp[$bar['idkomponen']] || 21 == $bar['idkomponen']) {
                    $tunjharian[$val[0]] += $bar['jumlah'];
                }

                if (23 == $comp[$bar['idkomponen']] || 23 == $bar['idkomponen']) {
                    $tunjdinas[$val[0]] += $bar['jumlah'];
                }

                if (12 == $comp[$bar['idkomponen']] || 12 == $bar['idkomponen']) {
                    $tunjcuti[$val[0]] += $bar['jumlah'];
                }

                if (62 == $comp[$bar['idkomponen']] || 62 == $bar['idkomponen']) {
                    $tunjlist[$val[0]] += $bar['jumlah'];
                }

                if (22 == $comp[$bar['idkomponen']] || 22 == $bar['idkomponen']) {
                    $tunjlain[$val[0]] += $bar['jumlah'];
                }

                if (54 == $comp[$bar['idkomponen']] || 54 == $bar['idkomponen']) {
                    $tunjrapel[$val[0]] += $bar['jumlah'];
                }

                if (2 == $bar['idkomponen']) {
                    $tunjab[$val[0]] += $bar['jumlah'];
                }

                $totuptetap[$val[0]] = $gapok[$val[0]] + $tunjab[$val[0]] + $gapoktunj[$val[0]] + $tunjgol[$val[0]] + $tunjnat[$val[0]] + $tunjmasker[$val[0]];
                $totgross[$val[0]] = $totuptetap[$val[0]] + $tunjlembur[$val[0]] + $tunjpremi[$val[0]] + $tunjkom[$val[0]] + $tunjlok[$val[0]] + $tunjprt[$val[0]] + $tunjbbm[$val[0]] + $tunjair[$val[0]] + $tunjsprpart[$val[0]] + $tunjharian[$val[0]] + $tunjdinas[$val[0]] + $tunjcuti[$val[0]] + $tunjlist[$val[0]] + $tunjlain[$val[0]] + $tunjrapel[$val[0]] + $jkk[$val[0]] + $jkm[$val[0]] + $bpjspt[$val[0]];
            }

            


        }
    }


    $listbutton = '<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>';
    $list0 = "<table class=sortable border=0 cellspacing=1>\r\n                     <thead>\r\n            <tr class=rowheader align=center>";
    $list0 .= '<td >'.$_SESSION['lang']['nomor'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['periodegaji'].'</td>';
    $list0 .= '<td>ID Karyawan</td>';
    $list0 .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['functionname'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['status'].'</td>';
    $list0 .= '<td>'.$_SESSION['lang']['kodegolongan'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['gajipokok'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['tunjgol'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['tjjabatan'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['naturapekerja'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['naturakeluarga'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold">'.$_SESSION['lang']['tunjanganmasakerja'].'</td>';
    $list0 .= '<td style="background-color:yellow;font-weight:bold"><b>'.$_SESSION['lang']['totalupahtetap'].'</b></td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['lembur2'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Premi BKM</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">Premi Pendapatan Lainnya</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjangankomunikasi'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganlokasi'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganrrumhtngg'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganbbm'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganairminum'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjangansparepart'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganharian'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjangandinas'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjangancuti'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganlistrik'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['tunjanganlain'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold">'.$_SESSION['lang']['rapelkenaikan'].'</td>';
    $list0 .= '<td style="background-color:lightblue;font-weight:bold"><b>'.$_SESSION['lang']['gross'].'</b></td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold">'.$_SESSION['lang']['jkk'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold">'.$_SESSION['lang']['jkm'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold">'.$_SESSION['lang']['bpjskes'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['totalgajibruto'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['biayajabatan'].'</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['jhtkary'].'</td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold">'.$_SESSION['lang']['jpkary'].'</td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['gjnettosebulan'].'</b></td>';
    $list0 .= '<td style="background-color:lightgrey;font-weight:bold"><b>'.$_SESSION['lang']['gjnettosetahun'].'</b></td>';
    $list0 .= '<td style="background-color:grey;font-weight:bold">'.$_SESSION['lang']['ptkp'].'</td>';
    $list0 .= '<td style="background-color:grey;font-weight:bold">'.$_SESSION['lang']['pkp'].'</td>';
    $list0 .= '<td style="background-color:lightcyan;font-weight:bold">'.$_SESSION['lang']['pph21'].'</td>';
    $list0 .= '<td style="background-color:cyan;font-weight:bold"><b>'.$_SESSION['lang']['thpbruto'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['potkoperasi'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['vop'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['potmotor'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['potlaptop'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['potdenda'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>Denda Panen</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['jhtkary'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['jpkary'].'</b></td>';
    $list0 .= '<td style="background-color:#efc6b1;font-weight:bold"><b>'.$_SESSION['lang']['bpjskes'].'</b></td>';
    $list0 .= '<td style="background-color:#55fc7f;font-weight:bold"><b>'.$_SESSION['lang']['thpnetto'].'</b></td></tr></thead><tbody>';
    $negatif = false;
    $list1 = '';
    $listx = 'Masih ada gaji dibawah 0:';
    $list2 = '';
    $list3 = '';
    $no = 0;
    $strsl = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=16";
    $slRes = fetchData($strsl);
    foreach ($slRes as $key => $val) {
        $premPengawas[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjLbr = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen =17";
    $resTunjLbr = fetchData($qTunjLbr);
    foreach ($resTunjLbr as $key => $val) {
        $tunjlembur[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen =63";
    $resTunj = fetchData($qTunjL);
    foreach ($resTunj as $key => $val) {
        $tunjkom[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjLok = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=58";
    $resTunjLok = fetchData($qTunjLok);
    foreach ($resTunjLok as $key => $val) {
        $tunjlok[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjRT = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=59";
    $resTunjRT = fetchData($qTunjRT);
    foreach ($resTunjRT as $key => $val) {
        $tunjrt[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjB = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=61";
    $resTunjB = fetchData($qTunjB);
    foreach ($resTunjB as $key => $val) {
        $tunjbbm[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjAM = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=65";
    $resTunjAM = fetchData($qTunjAM);
    foreach ($resTunjAM as $key => $val) {
        $tunjairminum[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjSP = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=60";
    $resTunjSP = fetchData($qTunjSP);
    foreach ($resTunjSP as $key => $val) {
        $tunjSP[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjH = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=21";
    $resTunjH = fetchData($qTunjH);
    foreach ($resTunjH as $key => $val) {
        $tunjharian[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjD = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=23";
    $resTunjD = fetchData($qTunjD);
    foreach ($resTunjD as $key => $val) {
        $tunjdinas[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjC = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=12";
    $resTunjC = fetchData($qTunjC);
    foreach ($resTunjC as $key => $val) {
        $tunjcuti[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=62";
    $resTunjL = fetchData($qTunjL);
    foreach ($resTunjL as $key => $val) {
        $tunjlistrik[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjLL = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=22";
    $resTunjLL = fetchData($qTunjLL);
    foreach ($resTunjLL as $key => $val) {
        $tunjlain[$val['karyawanid']] = $val['jumlah'];
    }
    $qTunjRU = 'select karyawanid,jumlah from '.$dbname.".sdm_gaji where periodegaji='".$param['periodegaji']."'\r\n         and kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and idkomponen=54";
    $resTunjRU = fetchData($qTunjRU);
    foreach ($resTunjRU as $key => $val) {
        $rapelupah[$val['karyawanid']] = $val['jumlah'];
    }
    foreach ($id as $key => $val) {
        $qJab = 'select namajabatan from '.$dbname.".sdm_5jabatan where kodejabatan='".$kdjabatan[$val[0]]."'";
        $res = mysql_query($qJab);
        while ($bar = mysql_fetch_object($res)) {
            $nmjabatan = $bar->namajabatan;
        }
        $qTipe = 'select tipe from '.$dbname.".sdm_5tipekaryawan where id='".$tipekar[$val[0]]."'";
        $resT = mysql_query($qTipe);
        while ($barT = mysql_fetch_object($resT)) {
            $namatipe = $barT->tipe;
        }
        $qGol = 'select namagolongan from '.$dbname.".sdm_5golongan where kodegolongan='".$kdgol[$val[0]]."'";
        $resG = mysql_query($qGol);
        while ($barG = mysql_fetch_object($resG)) {
            $namagolongan = $barG->namagolongan;
        }
        $totgross[$val[0]] = $totuptetap[$val[0]] + $tunjlembur[$val[0]] + $tunjpremi[$val[0]] + $premPengawas[$val[0]] + $tunjkom[$val[0]] + $tunjlok[$val[0]] + $tunjrt[$val[0]] + $tunjbbm[$val[0]] + $tunjairminum[$val[0]] + $tunjSP[$val[0]] + $tunjharian[$val[0]] + $tunjdinas[$val[0]] + $tunjcuti[$val[0]] + $tunjlistrik[$val[0]] + $tunjlain[$val[0]] + $rapelupah[$val[0]];
        $totgajibruto[$val[0]] = $totgross[$val[0]] + $jkk[$val[0]] + $jkm[$val[0]] + $bpjspt[$val[0]];
        $gjnettosebulan[$val[0]] = $totgajibruto[$val[0]] - $biayajab[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]];
        $gjnettosetahun[$val[0]] = $gjnettosebulan[$val[0]] * 12;
        $ptkp[$val[0]] = $ptkp[str_replace('K', '', $statuspajak[$val[0]])];
        $pkp[$val[0]] = floor(($gjnettosetahun[$val[0]] - $ptkp[$val[0]]) / 1000) * 1000;
        $zz = 0;
        $sisazz = 0;
        if (0 < $pkp[$val[0]]) {
            if ($pkp[$val[0]] < $pphtarif[0]) {
                $zz += $pphpercent[0] * $pkp[$val[0]];
                $sisazz = 0;
            } else {
                if ($pphtarif[0] <= $pkp[$val[0]]) {
                    $zz += $pphpercent[0] * $pphtarif[0];
                    $sisazz = $pkp[$val[0]] - $pphtarif[0];
                    if ($sisazz < $pphtarif[1] - $pphtarif[0]) {
                        $zz += $pphpercent[1] * $sisazz;
                        $sisazz = 0;
                    } else {
                        if ($pphtarif[1] - $pphtarif[0] <= $sisazz) {
                            $zz += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
                            $sisazz = $pkp[$val[0]] - $pphtarif[1];
                            if ($sisazz < $pphtarif[2] - $pphtarif[1]) {
                                $zz += $pphpercent[2] * $sisazz;
                                $sisazz = 0;
                            } else {
                                if ($pphtarif[2] - $pphtarif[1] <= $sisazz) {
                                    $zz += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
                                    $sisazz = $pkp[$val[0]] - $pphtarif[2];
                                    if (0 < $sisazz) {
                                        $zz += $pphpercent[3] * $sisazz;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $pphSetahun[$val[0]] = round($zz / 12);
        if ('' == $npwp[$val[0]]) {
            $pphSetahun[$val[0]] = ceil($pphSetahun[$val[0]] + ($pphSetahun[$val[0]] * 20) / 100);
        }

        foreach ($pphSetahun as $idx => $row) {
            if (0 < $row) {
                $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 24, 'jumlah' => round($row, 2), 'pengali' => 1, 'updateby' => $_SESSION['standard']['userid'], 'updatetime' => date('Y-m-d H:i:s')];
            }
        }
        $thpbruto[$val[0]] = $totgross[$val[0]] - $pphSetahun[$val[0]];
        $thpnetto[$val[0]] = $thpbruto[$val[0]] - $potongankoperasi[$val[0]] - $potonganvop[$val[0]] - $potonganmotor[$val[0]] - $potonganlaptop[$val[0]] - $potongandenda[$val[0]] - $potdendapanen[$val[0]] - $jhtkarypersen[$val[0]] - $jpkarypersen[$val[0]] - $potonganbpjskes[$val[0]];
        $sisa[$val[0]] = 0;
        foreach ($readyData as $dat => $bar) {
            if ($val[0] == $bar['karyawanid']) {
            }

            continue;
        }
        $sisa[$val[0]] += $premPengawas[$val[0]];
        if ($sisa[$val[0]] < 0) {
            $list1 .= '<tr class=rowcontent>';
            $list1 .= '<td>-</td>';
            $list1 .= '<td>'.$param['periodegaji'].'</td>';
            $list1 .= '<td>'.$namakar[$val[0]].'</td>';
            $list1 .= '<td>'.$nmjabatan.'</td>';
            $list1 .= '<td>'.$namatipe.'</td>';
            $list1 .= '<td>'.$namagolongan.'</td>';
            $list1 .= '<td>'.number_format($gapok[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjgol[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjab[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjnat[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjmasker[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($totuptetap[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjkom[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjlok[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjrt[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjbbm[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjairminum[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjSP[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjharian[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjdinas[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjcuti[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjlistrik[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($tunjlain[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($rapelupah[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($totgross[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($biayajab[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($jkk[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($jkm[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($pph21[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td>'.number_format($gapoktunj[$val[0]], 0, ',', '.').'</td>';
            $list1 .= '<td><b>'.number_format($sisa[$val[0]], 0, ',', '.').'</b></td></tr>';
            $negatif = true;
        } else {
            ++$no;
            $list2 .= '<tr class=rowcontent>';
            $list2 .= '<td>'.$no.'</td>';
            $list2 .= '<td>'.$param['periodegaji'].'</td>';
            $list2 .= '<td>'.$id[$val[0]][0].'</td>';
            $list2 .= '<td>'.$namakar[$val[0]].'</td>';
            $list2 .= '<td align=left>'.$nmjabatan.'</td>';
            $list2 .= '<td align=left>'.$namatipe.'</td>';
            $list2 .= '<td align=center>'.$namagolongan.'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($gapok[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjgol[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjab[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjnat[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($gapoktunj[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow">'.number_format($tunjmasker[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:yellow"><b>'.number_format($totuptetap[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlembur[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjpremi[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($premPengawas[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjkom[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlok[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjrt[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjbbm[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjairminum[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjSP[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjharian[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjdinas[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjcuti[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlistrik[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($tunjlain[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue">'.number_format($rapelupah[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightblue"><b>'.number_format($totgross[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:lightgrey">'.number_format($jkk[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey">'.number_format($jkm[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey">'.number_format($bpjspt[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey"><b>'.number_format($totgajibruto[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($biayajab[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:#efc6b1">'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightgrey"><b>'.number_format($gjnettosebulan[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right align=right style="background-color:lightgrey"><b>'.number_format($gjnettosetahun[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:grey">'.number_format($ptkp[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:grey">'.number_format($pkp[$val[0]], 0, ',', '.').'</td>';
            $list2 .= '<td align=right style="background-color:lightcyan"><b>'.number_format($pphSetahun[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:cyan"><b>'.number_format($thpbruto[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potongankoperasi[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganvop[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganmotor[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganlaptop[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potongandenda[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potdendapanen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#efc6b1"><b>'.number_format($potonganbpjskes[$val[0]], 0, ',', '.').'</b></td>';
            $list2 .= '<td align=right style="background-color:#55fc7f"><b>'.number_format($thpnetto[$val[0]], 0, ',', '.').'</b></td></tr>';
        }

        $gapok2 += $gapok[$val[0]];
        $tunjgol2 += $tunjgol[$val[0]];
        $tunjab2 += $tunjab[$val[0]];
        $tunjnat2 += $tunjnat[$val[0]];
        $tunjmasker2 += $tunjmasker[$val[0]];
        $gapoktunj2 += $gapoktunj[$val[0]];
        $totuptetap2 += $totuptetap[$val[0]];
        $tunjlembur2 += $tunjlembur[$val[0]];
        $tunjpremi2 += $tunjpremi[$val[0]];
        $tunjprt2 += $tunjprt[$val[0]];
        $tunjrapel2 += $tunjrapel[$val[0]];
        $premPengawas2 += $premPengawas[$val[0]];
        $tunjkom2 += $tunjkom[$val[0]];
        $tunjlok2 += $tunjlok[$val[0]];
        $tunjrt2 += $tunjrt[$val[0]];
        $tunjbbm2 += $tunjbbm[$val[0]];
        $tunjairminum2 += $tunjairminum[$val[0]];
        $tunjSP2 += $tunjSP[$val[0]];
        $tunjharian2 += $tunjharian[$val[0]];
        $tunjdinas2 += $tunjdinas[$val[0]];
        $tunjcuti2 += $tunjcuti[$val[0]];
        $tunjlistrik2 += $tunjlistrik[$val[0]];
        $tunjlain2 += $tunjlain[$val[0]];
        $rapelupah2 += $rapelupah[$val[0]];
        $totgross2 += $totgross[$val[0]];
        $jkk2 += $jkk[$val[0]];
        $jkm2 += $jkm[$val[0]];
        $bpjspt2 += $bpjspt[$val[0]];
        $totgajibruto2 += $totgajibruto[$val[0]];
        $biayajab2 += $biayajab[$val[0]];
        $jhtkarypersen2 += $jhtkarypersen[$val[0]];
        $jpkarypersen2 += $jpkarypersen[$val[0]];
        $gjnettosebulan2 += $gjnettosebulan[$val[0]];
        $gjnettosetahun2 += $gjnettosetahun[$val[0]];
        $ptkp2 = 0;
        $pkp2 = 0;
        $pphSetahun212 += $pphSetahun[$val[0]];
        $thpbruto2 += $thpbruto[$val[0]];
        $potongankoperasi2 += $potongankoperasi[$val[0]];
        $potonganvop2 += $potonganvop[$val[0]];
        $potonganmotor2 += $potonganmotor[$val[0]];
        $potonganlaptop2 += $potonganlaptop[$val[0]];
        $potongandenda2 += $potongandenda[$val[0]];
        $potdendapanen2 += $potdendapanen[$val[0]];
        $potonganbpjskes2 += $potonganbpjskes[$val[0]];
        $thpnetto2 += $thpnetto[$val[0]];
    }
    $list3 = '<tr class=rowcontent style="font-size:12pt"><td align=center style="background-color:grey;font-size:12pt" colspan=7><b>'.$_SESSION['lang']['total']."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($gapok2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($tunjgol2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($tunjab2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($tunjnat2, 0, ',', '.')."</b></td>\r\n                \r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($gapoktunj2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($tunjmasker2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:yellow\"><b>".number_format($totuptetap2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlembur2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjpremi2, 0, ',', '.')."</b></td>\r\n <td align=right style=\"background-color:lightblue\"><b>".number_format($premPengawas2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjkom2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlok2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjrt2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjbbm2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjairminum2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjSP2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjharian2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjdinas2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjcuti2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlistrik2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($tunjlain2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($rapelupah2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightblue\"><b>".number_format($totgross2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($jkk2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($jkm2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($bpjspt2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($totgajibruto2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($biayajab2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($jhtkarypersen2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($jpkarypersen2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($gjnettosebulan2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightgrey\"><b>".number_format($gjnettosetahun2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:grey\"><b>".number_format($ptkp2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:grey\"><b>".number_format($pkp2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:lightcyan\"><b>".number_format($pphSetahun212, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:cyan\"><b>".number_format($thpbruto2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potongankoperasi2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganvop2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganmotor2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganlaptop2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potongandenda2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potdendapanen2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($jhtkarypersen2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($jpkarypersen2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#efc6b1\"><b>".number_format($potonganbpjskes2, 0, ',', '.')."</b></td>\r\n                <td align=right style=\"background-color:#55fc7f\"><b>".number_format($thpnetto2, 0, ',', '.')."</b></td>\r\n               </tr></tbody><table>";
    switch ($proses) {
        case 'list':
        if ($negatif) {
            echo $listx.$list0.$list1.$list3;
        } else {
            echo $listbutton.$list0.$list2.$list3;
        }

        break;
        case 'post':
        $insError = '';
        foreach ($readyData as $row) {
            if (0 == $row['jumlah'] || '' == $row['jumlah']) {
                continue;
            }

            $queryIns = insertQuery($dbname, 'sdm_gaji', $row);
            if (!mysql_query($queryIns)) {
                $queryUpd = updateQuery($dbname, 'sdm_gaji', $row, "kodeorg='".$row['kodeorg']."' and periodegaji='".$row['periodegaji']."' and karyawanid='".$row['karyawanid']."' and idkomponen=".$row['idkomponen']);
                $tmpErr = mysql_error();
                if (!mysql_query($queryUpd)) {
                    echo 'DB Insert Error :'.$tmpErr."\n";
                    echo 'DB Update Error :'.mysql_error()."\n";
                }
            }
        }

        break;
        default:
        break;
    }
} else {
    exit("You're Not Authorized To Run This Process.");
}

// echo $tunjpremi['1000000077'];
?>