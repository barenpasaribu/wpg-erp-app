<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodebarang = $_GET['kodebarang'];
$str = 'select * from ' . $dbname . '.log_5photobarang where kodebarang=\'' . $jodebarang . '\'';
$depan = '';
$samping = '';
$atas = '';
$spesifikasi = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$depan = $bar->depan;
	$samping = $bar->samping;
	$atas = $bar->atas;
	$spesifikasi = $bar->spesifikasi;
}

echo '<fieldset  style=\'width:430px;\'><legend>Update Detail</legend>' . "\r\n" . '       <form id=\'' . $kodebarang . '\' name=photobarang method=post enctype=\'multipart/form-data\'>' . "\r\n\t" . '   ' . "\t" . ' ' . "\r\n" . '       <table cellapacing=1 border=0>' . "\r\n\t" . '   <tr> ' . "\r\n\t" . '   ' . "\t\t" . '<td>Spec</td>' . "\r\n\t" . '   ' . "\t\t" . '<td><textarea name=spec id=spec cols=20 rows=3 onkeypress="return parent.tanpa_kutip(event)">' . $spesifikasi . '</textarea></td>' . "\r\n\t" . '   </tr>' . "\t\t\r\n\t" . '   <tr>' . "\r\n\t" . '   ' . "\t\t" . '<td>Tampak depan</td>' . "\r\n\t" . '   ' . "\t\t" . '<td>' . "\r\n\t\t\t" . '   <input type=hidden name=MAX_FILE_SIZE value=100000>' . "\r\n\t\t\t" . '   <input type=file name=file[] size35>' . "\r\n\t\t\t" . '</td>' . "\r\n\t" . '   </tr>' . "\t\t\t\t\r\n\t" . '   ' . "\t\t" . '<td>Tampak Samping</td>' . "\r\n\t" . '   ' . "\t\t" . '<td>' . "\r\n\t\t\t" . '   <input type=file name=file[] size35>' . "\r\n\t\t\t" . '</td>' . "\r\n\t" . '   </tr>' . "\t\r\n\t" . '   ' . "\t\t" . '<td>Tampak Atas</td>' . "\r\n\t" . '   ' . "\t\t" . '<td>' . "\r\n\t\t\t" . '   <input type=file name=file[] size35>' . "\r\n\t\t\t" . '   <input type=hidden name=kodebarangx id=kodebarangx value=\'' . $kodebarang . '\'>' . "\r\n\t\t\t" . '</td>' . "\t\t\t\t\t\t\r\n\t" . '   </tr>' . "\r\n" . '       </table>' . "\r\n\t" . '   ' . "\r\n\t" . '   </form>' . "\r\n\t" . '   <center>' . "\r\n\t" . '   1 File(s) Max 100 Kb.<br>' . "\r\n\t" . '   <button onclick=parent.simpanPhoto()>Save</button>' . "\r\n\t" . '   </center>' . "\r\n\t" . '   </fieldset>';

?>
