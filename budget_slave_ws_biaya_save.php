<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tab = $_POST['tab'];
if ('cekclose' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tutup = 1 and tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."'\r\n        limit 0, 1    \r\n        ";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= 'Budget has been closed.';
    }
    if ('' !== $hkef) {
        echo $hkef;
    }
}

if ('0' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $kodebudget0 = $_POST['kodebudget0'];
    $hkefektif0 = $_POST['hkefektif0'];
    $jumlahpersonel0 = $_POST['jumlahpersonel0'];
    $totalbiaya0 = $_POST['totalbiaya0'];
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tipebudget = '".$tipebudget."' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebudget ='".$kodebudget0."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= $bar->kodebudget.' '.$bar->jumlah.' orang ';
    }
    if ('' !== $hkef) {
        $hkef = 'Data already exist : '.$hkef;
        echo $hkef;
        exit();
    }

    $volume0 = $hkefektif0 * $jumlahpersonel0;
    $str = 'INSERT INTO '.$dbname.".`bgt_budget` (\r\n    `tipebudget` ,\r\n    `tahunbudget` ,\r\n    `kodeorg` ,\r\n    `kodebudget` ,\r\n    `volume` ,\r\n    `satuanv` ,\r\n    `jumlah` ,\r\n    `satuanj` ,\r\n    `rupiah` ,\r\n    `updateby` ,\r\n    `lastupdate` \r\n    )\r\n    VALUES (\r\n    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget0."', '".$volume0."', 'hk' , '".$jumlahpersonel0."', 'orang' , '".$totalbiaya0."', '".$_SESSION['standard']['userid']."',\r\n    CURRENT_TIMESTAMP \r\n    )";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('1' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $kodebudget1 = $_POST['kodebudget1'];
    $totalharga1 = $_POST['totalharga1'];
    $kodebarang1 = $_POST['kodebarang1'];
    $regional1 = $_POST['regional1'];
    $jumlah1 = $_POST['jumlah1'];
    $satuan1 = $_POST['satuan1'];
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tipebudget = '".$tipebudget."' and kodebudget like 'M%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebarang ='".$kodebarang1."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= $bar->kodebarang.' '.$bar->jumlah.' '.$bar->satuanj;
    }
    if ('' !== $hkef) {
        $hkef = 'Data already exist : '.$hkef;
        echo $hkef;
        exit();
    }

    $str = 'INSERT INTO '.$dbname.".`bgt_budget` (\r\n    `tipebudget` ,\r\n    `tahunbudget` ,\r\n    `kodeorg` ,\r\n    `kodebudget` ,\r\n    `regional` ,\r\n    `kodebarang` ,\r\n    `jumlah` ,\r\n    `satuanj` ,\r\n    `rupiah` ,\r\n    `updateby` ,\r\n    `lastupdate` \r\n    )\r\n    VALUES (\r\n    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget1."', '".$regional1."', '".$kodebarang1."', '".$jumlah1."', '".$satuan1."', '".$totalharga1."', '".$_SESSION['standard']['userid']."',\r\n    CURRENT_TIMESTAMP \r\n    )";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('2' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $kodebudget2 = $_POST['kodebudget2'];
    $totalharga2 = $_POST['totalharga2'];
    $kodebarang2 = $_POST['kodebarang2'];
    $regional2 = $_POST['regional2'];
    $jumlah2 = $_POST['jumlah2'];
    $satuan2 = $_POST['satuan2'];
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tipebudget = '".$tipebudget."' and kodebudget like 'TOOL%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and kodebarang ='".$kodebarang2."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= $bar->kodebarang.' '.$bar->jumlah.' '.$bar->satuanj;
    }
    if ('' !== $hkef) {
        $hkef = 'Data already exist : '.$hkef;
        echo $hkef;
        exit();
    }

    $str = 'INSERT INTO '.$dbname.".`bgt_budget` (\r\n    `tipebudget` ,\r\n    `tahunbudget` ,\r\n    `kodeorg` ,\r\n    `kodebudget` ,\r\n    `regional` ,\r\n    `kodebarang` ,\r\n    `jumlah` ,\r\n    `satuanj` ,\r\n    `rupiah` ,\r\n    `updateby` ,\r\n    `lastupdate` \r\n    )\r\n    VALUES (\r\n    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget2."', '".$regional2."', '".$kodebarang2."', '".$jumlah2."', '".$satuan2."', '".$totalharga2."', '".$_SESSION['standard']['userid']."',\r\n    CURRENT_TIMESTAMP \r\n    )";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('3' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $kodebudget3 = $_POST['kodebudget3'];
    $totalbiaya3 = $_POST['totalbiaya3'];
    $kodeakun3 = $_POST['kodeakun3'];
    $str = 'select * from '.$dbname.".bgt_budget\r\n        where tipebudget = '".$tipebudget."' and kodebudget like 'TRANSIT%' and tahunbudget ='".$tahunbudget."' and kodeorg ='".$kodews."' and noakun ='".$kodeakun3."'";
    $res = mysql_query($str);
    $hkef = '';
    while ($bar = mysql_fetch_object($res)) {
        $hkef .= $bar->noakun.' '.$bar->rupiah;
    }
    if ('' !== $hkef) {
        $hkef = 'Data already exist : '.$hkef;
        echo $hkef;
        exit();
    }

    $str = 'INSERT INTO '.$dbname.".`bgt_budget` (\r\n    `tipebudget` ,\r\n    `tahunbudget` ,\r\n    `kodeorg` ,\r\n    `kodebudget` ,\r\n    `noakun` ,\r\n    `rupiah` ,\r\n    `updateby` ,\r\n    `lastupdate` \r\n    )\r\n    VALUES (\r\n    '".$tipebudget."', '".$tahunbudget."', '".$kodews."', '".$kodebudget3."', '".$kodeakun3."', '".$totalbiaya3."', '".$_SESSION['standard']['userid']."',\r\n    CURRENT_TIMESTAMP \r\n    )";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

if ('4' === $tab) {
    $tipebudget = $_POST['tipebudget'];
    $tahunbudget = $_POST['tahunbudget'];
    $kodews = $_POST['kodews'];
    $kunci = $_POST['kunci'];
    $str = 'update '.$dbname.".bgt_budget set tutup='1'\r\n        where kunci ='".$kunci."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal,'.addslashes(mysql_error($conn));
    }
}

?>