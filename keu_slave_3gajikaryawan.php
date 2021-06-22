<?php
ini_set('max_execution_time', 600);
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


if($tipe=='PABRIK' || $tipe=='KEBUN'){
$str1 = "SELECT distinct(a.karyawanid), namakaryawan FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='TRAKSI' AND a.karyawanid NOT IN (SELECT distinct(idkaryawan) from vhc_rundt_operatorvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' AND tanggal<='".$tgsampai."')";
$res1 = mysql_query($str1);
if ( 0 < mysql_num_rows($res1)) {
    $t .= 'Karyawan Traksi: ';
    while ($bart = mysql_fetch_object($res1)) {
        $t .= $bart->karyawanid." ";
        $t .= $bart->namakaryawan."\n";
    }
    exit("Error: Karyawan Traksi belum ada kegiatan di modul traksi:\n".$t);
}
}

$str = 'select * from ' . $dbname . '.sdm_ho_component ';
$resx = mysql_query($str);

$no = 0;

echo "<button  onclick=prosesAlokasiGaji(1) id=btnproses>Proses</button>
<table class=sortable cellspacing=1 border=0>
<thead><tr class=rowheader >
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
		$plus=$rows1['plus'];

		$noakunkredit=$rows1['noakun_kredit'];
		
		$noakunho=$rows1['noakun_ho'];
		
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
		$noakunkebunpanen=$rows1['noakun_kebunpanen'];
		$noakunkebunumum=$rows1['noakun_kebunumum'];
		
		$idkomponen=$rows1['id'];

		echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailAlokasiGajiExcel(event,\'' .$param['periode']. '\',\'' .$param['kodeorg']. '\',\'' .$rows1['id']. '\',\'detailAlokasiGajiExcel.php\');"> ';


		echo " <td id='id".$no."'>".$no."</td>
			  <td id='periode".$no."'>".$param['periode']."</td>
			  <td id='tipe".$no."'>".$tipe."</td>
			  <td id='kodeorg".$no."'>".$param['kodeorg']."</td>
			  <td id='idkomponen".$no."'>".$rows1['id']."</td>
			  <td id='namakomponen".$no."'>".$rows1['name']."</td>
			  ";
			

		if($tipe=='HOLDING'){

		
			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
				$jumlah=$rows['jumlah'];
				
						
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakunho".$no."'>".$noakunho."</td>";
				} else {
					echo "<td id='noakunho".$no."'>".$noakunho."</td>";
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}
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

				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";		
					echo "<td id='noakunmillproses".$no."'>".$noakunmillproses."</td>";	
				} else {
					echo "<td id='noakunmillproses".$no."'>".$noakunmillproses."</td>";	
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";		
				}


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
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakunmillumum".$no."'>".$noakunmillumum."</td>";		
				} else {
					echo "<td id='noakunmillumum".$no."'>".$noakunmillumum."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}

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

				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakunworkshop".$no."'>".$noakunworkshop."</td>";		
				} else {
					echo "<td id='noakunworkshop".$no."'>".$noakunworkshop."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}


			echo "<td id='jumlahworkshop".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahworkshop)."</td>";
			$totaljumlah2=$totaljumlah2+$jumlahworkshop;

			}


			//TRAKSI

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='TRAKSI'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
			$jumlahmilltraksi=$rows['jumlah'];

				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
					echo "<td id='noakunmilltraksi".$no."'>".$noakuntraksi."</td>";		
				} else {
					echo "<td id='noakunmilltraksi".$no."'>".$noakuntraksi."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}


			echo "<td id='jumlahmilltraksi".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahmilltraksi)."</td>";
			$totaljumlah3=$totaljumlah3+$jumlahmilltraksi;
			$subtotal=$jumlahmillproses+$jumlahmillumum+$jumlahworkshop+$jumlahmilltraksi;
			echo "<td align='right'>".number_format($subtotal)."</td>";
			}
		}

		
		if($tipe=='KEBUN'){

			//KEBUN BBT
			$jmlh1=0;
			$jmlh2=0;
			$jmlh3=0;



			if($idkomponen=='1'){
				$where="AND kodegolongan not LIKE 'BHL%'";
			}else{
				$where="";
			}


		// MULAI HITUNG BBT
		// cek dulu ada aktivitas bbt gak di db
		/*	$a="select distinct(tipetransaksi) from kebun_aktivitasvw where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='BBT'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);


			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek<1||$cek1<1){
		*/	$jumlahkebunBBT='0';			
		/*	}else{

			//GAJI PEMANEN
			$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='BBT' ".$where." 
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);

			//GAJI MANDOR
			$str2="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.nikmandor=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.nikmandor AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='BBT'
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res2 = mysql_query($str2);
			$jmlh2=mysql_fetch_assoc($res2);

			$jumlahkebunBBT=$jmlh1['jumlah']+$jmlh2['jumlah'];
		}
		*/		if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
					echo "<td id='noakun".$no."'>".$noakunkebunbbt."</td>";		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebunbbt."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
				}
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunBBT)."</td>";
			$totaljumlah=$totaljumlah+$jumlahkebunBBT;

			//END KEBUN BBT


			//KEBUN TB
			$jmlh1=0;
			$jmlh2=0;
			$jmlh3=0;

			// cek dulu ada aktivitas bbt gak di db
		/*	$a="select distinct(tipetransaksi) from kebun_aktivitasvw where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TB'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek<1||$cek1<1){
		*/	$jumlahkebunTB='0';			
		/*	}else{

			$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='TB' ".$where."
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);

			$str2="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.nikmandor=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.nikmandor AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='TB'
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res2 = mysql_query($str2);
			$jmlh2=mysql_fetch_assoc($res2);


			$jumlahkebunTB=$jmlh1['jumlah']+$jmlh2['jumlah'];

		}
		*/		if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
					echo "<td id='noakun".$no."'>".$noakunkebuntb."</td>";		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebuntb."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
				}

			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunTB)."</td>";
			$totaljumlah1=$totaljumlah+$jumlahkebunTB;

			
			//END KEBUN TB


			//KEBUN TBM
			$jmlh1=0;
			$jmlh2=0;
			$jmlh3=0;

		// cek dulu ada aktivitas TBM gak di db
		/*	$a="select distinct(tipetransaksi) from kebun_aktivitasvw where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TBM'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek<1||$cek1<1){
		*/	$jumlahkebunTBM='0';			
		/*	}else{


			$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='TBM' ".$where."
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);

			$str2="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.nikmandor=a.karyawanid 
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.nikmandor AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='TBM'
						GROUP BY kodeblok, tipetransaksi, karyawanid, idkomponen) as x";
			$res2 = mysql_query($str2);
			$jmlh2=mysql_fetch_assoc($res2);
			saveLog($str2);

			$jumlahkebunTBM=$jmlh1['jumlah']+$jmlh2['jumlah'];

		}
		*/		if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
					echo "<td id='noakun".$no."'>".$noakunkebuntbm."</td>";		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebuntbm."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
				}
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunTBM)."</td>";
			$totaljumlah2=$totaljumlah2+$jumlahkebunTBM;

			
			//END KEBUN TBM

			//KEBUN TM
			$jmlh1=0;
			$jmlh2=0;
			$jmlh3=0;
			$jmlh4=0;

		// cek dulu ada aktivitas TM gak di db
