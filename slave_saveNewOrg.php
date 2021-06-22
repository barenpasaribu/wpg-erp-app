<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$parent = strtoupper(trim($_POST['parent']));
$orgcode = strtoupper(trim($_POST['orgcode']));
$orgname = strtoupper(trim($_POST['orgname']));
$orgtype = strtoupper(trim($_POST['orgtype']));
$orgadd = trim($_POST['orgadd']);
$orgcity = strtoupper(trim($_POST['orgcity']));
$orgcountry = strtoupper(trim($_POST['orgcountry']));
$orgzip = strtoupper(trim($_POST['orgzip']));
$orgtelp = strtoupper(trim($_POST['orgtelp']));
$orgdetail = $_POST['orgdetail'];
$alokasi = strtoupper(trim($_POST['alokasi']));
$noakun = strtoupper(trim($_POST['noakun']));
$jum = 0;
$exist = false;
$s1 = 'select count(*) from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $orgcode . '\' and induk=\'' . $parent . '\'';
$re1 = mysql_query($s1);

while ($row = mysql_fetch_array($re1)) {
	$jum = $row[0];
}

if (0 < $jum) {
	$exist = true;
}

if (!$exist) {
	$st2 = 'insert into ' . $dbname . '.organisasi' . "\r\n\t\t" . '      (kodeorganisasi,namaorganisasi,alamat,telepon,wilayahkota,kodepos,induk,negara,tipe,lastuser,detail,alokasi,noakun)' . "\r\n\t\t" . 'values(\'' . $orgcode . '\',\'' . $orgname . '\',\'' . $orgadd . '\',\'' . $orgtelp . '\',\'' . $orgcity . '\',\'' . $orgzip . '\',\'' . $parent . '\',\'' . $orgcountry . '\',\'' . $orgtype . '\',\'' . $_SESSION['standard']['username'] . '\',' . $orgdetail . ',\'' . $alokasi . '\',\'' . $noakun . '\')';
}
else {
	$st2 = 'update ' . $dbname . '.organisasi' . "\r\n\t" . '        set' . "\t" . 'namaorganisasi=\'' . $orgname . '\',' . "\r\n\t\t\t\t" . 'alamat' . "\t" . '=\'' . $orgadd . '\',' . "\r\n\t\t\t\t" . 'telepon' . "\t" . '=\'' . $orgtelp . '\',' . "\r\n\t\t\t\t" . 'wilayahkota' . "\t" . '=\'' . $orgcity . '\',' . "\r\n\t\t\t\t" . 'kodepos' . "\t" . '=\'' . $orgzip . '\',' . "\r\n\t\t\t\t" . 'negara' . "\t" . '=\'' . $orgcountry . '\',' . "\r\n\t\t\t\t" . 'tipe' . "\t" . '=\'' . $orgtype . '\',' . "\r\n\t\t\t\t" . 'detail  =' . $orgdetail . ',' . "\r\n\t\t\t\t" . 'alokasi =\'' . $alokasi . '\',' . "\r\n\t\t\t\t" . 'noakun  =\'' . $noakun . '\',' . "\r\n\t\t\t\t" . 'lastuser=\'' . $_SESSION['standard']['username'] . '\'' . "\r\n\t\t\t" . ' where kodeorganisasi' . "\t" . '=\'' . $orgcode . '\'' . "\r\n\t\t\t" . ' and induk =\'' . $parent . '\'';
}

mysql_query($st2);

if (mysql_affected_rows($conn) != -1) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
