<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lokasitugas = $_SESSION['empl']['lokasitugas'];
('' == $_POST['tanggal21'] ? ($tanggal1 = $_GET['tanggal21']) : ($tanggal1 = $_POST['tanggal21']));
('' == $_POST['tanggal22'] ? ($tanggal2 = $_GET['tanggal22']) : ($tanggal2 = $_POST['tanggal22']));
('' == $_POST['karyawanid2'] ? ($karyawanid = $_GET['karyawanid2']) : ($karyawanid = $_POST['karyawanid2']));
$tangsys1 = putertanggal($tanggal1);
$tangsys2 = putertanggal($tanggal2);
$skaryawan = 'select a.karyawanid, a.kodejabatan, b.namajabatan, a.namakaryawan, c.nama from '.$dbname.".datakaryawan a \r\n    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan \r\n    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode \r\n    where a.lokasitugas = '".$lokasitugas."' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar is NULL) and a.karyawanid like '%".$karyawanid."%' AND a.`tipekaryawan` !=0\r\n    order by namakaryawan asc";
$rkaryawan = fetchData($skaryawan);
foreach ($rkaryawan as $row => $kar) {
    $karyawan[$kar['karyawanid']]['id'] = $kar['karyawanid'];
    $karyawan[$kar['karyawanid']]['nama'] = $kar['namakaryawan'];
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $jabakar[$kar['karyawanid']] = $kar['namajabatan'];
    $kojabakar[$kar['karyawanid']] = $kar['kodejabatan'];
    $bagikar[$kar['karyawanid']] = $kar['bagian'];
}
if ('' == $tanggal1 || '' == $tanggal2) {
    echo 'warning: Please fill all fields.';
    exit();
}

if ($tangsys2 < $tangsys1) {
    echo 'warning: Lower date first.';
    exit();
}

$tanggaltanggal = dates_inbetween($tangsys1, $tangsys2);
$jumlahhari = count($tanggaltanggal);
$str = 'SELECT a.tanggal, a.jam, a.karyawanid, c.namakaryawan FROM '.$dbname.".sdm_absensidt_vw a LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid WHERE a.tanggal between '".$tangsys1."' and '".$tangsys2."' and a.jam < '12:00:00' and a.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0 and kodeorg = '".$lokasitugas."' ORDER BY a.tanggal DESC, a.jam DESC";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if (!isset($bar->karyawanid)) {
    } else {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        $presensi[$bar->karyawanid]['m'.$bar->tanggal] = $bar->jam;
    }
}
$str = 'SELECT a.tanggal, a.jamPlg, a.karyawanid, c.namakaryawan FROM '.$dbname.".sdm_absensidt_vw a\r\n    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        \r\n    WHERE a.tanggal between '".$tangsys1."' and '".$tangsys2."' and a.jamPlg >= '12:00:00'\r\n        and a.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0 and kodeorg = '".$lokasitugas."'\r\n    ORDER BY a.tanggal ASC, a.jamPlg ASC";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if (!isset($bar->karyawanid)) {
    } else {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        $presensi[$bar->karyawanid]['k'.$bar->tanggal] = $bar->jamPlg;
    }
}
$str = 'SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM '.$dbname.".att_log a\r\n    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin\r\n    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        \r\n    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) < '12:00:00'\r\n        and b.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0\r\n    ORDER BY scan_date DESC";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if (!isset($bar->karyawanid)) {
    } else {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        $presensi[$bar->karyawanid]['m'.$bar->tanggal] = $bar->jam;
    }
}
$str = 'SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM '.$dbname.".att_log a\r\n    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin\r\n    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        \r\n    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) >= '12:00:00'\r\n        and b.karyawanid like '%".$karyawanid."%' AND c.`tipekaryawan` !=0\r\n    ORDER BY scan_date ASC";
$res = mysql_query($str);
echo mysql_error($conn);
while ($bar = mysql_fetch_object($res)) {
    if (!isset($bar->karyawanid)) {
    } else {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        $presensi[$bar->karyawanid]['k'.$bar->tanggal] = $bar->jam;
    }
}
if (!empty($karyawan)) {
    foreach ($karyawan as $c => $key) {
        $sort_nama[] = $key['nama'];
    }
}

if (!empty($karyawan)) {
    array_multisort($sort_nama, SORT_ASC, $karyawan);
}

