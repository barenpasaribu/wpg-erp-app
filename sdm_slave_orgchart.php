<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$parent = strtoupper(trim($_POST['parent']));
$kdStruk = strtoupper(trim($_POST['kdStruk']));
$karyId = trim($_POST['karyId']);
$kdJbtn = $_POST['kdJbtn'];
$detail = $_POST['detail'];
$mailDt = $_POST['mailDt'];
$alokasi = strtoupper(trim($_POST['alokasi']));
$jum = 0;
$exist = false;
$s1 = 'select count(*) from '.$dbname.".sdm_strukturjabatan where kodestruktur='".$kdStruk."' and induk='".$parent."'";
$re1 = mysql_query($s1);
while ($row = mysql_fetch_array($re1)) {
    $jum = $row[0];
}
if (0 < $jum) {
    $exist = true;
}

if (!$exist) {
    $st2 = 'insert into '.$dbname.".sdm_strukturjabatan\r\n\t\t      (`induk`, `kodestruktur`, `karyawanid`, `kodejabatan`, `email`, `kodept`, `lastuser`)\r\n\t\tvalues('".$parent."','".$kdStruk."','".$karyId."','".$kdJbtn."','".$mailDt."','".$alokasi."','".$_SESSION['standard']['username']."')";
} else {
    $st2 = 'update '.$dbname.".sdm_strukturjabatan\r\n\t        set\tkaryawanid='".$karyId."',\r\n\t\t\t\tkodejabatan\t='".$kdJbtn."',\r\n\t\t\t\temail\t='".$mailDt."',\r\n\t\t\t\tkodept\t='".$alokasi."',\r\n\t\t\t\tlastuser\t='".$_SESSION['standard']['username']."'\r\n\t\t\t where kodestruktur\t='".$kdStruk."'\r\n\t\t\t and induk ='".$parent."'";
}

mysql_query($st2);
if (-1 != mysql_affected_rows($conn)) {
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>