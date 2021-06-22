<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = trim($_POST['kodeorg']);
$what = $_POST['what'];
if ('adadata' === $what) {
    $str = 'select * from '.$dbname.".bgt_upah where tahunbudget='".$tahunbudget."' and kodeorg = '".$kodeorg."' limit 0,1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $adadata = '1';
    }
    if ('1' === $adadata) {
        echo 'Data already exist on the same period, replace..?';
        exit();
    }
}

if ('closing' === $what) {
    $str = 'select * from '.$dbname.".bgt_upah where tahunbudget='".$tahunbudget."' and kodeorg = '".$kodeorg." and closed = 1 limit 0,1";
    $res = mysql_query($str);
    while ($bar = mysql_fetch_object($res)) {
        $sudahtutup = '1';
    }
    if ('1' === $sudahtutup) {
        echo 'Budget for this period has been closed';
        exit();
    }
}

?>