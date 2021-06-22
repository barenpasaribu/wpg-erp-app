<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$thnbudget = $_POST['thnbudget'];
$method = $_POST['method'];
$kdblok = $_POST['kdblok'];
$jjg = $_POST['jjg'];
$pokprod = $_POST['pokprod'];
$bjr = $_POST['bjr'];
$total = $_POST['total'];
$totbrtthn = $_POST['totbrtthn'];
$totCol = $_POST['totCol'];
$totRow = $_POST['totRow'];
$kgsetahun = $_POST['kgsetahun'];
$thnclose = $_POST['thnclose'];
$lkstgs = $_POST['lkstgs'];
$thnttp = $_POST['thnttp'];
$thnbudgetHeader = $_POST['thnbudgetHeader'];
$kodeblokHeader = $_POST['kodeblokHeader'];
$thnsave = $_POST['thnsave'];
$optNm = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$blokLama = makeOption($dbname, 'setup_blok', 'kodeorg,bloklama');
$arrBln = array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des');
$where = 'tahunbudget=\'' . $thnbudget . '\' and kodeblok=\'' . $kdblok . '\'';

switch ($method) {
case 'pokok':
	$pokok = 'select pokokproduksi,thntnm from ' . $dbname . '.bgt_blok WHERE kodeblok=\'' . $kdblok . '\' and tahunbudget=\'' . $thnbudget . '\'';

	#exit(mysql_error());
	($qOpt = mysql_query($pokok)) || true;
	$rOpt = mysql_fetch_assoc($qOpt);
	$pokok2 = 'select bjr,thntanam from ' . $dbname . '.bgt_bjr WHERE ' . "\r\n" . '                        kodeorg=\'' . substr($kdblok, 0, 4) . '\' and thntanam=\'' . $rOpt['thntnm'] . '\' and tahunbudget=\'' . $thnbudget . '\'';

	#exit(mysql_error());
	($qOpt2 = mysql_query($pokok2)) || true;
	$rOp2t = mysql_fetch_assoc($qOpt2);
	echo $rOpt['pokokproduksi'] . '###' . $rOp2t['bjr'] . '###' . $rOpt['thntnm'];
	break;

case 'getthn':
	$optthnttp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sql = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_produksi_kebun where kodeunit like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and tutup=0 order by tahunbudget desc';

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;

	while ($data = mysql_fetch_assoc($qry)) {
		$optthnttp .= '<option value=' . $data['tahunbudget'] . '>' . $data['tahunbudget'] . '</option>';
	}

	echo $optthnttp;
	break;

case 'saveData':
	if (($bjr == '') || ($bjr == 0)) {
		exit('Error:FFB avg(BJR) required');
	}

	$a = 1;

	while ($a <= $totRow) {
		if ($_POST['arrBrt'][$a] == '') {
			$_POST['arrBrt'][$a] = 0;
		}

		$totalSum += $_POST['arrBrt'][$a];
		++$a;
	}

	if ($total < $totalSum) {
		exit('Error : Monthly total (' . $totalSum . ') greater than total a year (' . $total . ') ');
	}

	$sCek = 'select distinct * from ' . $dbname . '.bgt_produksi_kebun where tahunbudget=\'' . $thnbudget . '\' and kodeblok=\'' . $kdblok . '\'';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sInsert = 'insert into ' . $dbname . '.bgt_produksi_kebun (tahunbudget, kodeunit, kodeblok, jjgperpkk, updateby, jjg01, jjg02, jjg03, jjg04, jjg05, jjg06, jjg07, jjg08, jjg09, jjg10, jjg11, jjg12)';
		$sInsert .= ' values (\'' . $thnbudget . '\',\'' . $_SESSION['empl']['lokasitugas'] . '\',\'' . $kdblok . '\',\'' . $jjg . '\',\'' . $_SESSION['standard']['userid'] . '\'';
		$arb = 1;

		while ($arb <= $totRow) {
			$sInsert .= ',\'' . $_POST['arrBrt'][$arb] . '\'';
			++$arb;
		}

		$sInsert .= ')';

		if (!mysql_query($sInsert)) {
			echo ' Gagal,________' . $sInsert . '__' . mysql_error($conn);
		}
	}
	else {
		exit('Error: Data already exist');
	}

	break;

