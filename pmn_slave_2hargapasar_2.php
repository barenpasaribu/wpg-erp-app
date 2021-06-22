<?php


session_start();
require_once 'master_validation.php';
require_once 'config/connection.php';
include_once 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
include_once 'lib/zLib.php';
require_once 'jpgraph/jpgraph.php';
require_once 'jpgraph/jpgraph_line.php';
$proses = (isset($_POST['proses']) ? $_POST['proses'] : $_GET['proses']);
$periodePsr = (isset($_POST['periodePsr2']) ? $_POST['periodePsr2'] : $_GET['periodePsr2']);
$barang = (isset($_POST['komodoti']) ? $_POST['komodoti'] : $_GET['komodoti']);
$tglHarga = (isset($_POST['tglHarga']) ? tanggalsystem($_POST['tglHarga']) : '');
$where = '';
$whr = 'kelompokbarang=\'400\'';
$optNmBarang = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang', $whr);

if ($periodePsr == '') {
	exit('Error:Periode Tidak Boleh Kosong');
}

if ($barang == '') {
	exit('Error:Komoditi harus dipilih');
}

$dayInMonth = count(rangeTanggal(tanggalsystem($periodePsr), date('Ymd')));
$tmpTgl = explode('-', $periodePsr);
$tglPeriode = $tmpTgl[2] . '-' . $tmpTgl[1] . '-' . $tmpTgl[0];
$datetime1 = date_create($tglPeriode);
$datetime2 = date_create(date('Y-m-d'));
$interval = date_diff($datetime1, $datetime2);
$dayInMonth = $interval->format('%a');

if (91 < $dayInMonth) {
	$date2 = date('Y-m-d', mktime(0, 0, 0, $tmpTgl[1], $tmpTgl[0] + 90, $tmpTgl[2]));
	$dayInMonth = 91;
}
else {
	$date2 = date('Y-m-d');
}

$where .= ' and tanggal >= \'' . tanggalsystem($periodePsr) . '\'';
$where .= ' and tanggal <= \'' . $date2 . '\'';
$where .= ' and kodeproduk = \'' . $barang . '\'';
$str = 'select * from ' . $dbname . '.pmn_hargapasar where tanggal!=\'\' ' . $where . ' order by `tanggal` asc';
$resHarga = fetchData($str);
$optPasar = makeOption($dbname, 'pmn_5pasar', 'id,namapasar');

foreach ($resHarga as $row) {
	$datetime2 = date_create($row['tanggal']);
	$interval = date_diff($datetime1, $datetime2);
	$day = $interval->format('%a');
	$dataHarga[$day + 1][$row['pasar']] = $row['harga'];
}

foreach ($optPasar as $id => $nama) {
	$pasarHarga[$id] = 0;
}

$i = 1;

while ($i <= $dayInMonth) {
	foreach ($optPasar as $id => $nama) {
		if (isset($dataHarga[$i][$id])) {
			$pasarHarga[$id] = $dataHarga[$i][$id];
		}

		$tmpData1[$id][$i] = $pasarHarga[$id];
	}

	++$i;
}

$data1 = array();

foreach ($tmpData1 as $pasar => $row1) {
	$i = 1;
	$minggu = 1;
	$harga = 0;

	foreach ($row1 as $row2) {
		if (7 < $i) {
			$i = 1;
			$data1[$minggu][$pasar] = $harga / 7;
			$harga = 0;
			++$minggu;
		}

		$harga += $row2;
		++$i;
	}

	if ($i <= 8) {
		$data1[$minggu][$pasar] = $harga / ($i - 1);
	}
}

$arr = '##periodePsr##barang';

if ($proses == 'preview') {
	echo makeElement('excel1', 'btn', 'Export to Excel', array('onclick' => 'zExcel(event,\'pmn_slave_2hargapasar_2.php\',\'' . $arr . '\')'));
}

ob_start();
echo '<table class=sortable cellspacing=1 border=0>' . "\n\t" . '<thead>' . "\n\t\t" . '<tr class=rowheader>' . "\n\t\t\t" . '<td>';
echo $_SESSION['lang']['tanggal'];
echo '</td>' . "\n\t\t\t";

foreach ($optPasar as $pasar) {
	echo "\t\t\t" . '<td>';
	echo $pasar;
	echo '</td>' . "\n\t\t\t";
}

echo "\t\t" . '</tr>' . "\n\t" . '</thead>' . "\n\t" . '<tbody>' . "\n\t\t";

foreach ($data1 as $day => $row) {
	echo "\t\t" . '<tr class=rowcontent>' . "\n\t\t\t" . '<td>';
	echo $day;
	echo '</td>' . "\n\t\t\t";

	foreach ($row as $price) {
		echo "\t\t\t" . '<td align=right>';
		echo number_format($price, 2);
		echo '</td>' . "\n\t\t\t";
	}

	echo "\t\t" . '</tr>' . "\n\t\t";
}

echo "\t" . '</tbody>' . "\n" . '</table>' . "\n" . '<br>' . "\n";
$tab = ob_get_contents();
ob_end_clean();

if ($proses == 'excel') {
	$tab .= 'Print Time:' . date('Y-m-d H:i:s') . '<br>By:' . $_SESSION['empl']['name'];
	$dte = date('Hms');
	$nop_ = 'hargaPasar_' . $dte;
	$gztralala = gzopen('tempExcel/' . $nop_ . '.xls.gz', 'w9');
	gzwrite($gztralala, $tab);
	gzclose($gztralala);
	echo '<script language=javascript1.2>' . "\n\t" . 'window.location=\'tempExcel/' . $nop_ . '.xls.gz\';' . "\n\t" . '</script>';
}
else {
	echo $tab;
	echo '<div style=\'margin-top:10px\'>Graph</div>' . "\n" . '<iframe style=\'border:0;width:600px;height:300px\'' . "\n\t" . 'src=\'pmn_slave_2hargapasar_2_graph.php?periodePsr2=';
	echo $periodePsr;
	echo '&komodoti=';
	echo $barang;
	echo '\'></iframe>' . "\n\n";
}

?>
