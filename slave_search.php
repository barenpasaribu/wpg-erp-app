<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
$mode = $_GET['mode'];
$keyword = $_POST['keyword'];
$target = $_POST['target'];
$targetSatuan = $_POST['targetSatuan'];
$targetSaldo = $_POST['$targetSaldo'];

switch ($mode) {
case 'barang':
	$where = 'a.namabarang like \'%' . $keyword . '%\' and b.kodeorg like \'' . $_SESSION['empl']['kodeorganisasi'] . '%\' and b.kodegudang like \'' . $_SESSION['empl']['kdgudang'] . '%\'';
	$query = 'SELECT DISTINCT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.saldoqty),0,b.saldoqty) as saldo ';
	$query .= 'FROM ' . $dbname . '.`log_5masterbarang` a ';
	$query .= 'LEFT OUTER JOIN (' . $dbname . '.log_5masterbarangdt b) ';
	$query .= 'ON a.kodebarang=b.kodebarang ';
	$query .= 'WHERE ' . $where;
	$data = fetchData($query);
	$headers = array('Kode', 'Nama', 'Satuan', 'Saldo');
	$table = '<table>';
	$table .= '<thead><tr class=\'rowheader\'>';

	foreach ($headers as $head) {
		if ($head == 'Saldo') {
			$table .= '<td align=right>' . $head . '</td>';
		}
		else {
			$table .= '<td align=center>' . $head . '</td>';
		}
	}

	$table .= '</tr></thead>';
	$table .= '<tbody>';

	foreach ($data as $key => $row) {
		$qtynotpostedin = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['induklokasitugas'] . '\' and b.kodebarang=\'' . $row['kodebarang'] . '\' and a.tipetransaksi<5 and a.kodegudang=\'' . $_SESSION['empl']['kdgudang'] . '\'' . "\t" . 'and a.post=0 group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotpostedin = $bar2->jumlah;
		}

		if ($qtynotpostedin == '') {
			$qtynotpostedin = 0;
		}

		$qtynotposted = 0;
		$str2 = 'select sum(b.jumlah) as jumlah,b.kodebarang FROM ' . $dbname . '.log_transaksiht a left join ' . $dbname . '.log_transaksidt b on a.notransaksi=b.notransaksi where kodept=\'' . $_SESSION['empl']['induklokasitugas'] . '\' and b.kodebarang=\'' . $row['kodebarang'] . '\' and a.tipetransaksi>4 and a.kodegudang=\'' . $_SESSION['empl']['kdgudang'] . '\' and a.post=0 group by kodebarang';
		$res2 = mysql_query($str2);

		while ($bar2 = mysql_fetch_object($res2)) {
			$qtynotposted = $bar2->jumlah;
		}

		if ($qtynotposted == '') {
			$qtynotposted = 0;
		}

		$row['saldo'] = ($row['saldo'] + $qtynotpostedin) - $qtynotposted;
		$table .= '<tr id=\'inv_tr_' . $key . '\' class=\'rowcontent\' ';
		$table .= 'onclick="passValue(\'' . $row['kodebarang'] . '\',\'' . $target . '\');';
		$table .= 'passValue(\'' . $row['namabarang'] . '\',\'' . $target . '_name\');';
		$table .= 'passValue(\'' . $row['satuan'] . '\',\'' . $targetSatuan . '\');';
		$table .= 'passValue(\'' . $row['saldo'] . '\',\'' . $targetSaldo . '\');';
		$table .= '">';

		foreach ($row as $head => $con) {
			$table .= '<td id=\'' . $head . '_' . $key . '\' align=center>' . $con . '</td>';
		}

		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '<tfoot></tfoot></table>';
	echo $table;
	break;

case 'kegiatan':
	$where = 'namakegiatan like \'%' . $keyword . '%\'';
	$query = selectQuery($dbname, 'setup_kegiatan', 'kelompok,kodekegiatan,namakegiatan', $where);
	$data = fetchData($query);
	$headers = array('Kelompok', 'Kode Kegiatan', 'Nama Kegiatan');
	$table = '<table>';
	$table .= '<thead><tr class=\'rowheader\'>';

	foreach ($headers as $head) {
		$table .= '<td>' . $head . '</td>';
	}

	$table .= '</tr></thead>';
	$table .= '<tbody>';

	foreach ($data as $key => $row) {
		$table .= '<tr id=\'inv_tr_' . $key . '\' class=\'rowcontent\' ';
		$table .= 'onclick="passValue(\'' . $row['kodekegiatan'] . '\',\'' . $target . '\');';
		$table .= 'passValue(\'' . $row['namakegiatan'] . '\',\'' . $target . '_name\');">';

		foreach ($row as $head => $con) {
			$table .= '<td id=\'' . $head . '_' . $key . '\'>' . $con . '</td>';
		}

		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '<tfoot></tfoot></table>';
	echo $table;
	break;

case 'asset':
	$where = 'namabarang like \'%' . $keyword . '%\'';
	$query = 'SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ';
	$query .= 'FROM ' . $dbname . '.`log_5masterbarang` a ';
	$query .= 'LEFT OUTER JOIN (' . $dbname . '.log_5masterbarangdt b) ';
	$query .= 'ON a.kodebarang=b.kodebarang ';
	$query .= 'WHERE ' . $where;
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
		$table .= 'onclick="passValue(\'' . $row['kodebarang'] . '\',\'' . $target . '\');';
		$table .= 'passValue(\'' . htmlspecialchars($row['namabarang'], ENT_QUOTES, 'UTF-8') . '\',\'' . $target . '_name\');">';

		foreach ($row as $head => $con) {
			$table .= '<td id=\'' . $head . '_' . $key . '\'>' . $con . '</td>';
		}

		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '<tfoot></tfoot></table>';
	echo $table;
	break;

case 'customer':
	$where = 'namabarang like \'%' . $keyword . '%\'';
	$query = 'SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ';
	$query .= 'FROM ' . $dbname . '.`log_5masterbarang` a ';
	$query .= 'LEFT OUTER JOIN (' . $dbname . '.log_5masterbarangdt b) ';
	$query .= 'ON a.kodebarang=b.kodebarang ';
	$query .= 'WHERE ' . $where;
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
		$table .= 'onclick="passValue(\'' . $row['kodebarang'] . '\',\'' . $target . '\');';
		$table .= 'passValue(\'' . $row['namabarang'] . '\',\'' . $target . '_name\');">';

		foreach ($row as $head => $con) {
			$table .= '<td id=\'' . $head . '_' . $key . '\'>' . $con . '</td>';
		}

		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '<tfoot></tfoot></table>';
	echo $table;
	break;

case 'supplier':
	$where = 'namabarang like \'%' . $keyword . '%\'';
	$query = 'SELECT a.kodebarang,a.namabarang,a.satuan,IF(ISNULL(b.hargalastin),0,b.hargalastin) as harga ';
	$query .= 'FROM ' . $dbname . '.`log_5masterbarang` a ';
	$query .= 'LEFT OUTER JOIN (' . $dbname . '.log_5masterbarangdt b) ';
	$query .= 'ON a.kodebarang=b.kodebarang ';
	$query .= 'WHERE ' . $where;
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
		$table .= 'onclick="passValue(\'' . $row['kodebarang'] . '\',\'' . $target . '\');';
		$table .= 'passValue(\'' . $row['namabarang'] . '\',\'' . $target . '_name\');">';

		foreach ($row as $head => $con) {
			$table .= '<td id=\'' . $head . '_' . $key . '\'>' . $con . '</td>';
		}

		$table .= '</tr>';
	}

	$table .= '</tbody>';
	$table .= '<tfoot></tfoot></table>';
	echo $table;
	break;
}

?>
