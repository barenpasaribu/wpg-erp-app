<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$what = $_POST['what'];
$tahunbudget = $_POST['tahunbudget'];
$regional = $_POST['regional'];
$sumberharga = $_POST['sumberharga'];
$kodebarang = $_POST['kodebarang'];
$hargasatuan = $_POST['hargasatuan'];
$variant = $_POST['variant'];
$hargalalu = $_POST['hargalalu'];
if ('update' === $what) {
    $str = 'UPDATE '.$dbname.".bgt_masterbarang SET `hargasatuan` = '".$hargasatuan."', `sumberharga` = '".$sumberharga."',\r\n        `hargalalu` = '".$hargalalu."', `variant` = '".$variant."', `updateby` = '".$_SESSION['standard']['userid']."', \r\n        `lastupdate` = CURRENT_TIMESTAMP \r\n        WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."' AND `kodebarang` = '".$kodebarang."'";
    if (mysql_query($str)) {
    } else {
        echo ' Gagal9,'.addslashes(mysql_error($conn));
        exit();
    }
} else {
    if ('edit' === $what) {
        $str = 'select * from '.$dbname.'.bgt_masterbarang where '."tahunbudget='".$tahunbudget."' and regional='".$regional."' "."and kodebarang='".$kodebarang."' and hargasatuan=0 and closed=1";
        $adadata = false;
        $res = mysql_query($str);
        while ($bar = mysql_fetch_object($res)) {
            $adadata = true;
        }
        if (true === $adadata) {
            $str = 'UPDATE '.$dbname.".bgt_masterbarang SET `hargasatuan` = '".$hargasatuan."',\r\n        `updateby` = '".$_SESSION['standard']['userid']."', \r\n        `lastupdate` = CURRENT_TIMESTAMP \r\n        WHERE `regional` = '".$regional."' AND `tahunbudget` = '".$tahunbudget."' AND `kodebarang` = '".$kodebarang."'";
        } else {
            if (false === $adadata) {
                $str = 'INSERT INTO '.$dbname.".`bgt_masterbarang` (\r\n        `regional` ,\r\n        `tahunbudget` ,\r\n        `kodebarang` ,\r\n        `hargasatuan` ,\r\n        `updateby` ,\r\n        `closed` ,\r\n        `lastupdate`\r\n        )\r\n        VALUES (\r\n        '".$regional."', '".$tahunbudget."', '".$kodebarang."', '".$hargasatuan."', '".$_SESSION['standard']['userid']."', '".$tutupdata."',\r\n        CURRENT_TIMESTAMP \r\n        )";
            }
        }

        if (mysql_query($str)) {
        } else {
            echo ' Gagal9,'.addslashes(mysql_error($conn));
            exit();
        }
    } else {
        $str = 'DELETE FROM '.$dbname.".bgt_masterbarang WHERE tahunbudget='".$tahunbudget."' AND regional='".$regional."' AND kodebarang='".$kodebarang."'";
        if (mysql_query($str)) {
            $str = 'INSERT INTO '.$dbname.".`bgt_masterbarang` (\r\n    `regional` ,\r\n    `tahunbudget` ,\r\n    `kodebarang` ,\r\n    `hargasatuan` ,\r\n    `sumberharga` ,\r\n    `variant` ,\r\n    `updateby` ,\r\n    `lastupdate` ,\r\n    `hargalalu`\r\n    )\r\n    VALUES (\r\n    '".$regional."', '".$tahunbudget."', '".$kodebarang."', '".$hargasatuan."', '".$sumberharga."' , '".$variant."', '".$_SESSION['standard']['userid']."',\r\n    CURRENT_TIMESTAMP , '".$hargalalu."'\r\n    )";
            if (mysql_query($str)) {
            } else {
                echo ' Gagal,'.addslashes(mysql_error($conn));
            }
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
            exit();
        }
    }
}

?>