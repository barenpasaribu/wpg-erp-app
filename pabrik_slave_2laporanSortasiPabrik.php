<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
} else {
    $proses = $_GET['proses'];
}

$kdPbrk = ('' === $_POST['kdPbrk'] ? $_GET['kdPbrk'] : $_POST['kdPbrk']);
$statBuah = ('' === $_POST['statBuah'] ? $_GET['statBuah'] : $_POST['statBuah']);
if (isset($_POST['tglAkhir'])) {
    $tglAkhir = tanggalsystem($_POST['tglAkhir']);
} else {
    $tglAkhir = tanggalsystem($_GET['tglAkhir']);
}

if (isset($_POST['tglAwal'])) {
    $tglAwal = tanggalsystem($_POST['tglAwal']);
} else {
    $tglAwal = tanggalsystem($_GET['tglAwal']);
}

if ('excel' === $proses) {
    $border = 'border=1';
} else {
    $border = 'border=0';
}

('' === $_POST['suppId'] ? ($suppId = $_GET['suppId']) : ($suppId = $_POST['suppId']));
('' === $_POST['kdOrg'] ? ($kdOrg = $_GET['kdOrg']) : ($kdOrg = $_POST['kdOrg']));
$intextId = ('' === $_POST['intextId'] ? $_GET['intextId'] : $_POST['intextId']);
$BuahStat = ('' === $_POST['BuahStat'] ? $_GET['BuahStat'] : $_POST['BuahStat']);
$sFr = 'select * from '.$dbname.'.pabrik_5fraksi order by kode asc';
$qFr = mysql_query($sFr);
$rNm = mysql_num_rows($qFr);
while ($rFraksi = mysql_fetch_assoc($qFr)) {
    if ('EN' === $_SESSION['language']) {
        $zz = $rFraksi['keterangan1'];
    } else {
        $zz = $rFraksi['keterangan'];
    }

    $kodeFraksi[] = $rFraksi['kode'];
    $nmKeterangan[$rFraksi['kode']] = $zz;
}
if ('' !== $suppId) {
    $str = 'select namasupplier from '.$dbname.".log_5supplier where kodetimbangan='".$suppId."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $namaspl = $_SESSION['lang']['namasupplier'].':'.$bar->namasupplier;
    }
} else {
    if ('' !== $kdOrg) {
        $str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$kdOrg."'";
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $namaspl = $_SESSION['lang']['unit'].':'.$bar->namaorganisasi;
        }
    } else {
        $namaspl = $_SESSION['lang']['dari'].':'.$_SESSION['lang']['all'];
    }
}

