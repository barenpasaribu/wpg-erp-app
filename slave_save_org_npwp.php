<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodeorg = $_POST['kodeorg'];
$switch = $_POST['switch'];
$npwp = $_POST['npwp'];
$alamatnpwp = $_POST['alamatnpwp'];
$alamatdom = $_POST['alamatdom'];

switch ($switch) {
case 'delete':
	$stry = 'delete from setup_org_npwp where kodeorg=\'' . $kodeorg . '\'';
	break;

default:
	$strx = 'select * from setup_org_npwp where kodeorg=\'' . $kodeorg . '\' order by kodeorg';
	$res1 = mysql_query($strx);

	if (0 < mysql_num_rows($res1)) {
		$stry = 'update setup_org_npwp set alamatnpwp=\'' . $alamatnpwp . '\',npwp=\'' . $npwp . '\', alamatdomisili=\'' . $alamatdom . '\' where kodeorg=\'' . $kodeorg . '\'';
	}
	else {
		$stry = 'insert into ' . $dbname . '.setup_org_npwp(kodeorg,alamatnpwp,npwp,alamatdomisili) values(\'' . $kodeorg . '\',\'' . $alamatnpwp . '\',\'' . $npwp . '\',\'' . $alamatdom . '\')';
	}
	
}

//$strx = ($kodeorg = $_POST['kodeorg']) . $kodeorg . '\' limit 1';
if (mysql_query($stry)) {
	$str = 'select kodeorganisasi,namaorganisasi from organisasi where tipe=\'PT\' and length(kodeorganisasi)<=3 order by namaorganisasi desc';
	$res = mysql_query($str);

	while ($bar = mysql_fetch_object($res)) {
		$alamatnpwp = '';
		$npwp = '';
		$alamatdom = '';
		$str1 = 'select * from setup_org_npwp where kodeorg=\'' . $bar->kodeorganisasi . '\' order by kodeorg';
		$res1 = mysql_query($str1);

		while ($bar1 = mysql_fetch_object($res1)) {
			$alamatnpwp = $bar1->alamatnpwp;
			$npwp = $bar1->npwp;
			$alamatdom = $bar1->alamatdomisili;
		}

		echo '<tr class=rowcontent>' . "\r\n\t" . '  <td>' . $bar->kodeorganisasi . '</td>' . "\r\n\t" . '  <td>' . $bar->namaorganisasi . '</td>' . "\r\n\t" . '  <td>' . $npwp . '</td>' . "\r\n\t" . '  <td>' . $alamatnpwp . '</td>' . "\r\n\t" . '  <td>' . $alamatdom . '</td>' . "\r\n\t" . '  <td>' . "\r\n\t\t" . '  <img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delnpwp(\'' . $bar->kodeorganisasi . '\');">' . "\r\n\t" . '  </td>' . "\r\n\t" . '  </tr>';
	}
}
else {
	echo ' Gagal, HAPUS= '.$stry;
}

//break;
?>
