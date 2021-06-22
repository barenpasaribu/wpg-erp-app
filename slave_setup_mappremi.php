<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
$method = $_POST['method'];
$kodeorg = $_POST['kdOrg'];
$tipePremi = $_POST['tipePremi'];
$keyCode = $_POST['kyCode'];
$oldData = $_POST['oldData'];
$oldTipePremi = $_POST['oldTipePremi'];

switch ($method) {
case 'loadNewData':
	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_mappremi  order by kodeorg,keycode desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.setup_mappremi order by kodeorg,keycode desc limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_object($res)) {
			$sPt = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeorg . '\'';

			#exit(mysql_error());
			($qPt = mysql_query($sPt)) || true;
			$rOrg = mysql_fetch_assoc($qPt);
			$no += 1;
			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t" . '<td>' . $no . '</td>' . "\r\n\t" . '<td id=\'nmorg_' . $no . '\'>' . $rOrg['namaorganisasi'] . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->tipepremi . '</td>' . "\r\n\t" . '<td id=\'kpsits_' . $no . '\'>' . $bar->keycode . '</td>' . "\r\n\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kodeorg . '\',\'' . $bar->tipepremi . '\',\'' . $bar->keycode . '\');"><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delCode(\'' . $bar->kodeorg . '\',\'' . $bar->tipepremi . '\',,\'' . $bar->keycode . '\');"></td>' . "\r\n\t" . '</tr>';
		}

		echo ' ' . "\r\n\t" . '</tr><tr class=rowheader><td colspan=5 align=center>' . "\r\n\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . "\r\n\t" . '<br />' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t" . '</td></tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'insert':
	$sCek = 'select * from ' . $dbname . '.setup_mappremi where kodeorg=\'' . $kodeorg . '\' and tipepremi=\'' . $tipePremi . '\' and keycode=\'' . $keyCode . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_fetch_row($qCek);

	if ($rCek < 1) {
		$sIns = 'insert into ' . $dbname . '.setup_mappremi (`kodeorg`,`tipepremi`,`keycode`) values (\'' . $kodeorg . '\',\'' . $tipePremi . '\',\'' . $keyCode . '\')';

		if (mysql_query($sIns)) {
			echo '';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}
	}
	else {
		echo 'warning:This Data Already Input';
		exit();
	}

	break;

case 'updateData':
	$sUp = 'update  ' . $dbname . '.setup_mappremi set keycode=\'' . $keyCode . '\',tipepremi=\'' . $tipePremi . '\' where kodeorg=\'' . $kodeorg . '\' and tipepremi=\'' . $oldTipePremi . '\' and keycode=\'' . $oldData . '\' ';

	if (mysql_query($sUp)) {
		echo '';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'deleteData':
	$sDel = 'delete from ' . $dbname . '.setup_mappremi where kodeorg=\'' . $kodeorg . '\' and tipepremi=\'' . $tipePremi . '\' and keycode=\'' . $keyCode . '\'';

	if (mysql_query($sDel)) {
		echo '';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;
}

?>
