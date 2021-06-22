<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
($_POST['proses'] == '' ? ($proses = $_GET['proses']) : ($proses = $_POST['proses']));
($_POST['traksiId'] == '' ? ($traksiId = $_GET['traksiId']) : ($traksiId = $_POST['traksiId']));
($_POST['periode'] == '' ? ($periode = $_GET['periode']) : ($periode = $_POST['periode']));
($_POST['afdId'] == '' ? ($afdId = $_GET['afdId']) : ($afdId = $_POST['afdId']));
if ('preview' == $proses || 'excel' === $proses) {
    if ($traksiId !== '') {
        $whr = " and  b.kodeorg='".$traksiId."'";
        $whrpab = " and kodeorg='".$traksiId."'";
    }

    if ($afdId !== '') {
        $whr = " and a.nospb like '%".$afdId."%'";
        $whrpab = " and nospb like '%".$afdId."%'";
    }

    if ($periode == '') {
        exit('Error:Field Tidak Boleh Kosong');
    }

    $brd = 0;
    if ($proses  == 'excel') {
        $brd = 1;
        $bgcoloraja = 'bgcolor=#DEDEDE align=center';
    }

    $str = 'SELECT a.nospb,sum(a.jjg) as jjg,b.tanggal,substr(a.nospb,9,6) as afdeling FROM '.$dbname.".kebun_spbdt a\r\n           left join ".$dbname.".kebun_spbht b on a.nospb=b.nospb where b.tanggal like '".$periode."%' ".$whr." group by a.nospb\r\n           order by tanggal,nospb";
    $reskebun = mysql_query($str);
    $sPabrik = "select nospb,(jumlahtandan1+jumlahtandan2+jumlahtandan3) as jjgpabrik,(beratbersih-kgpotsortasi) as beratbersih,\r\n                notransaksi,left(tanggal,10) as tanggal from ".$dbname.".pabrik_timbangan where \r\n          left(tanggal,10)!='' ".$whrpab." and nospb!='' \r\n          and left(tanggal,10) like '".$periode."%' order by tanggal,nospb";
    $respabrik = mysql_query($sPabrik);
    while ($bar = mysql_fetch_object($reskebun)) {
        $nospb[$bar->nospb] .= $bar->nospb.' ';
        $afd[$bar->nospb] .= $bar->afdeling.' ';
        $tglkebun[$bar->nospb] .= $bar->tanggal.' ';
        $jjgkebun[$bar->nospb] += $bar->jjg.' ';
        $nospbkebun[$bar->nospb] .= $bar->nospb.' ';
    }
    while ($bar1 = mysql_fetch_object($respabrik)) {
        $nospb[$bar1->nospb] .= $bar1->nospb.' ';
        $tglpabrik[$bar1->nospb] .= $bar1->tanggal.' ';
        $tiket[$bar1->nospb] .= $bar1->notransaksi.' ';
        $beratbersih[$bar1->nospb] += $bar1->beratbersih.' ';
        $jjgpabrik[$bar1->nospb] += $bar1->jjgpabrik.' ';
        $nosppabrik[$bar1->nospb] .= $bar1->nospb.' ';
    }
    $tab .= "\r\n<table cellspacing=1 border=".$brd." >\r\n<thead>\r\n<tr><td align=center colspan=5>".$_SESSION['lang']['kebun']."</td>\r\n<td align=center colspan=5>".$_SESSION['lang']['pabrik']."</td></tr>\r\n            <tr class=rowheader>\r\n            <td ".$bgcoloraja.">No</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['kodeorg']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['nospb']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['tglNospb']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjg']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['tanggal']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['nospb']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['notransaksi']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['berat']."</td>\r\n            <td ".$bgcoloraja.'>'.$_SESSION['lang']['jjg']."</td>\r\n            </tr>\r\n</thead><tbody>";
    if (isset($nospb)) {
        $no = 0;
        foreach ($nospb as $spb => $val) {
            ++$no;
            if (!isset($nospbkebun[$spb])) {
                $colorkebun = 'red';
            } else {
                $colorkebun = '#D1E3BA';
            }

            if (!isset($nosppabrik[$spb])) {
                $colorpabrik = 'red';
            } else {
                $colorpabrik = '#CEDCDE';
            }

            $tab .= "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td bgcolor=".$colorkebun.'>'.$afd[$spb]."</td>\r\n            <td bgcolor=".$colorkebun.'>'.$nospbkebun[$spb]."</td>\r\n            <td bgcolor=".$colorkebun.'>'.tanggalnormal($tglkebun[$spb])."</td>\r\n            <td bgcolor=".$colorkebun.' align=right>'.number_format($jjgkebun[$spb])."</td> \r\n            <td bgcolor=".$colorpabrik.'>'.$tglpabrik[$spb]."</td>\r\n            <td bgcolor=".$colorpabrik.'>'.$nosppabrik[$spb]."</td>\r\n            <td bgcolor=".$colorpabrik.'>'.$tiket[$spb]."</td>\r\n            <td align=right bgcolor=".$colorpabrik.'>'.number_format($beratbersih[$spb])."</td>\r\n            <td align=right bgcolor=".$colorpabrik.'>'.number_format($jjgpabrik[$spb])."</td>\r\n            </tr>";
            $totaljjgkebun += $jjgkebun[$spb];
            $totalberatbersih += $beratbersih[$spb];
            $totaljjgpabrik += $jjgpabrik[$spb];
        }
        $tab .= "<tr class=rowcontent>\r\n            <td></td>\r\n            <td bgcolor=".$colorkebun."></td>\r\n            <td bgcolor=".$colorkebun."></td>\r\n            <td bgcolor=".$colorkebun.">Total</td>\r\n            <td bgcolor=".$colorkebun.' align=right>'.number_format($totaljjgkebun)."</td> \r\n            <td bgcolor=".$colorpabrik."></td>\r\n            <td bgcolor=".$colorpabrik."></td>\r\n            <td bgcolor=".$colorpabrik.">Total</td>\r\n            <td align=right bgcolor=".$colorpabrik.'>'.number_format($totalberatbersih)."</td>\r\n            <td align=right bgcolor=".$colorpabrik.'>'.number_format($totaljjgpabrik)."</td>\r\n            </tr>";
    }

    $tab .= '</tbody></table></td></tr></tbody><table>';
}

switch ($proses) {
    case 'preview':
        echo $tab;

        break;
    case 'excel':
        $tab .= 'Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
        $nop_ = 'spbvstimbangan__'.$traksiId.'__'.$periode;
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
                echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
                exit();
            }

            echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
            closedir($handle);
        }

        break;
    case 'getPrd':
        $optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';
        $sPrd = 'select distinct left(tanggal,7) as periode from '.$dbname.".kebun_spbht \r\n               where kodeorg = '".$traksiId."' order by left(tanggal,7) desc";
        $qPrd = mysql_query($sPrd);
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optPeriode .= '<option value='.$rPrd['periode'].'>'.$rPrd['periode'].'</option>';
        }
        $optAfd = "<option value=''>".$_SESSION['lang']['all'].'</option>';
        $sPrd = 'select distinct kodeorganisasi,namaorganisasi from '.$dbname.".organisasi \r\n               where induk = '".$traksiId."' and tipe='afdeling' order by namaorganisasi asc";
        $qPrd = mysql_query($sPrd) ;
        while ($rPrd = mysql_fetch_assoc($qPrd)) {
            $optAfd .= '<option value='.$rPrd['kodeorganisasi'].'>'.$rPrd['namaorganisasi'].'</option>';
        }
        echo $optPeriode.'####'.$optAfd;

        break;
    default:
        break;
}

?>