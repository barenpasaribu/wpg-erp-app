<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
require_once 'lib/terbilang.php';

if (isset($_POST['proses'])) {
	$proses = $_POST['proses'];
}
else {
	$proses = $_GET['proses'];
}

$_POST['formPil'] == '' ? $formPil = $_GET['formPil'] : $formPil = $_POST['formPil'];
$_POST['formPil2'] == '' ? $formPil = $_GET['formPil2'] : $formPil = $_POST['formPil2'];
$_POST['nopp'] == '' ? $nopp = $_GET['nopp'] : $nopp = $_POST['nopp'];
$_POST['ckno_permintaan'] != '' ? $no_prmntan = $_POST['ckno_permintaan'] : $no_prmntan = $_GET['ckno_permintaan'];
$arrNmSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$arrNmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$arrSatuan = makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$arrFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');
$arrOptTerm = array(1 => 'Tunai', 2 => 'Kerdit 2 Minggu', 3 => 'Kredit 1 Bulan', 4 => 'Termin', 5 => 'DP');
$arrStock = array(1 => 'Ready Stock', 2 => 'Not Ready');
$nilDiskon = $_POST['nilDiskon'];
$diskonPersen = $_POST['diskonPersen'];
$nilPPn = $_POST['nilPPn'];
$nilaiPermintaan = $_POST['nilaiPermintaan'];
$subTotal = $_POST['subTotal'];
$termPay = $_POST['termPay'];
$idFranco = $_POST['idFranco'];
$stockId = $_POST['stockId'];
$ketUraian = $_POST['ketUraian'];
$supplierId = $_POST['supplierId'];

if ($_POST['noUrut'] != '') {
	$noUrut = $_POST['noUrut'];
}

if ($_GET['noUrut'] != '') {
	$noUrut = $_GET['noUrut'];
}

if ($_POST['noppr'] != '') {
	$nopp = $_POST['noppr'];
}

if ($_GET['noppr'] != '') {
	$nopp = $_GET['noppr'];
}

if ($_GET['nopp2'] != '') {
	$dr = explode('###', $_GET['nopp2']);
	$nopp = $dr[0];
	$noUrut = $dr[1];
}

if ($_POST['nopp2'] != '') {
	$dr = explode('###', $_POST['nopp2']);
	$nopp = $dr[0];
	$noUrut = $dr[1];
}

if ($kdNopp != '') {
	$nopp = $kdNopp;
}

if ($nopp == '') {
	exit('Error: Document number is obligatory');
}

$kdNopp = $_POST['kdNopp'];
$sSupp = 'select * from ' . $dbname . '.log_perintaanhargaht where nopp=\'' . $nopp . '\' and nourut=\'' . $noUrut . '\'';

#exit(mysql_error($conn));
($qSupp = mysql_query($sSupp)) || true;

while ($rSupp = mysql_fetch_assoc($qSupp)) {
	$a += 1;
	$dtSupp[$a] = $rSupp['supplierid'];
	$dtNomor[$a] = $rSupp['nomor'];
	$rSupp['subtotal'] == '' ? $rSupp['subtotal'] = 0 : $rSupp['subtotal'] = $rSupp['subtotal'];
	$rSupp['nilaidiskon'] == '' ? $rSupp['nilaidiskon'] = 0 : $rSupp['nilaidiskon'] = $rSupp['nilaidiskon'];
	$rSupp['ppn'] == '' ? $rSupp['ppn'] = 0 : $rSupp['ppn'] = $rSupp['ppn'];
	$rSupp['nilaipermintaan'] == '' ? $rSupp['nilaipermintaan'] = 0 : $rSupp['nilaipermintaan'] = $rSupp['nilaipermintaan'];
	$dtSubtotal[$rSupp['nomor']] = $rSupp['subtotal'];
	$dtDiskon[$rSupp['nomor']] = $rSupp['nilaidiskon'];
	$dtDiskonPersen[$rSupp['nomor']] = $rSupp['diskonpersen'];
	$dtPPN[$rSupp['nomor']] = $rSupp['ppn'];
	$dtTotal[$rSupp['nomor']] = $rSupp['nilaipermintaan'];
	$dtTermPay[$rSupp['nomor']] = $rSupp['sisbayar'];
	$dtStock[$rSupp['nomor']] = $rSupp['stock'];
	$dtFranco[$rSupp['nomor']] = $rSupp['id_franco'];
	$dtCttn[$rSupp['nomor']] = $rSupp['catatan'];
	$mtUang[$rSupp['nomor']] = $rSupp['matauang'];
	$kurs[$rSupp['nomor']] = $rSupp['kurs'];
	$tglDr[$rSupp['nomor']] = $rSupp['tgldari'];
	$tglSmp[$rSupp['nomor']] = $rSupp['tglsmp'];
}

$total = count($dtNomor);

if ($total == 0) {
	exit('Error: No date found');
}

