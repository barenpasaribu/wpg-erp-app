<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
require_once 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src="js/log_2transaksigudang.js"></script>' . "\r\n";
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['transaksigudang']) . '</b>');
OPEN_BOX('', '<b>PENERIMAAN-PENGELUARAN BARANG</b>');

//if ('HOLDING' == trim($_SESSION['empl']['tipelokasitugas'])) {
//	$str = " SELECT " .
//		"o.kodeorganisasi,o.namaorganisasi,o.induk " .
//		"FROM  organisasi o    " .
//		"WHERE o.induk = '".$_SESSION['org']['induk']."'";
//} else {
//	$str = "SELECT 1 as level,d.karyawanid, d.namakaryawan, " .
//		"o.kodeorganisasi,o.namaorganisasi,o.induk " .
//		"FROM datakaryawan d " .
//		"INNER JOIN user u on u.karyawanid=d.karyawanid " .
//		"INNER JOIN organisasi o ON d.lokasitugas=o.kodeorganisasi " .
//		"WHERE u.namauser= '" . $_SESSION['standard']['username'] . "'";
//}

$optB = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
if ($_SESSION['empl']['tipelokasitugas'] == 'HOLDING'){
$i = "select kodeorganisasi,namaorganisasi from ". $dbname . ".organisasi  where kodeorganisasi like '".$_SESSION['empl']['kodeorganisasi']."%' and tipe like 'GUDANG%'";
}else{
$i = "select kodeorganisasi,namaorganisasi from ". $dbname . ".organisasi  where kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%' and tipe like 'GUDANG%'";
}

#exit(mysql_error($conn));
($n = mysql_query($i)) || true;

while ($d = mysql_fetch_assoc($n)) {
	$optB .= '<option value=\'' . $d['kodeorganisasi'] . '\'>' . $d['namaorganisasi'] . '</option>';
}
/*
$optunit= makeOption2(getQuery("gudang"),
	array("valuefield"=>'',"captionfield"=>  $_SESSION['lang']['pilihdata'] ),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);
*/
$optper = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$x = 0;

while ($x < 13) {
	$dt = mktime(0, 0, 0, date('m') - $x, 15, date('Y'));
	$optper .= '<option value=' . date('Y-m', $dt) . '>' . date('m-Y', $dt) . '</option>';
	++$x;
}

if ($_SESSION['language'] == 'EN') {
	$optjenis = '<option value=\'\'></option>' . "\r\n" . '                <option value=\'0\'>Goods movement on the way</option>' . "\r\n" . '                <option value=\'1\'>Goods Receipt(GR)</option>' . "\r\n" . '                <option value=\'2\'>Return of GI</option>' . "\r\n" . '                <option value=\'3\'>Goods movement receipt</option>' . "\r\n" . '                <option value=\'5\'>Good Issue(GI)</option>' . "\r\n" . '                <option value=\'6\'>Return of GR</option>' . "\r\n" . '                <option value=\'7\'>Goods movement issue</option>' . "\r\n" . '                <option value=\'9\'>All</option>';
}
else {
	$optjenis = '<option value=\'\'></option>' . "\r\n" . '                <option value=\'0\'>Mutasi dalam perjalanan</option>' . "\r\n" . '                <option value=\'1\'>Penerimaan</option>' . "\r\n" . '                <option value=\'2\'>Pengembalian pengeluaran</option>' . "\r\n" . '                <option value=\'3\'>Penerimaan mutasi</option>' . "\r\n" . '                <option value=\'5\'>Pengeluaran</option>' . "\r\n" . '                <option value=\'6\'>Pengembalian penerimaan</option>' . "\r\n" . '                <option value=\'7\'>Pengeluaran mutasi</option>';
}

$optbarang = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';
echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['transaksigudang'] . '</legend>' . "\r\n\t" . ' <table cellspacing=1 border=0><tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['unit'] . '</td>' . "\r\n\t" . '   <td>' . "\r\n\t" . '     <select id=unit style=\'width:150px;\' onchange=ambilPeriode(this.options[this.selectedIndex].value)>' . $optB . '</select></td>' . "\r\n\t" . ' </tr><tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n\t" . '   <td><select id=periode onchange=hideById(\'printPanel\')>' . $optper . '</select></td>' . "\r\n\t" . ' </tr><tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['tipetransaksi'] . '</td>' . "\r\n\t" . '   <td><select id=jenis onchange=hideById(\'printPanel\')>' . $optjenis . '</select></td>' . "\r\n\t" . ' </tr><tr>' . "\r\n\t" . '   <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '       <td><input type=text size=10 maxlength=10 id=kodebarang class=myinputtext onkeypress="return false;" onclick="showWindowBarang(\'' . $_SESSION['lang']['find'] . ' ' . $_SESSION['lang']['namabarang'] . '\',event);">' . "\r\n\t" . '   <button class=mybutton onclick=setAll()>' . $_SESSION['lang']['all'] . '</button></td>' . "\r\n\t" . ' </tr><tr>' . "\r\n\t" . '   <td colspan=2><button class=mybutton onclick=getTransaksiGudang()>' . $_SESSION['lang']['proses'] . '</button></td>' . "\r\n\t" . ' </tr></table>' . "\r\n\t" . ' </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '     <img onclick=transaksiGudangKeExcel(event,\'log_slave_2transaksigudang_Excel.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n\t" . ' </span>    ' . "\r\n\t" . ' <div style=\'width:97%; height:300px;\'>' . "\r\n" . ' <table class=sortable cellspacing=1 border=0 id=container>' . "\r\n\t" . '   </table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
