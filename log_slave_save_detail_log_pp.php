<?php


function statusPp($o)
{
	$tes = 3;
	$i = 0;

	if ($i < $tes) {
		if ($i == '0') {
			$a = '<a href=# onclick=prosPp(' . $i . ') title="Confirm All Data">Validate</a>';
		}
		else if ($i == '1') {
			$a = '<a href=# onclick=prosPp(' . $i . ') title="Need Approval">Need Approval</a>';
		}
		else if ($i == '2') {
			$a = '<a href=# onclick=prosPp(' . $i . ') title="Can Create PO">Approved</a>';
		}

		return $a;
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
$nopp = $_POST['rnopp'];
$tanggal = tanggalsystem($_POST['rtgl_pp']);
$user_id = $_POST['usr_id'];
$kodeorg = $_POST['rkd_bag'];
$method = $_POST['method'];

switch ($method) {
case 'update':
	$strx = 'update ' . $dbname . '.log_prapoht set tanggal=\'' . $tanggal . '\',dibuat=\'' . $user_id . '\' where nopp=\'' . $nopp . '\'';

	if (!mysql_query($strx)) {
		echo 'Gagal,' . mysql_error($conn);
		exit();
	}

	$ql = 'select `nopp` from ' . $dbname . '.log_prapodt where `nopp`=\'' . $nopp . '\'';

	#exit(mysql_error());
	($qry = mysql_query($ql)) || true;
	$hsl = mysql_fetch_object($qry);

	if ($nopp == $hsl->nopp) {
		foreach ($_POST['kdbrg'] as $row => $Act) {
			$kdbrg = $Act;
			$nmbrg = $_POST['nmbrg'][$row];
			$rjmlhDiminta = $_POST['rjmlhDiminta'][$row];
			$rkd_angrn = $_POST['rkd_angrn'][$row];
			$rtgl_sdt = tanggalsystem($_POST['rtgl_sdt'][$row]);
			$ktrang = $_POST['ketrng'][$row];
			$sqp = 'update ' . $dbname . '.log_prapodt set `jumlah`=\'' . $rjmlhDiminta . '\',`kd_anggran`=\'' . $rkd_angrn . '\',`tgl_sdt`=\'' . $rtgl_sdt . '\',`keterangan`=\'' . $ktrang . '\' where nopp=\'' . $nopp . '\' and kodebarang=\'' . $kdbrg . '\'';

			if (!mysql_query($sqp)) {
				echo 'Gagal,' . mysql_error($conn);
				exit();
			}
		}
	}

	break;

case 'insert':
	foreach ($_POST['kdbrg'] as $row => $Act) {
		$kdbrg = $Act;
		$nmbrg = $_POST['nmbrg'][$row];
		$rjmlhDiminta = $_POST['rjmlhDiminta'][$row];
		$rkd_angrn = $_POST['rkd_angrn'][$row];
		$rtgl_sdt = tanggalsystem($_POST['rtgl_sdt'][$row]);
		$ketrng = $_POST['ketrng'][$row];
		$sqp = 'insert into ' . $dbname . '.log_prapodt(`nopp`, `kodebarang`, `jumlah`,`kd_anggran`,`tgl_sdt`,`keterangan`) values(\'' . $nopp . '\',\'' . $kdbrg . '\',\'' . $rjmlhDiminta . '\',\'' . $rkd_angrn . '\',\'' . $rtgl_sdt . '\',\'' . $ketrng . '\')';

		if (!mysql_query($sqp)) {
			echo 'Gagal,' . mysql_error($conn);
			exit();
		}
	}

	break;
}

$str = ($nopp = $_POST['rnopp']);

if ($res = mysql_query($str)) {
	while ($bar = mysql_fetch_object($res)) {
		$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $bar->kodeorg . '\' or induk=\'' . $bar->kodeorg . '\'';

		#exit(mysql_error($conn));
		($rep = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rep);
		$no += 1;
		echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t" . '      <td>' . $no . '</td>' . "\r\n\t\t" . '      <td>' . $bar->nopp . '</td>' . "\r\n\t\t\t" . '  <td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t" . '  <td>' . $bar->kodeorg . '</td>' . "\r\n\t\t\t" . '  <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t" . '  <td>' . statuspp($bar->close) . '</td>' . "\r\n\t\t" . ' <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->nopp . '\',\'' . tanggalnormal($bar->tanggal) . '\',\'' . $bar->kodeorg . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPp(\'' . $bar->nopp . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_prapoht\',\'' . $bar->nopp . '\',\'\',\'log_slave_print_log_pp\',event);"></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

?>
