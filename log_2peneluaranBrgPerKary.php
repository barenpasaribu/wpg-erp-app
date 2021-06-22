<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
echo "\r\n" . '<script language=javascript src=\'js/log_2peneluaranBrgPerKary.js\'></script>' . "\r\n" . '<script language=javascript src=\'js/log_transaksi_pengeluaran.js\'></script>' . "\r\n" . '<script language=javascript src=js/zTools.js></script>' . "\r\n" . '<script language=javascript src=\'js/zReport.js\'></script>' . "\r\n" . '<link rel=stylesheet type=text/css href=style/zTable.css>' . "\r\n\r\n\r\n\r\n";
$optorg = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = getQuery("lokasitugas");// 'SELECT kodeorganisasi,namaorganisasi FROM ' . $dbname . '.organisasi where length(kodeorganisasi)=\'4\' ORDER BY kodeorganisasi';

//exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optorg .= '<option value=' . $data['kodeorganisasi'] . '>' . $data['namaorganisasi'] . '</option>';
}

$optbulan = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$sql = 'SELECT distinct(substr(tanggal,1,7)) as tanggal FROM ' . $dbname . '.log_poht group by tanggal';

//exit('SQL ERR : ' . mysql_error());
($qry = mysql_query($sql)) || true;

while ($data = mysql_fetch_assoc($qry)) {
	$optbulan .= '<option value=' . $data['tanggal'] . '>' . $data['tanggal'] . '</option>';
}

$optKar = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

//$sql =  "SELECT karyawanid,namakaryawan FROM $dbname.datakaryawan ".
//"where lokasitugas
//
//
////exit('SQL ERR : ' . mysql_error());
//($qry = mysql_query($sql)) || true;
//
//while ($data = mysql_fetch_assoc($qry)) {
//	$optorg .= '<option value=' . $data['kodeorganisasi'] . '>' . $data['namaorganisasi'] . '</option>';
//}

include 'master_mainMenu.php';
OPEN_BOX();
$arr = '##kdorg##karyawanid##tgl1##tgl2';
echo '<fieldset style=\'float:left;\'><legend><b>' . $_SESSION['lang']['pengeluaranbarang'] . ' ' . $_SESSION['lang']['karyawan'] . '</b></legend>' . "\r\n" . '<table>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['kodeorg'] . '</td>' . "\r\n\t\t" . '<td>:</td>' . "\r\n\t\t" . '<td><select id=kdorg onchange=getKar() style=\'width:200px;\'>' . $optorg . '</select></td>' . "\r\n\t" . '</tr>' . "\r\n\t\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['namakaryawan'] . '</td>' . "\r\n\t\t" . '<td>:</td>' . "\r\n\t\t" . '<td>'.
'<select id=karyawanid style=\'width:200px;\'>' . $optKar . '</select></td>' . "\r\n\t" . '</tr>' . "\r\n\t\r\n\t" . ' ' . "\r\n\t\r\n\t\r\n\t" . '<tr>' . "\r\n\t\t" . '<td>' . $_SESSION['lang']['tanggal'] . '</td>' . "\r\n\t\t" . '<td>:</td>' . "\r\n\t\t" . '<td><input type=\'text\' class=\'myinputtext\' id=\'tgl1\' onmousemove=\'setCalendar(this.id)\' onkeypress=\'return false;\'  size=\'7\' maxlength=\'10\' >' . "\r\n\t\t" . 's/d' . "\r\n\t\t" . '<input type=\'text\' class=\'myinputtext\' id=\'tgl2\' onmousemove=\'setCalendar(this.id)\' onkeypress=\'return false;\'  size=\'7\' maxlength=\'10\' ></td>' . "\r\n\t" . '</tr>' . "\t\r\n\t\r\n\t" . '<tr>' . "\r\n\t\t" . '<td colspan=100>&nbsp;</td>' . "\r\n\t" . '</tr>' . "\r\n\t" . '<tr>' . "\r\n\t\t" . '<td colspan=100>' . "\r\n\t\t" . '<button onclick=zPreview(\'log_slave_2peneluaranBrgPerKary\',\'' . $arr . '\',\'printContainer\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['preview'] . '</button>' . "\r\n\t\t" . '<button onclick=zExcel(event,\'log_slave_2peneluaranBrgPerKary.php\',\'' . $arr . '\') class=mybutton name=preview id=preview>' . $_SESSION['lang']['excel'] . '</button>' . "\r\n\t\t\r\n\t\t" . '<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>' . $_SESSION['lang']['cancel'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t" . '</tr>' . "\r\n" . '</table>' . "\r\n" . '</fieldset>';
echo "\r\n" . '<fieldset style=\'clear:both\'><legend><b>' . $_SESSION['lang']['printArea'] . '</b></legend>' . "\r\n" . '<div id=\'printContainer\'  >' . "\r\n" . '</div></fieldset>';
CLOSE_BOX();
echo close_body();

?>
