<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$txtcari = $_POST['txtcari'];
$str = "select a.kodebarang,a.namabarang,a.satuan ".
       "from $dbname.log_5masterbarang a ".
       "where a.namabarang like '%" . $txtcari . "%' or a.kodebarang like '%" . $txtcari . "'";
$res = mysql_query($str);

if (mysql_num_rows($res) < 1) {
	echo 'Error: ' . $_SESSION['lang']['tidakditemukan'];
}
else {
	echo "\r\n\t\t" . '<fieldset>' . "\r\n\t\t" . '<legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n\t\t" . '<div style="width:450px; height:300px; overflow:auto;">' . "\r\n\t\t\t" . '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t" . '     <thead>' . "\r\n\t\t\t" . '      <tr class=rowheader>' . "\r\n\t\t\t\t" . '      <td>No</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $_SESSION['lang']['satuan'] . '</td>' . "\r\n\t\t\t\t" . '  </tr>' . "\r\n\t\t" . '     </thead>' . "\r\n\t\t\t" . ' <tbody>';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent style=\'cursor:pointer;\' title=\'Click\' onclick="throwThisRow(\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\');">' . "\r\n\t\t\t\t" . '   <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t" . '      </tr>';
	}

	echo "\r\n\t\t\t\t" . ' </tbody>' . "\r\n\t\t\t\t" . ' <tfoot></tfoot>' . "\r\n\t\t\t\t" . ' </table></div></fieldset>';
}

?>
