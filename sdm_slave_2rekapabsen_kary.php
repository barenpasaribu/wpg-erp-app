<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
$kdOrg = $_POST['kdeOrg'];
$tgl_1 = tanggalsystem($_POST['tgl_1']);
$tgl_2 = tanggalsystem($_POST['tgl_2']);
$periodeGaji = $_POST['period'];
$periode = explode('-', $_POST['periode']);
$idKry = $_POST['idKry'];
$a = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTgs."' and periode='".$periodeGaji."'";
$b = mysql_query($a);
$c = mysql_fetch_assoc($b);
$tgl_1 = $c['tanggalmulai'];
$tgl_2 = $c['tanggalsampai'];
switch ($proses) {
    case 'preview':
        if ('' != $kdOrg) {
            $kodeOrg = $kdOrg;
            if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
                $where = '  lokasitugas in ('.$kodeOrg.')';
            } else {
                if (4 < strlen($kdOrg)) {
                    $where = "  lokasitugas='".substr($kdOrg, 0, 4)."'";
                } else {
                    $where = "  lokasitugas='".$kdOrg."'";
                }
            }

            if ('' != $tgl_1 && '' != $tgl_2) {
                $tgl1 = $tgl_1;
                $tgl2 = $tgl_2;
            }

            $test = dates_inbetween($tgl1, $tgl2);
            if ('' == $tgl2 && '' == $tgl1) {
                echo 'warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong';
                exit();
            }

            $jmlHari = count($test);
            if (31 < $jmlHari) {
                echo 'warning:Range tanggal tidak valid';
                exit();
            }

            if ('' != $idKry) {
                $where .= ' and karyawanid='.$idKry.'';
                $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
                $qAbsen = mysql_query($sAbsen);
                $jmAbsen = mysql_num_rows($qAbsen);
                $colSpan = (int) $jmAbsen + 2;
                echo "<table cellspacing='1' border='0' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td>No</td>\r\n\t<td>".$_SESSION['lang']['nama']."</td>\r\n\t<td>".$_SESSION['lang']['jabatan'].'</td>';
                $klmpkAbsn = [];
                foreach ($test as $ar => $isi) {
                    echo '<td width=5px align=center>'.substr($isi, 8, 2).'</td>';
                }
                while ($rKet = mysql_fetch_assoc($qAbsen)) {
                    $klmpkAbsn[] = $rKet;
                    echo '<td width=10px>'.$rKet['kodeabsen'].'</td>';
                }
                echo "\r\n\t<td>Jumlah</td></tr></thead>\r\n\t<tbody>";
                $resData[] = [];
                $hasilAbsn[] = [];
                $sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan from '.$dbname.'.datakaryawan a left join '.$dbname.'.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where   '.$where.' order by namakaryawan asc';
                $rGetkary = fetchData($sGetKary);
                foreach ($rGetkary as $row => $kar) {
                    $resData[$kar['karyawanid']][] = $kar['karyawanid'];
                    $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
                    $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
                }
                $sAbsn = 'select absensi,tanggal,karyawanid from '.$dbname.".sdm_absensidt where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg='".$kodeOrg."' and karyawanid=".$idKry.'';
                $rAbsn = fetchData($sAbsn);
                foreach ($rAbsn as $absnBrs => $resAbsn) {
                    $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                }
                $sKehadiran = 'select absensi,tanggal,karyawanid from '.$dbname.".kebun_kehadiran_vw \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and karyawanid=".$idKry." and unit='".substr($kdOrg, 0, 4)."'";
                $rkehadiran = fetchData($sKehadiran);
                foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                    $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                }
                $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."' and b.tanggal between '".$tgl1."' and '".$tgl2."' and a.nik=".$idKry.'';
                $rPrestasi = fetchData($sPrestasi);
                foreach ($rPrestasi as $presBrs => $resPres) {
                    $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
                }
                $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and substr(b.kodeorg,1,4) = '".substr($kodeOrg, 0, 4)."' and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and substr(b.kodeorg,1,4) = '".substr($kodeOrg, 0, 4)."' and c.namakaryawan is not NULL";
                $dzres = mysql_query($dzstr);
                while ($dzbar = mysql_fetch_object($dzres)) {
                    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                }
                $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and substr(b.kodeorg,1,4) = '".substr($kodeOrg, 0, 4)."' and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and substr(b.kodeorg,1,4) = '".substr($kodeOrg, 0, 4)."' and c.namakaryawan is not NULL";
                $dzres = mysql_query($dzstr);
                while ($dzbar = mysql_fetch_object($dzres)) {
                    $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
                }
                $dzstr = 'SELECT tanggal,idkaryawan FROM '.$dbname.".vhc_runhk\r\n    where tanggal between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".$kodeOrg."%'\r\n    ";
                $dzres = mysql_query($dzstr);
                while ($dzbar = mysql_fetch_object($dzres)) {
                    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
                    $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
                }
                $brt = [];
                $lmit = count($klmpkAbsn);
                $a = 0;
                foreach ($resData as $hslBrs => $hslAkhir) {
                    if ('' != $hslAkhir[0]) {
                        ++$no;
                        echo '<tr class=rowcontent><td>'.$no.'</td>';
                        echo "\r\n\t\t\t\t<td>".$namakar[$hslAkhir[0]]."</td>\r\n\t\t\t\t<td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n\t\t\t\t";
                        foreach ($test as $barisTgl => $isiTgl) {
                            if ('H' != $hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']) {
                                echo '<td><font color=red>'.$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'].'</font></td>';
                            } else {
                                echo '<td>'.$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'].'</td>';
                            }

                            ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                        }
                        foreach ($klmpkAbsn as $brsKet => $hslKet) {
                            if ('H' != $hslKet['kodeabsen']) {
                                echo '<td width=5px align=right><font color=red>'.$brt[$hslAkhir[0]][$hslKet['kodeabsen']].'</font></td>';
                            } else {
                                echo '<td width=5px align=right>'.$brt[$hslAkhir[0]][$hslKet['kodeabsen']].'</td>';
                            }

                            $subtot[$hslAkhir[0]]['total'] += $brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                        }
                        echo '<td width=5px>'.$subtot[$hslAkhir[0]]['total'].'</td>';
                        $subtot['total'] = 0;
                        echo '</tr>';
                    }
                }
                echo '</tbody></table>';

                break;
            }

            echo 'warning:Nama Karyawan Tidak Boleh Kosong!!!';
            exit();
        }

            echo 'warning:Unit Tidak Boleh Kosong';
            exit();

    case 'pdf':
        $kdOrg = $_GET['kdeOrg'];
        $period = explode('-', $_GET['period']);
        $periode = explode('-', $_GET['periode']);
        $idKry = $_GET['idKry'];
        $a = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTgs."' and periode='".$_GET['period']."'";
        $b = mysql_query($a);
        $c = mysql_fetch_assoc($b);
        $tgl_1 = $c['tanggalmulai'];
        $tgl_2 = $c['tanggalsampai'];
        if ('' != $tgl_1 && '' != $tgl_2) {
            $tgl1 = $tgl_1;
            $tgl2 = $tgl_2;
        }

        $test = dates_inbetween($tgl1, $tgl2);
        if ('' == $tgl2 && '' == $tgl1) {
            echo 'warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong';
            exit();
        }

        $jmlHari = count($test);
        if (31 < $jmlHari) {
            echo 'warning:Range tanggal tidak valid';
            exit();
        }

        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        $jmAbsen = mysql_num_rows($qAbsen);
        $colSpan = (int) $jmAbsen + 2;

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
        $jmlHari = $jmlHari * 1.5;
        $cols = 247.5;
        $query = selectQuery($dbname, 'organisasi', 'alamat,telepon', "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
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
        $this->Cell($width - 100, $height, 'Tel: '.$orgData[0]['telepon'], 0, 1, 'L');
        $this->Line($this->lMargin, $this->tMargin + $height * 4, $this->lMargin + $width, $this->tMargin + $height * 4);
        $this->Ln();
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['rkpAbsen'], '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->Cell($width, $height, strtoupper('Rekapitulasi Absensi Karyawan'), '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2), '', 0, 'C');
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
        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        while ($rAbsen = mysql_fetch_assoc($qAbsen)) {
            $klmpkAbsn[] = $rAbsen;
            $this->Cell(2 / 100 * $width, $height, $rAbsen['kodeabsen'], 1, 0, 'C', 1);
        }
        $this->Cell(5 / 100 * $width, $height, $_SESSION['lang']['jumlah'], 1, 1, 'C', 1);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
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
            if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
                $where = '  lokasitugas in ('.$kodeOrg.')';
            } else {
                if (4 < strlen($kdOrg)) {
                    $where = "  lokasitugas='".substr($kdOrg, 0, 4)."'";
                } else {
                    $where = "  lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null)";
                }
            }

            if ('' != $idKry) {
                $where .= ' and karyawanid='.$idKry.'';
            }

            $resData[] = [];
            $hasilAbsn[] = [];
            $sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan from '.$dbname.'.datakaryawan a left join '.$dbname.'.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where   '.$where.' order by namakaryawan asc';
            $rGetkary = fetchData($sGetKary);
            foreach ($rGetkary as $row => $kar) {
                $resData[$kar['karyawanid']][] = $kar['karyawanid'];
                $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
                $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
            }
            $sAbsn = 'select absensi,tanggal,karyawanid from '.$dbname.".sdm_absensidt \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg='".$kodeOrg."' and  karyawanid=".$idKry.' ';
            $rAbsn = fetchData($sAbsn);
            foreach ($rAbsn as $absnBrs => $resAbsn) {
                $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
            }
            $sKehadiran = 'select absensi,tanggal,karyawanid from '.$dbname.".kebun_kehadiran_vw \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and  karyawanid=".$idKry." and unit='".substr($kdOrg, 0, 4)."'";
            $rkehadiran = fetchData($sKehadiran);
            foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
            }
            $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."' and b.tanggal between '".$tgl1."' and '".$tgl2."' and  a.nik=".$idKry.'';
            $rPrestasi = fetchData($sPrestasi);
            foreach ($rPrestasi as $presBrs => $resPres) {
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            }
            $brt = [];
            $lmit = count($klmpkAbsn);
            $a = 0;
            foreach ($resData as $hslBrs => $hslAkhir) {
                if ('' != $hslAkhir[0]) {
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
        }

            echo 'warning:Unit Tidak Boleh Kosong';
            exit();

    case 'excel':
        $kdOrg = $_GET['kdeOrg'];
        $period = explode('-', $_GET['period']);
        $periode = explode('-', $_GET['periode']);
        $idKry = $_GET['idKry'];
        $a = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$lksiTgs."' and periode='".$_GET['period']."'";
        $b = mysql_query($a);
        $c = mysql_fetch_assoc($b);
        $tgl_1 = $c['tanggalmulai'];
        $tgl_2 = $c['tanggalsampai'];
        if ('' != $kdOrg) {
            $kodeOrg = $kdOrg;
            if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
                $where = '  lokasitugas in ('.$kodeOrg.')';
            } else {
                if (4 < strlen($kdOrg)) {
                    $where = "  lokasitugas='".substr($kdOrg, 0, 4)."'";
                } else {
                    $where = "  lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null)";
                }
            }

            if ('' != $tgl_1 && '' != $tgl_2) {
                $tgl1 = $tgl_1;
                $tgl2 = $tgl_2;
            }

            $test = dates_inbetween($tgl1, $tgl2);
            if ('' == $tgl2 && '' == $tgl1) {
                echo 'warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong';
                exit();
            }

            $jmlHari = count($test);
            if (31 < $jmlHari) {
                echo 'warning:Range tanggal tidak valid';
                exit();
            }

            $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
            $qAbsen = mysql_query($sAbsen);
            $jmAbsen = mysql_num_rows($qAbsen);
            $colSpan = (int) $jmAbsen + 2;
            $colatas = $jmlHari + $colSpan + 3;
            $stream .= "<table border='0'><tr><td colspan='".$colatas."' align=center>".strtoupper('Rekapitulasi Absensi Karyawan')."</td></tr>\r\n\t<tr><td colspan='".$colatas."' align=center>".strtoupper($_SESSION['lang']['periode']).' :'.tanggalnormal($tgl1).' s.d. '.tanggalnormal($tgl2)."</td></tr><tr><td colspan='".$colatas."'>&nbsp;</td></tr></table>";
            $stream .= "<table cellspacing='1' border='1' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td bgcolor=#DEDEDE align=center>No</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan'].'</td>';
            $klmpkAbsn = [];
            foreach ($test as $ar => $isi) {
                $stream .= '<td bgcolor=#DEDEDE align=center width=5px align=center>'.substr($isi, 8, 2).'</td>';
            }
            while ($rKet = mysql_fetch_assoc($qAbsen)) {
                $klmpkAbsn[] = $rKet;
                $stream .= '<td bgcolor=#DEDEDE align=center width=10px>'.$rKet['kodeabsen'].'</td>';
            }
            $stream .= "\r\n\t<td bgcolor=#DEDEDE align=center>Jumlah</td></tr></thead>\r\n\t<tbody>";
            if ('' != $idKry) {
                $where .= ' and karyawanid='.$idKry.'';
            }

            $resData[] = [];
            $hasilAbsn[] = [];
            $sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan from '.$dbname.'.datakaryawan a left join '.$dbname.'.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where  '.$where.' order by namakaryawan asc';
            $rGetkary = fetchData($sGetKary);
            foreach ($rGetkary as $row => $kar) {
                $resData[$kar['karyawanid']][] = $kar['karyawanid'];
                $namakar[$kar['karyawanid']] = $kar['namakaryawan'];
                $nmJabatan[$kar['karyawanid']] = $kar['namajabatan'];
            }
            $sAbsn = 'select absensi,tanggal,karyawanid from '.$dbname.".sdm_absensidt \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."' and kodeorg='".$kodeOrg."' and karyawanid=".$idKry.'';
            $rAbsn = fetchData($sAbsn);
            foreach ($rAbsn as $absnBrs => $resAbsn) {
                $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
            }
            $sKehadiran = 'select absensi,tanggal,karyawanid from '.$dbname.".kebun_kehadiran_vw \r\n                            where tanggal between  '".$tgl1."' and '".$tgl2."'and karyawanid=".$idKry." and unit='".substr($kdOrg, 0, 4)."'";
            $rkehadiran = fetchData($sKehadiran);
            foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
                $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
            }
            $sPrestasi = 'select b.tanggal,a.jumlahhk,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."' and b.tanggal between '".$tgl1."' and '".$tgl2."' and a.nik=".$idKry.'';
            $rPrestasi = fetchData($sPrestasi);
            foreach ($rPrestasi as $presBrs => $resPres) {
                $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            }
            $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."' and '".$tgl2."' and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
            $dzres = mysql_query($dzstr);
            while ($dzbar = mysql_fetch_object($dzres)) {
                $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            }
            $brt = [];
            $lmit = count($klmpkAbsn);
            $a = 0;
            foreach ($resData as $hslBrs => $hslAkhir) {
                if ('' != $hslAkhir[0]) {
                    ++$no;
                    $stream .= '<tr><td>'.$no.'</td>';
                    $stream .= "\r\n\t\t\t\t<td>".$namakar[$hslAkhir[0]]."</td>\r\n\t\t\t\t<td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n\t\t\t\t";
                    foreach ($test as $barisTgl => $isiTgl) {
                        $stream .= '<td>'.$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi'].'</td>';
                        ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                    }
                    foreach ($klmpkAbsn as $brsKet => $hslKet) {
                        $stream .= '<td width=5px>'.$brt[$hslAkhir[0]][$hslKet['kodeabsen']].'</td>';
                        $subtot[$hslAkhir[0]]['total'] += $brt[$hslAkhir[0]][$hslKet['kodeabsen']];
                    }
                    $stream .= '<td width=5px>'.$subtot[$hslAkhir[0]]['total'].'</td>';
                    $subtot['total'] = 0;
                    $stream .= '</tr>';
                }
            }
            $stream .= '</tbody></table>';
            $stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
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

            $nop_ = 'RekapAbsen'.$art.'__'.$kodeOrg;
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
                    echo "<script language=javascript1.2>\r\n\t\t\tparent.window.alert('Can't convert to excel format');\r\n\t\t\t</script>";
                    exit();
                }

                echo "<script language=javascript1.2>\r\n\t\t\twindow.location='tempExcel/".$nop_.".xls';\r\n\t\t\t</script>";
                closedir($handle);
            }

            break;
        }

            echo 'warning:Unit Tidak Boleh Kosong';
            exit();

    case 'getTgl':
        if ('' != $periode) {
            $tgl = $periode;
            $tanggal = $tgl[0].'-'.$tgl[1];
        } else {
            if ('' != $period) {
                $tgl = $period;
                $tanggal = $tgl[0].'-'.$tgl[1];
            }
        }

        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$tanggal."' ";
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        echo tanggalnormal($rTgl['tanggalmulai']).'###'.tanggalnormal($rTgl['tanggalsampai']);

        break;
    case 'getKry':
        if (4 < strlen($kdeOrg)) {
            $where = " lokasitugas='".substr($kdeOrg, 0, 4)."'";
        } else {
            $where = " lokasitugas='".$kdeOrg."'";
        }

        $sKry = 'select karyawanid,namakaryawan from '.$dbname.'.datakaryawan where '.$where.' order by namakaryawan asc';
        $qKry = mysql_query($sKry);
        while ($rKry = mysql_fetch_assoc($qKry)) {
            $optKry .= '<option value='.$rKry['karyawanid'].'>'.$rKry['namakaryawan'].'</option>';
        }
        echo $optKry;

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