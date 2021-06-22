<?php
//last update 17102020
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
$tipetrx[1]='Masuk';
$tipetrx[2]='Pengembalian pengeluaran';
$tipetrx[3]='penerimaan mutasi';
$tipetrx[5]='Pengeluaran';
$tipetrx[6]='Pengembalian penerimaan';
$tipetrx[7]='pengeluaran mutasi';
if (isTransactionPeriod()) {
	$limit = 20;
	$page = 0;
	$gudang = $_POST['gudang'];
	$add = '';

	if (isset($_POST['tex'])) {
		$notransaksi = '%' . $_POST['tex'] . '%-' . $gudang;
		$add = ' and notransaksi like \'' . $notransaksi . '\'';
	}

	$str = 'select count(*) as jlhbrs from ' . $dbname . '.log_transaksiht where kodegudang=\'' . $gudang . '\''. $add . ' and post=0 order by jlhbrs desc';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$jlhbrs = $bar->jlhbrs;
	}

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	//$str = 'select * from ' . $dbname . '.log_transaksiht where kodegudang=\'' . $gudang . '\''. $add .'and post=0 order by tanggal asc,notransaksi asc limit ' . $offset . ',20';
	$str = 'select * from ' . $dbname . '.log_transaksiht where kodegudang=\'' . $gudang . '\''. $add .'and post=0 order by tanggal asc,notransaksi asc limit ' . $offset . ',20';
	$res = mysql_query($str);
	$no = $page * $limit;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$namasupplier = $bar->idsupplier;
		$strx = 'select namasupplier from ' . $dbname . '.log_5supplier where supplierid=\'' . $bar->idsupplier . '\'';
		$resx = mysql_query($strx);
		while ($barx = mysql_fetch_object($resx)) {
			$namasupplier = $barx->namasupplier;
		}

		$namapembuat = '';
		$stry = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->user;
		$resy = mysql_query($stry);
		while ($bary = mysql_fetch_object($resy)) {
			$namapembuat = $bary->namauser;
		}
		
		//17102020 - Rev. NOPO
		$stry = 'select nopo from ' . $dbname . '.log_transaksiht where notransaksi =\''.$bar->notransaksireferensi.'\'';
		$resy = mysql_query($stry);

		while ($bary = mysql_fetch_object($resy)) {
			$namapembuat = $bary->namauser;
		}
		for($x=1;$x<=7;$x++){
			if($bar->tipetransaksi == $x) $trxtipe = $tipetrx[$x];
		}
		$sqlNp = "SELECT nopo FROM log_transaksiht WHERE notransaksi=(SELECT nopo FROM log_transaksidt WHERE notransaksi='".$bar->notransaksi."' LIMIT 1)";
		//echo $sqlNp;
		$qryNp = mysql_query($sqlNp);
		$dtNp = mysql_fetch_array($qryNp);
		echo '<tr class=rowcontent id=indukrow' . $no . '>' . "\r\n\t" . '  <td>' . $no . '</td> 
		<td>' . $bar->kodegudang . '</td>
		<td title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $trxtipe . '</td>
		<td>' . $bar->notransaksi . '</td>
		<td>' . tanggalnormal($bar->tanggal) . '</td>
		<td>' . $bar->kodept . '</td>
		<td>' . $dtNp['nopo'] . '</td>
		<td>' . $namasupplier . '</td>
		<td>' . $bar->gudangx . '</td>
		<td>' . $bar->notransaksireferensi . '</td> 
		<td>' . $namapembuat . '</td>  
		<td align=center> <button class=mybutton onclick="previewPosting(' . $bar->tipetransaksi . ',\'' . $bar->notransaksi . '\',\'' . $gudang . '\',event);">' . $_SESSION['lang']['proses'] . '</button></td></tr>';
	}

	echo '<tr><td colspan=11 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs .'<br>' . "\r\n" . '       <button class=mybutton onclick=cariUnconfirmed(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '   <button class=mybutton onclick=cariUnconfirmed(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '   </td>' . "\r\n\t" . '   </tr>';
}
else {
	echo ' Error: Transaction Period missing';
}

?>
