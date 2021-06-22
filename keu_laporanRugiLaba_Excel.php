<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$unit."'";
$namagudang = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namagudang = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'INCOME STATEMENT';
$periodesaldo = str_replace('-', '', $periode);
$tahunini = substr($periodesaldo, 0, 4);
$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2) + 1, 15, substr($periodesaldo, 0, 4));
$periodCUR = date('Ym', $t);
$kolomCUR = 'awal'.date('m', $t);
$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2), 15, substr($periodesaldo, 0, 4));
$captionCUR = date('M-Y', $t);
$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$stream = strtoupper($_SESSION['lang']['rugilaba']).' : '.$namapt.' '.$namagudang.'<br>'.strtoupper($_SESSION['lang']['periode']).' : '.$periode."<table class=sortable border=1>\r\n          <thead>\r\n           <tr class=rowheader>\r\n            <td colspan=3></td>\r\n            <td align=center>".$captionCUR."</td>\r\n            <td align=center>YTD</td>    \r\n            </tr>\r\n         </thead><tbody>";
$tnow2 = 0;
$ttill2 = 0;
$tnow3 = 0;
$ttill3 = 0;
while ($bar = mysql_fetch_object($res)) {
    if ('Header' === $bar->tipe) {
        if ('ID' === $_SESSION['language']) {
            $stream .= '<tr class=rowcontent><td colspan=5><b>'.$bar->keterangandisplay.'</b></td></tr>';
        } else {
            $stream .= '<tr class=rowcontent><td colspan=5><b>'.$bar->keterangandisplay1.'</b></td></tr>';
        }
    } else {
/*        $st12 = 'select sum(awal'.substr($periodesaldo, 4, 2).')+sum(debet'.substr($periodesaldo, 4, 2).') - sum(kredit'.substr($periodesaldo, 4, 2).") as akumilasi\r\n                from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n                and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where;
*/        
        $st12 = "select sum(kredit) - sum(debet) as akumilasi ".
            "from  $dbname.keu_jurnaldt_vw where noakun between '".$bar->noakundari."' and '".$bar->noakunsampai."' and  ".
            "tanggal>='".$tahunini."-01-01'  and  tanggal<='".$periode."31'and ".$where;

        $res12 = mysql_query($st12);

        $akumulasi = 0;
        while ($ba12 = mysql_fetch_object($res12)) {
            $akumulasi = $ba12->akumilasi;
        }
        $st13 = 'select sum(kredit'.substr($periodesaldo, 4, 2).') - sum(debet'.substr($periodesaldo, 4, 2).") as sekarang\r\n               from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n               and '".$bar->noakunsampai."' and  periode='".$periodesaldo."' and ".$where;
        $res13 = mysql_query($st13);
        $jlhsekarang = 0;
        while ($ba13 = mysql_fetch_object($res13)) {
            $jlhsekarang = $ba13->sekarang;
        }
        $tnow2 += $jlhsekarang;
        $ttill2 += $akumulasi;
        $tnow3 += $jlhsekarang;
        $ttill3 += $akumulasi;
        if ('Total' === $bar->tipe) {
            if ('' === $bar->noakundari || '' === $bar->noakunsampai) {
                if ('2' === $bar->variableoutput) {
                    $jlhsekarang = $tnow2;
                    $akumulasi = $ttill2;
                    $tnow2 = 0;
                    $ttill2 = 0;
                }

                if ('3' === $bar->variableoutput) {
                    $jlhsekarang = $tnow3;
                    $akumulasi = $ttill3;
                    $tnow3 = 0;
                    $ttill3 = 0;
                }
            }

            $stream .= "<tr class=rowcontent>\r\n                        <td></td>";
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td colspan=2><b>'.$bar->keterangandisplay.'</b></td>';
            } else {
                $stream .= '<td colspan=2><b>'.$bar->keterangandisplay1.'</b></td>';
            }

//            if ($jlhsekarang < 0 || $akumulasi < 0) {
//                $stream .= '<td align=right><b><strong style=color:red;>'.number_format($jlhsekarang * -1)."</strong></b></td>\r\n                    <td align=right><b><strong style=color:red;>".number_format($akumulasi * -1).'</strong></b></td></tr>';
//            } else {
                $stream .= '<td align=right><b>'.number_format($jlhsekarang)."</b></td>\r\n                    <td align=right><b>".number_format($akumulasi)."</b></td>\r\n                </tr>";
//            }
        } else {
            $stream .= "<tr class=rowcontent>\r\n                    <td style='width:30px'></td><td style='width:30px'></td>";
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td>'.$bar->keterangandisplay.'</td>';
            } else {
                $stream .= '<td>'.$bar->keterangandisplay1.'</td>';
            }

//            if ($jlhsekarang < 0 || $akumulasi < 0) {
//                $stream .= '<td align=right><strong style=color:red;>'.number_format($jlhsekarang * -1)."</strong></td>\r\n                <td align=right><strong style=color:red;>".number_format($akumulasi * -1)."</strong></td>\r\n                 </tr>";
//            } else {
                $stream .= '<td align=right>'.number_format($jlhsekarang)."</td>\r\n                <td align=right>".number_format($akumulasi)."</td>\r\n                 </tr>";
//            }
        }
    }
}
$stream .= '</tbody></tfoot></tfoot></table>';
$nop_ = 'LaporanLabaRugi-'.$pt.'_'.'_'.$unit.'_'.$periode;
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
        echo "<script language=javascript1.2>\r\n        parent.window.alert('Can't convert to excel format');\r\n        </script>";
        exit();
    }

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls';\r\n        </script>";
    closedir($handle);
}

?>