<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kodecustomer = $_POST['kodecustomer'];
$namacustomer = $_POST['namacustomer'];
$alamat = $_POST['alamat'];
$kota = $_POST['kota'];
$telepon = $_POST['telepon'];
$kontakperson = $_POST['kontakperson'];
$akun = $_POST['akun'];
$plafon = $_POST['plafon'];
$nilaihutang = $_POST['nilaihutang'];
$npwp = $_POST['npwp'];
$noseri = $_POST['noseri'];
$klcustomer = $_POST['klcustomer'];
$pk = $_POST['pk'];
$jpk = $_POST['jpk'];
$method = $_POST['method'];
$strx = 'select 1=1';
$query = mysql_query($strx);
switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $kodecustomer . '\'';
	$query = mysql_query($strx);
	break;

case 'update':
	$strx = 'update ' . $dbname . '. pmn_4customer set namacustomer=\'' . $namacustomer . '\',alamat=\'' . $alamat . '\',kota=\'' . $kota . '\',' . "\r\n\t\t" . 'telepon=\'' . $telepon . '\',kontakperson=\'' . $kontakperson . '\',' . "\r\n\t\t" . 'akun=\'' . $akun . '\',plafon=\'' . $plafon . '\',' . "\r\n\t\t" . 'nilaihutang=\'' . $nilaihutang . '\',npwp=\'' . $npwp . '\'' . "\r\n\t\t" . ',noseri=\'' . $noseri . '\',klcustomer=\'' . $klcustomer . '\',pk=\'' . $pk . '\',jpk=\'' . $jpk . '\'' . "\r\n\t\t" . 'where kodecustomer=\'' . $kodecustomer . '\'';
	$query = mysql_query($strx);
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.pmn_4customer' . "\r\n\t\t" . '(`kodecustomer`, `namacustomer`, `alamat`, `kota`, `telepon`, `kontakperson`, `akun`, `plafon`, `nilaihutang`, `npwp`, `noseri`, `klcustomer`,pk,jpk)' . "\r\n\t\t" . 'values' . "\r\n\t\t" . '(\'' . $kodecustomer . '\',\'' . $namacustomer . '\',\'' . $alamat . '\',\'' . $kota . '\',\'' . $telepon . '\',\'' . $kontakperson . '\',\'' . $akun . '\',\'' . $plafon . '\',\'' . $nilaihutang . '\',\'' . $npwp . '\',\'' . $noseri . '\',\'' . $klcustomer . '\',\'' . $pk . '\',\'' . $jpk . '\')';
	$query = mysql_query($strx);
	break;
	case 'trfWB':
$kodecustomer = $_POST['kodecustomer'];
$namacustomer = $_POST['namacustomer'];
$alamat = $_POST['alamat'];
$kota = $_POST['kota'];
$kdPt = $_SESSION['empl']['induklokasitugas'];
$userid = $_SESSION['standar']['userid'];
$stat = '0';
$vendorstat = '1';
$tgl = date('Y-m-d h:i:s');
 if ($kdPt == 'SSP') {
            $idip = 1;
        } else if ($kdPt == 'LSP') {
            $idip = 2;
        } else {
            $idip = 3;
        }
	$strx = 'select * from ' . $dbname . '.setup_remotetimbangan where id = ' . $idip . '';
	$qLokasi = mysql_query($strx);
        $rLokasi = mysql_fetch_assoc($qLokasi);
        $ipAdd = $rLokasi['ip'];
        $prt = $rLokasi['port'];
        $dbnm = $rLokasi['dbname'];
        $usrName = $rLokasi['username'];
        $pswrd = $rLokasi['password'];
         $corn = mysql_connect($ipAdd . ':' . $prt, $usrName, $pswrd);
        $sCob = 'select * from ' . $dbnm . '.msvendorbuyer where VENDORCODE = \'' . $kodecustomer . '\'';
        #exit(mysql_error());
        $res = mysql_query($sCob, $corn);
        $row = mysql_num_rows($res);
          if ($row == 0) {
          
          		$sIns = "INSERT INTO $dbnm.msvendorbuyer(BUYERCODE, BUYERNAME, BUYERADDR, BUYERCITY, BUYERSTATUS, USERID, CREATEDATE,uploadStat) VALUES ('" . $kodecustomer . "','" . $namacustomer . "','" . $alamat . "','" . $kota . "','" .$vendorstat . "','" . $_SESSION['standard']['userid'] . "','" . $tgl . "','1')";
          
        
            if (mysql_query($sIns, $corn)) {              
//echo "warning: ".$sInsDO;
//exit();
//             	$srt = 'select * from ' . $dbname . '.pmn_4customer order by kodecustomer desc';

// if ($rep = mysql_query($srt,$conn)) {
// 	$no = 0;

// 	while ($bar = mysql_fetch_object($rep)) {
// 		$sql = 'select * from ' . $dbname . '.pmn_4klcustomer where `kode`=\'' . $bar->klcustomer . '\'';

// 		#exit(mysql_error($conn));
// 		($query = mysql_query($sql)) || true;
// 		$res = mysql_fetch_object($query);
// 		$spr = 'select * from  ' . $dbname . '.keu_5akun where `noakun`=\'' . $bar->akun . '\'';

// 		#exit(mysql_error($conn));
// 		($rej = mysql_query($spr)) || true;
// 		$bas = mysql_fetch_object($rej);
// 		$no += 1;
// 		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->kodecustomer . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->kontakperson . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->telepon . '</td>' . "\r\n" . '                                <td>' . $bar->telepon . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->akun . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bas->namaakun . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->plafon . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->nilaihutang . '</td>' . "\r\n\t\t\t\t" . '<td>' . $res->kelompok . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->pk . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->jpk . '</td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kodecustomer . '\',\'' . $bar->namacustomer . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\',\'' . $bar->telepon . '\',\'' . $bar->kontakperson . '\',\'' . $bar->akun . '\',\'' . $bar->plafon . '\',\'' . $bar->nilaihutang . '\',\'' . $bar->npwp . '\',\'' . $bar->noseri . '\',\'' . $bar->klcustomer . '\',\'' . $bas->namaakun . '\',\'' . $res->kelompok . '\',\'' . $bar->pk . '\',\'' . $bar->jpk . '\');"></td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPlgn(\'' . $bar->kodecustomer . '\');"></td>' . "\r\n\t\t\t\t" . '  <td ><button class=mybutton onclick="trfWB(\'' . $bar->kodecustomer . '\',\'' . $bar->namacustomer . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\');">' . 'Kirim' . '</button></td>' . '</tr>';
// 	}
// }
// else {
// 	echo ' Gagal,' . mysql_error($conn);
// }
       
									
               
            }else{
            	echo ' Gagal Upload Data Customer, sql= ' . $sIns;
            }
        	
        }
        else {
            //echo ' Gagal mscontract,' . addslashes(mysql_error($conn));
            echo ' Warning, Suplier Sudah Ada Di Program Weight Bridge, sql= ' . $sIns;
        }
	break;
	
case 'aktif':
	$strx = 'update ' . $dbname . '.pmn_4customer set flag_aktif=\'Y\' where kodecustomer=\'' . $kodecustomer . '\'';
	$query = mysql_query($strx);
	$query = mysql_query($strx);
	break;
	
case 'non-aktif':
	$strx = 'update ' . $dbname . '.pmn_4customer set flag_aktif=\'N\' where kodecustomer=\'' . $kodecustomer . '\'';
	$query = mysql_query($strx);
	break;
}

