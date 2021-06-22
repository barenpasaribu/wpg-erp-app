<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$periode = $_POST['periode'];
$kodeorg = $_POST['kodeorg'];
$rs = $_POST['rs'];
$method = $_POST['method'];
$optJabatan = makeOption($dbname, 'sdm_5jabatan', 'kodejabatan,namajabatan');
$optNmRwt = makeOption($dbname, 'sdm_5jenisbiayapengobatan', 'kode,nama');
if (1 == $method) {
    $str = 'select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, c.lokasitugas as loktug,c.kodejabatan, nama from '.$dbname.".sdm_pengobatanht a left join\r\n        ".$dbname.".sdm_5rs b on a.rs=b.id \r\n        left join ".$dbname.".datakaryawan c\r\n        on a.karyawanid=c.karyawanid\r\n        left join ".$dbname.".sdm_5diagnosa d\r\n        on a.diagnosa=d.id\r\n        left join ".$dbname.".sdm_karyawankeluarga f\r\n        on a.ygsakit=f.nomor\r\n        where a.periode like '".$periode."%'\r\n        and c.lokasitugas like '".$kodeorg."%'\r\n        and b.namars like '".$rs."%'\r\n        order by a.updatetime desc, a.tanggal desc";
    $res = mysql_query($str);
    $no = 0;
    while ($bar = mysql_fetch_object($res)) {
        ++$no;
        $pasien = '';
        $stru = 'select hubungankeluarga from '.$dbname.".sdm_karyawankeluarga \r\n              where nomor=".$bar->ygsakit;
        $resu = mysql_query($stru);
        while ($baru = mysql_fetch_object($resu)) {
            $pasien = $baru->hubungankeluarga;
        }
        if ('' == $pasien) {
            $pasien = 'AsIs';
        }

        echo "<tr class=rowcontent>\r\n            <td>&nbsp <img src=images/zoom.png title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)></td>\r\n            <td>".$no."</td>\r\n            <td>".$bar->notransaksi."</td>\r\n            <td>".substr($bar->periode, 5, 2).'-'.substr($bar->periode, 0, 4)."</td>\r\n            <td>".tanggalnormal($bar->tanggal)."</td>\r\n            <td>".$bar->loktug."</td>\r\n            <td>".$bar->namakaryawan."</td>\r\n            <td>".$optJabatan[$bar->kodejabatan]."</td>\r\n            <td>".$pasien."</td>\r\n            <td>".$bar->nama."</td>\r\n            <td>".$bar->namars.'['.$bar->kota.']'."</td>\r\n            <td>".$bar->kodebiaya."</td>\r\n            <td align=right>".number_format($bar->totalklaim, 2, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->jlhbayar, 2, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanperusahaan, 2, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebankaryawan, 2, '.', ',')."</td>\r\n            <td align=right>".number_format($bar->bebanjamsostek, 2, '.', ',')."</td>     \r\n            <td>".$bar->ketdiag."</td>\r\n            <td>".$bar->keterangan."</td>\r\n        </tr>";
    }
}

if (2 == $method) {
    $str1 = 'select a.diagnosa, count(*) as kali,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a \r\n              left join ".$dbname.".sdm_5diagnosa d\r\n              on a.diagnosa=d.id \r\n        left join ".$dbname.".datakaryawan c\r\n        on a.karyawanid=c.karyawanid\r\n              \r\n              where a.periode like '".$periode."%'\r\n              and c.lokasitugas like '".$kodeorg."%'\r\n            group by a.diagnosa order by kali desc\r\n        ";
    $res1 = mysql_query($str1);
    $no = 0;
    while ($bar1 = mysql_fetch_object($res1)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar1->ketdiag."</td>\r\n            <td align=right>".$bar1->kali."</td>\r\n        </tr>";
    }
}

