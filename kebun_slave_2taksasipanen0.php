<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
('' === $_POST['proses'] ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
('' === $_POST['kebun0'] ? ($kebun = $_GET['kebun0']) : ($kebun = $_POST['kebun0']));
('' === $_POST['afdeling0'] ? ($afdeling = $_GET['afdeling0']) : ($afdeling = $_POST['afdeling0']));
('' === $_POST['periode0'] ? ($periode = $_GET['periode0']) : ($periode = $_POST['periode0']));
if ('preview' === $proses || 'excel' === $proses) {
    if ('' === $periode || '' === $kebun) {
        exit('Error: All field required');
    }

    $brd = 0;
    if ('excel' === $proses) {
        $brd = 1;
        $bgcoloraja = 'bgcolor=#DEDEDE align=center';
    }

    $sPabrik = "select nospb,kodeorg, (jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,\r\n        notransaksi,left(tanggal,10) as tanggal, substr(nospb,9,6) as afdeling from ".$dbname.".pabrik_timbangan \r\n        where left(tanggal,10)!='' and kodeorg = '".$kebun."' and nospb!='' and substr(nospb,9,6) like '".$afdeling."%'\r\n        and left(tanggal,10) like '".$periode."%' order by substr(nospb,9,6)";
    $respabrik = mysql_query($sPabrik);
    while ($bar0 = mysql_fetch_object($respabrik)) {
        $kunci2 = $kebun.$bar0->tanggal;
        $dzArr[$kunci2]['p_kg'] += $bar0->beratbersih;
    }
    $sPabrik = 'select kodeorganisasi from '.$dbname.".organisasi\r\n        where kodeorganisasi like '".$kebun."%' and kodeorganisasi like '".$afdeling."%' and tipe = 'AFDELING'";
    $respabrik = mysql_query($sPabrik);
    while ($bar0 = mysql_fetch_object($respabrik)) {
        $listafd[$bar0->kodeorganisasi] = $bar0->kodeorganisasi;
    }
    $sTaksasi = 'select afdeling, tanggal, blok, seksi, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from '.$dbname.".kebun_taksasi \r\n        where afdeling like '".$kebun."%' and afdeling like '%".$afdeling."%' and tanggal like '".$periode."%' \r\n        ";
    $restaksasi = mysql_query($sTaksasi);
    while ($bar1 = mysql_fetch_object($restaksasi)) {
        $kunci = $bar1->afdeling.$bar1->tanggal;
        ++$dzArr[$kunci]['counter'];
        $dzArr[$kunci]['afdeling'] = $bar1->afdeling;
        $dzArr[$kunci]['blok'] .= $bar1->blok.'</br>';
        $dzArr[$kunci]['seksi'] .= $bar1->seksi.'</br>';
        $dzArr[$kunci]['hasisa'] += $bar1->hasisa;
        $dzArr[$kunci]['haesok'] += $bar1->haesok;
        $dzArr[$kunci]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$kunci]['pbm'] += $bar1->persenbuahmatang;
        $dzArr[$kunci]['persenbuahmatang'] = $dzArr[$kunci]['pbm'] / $dzArr[$kunci]['counter'];
        $dzArr[$kunci]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$kunci]['jjgoutput'] += $bar1->jjgoutput;
        $dzArr[$kunci]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$kunci]['kg'] += $bar1->kg;
        $dzArr[$kunci]['bjr'] = $dzArr[$kunci]['kg'] / $dzArr[$kunci]['jjgmasak'];
    }
    $sTaksasi = 'select afdeling, tanggal, blok, seksi, hasisa, haesok, jmlhpokok, persenbuahmatang, jjgmasak, jjgoutput, hkdigunakan, bjr, (bjr*jjgmasak) as kg from '.$dbname.".kebun_taksasi \r\n        where afdeling like '".$kebun."%' and tanggal like '".$periode."%'\r\n        ";
    $restaksasi = mysql_query($sTaksasi);
    while ($bar1 = mysql_fetch_object($restaksasi)) {
        $kunci2 = $kebun.$bar1->tanggal;
        $dzArr[$kunci2]['hkdigunakan'] += $bar1->hkdigunakan;
        $dzArr[$kunci2]['hasisa'] += $bar1->hasisa;
        $dzArr[$kunci2]['haesok'] += $bar1->haesok;
        $dzArr[$kunci2]['jmlhpokok'] += $bar1->jmlhpokok;
        $dzArr[$kunci2]['jjgmasak'] += $bar1->jjgmasak;
        $dzArr[$kunci2]['kg'] += $bar1->kg;
    }
    if ('excel' !== $proses) {
    } else {
        $tab .= $_SESSION['lang']['laporan'].' '.$_SESSION['lang']['rencanapanen'].' '.$_SESSION['lang']['harian'].'<br>Kebun: '.$kebun.' '.$afdeling.' '.$periode.' ';
    }

    $tab .= "\r\n    <table width=100% cellspacing=1 border=".$brd." >\r\n    <thead>\r\n    <tr>\r\n        <td ".$bgcoloraja.' rowspan=3>'.$_SESSION['lang']['tanggal'].'</td>';
    if (!empty($listafd)) {
        foreach ($listafd as $laf) {
            $tab .= '<td '.$bgcoloraja.' colspan=12 align=center>'.$laf.'</td>';
        }
    }

    $tab .= "\r\n        <td ".$bgcoloraja.' colspan=11 align=center>'.$kebun."</td>\r\n    </tr>\r\n    <tr>";
    if (!empty($listafd)) {
        foreach ($listafd as $laf) {
            $tab .= '<td '.$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['section']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['blok']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['hasisa']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['haesok']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jumlahha']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jmlhpokok']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['persenbuahmatang']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jjgmasak']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jjgoutput']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['hkdigunakan']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['bjr']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['taksasi'].' (kg)</td>';
        }
    }

    $tab .= "\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jumlahhk']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['jumlahha']."</td>\r\n        <td ".$bgcoloraja.' rowspan=2>'.$_SESSION['lang']['persenbuahmatang']."</td>\r\n        <td ".$bgcoloraja.' colspan=2>'.$_SESSION['lang']['taksasi']." (kg)</td>\r\n        <td ".$bgcoloraja.' colspan=2>'.$_SESSION['lang']['realisasi']." (kg)</td>\r\n        <td ".$bgcoloraja." colspan=2>Varian (kg)</td>\r\n        <td ".$bgcoloraja.' colspan=2>'.$_SESSION['lang']['varian']."</td>\r\n    </tr>\r\n    <tr>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['sdhi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['sdhi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['sdhi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['hi']."</td>\r\n        <td ".$bgcoloraja.'>'.$_SESSION['lang']['sdhi']."</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
    $tanggalterakhir = date('t', strtotime($periode.'-01'));
    $kgsdhi = 0;
    $p_kgsdhi = 0;
    $varian_kgsdhi = 0;
    for ($i = 1; $i <= $tanggalterakhir; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $kunci2 = $kebun.$periode.'-'.$ii;
        $jumlahha2 = $dzArr[$kunci2]['hasisa'] + $dzArr[$kunci2]['haesok'];
        $kgsdhi += $dzArr[$kunci2]['kg'];
        $p_kgsdhi += $dzArr[$kunci2]['p_kg'];
        $varian_kg = $dzArr[$kunci2]['p_kg'] - $dzArr[$kunci2]['kg'];
        $varian_kgsdhi += $varian_kg;
        $varian_ps = 100 - ($dzArr[$kunci2]['p_kg'] - $dzArr[$kunci2]['kg']) / $dzArr[$kunci2]['p_kg'] * 100;
        $varian_pssdhi = 100 - ($p_kgsdhi - $kgsdhi) / $p_kgsdhi * 100;
        if (0 === $dzArr[$kunci2]['kg']) {
            $varian_ps = 0;
        }

        $tab .= "<tr class=rowcontent>\r\n        <td align=right>".$i.'</td>';
        if (!empty($listafd)) {
            foreach ($listafd as $laf) {
                $kunci = $laf.$periode.'-'.$ii;
                $jumlahha = $dzArr[$kunci]['hasisa'] + $dzArr[$kunci]['haesok'];
                $pbm = ($dzArr[$kunci]['jjgmasak'] * 100) / $dzArr[$kunci]['jmlhpokok'];
                $tab .= '<td>'.$dzArr[$kunci]['seksi']."</td>\r\n            <td>".$dzArr[$kunci]['blok']."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['hasisa'], 2)."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['haesok'], 2)."</td>\r\n            <td align=right>".number_format($jumlahha, 2)."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['jmlhpokok'])."</td>\r\n            <td align=right>".number_format($pbm, 2)."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['jjgmasak'])."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['jjgoutput'])."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['hkdigunakan'])."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['bjr'], 2)."</td>\r\n            <td align=right>".number_format($dzArr[$kunci]['kg']).'</td>';
            }
        }

        $pbm2 = ($dzArr[$kunci2]['jjgmasak'] * 100) / $dzArr[$kunci2]['jmlhpokok'];
        $tab .= "\r\n        <td align=right>".number_format($dzArr[$kunci2]['hkdigunakan'])."</td>\r\n        <td align=right>".number_format($jumlahha2, 2)."</td>\r\n        <td align=right>".number_format($pbm2, 2)."</td>\r\n        <td align=right>".number_format($dzArr[$kunci2]['kg'])."</td>\r\n        <td align=right>".number_format($kgsdhi)."</td>\r\n        <td align=right>".number_format($dzArr[$kunci2]['p_kg'])."</td>\r\n        <td align=right>".number_format($p_kgsdhi)."</td>\r\n        <td align=right>".number_format($varian_kg)."</td>\r\n        <td align=right>".number_format($varian_kgsdhi)."</td>\r\n        <td align=right>".number_format($varian_ps, 2)."</td>\r\n        <td align=right>".number_format($varian_pssdhi, 2)."</td>\r\n        </tr>";
    }
    $tab .= '</tbody></table></td></tr></tbody><table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'taksasi_pertgl_'.$kebun.'_'.$afdeling.'_'.$periode;
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
    default:
        break;
}

?>