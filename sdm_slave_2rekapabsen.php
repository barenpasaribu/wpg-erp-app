<?php

    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/zLib.php';
    require_once 'lib/devLibrary.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/terbilang.php';
    $proses = $_GET['proses'];
    $lksiTgs = $_SESSION['empl']['lokasitugas'];
    ('' == $_POST['kdeOrg'] ? ($kdeOrg = $_GET['kdeOrg']) : ($kdeOrg = $_POST['kdeOrg']));
    ('' == $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
    ('' == $_POST['afdId'] ? ($afdId = $_GET['afdId']) : ($afdId = $_POST['afdId']));
    ('' == $_POST['tgl1'] ? ($tgl1 = tanggalsystem($_GET['tgl1'])) : ($tgl1 = tanggalsystem($_POST['tgl1'])));
    ('' == $_POST['tgl2'] ? ($tgl2 = tanggalsystem($_GET['tgl2'])) : ($tgl2 = tanggalsystem($_POST['tgl2'])));
    ('' == $_POST['tgl_1'] ? ($tgl_1 = tanggalsystem($_GET['tgl_1'])) : ($tgl_1 = tanggalsystem($_POST['tgl_1'])));
    ('' == $_POST['tgl_2'] ? ($tgl_2 = tanggalsystem($_GET['tgl_2'])) : ($tgl_2 = tanggalsystem($_POST['tgl_2'])));
    ('' == $_POST['periode'] ? ($periodeGaji = $_GET['periode']) : ($periodeGaji = $_POST['periode']));
    ('' == $_POST['periode'] ? ($periode = explode('-', $_GET['periode'])) : ($periode = explode('-', $_POST['periode'])));
    ('' == $_POST['kdUnit'] ? ($kdUnit = $_GET['kdUnit']) : ($kdUnit = $_POST['kdUnit']));
    ('' == $_POST['idKry'] ? ($idKry = $_GET['idKry']) : ($idKry = $_POST['idKry']));
    ('' == $_POST['tipeKary'] ? ($tipeKary = $_GET['tipeKary']) : ($tipeKary = $_POST['tipeKary']));
    ('' == $_POST['sistemGaji'] ? ($sistemGaji = $_GET['sistemGaji']) : ($sistemGaji = $_POST['sistemGaji']));
    if ('' != $kdOrg) {
        $kodeOrg = $kdOrg;
        if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
            $where = "  lokasitugas in ('".$kodeOrg."')";
            if ('' != $afdId) {
                $where = "  subbagian='".$afdId."'";
            }
        } else {
            if (4 < strlen($kodeOrg)) {
                $where = "  subbagian='".$kodeOrg."'";
            } else {
    //            $where = "  lokasitugas='".$kodeOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
                $where = "  lokasitugas='".$kodeOrg."'";
            }
        }
    } else {
        $kodeOrg = $_SESSION['empl']['lokasitugas'];
        $where = "  lokasitugas='".$kodeOrg."'";
    }

    if ('' != $tipeKary) {
        $where .= " and tipekaryawan='".$tipeKary."'";
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

    $sGetKary = 'select 
                    a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,subbagian 
                from 
                    '.$dbname.".datakaryawan a 
                left join 
                    ".$dbname.".sdm_5jabatan b 
                on 
                    a.kodejabatan=b.kodejabatan 
                where
                    isduplicate=0
                AND
                    ".$where.' '.$wherez.' 
                order 
                    by namakaryawan asc';

    $rGetkary = fetchData($sGetKary);
    foreach ($rGetkary as $row => $kar) {
        $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
        $nikkar[$kar['karyawanid']] = $kar['nik'];
        $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
        $sbgnb[$kar['karyawanid']] = $kar['subbagian'];
    }
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
        $dimanaPnjng = " substring(kodeorg,1,4)='".$kodeOrg."'";
        $dimanaPnjng2 = " substring(kodeorg,1,4)='".$kodeOrg."'";
        $dimanaPnjng3 = " substr(b.kodeorg,1,4)='".$kodeOrg."'";
        if ('' != $afdId) {
            $dimanaPnjng = " kodeorg like '".substr($afdId, 0, 4)."%'";
            $dimanaPnjng2 = " substring(kodeorg,1,4)='".substr($afdId, 0, 4)."'";
            $dimanaPnjng3 = " substr(b.kodeorg,1,4)='".substr($afdId, 0, 4)."'";
        }
    } else {
        if (4 < strlen($kodeOrg)) {
            $dimanaPnjng = " kodeorg='".$kodeOrg."'";
            $dimanaPnjng2 = " substring(kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
            $dimanaPnjng3 = " substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
        } else {
            $dimanaPnjng = " substring(kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
            $dimanaPnjng2 = " substring(kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
            $dimanaPnjng3 = " substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
        }
    }

    switch ($proses) {
        case 'preview':
            if ('' != $tgl_1 && '' != $tgl_2) {
                $tgl1 = $tgl_1;
                $tgl2 = $tgl_2;
            }

            $test = dates_inbetween($tgl1, $tgl2);
            if ('' == $tgl2 && '' == $tgl1) {
                echo 'warning: Both period required';
                exit();
            }

            $jmlHari = count($test);
            if (40 < $jmlHari) {
                echo 'warning: Invalid period range';
                exit();
            }

            $sAbsen = 'select kodeabsen from ' . $dbname . '.sdm_5absensi order by kodeabsen';
            
            $qAbsen = mysql_query($sAbsen);
            $jmAbsen = mysql_num_rows($qAbsen);
            $colSpan = (int)$jmAbsen + 2;
            echo "<table cellspacing='1' border='0' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td>No</td>\r\n        <td>" . $_SESSION['lang']['nama'] . "</td>\r\n        <td>" . $_SESSION['lang']['nik'] . "</td>\r\n        <td>" . $_SESSION['lang']['jabatan'] . "</td>\r\n        <td>" . $_SESSION['lang']['subunit'] . '</td>';
            
            $klmpkAbsn = [];
            foreach ($test as $ar => $isi) {
                $qwe = date('D', strtotime($isi));
                echo '<td width=5px align=center>';
                if ('Sun' == $qwe) {
                    echo '<font color=red>' . substr($isi, 8, 2) . '</font>';
                } else {
                    echo substr($isi, 8, 2);
                }

                echo '</td>';
            }
            
            while ($rKet = mysql_fetch_assoc($qAbsen)) {
                $klmpkAbsn[] = $rKet;
                echo '<td width=10px>' . $rKet['kodeabsen'] . '</td>';
            }
            
            echo "\r\n        <td>" . $_SESSION['lang']['total'] . "</td></tr></thead>\r\n        <tbody>";
            
            $resData[] = [];
            $hasilAbsn[] = [];
            $sAbsn = 'select absensi,tanggal,karyawanid,kodeorg,catu from ' . $dbname . ".sdm_absensidt \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng . '';
            // print_r($sAbsn);
            // echo "<br><br>";
            $rAbsn = fetchData($sAbsn);
            foreach ($rAbsn as $absnBrs => $resAbsn) {
                if (null != $resAbsn['absensi']) {
                    $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                    $notran[$resAbsn['karyawanid']][$resAbsn['tanggal']] .= 'ABSENSI:' . $resAbsn['kodeorg'] . '__';
                    $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
                    $catuBerasStat[$resAbsn['karyawanid']][$resAbsn['tanggal']] = $resAbsn['catu'];
                }
            }
            
            $sKehadiran = 'select absensi,tanggal,karyawanid,notransaksi from ' . $dbname . ".kebun_kehadiran_vw \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng2 . '';
            // print_r($sKehadiran);
            // echo "<br><br>";
            $rkehadiran = fetchData($sKehadiran);
            foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                if ('' != $resKhdrn['absensi']) {
                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                    $notran[$resKhdrn['karyawanid']][$resKhdrn['tanggal']] .= 'BKM:' . $resKhdrn['notransaksi'] . '__';
                    $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
                }
            }
            $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik,a.notransaksi from ' . $dbname . '.kebun_prestasi a left join ' . $dbname . ".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and " . $dimanaPnjng3 . " and b.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "'";
            // print_r($sPrestasi);
            // echo "<br><br>";
            $rPrestasi = fetchData($sPrestasi);
            foreach ($rPrestasi as $presBrs => $resPres) {
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
                $notran[$resPres['nik']][$resPres['tanggal']] .= 'BKM:' . $resPres['notransaksi'] . '__';
                $resData[$resPres['nik']][] = $resPres['nik'];
            }
            $dzstr = 'SELECT tanggal,nikmandor,a.notransaksi FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1,a.notransaksi FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            // print_r($dzstr);
            // echo "<br><br>";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $notran[$dzbar->nikmandor][$dzbar->tanggal] .= 'BKM:' . $dzbar->notransaksi . '__';
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            $dzstr = 'SELECT tanggal,nikmandor,a.notransaksi FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat,a.notransaksi FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            // print_r($dzstr);
            // echo "<br><br>";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $notran[$dzbar->nikmandor][$dzbar->tanggal] .= 'BKM:' . $dzbar->notransaksi . '__';
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            // $dzstr = 'SELECT a.tanggal,idkaryawan, a.notransaksi FROM ' . $dbname . ".vhc_runhk a\r\n        left join " . $dbname . ".datakaryawan b on a.idkaryawan=b.karyawanid\r\n        where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and notransaksi like '%" . substr($kodeOrg, 0, 4) . "%'\r\n        and " . $where . "\r\n    ";
            // // print_r($dzstr);
            // // echo "<br><br>";
            // $dzres = mysql_query($dzstr);
            // while ($dzbar = mysql_fetch_object($dzres)) {
            //     $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
            //     $notran[$dzbar->idkaryawan][$dzbar->tanggal] .= 'TRAKSI:' . $dzbar->notransaksi . '__';
            //     $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
            // }
            // die();
            function kirimnama($nama)
            {
                $qwe = explode(' ', $nama);
                foreach ($qwe as $kyu) {
                    $balikin .= $kyu . '__';
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
                    $balikin .= $tumpz . '__';
                }

                return $balikin;
            }

            $brt = [];
            $lmit = count($klmpkAbsn);
            $a = 0;
            foreach ($resData as $hslBrs => $hslAkhir) {
                if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                    ++$no;
                    echo '<tr class=rowcontent><td>' . $no . '</td>';
                    echo "\r\n                                <td>" . $namakar[$hslAkhir[0]] . "</td>\r\n                                <td>" . $nikkar[$hslAkhir[0]] . "</td>\r\n                                <td>" . $nmJabatan[$hslAkhir[0]] . "</td>\r\n                                <td>" . $sbgnb[$hslAkhir[0]] . "</td>\r\n                                ";
                    foreach ($test as $barisTgl => $isiTgl) {
                        if ('H' != $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']) {
                            echo "<td title='Click for detail.' style=\"cursor: pointer\" onclick=showpopup('" . $hslAkhir[0] . "','" . kirimnama($namakar[$hslAkhir[0]]) . "','" . tanggalnormal($isiTgl) . "','" . removeduplicate($notran[$hslAkhir[0]][$isiTgl]) . "',event)><font color=red>" . $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'] . '</font></td>';
                        } else {
                            $bgdt = '';
                            if (0 != count($catuBerasStat[$hslAkhir[0]][$isiTgl]) && 0 == $catuBerasStat[$hslAkhir[0]][$isiTgl]) {
                                $bgdt = 'bgcolor=yellow';
                            }

                            echo '<td ' . $bgdt . " title='Click for detail' style=\"cursor: pointer\" onclick=showpopup('" . $hslAkhir[0] . "','" . kirimnama($namakar[$hslAkhir[0]]) . "','" . tanggalnormal($isiTgl) . "','" . removeduplicate($notran[$hslAkhir[0]][$isiTgl]) . "',event)>" . $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'] . '</td>';
                            ++$totTgl[$isiTgl];
                        }

                        ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                    }
                    foreach ($klmpkAbsn as $brsKet => $hslKet) {
                        if ('H' != $hslKet['kodeabsen']) {
                            echo '<td width=5px align=right><font color=red>' . $brt[$hslAkhir[0]][$hslKet['kodeabsen']] . '</font></td>';
                        } else {
                            echo '<td width=5px  align=right>' . $brt[$hslAkhir[0]][$hslKet['kodeabsen']] . '</td>';
                        }

                        $subtot[$hslAkhir[0]]['total'] += $brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                    }
                    echo '<td width=5px  align=right>' . $subtot[$hslAkhir[0]]['total'] . '</td>';
                    $subtot['total'] = 0;
                    echo '</tr>';
                }
            }
            $coldt = count($klmpkAbsn);
            echo '<tr class=rowcontent><td colspan=5>' . $_SESSION['total'] . ' ' . $_SESSION['absensi'] . '</td>';
            foreach ($test as $barisTgl => $isiTgl) {
                echo '<td>' . $totTgl[$isiTgl] . '</td>';
            }
            echo '<td colspan=' . ($coldt + 1) . '>&nbsp;</td></tr>';
            echo '</tbody></table>';

            break;
        case 'pdf':
            $test = dates_inbetween($tgl1, $tgl2);
            if ('' == $tgl2 && '' == $tgl1) {
                echo 'warning: Both period required';
                exit();
            }

            $jmlHari = count($test);
            if (40 < $jmlHari) {
                echo 'warning:Invalid period range ' . $jmlHari;
                exit();
            }

            $sAbsen = 'select kodeabsen from ' . $dbname . '.sdm_5absensi order by kodeabsen';
            $qAbsen = mysql_query($sAbsen);
            $jmAbsen = mysql_num_rows($qAbsen);
            $colSpan = (int)$jmAbsen + 2;

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
                    global $period;
                    global $periode;
                    global $kdOrg;
                    global $kdeOrg;
                    global $tgl1;
                    global $tgl2;
                    global $where;
                    global $jmlHari;
                    global $test;
                    global $klmpkAbsn;
                    global $tipeKary;
                    global $sistemGaji;
                    global $dimanaPnjng;
                    global $afdId;
                    global $dimanaPnjng2;
                    global $dimanaPnjng3;
                    $jmlHari = $jmlHari * 1.5;
                    $cols = 247.5;
                    $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='" . $_SESSION['org']['kodeorganisasi'] . "'");
                    $orgData = fetchData($query);
                    $width = $this->w - $this->lMargin - $this->rMargin;
                    $height = 20;
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
                    $this->Cell($width - 100, $height, 'Tel: ' . $orgData[0]['telepon'], 0, 1, 'L');
                    $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
                    $this->Ln();
                    $this->SetFont('Arial', 'B', 10);
                    $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['rkpAbsen'] . ' ' . $sistemGaji, '', 0, 'L');
                    $this->Ln();
                    $this->Ln();
                    $this->Cell($width, $height, strtoupper($_SESSION['lang']['rkpAbsen']), '', 0, 'C');
                    $this->Ln();
                    $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']) . ' :' . tanggalnormal($tgl1) . ' s.d. ' . tanggalnormal($tgl2), '', 0, 'C');
                    $this->Ln();
                    $this->Ln();
                    $this->SetFont('Arial', 'B', 7);
                    $this->SetFillColor(220, 220, 220);
                    $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
                    $this->Cell(13 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
                    $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['jabatan'], 1, 0, 'C', 1);
                    $this->GetX();
                    $this->SetY($this->GetY());
                    $this->SetX($this->GetX() + $cols);
                    foreach ($test as $ar => $isi) {
                        $this->Cell(1.5 / 100 * $width, $height, substr($isi, 8, 2), 1, 0, 'C', 1);
                        $akhirX = $this->GetX();
                    }
                    $this->SetY($this->GetY());
                    $this->SetX($akhirX);
                    $sAbsen = 'select kodeabsen from ' . $dbname . '.sdm_5absensi order by kodeabsen';
                    $qAbsen = mysql_query($sAbsen);
                    while ($rAbsen = mysql_fetch_assoc($qAbsen)) {
                        $klmpkAbsn[] = $rAbsen;
                        $this->Cell(2 / 100 * $width, $height, $rAbsen['kodeabsen'], 1, 0, 'C', 1);
                    }
                    $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['total'], 1, 1, 'C', 1);
                }

                public function Footer()
                {
                    $this->SetY(-15);
                    $this->SetFont('Arial', 'I', 8);
                    $this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
                }
            }

            $pdf = new PDF('L', 'pt', 'Legal');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 15;
            $pdf->AddPage();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', '', 7);
            $subtot = [];
            if ('' != $kdOrg) {
                $kodeOrg = $kdOrg;
                if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
                    $where = "  lokasitugas in ('" . $kodeOrg . "')";
                    if ('' != $afdId) {
                        $where = "  subbagian='" . $afdId . "'";
                    }
                } else {
                    if (4 < strlen($kdOrg)) {
                        $where = "  subbagian='" . $kdOrg . "'";
                    } else {
                        $where = "  lokasitugas='" . $kdOrg . "' and (subbagian='0' or subbagian is null or subbagian='')";
                    }
                }
            } else {
                $kodeOrg = $_SESSION['empl']['lokasitugas'];
                $where = "  lokasitugas='" . $kodeOrg . "'";
            }

            if ('' != $tipeKary) {
                $where .= " and tipekaryawan='" . $tipeKary . "'";
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

            $sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan from ' . $dbname . ".datakaryawan a \r\n                   left join " . $dbname . ".sdm_5jabatan b on a.kodejabatan=b.kodejabatan where\r\n                   " . $where . ' ' . $wherez . ' order by namakaryawan asc';
            $rGetkary = fetchData($sGetKary);
            $namakar = [];
            $nmJabatan = [];
            foreach ($rGetkary as $row => $kar) {
                $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
                $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
            }
            $sAbsn = 'select absensi,tanggal,karyawanid from ' . $dbname . ".sdm_absensidt \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng . '';
            $rAbsn = fetchData($sAbsn);
            foreach ($rAbsn as $absnBrs => $resAbsn) {
                if (null != $resAbsn['absensi']) {
                    $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                    $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
                }
            }
            $sKehadiran = 'select absensi,tanggal,karyawanid from ' . $dbname . ".kebun_kehadiran_vw \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng2 . '';
            $rkehadiran = fetchData($sKehadiran);
            foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                if ('' != $resKhdrn['absensi']) {
                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                    $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
                }
            }
            $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from ' . $dbname . '.kebun_prestasi a left join ' . $dbname . ".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and " . $dimanaPnjng3 . " and b.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "'";
            $rPrestasi = fetchData($sPrestasi);
            foreach ($rPrestasi as $presBrs => $resPres) {
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
                $resData[$resPres['nik']][] = $resPres['nik'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1 FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            $dzstr = 'SELECT a.tanggal,idkaryawan FROM ' . $dbname . ".vhc_runhk a\r\n        left join " . $dbname . ".datakaryawan b on a.idkaryawan=b.karyawanid\r\n        where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and notransaksi like '%" . $kodeOrg . "%'\r\n        and " . $where . "\r\n    ";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
            }
            $brt = [];
            $lmit = count($klmpkAbsn);
            $a = 0;
            foreach ($resData as $hslBrs => $hslAkhir) {
                if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                    ++$no;
                    $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                    $pdf->Cell(13 / 100 * $width, $height, strtoupper($namakar[$hslAkhir[0]]), 1, 0, 'L', 1);
                    $pdf->Cell(10 / 100 * $width, $height, strtoupper($nmJabatan[$hslAkhir[0]]), 1, 0, 'L', 1);
                    foreach ($test as $barisTgl => $isiTgl) {
                        $pdf->Cell(1.5 / 100 * $width, $height, $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'], 1, 0, 'C', 1);
                        $akhirX = $pdf->GetX();
                        ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                    }
                    for ($a = 0; $a < $lmit; ++$a) {
                        $pdf->Cell(2 / 100 * $width, $height, $brt[$hslAkhir[0]][$klmpkAbsn[$a]['kodeabsen']], 1, 0, 'C', 1);
                        $subtot[$hslAkhir[0]]['total'] += $brt[$hslAkhir[0]][$klmpkAbsn[$a]['kodeabsen']];
                    }
                    $pdf->Cell(5 / 100 * $width, $height, $subtot[$hslAkhir[0]]['total'], 1, 1, 'R', 1);
                    $subtot[$hslAkhir[0]]['total'] = 0;
                }
            }
            $pdf->Output();

            break;
        case 'excel':
            $test = dates_inbetween($tgl1, $tgl2);
            if ('' == $tgl2 && '' == $tgl1) {
                echo 'warning: Both period required';
                exit();
            }

            $jmlHari = count($test);
            if (40 < $jmlHari) {
                echo 'warning: Invalid period range';
                exit();
            }

            $sAbsen = 'select kodeabsen from ' . $dbname . '.sdm_5absensi order by kodeabsen';
            $qAbsen = mysql_query($sAbsen);
            $jmAbsen = mysql_num_rows($qAbsen);
            $colSpan = (int)$jmAbsen + 2;
            $colatas = $jmlHari + $colSpan + 3;
            $stream .= "<table border='0'><tr><td colspan='" . $colatas . "' align=center>" . strtoupper($_SESSION['lang']['rkpAbsen']) . ' ' . $sistemGaji . "</td></tr>\r\n        <tr><td colspan='" . $colatas . "' align=center>" . strtoupper($_SESSION['lang']['periode']) . ' :' . tanggalnormal($tgl1) . ' s.d. ' . tanggalnormal($tgl2) . "</td></tr><tr><td colspan='" . $colatas . "'>&nbsp;</td></tr></table>";
            $stream .= "<table cellspacing='1' border='1' class='sortable'>\r\n        <thead class=rowheader>\r\n        <tr>\r\n        <td bgcolor=#DEDEDE align=center>No</td>\r\n        <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['nama'] . "</td>\r\n        <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['nik'] . "</td>\r\n        <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['jabatan'] . "</td>\r\n        <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['bagian'] . "</td>\r\n         <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['subunit'] . '</td>';
            $klmpkAbsn = [];
            foreach ($test as $ar => $isi) {
                $qwe = date('D', strtotime($isi));
                if ('Sun' == $qwe) {
                    $stream .= '<td bgcolor=red align=center width=5px align=center><font color=white>' . substr($isi, 8, 2) . '</font></td>';
                } else {
                    $stream .= '<td bgcolor=#DEDEDE align=center width=5px align=center>' . substr($isi, 8, 2) . '</td>';
                }
            }
            while ($rKet = mysql_fetch_assoc($qAbsen)) {
                $klmpkAbsn[] = $rKet;
                $stream .= '<td bgcolor=#DEDEDE align=center width=10px>' . $rKet['kodeabsen'] . '</td>';
            }
            $stream .= "\r\n        <td bgcolor=#DEDEDE align=center>" . $_SESSION['lang']['total'] . "</td></tr></thead>\r\n        <tbody>";
            if ('' != $kdOrg) {
                $kodeOrg = $kdOrg;
                if ('HOLDING' == $_SESSION['empl']['tipelokasitugas'] || 'KANWIL' == $_SESSION['empl']['tipelokasitugas']) {
                    $where = "  lokasitugas in ('" . $kodeOrg . "')";
                    if ('' != $afdId) {
                        $where = "  subbagian='" . $afdId . "'";
                    }
                } else {
                    if (4 < strlen($kdOrg)) {
                        $where = "  subbagian='" . $kdOrg . "'";
                    } else {
                        $where = "  lokasitugas='" . $kdOrg . "' and (subbagian='0' or subbagian is null or subbagian='')";
                    }
                }
            } else {
                $kodeOrg = $_SESSION['empl']['lokasitugas'];
                $where = "  lokasitugas='" . $kodeOrg . "'";
            }

            if ('' != $tipeKary) {
                $where .= " and tipekaryawan='" . $tipeKary . "'";
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

            $resData[] = [];
            $hasilAbsn[] = [];
            $sGetKary = 'select a.karyawanid,a.nik,b.namajabatan,a.namakaryawan,c.nama,subbagian from ' . $dbname . ".datakaryawan a \r\n           left join " . $dbname . ".sdm_5jabatan b on a.kodejabatan=b.kodejabatan \r\n           left join " . $dbname . ".sdm_5departemen c on a.bagian=c.kode\r\n           where\r\n           " . $where . ' ' . $wherez . 'order by namakaryawan asc';
            $namakar = [];
            $nmJabatan = [];
            $rGetkary = fetchData($sGetKary);
            foreach ($rGetkary as $row => $kar) {
                $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
                $nikkar[$kar['karyawanid']] = $kar['nik'];
                $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
                $nmBagian[$kar['karyawanid']] = $kar['nama'];
                $sbgnb[$kar['karyawanid']] = $kar['subbagian'];
            }
            $sAbsn = 'select absensi,tanggal,karyawanid,catu from ' . $dbname . ".sdm_absensidt \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng . '';
            $rAbsn = fetchData($sAbsn);
            foreach ($rAbsn as $absnBrs => $resAbsn) {
                if (null != $resAbsn['absensi']) {
                    $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                    $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
                    $catuBerasStat[$resAbsn['karyawanid']][$resAbsn['tanggal']] = $resAbsn['catu'];
                }
            }
            $sKehadiran = 'select absensi,tanggal,karyawanid from ' . $dbname . ".kebun_kehadiran_vw \r\n                            where tanggal between  '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng2 . '';
            $rkehadiran = fetchData($sKehadiran);
            foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                if ('' != $resKhdrn['absensi']) {
                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                    $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
                }
            }
            $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from ' . $dbname . '.kebun_prestasi a left join ' . $dbname . ".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and " . $dimanaPnjng3 . " and b.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "'";
            $rPrestasi = fetchData($sPrestasi);
            foreach ($rPrestasi as $presBrs => $resPres) {
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
                $resData[$resPres['nik']][] = $resPres['nik'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1 FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM ' . $dbname . ".kebun_aktifitas a\r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . " and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat FROM " . $dbname . ".kebun_aktifitas a \r\n    left join " . $dbname . ".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join " . $dbname . ".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and " . $dimanaPnjng3 . ' and c.namakaryawan is not NULL';
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
            }
            $dzstr = 'SELECT a.tanggal,idkaryawan FROM ' . $dbname . ".vhc_runhk a\r\n        left join " . $dbname . ".datakaryawan b on a.idkaryawan=b.karyawanid\r\n        where a.tanggal between '" . $tgl1 . "' and '" . $tgl2 . "' and notransaksi like '%" . $kodeOrg . "%'\r\n        and " . $where . "\r\n    ";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
                $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
            }
            $brt = [];
            $lmit = count($klmpkAbsn);
            $a = 0;
            foreach ($resData as $hslBrs => $hslAkhir) {
                if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                    ++$no;
                    $stream .= '<tr><td>' . $no . '</td>';
                    $stream .= "\r\n                                <td>" . $namakar[$hslAkhir[0]] . "</td>\r\n                                <td>'" . $nikkar[$hslAkhir[0]] . "</td>\r\n                                <td>" . $nmJabatan[$hslAkhir[0]] . "</td>\r\n                                <td>" . $nmBagian[$hslAkhir[0]] . "</td>\r\n                                <td>" . $sbgnb[$hslAkhir[0]] . "</td>\r\n                                ";
                    foreach ($test as $barisTgl => $isiTgl) {
                        if ('H' != $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']) {
                            $stream .= '<td><font color=red>' . $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'] . '</font></td>';
                        } else {
                            $bgdt = '';
                            if (0 != count($catuBerasStat[$hslAkhir[0]][$isiTgl]) && 0 == $catuBerasStat[$hslAkhir[0]][$isiTgl]) {
                                $bgdt = 'bgcolor=yellow';
                            }

                            $stream .= '<td ' . $bgdt . '>' . $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'] . '</td>';
                            ++$totTgl[$isiTgl];
                        }

                        ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                    }
                    foreach ($klmpkAbsn as $brsKet => $hslKet) {
                        if ('H' != $hslKet['kodeabsen']) {
                            $stream .= '<td width=5px  align=right><font color=red>' . $brt[$hslAkhir[0]][$hslKet['kodeabsen']] . '</font></td>';
                        } else {
                            $stream .= '<td width=5px  align=right>' . $brt[$hslAkhir[0]][$hslKet['kodeabsen']] . '</td>';
                        }

                        $subtot[$hslAkhir[0]]['total'] += $brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                    }
                    $stream .= '<td width=5px  align=right>' . $subtot[$hslAkhir[0]]['total'] . '</td>';
                    $subtot['total'] = 0;
                    $stream .= '</tr>';
                }
            }
            $coldt = count($klmpkAbsn);
            $stream .= '<tr class=rowcontent><td colspan=6>' . $_SESSION['lang']['total']['absensi'] . '</td>';
            foreach ($test as $barisTgl => $isiTgl) {
                $stream .= '<td>' . $totTgl[$isiTgl] . '</td>';
            }
            $stream .= '<td colspan=' . ($coldt + 1) . '>&nbsp;</td></tr>';
            $stream .= '</tbody></table>';
            $stream .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
            if ('' != $period) {
                $art = $period;
                $art = $art[1] . $art[0];
            }

            if ('' != $periode) {
                $art = $periode;
                $art = $art[1] . $art[0];
            }

            if ('' != $kdeOrg) {
                $kodeOrg = $kdeOrg;
            }

            if ('' != $kdOrg) {
                $kodeOrg = $kdOrg;
            }

            $nop_ = 'RekapAbsen' . $art . '__' . $kodeOrg;
            if (0 < strlen($stream)) {
                if ($handle = opendir('tempExcel')) {
                    while (false != ($file = readdir($handle))) {
                        if ('.' != $file && '..' != $file) {
                            @unlink('tempExcel/' . $file);
                        }
                    }
                    closedir($handle);
                }

                $handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');
                if (!fwrite($handle, $stream)) {
                    echo "<script language=javascript1.2>\r\n                        parent.window.alert('Can't convert to excel format');\r\n                        </script>";
                    exit();
                }

                echo "<script language=javascript1.2>\r\n                        window.location='tempExcel/" . $nop_ . ".xls';\r\n                        </script>";
                closedir($handle);
            }

            break;
        case 'getTgl':
            if ('' != $periode) {
                $tgl = $periode;
                $tanggal = $tgl[0] . '-' . $tgl[1];
                $dmna .= " and periode='" . $tanggal . "'";
            } else {
                if ('' != $period) {
                    $tgl = $period;
                    $tanggal = $tgl[0] . '-' . $tgl[1];
                    $dmna .= " and periode='" . $tanggal . "'";
                }
            }

            if ('' != $sistemGaji) {
                $dmna .= " and jenisgaji='" . substr($sistemGaji, 0, 1) . "'";
            }

            if ('' == $kdUnit) {
                $kdUnit = $_SESSION['empl']['lokasitugas'];
            }

            $sTgl = 'select distinct tanggalmulai,tanggalsampai from ' . $dbname . ".sdm_5periodegaji where kodeorg='" . substr($kdUnit, 0, 4) . "' " . $dmna . ' ';
            $qTgl = mysql_query($sTgl);
            $rTgl = mysql_fetch_assoc($qTgl);
            echo tanggalnormal($rTgl['tanggalmulai']) . '###' . tanggalnormal($rTgl['tanggalsampai']);

            break;
        case 'getKry':
            $optKry = "<option value=''>" . $_SESSION['lang']['pilihdata'] . '</option>';
            if (4 < strlen($kdeOrg)) {
                $where = " lokasitugas='" . substr($kdeOrg, 0, 4) . "'";
            } else {
                $where = " lokasitugas='" . $kdeOrg . "' and (subbagian='0' or subbagian is null or subbagian='')";
            }

            $sKry = 'select nik,karyawanid,namakaryawan from ' . $dbname . '.datakaryawan where ' . $where . ' order by namakaryawan asc';
            $qKry = mysql_query($sKry);
            while ($rKry = mysql_fetch_assoc($qKry)) {
                $optKry .= '<option value=' . $rKry['karyawanid'] . '>' . $rKry['nik'] . '-' . $rKry['namakaryawan'] . '</option>';
            }
            $optPeriode = "<option value=''>" . $_SESSION['lang']['pilihdata'] . '</option>';
            $sPeriode = 'select distinct periode from ' . $dbname . ".sdm_5periodegaji where kodeorg='" . $kdeOrg . "'";
            $qPeriode = mysql_query($sPeriode);
            while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
                $optPeriode .= '<option value=' . $rPeriode['periode'] . '>' . substr(tanggalnormal($rPeriode['periode']), 1, 7) . '</option>';
            }
            echo $optKry . '###' . $optPeriode;

            break;
    //    case 'getBagian':
    //        if ($kdUnit!='') {
    //            $str = "SELECT DISTINCT d.bagian, s.nama
    //                from datakaryawan d
    //                INNER JOIN user u ON u.karyawanid=d.karyawanid
    //                INNER JOIN organisasi o ON o.kodeorganisasi=d.lokasitugas
    //                INNER JOIN sdm_5departemen s ON s.kode=d.bagian";
    ////            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    ////                $str .= "WHERE d.lokasitugas IN  (SELECT kodeorganisasi FROM organisasi WHERE induk='" . $_SESSION['empl']['kodeorganisasi'] . "') ";
    ////            } else {
    //                $str .= "WHERE d.lokasitugas ='" . $kdUnit . "' ";
    ////            }
    //            $bagian = mysql_query($str);
    //            $result = "";
    //            while ($bag = mysql_fetch_assoc($bagian)) {
    //                $result .= '<option value=' . $bag['kode'] . '>' . $bag[nama] . '</option>';
    //            }
    //            echo $result;
    //        }
    //        break;
        case 'getPeriode':
            if ('' != $periodeGaji) {
                $were = " kodeorg='" . $kdUnit . "' and periode='" . $periodeGaji . "' and jenisgaji='" . $sistemGaji . "'";
            } else {
                $were = " kodeorg='" . $kdUnit . "'";
            }

            $optPeriode = "<option value=''>" . $_SESSION['lang']['pilihdata'] . '</option>';
            $sPeriode = 'select distinct periode from ' . $dbname . '.sdm_5periodegaji where ' . $were . '';
            $qPeriode = mysql_query($sPeriode);
            while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
                $optPeriode .= '<option value=' . $rPeriode['periode'] . '>' . substr(tanggalnormal($rPeriode['periode']), 1, 7) . '</option>';
            }
            $optAfd = "<option value=''>" . $_SESSION['lang']['all'] . '</option>';
            $sSub = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . ".organisasi where induk='" . $kdUnit . "'  order by namaorganisasi asc";
            $qSub = mysql_query($sSub);
            while ($rSub = mysql_fetch_assoc($qSub)) {
                $optAfd .= "<option value='" . $rSub['kodeorganisasi'] . "'>" . $rSub['namaorganisasi'] . '</option>';
            }

    //        if ($kdUnit != '') $kdUnit = 'SSRO';
            $str = "SELECT DISTINCT d.bagian, s.nama 
                    from datakaryawan d 
                    INNER JOIN user u ON u.karyawanid=d.karyawanid
                    INNER JOIN organisasi o ON o.kodeorganisasi=d.lokasitugas
                    INNER JOIN sdm_5departemen s ON s.kode=d.bagian ";
    //            if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
    //                $str .= "WHERE d.lokasitugas IN  (SELECT kodeorganisasi FROM organisasi WHERE induk='" . $_SESSION['empl']['kodeorganisasi'] . "') ";
    //            } else {
            $str .= "WHERE d.lokasitugas ='" . $kdUnit . "' ";
    //            }
            $bagian = mysql_query($str);
            $result = "<option value=''>" . $_SESSION['lang']['all'] . '</option>';
            while ($bag = mysql_fetch_assoc($bagian)) {
                $result .= '<option value=' . $bag['kode'] . '>' . $bag['nama'] . '</option>';
            }

            $res = $optAfd . '####' . $optPeriode . '####' . $result;
            echo $res;
            echoMessage("res ",$str);
            break;
        case 'getPeriodeGaji5':
            $optPeriode = "<option value=''>" . $_SESSION['lang']['pilihdata'] . '</option>';
            $optPeriode2 = $optPeriode;
            $sPeriode = 'select distinct periode from ' . $dbname . ".sdm_5periodegaji where kodeorg='" . $_POST['kdUnit'] . "' order by periode desc";
            $qPeriode = mysql_query($sPeriode);
            while ($rPeriode = mysql_fetch_assoc($qPeriode)) {
                $optPeriode .= '<option value=' . $rPeriode['periode'] . '>' . substr(tanggalnormal($rPeriode['periode']), 1, 7) . '</option>';
            }
            $sPeriode2 = 'select distinct periode from ' . $dbname . ".sdm_5periodegaji where kodeorg='" . $_POST['kdUnit'] . "' order by periode asc";
            $qPeriode2 = mysql_query($sPeriode2);
            while ($rPeriode2 = mysql_fetch_assoc($qPeriode2)) {
                $optPeriode2 .= '<option value=' . $rPeriode2['periode'] . '>' . substr(tanggalnormal($rPeriode2['periode']), 1, 7) . '</option>';
            }
            echo $optPeriode2 . '####' . $optPeriode;

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