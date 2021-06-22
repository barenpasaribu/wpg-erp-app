<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/zLib.php';
$organisasi = $_POST['organisasi'];
$regional = $_POST['regional'];
$str = 'select * from '.$dbname.".bgt_regional_assignment \r\n    where kodeunit='".$organisasi."' \r\n            limit 0,1";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $sudahtutup = '1';
    $pesan = $bar->kodeunit.' - '.$bar->regional;
}
if ('1' === $sudahtutup) {
    echo 'data sudah ada: '.$pesan;
    exit();
}

?>