if (3 == $method) {
    $str2 = "select a.karyawanid, sum(totalklaim) as klaim,d.namakaryawan,d.lokasitugas,d.kodegolongan,\r\n    COALESCE(ROUND(DATEDIFF('".date('Y-m-d')."',d.tanggallahir)/365.25,1),0) as umur,kodebiaya\r\n    from ".$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".datakaryawan d\r\n\t  on a.karyawanid=d.karyawanid \r\n          left join ".$dbname.".datakaryawan e\r\n\t  on a.karyawanid=e.karyawanid\r\n\t  where a.periode like '".$periode."%'\r\n\t  and e.lokasitugas='".$kodeorg."'\r\n        group by a.karyawanid,kodebiaya order by klaim desc";
    $res2 = mysql_query($str2);
    while ($bar2 = mysql_fetch_object($res2)) {
        $kdBiaya[$bar2->kodebiaya] = $bar2->kodebiaya;
        $idKary[$bar2->karyawanid] = $bar2->karyawanid;
        $jmlhRp[$bar2->karyawanid.$bar2->kodebiaya] = $bar2->klaim;
        $umurKary[$bar2->karyawanid] = $bar2->umur;
        $nmKary[$bar2->karyawanid] = $bar2->namakaryawan;
        $kdGol[$bar2->karyawanid] = $bar2->kodegolongan;
        $lksiKary[$bar2->karyawanid] = $bar2->lokasitugas;
    }
    $no = 0;
    echo "<table class=sortable cellspacing=1 border=0>\r\n    <thead>\r\n    <tr class=rowheader>\r\n        <td>Rank</td>\r\n        <td>".$_SESSION['lang']['namakaryawan']."</td>\r\n        <td>".$_SESSION['lang']['kodegolongan']."</td>\r\n        <td>".$_SESSION['lang']['umur']."</td>\r\n        <td>".$_SESSION['lang']['lokasitugas'].'</td>';
    foreach ($kdBiaya as $lsBy) {
        echo '<td>'.$optNmRwt[$lsBy].'</td>';
    }
    echo '<td>'.$_SESSION['lang']['total']."</td>\r\n        <td>*</td>\r\n    </tr>\r\n    </thead>\r\n    <tbody>";
    foreach ($idKary as $lstKary) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$nmKary[$lstKary]."</td>\r\n            <td>".$kdGol[$lstKary]."</td>\r\n            <td>".$umurKary[$lstKary]."(Yrs)</td>\r\n            <td>".$lksiKary[$lstKary].'(Yrs)</td>';
        foreach ($kdBiaya as $lsBy) {
            echo '<td align=right>'.number_format($jmlhRp[$lstKary.$lsBy]).'</td>';
            $total[$lstKary] += $jmlhRp[$lstKary.$lsBy];
            $totPerBy[$lsBy] += $jmlhRp[$lstKary.$lsBy];
        }
        echo '<td align=right>'.number_format($total[$lstKary])."</td>\r\n               <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPerorang('".$lstKary."',event)></td>\r\n            </tr>";
    }
    echo "<tr class=rowcontent>\r\n              <td></td>\r\n               <td colspan=3 align=right>".$_SESSION['lang']['total'].'</td>';
    foreach ($kdBiaya as $lsBy) {
        echo '<td align=right>'.number_format($totPerBy[$lsBy]).'</td>';
        $totBy += $totPerBy[$lsBy];
    }
    echo '<td>'.number_format($totBy)."</td>\r\n                <td></td></tr></tbody>\r\n    <tfoot>\r\n    </tfoot>\r\n    </table>";
}

if (4 == $method) {
    $str3 = 'select a.diagnosa, sum(jlhbayar) as klaim,d.diagnosa as ketdiag from '.$dbname.".sdm_pengobatanht a \r\n\t  left join ".$dbname.".sdm_5diagnosa d\r\n\t  on a.diagnosa=d.id \r\n        left join ".$dbname.".datakaryawan c\r\n        on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n              and c.lokasitugas like '".$kodeorg."%'\r\n        group by a.diagnosa order by klaim desc\r\n    ";
    $res3 = mysql_query($str3);
    $no = 0;
    while ($bar3 = mysql_fetch_object($res3)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->ketdiag."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n        </tr>";
    }
}

if (5 == $method) {
    $str3 = 'select  sum(a.jlhbayar) as klaim,a.periode from '.$dbname.".sdm_pengobatanht a \r\n        left join ".$dbname.".datakaryawan c\r\n        on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n              and c.lokasitugas like '".$kodeorg."%'\r\n        group by periode order by periode\r\n    ";
    $res3 = mysql_query($str3);
    $no = 0;
    while ($bar3 = mysql_fetch_object($res3)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->periode."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n        </tr>";
    }
}

if (6 == $method) {
    if ('' == $_POST['karyawanid']) {
        $str3 = "select  sum(jasars) as rs, \r\n               sum(jasadr) as dr, sum(jasalab) as lab, \r\n               sum(byobat) as obat, \r\n               sum(bypendaftaran) administrasi, \r\n               a.periode, sum(a.totalklaim) as klaim,sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a \r\n               left join ".$dbname.".datakaryawan c\r\n               on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n             group by periode order by periode";
    } else {
        $str3 = "select  sum(jasars) as rs, \r\n               sum(jasadr) as dr, sum(jasalab) as lab, \r\n               sum(byobat) as obat, \r\n               sum(bypendaftaran) administrasi, \r\n               a.periode, sum(a.totalklaim) as klaim,sum(a.jlhbayar) as bayar from ".$dbname.".sdm_pengobatanht a \r\n               left join ".$dbname.".datakaryawan c\r\n               on a.karyawanid=c.karyawanid\r\n              where a.periode like '".$periode."%'\r\n               and c.karyawanid=".$_POST['karyawanid']."\r\n             group by periode order by periode";
    }

    $res3 = mysql_query($str3);
    $no = 0;
    $trs = 0;
    $tdr = 0;
    $tlb = 0;
    $tob = 0;
    $tad = 0;
    $ttl = 0;
    while ($bar3 = mysql_fetch_object($res3)) {
        ++$no;
        echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$bar3->periode."</td>\r\n            <td align=right>".number_format($bar3->rs)."</td>\r\n            <td align=right>".number_format($bar3->dr)."</td>\r\n            <td align=right>".number_format($bar3->lab)."</td>\r\n            <td align=right>".number_format($bar3->obat)."</td>\r\n            <td align=right>".number_format($bar3->administrasi)."</td>\r\n            <td align=right>".number_format($bar3->klaim)."</td>\r\n            <td align=right>".number_format($bar3->bayar)."</td>    \r\n        </tr>";
        $trs += $bar3->rs;
        $tdr += $bar3->dr;
        $tlb += $bar3->lab;
        $tob += $bar3->obat;
        $tad += $bar3->administrasi;
        $ttl += $bar3->klaim;
        $byr += $bar3->bayar;
    }
    echo "<tr class=rowcontent>\r\n            <td></td>\r\n            <td>".$_SESSION['lang']['total']."</td>\r\n            <td align=right>".number_format($trs)."</td>\r\n            <td align=right>".number_format($tdr)."</td>\r\n            <td align=right>".number_format($tlb)."</td>\r\n            <td align=right>".number_format($tob)."</td>\r\n            <td align=right>".number_format($tad)."</td>\r\n            <td align=right>".number_format($ttl)."</td>\r\n             <td align=right>".number_format($byr)."</td>   \r\n        </tr>";
}

