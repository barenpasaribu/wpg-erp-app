<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'config/connection.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
$method = $_POST['method'];
$txtSearch = $_POST['txtSearch'];
$tglCari = tanggalsystem($_POST['tglCari']);
$kdGudang = $_POST['kdGudang'];
$nmBrg = $_POST['nmBrg'];
$optNma = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$filter =" and (notransaksi like '%".$_SESSION['empl']['lokasitugas']."%' and ".
	"nopo like '%".$_SESSION['empl']['kodeorganisasi']."%') ";

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

		if ($nmBrg != '') {
			$where .= ' and b.kodebarang in (select  kodebarang from ' . $dbname . '.log_5masterbarang where namabarang like \'%' . $nmBrg . '%\')';
		}

		if ($kdGudang != '') {
			$where .= ' and a.kodegudang=\'' . $kdGudang . '\'';
		}

		if ($tglCari != '') {
			$where .= ' and a.tanggal=\'' . $tglCari . '\'';
		}

		if ($_POST['nopp'] != '') {
			if (strlen($_POST['nopp']) < 7) {
				exit('error: masukan nopp min: 001/12 (6 karakter)');
			}
			else {
				$where .= ' and a.nopo in (select distinct nopo from ' . $dbname . '.log_podt where nopp like \'' . $_POST['nopp'] . '%\')';
			}
		}
		else if ($txtSearch != '') {
			$where .= ' and a.nopo like \'%' . $txtSearch . '%\'';
		}

//		if ($where==""){
//			$where = "(1=1) $filter";
//		} else {
			$where.=" and (a.notransaksi like '%".$_SESSION['empl']['lokasitugas']."%' and ".
				"a.nopo like '%".$_SESSION['empl']['kodeorganisasi']."%') ";
//		}

		$sql2 = "select distinct count(*) as jmlhrow from $dbname.log_transaksiht a ".
			"left join $dbname.log_transaksidt b on a.notransaksi=b.notransaksi ".
			"where tipetransaksi=1 $where ORDER BY a.notransaksi DESC";

		$sql2 = "select * from ($sql2) x where 1=1 $filter";
		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;

		while ($jsl = mysql_fetch_object($query2)) {
			$jlhbrs = $jsl->jmlhrow;
		}
		$str = "SELECT distinct a.notransaksi,tanggal,a.nopo,kodegudang FROM $dbname.log_transaksiht a ".
			"left join $dbname.log_transaksidt b on a.notransaksi=b.notransaksi ".
			"where tipetransaksi=1  $where  ORDER BY a.notransaksi DESC limit $offset,$limit";

		$str = "select * from ($str) x where 1=1 $filter";
		if ($res = mysql_query($str)) {
			$row = mysql_num_rows($res);

			if ($row != 0) {
				$jlhbrs = $row;

				while ($bar = mysql_fetch_assoc($res)) {
					$kodeorg = $bar['kodeorg'];
					$no += 1;
					echo '<tr class=rowcontent >' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td id=td_' . $no . '>' . $bar['notransaksi'] . '</td>' . "\r\n\t\t\t\t" . '  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar['nopo'] . '</td>' . "\r\n" . '                                  <td>' . $optNma[$bar['kodegudang']] . '</td>';
					echo "\t\t\t\t\t" . ' <td>' . "\t\t\t\r\n\t\t\t\t\t" . ' <button class=mybutton onclick="previewBapb(\'';
					echo $bar['notransaksi'];
					echo '\',event);" >';
					echo $_SESSION['lang']['print'];
					echo "\t\t\t\t\t" . ' </button>' . "\r\n\t\t\t\t\t" . ' </td>' . "\r\n\t\r\n\t\t\t\t" . ' ';
					echo '</tr>';
				}

				echo "\r\n\t\t\t\t" . ' <tr><td colspan=8 align=center>' . "\r\n\t\t\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t\t\t" . '<button class=mybutton onclick=cariPage(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t\t\t" . '</td>' . "\r\n\t\t\t\t" . '</tr><input type=hidden id=nopp_' . $no . ' name=nopp_' . $no . ' value=\'' . $bar['nopp'] . '\' />';
			}
			else {
				echo '<tr class=rowcontent><td colspan=6>' . $_SESSION['lang']['dataempty'] . '</td></tr>';
			}
		}
		else {
			echo ' Gagal,' . mysql_error($conn);
		}

		break;

	case 'loadData':
		$limit = 20;
		$page = 0;

		if (isset($_POST['page'])) {
			$page = $_POST['page'];

			if ($page < 0) {
				$page = 0;
			}
		}

		$offset = $page * $limit;
		$sql2 = "select distinct count(*) as jmlhrow from $dbname.log_transaksiht ".
			"where tipetransaksi=1 $filter ORDER BY notransaksi DESC";

		#exit(mysql_error());
		($query2 = mysql_query($sql2)) || true;

		while ($jsl = mysql_fetch_object($query2)) {
			$jlhbrs = $jsl->jmlhrow;
		}

		$str = "SELECT distinct * FROM $dbname.log_transaksiht ".
			"where tipetransaksi=1 $filter ORDER BY notransaksi DESC limit $offset,$limit";

		if ($res = mysql_query($str)) {
			while ($bar = mysql_fetch_assoc($res)) {
				$kodeorg = $bar['kodeorg'];
				$no += 1;
				echo '<tr class=rowcontent >' . "\r\n\t\t\t\t" . '  <td>' . $no . '</td>' . "\r\n\t\t\t\t" . '  <td id=td_' . $no . '>' . $bar['notransaksi'] . '</td>' . "\r\n\t\t\t\t" . '  <td>' . tanggalnormal($bar['tanggal']) . '</td>' . "\r\n\t\t\t\t" . '  <td>' . $bar['nopo'] . '</td>' . "\r\n" . '                                  <td>' . $optNma[$bar['kodegudang']] . '</td>';
				echo "\t\t\t\t\t" . ' <td>' . "\t\t\t\r\n\t\t\t\t\t" . ' <button class=mybutton onclick="previewBapb(\'';
				echo $bar['notransaksi'];
				echo '\',event);" >';
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
