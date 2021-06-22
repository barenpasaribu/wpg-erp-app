<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lokasitugas = $_SESSION['empl']['lokasitugas'];
('' == $_POST['tahun'] ? ($tahun = $_GET['tahun']) : ($tahun = $_POST['tahun']));
$tangsys1 = $tahun.'-01-01';
$tangsys2 = $tahun.'-12-31';
$skaryawan = 'select a.karyawanid, b.namajabatan, a.namakaryawan, c.nama from '.$dbname.".datakaryawan a \r\n    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan \r\n    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode \r\n    where a.lokasitugas = '".$lokasitugas."' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar is NULL)\r\n    order by namakaryawan asc";
$rkaryawan = fetchData($skaryawan);
foreach ($rkaryawan as $row => $kar) {
    $karyawan[$kar['karyawanid']]['id'] = $kar['karyawanid'];
    $karyawan[$kar['karyawanid']]['nama'] = $kar['namakaryawan'];
    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
    $jabakar[$kar['karyawanid']] = $kar['namajabatan'];
    $bagikar[$kar['karyawanid']] = $kar['bagian'];
}
if ('' == $tahun) {
    echo 'warning: Please fill all fields.';
    exit();
}

$tanggaltanggal = dates_inbetween($tangsys1, $tangsys2);
$str = "SELECT a.karyawanid, substr(a.darijam,1,10) as daritanggal, substr(a.sampaijam,1,10) as sampaitanggal, a.jenisijin, c.namakaryawan, c.lokasitugas, a.jenisijin \r\n    FROM ".$dbname.".sdm_ijin a\r\n    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid WHERE substr(a.darijam,1,10) <= '".$tangsys2."' and substr(a.sampaijam,1,10) >= '".$tangsys1."' and stpersetujuan1 = '1' and stpersetujuanhrd = '1'\r\n    ORDER BY a.darijam, a.sampaijam";
$res = mysql_query($str);
echo mysql_error($conn);
while ($bar = mysql_fetch_object($res)) {
    if ($lokasitugas == substr($bar->lokasitugas, 2, 2)) {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
    }

    $presensi[$bar->karyawanid]['ijin1'] = $bar->daritanggal;
    $presensi[$bar->karyawanid]['ijin2'] = $bar->sampaitanggal;
    $presensi[$bar->karyawanid]['x'.$bar->daritanggal] = $bar->jenisijin;
}
$str = 'SELECT a.karyawanid, a.tanggalperjalanan, a.tanggalkembali, a.tujuan1, a.tujuan2, a.tujuan3, c.namakaryawan, a.kodeorg FROM '.$dbname.".sdm_pjdinasht a\r\n    LEFT JOIN ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid        \r\n    WHERE a.tanggalperjalanan <= '".$tangsys2."' and a.tanggalkembali >= '".$tangsys1."' order by a.tanggalperjalanan, a.tanggalkembali\r\n        and statuspersetujuan='1' and statushrd='1'";
$res = mysql_query($str);
echo mysql_error($conn);
while ($bar = mysql_fetch_object($res)) {
    if ('' < $bar->karyawanid) {
        if ($lokasitugas == substr($bar->kodeorg, -2)) {
            $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
            $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        }

        $presensi[$bar->karyawanid]['dinas1'] = $bar->tanggalperjalanan;
        $presensi[$bar->karyawanid]['dinas2'] = $bar->tanggalkembali;
    }
}
$str = 'SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM '.$dbname.".att_log a\r\n    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin\r\n    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        \r\n    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) < '12:00:00'\r\n    ORDER BY scan_date DESC";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    if (!isset($bar->karyawanid)) {
    } else {
        $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
        $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
        $presensi[$bar->karyawanid]['m'.$bar->tanggal] = $bar->jam;
    }
}
$str = 'SELECT a.pin, substr(a.scan_date,1,10) as tanggal, substr(a.scan_date,12,8) as jam, b.karyawanid, c.namakaryawan FROM '.$dbname.".att_log a\r\n    LEFT JOIN ".$dbname.".att_adaptor b on a.pin=b.pin\r\n    LEFT JOIN ".$dbname.".datakaryawan c on b.karyawanid=c.karyawanid        \r\n    WHERE substr(scan_date,1,10) between '".$tangsys1."' and '".$tangsys2."' and substr(scan_date,12,8) >= '12:00:00'\r\n    ORDER BY scan_date ASC";
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
$stream .= '<td colspan='.$kolomtanggal.' align=center'.$bgcolor.'>'.$_SESSION['lang']['rkpAbsen'].'</td>';
$stream .= '</tr>';
$stream .= '<tr class=rowtitle>';
if ('ID' == $_SESSION['language']) {
    $stream .= '<td align=center'.$bgcolor.'>Hadir</td>';
    $stream .= '<td align=center'.$bgcolor.'>Telat</td>';
    $stream .= '<td align=center'.$bgcolor.'>Dinas</td>';
    $stream .= '<td align=center'.$bgcolor.'>Cuti</td>';
    $stream .= '<td align=center'.$bgcolor.'>Mangkir</td>';
} else {
    $stream .= '<td align=center'.$bgcolor.'>Present</td>';
    $stream .= '<td align=center'.$bgcolor.'>Late</td>';
    $stream .= '<td align=center'.$bgcolor.'>Duty</td>';
    $stream .= '<td align=center'.$bgcolor.'>Leave</td>';
    $stream .= '<td align=center'.$bgcolor.'>Absence</td>';
}

