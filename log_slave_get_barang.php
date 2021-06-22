<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$txtfind = $_POST['txtfind'];
$str = ' select * from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $txtfind . '%\' or kodebarang like \'%' . $txtfind . '%\' limit 12';

if ($res = mysql_query($str)) {
	echo '<table class=data cellspacing=1 border=0>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader>' . "\r\n\t\t" . ' <td class=firsttd>' . "\r\n\t\t" . ' No.' . "\r\n\t\t" . ' </td>' . "\r\n\t\t" . ' <td>Kode.Kelompok</td>' . "\r\n\t\t" . ' <td>Kode Barang</td>' . "\r\n\t\t" . ' <td>Nama Barang</td>' . "\r\n\t\t" . ' <td>Satuan</td>' . "\r\n\t\t" . ' </tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody>';
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		echo '<tr class=rowcontent style=\'cursor:pointer;\' onclick="setKodeBarang(\'' . $bar->kelompokbarang . '\',\'' . $bar->kodebarang . '\',\'' . $bar->namabarang . '\',\'' . $bar->satuan . '\')" title=\'Click\' >' . "\r\n\t\t" . '      <td class=firsttd>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->kelompokbarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->kodebarang . '</td><td>' . $bar->namabarang . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->satuan . '</td>' . "\r\n\t\t\t" . ' </tr>';
	}

	echo '</tbody>' . "\r\n\t" . '      <tfoot>' . "\r\n\t\t" . '  </tfoot>' . "\r\n\t\t" . '  </table>';
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
