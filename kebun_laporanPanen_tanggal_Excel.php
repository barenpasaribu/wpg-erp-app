<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['unit'];
$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];
$tanggal1 = explode('-', $tgl1);
$tanggal2 = explode('-', $tgl2);
$date1 = $tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
$tanggalterakhir = date(t, strtotime($date1));
$tanggal = [];
if ($tanggal1[1] < $tanggal2[1]) {
    for ($i = $tanggal1[0]; $i <= $tanggalterakhir; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii] = $tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
    }
    for ($i = 1; $i <= $tanggal2[0]; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal2[2].'-'.$tanggal2[1].'-'.$ii] = $tanggal2[2].'-'.$tanggal2[1].'-'.$ii;
    }
} else {
    for ($i = $tanggal1[0]; $i <= $tanggal2[0]; ++$i) {
        if (1 === strlen($i)) {
            $ii = '0'.$i;
        } else {
            $ii = $i;
        }

        $tanggal[$tanggal1[2].'-'.$tanggal1[1].'-'.$ii] = $tanggal1[2].'-'.$tanggal1[1].'-'.$ii;
    }
}

if ('' === $unit) {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between '".tanggalsystem($tgl1)."' and '".tanggalsystem($tgl2)."' group by a.tanggal,a.kodeorg";
} else {
    $str = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n        where unit = '".$unit."'  and a.tanggal between '".tanggalsystem($tgl1)."' and '".tanggalsystem($tgl2)."' group by a.tanggal, a.kodeorg";
}

$dzArr = [];
$kmrn = strtotime('-1 day', strtotime($date1));
$kmrn = date('Y-m-d', $kmrn);
if ('' === $unit) {
    $str2 = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between '".$kmrn."' and '".tanggalsystem($tgl2)."' group by a.tanggal,a.kodeorg";
} else {
    $str2 = "select a.tanggal,a.tahuntanam,a.unit,a.kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,count(a.karyawanid) as jumlahhk  from ".$dbname.".kebun_prestasi_vw a\r\n        where unit = '".$unit."'  and a.tanggal between '".$kmrn."' and '".tanggalsystem($tgl2)."' group by a.tanggal, a.kodeorg";
}

$qKmrn = mysql_query($str2) ;
while ($rKmr = mysql_fetch_object($qKmrn)) {
    $dzArrk[$rKmr->kodeorg][$rKmr->tanggal.'j'] = $rKmr->jjg;
}
$jumlahhari = count($tanggal);
$res = mysql_query($str);
if (mysql_num_rows($res) < 1) {
    $jukol = $jumlahhari * 3 + 5;
    echo $_SESSION['lang']['tidakditemukan'];
    exit();
}

while ($bar = mysql_fetch_object($res)) {
    $dzArr[$bar->kodeorg][$bar->tanggal] = $bar->tanggal;
    $dzArr[$bar->kodeorg]['kodeorg'] = $bar->kodeorg;
    $dzArr[$bar->kodeorg]['tahuntanam'] = $bar->tahuntanam;
    $dzArr[$bar->kodeorg][$bar->tanggal.'j'] = $bar->jjg;
    $dzArr[$bar->kodeorg][$bar->tanggal.'k'] = $bar->berat;
    $dzArr[$bar->kodeorg][$bar->tanggal.'h'] = $bar->jumlahhk;
}
if (!empty($dzArr)) {
    foreach ($dzArr as $c => $key) {
        $sort_kodeorg[] = $key['kodeorg'];
        $sort_tahuntanam[] = $key['tahuntanam'];
    }
    array_multisort($sort_kodeorg, SORT_ASC, $sort_tahuntanam, SORT_ASC, $dzArr);
}

