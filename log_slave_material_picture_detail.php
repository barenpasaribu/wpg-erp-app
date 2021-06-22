<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodebarang = $_GET['kodebarang'];
$str = 'select a.*,b.* from ' . $dbname . '.log_5masterbarang a' . "\r\n" . '        left join ' . $dbname . '.log_5photobarang b ' . "\r\n\t\t" . 'on a.kodebarang=b.kodebarang' . "\r\n" . '       where a.kodebarang=\'' . $kodebarang . '\'';
$depan = '';
$samping = '';
$atas = '';
$spesifikasi = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namabarang = $bar->namabarang;
	$satuan = $bar->satuan;
	$depan = $bar->depan;
	$samping = $bar->samping;
	$atas = $bar->atas;
	$spesifikasi = $bar->spesifikasi;
}

echo '<fieldset><legend>[' . $kodebarang . ']' . $namabarang . '(' . $satuan . ')</legend>';
echo '<table>' . "\r\n\t" . '        <tr><td>Spec</td><td>' . $spesifikasi . '</td></tr>' . "\r\n\t\t\t" . '<tr><td>Pic1</td><td><img src=\'' . $depan . '\' height=150px></td></tr>' . "\r\n\t\t\t" . '<tr><td>Pic2</td><td><img src=\'' . $samping . '\' height=150px></td></tr>' . "\r\n\t\t\t" . '<tr><td>Pic3</td><td><img src=\'' . $atas . '\' height=150px></td></tr>' . "\r\n\t" . '        </table>';
echo '</fieldset>';

?>
