<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$keyword = $_POST['keyword'];
$target = $_POST['target'];
$tipeBud = $_POST['tipeanggaran'];
$where = 'namabarang like \'%' . $keyword . '%\'';
$query = 'SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ';
$query .= 'FROM ' . $dbname . '.`log_5masterbarang` a ';
$query .= 'LEFT OUTER JOIN (' . $dbname . '.log_5masterbarangdt b) ';
$query .= 'ON a.kodebarang=b.kodebarang ';
$query .= 'WHERE ' . $where;

if ($tipeBud == 'SDM') {
	$query .= ' and substr(a.kodebarang,1,3)=\'909\'';
}

$data = fetchData($query);
$headers = array('Kode', 'Nama', 'Satuan', 'Harga');
$table = '<table>';
$table .= '<thead><tr class=\'rowheader\'>';

foreach ($headers as $head) {
	$table .= '<td>' . $head . '</td>';
}

$table .= '</tr></thead>';
$table .= '<tbody>';

foreach ($data as $key => $row) {
	$table .= '<tr id=\'inv_tr_' . $key . '\' class=\'rowcontent\' ';
	$table .= 'onclick="passValue(\'' . $row['kodebarang'] . '\',\'' . $row['namabarang'] . '\',\'' . $row['satuan'] . '\');">';

	foreach ($row as $head => $con) {
		$table .= '<td id=\'' . $head . '_' . $key . '\'>' . $con . '</td>';
	}

	$table .= '</tr>';
}

$table .= '</tbody>';
$table .= '<tfoot></tfoot></table>';
echo $table;

?>
