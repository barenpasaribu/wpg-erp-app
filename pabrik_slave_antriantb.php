<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$noantrian = $_POST['noantrian'];
$nokendaraan = $_POST['nokendaraan'];
$sopir = $_POST['supir'];
$nospb = $_POST['nospb'];
$tanggal = $_POST['tgl'];
$noantrian = $_POST['noantrian'];
$method = $_POST['method'];

$kodeorg = $_SESSION['org']['kodeorganisasi'];
switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.pabrik_antriantb where noantrian=\'' . $noantrian . '\'';
	$query = mysql_query($strx);
	break;

case 'update':

	$strx = 'update ' . $dbname . '. pabrik_antriantb set nokendaraan=\'' . $nokendaraan . '\',sopir=\'' . $sopir . '\',nospb=\'' . $nospb . '\'' . "\r\n\t\t" . 'where noantrian=\'' . $noantrian . '\'';
	$query = mysql_query($strx);
	echo $strx;
	break;

case 'insert':
$userid = $_SESSION['empl']['karyawanid'];
$data = explode("-" , $tanggal);
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];
	$tgl = $y.'-'.$m.'-'.$d;
	$strx = 'insert into ' . $dbname . '.pabrik_antriantb' . "\r\n\t\t" . '( `tanggal`, `nokendaraan`, `sopir`, `nospb`, `userstamp`,`kodeorg`)' . "\r\n\t\t" . 'values' . "\r\n\t\t" . '(\'' . $tgl . '\',\'' . $nokendaraan . '\',\'' . $sopir . '\',\'' . $nospb . '\',\'' . $userid . '\',\'' . $kodeorg. '\')';
	$query = mysql_query($strx);
	echo $strx;
	break;
	
	
case 'loadData':
if(empty($_POST['tgl'])){
	$tanggal = date('Y-m-d');
}else{

	$tgl= $_POST['tgl'];
	$data = explode("-" , $tgl);
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];
	$tanggal = $y.'-'.$m.'-'.$d;
}

	$srt = 'select * from ' . $dbname . ".pabrik_antriantb  where tanggal='".$tanggal."' and kodeorg='".$kodeorg."' order by noantrian desc";

if ($rep = mysql_query($srt,$conn)) {
	$no = 0;

	while ($bar = mysql_fetch_object($rep)) {
	$no++;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->noantrian . '</td>'.'<td>' . $bar->tanggal . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->nokendaraan . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->sopir . '</td>' . "\r\n" . '                                <td>' . $bar->nospb . '</td>' . "\r\n\t\t\t\t"  . "\r\n\t\t\t\t" . '<td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delAntrian(\'' . $bar->noantrian . '\');"><img src=images/application/application_edit.png class=resicon  title=\'Ubah\' onclick="ubahAntrian(\'' . $bar->noantrian . '\',\'' . $bar->tanggal . '\',\'' . $bar->nokendaraan . '\',\'' . $bar->sopir . '\',\'' . $bar->nospb . '\');"></td>' . "\r\n\t\t\t\t" . '</tr>';
	}
}



	break;

	

}




?>
