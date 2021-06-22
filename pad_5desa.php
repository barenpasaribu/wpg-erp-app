<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n" . '<script language=javascript1.2 src=\'js/pad_desa.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', $_SESSION['lang']['desa']);
$str = 'select kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi where tipe in (\'KEBUN\',\'PABRIK\') order by namaorganisasi desc';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optpad .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

echo '<fieldset style=\'width:500px;\'><table>' . "\r\n" . '    <tr><td>' . $_SESSION['lang']['kebun'] . '</td><td>' . "\r\n" . '             <select id=\'unit\'>' . $optpad . '</select></td></tr>' . "\r\n" . '     <tr><td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td><td>' . "\r\n" . '             <input type=text id=desa size=30 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\r\n" . '      <tr><td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td><td>' . "\r\n" . '             <input type=text id=kecamatan size=30 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>' . "\r\n" . '      <tr><td>' . $_SESSION['lang']['kabupaten'] . '</td><td>' . "\r\n" . '             <input type=text id=kabupaten size=30 onkeypress="return tanpa_kutip(event);" class=myinputtext></td></tr>             ' . "\r\n" . '     </table>' . "\r\n" . '         <input type=hidden id=method value=\'insert\'>' . "\r\n" . '         <button class=mybutton onclick=simpanJabatan()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n" . '         <button class=mybutton onclick=cancelJabatan()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '         </fieldset>';
echo open_theme($_SESSION['lang']['list']);
echo '<img onclick=desaexcel(event,\'pad_slave_save_desa.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'>';
echo $_SESSION['lang']['kebun'] . ': <select id=\'unitbawah\' onchange=gantikebun()><option value=\'\'>' . $_SESSION['lang']['all'] . '</option>' . $optpad . '</select>';
echo '<div id=container>';
$str1 = 'select * from ' . $dbname . '.pad_5desa order by namadesa';
$res1 = mysql_query($str1);
echo '<table class=sortable cellspacing=1 border=0 style=\'width:500px;\'>' . "\r\n" . '         <thead>' . "\r\n" . '                <tr class=rowheader>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n" . '                <td style=\'width:150px;\'>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['desa'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['nama'] . ' ' . $_SESSION['lang']['kecamatan'] . '</td>' . "\r\n" . '                <td>' . $_SESSION['lang']['kabupaten'] . '</td>    ' . "\r\n" . '               <td style=\'width:30px;\'>*</td></tr>' . "\r\n" . '                </thead>' . "\r\n" . '                <tbody>';

while ($bar1 = mysql_fetch_object($res1)) {
	echo '<tr class=rowcontent>' . "\r\n" . '                          <td align=center>' . $bar1->unit . '</td>' . "\r\n" . '                           <td>' . $bar1->namadesa . '</td>' . "\r\n" . '                           <td>' . $bar1->kecamatan . '</td>' . "\r\n" . '                           <td>' . $bar1->kabupaten . '</td>    ' . "\r\n" . '                           <td><img src=images/application/application_edit.png class=resicon  caption=\'Edit\' onclick="fillField(\'' . $bar1->unit . '\',\'' . $bar1->namadesa . '\',\'' . $bar1->kecamatan . '\',\'' . $bar1->kabupaten . '\');">' . "\r\n" . '                            </td></tr>';
}

echo "\t" . ' ' . "\r\n" . '                 </tbody>' . "\r\n" . '                 <tfoot>' . "\r\n" . '                 </tfoot>' . "\r\n" . '                 </table>';
echo '</div>';
echo close_theme();
CLOSE_BOX();
echo close_body();

?>
