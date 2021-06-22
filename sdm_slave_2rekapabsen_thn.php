<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
$lksiTgs = $_SESSION['empl']['lokasitugas'];
('' == $_POST['kdeOrg2'] ? ($kdOrg = $_GET['kdeOrg2']) : ($kdOrg = $_POST['kdeOrg2']));
('' == $_POST['periodThn'] ? ($tgl1 = $_GET['periodThn']) : ($tgl1 = $_POST['periodThn']));
('' == $_POST['periodThnSmp'] ? ($tgl2 = $_GET['periodThnSmp']) : ($tgl2 = $_POST['periodThnSmp']));
('' == $_POST['tipeKary2'] ? ($tipeKary = $_GET['tipeKary2']) : ($tipeKary = $_POST['tipeKary2']));
('' == $_POST['sistemGaji3'] ? ($sistemGaji = $_GET['sistemGaji3']) : ($sistemGaji = $_POST['sistemGaji3']));
$optTmk = makeOption($dbname, 'datakaryawan', 'karyawanid,tanggalmasuk');
$optDept = makeOption($dbname, 'sdm_5departemen', 'kode,nama');
$thn = explode('-', $tgl1);
$thn2 = explode('-', $tgl2);
if ($thn[0] != $thn2[0]) {
    exit('Error:Tahun tidak boleh beda');
}

