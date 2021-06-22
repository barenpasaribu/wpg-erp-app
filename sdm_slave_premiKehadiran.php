<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$per = $_POST['per'];
$proses2 = $_POST['proses'];
$periode = $_POST['periode'];
$karyawanid = $_POST['karyawanid'];
$premi = $_POST['premi'];
$arrXV = ['X', '√'];
$tahunGaji = substr($per, 0, 4);
$atgl = 'select * from '.$dbname.".sdm_5periodegaji where periode='".$per."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$btgl = mysql_query($atgl);
$ctgl = mysql_fetch_assoc($btgl);
$tgl1 = $ctgl['tanggalmulai'];
$tgl2 = $ctgl['tanggalsampai'];
$golkar = makeOption($dbname, 'datakaryawan', 'karyawanid', 'kodegolongan');
$namagol = makeOption($dbname, 'sdm_5golongan', 'kodegolongan', 'namagolongan');
$namatipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$sGetKary = 'select sum(c.jumlah) as jumlah,a.kodegolongan,a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,a.tipekaryawan,subbagian from '.$dbname.".datakaryawan a \r\n           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan and lokasitugas='".$_SESSION['empl']['lokasitugas']."'\r\n\t\t   left join ".$dbname.".sdm_5gajipokok c on a.karyawanid=c.karyawanid\r\n\t\t    where  a.tipekaryawan='4' and c.tahun='".$tahunGaji."' group by a.karyawanid order by namakaryawan asc";
$rGetkary = fetchData($sGetKary);
foreach ($rGetkary as $row => $kar) {
    $jumlahUmr[$kar['karyawanid']] = $kar['jumlah'];
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $nikkar[$kar['karyawanid']] = $kar['nik'];
    $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
    $sbgnb[$kar['karyawanid']] = $kar['subbagian'];
    $tipekaryawan[$kar['karyawanid']] = $kar['tipekaryawan'];
    $golongankar[$kar['karyawanid']] = $kar['kodegolongan'];
}
switch ($proses) {
    case 'preview':
        $xi = 'select distinct * from '.$dbname.".sdm_5periodegaji where periode='".$per."' \r\n              and kodeorg='".$_SESSION['empl']['lokasitugas']."' and sudahproses='1'";
        $xu = mysql_query($xi);
        if (0 < mysql_num_rows($xu)) {
            $aktif2 = false;
        } else {
            $aktif2 = true;
        }

        if (!$aktif2) {
            exit('Error:Periode gaji untuk '.$_SESSION['empl']['lokasitugas'].' sudah ditutup');
        }

        $str = 'select * from '.$dbname.".setup_periodeakuntansi where periode='".$per."' and \r\n             kodeorg='".$_SESSION['empl']['lokasitugas']."' and tutupbuku=1";
        $res = mysql_query($str);
        if (0 < mysql_num_rows($res)) {
            $aktif = false;
        } else {
            $aktif = true;
        }

        if (!$aktif) {
            exit('Error:Periode akuntansi untuk '.$_SESSION['empl']['lokasitugas'].' sudah tutup buku');
        }

        if ('' == $per) {
            exit('Error:Periode masih kosong');
        }

        if ('' != $tgl_1 && '' != $tgl_2) {
            $tgl1 = $tgl_1;
            $tgl2 = $tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if ('' == $tgl2 && '' == $tgl1) {
            echo 'warning: Periode Penggajian Belum Terinput';
            exit();
        }

        $jmlHari = count($test);
        if (40 < $jmlHari) {
            echo 'warning:Range tanggal tidak valid';
            exit();
        }

        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        $jmAbsen = mysql_num_rows($qAbsen);
        $colSpan = (int) $jmAbsen + 2;
        echo "<table cellspacing='1' border='0' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td align=center>No</td>\r\n\t<td align=center>".$_SESSION['lang']['nama']."</td>\r\n\t<td align=center>".$_SESSION['lang']['nik']."</td>\r\n\t<td align=center>".$_SESSION['lang']['jabatan']."</td>\r\n\t<td align=center>".$_SESSION['lang']['subbagian']."</td>\r\n\t<td align=center>".$_SESSION['lang']['karyawanid']."</td>\r\n\t<td align=center>".$_SESSION['lang']['periode']."</td>\r\n\t";
        foreach ($test as $ar => $isi) {
            $qwe = date('D', strtotime($isi));
            echo '<td width=5px align=center>';
            if ('Sun' == $qwe) {
                echo '<font color=red>'.substr($isi, 8, 2).'</font>';
            } else {
                echo substr($isi, 8, 2);
            }

            echo '</td>';
        }
        echo "\r\n\t<td align=center>".$_SESSION['lang']['total'].' '.$_SESSION['lang']['absensi']."</td>\r\n\t<td align=center>".$_SESSION['lang']['upahpremi'].'</td>';
        $klmpkAbsn = [];
        foreach ($test as $ar => $isi) {
            $qwe = date('D', strtotime($isi));
        }
        while ($rKet = mysql_fetch_assoc($qAbsen)) {
            $klmpkAbsn[] = $rKet;
        }
        echo "\r\n\t</tr></thead>\r\n\t<tbody>";
        $resData[] = [];
        $hasilAbsn[] = [];
        $umrList[] = [];
        $sAbsn = 'select absensi,tanggal,karyawanid,kodeorg from '.$dbname.".sdm_absensidt \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
        $rAbsn = fetchData($sAbsn);
        foreach ($rAbsn as $absnBrs => $resAbsn) {
            if (null != $resAbsn['absensi']) {
                $umrList[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['umr' => 'ind'];
                $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                $notran[$resAbsn['karyawanid']][$resAbsn['tanggal']] .= 'ABSENSI:'.$resAbsn['kodeorg'].'__';
                $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
            }
        }
        $sKehadiran = 'select absensi,tanggal,karyawanid,notransaksi,umr from '.$dbname.".kebun_kehadiran_vw \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg like '%".$_SESSION['empl']['lokasitugas']."%'";
        $rkehadiran = fetchData($sKehadiran);
        foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
            if ('' != $resKhdrn['absensi']) {
                $umrList[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['umr' => $resKhdrn['umr']];
                $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                $notran[$resKhdrn['karyawanid']][$resKhdrn['tanggal']] .= 'BKM:'.$resKhdrn['notransaksi'].'__';
                $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
            }
        }
        $sPrestasi = 'select a.upahkerja,b.tanggal,a.jumlahhk,a.nik,a.notransaksi from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and b.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and b.tanggal between '".$tgl1."' and '".$tgl2."'";
        $rPrestasi = fetchData($sPrestasi);
        foreach ($rPrestasi as $presBrs => $resPres) {
            $umrList[$resPres['nik']][$resPres['tanggal']][] = ['umr' => $resPres['upahkerja']];
            $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
            $notran[$resPres['nik']][$resPres['tanggal']] .= 'BKM:'.$resPres['notransaksi'].'__';
            $resData[$resPres['nik']][] = $resPres['nik'];
        }
        $dzstr = 'SELECT tanggal,nikmandor,a.notransaksi,b.upahpremi FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1,a.notransaksi,b.upahpremi FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $umrList[$dzbar->nikmandor][$dzbar->tanggal][] = ['umr' => 'ind'];
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            $notran[$dzbar->nikmandor][$dzbar->tanggal] .= 'BKM:'.$dzbar->notransaksi.'__';
            $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
        }
        $dzstr = 'SELECT tanggal,nikmandor,a.notransaksi FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat,a.notransaksi FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $umrList[$dzbar->nikmandor][$dzbar->tanggal][] = ['umr' => 'ind'];
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            $notran[$dzbar->nikmandor][$dzbar->tanggal] .= 'BKM:'.$dzbar->notransaksi.'__';
            $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
        }
        $dzstr = 'SELECT a.upah,a.tanggal,idkaryawan, a.notransaksi FROM '.$dbname.".vhc_runhk a\r\n        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n        where a.tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".substr($_SESSION['empl']['lokasitugas'], 0, 4)."%'";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $umrList[$dzbar->idkaryawan][$dzbar->tanggal][] = ['umr' => $dzbar->upah];
            $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
            $notran[$dzbar->idkaryawan][$dzbar->tanggal] .= 'TRAKSI:'.$dzbar->notransaksi.'__';
            $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
        }