$stream = $_SESSION['lang']['laporanpanen'].' '.$pt.' '.$unit.' per '.$_SESSION['lang']['tanggal'].' '.$tgl1.' - '.$tgl2;
$stream .= '<table border=1 cellpading=1>';
$stream .= "<thead>\r\n        <tr>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>No.</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['afdeling']."</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['kodeblok']."</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['tahuntanam'].'</td>';
foreach ($tanggal as $tang) {
    $ting = explode('-', $tang);
    $qwe = date('D', strtotime($tang));
    $stream .= '<td bgcolor=#DEDEDE colspan=3 align=center>';
    if ('Sun' === $qwe) {
        $stream .= '<font color=red>'.$ting[2].'</font>';
    } else {
        $stream .= $ting[2];
    }

    $stream .= '</td>';
}
$stream .= '<td bgcolor=#DEDEDE colspan=3 align=center>Total</td><td bgcolor=#DEDEDE align=center>Average</td></tr><tr>';
foreach ($tanggal as $tang) {
    $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg']."</td>\r\n                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>\r\n                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlahhk'].'</td>';
}
$stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlahhk'].'</td><td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg']."</td></tr>  \r\n        </thead>\r\n\t<tbody>";
$no = 0;
foreach ($dzArr as $arey) {
    ++$no;
    $stream .= "<tr class='rowcontent'>\r\n            <td align=center>".$no."</td>\r\n            <td align=center>".substr($arey['kodeorg'], 0, 6)."</td>\r\n            <td align=center>".$arey['kodeorg']."</td>\r\n            <td align=center>".$arey['tahuntanam'].'</td>';
    $totalj = 0;
    $totalk = 0;
    $totalh = 0;
    $totaltanpanol = 0;
    $jumlahtanpanol = 0;
    foreach ($tanggal as $tang) {
        $qwe = date('D', strtotime($tang));
        $dbg = '';
        $tglkmrn = strtotime('-1 day', strtotime($tang));
        $tglkmrn2 = date('Y-m-d', $tglkmrn);
        if (0 !== $dzArrk[$arey['kodeorg']][$tglkmrn2.'j'] && 0 !== $arey[$tang.'j']) {
            $dbg = 'bgcolor=red';
        }

        if ('Sun' === $qwe) {
            $stream .= '<td align=right '.$dbg.'><font color=red>'.number_format($arey[$tang.'j']).'</font></td>';
            $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'k']).'</font></td>';
            $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'h']).'</font></td>';
        } else {
            $stream .= '<td align=right '.$dbg.'>'.number_format($arey[$tang.'j']).'</td>';
            $stream .= '<td align=right>'.number_format($arey[$tang.'k']).'</td>';
            $stream .= '<td align=right>'.number_format($arey[$tang.'h']).'</td>';
        }

        $stream .= '</td>';
        $total[$tang.'j'] += $arey[$tang.'j'];
        $total[$tang.'k'] += $arey[$tang.'k'];
        $total[$tang.'h'] += $arey[$tang.'h'];
        $totalj += $arey[$tang.'j'];
        $totalk += $arey[$tang.'k'];
        $totalh += $arey[$tang.'h'];
        if (0 < $arey[$tang.'j']) {
            $totaltanpanol += $arey[$tang.'j'];
            ++$jumlahtanpanol;
        }
    }
    $rataj = $totaltanpanol / $jumlahtanpanol;
    $stream .= '<td align=right>'.number_format($totalj)."</td>\r\n            <td align=right>".number_format($totalk)."</td>\r\n            <td align=right>".number_format($totalh).'</td><td align=right>'.number_format($rataj).'</td></tr>';
}
$stream .= "<tr class='rowcontent'>\r\n        <td colspan=4 align=center>Total</td>";
$totalj = 0;
$totalk = 0;
$totalh = 0;
foreach ($tanggal as $tang) {
    $stream .= '<td align=right>'.number_format($total[$tang.'j']).'</td>';
    $stream .= '<td align=right>'.number_format($total[$tang.'k']).'</td>';
    $stream .= '<td align=right>'.number_format($total[$tang.'h']).'</td>';
    $totalj += $total[$tang.'j'];
    $totalk += $total[$tang.'k'];
    $totalh += $total[$tang.'h'];
}
$stream .= '<td align=right>'.number_format($totalj)."</td>\r\n        <td align=right>".number_format($totalk)."</td>\r\n        <td align=right>".number_format($totalh).'</td><td></td></tr>';
$stream .= "</tbody>\r\n        <tfoot>\r\n        </tfoot>";
$stream .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
$tglSkrg = date('Ymd');
$nop_ = 'LaporanPanenTanggal'.$pt.'_'.$unit.'_'.$tgl1;
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