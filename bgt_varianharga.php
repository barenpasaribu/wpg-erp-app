<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optKelompok = makeOption($dbname, 'log_5klbarang', 'kode,kelompok');
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$optSup = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$sSup = 'select distinct substr(kodebudget,3,3) as kelompokbarang from ' . $dbname . '.bgt_budget_detail where kodebudget like \'M%\' order by substr(kodebudget,3,3) asc';

#exit(mysql_error($conn));
($qSup = mysql_query($sSup)) || true;

while ($rSup = mysql_fetch_assoc($qSup)) {
	$optSup .= '<option value=' . $rSup['kelompokbarang'] . '>' . $rSup['kelompokbarang'] . '-' . $optKelompok[$rSup['kelompokbarang']] . '</option>';
}

$optLokal = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
$arrPo = array('Pusat', 'Lokal');

foreach ($arrPo as $brsLokal => $isiLokal) {
	$optLokal .= '<option value=' . $brsLokal . '>' . $isiLokal . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThnBUdget = 'select distinct tahunbudget from ' . $dbname . '.bgt_budget order by tahunbudget desc';

#exit(mysql_error());
($qThnBudget = mysql_query($sThnBUdget)) || true;

while ($rThnBudget = mysql_fetch_assoc($qThnBudget)) {
	$optThn .= '<option value=' . $rThnBudget['tahunbudget'] . '>' . $rThnBudget['tahunbudget'] . '</option>';
}

$arrPilMode = array($_SESSION['lang']['fisik'], $_SESSION['lang']['rp']);

foreach ($arrPilMode as $pilihan => $lstData) {
	$optPilMode .= '<option value=' . $pilihan . '>' . $lstData . '</option>';
}

$arr = '##thnBudget##regional##kdBudget';
$optreg = $optOrg;
$str = 'select regional, nama from ' . $dbname . '.bgt_regional ' . "\r\n" . '      order by regional';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optreg .= '<option value=\'' . $bar->regional . '\'>' . $bar->regional . ' - ' . $bar->nama . '</option>';
}

echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=js/zReport.js></script>' . "\r\n" . '<script language=javascript src=js/keu_2laporanAnggaranKebun.js></script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['bgtVarian'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id="thnBudget" name="thnBudget" style="width:150px">';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['regional'];
echo '</label></td><td><select id="regional" name="regional" style="width:150px" onchange="getKdorg()">';
echo $optreg;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['kodebudget'];
echo '</label></td><td><select id="kdBudget" name="kdBudget" style="width:150px">';
echo $optSup;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_varianharga\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,\'bgt_slave_varianharga.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n\r\n" . '<fieldset style=\'clear:both\'><legend><b>Print Area</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