if (7 == $method) {
    $str3 = 'select  sum(a.jlhbayar) as klaim,a.periode,a.kodebiaya,c.nama from '.$dbname.".sdm_pengobatanht a \r\n        left join ".$dbname.".sdm_5jenisbiayapengobatan c\r\n        on a.kodebiaya=c.kode\r\n        left join ".$dbname.".datakaryawan b \r\n        on a.karyawanid=b.karyawanid\r\n              where a.periode like '".$periode."%'\r\n              and b.lokasitugas like '".$kodeorg."%'\r\n        group by kodebiaya,periode order by periode\r\n    ";
    $res3 = mysql_query($str3);
    $no = 0;
    while ($bar3 = mysql_fetch_object($res3)) {
        $kode[$bar3->kodebiaya][$bar3->periode] = $bar3->klaim;
        $kodex[$bar3->kodebiaya]['nama'] = $bar3->nama;
    }
    if (0 < count($kodex)) {
        foreach ($kodex as $key => $val) {
            ++$no;
            $total = $kode[$key][$periode.'-12'] + $kode[$key][$periode.'-11'] + $kode[$key][$periode.'-10'] + $kode[$key][$periode.'-09'] + $kode[$key][$periode.'-08'] + $kode[$key][$periode.'-07'] + $kode[$key][$periode.'-06'] + $kode[$key][$periode.'-05'] + $kode[$key][$periode.'-04'] + $kode[$key][$periode.'-03'] + $kode[$key][$periode.'-02'] + $kode[$key][$periode.'-01'];
            $gt += $total;
            echo "<tr class=rowcontent>\r\n            <td>".$no."</td>\r\n            <td>".$kodeorg."</td>\r\n            <td>".$periode."</td>    \r\n            <td>".$kodex[$key]['nama']."</td>                \r\n            <td align=right>".number_format($kode[$key][$periode.'-01'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-02'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-03'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-04'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-05'])."</td> \r\n            <td align=right>".number_format($kode[$key][$periode.'-06'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-07'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-08'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-09'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-10'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-11'])."</td>\r\n            <td align=right>".number_format($kode[$key][$periode.'-12'])."</td>\r\n            <td align=right>".number_format($total)."</td>    \r\n        </tr>";
            $t01 += $kode[$key][$periode.'-01'];
            $t02 += $kode[$key][$periode.'-02'];
            $t03 += $kode[$key][$periode.'-03'];
            $t04 += $kode[$key][$periode.'-04'];
            $t05 += $kode[$key][$periode.'-05'];
            $t06 += $kode[$key][$periode.'-06'];
            $t07 += $kode[$key][$periode.'-07'];
            $t08 += $kode[$key][$periode.'-08'];
            $t09 += $kode[$key][$periode.'-09'];
            $t10 += $kode[$key][$periode.'-10'];
            $t11 += $kode[$key][$periode.'-11'];
            $t12 += $kode[$key][$periode.'-12'];
        }
    }

    echo "<tr class=rowcontent>\r\n            <td colspan=4>Total</td>                \r\n            <td align=right>".number_format($t01)."</td>\r\n            <td align=right>".number_format($t02)."</td>\r\n            <td align=right>".number_format($t03)."</td>\r\n             <td align=right>".number_format($t04)."</td>\r\n             <td align=right>".number_format($t05)."</td>\r\n             <td align=right>".number_format($t06)."</td>\r\n             <td align=right>".number_format($t07)."</td>\r\n             <td align=right>".number_format($t08)."</td>\r\n             <td align=right>".number_format($t09)."</td>\r\n             <td align=right>".number_format($t10)."</td>\r\n             <td align=right>".number_format($t11)."</td>\r\n             <td align=right>".number_format($t12)."</td>     \r\n            <td align=right>".number_format($gt)."</td>    \r\n        </tr>";
}

?>