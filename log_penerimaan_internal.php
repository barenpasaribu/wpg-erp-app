<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
echo '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript1.2 src=\'js/log_penerimaan_internal.js\'></script>' . "\r\n";
$arrData = '##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method';
$sql = 'select namasupplier,supplierid from ' . $dbname . '.log_5supplier order by namasupplier asc';
$optSupplier = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

#exit(mysql_error());
($query = mysql_query($sql)) || true;

while ($res = mysql_fetch_assoc($query)) {
	$optSupplier .= '<option value=\'' . $res['supplierid'] . '\'>' . $res['namasupplier'] . '</option>';
}

$optKary = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sKary = 'select karyawanid,namakaryawan,lokasitugas from ' . $dbname . '.datakaryawan where tipekaryawan in (0,1,2) order by namakaryawan asc';

#exit(mysql_error());
($qKary = mysql_query($sKary)) || true;

while ($rKary = mysql_fetch_assoc($qKary)) {
	$optKary .= '<option value=\'' . $rKary['karyawanid'] . '\'>' . $rKary['namakaryawan'] . '-' . $rKary['lokasitugas'] . '</option>';
}

include 'master_mainMenu.php';
OPEN_BOX();
echo '<fieldset style=float:left><legend>' . $_SESSION['lang']['list'] . '</legend>';
echo '<fieldset style=float:left><legend>' . $_SESSION['lang']['searchdata'] . '</legend>';
echo '<table><tr><td>' . $_SESSION['lang']['suratjalan'] . '</td>' . "\r\n" . '     <td><input type=\'text\' id=\'txtsearch\' class=myinputtext onkeypress=\'return tanpa_kutip(event)\' style=\'width=150px;\' />';
echo '<td>' . $_SESSION['lang']['tanggal'] . '</td><td><input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 /></td>';
echo '</tr></table>';
echo '<button class=mybutton onclick=loadData()>' . $_SESSION['lang']['find'] . '</button>';
echo '</fieldset><div style=clear:both;></di>';
echo '<table class=sortable cellspacing=1 border=0>' . "\r\n" . '     <thead>' . "\r\n\t" . '  <tr class=rowheader>' . "\r\n\t" . '   <td>No</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['suratjalan'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['status'] . '</td>' . "\r\n" . '           <td>' . $_SESSION['lang']['expeditor'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tgl_kirim'] . '</td>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['keterangan'] . '</td>' . "\r\n" . '                       <td>' . $_SESSION['lang']['diterima'] . ' ' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t" . '   <td>Action</td>' . "\r\n\t" . '  </tr>' . "\r\n\t" . ' </thead>' . "\r\n\t" . ' <tbody id=container>';
echo '<script>loadData()</script>';
echo '</tbody>' . "\r\n" . '     <tfoot>' . "\r\n\t" . ' </tfoot>' . "\r\n\t" . ' </table></fieldset>';
CLOSE_BOX();
echo close_body();

?>
