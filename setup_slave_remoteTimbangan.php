<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
$proses = $_POST['proses'];
$loksi = $_POST['loksi'];
$ipAdd = $_POST['ipAdd'];
$idRemote = $_POST['idRemote'];
$ipAdd = $_POST['ipAdd'];
$userName = $_POST['userName'];
$passwrd = $_POST['passwrd'];
$port = $_POST['port'];
$dbnm = $_POST['dbnm'];

switch ($proses) {
case 'LoadData':
	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_remotetimbangan order by `id` desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$str = 'select * from ' . $dbname . '.setup_remotetimbangan order by `id` desc limit ' . $offset . ',' . $limit . '';

	if ($res = mysql_query($str)) {
		while ($bar = mysql_fetch_object($res)) {
			$no += 1;
			echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->lokasi . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->ip . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->username . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->password . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->port . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->dbname . '</td>' . "\r\n\t\t\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->id . '\');"><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="deldata(\'' . $bar->id . '\');"></td>' . "\r\n\t\t\t" . '</tr>';
		}

		echo "\r\n\t\t\t" . '<tr><td colspan=8 align=center>' . "\r\n\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t" . '</td>' . "\r\n\t\t\t" . '</tr>';
	}
	else {
		echo ' Gagal,' . mysql_error($conn);
	}

	break;

case 'insert':
	if (($loksi == '') || ($ipAdd == '') || ($userName == '') || ($port == '') || ($passwrd == '') || ($dbnm == '')) {
		echo 'warning: Lengkapi Form Inputan';
		exit();
	}

	if (!preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ipAdd)) {
		echo 'warning:Please Input Valid IP Address';
		exit();
	}

	$sIns = 'insert into ' . $dbname . '.setup_remotetimbangan (lokasi, ip, username, password, port,dbname) values (\'' . $loksi . '\', \'' . $ipAdd . '\', \'' . $userName . '\', \'' . $passwrd . '\', \'' . $port . '\',\'' . $dbnm . '\')';

	if (mysql_query($sIns)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'showData':
	$sql = 'select* from ' . $dbname . '.setup_remotetimbangan where id=\'' . $idRemote . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$res = mysql_fetch_assoc($query);
	echo $res['id'] . '###' . $res['lokasi'] . '###' . $res['ip'] . '###' . $res['username'] . '###' . $res['password'] . '###' . $res['port'] . '###' . $res['dbname'];
	break;

case 'update':
	if (($loksi == '') || ($ipAdd == '') || ($userName == '') || ($port == '') || ($passwrd == '') || ($dbnm == '')) {
		echo 'warning: Lengkapi Form Inputan';
		exit();
	}

	if (!preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ipAdd)) {
		echo 'warning:Please Input Valid IP Address';
		exit();
	}

	$sUpd = 'update ' . $dbname . '.setup_remotetimbangan set   lokasi=\'' . $loksi . '\', ip=\'' . $ipAdd . '\', username=\'' . $userName . '\', password=\'' . $passwrd . '\', port=\'' . $port . '\',dbname=\'' . $dbnm . '\'  where  id=\'' . $idRemote . '\'';

	if (mysql_query($sUpd)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.setup_remotetimbangan where id=\'' . $idRemote . '\'';

	if (mysql_query($sDel)) {
		echo '';
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}

	break;
}

?>
