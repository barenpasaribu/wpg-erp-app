<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
('' == $_POST['kdeOrg2'] ? ($kdOrg = $_GET['kdeOrg2']) : ($kdOrg = $_POST['kdeOrg2']));
('' == $_POST['periodThn'] ? ($tgl1 = $_GET['periodThn']) : ($tgl1 = $_POST['periodThn']));
('' == $_POST['periodThnSmp'] ? ($tgl2 = $_GET['periodThnSmp']) : ($tgl2 = $_POST['periodThnSmp']));
('' == $_POST['tipeKary2'] ? ($tipeKary = $_GET['tipeKary2']) : ($tipeKary = $_POST['tipeKary2']));
('' == $_POST['sistemGaji3'] ? ($sistemGaji = $_GET['sistemGaji3']) : ($sistemGaji = $_POST['sistemGaji3']));
('' == $_POST['nilaiMax'] ? ($nilaiMax = $_GET['nilaiMax']) : ($nilaiMax = $_POST['nilaiMax']));
$optTmk = makeOption($dbname, 'datakaryawan', 'karyawanid,tanggalmasuk');
if ('' == $kdOrg) {
    exit('error: Working unit required');
}

if ('' == $tgl1 || '' == $tgl2) {
    exit('error: Both period required');
}

if ('' == $sistemGaji) {
    exit('error: Payment system required');
}

if ('' == $nilaiMax) {
    exit('error: Minimum presence required, type 0 for all');
}

$optDept = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$thn = explode('-', $tgl1);
$thn2 = explode('-', $tgl2);
$blndt1 = (int) ($thn[1]);
$blndt12 = (int) ($thn2[1]);
if ($tgl2 < $tgl1) {
    exit('error: First period must lower');
}

if ($thn[0] != $thn2[0]) {
    for ($mule = $blndt1; $mule < 13; ++$mule) {
        $bulan[] = $mule;
    }
    for ($mule = 1; $mule <= $blndt12; ++$mule) {
        $bulan[] = $mule;
    }
}

$cerk = count($bulan);
if (12 < $cerk) {
    exit('error: Query maximum 12 months, your query is'.$cerk.' moths');
}

$where = "  lokasitugas='".$kdOrg."'";
if ('' != $tipeKary) {
    $where .= " and tipekaryawan='".$tipeKary."'";
    $whrd = "and b.tipekaryawan='".$tipeKary."'";
    $whrc = "and c.tipekaryawan='".$tipeKary."'";
}

if ('All' == $sistemGaji) {
    $wherez = '';
}

if ('Bulanan' == $sistemGaji) {
    $wherez = " and sistemgaji = 'Bulanan'";
}

if ('Harian' == $sistemGaji) {
    $wherez = " and sistemgaji = 'Harian'";
}

$sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan,c.nama,d.tipe from '.$dbname.".datakaryawan a \r\n           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n           left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode\r\n           left join ".$dbname.".sdm_5tipekaryawan d on a.tipekaryawan=d.id\r\n           order by namakaryawan asc";
$rGetkary = fetchData($sGetKary);
foreach ($rGetkary as $row => $kar) {
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
    $nmBagian[$kar['karyawanid']] = $kar['nama'];
    $nmTipe[$kar['karyawanid']] = $kar['tipe'];
}
$bln1 = explode('-', $tgl1);
$bln2 = explode('-', $tgl2);
$resData[] = [];
$hasilAbsn[] = [];
$dimanaPnjng = " kodeorg like '".$kdOrg."%'";
$sAbsn = 'select count(absensi) as total,absensi,a.karyawanid,left(tanggal,7) as periode from '.$dbname.".sdm_absensidt a\r\n                                left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid  where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' \r\n                                and ".$dimanaPnjng."  and a.karyawanid!='' ".$whrd."\r\n                                group by absensi,karyawanid,left(tanggal,7)";
$rAbsn = fetchData($sAbsn);
foreach ($rAbsn as $absnBrs => $resAbsn) {
    if (null != $resAbsn['absensi']) {
        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['periode']][$resAbsn['absensi']] = $resAbsn['total'];
        $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
        $dtPeriode[$resAbsn['periode']] = $resAbsn['periode'];
        $klmpkAbsn[$resAbsn['absensi']] = $resAbsn['absensi'];
    }
}
$sKehadiran = 'select count(absensi) as total,absensi,a.karyawanid,left(tanggal,7) as periode from '.$dbname.".kebun_kehadiran_vw a\r\n                                     left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                                     where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' and substring(unit,1,4)='".$kdOrg."'  ".$whrd."\r\n                                     group by absensi,karyawanid,left(tanggal,7)";
$rkehadiran = fetchData($sKehadiran);
foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
    if ('' != $resKhdrn['absensi']) {
        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['periode']][$resKhdrn['absensi']] += $resKhdrn['total'];
        $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
        $dtPeriode[$resKhdrn['periode']] = $resKhdrn['periode'];
        $klmpkAbsn[$resAbsn['absensi']] = $resAbsn['absensi'];
    }
}
$sPrestasi = 'select left(c.tanggal,7) as periode,a.jumlahhk,a.nik from '.$dbname.".kebun_prestasi a \r\n                                    left join ".$dbname.".kebun_aktifitas c on a.notransaksi=c.notransaksi \r\n                                    left join ".$dbname.".datakaryawan b on a.nik=b.karyawanid\r\n                                    where c.notransaksi like '%PNN%' and a.nik!=''   ".$whrd."\r\n                                    and substr(c.kodeorg,1,4)='".$kdOrg."' and substr(c.tanggal,1,7) between '".$tgl1."' and '".$tgl2."'";
$rPrestasi = fetchData($sPrestasi);
foreach ($rPrestasi as $presBrs => $resPres) {
    $resPres['absensi'] = 'H';
    ++$hasilAbsn[$resPres['nik']][$resPres['periode']]['H'];
    $resData[$resPres['nik']][] = $resPres['nik'];
    $dtPeriode[$resPres['periode']] = $resPres['periode'];
    $klmpkAbsn[$resPres['absensi']] = $resPres['absensi'];
}
$dzstr = 'SELECT left(a.tanggal,7) as periode,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kdOrg."%' and c.namakaryawan is not NULL\r\n    union select left(a.tanggal,7) as periode,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') \r\n    and c.karyawanid!='' and b.kodeorg like '".$kdOrg."%' ".$whrc.' and c.namakaryawan is not NULL';
$dzres = mysql_query($dzstr);
while ($dzbar = mysql_fetch_object($dzres)) {
    $dzbar->absensi = 'H';
    ++$hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H'];
    $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
    $dtPeriode[$dzbar->periode] = $dzbar->periode;
    $klmpkAbsn[$dzbar->absensi] = $dzbar->absensi;
}
$dzstr = 'SELECT left(a.tanggal,7) as periode,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kdOrg."%' and c.namakaryawan is not NULL\r\n    union select left(a.tanggal,7) as periode,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and c.karyawanid!='' and b.kodeorg like '".$kdOrg."%'  ".$whrc." \r\n    and c.namakaryawan is not NULL";
$dzres = mysql_query($dzstr);
while ($dzbar = mysql_fetch_object($dzres)) {
    $dzbar->absensi = 'H';
    ++$hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H'];
    $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
    $dtPeriode[$dzbar->periode] = $dzbar->periode;
    $klmpkAbsn[$dzbar->absensi] = $dzbar->absensi;
}
array_multisort($dtPeriode);
$bgc = '';
$brd = '0';
if ('excel' == $proses) {
    $bgc = ' bgcolor=#DEDEDE align=center';
    $brd = '1';
}

$tab .= "<table cellspacing='1' border='".$brd."' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr ".$bgc.">\r\n        <td rowspan=2>No</td>\r\n        <td rowspan=2>".$_SESSION['lang']['nama']."</td>\r\n        <td rowspan=2>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td rowspan=2>".$_SESSION['lang']['bagian']."</td>\r\n        <td rowspan=2>".$_SESSION['lang']['jabatan']."</td>\r\n        <td rowspan=2>".$_SESSION['lang']['tmk'].'</td>';
foreach ($dtPeriode as $dtprd) {
    $tab .= "<td align=center colspan='".count($klmpkAbsn)."'>".$dtprd.'</td>';
}
$tab .= '</tr><tr  '.$bgc.'>';
foreach ($dtPeriode as $dtprd) {
    foreach ($klmpkAbsn as $brsKet => $hslKet) {
        $tab .= '<td width=10px align=center>'.$hslKet['kodeabsen'].'</td>';
    }
}
$tab .= "\r\n        </tr></thead>\r\n        <tbody>";
foreach ($resData as $hslBrs => $hslAkhir) {
    if ('' != $hslAkhir[0]) {
        ++$not;
        $tab .= '<tr class=rowcontent><td>'.$not."</td>\r\n                <td>".$namakar[$hslAkhir[0]]."</td>\r\n                <td>".$nmTipe[$hslAkhir[0]]."</td>\r\n                <td>".$nmBagian[$hslAkhir[0]]."</td>\r\n                <td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n                <td>".$optTmk[$hslAkhir[0]].'</td>';
        foreach ($dtPeriode as $dtprd) {
            foreach ($klmpkAbsn as $brsKet => $hslKet) {
                $bgrd = '';
                if ('H' == $hslKet['kodeabsen'] && $hasilAbsn[$hslAkhir[0]][$dtprd][$hslKet['kodeabsen']] < $nilaiMax) {
                    $bgrd = 'bgcolor=red';
                }

                $tab .= '<td width=10px align=center '.$bgrd.'>'.$hasilAbsn[$hslAkhir[0]][$dtprd][$hslKet['kodeabsen']].'</td>';
            }
        }
    }
}
$tab .= '</tbody></table>';
switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'RekapAbsen_PerBulan__'.$kdOrg;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $tab)) {
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
}

?>