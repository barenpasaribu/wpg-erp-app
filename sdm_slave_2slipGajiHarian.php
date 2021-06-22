<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$proses = $_GET['proses'];
('' == $_POST['periode'] ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
('' == $_POST['period'] ? ($period = $_GET['period']) : ($period = $_POST['period']));
('' == $_POST['idKry'] ? ($idKry = $_GET['idKry']) : ($idKry = $_POST['idKry']));
('' == $_POST['kdBag'] ? ($kdBag = $_GET['kdBag']) : ($kdBag = $_POST['kdBag']));
('' == $_POST['tPkary'] ? ($tPkary = $_GET['tPkary']) : ($tPkary = $_POST['tPkary']));
$rNmTipe = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$dtTipe = '';
$arrBln = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];
if ('' != $periode && '' != $kdBag) {
    $where = "a.sistemgaji='HARIAN' and a.periodegaji='".$periode."' and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'\r\n            and b.bagian='".$kdBag."'";
} else {
    if ('' != $periode) {
        $where = "a.sistemgaji='Harian' and a.periodegaji='".$periode."'\r\n            and a.kodeorg='".$_SESSION['empl']['lokasitugas']."'";
    } else {
        if ('' != $period) {
            $periode = $period;
        }

        $where = "a.sistemgaji='Harian' and a.periodegaji='".$periode."' and a.karyawanid='".$idKry."'";
    }
}

if ('' != $tPkary) {
    $dtTipe = " and b.tipekaryawan='".$tPkary."'";
}

switch ($proses) {
    case 'preview':
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

        $sOrg = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
        $qOrg = mysql_query($sOrg);
        $rOrg = mysql_fetch_assoc($qOrg);
        $bln = explode('-', $periode);
        $idBln = (int) ($bln[1]);
        $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from\r\n               ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan\r\n               left join ".$dbname.'.sdm_5departemen d on b.bagian=d.kode where '.$where.' '.$dtTipe.'';
        $qSlip = mysql_query($sSlip);
        $rCek = mysql_num_rows($qSlip);
        if (0 < $rCek) {
            while ($rSlip = mysql_fetch_assoc($qSlip)) {
                if ('' != $rSlip['karyawanid']) {
                    $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                }
            }
            $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='1' and id not in ('14','13') ";
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompPls[] = $rKomp['id'];
                $arrNmKomPls[$rKomp['id']] = $rKomp['name'];
            }
            $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='0'  ";
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompMin[] = $rKomp['id'];
                $arrNmKomMin[$rKomp['id']] = $rKomp['name'];
            }
            foreach ($arrKary as $dtKary) {
                echo "<table cellspacing=1 border=0 width=500 style=\"background-color:white\">\r\n                    <tr><td> <h2><img src=".$path.' width=60 height=30>&nbsp;'.$_SESSION['org']['namaorganisasi']."</h2></td></tr>\r\n                    <tr style='border-bottom:#fff solid 2px; border-top:#fff solid 2px;'><td valign=top>\r\n                    <table border=0 width=100%>\r\n                    <tr><td width=49% valign=top><table border=0>\r\n                    <tr><td colspan=3>PAY SLIP/SLIP GAJI: ".$arrBln[$idBln].'-'.$bln[0]."</td></tr>\r\n                    <tr><td>NIP/TMK</td><td>:</td><td>".$arrNik[$dtKary].'/'.tanggalnormal($arrTglMsk[$dtKary])."</td></tr>\r\n                    <tr><td>NAMA</td><td>:</td><td>".$arrNmKary[$dtKary]."</td></tr>\r\n                    </table></td><td width=51% valign=top>\r\n                    <table border=0>\r\n                    <tr><td colspan=3>&nbsp;</td></tr>\r\n                    <tr><td>UNIT/BAGIAN</td><td>:</td><td>".$rOrg['namaorganisasi'].'/'.$arrBag[$dtKary]."</td></tr>\r\n                    <tr><td>JABATAN</td><td>:</td><td>".$arrJbtn[$dtKary]."</td></tr>\r\n                    </table></td></tr>\r\n                    </table>\r\n                    </td></tr>\r\n                    <tr>\r\n                    <td>\r\n                    <table width=100%>\r\n                    <thead>\r\n                    <tr><td align=center>PENAMBAH</td><td align=center>PENGURANG</td>\r\n                    </tr>\r\n                    </thead>\r\n                    <tbody>\r\n                    <tr>\r\n                    <td valign=top>\r\n                    <table width=100%>";
                $arrPlus = [];
                $s = 0;
                foreach ($arrIdKompPls as $idKompPls) {
                    echo '<tr><td>'.$arrNmKomPls[$idKompPls].'</td><td>:Rp.</td><td align=right> '.number_format($arrJmlh[$dtKary.$idKompPls], 2).'</td></tr>';
                    $arrPlus[$s] = $arrJmlh[$dtKary.$idKompPls];
                    ++$s;
                }
                echo "</table>\r\n\r\n                    </td>\r\n                    <td valign=top>\r\n                    <table width=100%>";
                $arrMin = [];
                $q = 0;
                foreach ($arrIdKompMin as $idKompMin) {
                    echo '<tr><td>'.$arrNmKomMin[$idKompMin].'</td><td>:Rp.</td><td align=right> '.number_format($arrJmlh[$dtKary.$idKompMin], 2).'</td></tr>';
                    $arrMin[$q] = $arrJmlh[$dtKary.$idKompMin];
                    ++$q;
                }
                $gajiBersih = array_sum($arrPlus) - array_sum($arrMin);
                echo "</table>\r\n                    </td></tr>\r\n                    <tr><td colspan=2><table width=100%>\r\n                    <tr><td>Total Penambahan</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrPlus), 2).'</td><td>Total Pengurangan</td><td>:Rp.</td><td align=right> '.number_format(array_sum($arrMin), 2)."</td></tr>\r\n                    <tr><td>Gaji Bersih</td><td>:Rp.</td><td align=right> ".number_format(array_sum($arrPlus) - array_sum($arrMin), 2)."</td><td>&nbsp;</td><td>&nbsp;</td><td align=right> &nbsp;</td></tr>\r\n                    <tr><td>Terbilang</td><td>:</td><td colspan=4> ".terbilang($gajiBersih, 2)." rupiah</td></tr></table></td></tr></tbody>\r\n                    </table></td>\r\n                    </tr>\r\n\r\n\r\n                    <tr>\r\n                    <td>&nbsp;</td>\r\n                    </tr>\r\n                    </table>\r\n                    ";
            }
        } else {
            echo 'Not Found';
        }

        break;
    case 'pdf':