$thn = substr($tglAwal, 0, 4);
$bln = substr($tglAwal, 4, 2);
$dte = substr($tglAwal, 6, 2);
$tglAwal1 = $thn.'-'.$bln.'-'.$dte;
$thn2 = substr($tglAkhir, 0, 4);
$bln2 = substr($tglAkhir, 4, 2);
$dte2 = substr($tglAkhir, 6, 2);
$tglAkhir1 = $thn2.'-'.$bln2.'-'.$dte2;
$stream .= '<div style=overflow:auto; height:650px;>';
$stream .= 'Mill FFB Grading Report '.$kdPbrk.'  '.$namaspl.' period :'.$tglAwal.'-'.$tglAkhir.'';
$colspand = count($kodeFraksi);
$stream .= '<table cellpadding=1 cellspacing=1 '.$border.' class=sortable width=100%>';
$stream .= '<thead><tr class=rowheader>';
$stream .= '<td rowspan=3>No.</td>';
$stream .= '<td rowspan=3>'.$_SESSION['lang']['nospb'].'</td>';
$stream .= '<td rowspan=3>'.$_SESSION['lang']['noTiket'].'</td>';
$stream .= '<td rowspan=3>'.$_SESSION['lang']['tanggal'].'</td>';
$stream .= '<td rowspan=3>'.str_replace(' ', '<br>', $_SESSION['lang']['nopol']).'</td>';
$stream .= '<td align=center  colspan=3 valign=middle>'.$_SESSION['lang']['hslTimbangan'].'</td>';
$stream .= '<td rowspan=3>'.str_replace(' ', '<br>', $_SESSION['lang']['jmlhTandan']).'</td>';
$stream .= '<td align=center rowspan=3 valign=middle>'.$_SESSION['lang']['bjr'].'</td>';
$stream .= '<td align=center rowspan=3 valign=middle>'.$_SESSION['lang']['sortasi'].'(JJG)</td>';
$stream .= '<td align=center rowspan=3 valign=middle>'.$_SESSION['lang']['bjr'].' '.$_SESSION['lang']['sortasi'].'</td>';
$stream .= '<td align=center  valign=middle colspan='.$colspand.'>Sortasi(JJg)</td>';
$stream .= '<td align=center rowspan=3 valign=middle></td></tr>';
$stream .= "<tr><td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratMasuk']."</td>
<td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratkosong']."</td>
<td align=center rowspan=2  valign=middle>".$_SESSION['lang']['beratnormal'].'</td>';
foreach ($kodeFraksi as $barisFraksi => $rFr) {
    $stream .= '<td align=center rowspan=2 >'.$rFr.'</td>';
}
$stream .= '</tr>';
$stream .= '</thead><tr></tr><tbody>';
if ($kdPbrk!=""  && $statBuah!='5') {
    if ($statBuah==0) {
        if ($suppId!="") {
            $add = " and kodecustomer='".$suppId."'";
        }
    } else {
        if ($statBuah < 0 && $kdOrg!="") {
            $add = " and kodeorg='".$kdOrg."'";
        }
    }

    $where = " substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."' and intex='".$statBuah."'  ".$add.'';
} else {
    if ($kdPbrk!="" && $statBuah=='5') {
        $where = " substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and millcode='".$kdPbrk."'";
    } else {
        if ($kdPbrk=="" && $statBuah!="5") {
            if ($statBuah=="0") {
                if ($suppId!="") {
                    $add = " and kodecustomer='".$suppId."'";
                }
            } else {
                if ($statBuah < 1 && $kdOrg!="") {
                    $add = " and kodeorg='".$kdOrg."'";
                }
            }

            $where = " substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."' and intex='".$statBuah."'   ".$add.'';
        } else {
            if ($kdPbrk=="" && $statBuah=="5") {
                $where = "substr(tanggal,1,10) between '".$tglAwal1."' and '".$tglAkhir1."'";
            }
        }
    }
}

