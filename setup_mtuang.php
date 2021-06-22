<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "\r\n" . '<script language=javascript1.2 src=\'js/setup_mtuang.js\'></script>' . "\r\n\r\n";
OPEN_BOX('', '<font size=3><u><b>Mata Uang</b></u><font>');
echo '<br /><br /><fieldset style=\'float:left;\'>' . "\r\n\t\t" . '<legend><font size=2.5><b>Header Mata Uang</b></legend></font>' . "\t\t\r\n\t\t\t" . '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\t\t\r\n\t\t\t\t\t" . '<td align=center>Kode Jurnal</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Mata Uang</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Simbol</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Kode ISO</td>' . "\r\n\t\t\t\t\t" . '<td align=center>*</td>' . "\r\n\t\t\t\t" . '</tr>';
$ha = 'select * from ' . $dbname . '.setup_matauang';

#exit(mysql_error($conn));
($hi = mysql_query($ha)) || true;

while ($hu = mysql_fetch_assoc($hi)) {
	$no += 1;
	echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td><input type=text maxlength=3 id=kode' . $hu['kode'] . ' value=' . $hu['kode'] . ' onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=matauang' . $hu['kode'] . ' value=' . $hu['matauang'] . ' onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=simbol' . $hu['kode'] . ' value=' . $hu['simbol'] . ' onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=kodeiso' . $hu['kode'] . ' value=' . $hu['kodeiso'] . ' onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td>' . "\r\n\t\t\t\t" . '<img src=images/application/application_edit.png class=resicon  title=\'Update\' onclick="edithead(\'' . $hu['kode'] . '\');" >' . "\r\n\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delhead(\'' . $hu['kode'] . '\',\'' . $hu['matauang'] . '\',\'' . $hu['simbol'] . '\',\'' . $hu['kodeiso'] . '\');" >' . "\r\n\t\t\t\t" . '<img src=images/application/application_go.png class=resicon  title=\'View\' onclick=loadData(\'' . $hu['kode'] . '\')>' . "\r\n\t\t\t\r\n\t\t\t" . '</td>' . "\r\n" . '     ' . "\t" . '</tr>';
}

echo '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td><input type=text maxlength=3 id=kodetambah onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=matauangtambah onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=simboltambah onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><input type=text  id=kodeisotambah onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:75px;"></td>' . "\r\n\t\t\t" . '<td><img src=images/application/application_add.png class=resicon  title=\'Save\'  onclick=simpanbaru()></td>' . "\r\n\t\t\t" . '</tr>';
echo '</table></fieldset>' . "\r\n\t\t\t\t\t" . '<input type=hidden id=method value=\'insert\'>';
echo '<fieldset style=\'float:left;\'>' . "\r\n\t\t" . '<legend><font size=2.5><b>Detail Mata Uang</b></legend></font>' . "\r\n\t\t" . '<input type=hidden id=kodedetail value=\'\'>' . "\r\n\t\t" . '<div id=container> ' . "\r\n\t\t\t\r\n\t\t" . '</div>' . "\r\n\t" . '</fieldset>';
CLOSE_BOX();
echo close_body();

?>
