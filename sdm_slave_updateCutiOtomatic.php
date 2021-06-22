<?php



require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$str1 = 'select karyawanid,namakaryawan,tanggalmasuk,lokasitugas,kodegolongan from '.$dbname.".datakaryawan\r\n\t       where  tanggalmasuk<>'0000-00-00'  and tanggalmasuk<".date('Ymd')."\r\n                          and tanggalmasuk like '%".date('m-d')."'\r\n                          and (tanggalkeluar is NULL or tanggalkeluar>".date('Ymd').') and tipekaryawan in(0,1,2,3)';
$res1 = mysql_query($str1);
while ($bar1 = mysql_fetch_object($res1)) {
    $x = readTextFile('config/jumlahcuti.lst');
    if (0 < (int) $x) {
        $hakcuti = $x;
    } else {
        $hakcuti = 12;
    }

    if (5 < substr($bar1->kodegolongan, 0, 1)) {
        $hakcuti = 24;
    }

    $tgl = substr(str_replace('-', '', $bar1->tanggalmasuk), 4, 4);
    $dari = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), date('Y'));
    $dari = date('Ymd', $dari);
    $sampai = mktime(0, 0, 0, substr($tgl, 0, 2), substr($tgl, 2, 2), date('Y') + 1);
    $sampai = date('Ymd', $sampai);
    $d = substr(str_replace('-', '', $bar1->tanggalmasuk), 0, 4);
    $str = 'select sisa from '.$dbname.'.sdm_cutiht where karyawanid='.$bar1->karyawanid." \r\n                       and periodecuti>".(date('Y') - 2).' order by periodecuti desc limit 1';
    $resx = mysql_query($str);
    $sisalalu = 0;
    while ($barx = mysql_fetch_object($resx)) {
        $sisalalu = $barx->sisa;
    }
    $str = 'select * from '.$dbname.'.sdm_cutiht where karyawanid='.$bar1->karyawanid." \r\n                       and periodecuti=".date('Y').' order by periodecuti desc limit 1';
    $resy = mysql_query($str);
    if (0 < mysql_num_rows($resy)) {
    } else {
        $saldo = $hakcuti;
        $strx = 'select sum(jumlahcuti) as diambil from '.$dbname.".sdm_cutidt\r\n                                    where karyawanid=".$bar1->karyawanid."\r\n                                     and  daritanggal >=".$dari.' and daritanggal<='.$sampai;
        $diambil = 0;
        $resx = mysql_query($strx);
        while ($barx = mysql_fetch_object($resx)) {
            $diambil = $barx->diambil;
            if ('' == $diambil) {
                $diambil = 0;
            }
        }
        $saldo = $saldo - $diambil;
        $str = 'insert into '.$dbname.".sdm_cutiht(kodeorg, karyawanid, periodecuti, keterangan, dari, sampai, hakcuti, diambil, sisa)\r\n                           values('".$bar1->lokasitugas."',".$bar1->karyawanid.','.date('Y').",'',".$dari.','.$sampai.','.$hakcuti.',0,'.$saldo.')';
        mysql_query($str);
    }
}

?>