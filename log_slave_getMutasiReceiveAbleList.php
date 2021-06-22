<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';

if (isTransactionPeriod()) {
	$limit = 20;
	$page = 0;
	$gudang = $_POST['gudang'];
	$add = '';

	if (isset($_POST['tex'])) {
		$notransaksi = $_POST['tex'] . '%';
		$add = ' and notransaksi like \'' . $notransaksi . '\'';
	}

	$str = 'select count(*) as jlhbrs from ' . $dbname . '.log_transaksiht where' . "\r\n\t\t" . 'tipetransaksi =7' . "\r\n\t\t" . $add . "\t\t\r\n\t\t" . 'and gudangx=\'' . $gudang . '\' and (notransaksireferensi=\'\' or  notransaksireferensi is null)' . "\r\n\t\t" . 'order by jlhbrs desc';
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
	$str = 'select * from ' . $dbname . '.log_transaksiht where' . "\r\n\t\t" . 'tipetransaksi =7' . "\r\n\t\t" . $add . "\r\n\t\t" . 'and gudangx=\'' . $gudang . '\' and (notransaksireferensi=\'\' or  notransaksireferensi is null)' . "\t\r\n\t\t" . 'order by notransaksi desc limit ' . $offset . ',20';
	$res = mysql_query($str);
	$no = $page * $limit;

	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$namapembuat = '';
		$stry = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->user;
		$resy = mysql_query($stry);

		while ($bary = mysql_fetch_object($resy)) {
			$namapembuat = $bary->namauser;
		}

		$namaposting = 'Hold';

		if (intval($bar->postedby) != 0) {
			$stry = 'select namauser from ' . $dbname . '.user where karyawanid=' . $bar->postedby;
			$resy = mysql_query($stry);

			while ($bary = mysql_fetch_object($resy)) {
				$namaposting = $bary->namauser;
			}
		}

		if (($namaposting == 'Hold') && ($bar->post == 1)) {
			$namaposting = ' Release By ???';
		}

		$status = $_SESSION['lang']['belumterima'];

		if ($bar->notransaksireferensi != '') {
			$status = $_SESSION['lang']['sudahditerima'];
		}
		else if (0 < $bar->post) {
			$add = '<img src=images/application/application_go.png class=resicon  title=\'Process\' onclick="processReceipt(\'' . $bar->notransaksi . '\');">';
		}
		else {
			$add = '';
		}

		echo '<tr class=rowcontent>' . "\r\n\t" . '  <td>' . $no . '</td>' . "\r\n\t" . '  <td>' . $bar->kodegudang . '</td>' . "\r\n\t" . '  <td title="1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi">' . $bar->tipetransaksi . '</td>' . "\r\n\t" . '  <td>' . $bar->notransaksi . '</td>' . "\r\n\t" . '  <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t" . '  <td>' . $bar->kodept . '</td>' . "\r\n\t" . '  <td>' . $bar->gudangx . '</td>' . "\t\t\t" . '  ' . "\r\n\t" . '  <td>' . $namapembuat . '</td>' . "\r\n\t" . '  <td>' . $namaposting . '</td>' . "\r\n\t" . '  <td>' . $status . '</td>' . "\r\n\t" . '  <td align=center>' . "\r\n\t" . '     ' . $add . "\r\n\t" . '     <img src=images/pdf.jpg class=resicon  title=\'' . $_SESSION['lang']['pdf'] . '\' onclick="previewMutasi(\'' . $bar->notransaksi . '\',event);"> ' . "\r\n\t" . '  </td>' . "\r\n\t" . '  </tr>';
	}

	echo '<tr><td colspan=11 align=center>' . "\r\n" . '       ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '   <br>' . "\r\n" . '       <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '   <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '   </td>' . "\r\n\t" . '   </tr>';
}
else {
	echo ' Error: Transaction Period missing';
}

?>
