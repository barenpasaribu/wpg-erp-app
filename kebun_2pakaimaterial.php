<?php







require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';

include_once 'lib/formReport.php';

if ('HOLDING' === $_SESSION['empl']['tipelokasitugas']) {

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'");

} else {

    $optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'");

}



$optTahunTanam = makeOption($dbname, 'setup_blok', 'tahuntanam,tahuntanam', "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'", '0', true);

$optTahunTanam[''] = $_SESSION['lang']['all'];

if ('EN' === $_SESSION['language']) {

    $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan1,kelompok', "kelompok in ('BBT', 'TB', 'TBM', 'TM')", '7', true);

} else {

    $optKegiatan = makeOption($dbname, 'setup_kegiatan', 'kodekegiatan,namakegiatan,kelompok', "kelompok in ('BBT', 'TB', 'TBM', 'TM')", '7', true);

}



$optKegiatan[''] = $_SESSION['lang']['all'];

$optBarang[''] = $_SESSION['lang']['all'];

$str = 'select distinct a.kodebarang, b.namabarang from '.$dbname.".kebun_pakaimaterial a \r\n    left join ".$dbname.".log_5masterbarang b on a.kodebarang = b.kodebarang \r\n    where a.kodebarang like '3%'\r\n    order by a.kodebarang asc";

$que = mysql_query($str) ;

while ($row = mysql_fetch_assoc($que)) {

    $optBarang[$row['kodebarang']] = $row['kodebarang'].' '.$row['namabarang'];

}

if (!isset($_SESSION['lang']['lapmaterial'])) {

    $_SESSION['lang']['lapmaterial'] = ucfirst('pakaimaterial');

}



$fReport = new formReport('pakaimaterial', 'kebun_slave_2pakaimaterial', $_SESSION['lang']['lapmaterial']);

$fReport->addPrime('kodeorg', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 20, $optOrg);

$fReport->addPrime('periode', $_SESSION['lang']['periode'], '', 'bulantahun', 'L', 25);

$fReport->addPrime('kegiatan', $_SESSION['lang']['kegiatan'], '', 'select', 'L', 20, $optKegiatan);

$fReport->addPrime('barang', $_SESSION['lang']['kodebarang'], '', 'select', 'L', 20, $optBarang);

echo open_body();

echo "<script language=\"JavaScript1.2\" src=\"js/formReport.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/biReport.js\"></script>\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"style/zTable.css\">\r\n";

include 'master_mainMenu.php';

OPEN_BOX();

$fReport->render();

CLOSE_BOX();

echo close_body();



?>