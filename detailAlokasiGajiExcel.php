<?php





require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

$periode = $_GET['periode'];

$kodeorg = $_GET['kodeorg'];

$komponenid = $_GET['komponenid'];



$str = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' .$kodeorg. '\'';

$namapt = 'COMPANY NAME';

$res = mysql_query($str);



$qry="select * from sdm_gaji a inner join datakaryawan b on a.karyawanid=b.karyawanid LEFT JOIN organisasi c ON  b.subbagian=c.kodeorganisasi  where a.periodegaji='".$periode."' AND kodeorg='".$kodeorg."' AND idkomponen='".$komponenid."' ";
saveLog($qry);
$has = mysql_query($qry);

$stream .= "<table> <tr> <td> Kodeorg </td> <td> Periode </td>  <td> Karyawanid </td> <td> Nama Karyawan </td> <td> Jumlah </td> </tr> ";

while ($rows = mysql_fetch_assoc($has)) {



	if($rows['subbagian']==""){

		$subbagian="UMUM";	

	}else if($rows['subbagian']!="" && $rows['tipe']=="TRAKSI" ){

		$subbagian="TRAKSI";

	}else if($rows['subbagian']!="" && $rows['tipe']=="WORKSHOP" ){

		$subbagian="WORKSHOP";

	}else if($rows['subbagian']!="" && $rows['tipe']=="AFDELING" ){



		$subbagian="AFDELING";


	}else{

	$subbagian=$rows['subbagian']." - ".$rows['tipe'];
	
	}
		$str1="SELECT namajabatan FROM sdm_5jabatan where kodejabatan='".$rows['kodejabatan']."'  ";



		$res1 = mysql_query($str1);

		$row1 = mysql_fetch_array($res1);

		$jenis= $row1[0];
	

	$stream .= "<tr> <td> ".$kodeorg." </td> <td> ".$periode." </td> <td> '".$rows['karyawanid']." </td> <td> ".$rows['namakaryawan']." </td> <td> ".$rows['jumlah']." </td> <td> ".$rows['kodegolongan']." </td> <td> ".$subbagian." </td> <td> ".$jenis." </td> </tr> ";



$jumlah=$jumlah+$rows['jumlah'];

}	

$stream .= "<tr> <td colspan='4' align='center'>T O T A L</td><td> ".$jumlah." </td> </tr> ";



$stream .= '</table>Print Time:' . date('YmdHis') . '<br>By:' . $_SESSION['empl']['name'];

$nop_ = 'DetailAlokasiGaji';



if (0 < strlen($stream)) {

	if ($handle = opendir('tempExcel')) {

		while (false !== $file = readdir($handle)) {

			if (($file != '.') && ($file != '..')) {

				@unlink('tempExcel/' . $file);

			}

		}



		closedir($handle);

	}



	$handle = fopen('tempExcel/' . $nop_ . '.xls', 'w');



	if (!fwrite($handle, $stream)) {

		echo '<script language=javascript1.2>' . "\r\n" . '        parent.window.alert(\'Can\'t convert to excel format\');' . "\r\n" . '        </script>';

		exit();

	}

	else {

		echo '<script language=javascript1.2>' . "\r\n" . '        window.location=\'tempExcel/' . $nop_ . '.xls\';' . "\r\n" . '        </script>';

	}



	closedir($handle);

}



?>

