<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$param = $_POST;
if ('' != isset($_GET['proses'])) {
    if ('excel' == substr($_GET['proses'], 0, 5)) {
        $param = $_GET;
        $tab .= $_SESSION['lang']['biayapengobatan'];
        $brd = 1;
        $bgcolor = 'bgcolor=#DEDEDE';
    } else {
        $param['proses'] = $_GET['proses'];
    }
}

$optNmBy = makeOption($dbname, 'sdm_5jenisbiayapengobatan', 'kode,nama');
$optTpkary = makeOption($dbname, 'sdm_5tipekaryawan', 'id,tipe');
$optRegional = makeOption($dbname, 'bgt_regional_assignment', 'kodeunit,regional');
$optNmKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
if ('' != $param['ptId2']) {
    $whr .= " and b.kodeorganisasi='".$param['ptId2']."'";
}

if ('' != $param['unitId2']) {
    $whr = '';
    $whr .= " and b.lokasitugas='".$param['ptId2']."'";
}

if ('I' == $param['smstr']) {
    $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
} else {
    $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
}

$arrbln = [];
$arrSmstrSatu = ['01' => $_SESSION['lang']['jan'], '02' => $_SESSION['lang']['feb'], '03' => $_SESSION['lang']['mar'], '04' => $_SESSION['lang']['apr'], '05' => $_SESSION['lang']['mei'], '06' => $_SESSION['lang']['jun']];
$arrSmstrDua = ['07' => $_SESSION['lang']['jul'], '08' => $_SESSION['lang']['agt'], '09' => $_SESSION['lang']['sep'], 10 => $_SESSION['lang']['okt'], 11 => $_SESSION['lang']['nov'], 12 => $_SESSION['lang']['dec']];
('I' == $param['smstr'] ? ($arrbln = $arrSmstrSatu) : ($arrbln = $arrSmstrDua));
if ('preview' == $param['proses'] || 'excel' == $param['proses']) {
    $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5\r\n                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
    $qstaff = mysql_query($sstaff);
    while ($rstaff = mysql_fetch_assoc($qstaff)) {
        $dtby[$optRegional[$rstaff['kodeorg']].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
        $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
        $dtReg[$optRegional[$rstaff['kodeorg']]] = $optRegional[$rstaff['kodeorg']];
    }
    $snonstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from \r\n                 ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=5\r\n                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
    $qnonstaff = mysql_query($snonstaff);
    while ($rnonstaff = mysql_fetch_assoc($qnonstaff)) {
        $dtby[$optRegional[$rnonstaff['kodeorg']].$rnonstaff['kodebiaya'].$rnonstaff['periode']] += $rnonstaff['jmlhdbyr'];
        $kdBy[$rnonstaff['kodebiaya']] = $rnonstaff['kodebiaya'];
        $tpKary[$rnonstaff['tipekaryawan']] = $rnonstaff['tipekaryawan'];
    }
    if ('excel' != $param['proses']) {
        $brd = 0;
        $bgcolor = '';
    } else {
        $tab .= $_SESSION['lang']['biayapengobatan'];
        $brd = 1;
        $bgcolor = 'bgcolor=#DEDEDE';
    }

    $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead><tr>';
    $tab .= '<td  rowspan=2>'.$_SESSION['lang']['regional'].'</td>';
    foreach ($kdBy as $lstBy) {
        $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
    }
    $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr>';
    foreach ($kdBy as $lstBy) {
        foreach ($arrbln as $lstBln => $dftrbln) {
            $tab .= '<td  align=center>'.$dftrbln.'</td>';
        }
        $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
    }
    $tab .= '</thead><tbody>';
    foreach ($dtReg as $lstRegional) {
        $tab .= '<tr class=rowcontent>';
        $tab .= "<td style='cursor:pointer;' onclick=getDetRegional('".$lstRegional."','')>".$lstRegional.'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                    $linkDet = "title='detail data ".$lstRegional.','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getDetRegional('".$lstRegional."','".$lstBy."')";
                }

                $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
        }
        $tab .= '</tr>';
    }
}

