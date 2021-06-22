<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX();
$str = "select kodeorganisasi,namaorganisasi from organisasi where tipe='PT' and length(kodeorganisasi)<=3";
$res = mysql_query($str);
$opt .= '';

while ($bar = mysql_fetch_object($res)) {
	$opt .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

echo '<script language=javascript src=js/org_npwp.js></script>' . "\r\n" . '<fieldset style=\'width:450px\'>' . "\r\n\t" . '<legend><b>';
echo $_SESSION['lang'][setupnpwporg];
echo '</b></legend>' . "\r\n\t" . '<table>' . "\r\n\t" . '<tr>' . "\t\r\n\t" . '<td>';
echo $_SESSION['lang']['namaorganisasi'];
echo '</td><td><select id=org>';
echo '.' . $opt . '.';
echo '</select></td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\t\r\n\t" . '<td>';
echo $_SESSION['lang']['npwp'];
echo '</td><td><input type=text class=myinputtext id=npwp onkeypress="return tanpa_kutip(event)" size=25 maxlength=30></td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\t\t\r\n\t" . '<td>';
echo $_SESSION['lang']['alamatnpwp'];
echo '</td><td><input type=text class=myinputtext id=alamatnpwp onkeypress="return tanpa_kutip(event)" size=45 maxlength=100></td>' . "\r\n\t" . '</tr>' . "\t\r\n\t" . '<tr>' . "\t\t\r\n\t" . '<td>';
echo $_SESSION['lang']['domisili'];
echo '</td><td><input type=text class=myinputtext id=alamatdomisili onkeypress="return tanpa_kutip(event)" size=45 maxlength=100></td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '</table>' . "\r\n\t" . '<button class=mybutton onclick=savenpwp()>';
echo $_SESSION['lang']['save'];
echo '</button>' . "\r\n\t" . '<button class=mybutton onclick=cancelnpwp()>';
echo $_SESSION['lang']['cancel'];
echo '</button>' . "\r\n" . '</fieldset>' . "\t\r\n";
echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['namaorganisasi'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['npwp'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['alamatnpwp'] . '</td>' . "\r\n\t" . '  <td>' . $_SESSION['lang']['domisili'] . '</td>' . "\r\n\t" . '  <td></td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe=\'PT\' and length(kodeorganisasi)<=3 order by namaorganisasi desc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$alamatnpwp = '';
	$npwp = '';
	$alamatdom = '';
	$str1 = 'select * from ' . $dbname . '.setup_org_npwp where kodeorg=\'' . $bar->kodeorganisasi . '\' order by kodeorg';
	$res1 = mysql_query($str1);

	while ($bar1 = mysql_fetch_object($res1)) {
		$alamatnpwp = $bar1->alamatnpwp;
		$npwp = $bar1->npwp;
		$alamatdom = $bar1->alamatdomisili;
	}

	echo '<tr class=rowcontent>' . "\r\n\t" . '  <td>' . $bar->kodeorganisasi . '</td>' . "\r\n\t" . '  <td>' . $bar->namaorganisasi . '</td>' . "\r\n\t" . '  <td>' . $npwp . '</td>' . "\r\n\t" . '  <td>' . $alamatnpwp . '</td>' . "\r\n\t" . '  <td>' . $alamatdom . '</td>' . "\r\n\t" . '  <td>' . "\r\n\t\t" . '  <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delnpwp(\'' . $bar->kodeorganisasi . '\');">' . "\r\n\t" . '  </td>' . "\r\n\t" . '  </tr>';
}

echo '</tbody>' . "\r\n\t" . ' <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n" . '     </table>';
CLOSE_BOX();

?>
