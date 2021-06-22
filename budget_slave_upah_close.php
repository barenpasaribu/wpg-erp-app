<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tahunbudget = $_POST['tahunbudget'];
$kodeorg = $_POST['kodeorg'];
$kodegolongan = $_POST['kodegolongan'];
$kodeupah = $_POST['kodeupah'];
$str = 'UPDATE '.$dbname.".`bgt_upah` \r\nSET `closed` = '1'\r\nWHERE `kodeorg` = '".$kodeorg."' AND `tahunbudget` = '".$tahunbudget."' AND `golongan` = '".$kodegolongan."'";
if (mysql_query($str)) {
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>