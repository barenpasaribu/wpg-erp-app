<?php







require_once 'master_validation.php';

include_once 'lib/eagrolib.php';

include_once 'lib/zLib.php';

include_once 'lib/formReport.php';

$optkebun = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi', "left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' ",  '0', true);

$optkebun[''] = $_SESSION['lang']['all'];

$optTahunTanam[''] = $_SESSION['lang']['all'];

$optAfdeling = getOrgBelow($dbname, $_SESSION['empl']['lokasitugas'], false, 'afdeling');

$optAfdeling[''] = $_SESSION['lang']['all'];

$fReport = new formReport('arealstatement', 'kebun_slave_2arealstatmen', $_SESSION['lang']['arealstatement']);

$fReport->addPrime('periode', $_SESSION['lang']['periode'], '', 'bulantahun', '', 'L', 25);

$fReport->addPrime('unit', $_SESSION['lang']['unit'], '', 'select', 'L', 20, $optkebun);

$fReport->_primeEls[1]->_attr['onchange'] = "getAfdeling(this,'afdeling','kebun_slave_2arealstatement')";

$fReport->addPrime('afdeling', $_SESSION['lang']['afdeling'], '', 'select', 'L', 20, $optAfdeling);

$fReport->_primeEls[2]->_attr['onchange'] = "getThnTnm('kebun_slave_2arealstatement')";

$fReport->addPrime('tahuntanam', $_SESSION['lang']['tahuntanam'], '', 'select', 'L', 20, $optTahunTanam);

echo open_body();

echo "<script language=javascript src=\"js/kebun_2arealstatement.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/formReport.js\"></script>\r\n<script language=\"JavaScript1.2\" src=\"js/biReport.js\"></script>\r\n\r\n\r\n<link rel=\"stylesheet\" type=\"text/css\" href=\"style/zTable.css\">\r\n";

include 'master_mainMenu.php';

OPEN_BOX();

$fReport->render();

CLOSE_BOX();

echo close_body();



?>