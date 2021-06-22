<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$unit = $_GET['unit'];
$tgl1 = $_GET['tgl1'];
$tgl2 = $_GET['tgl2'];
$pil = $_GET['pil'];
$tanggal1 = explode('-', $tgl1);
$tanggal2 = explode('-', $tgl2);
$date1 = $tanggal1[2].'-'.$tanggal1[1].'-'.$tanggal1[0];
$tanggalterakhir = date(t, strtotime($date1));
$tanggal1_1 = date('Y-m-d', mktime(0, 0, 0, $tanggal1[1] - 1, $tanggal1[0], $tanggal1[2]));
$bulankemarin = substr($tanggal1_1, 0, 7);
$sbjrlalu = 'select blok, sum(jjg) as jjg, sum(kgwb) as kgwb from '.$dbname.".kebun_spb_vw\r\n        where notiket IS NOT NULL and tanggal like '".$bulankemarin."%'\r\n        group by blok";
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

$sdakar = 'select karyawanid, namakaryawan, tipekaryawan, subbagian from '.$dbname.'.datakaryawan';
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
    $str = "select a.tanggal,GROUP_CONCAT(a.tahuntanam SEPARATOR ' ') as tahuntanam,a.unit,GROUP_CONCAT(a.kodeorg SEPARATOR ' ') as kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid, sum(a.brondolan) as brd   from ".$dbname.".kebun_prestasi_vw a\r\n        left join ".$dbname.".organisasi c\r\n        on substr(a.kodeorg,1,4)=c.kodeorganisasi\r\n        where c.induk = '".$pt."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal,a.karyawanid';
} else {
    $str = "select a.tanggal,GROUP_CONCAT(a.tahuntanam SEPARATOR ' ') as tahuntanam,a.unit,GROUP_CONCAT(a.kodeorg SEPARATOR ' ') as kodeorg,sum(a.hasilkerja) as jjg,sum(a.hasilkerjakg) as berat,sum(a.upahkerja) as upah,\r\n        sum(a.upahpremi) as premi,sum(a.rupiahpenalty) as penalty,sum(a.luaspanen) as luaspanen, a.karyawanid, sum(a.brondolan) as brd   from ".$dbname.".kebun_prestasi_vw a\r\n        where unit = '".$unit."'  and a.tanggal between ".tanggalsystem($tgl1).' and '.tanggalsystem($tgl2).' group by a.tanggal, a.karyawanid';
}

$jumlahhari = count($tanggal);
$res = mysql_query($str);
$dzArr = [];
if (mysql_num_rows($res) < 1) {
    $jukol = $jumlahhari * 3 + 5;
    echo $_SESSION['lang']['tidakditemukan'];
    exit();
}

while ($bar = mysql_fetch_object($res)) {
    $dzArr[$bar->karyawanid][$bar->tanggal] = $bar->tanggal;
    $dzArr[$bar->karyawanid]['karyawanid'] = $bar->karyawanid;
    $dzArr[$bar->karyawanid][$bar->tanggal.'j'] = $bar->jjg;
    $dzArr[$bar->karyawanid][$bar->tanggal.'k'] = $bar->berat;
    $dzArr[$bar->karyawanid][$bar->tanggal.'r'] = $bar->brd;
    $dzArr[$bar->karyawanid][$bar->tanggal.'h'] = $bar->luaspanen;
    $dzArr[$bar->karyawanid][$bar->tanggal.'b'] = $bar->kodeorg;
    $dzArr[$bar->karyawanid][$bar->tanggal.'t'] = $bar->tahuntanam;
}
if (!empty($dzArr)) {
    foreach ($dzArr as $c => $key) {
        $sort_kodeorg[] = $key['karyawanid'];
    }
    array_multisort($sort_kodeorg, SORT_ASC, $dzArr);
}

$stream = $_SESSION['lang']['laporanpanen'].' '.$pt.' '.$unit.' per '.$_SESSION['lang']['orang'].' '.$tgl1.' - '.$tgl2;
$stream .= '<table border=1 cellpading=1>';
$stream .= "<thead>\r\n        <tr>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>No.</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['namakaryawan']."</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['subbagian']."</td>\r\n            <td bgcolor=#DEDEDE rowspan=2 align=center>".$_SESSION['lang']['tipekaryawan'].'</td>';
foreach ($tanggal as $tang) {
    $ting = explode('-', $tang);
    $qwe = date('D', strtotime($tang));
    if ('fisik' === $pil) {
        $kolspan = 4;
    } else {
        $kolspan = 4;
    }

    $stream .= '<td bgcolor=#DEDEDE colspan='.$kolspan.' align=center>';
    if ('Sun' === $qwe) {
        $stream .= '<font color=red>'.$ting[2].'</font>';
    } else {
        $stream .= $ting[2];
    }

    $stream .= '</td>';
}
if ('fisik' === $pil) {
    $stream .= '<td bgcolor=#DEDEDE colspan=4 align=center>Total</td><td bgcolor=#DEDEDE align=center>Rata2</td>';
}

$stream .= '</tr><tr>';
foreach ($tanggal as $tang) {
    if ('fisik' === $pil) {
        $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg']."</td>\r\n                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>\r\n <td bgcolor=#DEDEDE align=center>Brd</td> <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['ha'].'</td>';
    } else {
        $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['blok']."</td>\r\n                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tahuntanam']."</td>\r\n                  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['bjr'].' Akt '.$_SESSION['lang']['bulanlalu'].'</td>';
    }
}
if ('fisik' === $pil) {
    $stream .= '<td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg']."</td>\r\n        <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kg']."</td>\r\n <td bgcolor=#DEDEDE align=center>Brd</td> <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['ha'].'</td><td bgcolor=#DEDEDE align=center>'.$_SESSION['lang']['jjg'].'</td>';
}