class PDF extends FPDF
{
    public $col = 0;
    public $dbname;

    public function SetCol($col)
    {
        $this->col = $col;
        $x = 10 + $col * 100;
        $this->SetLeftMargin($x);
        $this->SetX($x);
    }

    public function AcceptPageBreak()
    {
        if ($this->col < 1) {
            $this->SetCol($this->col + 1);
            $this->SetY(10);

            return false;
        }

        $this->SetCol(0);

        return true;
    }

    public function Header()
    {
        $this->lMargin = 5;
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 5);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

        $pdf = new PDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 5);
        $bln = explode('-', $periode);
        $idBln = (int) ($bln[1]);
        $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama from\r\n               ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan\r\n               left join ".$dbname.'.sdm_5departemen d on b.bagian=d.kode where '.$where.'  '.$dtTipe.'';
        $qSlip = mysql_query($sSlip);
        $rCek = mysql_num_rows($qSlip);
        if (0 < $rCek) {
            while ($rSlip = mysql_fetch_assoc($qSlip)) {
                if ('' != $rSlip['karyawanid']) {
                    $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                    $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                }
            }
            $sKomp = 'select id,name,plus from '.$dbname.'.sdm_ho_component where plus=1 ';
            $qKomp = mysql_query($sKomp);
            while ($rKomp = mysql_fetch_assoc($qKomp)) {
                $arrIdKompPls[] = $rKomp['id'];
                $arrNmKomPls[$rKomp['id']][1] = $rKomp['name'];
            }
            $sKomp2 = 'select id,name,plus from '.$dbname.'.sdm_ho_component where plus=0 ';
            $qKomp2 = mysql_query($sKomp2);
            while ($rKomp2 = mysql_fetch_assoc($qKomp2)) {
                $arrIdKompPls[] = $rKomp2['id'];
                $arrNmKomPls[$rKomp2['id']][0] = $rKomp2['name'];
            }
            $arrMinusId = [];
            $arrMinusName = [];
            $str = 'select distinct id,name from '.$dbname.".sdm_ho_component  a\r\n                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen\r\n                   where plus=0 and jumlah!=0 and b.periodegaji='".$periode."'\r\n                   and b.kodeorg='".$_SESSION['empl']['lokasitugas']."' order by id";
            $res = mysql_query($str, $conn);
            while ($bar = mysql_fetch_object($res)) {
                array_push($arrMinusId, $bar->id);
                array_push($arrMinusName, $bar->name);
            }
            $arrPlusId = $arrMinusId;
            $arrPlusName = $arrMinusName;
            for ($r = 0; $r < count($arrMinusId); ++$r) {
                $arrPlusId[$r] = '';
                $arrPlusName[$r] = '';
            }
            $str = 'select distinct id,name from '.$dbname.".sdm_ho_component  a\r\n                   left join ".$dbname.".sdm_gaji_vw b on a.id=b.idkomponen\r\n                   where plus=1 and jumlah!=0 and b.periodegaji='".$periode."'\r\n                   and b.kodeorg='".$_SESSION['empl']['lokasitugas']."' and id not in ('14','13') order by id";
            $res = mysql_query($str, $conn);
            $n = -1;
            while ($bar = mysql_fetch_object($res)) {
                ++$n;
                $arrPlusId[$n] = $bar->id;
                $arrPlusName[$n] = $bar->name;
            }
            $arrValPlus = [];
            $arrValMinus = [];
            for ($x = 0; $x < count($arrPlusId); ++$x) {
                $arrValPlus[$x] = 0;
                $arrValMinus[$x] = 0;
            }
            $str3 = 'select jumlah,idkomponen,a.karyawanid,c.plus from '.$dbname.".sdm_gaji_vw a\r\n                  left join ".$dbname.".sdm_ho_component c on a.idkomponen=c.id\r\n                 where a.sistemgaji='Harian' and a.periodegaji='".$periode."' group by a.karyawanid,idkomponen";
            $res3 = mysql_query($str3, $conn);
            while ($bar3 = mysql_fetch_assoc($res3)) {
                if ('1' == $bar3['plus']) {
                    if ('' != $bar3['jumlah']) {
                        $arrValPlus[$bar3['karyawanid']][$bar3['idkomponen']] = $bar3['jumlah'];
                    }
                } else {
                    if ('0' == $bar3['plus'] && '' != $bar3['jumlah']) {
                        $arrValMinus[$bar3['karyawanid']][$bar3['idkomponen']] = $bar3['jumlah'];
                    }
                }
            }
            $st = 0;
            foreach ($arrKary as $dtKary) {
                ++$st;
                if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
                    $pdf->Image('images/SSP_logo.jpg', $pdf->GetX(), $pdf->GetY() - 7, 15);
                } else {
                    if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                        $pdf->Image('images/MI_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                    } else {
                        if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                            $pdf->Image('images/HS_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                        } else {
                            $pdf->Image('images/BM_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                        }
                    }
                }

                $pdf->SetX($pdf->getX() + 15);
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(75, 6, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(71, 4, 'PAY SLYP/SLIP GAJI : '.$arrBln[$idBln].'-'.$bln[0], 'T', 0, 'L');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(25, 4, 'Printed on: '.date('d-m-Y: H:i:s'), 'T', 1, 'R');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Cell(15, 4, $_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk'], 0, 0, 'L');
                $pdf->Cell(35, 4, ': '.$arrNik[$dtKary].'/'.tanggalnormal($arrTglMsk[$dtKary]), 0, 0, 'L');
                $pdf->Cell(18, 4, $_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian'], 0, 0, 'L');
                $pdf->Cell(28, 4, ': '.$_SESSION['empl']['lokasitugas'].' / '.$arrBag[$dtKary], 0, 1, 'L');
                $pdf->Cell(15, 4, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
                $pdf->Cell(35, 4, ': '.$arrNmKary[$dtKary], 0, 0, 'L');
                $pdf->Cell(18, 3, $_SESSION['lang']['jabatan'], 0, 0, 'L');
                $pdf->Cell(28, 4, ':'.$arrJbtn[$dtKary], 0, 1, 'L');
                $pdf->Cell(48, 4, $_SESSION['lang']['penambah'], 'TB', 0, 'C');
                $pdf->Cell(48, 4, $_SESSION['lang']['pengurang'], 'TB', 1, 'C');
                for ($mn = 0; $mn < count($arrPlusId); ++$mn) {
                    $pdf->Cell(25, 4, $arrPlusName[$mn], 0, 0, 'L');
                    if ('' == $arrPlusName[$mn]) {
                        $pdf->Cell(5, 4, '', 0, 0, 'L');
                        $pdf->Cell(18, 4, '', 'R', 0, 'R');
                    } else {
                        if ('' == $arrPlusId[$mn]) {
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 'R', 0, 'R');
                        } else {
                            $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($arrValPlus[$dtKary][$arrPlusId[$mn]], 2, '.', ','), 'R', 0, 'R');
                            $arrPlus[$dtKary] += $arrValPlus[$dtKary][$arrPlusId[$mn]];
                        }
                    }

                    $pdf->Cell(25, 4, $arrMinusName[$mn], 0, 0, 'L');
                    if ('' == $arrMinusName[$mn]) {
                        $pdf->Cell(5, 4, '', 0, 0, 'L');
                        $pdf->Cell(18, 4, '', 0, 1, 'R');
                    } else {
                        if ('' == $arrMinusId[$mn]) {
                            $pdf->Cell(5, 4, '', 0, 0, 'L');
                            $pdf->Cell(18, 4, '', 0, 1, 'R');
                        } else {
                            $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                            $pdf->Cell(18, 4, number_format($arrValMinus[$dtKary][$arrMinusId[$mn]] * -1, 2, '.', ','), 0, 1, 'R');
                            $arrMin[$dtKary] += $arrValMinus[$dtKary][$arrMinusId[$mn]] * -1;
                        }
                    }
                }
                $pdf->Cell(25, 4, 'Total.Pendapatan', 'TB', 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 'TB', 0, 'L');
                $pdf->Cell(18, 4, number_format($arrPlus[$dtKary], 2, '.', ','), 'TB', 0, 'R');
                $pdf->Cell(25, 4, 'Total.Pengurangan', 'TB', 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 'TB', 0, 'L');
                $pdf->Cell(18, 4, number_format($arrMin[$dtKary] * -1, 2, '.', ','), 'TB', 1, 'R');
                $pdf->SetFont('Arial', 'B', 6);
                $pdf->Cell(23, 4, 'Gaji.Bersih', 0, 0, 'L');
                $pdf->Cell(5, 4, ':Rp.', 0, 0, 'L');
                $pdf->Cell(18, 4, number_format($arrPlus[$dtKary] - $arrMin[$dtKary] * -1, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(47, 4, '', 0, 1, 'L');
                $terbilang = $arrPlus[$dtKary] - $arrMin[$dtKary] * -1;
                $blng = terbilang($terbilang, 2).' rupiah';
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(23, 4, 'Terbilang', 0, 0, 'L');
                $pdf->Cell(5, 4, ':', 0, 0, 'L');
                $pdf->MultiCell(58, 4, $blng, 0, 'L');
                $pdf->SetFont('Arial', 'I', 5);
                $pdf->Cell(96, 4, 'Note: This is computer generated system, signature is not required', 'T', 1, 'L');
                $pdf->SetFont('Arial', '', 6);
                $pdf->Ln();
                $pdf->Ln();
                $pdf->Ln();
                if (140 < $pdf->GetY() && $pdf->col < 1) {
                    $pdf->AcceptPageBreak();
                }

                if (140 < $pdf->GetY() && 0 < $pdf->col) {
                    $r = 275 - $pdf->GetY();
                    $pdf->Cell(80, $r, '', 0, 1, 'L');
                }

                $pdf->cell(-1, 3, '', 0, 0, 'L');
            }
        } else {
            if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
                $pdf->Image('images/SSP_logo.jpg', $pdf->GetX(), $pdf->GetY() - 7, 15);
            } else {
                if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                    $pdf->Image('images/MI_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                } else {
                    if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                        $pdf->Image('images/HS_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                    } else {
                        $pdf->Image('images/BM_logo.jpg', $pdf->GetX(), $pdf->GetY(), 10);
                    }
                }
            }

            $pdf->SetX($pdf->getX() + 13);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(70, 5, $_SESSION['org']['namaorganisasi'], 0, 1, 'L');
            $pdf->SetFont('Arial', '', 5);
            $pdf->Cell(60, 3, 'NO DATA FOUND', 'T', 0, 'L');
        }

        $pdf->Output();

        break;
    case 'excel':
        $sAbsen = 'select kodeabsen from '.$dbname.'.sdm_5absensi order by kodeabsen';
        $qAbsen = mysql_query($sAbsen);
        while ($rKet = mysql_fetch_assoc($qAbsen)) {
            $klmpkAbsn[] = $rKet;
        }
        $sTgl = 'select distinct tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji\r\n                   where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$periode."'\r\n                   and jenisgaji='H'";
        $qTgl = mysql_query($sTgl);
        $rTgl = mysql_fetch_assoc($qTgl);
        $test = dates_inbetween($rTgl['tanggalmulai'], $rTgl['tanggalsampai']);
        $sAbsn = 'select absensi,tanggal,karyawanid from '.$dbname.".sdm_absensidt\r\n                            where tanggal like '".$periode."%' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
        $rAbsn = fetchData($sAbsn);
        foreach ($rAbsn as $absnBrs => $resAbsn) {
            if (null != $resAbsn['absensi']) {
                $hasilAbsn[$resAbsn['karyawanid']][$resAbsn['tanggal']][] = ['absensi' => $resAbsn['absensi']];
                $resData[$resAbsn['karyawanid']][] = $resAbsn['karyawanid'];
            }
        }
        $sKehadiran = 'select absensi,tanggal,karyawanid from '.$dbname.".kebun_kehadiran_vw\r\n                                     where tanggal like '".$periode."%' and kodeorg like '".$_SESSION['empl']['lokasitugas']."%'";
        $rkehadiran = fetchData($sKehadiran);
        foreach ($rkehadiran as $khdrnBrs => $resKhdrn) {
            if ('' != $resKhdrn['absensi']) {
                $hasilAbsn[$resKhdrn['karyawanid']][$resKhdrn['tanggal']][] = ['absensi' => $resKhdrn['absensi']];
                $resData[$resKhdrn['karyawanid']][] = $resKhdrn['karyawanid'];
            }
        }
        $sPrestasi = 'select a.nik,b.tanggal from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi\r\n                                    where b.notransaksi like '%PNN%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and b.tanggal like '".$periode."%'";
        $rPrestasi = fetchData($sPrestasi);
        foreach ($rPrestasi as $presBrs => $resPres) {
            $hasilAbsn[$resPres['nik']][$resPres['tanggal']][] = ['absensi' => 'H'];
            $resData[$resPres['nik']][] = $resPres['nik'];
        }
        $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL\r\n            union select tanggal,nikmandor1 FROM ".$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikmandor1=c.karyawanid\r\n            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
        }
        $dzstr = 'SELECT tanggal,nikmandor FROM '.$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.nikmandor=c.karyawanid\r\n            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL\r\n            union select tanggal,keranimuat FROM ".$dbname.".kebun_aktifitas a\r\n            left join ".$dbname.".kebun_prestasi b on a.notransaksi=b.notransaksi\r\n            left join ".$dbname.".datakaryawan c on a.keranimuat=c.karyawanid\r\n            where a.tanggal like '".$periode."%' and b.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' and c.namakaryawan is not NULL";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $hasilAbsn[$dzbar->nikmandor][$dzbar->tanggal][] = ['absensi' => 'H'];
            $resData[$dzbar->nikmandor][] = $dzbar->nikmandor;
        }
        $dzstr = 'SELECT a.tanggal,idkaryawan FROM '.$dbname.".vhc_runhk a\r\n        left join ".$dbname.".datakaryawan b on a.idkaryawan=b.karyawanid\r\n        where a.tanggal like '".$periode."%' and notransaksi like '%".$_SESSION['empl']['lokasitugas']."%'";
        $dzres = mysql_query($dzstr);
        while ($dzbar = mysql_fetch_object($dzres)) {
            $hasilAbsn[$dzbar->idkaryawan][$dzbar->tanggal][] = ['absensi' => 'H'];
            $resData[$dzbar->idkaryawan][] = $dzbar->idkaryawan;
        }
        foreach ($resData as $hslBrs => $hslAkhir) {
            if ('' != $hslAkhir[0]) {
                foreach ($test as $barisTgl => $isiTgl) {
                    ++$brt[$hslAkhir[0]][$hasilAbsn[$hslAkhir[0]][$isiTgl][0]['absensi']];
                }
            }
        }
        $bln = explode('-', $perod);
        $idBln = (int) ($bln[1]);
        $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='1'  and id not in ('13','14') ";
        $qKomp = mysql_query($sKomp);
        while ($rKomp = mysql_fetch_assoc($qKomp)) {
            $arrIdKompPls[] = $rKomp['id'];
            $arrNmKomPls[$rKomp['id']] = $rKomp['name'];
        }
        $totPlus = count($arrIdKompPls);
        $brsPlus = 0;
        $sKomp = 'select id,name from '.$dbname.".sdm_ho_component where plus='0'  ";
        $qKomp = mysql_query($sKomp);
        while ($rKomp = mysql_fetch_assoc($qKomp)) {
            $arrIdKompMin[] = $rKomp['id'];
            $arrNmKomMin[$rKomp['id']] = $rKomp['name'];
        }
        $sPeriod = 'select tanggalmulai,tanggalsampai from '.$dbname.".sdm_5periodegaji where jenisgaji='H' and periode='".$periode."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
        $qPeriod = mysql_query($sPeriod);
        $rPeriod = mysql_fetch_assoc($qPeriod);
        $mulai = tanggalnormal($rPeriod['tanggalmulai']);
        $selesi = tanggalnormal($rPeriod['tanggalsampai']);
        $stream .= "\r\n                        <table>\r\n                        <tr><td colspan=15 align=center>List Data Gaji Harian, Unit : ".$_SESSION['empl']['lokasitugas']."</td></tr>\r\n                        <tr><td colspan=15 align=center>Periode : ".$mulai.' s.d. '.$selesi."</td></tr>\r\n                        </table>\r\n                        <table border=1>\r\n                        <tr>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>No.</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['namakaryawan']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['nik'].'/'.$_SESSION['lang']['tmk']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['unit'].'/'.$_SESSION['lang']['bagian']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>No. Rekening</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['totLembur']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['tipekaryawan']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['statuspajak']."</td>\r\n                                <td bgcolor=#DEDEDE align=center rowspan='2'>".$_SESSION['lang']['jabatan'].'</td>';
        $shkdbyr = 'select distinct kodeabsen from '.$dbname.'.sdm_5absensi where kelompok=1 order by kodeabsen';
        $qhkdbyr = mysql_query($shkdbyr);
        $rowabs = mysql_num_rows($qhkdbyr);
        $shkdbyr2 = 'select distinct kodeabsen from '.$dbname.'.sdm_5absensi where kelompok=0 order by kodeabsen';
        $qhkdbyr2 = mysql_query($shkdbyr2);
        $rowabs2 = mysql_num_rows($qhkdbyr2);
        $stream .= "<td bgcolor=#DEDEDE align=center  colspan='".($rowabs + 1)."'>".$_SESSION['lang']['hkdibayar'].'</td>';
        $stream .= "<td bgcolor=#DEDEDE align=center colspan='".($rowabs2 + 1)."'>".$_SESSION['lang']['hktdkdibayar'].'</td>';
        $plsCol = count($arrIdKompPls);
        $minCol = count($arrIdKompMin);
        $stream .= "<td bgcolor=#DEDEDE align=center colspan='".($plsCol + 3)."'>".$_SESSION['lang']['penambah'].'</td>';
        $stream .= "<td bgcolor=#DEDEDE align=center colspan='".($minCol - 1)."'>".$_SESSION['lang']['pengurang'].'</td>';
        $stream .= "<td bgcolor=#DEDEDE align=center rowspan='2'>GAJI BERSIH</td></tr><tr>";
        while ($rdbyr = mysql_fetch_assoc($qhkdbyr)) {
            $stream .= '<td bgcolor=#DEDEDE align=center>'.$rdbyr['kodeabsen'].'</td>';
            $dtAbsByr[] = $rdbyr['kodeabsen'];
        }
        $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['total'].'</td>';
        while ($rdbyr = mysql_fetch_assoc($qhkdbyr2)) {
            $stream .= '<td bgcolor=#DEDEDE align=center>'.$rdbyr['kodeabsen'].'</td>';
            $dtAbsTdkByr[] = $rdbyr['kodeabsen'];
        }
        $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['total'].'</td>';
        foreach ($arrIdKompPls as $lstKompPls) {
            ++$brsPlus;
            $stream .= '<td bgcolor=#DEDEDE align=center>'.$arrNmKomPls[$lstKompPls].'</td>';
            if (1 == $brsPlus) {
                $stream .= '<td bgcolor=#DEDEDE align=center>'.$arrNmKomMin[37].'</td>';
                $stream .= '<td bgcolor=#DEDEDE align=center>'.$arrNmKomMin[36].'</td>';
            }
        }
        $stream .= '<td bgcolor=#DEDEDE align=center >'.$_SESSION['lang']['totalPendapatan'].'</td>';
        foreach ($arrIdKompMin as $lstKompMin) {
            if (20 != $lstKompMin && 19 != $lstKompMin) {
                $stream .= '<td bgcolor=#DEDEDE align=center>'.$arrNmKomMin[$lstKompMin].'</td>';
            }
        }
        $stream .= '<td bgcolor=#DEDEDE align=center >'.$_SESSION['lang']['totalPotongan'].'</td></tr>';
        $sSlip = "select distinct a.*,b.tipekaryawan,b.statuspajak,b.tanggalmasuk,b.nik,b.namakaryawan,b.bagian,c.namajabatan,d.nama,\r\n                b.norekeningbank from ".$dbname.'.sdm_gaji_vw a  left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n               left join ".$dbname.".sdm_5jabatan c on b.kodejabatan=c.kodejabatan\r\n               left join ".$dbname.'.sdm_5departemen d on b.bagian=d.kode where '.$where.'  '.$dtTipe.'';
        $qSlip = mysql_query($sSlip);
        $rCek = mysql_num_rows($qSlip);
        if (0 < $rCek) {
            while ($rSlip = mysql_fetch_assoc($qSlip)) {
                if ('' != $rSlip['karyawanid']) {
                    $arrKary[$rSlip['karyawanid']] = $rSlip['karyawanid'];
                    $arrKomp[$rSlip['karyawanid']] = $rSlip['idkomponen'];
                    $arrTglMsk[$rSlip['karyawanid']] = $rSlip['tanggalmasuk'];
                    $arrNik[$rSlip['karyawanid']] = $rSlip['nik'];
                    $arrNmKary[$rSlip['karyawanid']] = $rSlip['namakaryawan'];
                    $arrBag[$rSlip['karyawanid']] = $rSlip['bagian'];
                    $arrJbtn[$rSlip['karyawanid']] = $rSlip['namajabatan'];
                    $arrTipekary[$rSlip['karyawanid']] = $rSlip['tipekaryawan'];
                    $arrStatPjk[$rSlip['karyawanid']] = $rSlip['statuspajak'];
                    $arrDept[$rSlip['karyawanid']] = $rSlip['nama'];
                    $arrRek[$rSlip['karyawanid']] = $rSlip['norekeningbank'];
                    $arrJmlh[$rSlip['karyawanid'].$rSlip['idkomponen']] = $rSlip['jumlah'];
                    $arrTotal[$rSlip['idkomponen']] += $rSlip['jumlah'];
                }
            }
            $sTot = 'select tipelembur,jamaktual,karyawanid from '.$dbname.".sdm_lemburdt where substr(kodeorg,1,4)='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$rPeriod['tanggalmulai']."' and '".$rPeriod['tanggalsampai']."'";
            $qTot = mysql_query($sTot);
            while ($rTot = mysql_fetch_assoc($qTot)) {
                $sJum = 'select jamlembur as totalLembur from '.$dbname.".sdm_5lembur where tipelembur='".$rTot['tipelembur']."'\r\n                        and jamaktual='".$rTot['jamaktual']."' and kodeorg='".$_SESSION['empl']['lokasitugas']."'";
                $qJum = mysql_query($sJum);
                $rJum = mysql_fetch_assoc($qJum);
                $jumTot[$rTot['karyawanid']] += $rJum['totalLembur'];
            }
            $peng1 = 20;
            $peng2 = 19;
            foreach ($arrKary as $dtKary) {
                ++$no;
                $stream .= "<tr class=rowcontent>\r\n                                <td>".$no."</td>\r\n                                <td>".$arrNmKary[$dtKary]."</td>\r\n                                <td>".$arrNik[$dtKary]."</td>\r\n                                <td>".$arrDept[$dtKary]."</td>\r\n                                <td>".$arrRek[$dtKary]."</td>\r\n                                <td>".$jumTot[$dtKary]."</td>\r\n                                <td>".$rNmTipe[$arrTipekary[$dtKary]]."</td>\r\n                                <td>".$arrStatPjk[$dtKary]."</td>\r\n                                <td>".$arrJbtn[$dtKary].'</td>';
                foreach ($dtAbsByr as $dtJmlhAbsDbyr) {
                    $stream .= '<td align=right>'.number_format($brt[$dtKary][$dtJmlhAbsDbyr]).'</td>';
                    $totAbsen[$dtKary] += $brt[$dtKary][$dtJmlhAbsDbyr];
                    $grTotDbyr[$dtJmlhAbsDbyr] += $brt[$dtKary][$dtJmlhAbsDbyr];
                }
                $stream .= '<td align=right>'.number_format($totAbsen[$dtKary]).'</td>';
                foreach ($dtAbsTdkByr as $dtTidakDbyr) {
                    $stream .= '<td align=right>'.number_format($brt[$dtKary][$dtTidakDbyr]).'</td>';
                    $totAbsenTdkDbyr[$dtKary] += $brt[$dtKary][$dtTidakDbyr];
                    $grTotTdkDbyr[$dtTidakDbyr] += $brt[$dtKary][$dtTidakDbyr];
                }
                $stream .= '<td align=right>'.number_format($totAbsenTdkDbyr[$dtKary]).'</td>';
                $arrPlus = [];
                $s = 0;
                $brsPlus2 = 0;
                foreach ($arrIdKompPls as $lstKompPls) {
                    $stream .= '<td align=right>'.number_format($arrJmlh[$dtKary.$lstKompPls], 2).'</td>';
                    $arrPlus[$s] = $arrJmlh[$dtKary.$lstKompPls];
                    ++$s;
                    ++$brsPlus2;
                    if (1 == $brsPlus2) {
                        $stream .= '<td>-'.number_format($arrJmlh[$dtKary.$peng1], 2).'</td>';
                        $stream .= '<td>-'.number_format($arrJmlh[$dtKary.$peng2], 2).'</td>';
                    }
                }
                $totDpt = array_sum($arrPlus) - ($arrJmlh[$dtKary.$peng1] + $arrJmlh[$dtKary.$peng2]);
                $stream .= '<td align=right>'.number_format($totDpt, 2).'</td>';
                $arrMin = [];
                $q = 0;
                foreach ($arrIdKompMin as $lstKompMin) {
                    if (20 != $lstKompMin && 19 != $lstKompMin) {
                        $stream .= '<td align=right>'.number_format($arrJmlh[$dtKary.$lstKompMin]).'</td>';
                        $arrMin[$q] = $arrJmlh[$dtKary.$lstKompMin];
                        ++$q;
                    }
                }
                $gajiBersih = $totDpt - array_sum($arrMin);
                $stream .= '<td align=right>'.number_format(array_sum($arrMin), 2).'</td>';
                $stream .= '<td align=right>'.number_format($gajiBersih, 0).'</td></tr>';
            }
            $stream .= '<tr><td colspan='.(9 + $rowabs + $rowabs2 + 2).' align=right>'.$_SESSION['lang']['total'].'</td>';
            $s = 0;
            $brsPlus2 = 0;
            $arrPlus = [];
            foreach ($arrIdKompPls as $lstKompPls) {
                $stream .= '<td align=right>'.number_format($arrTotal[$lstKompPls], 2).'</td>';
                $arrPlus[$s] = $arrTotal[$lstKompPls];
                ++$s;
                ++$brsPlus2;
                if (1 == $brsPlus2) {
                    $stream .= '<td>-'.number_format($arrTotal[$peng1], 2).'</td>';
                    $stream .= '<td>-'.number_format($arrTotal[$peng2], 2).'</td>';
                }
            }
            $totDpt = array_sum($arrPlus) - ($arrTotal[$peng1] + $arrTotal[$peng2]);
            $stream .= '<td align=right>'.number_format($totDpt, 2).'</td>';
            $arrMin = [];
            $q = 0;
            foreach ($arrIdKompMin as $lstKompMin) {
                if (20 != $lstKompMin && 19 != $lstKompMin) {
                    $stream .= '<td align=right>'.number_format($arrTotal[$lstKompMin]).'</td>';
                    $arrMin[$q] = $arrTotal[$lstKompMin];
                    ++$q;
                }
            }
            $gajiBersih = $totDpt - array_sum($arrMin);
            $stream .= '<td align=right>'.number_format(array_sum($arrMin), 2).'</td>';
            $stream .= '<td align=right>'.number_format($gajiBersih, 0).'</td>';
            $stream .= '</tr>';
        }

        $stream .= '</table>Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $dte = date('YmdHms');
        $nop_ = 'GajiHarian'.$_SESSION['empl']['lokasitugas'].$dte;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $stream);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                            window.location='tempExcel/".$nop_.".xls.gz';\r\n                            </script>";

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