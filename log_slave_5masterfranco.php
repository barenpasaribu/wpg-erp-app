<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';
$method = $_POST['method'];
$idFranco = $_POST['idFranco'];
$nmFranco = $_POST['nmFranco'];
$almtFranco = $_POST['almtFranco'];
$cntcPerson = $_POST['cntcPerson'];
$hdnPhn = $_POST['hdnPhn'];
$statFr = $_POST['statFr'];

switch ($method) {
case 'insert':
	if ($nmFranco == '') {
		echo 'warning:Nama Franco tidak boleh kosong';
		exit();
	}

	$sCek = 'select franco_name from ' . $dbname . '.setup_franco where franco_name=\'' . $nmFranco . '\'';

	#exit(mysql_error($conn));
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if (0 < $rCek) {
		echo 'warning:Nama Franco sudah ada';
		exit();
	}
	else {
		if (($almtFranco == '') || ($cntcPerson == '')) {
			echo 'warning:Alamat dan Contat Person tidak boleh kosong';
			exit();
		}
		else {
			$sIns = 'insert into ' . $dbname . '.setup_franco (`franco_name`,`alamat`,`contact`,`handphone`,`status`,`updateby`) values (\'' . $nmFranco . '\',\'' . $almtFranco . '\',\'' . $cntcPerson . '\',\'' . $hdnPhn . '\',\'' . $statFr . '\',\'' . $_SESSION['standard']['userid'] . '\')';

			if (!mysql_query($sIns)) {
				echo 'Gagal' . mysql_error($conn);
			}
		}
	}

	break;

case 'loadData':
	$no = 0;
	$arr = array('Aktif', 'Tidak Aktif');
	$str = 'select * from ' . $dbname . '.setup_franco order by id_franco desc';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_assoc($res)) {
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t" . '<td>' . $bar['franco_name'] . '</td>' . "\r\n\t\t" . '<td>' . substr($bar['alamat'], 0, 50) . '</td>' . "\r\n\t\t" . '<td>' . $bar['contact'] . '</td>' . "\r\n\t\t" . '<td>' . $bar['handphone'] . '</td>' . "\r\n\t\t" . '<td>' . $arr[$bar['status']] . '</td>' . "\r\n\t\t" . '<td>' . "\r\n\t\t\t" . '  <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar['id_franco'] . '\');"> ' . "\r\n\t\t\t" . '  <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delData(\'' . $bar['id_franco'] . '\');">' . "\r\n\t\t" . '  </td>' . "\r\n\t\t\r\n\t\t" . '</tr>';
	}

	break;

case 'update':
	if (($almtFranco == '') || ($cntcPerson == '')) {
		echo 'warning:Alamat dan Contat Person tidak boleh kosong';
		exit();
	}
	else {
		$sUpd = 'update ' . $dbname . '.setup_franco set `alamat`=\'' . $almtFranco . '\',`contact`=\'' . $cntcPerson . '\',`handphone`=\'' . $hdnPhn . '\',`status`=\'' . $statFr . '\' where id_franco=\'' . $idFranco . '\'';

		if (!mysql_query($sUpd)) {
			echo 'Gagal' . mysql_error($conn);
		}
	}

	break;

case 'delData':
	$sDel = 'delete from ' . $dbname . '.setup_franco where id_franco=\'' . $idFranco . '\'';

	if (!mysql_query($sDel)) {
		echo 'Gagal' . mysql_error($conn);
	}

	break;

case 'getData':
	$sDt = 'select * from ' . $dbname . '.setup_franco where id_franco=\'' . $idFranco . '\'';

	#exit(mysql_error($conn));
	($qDt = mysql_query($sDt)) || true;
	$rDet = mysql_fetch_assoc($qDt);
	echo $rDet['id_franco'] . '###' . $rDet['franco_name'] . '###' . $rDet['alamat'] . '###' . $rDet['contact'] . '###' . $rDet['handphone'] . '###' . $rDet['status'];
	break;
}

?>
