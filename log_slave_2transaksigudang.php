<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
$unit = $_POST['unit'];
$periode = $_POST['periode'];
$jenis = $_POST['jenis'];
$kodebarang = $_POST['kodebarang'];
$kamusjenis[0] = 'Mutasi dalam perjalanan';
$kamusjenis[1] = 'Penerimaan';
$kamusjenis[2] = 'Pengembalian pengeluaran';
$kamusjenis[3] = 'Penerimaan mutasi';
$kamusjenis[5] = 'Pengeluaran';
$kamusjenis[6] = 'Pengembalian penerimaan';
$kamusjenis[7] = 'Pengeluaran mutasi';
if($jenis==0){
	$jenis=7;
	$movmutasi = 1;
}
if ($unit == '') {
	echo 'Warning: Period is missing';
	exit();
}

if ($periode == '') {
	echo 'Warning: Period is missing';
	exit();
}

if ($jenis == '') {
	echo 'Warning: trancstion type is missing';
	exit();
}

if ($jenis == '9') {
	$jenis = '';
}

$tipetransaksi = 'a.tipetransaksi = '.$jenis.' ';//like \'%' . $jenis . '%\'';
$str = 'select tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '    where periode =\'' . $periode . '\' and kodeorg=\'' . $unit . '\'';

if ($unit == 'sumatera') {
	$str = 'select tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '        where periode =\'' . $periode . '\' and kodeorg in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\')';
}

if ($unit == 'kalimantan') {
	$str = 'select tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '        where periode =\'' . $periode . '\' and kodeorg in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\')';
}



$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$tanggalmulai = $bar->tanggalmulai;
	$tanggalsampai = $bar->tanggalsampai;
}

if ($kodebarang == '') {
	if ($unit == 'sumatera') {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, ' . "\r\n" . '        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' ' . "\r\n" . '        and ' . $tipetransaksi . ' and a.kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\')' . "\r\n" . '        order by a.tanggal';
	}
	else if ($unit == 'kalimantan') {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, ' . "\r\n" . '        a.nopo, c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' ' . "\r\n" . '        and ' . $tipetransaksi . ' and a.kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\')' . "\r\n" . '        order by a.tanggal';
	}
	else {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, d.notransaksireferensi, d.post, d.lastupdate '. "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' ' . "\r\n" . '        and ' . $tipetransaksi . ' and a.kodegudang like \'%' . $unit . '%\'' . "\r\n" . '        order by a.tanggal';
//		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' ' . "\r\n" . '        and ' . $tipetransaksi . ' and a.kodegudang = \'' . $unit . '\'' . "\r\n" . '        order by a.tanggal';
	}
}
else {
	if ($unit == 'sumatera') {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and ' . $tipetransaksi . ' ' . "\r\n" . '        and a.kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\') and a.kodebarang = \'' . $kodebarang . '\'' . "\r\n" . '        order by a.tanggal';
		$str22 = 'select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'MRKE10\',\'SKSE10\',\'SOGM20\',\'SSRO21\',\'WKNE10\')' . "\r\n" . '        and kodebarang = \'' . $kodebarang . '\' and periode = \'' . $periode . '\'';
	}
	else if ($unit == 'kalimantan') {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and ' . $tipetransaksi . "\r\n" . '        and a.kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\') and a.kodebarang = \'' . $kodebarang . '\'' . "\r\n" . '        order by a.tanggal';
		$str22 = 'select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ' . $dbname . '.log_5saldobulanan where kodegudang in (\'SBME10\',\'SBNE10\',\'SMLE10\',\'SMTE10\',\'SSGE10\',\'STLE10\')' . "\r\n" . '        and kodebarang = \'' . $kodebarang . '\' and periode = \'' . $periode . '\'';
	}
	else {
		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi, d.notransaksireferensi, d.post, d.lastupdate ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and ' . $tipetransaksi . "\r\n" . '        and a.kodegudang like \'%' . $unit . '%\' and a.kodebarang = \'' . $kodebarang . '\' ' . "\r\n" . '        order by a.tanggal';
//		$str = 'select a.tanggal, a.kodebarang, b.namabarang, a.jumlah, a.satuan, a.hargasatuan, a.hargarata, a.nopo, ' . "\r\n" . '        c.namasupplier, a.kodeblok, a.kodemesin, a.notransaksi, d.gudangx, a.tipetransaksi ' . "\r\n" . '        from ' . $dbname . '.log_transaksi_vw a' . "\r\n" . '        left join ' . $dbname . '.log_5masterbarang b on a.kodebarang=b.kodebarang  ' . "\r\n" . '        left join ' . $dbname . '.log_5supplier c on a.idsupplier=c.supplierid  ' . "\r\n" . '        left join ' . $dbname . '.log_transaksiht d on a.notransaksi=d.notransaksi  ' . "\r\n" . '        where a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and ' . $tipetransaksi . "\r\n" . '        and a.kodegudang = \'' . $unit . '\' and a.kodebarang = \'' . $kodebarang . '\' ' . "\r\n" . '        order by a.tanggal';
		$str22 = 'select sum(saldoawalqty) as saldoawalqty, avg(hargaratasaldoawal) as hargaratasaldoawal, sum(nilaisaldoawal) as nilaisaldoawal from ' . $dbname . '.log_5saldobulanan where kodegudang = \'' . $unit . '\'' . "\r\n" . '        and kodebarang = \'' . $kodebarang . '\' and periode = \'' . $periode . '\'';
	}

	$res22 = mysql_query($str22);

	if (0 < mysql_num_rows($res22)) {
		while ($bar22 = mysql_fetch_object($res22)) {
			$saldoawalqty = $bar22->saldoawalqty;
			$hargaratasaldoawal = $bar22->hargaratasaldoawal;
			$nilaisaldoawal = $bar22->nilaisaldoawal;
		}
	}
}
$str44 = 'select kodebarang, namabarang, satuan from ' . $dbname . '.log_5masterbarang where kodebarang = \'' . $kodebarang . '\'';
$res44 = mysql_query($str44);