$stream .= '</tr></thead>';
$stream .= '<tbody>';
if (!empty($karyawan)) {
    foreach ($karyawan as $kar) {
        ++$no;
        $hadir = 0;
        $telat = 0;
        $cuti = 0;
        $dinas = 0;
        $mangkir = 0;
        $stream .= '<tr class=rowcontent>';
        $stream .= '<td align=right>'.number_format($no).'.</td>';
        $stream .= '<td>'.$kar['nama'].'</td>';
        if (!empty($tanggaltanggal)) {
            foreach ($tanggaltanggal as $tang) {
                $hari = date('D', strtotime($tang));
                $pres = '';
                if (isset($presensi[$kar['id']]['ijin1']) && $presensi[$kar['id']]['ijin1'] <= $tang && $tang <= $presensi[$kar['id']]['ijin2']) {
                    if ('Sat' != $hari && 'Sun' != $hari) {
                        $pres = $presensi[$kar['id']]['x'.$presensi[$kar['id']]['ijin1']];
                    }

                    if ('Sat' != $hari && 'Sun' != $hari) {
                        ++$cuti;
                    }
                }

                if (isset($presensi[$kar['id']]['dinas1']) && $presensi[$kar['id']]['dinas1'] <= $tang && $tang <= $presensi[$kar['id']]['dinas2']) {
                    $pres = 'DINAS';
                }

                if (isset($presensi[$kar['id']]['m'.$tang]) || isset($presensi[$kar['id']]['k'.$tang])) {
                    $ontime = true;
                    if (isset($presensi[$kar['id']]['m'.$tang])) {
                        if ('2013-07-09' <= $tang && $tang <= '2013-08-08') {
                            if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= '07:30') {
                                $pres = '&nbsp;'.substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                            } else {
                                $pres = '&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'</font>';
                                $ontime = false;
                            }
                        } else {
                            if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= '08:00') {
                                $pres = '&nbsp;'.substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                            } else {
                                $pres = '&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'</font>';
                                $ontime = false;
                            }
                        }
                    } else {
                        $ontime = false;
                    }

                    if (isset($presensi[$kar['id']]['k'.$tang])) {
                        if ('2013-07-09' <= $tang && $tang <= '2013-08-08') {
                            if ('16:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                            } else {
                                $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'</font>';
                                $ontime = false;
                            }
                        } else {
                            if ('17:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                            } else {
                                $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'</font>';
                                $ontime = false;
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

                if ('DINAS' == $pres) {
                    ++$dinas;
                }

                if ('' == $pres) {
                    ++$mangkir;
                }
            }
        }

        $stream .= '<td align=right>'.$hadir.'</td>';
        $stream .= '<td align=right>'.$telat.'</td>';
        $stream .= '<td align=right>'.$dinas.'</td>';
        $stream .= '<td align=right>'.$cuti.'</td>';
        $stream .= '<td align=right>'.$mangkir.'</td>';
        $stream .= '</tr>';
    }
}

$stream .= '</tbody></table>';
if ('ID' == $_SESSION['language']) {
    $stream .= 'Bila karyawan tertentu tidak/muncul, harap dipastikan data Lokasi Tugas-nya dan telah terdaftar PIN Fingerprint-nya.</br>';
    $stream .= 'Hanya Ijin/Cuti yang telah disetujui oleh atasan dan HRD yang ditampilkan. Cuti Sabtu/Minggu tidak dihitung.</br>';
    $stream .= 'Bila karyawan tidak absen masuk/pulang maka dianggap telat.</br>';
    $stream .= 'Absen masuk 00:00 - 11:59. Absen pulang 12:00 - 23:59.</br>';
} else {
    $stream .= 'If any employee not listed, please make sure duty location of the employee and fingerprint has been registred.</br>';
    $stream .= 'For leave data, only approved leave are displayed. Leave on Saturday and Sunday are not counted.</br>';
    $stream .= 'If employee out earlier, then system will recognize it as late</br>';
    $stream .= 'In between 00:00 - 11:59. Out between 12:00 - 23:59.</br>';
}

switch ($proses) {
    case 'preview':
        echo $stream;

        break;
    case 'pdf':

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $align;
        global $length;
        global $colArr;
        global $title;
        global $tahun;
        global $tanggal1;
        global $tanggal2;
        global $tangsys1;
        global $tangsys2;
        global $tanggaltanggal;
        global $jumlahhari;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
        $orgData = fetchData($query);
        $width = $this->w - $this->lMargin - $this->rMargin;
        $height = 15;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        $this->Image($path, $this->lMargin, $this->tMargin, 70);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(100);
        $this->Cell($width - 100, $height, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, $orgData[0]['alamat'], 0, 1, 'L');
        $this->SetX(100);
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 3, $this->lMargin + $width, $this->tMargin + $height * 3);
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($width - 5, $height, $_SESSION['lang']['rkpAbsen'], '', 0, 'C');
        $this->Ln();
        $this->Cell($width - 5, $height, $_SESSION['lang']['periode'].' : '.$tahun, '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(2 / 100 * $width, $height, 'No', TRL, 0, 'C', 1);
        $this->Cell(9.5 / 100 * $width, $height, $_SESSION['lang']['namakaryawan'], TRL, 0, 'C', 1);
        if ('ID' == $_SESSION['language']) {
            $this->Cell(2.7 / 100 * $width, $height, 'Hadir', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Telat', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Dinas', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Cuti', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Mangkir', TRL, 0, 'C', 1);
        } else {
            $this->Cell(2.7 / 100 * $width, $height, 'Present', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Late', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Duty', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Leave', TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width, $height, 'Absence', TRL, 0, 'C', 1);
        }

        $this->Ln();
        $this->Cell(2 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(9.5 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'C', 1);
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo().' Print Time:'.date('Y-m-d H:i:s').' By:'.$_SESSION['empl']['name'], 0, 0, 'L');
    }
}

        $pdf = new PDF('L', 'pt', 'Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 15;
        $pdf->AddPage();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 7);
        $no = 0;
        if (!empty($karyawan)) {
            foreach ($karyawan as $kar) {
                $hadir = 0;
                $telat = 0;
                $cuti = 0;
                $dinas = 0;
                $mangkir = 0;
                ++$no;
                $pdf->Cell(2 / 100 * $width, $height, $no, TRL, 0, 'R', 1);
                $pdf->Cell(9.5 / 100 * $width, $height, $kar['nama'], TRL, 0, 'L', 1);
                if (!empty($tanggaltanggal)) {
                    foreach ($tanggaltanggal as $tang) {
                        $hari = date('D', strtotime($tang));
                        $pres = '';
                        if (isset($presensi[$kar['id']]['ijin1']) && $presensi[$kar['id']]['ijin1'] <= $tang && $tang <= $presensi[$kar['id']]['ijin2']) {
                            if ('Sat' != $hari && 'Sun' != $hari) {
                                $pres = $presensi[$kar['id']]['x'.$presensi[$kar['id']]['ijin1']];
                            }

                            if ('Sat' != $hari && 'Sun' != $hari) {
                                ++$cuti;
                            }
                        }

                        if (isset($presensi[$kar['id']]['dinas1']) && $presensi[$kar['id']]['dinas1'] <= $tang && $tang <= $presensi[$kar['id']]['dinas2']) {
                            $pres = 'DINAS';
                        }

                        if (isset($presensi[$kar['id']]['m'.$tang]) || isset($presensi[$kar['id']]['k'.$tang])) {
                            $ontime = true;
                            if (isset($presensi[$kar['id']]['m'.$tang])) {
                                if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= '08:00') {
                                    $pres = '&nbsp;'.substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                                } else {
                                    $pres = '&nbsp;<font color=red>'.substr($presensi[$kar['id']]['m'.$tang], 0, 5).'</font>';
                                    $ontime = false;
                                }
                            } else {
                                $ontime = false;
                            }

                            if (isset($presensi[$kar['id']]['k'.$tang])) {
                                if ('17:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                    $pres .= '</br>&nbsp;'.substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                                } else {
                                    $pres .= '</br>&nbsp;<font color=red>'.substr($presensi[$kar['id']]['k'.$tang], 0, 5).'</font>';
                                    $ontime = false;
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

                        if ('DINAS' == $pres) {
                            ++$dinas;
                        }

                        if ('' == $pres) {
                            ++$mangkir;
                        }
                    }
                }

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(2.7 / 100 * $width, $height, $hadir, TRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, $telat, TRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, $dinas, TRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, $cuti, TRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, $mangkir, TRL, 0, 'R', 1);
                $pdf->Ln();
                $pdf->Cell(2 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Cell(9.5 / 100 * $width, $height, $jabakar[$kar['id']], BRL, 0, 'L', 1);
                if (!empty($tanggaltanggal)) {
                    foreach ($tanggaltanggal as $tang) {
                        $pres = '';
                        if (isset($presensi[$kar['id']]['k'.$tang])) {
                            $ontime = true;
                            $pres .= substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                            if ('17:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                            } else {
                                $ontime = false;
                            }

                            if ($ontime) {
                                $pdf->SetTextColor(0, 0, 0);
                            } else {
                                $pdf->SetTextColor(255, 0, 0);
                            }
                        }

                        $hari = date('D', strtotime($tang));
                        if ('Sat' == $hari || 'Sun' == $hari) {
                            $pdf->SetFillColor(255, 224, 224);
                        } else {
                            $pdf->SetFillColor(255, 255, 255);
                        }
                    }
                }

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                $pdf->Ln();
            }
        }

        if ('ID' == $_SESSION['language']) {
            $pdf->Cell($width, $height, 'Bila karyawan tertentu tidak/muncul, harap dipastikan data Lokasi Tugas-nya dan telah terdaftar PIN Fingerprint-nya.', T, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'Hanya Ijin/Cuti yang telah disetujui oleh atasan dan HRD yang ditampilkan. Cuti Sabtu/Minggu tidak dihitung.', 0, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'Bila karyawan tidak absen masuk/pulang maka dianggap telat.', 0, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'Absen masuk 00:00 - 11:59. Absen pulang 12:00 - 23:59.', 0, 0, 'L', 1);
        } else {
            $pdf->Cell($width, $height, 'If any employee not listed, please make sure duty location of the employee and fingerprint has been registred.', T, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'For leave data, only approved leave are displayed. Leave on Saturday and Sunday are not counted.', 0, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'If employee out earlier, then system will recognize it as late.', 0, 0, 'L', 1);
            $pdf->Ln();
            $pdf->Cell($width, $height, 'In between 00:00 - 11:59. Out between 12:00 - 23:59.', 0, 0, 'L', 1);
            $pdf->Ln();
        }

        $pdf->Ln();
        $pdf->Output();

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