switch ($proses) {
case 'preview':
	$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier order by namasupplier asc';

	#exit(mysql_error());
	($query = mysql_query($sql)) || true;

	while ($res = mysql_fetch_assoc($query)) {
		$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
	}

	$optTermPay = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optStock = $optTermPay;
	$optKrm = $optTermPay;
	$arrOptTerm = array(1 => 'Cash', 2 => 'Credit 2 Weeks', 3 => 'Credit 1 month', 4 => 'Terms', 5 => 'DP');
	$arrStock = array(1 => 'Ready Stock', 2 => 'Not Ready');

	foreach ($dtNomor as $brsNomor => $listNomor) {
		$sDetail = 'select distinct kodebarang,jumlah,nomor,harga,merk from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $listNomor . '\' ';

		#exit(mysql_error());
		($qDetail = mysql_query($sDetail)) || true;

		while ($rDetail = mysql_fetch_assoc($qDetail)) {
			if ($rDetail['harga'] == '') {
				$rDetail['harga'] = 0;
			}

			$dtSub[$rDetail['nomor']][$rDetail['kodebarang']] = floatval($rDetail['jumlah']) * floatval($rDetail['harga']);
			$dtHarga[$rDetail['nomor']][$rDetail['kodebarang']] = $rDetail['harga'];
			$dtMerk[$rDetail['nomor']][$rDetail['kodebarang']] = $rDetail['merk'];
		}
	}

	$sDetail = 'select distinct kodebarang,jumlah from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $dtNomor[1] . '\' ';

	#exit(mysql_error());
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$listBarang[$rDetail['kodebarang']] = $rDetail['kodebarang'];
		$arrJmlh[$rDetail['kodebarang']] = $rDetail['jumlah'];
	}

	$tab = '<table cellspacing=1 border=0 class=sortable >' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td rowspan=2 align=center>No.</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                <td rowspan=2 align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>';

	foreach ($dtSupp as $brs => $listData) {
		$tab .= '<td colspan=3 align=center>' . $arrNmSupp[$listData] . '</td>';
	}

	$tab .= "\r\n\t" . '</tr><tr>';
	$b = 1;

	while ($b <= $total) {
		$tab .= '<td  align=center width=85px>' . $_SESSION['lang']['merk'] . '</td><td  align=center width=85px>' . $_SESSION['lang']['harga'] . '</td><td align=center width=85px>' . $_SESSION['lang']['subtotal'] . '</td>';
		++$b;
	}

	$tab .= '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';
	$totRow = count($listBarang);

	if ($totRow != 0) {
		foreach ($listBarang as $brsKdBrg) {
			$no += 1;
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td>' . $no . '</td>';
			$tab .= '<td id=\'kd_brg_' . $no . '\'>' . $brsKdBrg . '</td>';
			$tab .= '<td title=\'' . $arrNmBrg[$brsKdBrg] . '\'>' . $arrNmBrg[$brsKdBrg] . '</td>';
			$tab .= '<td align=right id=\'jumlah_' . $no . '\'>' . $arrJmlh[$brsKdBrg] . '</td>';
			$tab .= '<td align=center>' . $arrSatuan[$brsKdBrg] . '</td>';
			$ard = 0;

			foreach ($dtNomor as $brsNomor => $listNomor) {
				$ard += 1;

				if ($formPil != '1') {
					$tab .= '<td align=left>' . $dtMerk[$listNomor][$brsKdBrg] . '</td>';
					$tab .= '<td align=right>' . number_format($dtHarga[$listNomor][$brsKdBrg], 2) . '</td>';
					$tab .= '<td align=right>' . number_format($dtSub[$listNomor][$brsKdBrg], 2) . '</td>';
				}
				else {
					$tab .= '<td align=right><input type=text id=\'merk_' . $no . '_' . $ard . '\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event)\' maxlength=50 style=\'width:85px\'  value=\'' . $dtMerk[$listNomor][$brsKdBrg] . '\'  /></td>';
					$tab .= '<td align=right><input type=text id=\'price_' . $no . '_' . $ard . '\'  class=\'myinputtextnumber\' onkeypress=\'return angka_doang(event)\' onfocus=\'normal_number(' . $no . ',' . $ard . ',' . $totRow . ')\' onkeyup=\'calculate(' . $no . ',' . $ard . ',' . $totRow . ')\' style=\'width:85px\' value=\'' . $dtHarga[$listNomor][$brsKdBrg] . '\' /></td>';
					$tab .= '<td align=right><input type=text id=\'total_' . $no . '_' . $ard . '\' disabled   class=\'myinputtextnumber\' onkeypress=\'return angka_doang(event)\' style=\'width:85px\' value=\'' . $dtSub[$listNomor][$brsKdBrg] . '\'  /></td>';
				}
			}

			$tab .= '</tr>';
		}

		$tab .= '<tr class=\'rowcontent\'><td rowspan=4 colspan=3 valign=top align=left>&nbsp</td><td colspan=2>' . $_SESSION['lang']['subtotal'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			$noUrut += 1;
			$tab .= '<td align=right colspan=3 id=total_harga_po_' . $noUrut . '>' . number_format($dtSubtotal[$listNomor2], 2) . '</td>';
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['diskon'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			if ($formPil != '1') {
				$tab .= '<td align=right colspan=2>' . number_format($dtDiskonPersen[$listNomor2], 2) . '%</td>';
				$tab .= '<td align=right>' . number_format($dtDiskon[$listNomor2], 2) . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td align=right colspan=2><input type=text  id=diskon_' . $noUrut . ' name=diskon_' . $noUrut . ' class=myinputtextnumber onkeyup=calculate_diskon(' . $noUrut . ') maxlength=3 onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')" value=\'' . $dtDiskonPersen[$listNomor2] . '\' style=\'width:85px\'  /></td>';
				$tab .= '<td align=right><input type=text  id=angDiskon_' . $noUrut . ' name=angDiskon_' . $noUrut . ' class=myinputtextnumber  onkeyup=calculate_angDiskon(' . $noUrut . ') onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')" value=\'' . $dtDiskon[$listNomor2] . '\' style=\'width:85px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['ppn'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			if ($formPil != '1') {
				$tab .= '<td align=right colspan=3>' . number_format($dtPPN[$listNomor2], 2) . '</td>';
			}
			else {
				$noUrut += 1;
				@$persen[$listNomor2] = ($dtDiskon[$listNomor2] / $dtSubtotal[$listNomor2]) * 100;
				$tab .= '<td align=right colspan=2><input type=text  id=ppN_' . $noUrut . ' name=ppN_' . $noUrut . ' class=myinputtextnumber  onkeyup=calculatePpn(' . $noUrut . ')  maxlength=2  onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')"  value=\'' . $persen[$listNomor2] . '\' style=\'width:85px\' /></td>';
				$tab .= '<td align=right><input type=text  id=ppn_' . $noUrut . ' name=ppn_' . $noUrut . ' class=myinputtextnumber  disabled value=\'' . $dtPPN[$listNomor2] . '\' style=\'width:85px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['grnd_total'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor7 => $listNomor7) {
			$noUrut += 1;
			$tab .= '<td align=right colspan=3 id=grand_total_' . $noUrut . '>' . number_format($dtTotal[$listNomor7], 2) . '</td>';
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td rowspan=10 colspan=3 valign=top align=left>' . $_SESSION['lang']['rekomendasi'] . '</td>';
		$tab .= '<td colspan=2>' . $_SESSION['lang']['nopermintaan'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			$noUrut += 1;

			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $listNomor10 . '</td>';
			}
			else {
				$tab .= '<td colspan=3><input type=text disabled id=no_prmntan_' . $noUrut . ' value=\'' . $listNomor10 . '\' class=myinputtext style=\'width:150px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['matauang'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $mtUang[$listNomor10] . '</td>';
			}
			else {
				$optMt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
				$sMt = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

				#exit(mysql_error());
				($qMt = mysql_query($sMt)) || true;

				while ($rMt = mysql_fetch_assoc($qMt)) {
					if ($mtUang[$listNomor10] != '') {
						$optMt .= '<option value=' . $rMt['kode'] . ' ' . ($mtUang[$listNomor10] == $rMt['kode'] ? 'selected' : '') . '>' . $rMt['kodeiso'] . '</option>';
					}
					else {
						$optMt .= '<option value=' . $rMt['kode'] . '>' . $rMt['kodeiso'] . '</option>';
					}
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id="mtUang_' . $noUrut . '" name="mtUang_' . $noUrut . '" style="width:150px;" >' . $optMt . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['kurs'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $kurs[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type="text" class="myinputtextnumber" id="Kurs_' . $noUrut . '" name="Kurs_' . $noUrut . '" style="width:150px;" onkeypress="return angka_doang(event)" value=' . $kurs[$listNomor10] . '  /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['tgldari'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $tglDr[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type=text class=myinputtext style=\'width:150px\' id=tgl_dari_' . $noUrut . ' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value=\'' . tanggalnormal($tglDr[$listNomor10]) . '\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['tglsmp'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $tglSmp[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type=text class=myinputtext style=\'width:150px\' id=tgl_smp_' . $noUrut . ' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value=\'' . tanggalnormal($tglSmp[$listNomor10]) . '\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['syaratPem'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrOptTerm[$dtTermPay[$listNomor10]] . '</td>';
			}
			else {
				foreach ($arrOptTerm as $brsOptTerm => $listTerm) {
					$optTermPay .= '<option value=\'' . $brsOptTerm . '\' ' . ($brsOptTerm == $dtTermPay[$listNomor10] ? 'selected' : '') . '>' . $listTerm . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=\'term_pay_' . $noUrut . '\'  style=\'width:150px\'>' . $optTermPay . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['stock'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor9 => $listNomor9) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrStock[$dtStock[$listNomor9]] . '</td>';
			}
			else {
				foreach ($arrStock as $brsStock => $listStock) {
					$optStock .= '<option value=\'' . $brsStock . '\' ' . ($brsStock == $dtStock[$listNomor9] ? 'selected' : '') . '>' . $listStock . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=stockId_' . $noUrut . ' style=\'width:150px\'>' . $optStock . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['almt_kirim'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor8 => $listNomor8) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrFranco[$dtFranco[$listNomor8]] . '</td>';
			}
			else {
				$sKrm = 'select id_franco,franco_name from ' . $dbname . '.setup_franco where status=0 order by franco_name asc';

				#exit(mysql_error($conn));
				($qKrm = mysql_query($sKrm)) || true;

				while ($rKrm = mysql_fetch_assoc($qKrm)) {
					$optKrm .= '<option value=' . $rKrm['id_franco'] . ' ' . ($rKrm['id_franco'] == $dtFranco[$listNomor8] ? 'selected' : '') . '>' . $rKrm['franco_name'] . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=tmpt_krm_' . $noUrut . ' style=\'width:150px\'>' . $optKrm . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['keterangan'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor5 => $listNomor5) {
			if ($formPil != '1') {
				$tab .= '<td align=justify colspan=3>' . $dtCttn[$listNomor5] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td align=justify colspan=3><textarea id=\'ketUraian_' . $noUrut . '\' name=\'ketUraian_' . $noUrut . '\' onkeypress=\'return tanpa_kutip(event);\' cols=18 rows=3>' . $dtCttn[$listNomor5] . '</textarea></td>';
			}
		}

		$tab .= '</tr>';
		$noUrut = 0;

		if ($formPil != '0') {
			$tab .= '<tr class=rowcontent>';

			foreach ($dtNomor as $brsNomor2 => $listNomor2) {
				$noUrut += 1;
				$tab .= '<td align=center colspan=5><button class=mybutton id=save_' . $noUrut . ' onclick=simpanSemua(' . $noUrut . ',' . $totRow . ')>' . $_SESSION['lang']['save'] . '</button></td>';
			}

			$tab .= '</tr>';
		}
	}

	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'preview2':
	$formPil = 1;
	$optTermPay = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optStock = $optTermPay;
	$optKrm = $optTermPay;
	$arrOptTerm = array(1 => 'Tunai', 2 => 'Kerdit 2 Minggu', 3 => 'Kredit 1 Bulan', 4 => 'Termin', 5 => 'DP');
	$arrStock = array(1 => 'Ready Stock', 2 => 'Not Ready');

	foreach ($dtNomor as $brsNomor => $listNomor) {
		$sDetail = 'select distinct kodebarang,jumlah,nomor,harga,merk from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $listNomor . '\' ';

		#exit(mysql_error());
		($qDetail = mysql_query($sDetail)) || true;

		while ($rDetail = mysql_fetch_assoc($qDetail)) {
			if ($rDetail['harga'] == '') {
				$rDetail['harga'] = 0;
			}

			$dtSub[$rDetail['nomor']][$rDetail['kodebarang']] = floatval($rDetail['jumlah']) * floatval($rDetail['harga']);
			$dtHarga[$rDetail['nomor']][$rDetail['kodebarang']] = $rDetail['harga'];
			$dtMerk[$rDetail['nomor']][$rDetail['kodebarang']] = $rDetail['merk'];
		}
	}

	$sDetail = 'select distinct kodebarang,jumlah from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $dtNomor[1] . '\' ';

	#exit(mysql_error());
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$listBarang[$rDetail['kodebarang']] = $rDetail['kodebarang'];
		$arrJmlh[$rDetail['kodebarang']] = $rDetail['jumlah'];
	}

	$tab = '<table cellspacing=1 border=0 class=sortable >' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td rowspan=2 align=center>No.</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '                <td rowspan=2 align=center>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center>' . $_SESSION['lang']['satuan'] . '</td>';

	foreach ($dtSupp as $brs => $listData) {
		$ard += 1;

		if ($listData != '') {
			$optSupplier = '';
			$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier order by namasupplier asc';

			#exit(mysql_error());
			($query = mysql_query($sql)) || true;

			while ($res = mysql_fetch_assoc($query)) {
				$optSupplier .= '<option value=\'' . $res['supplierid'] . '\' ' . ($res['supplierid'] == $listData ? 'selected' : '') . '>' . $res['namasupplier'] . '</option>';
			}
		}

		$tab .= '<td colspan=3 align=center><select id=supplierId_' . $ard . '>' . $optSupplier . '</select></td>';
	}

	$tab .= "\r\n\t" . '</tr><tr>';
	$b = 1;

	while ($b <= $total) {
		$tab .= '<td  align=center width=85px>' . $_SESSION['lang']['merk'] . '</td><td  align=center width=85px>' . $_SESSION['lang']['harga'] . '</td><td align=center width=85px>' . $_SESSION['lang']['subtotal'] . '</td>';
		++$b;
	}

	$tab .= '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';
	$totRow = count($listBarang);

	if ($totRow != 0) {
		foreach ($listBarang as $brsKdBrg) {
			$no += 1;
			$tab .= '<tr class=\'rowcontent\'>';
			$tab .= '<td>' . $no . '</td>';
			$tab .= '<td id=\'kd_brg_' . $no . '\'>' . $brsKdBrg . '</td>';
			$tab .= '<td title=\'' . $arrNmBrg[$brsKdBrg] . '\'>' . $arrNmBrg[$brsKdBrg] . '</td>';
			$tab .= '<td align=right id=\'jumlah_' . $no . '\'>' . $arrJmlh[$brsKdBrg] . '</td>';
			$tab .= '<td align=center>' . $arrSatuan[$brsKdBrg] . '</td>';
			$ard = 0;

			foreach ($dtNomor as $brsNomor => $listNomor) {
				$ard += 1;

				if ($formPil != '1') {
					$tab .= '<td align=left>' . $dtMerk[$listNomor][$brsKdBrg] . '</td>';
					$tab .= '<td align=right>' . number_format($dtHarga[$listNomor][$brsKdBrg], 2) . '</td>';
					$tab .= '<td align=right>' . number_format($dtSub[$listNomor][$brsKdBrg], 2) . '</td>';
				}
				else {
					$tab .= '<td align=right><input type=text id=merk_' . $no . '_' . $ard . ' value=\'' . $dtMerk[$listNomor][$brsKdBrg] . '\' class=\'myinputtext\' onkeypress=\'return tanpa_kutip(event)\' maxlength=50 style=\'width:85px\' /></td>';
					$tab .= '<td align=right><input type=text id=price_' . $no . '_' . $ard . ' value=\'' . $dtHarga[$listNomor][$brsKdBrg] . '\' class=\'myinputtextnumber\' onkeypress=\'return angka_doang(event)\' onfocus=\'normal_number(' . $no . ',' . $ard . ',' . $totRow . ')\' onkeyup=\'calculate(' . $no . ',' . $ard . ',' . $totRow . ')\' style=\'width:85px\' /></td>';
					$tab .= '<td align=right><input type=text id=total_' . $no . '_' . $ard . ' disabled value=\'' . $dtSub[$listNomor][$brsKdBrg] . '\'  class=\'myinputtextnumber\' onkeypress=\'return angka_doang(event)\' style=\'width:85px\'  /></td>';
				}
			}

			$tab .= '</tr>';
		}

		$tab .= '<tr class=\'rowcontent\'><td rowspan=4 colspan=3 valign=top align=left>&nbsp</td><td colspan=2>' . $_SESSION['lang']['subtotal'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			$noUrut += 1;
			$tab .= '<td align=right colspan=3 id=total_harga_po_' . $noUrut . '>' . number_format($dtSubtotal[$listNomor2], 2) . '</td>';
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['diskon'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			if ($formPil != '1') {
				$tab .= '<td align=right colspan=2>' . number_format($dtDiskonPersen[$listNomor2], 2) . '%</td>';
				$tab .= '<td align=right>' . number_format($dtDiskon[$listNomor2], 2) . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td align=right colspan=2><input type=text  id=diskon_' . $noUrut . ' name=diskon_' . $noUrut . ' class=myinputtextnumber onkeyup=calculate_diskon(' . $noUrut . ') maxlength=3 onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')" value=\'' . $dtDiskonPersen[$listNomor2] . '\' style=\'width:85px\'  /></td>';
				$tab .= '<td align=right><input type=text  id=angDiskon_' . $noUrut . ' name=angDiskon_' . $noUrut . ' class=myinputtextnumber  onkeyup=calculate_angDiskon(' . $noUrut . ') onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')" value=\'' . $dtDiskon[$listNomor2] . '\' style=\'width:85px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['ppn'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor2 => $listNomor2) {
			if ($formPil != '1') {
				$tab .= '<td align=right colspan=3>' . number_format($dtPPN[$listNomor2], 2) . '</td>';
			}
			else {
				$noUrut += 1;
				@$persen[$listNomor2] = ($dtDiskon[$listNomor2] / $dtSubtotal[$listNomor2]) * 100;
				$tab .= '<td align=right colspan=2><input type=text  id=ppN_' . $noUrut . ' name=ppN_' . $noUrut . ' class=myinputtextnumber  onkeyup=calculatePpn(' . $noUrut . ')  maxlength=2  onkeypress=return angka_doang(event) onblur="getZero(' . $noUrut . ')"  value=\'' . $persen[$listNomor2] . '\' style=\'width:85px\' /></td>';
				$tab .= '<td align=right><input type=text  id=ppn_' . $noUrut . ' name=ppn_' . $noUrut . ' class=myinputtextnumber  disabled value=\'' . $dtPPN[$listNomor2] . '\' style=\'width:85px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['grnd_total'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor7 => $listNomor7) {
			$noUrut += 1;
			$tab .= '<td align=right colspan=3 id=grand_total_' . $noUrut . '>' . number_format($dtTotal[$listNomor7], 2) . '</td>';
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td rowspan=10 colspan=3 valign=top align=left>' . $_SESSION['lang']['rekomendasi'] . '</td>';
		$tab .= '<td colspan=2>' . $_SESSION['lang']['nopermintaan'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			$noUrut += 1;

			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $listNomor10 . '</td>';
			}
			else {
				$tab .= '<td colspan=3><input type=text disabled id=no_prmntan_' . $noUrut . ' value=\'' . $listNomor10 . '\' class=myinputtext style=\'width:150px\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['matauang'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $mtUang[$listNomor10] . '</td>';
			}
			else {
				$optMt = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
				$sMt = 'select kode,kodeiso from ' . $dbname . '.setup_matauang order by kode desc';

				#exit(mysql_error());
				($qMt = mysql_query($sMt)) || true;

				while ($rMt = mysql_fetch_assoc($qMt)) {
					if ($mtUang[$listNomor10] != '') {
						$optMt .= '<option value=' . $rMt['kode'] . ' ' . ($mtUang[$listNomor10] == $rMt['kode'] ? 'selected' : '') . '>' . $rMt['kodeiso'] . '</option>';
					}
					else {
						$optMt .= '<option value=' . $rMt['kode'] . '>' . $rMt['kodeiso'] . '</option>';
					}
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id="mtUang_' . $noUrut . '" name="mtUang_' . $noUrut . '" style="width:150px;" >' . $optMt . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['kurs'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $kurs[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type="text" class="myinputtextnumber" id="Kurs_' . $noUrut . '" name="Kurs_' . $noUrut . '" style="width:150px;" onkeypress="return angka_doang(event)" value=' . $kurs[$listNomor10] . '  /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['tgldari'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $tglDr[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type=text class=myinputtext style=\'width:150px\' id=tgl_dari_' . $noUrut . ' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value=\'' . tanggalnormal($tglDr[$listNomor10]) . '\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['tglsmp'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $tglSmp[$listNomor10] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td colspan=3><input type=text class=myinputtext style=\'width:150px\' id=tgl_smp_' . $noUrut . ' onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value=\'' . tanggalnormal($tglSmp[$listNomor10]) . '\' /></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['syaratPem'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor10 => $listNomor10) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrOptTerm[$dtTermPay[$listNomor10]] . '</td>';
			}
			else {
				foreach ($arrOptTerm as $brsOptTerm => $listTerm) {
					$optTermPay .= '<option value=\'' . $brsOptTerm . '\' ' . ($brsOptTerm == $dtTermPay[$listNomor10] ? 'selected' : '') . '>' . $listTerm . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=\'term_pay_' . $noUrut . '\'  style=\'width:150px\'>' . $optTermPay . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['stock'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor9 => $listNomor9) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrStock[$dtStock[$listNomor9]] . '</td>';
			}
			else {
				foreach ($arrStock as $brsStock => $listStock) {
					$optStock .= '<option value=\'' . $brsStock . '\' ' . ($brsStock == $dtStock[$listNomor9] ? 'selected' : '') . '>' . $listStock . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=stockId_' . $noUrut . ' style=\'width:150px\'>' . $optStock . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['almt_kirim'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor8 => $listNomor8) {
			if ($formPil != '1') {
				$tab .= '<td colspan=3>' . $arrFranco[$dtFranco[$listNomor8]] . '</td>';
			}
			else {
				$sKrm = 'select id_franco,franco_name from ' . $dbname . '.setup_franco where status=0 order by franco_name asc';

				#exit(mysql_error($conn));
				($qKrm = mysql_query($sKrm)) || true;

				while ($rKrm = mysql_fetch_assoc($qKrm)) {
					$optKrm .= '<option value=' . $rKrm['id_franco'] . ' ' . ($rKrm['id_franco'] == $dtFranco[$listNomor8] ? 'selected' : '') . '>' . $rKrm['franco_name'] . '</option>';
				}

				$noUrut += 1;
				$tab .= '<td colspan=3><select id=tmpt_krm_' . $noUrut . ' style=\'width:150px\'>' . $optKrm . '</select></td>';
			}
		}

		$tab .= '</tr>';
		$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['keterangan'] . '</td>';
		$noUrut = 0;

		foreach ($dtNomor as $brsNomor5 => $listNomor5) {
			if ($formPil != '1') {
				$tab .= '<td align=justify colspan=3>' . $dtCttn[$listNomor5] . '</td>';
			}
			else {
				$noUrut += 1;
				$tab .= '<td align=justify colspan=3><textarea id=\'ketUraian_' . $noUrut . '\' name=\'ketUraian_' . $noUrut . '\' onkeypress=\'return tanpa_kutip(event);\' cols=18 rows=3>' . $dtCttn[$listNomor5] . '</textarea></td>';
			}
		}

		$tab .= '</tr>';
		$noUrut = 0;

		if ($formPil != '0') {
			$tab .= '<tr class=rowcontent>';

			foreach ($dtNomor as $brsNomor2 => $listNomor2) {
				$noUrut += 1;
				$noUrut == 1 ? $dret = 5 : $dret = 3;
				$tab .= '<td align=center colspan=' . $dret . '><button class=mybutton id=save_' . $noUrut . ' onclick=simpanSemua2(' . $noUrut . ',' . $totRow . ')>' . $_SESSION['lang']['save'] . '</button></td>';
			}

			$tab .= '</tr>';
		}
	}
	else {
		exit('Error: No data found');
	}

	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'update':
	$subTotal = str_replace(',', '', $subTotal);
	$nilaiPermintaan = str_replace(',', '', $nilaiPermintaan);
	$sUpdate = 'update ' . $dbname . '.log_perintaanhargaht set id_franco=\'' . $idFranco . '\', stock=\'' . $stockId . '\\', ' . "\r\n" . '                                              catatan=\'' . $ketUraian . '\',sisbayar=\'' . $termPay . '\', ppn=\'' . $nilPPn . '\', subtotal=\'' . $subTotal . '\\', ' . "\r\n" . '                                              diskonpersen=\'' . $diskonPersen . '\', nilaidiskon=\'' . $nilDiskon . '\', nilaipermintaan=\'' . $nilaiPermintaan . '\\', ' . "\r\n" . '                                              tgldari=\'' . tanggalsystem($_POST['tglDari']) . '\', tglsmp=\'' . tanggalsystem($_POST['tglSmp']) . '\', kurs=\'' . $_POST['kurs'] . '\',' . "\r\n" . '                                              matauang=\'' . $_POST['mtUang'] . '\',supplierid=\'' . $_POST['supplierId'] . '\'' . "\r\n" . '                                              where nomor=\'' . $no_prmntan . '\'';

	if (mysql_query($sUpdate)) {
		$totRow = count($_POST['kdbrg']);

		foreach ($_POST['kdbrg'] as $row => $Act) {
			$kdbrg = $Act;
			$merk = $_POST['merk'][$row];
			$hrg = $_POST['price'][$row];
			$jmlh = $_POST['jmlh'][$row];
			$sUpdate2 = 'update ' . $dbname . '.log_permintaanhargadt set `jumlah`=\'' . $jmlh . '\',`harga`=\'' . $hrg . '\',`merk`=\'' . $merk . '\' ' . "\r\n" . '                                                                    where nomor=\'' . $no_prmntan . '\' and kodebarang=\'' . $kdbrg . '\'';

			if (mysql_query($sUpdate2)) {
				$berhasil += 1;
			}
			else {
				echo ' Gagal,' . $sUpdate2 . "\n" . ' detail' . addslashes(mysql_error($conn));
			}
		}
	}
	else {
		echo $sUpdate . "\n";
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	if ($totRow == $berhasil) {
		exit('Done');
	}

	break;

case 'excel':
	foreach ($dtNomor as $brsNomor => $listNomor) {
		$sDetail = 'select distinct kodebarang,jumlah,nomor,harga from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $listNomor . '\' ';

		#exit(mysql_error());
		($qDetail = mysql_query($sDetail)) || true;

		while ($rDetail = mysql_fetch_assoc($qDetail)) {
			if ($rDetail['harga'] == '') {
				$rDetail['harga'] = 0;
			}

			$dtSub[$rDetail['nomor']][$rDetail['kodebarang']] = floatval($rDetail['jumlah']) * floatval($rDetail['harga']);
			$dtHarga[$rDetail['nomor']][$rDetail['kodebarang']] = $rDetail['harga'];
		}
	}

	$sDetail = 'select distinct kodebarang,jumlah from ' . $dbname . '.log_permintaanhargadt where nomor=\'' . $dtNomor[1] . '\' ';

	#exit(mysql_error());
	($qDetail = mysql_query($sDetail)) || true;

	while ($rDetail = mysql_fetch_assoc($qDetail)) {
		$listBarang[$rDetail['kodebarang']] = $rDetail['kodebarang'];
		$arrJmlh[$rDetail['kodebarang']] = $rDetail['jumlah'];
	}

	$tab = '<table cellspacing=1 border=1 class=sortable>' . "\r\n\t" . '<thead class=rowheader>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td rowspan=2 align=center bgcolor=#DEDEDE>No.</td>' . "\r\n\t\t" . '<td rowspan=2 align=center bgcolor=#DEDEDE>' . $_SESSION['lang']['namabarang'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center bgcolor=#DEDEDE>' . $_SESSION['lang']['jumlah'] . '</td>' . "\r\n\t\t" . '<td rowspan=2 align=center bgcolor=#DEDEDE>' . $_SESSION['lang']['satuan'] . '</td>';

	foreach ($dtSupp as $brs => $listData) {
		$tab .= '<td colspan=2 align=center bgcolor=#DEDEDE >' . $arrNmSupp[$listData] . '</td>';
	}

	$tab .= "\r\n\t" . '</tr><tr>';
	$b = 1;

	while ($b <= $total) {
		$tab .= '<td  align=center bgcolor=#DEDEDE>' . $_SESSION['lang']['harga'] . '</td><td bgcolor=#DEDEDE>' . $_SESSION['lang']['subtotal'] . '</td>';
		++$b;
	}

	$tab .= '</tr>' . "\r\n\t" . '</thead>' . "\r\n\t" . '<tbody>';

	foreach ($listBarang as $brsKdBrg) {
		$no += 1;
		$tab .= '<tr class=\'rowcontent\'>';
		$tab .= '<td>' . $no . '</td>';
		$tab .= '<td>' . $arrNmBrg[$brsKdBrg] . '</td>';
		$tab .= '<td align=right>' . $arrJmlh[$brsKdBrg] . '</td>';
		$tab .= '<td align=center>' . $arrSatuan[$brsKdBrg] . '</td>';

		foreach ($dtNomor as $brsNomor => $listNomor) {
			$tab .= '<td align=right>' . number_format($dtHarga[$listNomor][$brsKdBrg], 2) . '</td>';
			$tab .= '<td align=right>' . number_format($dtSub[$listNomor][$brsKdBrg], 2) . '</td>';
		}

		$tab .= '</tr>';
	}

	$tab .= '<tr class=\'rowcontent\'><td rowspan=4 colspan=2 valign=top align=left>&nbsp</td><td colspan=2>' . $_SESSION['lang']['subtotal'] . '</td>';

	foreach ($dtNomor as $brsNomor2 => $listNomor2) {
		$tab .= '<td align=right colspan=2>' . number_format($dtSubtotal[$listNomor2], 2) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>Diskon</td>';

	foreach ($dtNomor as $brsNomor2 => $listNomor2) {
		$tab .= '<td align=right colspan=2>' . number_format($dtDiskon[$listNomor2], 2) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['ppn'] . '</td>';

	foreach ($dtNomor as $brsNomor2 => $listNomor2) {
		$tab .= '<td align=right colspan=2>' . number_format($dtPPN[$listNomor2], 2) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['grnd_total'] . '</td>';

	foreach ($dtNomor as $brsNomor7 => $listNomor7) {
		$tab .= '<td align=right colspan=2>' . number_format($dtTotal[$listNomor7], 2) . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td rowspan=4 colspan=2 valign=top align=left>' . $_SESSION['lang']['rekomendasi'] . '</td><td colspan=2>' . $_SESSION['lang']['syaratPem'] . '</td>';

	foreach ($dtNomor as $brsNomor10 => $listNomor10) {
		$tab .= '<td colspan=2>' . $arrOptTerm[$dtTermPay[$listNomor10]] . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['stock'] . '</td>';

	foreach ($dtNomor as $brsNomor9 => $listNomor9) {
		$tab .= '<td colspan=2>' . $arrStock[$dtStock[$listNomor9]] . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['almt_kirim'] . '</td>';

	foreach ($dtNomor as $brsNomor8 => $listNomor8) {
		$tab .= '<td colspan=2>' . $arrFranco[$dtFranco[$listNomor8]] . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '<tr class=\'rowcontent\'><td colspan=2>' . $_SESSION['lang']['keterangan'] . '</td>';

	foreach ($dtNomor as $brsNomor5 => $listNomor5) {
		$tab .= '<td width=40px align=justify colspan=2>' . $dtCttn[$listNomor5] . '</td>';
	}

	$tab .= '</tr>';
	$tab .= '</tbody></table>';
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('YmdHms');
	$nop_ = 'bandingHarga' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\r\n" . '                            window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\r\n" . '                            </script>';
	break;

case 'getNopp':
	echo '<fieldset><legend>' . $_SESSION['lang']['result'] . '</legend>' . "\r\n" . '                        <div style="overflow:auto;height:295px;width:455px;">' . "\r\n" . '                        <table cellpading=1 border=0 cellspacing=1 class=sortbale>' . "\r\n" . '                        <thead>' . "\r\n" . '                        <tr class=rowheader>' . "\r\n" . '                        <td>No.</td>' . "\r\n" . '                        <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n" . '                        ' . "\r\n" . '                        </tr><tbody>' . "\r\n" . '                        ';
	$sSupplier = 'select  nopp  from ' . $dbname . '.log_perintaanhargaht where  nopp like \'%' . $kdNopp . '%\' order by nopp asc';

	#exit(mysql_error($conn));
	($qSupplier = mysql_query($sSupplier)) || true;
	$barisCek = mysql_num_rows($qSupplier);

	if (0 < $barisCek) {
		while ($rSupplier = mysql_fetch_assoc($qSupplier)) {
			$no += 1;
			echo '<tr class=rowcontent onclick=setDataNopp(\'' . $rSupplier['nopp'] . '\')>' . "\r\n" . '                             <td>' . $no . '</td>' . "\r\n" . '                             <td>' . $rSupplier['nopp'] . '</td>' . "\r\n\r\n" . '                        </tr>';
		}
	}
	else {
		echo '<tr class=rowcontent>' . "\r\n" . '                             <td>' . $_SESSION['lang']['dataempty'] . '</td>' . "\r\n" . '                        </tr>';
	}

	echo '</tbody></table></div>';
	break;
}

?>
