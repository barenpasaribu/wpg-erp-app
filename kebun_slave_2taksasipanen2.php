<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' === $_POST['kebun2'] ? ($kebun = $_GET['kebun2']) : ($kebun = $_POST['kebun2']));
('' === $_POST['periode2'] ? ($periode = $_GET['periode2']) : ($periode = $_POST['periode2']));
('' === $_POST['afdeling2'] ? ($afdeling = $_GET['afdeling2']) : ($afdeling = $_POST['afdeling2']));
if ('preview' === $proses || 'excel' === $proses) {
    if ('' === $periode || '' === $kebun) {
        exit('Error: All field required');
    }

    $brd = 0;
    if ('excel' === $proses) {
        $brd = 1;
        $bgcoloraja = 'bgcolor=#DEDEDE align=center';
    }

    $sPabrik = "select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,\r\n        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan \r\n        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'\r\n        and left(tanggal,7) like '".$periode."%' order by substr(nospb,9,6)";
    $respabrik = mysql_query($sPabrik);
    while ($bar0 = mysql_fetch_object($respabrik)) {
        $keyAfd[$bar0->afdeling] = $bar0->afdeling;
        $keyTgl[$bar0->tanggal] = $bar0->tanggal;
        $dzArr[$bar0->afdeling][$bar0->tanggal]['p_kg'] += $bar0->beratbersih;
        $dzArr[$kebun][$bar0->tanggal]['p_kg'] += $bar0->beratbersih;
    }
    $sTaksasi = 'select afdeling, tanggal, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from '.$dbname.".kebun_taksasi \r\n        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal like '".$periode."%'\r\n        ";
    $restaksasi = mysql_query($sTaksasi);
    while ($bar1 = mysql_fetch_object($restaksasi)) {
        $keyAfd[$bar1->afdeling] = $bar1->afdeling;
        $keyTgl[$bar1->tanggal] = $bar1->tanggal;
        ++$dzArr[$bar1->afdeling][$bar1->tanggal]['counter'];
        $dzArr[$bar1->afdeling][$bar1->tanggal]['afdeling'] = $bar1->afdeling;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['hasisa'] += $bar1->hasisa;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['haesok'] += $bar1->haesok;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['pbm'] += $bar1->persenbuahmatang;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['persenbuahmatang'] = $dzArr[$bar1->afdeling][$bar1->tanggal]['pbm'] / $dzArr[$bar1->afdeling][$bar1->tanggal]['counter'];
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['jjgoutput'] += $bar1->jjgoutput;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['kg'] += $bar1->kg;
        $dzArr[$bar1->afdeling][$bar1->tanggal]['bjr'] = $dzArr[$bar1->afdeling][$bar1->tanggal]['kg'] / $dzArr[$bar1->afdeling][$bar1->tanggal]['jjgmasak'];
        ++$dzArr[$kebun][$bar1->tanggal]['counter'];
        $dzArr[$kebun][$bar1->tanggal]['afdeling'] = $kebun;
        $dzArr[$kebun][$bar1->tanggal]['hasisa'] += $bar1->hasisa;
        $dzArr[$kebun][$bar1->tanggal]['haesok'] += $bar1->haesok;
        $dzArr[$kebun][$bar1->tanggal]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$kebun][$bar1->tanggal]['pbm'] += $bar1->persenbuahmatang;
        $dzArr[$kebun][$bar1->tanggal]['persenbuahmatang'] = $dzArr[$kebun][$bar1->tanggal]['pbm'] / $dzArr[$kebun][$bar1->tanggal]['counter'];
        $dzArr[$kebun][$bar1->tanggal]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$kebun][$bar1->tanggal]['jjgoutput'] += $bar1->jjgoutput;
        $dzArr[$kebun][$bar1->tanggal]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$kebun][$bar1->tanggal]['kg'] += $bar1->kg;
        $dzArr[$kebun][$bar1->tanggal]['bjr'] = $dzArr[$kebun][$bar1->tanggal]['kg'] / $dzArr[$kebun][$bar1->tanggal]['jjgmasak'];
    }
    sort($keyTgl);
    sort($keyAfd);
    if ('excel' !== $proses) {
    } else {
        $tab .= 'Laporan Taksasi<br>Kebun: '.$kebun.' '.$afdeling.' '.$periode.' ';
    }

    $tab .= "\r\n    <table width=100% cellspacing=1 border=".$brd." >\r\n    <thead>\r\n    <tr>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['tanggal']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['kebun']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['afdeling']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hasisa']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['haesok']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jumlahha']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jmlhpokok']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['persenbuahmatang']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjgmasak']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjgoutput']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hkdigunakan']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['taksasi']." (kg)</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['realisasi']." (kg)</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['varian']."</td>\r\n    </tr></thead><tbody>";
    if (!empty($keyTgl)) {
        foreach ($keyTgl as $tgl) {
            $jumlahha = $dzArr[$kebun][$tgl]['hasisa'] + $dzArr[$kebun][$tgl]['haesok'];
            $pbm = ($dzArr[$kebun][$tgl]['jjgmasak'] * 100) / $dzArr[$kebun][$tgl]['jmlhpokok'];
            $varian = 100 - ($dzArr[$kebun][$tgl]['p_kg'] - $dzArr[$kebun][$tgl]['kg']) / $dzArr[$kebun][$tgl]['p_kg'] * 100;
            $varian_k = 100 - ($dzArr_k[$kebun][$tgl]['p_kg'] - $dzArr_k[$kebun][$tgl]['kg']) / $dzArr_k[$kebun][$tgl]['p_kg'] * 100;
            if (0 === $dzArr[$kebun][$tgl]['kg']) {
                $varian = 0;
            }

            $tab .= "<tr class=rowcontent>\r\n        <td ".$bgcoloraja.'>'.$tgl."</td>\r\n        <td ".$bgcoloraja.'>'.$kebun."</td>\r\n        <td ".$bgcoloraja."></td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['hasisa'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['haesok'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($jumlahha, 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['jmlhpokok'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($pbm, 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['jjgmasak'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['jjgoutput'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['hkdigunakan'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['bjr'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['kg'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun][$tgl]['p_kg'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($varian, 2)."</td>\r\n        </tr>";
            if (!empty($keyAfd)) {
                foreach ($keyAfd as $afd) {
                    $jumlahha = $dzArr[$afd][$tgl]['hasisa'] + $dzArr[$afd][$tgl]['haesok'];
                    $pbm = ($dzArr[$afd][$tgl]['jjgmasak'] * 100) / $dzArr[$afd][$tgl]['jmlhpokok'];
                    $varian = 100 - ($dzArr[$afd][$tgl]['p_kg'] - $dzArr[$afd][$tgl]['kg']) / $dzArr[$afd][$tgl]['p_kg'] * 100;
                    $varian_k = 100 - ($dzArr_k[$afd][$tgl]['p_kg'] - $dzArr_k[$afd][$tgl]['kg']) / $dzArr_k[$afd][$tgl]['p_kg'] * 100;
                    if (0 === $dzArr[$afd][$tgl]['kg']) {
                        $varian = 0;
                    }

                    $tab .= "<tr class=rowcontent>\r\n        <td></td>\r\n        <td>".$kebun."</td>\r\n        <td>".$afd."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['hasisa'], 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['haesok'], 2)."</td>\r\n        <td align=right>".number_format($jumlahha, 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['jmlhpokok'])."</td>\r\n        <td align=right>".number_format($pbm, 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['jjgmasak'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['jjgoutput'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['hkdigunakan'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['bjr'], 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['kg'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd][$tgl]['p_kg'])."</td>\r\n        <td align=right>".number_format($varian, 2)."</td>\r\n        </tr>";
                }
            }
        }
    }

    $tab .= '</tbody></table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'taksasi_'.$kebun.'_'.$afdeling.'_'.$tanggal;
        if (0 < strlen($tab)) {
            if ($handle = opendir('tempExcel')) {
                while (false !== ($file = readdir($handle))) {
                    if ('.' !== $file && '..' !== $file) {
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
    case 'getAfdeling0':
        $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sPrd = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n               where induk = '".$kebun."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd = mysql_query($sPrd);
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optAfd .= '<option value='.$rPrd['kodeorganisasi'].'>'.$rPrd['namaorganisasi'].'</option>';
        }
        $sorg2 = 'select distinct karyawanid,namakaryawan from '.$dbname.".datakaryawan \r\n                where lokasitugas='".$kebun."' and tipekaryawan!='4' order by namakaryawan asc";
        $qorg2 = mysql_query($sorg2);
        while ($rorg2 = mysql_fetch_assoc($qorg2)) {
            if ('' !== $param['mandor']) {
                $optafd2 .= "<option value='".$rorg2['karyawanid']."' ".(($param['mandor'] === $rorg2['karyawanid'] ? 'selected' : '')).'>'.$rorg2['namakaryawan'].'</option>';
            } else {
                $optafd2 .= "<option value='".$rorg2['karyawanid']."'>".$rorg2['namakaryawan'].'</option>';
            }
        }
        echo $optAfd.'####'.$optafd2;

        break;
    case 'getAfdeling':
        $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sPrd = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n               where induk = '".$kebun."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd = mysql_query($sPrd)
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optAfd .= '<option value='.$rPrd['kodeorganisasi'].'>'.$rPrd['namaorganisasi'].'</option>';
        }
        echo $optAfd;

        break;
    default:
        break;
}

?>