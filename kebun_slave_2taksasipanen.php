<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' === $_POST['kebun'] ? ($kebun = $_GET['kebun']) : ($kebun = $_POST['kebun']));
('' === $_POST['tanggal'] ? ($tanggal = $_GET['tanggal']) : ($tanggal = $_POST['tanggal']));
('' === $_POST['afdeling'] ? ($afdeling = $_GET['afdeling']) : ($afdeling = $_POST['afdeling']));
if ('preview' === $proses || 'excel' === $proses) {
    if ('' === $tanggal || '' === $kebun) {
        exit('Error: All field required');
    }

    $tanggal = putertanggal($tanggal);
    $esok = putertanggal(date('Y-m-d', strtotime('+1 day', strtotime($tanggal))));
    $kemarin = putertanggal(date('Y-m-d', strtotime('-1 day', strtotime($tanggal))));
    $tanggalkemarin = putertanggal($kemarin);
    $brd = 0;
    if ('excel' === $proses) {
        $brd = 1;
        $bgcoloraja = 'bgcolor=#DEDEDE align=center';
    }

    $sPabrik = "select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,\r\n        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan \r\n        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'\r\n        and left(tanggal,10) like '".$tanggal."%' order by substr(nospb,9,6)";
    $respabrik = mysql_query($sPabrik);
    while ($bar0 = mysql_fetch_object($respabrik)) {
        $keyAfd[$bar0->afdeling] = $bar0->afdeling;
        $dzArr[$bar0->afdeling]['p_kg'] += $bar0->beratbersih;
        $dzArr[$kebun]['p_kg'] += $bar0->beratbersih;
    }
    $sTaksasi = 'select afdeling, tanggal, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from '.$dbname.".kebun_taksasi \r\n        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal = '".$tanggal."'\r\n        ";
    $restaksasi = mysql_query($sTaksasi);
    while ($bar1 = mysql_fetch_object($restaksasi)) {
        $keyAfd[$bar1->afdeling] = $bar1->afdeling;
        ++$dzArr[$bar1->afdeling]['counter'];
        $dzArr[$bar1->afdeling]['afdeling'] = $bar1->afdeling;
        $dzArr[$bar1->afdeling]['hasisa'] += $bar1->hasisa;
        $dzArr[$bar1->afdeling]['haesok'] += $bar1->haesok;
        $dzArr[$bar1->afdeling]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$bar1->afdeling]['pbm'] += $bar1->persenbuahmatang;
        $dzArr[$bar1->afdeling]['persenbuahmatang'] = $dzArr[$bar1->afdeling]['pbm'] / $dzArr[$bar1->afdeling]['counter'];
        $dzArr[$bar1->afdeling]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$bar1->afdeling]['jjgoutput'] += $bar1->jjgoutput;
        $dzArr[$bar1->afdeling]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$bar1->afdeling]['kg'] += $bar1->kg;
        $dzArr[$bar1->afdeling]['bjr'] = $dzArr[$bar1->afdeling]['kg'] / $dzArr[$bar1->afdeling]['jjgmasak'];
        ++$dzArr[$kebun]['counter'];
        $dzArr[$kebun]['afdeling'] = $kebun;
        $dzArr[$kebun]['hasisa'] += $bar1->hasisa;
        $dzArr[$kebun]['haesok'] += $bar1->haesok;
        $dzArr[$kebun]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$kebun]['pbm'] += $bar1->persenbuahmatang;
        $dzArr[$kebun]['persenbuahmatang'] = $dzArr[$kebun]['pbm'] / $dzArr[$kebun]['counter'];
        $dzArr[$kebun]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$kebun]['jjgoutput'] += $bar1->jjgoutput;
        $dzArr[$kebun]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$kebun]['kg'] += $bar1->kg;
        $dzArr[$kebun]['bjr'] = $dzArr[$kebun]['kg'] / $dzArr[$kebun]['jjgmasak'];
    }
    if ('excel' !== $proses) {
        $tab .= "\r\n        <table width=100% cellspacing=1 border=".$brd." >\r\n        <tr>\r\n            <td align=left><button onclick=pindahtanggal('".$kebun."','".$afd."','".$esok."') class=mybutton name=preview id=preview><- Esok/Tomorrow (".$esok.")</button></td>\r\n            <td>&nbsp;</td>\r\n            <td align=right><button onclick=pindahtanggal('".$kebun."','".$afd."','".$kemarin."') class=mybutton name=preview id=preview>(".$kemarin.") Kemarin/Yesterday -></button></td>\r\n        </tr>\r\n        </table>    \r\n        ";
    } else {
        $tab .= 'Laporan Taksasi<br>Kebun: '.$kebun.' '.$afdeling.' '.putertanggal($tanggal).' ';
    }

    $tab .= "\r\n    <table width=100% cellspacing=1 border=".$brd." >\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['kebun']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['afdeling']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hasisa']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['haesok']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jumlahha']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jmlhpokok']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['persenbuahmatang']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjgmasak']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjgoutput']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hkdigunakan']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['taksasi']." (kg)</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['realisasi']." (kg)</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['varian']."</td>\r\n    </tr>";
    $jumlahha = $dzArr[$kebun]['hasisa'] + $dzArr[$kebun]['haesok'];
    $pbm = ($dzArr[$kebun]['jjgmasak'] * 100) / $dzArr[$kebun]['jmlhpokok'];
    $varian = 100 - ($dzArr[$kebun]['p_kg'] - $dzArr[$kebun]['kg']) / $dzArr[$kebun]['p_kg'] * 100;
    $varian_k = 100 - ($dzArr_k[$kebun]['p_kg'] - $dzArr_k[$kebun]['kg']) / $dzArr_k[$kebun]['p_kg'] * 100;
    if (0 === $dzArr[$kebun]['kg']) {
        $varian = 0;
    }

    $tab .= "<tr class=rowcontent>\r\n        <td ".$bgcoloraja.'>'.$kebun."</td>\r\n        <td ".$bgcoloraja."></td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['hasisa'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['haesok'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($jumlahha, 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['jmlhpokok'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($pbm, 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['jjgmasak'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['jjgoutput'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['hkdigunakan'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['bjr'], 2)."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['kg'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($dzArr[$kebun]['p_kg'])."</td>\r\n        <td ".$bgcoloraja.' align=right>'.number_format($varian, 2)."</td>\r\n      </tr>";
    $tab .= "</thead>\r\n    <tbody>";
    if (!empty($keyAfd)) {
        foreach ($keyAfd as $afd) {
            $jumlahha = $dzArr[$afd]['hasisa'] + $dzArr[$afd]['haesok'];
            $pbm = ($dzArr[$afd]['jjgmasak'] * 100) / $dzArr[$afd]['jmlhpokok'];
            $varian = 100 - ($dzArr[$afd]['p_kg'] - $dzArr[$afd]['kg']) / $dzArr[$afd]['p_kg'] * 100;
            $varian_k = 100 - ($dzArr_k[$afd]['p_kg'] - $dzArr_k[$afd]['kg']) / $dzArr_k[$afd]['p_kg'] * 100;
            if (0 === $dzArr[$afd]['kg']) {
                $varian = 0;
            }

            $tab .= "<tr class=rowcontent>\r\n        <td>".$kebun."</td>\r\n        <td>".$afd."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['hasisa'], 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['haesok'], 2)."</td>\r\n        <td align=right>".number_format($jumlahha, 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['jmlhpokok'])."</td>\r\n        <td align=right>".number_format($pbm, 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['jjgmasak'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['jjgoutput'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['hkdigunakan'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['bjr'], 2)."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['kg'])."</td>\r\n        <td align=right>".number_format($dzArr[$afd]['p_kg'])."</td>\r\n        <td align=right>".number_format($varian, 2)."</td>\r\n      </tr>";
        }
    }

    $tab .= '</tbody></table></td></tr></tbody><table>';
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
        $qPrd = mysql_query($sPrd)
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optAfd .= '<option value='.$rPrd['kodeorganisasi'].'>'.$rPrd['namaorganisasi'].'</option>';
        }
        $sorg2 = 'select distinct karyawanid,namakaryawan from '.$dbname.".datakaryawan \r\n                where lokasitugas='".$kebun."' and tipekaryawan!='4' order by namakaryawan asc";
        $qorg2 = mysql_query($sorg2)
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
        $qPrd = mysql_query($sPrd) ;
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optAfd .= '<option value='.$rPrd['kodeorganisasi'].'>'.$rPrd['namaorganisasi'].'</option>';
        }
        echo $optAfd;

        break;
    default:
        break;
}
function putertanggal($tgl)
{
    $qwe = explode('-', $tgl);

    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
}

?>