$stream .= "</thead>\r\n\t<tbody>";
$no = 0;
 $rrt=0;
foreach ($dzArr as $arey) {
    ++$no;
    $stream .= "<tr class='rowcontent'>\r\n            <td align=center>".$no."</td>\r\n            <td align=left>".$dakar[$arey['karyawanid']]['namakaryawan']."</td>\r\n            <td align=left>".$dakar[$arey['karyawanid']]['subbagian']."</td>\r\n            <td align=center>".$tikar[$dakar[$arey['karyawanid']]['tipekaryawan']].'</td>';
    $totalj = 0;
    $totalk = 0;
    $totalh = 0;
    $totalr =0;
    $no1=0;
    $totaltanpanol = 0;
    $jumlahtanpanol = 0;

    foreach ($tanggal as $tang) {
        $no1++;
        $qwe = date('D', strtotime($tang));
        if ('Sun' === $qwe) {
            if ('fisik' === $pil) {
                $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'j']).'</font></td>';
                $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'k']).'</font></td>';
                $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'r']).'</font></td>';
                $stream .= '<td align=right><font color=red>'.number_format($arey[$tang.'h']).'</font></td>';
            } else {
                $stream .= '<td align=left><font color=red>'.$arey[$tang.'b'].'</font></td>';
                $stream .= '<td align=left><font color=red>'.$arey[$tang.'t'].'</font></td>';
                $tampil = number_format($bjrlalu[$arey[$tang.'b']], 2);
                if (0 === $tampil) {
                    $tampil = '';
                }

                $stream .= '<td align=right><font color=red>'.$tampil.'</font></td>';
            }
        } else {
            if ('fisik' === $pil) {
                $stream .= '<td align=right>'.number_format($arey[$tang.'j']).'</td>';
                $stream .= '<td align=right>'.number_format($arey[$tang.'k']).'</td>';
                
                $stream .= '<td align=right>'.number_format($arey[$tang.'r']).'</td>';
                $stream .= '<td align=right>'.number_format($arey[$tang.'h']).'</td>';
              
              
            } else {
                $stream .= '<td align=left>'.$arey[$tang.'b'].'</td>';
                $stream .= '<td align=left>'.$arey[$tang.'t'].'</td>';
                $tampil = number_format($bjrlalu[$arey[$tang.'b']], 2);
                if (0 === $tampil) {
                    $tampil = '';
                }

                $stream .= '<td align=right>'.$tampil.'</td>';
            }

        }

        $stream .= '</td>';
        $total[$tang.'j'] += $arey[$tang.'j'];
        $total[$tang.'k'] += $arey[$tang.'k'];
        $total[$tang.'h'] += $arey[$tang.'h'];
        $total[$tang.'r'] += $arey[$tang.'r'];
        $totalj += $arey[$tang.'j'];
        $totalk += $arey[$tang.'k'];
        $totalh += $arey[$tang.'h'];
        $totalr += $arey[$tang.'r'];
        $totalrn += $arey[$tang.'r'];
        if (0 < $arey[$tang.'j']) {
            $totaltanpanol += $arey[$tang.'j'];
            ++$jumlahtanpanol;
        }
    }
    $rataj = $totaltanpanol / $jumlahtanpanol;
    if ('fisik' === $pil) {
        $stream .= '<td align=right>'.number_format($totalj)."</td>\r\n            <td align=right>".number_format($totalk)."</td>\r\n            <td align=right>".$totalr."</td><td align=right>".number_format($totalh).'</td><td align=right>'.number_format($rataj).'</td>';
    }

    $stream .= '</tr>';
}
if ('fisik' === $pil) {
    $stream .= "<tr class='rowcontent'>\r\n        <td colspan=4 align=center>Total</td>";
    $totalj = 0;
    $totalk = 0;
    $totalh = 0;
    $totalr = 0;
    foreach ($tanggal as $tang) {
        $stream .= '<td align=right>'.number_format($total[$tang.'j']).'</td>';
        $stream .= '<td align=right>'.number_format($total[$tang.'k']).'</td>';
        $stream .= '<td align=right>'.number_format($total[$tang.'r']).'</td>';
        $stream .= '<td align=right>'.number_format($total[$tang.'h']).'</td>';
        $totalj += $total[$tang.'j'];
        $totalk += $total[$tang.'k'];
        $totalr += $total[$tang.'r'];
        $totalh += $total[$tang.'h'];
    }
    $stream .= '<td align=right>'.number_format($totalj)."</td>\r\n        <td align=right>".number_format($totalk)."</td>\r\n   <td align=right>".number_format($totalr)."</td>     <td align=right>".number_format($totalh).'</td><td></td></tr>';
}

$stream .= "</tbody>\r\n        <tfoot>\r\n        </tfoot>";
$stream .= '</table>Print Time:'.date('Y-m-d H:i:s').'<br>By:'.$_SESSION['empl']['name'];
$tglSkrg = date('Ymd');
$nop_ = 'LaporanPanenOrang'.$pt.'_'.$unit.'_'.$tgl1.'_'.$pil;
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
echo $stream;

?>