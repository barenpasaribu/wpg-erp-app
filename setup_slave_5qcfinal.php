<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$nmQc = $_POST['nmQc'];
$wrnaId = $_POST['wrnaId'];
$maxDt = $_POST['maxDt'];
$nmQcOld = $_POST['nmQcOld'];

switch ($method) {
case 'insert':
	$sIns = 'insert into ' . $dbname . '.qc_5final (name, color, max)' . "\r\n" . '                           values (\'' . $nmQc . '\',\'' . $wrnaId . '\',\'' . $maxDt . '\')';

	if (!mysql_query($sIns)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'loadData':
	$no = 0;
	$str = 'select * from ' . $dbname . '.qc_5final order by name asc';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_assoc($res)) {
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $bar['name'] . '</td>' . "\r\n\t\t" . '<td>' . $bar['color'] . '</td>' . "\r\n\t\t" . '<td>' . $bar['max'] . '</td>' . "\r\n\t\t" . '<td align=center>' . "\r\n\t\t\t" . '  <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['name'] . '\',\'' . $bar['color'] . '\',\'' . $bar['max'] . '\');">' . "\r\n\t\t\t" . '  <!--<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . $bar['tipe'] . '\',\'' . $bar['id'] . '\');">-->' . "\r\n\t\t" . '  </td>' . "\r\n\t\t\r\n\t\t" . '</tr>';
	}

	break;

case 'updateData':
	$sUpd = 'update ' . $dbname . '.qc_5final set `name`=\'' . $nmQc . '\',`color`=\'' . $wrnaId . '\',`max`=\'' . $maxDt . '\'' . "\r\n" . '                              where name=\'' . $nmQcOld . '\'';

	if (!mysql_query($sUpd)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.setup_franco where id_franco=\'' . $idFranco . '\'';

	if (!mysql_query($sDel)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'getData':
	$sDt = 'select distinct id from ' . $dbname . '.qc_5parameter  order by id desc';

	#exit(mysql_error($conn));
	($qDt = mysql_query($sDt)) || true;
	$rDet = mysql_fetch_assoc($qDt);
	$dt = 1 + intval($rDet['id']);
	echo $dt;
	break;
}

?>