if (0 < mysql_num_rows($res44)) {
	while ($bar44 = mysql_fetch_object($res44)) {
		$namabarang = $bar44->namabarang;
		$satuan = $bar44->satuan;
	}
}
//echo $str;

$res = mysql_query($str);
/* if (mysql_num_rows($res) < 1) {
	echo '<tr class=rowcontent><td colspan=14>' . $_SESSION['lang']['tidakditemukan'] . '</td></tr>';
}
else { */
	echo '<thead><tr>' . "\r\n" . '        <td align=center>No.</td>';
	echo '<td align=center>' . $_SESSION['lang']['tipetransaksi'] . '</td>';
	echo '<td align=center>' . $_SESSION['lang']['tanggal'] . '</td>';
	echo '<td align=center>' . $_SESSION['lang']['notransaksi'] . '</td>';
	echo '<td align=center>' . $_SESSION['lang']['kodebarang'] . '</td>';
	echo '<td align=center>' . $_SESSION['lang']['namabarang'] . '</td>';

	if ($jenis == '') {
		echo '<td align=center>' . $_SESSION['lang']['masuk'] . '</td>';
		echo '<td align=center>' . $_SESSION['lang']['keluar'] . '</td>';
		echo '<td align=center>' . $_SESSION['lang']['saldo'] . '</td>';
	}
	else {
		echo '<td align=center>' . $_SESSION['lang']['jumlah'] . '</td>';
	}

	echo '<td align=center>' . $_SESSION['lang']['satuan'] . '</td>';

	if ($jenis == '') {
	}
	else {
		echo '<td align=center>' . $_SESSION['lang']['hargasatuan'] . '</td>';
		echo '<td align=center>' . $_SESSION['lang']['totalharga'] . '</td>';
	}

	if (($jenis == '0') || ($jenis == '1') || ($jenis == '2') || ($jenis == '3')) {
		echo '<td align=center>' . $_SESSION['lang']['nopo'] . '</td>';
	}

	if (($jenis == '0') || ($jenis == '1') || ($jenis == '2') || ($jenis == '3')) {
		echo '<td align=center>' . $_SESSION['lang']['supplier'] . '</td>';
	}

	if ($jenis == '') {
		echo '<td align=center>' . $_SESSION['lang']['tujuan'] . '/' . $_SESSION['lang']['sumber'] . '</td>';
	}

	if ($jenis == '7') {
		echo '<td align=center>' . $_SESSION['lang']['tujuan'] . '</td>';
	}

	if (($jenis == '5') || ($jenis == '6')) {
		echo '<td align=center>' . $_SESSION['lang']['kodeblok'] . '</td>';
	}

	if (($jenis == '5') || ($jenis == '6')) {
		echo '<td align=center>' . $_SESSION['lang']['kodevhc'] . '</td>';
	}
	if(($jenis=='7') || ($jenis == '3'))
	{
		echo '<td align=center>' . 'Referensi' . '</td>';
		echo '<td align=center>' . 'Tanggal Terima ' . '</td>';
	}
	echo '</tr></thead><tbody>';
	
	if ($jenis == '') {
		$no = 1;
		$saldo = $saldoawalqty;
		$masuk = 0;
		$keluar = 0;$totmas=0;
		echo '<tr class=rowcontent>' . "\r\n" . '            <td align=right>' . $no . '</td>';
		echo '<td>Saldo Awal</td>';
		echo '<td>' . tanggalnormal($periode . '-01') . '</td>';
		echo '<td>' . $kodebarang . '</td>';
		echo '<td nowrap>' . $namabarang . '</td>';

		if (0 <= $saldoawalqty) {
			$masuk = $saldoawalqty;
			$totmas += $masuk;
		}
		else {
			$keluar = $saldoawalqty * -1;
			$totkel += $keluar;
		}

		echo '<td align=right>' . number_format($masuk, 2) . '</td>';
		echo '<td align=right>' . number_format($keluar, 2) . '</td>';
		echo '<td align=right>' . number_format($saldoawalqty, 2) . '</td>';
		echo '<td>' . $satuan . '</td>';

		if ($jenis == '') {
		}
		else {
			echo '<td align=right>' . number_format($hargaratasaldoawal) . '</td>';
			echo '<td align=right>' . number_format($nilaisaldoawal) . '</td>';
		}

		echo '<td></td>';
		echo '<td></td>';
		echo '</tr>';
	}

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$total = 0;
		$total = $bar->jumlah * $bar->hargarata;
		if($jenis==7 and $movmutasi ==1){
			$sqlcheck = "SELECT post, lastupdate FROM log_transaksiht 
			WHERE notransaksi ='" . $bar->notransaksireferensi ."'";	
			$que = mysql_query($sqlcheck);
			while($res20= mysql_fetch_object($que)){
				$postpemerima= $res20->post;
			}
			if($postpemerima == 0){
				echo '<tr class=rowcontent>' . "\r\n" . '<td align=right>' . $no . '</td>';
				echo '<td nowrap>Mutasi dalam Perjalanan</td>';
				echo '<td nowrap>' . $bar->tanggal . '</td>';		
				echo '<td nowrap>' . $bar->notransaksi . '</td>';
				echo '<td>' . $bar->kodebarang . '</td>';
				echo '<td>' . $bar->namabarang . '</td>';
				echo '<td align=right>' . number_format($bar->jumlah, 2) . '</td>';
				echo '<td>' . $bar->satuan . '</td>';
				echo '<td align=right>' . number_format($total,2) . '</td>';
				echo '<td align=right>' . number_format($bar->hargarata) . '</td>';
				echo '<td>' . $bar->gudangx . '</td>';
				echo '<td nowrap colspan =2 align="center"> Belum Diterima </td>';
			}
		}else{
		if (($jenis == '0') || ($jenis == '1') || ($jenis == '3')) {
			$total = $bar->jumlah * $bar->hargasatuan;
		}
		else {
			$total = $bar->jumlah * $bar->hargarata;
		}

		echo '<tr class=rowcontent>' . "\r\n" . '<td align=right>' . $no . '</td>';
		echo '<td nowrap>' . $kamusjenis[$bar->tipetransaksi] . '</td>';
		echo '<td nowrap>' . $bar->tanggal . '</td>';		
		echo '<td nowrap>' . $bar->notransaksi . '</td>';
		echo '<td>' . $bar->kodebarang . '</td>';
		echo '<td>' . $bar->namabarang . '</td>';

		if ($jenis == '') {
			$masuk = 0;
			$keluar = 0;

			if ($bar->tipetransaksi < 4) {
				$masuk = $bar->jumlah;
			}

			if (4 < $bar->tipetransaksi) {
				$keluar = $bar->jumlah;
			}

			$totmas += $masuk;
			$totkel += $keluar;
			echo '<td align=right>' . number_format($masuk, 2) . '</td>';
			echo '<td align=right>' . number_format($keluar, 2) . '</td>';
			$saldo += $masuk - $keluar;
			echo '<td align=right>' . number_format($saldo, 2) . '</td>';
		}
		else {
			echo '<td align=right>' . number_format($bar->jumlah, 2) . '</td>';
		}

		echo '<td>' . $bar->satuan . '</td>';

		if ($jenis == '') {
		}
		else {
			if (($jenis == '0') || ($jenis == '1') || ($jenis == '3')) {
				echo '<td align=right>' . number_format($bar->hargasatuan) . '</td>';
			}
			else {
				echo '<td align=right>' . number_format($bar->hargarata) . '</td>';
			}

			echo '<td align=right>' . number_format($total) . '</td>';
		}

		if (($jenis == '0') || ($jenis == '1') || ($jenis == '2') || ($jenis == '3')) {
			echo '<td nowrap>' . trim($bar->nopo) . '</td>';
		}

		if (($jenis == '0') || ($jenis == '1') || ($jenis == '2') || ($jenis == '3')) {
			echo '<td nowrap>' . $bar->namasupplier . '</td>';
		}

		if ($jenis == '7') {
			echo '<td>' . $bar->gudangx . '</td>';
		}

		if (($jenis == '5') || ($jenis == '6')) {
			echo '<td>' . $bar->kodeblok . '</td>';
		}

		if (($jenis == '5') || ($jenis == '6')) {
			echo '<td>' . $bar->kodemesin . '</td>';
		}

		if ($jenis == '') {
			if ($bar->tipetransaksi < 4) {
				$keluarmasuk = $bar->nopo . ' ' . $bar->namasupplier;
			}

			if (4 < $bar->tipetransaksi) {
				$keluarmasuk = $bar->kodeblok . ' ' . $bar->kodemesin . ' ' . $bar->gudangx;
			}

			echo '<td nowrap>' . $keluarmasuk . '</td>';
		}
		if($jenis=='3'){
			$chkpost = $bar->post;
			$nokirim = $bar->notransaksireferensi;
			if($nokirim <> ''){
				echo '<td nowrap>' . $bar->notransaksireferensi . '</td>';
			}else{
				echo '<td nowrap> Belum Diposting </td>';
			}
			if($chkpost <> 0){
				echo '<td nowrap>' . $bar->lastupdate . '</td>';
			}else{
				echo '<td nowrap> Belum Diposting </td>';
			}
		}
		if($jenis=='7'){
			$chkpost = $bar->post;
			if($bar->notransaksireferensi <> ''){
				echo '<td nowrap>' . $bar->notransaksireferensi . '</td>';
				$sqlcheck = "SELECT post, lastupdate FROM log_transaksiht 
				WHERE notransaksi ='" . $bar->notransaksireferensi ."'";	
				$que = mysql_query($sqlcheck);
				while($res20= mysql_fetch_object($que)){
					$postpemerima= $res20->post;
					if($postpemerima == 1){
						echo '<td nowrap>' . $res20->lastupdate .'</td>';
					}else{
						echo '<td nowrap> Belum Diterima </td>';
					}
				}
			}else{
				echo '<td nowrap colspan =2 align="center"> Belum Diterima </td>';
			}
		}
		echo '</tr>';
		}
	}

	if ($jenis == '') {
		echo '<tr class=rowcontent>' . "\r\n" . '            <td align=center colspan=5>Total</td>';
		echo '<td align=right>' . number_format($totmas, 2) . '</td>';
		echo '<td align=right>' . number_format($totkel, 2) . '</td>';
		echo '<td align=right>' . number_format($saldo, 2) . '</td>';
		echo '<td colspan=3>' . $satuan . '</td>';
		echo '</tr>';
	}

	echo '</tbody<tfoot></tfoot>';
/* // } */

?>
