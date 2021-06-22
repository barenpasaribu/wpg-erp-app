<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$where = 'a.namabarang like \'%' . $_POST['mat'] . '%\'';
$query = 'select * from ' . $dbname . '.log_5masterbarang a where ' . $where;
$data = fetchData($query);
echo '<button class=mybutton onclick="add2detail(\'m\')" style=\'margin-top:15px\'>Add to Detail</button>' . "\r\n" . '<div style="max-height:340px;overflow:auto">' . "\r\n" . '<table cellpadding=1 cellspacing=1 border=0 class=\'sortable\'>' . "\r\n\t" . '<thead><tr class=rowheader>' . "\r\n\t\t" . '<td>*</td>' . "\r\n\t\t" . '<td>Kode</td>' . "\r\n\t\t" . '<td>Nama</td>' . "\r\n\t\t" . '<td>Satuan</td>' . "\r\n\t" . '</tr></thead>' . "\r\n\t" . '<tbody id=bodySearch>' . "\r\n\t\t";

foreach ($data as $key => $row) {
	echo "\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td>';
	echo makeElement('m_' . $key, 'checkbox', 0);
	echo '</td>' . "\r\n\t\t\t" . '<td id="kodebarang_';
	echo $key;
	echo '">';
	echo $row['kodebarang'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="namabarang_';
	echo $key;
	echo '">';
	echo $row['namabarang'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="satuan_';
	echo $key;
	echo '">';
	echo $row['satuan'];
	echo '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t";
}

echo "\t" . '</tbody>' . "\r\n" . '</table>' . "\r\n" . '</div>';

?>
