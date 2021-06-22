<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$proses = $_GET['proses'];
$param = $_POST;
$namakar = [];
$sCekPeriode = 'select distinct * from '.$dbname.".sdm_5periodegaji where periode='".$param['periodegaji']."'\r\n              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses=1 and jenisgaji='B'";
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

$qPeriod = selectQuery($dbname, 'sdm_5periodegaji', 'tanggalmulai,tanggalsampai', "periode='".$param['periodegaji']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."' and jenisgaji='B'");
$resPeriod = fetchData($qPeriod);
$tanggal1 = $resPeriod[0]['tanggalmulai'];
$tanggal2 = $resPeriod[0]['tanggalsampai'];
$query1 = selectQuery($dbname, 'datakaryawan', 'karyawanid,namakaryawan,jms,statuspajak,npwp', 'tipekaryawan in(1,2,3,4,5,6) and '."kodeorganisasi='".$_SESSION['empl']['induklokasitugas']."' and "."(tanggalkeluar>='".$tanggal1."' or tanggalkeluar is NULL) and alokasi in (0,1) and sistemgaji='Bulanan'"." and ( tanggalmasuk<='".$tanggal2."' or tanggalmasuk='0000-00-00' or tanggalmasuk is null) and karyawanid not in (0999999999,0888888888)");
$absRes = fetchData($query1);
if (empty($absRes)) {
    echo 'Error : No Presence(Kehadiran) On this Payroll Period';
    exit();
}

