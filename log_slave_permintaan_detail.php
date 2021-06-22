<?php


function createTabDetail($id, $data)
{
	global $dbname;
	global $conn;
	global $optKurs;
	$table = '<b>' . $_SESSION['lang']['nopermintaan'] . '</b> : ' . makeElement('detail_kode', 'text', $_POST['idPer'], array('disabled' => 'disabled', 'style' => 'width:200px'));
	$table .= '<table id=\'ppDetailTable\'>';
	$table .= '<thead>';
	$table .= '<tr>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['spesifikasi'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kurs'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['tgldari'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['tglsmp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jumlah'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['harga'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['subtotal'] . '</td>';
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
			$columnw = array('Rupiah', 'USD');
			$optTest = makeOption('', '', $columnw, '', 3);
			$optNopp = '';
			$sql = 'SELECT a.nopp FROM ' . $dbname . '.`log_prapodt` a left join ' . $dbname . '.`log_prapoht` b on a.nopp=b.nopp where b.close=2 ' . "\r\n\t\t\t" . 'and (a.create_po is null or create_po=\'\') ' . "\r\n\t\t\t" . 'and a.kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;

			while ($rest = mysql_fetch_assoc($query)) {
				$optNopp .= '<option \'' . ($row['nopp'] == $rest['nopp'] ? 'selected=selected' : '') . '\' value=' . $rest['nopp'] . '>' . $rest['nopp'] . '</option>';
			}

			$optKurs2 = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
			$sKurs2 = 'select  distinct kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

			#exit(mysql_error());
			($qKurs2 = mysql_query($sKurs2)) || true;

			while ($rKurs2 = mysql_fetch_assoc($qKurs2)) {
				$optKurs2 .= '<option value=' . $rKurs2['kode'] . ' ' . ($row['matauang'] == $rKurs2['kode'] ? 'selected=selected' : '') . ' >' . $rKurs2['kodeiso'] . '</option>';
			}

			if ($row['tgldari'] == '') {
				$row['tgldari'] = '0000-00-00';
			}

			if ($row['tglsmp'] == '') {
				$row['tglsmp'] = '0000-00-00';
			}

			if ($row['kurs'] == '') {
				$row['kurs'] = 1;
			}

			$sub_total = $row['harga'] * $row['jumlah'];
			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td id=\'dtKdbrg_' . $key . '\'>' . makeElement('kd_brg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . ' <input type=hidden id=oldKdbrg_' . $key . ' name=oldKdbrg_' . $key . ' value=' . $row['kodebarang'] . ' /></td>';
			$table .= '<td>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('sat_' . $key . '', 'txt', $res['satuan'], array('style' => 'width:70px', 'disabled' => 'disabled', 0 => 'class=myinputtext'));
			$table .= '<td>' . makeElement('spek_' . $key . '', 'txt', $row['spec'], array('style' => 'width:230px', 0 => 'class=myinputtext', 'onkeypress' => 'return angka_doang(event)', 'maxlenght' => '100')) . '</td>';
			$table .= '<td><select id=kurs_' . $key . ' onchange=\'getKurs(' . $key . ')\'>' . $optKurs2 . '</select><input type=hidden id=jmlhKurs_' . $key . ' name=jmlhKurs_' . $key . ' value=' . $row['kurs'] . ' /></td>';
			$table .= '<td>' . makeElement('tgl_dari_' . $key . '', 'txt', tanggalnormal($row['tgldari']), array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)', 'onmousemove' => 'setCalendar(this.id)', 'readonly' => 'readonly', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('tgl_smp_' . $key . '', 'txt', tanggalnormal($row['tglsmp']), array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)', 'onmousemove' => 'setCalendar(this.id)', 'readonly' => 'readonly', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('jumlah_' . $key . '', 'textnumber', number_format($row['jumlah'], 2), array('style' => 'width:70px', 0 => 'class=myinputtext', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('price_' . $key . '', 'textnumber', number_format($row['harga'], 2, '.', ','), array('style' => 'width:100px', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('total_' . $key . '', 'textnum', number_format($sub_total, 2, '.', ','), array('style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td align=center><img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$sHrgPnwr = 'select ppn,subtotal,diskonpersen,nilaidiskon,nilaipermintaan,catatan from ' . $dbname . '.log_perintaanhargaht where nomor=\'' . $_POST['idPer'] . '\'';

	#exit(mysql_error($conn));
	($qHrgPnwr = mysql_query($sHrgPnwr)) || true;
	$rHrgPnwr = mysql_fetch_assoc($qHrgPnwr);
	if (($rHrgPnwr['subtotal'] == '') || is_null($rHrgPnwr['subtotal'])) {
		$rHrgPnwr['subtotal'] = $rHrgPnwr['ppn'] = $rHrgPnwr['diskonpersen'] = 0;
		$rHrgPnwr['nilaipermintaan'] = $rHrgPnwr['nilaidiskon'] = 0;
	}

	$table .= '<tr><td>&nbsp;</td>' . "\r\n" . '            <td colspan=8 align=right>' . $_SESSION['lang']['subtotal'] . '</td>' . "\r\n" . '            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px value=\'' . $rHrgPnwr['subtotal'] . '\' /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td >&nbsp;</td>' . "\r\n" . '            <td colspan=8 align=right>' . $_SESSION['lang']['diskon'] . 'Discount</td>' . "\r\n" . '            <td><input type=text  id=angDiskon name=angDiskon class=myinputtextnumber style=width:100px onkeyup=calculate_angDiskon() onkeypress=return angka_doang(event) onblur="getZero()" value=\'' . $rHrgPnwr['nilaidiskon'] . '\' /></td>' . "\r\n" . '        </tr>' . "\r\n\t\t" . '    <tr>' . "\r\n" . '            <td >&nbsp;</td>' . "\r\n" . '            <td colspan=8 align=right>Discount (%)</td>' . "\r\n" . '            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:100px onkeyup=calculate_diskon() maxlength=3 onkeypress=return angka_doang(event) onblur="getZero()" value=\'' . $rHrgPnwr['diskonpersen'] . '\' /> </td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=8 align=right>' . $_SESSION['lang']['diskon'] . 'PPh/PPn (%)</td>' . "\r\n" . '            <td><input type=text id=ppN name=ppN  class=myinputtextnumber style=width:100px onkeyup=calculatePpn()  maxlength=2  onkeypress=return angka_doang(event) onblur="getZero()"   />  <input type=hidden id=ppn name=ppn class=myinputtext onkeypress=return angka_doang(event) style=width:100px onblur="getZero()" /><br /><span id=hslPPn>' . $rHrgPnwr['ppn'] . '</span> </td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=8 align=right>' . $_SESSION['lang']['grnd_total'] . '</td>' . "\r\n" . '            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:100px value=\'' . $rHrgPnwr['nilaipermintaan'] . '\' /></td>' . "\r\n" . '        </tr><input type=hidden id=sub_total name=sub_total value=\'' . $rHrgPnwr['subtotal'] . '\'><input type=hidden id=nilai_diskon name=nilai_diskon value=\'' . $rHrgPnwr['nilaidiskon'] . '\'  />';
	$table .= '</tbody>';
	$table .= '</table>';

	if ($rHrgPnwr['catatan'] == '') {
		echo $table . '###';
	}
	else {
		echo $table . '###' . $rHrgPnwr['catatan'];
	}
}

session_start();
include_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

if ($_POST['proses'] == 'createTable') {
	if ($_POST['saveStat'] == 1) {
		$table = 'log_prapodt';
		$where = 'nopp=\'' . $_POST['id'] . '\' and status=0';
	}
	else {
		$where = '`nomor`=\'' . $_POST['id'] . '\'';
		$table = 'log_permintaanhargadt';
		$_POST['idPer'] = $_POST['id'];
	}

	$query = selectQuery($dbname, $table, '*', $where);
	$data = fetchData($query);
	createTabDetail($_POST['id'], $data);
}
else {
	$data = $_POST;
	unset($data['proses']);

	switch ($_POST['proses']) {
	case 'detail_add':
		if (($data['kdbrg'] == '') || ($data['jmlh'] == '')) {
			echo 'Error : Kode Barang, Spesifikasi dan Jumlah Barang Tidak Boleh Kosong';
			exit();
		}

		if ($data['tglDari'] == '') {
			$data['tglDari'] = '00-00-0000';
		}

		if ($data['tglSamp'] == '') {
			$data['tglSamp'] = '00-00-0000';
		}

		if ($data['jmlhKurs'] == '') {
			$data['jmlhKurs'] = 1;
		}

		if ($data['kurs'] == '') {
			$data['kurs'] = NULL;
		}

		$ql = 'select nomor from ' . $dbname . '.log_perintaanhargaht where `nomor`=\'' . $data['kode'] . '\'';

		#exit(mysql_error());
		($qry = mysql_query($ql)) || true;
		$res = mysql_num_rows($qry);

		if (0 < $res) {
			if ($data['price'] == '') {
				$data['price'] = 0;
			}

			$query = 'insert into ' . $dbname . '.log_permintaanhargadt (`nomor`,`kodebarang`,`harga`,`spec`,`jumlah`, `kurs`,`matauang`,`tgldari`,`tglsmp`) ' . "\r\n" . '                                            values (\'' . $data['kode'] . '\',\'' . $data['kdbrg'] . '\',\'' . $data['price'] . '\',\'' . $data['rspek'] . '\',\'' . $data['jmlh'] . '\',\'' . $data['jmlhKurs'] . '\',\'' . $data['kurs'] . '\',\'' . tanggalsystem($data['tglDari']) . '\',\'' . tanggalsystem($data['tglSamp']) . '\')';

			if (!mysql_query($query)) {
				echo 'DB Error : ' . mysql_error($conn);
			}
		}
		else {
			$ins = 'insert into ' . $dbname . '.log_perintaanhargaht (`nomor`,`tanggal`,`purchaser`,`supplierid`) ' . "\r\n\t\t\t\t\t" . 'values (\'' . $data['no_permintaan'] . '\',\'' . tanggalsystem($data['tgl']) . '\',\'' . $data['user_id'] . '\',\'' . $data['supplier_id'] . '\')';

			if (!mysql_query($ins)) {
				echo 'DB Error : ' . mysql_error($conn);
			}
			else {
				if ($data['price'] == '') {
					$data['price'] = 0;
				}

				$query = 'insert into ' . $dbname . '.log_permintaanhargadt (`nomor`,`kodebarang`,`harga`,`spec`,`jumlah`, `kurs`,`matauang`,`tgldari`,`tglsmp`) ' . "\r\n" . '                                                            values (\'' . $data['kode'] . '\',\'' . $data['kdbrg'] . '\',\'' . $data['price'] . '\',\'' . $data['rspek'] . '\',\'' . $data['jmlh'] . '\',\'' . $data['jmlhKurs'] . '\',\'' . $data['kurs'] . '\',\'' . tanggalsystem($data['tglDari']) . '\',\'' . tanggalsystem($data['tglSamp']) . '\')';

				if (!mysql_query($query)) {
					echo 'DB Error : ' . mysql_error($conn);
				}
			}
		}

		break;

	case 'detail_edit':
		if (($data['kdbrg'] == '') || ($data['jmlh'] == '')) {
			echo 'Error : Data Barang, Jumlah tidak boleh kosong';
			exit();
		}

		$where = '`nomor`=\'' . $data['kode'] . '\'';
		$where .= ' and `kodebarang`=\'' . $data['kdbrg'] . '\'';

		if ($data['price'] == '') {
			$data['price'] = 0;
		}

		$query = 'update ' . $dbname . '.`log_permintaanhargadt` set kodebarang=\'' . $data['kdbrg'] . '\',harga=\'' . $data['price'] . '\',spec=\'' . $data['rspek'] . '\',jumlah=\'' . $data['jmlh'] . '\',kurs=\'' . $data['jmlhKurs'] . '\',matauang=\'' . $data['krs'] . '\',`tgldari`=\'' . tanggalsystem($data['tglDari']) . '\',`tglsmp`=\'' . tanggalsystem($data['tglSamp']) . '\'  ' . "\r\n" . '                       where `nomor`=\'' . $data['kode'] . '\' and `kodebarang`=\'' . $data['oldKdbrg'] . '\'';

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error($conn);
		}

		break;

	case 'detail_delete':
		$data = $_POST;
		$where = '`nomor`=\'' . $data['kode'] . '\'';
		$where .= ' and `kodebarang`=\'' . $data['kdbrg'] . '\'';
		$query = 'delete from `' . $dbname . '`.`log_permintaanhargadt` where ' . $where;

		if (!mysql_query($query)) {
			echo 'DB Error : ' . mysql_error($conn);
		}

		break;
	}
}

?>
