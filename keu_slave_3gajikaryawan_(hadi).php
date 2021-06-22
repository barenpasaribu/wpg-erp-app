<?php

require_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/zPosting.php';
$param = $_POST;
$tahunbulan = implode('', explode('-', $param['periode']));
$str = 'select tanggalmulai,tanggalsampai from ' . $dbname . ".sdm_5periodegaji\r\n  where kodeorg='" .$param['kodeorg']. "'\r\n    and periode='" . $param['periode'] . "'";
$tgmulai = '';
$tgsampai = '';
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
	$tgsampai = $bar->tanggalsampai;
	$tgmulai = $bar->tanggalmulai;
}
if ('' == $tgmulai || '' == $tgsampai) {
	exit('Error: Accounting period is not registered');
}

$str = 'select * from ' . $dbname . ".flag_alokasi where kodeorg='" .$param['kodeorg']. "' and periode='" . $param['periode'] . "' AND tipe='GAJI' ";
$res = mysql_query($str);

if (mysql_num_rows($res) > 0) {
		exit('Error: Alokasi Gaji Sudah Dilakukan');
}

$str1 = 'select tipe from ' . $dbname . ".organisasi where kodeorganisasi='" .$param['kodeorg']. "'";
$res1 = mysql_query($str1);
$hasil=mysql_fetch_assoc($res1);
$tipe=$hasil['tipe'];

$str = 'select * from ' . $dbname . '.sdm_ho_component ';
$resx = mysql_query($str);

$no = 0;

echo "<button  onclick=prosesAlokasiGaji(1) id=btnproses>Proses</button>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader>
<td rowspan='2'>No</td>
<td rowspan='2'>Periode</td>
<td rowspan='2'>Tipe</td>
<td rowspan='2'>Kode Organisasi</td>
<td rowspan='2'>ID Komponen</td>
<td rowspan='2'>Nama Komponen</td>
";

if($tipe=='HOLDING'){
echo "<td colspan='3'>H O</td></tr>
	 	<tr class=rowheader>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>";
}if($tipe=='PABRIK'){
echo "<td colspan='3'>PROSES MILL</td>
	  <td colspan='3'>UMUM PKS</td>
	  <td colspan='3'>BENGKEL</td>
	  <td colspan='3'>TRAKSI</td>
	  <td rowspan='2'>SUBTOTAL</td>
	  </tr>
	 	<tr class=rowheader>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>";
}if($tipe=='KEBUN'){
echo "<td colspan='3'>BBT</td>
	  <td colspan='3'>TB</td>
	  <td colspan='3'>TBM</td>
	  <td colspan='3'>TM</td>
	  <td colspan='3'>PANEN</td>
	  <td colspan='3'>KEBUN UMUM</td>
	  <td colspan='3'>TRAKSI</td>
	  <td>SUBTOTAL</td>
	  </tr>
	 	<tr class=rowheader>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>";
}

echo "</tr></thead><tbody>";

