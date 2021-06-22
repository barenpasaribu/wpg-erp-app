<?php


session_start();
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'config/connection.php';
$proses = $_POST['proses'];
$periode = $_POST['periode'];
$kdBrg = $_POST['kdBrg'];
$smbrHargaEx = $_POST['smbrHargaEx'];
$tnggl = tanggalsystem($_POST['tnggl']);

switch ($proses) {
case 'createTable':
	$table .= '<table id=\'ppDetailTable\' class=sortable>';
	$table .= '<thead>';
	$table .= '<tr class=rowheader>';
	$table .= '<td>' . $_SESSION['lang']['bulan'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['sumberHarga'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargaOpen'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargaHigh'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargaLow'] . '</td>';
	$table .= '<td>' . $_SESSION['lang']['hargaClose'] . '</td>';
	$table .= '<td>Action</td>';
	$table .= '</tr>';
	$table .= '</thead>';
	$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');

	foreach ($arrBln as $is => $dt) {
		$optBln .= '<option value=' . $dt . '>' . $dt . '</option>';
	}

	$optsmbrhargaEx = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$arrsstk = getEnum($dbname, 'pmn_hargapasardt', 'smbrHarga');

	foreach ($arrsstk as $kei => $fal) {
		$optsmbrhargaEx .= '<option value=\'' . $kei . '\'>' . $fal . '</option>';
	}

	$table .= '<tbody id=\'detailBody\'>';
	$table .= '<tr id=\'detail_tr\' class=\'rowcontent\'>';
	$table .= '<td><select id=bln name=bln style=\'width:150px\'>' . $optBln . '</select></td>';
	$table .= '<td><select id=smbrHarga name=smbrHarga style=\'width:150px\'>' . $optsmbrhargaEx . '</select></td>';
	$table .= '<td><input type="text" class="myinputtextnumber" id="hargaOp"  name="hargaOp" onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /></td>';
	$table .= '<td><input type="text" class="myinputtextnumber" id="hargaHig"  name="hargaHig" onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /></td>';
	$table .= '<td><input type="text" class="myinputtextnumber" id="hargaLo"  name="hargaLo" onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /></td>';
	$table .= '<td><input type="text" class="myinputtextnumber" id="harga"  name="harga" onkeypress="return angka_doang(event)" size="10" maxlength="10" value="0" style="width:150px;" /></td>';
	$table .= '<td><img id=\'detail_add\' title=\'Simpan\' class=zImgBtn onclick="addDetail()" src=\'images/save.png\'/>';
	$table .= '&nbsp;<img id=\'detail_delete\' /></td>';
	$table .= '</tr>';
	$table .= '</tbody>';
	$table .= '</table><input type=hidden id=blnOld name=blnOld value=\'\' /><input type=hidden id=smbrHargaOld name=smbrHargaOld value=\'\' />';
	echo $table;
	break;

case 'loadDetail':
	$sDt = 'select * from ' . $dbname . '.pmn_hargapasardt where periode=\'' . $periode . '\' and tanggal=\'' . $tnggl . '\' and smbrHrgExternal=\'' . $smbrHargaEx . '\'' . "\r\n" . '                    and kodeproduk=\'' . $kdBrg . '\'';

	#exit(mysql_error());
	($qDt = mysql_query($sDt)) || true;

	while ($rDet = mysql_fetch_assoc($qDt)) {
		$no += 1;
		echo "\r\n" . '                        <tr class=rowcontent>' . "\r\n" . '                        <td>' . $no . '</td>' . "\r\n" . '                        <td>' . $rDet['bulan'] . '</td>' . "\r\n" . '                        <td>' . $rDet['smbrHarga'] . '</td>' . "\r\n" . '                        <td>' . number_format($rDet['hargaOpen'], 2) . '</td>' . "\r\n" . '                        <td>' . number_format($rDet['hargaHigh'], 2) . '</td>' . "\r\n" . '                        <td>' . number_format($rDet['hargaLow'], 2) . '</td>' . "\r\n" . '                        <td>' . number_format($rDet['hargaClose'], 2) . '</td>' . "\r\n" . '                        <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editDetail(\'' . $rDet['bulan'] . '\',\'' . $rDet['smbrHarga'] . '\',\'' . $rDet['hargaOpen'] . '\',\'' . $rDet['hargaHigh'] . '\',\'' . $rDet['hargaLow'] . '\',\'' . $rDet['hargaClose'] . '\');">' . "\r\n" . '                        <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delDetail(\'' . $rDet['bulan'] . '\',\'' . tanggalnormal($rDet['tanggal']) . '\',\'' . $rDet['periode'] . '\',\'' . $rDet['smbrHarga'] . '\',\'' . $rDet['kodeproduk'] . '\',\'' . $rDet['smbrHrgExternal'] . '\');" ></td>' . "\r\n" . '                        </tr>' . "\r\n" . '                        ';
	}

	break;
}

?>
