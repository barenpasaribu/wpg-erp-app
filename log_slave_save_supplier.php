<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
$kelompok = $_POST['kelompok'];
$telp = $_POST['telp'];
$fax = $_POST['fax'];
$idsupplier = $_POST['idsupplier'];
$email = $_POST['email'];
$namasupplier = $_POST['namasupplier'];
$npwp = $_POST['npwp'];
$cperson = $_POST['cperson'];
$kota = $_POST['kota'];
$plafon = $_POST['plafon'];
$method = $_POST['method'];
$alamat = $_POST['alamat'];
$pkp = $_POST['pkp'];
$strx = 'select 1=1';

switch ($method) {
case 'delete':
	$strx = 'delete from ' . $dbname . '.log_5supplier where supplierid=\'' . $idsupplier . '\'';
	break;

case 'update':
	$strx = 'update ' . $dbname . '.log_5supplier set' . "\r\n" . '                   pkp=\'' . $pkp . '\',' . '                   kodekelompok=\'' . $kelompok . '\',' . "\r\n\t\t\t\t" . '   namasupplier=\'' . $namasupplier . '\',' . "\r\n\t\t\t\t" . '   alamat=\'' . $alamat . '\',' . "\r\n\t\t\t\t" . '   kota=\'' . $kota . '\',' . "\r\n\t\t\t\t" . '   telepon=\'' . $telp . '\',' . "\r\n\t\t\t\t" . '   kontakperson=\'' . $cperson . '\',' . "\r\n\t\t\t\t" . '   plafon=' . $plafon . ',' . "\r\n\t\t\t" . '       npwp=\'' . $npwp . '\',' . "\r\n\t\t\t\t" . '   fax=\'' . $fax . '\',' . "\r\n\t\t\t\t" . '   email=\'' . $email . '\'' . "\r\n\t\t\t\t" . '   where supplierid=\'' . $idsupplier . '\'' . "\r\n\t\t\t\t" . '  ';
	break;

case 'insert':
	$strx = 'insert into ' . $dbname . '.log_5supplier(' . "\r\n\t\t\t" . 'kodekelompok,namasupplier,alamat,' . "\r\n\t\t\t" . 'kota,telepon,kontakperson,plafon,' . "\r\n\t\t\t" . 'npwp,supplierid,fax,email,pkp)' . "\r\n\t\t\t" . 'values(\'' . $kelompok . '\',\'' . $namasupplier . '\',\'' . $alamat . '\',\'' . $kota . '\',\'' . $telp . '\',\'' . $cperson . '\',' . $plafon . ',\'' . $npwp . '\',\'' . $idsupplier . '\',\'' . $fax . '\',\'' . $email . '\',\'' . $pkp . '\')';
	break;

case 'updStatus':
	if ($_POST['status'] == 1) {
		$strx = 'update ' . $dbname . '.log_5supplier set status=0 where supplierid=\'' . $_POST['supplierid'] . '\'';
	}
	else {
		$strx = 'update ' . $dbname . '.log_5supplier set status=1 where supplierid=\'' . $_POST['supplierid'] . '\'';
	}

	break;
case 'trfWB':
$id_suplier = $_POST['idsuplier'];
$nama_suplier = $_POST['namasupplier'];
$alamat = $_POST['alamat'];
$kota = $_POST['kota'];
$kodekl = $_POST['kodekl'];
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
        $sCob = 'select * from ' . $dbnm . '.msvendortbs_eks where VENDORCODE = \'' . $id_suplier . '\'';
        #exit(mysql_error());
       	$koneksi = $ipAdd . ':' . $prt. $usrName. $pswrd;
        $res = mysql_query($sCob, $corn);
        $row = mysql_num_rows($res);
          if ($row == 0) {
          	if($kodekl == 'S006'){
          		$sIns = "INSERT INTO $dbnm.msvendortrp(TRPCODE, TRPNAME, TRPADDR, TRPCITY, TRPSTATUS, USERID, CREATEDATE,uploadStat) VALUES ('" . $id_suplier . "','" . $nama_suplier . "','" . $alamat . "','" . $kota . "','" .$vendorstat . "','" . $_SESSION['standard']['userid'] . "','" . $tgl . "','1')";
          	}else{


            $sIns = "INSERT INTO $dbnm.msvendortbs_eks(VENDORCODE, VENDORNAME, VENDORADDR, VENDORCITY, VENDORSTATUS, USERID, CREATEDATE,uploadStat) VALUES ('" . $id_suplier . "','" . $nama_suplier . "','" . $alamat . "','" . $kota . "','" .$vendorstat . "','" . $_SESSION['standard']['userid'] . "','" . $tgl . "','1')";
        }
        
            if (mysql_query($sIns, $corn)) {
              
//echo "warning: ".$sInsDO;
//exit();
        //     					$str = ' select * from ' . $dbname . '.log_5supplier where kodekelompok=\'' . $kodekl . '\' order by supplierid';

								// $res = mysql_query($str,$conn);
								// while ($bar = mysql_fetch_object($res)) {
								// $no += 1;
								// $bg = 'class=rowcontent';
								// $bger = 'onclick=updateStatus(\'' . $bar->supplierid . '\',\'' . $bar->status . '\') style=\'cursor:pointer\' title=\'Non Aktifkan ' . $bar->namasupplier . '\'';

								// if ($bar->status == 0) {
								// $bger = 'onclick=updateStatus(\'' . $bar->supplierid . '\',\'' . $bar->status . '\') style=\'cursor:pointer\' title=\'Aktifkan ' . $bar->namasupplier . '\'';
								// $bg = 'bgcolor=orange';
								// 	}

								// echo '<tr ' . $bg . '>' . "\r\n\t\t" . '     <td ' . $bger . '>' . $kodekl . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->supplierid . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->namasupplier . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->alamat . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->kontakperson . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->kota . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->telepon . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->fax . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->email . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->npwp . '</td>' . "\t" . ' ' . "\r\n\t\t\t" . ' <td align=right>' . number_format($bar->plafon, 0, ',', '.') . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delSupplier(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editSupplier(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\',\'' . $bar->alamat . '\',\'' . $bar->kontakperson . '\',\'' . $bar->kota . '\',\'' . $bar->telepon . '\',\'' . $bar->fax . '\',\'' . $bar->email . '\',\'' . $bar->npwp . '\',\'' . $bar->plafon . '\');"></td>'. '  <td ><button class=mybutton onclick="trfWB(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\',\'' . $kelompok . '\');">' . 'Kirim' . '</button></td>' . "\r\n\t\t\t" . ' </tr>';
								// 	}
									
               
            }else{
            	echo ' Gagal Upload Data Suplier, ' ;
            }
        	
        }
        else {
            //echo ' Gagal mscontract,' . addslashes(mysql_error($conn));
            echo ' Warning, Suplier Sudah Ada Di Program Weight Bridge, sql= ' . $sIns;
        }
	break;
}

