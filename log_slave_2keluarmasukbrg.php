<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/fpdf.php';
require_once 'lib/eagrolib.php';
$pt = $_POST['pt'];
$gudang = $_POST['kd_gudang'];
$periode = substr($_POST['periode'], 0, 7);
$kodebarang = $_POST['kodebarang'];
$namabarang = $_POST['namabarang'];
$satuan = $_POST['satuan'];
$x = str_replace('-', '', $periode);
$x = str_replace('/', '', $x);
$x = mktime(0, 0, 0, intval(substr($x, 4, 2)) - 1, 15, substr($x, 0, 4));
$prefper = date('Y-m', $x);
$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $pt . '\'';
$namapt = 'COMPANY NAME';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namapt = strtoupper($bar->namaorganisasi);
}

$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      where kodeorg=\'' . $gudang . '\' and periode=\'' . $periode . '\'';
$awal = '';
$akhir = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$awal = $bar->tanggalmulai;
	$akhir = $bar->tanggalsampai;
}

if ($gudang == '') {
	$str = 'select  sum(saldoakhirqty) as sawal,' . "\r\n" . '                            sum(nilaisaldoakhir) as sawalrp from ' . "\r\n" . '                            ' . $dbname . '.log_5saldobulanan' . "\r\n" . '                            where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                            and periode=\'' . $prefper . '\'';
	$strx = 'select a.*,b.idsupplier,b.tanggal,b.kodegudang,' . "\r\n" . '                  b.tipetransaksi ' . "\r\n" . '                  from ' . $dbname . '.log_transaksidt a' . "\r\n" . '                  left join ' . $dbname . '.log_transaksiht b' . "\r\n" . '                      on a.notransaksi=b.notransaksi' . "\r\n" . '                      where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                      and kodept=\'' . $pt . '\'' . "\r\n" . '                      and b.tanggal>=\'' . $awal . '\'' . "\r\n" . '                      and b.tanggal<=\'' . $akhir . '\'' . "\r\n" . '                      and b.post=1' . "\r\n" . '                      order by tanggal,waktutransaksi';
}
else {
	$str = 'select  sum(saldoakhirqty) as sawal,' . "\r\n" . '                            sum(nilaisaldoakhir) as sawalrp from ' . "\r\n" . '                            ' . $dbname . '.log_5saldobulanan' . "\r\n" . '                            where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                            and periode=\'' . $prefper . '\'' . "\r\n" . '                            and kodegudang=\'' . $gudang . '\'';
	$strx = 'select a.*,b.idsupplier,b.tanggal,b.kodegudang,' . "\r\n" . '                  b.tipetransaksi' . "\r\n" . '                      from ' . $dbname . '.log_transaksidt a' . "\r\n" . '                  left join ' . $dbname . '.log_transaksiht b' . "\r\n" . '                      on a.notransaksi=b.notransaksi' . "\r\n" . '                      where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                      and kodept=\'' . $pt . '\'' . "\r\n" . '                      and kodegudang=\'' . $gudang . '\'' . "\r\n" . '                      and b.tanggal>=\'' . $awal . '\'' . "\r\n" . '                      and b.tanggal<=\'' . $akhir . '\'' . "\r\n" . '                      and b.post=1' . "\r\n" . '                      order by tanggal,waktutransaksi';
}

$sawal = 0;
$sawalrp = 0;
$hargasawal = 0;
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$sawal = $bar->sawal;
	$sawalrp = $bar->sawalrp;
}

if (0 < $sawal) {
	$hargasawal = $sawalrp / $sawal;
}

$resx = mysql_query($strx);
$no = 0;
$saldo = $sawal;
$masuk = 0;
$keluar = 0;

while ($barx = mysql_fetch_object($resx)) {
	$no += 1;

	if ($barx->tipetransaksi < 5) {
		$saldo = $saldo + $barx->jumlah;
		$masuk = $barx->jumlah;
		$keluar = 0;
	}
	else {
		$saldo = $saldo - $barx->jumlah;
		$keluar = $barx->jumlah;
		$masuk = 0;
	}

	echo "\t" . '<tr class=rowcontent>' . "\r\n" . '            <td>' . $no . '</td>' . "\r\n" . '            <td align=center>' . tanggalnormal($barx->tanggal) . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawal, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawalrp / $sawal, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawalrp, 2, '.', ',') . '</td>                ' . "\r\n" . '            <td align=center>' . number_format($masuk, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($barx->hargasatuan, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($masuk * $barx->hargasatuan, 2, '.', ',') . '</td>                ' . "\r\n" . '            <td align=center>' . number_format($keluar, 2, '.', ',') . '</td>' . "\r\n" . '             <td align=center>' . number_format($barx->hargarata, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($keluar * $barx->hargarata, 2, '.', ',') . '</td>                    ' . "\r\n" . '            <td align=center>' . number_format($saldo, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($barx->hargarata, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($saldo * $barx->hargarata, 2, '.', ',') . '</td>                ' . "\r\n" . '            </tr>';
	$sawal = $saldo;
	$sawalrp = $saldo * $barx->hargarata;
}

?>