if ('excel' == $proses) {
    $bgcolor = ' bgcolor=#DEDEDE';
    $border = 1;
} else {
    $bgcolor = '';
    $border = 0;
}

$stream = '';
$no = 0;
$kolomtanggal = $jumlahhari + 5;
$stream .= '<table class=sortable cellspacing=1 border='.$border.'>';
$stream .= '<thead><tr class=rowtitle>';
$stream .= '<td rowspan=2 align=center'.$bgcolor.'>'.$_SESSION['lang']['nourut'].'</td>';
$stream .= '<td rowspan=2 align=center'.$bgcolor.'>'.$_SESSION['lang']['namakaryawan'].'</td>';
$stream .= '<td colspan='.$kolomtanggal.' align=center'.$bgcolor.'>'.$_SESSION['lang']['tanggal'].'</td>';
$stream .= '</tr>';
$stream .= '<tr class=rowtitle>';
if (!empty($tanggaltanggal)) {
    foreach ($tanggaltanggal as $tang) {
        $hari = date('D', strtotime($tang));
        if ('excel' == $proses) {
            $qwe = substr($tang, 5, 2).'/'.substr($tang, 8, 2);
        } else {
            $qwe = substr($tang, 8, 2).'/'.substr($tang, 5, 2);
        }

        if ('Sat' == $hari || 'Sun' == $hari) {
            $qwe = "<font color='#FF0000'>".$qwe.'</font>';
        }

        $stream .= '<td align=center'.$bgcolor.'>';
        $stream .= $qwe;
        $stream .= '</td>';
    }
}

if ('ID' == $_SESSION['language']) {
    $stream .= '<td align=center'.$bgcolor.'>Hadir</td>';
    $stream .= '<td align=center'.$bgcolor.'>Telat</td>';
    $stream .= '<td align=center'.$bgcolor.'>Jam Telat</td>';
    $stream .= '<td align=center'.$bgcolor.'>Lembur</td>';
    $stream .= '<td align=center'.$bgcolor.'>Lembur-Telat</td>';
} else {
    $stream .= '<td align=center'.$bgcolor.'>Present</td>';
    $stream .= '<td align=center'.$bgcolor.'>Late</td>';
    $stream .= '<td align=center'.$bgcolor.'>Hours Late</td>';
    $stream .= '<td align=center'.$bgcolor.'>Overtime</td>';
    $stream .= '<td align=center'.$bgcolor.'>Overtime-Late</td>';
}

