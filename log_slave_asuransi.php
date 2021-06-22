<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$method = $_POST['method'];
$txt = $_POST['txt'];
$hrini = date('Ymd');
$nmFranco = makeOption($dbname, 'setup_franco', 'id_franco,franco_name');

switch ($method) {
case 'loadData':
	echo "\r\n\t\t\r\n\t\t" . '<table cellspacing=\'1\' border=\'0\' class=\'sortable\'>' . "\r\n\t\t\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nourut'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nokonosemen'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['nokonosemen'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['kodept'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['tanggalberangkat'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['shipper'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['vessel'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['franco'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['asalbarang'] . '</td>' . "\r\n\t\t\t\t\t" . '<td align=center>' . $_SESSION['lang']['pdf'] . '</td>' . "\r\n\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t" . '</thead>' . "\r\n\t\t" . '<tbody>';
	$limit = 30;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$maxdisplay = $page * $limit;

	if ($txt != '') {
		$txt = 'where nokonosemen like \'%' . $txt . '%\'';
	}
	else {
		$txt = '';
	}

	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.log_konosemenht  ' . $txt . '  order by tanggal desc';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$ha = 'SELECT * FROM ' . $dbname . '.log_konosemenht ' . $txt . ' order by tanggal desc  limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($hi = mysql_query($ha)) || true;
	$no = $maxdisplay;

	while ($hu = mysql_fetch_assoc($hi)) {
		$no += 1;
		echo "\r\n\t\t\t" . '<tr class=rowcontent id=tr_' . $no . '>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['nokonosemen'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['nokonosemenexp'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['kodept'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['tanggal'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . tanggalnormal($hu['tanggalberangkat']) . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['shipper'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['vessel'] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $nmFranco[$hu['franco']] . '</td>' . "\r\n\t\t\t\t" . '<td>' . $hu['asalbarang'] . '</td>' . "\r\n\t\t\t\t\r\n\r\n\t\t\t\t" . '<td><img src=images/pdf.jpg class=resicon  title=\'Print\' onclick="masterPDF(\'log_konosemenht\',\'' . $hu['nokonosemen'] . '\',\'\',\'log_slave_asuransi_pdf\',event)"></td>' . "\r\n\t\t\t" . '</tr>';
	}

	echo "\r\n\t\t" . '<tr class=rowheader><td colspan=18 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	echo '</tbody></table>';
	break;
}

?>
