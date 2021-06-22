<?php
	require_once 'master_validation.php';
	require_once 'config/connection.php';
	require_once 'lib/eagrolib.php';
	require_once 'lib/zFunction.php';

	require_once 'lib/zLib.php';
	include_once 'lib/zMysql.php';
	include_once 'lib/terbilang.php';
	include_once 'lib/spesialCharacter.php';

	
	function tgl_indo($tanggal){
		$bulan = array (
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$pecahkan = explode('-', $tanggal);

		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}

	$table = $_GET['table'];
	$column = $_GET['column'];
	$where = $_GET['cond'];
	$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
	$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
	$mtUang = makeOption($dbname, 'setup_matauang', 'kode,simbol');

	$i = 'select * from ' . $dbname . '.pmn_kontrakjual where nokontrak=\'' . $_GET['column'] . '\' ';

	($n = mysql_query($i)) || true;
	$d = mysql_fetch_assoc($n);

	if ($d['ppn'] == '0') {
		$ppn = 'tidak termasuk PPN 10%';
	}
	else {
		$ppn = 'termasuk PPN '.$d['ppn'].'% ';
	}

	$isiKualitas = explode(' ', $d['kualitas']);
	$ffa = $isiKualitas[0];
	$mi = $isiKualitas[0];


	// sementara $mutu tidak dipakai dimana mana
	if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'SSP')) {
		$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Semunai Sawit Perkasa dari campuran contoh CPO yang diambil dari bagian atas, tengah dan bawah tanki timbul bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';
		$dasarBerat = 'Berat final berdasarkan data hasil sounding tangki timbun PT. Semunai Sawit Perkasa di Kumaligon, dengan menggunakan TABEL DENSITY yang dikeluarkan oleh Surveyor SUCOFINDO.';
	}
	else if (($d['kodebarang'] == '40000001') && ($d['kodept'] == 'MJR')) {
		$mutu = 'FFA ' . $ffa . '% max; M&I ' . $mi . '% max; berdasarkan hasil pemeriksaan di laboratorium PMKS PT. Hexa Sawita bersama-sama wakil dari pembeli, tetapi bila CPO tidak diambil lebih dari satu minggu dari tanggal sesuai dengan perjanjian maka kami tidak menjamin FFA ' . $ffa . '% ';
		$dasarBerat = 'Berat final berdasarkan laporan hasil penimbangan di PMKS PT. Hexa Sawita, Kab. _____, ________.';
	}
	else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'SSP')) {
		$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Semunai Sawit Perkasa disaksikan dari wakil pihak Pembeli';
		$dasarBerat = '';
	}
	else if (($d['kodebarang'] == '40000002') && ($d['kodept'] == 'MJR')) {
		$mutu = 'FFA ' . $ffa . '% max; M&I total ' . $mi . '% max; berdasarkan hasil laboratorium PMKS PT. Merbaujaya Indahraya disaksikan dari wakil pihak Pembeli';
		$dasarBerat = '';
	}

	$derajat = 'º';
	$derajat = em($derajat);
	$derajat = urldecode($derajat);

	// sementara belum dipakai dimana mana
	$lokasiTtd = 'Pekanbaru';
	$barang=$nmBrg[$d['kodebarang']];
	
	if ($d['kodebarang'] == '40000001') {
		$klaimMutu = 'Apabila FFA CPO di atas standar maka akan diklaim secara proporsional';
		$barang = 'CPO (CRUDE PALM OIL)';
	}
	else if ($d['kodebarang'] == '40000002') {
		$klaimMutu = 'Apabila FFA PK di atas standar maka akan diklaim secara proporsional';
		$barang = 'PK (PALM KERNEL)';
	}

	$kodebarang = $d['kodebarang'];
	$kodept = $d['kodept'];

	$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
	$res1 = mysql_query($str1);
	$bar1 = mysql_fetch_object($res1);
	$namapt = $bar1->namaorganisasi;
	$alamatpt = $bar1->alamat;
	$kotapt= $bar1->wilayahkota;
	$telp = $bar1->telepon;
	$logo = $bar1->logo;
	$kotatelp = $kotapt;

