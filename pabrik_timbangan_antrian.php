<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zLib.php';
include 'lib/zFunction.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/pabrik_antrian.js\'></script>' . "\r\n";
$arr = '##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses##status##catatan';
include 'master_mainMenu.php';
OPEN_BOX();
$tanggal = date('d-m-Y');

echo '<fieldset style=width:250px>' . "\r\n" . '     <legend>Antrian Timbangan</legend>' . "\r\n\t" . ' <table>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '   <td><input type=text class=myinputtext id=tgl onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 style="width:150px;" value='.$tanggal.' disabled/></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>No Kendaraan</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=nokendaraan "  /></td>' . "\r\n\t" . ' </tr>' . "\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>Supir</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=supir "  /></td>' . "\r\n\t" . ' </tr>' . "\t\r\n\t" . ' <tr>' . "\r\n\t" . '   <td>No SPB</td>' . "\r\n\t" . '   <td><input type=text class=myinputtextnumber id=nospb  /></td>' . "\r\n\t" . ' </tr>' . "\t" . ' ' . "\r\n\t" . ' </table>' . "\r\n\t" . ' <input type=hidden value=insert id=proses> <input type=hidden value=insert id=noantrian>' . "\r\n\t" . ' <button class=mybutton onclick=simpanAntrian()>' . $_SESSION['lang']['save'] . '</button>' . "\r\n\t" . ' <button class=mybutton onclick=cancelIsi()>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n" . '     </fieldset><input type=\'hidden\' id=idFranco name=idFranco />';
CLOSE_BOX();
OPEN_BOX();
echo '<fieldset  style=width:650px><legend>' . $_SESSION['lang']['list'] . '</legend>';
echo '<table cellpadding=1 cellspacing=1 border=0><tr><td>' . $_SESSION['lang']['tanggal'] . ' : <input type=text class=myinputtext id=tglCri onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 value='.$tanggal.'  />';
echo '<button class=mybutton onclick=cariTransaksi()>' . $_SESSION['lang']['find'] . '</button></td></tr></table>';
echo "\r\n" . '    <table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n\t" . '   <td>No Antrian</td>' . "\r\n\t" . "\r\n\t" . '   <td>Tanggal</td>' . "\r\n\t" . '   <td>No Kendaraan</td>' . "\r\n" . '       <td>Supir</td>' . "\r\n\t" . '   <td>No SPB</td>'.'   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