$id = [];
foreach ($absRes as $row => $kar) {
    $id[$kar['karyawanid']][] = $kar['karyawanid'];
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $nojms[$kar['karyawanid']] = trim($kar['jms']);
    $statuspajak[$kar['karyawanid']] = trim($kar['statuspajak']);
    $npwp[$kar['karyawanid']] = trim($kar['npwp']);
}
$strgjh = 'select a.karyawanid,sum(jumlah)/25 as gjperhari from '.$dbname.".sdm_5gajipokok a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where a.tahun=".substr($tanggal1, 0, 4)." and b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL) and b.alokasi in(0,1)\r\n               and a.idkomponen in(1,2,29,30,31,32,33) and sistemgaji='Bulanan' and b.karyawanid not in (0999999999,0888888888)\r\n               group by a.karyawanid";
$resgjh = fetchData($strgjh);
foreach ($resgjh as $idx => $val) {
    $gajiperhari[$val['karyawanid']] = $val['gjperhari'];
}
$strgjh = 'select  count(*) as jlh,b.karyawanid from '.$dbname.".sdm_hktdkdibayar_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal2."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan'\r\n               group by a.karyawanid";
$tdkdibayar = [];
$resgjh = fetchData($strgjh);
foreach ($resgjh as $idx => $val) {
    $tdkdibayar[$val['karyawanid']] = $gajiperhari[$val['karyawanid']] * $val['jlh'];
    $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => 20, 'jumlah' => $tdkdibayar[$val['karyawanid']], 'pengali' => 1];
}
$str1 = 'select a.*,b.namakaryawan,b.tipekaryawan from '.$dbname.".sdm_5gajipokok a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where a.tahun=".substr($tanggal1, 0, 4)." and b.tipekaryawan in(1,2,3,4,5,6) and b.kodeorganisasi='".$_SESSION['empl']['induklokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0,1)\r\n               and sistemgaji='Bulanan'";
$res1 = fetchData($str1);
$query6 = selectQuery($dbname, 'sdm_ho_hr_jms_porsi', '*', "id='karyawan'");
$jmsRes = fetchData($query6);
$persenJms = $jmsRes[0]['value'] / 100;
$persenjhtkar = $jmsRes[0]['jhtkar'] / 100;
$persenjpkar = $jmsRes[0]['jpkar'] / 100;
$tjms = [];
$tipekaryawan = [];
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
                        if ('5' == $val['tipekaryawan']) {
                            $tipekaryawan[$val['karyawanid']] = 'STAFF';
                        } else {
                            $tipekaryawan[$val['karyawanid']] = 'PKWT';
                        }
                    }
                }
            }
        }

        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $val['karyawanid'], 'idkomponen' => $val['idkomponen'], 'jumlah' => $val['jumlah'], 'pengali' => 1];
        if ((1 == $val['idkomponen'] || 2 == $val['idkomponen'] || 29 == $val['idkomponen'] || 30 == $val['idkomponen'] || 32 == $val['idkomponen'] || 33 == $val['idkomponen']) && '' != $nojms[$val['karyawanid']]) {
            $tjms[$val['karyawanid']] += $val['jumlah'];
        }
    }
}
foreach ($tjms as $key => $nilai) {
    if ('BHL' == $tipekaryawan[$key] || 'ORGANIK' == $tipekaryawan[$key] || 'SKU' == $tipekaryawan[$key] || 'SKUP' == $tipekaryawan[$key] || 'STAFF' == $tipekaryawan[$key] || 'PKWT' == $tipekaryawan[$key]) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 5, 'jumlah' => $nilai * $persenjhtkar, 'pengali' => 1];
        $nilaibatasmax = 7703500;
        if ($nilaibatasmax < $nilai) {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilaibatasmax * $persenjpkar, 'pengali' => 1];
        } else {
            $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $key, 'idkomponen' => 9, 'jumlah' => $nilai * $persenjpkar, 'pengali' => 1];
        }
    }
}
$where2 = " a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and (tanggal>='".$tanggal1."' and tanggal<='".$tanggal2."')";
$query2 = 'select a.karyawanid,sum(a.uangkelebihanjam) as lembur from '.$dbname.".sdm_lemburdt a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,5,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0,1)\r\n               and sistemgaji='Bulanan'\r\n               and ".$where2.' group by a.karyawanid';
$lbrRes = fetchData($query2);
foreach ($lbrRes as $idx => $row) {
    if (isset($id[$row['karyawanid']])) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 17, 'jumlah' => $row['lembur'], 'pengali' => 1];
    }
}
$where3 = " kodeorg='".$_SESSION['empl']['lokasitugas']."' and periodegaji='".$param['periodegaji']."'";
$query3 = 'select a.nik as karyawanid,sum(jumlahpotongan) as potongan from '.$dbname.".sdm_potongandt a left join\r\n              ".$dbname.".datakaryawan b on a.nik=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,5,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0,1)\r\n               and sistemgaji='Bulanan'\r\n               and ".$where3.' group by a.nik';
$potRes = fetchData($query3);
foreach ($potRes as $idx => $row) {
    if (isset($id[$row['karyawanid']])) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => 27, 'jumlah' => $row['potongan'], 'pengali' => 1];
    }
}
$where4 = " start<='".$param['periodegaji']."' and end>='".$param['periodegaji']."'";
$query4 = 'select a.karyawanid,a.bulanan,a.jenis from '.$dbname.".sdm_angsuran a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,3,4,5,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi in(0,1)\r\n               and a.active=1\r\n               and sistemgaji='Bulanan'\r\n               and ".$where4;
$angRes = fetchData($query4);
foreach ($angRes as $idx => $row) {
    if ($id[$row['karyawanid']][0] == $row['karyawanid']) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $row['karyawanid'], 'idkomponen' => $row['jenis'], 'jumlah' => $row['bulanan'], 'pengali' => 1];
    }
}
$stru1 = 'select distinct(tanggal) from '.$dbname.".kebun_kehadiran_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan' order by tanggal";
$resu1 = mysql_query($stru1);
$stru2 = 'select distinct(tanggal) from '.$dbname.".kebun_prestasi_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%' and a.jurnal=0\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan' order by tanggal";
$resu2 = mysql_query($stru2);
$stru3 = "select distinct(tanggal)\r\n           from ".$dbname.".vhc_runhk_vw a left join\r\n          ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n           where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n           and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n           and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n           and posting=0 and sistemgaji='Bulanan' order by tanggal";
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
$query5 = 'select a.karyawanid,sum(a.insentif) as premi from '.$dbname.".kebun_kehadiran_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan'\r\n               group by a.karyawanid";
$premRes = fetchData($query5);
foreach ($premRes as $idx => $val) {
    if (0 < $val['premi']) {
        $premi[$val['karyawanid']] = $val['premi'];
    }
}
$query6 = "select a.karyawanid,sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty\r\n               from ".$dbname.".kebun_prestasi_vw a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.unit like '".$_SESSION['empl']['lokasitugas']."%'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan'\r\n               group by a.karyawanid";
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
$query7 = "select a.idkaryawan as karyawanid,sum(a.premi+a.premiluarjam) as premi,sum(a.penalty) as penalty\r\n               from ".$dbname.".vhc_runhk_vw a left join\r\n              ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and substr(a.notransaksi,1,4)='".$_SESSION['empl']['lokasitugas']."'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."'\r\n               and sistemgaji='Bulanan'\r\n               group by a.idkaryawan";
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
$query8 = "select sum(a.premiinput) as premi,a.karyawanid\r\n               from ".$dbname.".kebun_premikemandoran a left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n\t\t\t    and a.periode like  '%".$param['periodegaji']."%'\r\n\r\n               and b.sistemgaji='Bulanan'  and a.posting=1\r\n               group by a.karyawanid";
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
$stkh = 'select a.karyawanid,sum(a.premi+a.insentif) as premi from '.$dbname.".sdm_absensidt a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.tipekaryawan in(1,2,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Bulanan'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
$reskh = mysql_query($stkh);
while ($barky = mysql_fetch_object($reskh)) {
    if (isset($premi[$barky->karyawanid])) {
        $premi[$barky->karyawanid] += $barky->premi;
    } else {
        $premi[$barky->karyawanid] = $barky->premi;
    }
}
$stkh1 = 'select a.karyawanid,b.rupiahpremi  from '.$dbname.".kebun_premipanen a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.tipekaryawan in(1,2,6)  and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%'\r\n\r\n\t\t\t    and a.periode like  '%".$param['periodegaji']."%' group by a.karyawanid";
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
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 12, 'jumlah' => $row, 'pengali' => 1];
    }
}
foreach ($penalty as $idx => $row) {
    if (0 < $row) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 18, 'jumlah' => $row, 'pengali' => 1];
    }
}
$stkh = 'select a.karyawanid,sum(a.penaltykehadiran) as penaltykehadiran from '.$dbname.".sdm_absensidt a\r\n                left join\r\n              ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                          where b.tipekaryawan in(1,2,6) and b.lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n               and  (b.tanggalkeluar>='".$tanggal1."' or b.tanggalkeluar is NULL) and b.alokasi=0\r\n               and a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and sistemgaji='Bulanan'\r\n               and a.tanggal>='".$tanggal1."' and a.tanggal<='".$tanggal2."' group by a.karyawanid";
$reskh = mysql_query($stkh);
while ($barkh = mysql_fetch_object($reskh)) {
    if (0 < $barkh->penaltykehadiran) {
        $penaltykehadiran[$barkh->karyawanid] = $barkh->penaltykehadiran;
    }
}
foreach ($penaltykehadiran as $idx => $row) {
    if (0 < $row) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 41, 'jumlah' => $row, 'pengali' => 1];
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
$jabPersen = 0;
$jabMax = 0;
$str = 'select persen,max from '.$dbname.'.sdm_ho_pph21jabatan';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jabPersen = $bar->persen / 100;
    $jabMax = $bar->max * 12;
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
    foreach ($readyData as $dat => $bar) {
        if ($val[0] == $bar['karyawanid'] && (1 == $comp[$bar['idkomponen']] || 20 == $bar['idkomponen'])) {
            $penghasilan[$val[0]] += $bar['jumlah'];
        }
    }
}
foreach ($penghasilan as $xid => $jlh) {
    $penghasilanSetahun[$xid] = $jlh * 12;
    $biayaJab[$xid] = $penghasilanSetahun[$xid] * $jabPersen;
    if ($jabMax < $biayaJab[$xid]) {
        $biayaJab[$xid] = $jabMax;
    }

    $penghasilanKurangJab[$xid] = $penghasilanSetahun[$xid] - $biayaJab[$xid];
    $pkp[$xid] = $penghasilanKurangJab[$xid] - $ptkp[str_replace('K', '', $statuspajak[$xid])];
    $zz = 0;
    $sisazz = 0;
    if (0 < $pkp[$xid]) {
        if ($pkp[$xid] < $pphtarif[0]) {
            $zz += $pphpercent[0] * $pkp[$xid];
            $sisazz = 0;
        } else {
            if ($pphtarif[0] <= $pkp[$xid]) {
                $zz += $pphpercent[0] * $pphtarif[0];
                $sisazz = $pkp[$xid] - $pphtarif[0];
                if ($sisazz < $pphtarif[1] - $pphtarif[0]) {
                    $zz += $pphpercent[1] * $sisazz;
                    $sisazz = 0;
                } else {
                    if ($pphtarif[1] - $pphtarif[0] <= $sisazz) {
                        $zz += $pphpercent[1] * ($pphtarif[1] - $pphtarif[0]);
                        $sisazz = $pkp[$xid] - $pphtarif[1];
                        if ($sisazz < $pphtarif[2] - $pphtarif[1]) {
                            $zz += $pphpercent[2] * $sisazz;
                            $sisazz = 0;
                        } else {
                            if ($pphtarif[2] - $pphtarif[1] <= $sisazz) {
                                $zz += $pphpercent[2] * ($pphtarif[2] - $pphtarif[1]);
                                $sisazz = $pkp[$xid] - $pphtarif[2];
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

    $pphSetahun[$xid] = $zz / 12;
    if ('' == $npwp[$xid]) {
        $pphSetahun[$xid] = $pphSetahun[$xid] + ($pphSetahun[$xid] * 20) / 100;
    }
}
foreach ($pkp as $idx => $row) {
    if (0 < $row) {
        $readyData[] = ['kodeorg' => $_SESSION['empl']['lokasitugas'], 'periodegaji' => $param['periodegaji'], 'karyawanid' => $idx, 'idkomponen' => 24, 'jumlah' => $row, 'pengali' => 1];
    }
}
$listbutton = '<button class=mybuttton name=postBtn id=postBtn onclick=post()>Proses</button>';
$list0 = "<table class=sortable border=0 cellspacing=1>\r\n                     <thead>\r\n                     <tr class=rowheader>";
$list0 .= '<td>'.$_SESSION['lang']['nomor'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['periodegaji'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['namakaryawan'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['jhtkary'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['jpkary'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['gapoktunj'].'</td>';
$list0 .= '<td>'.$_SESSION['lang']['jumlah'].'</td></tr></thead><tbody>';
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
foreach ($id as $key => $val) {
    $sisa[$val[0]] = 0;
    foreach ($readyData as $dat => $bar) {
        if ($val[0] == $bar['karyawanid']) {
            $sisa[$val[0]] += $bar['jumlah'] * $comp[$bar['idkomponen']];
        }

        continue;
    }
    $sisa[$val[0]] += $premPengawas[$val[0]];
    if ($sisa[$val[0]] < 0) {
        $list1 .= '<tr class=rowcontent>';
        $list1 .= '<td>-</td>';
        $list1 .= '<td>'.$param['periodegaji'].'</td>';
        $list1 .= '<td>'.$namakar[$val[0]].'</td>';
        $list1 .= '<td>'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</td>';
        $list1 .= '<td>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
        $list1 .= '<td>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
        $list1 .= '<td>'.number_format($gapoktunj[$val[0]], 0, ',', '.').'</td>';
        $list1 .= '<td><b>'.number_format($sisa[$val[0]], 0, ',', '.').'</b></td></tr>';
        $negatif = true;
    } else {
        ++$no;
        $list2 .= '<tr class=rowcontent>';
        $list2 .= '<td>'.$no.'</td>';
        $list2 .= '<td>'.$param['periodegaji'].'</td>';
        $list2 .= '<td>'.$namakar[$val[0]].'</td>';
        $list2 .= '<td align=right>'.number_format($jhtkarypersen[$val[0]], 0, ',', '.').'</td>';
        $list2 .= '<td align=right>'.number_format($jpkarypersen[$val[0]], 0, ',', '.').'</td>';
        $list2 .= '<td align=right>'.number_format($gapoktunj[$val[0]], 0, ',', '.').'</td>';
        $list2 .= '<td align=right><b>'.number_format($sisa[$val[0]], 0, ',', '.').'</b></td></tr>';
    }
}
$list3 = '</tbody><table>';
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

?>