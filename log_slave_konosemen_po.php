<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$where = 'a.kodeorg like \'%' . $_POST['kodept'] . '%\' and a.statuspo=3';

if (!empty($_POST['po'])) {
	$where .= ' and a.nopo like \'%' . $_POST['po'] . '%\'';
}

$query = 'select distinct ' . "\r\n\t\t" . 'a.nopo,a.kodebarang,a.namabarang,a.nopp,' . "\r\n\t\t" . 'a.jumlahpesan,a.satuan' . "\r\n\t" . 'from ' . $dbname . '.log_po_vw a' . "\r\n\t" . 'where ' . $where;
$data = fetchData($query);
$q2 = 'SELECT nopo,kodebarang,sum(jumlah) as jumlah FROM ' . $dbname . '.`log_rinciankono` where nopo like \'%' . $_POST['po'] . '%\'';
$data2 = fetchData($q2);
$optData = array();

foreach ($data2 as $row) {
	$optData[$row['nopo']][$row['kodebarang']] = $row['jumlah'];
}

echo '<button class=mybutton onclick="add2detail(\'po\')" style=\'margin-top:15px\'>Add to Detail</button>' . "\r\n" . '<div style="max-height:340px;overflow:auto">' . "\r\n" . '<table cellpadding=1 cellspacing=1 border=0 class=\'sortable\'>' . "\r\n\t" . '<thead><tr class=rowheader>' . "\r\n\t\t" . '<td>*</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['kodebarang'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['jumlah'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['jumlah'] . ' terkirim';
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n\t" . '</tr></thead>' . "\r\n\t" . '<tbody id=bodySearch>' . "\r\n\t\t";
$i = 0;

foreach ($data as $key => $row) {
	echo "\t\t";
	if ((isset($optData[$row['nopo']][$row['kodebarang']]) && ($optData[$row['nopo']][$row['kodebarang']] < $row['jumlahpesan'])) || !isset($optData[$row['nopo']][$row['kodebarang']])) {
		echo "\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td>';
		echo makeElement('po_' . $key, 'checkbox', 0);
		echo '</td>' . "\r\n\t\t\t" . '<td id="nopo_';
		echo $key;
		echo '">';
		echo $row['nopo'];
		echo '</td>' . "\r\n\t\t\t" . '<td id="kodebarang_';
		echo $key;
		echo '">';
		echo $row['kodebarang'];
		echo '</td>' . "\r\n\t\t\t" . '<td id="namabarang_';
		echo $key;
		echo '">';
		echo $row['namabarang'];
		echo '</td>' . "\r\n\t\t\t" . '<td id="nopp_';
		echo $key;
		echo '">';
		echo $row['nopp'];
		echo '</td>' . "\r\n\t\t\t" . '<td id="jumlah_';
		echo $key;
		echo '" align=right>';
		echo $row['jumlahpesan'];
		echo '</td>' . "\r\n\t\t\t" . '<td id="jumlahkirim_';
		echo $i;
		echo '" align=right>' . "\r\n\t\t\t\t";
		echo isset($optData[$row['nopo']][$row['kodebarang']]) ? $optData[$row['nopo']][$row['kodebarang']] : 0;
		echo "\t\t\t" . '</td>' . "\r\n\t\t\t" . '<td id="satuan_';
		echo $key;
		echo '">';
		echo $row['satuan'];
		echo '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t";
		++$i;
	}

	echo "\t\t";
}

echo "\t" . '</tbody>' . "\r\n" . '</table>' . "\r\n" . '</div>';

?>