while ($rows1 = mysql_fetch_assoc($resx)) {
		
		$no++;
		$namakomponen=$rows1['name'];
		$noakunho=$rows1['noakun_ho'];
		$noakunkredit=$rows1['noakun_kredit'];
		
		//PABRIK
		$noakuntraksi=$rows1['noakun_traksi'];
		$noakunworkshop=$rows1['noakun_workshop'];
		$noakunmillproses=$rows1['noakun_millproses'];
		$noakunmillumum=$rows1['noakun_millumum'];

		//KEBUN
		$noakuntraksi=$rows1['noakun_traksi'];
		$noakunkebunbbt=$rows1['noakun_kebun_bbt'];
		$noakunkebuntb=$rows1['noakun_kebun_tb'];
		$noakunkebuntm=$rows1['noakun_kebun_tm'];
		$noakunkebuntbm=$rows1['noakun_kebun_tbm'];
		$noakunkebunpanen=$rows1['noakun_kebun_panen'];
		$noakunkebunumum=$rows1['noakun_kebunumum'];
		
		$idkomponen=$rows1['id'];

		echo "<tr class=rowcontent id='row".$no."'>
			  <td id='id".$no."'>".$no."</td>
			  <td id='periode".$no."'>".$param['periode']."</td>
			  <td id='tipe".$no."'>".$tipe."</td>
			  <td id='kodeorg".$no."'>".$param['kodeorg']."</td>
			  <td id='idkomponen".$no."'>".$rows1['id']."</td>
			  <td id='namakomponen".$no."'>".$rows1['name']."</td>
			  ";
			
		$lokres= 'E';
		if (substr($param['kodeorg'],0,3)=='LSP' || substr($param['kodeorg'],0,3)=='SSP' || substr($param['kodeorg'],0,3)=='MPS'){
			$lokres= 'M';
		}
		$str= "SELECT * FROM sdm_ho_hr_jms_porsi where lokasiresiko='".$lokres."' AND id='perusahaan'";
		$res = mysql_query($str);
		while ($row = mysql_fetch_array($res)) {
			$persenjhtpt=$row['jhtpt'];
			$persenjppt=$row['jppt'];
			$maksjppt=$row['jmppt']; //FA 20200228 - untuk maksimal BPJS TK - JP
		}

		if($tipe=='HOLDING'){

			//HARDCODE untuk hitung JHT dan JP PERUSAHAAN 

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
				$jumlah=$rows['jumlah'];
				if($idkomponen=='55' || $idkomponen=='56'){
					$qry="select distinct(karyawanid) 
							FROM sdm_gaji where periodegaji='".$param['periode']."' 
							AND kodeorg='".$param['kodeorg']."' AND (idkomponen='5' or idkomponen='9')";
					$queri = mysql_query($qry);
					$jumlah=0;
					while ($hasil = mysql_fetch_array($queri)) {
						$nilaix= 0;
						$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
						$timex = strtotime($datex);
						$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}
							
							if($idkomponen=='55'){
								$jhtpt=$persenjhtpt*$nilaix/100;		
							}
							// FA 20200228 - cek usia pensiun
							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
							if($idkomponen=='56'){
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							}
						}
					
						if($idkomponen=='55'){
							$jumlah=$jumlah+$jhtpt;		
						}
						if($idkomponen=='56'){
							$jumlah=$jumlah+$jppt;
						}
					}
				}
				echo "<td id='noakunho".$no."'>".$noakunho."</td>";
				echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				echo "<td id='jumlahho".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlah)."</td>";
				$totaljumlah=$totaljumlah+$jumlah;
			}
		}
		
		if($tipe=='PABRIK'){

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='STATION'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			$jumlahmillproses=$rows['jumlah'];

			if($idkomponen=='55' || $idkomponen=='56'){
			$qry="select distinct(a.karyawanid) 
					FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
					LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
					where (idkomponen='5' or idkomponen='9') AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
					AND tipe='STATION'";
			$queri = mysql_query($qry);
			$jumlah=0;
			while ($hasil = mysql_fetch_array($queri)) {
				
						$nilaix= 0;
						$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
						$timex = strtotime($datex);
						$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}
							
							if($idkomponen=='55'){
								$jhtpt=$persenjhtpt*$nilaix/100;		
							}
							// FA 20200228 - cek usia pensiun
							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
							if($idkomponen=='56'){
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							}
						}
/*
				$res1 = mysql_query($str1);
				while ($rows = mysql_fetch_array($res1)) {
					if($idkomponen=='55'){
						$jhtpt=$persenjhtpt*$rows['jumlah']/100;		
					}
					if($idkomponen=='56'){
						$jppt=$persenjppt*$rows['jumlah']/100;
					}
				}
*/				
				if($idkomponen=='55'){
					$jumlahmillproses=$jumlahmillproses+$jhtpt;		
				}
				if($idkomponen=='56'){
					$jumlahmillproses=$jumlahmillproses+$jppt;
				}
			}
			}


			echo "<td id='noakunmillproses".$no."'>".$noakunmillproses."</td>";	
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";		
			echo "<td id='jumlahmillproses'".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahmillproses)."</td>";
			$totaljumlah=$totaljumlah+$jumlahmillproses;

			}

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  ( tipe is null OR tipe='' OR tipe='GUDANG')
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
			$jumlahmillumum=$rows['jumlah'];

			if($idkomponen=='55' || $idkomponen=='56'){
			$qry="select distinct(a.karyawanid) 
					FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
					LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
					where (idkomponen='5' or idkomponen='9') AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
					AND  ( tipe is null OR tipe='' OR tipe='GUDANG') ";
			$queri = mysql_query($qry);
			$jumlah=0;
			while ($hasil = mysql_fetch_array($queri)) {
				
						$nilaix= 0;
						$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
						$timex = strtotime($datex);
						$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}
							
							if($idkomponen=='55'){
								$jhtpt=$persenjhtpt*$nilaix/100;		
							}
							// FA 20200228 - cek usia pensiun
							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
							if($idkomponen=='56'){
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							}
						}

