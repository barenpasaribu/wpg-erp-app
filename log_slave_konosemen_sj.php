<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$where = 'a.kodept like \'%' . $_POST['kodept'] . '%\'';
$where .= ' and b.nosj IS NULL';

if (!empty($_POST['sj'])) {
	$where .= ' and a.nosj like \'%' . $_POST['s'] . '%\'';
}

$query = 'select distinct ' . "\r\n\t\t" . 'a.nosj,a.nopo,a.kodebarang,a.nopp,' . "\r\n\t\t" . 'a.jumlah,a.satuanpo,a.jenis' . "\r\n\t" . 'from ' . $dbname . '.log_suratjalandt a' . "\r\n\t" . 'left join ' . $dbname . '.log_konosemendt b' . "\r\n\t" . 'on a.kodebarang=b.kodebarang and a.nopo=b.nopo' . "\r\n\t" . 'where ' . $where;
$data = fetchData($query);
$optBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
echo '<button class=mybutton onclick="add2detail(\'sj\')" style=\'margin-top:15px\'>Add to Detail</button>' . "\r\n" . '<div style="max-height:340px;overflow:auto">' . "\r\n" . '<table cellpadding=1 cellspacing=1 border=0 class=\'sortable\'>' . "\r\n\t" . '<thead><tr class=rowheader>' . "\r\n\t\t" . '<td>*</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['nosj'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['jenis'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['kodebarang'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['namabarang'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['nopo'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['nopp'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['jumlah'];
echo '</td>' . "\r\n\t\t" . '<td>';
echo $_SESSION['lang']['satuan'];
echo '</td>' . "\r\n\t" . '</tr></thead>' . "\r\n\t" . '<tbody id=bodySearch>' . "\r\n\t\t";

foreach ($data as $key => $row) {
	echo "\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td>';
	echo makeElement('sj_' . $key, 'checkbox', 0);
	echo '</td>' . "\r\n\t\t\t" . '<td id="nosj_';
	echo $key;
	echo '">';
	echo $row['nosj'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="jenis_';
	echo $key;
	echo '">';
	echo $row['jenis'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="kodebarang_';
	echo $key;
	echo '">';
	echo $row['kodebarang'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="namabarang_';
	echo $key;
	echo '">' . "\r\n\t\t\t\t";
	echo $row['jenis'] == 'PO' ? $optBarang[$row['kodebarang']] : '';
	echo "\t\t\t" . '</td>' . "\r\n\t\t\t" . '<td id="nopo_';
	echo $key;
	echo '">';
	echo $row['nopo'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="nopp_';
	echo $key;
	echo '">';
	echo $row['nopp'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="jumlah_';
	echo $key;
	echo '" align=right>';
	echo $row['jumlah'];
	echo '</td>' . "\r\n\t\t\t" . '<td id="satuan_';
	echo $key;
	echo '">';
	echo $row['jenis'] == 'PO' ? $row['satuanpo'] : 'PETI';
	echo '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t";
}

echo "\t" . '</tbody>' . "\r\n" . '</table>' . "\r\n" . '</div>';

?>
