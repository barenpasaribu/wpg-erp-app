<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$txtfind = $_POST['txtfind'];
$str = 'select * from ' . $dbname . '.log_prapoht where nopp like \'%' . $txtfind . '%\' and close=\'2\'';

if ($res = mysql_query($str)) {
	echo '<table class=data cellspacing=1 cellpadding=2  border=0>' . "\r\n\t\t\t\t" . ' <thead>' . "\r\n\t\t\t\t" . ' <tr class=rowheader>' . "\r\n\t\t\t\t" . ' <td class=firsttd>' . "\r\n\t\t\t\t" . ' No.' . "\r\n\t\t\t\t" . ' </td>' . "\r\n\t\t\t\t" . ' <td>No. PP</td>' . "\r\n\t\t\t\t" . ' <td>Kode Barang</td>' . "\r\n\t\t\t\t" . ' <td>Nama Barang</td>' . "\r\n\t\t\t\t" . ' <td>Jumlah Diminta</td>' . "\r\n\t\t\t\t" . ' </tr>' . "\r\n\t\t\t\t" . ' </thead>' . "\r\n\t\t\t\t" . ' <tbody>';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$sql = 'select * from ' . $dbname . '.log_prapodt where nopp=\'' . $bar->nopp . '\'';

		#exit(mysql_error());
		($query = mysql_query($sql)) || true;
		$res2 = mysql_fetch_object($query);
		$sql2 = 'select * from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res2->kodebarang . '\'';

		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;
		$res3 = mysql_fetch_object($query2);
		$sql3 = 'select * from ' . $dbname . '.log_5masterbarangdt where kodebarang=\'' . $res3->kodebarang . '\'';

		#exit(mysql_error());
		($query3 = mysql_query($sql3)) || true;
		$res4 = mysql_fetch_object($query3);
		$no += 1;
		echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setPp(\'' . $bar->nopp . '\',\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->jumlah . '\',\'' . $bar->satuan . '\')" title=\'Click\' >' . "\r\n\t\t\t\t\t" . '  <td class=firsttd>' . $no . '</td>' . "\r\n\t\t\t\t\t" . '   <td>' . $bar->nopp . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->kodebarang . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t\t\t" . '  <td>' . $bar->jumlah . '</td>' . "\r\n\t\t\t\t\t" . ' </tr>';
	}

	echo '</tbody>' . "\r\n\t\t\t\t" . '  <tfoot>' . "\r\n\t\t\t\t" . '  </tfoot>' . "\r\n\t\t\t\t" . '  </table>';
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
