<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');

$barang = makeOption($dbname,'vhc_5master','kodevhc,kodebarang',"kodevhc='".$_POST['kodevhc']."'");
$harga = makeOption($dbname,'log_5masterbaranganggaran','kodebarang,hargasatuan',"kodebarang='".end($barang)."'");
$res['vhc'] = end($barang);
if(end($harga)=='') {
    $res['harga'] = 0;
} else {
    $res['harga'] = end($harga);
}
?>