/*			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TM'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek<1||$cek1<1){
			$jumlahkebunTM='0';			
			}else{
			$jumlahkebunTM='0';

*/
			$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='TM' AND (alias like '%pemanen%' OR alias IS NULL) AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, tipetransaksi) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);

			$str2="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, round(SUM(b.hasilkerja)) AS jmlh, tipetransaksi, (SELECT round(sum(hasilkerja)) FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid) AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0 AND idkomponen='".$idkomponen."' AND tipetransaksi='TM' GROUP BY a.karyawanid, tipetransaksi,  idkomponen) as x";
			$res2 = mysql_query($str2);
			$jmlh2=mysql_fetch_assoc($res2);

			//GAJI KERANI
			$str4="  SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND tipe='AFDELING' AND bagian like '%ADM'";
			$res4 = mysql_query($str4);
			$jmlh4=mysql_fetch_assoc($res4);


			$jumlahkebunTM=$jmlh1['jumlah']+$jmlh2['jumlah']+$jmlh4['jumlah'];

//		}
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
					echo "<td id='noakun".$no."'>".$noakunkebuntm."</td>";		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebuntm."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
				}

			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunTM)."</td>";
			$totaljumlah3=$totaljumlah3+$jumlahkebunTM;

			
			//END KEBUN TM



			//KEBUN PANEN
			$jmlh1=0;
			$jmlh2=0;
			$jmlh3=0;


		// cek dulu ada aktivitas PNN gak di db
/*			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='PNN'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek<1||$cek1<1){
			$jumlahkebunPNN='0';			
			}else{
/*
			$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, ROUND((SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where y.karyawanid=a.karyawanid  
						AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='PNN' ".$where."
						GROUP BY  karyawanid, kodeblok, tipetransaksi) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);
*/

						$str1="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='PNN'  AND (alias like '%pemanen%' OR alias IS NULL) AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, tipetransaksi) as x";
			$res1 = mysql_query($str1);
			$jmlh1=mysql_fetch_assoc($res1);

			$str2="  SELECT ROUND(SUM(jumlah*jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT round(sum(hasilkerja)) FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid ) AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b ON (a.karyawanid=b.nikmandor OR a.karyawanid=b.nikmandor1) AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND idkomponen='".$idkomponen."' AND tipetransaksi='PNN'
						GROUP BY a.karyawanid, tipetransaksi,idkomponen) as x";
			$res2 = mysql_query($str2);
			$jmlh2=mysql_fetch_assoc($res2);
		//	saveLog($str2);

			$jumlahkebunPNN=$jmlh1['jumlah']+$jmlh2['jumlah'];
