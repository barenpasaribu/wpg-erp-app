<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['gudang'];
$periode = $_GET['periode'];
$periode1 = $_GET['periode1'];
$revisi = $_GET['revisi'];
$qwe = explode('-', $periode);
$tahun = $qwe[0];
$tahunlalu = $tahun - 1;
$bulan = $qwe[1];
$str = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt = 'COMPANY NAME';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $namapt = strtoupper($bar->namaorganisasi);
}
$kodelaporan = 'BALANCE SHEET';
$periodesaldo = str_replace('-', '', $periode);
if ('akhir' == $periode1) {
    $periodPRF = substr($periodesaldo, 0, 4).'01';
} else {
    $periodPRF = $tahunlalu.$bulan;
}

if ('akhir' == $periode1) {
    $periodPRF2 = substr($periodesaldo, 0, 4).'-01';
} else {
    $periodPRF2 = $tahunlalu.'-'.$bulan;
}

if ('akhir' == $periode1) {
    $kolomPRF = 'awal01';
} else {
    $kolomPRF = 'awal'.date('m', $t);
}

$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2) + 1, 15, substr($periodesaldo, 0, 4));
$periodCUR = date('Ym', $t);
$periodCUR2 = substr($periodesaldo, 0, 4).'-'.substr($periodesaldo, 4, 2);
$kolomCUR = 'awal'.date('m', $t);
$t = mktime(0, 0, 0, substr($periodesaldo, 4, 2), 15, substr($periodesaldo, 0, 4));
$captionCUR = date('M-Y', $t);
$t = mktime(0, 0, 0, 12, 15, substr($periodesaldo, 0, 4) - 1);
$t1 = mktime(0, 0, 0, $bulan, 15, substr($periodesaldo, 0, 4) - 1);
if ('akhir' == $periode1) {
    $captionPRF = date('M-Y', $t);
} else {
    $captionPRF = $captionPRF = date('M-Y', $t1);
}

if ('' == $unit) {
    $where = ' kodeorg in(select kodeorganisasi from '.$dbname.".organisasi where induk='".$pt."')";
} else {
    $where = " kodeorg='".$unit."'";
}

$str = 'select * from '.$dbname.".keu_5mesinlaporandt where namalaporan='".$kodelaporan."' order by nourut";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $dzArr[$bar->nourut]['nourut'] = $bar->nourut;
    $dzArr[$bar->nourut]['tampil'] = $bar->variableoutput;
    $dzArr[$bar->nourut]['tipe'] = $bar->tipe;
    if ('ID' == $_SESSION['language']) {
        $dzArr[$bar->nourut]['keterangan'] = $bar->keterangandisplay;
    } else {
        $dzArr[$bar->nourut]['keterangan'] = $bar->keterangandisplay1;
    }

    $dzArr[$bar->nourut]['noakundari'] = $bar->noakundari;
    $dzArr[$bar->nourut]['noakunsampai'] = $bar->noakunsampai;
}
$stream = "<table class=sortable border=0 cellspacing=1>\r\n    <thead>\r\n        <tr class=rowheader>\r\n        <td colspan=5></td>\r\n        <td align=center>".$captionCUR."</td>\r\n        <td align=center>".$captionPRF."</td>    \r\n        </tr>\r\n    </thead><tbody>";
$jlhkolom = 7;
if (!empty($dzArr)) {
    foreach ($dzArr as $data) {
        $st12 = 'select sum('.$kolomPRF.") as kemarin\r\n        from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' \r\n        and '".$data['noakunsampai']."' and (periode='".$periodPRF."') and ".$where;
        $res12 = mysql_query($st12);
        $jlhlalu = 0;
        while ($ba12 = mysql_fetch_object($res12)) {
            $jlhlalu = $ba12->kemarin;
        }
        $dzArr[$data['nourut']]['jumlahlalu'] = $jlhlalu;
        if (0 == $revisi) {
            $st12 = 'select sum('.$kolomCUR.") as sekarang\r\n            from ".$dbname.".keu_saldobulanan where noakun between '".$data['noakundari']."' \r\n            and '".$data['noakunsampai']."' and (periode='".$periodCUR."') and ".$where;
            $res12 = mysql_query($st12);
            $jlhsekarang = 0;
            while ($ba12 = mysql_fetch_object($res12)) {
                $jlhsekarang = $ba12->sekarang;
            }
            $dzArr[$data['nourut']]['jumlahsekarang'] = $jlhsekarang;
        }
    }
}

if (0 < $revisi) {
    $st12 = "select noakun, sum(jumlah) as jumlah\r\n        from ".$dbname.".keu_jurnaldt_vw where periode between '".$periodPRF2."' \r\n        and '".$periodCUR2."' and ".$where." and revisi <= '".$revisi."' group by noakun";
    $res12 = mysql_query($st12);
    $jlhsekarang = 0;
    while ($ba12 = mysql_fetch_object($res12)) {
        if (!empty($dzArr)) {
            foreach ($dzArr as $data) {
                if ($data['noakundari'] <= $ba12->noakun && $ba12->noakun <= $data['noakunsampai']) {
                    $dzArr[$data['nourut']]['jumlahtemp'] += $ba12->jumlah;
                    $dzArr[$data['nourut']]['jumlahsekarang'] = $dzArr[$data['nourut']]['jumlahlalu'] + $dzArr[$data['nourut']]['jumlahtemp'];
                }
            }
        }
    }
}

