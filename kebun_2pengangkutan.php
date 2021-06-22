<?php







require_once 'master_validation.php';

include 'lib/eagrolib.php';

include_once 'lib/zLib.php';

echo open_body();

include 'master_mainMenu.php';

OPEN_BOX();

$optPeriode = "<option value=''>".$_SESSION['lang']['pilihdata'].'</option>';

$sTgl = 'select distinct substr(tanggal,1,7) as periode from '.$dbname.'.kebun_spbht order by tanggal desc';

$qTgl = mysql_query($sTgl) ;

while ($rTgl = mysql_fetch_assoc($qTgl)) {

    $thn = explode('-', $rTgl['periode']);

    if ('12' === $thn[1]) {

        $optPeriode .= "<option value='".substr($rTgl['periode'], 0, 4)."'>".substr($rTgl['periode'], 0, 4).'</option>';

    }



    $optPeriode .= "<option value='".$rTgl['periode']."'>".substr($rTgl['periode'], 5, 2).'-'.substr($rTgl['periode'], 0, 4).'</option>';

}

$sPabrik = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='KEBUN'";

$qPabrik = mysql_query($sPabrik) ;

while ($rPabrik = mysql_fetch_assoc($qPabrik)) {

    $optPabrik .= '<option value='.$rPabrik['kodeorganisasi'].'>'.$rPabrik['namaorganisasi'].'</option>';

}

$sBrg = 'select namabarang,kodebarang from '.$dbname.".log_5masterbarang where kelompokbarang like '400%'";

$qBrg = mysql_query($sBrg) ;

while ($rBrg = mysql_fetch_assoc($qBrg)) {

    $optBrg .= '<option value='.$rBrg['kodebarang'].'>'.$rBrg['namabarang'].'</option>';

}

$arr = '##periode##idKebun';

echo "<script language=javascript src=js/zTools.js></script>\r\n<script language=javascript src=js/zReport.js></script>\r\n<!--<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>-->\r\n\r\n<script language=javascript>\r\n\tfunction batal()\r\n\t{\r\n\t\tdocument.getElementById('periode').value='';\t\r\n\t\tdocument.getElementById('idKebun').value='';\r\n\t\tdocument.getElementById('printContainer').innerHTML='';\r\n\t}\r\n</script>\r\n\r\n<link rel=stylesheet type=text/css href=style/zTable.css>\r\n<div style=\"margin-bottom: 30px;\">\r\n<fieldset style=\"float: left;\">\r\n<legend><b>";

echo $_SESSION['lang']['laporanPengangkutan'];

echo "</b></legend>\r\n<table cellspacing=\"1\" border=\"0\" >\r\n<tr><td><label>";

echo $_SESSION['lang']['periode'];

echo '</label></td><td><select id="periode" name="periode" style="width:150px">';

echo $optPeriode;

echo "</select></td></tr>\r\n<tr><td><label>";

echo $_SESSION['lang']['kebun'];

echo '</label></td><td><select id="idKebun" name="idKebun" style="width:150px">';

echo $optPabrik;

echo "</select></td></tr>\r\n\r\n<tr><td colspan=\"2\"><button onclick=\"zPreview('kebun_slave_2pengangkutan','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">";

echo $_SESSION['lang']['preview'];

echo "</button>\r\n\r\n<button onclick=\"zPdf('kebun_slave_2pengangkutan','";

echo $arr;

echo "','printContainer')\" class=\"mybutton\" name=\"preview\" id=\"preview\">";

echo $_SESSION['lang']['pdf'];

echo "</button>\r\n\r\n<button onclick=\"zExcel(event,'kebun_slave_2pengangkutan.php','";

echo $arr;

echo "')\" class=\"mybutton\" name=\"preview\" id=\"preview\">";

echo $_SESSION['lang']['excel'];

echo "</button>\r\n\r\n<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">";

echo $_SESSION['lang']['cancel'];

echo "</button></td></tr>\r\n</table>\r\n</fieldset>\r\n</div>\r\n<fieldset style='clear:both'><legend><b>Print Area</b></legend>\r\n<div id='printContainer' style='overflow:auto;height:50%;max-width:100%;'>\r\n\r\n</div></fieldset>\r\n";

CLOSE_BOX();

echo close_body();



?>