<?php

require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/fpdf.php';

include_once 'lib/zMysql.php';

$table = $_GET['table'];

$column = $_GET['column'];

$where = $_GET['cond'];



class PDF extends FPDF

{

	public function Header()

	{

		global $conn;

		global $dbname;

		global $userid;

		global $posted;

		global $tanggal;

		global $norek_sup;

		global $npwp_sup;

		global $nm_kary;

		global $nm_pt;

		global $nmSupplier;

		global $almtSupplier;

		global $tlpSupplier;

		global $faxSupplier;

		global $nopo;

		global $tglPo;

		global $kdBank;

		global $an;

		$str = 'select kodeorg,kodesupplier,purchaser,nopo,tanggal from ' . $dbname . '.log_poht  where nopo=\'' . $_GET['column'] . '\'';

		$res = mysql_query($str);

		$bar = mysql_fetch_object($res);



		if ($bar->kodeorg == '') {

			$bar->kodeorg = $_SESSION['org']['kodeorganisasi'];

		}



		$str1 = 'select namaorganisasi,alamat,wilayahkota,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeorg . '\'';

		$res1 = mysql_query($str1);



		while ($bar1 = mysql_fetch_object($res1)) {

			$namapt = $bar1->namaorganisasi;

			$alamatpt = $bar1->alamat . ', ' . $bar1->wilayahkota;

			$telp = $bar1->telepon;

		}



		$sNpwp = 'select npwp,alamatnpwp from ' . $dbname . '.setup_org_npwp where kodeorg=\'' . $bar->kodeorg . '\'';



		#exit(mysql_error());

		($qNpwp = mysql_query($sNpwp)) || true;

		$rNpwp = mysql_fetch_assoc($qNpwp);

		$sql = 'select * from ' . $dbname . '.log_5supplier where supplierid=\'' . $bar->kodesupplier . '\'';



		#exit(mysql_error());

		($query = mysql_query($sql)) || true;

		$res = mysql_fetch_object($query);

		$sql2 = 'select namakaryawan from ' . $dbname . '.datakaryawan where karyawanid=\'' . $bar->purchaser . '\'';



		#exit(mysql_error());

		($query2 = mysql_query($sql2)) || true;

		$res2 = mysql_fetch_object($query2);

		$sql3 = 'select namaorganisasi from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeorg . '\'';



		#exit(mysql_error());

		($query3 = mysql_query($sql3)) || true;

		$res3 = mysql_fetch_object($query3);

		$norek_sup = $res->rekening;

		$kdBank = $res->bank;

		$npwp_sup = $res->npwp;

		$an = $res->an;

		$nm_kary = $res2->namakaryawan;

		$nm_pt = $res3->namaorganisasi;

		$nopo = $bar->nopo;

		$tglPo = tanggalnormal($bar->tanggal);

		$nmSupplier = $res->namasupplier;

		$almtSupplier = $res->alamat;

		$tlpSupplier = $res->telepon;

		$faxSupplier = $res->fax;

		$kota = $res->kota;

		$this->SetMargins(15, 10, 0);



		if ($_SESSION['org']['kodeorganisasi'] == 'SSP' ) {

			$path = 'images/SSP_logo.jpg';

		}

		else if ($_SESSION['org']['kodeorganisasi'] == 'MJR') {

			$path = 'images/MI_logo.jpg';

		}

		else if ($_SESSION['org']['kodeorganisasi'] == 'HSS') {

			$path = 'images/HS_logo.jpg';

		}

		else if ($_SESSION['org']['kodeorganisasi'] == 'BNM') {

			$path = 'images/BM_logo.jpg';

		}



		if($path!=''){
			$this->Image($path, 15, 9, 35, 24);
		}

		$this->SetFont('Arial', 'B', 9);

		$this->SetFillColor(255, 255, 255);

		$this->SetX(55);

		$this->Cell(60, 5, $namapt, 0, 1, 'L');

		$this->SetX(55);

		$this->Cell(60, 5, $alamatpt, 0, 1, 'L');

		$this->SetX(55);

		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');

		$this->SetFont('Arial', 'B', 7);

		$this->SetX(55);

		$this->Cell(60, 5, 'NPWP: ' . $rNpwp['npwp'], 0, 1, 'L');

		$this->SetX(55);

		$this->Cell(60, 5, $_SESSION['lang']['alamat'] . ' NPWP: ' . $rNpwp['alamatnpwp'], 0, 1, 'L');

		$this->Line(15, 35, 205, 35);

		$this->SetFont('Arial', '', 6);

		$this->SetX(163);

		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s'), 0, 1, 'L');

	}



	public function Footer()

	{

		$this->SetY(-15);

		$this->SetFont('Arial', 'I', 8);

		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');

	}

}



require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/fpdf.php';

include_once 'lib/zMysql.php';

$table = $_GET['table'];

$column = $_GET['column'];

$where = $_GET['cond'];

$pdf = new PDF('P', 'mm', 'A4');