function kirimnama($nama)
{
    $qwe = explode(' ', $nama);
    foreach ($qwe as $kyu) {
        $balikin .= $kyu.'__';
    }

    return $balikin;
}

function removeduplicate($notransaksi)
{
    $notransaksi = substr($notransaksi, 0, -2);
    $qwe = explode('__', $notransaksi);
    foreach ($qwe as $kyu) {
        $tumpuk[$kyu] = $kyu;
    }
    foreach ($tumpuk as $tumpz) {
        $balikin .= $tumpz.'__';
    }

    return $balikin;
}

        $brt = [];
        $lmit = count($klmpkAbsn);
        $a = 0;
        foreach ($resData as $hslBrs => $hslAkhir) {
            if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                $umpHari = $jumlahUmr[$hslAkhir[0]] / 25;
                ++$no;
                echo '<tr class=rowcontent id=row'.$no.'><td>'.$no.'</td>';
                echo "\r\n\t\t\t<td>".$namakar[$hslAkhir[0]]."</td>\r\n\t\t\t<td>".$nikkar[$hslAkhir[0]]."</td>\r\n\t\t\t<td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n\t\t\t<td>".$sbgnb[$hslAkhir[0]]."</td>\r\n\t\t\t<td id=karyawanid".$no.'>'.$hslAkhir[0]."</td>\r\n\t\t\t<td id=periode".$no.'>'.$per."</td>\r\n\t\t\t";
                foreach ($test as $barisTgl => $isiTgl) {
                    if ('H' != $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']) {
                        echo '<td align=center>-</td>';
                    } else {
                        if ('ind' == $umrList[$hslAkhir[0]][$isiTgl][0]['umr']) {
                            $umrData = $umpHari;
                        } else {
                            $umrData = 0;
                            for ($i = 0; $i <= 10; ++$i) {
                                $umrData += $umrList[$hslAkhir[0]][$isiTgl][$i]['umr'];
                            }
                        }

                        if ($umpHari <= $umrData) {
                            $cekList = 1;
                            ++$totCekList[$hslAkhir[0]];
                        } else {
                            $cekList = 0;
                        }

                        echo '<td align=center>'.$arrXV[$cekList].'</td> ';
                    }
                }
                echo '<td width=5px  align=right>'.$totCekList[$hslAkhir[0]].'</td>';
                if ('22' <= $totCekList[$hslAkhir[0]]) {
                    $premi = $totCekList[$hslAkhir[0]] * 1000;
                } else {
                    $premi = '0';
                }

                echo '<td width=5px  align=right id=premi'.$no.'>'.$premi.'</td>';
                echo '</tr>';
            }
        }
        echo '<button class=mybutton onclick=saveAll('.$no.');>'.$_SESSION['lang']['proses'].'</button>';
        echo '</tbody></table>';

        break;
}
switch ($proses2) {
    case 'savedata':
        if ('0' == $premi || '' == $premi) {
        } else {
            $str = 'insert into '.$dbname.".kebun_premikemandoran (`kodeorg`,`periode`,`karyawanid`,`jabatan`,`pembagi`,`premiinput`,`updateby`,`posting`)\r\n\t\t\tvalues ('".$_SESSION['empl']['lokasitugas']."','".$periode."','".$karyawanid."','PREMIHADIR','1','".$premi."','".$_SESSION['standard']['userid']."',1)";
            if (mysql_query($str)) {
            } else {
                $str = 'update '.$dbname.".kebun_premikemandoran set posting=1,premiinput='".$premi."' where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."' and karyawanid='".$karyawanid."'";
                if (mysql_query($str)) {
                } else {
                    echo ' Gagal,'.addslashes(mysql_error($conn));
                }
            }
        }

        break;
}
function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[] = date('Y-m-d', $date2);
    if ($date1 == $date2) {
        $dates_array = [];
        $dates_array[] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

?>