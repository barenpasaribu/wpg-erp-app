<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zFunction.php';
$txtcari = $_POST['txtcari'];
$str = 'select a.kodebarang,a.namabarang,a.satuan from' . "\r\n\t\t" . '      ' . $dbname . '.log_5masterbarang a where a.namabarang like \'%' . $txtcari . '%\' order by a.namabarang';
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	echo 'Error: ' . $_SESSION['lang']['tidakditemukan'];
}
else {
	echo '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t" . '     <thead>' . "\r\n\t\t\t" . '      <tr class=rowheader>' . "\r\n\t\t\t\t" . '      <td>No</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t" . '  </tr>' . "\r\n\t\t" . '     </thead>' . "\r\n\t\t\t" . ' <tbody>';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="loadField(\'' . $bar->kodebarang . '\');">' . "\r\n\t\t\t\t" . '   <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t" . '      </tr>';
	}

	echo "\r\n\t\t\t\t" . ' </tbody>' . "\r\n\t\t\t\t" . ' <tfoot></tfoot>' . "\r\n\t\t\t\t" . ' </table>';
}

?>
