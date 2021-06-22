<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$pt = $_POST['pt'];
$unit = $_POST['unit'];
$tgl1 = $_POST['tgl1'];
$tgl2 = $_POST['tgl2'];
$pil = $_POST['pil'];
$tanggal1 = explode('-', $tgl1);
$tanggal2 = explode('-', $tgl2);
$date1 = $tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
$tanggalterakhir = date(t, strtotime($date1));
$tanggal1_1 = date('Y-m-d', mktime(0, 0, 0, $tanggal1[1] - 1, $tanggal1[0], $tanggal1[2]));
$bulankemarin = substr($tanggal1_1, 0, 7);
$sbjrlalu = "select blok, sum(jjg) as jjg, sum(kgwb) as kgwb from $dbname.kebun_spb_vw ".
    "where notiket IS NOT NULL and tanggal like '".$bulankemarin."%' group by blok";

$qbjrlalu = mysql_query($sbjrlalu) ;
while ($rbjrlalu = mysql_fetch_assoc($qbjrlalu)) {
    $beje = $rbjrlalu['kgwb'] / $rbjrlalu['jjg'];
    $bjrlalu[$rbjrlalu['blok']] = $beje;
}
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

$sdakar = "select karyawanid, namakaryawan, tipekaryawan, subbagian from  $dbname.datakaryawan";
$qdakar = mysql_query($sdakar) ;
while ($rdakar = mysql_fetch_assoc($qdakar)) {
    $dakar[$rdakar['karyawanid']]['karyawanid'] = $rdakar['karyawanid'];
    $dakar[$rdakar['karyawanid']]['namakaryawan'] = $rdakar['namakaryawan'];
    $dakar[$rdakar['karyawanid']]['tipekaryawan'] = $rdakar['tipekaryawan'];
    $dakar[$rdakar['karyawanid']]['subbagian'] = $rdakar['subbagian'];
}
$stikar = 'select id, tipe from '.$dbname.'.sdm_5tipekaryawan';
$qtikar = mysql_query($stikar) ;
while ($rtikar = mysql_fetch_assoc($qtikar)) {
    $tikar[$rtikar['id']] = $rtikar['tipe'];
}
if ('' === $unit) {
    $str = "select a.tanggal,GROUP_CONCAT(a.tahuntanam SEPARATOR ' ') as tahuntanam,a.unit,GROUP_CONCAT(a.kodeorg SEPARATOR ' ') as kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid , sum(a.brondolan) as brd from ".$dbname.".kebun_prestasi_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal,a.karyawanid';
} else {
    $str = "select a.tanggal,GROUP_CONCAT(a.tahuntanam SEPARATOR ' ') as tahuntanam,a.unit,GROUP_CONCAT(a.kodeorg SEPARATOR ' ') as kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid , sum(a.brondolan) as brd from ".$dbname.".kebun_prestasi_vw a\r\n        where unit = '".$unit."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal, a.karyawanid';
}

$jumlahhari = count($tanggal);
$res = mysql_query($str);
$dzArr = [];
if (mysql_num_rows($res) < 1) {
    $jukol = $jumlahhari * 3 + 5;
    echo '<tr class=rowcontent><td colspan='.$jukol.'>'.$_SESSION['lang']['tidakditemukan'].'</td></tr>';
} else {
    while ($bar = mysql_fetch_object($res)) {
        $dzArr[$bar->karyawanid][$bar->tanggal] = $bar->tanggal;
        $dzArr[$bar->karyawanid]['karyawanid'] = $bar->karyawanid;
        $dzArr[$bar->karyawanid][$bar->tanggal.'j'] = $bar->berat;
        $dzArr[$bar->karyawanid][$bar->tanggal.'k'] = $bar->jjg;
        $dzArr[$bar->karyawanid][$bar->tanggal.'r'] = $bar->brd;
        $dzArr[$bar->karyawanid][$bar->tanggal.'h'] = $bar->luaspanen;
        $dzArr[$bar->karyawanid][$bar->tanggal.'b'] = $bar->kodeorg;
        $dzArr[$bar->karyawanid][$bar->tanggal.'t'] = $bar->tahuntanam;
    }
}
if (!empty($dzArr)) {
    foreach ($dzArr as $c => $key) {
        $sort_kodeorg[] = $key['karyawanid'];
    }
    array_multisort($sort_kodeorg, SORT_ASC, $dzArr);
}

echo "<thead>\r\n        <tr>\r\n            <td rowspan=2 align=center>No.</td>\r\n            <td rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n            <td rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>\r\n            <td rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan'].'</td>';
foreach ($tanggal as $tang) {
    $ting = explode('-', $tang);
    $qwe = date('D', strtotime($tang));
    if ('fisik' === $pil) {
        $kolspan = 4;
    } else {
        $kolspan = 4;
    }

    echo '<td colspan='.$kolspan.' align=center>';
    if ('Sun' === $qwe) {
        echo '<font color=red>'.$ting[2].'</font>';
    } else {
        echo $ting[2];
    }

    echo '</td>';
}
if ('fisik' === $pil) {
    echo '<td colspan=4 align=center>Total</td><td align=center>Rata2</td>';
}