/*
				$res1 = mysql_query($str1);
				while ($rows = mysql_fetch_array($res1)) {
					if($idkomponen=='55'){
						$jhtpt=$persenjhtpt*$rows['jumlah']/100;		
					}
					if($idkomponen=='56'){
						$jppt=$persenjppt*$rows['jumlah']/100;
					}
				}
*/				
				if($idkomponen=='55'){
					$jumlahmillumum=$jumlahmillumum+$jhtpt;		
				}
				if($idkomponen=='56'){
					$jumlahmillumum=$jumlahmillumum+$jppt;
				}
			}
			}

			echo "<td id='noakunmillumum".$no."'>".$noakunmillumum."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlahmillumum".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahmillumum)."</td>";
			$totaljumlah1=$totaljumlah1+$jumlahmillumum;
			}

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='WORKSHOP'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
			$jumlahworkshop=$rows['jumlah'];


			if($idkomponen=='55' || $idkomponen=='56'){
			$qry="select distinct(a.karyawanid) 
					FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
					LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
					where (idkomponen='5' or idkomponen='9') AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
					AND  tipe='WORKSHOP' ";
			$queri = mysql_query($qry);
			$jumlah=0;
			while ($hasil = mysql_fetch_array($queri)) {
				
						$nilaix= 0;
						$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
						$timex = strtotime($datex);
						$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}
							
							if($idkomponen=='55'){
								$jhtpt=$persenjhtpt*$nilaix/100;		
							}
							// FA 20200228 - cek usia pensiun
							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
							if($idkomponen=='56'){
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							}
						}