$stream = "<table border='0'>";

$stream .=" <tr>
    <td rowspan='3' align='center' > <img src='http://trialwpg.anthesis-erp.xyz/".$logo."'>  </td>
    <td colspan='2'><B>".$namapt."</B></td>
  </tr>
  <tr>
    <td colspan='2'>".$alamatpt."</td>
  </tr>
  <tr>
    <td colspan='2'>".$kotatelp."</td>
  </tr>";

$stream .= "<tr><td colspan='3' align='center' >&nbsp;</td></tr>";
$stream .= "<tr><td colspan='3' align='center' width='600px'> <B> <u>KONTRAK JUAL BELI </U></b></td></tr>";
$stream .= "<tr><td colspan='3' align='center' >No.  ".$d['nokontrak']."</td></tr>";
$stream .= "<tr><td colspan='3' align='center' >&nbsp;</td></tr>";

$x = "select * from  ".$dbname.".organisasi where kodeorganisasi='".$d['kodept']."'";
$y = mysql_query($x);
$z = mysql_fetch_assoc($y);

$stream .= "<tr><td width='180' valign='top'>PENJUAL</td><td width='10' valign='top'>:</td><td width='400' valign='top'>".$z['namaorganisasi']."</td></tr>";
$stream .= "<tr><td valign='top' width='180'>Alamat</td><td valign='top' width='10'>:</td><td valign='top' width='400'>".$z['alamat']." ".$z['wilayahkota']."</td></tr>";

$xx = "select * from ".$dbname.".setup_org_npwp where kodeorg='".$d['kodept']."'";
$yy = mysql_query($xx);
$zz = mysql_fetch_assoc($yy);

$stream .= "<tr><td valign='top'>NPWP</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$zz['npwp']."</td>
		</tr>";

$o = "select * from ".$dbname.".pmn_4customer where kodecustomer='".$d['koderekanan']."'";
$p = mysql_query($o);
$q = mysql_fetch_assoc($p);
$stream .= "<tr>	<td  valign='top'>PEMBELI</td>
			<td width='10'  valign='top'>:</td>
			<td  valign='top'>".$q['namacustomer']."</td>
		</tr><tr>
			<td  valign='top'>Alamat</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$q['alamat']."</td>
		</tr><tr>
			<td valign='top'>NPWP</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$q['npwp']."</td>
		</tr>";
$stream .= "
		<tr>
			<td valign='top'>JENIS BARANG</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$barang."</td>
		</tr>";

$awan = stripslashes($d['kualitas']);
$awan = iconv('UTF-8', 'windows-1252', $awan);

$stream .= "<tr>	<td valign='top'>MUTU</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".str_replace('dan', '&', $awan)."</td>
		</tr>";

	$tempAngka = number_format($d['kuantitaskontrak']);
	$temp1 = str_replace(".","X",$tempAngka);
	$temp2 = str_replace(",","Y",$temp1);
	$temp1 = str_replace("X",",",$temp2);
	$temp2 = str_replace("Y",".",$temp1);

$stream .= "<tr>	<td valign='top'>BANYAKNYA (Kwantiti)</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$temp2." ".$d['satuan']."</td>
		</tr>";

$stream .= "<tr>	<td valign='top'>TERBILANG</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".ucwords(strtolower(terbilang($d['kuantitaskontrak'],1)))." Kilogram</td>
		</tr>";

