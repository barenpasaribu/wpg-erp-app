<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$karyawanid = $_SESSION['standard']['userid'];
$nopp = $_POST['nopp'];
$kodebarang = $_POST['kodebarang'];

if (isset($_POST['pesan'])) {
	$pesan = $_POST['pesan'];
	$str = 'insert into ' . $dbname . '.log_pp_chat (`nopp`,`karyawanid`,' . "\r\n" . '          `pesan`,`kodebarang`)' . "\r\n\t\t" . '  values(\'' . $nopp . '\',' . $karyawanid . ',\'' . $pesan . '\',\'' . $kodebarang . '\')';

	if ($res = mysql_query($str)) {
	}
	else {
		echo ' Error: ' . addslashes(mysql_error($conn));
	}
}

echo '<table class=sortable cellspacing=1 border=0 width=100%>' . "\r\n" . '        <tr>' . "\r\n\t\t" . '  <td>From</td>' . "\r\n\t\t" . '  <td>Time</td>' . "\r\n\t\t" . '  <td>Messages</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t" . '   ';
$str = 'select a.*,b.namauser from ' . $dbname . '.log_pp_chat a left join ' . $dbname . '.user b' . "\r\n" . '         on a.karyawanid=b.karyawanid' . "\r\n" . '         where a.kodebarang=\'' . $kodebarang . '\' and a.nopp=\'' . $nopp . '\' order by tanggal';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;

	if (($no % 2) == 0) {
		$ct = 'style=\'background-color:#FFFFFF\'';
	}
	else {
		$ct = 'style=\'background-color:#E8F2FE\'';
	}

	echo '<tr>' . "\r\n\t" . '        <td ' . $ct . '>' . $bar->namauser . '</td>' . "\r\n\t\t\t" . '<td ' . $ct . '>' . $bar->tanggal . '</td>' . "\r\n\t\t\t" . '<td ' . $ct . '>' . $bar->pesan . '</td>' . "\r\n\t" . '      </tr>';
}

echo '</table>';

?>
