<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
$gudang = $_POST['gudang'];
$unit = $_POST['unit'];
if ($gudang) {
    $str = 'select kodeorg, periode from '.$dbname.".setup_periodeakuntansi \r\n      where kodeorg='".$gudang."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $hasil .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
    }
    echo $hasil;
}

if ($unit) {
    $str = 'select induk from '.$dbname.".organisasi\r\n      where kodeorganisasi ='".$unit."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $induk = $bar->induk;
        $hasil .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
    }
    $str = 'select periode from '.$dbname.".setup_periodeakuntansi\r\n      where kodeorg ='".$unit."'";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $hasil .= "<option value='".$bar->periode."'>".$bar->periode.'</option>';
    }
    echo $hasil;
}

?>