switch ($param['proses']) {
    case 'preview':
        echo $tab;

        break;
    case 'level1':
        echo $tab;

        break;
    case 'excel':
        if ('' == $param['ptId2']) {
            $param['ptId2'] = $_SESSION['lang']['all'];
        }

        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'RekapByPengobatan_'.$param['ptId2'];
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getDetailRegional':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= "and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5\r\n                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['kodebiaya'].$rstaff['periode']] = $rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
        }
        $snonstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from \r\n                 ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=5\r\n                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
        $qnonstaff = mysql_query($snonstaff);
        while ($rnonstaff = mysql_fetch_assoc($qnonstaff)) {
            $dtnonby[$rnonstaff['tipekaryawan']][$rnonstaff['kodebiaya'].$rnonstaff['periode']] = $rnonstaff['jmlhdbyr'];
            $kdBy[$rnonstaff['kodebiaya']] = $rnonstaff['kodebiaya'];
            $tpKary[$rnonstaff['tipekaryawan']] = $rnonstaff['tipekaryawan'];
        }
        if ('excel' != $param['proses']) {
            $brd = 0;
            $bgcolor = '';
        } else {
            $tab .= $_SESSION['lang']['biayapengobatan'];
            $brd = 1;
            $bgcolor = 'bgcolor=#DEDEDE';
        }

        $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead><tr>';
        $tab .= '<td rowspan=2>No.</td>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['tipekaryawan'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr>';
        $tab .= '<tr>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</tr></thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>1</td>';
        $tab .= "<td style='cursor:pointer;' onclick=detailStaff('0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>Staff</td>";
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $det = "style='cursor:pointer;' onclick=detailDt2('0','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."')";
                $tab .= '<td align=right '.$det.'>'.number_format($dtby[$lstBy.$prd], 0).'</td>';
                $totPerby[$lstBy] += $dtby[$lstBy.$prd];
                $grndtotstaff += $dtby[$lstBy.$prd];
                $totPerbln[$lstBy.$prd] += $dtby[$lstBy.$prd];
                $totBiaya[$lstBy] += $dtby[$lstBy.$prd];
            }
            $tab .= '<td align=right>'.number_format($totPerby[$lstBy], 0).'</td>';
        }
        $tab .= '<td align=right>'.number_format($grndtotstaff, 0).'</td>';
        $tab .= '</tr>';
        $now = 1;
        foreach ($tpKary as $lstKary) {
            ++$now;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$now.'</td>';
            $tab .= '<td>'.$optTpkary[$lstKary].'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    $det = "style='cursor:pointer;' onclick=detailDt2('".$lstKary."','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')";
                    $tab .= '<td align=right '.$det.'>'.number_format($dtnonby[$lstKary][$lstBy.$prd], 0).'</td>';
                    $grndtotnonstaff[$lstKary] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totPerbln[$lstBy.$prd] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totPerby2[$lstKary.$lstBy] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totBiaya[$lstBy] += $dtnonby[$lstKary][$lstBy.$prd];
                }
                $tab .= '<td align=right>'.number_format($totPerby2[$lstKary.$lstBy], 0).'</td>';
            }
            $tab .= '<td align=right>'.number_format($grndtotnonstaff[$lstKary], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['grnd_total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td align=right>'.number_format($totPerbln[$lstBy.$prd], 0).'</td>';
                $grndtotsmua += $totPerbln[$lstBy.$prd];
            }
            $tab .= '<td align=right>'.number_format($totBiaya[$lstBy], 0).'</td>';
        }
        $tab .= '<td align=right>'.number_format($grndtotsmua, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelDt(event,'sdm_slave_2biayapengobatan1.php','".$param['regional']."','".$param['byPeng']."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(0)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'excelgetDetail2':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= "and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5\r\n                  group by kodebiaya,periode,a.kodeorg order by b.tipekaryawan";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['kodebiaya'].$rstaff['periode']] = $rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
        }
        $snonstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,a.kodeorg,kodebiaya,b.tipekaryawan from \r\n                 ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan!=5\r\n                  group by kodebiaya,periode,a.kodeorg,b.tipekaryawan order by b.tipekaryawan";
        $qnonstaff = mysql_query($snonstaff);
        while ($rnonstaff = mysql_fetch_assoc($qnonstaff)) {
            $dtnonby[$rnonstaff['tipekaryawan']][$rnonstaff['kodebiaya'].$rnonstaff['periode']] = $rnonstaff['jmlhdbyr'];
            $kdBy[$rnonstaff['kodebiaya']] = $rnonstaff['kodebiaya'];
            $tpKary[$rnonstaff['tipekaryawan']] = $rnonstaff['tipekaryawan'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable><thead><tr '.$bgcolor.'>';
        $tab .= '<td rowspan=2>No.</td>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['tipekaryawan'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr>';
        $tab .= '<tr  '.$bgcolor.'>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</tr></thead><tbody>';
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>1</td>';
        $tab .= "<td style='cursor:pointer;' onclick=detailStaff('0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>Staff</td>";
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $det = "style='cursor:pointer;' onclick=detailDt2('0','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."')";
                $tab .= '<td align=right '.$det.'>'.number_format($dtby[$lstBy.$prd], 0).'</td>';
                $totPerby[$lstBy] += $dtby[$lstBy.$prd];
                $grndtotstaff += $dtby[$lstBy.$prd];
                $totPerbln[$lstBy.$prd] += $dtby[$lstBy.$prd];
                $totBiaya[$lstBy] += $dtby[$lstBy.$prd];
            }
            $tab .= '<td align=right>'.number_format($totPerby[$lstBy], 0).'</td>';
        }
        $tab .= '<td align=right>'.number_format($grndtotstaff, 0).'</td>';
        $tab .= '</tr>';
        $now = 1;
        foreach ($tpKary as $lstKary) {
            ++$now;
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$now.'</td>';
            $tab .= '<td>'.$optTpkary[$lstKary].'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    $det = "style='cursor:pointer;' onclick=detailDt2('".$lstKary."','".$lstBy."','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."')";
                    $tab .= '<td align=right '.$det.'>'.number_format($dtnonby[$lstKary][$lstBy.$prd], 0).'</td>';
                    $grndtotnonstaff[$lstKary] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totPerbln[$lstBy.$prd] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totPerby2[$lstKary.$lstBy] += $dtnonby[$lstKary][$lstBy.$prd];
                    $totBiaya[$lstBy] += $dtnonby[$lstKary][$lstBy.$prd];
                }
                $tab .= '<td align=right>'.number_format($totPerby2[$lstKary.$lstBy], 0).'</td>';
            }
            $tab .= '<td align=right>'.number_format($grndtotnonstaff[$lstKary], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td colspan=2>'.$_SESSION['lang']['grnd_total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td align=right>'.number_format($totPerbln[$lstBy.$prd], 0).'</td>';
                $grndtotsmua += $totPerbln[$lstBy.$prd];
            }
            $tab .= '<td align=right>'.number_format($totBiaya[$lstBy], 0).'</td>';
        }
        $tab .= '<td align=right>'.number_format($grndtotsmua, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'detailPengobatan';
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getDetail3':
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>\r\n        <tr class=rowheader>\r\n        <td width=50></td>\r\n        <td>No</td>\r\n        <td width=100>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td width=50>".$_SESSION['lang']['periode']."</td>\r\n        <td width=30>".$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td width=200>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['dibayar']."</td>\r\n        <td width=90>".$_SESSION['lang']['perusahaan']."</td>\r\n        <td width=90>".$_SESSION['lang']['karyawan']."</td>\r\n        <td width=90>Jamsostek</td>      \r\n        <td>".$_SESSION['lang']['diagnosa']."</td>\r\n        <td>".$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead><tbody>";
        $str = "select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama \r\n              from ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".sdm_5rs b on a.rs=b.id \r\n              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid \r\n              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id \r\n              left join ".$dbname.".sdm_karyawankeluarga f\r\n              on a.ygsakit=f.nomor\r\n              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'\r\n              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'\r\n              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
        $res = mysql_query($str) || mysql_error($conn);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pasien = '';
            $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n              where nomor=".$bar->ygsakit;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $pasien = $baru->hubungankeluarga;
            }
            if ('' == $pasien) {
                $pasien = 'AsIs';
            }

            $tab .= "<tr class=rowcontent>\r\n            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>\r\n            <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->loktug."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->jlhbayar, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanperusahaan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebankaryawan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanjamsostek, 0, '.', ',')."</td>     \r\n            <td>".$bar->ketdiag."</td>\r\n            <td>".$bar->keterangan."</td>\r\n        </tr>";
            $totKlaim += $bar->totalklaim;
            $totJlhByr += $bar->jlhbayar;
            $totBbnprshn += $bar->bebanperusahaan;
            $totBbnKary += $bar->bebankaryawan;
            $totBbnJam += $bar->bebanjamsostek;
        }
        $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($totKlaim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totJlhByr, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnprshn, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnKary, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnJam, 0).'</td>';
        $tab .= '<td colspan=2>&nbsp</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'excelgetDetail3':
        $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd." class=sortable><thead>\r\n        <tr class=rowheader>\r\n        \r\n        <td  ".$bgcolor.">No</td>\r\n        <td width=100  ".$bgcolor.'>'.$_SESSION['lang']['notransaksi']."</td>\r\n        <td width=50  ".$bgcolor.'>'.$_SESSION['lang']['periode']."</td>\r\n        <td width=30  ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['lokasitugas']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['namakaryawan']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['jabatan']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150 ".$bgcolor.'>'.$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150 ".$bgcolor.'>'.$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=50 ".$bgcolor.'>'.$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['dibayar']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['perusahaan']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['karyawan']."</td>\r\n        <td width=90 ".$bgcolor.">Jamsostek</td>      \r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['diagnosa']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead><tbody>";
        $str = "select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama \r\n              from ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".sdm_5rs b on a.rs=b.id \r\n              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid \r\n              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id \r\n              left join ".$dbname.".sdm_karyawankeluarga f\r\n              on a.ygsakit=f.nomor\r\n              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'\r\n              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'\r\n              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
        $res = mysql_query($str) || mysql_error($conn);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pasien = '';
            $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n              where nomor=".$bar->ygsakit;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $pasien = $baru->hubungankeluarga;
            }
            if ('' == $pasien) {
                $pasien = 'AsIs';
            }

            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>\r\n            <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->loktug."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->jlhbayar, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanperusahaan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebankaryawan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanjamsostek, 0, '.', ',')."</td>     \r\n            <td>".$bar->ketdiag."</td>\r\n            <td>".$bar->keterangan."</td>\r\n        </tr>";
            $totKlaim += $bar->totalklaim;
            $totJlhByr += $bar->jlhbayar;
            $totBbnprshn += $bar->bebanperusahaan;
            $totBbnKary += $bar->bebankaryawan;
            $totBbnJam += $bar->bebanjamsostek;
        }
        $tab .= '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($totKlaim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totJlhByr, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnprshn, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnKary, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnJam, 0).'</td>';
        $tab .= '<td colspan=2>&nbsp</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'detailPengobatan2_'.$param['ptId2'];
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'detailStaff':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= " and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5\r\n                  group by kodebiaya,periode,b.bagian order by b.tipekaryawan";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['bagian'].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
            $dtBagian[$rstaff['bagian']] = $rstaff['bagian'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>';
        $tab .= '<tr>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['bagian'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</thead><tbody>';
        foreach ($dtBagian as $lstRegional) {
            $tab .= '<tr class=rowcontent>';
            $tab .= "<td style='cursor:pointer;' onclick=getDetDept('".$lstRegional."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."','".$param['byPeng']."')>".$lstRegional.'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                        $linkDet = "title='detail data ".$lstRegional.','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getDetailDept('".$lstRegional."','".$lstBy."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')";
                    }

                    $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                    $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTot[$lstRegional] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTotPrd[$lstBy.$prd] += $dtby[$lstRegional.$lstBy.$prd];
                }
                $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
            }
            $tab .= '<td  align=right>'.number_format($grTot[$lstRegional], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td  align=right>'.number_format($grTotPrd[$lstBy.$prd], 0).'</td>';
                $totDtAl += $grTotPrd[$lstBy.$prd];
                $totDtper[$lstBy] += $grTotPrd[$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($totDtper[$lstBy], 0).'</td>';
        }
        $tab .= '<td  align=right>'.number_format($totDtAl, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelStaff(event,'sdm_slave_2biayapengobatan1.php','0','".$param['thn']."','".$param['smstr']."','".$param['ptId2']."','".$param['unitId2']."','".$param['regional']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(1)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'excelStaffDet':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= " and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5\r\n                  group by kodebiaya,periode,b.bagian order by b.tipekaryawan";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['bagian'].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
            $dtBagian[$rstaff['bagian']] = $rstaff['bagian'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border='.$brd.' class=sortable><thead>';
        $tab .= '<tr '.$bgcolor.'>';
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['bagian'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr><tr '.$bgcolor.'>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</thead><tbody>';
        foreach ($dtBagian as $lstRegional) {
            $tab .= '<tr class=rowcontent>';
            $tab .= "<td style='cursor:pointer;' onclick=getDetDept('".$lstRegional."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."','".$param['byPeng']."')>".$lstRegional.'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                        $linkDet = "title='detail data ".$lstRegional.','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getDetailDept('".$lstRegional."','".$lstBy."')";
                    }

                    $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                    $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTot[$lstRegional] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTotPrd[$lstBy.$prd] += $dtby[$lstRegional.$lstBy.$prd];
                }
                $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
            }
            $tab .= '<td  align=right>'.number_format($grTot[$lstRegional], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td  align=right>'.number_format($grTotPrd[$lstBy.$prd], 0).'</td>';
                $totDtAl += $grTotPrd[$lstBy.$prd];
                $totDtper[$lstBy] += $grTotPrd[$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($totDtper[$lstBy], 0).'</td>';
        }
        $tab .= '<td  align=right>'.number_format($totDtAl, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'detailPengobatan2Staff_'.$param['ptId2'];
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getDetDept':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= " and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5 and b.bagian='".$param['bagian']."'\r\n                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']] = $rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']] = $rstaff['karyawanid'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><tr '.$bgcolor.'><td rowspan=2>'.$_SESSION['lang']['namakaryawan'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr><tr '.$bgcolor.'>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</thead><tbody>';
        foreach ($dtBagian as $lstRegional) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$optNmKary[$lstRegional].'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                        $linkDet = "title='detail data ".$optNmKary[$lstRegional].','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                    }

                    $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                    $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTot[$lstRegional] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTotPrd[$lstBy.$prd] += $dtby[$lstRegional.$lstBy.$prd];
                }
                $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
            }
            $tab .= '<td  align=right>'.number_format($grTot[$lstRegional], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td  align=right>'.number_format($grTotPrd[$lstBy.$prd], 0).'</td>';
                $totDtAl += $grTotPrd[$lstBy.$prd];
                $totDtper[$lstBy] += $grTotPrd[$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($totDtper[$lstBy], 0).'</td>';
        }
        $tab .= '<td  align=right>'.number_format($totDtAl, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelStaff2(event,'sdm_slave_2biayapengobatan1.php','".$param['bagian']."','','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'getDetail3':
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>\r\n        <tr class=rowheader>\r\n        <td width=50></td>\r\n        <td>No</td>\r\n        <td width=100>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td width=50>".$_SESSION['lang']['periode']."</td>\r\n        <td width=30>".$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200>".$_SESSION['lang']['lokasitugas']."</td>\r\n        <td width=200>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td width=200>".$_SESSION['lang']['jabatan']."</td>\r\n        <td>".$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150>".$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td>".$_SESSION['lang']['dibayar']."</td>\r\n        <td width=90>".$_SESSION['lang']['perusahaan']."</td>\r\n        <td width=90>".$_SESSION['lang']['karyawan']."</td>\r\n        <td width=90>Jamsostek</td>      \r\n        <td>".$_SESSION['lang']['diagnosa']."</td>\r\n        <td>".$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead><tbody>";
        $str = "select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama \r\n              from ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".sdm_5rs b on a.rs=b.id \r\n              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid \r\n              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id \r\n              left join ".$dbname.".sdm_karyawankeluarga f\r\n              on a.ygsakit=f.nomor\r\n              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'\r\n              and a.kodeorg = '".$param['unitId2']."' and c.tipekaryawan='".$param['tipeKary']."'\r\n              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
        $res = mysql_query($str) || mysql_error($conn);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pasien = '';
            $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n              where nomor=".$bar->ygsakit;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $pasien = $baru->hubungankeluarga;
            }
            if ('' == $pasien) {
                $pasien = 'AsIs';
            }

            $tab .= "<tr class=rowcontent>\r\n            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>\r\n            <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->loktug."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->jlhbayar, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanperusahaan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebankaryawan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanjamsostek, 0, '.', ',')."</td>     \r\n            <td>".$bar->ketdiag."</td>\r\n            <td>".$bar->keterangan."</td>\r\n        </tr>";
            $totKlaim += $bar->totalklaim;
            $totJlhByr += $bar->jlhbayar;
            $totBbnprshn += $bar->bebanperusahaan;
            $totBbnKary += $bar->bebankaryawan;
            $totBbnJam += $bar->bebanjamsostek;
        }
        $tab .= '<tr class=rowcontent><td colspan=12>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($totKlaim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totJlhByr, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnprshn, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnKary, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnJam, 0).'</td>';
        $tab .= '<td colspan=2>&nbsp</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'detailKarywan':
        $tab .= "<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead>\r\n        <tr class=rowheader>\r\n        \r\n        <td  ".$bgcolor.">No</td>\r\n        <td width=100  ".$bgcolor.'>'.$_SESSION['lang']['notransaksi']."</td>\r\n        <td width=50  ".$bgcolor.'>'.$_SESSION['lang']['periode']."</td>\r\n        <td width=30  ".$bgcolor.'>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['lokasitugas']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['namakaryawan']."</td>\r\n        <td width=200 ".$bgcolor.'>'.$_SESSION['lang']['jabatan']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150 ".$bgcolor.'>'.$_SESSION['lang']['nama'].' '.$_SESSION['lang']['pasien']."</td>\r\n        <td width=150 ".$bgcolor.'>'.$_SESSION['lang']['rumahsakit']."</td>\r\n        <td width=50 ".$bgcolor.'>'.$_SESSION['lang']['jenisbiayapengobatan']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['nilaiklaim']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['dibayar']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['perusahaan']."</td>\r\n        <td width=90 ".$bgcolor.'>'.$_SESSION['lang']['karyawan']."</td>\r\n        <td width=90 ".$bgcolor.">Jamsostek</td>      \r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['diagnosa']."</td>\r\n        <td ".$bgcolor.'>'.$_SESSION['lang']['keterangan']."</td>\r\n    </tr>\r\n    </thead><tbody>";
        $str = "select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama \r\n              from ".$dbname.'.sdm_pengobatanht a left join '.$dbname.".sdm_5rs b on a.rs=b.id \r\n              left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid \r\n              left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id \r\n              left join ".$dbname.".sdm_karyawankeluarga f\r\n              on a.ygsakit=f.nomor\r\n              where a.periode like '".$param['periode']."%' and a.kodebiaya='".$param['byPeng']."'\r\n              and a.karyawanid='".$param['karyId']."'\r\n              order by a.jlhbayar desc,a.updatetime desc, a.tanggal desc";
        $res = mysql_query($str) || mysql_error($conn);
        $no = 0;
        while ($bar = mysql_fetch_object($res)) {
            ++$no;
            $pasien = '';
            $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n              where nomor=".$bar->ygsakit;
            $resu = mysql_query($stru);
            while ($baru = mysql_fetch_object($resu)) {
                $pasien = $baru->hubungankeluarga;
            }
            if ('' == $pasien) {
                $pasien = 'AsIs';
            }

            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>\r\n            <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->loktug."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->jlhbayar, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanperusahaan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebankaryawan, 0, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanjamsostek, 0, '.', ',')."</td>     \r\n            <td>".$bar->ketdiag."</td>\r\n            <td>".$bar->keterangan."</td>\r\n        </tr>";
            $totKlaim += $bar->totalklaim;
            $totJlhByr += $bar->jlhbayar;
            $totBbnprshn += $bar->bebanperusahaan;
            $totBbnKary += $bar->bebankaryawan;
            $totBbnJam += $bar->bebanjamsostek;
        }
        $tab .= '<tr class=rowcontent><td colspan=11>'.$_SESSION['lang']['total'].'</td>';
        $tab .= '<td align=right>'.number_format($totKlaim, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totJlhByr, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnprshn, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnKary, 0).'</td>';
        $tab .= '<td align=right>'.number_format($totBbnJam, 0).'</td>';
        $tab .= '<td colspan=2>&nbsp</td></tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelDt2(event,'sdm_slave_2biayapengobatan.php','".$param['unitId2']."','".$param['tipeKary']."','".$param['periode']."','".$param['byPeng']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(3)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
    case 'excelStaffDet2':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        if ('' != $param['byPeng']) {
            $whr .= " and kodebiaya='".$param['byPeng']."'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5 and b.bagian='".$param['bagian']."'\r\n                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']] = $rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']] = $rstaff['karyawanid'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=1 class=sortable>';
        $tab .= '<thead><td rowspan=2 '.$bgcolor.'>'.$_SESSION['lang']['namakaryawan'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr><tr '.$bgcolor.'>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</thead><tbody>';
        foreach ($dtBagian as $lstRegional) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$optNmKary[$lstRegional].'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                        $linkDet = "title='detail data ".$optNmKary[$lstRegional].','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                    }

                    $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                    $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTot[$lstRegional] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTotPrd[$lstBy.$prd] += $dtby[$lstRegional.$lstBy.$prd];
                }
                $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
            }
            $tab .= '<td  align=right>'.number_format($grTot[$lstRegional], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td  align=right>'.number_format($grTotPrd[$lstBy.$prd], 0).'</td>';
                $totDtAl += $grTotPrd[$lstBy.$prd];
                $totDtper[$lstBy] += $grTotPrd[$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($totDtper[$lstBy], 0).'</td>';
        }
        $tab .= '<td  align=right>'.number_format($totDtAl, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'detailPengobatan2Staff2_'.$param['ptId2'];
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
                echo "<script language=javascript1.2>\r\n                parent.window.alert('Can't convert to excel format');\r\n                </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n                window.location='tempExcel/".$nop_.".xls';\r\n                </script>";
            closedir($handle);
        }

        break;
    case 'getDetailDeptBy':
        $whr = '';
        $ert = 1;
        if ('' != $param['ptId2']) {
            $whertd .= "and induk='".$param['ptId2']."'";
        }

        $isir = 'kodeorganisasi in (select distinct kodeunit from '.$dbname.".bgt_regional_assignment \r\n             where regional='".$param['regional']."')";
        if ('' != $param['unitId2']) {
            $isir = '';
            $isir = "kodeorganisasi='".$param['unitId2']."'";
        }

        $whr = ' and a.kodeorg in (';
        $sreg = 'select distinct kodeorganisasi from '.$dbname.'.organisasi where '.$isir.' '.$whertd."\r\n              order by namaorganisasi asc";
        $qreg = mysql_query($sreg);
        while ($rreg = mysql_fetch_assoc($qreg)) {
            if (1 == $ert) {
                $whr .= "'".$rreg['kodeorganisasi']."'";
            } else {
                $whr .= ",'".$rreg['kodeorganisasi']."'";
            }

            ++$ert;
        }
        $whr .= ')';
        if ('I' == $param['smstr']) {
            $whr .= " and left(periode,7) between '".$param['thn']."-01' and '".$param['thn']."-06'";
        } else {
            $whr .= " and left(periode,7) between '".$param['thn']."-07' and '".$param['thn']."-12'";
        }

        $sstaff = "select distinct sum(jlhbayar) as jmlhdbyr,periode,b.bagian,kodebiaya,a.karyawanid,b.lokasitugas from \r\n                 ".$dbname.'.sdm_pengobatanht  a left join '.$dbname.".datakaryawan b on a.karyawanid=b.karyawanid\r\n                  where jlhbayar!=0 ".$whr." and b.tipekaryawan=5 and b.bagian='".$param['bagian']."' and kodebiaya='".$param['byPeng']."'\r\n                  group by a.karyawanid,periode  order by b.namakaryawan asc";
        $qstaff = mysql_query($sstaff);
        while ($rstaff = mysql_fetch_assoc($qstaff)) {
            $dtby[$rstaff['karyawanid'].$rstaff['kodebiaya'].$rstaff['periode']] += $rstaff['jmlhdbyr'];
            $dtTgs[$rstaff['karyawanid']] = $rstaff['lokasitugas'];
            $kdBy[$rstaff['kodebiaya']] = $rstaff['kodebiaya'];
            $dtBagian[$rstaff['karyawanid']] = $rstaff['karyawanid'];
        }
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
        $tab .= '<thead><td rowspan=2>'.$_SESSION['lang']['namakaryawan'].'</td>';
        foreach ($kdBy as $lstBy) {
            $tab .= '<td colspan=7 align=center colspan=7>'.$optNmBy[$lstBy].'</td>';
        }
        $tab .= '<td rowspan=2>'.$_SESSION['lang']['grnd_total'].'</td></tr><tr '.$bgcolor.'>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $tab .= '<td  align=center>'.$dftrbln.'</td>';
            }
            $tab .= '<td  align=center>'.$_SESSION['lang']['total'].'</td>';
        }
        $tab .= '</thead><tbody>';
        foreach ($dtBagian as $lstRegional) {
            $tab .= '<tr class=rowcontent>';
            $tab .= '<td>'.$optNmKary[$lstRegional].'</td>';
            foreach ($kdBy as $lstBy) {
                foreach ($arrbln as $lstBln => $dftrbln) {
                    $prd = $param['thn'].'-'.$lstBln;
                    if (0 != $dtby[$lstRegional.$lstBy.$prd]) {
                        $linkDet = "title='detail data ".$optNmKary[$lstRegional].','.$lstBy.','.$prd."' style='cursor:pointer;' onclick=getKaryDept('".$lstRegional."','".$lstBy."','".$dtTgs[$lstRegional]."','".$prd."')";
                    }

                    $tab .= '<td  align=right '.$linkDet.'>'.number_format($dtby[$lstRegional.$lstBy.$prd], 0).'</td>';
                    $subTot[$lstRegional.$lstBy] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTot[$lstRegional] += $dtby[$lstRegional.$lstBy.$prd];
                    $grTotPrd[$lstBy.$prd] += $dtby[$lstRegional.$lstBy.$prd];
                }
                $tab .= '<td  align=right>'.number_format($subTot[$lstRegional.$lstBy], 0).'</td>';
            }
            $tab .= '<td  align=right>'.number_format($grTot[$lstRegional], 0).'</td>';
            $tab .= '</tr>';
        }
        $tab .= '<tr class=rowcontent>';
        $tab .= '<td>'.$_SESSION['lang']['total'].'</td>';
        foreach ($kdBy as $lstBy) {
            foreach ($arrbln as $lstBln => $dftrbln) {
                $prd = $param['thn'].'-'.$lstBln;
                $tab .= '<td  align=right>'.number_format($grTotPrd[$lstBy.$prd], 0).'</td>';
                $totDtAl += $grTotPrd[$lstBy.$prd];
                $totDtper[$lstBy] += $grTotPrd[$lstBy.$prd];
            }
            $tab .= '<td  align=right>'.number_format($totDtper[$lstBy], 0).'</td>';
        }
        $tab .= '<td  align=right>'.number_format($totDtAl, 0).'</td>';
        $tab .= '</tr>';
        $tab .= '</tbody></table>';
        $tab .= "<button class=mybutton onclick=zExcelStaff2(event,'sdm_slave_2biayapengobatan1.php','".$param['bagian']."','".$param['byPeng']."','".$param['regional']."','".$param['ptId2']."','".$param['unitId2']."','".$param['thn']."','".$param['smstr']."')>".$_SESSION['lang']['excel']."</button>\r\n               <button class=mybutton onclick=kembali(2)>".$_SESSION['lang']['back'].'</button>';
        echo $tab;

        break;
}

?>