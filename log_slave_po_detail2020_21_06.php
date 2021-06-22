<?php

function createTabDetail($id, $data)
{
	global $conn;
	global $dbname;
	$table .= '<thead class=rowheader>';
	$table .= '<tr>';
	$table .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['spesifikasi'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlh_brg_blm_po'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlhPesan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['subtotal'] . '</td>';
	$table .= '<td>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody id=\'detailBody\'>';

	if ($data != array()) {
		foreach ($data as $key => $row) {
			$ql = 'select satuan,namabarang from ' . $dbname . '.`log_5masterbarang` where `kodebarang`=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qry = mysql_query($ql)) || true;
			$res = mysql_fetch_assoc($qry);
			$sSat = 'select satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qSat = mysql_query($sSat)) || true;
			$rSat = mysql_fetch_assoc($qSat);
			$optSatuan = '<option value=' . $rSat['satuan'] . '>' . $rSat['satuan'] . '</option>';
			$where = ' kodebarang=\'' . $row['kodebarang'] . '\' and darisatuan=\'' . $res['satuan'] . '\'';
			$sSknv = 'select satuankonversi from ' . $dbname . '.log_5stkonversi where ' . $where . '';

			#exit(mysql_error());
			($qSknv = mysql_query($sSknv)) || true;

			while ($rSknv = mysql_fetch_assoc($qSknv)) {
				$optSatuan .= '<option value=' . $rSknv['satuankonversi'] . '>' . $rSknv['satuankonversi'] . '</option>';
			}

			$optTest = makeOption($dbname, 'setup_matauang', 'kode,kodeiso');
			$sqjmlh = 'select selisih,jlpesan,realisasi from ' . $dbname . '.log_sudahpo_vsrealisasi_vw where nopp=\'' . $row['nopp'] . '\' and kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qujmlh = mysql_query($sqjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qujmlh);

			if ($resjmlh['selisih'] == '') {
				$row['realisasi'] = $row['realisasi'];
			}
			else if ($resjmlh['selisih'] != $resjmlh['realisasi']) {
				$row['realisasi'] = $resjmlh['selisih'];
			}

			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td id=\'dtNopp_' . $key . '\'>' . makeElement('rnopp_' . $key . '', 'txt', $row['nopp'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td id=\'dtKdbrg_' . $key . '\'>' . makeElement('rkdbrg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td><textarea id="spek_brg_' . $key . '" cols="25" style="height:13px;"></textarea></td>';
			$table .= '<td>' . makeElement('realisasi_' . $key . '', 'txt', $row['realisasi'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('jmlhDiminta_' . $key . '', 'textnum', $row['realisasi'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'display_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td><select id=sat_' . $key . ' style=\'width:70px\'>' . $optSatuan . '</option></td>';
			$table .= '<td>' . makeElement('harga_satuan_' . $key . '', 'textnum', '', array('style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'calculate(\'' . $key . '\')', 'onblur' => 'periksa_isi(this)', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('total_' . $key . '', 'textnum', '', array('style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled')) . '<input type=hidden id=subTotal_' . $key . ' /></td>';
			$table .= '<td align_center><img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$table .= '<tr><td>&nbsp;</td>' . "\n" .
	'<td colspan=7 align=right>' . $_SESSION['lang']['subtotal'] . '</td>' . "\n" . '<td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px /></td>' . "\n" . '        </tr>' . "\n" . '         <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" .
	'<td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . '</td>' . "\n" . '<td><input type=text  id=\'angDiskon\' name=\'angDiskon\' class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculate_angDiskon()\' onkeypress=\'return angka_doang(event)\' onblur="getZero()" /></td>' . "\n" . '        </tr>' . "\n\t\t" . '    <tr>' . "\n" . '            <td >&nbsp;</td>' . "\n" .
	'<td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . ' (%)</td>' . "\n" . '            <td><input type=text  id=\'diskon\' name=\'diskon\' class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculate_diskon()\' maxlength=\'3\' onkeypress=\'return angka_doang(event)\' onblur="getZero()" /> </td>' . "\n" . '        </tr>' . "\n" . '        <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" .
	'<td colspan=7 align=right>PPn (%)</td>
		<td>
			<input type=text id=\'ppN\' name=\'ppN\'  class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculatePpn()\'  maxlength=\'2\'  onkeypress=\'return angka_doang(event)\' onblur="getZero()" />  
			<input type=\'hidden\' id=\'ppn\' name=\'ppn\' class=\'myinputtext\' onkeypress=\'return angka_doang(event)\' style=\'width:100px\' onblur="getZero()" />
			<span id=\'hslPPn\'> </span> 
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=7 align=right>PPh (%)</td>
		<td><input type=text id=\'ppH\' name=\'ppH\'  class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculatePph()\'  maxlength=\'2\'  onkeypress=\'return angka_doang(event)\' onblur="getZero()" />  <input type=\'hidden\' id=\'pph\' name=\'pph\' class=\'myinputtext\' onkeypress=\'return angka_doang(event)\' style=\'width:100px\' onblur="getZero()" /><span id=\'hslPPh\'> </span> </td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=7 align=right>' . $_SESSION['lang']['ongkoskirim'] . '</td>
		<td><input type="text" id="ongKirim" class=myinputtextnumber style="width:100px" onkeypress="return angka_doang(event)"  onblur=\'calculateMiscPpn(2)\'  onfocus=\'normalmiscppn(2)\'  /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=7 align=right>Ppn ' . $_SESSION['lang']['ongkoskirim'] . '  (%)</td>
		<td><input type="text" id="ongKirimPPn" class=myinputtextnumber style="width:100px" onkeypress="return angka_doang(event)"   onblur=\'calculateOngkirPPn()\' maxlength=\'2\'    /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=7 align=right>' . $_SESSION['lang']['misc'] . '</td>
		<td><input type=number id=\'misc\' name=\'misc\'  class=\'myinputtextnumber\' style=\'width:100px\'  onblur=\'calculateMiscPpn(2)\'  onfocus=\'normalmiscppn(2)\'/></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td colspan=7 align=right>' . $_SESSION['lang']['grnd_total'] . '</td>
		<td><input type=text id=grand_total name=grand_total disabled  class=myinputtextnumber style=width:100px  /></td>
	</tr>
	<input type=hidden id=sub_total name=sub_total ><input type=hidden id=nilai_diskon name=nilai_diskon  />'. "\n" .
		'<td><input type=hidden id=\'miscNppn\' name=\'miscNppn\'  class=\'myinputtextnumber\' style=\'width:100px\' onblur=\'calculateMiscPpn(0)\'  onfocus=\'normalmiscppn(0)\'   onkeypress=\'return angka_doang(event)\'/></td>';

	$table .= '</tbody>';
	$table .= '</table> <br />';
	echo $table;
}

function createTabEditDetail($id, $data)
{
	global $conn;
	global $dbname;
	$table .= '<thead>';
	$table .= '<tr class=rowheader>';
	$table .= '<td>' . $_SESSION['lang']['nopp'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['kodebarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['namabarang'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['spesifikasi'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlh_brg_blm_po'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['jmlhPesan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['satuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargasatuan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['subtotal'] . '</td>';
	$table .= '<td>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$table .= '<tbody id=\'detailBody\'>';
	$i = 0;
	$jmlhPesan = 0;

	if ($data != array()) {
		foreach ($data as $key => $row) {
			$ql = 'select satuan,namabarang from ' . $dbname . '.`log_5masterbarang` where `kodebarang`=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qry = mysql_query($ql)) || true;
			$res = mysql_fetch_assoc($qry);
			$sSat = 'select satuan from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qSat = mysql_query($sSat)) || true;
			$rSat = mysql_fetch_assoc($qSat);
			$optSatuan = '<option value=' . $rSat['satuan'] . '>' . $rSat['satuan'] . '</option>';
			$where = ' kodebarang=\'' . $row['kodebarang'] . '\' and darisatuan=\'' . $res['satuan'] . '\'';
			$sSknv = 'select satuankonversi from ' . $dbname . '.log_5stkonversi where ' . $where . '';

			#exit(mysql_error());
			($qSknv = mysql_query($sSknv)) || true;

			while ($rSknv = mysql_fetch_assoc($qSknv)) {
				$optSatuan .= '<option value=' . $rSknv['satuankonversi'] . '>' . $rSknv['satuankonversi'] . '</option>';
			}

			$optTest = makeOption($dbname, 'setup_matauang', 'kode,kodeiso');
			$sqpp = 'select * from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw where nopp=\'' . $row['nopp'] . '\' and kodebarang=\'' . $row['kodebarang'] . '\'';

			#exit(mysql_error());
			($qpp = mysql_query($sqpp)) || true;
			$rpp = mysql_fetch_assoc($qpp);
			$sub_tot = ($row['jumlahpesan'] * $row['hargasbldiskon']) + $row['ongkangkut'];
			$sub_tot_nor = $row['jumlahpesan'] * $row['hargasbldiskon'];
			$sjmlh = 'select sum(jumlahpesan) as jumlahPesan from ' . $dbname . '.log_podt where kodebarang=\'' . $row['kodebarang'] . '\' and nopp=\'' . $row['nopp'] . '\'';

			#exit(mysql_error());
			($qjmlh = mysql_query($sjmlh)) || true;
			$resjmlh = mysql_fetch_assoc($qjmlh);
			$sEdit = 'select jumlahpesan from ' . $dbname . '.log_podt where nopo=\'' . $id . '\' and kodebarang=\'' . $row['kodebarang'] . '\' and nopp=\'' . $row['nopp'] . '\'';

			#exit(mysql_error());
			($qEdit = mysql_query($sEdit)) || true;
			$rEdit = mysql_fetch_assoc($qEdit);
			$tmpil = ($rpp['realisasi'] - $resjmlh['jumlahPesan']) + $rEdit['jumlahpesan'];

			if ($row['harganormal'] == 0) {
				$row['harganormal'] = $row['hargasatuan'];
			}

			$table .= '<tr id=\'detail_tr_' . $key . '\' class=\'rowcontent\'>';
			$table .= '<td id=\'dtNopp_' . $key . '\'>' . makeElement('rnopp_' . $key . '', 'txt', $row['nopp'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td id=\'dtKdbrg_' . $key . '\'>' . makeElement('rkdbrg_' . $key . '', 'txt', $row['kodebarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td>' . makeElement('nm_brg_' . $key . '', 'txt', $res['namabarang'], array('style' => 'width:120px', 'disabled' => 'disabled')) . '</td>';
			$table .= '<td><textarea id="spek_brg_' . $key . '" cols="25" style="height:13px;">' . $row['catatan'] . '</textarea></td>';
			$table .= '<td>' . makeElement('realisasi_' . $key . '', 'txt', $tmpil, array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled', 0 => 'class=myinputtext')) . '</td>';
			$table .= '<td>' . makeElement('jmlhDiminta_' . $key . '', 'textnum', $row['jumlahpesan'], array('style' => 'width:70px', 'onkeypress' => 'return angka_doang(event)', 'onblur' => 'display_number(\'' . $key . '\')', 'onkeyup' => 'calculate(\'' . $key . '\')')) . '</td>';
			$table .= '<td><select id=sat_' . $key . ' style=\'width:70px\'>' . $optSatuan . '</option></td>';
			$table .= '<td>' . makeElement('harga_satuan_' . $key . '', 'textnum', number_format($row['hargasbldiskon'], 2, '.', ','), array('style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'onkeyup' => 'calculate(\'' . $key . '\')', 'onblur' => 'periksa_isi(this)', 'onblur' => 'display_number(\'' . $key . '\')', 'onfocus' => 'normal_number(\'' . $key . '\')')) . '</td>';
			$table .= '<td>' . makeElement('total_' . $key . '', 'textnum', number_format($sub_tot, 2, '.', ','), array('style' => 'width:100px', 'onkeypress' => 'return angka_doang(event)', 'disabled' => 'disabled')) . '<input type=hidden id=subTotal_' . $key . ' value=' . $sub_tot_nor . ' /></td>';
			$table .= '<td align=center><img id=\'detail_delete_' . $key . '\' title=\'Hapus\' class=zImgBtn onclick="deleteDetail(\'' . $key . '\')" src=\'images/delete_32.png\'/></td>';
			$table .= '</tr>';
			$i = $key;
		}

		++$i;
	}

	$table .= '<tr><td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['subtotal'] . '</td>' . "\n" . '            <td><input type=text id=total_harga_po name=total_harga_po disabled  class=myinputtextnumber  style=width:100px /></td>' . "\n" . '        </tr>' . "\n" . '        <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" .
	'<td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . '</td>' . "\n" . '            <td><input type=\'text\'  id=\'angDiskon\' name=\'angDiskon\' class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculate_angDiskon()\' onkeypress=\'return angka_doang(event)\' onblur="getZero()" /></td>' . "\n" . '        </tr>' . "\n\t\t" . '    <tr>' . "\n" . '            <td >&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['diskon'] . ' (%)</td>' . "\n" . '            <td><input type=\'text\'  id=\'diskon\' name=\'diskon\' class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculate_diskon()\' maxlength=\'3\' onkeypress=\'return angka_doang(event)\' onblur="getZero()" /> </td>' . "\n" . '        </tr>' . "\n" . '        <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>PPn (%)</td>' . "\n" . '            <td><input type=text id=\'ppN\' name=\'ppN\'  class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculatePpn()\'  maxlength=\'2\'  onkeypress=\'return angka_doang(event)\' onblur="getZero()" />  <input type=\'hidden\' id=\'ppn\' name=\'ppn\' class=\'myinputtext\' onkeypress=\'return angka_doang(event)\' style=\'width:100px\' onblur="getZero()" /><span id=\'hslPPn\'> </span> </td>' . "\n" . '        </tr><tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>PPh (%)</td>' . "\n" . '            <td><input type=text id=\'ppH\' name=\'ppH\'  class=\'myinputtextnumber\' style=\'width:100px\' onkeyup=\'calculatePph()\'  maxlength=\'2\'  onkeypress=\'return angka_doang(event)\' onblur="getZero()" />  <input type=\'hidden\' id=\'pph\' name=\'pph\' class=\'myinputtext\' onkeypress=\'return angka_doang(event)\' style=\'width:100px\' onblur="getZero()" /><span id=\'hslPPh\'> </span> </td>' . "\n" . '        </tr>' . "\n" . '        <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['ongkoskirim'] . '</td>' . "\n" . '            <td><input type="text" id="ongKirim" class=myinputtextnumber style="width:100px" onkeypress="return angka_doang(event)"  onblur=\'calculateMiscPpn(2)\'  onfocus=\'normalmiscppn(1)\'  /></td>' . "\n" . '        </tr>' . "\n" . '         <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>Ppn ' . $_SESSION['lang']['ongkoskirim'] . '  (%)</td>' . "\n" . '            <td><input type="text" id="ongKirimPPn" class=myinputtextnumber style="width:100px" onkeypress="return angka_doang(event)"    onblur=\'calculateOngkirPPn()\' maxlength=\'2\'   /></td>' . "\n" . '        </tr>' . "\n" . '        <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['misc'] . '</td>' . "\n" . '            
	<td><input type=number id=\'misc\' name=\'misc\'  class=\'myinputtextnumber\' style=\'width:100px\'  onblur=\'calculateMiscPpn(2)\'  onfocus=\'normalmiscppn(2)\'/></td>' . "\n" . '        </tr>' . "\n" . '         <tr>' . "\n" . '            <td>&nbsp;</td>' . "\n" . '            <td colspan=7 align=right>' . $_SESSION['lang']['grnd_total'] . '</td>' . "\n" . '            <td><input type=text id=\'grand_total\' name=\'grand_total\' disabled  class=\'myinputtextnumber\' style=width:100px /></td>' . "\n" . 

	'</tr><input type=hidden id=\'sub_total\' name=\'sub_total\' ><input type=hidden id=\'nilai_diskon\' name=\'nilai_diskon\'  />'."\n".
	'<td><input type=hidden id=\'miscNppn\' name=\'miscNppn\'  class=\'myinputtextnumber\' style=\'width:100px\' onblur=\'calculateMiscPpn(0)\'  onfocus=\'normalmiscppn(0)\'   onkeypress=\'return angka_doang(event)\'/></td>';
	
	$table .= '</tbody>';
	$sPoht = 'select tanggalkirim,lokasipengiriman,syaratbayar,uraian,purchaser from ' . $dbname . '.log_poht where nopo=\'' . $id . '\' ';

	#exit(mysql_error());
	($qPoht = mysql_query($sPoht)) || true;
	$rPoht = mysql_fetch_assoc($qPoht);
	$snmkary = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $rPoht['purchaser'] . '\'';

	#exit(mysql_error());
	($qnmkary = mysql_query($snmkary)) || true;
	$rnmkary = mysql_fetch_assoc($qnmkary);
	echo $table . '###' . $rPoht['syaratbayar'] . '###' . $rPoht['uraian'] . '###' . $rPoht['uraian'];
}

session_start();
require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';

if ($_POST['proses'] == 'createTable') {

	$rnopp = $_POST['nopp'];
	$baris = $_POST['baris'];
	$kdbrg = $_POST['kdbrg'];
	$bara = 0;

	foreach ($rnopp as $row => $Rslt) {
		$a = 0;

		while ($a < $row) {
			$b = 0;

			while ($b < $baris) {
				if ($a != $b) {
					if ($kdbrg[$a] == $kdbrg[$b]) {
						$cek += 1;
						$cekBrg2 = $kdbrg[$a];
					}
				}

				++$b;
			}

			++$a;
		}

		if ($cek != 0) {
			echo 'warning:Kodebarang : ' . $cekBrg2 . ' Lebih Dari Satu';
			exit();
		}
		else if ($row == 0) {
			$where .= ' (nopp=\'' . $Rslt . '\''.' AND kodebarang=\'' . $kdbrg[$row] . '\')';
		}
		else {
			$where .= ' OR (nopp=\'' . $Rslt . '\''.' AND kodebarang=\'' . $kdbrg[$row] . '\') ';
		}
	}

	$query = 'select * from ' . $dbname . '.log_prapodt where ' . $where ;

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
			$where2 .= ' kodebarang=\'' . $kdbrg[$row]. '\'';
		}
		else {
			$where .= ' or nopp=\'' . $Rslt . '\'';
			$where2 .= ' or kodebarang=\'' . $kdbrg[$row]. '\'';
		}
	}

	$sql = 'select nopp from ' . $dbname . '.log_prapodt where (' . $where . ') and (' . $where2 . ')';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;
	$cond = '';
	$i = 0;

	while ($res = mysql_fetch_assoc($query)) {
		$nopp = substr($res['nopp'], 15, 4);

		if ($i == 0) {
			$cond .= ' nopp=\'' . $res['nopp'] . '\'';
		}
		else {
			$cond .= ' or nopp=\'' . $res['nopp'] . '\'';
		}

		++$i;
	}

	$sql2 = 'select distinct kodeorg from ' . $dbname . '.log_prapoht where (' . $cond . ')';

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;
	$res2 = mysql_fetch_assoc($query2);

	if ($res2['kodeorg'] == 'KUD') {
		$res2['kodeorg'] = 'PMO';
	}
	
	$type_koding_po = "M"; // [M = tiap bulan di reset jadi '1' | Y= tiap tahun di reset jadi '1']
	
	
	if( $type_koding_po == "M"){
		//$nopo = '/'.date('m').'/' . date('Y') . '/PO/HO/' . $res2['kodeorg'];
		$nopo = '/'.$bln.'/' . $thn . '/PO/HO/' . $res2['kodeorg'];
	}elseif($type_koding_po == "Y" ){
		//$nopo = '/' . date('Y') . '/PO/HO/' . $res2['kodeorg'];
		$nopo = '/' . $thn . '/PO/HO/' . $res2['kodeorg'];
	}
	
	$ql = 'select `nopo` from ' . $dbname . '.`log_poht` where nopo like \'%' . $nopo . '%\' order by length(`nopo`) desc, `nopo` desc limit 0,1';

	#exit(mysql_error());
	($qr = mysql_query($ql)) || true;
	$rp = mysql_fetch_object($qr);
	$eksplot = explode('/', $rp->nopo);
	$awal = $eksplot[0];
	$awal = intval($awal);
	$cekbln = $eksplot[1];
	$cekthn = $eksplot[2];
	
	if( $type_koding_po == "M"){		
		if ($bln != $cekbln) {
			$awal = 1;
		}
		else {
			++$awal;
		}
	}elseif($type_koding_po == "Y" ){
		if ($thn != $cekthn) {		
			$awal = 1;
		}
		else {
			++$awal;
		}
	}
	

	$counter = $awal;

	if ($awal < 1000) {
		$counter = addZero($awal, 3);
	}

	$nopo = $counter . '/' . $bln . '/' . $thn . '/PO/HO/' . $res2['kodeorg'];
	$tglSkrng = date('Y-m-d');
	$wrt = 'purchaser!=\'\'';
	$mkpurchsr = makeOption($dbname, 'log_prapodt', 'nopp,purchaser', $wrt);
	$barisnopp = 1;
	$sIns = 'insert into ' . $dbname . '.log_poht (nopo,tanggal,kodeorg,lokalpusat,statuspo,tgledit,kurs,matauang,updateby) values(\'' . $nopo . '\',\'' . $tglSkrng . '\',\'' . $res2['kodeorg'] . '\',\'0\',\'2\',\'' . $tglSkrng . '\',\'1\',\'IDR\',\'' . $_SESSION['standard']['userid'] . '\')';

	if (mysql_query($sIns)) {
		foreach ($_POST['kdbrg'] as $row => $isi) {
			$sql = 'insert into ' . $dbname . '.log_podt (`nopo`,`kodebarang`,`nopp`) values (\'' . $nopo . '\',\'' . $isi . '\',\'' . $_POST['nopp'][$row] . '\')';

			if (!mysql_query($sql)) {
				echo $sql . '-----';
				echo 'Gagal,' . mysql_error($conn);
				exit();
			}
			else {
				$sUpdate = 'update ' . $dbname . '.log_prapodt set create_po=1 where nopp=\'' . $_POST['nopp'][$row] . '\' and kodebarang=\'' . $isi . '\'';

				if (!mysql_query($sUpdate)) {
					echo 'Gagal,' . mysql_error($conn);
					exit();
				}
			}
		}

		echo $nopo . '###';
		createTabDetail($Rslt, $data);
	}
	else {
		echo 'DB Error : ' . mysql_error($conn);
	}
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

	if ($_SESSION['empl']['kodejabatan'] == '138') {
		$sql2 = 'select * from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw  where (kodept=\'' . $_POST['kodept'] . '\' and purchaser=\'' . $user_id . '\' and lokalpusat=\'0\' and status!=\'3\') and (selisih>0 or selisih is null) order by tgl_sdt desc ';
	}
	else {
		$sql2 = 'select * from  ' . $dbname . '.log_sudahpo_vsrealisasi_vw  where (kodept=\'' . $_POST['kodept'] . '\' and purchaser=\'' . $user_id . '\' and lokalpusat=\'0\' and status!=\'3\') and (selisih>0 or selisih is null) order by tgl_sdt desc';
	}

	#exit(mysql_error());
	($query2 = mysql_query($sql2)) || true;

	while ($res2 = mysql_fetch_object($query2)) {
		$no += 1;
		$sbrg = 'select * from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $res2->kodebarang . '\'';

		#exit(mysql_error());
		($qbrg = mysql_query($sbrg)) || true;
		$rbrg = mysql_fetch_object($qbrg);
		$sJmlhPsn = 'select sum(jumlahpesan) as jmlhPesan from ' . $dbname . '.log_podt where nopp=\'' . $res2->nopp . '\' and kodebarang=\'' . $res2->kodebarang . '\'';

		#exit(mysql_error());
		($qJmlhPsn = mysql_query($sJmlhPsn)) || true;
		$rJmlhPsn = mysql_fetch_assoc($qJmlhPsn);
		echo "\n" . '                     <tr class=rowcontent ' . $show . ' id=tr_' . $no . '>' . "\n" . '                            <td onclick="checkIt(' . $no . ')" >' . $no . '</td>' . "\n" . '                            <td id="nopp_x' . $no . '" onclick="checkIt(' . $no . ')" >' . $res2->nopp . '</td>' . "\n" . '                            <td id=kdbrg_' . $no . '>' . $rbrg->kodebarang . '</td>' . "\n" . '                            <td>' . $rbrg->namabarang . '</td>' . "\n" . '                            <td>' . $rbrg->satuan . '</td>' . "\n" . '                            <td align=center>' . $res2->realisasi . '</td>' . "\n" . '                             <td align=center>' . tanggalnormal($res2->tgl_sdt) . '</td>';
		if (($res2->selisih == '') || is_null($res2->selisih) || ($res2->selisih == 0)) {
			echo '<td align=center>' . $res2->realisasi . '</td>';
		}
		else if ($res2->selisih != $res2->jmlhPesan) {
			$blm_pesan = $res2->selisih;
			echo '<td align=center>' . $blm_pesan . '</td>';
		}

		if (($res2->jlpesan == '') || is_null($res2->jlpesan)) {
			$jlpesan = 0;
		}
		else {
			$jlpesan = $rJmlhPsn['jmlhPesan'];
		}

		echo '<td  align=center>' . $jlpesan . '</td>';
		echo '<td align=center><input type=checkbox id=plh_pp_' . $no . ' name=plh_pp_' . $no . '/></td>' . "\n" . '                     </tr>';
	}

	echo '<tr><td colspan=9 align=center>' . "\n" . '            <button name=process id=process onclick=process()>' . $_SESSION['lang']['proses'] . '</button>' . "\n" . '            <button name=cancel id=cancel onclick=cancel_headher()>' . $_SESSION['lang']['cancel'] . '</button>' . "\n" . '            </td></tr>';
}

if ($_POST['proses'] == 'detail_delete') {
	$data = $_POST;
	$where = '`nopo`=\'' . $data['nopo'] . '\'';
	$where .= ' and `kodebarang`=\'' . $data['kd_brg'] . '\'';
	$where .= ' and `nopp`=\'' . $data['nopp'] . '\'';
	$sCekGdng = 'select distinct nopo from ' . $dbname . '.log_transaksi_vw where nopo=\'' . $data['nopo'] . '\' and kodebarang=\'' . $data['kd_brg'] . '\'';

	#exit(mysql_error($conn));
	($qCekGdng = mysql_query($sCekGdng)) || true;
	$rCekGdng = mysql_num_rows($qCekGdng);

	if (0 < $rCekGdng) {
		exit('Error: Nopo : ' . $data['nopo'] . ' Sudah diterima di gudang tidak dapat di hapus');
	}

	$query = 'delete from `' . $dbname . '`.`log_podt` where ' . $where;
	saveLog($query);
	if (!mysql_query($query)) {
		echo 'DB Error : ' . mysql_error($conn);
	}
}

?>
