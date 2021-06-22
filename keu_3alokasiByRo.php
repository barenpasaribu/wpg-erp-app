<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
echo open_body();
echo "\r\n<script language=javascript1.2 src='js/alokasiByRo.js'></script>\r\n";
include 'master_mainMenu.php';
OPEN_BOX('', 'REGIONAL COST ALLOCATIONS (to working unit)');
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
$res = mysql_query($str);
$optOrg = '';
while ($bar = mysql_fetch_object($res)) {
    $optOrg .= "<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi.'</option>';
}
echo '<fieldset><legend>'.$_SESSION['lang']['sumber']."</legend><table>\r\n       <tr><td>".$_SESSION['lang']['kodeorg']."</td>\r\n              <td><select id=kodeorg>".$optOrg."</select></td></tr>\r\n        <tr><td>".$_SESSION['lang']['periode']."</td><td><input type=text size=12 id=periode disabled  class=myinputtext value='".$_SESSION['org']['period']['tahun'].'-'.$_SESSION['org']['period']['bulan']."'></td></tr>\r\n</table></fieldset>";
echo '<table><tr><td><fieldset><legend>'.$_SESSION['lang']['tujuan'].'</legend><table>';
$str = 'select kodeorganisasi,namaorganisasi from '.$dbname.".organisasi where tipe='PT' order by namaorganisasi desc";
$res = mysql_query($str);
$no = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    echo '<tr><td><select id=pt'.$no."><option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option></select></td>\r\n                <td>".$_SESSION['lang']['logouang'].'.<input type=text class=myinputtextnumber id=jumlah'.$no.' onkeypress="return angka_doang(event)" maxlength=15 size=15 onblur=hitungTotal('.mysql_num_rows($res).')><button onclick=alokasiKan('.$no.') class=mybutton id=button'.$no.'>'.$_SESSION['lang']['proses'].'</button></td></tr>';
}
echo '<tr><td>'.$_SESSION['lang']['total'].'</td><td>'.$_SESSION['lang']['logouang'].'.<input type=text class=myinputtextnumber size=15 maxlength=15 id=total></td></tr>';
if ('EN' === $_SESSION['language']) {
    echo "</table></fieldset>\r\n         </td><td>\r\n         <fieldset style='width:250px;'><legend>Info:</legend>\r\n           The allocation process will only apply to estate units in the destination company, \r\n           divided by the area of the estate in the company, and in the one unit will be divided based on the extent of TBM and TM (if any).\r\n        </fieldset>\r\n         </td></tr></table>";
} else {
    echo "</table></fieldset>\r\n         </td><td>\r\n         <fieldset style='width:250px;'><legend>Info:</legend>\r\n         Proses alokasi ini hanya akan berlaku untuk unit kebun dalam PT tujuan, dibagi berdasarkan luasan areal per unit kebun dalam satu PT, dan di dalam satu unit akan dibagi ber\r\n         dasarkan luasan TBM dan TM (jika ada).\r\n        </fieldset>\r\n         </td></tr></table>";
}

CLOSE_BOX();
close_body();

?>