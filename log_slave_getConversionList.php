<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$mayor = $_POST['mayor'];
$str = 'select a.*,b.namabarang,b.satuan as satuanori from ' . $dbname . '.log_5stkonversi a' . "\r\n" . '      left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang' . "\r\n" . '      where a.kodebarang like \'' . $mayor . '%\'';
$res = mysql_query($str);
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n" . '         <td class=firsttd>' . $no . '</td>' . "\r\n" . '         <td>' . $bar->kodebarang . '</td>' . "\r\n" . '         <td>' . $bar->namabarang . '</td>' . "\r\n" . '         <td>' . $bar->satuanori . '</td>' . "\r\n" . '         <td>' . $bar->satuankonversi . '</td>' . "\r\n" . '         <td align=right>' . $bar->jumlah . '</td>' . "\r\n" . '         </tr>';
}

?>