$sMax = 'select notiket,kodefraksi,jumlah from '.$dbname.'.pabrik_sortasi_vw where '.$where.' order by kodefraksi asc';
$qMax = fetchData($sMax);
foreach ($qMax as $brsMax => $rMax) {
    $jmlhFraksi[$rMax['notiket']][$rMax['kodefraksi']] = $rMax['jumlah'];
}
$sql = "select notransaksi,tanggal,nokendaraan,beratmasuk,beratkeluar,beratbersih,nospb,`jumlahtandan1` , `jumlahtandan2` , `jumlahtandan3`,a.jjgsortasi,a.persenBrondolan,a.kgpotsortasi\r\n            from ".$dbname.'.pabrik_timbangan a left join '.$dbname.'.pabrik_sortasi b on a.notransaksi=b.notiket where '.$where." and kodebarang='40000003' group by notransaksi,notiket  order by `tanggal` asc ";
$query = mysql_query($sql);
$row = mysql_num_rows($query);
if (0 < $row) {
    while ($res = mysql_fetch_assoc($query)) {
        $jmlhTndn = $res['jumlahtandan1'] + $res['jumlahtandan2'] + $res['jumlahtandan3'];
        if (0 !== $jmlhTndn || 0 !== $res['jjgsortasi']) {
            $jBrt = $res['beratbersih'] / $res['jjgsortasi'];
            $jBrt2 = $res['beratbersih'] / $jmlhTndn;
        } else {
            $jBrt = 0;
            $jBrt2 = 0;
        }

        $subTotal['beratmasuk'] += $res['beratmasuk'];
        $subTotal['beratkeluar'] += $res['beratkeluar'];
        $subTotal['beratbersih'] += $res['beratbersih'];
        $subTotal['jjgSortasitot'] += $res['jjgsortasi'];
        $subTotal['prsnBrondolan'] += $res['persenBrondolan'];
        $subTotal['jmlhTndn'] += $jmlhTndn;
        $subTotal['kgpotsortasi'] += $res['kgpotsortasi'];
        ++$no;
        $stream .= "<tr class=rowcontent>\r\n                                    <td>".$no."</td>\r\n                                    <td>".$res['nospb']."</td>\r\n                                    <td>".$res['notransaksi']."</td>\r\n                                    <td>".tanggalnormal($res['tanggal'])."</td>\t\t\t\t \r\n                                    <td>".$res['nokendaraan']."</td>\t\t\t \t\t\r\n                                    <td align=right>".number_format($res['beratmasuk'], 2)."</td>\r\n                                    <td align=right>".number_format($res['beratkeluar'], 2)."</td>\r\n                                    <td align=right>".number_format($res['beratbersih'], 2)."</td>\r\n                                    <td align=right>".number_format($jmlhTndn, 0)."</td>\r\n                                    <td align=right>".number_format($jBrt, 2)."</td>\r\n                                    <td align=right>".number_format($res['jjgsortasi'], 0)."</td>\r\n                                    <td align=right>".number_format($jBrt2, 2).'</td>';
        foreach ($kodeFraksi as $brsKdFraksi => $listFraksi) {
            $stream .= '<td width=60 align=right>'.number_format($jmlhFraksi[$res['notransaksi']][$listFraksi], 2).'</td>';
            $subTotal[$listFraksi] += $jmlhFraksi[$res['notransaksi']][$listFraksi];
            ++$j;
        }
        $stream .= '<td align=right></td>';
        $stream .= "\t\r\n                            </tr>\r\n                            ";
    }
    $stream .= '<tr class=rowcontent><td colspan=5>'.$_SESSION['lang']['total']."</td>\r\n                    <td align=right>".number_format($subTotal['beratmasuk'], 2)."</td>\r\n                    <td align=right>".number_format($subTotal['beratkeluar'], 2)."</td>\r\n                    <td align=right>".number_format($subTotal['beratbersih'], 2)."</td>\r\n                    <td align=right>".number_format($subTotal['jmlhTndn'], 2)."</td>\r\n                    <td align=right>&nbsp;</td>\r\n                    <td align=right>".number_format($subTotal['jjgSortasitot'], 2)."</td>\r\n                    <td align=right>&nbsp;</td>\r\n                        ";
    $sFraksi = 'select kode from '.$dbname.'.pabrik_5fraksi order by kode asc';
    $qFraksi = mysql_query($sFraksi);
    while ($rFraksi = mysql_fetch_assoc($qFraksi)) {
        $stream .= '<td align=right>'.number_format($subTotal[$rFraksi['kode']], 2).'</td>';
        $subTotal[$rFraksi['kode']] = 0;
    }
    $stream .= '<td align=right></td>';
    $stream .= '</tr>';
    $subTotal['beratmasuk'] = 0;
    $subTotal['beratkeluar'] = 0;
    $subTotal['beratbersih'] = 0;
    $subTotal['jmlhTndn'] = 0;
    $subTotal['jjgSortasitot'] = 0;
    $subTotal['prsnBrondolan'] = 0;
    $subTotal['kgpotsortasi'] = 0;
} else {
    $stream .= '<tr class=rowcontent><td colspan=23 align=center>Not Found</td></tr>';
}

