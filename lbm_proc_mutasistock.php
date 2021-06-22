<?php


require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/rTable.php';
$arr = '##unit##tahun##kelompok##pilih##mayor##urut##asc##judul';
$_POST['judul'] == '' ? $judul = $_GET['judul'] : $judul = $_POST['judul'];
echo "\r\n";
$str = 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '    where tipe = \'GUDANG\'' . "\r\n" . '    order by kodeorganisasi';
$res = mysql_query($str);
$optunit = '<option value=\'\'>' . $_SESSION['lang']['pilihdata'] . '</option>';
$optunit .= '<option value=\'sumatera\'>Sumatera</option>';
$optunit .= '<option value=\'kalimantan\'>Kalimantan</option>';

while ($bar = mysql_fetch_object($res)) {
	$optunit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select distinct kodeorganisasi, namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '    where tipe = \'GUDANGTEMP\'' . "\r\n" . '    order by kodeorganisasi';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$optunit .= '<option value=\'' . $bar->kodeorganisasi . '\'>' . $bar->namaorganisasi . '</option>';
}

$str = 'select distinct SUBSTR(tanggal, 1, 4) as tahun from ' . $dbname . '.log_transaksiht' . "\r\n" . '    order by tahun';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$opttahun .= '<option value=\'' . $bar->tahun . '\'>' . $bar->tahun . '</option>';
}

$str = 'select kode, kelompok from ' . $dbname . '.log_5klbarang' . "\r\n" . '      order by kode';
$res = mysql_query($str);
$optkelompok = '<option value=\'\'>' . $_SESSION['lang']['all'] . '</option>';

while ($bar = mysql_fetch_object($res)) {
	$optkelompok .= '<option value=\'' . $bar->kode . '\'>' . $bar->kode . ' - ' . $bar->kelompok . '</option>';
}

$optpilih .= '<option value=\'volume\'>' . $_SESSION['lang']['volume'] . '</option>';
$optpilih .= '<option value=\'nilai\'>' . $_SESSION['lang']['nilai'] . '</option>';
$optmayor .= '<option value=\'\'></option>';
$optmayor .= '<option value=\'mayor\'>' . $_SESSION['lang']['yes'] . '</option>';
$opturut .= '<option value=\'kodebarang\'>Kode Barang</option>';
$opturut .= '<option value=\'awal\'>Saldo Awal</option>';
$opturut .= '<option value=\'masuk\'>Penerimaan</option>';
$opturut .= '<option value=\'keluar\'>Saldo Awal</option>';
$opturut .= '<option value=\'akhir\'>Saldo Akhir</option>';
$opturut .= '<option value=\'harga\'>Harga</option>';
$optasc .= '<option value=\'asc\'>Asc</option>';
$optasc .= '<option value=\'desc\'>Desc</option>';
echo '<table cellspacing=1 border=0>' . "\r\n" . '    <tr>' . "\r\n" . '    <td>' . $_SESSION['lang']['pilihgudang'] . '<input type=hidden id=judul name=judul value=\'' . $judul . '\'></td>' . "\r\n" . '    <td><select id=unit style=\'width:150px;\'>' . $optunit . '</select></td>' . "\r\n" . '    <td>Display</td>' . "\r\n" . '    <td><select id=pilih style=\'width:150px;\'>' . $optpilih . '</select></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td>' . $_SESSION['lang']['kelompokbarang'] . '</td>' . "\r\n" . '    <td><select id=kelompok style=\'width:150px;\'>' . $optkelompok . '</select></td>' . "\r\n" . '    <td>Per Mayor</td>' . "\r\n" . '    <td><select id=mayor style=\'width:150px;\' onchange=pilihmayor()>' . $optmayor . '</select></td>' . "\r\n" . '    </tr>' . "\r\n" . '    <tr>' . "\r\n" . '    <td>' . $_SESSION['lang']['periode'] . '</td>' . "\r\n" . '    <td><select id=tahun style=\'width:150px;\'>' . $opttahun . '</select></td>' . "\r\n" . '    <td>Urut Berdasarkan</td>' . "\r\n" . '    <td>' . "\r\n" . '        <select id=urut style=\'width:96px;\'>' . $opturut . '</select>' . "\r\n" . '        <select id=asc style=\'width:50px;\'>' . $optasc . '</select>' . "\r\n" . '    </td>' . "\r\n" . '    </tr>' . "\r\n" . '<!--    <tr>' . "\r\n" . '    <td>Per Mayor</td>' . "\r\n" . '    <td><input type="checkbox" name=mayor id=mayor value="Mayor" onclick=pilihMayor()></td>' . "\r\n" . '    </tr>' . "\r\n" . '-->    <tr>' . "\r\n" . '    <td colspan=2><button class=mybutton onclick="zPreview(\'lbm_slave_proc_mutasistock\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="preview" id="preview">' . $_SESSION['lang']['proses'] . '</button></td>' . "\r\n" . '    <td colspan=2><button class=mybutton onclick="zExcel(event,\'lbm_slave_proc_mutasistock.php\',\'' . $arr . '\',\'reportcontainer\')" class="mybutton" name="excel" id="excel">' . $_SESSION['lang']['excel'] . '</button></td>' . "\r\n" . '    </tr></table>';

?>
