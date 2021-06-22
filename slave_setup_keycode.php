<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$method = $_POST['method'];
$Code = $_POST['Code'];
$ket = $_POST['ket'];
$oldCode = $_POST['oldCode'];

switch ($method) {
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
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_keycode  order by code desc ';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.setup_keycode order by code desc limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t" . '<td>' . $no . '</td>' . "\r\n\t" . '<td id=\'nmorg_' . $no . '\'>' . $bar->code . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->keterangan . '</td>' . "\r\n\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->code . '\',\'' . $bar->keterangan . '\');"></td>' . "\r\n\t" . '</tr>';
		}

		echo ' ' . "\r\n\t" . '</tr><tr class=rowheader><td colspan=3 align=center>' . "\r\n\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '<br />' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '</td></tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'insert':
	if (($Code == '') || ($ket == '')) {
		echo 'warning:Please Complete The Form';
		exit();
	}

	$sCek = 'select code from ' . $dbname . '.setup_keycode where code=\'' . $Code . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_row($qCek);

	if ($rCek < 1) {
		$sIns = 'insert into ' . $dbname . '.setup_keycode (`code`,`keterangan`) values (\'' . $Code . '\',\'' . $ket . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}
	}
	else {
		echo 'warning:Data Already Input';
	}

	break;

case 'updateCode':
	if (($Code == '') || ($ket == '')) {
		echo 'warning:Please Complete The Form';
		exit();
	}

	$sUpd = 'update  ' . $dbname . '.setup_keycode set code=\'' . $Code . '\', keterangan=\'' . $ket . '\' where code=\'' . $oldCode . '\'';

	if (mysql_query($sUpd)) {
		echo '';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.setup_keycode where code=\'' . $Code . '\'';

	if (mysql_query($sDel)) {
		echo '';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;
}

?>
