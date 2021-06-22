<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/zLib.php';
echo "\t\r\n\r\n";
$kodehead = $_POST['kodehead'];
$kodeheadedit = $_POST['kodeheadedit'];
$matauangheadedit = $_POST['matauangheadedit'];
$simbolheadedit = $_POST['simbolheadedit'];
$kodeisoheadedit = $_POST['kodeisoheadedit'];
$kode = $_POST['kode'];
$kodedetail = $_POST['kodedetail'];
$matauang = $_POST['matauang'];
$simbol = $_POST['simbol'];
$kodeiso = $_POST['kodeiso'];
$kodedetail = $_POST['kodedetail'];
$kodedet = $_POST['kodedet'];
$jm = $_POST['jm'];
$mn = $_POST['mn'];
$jmsavedet = $jm . ':' . $mn;
$tgl = tanggalsystem($_POST['tgl']);
$kursdet = $_POST['kursdet'];
$jam = $_POST['jam'];
$daritanggal = tanggalsystem($_POST['daritanggal']);
$kodetambah = $_POST['kodetambah'];
$matauangtambah = $_POST['matauangtambah'];
$simboltambah = $_POST['simboltambah'];
$kodeisotambah = $_POST['kodeisotambah'];
$method = $_POST['method'];
$t = 0;

while ($t < 24) {
	if (strlen($t) < 2) {
		$t = '0' . $t;
	}

	$jm .= '<option value=' . $t . ' ' . ($t == 0 ? 'selected' : '') . '>' . $t . '</option>';
	++$t;
}

$y = 0;

while ($y < 60) {
	if (strlen($y) < 2) {
		$y = '0' . $y;
	}

	$mnt .= '<option value=' . $y . ' ' . ($y == 0 ? 'selected' : '') . '>' . $y . '</option>';
	++$y;
}

echo "\r\n";