//		}
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakun".$no."'>".$noakunkebunpanen."</td>";	
		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebunpanen."</td>";	
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}

			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunPNN)."</td>";
			$totaljumlah4=$totaljumlah4+$jumlahkebunPNN;



			//KEBUN UMUM

			$aa="select distinct(idkomponen) from sdm_gaji where periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND idkomponen='".$idkomponen."' ";
			$bb = mysql_query($aa);
			$cek1=mysql_num_rows($bb);


			if($cek1<1){
			$jumlahkebunUmum='0';			
			}else{

			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  ( tipe is null OR tipe='' OR tipe='GUDANG')
			";

			$res1 = mysql_query($str1);
			$rows = mysql_fetch_assoc($res1);
				$jumlahkebunUmum=$rows['jumlah'];
		}
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakun".$no."'>".$noakunkebunumum."</td>";	
		
				} else {
					echo "<td id='noakun".$no."'>".$noakunkebunumum."</td>";	
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
				}

			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahkebunUmum)."</td>";
			$totaljumlah5=$totaljumlah5+$jumlahkebunUmum;




			$str1="	SELECT SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi
			where idkomponen='".$idkomponen."' AND periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."'
			AND  tipe='TRAKSI'
			";

			$res1 = mysql_query($str1);
			while ($rows = mysql_fetch_array($res1)) {
				$jumlahTraksi=$rows['jumlah'];
				if($plus==0 && $noakunkredit==''){
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";	
					echo "<td id='noakun".$no."'>".$noakuntraksi."</td>";	
		
				} else {

					echo "<td id='noakun".$no."'>".$noakuntraksi."</td>";		
					echo "<td id='noakunkredit".$no."'>".$noakunkredit."</td>";
				}
			echo "<td id='jumlah".$no."' align='right' style='background-color:#d4c9c9'>".number_format($jumlahTraksi)."</td>";
			$totaljumlah6=$totaljumlah6+$jumlahTraksi;

			$subtotal=$jumlahkebunBBT+$jumlahkebunTB+$jumlahkebunTBM+$jumlahkebunTM+$jumlahkebunPNN+$jumlahkebunUmum+$jumlahTraksi;
			echo "<td align='right'>".number_format($subtotal)."</td></tr>";

			}
		}
	}

	//END KEBUN



		echo "<tr><td colspan='8' align='center'>TOTAL</td><td id='jumlah".$no."' align='right'>".number_format($totaljumlah)."</td>";

		if($tipe=='PABRIK'){

			echo "<td colspan='3' align='right'>".number_format($totaljumlah1)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah2)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah3)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah+$totaljumlah1+$totaljumlah2+$totaljumlah3)."</td>";
		}

		if($tipe=='KEBUN'){

			echo "<td colspan='3' align='right'>".number_format($totaljumlah1)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah2)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah3)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah4)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah5)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah6)."</td>";
			echo "<td colspan='3' align='right'>".number_format($totaljumlah+$totaljumlah1+$totaljumlah2+$totaljumlah3+$totaljumlah4+$totaljumlah5+$totaljumlah6)."</td>";
		}

echo "</tr>";


if($tipe=='KEBUN'){

$sqla="SELECT DISTINCT(a.karyawanid) as karyawanid, b.namakaryawan FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid INNER JOIN sdm_5jabatan c ON b.kodejabatan=c.kodejabatan WHERE periodegaji='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' AND a.karyawanid NOT IN ( SELECT distinct(keranimuat) AS karyawanid FROM kebun_aktivitasvw WHERE LEFT(tanggal,7)='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' UNION SELECT distinct(karyawanid) AS karyawanid FROM kebun_aktivitasvw WHERE LEFT(tanggal,7)='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' UNION SELECT distinct(nikmandor) AS karyawanid FROM kebun_aktivitasvw WHERE LEFT(tanggal,7)='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' UNION SELECT distinct(nikmandor1) AS karyawanid FROM kebun_aktivitasvw WHERE LEFT(tanggal,7)='".$param['periode']."' AND kodeorg='".$param['kodeorg']."' ) AND subbagian='SPSE1' and kodegolongan !='BHL2' ";
	
	$reb=mysql_query($sqla);
	echo "<tr><td colspan='28' >";
	echo "TIDAK ADA KEGIATAN : <br>";
	while ($bax = mysql_fetch_object($reb)) {
	echo $bax->karyawanid." - ".$bax->namakaryawan." <br> ";
	}
	echo "</td></tr>";
}

		echo "</tbody></table> ";
?>