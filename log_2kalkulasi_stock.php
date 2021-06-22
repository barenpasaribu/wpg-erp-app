<?php


require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/devLibrary.php';
echo open_body();
echo '<script language=javascript1.2 src=\'js/log_2kalkulasi_stock.js\'></script>' . "\r\n";
include 'master_mainMenu.php';
// OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['mutasi']) . '</b>');
OPEN_BOX('', '<b>MUTASI STOK</b>');
$str = getQuery("gudang");//'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '      where tipe like \'GUDANG%\'' . "\r\n" . '      order by kodeorganisasi';
$res = mysql_query($str);
//$optunit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
//$optunit .= '<option value=\'sumatera\'>Sumatera (MRKE, SKSE, SOGM, SSRO, WKNE, SOGE, SENE)</option>';
//$optunit .= '<option value=\'kalimantan\'>Kalimantan (SBME, SBNE, SMLE, SMTE, SSGE, STLE)</option>';
//
//while ($bar = mysql_fetch_object($res)) {
//	$optunit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
//}
$optunit =makeOption2(getQuery("gudang"),
	array("valueinit"=>'',"captioninit"=> $_SESSION['lang']['all']." "),
	array("valuefield"=>'kodeorganisasi',"captionfield"=> 'namaorganisasi' )
);

$str = 'select distinct SUBSTR(tanggal, 1, 4) as tahun from ' . $dbname . '.log_transaksiht' . "\r\n" . '      order by tahun';
$res = mysql_query($str);
$opttahun = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahun . '\'>' . $bar->tahun . '</option>';
}

if ($_SESSION['language'] == 'EN') {
	$str = 'select kode, kelompok1 as kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '      order by kode';
}
else {
	$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '      order by kode';
}

$res = mysql_query($str);
$optkelompok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optkelompok .= '<option value=\'' . $bar->kode . '\'>' . $bar->kode . ' - ' . $bar->kelompok . '</option>';
}

$optpilih .= '<option value=\'volume\'>' . $_SESSION['lang']['volume'] . '</option>';
$optpilih .= '<option value=\'nilai\'>' . $_SESSION['lang']['harga'] . '</option>';
echo '<fieldset>' . "\r\n" . '     <legend>' . $_SESSION['lang']['mutasi'] . '</legend>' . "\r\n" . '     <table cellspacing=1 border=0>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>' . $_SESSION['lang']['pilihgudang'] . '</td>' . "\r\n" . '        <td><select id=unit style=\'width:150px;\'>' . $optunit . '</select></td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '        <td><select id=tahun style=\'width:150px;\'>' . $opttahun . '</select></td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n" . '        <td><select id=kelompok onchange=clearkobar() style=\'width:150px;\'>' . $optkelompok . '</select></td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>' . $_SESSION['lang']['kodebarang'] . '</td>' . "\r\n" . '        <td>' . "\r\n" . '            <input type="text" class="myinputtextnumber" id="kodebarang" name="kodebarang" onkeypress="return angka_doang(event);" value="" maxlength="10" style="width:150px;" disabled=true/>' . "\r\n" . '            <img src=images/search.png class=dellicon title=' . $_SESSION['lang']['find'] . ' onclick="searchBrg(\'' . $_SESSION['lang']['findBrg'] . '\',\'<fieldset><legend>' . $_SESSION['lang']['findnoBrg'] . '</legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div><input type=hidden id=nomor name=nomor value=' . $key . '>\',event)";>' . "\r\n" . '        </td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>Display</td>' . "\r\n" . '        <td><select id=pilih style=\'width:150px;\'>' . $optpilih . '</select></td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td>Per Mayor</td>' . "\r\n" . '        <td><input type="checkbox" name=mayor id=mayor value="Major" onclick=pilihMayor()></td>' . "\r\n" . '     </tr>' . "\r\n" . '     <tr>' . "\r\n" . '        <td colspan=2><button class=mybutton onclick=getTransaksiGudang()>' . $_SESSION['lang']['proses'] . '</button></td>' . "\r\n" . '     </tr></table>' . "\r\n" . '    </fieldset>';
CLOSE_BOX();
OPEN_BOX('', 'Result:');
echo '<span id=printPanel style=\'display:none;\'>' . "\r\n" . '        <img onclick=rekalkulasiStockKeExcel(event,\'log_slave_2kalkulasi_stock.php\') src=images/excel.jpg class=resicon title=\'MS.Excel\'> ' . "\r\n" . '     </span>    ' . "\r\n" . '     <div style=\'width:100%;height:50%;overflow:scroll;\'>' . "\r\n" . '        <table class=sortable cellspacing=1 border=0 width=100% id=container2></table>' . "\r\n" . '     </div>';
CLOSE_BOX();
close_body();

?>
