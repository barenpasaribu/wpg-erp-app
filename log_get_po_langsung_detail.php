<?php


function createTabDetail($id, $data)
{
	global $dbname;
	global $conn;
	global $key;
	global $method;
	$table = '<b>NoPL</b> : ' . makeElement('detail_kode', 'text', $id, array('disabled' => 'disabled', 'style' => 'width:200px'));
	$table .= '<table id=\'ppDetailTable\'>';
	$table .= '<thead>';
	$table .= '<tr>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>Jumlah</td>';
	$table .= '<td>Harga</td>';
	$table .= '<td>' . $_SESSION['lang']['keterangan'] . '</td>';
	$table .= '<td colspan=3>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody id=\'detailBody\'>';
	$i = 0;

	if ($data != array()) {
		foreach ($data as $key => $row) {
			$ql = 'select * from ' . $dbname . '.`log_5masterbarang` where `kodebarang`=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qry = mysql_query($ql)) || true;
			$res = mysql_fetch_assoc($qry);
			$tmpTgl = tanggalnormal($row['tgl_sdt']);
			$row['tgl_sdt'] = $tmpTgl;
			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $key . '>\',event)";>' . makeElement('kd_brg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $key . '>\',event)";>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $key . '>\',event)";>' . makeElement('sat_' . $key . '', 'txt', $res['satuan'], array('style' => 'width:70px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '<img src=images/search.png class=dellicon title=' . $_SESSION['lang']['find'] . ' onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $key . '>\',event)";><input type=hidden id=oldKdbrg_' . $key . ' name=oldKdbrg_' . $key . ' value=\'' . $row['kodebarang'] . '\'></td>';
			// Jumlah
			$table .= '<td>' . makeElement('jumlah_' . $key . '', 'textnum', $row['jumlah'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 0 => 'class=myinputtext')) . '</td>';
			// Harga
			$table .= '<td>' . makeElement('hargasatuan_' . $key . '', 'textnum', $row['hargasatuan'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 0 => 'class=myinputtext')) . '</td>';
			
			$table .= '<td>' . makeElement('ket_' . $key . '', 'txt', $row['keterangan'], array('style' => 'width:130px', 0 => 'class=myinputtext', 'onkeypress' => 'return tanpa_kutip(event)')) . '</td>';
			$table .= '<td><img id=\'detail_edit_' . $key . '\' title=\'Edit\' class=zImgBtn onclick="editDetail(\'' . $key . '\')" src=\'images/save.png\'/>';
			$table .= '&nbsp;<img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$table .= '<tr id=\'detail_tr_' . $i . '\' class=\'rowcontent\'>';
	$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('kd_brg_' . $i . '', 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
	
	$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('nm_brg_' . $i . '', 'txt', '', array('style' => 'width:120px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '</td>';
	
	$table .= '<td onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $i . '>\',event)";>' . makeElement('sat_' . $i . '', 'txt', '', array('style' => 'width:70px', 'readonly' => 'readonly', 'cursor' => 'pointer', 0 => 'class=myinputtext')) . '<img src=images/search.png class=dellicon title=' . $_SESSION['lang']['find'] . ' onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><input type=hidden id=nomor name=nomor value=' . $i . '><div id=container></div>\',event)";><input type=hidden id=oldKdbrg_' . $i . ' name=oldKdbrg_' . $i . '>' . '</td>';
	// Jumlah
	$table .= '<td>' . makeElement('jumlah_' . $i . '', 'textnum', '', array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 0 => 'class=myinputtext')) . '</td>';
	// Harga
	$table .= '<td>' . makeElement('hargasatuan_' . $i . '', 'textnum', '', array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 0 => 'class=myinputtext')) . '</td>';
	
	$table .= '<td>' . makeElement('ket_' . $i . '', 'txt', '', array('style' => 'width:130px', 0 => 'class=myinputtext', 'onkeypress' => 'return tanpa_kutip(event)')) . '</td>';
	$table .= makeElement('nopp_' . $i . '', 'hidden', $id, array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)')) . '</td>';
	$table .= '<td><img id=\'detail_add_' . $i . '\' title=\'Simpan\' class=zImgBtn onclick="addDetail(\'' . $i . '\')" src=\'images/save.png\'/>';
	$table .= '&nbsp;<img id=\'detail_delete_' . $i . '\' /></td>';
	$table .= '</tr>';
	$table .= '</tbody>';
	$table .= '</table>';

	if ($method == 'update') {
		$whrdt = 'nopl=\'' . $id . '\'';
		$cttn = makeOption($dbname, 'log_pol_ht', 'nopl,catatan', $whrdt);
		echo $table . '####' . $cttn[$id];
	}
	else {
		echo $table;
	}
}

session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

if ($_POST['proses'] == 'createTable') {
	$query = selectQuery($dbname, 'log_pol_dt', '*', '`nopl`=\'' . $_POST['id'] . '\'');
	$data = fetchData($query);
	$method = $_POST['method'];
	createTabDetail($_POST['id'], $data);
}
else {
	$data = $_POST;
	$tglsdt = tanggalsystem($data['tgl_sdt']);
	$rtglpp = tanggalsystem($data['rtgl_pp']);
	unset($data['proses']);

	switch ($_POST['proses']) {
	case 'detail_add':
		$ql = 'select * from ' . $dbname . '.log_pol_ht where `nopl`=\'' . $data['kode'] . '\'';

		#exit(mysql_error());
		($qry = mysql_query($ql)) || true;
		$res = mysql_fetch_assoc($qry);

		if ($res['nopl'] != '') {
			$query = 'insert into ' . $dbname . '.log_pol_dt (`nopl`,`kodebarang`,`jumlah`,`hargasatuan`,`keterangan`) values (\'' . $data['kode'] . '\',\'' . $data['kd_brg'] . '\',\'' . $data['jmlhDiminta'] . '\',\'' . $data['hargasatuan'] . '\',\'' . $data['ket'] . '\')';

			if (!mysql_query($query)) {
				echo 'DB Error : ' . mysql_error($conn);
			}
		}
		else {
			$nopp = $data['kode'];
			$tgl = tanggalsystem($data['rtgl_pp']);
			$kodeorg = $data['rkd_bag'];
			$id_user = $_POST['user_id'];
			$sorg = 'select alokasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $kodeorg . '\'';

			#exit(mysql_error());
			($qorg = mysql_query($sorg)) || true;
			$rorg = mysql_fetch_assoc($qorg);
			$kd_org = $rorg['alokasi'];
			$ins = 'insert into ' . $dbname . '.log_pol_ht (`nopl`,`kodeorg`,`tanggal`,`dibuat`,`catatan`) values (\'' . $nopp . '\',\'' . $kd_org . '\',\'' . $tgl . '\',\'' . $id_user . '\',\'' . $_POST['catatan'] . '\')';

			#exit(mysql_error());
			($qry = mysql_query($ins)) || true;
			$query = 'insert into ' . $dbname . '.log_pol_dt (`nopp`,`kodebarang`,`jumlah`,`hargasatuan`,`keterangan`) values (\'' . $data['kode'] . '\',\'' . $data['kd_brg'] . '\',\'' . $data['jmlhDiminta'] . '\',\'' . $data['hargasatuan'] . '\',\'' . $data['ket'] . '\')';

			if (!mysql_query($query)) {
				echo 'DB Error : ' . mysql_error($conn);
			}
		}

		break;

	case 'detail_edit':
		if (($data['nopp'] == '') || ($data['kd_brg'] == '') || ($data['jmlhDiminta'] == '')) {
			echo 'Error : Data should not be empty';
			exit();
		}

		$where = '`nopl`=\'' . $data['nopp'] . '\'';
		$where .= ' and `kodebarang`=\'' . $data['kd_brg'] . '\'';
		$query = 'update ' . $dbname . '.`log_pol_dt` set kodebarang=\'' . $data['kd_brg'] . '\',jumlah=\'' . $data['jmlhDiminta'] . '\',hargasatuan=\'' . $data['hargasatuan'] . '\', keterangan=\'' . $data['ket'] . '\' where `nopl`=\'' . $data['nopp'] . '\' and `kodebarang`=\'' . $data['oldKdbrg'] . '\'';

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error($conn);
		}

		echo $query;
		exit();
		break;

	case 'detail_delete':
		$data = $_POST;
		$tmpTgl = tanggalsystem($data['tgl_sdt']);
		$data['tgl_sdt'] = $tmpTgl;
		$where = '`nopl`=\'' . $data['nopp'] . '\'';
		$where .= ' and `kodebarang`=\'' . $data['kd_brg'] . '\'';
		$query = 'delete from `' . $dbname . '`.`log_pol_dt` where ' . $where;

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error($conn);
		}

		break;
	}
}

?>
