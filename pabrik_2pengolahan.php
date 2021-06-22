<?php



require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formReport.php';
$optOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "tipe='PABRIK'");
$optTahunTanam = makeOption($dbname, 'setup_blok', 'tahuntanam,tahuntanam', "left(kodeorg,4)='".$_SESSION['empl']['lokasitugas']."'", '0', true);
$optTahunTanam[''] = $_SESSION['lang']['all'];
$fReport = new formReport('Mill Processing', 'pabrik_slave_2pengolahan');
$fReport->addPrime('kodeorg', $_SESSION['lang']['kodeorg'], '', 'select', 'L', 20, $optOrg);
$fReport->addPrime('periode', $_SESSION['lang']['periode'], '', 'bulantahun', 'L', 25);
echo open_body();
echo "<script language=\"JavaScript1.2\" src=\"js/formReport.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/biReport.js\"></script>\r\n<script language=javascript1.2 src=\"js/pabrik_2pengolahan.js\"></script>\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"style/zTable.css\">\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$fReport->render();
CLOSE_BOX();
echo close_body();

?>