if (($strx)) {
	mysql_query($strx);
}
else {
	echo ' Gagal,' . $strx;
}

$str = ' select * from ' . $dbname . '.log_5supplier where kodekelompok=\'' . $kelompok . '\' order by supplierid';

if ($res = mysql_query($str,$conn)) {
	while ($bar = mysql_fetch_object($res)) {
		$no += 1;
		$bg = 'class=rowcontent';
		$bger = 'onclick=updateStatus(\'' . $bar->supplierid . '\',\'' . $bar->status . '\') style=\'cursor:pointer\' title=\'Non Aktifkan ' . $bar->namasupplier . '\'';

		if ($bar->status == 0) {
			$bger = 'onclick=updateStatus(\'' . $bar->supplierid . '\',\'' . $bar->status . '\') style=\'cursor:pointer\' title=\'Aktifkan ' . $bar->namasupplier . '\'';
			$bg = 'bgcolor=orange';
		}
		if($bar->pkp==0){
			$pkp = "Tidak";
		}else{
			$pkp ="Ya";
		}
		
		echo '<tr ' . $bg . '>' . "\r\n\t\t" . '     <td ' . $bger . '>' . $kelompok . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->supplierid . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->namasupplier . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->alamat . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->kontakperson . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->kota . '</td>' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->telepon . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->fax . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->email . '</td>' . "\t\t" . ' ' . "\r\n\t\t\t" . ' <td ' . $bger . '>' . $bar->npwp . '</td>'. ' <td ' . $bger . '>' . $pkp . '</td>' . "\t" . ' ' . "\r\n\t\t\t" . ' <td align=right>' . number_format($bar->plafon, 0, ',', '.') . '</td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_delete.png class=resicon  title=\'Delete\' onclick="delSupplier(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\');"></td>' . "\r\n\t\t\t" . '  <td><img src=images/application/application_edit.png class=resicon  title=\'Edit\' onclick="editSupplier(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\',\'' . $bar->alamat . '\',\'' . $bar->kontakperson . '\',\'' . $bar->kota . '\',\'' . $bar->telepon . '\',\'' . $bar->fax . '\',\'' . $bar->email . '\',\'' . $bar->npwp . '\',\'' . $bar->plafon . '\',\'' . $bar->pkp . '\');"></td>'. '  <td ><button class=mybutton onclick="trfWB(\'' . $bar->supplierid . '\',\'' . $bar->namasupplier . '\',\'' . $bar->alamat . '\',\'' . $bar->kota . '\',\'' . $kelompok . '\');">' . 'Kirim' . '</button></td>' . "\r\n\t\t\t" . ' </tr>';
	}
}
else {
	echo ' Gagal,' . addslashes(mysql_error($conn));
}

?>