if (($query)) {
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

$srt = 'select * from ' . $dbname . '.pmn_4customer order by kodecustomer desc';

if ($rep = mysql_query($srt,$conn)) {
	$no = 0;

	while ($bar = mysql_fetch_object($rep)) {
		$sql = 'select * from ' . $dbname . '.pmn_4klcustomer where `kode`=\'' . $bar->klcustomer . '\'';

		#exit(mysql_error($conn));
		($query = mysql_query($sql)) || true;
		$res = mysql_fetch_object($query);
		$spr = 'select * from  ' . $dbname . '.keu_5akun where `noakun`=\'' . $bar->akun . '\'';

		#exit(mysql_error($conn));
		($rej = mysql_query($spr)) || true;
		$bas = mysql_fetch_object($rej);
		$no += 1;
		echo '<tr class=rowcontent>' . "\r\n\t\t\t\t" . '<td>' . $no . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->kodecustomer . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->kontakperson . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->telepon . '</td>' . "\r\n" . '                                <td>' . $bar->telepon . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->akun . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bas->namaakun . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->plafon . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->nilaihutang . '</td>' . "\r\n\t\t\t\t" . '<td>' . $res->kelompok . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->pk . '</td>' . "\r\n\t\t\t\t" . '<td>' . $bar->jpk . '</td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="fillField(\'' . $bar->kodecustomer . '\',\'' . $bar->namacustomer . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\',\'' . $bar->telepon . '\',\'' . $bar->kontakperson . '\',\'' . $bar->akun . '\',\'' . $bar->plafon . '\',\'' . $bar->nilaihutang . '\',\'' . $bar->npwp . '\',\'' . $bar->noseri . '\',\'' . $bar->klcustomer . '\',\'' . $bas->namaakun . '\',\'' . $res->kelompok . '\',\'' . $bar->pk . '\',\'' . $bar->jpk . '\');"></td>' . "\r\n\t\t\t\t" . '<td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delPlgn(\'' . $bar->kodecustomer . '\');"></td>' . "\r\n\t\t\t\t" . '</tr>';
	}
}
else {
	echo ' Gagal,' . mysql_error($conn);
}

?>
