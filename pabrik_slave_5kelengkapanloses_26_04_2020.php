<?php
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\r\n";
$id = $_POST['id'];
$kodeorg = $_POST['kodeorg'];
$produk = $_POST['produk'];
$namaitem = $_POST['namaitem'];
$standard = $_POST['standard'];
$satuan = $_POST['satuan'];
$method = $_POST['method'];
switch ($method) {
    case 'insert':
        $i = 'insert into '.$dbname.".pabrik_5kelengkapanloses(kodeorg,produk,namaitem,standard,satuan,updateby) values ('".$kodeorg."','".$produk."','".$namaitem."','".$standard."','".$satuan."','".$_SESSION['standard']['userid']."')";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'update':
        $i = 'update '.$dbname.".pabrik_5kelengkapanloses set kodeorg='".$kodeorg."',produk='".$produk."',\r\n                    namaitem='".$namaitem."',standard='".$standard."',satuan='".$satuan."',updateby='".$_SESSION['standard']['userid']."'\r\n                        where id='".$id."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
    case 'delete':
        $i = 'DELETE FROM '.$dbname.".pabrik_5kelengkapanloses WHERE id='".$id."'";
        if (mysql_query($i)) {
            echo '';
        } else {
            echo ' Gagal,'.addslashes(mysql_error($conn));
        }

        break;
}

?>