<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
('' === $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
('' === $_POST['thnId'] ? ($thnId = $_GET['thnId']) : ($thnId = $_POST['thnId']));
('' === $_POST['kdProj'] ? ($kdProj = $_GET['kdProj']) : ($kdProj = $_POST['kdProj']));
$tipe = 'PNN';
$unitId = $_SESSION['lang']['all'];
$dktlmpk = $_SESSION['lang']['all'];
if ('preview' === $proses) {
    if ($_POST['tanggal2'] < $_POST['tanggal1']) {
        exit('error: Tolong gunakan urutan tanggal yang benar');
    }

    $tglPP = explode('-', $_POST['tanggal1']);
    list($date1, $month1, $year1) = $tglPP;
    $tgl2 = $_POST['tanggal2'];
    $pecah2 = explode('-', $tgl2);
    list($date2, $month2, $year2) = $pecah2;
    $jd1 = gregoriantojd($month1, $date1, $year1);
    $jd2 = gregoriantojd($month2, $date2, $year2);
    $jmlHari = $jd2 - $jd1;
    if ('' === $_POST['tanggal1'] || '' === $_POST['tanggal2']) {
        exit('error: '.$_SESSION['lang']['tanggal'].'1 dan '.$_SESSION['lang']['tanggal'].' 2 tidak boleh kosong');
    }

    if ($month1 !== $month2) {
        exit('error: Harus dalam periode yang sama');
    }
}

('' === $_POST['tanggal1'] ? ($tanggal1 = $_GET['tanggal1']) : ($tanggal1 = $_POST['tanggal1']));
('' === $_POST['tanggal2'] ? ($tanggal2 = $_GET['tanggal2']) : ($tanggal2 = $_POST['tanggal2']));
$tangsys1 = putertanggal($tanggal1);
$tangsys2 = putertanggal($tanggal2);
$wheretang = " b.tanggal like '%%' ";
if ('' !== $tanggal1) {
    $wheretang = " b.tanggal = '".$tangsys1."' ";
    if ('' !== $tanggal2) {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

if ('' !== $tanggal2) {
    $wheretang = " b.tanggal = '".$tangsys2."' ";
    if ('' !== $tanggal1) {
        $wheretang = " b.tanggal between '".$tangsys1."' and '".$tangsys2."' ";
    }
}

$arr = '##kdOrg##tanggal1##tanggal2';
if ('preview' === $proses || 'excel' === $proses) {
    $brdr = 0;
    $bgcoloraja = '';
    if ('excel' === $proses) {
        $brdr = 1;
        $bgcoloraja = 'green';
    }

    if ('' !== $_POST['tipeTrk']) {
        $whre = " and tipetransaksi='".$_POST['tipeTrk']."'";
    }

    $sData = 'select distinct a.notransaksi,a.nik,a.tarif,a.hasilkerja,a.hasilkerjakg,a.bjraktual,a.upahkerja,a.upahpremi,a.kodeorg,a.rupiahpenalty'.',a.penalti1,a.penalti2,a.penalti3,a.penalti4,a.penalti5,a.penalti6,a.penalti7,b.tanggal from '.$dbname.'.kebun_prestasi a left join '.$dbname.".kebun_aktifitas b\r\n               on a.notransaksi=b.notransaksi\r\n               where  b.kodeorg='".$kdOrg."' and b.jurnal=0 and b.notransaksi like '%".$tipe."%'\r\n               and ".$wheretang."\r\n               ".$whre.' order by tanggal,kodeorg asc';
    $qData = mysql_query($sData) ;
    $rowdt = mysql_num_rows($qData);
    if ('HO_ITGS' === $_SESSION['empl']['bagian']) {
        $tab .= '<button class=mybutton onclick=postingDat('.$rowdt.")  id=revTmbl>Update Data</button>&nbsp;<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr.php','".$arr."')>Excel</button>";
    } else {
        $tab .= "<button class=mybutton onclick=zExcel(event,'kebun_slave_3updategajibjr.php','".$arr."')>Excel</button>";
    }

    $tab .= '<table cellspacing=1 border='.$brdr." class=sortable>\r\n\t<thead class=rowheader>\r\n\t<tr>\r\n        <td ".$bgcoloraja." align=center rowspan=2>No.</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['notransaksi']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['kodeblok']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['nik']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['namakaryawan']."</td>\r\n        <td ".$bgcoloraja.' align=center rowspan=2>'.$_SESSION['lang']['tarif']."</td>\r\n            <td colspan=6 align=center>Sebelum</td>\r\n            <td colspan=6 align=center>Sesudah</td></tr>\r\n        <tr><td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['kg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['upah']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['premi']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['rupiahpenalty']."</td>\r\n            <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['kg']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['upah']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['premi']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['rupiahpenalty']."</td>\r\n            \r\n        </tr>\r\n            \r\n\r\n        <!--<td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti1']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti2']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti3']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti4']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti5']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti6']."</td>\r\n        <td ".$bgcoloraja.' align=center>'.$_SESSION['lang']['penalti7']."</td>-->\r\n        \r\n        </tr>";
    $tab .= '</tr></thead><tbody>';
    while ($rData = mysql_fetch_assoc($qData)) {
        ++$nor;
        $whr = "karyawanid='".$rData['nik']."'";
        $optNmKar = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', $whr);
        $optNikKar = makeOption($dbname, 'datakaryawan', 'karyawanid,nik', $whr);
        $tab .= '<tr class=rowcontent id=rowDt_'.$nor.'><td align=center>'.$nor.'</td>';
        $tab .= '<td id=notransaksi_'.$nor.'>'.$rData['notransaksi'].'</td>';
        $tab .= '<td id=kodeblok_'.$nor.'>'.$rData['kodeorg'].'</td>';
        $tab .= "<td id='tanggal_".$nor."'>".$rData['tanggal'].'</td>';
        $tab .= '<td><input type=hidden id=karyawanid_'.$nor.' value='.$rData['nik'].' />'.$optNikKar[$rData['nik']].'</td>';
        $tab .= '<td>'.$optNmKar[$rData['nik']].'</td>';
        $tab .= '<td>'.$rData['tarif'].'</td>';
        $tab .= '<td align=right>'.$rData['bjraktual'].'</td>';
        $tab .= '<td align=right>'.$rData['hasilkerja'].'</td>';
        $tab .= '<td align=right>'.$rData['hasilkerjakg'].'</td>';
        $tab .= '<td align=right>'.number_format($rData['upahkerja'], 2).'</td>';
        $tab .= '<td align=right>'.number_format($rData['upahpremi'], 0).'</td>';
        $tab .= '<td align=right>'.number_format($rData['rupiahpenalty'], 2).'</td>';
        if (substr($rData['kodeorg'], 0, 6) !== $afdDet) {
            $afdDet = substr($rData['kodeorg'], 0, 6);
            $sBjr = "SELECT sum(a.totalkg)/sum(a.jjg) as bjr,tanggal \r\n                       FROM ".$dbname.'.`kebun_spbdt` a left join '.$dbname.".kebun_spbht b on \r\n                       a.nospb=b.nospb where blok like '".$afdDet."%'\r\n                       and tanggal <= '".$rData['tanggal']."' group by tanggal order by tanggal desc limit 1";
            $qBjr = mysql_query($sBjr) ;
            $rBjr = mysql_fetch_assoc($qBjr);
            $reg = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
            $regionalDt = $reg[substr($afdDet, 0, 4)];
        }

        if (0 === $rBjr['bjr']) {
            $rBjr['bjr'] = $rData['bjraktual'];
        } else {
            $rBjr['bjr'] = $rBjr['bjr'];
        }

        $tab .= '<td align=right id=brjAktual_'.$nor.'>'.number_format($rBjr['bjr'], 2).'</td>';
        $tab .= '<td align=right>'.$rData['hasilkerja'].'</td>';
        $hasilKg = 0;
        $hasilKg = $rData['hasilkerja'] * $rBjr['bjr'];
        $tab .= '<td align=right id=hasilKg_'.$nor.'>'.number_format($hasilKg, 2).'</td>';
        $qUMR = selectQuery($dbname, 'sdm_5gajipokok', 'sum(jumlah) as nilai', 'karyawanid='.$rData['nik'].' and tahun='.substr($rData['tanggal'], 0, 4).' and idkomponen in (1,31)');
        $Umr = fetchData($qUMR);
        $uphHarian = $Umr[0]['nilai'] / 25;
        if (0 === $uphHarian) {
            $uphHarian = 0;
            $insentif = 0;
        }

        $qwe = date('D', strtotime($rData['tanggal']));
        $dhr = "regional='".$regionalDt."' and tanggal='".$rData['tanggal']."'";
        $optHariLbr = makeOption($dbname, 'sdm_5harilibur', 'regional,tanggal', $dhr);
        $regData = $regionalDt;
        $param['blok'] = $rData['kodeorg'];
        $dtr = "kodeorg='".$param['blok']."'";
        unset($optTopo);
        $optTopo = makeOption($dbname, 'setup_blok', 'kodeorg,topografi', $dtr);
        if ('KALTENG' === $regData) {
            $afd = substr($param['blok'], 0, 6);
            $dmn = "kodeorg='".$afd."'";
            $optCek = makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis', $dmn);
            if (isset($optCek[$afd]) && '' !== $optCek[$afd]) {
                $regData = $afd;
            }
        }

        $dmn = "kodeorg='".$regData."' and jenis='".$rData['tarif']."'";
        $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
        $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
        $optDenda = makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis', $dmn);
        unset($optIns);
        if ('B1' === $optTopo[$param['blok']]) {
            $optIns = makeOption($dbname, 'kebun_5basispanen', 'jenis,rptopografi', $dmn);
        }

        $lstert = 0;
        $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                         kodeorg='".$regionalDt."' and jenis='satuan' order by bjr desc";
        $qTarif = mysql_query($sTarif) ;
        while ($rTarif = mysql_fetch_assoc($qTarif)) {
            $rpLbh[$rTarif['bjr']] = $rTarif['rplebih'];
            $basisPanen[$rTarif['bjr']] = $rTarif['basisjjg'];
            $lstBjr[] = $rTarif['bjr'];
            $lstBjr2[$lstert] = $rTarif['bjr'];
            ++$lstert;
        }
        $param['tarif'] = $rData['tarif'];
        $param['jmlhJjg'] = $rData['hasilkerja'];
        $param['bjraktual'] = $rBjr['bjr'];
        if ('Sun' === $qwe || isset($optHariLbr[$regionalDt]) && '' !== $optHariLbr[$regionalDt]) {
            $basis = 0;
            if ('KALTENG' === $regionalDt) {
                switch ($param['tarif']) {
                    case 'harian':
                        $basis = $optBasis[$param['tarif']];
                        if (0 === $basis) {
                            $upah = $uphHarian;
                            $insentif = $optIns[$param['tarif']];
                        }

                        if (0 !== $basis) {
                            if ('1' === $optDenda[$param['tarif']]) {
                                if ($param['jmlhJjg'] < $basis) {
                                    $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                                } else {
                                    if ($basis < $param['jmlhJjg']) {
                                        if (0 !== $optRp[$param['tarif']]) {
                                            $upah = $uphHarian + $optRp[$param['tarif']] * ($param['jmlhJjg'] - $basis);
                                        } else {
                                            $upah = $uphHarian;
                                        }
                                    } else {
                                        $upah = $uphHarian;
                                    }
                                }
                            } else {
                                $upah = $optRp[$param['tarif']] * $hasilKg;
                            }

                            $insentif = $optIns[$param['tarif']];
                        }

                        break;
                    case 'satuan':
                        $upah = $optRp[$param['tarif']] * $hasilKg;
                        $insentif = $optIns[$param['tarif']];

                        break;
                }
            } else {
                if ('SUMUT' === $regionalDt) {
                    switch ($param['tarif']) {
                        case 'harian':
                            $upah = $uphHarian * $optRp[$param['tarif']];
                            $insentif = 0;
                            $basis = $optBasis[$param['tarif']];

                            break;
                        case 'satuan':
                            $MaxRow = count($lstBjr);
                            foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                                if (0 === $lstRow) {
                                    if ($dtIsiBjr < $param['bjraktual']) {
                                        $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;
                                        $dtbjr = $dtIsiBjr;

                                        break;
                                    }
                                } else {
                                    if ($lstRow !== $MaxRow) {
                                        $leapdt = $lstRow + 1;
                                        if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                                            $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;
                                            $dtbjr = $dtIsiBjr;

                                            break;
                                        }
                                    } else {
                                        $dmin = $dtIsiBjr - 1;
                                        $dtbjr = $dtIsiBjr;
                                        if ($dmin <= $param['bjraktual']) {
                                            $hsl = $rpLbh[$dtIsiBjr] * $hasilKg;

                                            break;
                                        }

                                        $hsl = $param['jmlhJjg'] / $basisPanen[$dtIsiBjr] * $uphHarian;
                                    }
                                }
                            }
                            $dmn = "kodeorg='".$regionalDt."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
                            $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
                            $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
                            $optDenda = makeOption($dbname, 'kebun_5basispanen', 'jenis,dendabasis', $dmn);
                            $upah = $hsl;
                            $insentif = 0;
                            $basis = $optBasis[$param['tarif']];

                            break;
                    }
                }
            }
        } else {
            switch ($regionalDt) {
                case 'KALTENG':
                    switch ($param['tarif']) {
                        case 'harian':
                            $basis = $optBasis[$param['tarif']];
                            if (0 === $basis) {
                                $upah = $uphHarian;
                                $insentif = $optIns[$param['tarif']];
                            }

                            if (0 !== $basis) {
                                if ('1' === $optDenda[$param['tarif']]) {
                                    if ($param['jmlhJjg'] < $basis) {
                                        $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                                    } else {
                                        if ($basis < $param['jmlhJjg']) {
                                            if (0 !== $optRp[$param['tarif']]) {
                                                $upah = $uphHarian + $optRp[$param['tarif']] * ($param['jmlhJjg'] - $basis);
                                            } else {
                                                $upah = $uphHarian;
                                            }
                                        } else {
                                            $upah = $uphHarian;
                                        }
                                    }
                                } else {
                                    $upah = $optRp[$param['tarif']] * $hasilKg;
                                }

                                $insentif = $optIns[$param['tarif']];
                            }

                            break;
                        case 'satuan':
                            $basis = $optBasis[$param['tarif']];
                            $upah = $hasilKg * $optRp[$param['tarif']];
                            $insentif = $optIns[$param['tarif']];

                            break;
                    }

                    break;
                case 'KALIMANTAN':
                    switch ($param['tarif']) {
                        case 'harian':
                            $basis = $optBasis[$param['tarif']];
                            if ('1' === $optDenda[$param['tarif']]) {
                                if ($param['jmlhJjg'] < $basis) {
                                    $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                                } else {
                                    $upah = $uphHarian;
                                }
                            } else {
                                $upah = $uphHarian;
                            }

                            $insentif = 0;

                            break;
                        case 'satuan':
                            $insentif = 0;
                            $MaxRow = count($lstBjr);
                            foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                                if (0 === $lstRow) {
                                    if ($dtIsiBjr < $param['bjraktual']) {
                                        $upah = $rpLbh[$dtIsiBjr] * $hasilKg;
                                        $dtbjr = $dtIsiBjr;

                                        break;
                                    }
                                } else {
                                    if ($lstRow !== $MaxRow) {
                                        $leapdt = $lstRow + 1;
                                        if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                                            $upah = $rpLbh[$dtIsiBjr] * $hasilKg;
                                            $dtbjr = $dtIsiBjr;

                                            break;
                                        }
                                    } else {
                                        $dmin = $dtIsiBjr - 1;
                                        $dtbjr = $dtIsiBjr;
                                        if ($dmin <= $param['bjraktual']) {
                                            $upah = $rpLbh[$dtIsiBjr] * $hasilKg;

                                            break;
                                        }

                                        $upah = $param['jmlhJjg'] / $basisPanen[$dtIsiBjr] * $uphHarian;
                                    }
                                }
                            }
                            $dmn = "kodeorg='".$regionalDt."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
                            $optBasis = makeOption($dbname, 'kebun_5basispanen', 'jenis,basisjjg', $dmn);
                            $basis = $optBasis[$param['tarif']];
                            $insentif = 0;
                            if ('1' === $optDenda[$param['tarif']] && $param['jmlhJjg'] < $basis) {
                                $upah = $param['jmlhJjg'] / $basis * $uphHarian;
                            }

                            break;
                    }

                    break;
            }
            if ('KALTENG' === $regionalDt) {
                $dtbjr = 0;
            } else {
                $lstert = 0;
                $sTarif = 'select distinct * from '.$dbname.".kebun_5basispanen where \r\n                             kodeorg='".$regionalDt."' and jenis='".$param['tarif']."' order by bjr desc";
                $qTarif = mysql_query($sTarif) ;
                while ($rTarif = mysql_fetch_assoc($qTarif)) {
                    $rpLbh[$rTarif['bjr']] = $rTarif['rplebih'];
                    $basisPanen[$rTarif['bjr']] = $rTarif['basisjjg'];
                    $lstBjr[] = $rTarif['bjr'];
                    $lstBjr2[$lstert] = $rTarif['bjr'];
                    ++$lstert;
                }
                $MaxRow = count($lstBjr);
                foreach ($lstBjr as $lstRow => $dtIsiBjr) {
                    if (0 === $lstRow) {
                        if ($dtIsiBjr < $param['bjraktual']) {
                            $dtbjr = $dtIsiBjr;

                            break;
                        }
                    } else {
                        if ($lstRow !== $MaxRow) {
                            $leapdt = $lstRow + 1;
                            if ($param['bjraktual'] === $dtIsiBjr || $lstBjr2[$leapdt] < $param['bjraktual']) {
                                $dtbjr = $dtIsiBjr;

                                break;
                            }
                        } else {
                            $dmin = $dtIsiBjr - 1;
                            if ($dmin <= $param['bjraktual']) {
                                $dtbjr = $dtIsiBjr;

                                break;
                            }

                            $dtbjr = 0;
                        }
                    }
                }
            }

            $regData = $regionalDt;
            if ('KALTENG' === $regionalDt) {
                $afd = substr($param['blok'], 0, 6);
                $dmn = "kodeorg='".$afd."'";
                $optCek = makeOption($dbname, 'kebun_5basispanen', 'kodeorg,jenis', $dmn);
                if ('' !== $optCek[$afd]) {
                    $regData = $afd;
                }
            }

            $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."' and bjr='".$dtbjr."'";
            if ('KALTENG' === $_SESSION['empl']['regional']) {
                $dmn = "kodeorg='".$regData."' and jenis='".$param['tarif']."'";
            }

            if ('H12E02' === $regData) {
                $dmn = "kodeorg='".$regionalDt."' and jenis='satuan'";
            }

            unset($optRp, $optDenda);

            $denda = 0;
            $optRp = makeOption($dbname, 'kebun_5basispanen', 'jenis,rplebih', $dmn);
            $optDenda = makeOption($dbname, 'kebun_5denda', 'kode,jumlah');
            for ($der = 1; $der < 8; ++$der) {
                if (1 === $der) {
                    $det = 'BM';
                    $dend = $rData['penalti'.$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                } else {
                    if (3 === $der) {
                        $det = 'TD';
                        $dend = $rData['penalti'.$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                    } else {
                        if (5 === $der) {
                            $det = 'BT';
                            $dend = $rData['penalti'.$der] / $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                        } else {
                            $det = 'TP';
                            $dend = $rData['penalti'.$der] * $optDenda[$det] * $param['bjraktual'] * $optRp[$param['tarif']];
                        }
                    }
                }

                $denda += $dend;
            }
            $tab .= '<td align=right  id=updUpah_'.$nor.'>'.number_format($upah, 2).'</td>';
            $tab .= '<td align=right  id=updInsentif_'.$nor.'>'.number_format($insentif, 0).'</td>';
            $tab .= '<td align=right  id=updDenda_'.$nor.'>'.number_format($denda, 2).'</td>';
            $tab .= '</tr>';
        }
    }
    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'getPeriode':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPeriodeAkut = 'select distinct periode from '.$dbname.".setup_periodeakuntansi \r\n                         where kodeorg='".$_POST['kdOrg']."' and tutupbuku=0";
        $qPeriodeCari = mysql_query($sPeriodeAkut) ;
        while ($rPeriodeCari = mysql_fetch_assoc($qPeriodeCari)) {
            $optPeriode .= "<option value='".$rPeriodeCari['periode']."'>".$rPeriodeCari['periode'].'</option>';
        }
        echo $optPeriode;

        break;
    case 'updateData':
        $scek = 'select distinct * from '.$dbname.".kebun_aktifitas where notransaksi='".$_POST['notransaksi']."' and jurnal=1";
        $qcek = mysql_query($scek) ;
        $rcek = mysql_num_rows($qcek);
        if (1 === $rcek) {
            echo '1';

            break;
        }

        if ('' === $_POST['kodeorg'] && '' === $_POST['notransaksi'] && '' === $_POST['nik']) {
            echo '1';

            break;
        }

        if ('0' === (int) ($_POST['upah']) && '' === $_POST['nik'] && '0' === (int) ($_POST['hasilKg']) && '0' === (int) ($_POST['brjAktual'])) {
            echo '1';

            break;
        }

        $_POST['upah'] = str_replace(',', '', $_POST['upah']);
        $_POST['hasilKg'] = str_replace(',', '', $_POST['hasilKg']);
        $_POST['insentif'] = str_replace(',', '', $_POST['insentif']);
        $_POST['denda'] = str_replace(',', '', $_POST['denda']);
        $supdate = 'update '.$dbname.".kebun_prestasi set upahkerja='".$_POST['upah']."',"."hasilkerjakg='".$_POST['hasilKg']."',upahpremi='".$_POST['insentif']."',"."rupiahpenalty='".$_POST['denda']."',bjraktual='".$_POST['brjAktual']."' "."where kodeorg='".$_POST['kodeorg']."' and notransaksi='".$_POST['notransaksi']."' and nik='".$_POST['nik']."'";
        if (!mysql_query($supdate)) {
            exit('error: db bermasalah '.mysql_error($conn).'___'.$supdate);
        }

        break;
    case 'excel':
        $thisDate = date('YmdHms');
        $nop_ = 'laporanUpdateBjr_'.$thisDate;
        $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');
        gzwrite($gztralala, $tab);
        gzclose($gztralala);
        echo "<script language=javascript1.2>\r\n                       window.location='tempExcel/".$nop_.".xls.gz';\r\n                       </script>";

        break;
    default:
        break;
}
function putertanggal($tanggal)
{
    $qwe = explode('-', $tanggal);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

?>