case 'loadData':
	$tmbh = '';

	if ($thnbudgetHeader != '') {
		$tmbh = ' and tahunbudget=\'' . $thnbudgetHeader . '\' ';
	}

	$tmbh2 = '';

	if ($kodeblokHeader != '') {
		$tmbh2 = ' and kodeblok=\'' . $kodeblokHeader . '\' ';
	}

	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.bgt_produksi_kbn_kg_vw where kodeblok like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' ' . $tmbhsimpan . ' ' . $tmbh . ' order by kodeblok asc ';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$totRowDlm = count($arrBln);
	$tab = '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead><tr class=rowheader><td width=15 align=center>No</td>';
	$tab .= '<td align=center width=90>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$tab .= '<td align=center width=90>' . $_SESSION['lang']['budgetyear'] . '</td>';
	$tab .= '<td align=center width=75>' . $_SESSION['lang']['thntnm'] . '</td>';
	$tab .= '<td align=center width=100>' . $_SESSION['lang']['pkkproduktif'] . '</td>';
	$tab .= '<td align=center width=50>' . $_SESSION['lang']['bjr'] . '</td>';
	$tab .= '<td align=center width=150>' . $_SESSION['lang']['jenjangpokoktahun'] . '</td>';
	$tab .= '<td align=center  width=50>' . $_SESSION['lang']['jjgThn'] . '</td>';

	foreach ($arrBln as $brs7 => $dtBln7) {
		$tab .= '<td  align=center>' . $dtBln7 . '(kg)</td>';
	}

	$tab .= '<td align=center  width=50>' . $_SESSION['lang']['total'] . ' (KG)</td>';
	$tab .= '<td align=center>Aksi</td></tr></thead><tbody>';
	$sList = 'select * from ' . $dbname . '.bgt_produksi_kbn_kg_vw where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $tmbh . ' ' . $tmbh2 . ' order by kodeblok asc limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($qList = mysql_query($sList)) || true;

	while ($rList = mysql_fetch_assoc($qList)) {
		$pokok = 'select jjgperpkk,tutup from ' . $dbname . '.bgt_produksi_kebun WHERE kodeblok=\'' . $rList['kodeblok'] . '\' and tahunbudget=\'' . $rList['tahunbudget'] . '\'';

		#exit(mysql_error());
		($qOpt = mysql_query($pokok)) || true;
		$rOpt = mysql_fetch_assoc($qOpt);
		$a1 = $rOpt['jjgperpkk'];
		$a3 = $rList['pokokproduksi'];
		$totala = $a1 * $a3;

		if ($rOpt['tutup'] == 0) {
			$rtp = 'onclick="fillField(\'' . $rList['tahunbudget'] . '\',\'' . $rList['kodeblok'] . '\',\'' . $rList['pokokproduksi'] . '\',\'' . $rList['bjr'] . '\',\'' . $rOpt['jjgperpkk'] . '\',\'' . $totala . '\',\'' . $rList['thntnm'] . '\');" title="Edit Data ' . $rList['kodeblok'] . '" style=\'cursor:pointer;\'';
		}
		else {
			$rtp = '';
		}

		$no += 1;
		$tab .= '<tr class=rowcontent >';
		$tab .= '<td align=center ' . $rtp . '>' . $no . '</td>';
		$tab .= '<td align=left ' . $rtp . '>' . $rList['kodeblok'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . $rList['tahunbudget'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . $rList['thntnm'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . $rList['pokokproduksi'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . $rList['bjr'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . $rOpt['jjgperpkk'] . '</td>';
		$tab .= '<td align=right ' . $rtp . '>' . number_format($totala, 0) . '</td>';
		$a = 1;

		while ($a <= $totRowDlm) {
			if (strlen($a) == '1') {
				$b = '0' . $a;
			}
			else {
				$b = $a;
			}

			if ($rList['kg' . $b] == '') {
				$rList['kg' . $b] = 0;
			}

			$tab .= '<td align=\'right\' ' . $rtp . '>' . number_format($rList['kg' . $b], 0) . '</td>';
			$rTotal += $rList['kodeblok'];
			++$a;
		}

		$tab .= '<td align=right ' . $rtp . '>' . number_format($rTotal[$rList['kodeblok']], 0) . '</td>';

		if ($rOpt['tutup'] == 0) {
			$tab .= '<td align=\'center\'>' . "\r\n" . '                                                                <!--<img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $rList['tahunbudget'] . '\',\'' . $rList['kodeblok'] . '\',\'' . $rList['pokokproduksi'] . '\',\'' . $rList['bjr'] . '\',\'' . $rOpt['jjgperpkk'] . '\',\'' . $totala . '\',\'' . $rList['thntnm'] . '\');">-->' . "\r\n" . '                                                                <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="Del(\'' . $rList['tahunbudget'] . '\',\'' . $rList['kodeblok'] . '\');">' . "\r\n\r\n" . '                                                   </td>';
		}
		else {
			$tab .= '<td>' . $_SESSION['lang']['tutup'] . '</td>';
		}

		$tab .= '</tr>';
		$a = array(1 => 'kg01', 2 => 'kg02', 3 => 'kg03', 4 => 'kg04', 5 => 'kg05', 6 => 'kg06', 7 => 'kg07', 8 => 'kg08', 9 => 'kg09', 10 => 'kg10', 11 => 'kg11', 12 => 'kg12');
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == '1') {
				$b = '0' . $i;
			}
			else {
				$b = $i;
			}

			$totseb1 = 'select kg' . $b . ' from ' . $dbname . '.bgt_produksi_kbn_kg_vw where kodeblok=\'' . $rList['kodeblok'] . '\' and tahunbudget=\'' . $rList['tahunbudget'] . '\'';

			#exit(mysql_error());
			($totseb2 = mysql_query($totseb1)) || true;

			#exit(mysql_error());
			($totseb3 = mysql_fetch_array($totseb2)) || true;
			$hasil += 'kg' . $b;
			++$i;
		}

		$totSemua += $totala;
		$totbjr += $rList['bjr'];
		$totpkkprod += $rList['pokokproduksi'];
		$totjpt += $rOpt['jjgperpkk'];
	}

	$tab .= '<thead><tr class=rowheader><td align=center colspan=4>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totpkkprod, 0) . '</td>';
	$tab .= '<td align=right>&nbsp</td>';
	$tab .= '<td align=right>' . number_format($totjpt, 0) . '</td>';
	$tab .= '<td align=right>' . number_format($totSemua, 0) . '</td>';
	$i = 1;

	while ($i <= 12) {
		$tab .= '<td align=right>' . number_format($hasil[$a[$i]], 2) . '</td>';
		++$i;
	}

	$tab .= '<td colspan=2>&nbsp;</td>';
	$tab .= '</tr></thead>';
	$spnCol = $totRowDlm + 21;
	$tab .= "\r\n" . '                        <tr><td colspan=\'' . $spnCol . '\' align=center><br />' . "\r\n" . '                        ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                        </td>' . "\r\n" . '                        </tr>';
	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'delete':
	$tab = 'delete from ' . $dbname . '.bgt_produksi_kebun where tahunbudget=\'' . $thnbudget . '\' and kodeblok =\'' . $kdblok . '\' ';

	if (mysql_query($tab)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'getBlok':
	$optBlok = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sVhc = 'select distinct kodeblok from ' . $dbname . '.bgt_blok where tahunbudget=\'' . $thnbudget . '\' and kodeblok like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and closed=1';

	#exit(mysql_error($conn));
	($qVhc = mysql_query($sVhc)) || true;
	$brs = mysql_num_rows($qVhc);

	if (0 < $brs) {
		while ($rVhc = mysql_fetch_assoc($qVhc)) {
			if ($kdblok != '') {
				$optBlok .= '<option value=\'' . $rVhc['kodeblok'] . '\' ' . ($kdblok == $rVhc['kodeblok'] ? 'selected' : '') . '>' . $rVhc['kodeblok'] . ' [ ' . $blokLama[$rVhc['kodeblok']] . ' ]</option>';
			}
			else {
				$optBlok .= '<option value=\'' . $rVhc['kodeblok'] . '\'>' . $rVhc['kodeblok'] . ' [ ' . $blokLama[$rVhc['kodeblok']] . ' ]</option>';
			}
		}

		echo $optBlok;
	}
	else {
		exit('Error: Block for budget not set(close) yet');
	}

	break;

case 'update':
	$a = 1;

	while ($a <= $totRow) {
		if ($_POST['arrBrt'][$a] == '') {
			$_POST['arrBrt'][$a] = 0;
		}

		$totalSum += $_POST['arrBrt'][$a];
		++$a;
	}

	if ($total < $totalSum) {
		exit('Error : Monthly total (' . $totalSum . ') greater than total a year(' . $total . ') ');
	}

	$sUpdate = 'update ' . $dbname . '.bgt_produksi_kebun set updateby=\'' . $_SESSION['standard']['userid'] . '\',jjgperpkk=\'' . $jjg . '\'';
	$a = 1;

	while ($a <= $totRow) {
		if (strlen($a) == '1') {
			$c = '0' . $a;
		}
		else {
			$c = $a;
		}

		$sUpdate .= ' ,jjg' . $c . '=\'' . $_POST['arrBrt'][$a] . '\'';
		++$a;
	}

	$sUpdate .= ' where  ' . $where . '';

	if (!mysql_query($sUpdate)) {
		echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
	}

	break;

case 'getData':
	$totBrs = count($arrBln);
	$pokok = 'select * from ' . $dbname . '.bgt_produksi_kebun WHERE kodeblok=\'' . $kdblok . '\' and tahunbudget=\'' . $thnbudget . '\'';

	#exit(mysql_error());
	($qOpt = mysql_query($pokok)) || true;
	$rRow = mysql_num_rows($qOpt);

	if (0 < $rRow) {
		if ($_POST['statInputan'] != 1) {
			$sTot = 'select distinct pokokproduksi,jjgperpkk from ' . $dbname . '.bgt_produksi_kbn_vw where kodeblok=\'' . $kdblok . '\' and tahunbudget=\'' . $thnbudget . '\'';

			exit(mysql_error($sTot));
			($qTot = mysql_query($sTot)) || true;
			$rRes = mysql_fetch_assoc($qTot);
			$a3 = $rRes['pokokproduksi'];
			$a1 = $rRes['jjgperpkk'];
			$total = $a1 * $a3;
			$isi .= '<fieldset style=\'width:200px;\'><legend>' . $_SESSION['lang']['sebaran'] . '/' . $_SESSION['lang']['bulan'] . ' :' . $kdblok . '</legend>';
			$isi .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
			$isi .= '<tr class=rowheader><td>' . $_SESSION['lang']['total'] . ' (Jjg)</td><td align=center>%</td><td align=right id=\'hasilPerkalian\'>' . number_format($total) . '</td></tr></thead><tbody>';
			$rOpt = mysql_fetch_assoc($qOpt);
			$bre = 1;

			while ($bre <= $totBrs) {
				if (strlen($bre) < 2) {
					$abe = '0' . $bre;
				}
				else {
					$abe = $bre;
				}

				@$hslDr = ($rOpt['jjg' . $abe] / $total) * 100;
				$isi .= '<tr class=rowcontent><td>' . $arrBln[$bre] . '</td>' . "\r\n" . '                                            <td><input type=text class=myinputtextnumber size=3 onkeypress="return angka_doang(event);" id=persenPrdksi' . $bre . ' onblur=ubahNilai(this.value,\'' . $total . '\',\'brt_x\') value=\'' . number_format($hslDr, 0) . '\' /></td>';
				$isi .= '<td><input type=\'text\' id=brt_x' . $bre . ' class="myinputtextnumber" style="width:75px;" value=' . $rOpt['jjg' . $abe] . ' /></td>' . "\r\n" . '                                            </tr>';
				++$bre;
			}
		}
		else {
			$isi .= '<fieldset style=\'width:200px;\'><legend>' . $_SESSION['lang']['sebaran'] . '/' . $_SESSION['lang']['bulan'] . ' :' . $kdblok . '</legend>';
			$isi .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
			$isi .= '<tr class=rowheader><td>' . $_SESSION['lang']['total'] . ' (Jjg)</td><td align=center>%</td><td align=right>' . number_format($total) . '</td></tr></thead><tbody>';
			@$bagi = $total / 12;

			foreach ($arrBln as $brs2 => $dtBln2) {
				@$bagi2 = $bagi / $total;
				$isi .= '<tr class=rowcontent><td>' . $dtBln2 . '</td>' . "\r\n" . '                                    <td><input type=text class=myinputtextnumber size=3 onkeypress="return angka_doang(event);" id=persenPrdksi' . $brs2 . ' onblur=ubahNilai(this.value,\'' . $total . '\',\'brt_x\') value=' . number_format($bagi2 * 100, 0, '.', '') . '></td>';
				$isi .= '<td><input type=\'text\' id=brt_x' . $brs2 . ' class="myinputtextnumber" style="width:75px;" value=' . $bagi . ' /></td>' . "\r\n" . '                                    </tr>';
			}
		}
	}
	else {
		$isi .= '<fieldset style=\'width:200px;\'><legend>' . $_SESSION['lang']['sebaran'] . '/' . $_SESSION['lang']['bulan'] . ' :' . $kdblok . '</legend>';
		$isi .= '<table cellspacing=1 cellpadding=1 border=0 class=sortable align=center><thead>';
		$isi .= '<tr class=rowheader><td>' . $_SESSION['lang']['total'] . ' (Jjg)</td><td align=center>%</td><td align=right>' . number_format($total) . '</td></tr></thead><tbody>';
		@$bagi = $total / 12;

		foreach ($arrBln as $brs2 => $dtBln2) {
			@$bagi2 = $bagi / $total;
			$isi .= '<tr class=rowcontent><td>' . $dtBln2 . '</td>' . "\r\n" . '                                <td><input type=text class=myinputtextnumber size=3 onkeypress="return angka_doang(event);" id=persenPrdksi' . $brs2 . ' onblur=ubahNilai(this.value,\'' . $total . '\',\'brt_x\') value=' . number_format($bagi2 * 100, 0, '.', '') . '></td>';
			$isi .= '<td><input type=\'text\' id=brt_x' . $brs2 . ' class="myinputtextnumber" style="width:75px;" value=' . $bagi . ' /></td>' . "\r\n" . '                                </tr>';
		}
	}

	$isi .= '<tr class=rowcontent><td  colspan=3 align=center style=\'cursor:pointer;\'><img id=\'detail_add\' title=\'Simpan\' class=zImgBtn onclick="saveBrt(' . $totBrs . ')" src=\'images/save.png\'/>&nbsp;&nbsp;<img id=\'detail_add\' title=\'Clear Form\' class=zImgBtn  width=\'16\' height=\'16\'  onclick="clearForm()" src=\'images/clear.png\'/></td>';
	$isi .= '</tr></tbody></table></fieldset>';
	echo $isi;
	break;

case 'closeBudget':
	$sQl = 'select distinct tutup from ' . $dbname . '.bgt_produksi_kebun where tahunbudget=\'' . $thnttp . '\' and kodeunit=\'' . $lkstgs . '\' and tutup=1 ';

	#exit(mysql_error($conn));
	($qQl = mysql_query($sQl)) || true;
	$row = mysql_num_rows($qQl);

	if ($row != 1) {
		$sUpdate = 'update ' . $dbname . '.bgt_produksi_kebun set tutup=1 where tahunbudget=\'' . $thnttp . '\' and kodeunit=\'' . $lkstgs . '\'  ';

		if (mysql_query($sUpdate)) {
			echo '';
		}
		else {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}
	else {
		exit('Error: Budget for this period has been closed');
	}

	break;

case 'cek':
	$aCek = 'select distinct tutup from ' . $dbname . '.bgt_produksi_kebun where tahunbudget=\'' . $thnbudget . '\' and kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\' ';

	#exit(mysql_error());
	($bCek = mysql_query($aCek)) || true;

	while ($cCek = mysql_fetch_assoc($bCek)) {
		if ($cCek['tutup'] == 1) {
			echo 'warning : Budget for this period has been closed, coud not proceed';
			exit();
		}
	}

	$xCek = 'select tahunbudget,kodeblok from ' . $dbname . '.bgt_blok where tahunbudget=\'' . $thnbudget . '\' and kodeblok=\'' . $kdblok . '\' ';
	$ada = false;

	#exit(mysql_error());
	($yCek = mysql_query($xCek)) || true;

	while ($zCek = mysql_fetch_assoc($yCek)) {
		$ada = true;
	}

	if ($ada == false) {
		echo 'warning : Budget year ' . $thnbudget . ' or block code ' . $kdblok . ' not listed on block budget (Anggaran->Transaksi->Kebun->Blok Anggaran) ';
		exit();
	}

	$xCek = 'select tahunbudget,kodeblok from ' . $dbname . '.bgt_produksi_kebun where tahunbudget=\'' . $thnbudget . '\' and kodeblok=\'' . $kdblok . '\' ';
	$ada = false;

	#exit(mysql_error());
	($yCek = mysql_query($xCek)) || true;

	while ($zCek = mysql_fetch_assoc($yCek)) {
		$ada = true;
	}

	if ($ada == true) {
		echo 'warning : data already exist ';
		exit();
	}

	$iCek = 'select tahunbudget from ' . $dbname . '.bgt_bjr where tahunbudget=\'' . $thnbudget . '\' ';
	$ada = false;

	#exit(mysql_error());
	($nCek = mysql_query($iCek)) || true;

	while ($dCek = mysql_fetch_assoc($nCek)) {
		$ada = true;
	}

	if ($ada == false) {
		echo 'warning : Budget year  ' . $thnbudget . ' not found on FFB avg weight(BJR), (Anggaran->Transaksi->Kebun->BJR)';
		exit();
	}

	break;

case 'getkodeblokHeader':
	$optKodeBlokHeader = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
	$sThn = 'SELECT distinct kodeblok FROM ' . $dbname . '.bgt_produksi_kebun where kodeunit like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' order by kodeblok';

	#exit(mysql_error($conn));
	($qThn = mysql_query($sThn)) || true;

	while ($rThn = mysql_fetch_assoc($qThn)) {
		$optKodeBlokHeader .= '<option value=\'' . $rThn['kodeblok'] . '\'>' . $rThn['kodeblok'] . '</option>';
	}

	echo $optKodeBlokHeader;
	break;

case 'getthnbudgetHeader':
	$optTahunBudgetHeader = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sThn = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_produksi_kebun where kodeunit like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' order by tahunbudget desc';

	#exit(mysql_error($conn));
	($qThn = mysql_query($sThn)) || true;

	while ($rThn = mysql_fetch_assoc($qThn)) {
		$optTahunBudgetHeader .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
	}

	echo $optTahunBudgetHeader;
	break;

case 'getThn':
	$optthnttp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sql = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_produksi_kebun where kodeunit like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and tutup=0 order by tahunbudget desc';

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;

	while ($data = mysql_fetch_assoc($qry)) {
		$optthnttp .= '<option value=' . $data['tahunbudget'] . '>' . $data['tahunbudget'] . '</option>';
	}

	echo $optthnttp;
	break;

case 'getOrg':
	$optorgclose = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sql = 'SELECT distinct kodeunit FROM ' . $dbname . '.bgt_produksi_kebun where kodeunit like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and tutup=0 ';

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;

	while ($data = mysql_fetch_assoc($qry)) {
		$optorgclose .= '<option value=' . $data['kodeunit'] . '>' . $optNm[$data['kodeunit']] . '</option>';
	}

	echo $optorgclose;
	break;

case 'carikebun':
	if (isset($_POST['kebun'])) {
		$txt_search = $_POST['kebun'];
	}
	else {
		$txt_search = '';
	}

	if ($txt_search != '') {
		$where = ' kodeblok LIKE  \'%' . $txt_search . '%\'';
	}
	else if ($txt_tgl != '') {
		$where .= ' tanggal LIKE \'' . $txt_tgl . '\'';
	}
	else if (($txt_tgl != '') && ($txt_search != '')) {
		$where .= ' notransaksi LIKE \'%' . $txt_search . '%\' and tanggal LIKE \'%' . $txt_tgl . '%\'';
	}

	if (($txt_search == '') && ($txt_tgl == '')) {
		$strx = 'select * from ' . $dbname . '.vhc_penggantianht where  ' . $where . ' order by tanggal desc';
	}
	else {
		$strx = 'select * from ' . $dbname . '.vhc_penggantianht where   ' . $where . ' order by tanggal desc';
	}

	$limit = 10;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.bgt_produksi_kebun where kodeblok like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' ' . $tmbhsimpan . ' ' . $tmbh . ' order by kodeblok asc ';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$totRowDlm = count($arrBln);
	$tab = '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead><tr class=rowheader><td width=15 align=center>No</td>';
	$tab .= '<td align=center width=90>' . $_SESSION['lang']['kodeblok'] . '</td>';
	$tab .= '<td align=center width=90>' . $_SESSION['lang']['budgetyear'] . '</td>';
	$tab .= '<td align=center width=75>' . $_SESSION['lang']['thntnm'] . '</td>';
	$tab .= '<td align=center width=100>' . $_SESSION['lang']['pkkproduktif'] . '</td>';
	$tab .= '<td align=center width=50>' . $_SESSION['lang']['bjr'] . '</td>';
	$tab .= '<td align=center width=150>' . $_SESSION['lang']['jenjangpokoktahun'] . '</td>';
	$tab .= '<td align=center  width=50>' . $_SESSION['lang']['kgThn'] . '</td>';

	foreach ($arrBln as $brs7 => $dtBln7) {
		$tab .= '<td  width=45 align=center>' . $dtBln7 . '</td>';
	}

	$tab .= '<td align=center>Aksi</td></tr></thead><tbody>';
	$sList = 'select * from ' . $dbname . '.bgt_produksi_kbn_kg_vw where kodeunit=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $tmbhsimpan . ' ' . $tmbh . '  order by kodeblok asc limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($qList = mysql_query($sList)) || true;

	while ($rList = mysql_fetch_assoc($qList)) {
		$pokok = 'select jjgperpkk,tutup from ' . $dbname . '.bgt_produksi_kebun WHERE kodeblok=\'' . $rList['kodeblok'] . '\' and tahunbudget=\'' . $rList['tahunbudget'] . '\'';

		#exit(mysql_error());
		($qOpt = mysql_query($pokok)) || true;
		$rOpt = mysql_fetch_assoc($qOpt);
		$no += 1;
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=center>' . $no . '</td>';
		$tab .= '<td align=left>' . $rList['kodeblok'] . '</td>';
		$tab .= '<td align=right>' . $rList['tahunbudget'] . '</td>';
		$tab .= '<td align=right>' . $rList['thntnm'] . '</td>';
		$tab .= '<td align=right>' . $rList['pokokproduksi'] . '</td>';
		$tab .= '<td align=right>' . $rList['bjr'] . '</td>';
		$tab .= '<td align=right>' . $rOpt['jjgperpkk'] . '</td>';
		$a1 = $rOpt['jjgperpkk'];
		$a3 = $rList['pokokproduksi'];
		$totala = $a1 * $a3;
		$tab .= '<td align=right>' . number_format($totala) . '</td>';
		$a = 1;

		while ($a <= $totRowDlm) {
			if (strlen($a) == '1') {
				$b = '0' . $a;
			}
			else {
				$b = $a;
			}

			if ($rList['kg' . $b] == '') {
				$rList['kg' . $b] = 0;
			}

			$tab .= '<td align=\'right\'>' . number_format($rList['kg' . $b], 2) . '</td>';
			++$a;
		}

		if ($rOpt['tutup'] == 0) {
			$tab .= '<td align=\'center\'>' . "\r\n" . '                                                                <img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $rList['tahunbudget'] . '\',\'' . $rList['kodeblok'] . '\',\'' . $rList['pokokproduksi'] . '\',\'' . $rList['bjr'] . '\',\'' . $rOpt['jjgperpkk'] . '\',\'' . $totala . '\',\'' . $rList['thntnm'] . '\');">' . "\r\n" . '                                                                <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="Del(\'' . $rList['tahunbudget'] . '\',\'' . $rList['kodeblok'] . '\');">' . "\r\n\r\n" . '                                                   </td>';
		}
		else {
			$tab .= '<td>' . $_SESSION['lang']['tutup'] . '</td>';
		}

		$tab .= '</tr>';
		$a = array(1 => 'kg01', 2 => 'kg02', 3 => 'kg03', 4 => 'kg04', 5 => 'kg05', 6 => 'kg06', 7 => 'kg07', 8 => 'kg08', 9 => 'kg09', 10 => 'kg10', 11 => 'kg11', 12 => 'kg12');
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == '1') {
				$b = '0' . $i;
			}
			else {
				$b = $i;
			}

			$totseb1 = 'select kg' . $b . ' from ' . $dbname . '.bgt_produksi_kbn_kg_vw where kodeblok=\'' . $rList['kodeblok'] . '\' and tahunbudget=\'' . $rList['tahunbudget'] . '\'';

			#exit(mysql_error());
			($totseb2 = mysql_query($totseb1)) || true;

			#exit(mysql_error());
			($totseb3 = mysql_fetch_array($totseb2)) || true;
			$hasil += 'kg' . $b;
			++$i;
		}

		$totSemua += $totala;
		$totbjr += $rList['bjr'];
		$totpkkprod += $rList['pokokproduksi'];
		$totjpt += $rOpt['jjgperpkk'];
	}

	$tab .= '<thead><tr class=rowheader><td align=center colspan=4>' . $_SESSION['lang']['total'] . '</td>';
	$tab .= '<td align=right>' . number_format($totpkkprod) . '</td>';
	$tab .= '<td align=right>' . number_format($totbjr) . '</td>';
	$tab .= '<td align=right>' . number_format($totjpt) . '</td>';
	$tab .= '<td align=right>' . number_format($totSemua) . '</td>';
	$i = 1;

	while ($i <= 12) {
		$tab .= '<td align=right>' . number_format($hasil[$a[$i]], 2) . '</td>';
		++$i;
	}

	$tab .= '<td></td>';
	$tab .= '</tr></thead>';
	$spnCol = $totRowDlm + 21;
	$tab .= "\r\n" . '                        <tr><td colspan=\'' . $spnCol . '\' align=center><br />' . "\r\n" . '                        ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                        <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                        </td>' . "\r\n" . '                        </tr>';
	$tab .= '</tbody></table>';
	echo $tab;
	break;
}

?>