$stream .= '</tr></thead>';
$stream .= '<tbody>';
if (!empty($karyawan)) {
    foreach ($karyawan as $kar) {
        ++$no;
        $hadir = 0;
        $telat = 0;
        $totallembur = '00:00';
        $totaltelat = '00:00';
        $stream .= '<tr class=rowcontent>';
        $stream .= '<td align=right>'.$no.'.</td>';
        $stream .= '<td>'.$kar['nama'].'</td>';
        if (!empty($tanggaltanggal)) {
            foreach ($tanggaltanggal as $tang) {
                $hari = date('D', strtotime($tang));
                $pres = '';
                if (isset($presensi[$kar['id']]['m'.$tang]) || isset($presensi[$kar['id']]['k'.$tang])) {
                    $ontime = true;
                    if (isset($presensi[$kar['id']]['m'.$tang])) {
                        if ('2013-07-09' <= $tang && $tang <= '2013-08-08') {
                            $jammasuk = '07:00';
                            if ('40' == $kojabakar[$kar['id']] || '377') {
                                $jammasuk = '06:00';
                            }

                            $a = $jammasuk;
                            $b = substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                            $jamlembur = kuranglembur($a, $b);
                            if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= $jammasuk) {
                                $totallembur = tambahlembur($totallembur, $jamlembur);
                                $pres = '&nbsp;'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'['.$jamlembur.']';
                            } else {
                                $totaltelat = tambahlembur($totaltelat, $jamlembur);
                                $pres = '&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'['.$jamlembur.']</font>';
                                $ontime = false;
                            }
                        } else {
                            $jammasuk = '08:00';
                            if ('40' == $kojabakar[$kar['id']] || '377') {
                                $jammasuk = '07:00';
                            }

                            $a = $jammasuk;
                            $b = substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                            $jamlembur = kuranglembur($a, $b);
                            if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= $jammasuk) {
                                $totallembur = tambahlembur($totallembur, $jamlembur);
                                $pres = '&nbsp;'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'['.$jamlembur.']';
                            } else {
                                $totaltelat = tambahlembur($totaltelat, $jamlembur);
                                $pres = '&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'['.$jamlembur.']</font>';
                                $ontime = false;
                            }
                        }
                    } else {
                        $ontime = false;
                    }

                    if (isset($presensi[$kar['id']]['k'.$tang])) {
                        if ('2013-07-09' <= $tang && $tang <= '2013-08-08') {
                            $jamkeluar = '16:00';
                            if ('40' == $kojabakar[$kar['id']] || '377') {
                                $jamkeluar = '17:00';
                            }

                            $b = $jamkeluar;
                            $a = substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                            $jamlembur = kuranglembur($a, $b);
                            if ($jamkeluar <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                $totallembur = tambahlembur($totallembur, $jamlembur);
                                $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']';
                            } else {
                                $totaltelat = tambahlembur($totaltelat, $jamlembur);
                                $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']</font>';
                                $ontime = false;
                            }
                        } else {
                            if ('2013-10-14' == $tang) {
                                $jamkeluar = '15:00';
                                if ('40' == $kojabakar[$kar['id']] || '377') {
                                    $jamkeluar = '16:00';
                                }

                                $b = $jamkeluar;
                                $a = substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                                $jamlembur = kuranglembur($a, $b);
                                if ($jemkeluar <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                    $totallembur = tambahlembur($totallembur, $jamlembur);
                                    $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']';
                                } else {
                                    $totaltelat = tambahlembur($totaltelat, $jamlembur);
                                    $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']</font>';
                                    $ontime = false;
                                }
                            } else {
                                $jamkeluar = '17:00';
                                if ('40' == $kojabakar[$kar['id']] || '377') {
                                    $jamkeluar = '18:00';
                                }

                                $b = $jamkeluar;
                                $a = substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                                $jamlembur = kuranglembur($a, $b);
                                if ($jamkeluar <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                    $totallembur = tambahlembur($totallembur, $jamlembur);
                                    $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']';
                                } else {
                                    $totaltelat = tambahlembur($totaltelat, $jamlembur);
                                    $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'['.$jamlembur.']</font>';
                                    $ontime = false;
                                }
                            }
                        }
                    } else {
                        $ontime = false;
                    }

                    if ($ontime) {
                        ++$hadir;
                    } else {
                        ++$telat;
                    }
                }

                if ('Sat' == $hari || 'Sun' == $hari) {
                    $bgcolor = " bgcolor='#FFCCCC'";
                    if ('' == $pres) {
                        $pres = ' ';
                    }
                } else {
                    $bgcolor = '';
                }

                $stream .= '<td valign=top align=center'.$bgcolor.'>'.$pres.'</td>';
            }
        }

        $stream .= '<td align=right>'.$hadir.'</td>';
        $stream .= '<td align=right>'.$telat.'</td>';
        $stream .= '<td align=right>'.$totaltelat.'</td>';
        $stream .= '<td align=right>'.$totallembur.'</td>';
        $lemburbersih = kuranglembur($totallembur, $totaltelat);
        $stream .= '<td align=right>'.$lemburbersih.'</td>';
        $stream .= '</tr>';
    }
}

