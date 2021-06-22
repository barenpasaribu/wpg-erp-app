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
$periodePsr = (isset($_POST['periodePsr2']) ? $_POST['periodePsr2'] : $_GET['periodePsr2']);
$barang = (isset($_POST['komodoti']) ? $_POST['komodoti'] : $_GET['komodoti']);
$tglHarga = (isset($_GET['tglHarga']) ? tanggalsystem($_GET['tglHarga']) : '');
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
$arrTgl = array();

foreach ($tmpData1 as $pasar => $row1) {
	$i = 1;
	$minggu = 1;
	$harga = 0;

	foreach ($row1 as $row2) {
		if (7 < $i) {
			$i = 1;
			$data1[$pasar][$minggu - 1] = $harga / 7;
			$arrTgl[] = $minggu;
			$harga = 0;
			++$minggu;
		}

		$harga += $row2;
		++$i;
	}

	if ($i <= 8) {
		$data1[$pasar][$minggu - 1] = $harga / ($i - 1);
		$arrTgl[] = $minggu;
	}
}

$ydata = $data1;
$test = array(1, 2, 3, 4, 5);
$width = 600;
$height = 300;
$graph = new Graph($width, $height);
$graph->SetScale('intlin');
$graph->img->SetMargin(60, 30, 40, 40);
$graph->xaxis->SetTickLabels($arrTgl);

foreach ($ydata as $pasar => $data) {
	$lineplot = new LinePlot($data);
	$lineplot->SetLegend($optPasar[$pasar]);
	$graph->Add($lineplot);
}

$graph->Stroke();

?>
