<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$optOrg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sOrg = 'select namaorganisasi,kodeorganisasi from ' . $dbname . '.organisasi where  tipe=\'WORKSHOP\' order by namaorganisasi asc';

#exit(mysql_error($conn));
($qOrg = mysql_query($sOrg)) || true;

while ($rOrg = mysql_fetch_assoc($qOrg)) {
	$optOrg .= '<option value=' . $rOrg['kodeorganisasi'] . '>' . $rOrg['namaorganisasi'] . '</option>';
}

$optThn = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sThn = 'select distinct  tahunbudget from ' . $dbname . '. bgt_budget order by tahunbudget desc';

#exit(mysql_error($conn));
($qThn = mysql_query($sThn)) || true;

while ($rThn = mysql_fetch_assoc($qThn)) {
	$optThn .= '<option value=\'' . $rThn['tahunbudget'] . '\'>' . $rThn['tahunbudget'] . '</option>';
}

$arr = '##thnBudget##kdWS';
echo '<script language=javascript src=\'js/zTools.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<script>' . "\r\n\r\n" . 'function summForm()' . "\r\n" . '{' . "\r\n\t" . '//closeDialog();' . "\r\n\t" . 'width=\'350\';' . "\r\n\t" . 'height=\'200\';' . "\r\n\t" . 'content="<div id=container style=\'overflow:auto;width:100%;height:190px;\'></div>";' . "\r\n\t" . 'ev=\'event\';' . "\r\n\t" . 'title="Detail Alokasi";' . "\r\n\t" . 'showDialog1(title,content,width,height,ev);' . "\r\n" . '}' . "\r\n" . '//function getAlokasi(kdWS,kdkend,thnbdget)' . "\r\n" . 'function summForm2()' . "\r\n" . '{' . "\r\n\t" . '//closeDialog();' . "\r\n\t" . 'width=\'650\';' . "\r\n\t" . 'height=\'350\';' . "\r\n\t" . 'content="<div id=container2 style=\'overflow:auto;width:100%;height:330px;\'></div>";' . "\r\n\t" . 'ev=\'event\';' . "\r\n\t" . 'title="Detail Alokasi";' . "\r\n\t" . 'showDialog2(title,content,width,height,ev);' . "\r\n" . '}' . "\r\n" . 'function printFile(param,tujuan,title,ev)' . "\r\n" . '{' . "\r\n" . '   tujuan=tujuan+"?"+param;  ' . "\r\n" . '   width=\'200\';' . "\r\n" . '   height=\'150\';' . "\r\n" . '   content="<iframe frameborder=0 width=100% height=100% src=\'"+tujuan+"\'></iframe>"' . "\r\n" . '   showDialog1(title,content,width,height,ev); ' . "\t\r\n" . '}' . "\r\n" . 'function Clear1()' . "\r\n" . '{' . "\r\n" . '    document.getElementById(\'thnBudget\').value=\'\';' . "\r\n" . '    document.getElementById(\'kdWS\').value=\'\';' . "\r\n" . '    document.getElementById(\'printContainer\').innerHTML=\'\';' . "\r\n" . '}' . "\r\n" . '</script>' . "\r\n\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n" . '<div>' . "\r\n" . '<fieldset style="float: left;">' . "\r\n" . '<legend><b>';
echo $_SESSION['lang']['biaya'] . $_SESSION['lang']['workshop'];
echo '</b></legend>' . "\r\n" . '<table cellspacing="1" border="0" >' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['budgetyear'];
echo '</label></td><td><select id=\'thnBudget\' style="width:150px;">';
echo $optThn;
echo '</select></td></tr>' . "\r\n" . '<tr><td><label>';
echo $_SESSION['lang']['workshop'];
echo '</label></td><td><select id=\'kdWS\'  style="width:150px;">';
echo $optOrg;
echo '</select></td></tr>' . "\r\n" . '<tr height="20"><td colspan="2">&nbsp;</td></tr>' . "\r\n" . '<tr><td colspan="2"><button onclick="zPreview(\'bgt_slave_laporan_biaya_bengkel\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['preview'];
echo '</button>' . "\r\n\t\t\t\t\t" . '<button onclick="zPdf(\'bgt_slave_laporan_biaya_bengkel\',\'';
echo $arr;
echo '\',\'printContainer\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['pdf'];
echo '</button>' . "\r\n" . '                    <button onclick="zExcel(event,\'bgt_slave_laporan_biaya_bengkel.php\',\'';
echo $arr;
echo '\')" class="mybutton" name="preview" id="preview">';
echo $_SESSION['lang']['excel'];
echo '</button>' . "\r\n" . '                    <button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal">';
echo $_SESSION['lang']['cancel'];
echo '</button></td></tr>' . "\r\n\r\n" . '</table>' . "\r\n" . '</fieldset>' . "\r\n" . '</div>' . "\r\n\r\n" . '<div style="margin-bottom: 30px;">' . "\r\n" . '</div>' . "\r\n" . '<fieldset style=\'clear:both\'><legend><b>';
echo $_SESSION['lang']['printArea'];
echo '</b></legend>' . "\r\n" . '<div id=\'printContainer\' style=\'overflow:auto;height:50%;max-width:100%;\'>' . "\r\n\r\n" . '</div></fieldset>' . "\r\n\r\n";
CLOSE_BOX();
echo close_body();

?>
