<?php
ini_set('max_execution_time', '300');

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

/*
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
*/
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
		<td rowspan='2'>jumlah</td>";
}if($tipe=='KEBUN'){
echo "<td colspan='3'>BBT</td>
	  <td colspan='3'>TB</td>
	  <td colspan='3'>TBM</td>
	  <td colspan='3'>TM</td>
	  <td colspan='3'>PANEN</td>
	  <td colspan='3'>KEBUN UMUM</td>
  	  <td colspan='3'>BENGKEL</td>
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
		<td>jumlah</td>
		<td>No Akun Debet</td>
		<td>No Akun Kredit</td>
		<td>jumlah</td>";
}

echo "</tr></thead><tbody>";
//$data=array[];
while ($rows1 = mysql_fetch_assoc($resx)) {
		
		$no++;
		$id=$rows1['id'];

		$data[$id]['no']=$no;
		$data[$id]['periode']=$param['periode'];
		$data[$id]['tipe']=$tipe;
		$data[$id]['plus']=$plus;
		$data[$id]['kodeorg']=$param['kodeorg'];
		$data[$id]['idkomponen']=$rows1['id'];
		$data[$id]['namakomponen']=$rows1['name'];
		$data[$id]['noakunkredit']=$rows1['noakun_kredit'];
		
		//HO
		$data[$id]['noakunho']=$rows1['noakun_ho'];
				
		//PABRIK
		$data[$id]['noakuntraksi']=$rows1['noakun_traksi'];
		$data[$id]['noakunworkshop']=$rows1['noakun_workshop'];
		$data[$id]['noakunmillproses']=$rows1['noakun_millproses'];
		$data[$id]['noakunmillumum']=$rows1['noakun_millumum'];

		//KEBUN
		$data[$id]['noakunkebunbbt']=$rows1['noakun_kebun_bbt'];
		$data[$id]['noakunkebuntb']=$rows1['noakun_kebun_tb'];
		$data[$id]['noakunkebuntm']=$rows1['noakun_kebun_tm'];
		$data[$id]['noakunkebuntbm']=$rows1['noakun_kebun_tbm'];
		$data[$id]['noakunkebunpanen']=$rows1['noakun_kebunpanen'];
		$data[$id]['noakunkebunumum']=$rows1['noakun_kebunumum'];
		
		


}

		if($tipe=='HOLDING'){

			$str1="	SELECT idkomponen,SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."'	group by idkomponen	";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlah']=$rows1['jumlah'];
			}
		

			foreach ($data as $idkomponen => $value) {
				echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailAlokasiGajiExcel(event,\'' .$param['periode']. '\',\'' .$param['kodeorg']. '\',\'' .$idkomponen. '\',\'detailAlokasiGajiExcel.php\');"> ';

				echo 	"<td>".$value['no']."</td>
					<td>".$value['periode']."</td>
					<td>".$value['tipe']."</td>
					<td>".$value['kodeorg']."</td>
					<td>".$value['idkomponen']."</td>
					<td>".$value['namakomponen']."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){

					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunho']."</td>";
	
				} else {

					echo "<td>".$value['noakunho']."</td>
						<td>".$value['noakunkredit']."</td>";
				}

				echo "<td align='right'>".number_format($value['jumlah'],2)."</td>";
		
		
				echo "</tr>";
			}
		}


		if($tipe=='PABRIK'){

			//mill proses
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  tipe='STATION' group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahmillproses']=$rows1['jumlah'];
			}

			//mill umum
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  ( tipe is null OR tipe='' OR tipe='GUDANG') group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahmillumum']=$rows1['jumlah'];
			}

			//mill bengkel
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  tipe='WORKSHOP' group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahmillworkshop']=$rows1['jumlah'];
			}

			//mill traksi
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  tipe='traksi' group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahmilltraksi']=$rows1['jumlah'];
			}


			//tampilkan data
			foreach ($data as $idkomponen => $value) {
				echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailAlokasiGajiExcel(event,\'' .$param['periode']. '\',\'' .$param['kodeorg']. '\',\'' .$idkomponen. '\',\'detailAlokasiGajiExcel.php\');"> ';

				echo 	"<td>".$value['no']."</td>
					<td>".$value['periode']."</td>
					<td>".$value['tipe']."</td>
					<td>".$value['kodeorg']."</td>
					<td>".$value['idkomponen']."</td>
					<td>".$value['namakomponen']."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunmillproses']."</td>";
				} else {
					echo "<td>".$value['noakunmillproses']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahmillproses'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunmillumum']."</td>";
				} else {
					echo "<td>".$value['noakunmillumum']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahmillumum'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunworkshop']."</td>";
				} else {
					echo "<td>".$value['noakunworkshop']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahmillworkshop'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakuntraksi']."</td>";
				} else {
					echo "<td>".$value['noakuntraksi']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahmilltraksi'],2)."</td>";

				$subtotal=$value['jumlahmillproses']+$value['jumlahmillumum']+$value['jumlahmillworkshop']+$value['jumlahmilltraksi'];
				echo "<td align='right'>".number_format($subtotal,2)."</td>";
		
				echo "</tr>";

				$submillproses=$submillproses+$value['jumlahmillproses'];
				$submillumum=$submillumum+$value['jumlahmillumum'];
				$submillworkshop=$submillworkshop+$value['jumlahmillworkshop'];
				$submilltraksi=$submilltraksi+$value['jumlahmilltraksi'];
			}
			echo 	"<tr>
						<td colspan='6' align='center'> TOTAL </td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($submillproses,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($submillumum,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($submillworkshop,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($submilltraksi,2)."</td>
						<td align='right'>".number_format($submillproses+$submillumum+$submillworkshop+$submilltraksi,2)."</td>
					</tr>";		

		}


		if($tipe=='KEBUN'){
			//KEBUN BBT
			// cek dulu ada aktivitas BBT gak di db
			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='BBT'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			if($cek<1){
			$jumlahkebunBBT='0';			
			}else{
			$jumlahkebunBBT='0';


			$str1="  SELECT idkomponen, ROUND(SUM(   if(isharian='1' AND idkomponen='1',0,jumlah)  *jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, b.kodeblok, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk , b.isharian
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND tipetransaksi='BBT'  AND (alias !='mandor') AND tipe='AFDELING'
						GROUP BY  karyawanid, idkomponen) as x group by idkomponen
					";
			
				$res1 = mysql_query($str1);
				while ($rows1 = mysql_fetch_array($res1)) {
					$id=$rows1['idkomponen'];
					$data[$id]['jumlahkebunBBT']=$rows1['jumlah'];
				}
			}

			//KEBUN TB
			// cek dulu ada aktivitas TB gak di db
			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TB'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			if($cek<1){
			$jumlahkebunTB='0';			
			}else{
			$jumlahkebunTB='0';


			$str1="  SELECT idkomponen, ROUND(SUM(   if(isharian='1' AND idkomponen='1',0,jumlah)  *jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk, b.isharian 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND tipetransaksi='TB'  AND (alias !='mandor') AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, idkomponen) as x group by idkomponen ";
			
				$res1 = mysql_query($str1);
				while ($rows1 = mysql_fetch_array($res1)) {
					$id=$rows1['idkomponen'];
					$data[$id]['jumlahkebunTB']=$rows1['jumlah'];
				}
			}



			//KEBUN TBM
			// cek dulu ada aktivitas TM gak di db
			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TBM'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			if($cek<1){
			$jumlahkebunTBM='0';			
			}else{
			$jumlahkebunTBM='0';


			$str1="  SELECT idkomponen, ROUND(SUM(   if(isharian='1' AND idkomponen='1',0,jumlah)  *jmlh/jumlahhk)) as jumlah FROM (SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk, b.isharian 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND tipetransaksi='TBM'  AND (alias !='mandor') AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, idkomponen) as x group by idkomponen ";
			
				$res1 = mysql_query($str1);
				while ($rows1 = mysql_fetch_array($res1)) {
					$id=$rows1['idkomponen'];
					$data[$id]['jumlahkebunTBM']=$rows1['jumlah'];
				}
			}

			//KEBUN TM
			// cek dulu ada aktivitas TM gak di db
			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='TM'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			if($cek<1){
			$jumlahkebunTM='0';			
			}else{
			$jumlahkebunTM='0';


			$str1="  SELECT idkomponen, ROUND(SUM(   if(isharian='1' AND idkomponen='1',0,jumlah)  *jmlh/jumlahhk)) as jumlah FROM (

					SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk, b.isharian 
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND tipetransaksi='TM'  AND (b.alias like '%pemanen%' OR b.alias IS NULL) AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, idkomponen
					union
					SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid  
					OR y.updateby=a.karyawanid OR y.keranimuat=a.karyawanid) AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk , isharian
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b ON (a.karyawanid=b.nikmandor 
						OR a.karyawanid=b.nikmandor1 OR b.updateby=a.karyawanid OR b.keranimuat=a.karyawanid ) AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0 AND tipetransaksi='TM'
						GROUP BY a.karyawanid,idkomponen

							) as x group by idkomponen ";
			saveLog($str1);
				$res1 = mysql_query($str1);
				while ($rows1 = mysql_fetch_array($res1)) {
					$id=$rows1['idkomponen'];
					$data[$id]['jumlahkebunTM']=$rows1['jumlah'];
				}
			}

			//KEBUN PNN
			// cek dulu ada aktivitas PNN gak di db
			$a="select distinct(tipetransaksi) from kebun_aktivitasvw where kodeorg='".$param['kodeorg']."' AND tanggal>='".$tgmulai."' and tanggal<='".$tgsampai."' AND tipetransaksi='PNN'";
			$b = mysql_query($a);
			$cek=mysql_num_rows($b);

			if($cek<1){
			$jumlahkebunPNN='0';			
			}else{
			$jumlahkebunPNN='0';


			$str1="  SELECT idkomponen, ROUND(SUM(   if(isharian='1' AND idkomponen='1',0,jumlah)  *jmlh/jumlahhk)) as jumlah  FROM (SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT totalhk FROM kebun_totalhk_vw y WHERE periode='".$param['periode']."' AND y.karyawanid=a.karyawanid ) AS jumlahhk , isharian
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b 
						ON a.karyawanid=b.karyawanid AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						inner join sdm_5jabatan c ON b.kodejabatan=c.kodejabatan inner join organisasi d ON b.subbagian=d.kodeorganisasi
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0
						AND tipetransaksi='PNN'  AND (b.alias like '%pemanen%' OR b.alias IS NULL) AND tipe='AFDELING' ".$where."
						GROUP BY  karyawanid, idkomponen
					union
					SELECT a.*, SUM(b.hasilkerja) AS jmlh, tipetransaksi, (SELECT sum(hasilkerja) FROM kebun_aktivitasvw y where (y.nikmandor=a.karyawanid OR y.nikmandor1=a.karyawanid  
					OR y.updateby=a.karyawanid OR y.keranimuat=a.karyawanid) AND y.tanggal >= '".$tgmulai."' AND y.tanggal <= '".$tgsampai."') AS jumlahhk , isharian
						FROM sdm_gaji a INNER JOIN kebun_aktivitasvw b ON (a.karyawanid=b.nikmandor 
						OR a.karyawanid=b.nikmandor1 OR b.updateby=a.karyawanid OR b.keranimuat=a.karyawanid ) AND b.tanggal >= '".$tgmulai."' AND b.tanggal <= '".$tgsampai."' 
						WHERE periodegaji='".$param['periode']."' AND a.kodeorg='".$param['kodeorg']."' AND hasilkerja!=0 AND tipetransaksi='PNN'
						GROUP BY a.karyawanid,idkomponen

						) as x group by idkomponen
					";
			saveLog($str1);
				$res1 = mysql_query($str1);
				while ($rows1 = mysql_fetch_array($res1)) {
					$id=$rows1['idkomponen'];
					$data[$id]['jumlahkebunPNN']=$rows1['jumlah'];
				}
			}


			//kebun umum
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  ( tipe is null OR tipe='' OR tipe='GUDANG') group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahkebunumum']=$rows1['jumlah'];
			}


			//kebun bengkel
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  tipe='WORKSHOP' group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahkebunworkshop']=$rows1['jumlah'];
			}

			//kebun traksi
			$str1="	SELECT idkomponen, SUM(jumlah) as jumlah FROM sdm_gaji a INNER JOIN datakaryawan b ON a.karyawanid=b.karyawanid
			LEFT JOIN organisasi c ON b.subbagian=c.kodeorganisasi where periodegaji='".$param['periode']."' 
			AND kodeorg='".$param['kodeorg']."' AND  tipe='traksi' group by idkomponen ";

			$res1 = mysql_query($str1);
			while ($rows1 = mysql_fetch_array($res1)) {
				$id=$rows1['idkomponen'];
				$data[$id]['jumlahkebuntraksi']=$rows1['jumlah'];
			}


			//tampilkan data
			foreach ($data as $idkomponen => $value) {
				echo '<tr class=rowcontent  style=\'cursor:pointer;\' title=\'Click\' onclick="detailAlokasiGajiExcel(event,\'' .$param['periode']. '\',\'' .$param['kodeorg']. '\',\'' .$idkomponen. '\',\'detailAlokasiGajiExcel.php\');"> ';

				$no=$value['no'];

				echo 	"<td>".$value['no']."</td>
					<td id='periode".$no."'>".$value['periode']."</td>
					<td id='tipe".$no."'>".$value['tipe']."</td>
					<td id='kodeorg".$no."'>".$value['kodeorg']."</td>
					<td>".$value['idkomponen']."</td>
					<td>".$value['namakomponen']."</td>";


				if($value['plus']=='0' && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebunbbt']."</td>";
				} else {
					echo "<td>".$value['noakunkebunbbt']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunBBT'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebuntb']."</td>";
				} else {
					echo "<td>".$value['noakunkebuntb']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunTB'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebuntbm']."</td>";
				} else {
					echo "<td>".$value['noakunkebuntbm']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunTBM'],2)."</td>";



				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebuntm']."</td>";
				} else {
					echo "<td>".$value['noakunkebuntm']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunTM'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebunpnn']."</td>";
				} else {
					echo "<td>".$value['noakunkebunpnn']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunPNN'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunkebunumum']."</td>";
				} else {
					echo "<td>".$value['noakunkebunumum']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunumum'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakunworkshop']."</td>";
				} else {
					echo "<td>".$value['noakunworkshop']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebunworkshop'],2)."</td>";

				if($value['plus']==0 && $value['noakunkredit']==''){
					echo "<td>".$value['noakunkredit']."</td>
						<td>".$value['noakuntraksi']."</td>";
				} else {
					echo "<td>".$value['noakuntraksi']."</td>
						<td>".$value['noakunkredit']."</td>";
				}
				echo "<td align='right' style='background-color:#d4c9c9'>".number_format($value['jumlahkebuntraksi'],2)."</td>";

				$subtotal=$value['jumlahkebunBBT']+$value['jumlahkebunTB']+$value['jumlahkebunTBM']+$value['jumlahkebunTM']+$value['jumlahkebunPNN']+$value['jumlahkebunumum']+$value['jumlahkebunworkshop']+$value['jumlahkebuntraksi'];
				echo "<td align='right'>".number_format($subtotal,2)."</td>";
		
				echo "</tr>";

				$subkebunBBT=$subkebunBBT+$value['jumlahkebunBBT'];
				$subkebunTB=$subkebunTB+$value['jumlahkebunTB'];
				$subkebunTBM=$subkebunTBM+$value['jumlahkebunTBM'];
				$subkebunTM=$subkebunTM+$value['jumlahkebunTM'];
				$subkebunPNN=$subkebunPNN+$value['jumlahkebunPNN'];

				$subkebunumum=$subkebunumum+$value['jumlahkebunumum'];
				$subkebunworkshop=$subkebunworkshop+$value['jumlahkebunworkshop'];
				$subkebuntraksi=$subkebuntraksi+$value['jumlahkebuntraksi'];
			}
			echo 	"<tr>
						<td colspan='6' align='center'> TOTAL </td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunBBT,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunTB,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunTBM,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunTM,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunPNN,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunumum,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebunworkshop,2)."</td>
						<td colspan='2'></td>
						<td align='right' style='background-color:#d4c9c9'>".number_format($subkebuntraksi,2)."</td>
						<td align='right'>".number_format($subkebunBBT+$subkebunTB+$subkebunTBM+$subkebunTM+$subkebunPNN+$subkebunumum+$subkebunworkshop+$subkebuntraksi,2)."</td>
					</tr>";		

		}
		echo "</table> ";



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

?>