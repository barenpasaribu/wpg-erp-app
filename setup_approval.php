<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/approval.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['input'] . ' ' . $_SESSION['lang']['persetujuan']);
$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where length(kodeorganisasi)=4 order by namaorganisasi desc';
$res = mysql_query($str);
while ($bar = mysql_fetch_assoc($res)) {
	$optOrg .= '<option value=\'' . $bar['kodeorganisasi'] . '\'>' . $bar['namaorganisasi'] . '</option>';
}

//$str2 = "select karyawanid,namakaryawan, lokasitugas from $dbname.datakaryawan " ."where tipekaryawan=1 and  (IFNULL(tanggalkeluar,0)=0) and lokasitugas like '".$_SESSION['empl']['induklokasitugas']."%' and karyawanid in (select karyawanid from user where status='1' ) order by namakaryawan";
$str2 = "select karyawanid,namakaryawan, lokasitugas from $dbname.datakaryawan " ."where (IFNULL(tanggalkeluar,0)=0) and lokasitugas like '".$_SESSION['empl']['induklokasitugas']."%' and karyawanid in (select karyawanid from user where status='1' ) order by namakaryawan";
$res2 = mysql_query($str2);
$optkar='';
while ($bar2 = mysql_fetch_assoc($res2)) {
	$optkar .= "<option value='" . $bar2['karyawanid'] . "'>" . $bar2['namakaryawan'] . " - " . $bar2['lokasitugas'] . "</option>";
}
echo '<fieldset style=\'width:500px;\'><table>' . "\r\n" . '     <tr><td>' . $_SESSION['lang']['kodeorg'] . '</td><td>' . "\r\n" . '     <select id=kodeorg>' . $optOrg . '</select>    ' . "\r\n" . '     </td></tr>' . "\r\n\t" . ' <tr><td>Kode.App</td><td><input type=text id=app size=45 maxlength=45 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\r\n" . '        <tr><td>Approval</td><td><select id=karyawanid>' . $optkar . '</select></td></tr> ' . "\r\n" . '     </table>' . "\r\n\t" . ' <input type=hidden id=method value=\'insert\'>' . "\r\n\t" . ' <button class=mybutton onclick=simpanDep()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelDep()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t" . ' </fieldset>';
echo open_theme($_SESSION['lang']['list']);
$str1 = "select a.*,b.namakaryawan from $dbname.setup_approval a ".
	"left join $dbname.datakaryawan b on a.karyawanid=b.karyawanid  order by kodeunit";
$res1 = mysql_query($str1);
echo '<table class=sortable cellspacing=1 border=0 style=\'width:500px;\'>' . "\r\n\t" . '     <thead>' . "\r\n\t\t" . ' <tr class=rowheader><td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td><td>APP</td><td>' . $_SESSION['lang']['persetujuan'] . '</td><td style=\'width:30px;\'>*</td></tr>' . "\r\n\t\t" . ' </thead>' . "\r\n\t\t" . ' <tbody id=container>';

while ($bar1 = mysql_fetch_object($res1)) {
	echo '<tr class=rowcontent><td align=center>' . $bar1->kodeunit . '</td><td>' . $bar1->applikasi . '</td><td>' . $bar1->namakaryawan . '</td>' . "\r\n" . '                    <td>' . "\r\n" . '                   <img src=images/skyblue/delete.png class=resicon  caption=\'Edit\' onclick="dellField(\'' . $bar1->kodeunit . '\',\'' . $bar1->applikasi . '\',\'' . $bar1->karyawanid . '\');">     ' . "\r\n" . '                   </td></tr>';
}

echo "\t" . ' ' . "\r\n\t\t" . ' </tbody>' . "\r\n\t\t" . ' <tfoot>' . "\r\n\t\t" . ' </tfoot>' . "\r\n\t\t" . ' </table>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
