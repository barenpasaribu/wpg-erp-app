<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/kelompok_barang.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX();
$str = 'select distinct kelompokbiaya from ' . $dbname . '.keu_5komponenbiaya order by kelompokbiaya';
$res = mysql_query($str);
$opt = '<option value=\'\'></option>';

while ($bar = mysql_fetch_object($res)) {
	if ($bar->kelompokbiaya == '') {
	}
	else {
		$opt .= '<option value=\'' . $bar->kelompokbiaya . '\'>' . $bar->kelompokbiaya . '</option>';
	}
}

echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['kelompokbarang'] . '</legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['materialgroupcode'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=kelnumber size=3 maxlength=3 onkeypress="return angka_doang(event);"></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['namakelompok'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=kelname size=60 maxlength=60 onkeypress="return tanpa_kutip(event);"></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['namakelompok'] . '(EN)</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=kelname1 size=60 maxlength=60 onkeypress="return tanpa_kutip(event);"></td>' . "\r\n\t" . ' </tr>' . "\r\n" . '                        <!--tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kelompokbiaya'] . '</td>' . "\r\n\t" . '   <td><select id=kelompokbiaya>' . $opt . '</select></td>' . "\r\n\t" . ' </tr-->' . "\t\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['noakun'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=noakun size=15 maxlength=15 onkeypress="return angka_doang(event);"></td>' . "\r\n\t" . ' </tr>' . "\t" . '  ' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden value=insert id=method>' . "\r\n\t" . ' <button class=mybutton onclick=saveKelompokBarang()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelKelompokBarang()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset>';
CLOSE_BOX();
OPEN_BOX();
$str = 'select a.*,b.namaakun from ' . $dbname . '.log_5klbarang a 
left join keu_5akun b on a.noakun = b.noakun
order by a.kode asc';
$res = mysql_query($str);
echo '<table class=sortable cellspacing=1 border=0><thead>
		<tr class=rowheader>
			<td>No</td>
			<td>' . $_SESSION['lang']['materialgroupcode'] . '</td>
			<td>' . $_SESSION['lang']['namakelompok'] . '</td>
			<td>' . $_SESSION['lang']['namakelompok'] . '(EN)</td>
			<!--td>' . $_SESSION['lang']['kelompokbiaya'] . '</td-->
			<td>' . $_SESSION['lang']['noakun'] . '</td>
			<td>' . $_SESSION['lang']['namaakun'] . '</td>
			<td>Aksi</td>
		</tr>
		</thead><tbody id=container>';
$no = 0;

while ($bar = mysql_fetch_object($res)) {
	$no += 1;
	echo '<tr class=rowcontent>
		<td>' . $no . '</td>
		<td>' . $bar->kode . '</td>
		<td>' . $bar->kelompok . '</td>
		<td>' . $bar->kelompok1 . '</td>
		<!--td>' . $bar->kelompokbiaya . '</td-->
		<td>' . $bar->noakun . '</td>
		<td>' . $bar->namaakun . '</td>
		<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\',\'' . $bar->kelompok1 . '\',\'' . $bar->noakun . '\');"><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delKelompok(\'' . $bar->kode . '\',\'' . $bar->kelompok . '\');"></td>
	</tr>';
}

echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table>';
CLOSE_BOX();
echo close_body();

?>
