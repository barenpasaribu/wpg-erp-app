<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$keuser = $_POST['keuser'];
$notransaksi = $_POST['notransaksi'];
$kolom = $_POST['kolom'];
$kolomstatus = 'status'.$kolom;
$str = ' update '.$dbname.'.sdm_pjdinasht set '.$kolom.'='.$keuser."\r\n       where notransaksi='".$notransaksi."' and ".$kolomstatus.'=0';
if (mysql_query($str)) {
    $to = getUserEmail($keuser);
    $namakaryawan = getNamaKaryawan($_SESSION['standard']['userid']);
    $subject = '[Notifikasi]Persetujuan Perjalanan Dinas a/n '.$namakaryawan;
    $body = "<html>\r\n                 <head>\r\n                 <body>\r\n                   <dd>Dengan Hormat,</dd><br>\r\n                   <br>\r\n                   Pada hari ini, tanggal ".date('d-m-Y').' karyawan a/n  '.$namakaryawan." mengajukan surat perjalanan dinas\r\n                   kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.\r\n                   <br>\r\n                   <br>\r\n                   <br>\r\n                   Regards,<br>\r\n                   eAgro Plantation Management Software.\r\n                 </body>\r\n                 </head>\r\n               </html>\r\n               ";
    $kirim = kirimEmailWindows($to, $subject, $body);
} else {
    echo ' Gagal,'.addslashes(mysql_error($conn));
}

?>