if (!empty($dzArr)) {
    foreach ($dzArr as $data) {
        if ('Header' == $data['tipe']) {
            if (0 == $data['tampil']) {
                $stream .= '<tr class=rowcontent><td colspan=7><b>'.$data['keterangan'].'</b></td></tr>';
            } else {
                $stream .= "<tr class=rowcontent>\r\n                <td colspan=".$data['tampil']."></td>\r\n                <td colspan=".($jlhkolom - $data['tampil']).'><b>'.$data['keterangan']."</b></td>\r\n            </tr>";
            }
        } else {
            if ('Total' == $data['tipe']) {
                if (0 == $data['tampil']) {
                    if ($data['jumlahsekarang'] < 0) {
                        $stream .= "<tr class=rowcontent>\r\n                <td colspan=5></td>\r\n                <td colspan=2>------------------------------------------------------------</td>\r\n                </tr>\r\n            <tr class=rowcontent>\r\n                <td colspan=5><b>".$data['keterangan']."</b></td>\r\n                <td align=right><strong style=color:red;>".number_format($data['jumlahsekarang'] * -1)."</strong></td>\r\n                <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    \r\n            </tr>\r\n            <tr class=rowcontent>\r\n                <td style='width:30px'></td>\r\n                <td style='width:30px'></td>\r\n                <td style='width:30px'></td>\r\n                <td colspan=4></td>\r\n            </tr>\r\n            ";
                    } else {
                        $stream .= "<tr class=rowcontent>\r\n                    <td colspan=5></td>\r\n                    <td colspan=2>------------------------------------------------------------</td>\r\n                    </tr>\r\n                <tr class=rowcontent>\r\n                    <td colspan=5><b>".$data['keterangan']."</b></td>\r\n                    <td align=right><b>".number_format($data['jumlahsekarang'])."</b></td>\r\n                    <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    \r\n                </tr>\r\n                <tr class=rowcontent>\r\n                    <td style='width:30px'></td>\r\n                    <td style='width:30px'></td>\r\n                    <td style='width:30px'></td>\r\n                    <td colspan=4></td>\r\n                </tr>\r\n                ";
                    }
                } else {
                    if ($data['jumlahsekarang'] < 0) {
                        $stream .= "<tr class=rowcontent>\r\n                <td colspan=5></td>\r\n                <td colspan=".($jlhkolom - 5).">------------------------------------------------------------</td>\r\n                </tr>\r\n                <tr class=rowcontent>\r\n                    <td colspan=".$data['tampil']."></td>\r\n                    <td colspan=".(5 - $data['tampil']).'><b>'.$data['keterangan']."</b></td>\r\n                    <td align=right><strong style=color:red;>".number_format($data['jumlahsekarang'] * -1)."</strong></td>\r\n                    <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    \r\n                </tr>\r\n                <tr class=rowcontent><td colspan=7>.</td></tr>\r\n                ";
                    } else {
                        $stream .= "<tr class=rowcontent>\r\n                    <td colspan=5></td>\r\n                    <td colspan=".($jlhkolom - 5).">------------------------------------------------------------</td>\r\n                </tr>\r\n                <tr class=rowcontent>\r\n                    <td colspan=".$data['tampil']."></td>\r\n                    <td colspan=".(5 - $data['tampil']).'><b>'.$data['keterangan']."</b></td>\r\n                    <td align=right><b>".number_format($data['jumlahsekarang'])."</b></td>\r\n                    <td align=right><b>".number_format($data['jumlahlalu'])."</b></td>    \r\n                </tr>\r\n                <tr class=rowcontent><td colspan=7>.</td></tr>\r\n                ";
                    }
                }
            } else {
                if ($data['jumlahsekarang'] < 0) {
                    $stream .= "\r\n            <tr class=rowcontent title='Click untuk melihat detail' onclick=\"lihatDetailNeraca('".$data['noakundari']."','".$data['noakunsampai']."','".$periode."','".$periode1."','".$pt."','".$unit."',event);\">\r\n                <td colspan=".$data['tampil']."></td>\r\n                <td colspan=".(5 - $data['tampil']).'>'.$data['keterangan']."</td>\r\n                <td align=right><strong style=color:red;>".number_format($data['jumlahsekarang'] * -1)."</strong></td>\r\n                <td align=right>".number_format($data['jumlahlalu'])."</td>    \r\n            </tr>";
                } else {
                    $stream .= "\r\n                <tr class=rowcontent title='Click untuk melihat detail' onclick=\"lihatDetailNeraca('".$data['noakundari']."','".$data['noakunsampai']."','".$periode."','".$periode1."','".$pt."','".$unit."',event);\">\r\n                    <td colspan=".$data['tampil']."></td>\r\n                    <td colspan=".(5 - $data['tampil']).'>'.$data['keterangan']."</td>\r\n                    <td align=right>".number_format($data['jumlahsekarang'])."</td>\r\n                    <td align=right>".number_format($data['jumlahlalu'])."</td>    \r\n                </tr>";
                }
            }
        }
    }
}

$stream .= '</tbody></tfoot></tfoot></table>';
$nop_ = 'Neraca-'.$pt.'-'.$unit.'-'.$periodesaldo;
if (0 < strlen($stream)) {
    if ($handle = opendir('tempExcel')) {
        while (false != ($file = readdir($handle))) {
            if ('.' != $file && '..' != $file) {
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