$stream .= '</tbody></table><div>';
switch ($proses) {
    case 'preview':
        if ('' === $tglAkhir || '' === $tglAwal) {
            echo 'warning:Date required';
            exit();
        }

        echo $stream;

        break;
    case 'excel':
        if ('' === $tglAkhir || '' === $tglAwal) {
            echo 'warning:Date required';
            exit();
        }

        $stream .= 'Print Time:'.date('YmdHis').'<br>By:'.$_SESSION['empl']['name'];
        $tglSkrg = date('Ymd');
        $nop_ = 'rekapSortasiBuah_'.$tglSkrg;
        if (0 < strlen($stream)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
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
    case 'getDetail':
        echo '<link rel=stylesheet type=text/css href=style/generic.css>';
        $nokontrak = $_GET['nokontrak'];
        $sHed = 'select  a.tanggalkontrak,a.koderekanan,a.kodebarang from '.$dbname.".pmn_kontrakjual a where a.nokontrak='".$nokontrak."'";
        $qHead = mysql_query($sHed);
        $rHead = mysql_fetch_assoc($qHead);
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$rHead['kodebarang']."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $sCust = 'select namacustomer  from '.$dbname.".pmn_4customer where kodecustomer='".$rHead['koderekanan']."'";
        $qCust = mysql_query($sCust);
        $rCust = mysql_fetch_assoc($qCust);
        echo '<fieldset><legend>'.$_SESSION['lang']['detailPengiriman']."</legend>\r\n        <table cellspacing=1 border=0 class=myinputtext>\r\n        <tr>\r\n                <td>".$_SESSION['lang']['NoKontrak'].'</td><td>:</td><td>'.$nokontrak."</td>\r\n        </tr>\r\n        <tr>\r\n                <td>".$_SESSION['lang']['tglKontrak'].'</td><td>:</td><td>'.tanggalnormal($rHead['tanggalkontrak'])."</td>\r\n        </tr>\r\n        <tr>\r\n                <td>".$_SESSION['lang']['komoditi'].'</td><td>:</td><td>'.$rBrg['namabarang']."</td>\r\n        </tr>\r\n        <tr>\r\n                <td>".$_SESSION['lang']['Pembeli'].'</td><td>:</td><td>'.$rCust['namacustomer']."</td>\r\n        </tr>\r\n        </table><br />\r\n        <table cellspacing=1 border=0 class=sortable><thead>\r\n        <tr class=data>\r\n        <td>".$_SESSION['lang']['notransaksi']."</td>\r\n        <td>".$_SESSION['lang']['tanggal']."</td>\r\n        <td>".$_SESSION['lang']['nodo']."</td>\r\n        <td>".$_SESSION['lang']['nosipb']."</td>\r\n        <td>".$_SESSION['lang']['beratnormal']."</td>\r\n        <td>".$_SESSION['lang']['kodenopol']."</td>\r\n        <td>".$_SESSION['lang']['sopir']."</td>\r\n        </tr></thead><tbody>\r\n        ";
        $sDet = 'select notransaksi,tanggal,nodo,nosipb,beratbersih,nokendaraan,supir from '.$dbname.".pabrik_timbangan where nokontrak='".$nokontrak."'";
        $qDet = mysql_query($sDet);
        $rCek = mysql_num_rows($qDet);
        if (0 < $rCek) {
            while ($rDet = mysql_fetch_assoc($qDet)) {
                echo "<tr class=rowcontent>\r\n                        <td>".$rDet['notransaksi']."</td>\r\n                        <td>".tanggalnormal($rDet['tanggal'])."</td>\r\n                        <td>".$rDet['nodo']."</td>\r\n                        <td>".$rDet['nosipb']."</td>\r\n                        <td align=right>".number_format($rDet['beratbersih'], 2)."</td>\r\n                        <td>".$rDet['nokendaraan']."</td>\r\n                        <td>".ucfirst($rDet['supir'])."</td>\r\n                        </tr>";
            }
        } else {
            echo '<tr><td colspan=7>Not Found</td></tr>';
        }

        echo '</tbody></table></fieldset>';

        break;
    case 'getkbn':
        if ('' === $kdPbrk) {
            exit('Error: Mill code required');
        }

        if (0 === $BuahStat) {
            $optkdOrg2 .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
            $sOrg = 'SELECT namasupplier,supplierid,kodetimbangan FROM '.$dbname.".log_5supplier WHERE substring(kodekelompok,1,4)='S003' and kodetimbangan is not null";
            $qOrg = mysql_query($sOrg);
            while ($rOrg = mysql_fetch_assoc($qOrg)) {
                $optkdOrg2 .= '<option value='.$rOrg['kodetimbangan'].''.(($rOrg['kodetimbangan'] === $idCust ? 'selected' : '')).'>'.$rOrg['namasupplier'].'</option>';
            }
            echo $optkdOrg2.'###'.$BuahStat;
            exit();
        }

        if (5 === $BuahStat) {
            $optkdOrg2 .= "<option value=''>".$_SESSION['lang']['all'].'</option>';
            echo $optkdOrg2.'###'.$BuahStat;
            exit();
        }

        if (1 === $BuahStat) {
            $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."' and millcode='".$kdPbrk."')";
        } else {
            if (2 === $BuahStat) {
                $sOrg = 'SELECT namaorganisasi,kodeorganisasi FROM '.$dbname.".organisasi WHERE tipe='KEBUN' and kodeorganisasi in(select distinct kodeorg from ".$dbname.".pabrik_timbangan where intex='".$BuahStat."'  and millcode='".$kdPbrk."')";
            }
        }

        $optkdOrg = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $qOrg = mysql_query($sOrg);
        while ($rOrg = mysql_fetch_assoc($qOrg)) {
            $optkdOrg .= '<option value='.$rOrg['kodeorganisasi'].''.(($rOrg['kodeorganisasi'] === $kdKbn ? 'selected' : '')).'>'.$rOrg['namaorganisasi'].'</option>';
        }
        echo $optkdOrg.'###'.$BuahStat;

        break;
}

?>