echo '</tr><tr>';
foreach ($tanggal as $tang) {
    if ('fisik' === $pil) {
        echo '<td align=center>'.$_SESSION['lang']['jjg']."</td>\r\n                <td align=center>".$_SESSION['lang']['kg']."</td>\r\n                <td align=center>brd</td><td align=center>".$_SESSION['lang']['ha'].'</td>';
    } else {
        echo '<td align=center>'.$_SESSION['lang']['blok']."</td>\r\n                <td align=center>".$_SESSION['lang']['tahuntanam']."</td>\r\n            <td align=center>".$_SESSION['lang']['bjr'].' Akt '.$_SESSION['lang']['bulanlalu'].'</td>';
    }
}
if ('fisik' === $pil) {
    echo '<td align=center>'.$_SESSION['lang']['jjg']."</td>\r\n            <td align=center>".$_SESSION['lang']['kg']."</td>\r\n           <td align=center>brd</td><td align=center>".$_SESSION['lang']['ha'].'</td><td align=center>'.$_SESSION['lang']['jjg'].'</td>';
}

echo "</tr></thead>\r\n\t<tbody>";
$no = 0;
foreach ($dzArr as $arey) {
    ++$no;
    echo "<tr class='rowcontent'>\r\n            <td align=center>".$no."</td>\r\n            <td align=left>".$dakar[$arey['karyawanid']]['namakaryawan']."</td>\r\n            <td align=left>".$dakar[$arey['karyawanid']]['subbagian']."</td>\r\n            <td align=center>".$tikar[$dakar[$arey['karyawanid']]['tipekaryawan']].'</td>';
    $totalj = 0;
    $totalk = 0;
    $totalh = 0;
    $totalr = 0;
    $totaltanpanol = 0;
    $jumlahtanpanol = 0;
    foreach ($tanggal as $tang) {
        $qwe = date('D', strtotime($tang));
        if ('Sun' === $qwe) {
            if ('fisik' === $pil) {
                echo '<td align=right><font color=red>'.number_format($arey[$tang.'k']).'</font></td>';
                echo '<td align=right><font color=red>'.number_format($arey[$tang.'j']).'</font></td>';
                echo '<td align=right><font color=red>'.number_format($arey[$tang.'r']).'</font></td>';
                echo '<td align=right><font color=red>'.number_format($arey[$tang.'h']).'</font></td>';
            } else {
                echo '<td align=left><font color=red>'.$arey[$tang.'b'].'</font></td>';
                echo '<td align=right><font color=red>'.$arey[$tang.'t'].'</font></td>';
                $tampil = number_format($bjrlalu[$arey[$tang.'b']], 2);
                if (0 === $tampil) {
                    $tampil = '';
                }

                echo '<td align=right><font color=red>'.$tampil.'</font></td>';
            }
        } else {
            if ('fisik' === $pil) {
                echo '<td align=right>'.number_format($arey[$tang.'k']).'</td>';
                echo '<td align=right>'.number_format($arey[$tang.'j']).'</td>';
                echo '<td align=right>'.number_format($arey[$tang.'r']).'</td>';
                echo '<td align=right>'.number_format($arey[$tang.'h']).'</td>';
            } else {
                echo '<td align=left>'.$arey[$tang.'b'].'</td>';
                echo '<td align=right>'.$arey[$tang.'t'].'</td>';
                $tampil = number_format($bjrlalu[$arey[$tang.'b']], 2);
                if (0 === $tampil) {
                    $tampil = '';
                }

                echo '<td align=right>'.$tampil.'</td>';
            }
        }

        echo '</td>';
        $total[$tang.'k'] += $arey[$tang.'k'];
        $total[$tang.'j'] += $arey[$tang.'j'];
        $total[$tang.'r'] += $arey[$tang.'r'];
        $total[$tang.'h'] += $arey[$tang.'h'];
        $totalk += $arey[$tang.'k'];
        $totalj += $arey[$tang.'j'];
        $totalr += $arey[$tang.'r'];
        $totalh += $arey[$tang.'h'];
        if (0 < $arey[$tang.'j']) {
            $totaltanpanol += $arey[$tang.'j'];
            ++$jumlahtanpanol;
        }
    }
    $rataj = $totaltanpanol / $jumlahtanpanol;
    if ('fisik' === $pil) {
        echo '<td align=right>'.number_format($totalk)."</td>\r\n            <td align=right>".number_format($totalj)."</td><td align=right>".number_format($totalr)."</td> <td align=right>".number_format($totalh).'</td><td align=right>'.number_format($rataj).'</td>';
    }

    echo '</tr>';
}
if ('fisik' === $pil) {
    echo "<tr class='rowcontent'>\r\n        <td colspan=4 align=center>Total</td>";
    $totalj = 0;
    $totalk = 0;
    $totalh = 0;
    $totalr = 0;
    foreach ($tanggal as $tang) {
        echo '<td align=right>'.number_format($total[$tang.'k']).'</td>';
        echo '<td align=right>'.number_format($total[$tang.'j']).'</td>';
        echo '<td align=right>'.number_format($total[$tang.'r']).'</td>';
        echo '<td align=right>'.number_format($total[$tang.'h']).'</td>';
        $totalk += $total[$tang.'k'];
        $totalj += $total[$tang.'j'];
        $totalh += $total[$tang.'h'];
        $totalr += $total[$tang.'r'];
    }
    echo '<td align=right>'.number_format($totalk)."</td>\r\n        <td align=right>".number_format($totalj)."</td><td align=right>".number_format($totalr)."</td> <td align=right>".number_format($totalh).'</td><td></td></tr>';
}

echo "</tbody>\r\n        <tfoot>\r\n        </tfoot>";

?>