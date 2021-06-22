<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';

if (isTransactionPeriod()) {
	$nopo = (isset($_POST['nopo']) ? $_POST['nopo'] : NULL);
	$supp = (isset($_POST['supplierNm']) ? $_POST['supplierNm'] : NULL);
	echo '<table cellspacing=1 border=0 class=sortable>' . "\r\n" . '        <thead>' . "\r\n\t\t" . '<tr ><td>No</td>' . "\r\n\t\t" . '    <td>' . $_SESSION['lang']['nopo'] . '</td>' . "\r\n" . '            <td>' . $_SESSION['lang']['nopp'] . '</td>' . "\r\n\t\t\t" . '<td>' . $_SESSION['lang']['pt'] . '</td>' . "\r\n\t\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t\t" . '<td>' . $_SESSION['lang']['purchaser'] . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t" . '</thead>' . "\r\n\t\t" . '</tbody>';

	if ($nopo != '') {
		$whr .= 'and (a.nopo like \'%' . $nopo . '%\' or b.nopp like \'%' . $nopo . '%\') and a.kodeorg like \'%'.$_SESSION['empl']['kodeorganisasi'].'%\' ';
	}

	if ($supp != '') {
		$whr .= 'and a.kodesupplier in (select distinct supplierid from ' . $dbname . '.log_5supplier where namasupplier like \'%' . $supp . '%\') and a.kodeorg like \'%'.$_SESSION['empl']['kodeorganisasi'].'%\' ';
	}

	$str = 'select distinct a.*,b.nopp  from ' . $dbname . '.log_poht a left join ' . $dbname . '.log_podt b on a.nopo=b.nopo where a.stat_release=1 and a.statuspo in (\'2\',\'3\') ' . $whr . '';
	$str .= 'order by a.tanggal desc,a.nopo desc';
//	saveLog($str);
	$res = mysql_query($str);
	$no = 0;

	while ($bar = mysql_fetch_object($res)) {
		$hwr = 'nopo=\'' . $bar->nopo . '\'';
		$optPur = makeOption($dbname, 'log_poht', 'nopo,purchaser', $hwr);
		$purchaser = '';
		$str = 'select namauser from ' . $dbname . '.user where karyawanid=\'' . $optPur[$bar->nopo] . '\'';
		$resv = mysql_query($str);
		$barv = mysql_fetch_object($resv);
		$purchaser = $barv->namauser;
		$no += 1;
		echo "\r\n\t\t" . '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click It\' onclick=goPickPo(\'' . $bar->nopo . '\')><td>' . $no . '</td>' . "\r\n\t\t" . '    <td>' . $bar->nopo . '</td>' . "\r\n" . '            <td>' . $bar->nopp . '</td>' . "\r\n\t\t\t" . '<td>' . $bar->kodeorg . '</td>' . "\r\n\t\t\t" . '<td>' . tanggalnormal($bar->tanggal) . '</td>' . "\r\n\t\t\t" . '<td>' . $purchaser . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t";
	}

	echo '</tbody>' . "\r\n\t" . '     <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
}
else {
	echo ' Error: Transaction Period missing';
}

?>
