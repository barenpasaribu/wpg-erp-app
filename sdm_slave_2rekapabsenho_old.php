<?php
    echo "<pre>";
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/terbilang.php';

    $proses = $_GET['proses'];

    $lokasitugas = $_SESSION['empl']['lokasitugas'];
    ('' == $_POST['tanggal1'] ? ($tanggal1 = $_GET['tanggal1']) : ($tanggal1 = $_POST['tanggal1']));
    ('' == $_POST['tanggal2'] ? ($tanggal2 = $_GET['tanggal2']) : ($tanggal2 = $_POST['tanggal2']));
    ('' == $_POST['karyawanid'] ? ($karyawanid = $_GET['karyawanid']) : ($karyawanid = $_POST['karyawanid']));
    $tangsys1 = putertanggal($tanggal1);
    $tangsys2 = putertanggal($tanggal2);

    $skaryawan = 'select a.karyawanid, b.namajabatan, a.namakaryawan, a.bagian, c.nama from '.$dbname.".datakaryawan a 
    left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan 
    left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode 
    where a.lokasitugas like '%H' and ((a.tanggalkeluar >= '".$tangsys1."' and a.tanggalkeluar <= '".$tangsys2."') or a.tanggalkeluar is NULL)
    and isduplicate=0 order by namakaryawan asc";

    $rkaryawan = fetchData($skaryawan);
    foreach ($rkaryawan as $row => $kar) {
        $karyawan[$kar['karyawanid']]['id'] = $kar['karyawanid'];
        $karyawan[$kar['karyawanid']]['nama'] = $kar['namakaryawan'];
        $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
        $jabakar[$kar['karyawanid']] = $kar['namajabatan'];
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
    
    $str = 'SELECT a.karyawanid, substr(a.tanggal,1,10) as tanggal, a.jam as jam, a.jamPlg as jam2, a.absensi, b.karyawanid, b.namakaryawan 
    FROM '.$dbname.'.sdm_absensidt a 
    LEFT JOIN '.$dbname.".datakaryawan b 
    on a.karyawanid=b.karyawanid 
    WHERE 
    substr(tanggal,1,10) between '".$tangsys1."' 
    and 
    '".$tangsys2."' 
    and a.kodeorg like '%H' ORDER BY tanggal DESC";
    $res = mysql_query($str);

    while ($bar = mysql_fetch_object($res)) {
        if (!isset($bar->karyawanid)) {
        } else {
            $karyawan[$bar->karyawanid]['id'] = $bar->karyawanid;
            $karyawan[$bar->karyawanid]['nama'] = $bar->namakaryawan;
            $presensi[$bar->karyawanid]['m'.$bar->tanggal] = $bar->jam;
            $presensi[$bar->karyawanid]['k'.$bar->tanggal] = $bar->jam2;
            $presensi[$bar->karyawanid]["tipe".$bar->tanggal] = $bar->absensi;
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

                    if (isset($presensi[$kar['id']]['m'.$tang]) || isset($presensi[$kar['id']]['k'.$tang])) {
                        $ontime = true;
                        if ($presensi[$kar['id']]['tipe'.$tang] == "H") {
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
                        }else{
                            if ($presensi[$kar['id']]['tipe'.$tang] == "D1" || $presensi[$kar['id']]['tipe'.$tang] == "D2"){
                                $pres = '&nbsp;'.$presensi[$kar['id']]['tipe'.$tang];
                                ++$dinas;
                            }else{
                                if ('Sat' == $hari || 'Sun' == $hari) {
                                }else{
                                    $pres = '&nbsp;'.$presensi[$kar['id']]['tipe'.$tang];
                                    ++$cuti;
                                }
                                
                            }
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

                    if ('' == $pres) {
                        ++$mangkir;
                    }

                    $stream .= '<td valign=top align=center'.$bgcolor.'>'.$pres.'</td>';
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
        $stream .= 'Bila karyawan tertentu tidak/muncul, harap dipastikan data Lokasi Tugas-nya adalah (H) dan telah terdaftar PIN Fingerprint-nya.</br>';
        $stream .= 'Hanya Ijin/Cuti yang telah disetujui oleh atasan dan HRD yang ditampilkan. Cuti Sabtu/Minggu tidak dihitung.</br>';
        $stream .= 'Bila karyawan tidak absen masuk/pulang maka dianggap telat.</br>';
        $stream .= 'Absen masuk 00:00 - 11:59. Absen pulang 12:00 - 23:59.</br>';
        $stream .= 'Jam masuk 08:00 dan Jam Pulang 17:00</br>';
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
            global $tanggal1;
            global $tanggal2;
            global $karyawanid;
            global $tangsys1;
            global $tangsys2;
            global $tanggaltanggal;
            global $jumlahhari;
            $cols = 247.5;
            $query = selectQuery($dbname, 'organisasi', 'alamat, telepon, logo', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
            $orgData = fetchData($query);
            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 15;
            if (isset($orgData[0]['logo'])) {
                $this->Image($orgData[0]['logo'], $this->lMargin, $this->tMargin, 70);
            }

            
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
            $this->Cell($width - 5, $height, $_SESSION['lang']['rkpAbsen'].' HO', '', 0, 'C');
            $this->Ln();
            $this->Cell($width - 5, $height, $_SESSION['lang']['periode'].' : '.$tanggal1.' s.d. '.$tanggal2, '', 0, 'C');
            $this->Ln();
            $this->Ln();
            $this->SetFont('Arial', 'B', 7);
            $this->SetFillColor(220, 220, 220);
            $this->Cell(2 / 100 * $width, $height, 'No', TRL, 0, 'C', 1);
            $this->Cell(7.3 / 100 * $width, $height, $_SESSION['lang']['namakaryawan'], TRL, 0, 'C', 1);
            $this->Cell(2.7 / 100 * $width * $jumlahhari, $height, $_SESSION['lang']['tanggal'], 1, 0, 'C', 1);
            if ('ID' == $_SESSION['language']) {
                $this->Cell(2.7 / 100 * $width, $height, 'Hadir', TRL, 0, 'C', 1);
                $this->Cell(2.7 / 100 * $width, $height, 'Telat', TRL, 0, 'C', 1);
                $this->Cell(2.7 / 100 * $width, $height, 'Dinas', TRL, 0, 'C', 1);
            } else {
                $this->Cell(2.7 / 100 * $width, $height, 'Present', TRL, 0, 'C', 1);
                $this->Cell(2.7 / 100 * $width, $height, 'Late', TRL, 0, 'C', 1);
                $this->Cell(2.7 / 100 * $width, $height, 'Duty', TRL, 0, 'C', 1);
            }

            $this->Ln();
            $this->Cell(2 / 100 * $width, $height, '', BRL, 0, 'C', 1);
            $this->Cell(7.3 / 100 * $width, $height, '', BRL, 0, 'C', 1);
            if (!empty($tanggaltanggal)) {
                foreach ($tanggaltanggal as $tang) {
                    $hari = date('D', strtotime($tang));
                    $qwe = substr($tang, 8, 2);
                    if ('Sat' == $hari || 'Sun' == $hari) {
                        $this->SetTextColor(255, 0, 0);
                    } else {
                        $this->SetTextColor(0, 0, 0);
                    }

                    $this->Cell(2.7 / 100 * $width, $height, $qwe, 1, 0, 'C', 1);
                }
            }

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
                    $dinas = 0;
                    ++$no;
                    $pdf->Cell(2 / 100 * $width, $height, $no, TRL, 0, 'R', 1);
                    $pdf->Cell(7.3 / 100 * $width, $height, $kar['nama'], TRL, 0, 'L', 1);
                    if (!empty($tanggaltanggal)) {
                        foreach ($tanggaltanggal as $tang) {
                            $hari = date('D', strtotime($tang));
                            $pres = '';

                            if (isset($presensi[$kar['id']]['m'.$tang]) || isset($presensi[$kar['id']]['k'.$tang])) {
                                $ontime = true;
                                $ontime2 = true;

                                if ($presensi[$kar['id']]['tipe'.$tang] == "H") {
                                    if (isset($presensi[$kar['id']]['m'.$tang])) {
                                        $pres = substr($presensi[$kar['id']]['m'.$tang], 0, 5);
                                        if (substr($presensi[$kar['id']]['m'.$tang], 0, 5) <= '08:00') {
                                        } else {
                                            $ontime = false;
                                            $ontime2 = false;
                                        }
                                    } else {
                                        $ontime = false;
                                    }
    
                                    if (isset($presensi[$kar['id']]['k'.$tang])) {
                                        if ('17:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                        } else {
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
    
                                    if ($ontime2) {
                                        $pdf->SetTextColor(0, 0, 0);
                                    } else {
                                        $pdf->SetTextColor(255, 0, 0);
                                    }
                                }else{
                                    if ($presensi[$kar['id']]['tipe'.$tang] == "D1" || $presensi[$kar['id']]['tipe'.$tang] == "D2"){
                                        $pres = $presensi[$kar['id']]['tipe'.$tang];
                                        ++$dinas;
                                    }else{
                                        if ('Sat' == $hari || 'Sun' == $hari) {
                                        }else{
                                            $pres = $presensi[$kar['id']]['tipe'.$tang];
                                            ++$cuti;
                                        }
                                        
                                    }
                                }
                            }

                            if ('Sat' == $hari || 'Sun' == $hari) {
                                $pdf->SetFillColor(255, 224, 224);
                                if ('' == $pres) {
                                    $pres = ' ';
                                }
                            } else {
                                $pdf->SetFillColor(255, 255, 255);
                            }

                            if ('' == $pres) {
                                ++$mangkir;
                            }

                            $pdf->Cell(2.7 / 100 * $width, $height, $pres, TRL, 0, 'L', 1);
                        }
                    }

                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(2.7 / 100 * $width, $height, $hadir, TRL, 0, 'R', 1);
                    $pdf->Cell(2.7 / 100 * $width, $height, $telat, TRL, 0, 'R', 1);
                    $pdf->Cell(2.7 / 100 * $width, $height, $dinas, TRL, 0, 'R', 1);
                    $pdf->Ln();
                    $pdf->Cell(2 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                    $pdf->Cell(7.3 / 100 * $width, $height, $jabakar[$kar['id']], BRL, 0, 'L', 1);
                    if (!empty($tanggaltanggal)) {
                        foreach ($tanggaltanggal as $tang) {
                            $pres = '';
                            if (isset($presensi[$kar['id']]['k'.$tang])) {
                                $ontime = true;
                                $pres .= substr($presensi[$kar['id']]['k'.$tang], 0, 5);
                                if ('2013-07-09' <= $tang && $tang <= '2013-08-08') {
                                    if ('16:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                    } else {
                                        $ontime = false;
                                    }
                                } else {
                                    if ('17:00' <= substr($presensi[$kar['id']]['k'.$tang], 0, 5)) {
                                    } else {
                                        $ontime = false;
                                    }
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

                            $pdf->Cell(2.7 / 100 * $width, $height, $pres, BRL, 0, 'L', 1);
                        }
                    }

                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                    $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                    $pdf->Cell(2.7 / 100 * $width, $height, '', BRL, 0, 'R', 1);
                    $pdf->Ln();
                }
            }

            $stream .= '.</br>';
            $stream .= '.</br>';
            $stream .= '</br>';
            $stream .= '.</br>';
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

            $nop_ = 'RekapAbsen_HO_'.$tangsys1.'_'.$tangsys2;
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