<?php


function createTabDetail($id, $data)
{
	$table .= '<table id=\'ppDetailTable\'>';
	$table .= '<thead>';
	$table .= '<tr>';
	$table .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlh_brg_blm_po'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlhPesan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kurs'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['subtotal'] . '</td>';
	$table .= '<td>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody id=\'detailBody\'>';

	if ($data != array()) {
		foreach ($data as $key => $row) {
			$ql = 'select * from e-Agro.`log_5masterbarang` where `kodebarang`=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qry = mysql_query($ql)) || true;
			$res = mysql_fetch_assoc($qry);
			$columnw = array(1 => 'IDR', 0 => 'USD');
			$optTest = makeOption('', '', $columnw, '', 3);
			$where = ' kodebarang=\'' . $row['kodebarang'] . '\' and darisatuan=\'' . $res['satuan'] . '\'';
			$optSatuan = makeOption('e-Agro', 'log_5stkonversi', 'satuankonversi', $where, 1);
			$sqjmlh = 'select selisih,jlpesan,realisasi from e-Agro.log_sudahpo_vsrealisasi_vw where nopp=\'' . $row['nopp'] . '\' and kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qujmlh = mysql_query($sqjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qujmlh);

			if ($resjmlh['jlpesan'] != '') {
				$row['realisasi'] = $resjmlh['realisasi'] - $resjmlh['selisih'];
			}

			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td>' . makeElement('rnopp_' . $key . '', 'txt', $row['nopp'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('rkdbrg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('realisasi_' . $key . '', 'txt', $resjmlh['selisih'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('jmlhDiminta_' . $key . '', 'textnum', '', array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'display_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('sat_' . $key . '', 'select', $res['satuan'], array('style' => 'width:70px'), $optSatuan) . '</td>';
			$table .= '<td>' . makeElement('kurs_' . $key . '', 'select', $row['kurs'], array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)'), $optTest) . '</td>';
			$table .= '<td>' . makeElement('harga_satuan_' . $key . '', 'textnum', '', array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'calculate(\'' . $key . '\')', 'onblur' => 'periksa_isi(this)', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('total_' . $key . '', 'textnum', '', array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td align_center><img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$table .= '<tr><td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['subtotal'] . '</td>' . "\r\n" . '            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber style=width:70px /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td >&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>Discount(%)</td>' . "\r\n" . '            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:70px  onkeyup=calculate_diskon()  maxlength=3 /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>PPh/PPn(%)</td>' . "\r\n" . '            <td><input type=text id=ppn name=ppn disabled class=myinputtextnumber style=width:70px /></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['grnd_total'] . '</td>' . "\r\n" . '            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:70px  /></td>' . "\r\n" . '        </tr><input type=hidden id=sub_total name=sub_total ><input type=hidden id=nilai_diskon name=nilai_diskon  />';
	$table .= '</tbody>';
	$table .= '</table> <br />';
	echo $table;
}

function createTabEditDetail($id, $data)
{
	$table .= '<table id=\'ppDetailTable\'>';
	$table .= '<thead>';
	$table .= '<tr>';
	$table .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlh_brg_blm_po'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlhPesan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kurs'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['subtotal'] . '</td>';
	$table .= '<td>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody id=\'detailBody\'>';

	if ($data != array()) {
		foreach ($data as $key => $row) {
			$ql = 'select * from e-Agro.`log_5masterbarang` where `kodebarang`=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qry = mysql_query($ql)) || true;
			$res = mysql_fetch_assoc($qry);
			$columnw = array(1 => 'IDR', 0 => 'USD');
			$optTest = makeOption('', '', $columnw, '', 3);
			$where = ' kodebarang=\'' . $row['kodebarang'] . '\' and darisatuan=\'' . $res['satuan'] . '\'';
			$optSatuan = makeOption('e-Agro', 'log_5stkonversi', 'satuankonversi', $where, 1);
			$sqpp = 'select * from e-Agro.log_sudahpo_vsrealisasi_vw where nopp=\'' . $row['nopp'] . '\' and kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qpp = mysql_query($sqpp)) || true;
			$rpp = mysql_fetch_assoc($qpp);
			$sub_tot = $row['jumlahpesan'] * $row['hargasbldiskon'];
			$sjmlh = 'select b.jumlahpesan from e-Agro.log_poht a inner join e-Agro.log_podt b on a.nopo=b.nopo ' . "\r\n\t\t\t" . 'where kodebarang=\'' . $row['kodebarang'] . '\' and a.statuspo=\'2\' and nopp=\'' . $row['nopp'] . '\'';

			#exit(mysql_error());
			($qjmlh = mysql_query($sjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qjmlh);

			if ($resjmlh['jumlahpesan'] != '') {
				$rpp['selisih'] = $rpp['realisasi'] - $resjmlh['jumlahpesan'];
			}
			else {
				$rpp['selisih'] = $rpp['realisasi'];
			}

			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td>' . makeElement('rnopp_' . $key . '', 'txt', $row['nopp'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('rkdbrg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('realisasi_' . $key . '', 'txt', $rpp['selisih'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('jmlhDiminta_' . $key . '', 'textnum', $row['jumlahpesan'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'display_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('sat_' . $key . '', 'select', $row['satuan'], array('style' => 'width:70px'), $optSatuan) . '</td>';
			$table .= '<td>' . makeElement('kurs_' . $key . '', 'select', $row['matauang'], array('style' => 'width:70px', 'onkeypress' => 'return tanpa_kutip(event)'), $optTest) . '</td>';
			$table .= '<td>' . makeElement('harga_satuan_' . $key . '', 'textnum', number_format($row['hargasbldiskon'], 2, '.', ','), array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'calculate(\'' . $key . '\')', 'onblur' => 'periksa_isi(this)', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('total_' . $key . '', 'textnum', number_format($sub_tot, 2, '.', ','), array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td align=center><img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$table .= '<tr><td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['subtotal'] . '</td>' . "\r\n" . '            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:70px /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td >&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . 'Discount(%)</td>' . "\r\n" . '            <td><input type=text  id=diskon name=diskon class=myinputtextnumber style=width:70px onkeyup=calculate_diskon() maxlength=3 onkeypress=return angka_doang(event) /></td>' . "\r\n" . '        </tr>' . "\r\n" . '        <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . 'PPh/PPn(%)</td>' . "\r\n" . '            <td><input type=text id=ppn name=ppn disabled class=myinputtextnumber style=width:70px  /></td>' . "\r\n" . '        </tr>' . "\r\n" . '         <tr>' . "\r\n" . '            <td>&nbsp;</td>' . "\r\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['grnd_total'] . '</td>' . "\r\n" . '            <td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:70px /></td>' . "\r\n" . '        </tr><input type=hidden id=sub_total name=sub_total ><input type=hidden id=nilai_diskon name=nilai_diskon  />';
	$table .= '</tbody>';
	$table .= '</table> <br />';
	echo $table;
}

session_start();
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

if ($_POST['proses'] == 'createTable') {
	$rnopp = $_POST['nopp'];

	foreach ($rnopp as $row => $Rslt) {
		$kdbrg = $_POST['kdbrg'];

		if ($row == 0) {
			$where .= ' nopp=\'' . $Rslt . '\'';
			$where2 .= ' kodebarang=' . $kdbrg[$row];
		}
		else {
			$where .= ' or nopp=\'' . $Rslt . '\'';
			$where2 .= ' or kodebarang=' . $kdbrg[$row];
		}
	}

	$query = 'select * from ' . $dbname . '.log_prapodt where (' . $where . ') and (' . $where2 . ')';
	$data = fetchData($query);
	$rnopp = $_POST['nopp'];
	$tgl = date('Ymd');
	$bln = substr($tgl, 4, 2);
	$thn = substr($tgl, 0, 4);
	$where = '';
	$where2 = '';

	foreach ($rnopp as $row => $Rslt) {
		$kdbrg = $_POST['kdbrg'];

		if ($row == 0) {
			$where .= ' nopp=\'' . $Rslt . '\'';
			$where2 .= ' kodebarang=' . $kdbrg[$row];
		}
		else {
			$where .= ' or nopp=\'' . $Rslt . '\'';
			$where2 .= ' or kodebarang=' . $kdbrg[$row];
		}
	}

	$sql = 'select * from ' . $dbname . '.log_prapodt where (' . $where . ') and (' . $where2 . ')';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$cond = '';
	$i = 0;

	while ($res = mysql_fetch_assoc($query)) {
		if ($res['lokalpusat'] == 0) {
			$sql3 = 'select kodeorganisasi from ' . $dbname . '.organisasi where tipe=\'HOLDING\' and length(kodeorganisasi)=4';

			#exit(mysql_error());
			($query3 = mysql_query($sql3)) || true;
			$res3 = mysql_fetch_assoc($query3);
		}

		$nopp = substr($res['nopp'], 15, 4);

		if ($i == 0) {
			$cond .= ' kodeorganisasi=\'' . $nopp . '\'';
		}
		else {
			$cond .= ' or kodeorganisasi=\'' . $nopp . '\'';
		}

		++$i;
	}

	$sql2 = 'select induk from ' . $dbname . '.organisasi where (' . $cond . ')';

	exit(myql_error());
	($query2 = mysql_query($sql2)) || true;
	$res2 = mysql_fetch_assoc($query2);
	$nopo = '/' . date('m') . '/' . date('Y') . '/PO/' . $res3['kodeorganisasi'] . '/' . $res2['induk'];
	$ql = 'select `nopo` from ' . $dbname . '.`log_poht` where nopo like \'%' . $nopo . '%\' order by `nopo` desc limit 0,1';

	#exit(mysql_error());
	($qr = mysql_query($ql)) || true;
	$rp = mysql_fetch_object($qr);
	$awal = substr($rp->nopo, 0, 3);
	$awal = intval($awal);
	$cekbln = substr($rp->nopo, 4, 2);
	$cekthn = substr($rp->nopo, 7, 4);

	if (($bln != $cekbln) && ($thn != $cekthn)) {
		$awal = 1;
	}
	else {
		++$awal;
	}

	$counter = addZero($awal, 3);
	$nopo = $counter . '/' . $bln . '/' . $thn . '/PO/' . $res3['kodeorganisasi'] . '/' . $res2['induk'];
	echo $nopo . ',';
	createTabDetail($Rslt, $data);
}

if ($_POST['proses'] == 'edit_po') {
	$query = 'select * from ' . $dbname . '.log_podt where nopo=\'' . $_POST['nopo'] . '\'';
	$data = fetchData($query);
	createTabEditDetail($_POST['nopo'], $data);
}

if ($_POST['proses'] == 'listPp') {
	$kode_pt = $_POST['kodept'];
	$user_id = $_POST['id_user'];

	if ($user_id != $_SESSION['standard']['userid']) {
	}

	$user_id = $_SESSION['standard']['userid'];
	$sql = 'select kodeorganisasi from ' . $dbname . '.organisasi where induk=\'' . $kode_pt . '\' or alokasi=\'' . $kode_pt . '\'';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$where = '';
	$i = 0;

	while ($row1 = mysql_fetch_assoc($query)) {
		if ($i == 0) {
			$where .= ' \'' . $row1['kodeorganisasi'] . '\'';
		}
		else {
			$where .= ' ,\'' . $row1['kodeorganisasi'] . '\'';
		}

		++$i;
	}

	$sql2 = 'select * from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw  where (kodept=\'' . $_POST['kodept'] . '\' and purchaser=\'' . $user_id . '\' and lokalpusat=\'0\' and status!=\'3\') and (selisih>0 or selisih is null)';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;

	while ($res2 = mysql_fetch_object($query2)) {
		$no += 1;
		$sbrg = 'select * from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res2->kodebarang . '\'';

		#exit(mysql_error());
		($qbrg = mysql_query($sbrg)) || true;
		$rbrg = mysql_fetch_object($qbrg);
		echo "\r\n\t\t\t" . ' <tr class=rowcontent ' . $show . ' id=tr_' . $no . '>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td id=nopp_' . $no . '>' . $res2->nopp . '</td>' . "\r\n\t\t\t\t" . '<td id=kdbrg_' . $no . '>' . $rbrg->kodebarang . '</td>' . "\r\n\t\t\t\t" . '<td>' . $rbrg->namabarang . '</td>' . "\r\n\t\t\t\t" . '<td>' . $rbrg->satuan . '</td>' . "\r\n\t\t\t\t" . '<td align=center>' . $res2->realisasi . '</td>';

		if ($res2->selisih == '') {
			echo '<td align=center>' . $res2->realisasi . '</td>';
		}
		else if ($res2->selisih != $res2->realisasi) {
			$blm_pesan = $res2->selisih;
			echo '<td align=center>' . $blm_pesan . '</td>';
		}

		echo '<td align=center><input type=checkbox id=plh_pp_' . $no . ' name=plh_pp_' . $no . ' ' . $test . ' /></td>' . "\r\n\t\t\t" . ' </tr>';
	}

	echo '<tr><td colspan=6 align=center>' . "\r\n\t\t" . '<button name=process id=process onclick=process()>' . $_SESSION['lang']['proses'] . '</button>' . "\r\n\t\t" . '<button name=cancel id=cancel onclick=cancel_headher()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t\t" . '</td></tr>';
}

if ($_POST['proses'] == 'detail_delete') {
	$data = $_POST;
	$where = '`nopo`=\'' . $data['nopo'] . '\'';
	$where .= ' and `kodebarang`=\'' . $data['kd_brg'] . '\'';
	$where .= ' and `nopp`=\'' . $data['nopp'] . '\'';
	$query = 'delete from `' . $dbname . '`.`log_podt` where ' . $where;

	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
}

?>
