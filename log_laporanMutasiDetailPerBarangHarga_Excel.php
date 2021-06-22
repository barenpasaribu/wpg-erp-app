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

if ($gudang == '') {
	$gudang=$pt;
}

$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      where kodeorg like \'' . $gudang . '%\' and periode=\'' . $periode . '\'';
$awal = '';
$akhir = '';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$awal = $bar->tanggalmulai;
	$akhir = $bar->tanggalsampai;
}

$str = 'select  sum(saldoakhirqty) as sawal,' . "\r\n" . '                            sum(nilaisaldoakhir) as sawalrp from ' . "\r\n" . '                            ' . $dbname . '.log_5saldobulanan' . "\r\n" . '                            where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                            and periode=\'' . $prefper . '\'' . "\r\n" . '                            and kodegudang like \'' . $gudang . '%\'';
$strx = 'select a.*,b.idsupplier,b.tanggal,b.kodegudang,' . "\r\n" . '                  b.tipetransaksi' . "\r\n" . '                      from ' . $dbname . '.log_transaksidt a' . "\r\n" . '                  left join ' . $dbname . '.log_transaksiht b' . "\r\n" . '                      on a.notransaksi=b.notransaksi' . "\r\n" . '                      where kodebarang=\'' . $kodebarang . '\'' . "\r\n" . '                      and kodegudang like \'' . $gudang . '%\'' . "\r\n" . '                      and b.tanggal>=\'' . $awal . '\'' . "\r\n" . '                      and b.tanggal<=\'' . $akhir . '\'' . "\r\n" . '                      and b.post=1' . "\r\n" . '                      order by tanggal,waktutransaksi';
//saveLog($strx);
$sawal = 0;
$sawalrp = 0;

$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$sawal = $bar->sawal;
	$sawalrp = $bar->sawalrp;
}

$hargasawal = $sawalrp / $sawal;
$stream .= $_SESSION['lang']['detailtransaksibarang'] . '<br> ' . $_SESSION['lang']['pt'] . ':' . $pt . '<br>' . "\r\n" . '    ' . $_SESSION['lang']['namabarang'] . ':[' . $kodebarang . ']' . $namabarang . '(' . $satuan . ')<br>' . $_SESSION['lang']['periode'] . ':' . $periode . '<br>      ' . "\r\n" . '<table border=1>' . "\r\n" . '        <tr>' . "\r\n" . '          <td rowspan=2 align=center bgcolor=#DEDEDE >No.</td>' . "\r\n" . '          <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['sloc'] . '</td>' . "\r\n" . '          <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n" . '          <td rowspan=2 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['tipe'] . '</td>' . "\r\n" . '          <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldoawal'] . '</td>' . "\r\n" . '          <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['masuk'] . '</td>' . "\r\n" . '          <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['keluar'] . '</td>' . "\r\n" . '          <td colspan=3 align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['saldo'] . '</td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['kuantitas'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['hargasatuan'] . '</td>' . "\r\n" . '           <td align=center bgcolor=#DEDEDE >' . $_SESSION['lang']['totalharga'] . '</td>' . "\t" . '   ' . "\r\n" . '        </tr>';
$resx = mysql_query($strx);
$no = 0;
$saldo = $sawal;
$masuk = 0;
$keluar = 0;
$nilaisawal=$sawalrp;
while ($barx = mysql_fetch_object($resx)) {
	$no += 1;

	if ($barx->tipetransaksi == 1) {
		$saldo = $saldo + $barx->jumlah;
		$masuk = $barx->jumlah;
		$keluar = 0;
		$hargasatuank=0;
		$hargasatuanm = $barx->hargasatuan;
		if($saldo==0){
			$nilaisaldoakhir=0;	
		}else{
			$nilaisaldoakhir=$nilaisawal+($masuk*$hargasatuanm);
		}
	} else if ($barx->tipetransaksi == 2 || $barx->tipetransaksi == 3) {
		$saldo = $saldo + $barx->jumlah;
		$masuk = $barx->jumlah;
		$keluar = 0;
		$hargasatuanm = $barx->hargarata;
		$hargasatuank=0;
		if($saldo==0){
			$nilaisaldoakhir=0;	
		}else{
			$nilaisaldoakhir=$nilaisawal+($masuk*$hargasatuanm);
		}
	}
	else {
		$saldo = $saldo - $barx->jumlah;
		$keluar = $barx->jumlah;
		$hargasatuank = $barx->hargarata;
		$hargasatuanm=0;
		$masuk = 0;
		if($saldo==0){
			$nilaisaldoakhir=0;	
		}else{
			$nilaisaldoakhir=$nilaisawal-($keluar*$hargasatuank);
		}
	}
	$tipe=$barx->tipetransaksi;
	if($tipe=="1"){
		$tipe="Penerimaan Barang";
	}else if($tipe=="2"){
		$tipe="Pengembalian Pengeluaran(retur Gudang) Barang";
	}else if($tipe=='3'){
		$tipe='Penerimaan Mutasi Barang';
	}else if($tipe=='5'){
		$tipe='Pengeluaran Barang';
	}else if($tipe=='6'){
		$tipe='Pengembalian Penerimaan(Retur Supplier) Barang';
	}else if($tipe=='7'){
		$tipe='Pengeluaran Mutasi Barang';
	}

	$stream .= '<tr> <td>' . $no . '</td>
				<td>' . $barx->kodegudang . '</td>
				<td>' . tanggalnormal($barx->tanggal) . '</td>
				<td>' . $tipe . '</td>
				<td align=right class=firsttd>' . number_format($sawal, 2, '.', '') . '</td>
				<td align=right>' . number_format($nilaisawal/$sawal, 2, '.', '') . '</td>
				<td align=right>' . number_format($nilaisawal, 2, '.', '') . '</td>
				<td align=right class=firsttd>' . number_format($masuk, 2, '.', '') . '</td>
				<td align=right>' . number_format($hargasatuanm, 2, '.', '') . '</td>
				<td align=right>' . number_format($masuk * $hargasatuanm, 2, '.', '') . '</td>
				<td align=right class=firsttd>' . number_format($keluar, 2, '.', '') . '</td>
				<td align=right>' . number_format($hargasatuank, 2, '.', '') . '</td>
				<td align=right>' . number_format($keluar * $hargasatuank, 2, '.', '') . '</td>
				<td align=right class=firsttd>' . number_format($saldo, 2, '.', '') . '</td>
				<td align=right>' . number_format($nilaisaldoakhir/$saldo, 2, '.', '') . '</td>
				<td align=right>' . number_format($nilaisaldoakhir, 2, '.', '') . '</td></tr>';
	$sawal = $saldo;
	$nilaisawal = $nilaisaldoakhir;
}

$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];
$nop_ = 'DetailMaterialBalanceWPrice';

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
