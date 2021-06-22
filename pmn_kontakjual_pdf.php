<?php
	require_once 'master_validation.php';
	require_once 'config/connection.php';
	require_once 'lib/eagrolib.php';
	require_once 'lib/fpdf.php';
	require_once 'lib/zLib.php';
	include_once 'lib/zMysql.php';
	include_once 'lib/terbilang.php';
	include_once 'lib/spesialCharacter.php';

	class PDF extends FPDF
	{
		public function Footer()
		{
			$this->SetY(-15);
			$this->SetFont('Arial', '', 8);
		}
	}

	$table = $_GET['table'];
	$column = $_GET['column'];
	$where = $_GET['cond'];
	$nmOrg = makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
	$nmBrg = makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
	$mtUang = makeOption($dbname, 'setup_matauang', 'kode,simbol');

	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddFont('Calibri','','calibri.php');
	$pdf->AddPage();
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

	if(!empty($logo)){
		// $pdf->Image($logo, 15, 5, 35, 20);
		$pdf->Image($logo,10,5,20);
	}

	$pdf->SetFont('Arial', 'B', 12);
	$pdf->SetFillColor(255, 255, 255);
	$pdf->SetY(6);
	$pdf->SetX(40);
	$pdf->Cell(60, 5, $namapt, 0, 1, 'L');
	$pdf->SetFont('Arial', 'B', 9);
	$pdf->SetX(40);
	$pdf->Cell(60, 5, $alamatpt, 0, 1, 'L');
	$pdf->SetX(40);
	$pdf->Cell(60, 5, $kotatelp, 0, 1, 'L');
	//$pdf->SetX(55);
	//$pdf->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
	$pdf->SetFont('Arial', '', 9);
	$pdf->SetY(18);
	$pdf->Ln();
	$pdf->SetFont('Arial', 'BU', '9');
	//$pdf->Cell(200, 5, 'SURAT PERJANJIAN JUAL BELI', 0, 1, 'C');
	$pdf->Cell(200, 5, 'KONTRAK JUAL BELI', 0, 1, 'C');
	$pdf->SetFont('Arial', '', '9');
	$pdf->Cell(200, 5, 'No. ' . $d['nokontrak'], 0, 1, 'C');
	$pdf->Ln();
	$x = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
	$y = mysql_query($x);
	$z = mysql_fetch_assoc($y);
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'PENJUAL', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(10, 5, $z['namaorganisasi'], 0, 1, 'L');
	$pdf->Cell(40, 5, 'ALAMAT', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $z['alamat'], 0, 1, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(45, 5, '', 0, 0, 'L');
	$pdf->Cell(10, 5, $z['wilayahkota'], 0, 1, 'L');
	$xx = 'select * from ' . $dbname . '.setup_org_npwp where kodeorg=\'' . $d['kodept'] . '\'';
	$yy = mysql_query($xx);
	$zz = mysql_fetch_assoc($yy);
	$pdf->Cell(40, 5, 'NPWP', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $zz['npwp'], 0, 1, 'L');
	$o = 'select * from ' . $dbname . '.pmn_4customer where kodecustomer=\'' . $d['koderekanan'] . '\'';
	$p = mysql_query($o);
	$q = mysql_fetch_assoc($p);
	

	$pdf->Cell(40, 5, 'PEMBELI', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $q['namacustomer'], 0, 1, 'L');
	$pdf->Cell(40, 5, 'ALAMAT', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $q['alamat'], 0, 1, 'L');
	$pdf->Cell(40, 5, 'NPWP', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $q['npwp'], 0, 1, 'L');
	$pdf->Cell(40, 5, 'JENIS BARANG', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(10, 5, $barang, 0, 1, 'L');

	$pdf->Cell(40, 5, 'MUTU', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$awan = stripslashes($d['kualitas']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);
	$pdf->Cell(10, 5, str_replace("dan", "&", $awan), 0, 1, 'L');

	$tempAngka = number_format($d['kuantitaskontrak']);
	$temp1 = str_replace(".","X",$tempAngka);
	$temp2 = str_replace(",","Y",$temp1);
	$temp1 = str_replace("X",",",$temp2);
	$temp2 = str_replace("Y",".",$temp1);
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'BANYAKNYA (Kwantiti)', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5,  $temp2 . ' ' . $d['satuan'], 0, 1, 'L');

	$pdf->Cell(40, 5, 'TERBILANG', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(60, 5, ucwords(strtolower(terbilang($d['kuantitaskontrak'],1) . ' KILOGRAM')), 0, 1, 'L');


	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'NO. DO', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->MultiCell(125, 5, $d['nodo'], 0, 'L');

	if ($d['ppn'] != '0') {
		$descppn = ' /Kg (Inc. PPN '.$d['ppn'].'%)';
		$ppnx = number_format($d['hargasatuan']+($d['hargasatuan']*$d['ppn']/100),2);
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
	
	
	$pdf->Cell(40, 5, 'HARGA SATUAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(10, 5, 'Rp. ' . $ppnx . '' . $descppn, 0, 1, 'L');

	
	$tempAngka = number_format($d['grand_total'],2);
	$temp1 = str_replace(".","X",$tempAngka);
	$temp2 = str_replace(",","Y",$temp1);
	$temp1 = str_replace("X",",",$temp2);
	$temp2 = str_replace("Y",".",$temp1);
	$temp2 = str_replace(",00",",-",$temp2);
	$pdf->Cell(40, 5, 'JUMLAH HARGA', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(60, 5, "Rp ".$temp2, 0, 1, 'L');

	$pdf->Cell(40, 5, 'TERBILANG', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(60, 5, ucwords(strtolower($d['terbilang'] . ' RUPIAH')), 0, 1, 'L');

	if ($d['ppn'] == '0') {
		$pdf->Cell(40, 5, '', 0, 0, 'L');
		$pdf->Cell(5, 5, '', 0, 0, 'L');
		$pdf->Cell(60, 5, "*PPN Tidak Dipungut Sesuai PP Tempat Penimbunan Berikat*", 0, 1, 'L');
	}

	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'LOKASI PENYERAHAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->MultiCell(125, 5, $d['pelabuhan'] , 0, 'J');
	$pdf->Cell(40, 5, 'WAKTU PENYERAHAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');

	$pdf->SetFont('Arial', '', '8');
	$tglKirim = explode('-', $d['tanggalkirim']);
	$tglSd = explode('-', $d['sdtanggal']);
	$nmBlnKirim = numToMonth($tglKirim[1], 'I', 'long');
	$nmBlnSd = numToMonth($tglSd[1], 'I', 'long');
	$tglisiKirim = $tglKirim[2] . ' ' . $nmBlnKirim . ' ' . $tglKirim[0];
	$tglisiSd = $tglSd[2] . ' ' . $nmBlnSd . ' ' . $tglSd[0];
	//$pdf->Cell(10, 5, date('d F Y', strtotime($tglisiKirim[3])), 0, 1, 'L');
	$pdf->Cell(10, 5, tanggalnormal($d['tanggalkirim']).' s/d. '.tanggalnormal($d['sdtanggal']), 0, 1, 'L');

	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'PEMBAYARAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$awan = stripslashes($d['syratpembayaran']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);
	$pdf->Cell(60, 5, str_replace("dan", "&", $awan), 0, 1, 'L');
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(45, 5, '', 0, 0, 'L');
	$awan = stripslashes($d['syratpembayaran2']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);
	$pdf->Cell(10, 5, str_replace("dan", "&", $awan), 0, 1, 'L');


	$pdf->SetFont('Arial', '', '8');

	$pdf->Cell(40, 5, 'SYARAT PENYERAHAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->MultiCell(150, 5,$d['tipemuat'] . " - " .$d['keterangan_muat'], 0, 1, 'L');
	
	$awan = stripslashes($d['standartimbangan']);
	$awan = iconv('UTF-8', 'windows-1252', $awan);
	$pdf->Cell(40, 5, 'DASAR TIMBANGAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	$pdf->Cell(60, 5,$awan, 0, 1, 'L');

	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'TOLERANSI SUSUT', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	if( $d['toleransi'] == 0){
		$pdf->Cell(60, 5, '-', 0, 1, 'L');
	}else{
		$pdf->Cell(60, 5, $d['toleransi'] . '%, akan diklaim full apa bila susut di atas 0,5% per truck', 0, 1, 'L');
	}

	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(40, 5, 'CATATAN', 0, 0, 'L');
	$pdf->Cell(5, 5, ':', 0, 0, 'L');
	if($d['catatan1']!="") {$pdf->MultiCell(150, 5,$d['catatan1'], 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan2']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan2']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan3']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan3']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan4']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan4']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan5']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan5']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan6']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan6']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan7']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan7']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan8']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan8']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan9']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan9']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan10']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan10']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan11']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan11']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan12']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan12']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan13']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan13']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan14']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan14']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	if($d['catatan15']!="") {$pdf->MultiCell(150, 5, str_replace("dan", "&", $d['catatan15']), 0, 1, 'L'); $pdf->Cell(45, 5, '', 0, 0, 'L');}
	$pdf->Ln();

	$nmPt = '-';
	$namaBank = "-";
	$namaBankCabang = "-";
	$noRekeningBank = "-";

	$queryGet5Rekening = "	SELECT 
								* 
							FROM 
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

	$pdf->Cell(10, 5, 'Pembayaran via transfer dapat dilakukan melalui :', 0, 1, 'L');
	$pdf->Cell(10, 5, 'AC : '.$noRekeningBank.' '.$namaBank, 0, 1, 'L');
	$pdf->Cell(10, 5, 'a/n : '.$nmPt, 0, 1, 'L');
	$pdf->Ln();

	$tglTtd = explode('-', $d['tanggalkontrak']);
	$nmBlnTtd = numToMonth($tglTtd[1], 'I', 'long');
	$tglisiTtd = $tglTtd[2] . ' ' . date('F', strtotime($tglTtd[0])) . ' ' . $tglTtd[0];
	
	$pdf->SetFont('Arial', '', '8');
	$pdf->Cell(10, 5, $lokasiTtd . ', ' . tgl_indo(date($d['tanggalkontrak'])), 0, 1, 'L');
	
	$pdf->Cell(10, 5, 'PIHAK PENJUAL', 0, 0, 'L');
	$pdf->SetX(155);
	$pdf->Cell(10, 5, 'PIHAK PEMBELI', 0, 1, 'L');

	$pdf->SetFont('Arial', '', '8');

	$pdf->Cell(10, 5, $namapt, 0, 0, 'L');
	$pdf->SetX(155);
	$pdf->Cell(10, 5, $q['namacustomer'], 0, 1, 'L');
	$pdf->Ln(15);
	$pdf->Cell(10, 5, $d['penandatangan'], 0, 0, 'L');
	$pdf->SetX(155);
	$pdf->Cell(10, 5, $d['tanda_tangan_pembeli'], 0, 1, 'L');
	$pdf->Ln(25);
	
	$pdf->Output();

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

		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun
	 
		return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
	}

?>
