<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script language=javascript src='js/keu_alokasiByLain.js'></script>\r\n";
$optPeriode = '';
for ($x = 0; $x <= 12; ++$x) {
    $dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
    $optPeriode .= "<option value='".date('Y-m', $dt)."'>".date('m-Y', $dt).'</option>';
}
$str = 'select * from '.$dbname.'.sdm_5tipekaryawan where id>0';
$res = mysql_query($str);
$opttipe = '';
while ($bar = mysql_fetch_object($res)) {
    $opttipe .= "<option value='".$bar->id."'>".$bar->tipe.'</option>';
}
OPEN_BOX('', $_SESSION['lang']['alokasitambahan']);
echo "<fieldset><table>\r\n      <tr><td>".$_SESSION['lang']['periode'].'</td><td>:<select id=periode>'.$optPeriode."</select></td></tr>\r\n      <tr><td>".$_SESSION['lang']['tipekaryawan'].'</td><td>:<select id=tipekaryawan>'.$opttipe."</select></td></tr>\r\n      <tr><td colspan=2>\r\n          <button class=mybutton onclick=displayForm()>".$_SESSION['lang']['tampilkan']."</button>\r\n          <button class=mybutton onclick=dataKeExcel()>Spreadsheet</button>\r\n      </td></tr>    \r\n      </table>\r\n      </fieldset>    \r\n\t  <div id=container style='height:400px; overflow:scroll'>\r\n\t  </div>";
CLOSE_BOX();
echo close_body();

?>