switch ($method) {
case 'insert':
	$str = 'insert into ' . $dbname . '.setup_matauang (`kode`,`matauang`,`simbol`,`kodeiso`)' . "\r\n\t\t" . 'values (\'' . $kodetambah . '\',\'' . $matauangtambah . '\',\'' . $simboltambah . '\',\'' . $kodeisotambah . '\')';

	if (mysql_query($str)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'simpandetail':
	$str = 'insert into ' . $dbname . '.setup_matauangrate (`kode`,`daritanggal`,`jam`,`kurs`)' . "\r\n\t\t" . 'values (\'' . $kodedet . '\',\'' . $tgl . '\',\'' . $jmsavedet . '\',\'' . $kursdet . '\')';

	if (mysql_query($str)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'edithead':
	$str = 'update ' . $dbname . '.setup_matauang set kode=\'' . $kodeheadedit . '\',matauang=\'' . $matauangheadedit . '\',simbol=\'' . $simbolheadedit . '\',kodeiso=\'' . $kodeisoheadedit . '\'' . "\r\n\t\t\t\t" . 'where kode=\'' . $kodehead . '\' ';

	if (mysql_query($str)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'loadData':
	echo "\r\n\t\t" . '<table class=sortable cellspacing=1 border=0>' . "\r\n\t\t\t" . '<thead>' . "\r\n\t\t\t\t" . '<tr class=rowheader>' . "\r\n\t\t\t\t\t" . '<td align=center>No.</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Kode</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Tanggal</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Jam</td>' . "\r\n\t\t\t\t\t" . '<td align=center>Kurs</td>' . "\r\n\t\t\t\t\t" . '<td align=center>*</td>' . "\r\n\t\t\t\t" . '</tr>' . "\r\n\t\t\t" . '</thead>' . "\r\n\t\t" . '<tbody>';
	$limit = 30;
	$page = 0;

	if (isset($_POST['page'])) {
		$page = $_POST['page'];

		if ($page < 0) {
			$page = 0;
		}
	}

	$offset = $page * $limit;
	$maxdisplay = $page * $limit;
	$tmbh1 = '';

	if ($thnsort != '') {
		$tmbh1 = ' and tanggal like \'%' . $thnsort . '%\' ';
	}

	if ($kode == '') {
		$kode = $kodedetail;
	}

	$ql2 = 'select count(*) as jmlhrow from ' . $dbname . '.setup_matauangrate where kode=\'' . $kode . '\' ';

	#exit(mysql_error());
	($query2 = mysql_query($ql2)) || true;

	while ($jsl = mysql_fetch_object($query2)) {
		$jlhbrs = $jsl->jmlhrow;
	}

	$ha = 'select * from ' . $dbname . '. setup_matauangrate where kode=\'' . $kode . '\' order by daritanggal desc limit ' . $offset . ',' . $limit . '';

	#exit(mysql_error());
	($hi = mysql_query($ha)) || true;
	$no = $maxdisplay;

	while ($hu = mysql_fetch_assoc($hi)) {
		$no += 1;
		echo "\r\n\t\t" . '<tr class=rowcontent>' . "\r\n\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t" . '<td>' . $hu['kode'] . '</td>' . "\r\n\t\t\t" . '<td>' . tanggalnormal($hu['daritanggal']) . '</td>' . "\r\n\t\t\t" . '<td>' . $hu['jam'] . '</td>' . "\r\n\t\t\t" . '<td>' . $hu['kurs'] . '</td>' . "\r\n\t\t\t" . '<td>' . "\r\n\t\t\t\t" . '<img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="deldetail(\'' . $hu['kode'] . '\',\'' . tanggalnormal($hu['daritanggal']) . '\',\'' . $hu['jam'] . '\');" >' . "\r\n\t\t\t" . '</td>' . "\r\n\t\t" . '</tr>' . "\r\n\t\t\r\n\t\t";
	}

	echo '<tr class=rowcontent><td></td>' . "\r\n\t\t\t" . '<td><input type=text maxlength=3 id=kodedet value=' . $kode . ' disabled onkeypress="return_tanpa_kutip(event);" class=myinputtext style="width:50px;"></td>' . "\r\n\t\t\t" . '<td><input type=\'text\' class=\'myinputtext\' id=\'tgl\' onmousemove=\'setCalendar(this.id)\' onkeypress=\'return false;\'  size=\'10\' maxlength=\'10\' style=width:75px; /></td>' . "\r\n\t\t\t" . '<td><select id=jm>' . $jm . '</select>:<select id=mn>' . $mnt . '</select></td>' . "\r\n\t\t\t" . '<td><input type=text  id=kursdet onkeypress="return_angka_doang(event);" class=myinputtext style="width:50px;"></td>' . "\r\n\t\t\t" . '<td><img src=images/application/application_add.png class=resicon  title=\'Save\'  onclick=simpandetail()></td>' . "\r\n\t\t" . '</tr>';
	echo "\r\n\t\t" . '<tr class=rowheader><td colspan=6 align=center>' . "\r\n\t\t" . (($page * $limit) + 1) . ' to ' . (($page + 1) * $limit) . ' Of ' . $jlhbrs . '<br />' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page - 1) . ');>' . $_SESSION['lang']['pref'] . '</button>' . "\r\n\t\t" . '<button class=mybutton onclick=cariBast(' . ($page + 1) . ');>' . $_SESSION['lang']['lanjut'] . '</button>' . "\r\n\t\t" . '</td>' . "\r\n\t\t" . '</tr>';
	echo '</tbody></table>';
	break;

case 'delhead':
	$str = 'delete from ' . $dbname . '.setup_matauang where kode=\'' . $kode . '\' and matauang=\'' . $matauang . '\' and simbol=\'' . $simbol . '\' and kodeiso=\'' . $kodeiso . '\'';

	if (mysql_query($str)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;

case 'deldetail':
	$str = 'delete from ' . $dbname . '.setup_matauangrate where kode=\'' . $kode . '\' and daritanggal=\'' . $daritanggal . '\' and jam=\'' . $jam . '\'';

	if (mysql_query($str)) {
		echo '';
	}
	else {
		echo ' Gagal,' . addslashes(mysql_error($conn));
	}

	break;
}

?>
