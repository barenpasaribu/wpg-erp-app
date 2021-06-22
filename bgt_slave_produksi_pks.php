<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$method = $_POST['method'];
$thnbudget = $_POST['thnbudget'];
$kunci = $_POST['kunci'];
$kdunit = $_POST['kdunit'];
$totjamthn = $_POST['totjamthn'];
$totRow = $_POST['totRow'];
$ktbs = $_POST['ktbs'];
$kdpks = $_POST['kdpks'];
$optkdsup = $_POST['optkdsup'];
$kdsup = $_POST['kdsup'];
$kgtbs = $_POST['kgtbs'];
$oerc = $_POST['oerc'];
$oerk = $_POST['oerk'];
$thnclose = $_POST['thnclose'];
$lkstgs = $_POST['lkstgs'];
$thnttp = $_POST['thnttp'];
$thnbudgetHeader = $_POST['thnbudgetHeader'];
$optNmsupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier');
$optNmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$arrBln = array(1 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Jan', 2 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Feb', 3 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Mar', 4 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Apr', 5 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Mei', 6 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Jun', 7 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Jul', 8 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Aug', 9 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Sep', 10 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Okt', 11 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Nov', 12 => 'Kg.' . $_SESSION['lang']['tbs'] . '.Des');

switch ($method) {
case 'getsup':
	echo $optkdsup;
	break;

case 'saveData':
	$a = 1;

	while ($a <= $totRow) {
		if ($_POST['arrBrt'][$a] == '') {
			$_POST['arrBrt'][$a] = 0;
		}

		$totalSum += $_POST['arrBrt'][$a];
		++$a;
	}

	if ($kgtbs < $totalSum) {
		exit('Error: Monthly total ' . $totalSum . ' larger than annual :' . $kgtbs . '');
	}

	$sCek = 'select distinct * from ' . $dbname . '.bgt_produksi_pks where kunci=\'' . $kunci . '\' and tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\' and kodeunit=\'' . $kdsup . '\' ';

	#exit(mysql_error());
	($qCek = mysql_query($sCek)) || true;
	$rCek = mysql_num_rows($qCek);

	if ($rCek < 1) {
		$sInsert = 'insert into ' . $dbname . '.bgt_produksi_pks (tahunbudget, millcode, kodeunit, kgolah, oerbunch, oerkernel, updateby, olah01, olah02, olah03, olah04, olah05, olah06, olah07, olah08, olah09, olah10, olah11, olah12)';
		$sInsert .= ' values (\'' . $thnbudget . '\',\'' . $kdpks . '\',\'' . $kdsup . '\',\'' . $kgtbs . '\',\'' . $oerc . '\',\'' . $oerk . '\',\'' . $_SESSION['standard']['userid'] . '\'';
		$abr = 1;

		while ($abr <= $totRow) {
			$sInsert .= ',\'' . $_POST['arrBrt'][$abr] . '\'';
			++$abr;
		}

		$sInsert .= ')';

		if (!mysql_query($sInsert)) {
			echo ' Gagal,__' . $sInsert . '__' . mysql_error($conn);
		}
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

	if ($kgtbs < $totalSum) {
		exit('Error:Distribution incoorect, monthly total larger than annual');
	}

	$sUpdate = 'update ' . $dbname . '.bgt_produksi_pks set kgolah=\'' . $kgtbs . '\',oerbunch=\'' . $oerc . '\',oerkernel=\'' . $oerk . '\',updateby=\'' . $_SESSION['standard']['userid'] . '\'';
	$a = 1;

	while ($a <= $totRow) {
		if (strlen($a) == '1') {
			$c = '0' . $a;
		}
		else {
			$c = $a;
		}

		$sUpdate .= ' ,olah' . $c . '=\'' . $_POST['arrBrt'][$a] . '\'';
		++$a;
	}

	$sUpdate .= ' where tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\' and kodeunit=\'' . $kdsup . '\'';

	if (!mysql_query($sUpdate)) {
		echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
	}

	break;

case 'loadData':
	$tmbh = '';

	if ($thnbudgetHeader != '') {
		$tmbh = ' and tahunbudget=\'' . $thnbudgetHeader . '\' ';
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
	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.bgt_produksi_pks where millcode=\'' . $_SESSION['empl']['lokasitugas'] . '\'  ';

	#exit(mysql_error($conn));
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$totRowDlm = count($arrBln);
	$tab = '<table cellpadding=1 cellspacing=1 border=0 class=sortable>';
	$tab .= '<thead><tr class=rowheader><td width=25>No</td>';
	$tab .= '<td align=center  width=100>' . $_SESSION['lang']['budgetyear'] . '</td>';
	$tab .= '<td align=center  width=125>' . $_SESSION['lang']['kodesupplier'] . '</td>';
	$tab .= '<td align=center  width=75>' . $_SESSION['lang']['kgtbs'] . '</td>';
	$tab .= '<td align=center  width=75>' . $_SESSION['lang']['oer'] . '(CPO)</td>';
	$tab .= '<td align=center  width=75>' . $_SESSION['lang']['oer'] . '(Ker)</td>';

	foreach ($arrBln as $brs6 => $dtBln6) {
		$tab .= '<td align=center width=50>' . $dtBln6 . '</td>';
	}

	$tab .= '<td>Aksi</td></tr></thead><tbody>';
	$sList = 'select * from ' . $dbname . '.bgt_produksi_pks where  millcode=\'' . $_SESSION['empl']['lokasitugas'] . '\' ' . $tmbh . ' order by tahunbudget desc limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($qList = mysql_query($sList)) || true;

	while ($rList = mysql_fetch_assoc($qList)) {
		$no += 1;

		if (4 < strlen($rList['kodeunit'])) {
			$dtKdunit = $optNmsupp[$rList['kodeunit']];
			$intex = 0;
		}
		else {
			$sintex = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $rList['kodeunit'] . '\'';

			exit(mysql_error($sintex));
			($qIntex = mysql_query($sintex)) || true;
			$rIntex = mysql_fetch_assoc($qIntex);

			if ($rIntex['induk'] == $_SESSION['org']['kodeorganisasi']) {
				$intex = 1;
			}
			else {
				$intex = 2;
			}

			$dtKdunit = $optNmOrg[$rList['kodeunit']];
		}

		$rList['tutup'] == 0 ? $edDt = 'onclick="fillField(\'' . $rList['tahunbudget'] . '\',\'' . $_SESSION['empl']['lokasitugas'] . '\',\'' . $rList['kodeunit'] . '\',\'' . $rList['kgolah'] . '\',\'' . $rList['oerbunch'] . '\',\'' . $rList['oerkernel'] . '\',\'' . $intex . '\');"  title=\'Edit Data ' . $dtKdunit . '__' . $rList['tahunbudget'] . '\'  style=\'cursor:pointer;\' ' : $edDt = '';
		$tab .= '<tr class=rowcontent>';
		$tab .= '<td align=center ' . $edDt . '>' . $no . '</td>';
		$tab .= '<td align=right  ' . $edDt . '>' . $rList['tahunbudget'] . '</td>';
		$tab .= '<td align=left  ' . $edDt . '>' . $dtKdunit . '</td>';
		$tab .= '<td align=right  ' . $edDt . '>' . number_format($rList['kgolah'], 2) . '</td>';
		$tab .= '<td align=right  ' . $edDt . '>' . number_format($rList['oerbunch'], 2) . '</td>';
		$tab .= '<td align=right  ' . $edDt . '>' . number_format($rList['oerkernel'], 2) . '</td>';
		$a = 1;

		while ($a <= $totRowDlm) {
			if (strlen($a) == '1') {
				$b = '0' . $a;
			}
			else {
				$b = $a;
			}

			if ($rList['olah' . $b] == '') {
				$rList['olah' . $b] = 0;
			}

			$tab .= '<td align=\'right\' ' . $edDt . '>' . number_format($rList['olah' . $b], 2) . '</td>';
			++$a;
		}

		if ($rList['tutup'] == 0) {
			$tab .= '<td align=\'center\'>' . "\r\n" . '                                                        <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="Del(\'' . $rList['kunci'] . '\');">' . "\r\n" . '                                           </td>';
		}
		else {
			$tab .= '<td>' . $_SESSION['lang']['tutup'] . '</td>';
		}

		$tab .= '</tr>';
		$totkgolah += $rList['kgolah'];
		$totoerb += $rList['oerbunch'];
		$totoerk += $rList['oerkernel'];
		$a = array(1 => 'olah01', 2 => 'olah02', 3 => 'olah03', 4 => 'olah04', 5 => 'olah05', 6 => 'olah06', 7 => 'olah07', 8 => 'olah08', 9 => 'olah09', 10 => 'olah10', 11 => 'olah11', 12 => 'olah12');
		$i = 1;

		while ($i <= 12) {
			if (strlen($i) == '1') {
				$b = '0' . $i;
			}
			else {
				$b = $i;
			}

			$totseb1 = 'select olah' . $b . ' from ' . $dbname . '.bgt_produksi_pks where tahunbudget=\'' . $rList['tahunbudget'] . '\' and millcode=\'' . $_SESSION['empl']['lokasitugas'] . '\' and kodeunit=\'' . $rList['kodeunit'] . '\'  ';

			#exit(mysql_error());
			($totseb2 = mysql_query($totseb1)) || true;

			#exit(mysql_error());
			($totseb3 = mysql_fetch_array($totseb2)) || true;
			$hasil += 'olah' . $b;
			++$i;
		}
	}

	$tab .= '<thead><tr class=rowheader><td align=center colspan=3>Total</td>';
	$tab .= '<td align=right>' . number_format($totkgolah, 2) . '</td>';
	$tab .= '<td align=right> </td>';
	$tab .= '<td align=right> </td>';
	$i = 1;

	while ($i <= 12) {
		$tab .= '<td align=right>' . number_format($hasil[$a[$i]], 2) . '</td>';
		++$i;
	}

	$tab .= '<td></td>';
	$tab .= '</tr></thead>';
	$spnCol = $totRowDlm + 7;
	$tab .= "\r\n" . '                <tr><td>&nbsp;</td></tr><tr><td colspan=\'' . $spnCol . '\' align=center>' . "\r\n" . '                ' . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n" . '                <button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n" . '                </td>' . "\r\n" . '                </tr>';
	$tab .= '</tbody></table>';
	echo $tab;
	break;

case 'delete':
	$tab = 'delete from ' . $dbname . '.bgt_produksi_pks where kunci=\'' . $kunci . '\'';

	if (mysql_query($tab)) {
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'closepks':
	$sQl = 'select distinct tutup from ' . $dbname . '.bgt_produksi_pks where tahunbudget=\'' . $thnttp . '\' and millcode=\'' . $lkstgs . '\' and tutup=1 ';

	#exit(mysql_error($conn));
	($qQl = mysql_query($sQl)) || true;
	$row = mysql_num_rows($qQl);

	if ($row != 1) {
		$sUpdate = 'update ' . $dbname . '.bgt_produksi_pks set tutup=1 where tahunbudget=\'' . $thnttp . '\' and millcode=\'' . $lkstgs . '\'  ';

		if (mysql_query($sUpdate)) {
			echo '';
		}
		else {
			echo ' Gagal,_' . $sUpdate . '__' . mysql_error($conn);
		}
	}
	else {
		exit('Error: Data has been closed');
	}

	break;

case 'cekclose':
	$aCek = 'select distinct tutup from ' . $dbname . '.bgt_produksi_pks where tahunbudget=\'' . $thnbudget . '\' and millcode=\'' . $kdpks . '\' ';

	#exit(mysql_error());
	($bCek = mysql_query($aCek)) || true;
	$cCek = mysql_fetch_assoc($bCek);

	if ($cCek['tutup'] == 1) {
		echo 'warning : Budget ' . $thnbudget . ' for code ' . $kdpks . ' has been closed';
		exit();
	}
	else {
		$optKode = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

		if ($ktbs == '1') {
			$sOpt = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where induk=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and tipe=\'KEBUN\'';
		}
		else if ($ktbs == '2') {
			$sOpt = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where induk<>\'' . $_SESSION['org']['kodeorganisasi'] . '\'  and tipe=\'KEBUN\'';
		}
		else if ($ktbs == '0') {
			$sOpt = 'select supplierid as kodeorganisasi,namasupplier as namaorganisasi from ' . $dbname . '.log_5supplier where kodetimbangan!=\'\' order by namasupplier asc';
		}

		exit(mysql_error($sOpt));
		($qOpt = mysql_query($sOpt)) || true;

		while ($rOpt = mysql_fetch_assoc($qOpt)) {
			if ($kdunit != '') {
				$optKode .= '<option value=\'' . $rOpt['kodeorganisasi'] . '\' ' . ($rOpt['kodeorganisasi'] == $kdunit ? 'selected' : '') . '>' . $rOpt['namaorganisasi'] . '</option>';
			}
			else {
				$optKode .= '<option value=\'' . $rOpt['kodeorganisasi'] . '\'>' . $rOpt['namaorganisasi'] . '</option>';
			}
		}

		if ($kdunit != '') {
			$sData = 'select * from ' . $dbname . '.bgt_produksi_pks where millcode=\'' . $_SESSION['empl']['lokasitugas'] . '\' and tahunbudget=\'' . $thnbudget . '\' and kodeunit=\'' . $kdunit . '\'';

			exit(mysql_error($sData));
			($qData = mysql_query($sData)) || true;

			while ($rData = mysql_fetch_assoc($qData)) {
				$abr = 1;

				while ($abr < 13) {
					if (strlen($abr) < 2) {
						$a = '0' . $abr;
					}
					else {
						$a = $abr;
					}

					$brd[$abr] = $rData['olah' . $a];
					++$abr;
				}
			}

			echo $optKode . '###';

			foreach ($brd as $brs) {
				$sat += 1;
				echo $brs . '###';

				if ($sat == 12) {
					echo $brs;
				}
			}
		}
		else {
			echo $optKode;
		}
	}

	break;

case 'getThn':
	$optthnttp = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sql = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_produksi_pks where millcode like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' and tutup=0 order by tahunbudget desc';

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;

	while ($data = mysql_fetch_assoc($qry)) {
		$optthnttp .= '<option value=' . $data['tahunbudget'] . '>' . $data['tahunbudget'] . '</option>';
	}

	echo $optthnttp;
	break;

case 'getOrg':
	$optorgclose = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sql = 'SELECT distinct millcode,tahunbudget FROM ' . $dbname . '.bgt_produksi_pks where millcode = \'' . $_SESSION['empl']['lokasitugas'] . '\' and tutup=0 ';

	exit('SQL ERR : ' . mysql_error());
	($qry = mysql_query($sql)) || true;

	while ($data = mysql_fetch_assoc($qry)) {
		$optorgclose .= '<option value=' . $data['millcode'] . '>' . $optNmOrg[$data['millcode']] . '</option>';
		$optThn .= '<option value=\'' . $data['tahunbudget'] . '\'>' . $data['tahunbudget'] . '</option>';
	}

	echo $optorgclose . '###' . $optThn;
	break;

case 'getthnbudgetHeader':
	$optTahunBudgetHeader = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
	$sThn = 'SELECT distinct tahunbudget FROM ' . $dbname . '.bgt_produksi_pks where millcode like \'%' . $_SESSION['empl']['lokasitugas'] . '%\' order by tahunbudget desc';

	#exit(mysql_error($conn));
	($qThn = mysql_query($sThn)) || true;

	while ($rThn = mysql_fetch_assoc($qThn)) {
		$optTahunBudgetHeader .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
	}

	echo $optTahunBudgetHeader;
	break;
}

?>