/*
				$res1 = mysql_query($str1);
				while ($rows = mysql_fetch_array($res1)) {
					if($idkomponen=='55'){
						$jhtpt=$persenjhtpt*$rows['jumlah']/100;		
					}
					if($idkomponen=='56'){
						$jppt=$persenjppt*$rows['jumlah']/100;
					}
				}
*/				
				if($idkomponen=='55'){
					$jumlahworkshop=$jumlahworkshop+$jhtpt;		
				}
				if($idkomponen=='56'){
					$jumlahworkshop=$jumlahworkshop+$jppt;
				}
			}
			}


			echo "<td id='noakunworkshop".$no."'>".$noakunworkshop."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlahworkshop".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahworkshop)."</td>";
			$totaljumlah2=$totaljumlah2+$jumlahworkshop;

			}

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='TRAKSI'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
			$jumlahmilltraksi=$rows['jumlah'];


			if($idkomponen=='55' || $idkomponen=='56'){
			$qry="select distinct(a.karyawanid) 
					FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
					LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
					where (idkomponen='5' or idkomponen='9') AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
					AND  tipe='TRAKSI' ";
			$queri = mysql_query($qry);
			$jumlah=0;
			while ($hasil = mysql_fetch_array($queri)) {
				
						$nilaix= 0;
						$datex= substr($param['periode'],-2)."/01/".substr($param['periode'],0,4);
						$timex = strtotime($datex);
						$ddatex = date('Y-m-d',$timex);
						$sql = "select nominal from sdm_5bpjstk_nominallain where lokasitugas='".$param['kodeorg']."' and kodegolongan like (select concat('%',a.kodegolongan,'%') x from datakaryawan a where a.karyawanid=".$hasil['karyawanid'].") and '".$ddatex."' >= tglmulai and '".$ddatex."' <= tglselesai and komponenid=5";
						$qstr = mysql_query($sql);

						$str1="	SELECT SUM(a.jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
						LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
						where a.karyawanid='".$hasil['karyawanid']."' AND (idkomponen='1' OR idkomponen='2' ) AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";
						$res1 = mysql_query($str1);

						while ($rows = mysql_fetch_array($res1)) {
							if (mysql_num_rows($qstr)>0){
								$rstr = mysql_fetch_assoc($qstr);
								$nilaix= $rstr['nominal'];
							} else {
								$nilaix= $rows['jumlah'];
							}
							
							if($idkomponen=='55'){
								$jhtpt=$persenjhtpt*$nilaix/100;		
							}
							// FA 20200228 - cek usia pensiun
							$gabthnbln= 0;
							$tanggallahir= $rows['tanggallahir'];
							list($year,$month,$day)= explode("-",$tanggallahir);
							$year_diff= date("Y") - $year;
							$month_diff= date("m") - $month;
							$day_diff= date("d") - $day;
							if ($month_diff < 0) $year_diff--;
								elseif (($month_diff==0) && ($day_diff < 0)) $year_diff--;
							
							$gabthnbln= $year_diff + ($month_diff/100);
							if($idkomponen=='56'){
								if ($rows['jumlah'] <= $maksjppt){
									$jppt=$persenjppt*$nilaix/100;
								} else {
									$jppt=$persenjppt*$maksjppt/100;
								}
								if ($gabthnbln > 57.01){
									$jppt= 0;
								}
							}
						}

/*
				$res1 = mysql_query($str1);
				while ($rows = mysql_fetch_array($res1)) {
					if($idkomponen=='55'){
						$jhtpt=$persenjhtpt*$rows['jumlah']/100;		
					}
					if($idkomponen=='56'){
						$jppt=$persenjppt*$rows['jumlah']/100;
					}
				}
*/				
				if($idkomponen=='55'){
					$jumlahmilltraksi=$jumlahmilltraksi+$jhtpt;		
				}
				if($idkomponen=='56'){
					$jumlahmilltraksi=$jumlahmilltraksi+$jppt;
				}
			}
			}


			echo "<td id='noakunmilltraksi".$no."'>".$noakuntraksi."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlahmilltraksi".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahmilltraksi)."</td>";
			$totaljumlah3=$totaljumlah3+$rows['jumlah'];
			$subtotal=$jumlahmillproses+$jumlahmillumum+$jumlahworkshop+$jumlahmilltraksi;
			echo "<td align='right'>".number_format($subtotal)."</td>";
			}
		}

		
		if($tipe=='KEBUN'){

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi LEFT JOIN setup_blok d on c.kodeorganisasi=d.kodeorg 
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."'
			AND  d.statusblok='BBT'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

	
			echo "<td id='noakun".$no."'>".$noakunkebunbbt."</td>";	
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah=$totaljumlah+$rows['jumlah'];

			}

			$str1="	SELECT SUM(jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi LEFT JOIN setup_blok d on c.kodeorganisasi=d.kodeorg 
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."'
			AND  d.statusblok='TB'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakunkebuntb."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah1=$totaljumlah1+$rows['jumlah'];

			}


			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi LEFT JOIN setup_blok d on c.kodeorganisasi=d.kodeorg 
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."'
			AND  d.statusblok='TBM'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakunkebuntbm."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah2=$totaljumlah2+$rows['jumlah'];

			}
		

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi LEFT JOIN setup_blok d on c.kodeorganisasi=d.kodeorg 
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."'
			AND  d.statusblok='TM'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakunkebuntm."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah3=$totaljumlah3+$rows['jumlah'];

			}


			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."'
			AND  tipe='AFDELING'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakunkebunpanen."</td>";	
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah4=$totaljumlah4+$rows['jumlah'];

			}


			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  ( tipe is null OR tipe='' OR tipe='GUDANG')
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakunkebunumum."</td>";	
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah5=$totaljumlah5+$rows['jumlah'];

			}


			$str1="	SELECT SUM(jumlah) as jumlah, b.tanggallahir FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='TRAKSI'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {

			echo "<td id='noakun".$no."'>".$noakuntraksi."</td>";		
			echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($rows['jumlah'])."</td>";
			$totaljumlah6=$totaljumlah6+$rows['jumlah'];

			}
		}
}
		echo "<tr><td colspan='8' align='center'>TOTAL</td><td id='jumlah".$no."' align='right'>".number_format($totaljumlah)."</td>";

		if($tipe=='PABRIK'){

			echo "<td colspan='3' align='right'>".number_format($totaljumlah1)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah2)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah3)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah1+$totaljumlah2+$totaljumlah3)."</td>";
		}

		if($tipe=='KEBUN'){

			echo "<td colspan='3' align='right'>".number_format($totaljumlah1)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah2)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah3)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah4)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah5)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah6)."</td>";

		}


		echo "</tr></table> ";