$stream .= "<tr>	<td valign='top'>No. DO</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>". $d['nodo']."</td>
		</tr>";
	
	if ($d['ppn'] != '0') {
		$descppn = ' /Kg (Inc. PPN '.$d['ppn'].'%)';
		$ppnx = $d['hargasatuan']+($d['hargasatuan']*$d['ppn']/100);
		$tempAngka = $ppnx;
		$temp1 = str_replace(".","X",$tempAngka);
		$temp2 = str_replace(",","Y",$temp1);
		$temp1 = str_replace("X",",",$temp2);
		$temp1 = str_replace(",00",",-",$temp1);
		$ppnx = str_replace("Y",".",$temp1);
	}
	else {
		$descppn = ' /Kg (Exc. PPN 10%)';
		$ppnx = number_format($d['hargasatuan'],2);
		$tempAngka = $ppnx;
		$temp1 = str_replace(".","X",$tempAngka);
		$temp2 = str_replace(",","Y",$temp1);
		$temp1 = str_replace("X",",",$temp2);
		$temp1 = str_replace(",00",",-",$temp1);
		$ppnx = str_replace("Y",".",$temp1);
	}
	

$stream .= "<tr>	<td valign='top'>HARGA SATUAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'> Rp.".$ppnx." ".$descppn."</td>
		</tr>";


	$tempAngka = number_format($d['grand_total'],2);
	$temp1 = str_replace(".","X",$tempAngka);
	$temp2 = str_replace(",","Y",$temp1);
	$temp1 = str_replace("X",",",$temp2);
	$temp2 = str_replace("Y",".",$temp1);
	$temp2 = str_replace(",00",",-",$temp2);

$stream .= "<tr>	<td valign='top'>JUMLAH HARGA</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'> Rp.".$temp2."</td>
		</tr>";

$stream .= "<tr>	<td valign='top'>TERBILANG</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".ucwords(strtolower($d['terbilang']))." Rupiah</td>
		</tr>";

	if ($d['ppn'] == '0') {
		$stream .= "<tr>	<td valign='top'>&nbsp;</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>*PPN Tidak Dipungut Sesuai PP Tempat Penimbunan Berikat*</td>
		</tr>";

	}

$stream .= "<tr>	<td>LOKASI PENYERAHAN</td>
			<td width='10'>:</td>
			<td>".$d['pelabuhan']."</td>
		</tr>";

	$tglKirim = explode('-', $d['tanggalkirim']);
	$tglSd = explode('-', $d['sdtanggal']);
	$nmBlnKirim = numToMonth($tglKirim[1], 'I', 'long');
	$nmBlnSd = numToMonth($tglSd[1], 'I', 'long');
	$tglisiKirim = $tglKirim[2] . ' ' . $nmBlnKirim . ' ' . $tglKirim[0];
	$tglisiSd = $tglSd[2] . ' ' . $nmBlnSd . ' ' . $tglSd[0];

$stream .= "<tr>	<td valign='top'>WAKTU PENYERAHAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".tanggalnormal($d['tanggalkirim'])." s/d. ".tanggalnormal($d['sdtanggal'])."</td>
		</tr>";

	$awan = stripslashes($d['syratpembayaran']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);

$stream .= "<tr>	<td valign='top'>PEMBAYARAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".str_replace('dan', '&', $awan)."</td>
		</tr>";

$stream .= "<tr>	<td valign='top'>SYARAT PENYERAHAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$d['tipemuat'] . " - " .$d['keterangan_muat']."</td>
		</tr>";
	
	$awan = stripslashes($d['standartimbangan']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);

$stream .= "<tr>	<td valign='top'>DASAR TIMBANGAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".$awan."</td>
		</tr>";


$stream .= "<tr>	<td valign='top'>TOLERANSI SUSUT</td>
			<td width='10' valign='top'>:</td>";

	if( $d['toleransi'] == 0){
		$stream .="<td valign='top'>-</td></tr>";
	}else{
		$stream .="<td valign='top'>".$d['toleransi']." %, akan diklaim full apabila susut di atas 0,5% per truck </td></tr>";
	}


