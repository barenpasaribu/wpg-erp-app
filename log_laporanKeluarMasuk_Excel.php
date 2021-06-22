<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$pt = $_GET['pt'];
$gudang = $_GET['gudang'];
$periode = substr($_GET['periode'], 0, 7);
$kodebarang = $_GET['kodebarang'];
$namabarang = $_GET['namabarang'];
$satuan = $_GET['satuan'];
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

$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . 'where kodeorg=\'' . $gudang . '\' and periode=\'' . $periode . '\'';
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

$stream .= '<table><tr><td colspan=6 align=center>' . $_SESSION['lang']['laporanstok'] . '</td></tr>' . "\r\n" . '            <tr><td colspan=3>' . $_SESSION['lang']['pt'] . ':' . $namapt . '</td>' . "\r\n" . '            <td colspan=3>' . $_SESSION['lang']['sloc'] . ':' . $gudang . '</td></tr>' . "\r\n" . '            <tr><td colspan=3>' . $_SESSION['lang']['kodebarang'] . ':' . $kodebarang . '</td>' . "\r\n" . '            <td colspan=3>' . $_SESSION['lang']['namabarang'] . ':' . $namabarang . '</td></tr>' . "\r\n" . '            <tr><td colspan=3>' . $_SESSION['lang']['periode'] . ':' . $periode . '</td>' . "\r\n" . '            <td colspan=3>&nbsp;</td></tr></table>' . "\r\n" . '            <table border=1>' . "\r\n" . '                <tr>' . "\r\n" . '                      <td rowspan=2 bgcolor=#DEDEDE align=center>No.</td>' . "\r\n" . '                      <td rowspan=2  bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '                      <td colspan=3  bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '                      <td colspan=3 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '                      <td colspan=3 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '                      <td colspan=3 bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['saldo'] . '</td>' . "\t\r\n" . '                    </tr>' . "\r\n" . '                    <tr>' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['totalharga'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['totalharga'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['totalharga'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>    ' . "\r\n" . '                    <td bgcolor=#DEDEDE align=center>' . $_SESSION['lang']['totalharga'] . '</td>                            ' . "\r\n" . '                    </tr>' . "\r\n" . '                    ';

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

	$stream .= ' <tr class=rowcontent>' . "\r\n" . '            <td>' . $no . '</td>' . "\r\n" . '            <td align=center>' . tanggalnormal($barx->tanggal) . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawal, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawalrp / $sawal, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($sawalrp, 2, '.', ',') . '</td>                ' . "\r\n" . '            <td align=center>' . number_format($masuk, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($barx->hargasatuan, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($masuk * $barx->hargasatuan, 2, '.', ',') . '</td>                ' . "\r\n" . '            <td align=center>' . number_format($keluar, 2, '.', ',') . '</td>' . "\r\n" . '             <td align=center>' . number_format($barx->hargarata, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($keluar * $barx->hargarata, 2, '.', ',') . '</td>                    ' . "\r\n" . '            <td align=center>' . number_format($saldo, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($barx->hargarata, 2, '.', ',') . '</td>' . "\r\n" . '            <td align=center>' . number_format($saldo * $barx->hargarata, 2, '.', ',') . '</td> ' . "\r\n" . '            </tr>';
	$sawal = $saldo;
	$sawalrp = $saldo * $barx->hargarata;
}

$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'ReportBalance';

if (0 < strlen($stream)) {
	if ($handle = opendir('tempExcel')) {
		while (false !== $file = readdir($handle)) {
			if (($file != '.') && ($file != '..')) {
				@unlink('tempExcel/' . $file);
			}
		}

		closedir($handle);
	}

	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');

	if (!fwrite($handle, $stream)) {
		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';
		exit();
	}
	else {
		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';
	}

	closedir($handle);
}

?>
