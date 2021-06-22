<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$proses = $_POST['proses'];
$periode = $_POST['periode'];
$kdOrg = $_POST['kdOrg'];
$lokasi = $_POST['lokasi'];
$kdBrg = $_POST['kdBrg'];
$jmlh = $_POST['jmlh'];
$oldKdbrg = $_POST['oldKdbrg'];
$periodeDetail = $_POST['periodeDetail'];
$kdCustomer = $_POST['kdCustomer'];
$lokasi = $_POST['lokasi'];
$tglDetail = tanggalsystem($_POST['tglDetail']);

switch ($proses) {
case 'insert':
	if ($periode == '') {
		echo 'warning: Please complete the Form';
		exit();
	}

	$sCek = 'select periode from ' . $dbname . '.pmn_rencanajualht where periode=\'' . $periode . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$jlhOprSthn = $jmlhHari * $pemakaianHm;
		$sIns = 'insert into ' . $dbname . '.pmn_rencanajualht (periode, kodeorg) values (\'' . $periode . '\',\'' . $kdOrg . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . mysql_error($conn);
		}
	}
	else {
		echo 'warning: Transaction already exist';
		exit();
	}

	break;

case 'update':
	if ($periode == '') {
		echo 'warning: Please complete the Form';
		exit();
	}

	$jlhOprSthn = $jmlhHari * $pemakaianHm;
	$sUpd = 'update ' . $dbname . '.pmn_rencanajualht set lokasi=\'' . $lokasi . '\', kodeorg=\'' . $kdOrg . ' where  periode=\'' . $periode . '\'';

	if (mysql_query($sUpd)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'insertDetail':
	if (($jmlh == '0') || ($tglDetail == '') || ($lokasi == '')) {
		echo 'warning: Please complete the Form';
		exit();
	}

	$thnInput = substr($tglDetail, 0, 4);
	$blnInput = substr($tglDetail, 4, 2);
	$preod = $thnInput . '-' . $blnInput;

	if ($preod != $periodeDetail) {
		echo 'warning:Please insert appropriate year';
		exit();
	}

	$sCek = 'select periode,tanggal,kodeorg,kodebarang from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periodeDetail . '\' and tanggal=\'' . $tglDetail . '\' and kodebarang=\'' . $kdBrg . '\' and kodeorg=\'' . $kdOrg . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sIns = 'insert into ' . $dbname . '.pmn_rencanajualdt (periode, tanggal, kodebarang, jumlah, lokasipengiriman, pembeli,kodeorg) values (\'' . $periodeDetail . '\',\'' . $tglDetail . '\',\'' . $kdBrg . '\',\'' . $jmlh . '\',\'' . $lokasi . '\',\'' . $kdCustomer . '\',\'' . $kdOrg . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo 'DB Error : ' . mysql_error($conn);
		}
	}
	else {
		echo 'warning: Transaction already exist';
		exit();
	}

	break;

case 'updateDetail':
	if (($jmlh == '0') || ($tglDetail == '') || ($lokasi == '')) {
		echo 'warning: Please complete the Form';
		exit();
	}

	$sUpd = 'update ' . $dbname . '.pmn_rencanajualdt set  jumlah=\'' . $jmlh . '\',lokasipengiriman=\'' . $lokasi . '\',pembeli=\'' . $kdCustomer . '\' where periode=\'' . $periodeDetail . '\' and tanggal=\'' . $tglDetail . '\' and kodebarang=\'' . $kdBrg . '\' and kodeorg=\'' . $kdOrg . '\'';

	if (mysql_query($sUpd)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'loadData':
	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_rencanajualht order by `periode` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.pmn_rencanajualht order by `periode` desc limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_assoc($res)) {
			$no += 1;
			echo "\r\n" . '                        <tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $bar['periode'] . '</td>' . "\r\n" . '                        <td>' . $bar['kodeorg'] . '</td>';
			echo '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['periode'] . '\',\'' . $bar['kodeorg'] . '\');">' . "\r\n" . '                        <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="deldata(\'' . $bar['periode'] . '\',\'' . $bar['kodeorg'] . '\');"><img onclick="masterPDF(\'pmn_rencanajualht\',\'' . $bar['periode'] . ',' . $bar['kodeorg'] . '\',\'\',\'pmn_slave_rencanajualPdf\',event);" title=Print class=resicon src=images/pdf.jpg></td>' . "\r\n" . '                        </tr>';
		}

		echo "\r\n" . '                        <tr><td colspan=5 align=center>' . "\r\n" . '                        ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                        </td>' . "\r\n" . '                        </tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'loadDetail':
	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\' order by `tanggal` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\' order by `tanggal` desc limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_assoc($res)) {
			$sBrg = 'select namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar['kodebarang'] . '\'';

			#exit(mysql_error());
			($qBrg = mysql_query($sBrg)) || true;
			$rBrg = mysql_fetch_assoc($qBrg);
			$no += 1;
			echo "\r\n" . '                        <tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $bar['periode'] . '</td>' . "\r\n" . '                        <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n" . '                        <td>' . $rBrg['namabarang'] . '</td>' . "\r\n" . '                        <td>' . $bar['pembeli'] . '</td>' . "\t\t\r\n" . '                        <td>' . $bar['lokasipengiriman'] . '</td>' . "\r\n" . '                        <td align=right>' . number_format($bar['jumlah'], 2) . '</td>';
			echo "\r\n" . '                        <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillFieldDetail(\'' . $bar['periode'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $bar['pembeli'] . '\',\'' . $bar['kodebarang'] . '\',\'' . $bar['lokasipengiriman'] . '\',\'' . $bar['jumlah'] . '\');"><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delDet(\'' . $bar['periode'] . '\',\'' . tanggalnormal($bar['tanggal']) . '\',\'' . $bar['kodebarang'] . '\',\'' . $bar['kodeorg'] . '\');"></td>' . "\r\n" . '                        </tr>';
		}

		echo "\r\n" . '                        <tr><td colspan=8 align=center>' . "\r\n" . '                        ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                        <button class=mybutton onclick=cariDetail(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                        <button class=mybutton onclick=cariDetail(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                        </td>' . "\r\n" . '                        </tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'delHeader':
	$sDelLok = 'delete from ' . $dbname . '.pmn_rencanajualht where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\'';

	if (mysql_query($sDelLok)) {
		$sDelDetail = 'delete from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periode . '\' and kodeorg=\'' . $kdOrg . '\'';

		if (mysql_query($sDelDetail)) {
		}
		else {
			echo 'DB Error : ' . mysql_error($conn);
		}
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'delDet':
	$sDelDetail = 'delete from ' . $dbname . '.pmn_rencanajualdt where periode=\'' . $periodeDetail . '\' and tanggal=\'' . $tglDetail . '\' and kodebarang=\'' . $kdBrg . '\'  and kodeorg=\'' . $kdOrg . '\'';

	if (mysql_query($sDelDetail)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;
}

?>
