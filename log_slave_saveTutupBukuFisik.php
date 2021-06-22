<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$gudang = $_POST['gudang'];
$user = $_SESSION['standard']['userid'];
$period = $_POST['periode'];
$awal = $_POST['tanggalmulai'];
$akhir = $_POST['tanggalsampai'];
$x = str_replace('-', '', $period);
$x = str_replace('/', '', $x);
$x = mktime(0, 0, 0, (int) (substr($x, 4, 2)) + 1, 15, substr($x, 0, 4));
$prefper = $period;
$period = date('Y-m', $x);
$str = 'select distinct(periode)  from `'.$dbname."`.`log_5saldobulanan` where periode='".$period."' and kodegudang='".$gudang."'";
$res = mysql_query($str);
if (0 < mysql_num_rows($res)) {
    exit('Error: gudang '.$gudang.' sudah tutup buku pada periode tersebut ('.$prefper.'), mohon hubungi IT');
}

$str = 'select induk from '.$dbname.".organisasi where kodeorganisasi='".substr($gudang, 0, 4)."'";
$res = mysql_query($str);
$pt = '';
while ($bar = mysql_fetch_object($res)) {
    $pt = $bar->induk;
}
if ('' == $pt) {
    exit(' Error: Gudang belum memiliki PT');
}

$str = 'select count(tanggal) as tgl from '.$dbname.".log_transaksiht\r\n      where kodegudang='".$gudang."' and tanggal>=".$awal.' and tanggal<='.$akhir."\r\n      and post=0";
$res = mysql_query($str);
$jlhNotPost = 0;
while ($bar = mysql_fetch_object($res)) {
    $jlhNotPost = $bar->tgl;
}
if (0 < $jlhNotPost) {
    exit(' Error: '.$_SESSION['lang']['belumposting'].' > 0');
}

$str = "select kodebarang,saldoakhirqty,nilaisaldoakhir,hargarata \r\n            from ".$dbname.".log_5saldobulanan\r\n            where kodeorg='".$pt."' and kodegudang='".$gudang."' and periode='".$prefper."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $str1 = 'INSERT INTO `'.$dbname."`.`log_5saldobulanan`(`kodeorg`,`kodebarang`,`saldoakhirqty`,`hargarata`,`lastuser`,`periode`,`nilaisaldoakhir`,`kodegudang`,`qtymasuk`,`qtykeluar`,`qtymasukxharga`,`qtykeluarxharga`,`saldoawalqty`,`hargaratasaldoawal`,`nilaisaldoawal`) VALUES ('".$pt."',\r\n'".trim($bar->kodebarang)."',\r\n".$bar->saldoakhirqty.','.$bar->hargarata.','.$user.",'".$period."',".$bar->nilaisaldoakhir.",'".$gudang."',0,0,0,0,".$bar->saldoakhirqty.','.$bar->hargarata.','.$bar->nilaisaldoakhir.')';
    if (!mysql_query($str1)) {
        $err = addslashes(mysql_error($conn)).'('.$str1.')';

        break;
    }
}
if ('' == $err) {
    $nextPeriod = $period;
    $tg = mktime(0, 0, 0, substr($akhir, 5, 2), (int) (substr($akhir, 8, 2) + 1), (int) (substr($prefper, 0, 4)));
    $nextAwal = date('Ymd', $tg);
    $tg = mktime(0, 0, 0, (int) (substr($akhir, 5, 2)) + 1, date('t', $tg), (int) (substr($prefper, 0, 4)));
    $nextAkhir = date('Ymd', $tg);
    $str = 'update '.$dbname.".setup_periodeakuntansi set tutupbuku=1\r\n          where kodeorg='".$gudang."' and periode='".$prefper."'";
    if (mysql_query($str)) {
        $str = 'INSERT INTO `'.$dbname."`.`setup_periodeakuntansi`\r\n            (`kodeorg`,\r\n            `periode`,\r\n            `tanggalmulai`,\r\n            `tanggalsampai`,\r\n            `tutupbuku`)\r\n            VALUES\r\n            ('".$gudang."',\r\n                '".$nextPeriod."',\r\n                ".$nextAwal.",\r\n                ".$nextAkhir.",\r\n                0\r\n                )";
        if (mysql_query($str)) {
            $str = 'delete from '.$dbname.".keu_setup_watu_tutup where periode='".$prefper."'. and kodeorg='".$gudang."'";
            mysql_query($str);
            $str = 'insert into '.$dbname.".keu_setup_watu_tutup(kodeorg,periode,username) values(\r\n                  '".$gudang."','".$prefper."','".$_SESSION['standard']['username']."')";
            mysql_query($str);
        } else {
            $err = addslashes(mysql_error($conn)).'('.$str.')';
            $str = 'update '.$dbname.".setup_periodeakuntansi set tutupbuku=0\r\n          where kodeorg='".$gudang."' and periode='".$period."'";
            mysql_query($str);
            $str = 'delete from '.$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
            mysql_query($str);
            exit('Error: data '.$err);
        }
    } else {
        $err = addslashes(mysql_error($conn)).'('.$str.')';
        $str = 'delete from '.$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
        mysql_query($str);
        exit('Error: data '.$err);
    }
} else {
    $str = 'delete from '.$dbname.".log_5saldobulanan where kodeorg='".$pt."' and kodegudang='".$gudang."'  and periode='".$period."'";
    mysql_query($str);
    exit('Error: data '.$err);
}

?>