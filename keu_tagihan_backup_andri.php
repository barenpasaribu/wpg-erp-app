<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
echo open_body();
include 'master_mainMenu.php';
echo '<script language=javascript src=js/zTools.js?v='.date('YmdHis').'></script>'.'<script language=javascript src=js/zMaster.js></script>' . "\r\n" . '<script language=javascript src=js/zSearch.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/keu_tagihan.js\'></script>' . "\r\n" . '<script languange=javascript1.2 src=\'js/formTable.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=\'style/zTable.css\'>' . "\r\n";
$ctl = array();
$ctl[] = '<div align=\'center\'><img class=delliconBig src=images/' . $_SESSION['theme'] . '/addbig.png title=\'' . $_SESSION['lang']['new'] . '\' onclick="showAdd()"><br><span align=\'center\'>' . $_SESSION['lang']['new'] . '</span></div>';
$ctl[] = '<div align=\'center\'><img class=delliconBig src=images/' . $_SESSION['theme'] . '/list.png title=\'' . $_SESSION['lang']['list'] . '\' onclick="defaultList()"><br><span align=\'center\'>' . $_SESSION['lang']['list'] . '</span></div>';
$ctl[] = '<fieldset><legend><b>' . $_SESSION['lang']['find'] . '</b></legend>' . makeElement('sNoTrans', 'label', $_SESSION['lang']['noinvoice']) . makeElement('sNoTrans', 'text', '') . makeElement('sNoPo', 'label', $_SESSION['lang']['nopo']) . makeElement('sNoPo', 'text', '') . makeElement('sFind', 'btn', $_SESSION['lang']['find'], array('onclick' => 'searchTrans()')) . '</fieldset>';
$header = array('No Transaksi', $_SESSION['lang']['noinvoice'] . ' Supplier', $_SESSION['lang']['pt'], $_SESSION['lang']['tanggal'], 'Last Update', $_SESSION['lang']['nopo'], $_SESSION['lang']['keterangan'], $_SESSION['lang']['subtotal'], 'postingby', 'Posting Date');
$str = 'select karyawanid, namakaryawan from ' . $dbname . '.datakaryawan';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$nama[$bar->karyawanid] = $bar->namakaryawan;
}

$cols = 'noinvoice,noinvoicesupplier,kodeorg,tanggal,updateby,nopo,keterangan,nilaiinvoice,postingby,tanggalposting,posting';
$order = 'tanggal desc';
$query = selectQuery($dbname, 'keu_tagihanht', $cols, 'kodeorg=\'' . $_SESSION['org']['kodeorganisasi'] . '\' and updateby=\'' . $_SESSION['standard']['userid'] . '\'', $order, false, 10, 1);
$data = fetchData($query);
$totalRow = getTotalRow($dbname, 'keu_tagihanht');

foreach ($data as $key => $row) {
	if ($row['posting'] == 1) {
		$data[$key]['switched'] = true;
	}

	unset($data[$key]['posting']);
	$data[$key]['tanggal'] = tanggalnormal($row['tanggal']);

	if (!empty($row['tanggalposting'])) {
		$data[$key]['tanggalposting'] = tanggalnormal($row['tanggalposting']);
	}

	$data[$key]['nilaiinvoice'] = number_format($row['nilaiinvoice'], 2);
	$data[$key]['updateby'] = $nama[$row['updateby']];

	if ($row['postingby'] == 0) {
		$data[$key]['postingby'] = '';
	}
	else {
		$data[$key]['postingby'] = $nama[$row['postingby']];
	}
}

$tHeader = new rTable('headTable', 'headTableBody', $header, $data);
$tHeader->addAction('showEdit', 'Edit', 'images/' . $_SESSION['theme'] . '/edit.png');
if (($_SESSION['empl']['tipelokasitugas'] == 'HOLDING') || ($_SESSION['empl']['tipelokasitugas'] == 'KANWIL')) {
	$tHeader->addAction('deleteData', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
}
else {
	$tHeader->addAction('', 'Delete', 'images/' . $_SESSION['theme'] . '/delete.png');
}

$tHeader->addAction('postingData', 'Posting', 'images/' . $_SESSION['theme'] . '/posting.png');
$tHeader->_actions[2]->setAltImg('images/' . $_SESSION['theme'] . '/posted.png');
$tHeader->pageSetting(1, $totalRow, 10);
OPEN_BOX();
echo '<div align=\'center\'><h3>Invoice</h3></div>';
echo '<div><table align=\'center\'><tr>';

foreach ($ctl as $el) {
	echo '<td v-align=\'middle\' style=\'min-width:100px\'>' . $el . '</td>';
}

echo '</tr></table></div>';
CLOSE_BOX();
OPEN_BOX();
echo '<div id=\'workField\'>';
$tHeader->renderTable();
echo '</div>';
CLOSE_BOX();
echo close_body();

?>