$stream .= '</tbody></table>';
if ('ID' == $_SESSION['language']) {
    $stream .= 'Data yang ditampilkan adalah karyawan non staf</br>';
    $stream .= 'Lembur karyawan dengan kode jabatan Office Boy/Girl (40) dihitung bila karyawan datang 1 jam sebelum jam masuk dan/atau pulang 1 jam setelah jam keluar.</br>';
} else {
    $stream .= 'Data displayed is non-staff employees.</br>';
    $stream .= 'Overtime for employees with title Office Boy/Girl (40) is counted if a person come 1 hour before opening hour and/or home 1 hour after closing hour.</br>';
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'excel':
        $stream .= '<br><br>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        if ('' != $period) {
            $art = $period;
            $art = $art[1].$art[0];
        }

        if ('' != $periode) {
            $art = $periode;
            $art = $art[1].$art[0];
        }

        if ('' != $kdeOrg) {
            $kodeOrg = $kdeOrg;
        }

        if ('' != $kdOrg) {
            $kodeOrg = $kdOrg;
        }

        $nop_ = 'RekapAbsen_Jam_'.$tangsys1.'_'.$tangsys2;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false != ($file = readdir($handle))) {
                    if ('.' != $file && '..' != $file) {
                        @unlink('tempExcel/'.$file);
                    }
                }
                closedir($handle);
            }

            $handle = fopen('tempExcel/'.$nop_.'.xls', 'w');
            if (!fwrite($handle, $stream)) {
                echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/".$nop_.".xls';\r\n                        </script>";
            closedir($handle);
        }

        break;
    case 'getTgl':
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
            $dmna .= " and periode='".$tanggal."'";
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
                $dmna .= " and periode='".$tanggal."'";
            }
        }

        if ('' != $sistemGaji) {
            $dmna .= " and jenisgaji='".substr($sistemGaji, 0, 1)."'";
        }

        if ('' == $kdUnit) {
            $kdUnit = $_SESSION['empl']['lokasitugas'];
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".substr($kdUnit, 0, 4)."' ".$dmna.' ';
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
    case 'getKry':
        $optKry = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        if (4 < strlen($kdeOrg)) {
            $where = " subbagian='".$kdeOrg."'";
        } else {
            $where = " lokasitugas='".$kdeOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
        }

        $sKry = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
        }
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.".sdm_5periodegaji where kodeorg='".$kdeOrg."'";
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
        }
        echo $optKry.'###'.$optPeriode;

        break;
    case 'getPeriode':
        if ('' != $periodeGaji) {
            $were = " kodeorg='".$kdUnit."' and periode='".$periodeGaji."' and jenisgaji='".$sistemGaji."'";
        } else {
            $were = " kodeorg='".$kdUnit."'";
        }

        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriode = 'select distinct periode from '.$dbname.'.sdm_5periodegaji where '.$were.'';
        $qPeriode = mysql_query($sPeriode);
        while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
            $optPeriode .= '<option value='.$rPeriode['periode'].'>'.substr(tanggalnormal($rPeriode['periode']), 1, 7).'</option>';
        }
        $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sSub = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where induk='".$kdUnit."'  order by namaorganisasi asc";
        $qSub = mysql_query($sSub);
        while ($rSub = mysql_fetch_assoc($qSub)) {
            $optAfd .= "<option value='".$rSub['kodeorganisasi']."'>".$rSub['namaorganisasi'].'</option>';
        }
        echo $optAfd.'####'.$optPeriode;

        break;
    default:
        break;
}
function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

function dates_inbetween($date1, $date2)
{
    $day = 60 * 60 * 24;
    $date1 = strtotime($date1);
    $date2 = strtotime($date2);
    $days_diff = round(($date2 - $date1) / $day);
    $dates_array = [];
    $dates_array[date('Y-m-d', $date1)] = date('Y-m-d', $date1);
    for ($x = 1; $x < $days_diff; ++$x) {
        $dates_array[date('Y-m-d', $date1 + $day * $x)] = date('Y-m-d', $date1 + $day * $x);
    }
    $dates_array[date('Y-m-d', $date2)] = date('Y-m-d', $date2);
    if ($date1 == $date2) {
        $dates_array = [];
        $dates_array[date('Y-m-d', $date1)] = date('Y-m-d', $date1);
    }

    return $dates_array;
}

function tambahlembur($totallembur, $lembur)
{
    $i = explode(':', $totallembur);
    $j = explode(':', $lembur);
    $menit = $j[1] + $i[1];
    $jam = $j[0] + $i[0];
    if (60 <= $menit) {
        $menit -= 60;
        ++$jam;
    }

    if (1 == strlen($menit)) {
        $menit = '0'.$menit;
    }

    if (1 == strlen($jam)) {
        $jam = '0'.$jam;
    }

    return $jam.':'.$menit;
}

function kuranglembur($lembur, $totallembur)
{
    if ($totallembur < $lembur) {
        $i = explode(':', $totallembur);
        $j = explode(':', $lembur);
    } else {
        $j = explode(':', $totallembur);
        $i = explode(':', $lembur);
    }

    $menit = $j[1] - $i[1];
    $jam = $j[0] - $i[0];
    if ($menit < 0) {
        $menit += 60;
        --$jam;
    }

    if (1 == strlen($menit)) {
        $menit = '0'.$menit;
    }

    if (1 == strlen($jam)) {
        $jam = '0'.$jam;
    }

    return $jam.':'.$menit;
}

?>