<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$blok = $_POST['blok'];
$norumah = $_POST['norumah'];
$str = " select a.karyawanid,b.namakaryawan,b.lokasitugas,b.bagian,b.jeniskelamin \r\n       from ".$dbname.".sdm_penghunirumah a \r\n       left join ".$dbname.".datakaryawan b\r\n\t   on a.karyawanid=b.karyawanid\r\n\t   where a.kodeorg='".$kodeorg."' and a.blok='".$blok."'\r\n\t   and a.norumah='".$norumah."'";
$res = mysql_query($str);
$no = 0;
echo ''.$_SESSION['lang']['kodeorg'].': '.$kodeorg."<br>\r\n     ".$_SESSION['lang']['blok'].': '.$blok."<br>\r\n\t ".$_SESSION['lang']['no_rmh'].': '.$norumah."\r\n     <table class=sortable cellspacing=1 border=0>\r\n     <thead><tr class=rowheader>\r\n\t    <td>No.</td>\r\n\t\t<td>".$_SESSION['lang']['nokaryawan']."</td>\r\n\t\t<td>".$_SESSION['lang']['namakaryawan']."</td>\r\n\t\t<td>".$_SESSION['lang']['jeniskelamin']."</td>\r\n\t\t<td>".$_SESSION['lang']['lokasitugas']."</td>\r\n\t\t<td>".$_SESSION['lang']['bagian']."</td>\r\n\t\t</tr>\r\n\t </thead><tbody>";
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo "<tr class=rowcontent>\r\n\t\t <td>".$no."</td>\r\n\t\t <td>".$bar->karyawanid."</td>\r\n\t\t <td>".$bar->namakaryawan."</td>\r\n\t\t <td>".$bar->jeniskelamin."</td>\r\n\t\t <td>".$bar->lokasitugas."</td>\r\n\t\t <td>".$bar->bagian."</td>\r\n\t\t </tr>";
}
echo '</tbody><tfoot></tfoot></table>';

?>