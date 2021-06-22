<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$method = $_POST['method'];

$filterPO =" and " .getQuery("filterpo");

switch ($method) {
	case 'list_new_data':
		$limit = 20;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;

		if (isset($_POST['txtSearch'])) {
			$txt_search = $_POST['txtSearch'];
			$txt_tgl = tanggalsystem($_POST['tglCari']);
			$txt_tgl_t = substr($txt_tgl, 0, 4);
			$txt_tgl_b = substr($txt_tgl, 4, 2);
			$txt_tgl_tg = substr($txt_tgl, 6, 2);
			$txt_tgl = $txt_tgl_t . '-' . $txt_tgl_b . '-' . $txt_tgl_tg;
		}
		else {
			$txt_search = '';
			$txt_tgl = '';
		}

		if ($txt_search != '') {
			$where = ' and nopo LIKE  \'%' . $txt_search . '%\'';
		}
		else if ($txt_tgl != '') {
			$where .= ' and tanggal LIKE \'' . $txt_tgl . '\'';
		}

		$addTmbh = $filterPO;

//		if (($_SESSION['empl']['tipelokasitugas'] != 'HOLDING') && ($_SESSION['empl']['tipelokasitugas'] != 'KANWIL')) {
//			$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
//
//			#exit(mysql_error($conn));
//			($qPt = mysql_query($sPt)) || true;
//			$rPt = mysql_fetch_assoc($qPt);
//			$addTmbh = ' and kodeorg=\'' . $rPt['induk'] . '\'';
//		}

		$strx = 'SELECT * FROM ' . $dbname . '.log_poht where statuspo>1  ' . $addTmbh . ' ' . $where . ' order by tanggal desc limit ' . $offset . ',' . $limit . '';
		$sql2 = 'SELECT count(*) as jmlhrow FROM ' . $dbname . '.log_poht where statuspo>1   ' . $addTmbh . ' ' . $where . ' order by tanggal desc ';

		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;

		while ($jsl = mysql_fetch_object($query2)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		if ($res = mysql_query($strx)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$kodeorg = $bar['kodeorg'];
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $kodeorg . '\' or induk=\'' . $kodeorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$no += 1;

				if ($bar['stat_release'] == 1) {
					$st = $_SESSION['lang']['release_po'];
				}
				else {
					$st = $_SESSION['lang']['un_release_po'];
				}

				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t\t\t" . '  <td id=td_' . $no . '>' . $bar['nopo'] . '</td>' . "\r\n\t\t\t\t\t\t" . '  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n\t\t\t\t\t\t" . '  <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t\t\t\t" . '  <td>' . $st . '</td>';
				$sql = 'select * from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['persetujuan1'] . '\'';

				#exit(mysql_error());
				($query = mysql_query($sql)) || true;
				$yrs = mysql_fetch_assoc($query);
				echo '<td align=center>' . $yrs['namakaryawan'] . '</td>';
				echo "\t\t\t\t\t\t\t" . ' <td>' . "\r\n\t\t\t\t\t\t\t" . ' <button class=mybutton onclick="masterPDF(\'log_poht\',\'';
				echo $bar['nopo'];
				echo '\',\'\',\'log_slave_print_po\',event);" >';
				echo $_SESSION['lang']['print'];
				echo "\t\t\t\t\t\t\t" . ' </button>' . "\r\n\t\t\t\t\t\t\t" . ' </td>' . "\r\n\t\t\t\t\t\t" . ' ';
				echo "\t\t\t\t\t\t" . '<!-- <td>&nbsp;</td>-->' . "\r\n\t\t\t\t\t\t" . ' ';
				echo '</tr>';
			}

			echo "\r\n\t\t\t\t\t\t" . ' <tr><td colspan=9 align=center>' . "\r\n\t\t\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t\t\t\t" . '</td>' . "\r\n\t\t\t\t\t\t" . '</tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;

	case 'loadData':
		$addTmbh = $filterPO;

//	if (($_SESSION['empl']['tipelokasitugas'] != 'HOLDING') && ($_SESSION['empl']['tipelokasitugas'] != 'KANWIL')) {
//		$sPt = 'select distinct induk from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $_SESSION['empl']['lokasitugas'] . '\'';
//
//		#exit(mysql_error($conn));
//		($qPt = mysql_query($sPt)) || true;
//		$rPt = mysql_fetch_assoc($qPt);
//		$addTmbh = ' and kodeorg=\'' . $rPt['induk'] . '\'';
//	}

		$limit = 20;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;
		$sql2 = "select count(*) as jmlhrow from $dbname.log_poht ".
			"where stat_release=1 " . $addTmbh . "  ORDER BY nopo DESC";
		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;

		while ($jsl = mysql_fetch_object($query2)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		$str = "SELECT * FROM $dbname.log_poht ".
			"where stat_release=1  " . $addTmbh . "  ORDER BY tanggal DESC limit $offset ,$limit ";

		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$kodeorg = $bar['kodeorg'];
				$spr = 'select * from  ' . $dbname . '.organisasi where  kodeorganisasi=\'' . $kodeorg . '\' or induk=\'' . $kodeorg . '\'';

				#exit(mysql_error($conn));
				($rep = mysql_query($spr)) || true;
				$bas = mysql_fetch_object($rep);
				$no += 1;

				if ($bar['stat_release'] == 1) {
					$st = $_SESSION['lang']['release_po'];
				}
				else {
					$st = $_SESSION['lang']['un_release_po'];
				}

				echo '<tr class=rowcontent id=\'tr_' . $no . '\'>' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td id=td_' . $no . '>' . $bar['nopo'] . '</td>' . "\r\n\t\t\t\t" . '  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bas->namaorganisasi . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $st . '</td>';
				$sql = 'select * from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar['persetujuan1'] . '\'';

				#exit(mysql_error());
				($query = mysql_query($sql)) || true;
				$yrs = mysql_fetch_assoc($query);
				echo '<td align=center>' . $yrs['namakaryawan'] . '</td>';
				echo "\t\t\t\t\t" . ' <td>' . "\t\t\t\r\n\t\t\t\t\t" . ' <button class=mybutton onclick="masterPDF(\'log_poht\',\'';
				echo $bar['nopo'];
				echo '\',\'\',\'log_slave_print_po\',event);" >';
				echo $_SESSION['lang']['print'];
				echo "\t\t\t\t\t" . ' </button>' . "\r\n\t\t\t\t\t" . ' </td>' . "\r\n\t\r\n\t\t\t\t" . ' ';
				echo '</tr>';
			}

			echo "\r\n\t\t\t\t" . ' <tr><td colspan=8 align=center>' . "\r\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;
}

?>
