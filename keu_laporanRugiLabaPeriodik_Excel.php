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
$kodelaporan = 'INCOME STATEMENT';
$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
if ('' === $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$stream = $_SESSION['lang']['laporanrugilabaperiodik'].'<br>'.$pt.'-'.$unit.'-'.$periode."<br>\r\n    <table class=sortable border=1 cellspacing=1>\r\n          <thead>\r\n           <tr class=rowheader>\r\n            <td colspan=3>".$_SESSION['lang']['keterangan']."</td>\r\n            <td align=center>".numToMonth(1, 'E')."</td>\r\n            <td align=center>".numToMonth(2, 'E')."</td>\r\n            <td align=center>".numToMonth(3, 'E')."</td>\r\n            <td align=center>".numToMonth(4, 'E')."</td>\r\n            <td align=center>".numToMonth(5, 'E')."</td>\r\n            <td align=center>".numToMonth(6, 'E')."</td>\r\n            <td align=center>".numToMonth(7, 'E')."</td>\r\n            <td align=center>".numToMonth(8, 'E')."</td>\r\n            <td align=center>".numToMonth(9, 'E')."</td>\r\n            <td align=center>".numToMonth(10, 'E')."</td>\r\n            <td align=center>".numToMonth(11, 'E')."</td>\r\n            <td align=center>".numToMonth(12, 'E')."</td>\r\n            <td align=center>YTD</td>    \r\n            </tr>\r\n         </thead><tbody>";
$tnow2[] = 0;
$ttill2 = 0;
$tnow3[] = 0;
$ttill3 = 0;
while ($bar = mysql_fetch_object($res)) {
    if ('Header' === $bar->tipe) {
        if ('ID' === $_SESSION['language']) {
            $stream .= '<tr class=rowcontent><td colspan=16><b>'.$bar->keterangandisplay.'</b></td></tr>';
        } else {
            $stream .= '<tr class=rowcontent><td colspan=16><b>'.$bar->keterangandisplay1.'</b></td></tr>';
        }
    } else {
        $akum = 0;
        for ($i = 1; $i <= 12; ++$i) {
            if (1 === strlen($i)) {
                $ii = '0'.$i;
            } else {
                $ii = $i;
            }

            $st13 = 'select sum(debet'.$ii.') - sum(kredit'.$ii.") as sekarang\r\n                       from ".$dbname.".keu_saldobulanan where noakun between '".$bar->noakundari."' \r\n                       and '".$bar->noakunsampai."' and  periode like '".$periode."%' and ".$where;
            $res13 = mysql_query($st13);
            $jlhsekarang[$ii] = 0;
            while ($ba13 = mysql_fetch_object($res13)) {
                $jlhsekarang[$ii] = $ba13->sekarang;
                $akum += $ba13->sekarang;
            }
            $tnow201[$ii] += $jlhsekarang[$ii];
            $tnow301[$ii] += $jlhsekarang[$ii];
        }
        $ttill2 += $akum;
        $ttill3 += $akum;
        if ('Total' === $bar->tipe) {
            if ('' === $bar->noakundari || '' === $bar->noakunsampai) {
                if ('2' === $bar->variableoutput) {
                    $akum = $ttill2;
                    $ttill2 = 0;
                    for ($i = 1; $i <= 12; ++$i) {
                        if (1 === strlen($i)) {
                            $ii = '0'.$i;
                        } else {
                            $ii = $i;
                        }

                        $jlhsekarang[$ii] = $tnow201[$ii];
                        $tnow201[$ii] = 0;
                    }
                }

                if ('3' === $bar->variableoutput) {
                    $akum = $ttill3;
                    $ttill3 = 0;
                    for ($i = 1; $i <= 12; ++$i) {
                        if (1 === strlen($i)) {
                            $ii = '0'.$i;
                        } else {
                            $ii = $i;
                        }

                        $jlhsekarang[$ii] = $tnow301[$ii];
                        $tnow301[$ii] = 0;
                    }
                }
            }

            $stream .= "<tr class=rowcontent>\r\n                        <td><td>\r\n                        <td></td>\r\n                        <td colspan=13><hr></td></tr>\r\n                    <tr class=rowcontent>\r\n                        <td></td>";
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td colspan=2><b>'.$bar->keterangandisplay.'</b></td>';
            } else {
                $stream .= '<td colspan=2><b>'.$bar->keterangandisplay1.'</b></td>';
            }

            for ($i = 1; $i <= 12; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $stream .= '<td align=right><b>'.number_format($jlhsekarang[$ii]).'</b></td>';
            }
            $stream .= '<td align=right><b>'.number_format($akum)."</b></td>    \r\n                     </tr>\r\n                     <tr class=rowcontent><td colspan=16>.</td></tr>\r\n                     ";
        } else {
            $stream .= "\r\n                    <tr class=rowcontent>\r\n                    <td style='width:30px'></td><td style='width:30px'></td>";
            if ('ID' === $_SESSION['language']) {
                $stream .= '<td>'.$bar->keterangandisplay.'</td>';
            } else {
                $stream .= '<td>'.$bar->keterangandisplay1.'</td>';
            }

            for ($i = 1; $i <= 12; ++$i) {
                if (1 === strlen($i)) {
                    $ii = '0'.$i;
                } else {
                    $ii = $i;
                }

                $stream .= '<td align=right>'.number_format($jlhsekarang[$ii]).'</td>';
            }
            $stream .= '<td align=right>'.number_format($akum)."</td>    \r\n                     </tr>";
        }
    }
}
$stream .= '</tbody></tfoot></tfoot></table>';
$stream .= 'Print Time:'.date('Y-m-d H:i:s').'<br />By:'.$_SESSION['empl']['name'];
$nop_ = 'LaporanLabaRugiPeriodik-'.$pt.'-'.'-'.$unit.'-'.$periode;
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