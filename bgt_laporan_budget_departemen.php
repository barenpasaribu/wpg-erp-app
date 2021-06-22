<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src="js/bgt_laporan_budget_departemen.js"></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$str = 'select distinct tahunbudget from ' . $dbname . '.bgt_dept' . "\r\n" . '                  order by tahunbudget desc';
$res = mysql_query($str);
$opttahun = '';

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahunbudget . '\'>' . $bar->tahunbudget . '</option>';
}

$str = 'select * from ' . $dbname . '.sdm_5departemen' . "\r\n" . '                  order by kode';
$res = mysql_query($str);
$optdepartemen = '';

while ($bar = mysql_fetch_object($res)) {
	$optdepartemen .= '<option value=\'' . $bar->kode . '\'>' . $bar->kode . ' - ' . $bar->nama . '</option>';
}

echo '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['budgetdepartemen'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td>';
echo $_SESSION['lang']['budgetyear'];
echo '</td><td><select id=\'tahun\' style=\'width:200px;\' onchange="hideById(\'printPanel\');">';
echo $opttahun;
echo '</select></td></tr>' . "\r\n" . '<tr><td>';
echo $_SESSION['lang']['departemen'];
echo '</td><td><select id=\'departemen\' style=\'width:200px;\' onchange="hideById(\'printPanel\');">';
echo $optdepartemen;
echo '</select></td></tr>' . "\r\n" . '<tr><td></td><td><button class=mybutton onclick=getBudget()>';
echo $_SESSION['lang']['proses'];
echo '</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n";
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=budgetKeExcel(event,\'bgt_slave_laporan_budget_departemen_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' <img onclick=budgetKePDF(event,\'bgt_slave_laporan_budget_departemen_pdf.php\') title=\'PDF\' class=resicon src=images/pdf.jpg>' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div id=container style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
