<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

echo " \r\n";

$optbatch = "<option value=''>".$_SESSION['lang']['all'].'</option>';

$optkodeorg = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$kodeorg = "select distinct kodeorganisasi,namaorganisasi \r\n    from ".$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN' \r\n    order by namaorganisasi asc";

$query = mysql_query($kodeorg) ;

while ($result = mysql_fetch_assoc($query)) {

    $optkodeorg .= "<option value='".$result['kodeorganisasi']."'>".$result['namaorganisasi'].'</option>';

}

$arr = '##kodeunit##kodebatch';

echo "<script language='javascript' src='js/zTools.js'></script>\r\n<script language='javascript' src='js/zReport.js'></script>\r\n<link rel='stylesheet' type='text/css' href='style/zTable.css'>\r\n<script language='javascript1.2' src='js/bibit_2kartu.js'></script>      \r\n";

echo "\r\n<div style=\"margin-bottom: 30px;\">\r\n    <fieldset style=\"float: left;\">\r\n    <legend><b>".$_SESSION['lang']['laporanStockBIbit']."</b></legend>\r\n    <table cellspacing=\"1\" border=\"0\" >\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['unit']."</label></td>\r\n        <td><select id=\"kodeunit\" name=\"kodeunit\" onchange=\"ambilbatch(this.value);\" style=\"width:150px\">".$optkodeorg."</select>\r\n        </td>\r\n    </tr>\r\n    <tr>\r\n        <td><label>".$_SESSION['lang']['batch']."</label></td>\r\n        <td><select id=\"kodebatch\" name=\"kodebatch\" style=\"width:150px\">".$optbatch."</select></td>\r\n    </tr>\r\n    <tr>\r\n        <td colspan=\"2\">\r\n        <button onclick=\"zPreview('bibit_slave_2kartu','".$arr."','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Preview</button>\r\n        <button onclick=\"zExcel(event,'bibit_slave_2kartu.php','".$arr."')\" class=\"mybutton\" name=\"preview\" id=\"preview\">Excel</button>\r\n        </td>\r\n    </tr>\r\n    </table>\r\n    </fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n    <div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n    </div>\r\n</fieldset>    \r\n    ";

CLOSE_BOX();

echo close_body();



?>