$tanggal1 = $tgl1.'-01';
$tanggal2 = $tgl2.'-01';
$totBln = datediff($tanggal1, $tanggal2);
for ($ard = 1; $ard <= $totBln[months_total]; ++$ard) {
    if (strlen($ard) < 2) {
        $bar = '0'.$ard;
    }

    $test[] = $thn1[0].'-'.$bar;
}
if ('' != $kdOrg) {
    $kodeOrg = $kdOrg;
    if ('HOLDING' == $_SESSION['empl']['tipelokasitugas']) {
        $where = "  lokasitugas in ('".$kodeOrg."')";
    } else {
        if (4 < strlen($kdOrg)) {
            $where = "  subbagian='".$kdOrg."'";
        } else {
            $where = "  lokasitugas='".$kdOrg."' and (subbagian='0' or subbagian is null or subbagian='')";
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

$sGetKary = 'select a.karyawanid,b.namajabatan,a.namakaryawan,c.nama,d.tipe from '.$dbname.".datakaryawan a \r\n           left join ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n           left join ".$dbname.".sdm_5departemen c on a.bagian=c.kode\r\n           left join ".$dbname.".sdm_5tipekaryawan d on a.tipekaryawan=d.id\r\n           where ".$where.' '.$wherez.' order by namakaryawan asc';
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
if (4 < strlen($kodeOrg)) {
    $dimanaPnjng = " kodeorg='".$kodeOrg."'";
} else {
    $dimanaPnjng = " substring(kodeorg,1,4)='".substr($kodeOrg, 0, 4)."'";
}

$sAbsn = 'select count(absensi) as total,absensi,karyawanid,substr(tanggal,1,4) as periode from '.$dbname.".sdm_absensidt \r\n                                where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' and ".$dimanaPnjng.' group by absensi,karyawanid';
$rAbsn = fetchData($sAbsn);
foreach ($rAbsn as $absnBrs => $resAbsn) {
    if (null != $resAbsn['absensi']) {
        $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['periode']][$resAbsn['absensi']] = $resAbsn['total'];
        $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
    }
}
$sKehadiran = 'select count(absensi) as total,absensi,karyawanid,substr(tanggal,1,4) as periode from '.$dbname.".kebun_kehadiran_vw \r\n                            where substr(tanggal,1,7) between  '".$tgl1."' and '".$tgl2."' and substring(unit,1,4)='".substr($kodeOrg, 0, 4)."' group by absensi,karyawanid";
$rkehadiran = fetchData($sKehadiran);
foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
    if ('' != $resKhdrn['absensi']) {
        $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['periode']][$resKhdrn['absensi']] += $resKhdrn['total'];
        $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
    }
}
$sPrestasi = 'select substr(b.tanggal,1,4) as periode,a.jumlahhk,a.nik from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi \r\n                            where b.notransaksi like '%PNN%' and substr(b.kodeorg,1,4)='".substr($kodeOrg, 0, 4)."' and substr(b.tanggal,1,7) between '".$tgl1."' and '".$tgl2."'";
$rPrestasi = fetchData($sPrestasi);
foreach ($rPrestasi as $presBrs => $resPres) {
    ++$hasilAbsn[$resPres['nik']][$resPres['periode']]['H'];
    $resData[$resPres['nik']][] = $resPres['nik'];
}
$dzstr = 'SELECT substr(a.tanggal,1,4) as periode,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select substr(a.tanggal,1,4) as periode,nikmandor1 FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
$dzres = mysql_query($dzstr);
while ($dzbar = mysql_fetch_object($dzres)) {
    ++$hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H'];
    $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
}
$dzstr = 'SELECT substr(a.tanggal,1,4) as periode,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL\r\n    union select substr(a.tanggal,1,4) as periode,keranimuat FROM ".$dbname.".kebun_aktifitas a \r\n    left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n    left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n    where a.tanggal between '".$tgl1."-01' and LAST_DAY('".$tgl2."-15') and b.kodeorg like '".$kodeOrg."%' and c.namakaryawan is not NULL";
$dzres = mysql_query($dzstr);
while ($dzbar = mysql_fetch_object($dzres)) {
    ++$hasilAbsn[$dzbar->nikmandor][$dzbar->periode]['H'];
    $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
}
$dzstr = 'SELECT tanggal,idkaryawan FROM '.$dbname.".vhc_runhk\r\n    where substr(tanggal,1,7) between '".$tgl1."' and '".$tgl2."' and notransaksi like '%".$kodeOrg."%'\r\n    ";
$dzres = mysql_query($dzstr);
while ($dzbar = mysql_fetch_object($dzres)) {
    $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
    $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
}
switch ($proses) {
    case 'preview':
        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        $jmAbsen = mysql_num_rows($qAbsen);
        $colSpan = (int) $jmAbsen + 2;
        echo "<table cellspacing='1' border='0' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td>No</td>\r\n\t<td>".$_SESSION['lang']['nama']."</td>\r\n        <td>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td>".$_SESSION['lang']['bagian']."</td>\r\n\t<td>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['tmk'].'</td>';
        $klmpkAbsn = [];
        while ($rKet = mysql_fetch_assoc($qAbsen)) {
            $klmpkAbsn[] = $rKet;
            echo '<td width=10px align=center>'.$rKet['kodeabsen'].'</td>';
        }
        echo "\r\n\t<td>".$_SESSION['lang']['total']."</td></tr></thead>\r\n\t<tbody>";
        $brt = [];
        $lmit = count($klmpkAbsn);
        $a = 0;
        foreach ($resData as $hslBrs => $hslAkhir) {
            if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                ++$no;
                echo '<tr class=rowcontent><td>'.$no.'</td>';
                echo "\r\n\t\t\t\t<td>".$namakar[$hslAkhir[0]]."</td>\r\n                                <td>".$nmTipe[$hslAkhir[0]]."</td>\r\n                                <td>".$nmBagian[$hslAkhir[0]]."</td>\r\n\t\t\t\t<td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n                                <td>".$optTmk[$hslAkhir[0]]."</td>\r\n\t\t\t\t";
                foreach ($klmpkAbsn as $brsKet => $hslKet) {
                    echo '<td align=right>'.$hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']].'</td>';
                    $subtot[$hslAkhir[0]]['total'] += $hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']];
                }
                echo '<td width=5px align=right>'.$subtot[$hslAkhir[0]]['total'].'</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';

        break;
    case 'pdf':
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
        global $tipeKary;
        global $sistemGaji;
        global $dimanaPnjng;
        global $hasilAbsn;
        global $thn;
        global $nmBagian;
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
        $this->Cell(20 / 100 * $width - 5, $height, $_SESSION['lang']['rkpAbsen'].' '.$sistemGaji, '', 0, 'L');
        $this->Ln();
        $this->Ln();
        $this->Cell($width, $height, strtoupper('Rekapitulasi Absensi Karyawan'), '', 0, 'C');
        $this->Ln();
        $this->Cell($width, $height, strtoupper($_SESSION['lang']['periode']).' : '.substr(tanggalnormal($tgl1), 1, 7).' s.d. '.substr(tanggalnormal($tgl2), 1, 7), '', 0, 'C');
        $this->Ln();
        $this->Ln();
        $this->SetFont('Arial', 'B', 7);
        $this->SetFillColor(220, 220, 220);
        $this->Cell(3 / 100 * $width, $height, 'No', 1, 0, 'C', 1);
        $this->Cell(15 / 100 * $width, $height, $_SESSION['lang']['nama'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['bagian'], 1, 0, 'C', 1);
        $this->Cell(10 / 100 * $width, $height, $_SESSION['lang']['jabatan'], 1, 0, 'C', 1);
        $this->Cell(8 / 100 * $width, $height, $_SESSION['lang']['tmk'], 1, 0, 'C', 1);
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
        $brt = [];
        $lmit = count($klmpkAbsn);
        $a = 0;
        foreach ($resData as $hslBrs => $hslAkhir) {
            if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                ++$no;
                $pdf->Cell(3 / 100 * $width, $height, $no, 1, 0, 'C', 1);
                $pdf->Cell(15 / 100 * $width, $height, strtoupper($namakar[$hslAkhir[0]]), 1, 0, 'L', 1);
                $pdf->Cell(10 / 100 * $width, $height, $nmBagian[$hslAkhir[0]], 1, 0, 'L', 1);
                $pdf->Cell(10 / 100 * $width, $height, strtoupper($nmJabatan[$hslAkhir[0]]), 1, 0, 'L', 1);
                $pdf->Cell(8 / 100 * $width, $height, $optTmk[$hslAkhir[0]], 1, 0, 'L', 1);
                foreach ($klmpkAbsn as $brsKet => $hslKet) {
                    $pdf->Cell(2 / 100 * $width, $height, $hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']], 1, 0, 'C', 1);
                    $subtot[$hslAkhir[0]]['total'] += $hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']];
                }
                $pdf->Cell(5 / 100 * $width, $height, $subtot[$hslAkhir[0]]['total'], 1, 1, 'R', 1);
                $subtot[$hslAkhir[0]]['total'] = 0;
            }
        }
        $pdf->Output();

        break;
    case 'excel':
        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        $jmAbsen = mysql_num_rows($qAbsen);
        $colSpan = (int) $jmAbsen + 2;
        $colatas = $jmlHari + $colSpan + 3;
        $stream .= "<table border='0'><tr><td colspan='".$colatas."' align=center>".strtoupper('Rekapitulasi Absensi Karyawan').' '.$sistemGaji."</td></tr>\r\n\t<tr><td colspan='".$colatas."' align=center>".strtoupper($_SESSION['lang']['periode']).' :'.substr(tanggalnormal($tgl1), 1, 7).' s.d. '.substr(tanggalnormal($tgl2), 1, 7)."</td></tr><tr><td colspan='".$colatas."'>&nbsp;</td></tr></table>";
        $stream .= "<table cellspacing='1' border='1' class='sortable'>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n\t<td bgcolor=#DEDEDE align=center>No</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tipekaryawan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bagian']."</td>\r\n\t<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jabatan']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tmk'].'</td>';
        $klmpkAbsn = [];
        while ($rKet = mysql_fetch_assoc($qAbsen)) {
            $klmpkAbsn[] = $rKet;
            $stream .= '<td bgcolor=#DEDEDE align=center width=10px>'.$rKet['kodeabsen'].'</td>';
        }
        $stream .= "\r\n\t<td bgcolor=#DEDEDE align=center>Jumlah</td></tr></thead>\r\n\t<tbody>";
        $brt = [];
        $lmit = count($klmpkAbsn);
        $a = 0;
        foreach ($resData as $hslBrs => $hslAkhir) {
            if ('' != $hslAkhir[0] && '' != $namakar[$hslAkhir[0]]) {
                ++$no;
                $stream .= '<tr class=rowcontent><td>'.$no.'</td>';
                $stream .= "\r\n\t\t\t\t<td>".$namakar[$hslAkhir[0]]."</td>\r\n                                <td>".$nmTipe[$hslAkhir[0]]."</td>\r\n                                <td>".$nmBagian[$hslAkhir[0]]."</td>\r\n\t\t\t\t<td>".$nmJabatan[$hslAkhir[0]]."</td>\r\n\t\t\t\t<td>".$optTmk[$hslAkhir[0]].'</td>';
                foreach ($klmpkAbsn as $brsKet => $hslKet) {
                    $stream .= '<td align=right>'.$hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']].'</td>';
                    $subtot[$hslAkhir[0]]['total'] += $hasilAbsn[$hslAkhir[0]][$thn[0]][$hslKet['kodeabsen']];
                }
                $stream .= '<td width=5px align=right>'.$subtot[$hslAkhir[0]]['total'].'</td>';
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
        echo $optPeriode;

        break;
    default:
        break;
}
function datediff($tgl1, $tgl2)
{
    $tgl1 = strtotime($tgl1);
    $tgl2 = strtotime($tgl2);
    $diff_secs = abs($tgl1 - $tgl2);
    $base_year = min(date('Y', $tgl1), date('Y', $tgl2));
    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);

    return ['years' => date('Y', $diff) - $base_year, 'months_total' => ((date('Y', $diff) - $base_year) * 12 + date('n', $diff)) - 1, 'months' => date('n', $diff) - 1, 'days_total' => floor($diff_secs / (3600 * 24)), 'days' => date('j', $diff) - 1, 'hours_total' => floor($diff_secs / 3600), 'hours' => date('G', $diff), 'minutes_total' => floor($diff_secs / 60), 'minutes' => (int) date('i', $diff), 'seconds_total' => $diff_secs, 'seconds' => (int) date('s', $diff)];
}

?>