$stream .= "<tr>	<td valign='top'>CATATAN</td>
			<td width='10' valign='top'>:</td>
			<td valign='top'>".str_replace('dan', '&', $d['catatan1'])."</td></tr>";

	if($d['catatan2']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan2'])."</td></tr>";
	}
	if($d['catatan3']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan3'])."</td></tr>";
	}
	if($d['catatan4']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan4'])."</td></tr>";
	}
	if($d['catatan5']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan5'])."</td></tr>";
	}
	if($d['catatan6']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan6'])."</td></tr>";
	}
	if($d['catatan7']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan7'])."</td></tr>";
	}
	if($d['catatan8']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan8'])."</td></tr>";
	}
	if($d['catatan9']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan9'])."</td></tr>";
	}
	if($d['catatan10']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan10'])."</td></tr>";
	}
	if($d['catatan11']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan11'])."</td></tr>";
	}
	if($d['catatan12']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan12'])."</td></tr>";
	}
	if($d['catatan13']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan13'])."</td></tr>";
	}
	if($d['catatan14']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan14'])."</td></tr>";
	}
	if($d['catatan15']!="") {
		$stream .= "<tr><td>&nbsp;</td><td width='10'></td><td valign='top'>".str_replace('dan', '&', $d['catatan15'])."</td></tr>";
	}


	$nmPt = '-';
	$namaBank = "-";
	$namaBankCabang = "-";
	$noRekeningBank = "-";

	$queryGet5Rekening = "SELECT * FROM 
								pmn_5rekening 
							WHERE 
								kodeorg like '".$kodept."%' 
							AND 
								jenis_product = '".$kodebarang."' ";
	$data5Rekening = fetchData($queryGet5Rekening); 

	if (!empty($data5Rekening[0])) {
		$nmPt = $data5Rekening[0]['penjelasan1'];
		$namaBank = $data5Rekening[0]['penjelasan2'];
		$noRekeningBank = $data5Rekening[0]['no_rekening'];
	}

$stream .= "<tr>
		<td colspan='3' valign='top'>TRANSFER KE REKENING : </td>
		</tr><tr>
		<td colspan='3' valign='top'> AC : ".$noRekeningBank."</td>
		</tr><tr>
		<td colspan='3' valign='top'> a/n : ".$nmPt."</td>
		</tr><tr>
		<td colspan='3' valign='top'> a/n : ".$namaBank."</td></tr>";

	$tglTtd = explode('-', $d['tanggalkontrak']);
	$nmBlnTtd = numToMonth($tglTtd[1], 'I', 'long');
	$tglisiTtd = $tglTtd[2] . ' ' . date('F', strtotime($tglTtd[0])) . ' ' . $tglTtd[0];
$stream .= "<tr><td colspan='3'></td></tr>";
$stream .= "<tr><td colspan='3'>".$lokasiTtd." ".tgl_indo(date($d['tanggalkontrak']))."</td></tr>";
$stream .= "<tr><td colspan='2' align='center'>PIHAK PENJUAL</td><td align='center'>PIHAK PEMBELI</td></tr>";
$stream .= "<tr><td colspan='2' align='center'>".$namapt."</td><td align='center'>".$q['namacustomer']."</td></tr>";
$stream .= "<tr><td colspan='3'></td></tr>";
$stream .= "<tr><td colspan='2' align='center'>".$d['penandatangan']."</td><td align='center'>".$d['tanda_tangan_pembeli']."</td></tr>";
$stream .= "</table>";
$wktu = date('Hms');

$nop_ = 'Kontrak_Penjualan'.$wktu.'__'.date('Y');

    $gztralala = gzopen('tempExcel/'.$nop_.'.xls.gz', 'w9');

    gzwrite($gztralala, $stream);

    gzclose($gztralala);

    echo "<script language=javascript1.2>\r\n        window.location='tempExcel/".$nop_.".xls.gz';\r\n        </script>";	


?>