$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 8);



if ($_SESSION['language'] == 'EN') {

	$pdf->Cell(30, 4, 'TO :', 0, 0, 'L');

}

else {

	$pdf->Cell(30, 4, 'KEPADA YTH :', 0, 0, 'L');

}



$pdf->Ln();

$pdf->Cell(35, 4, $_SESSION['lang']['nm_perusahaan'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $nmSupplier, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['alamat'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $almtSupplier, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['telp'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $tlpSupplier, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['fax'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $faxSupplier, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['namabank'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $kdBank . ' ' . $kdBank, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['norekeningbank'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $an . ' ' . $norek_sup, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['npwp'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $npwp_sup, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['kota'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $kota, 0, 1, 'L');

$pdf->SetFont('Arial', 'U', 12);

$ar = $pdf->GetY();

$pdf->SetY($ar + 5);

$pdf->Cell(190, 5, strtoupper('Purchase Order'), 0, 1, 'C');

$pdf->SetY($ar + 12);

$pdf->SetFont('Arial', '', 9);

$pdf->Cell(10, 4, $_SESSION['lang']['nopo'], 0, 0, 'L');

$pdf->Cell(20, 4, ': ' . $nopo, 0, 0, 'L');

$pdf->SetX(163);

$pdf->Cell(20, 4, $_SESSION['lang']['tanggal'], 0, 0, 'L');

$pdf->Cell(20, 4, ': ' . $tglPo, 0, 0, 'L');

$pdf->SetY($ar + 17);

$pdf->SetFont('Arial', 'B', 9);

$pdf->SetFillColor(220, 220, 220);

$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);

$pdf->Cell(72, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);

$pdf->Cell(29, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C', 1);

$pdf->Cell(12, 5, $_SESSION['lang']['nopp'], 1, 0, 'C', 1);

$pdf->Cell(12, 5, $_SESSION['lang']['untukunit'], 1, 0, 'C', 1);

$pdf->Cell(15, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);

$pdf->Cell(14, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);

$pdf->Cell(26, 5, '', 1, 1, 'C', 1);

$pdf->SetFillColor(255, 255, 255);

$pdf->SetFont('Arial', '', 8);

$str = 'select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ' . $dbname . '.log_podt a inner join ' . $dbname . '.log_poht b on a.nopo=b.nopo  where a.nopo=\'' . $_GET['column'] . '\'';

$re = mysql_query($str);

$no = 0;



while ($bar = mysql_fetch_object($re)) {

	$no += 1;

	$kodebarang = $bar->kodebarang;

	$jumlah = $bar->jumlahpesan;

	$harga_sat = $bar->hargasbldiskon;

	$total = ($jumlah * $harga_sat) + $bar->ongkangkut;

	$unit = substr($bar->nopp, 15, 4);

	$namabarang = '';

	$nopp = substr($bar->nopp, 0, 3);

	$strv = 'select b.spesifikasi from  ' . $dbname . '.log_5photobarang b  where b.kodebarang=\'' . $bar->kodebarang . '\'';

	$resv = mysql_query($strv);

	$barv = mysql_fetch_object($resv);



	if ($barv->spesifikasi != '') {

		$spek = $barv->spesifikasi . "\n";

	}

	else {

		$spek = '';

	}



	$sSat = 'select satuan,namabarang,kodebarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';



	#exit(mysql_error());

	($qSat = mysql_query($sSat)) || true;

	$rSat = mysql_fetch_assoc($qSat);

	$satuan = $rSat['satuan'];

	$namabarang = $rSat['namabarang'];

	$kdBrg = $rSat['kodebarang'];

	++$i;



	if ($no != 1) {

		$pdf->SetY($akhirY);

	}



	$posisiY = $pdf->GetY();

	$pdf->Cell(8, 4, $no, 0, 0, 'L', 0);

	$pdf->SetX($pdf->GetX());

	$pdf->MultiCell(72, 5, $namabarang . "\n" . $spek . $bar->catatan, 0, 'J', 0);

	$akhirY = $pdf->GetY();

	$pdf->SetY($posisiY);

	$pdf->SetX($pdf->GetX() + 82);

	$pdf->Cell(29, 5, $kdBrg, 0, 0, 'L', 0);

	$pdf->Cell(12, 5, $nopp, 0, 0, 'C', 0);

	$pdf->Cell(12, 5, $unit, 0, 0, 'C', 0);

	$pdf->Cell(14, 5, number_format($jumlah, 2, '.', ','), 0, 0, 'R', 0);

	$pdf->Cell(14, 5, $barv->satuan, 0, 0, 'C', 0);

	$pdf->Cell(29, 5, '', 0, 0, 'R', 0);

	$pdf->Cell(26, 5, '', 0, 1, 'R', 0);



	if ($i == 20) {

		$i = 0;

		$akhirY = $akhirY - 20;

		$akhirY = $pdf->GetY() - $akhirY;

		$akhirY = $akhirY + 25;

		$pdf->AddPage();

	}

}



$akhirSubtot = $pdf->GetY();

$pdf->SetY($akhirY);

$slopoht = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $_GET['column'] . '\'';



#exit(mysql_error());

($qlopoht = mysql_query($slopoht)) || true;

$rlopoht = mysql_fetch_object($qlopoht);

$sb_tot = $rlopoht->subtotal;

$nil_diskon = $rlopoht->nilaidiskon;

$nppn = $rlopoht->ppn;

$stat_release = $rlopoht->stat_release;

$user_release = $rlopoht->useridreleasae;

$gr_total = ($sb_tot - $nil_diskon) + $nppn;

$pdf->MultiCell(133, 4, $_SESSION['lang']['keterangan'] . ' :' . "\n" . $rlopoht->uraian, 'T', 1, 'J', 0);

$pdf->SetY($akhirY);

$pdf->SetX($pdf->GetX() + 131);

$pdf->Cell(29, 5, '', 'T', 0, 'L', 1);

$pdf->Cell(26, 5, '', 'T', 1, 'R', 1);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 131);

$pdf->Cell(29, 5, '', 0, 0, 'L', 1);

$pdf->Cell(26, 5, '', 0, 1, 'R', 1);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 131);

$pdf->Cell(29, 5, '', 0, 0, 'L', 1);

$pdf->Cell(26, 5, '', 0, 1, 'R', 1);

$pdf->SetFont('Arial', 'B', 8);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 131);

$pdf->Cell(29, 5, '', 0, 0, 'L', 1);

$pdf->Cell(26, 5, '', 0, 1, 'R', 1);



if (350 < strlen($rlopoht->uraian)) {

	$tmbhBrs = 60;

	$tmbhBrs2 = 85;

	$tmbhBrs3 = 55;

	$tmbhBrs5 = 115;

}

else {

	$tmbhBrs = 30;

	$tmbhBrs2 = 55;

	$tmbhBrs3 = 45;

	$tmbhBrs5 = 85;

}



$pdf->SetY($akhirY + $tmbhBrs);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(35, 4, $_SESSION['lang']['syaratPem'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $rlopoht->syaratbayar, 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['tgl_kirim'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . tanggalnormald($rlopoht->tanggalkirim), 0, 1, 'L');

if (is_null($rlopoht->idFranco) || ($rlopoht->idFranco == '') || ($rlopoht->idFranco == 0)) {

	$pdf->Cell(35, 4, $_SESSION['lang']['almt_kirim'], 0, 0, 'L');

	$pdf->Cell(40, 4, ': ' . $rlopoht->lokasipengiriman, 0, 1, 'L');

}

else {

	$sFr = 'select * from ' . $dbname . '.setup_franco where id_franco=\'' . $rlopoht->idFranco . '\'';



	#exit(mysql_error());

	($qFr = mysql_query($sFr)) || true;

	$rFr = mysql_fetch_assoc($qFr);

	$pdf->Cell(35, 4, $_SESSION['lang']['almt_kirim'], 0, 0, 'L');

	$pdf->Cell(40, 4, ': ' . $rFr['alamat'], 0, 1, 'L');

	$pdf->Cell(35, 4, 'Kontak Person', 0, 0, 'L');

	$pdf->Cell(40, 4, ': ' . $rFr['contact'], 0, 1, 'L');

	$pdf->Cell(35, 4, 'Telp / Handphone No.', 0, 0, 'L');

	$pdf->Cell(40, 4, ': ' . $rFr['handphone'], 0, 1, 'L');

}



$pdf->SetY($akhirY + $tmbhBrs2);

$pdf->Cell(185, 4, $nm_pt, 0, 0, 'R');

$pdf->SetY($akhirY + $tmbhBrs3);

$pdf->SetY($akhirY + $tmbhBrs5);

$sPo = 'select persetujuan1 from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';



#exit(mysql_error($conn));

($qPo = mysql_query($sPo)) || true;

$rPo = mysql_fetch_assoc($qPo);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(10, 4, strtoupper($_SESSION['lang']['purchaser']) . ': ' . strtoupper($nm_kary), 0, 0, 'L', 0);

$sql_kry = 'select namakaryawan, b.namajabatan from ' . $dbname . '.datakaryawan a inner join ' . $dbname . '.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where a.karyawanid=\'' . $rPo['persetujuan1'] . '\' ';



#exit(mysql_error());

($query_kry = mysql_query($sql_kry)) || true;

$resv = mysql_fetch_assoc($query_kry);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(175, 4, strtoupper($resv['namakaryawan']), 0, 0, 'R', 0);

$pdf->Ln();

$akrhr = $tmbhBrs5 + 5;

$pdf->SetY($akhirY + $akrhr);

$pdf->SetFont('Arial', 'I', 8);

$pdf->Cell(10, 4, $_SESSION['lang']['fyiGudang'], 0, 0, 'L', 0);

$pdf->Output();



?>

