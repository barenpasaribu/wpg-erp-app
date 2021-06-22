<?php



require_once 'master_validation.php';

require_once 'config/connection.php';

require_once 'lib/eagrolib.php';

require_once 'lib/fpdf.php';

include_once 'lib/zMysql.php';

include_once 'lib/zLib.php';
require_once 'lib/terbilang.php';

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

		global $namapt;

		global $nmSupplier;

		global $almtSupplier;

		global $tlpSupplier;

		global $faxSupplier;

		global $nopo;

		global $tglPo;

		global $kdBank;

		global $an;

		global $optNmkry;

		global $nama_persetujuan1;

		global $waktu_persetujuan1;

		global $nama_persetujuan2;

		global $waktu_persetujuan2;

		$optNmkry = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

		$str = 'select kodeorg,kodesupplier,purchaser,nopo,tanggal,useridapprovepo as persetujuan1,useridreleasae as persetujuan2,tglapprovepo as tglp1,tglrelease as tglp2,wktapprovepo as wktp1,wktrelease as wktp2 from ' . $dbname . '.log_poht  where nopo=\'' . $_GET['column'] . '\'';

		$res = mysql_query($str);

		$bar = mysql_fetch_object($res);


		//cari approval 1

//		$nama_persetujuan1 = "";

//		$waktu_persetujuan1 = "";

//		if( $bar->persetujuan1 != ""){

			$str2 = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid = '".$bar->persetujuan1."'";

			$res2 = mysql_query($str2);

			$bar2 = mysql_fetch_object($res2);

			$nama_persetujuan1 = $bar2->namakaryawan;			

			$waktu_persetujuan1 = $bar->tglp1." ".$bar->wktp1;

//		}

		//cari approval 2

//		$nama_persetujuan2 = "";

//		$waktu_persetujuan2 = "";

//		if( $bar->persetujuan2 != "" ){

			$str3 = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->persetujuan2."'";

			$res3 = mysql_query($str3);

			$bar3 = mysql_fetch_object($res3);

			$nama_persetujuan2 = $bar3->namakaryawan;

			$waktu_persetujuan2 = $bar->tglp2." ".$bar->wktp2; 

//		}



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

		$this->SetMargins(15, 5, 0);

		//$path = $_SESSION['org']['logo'];

		//$this->Image($path, 15, 5, 35, 20);

		$this->SetFont('Arial', 'B', 9);

		$this->SetFillColor(255, 255, 255);

		$this->SetX(10);

		$this->Cell(125, 5, $namapt, 0, 0, 'L');
		$this->Cell(10, 4, 'No.PO', 0, 0, 'L');

		$this->Cell(20, 4, ': ' . $nopo, 0, 1, 'L');

		//$this->SetX(55);

		//$this->Cell(60, 5, $alamatpt, 0, 1, 'L');

		//$this->SetX(55);

		//$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');

		$this->SetX(10);

		$this->Cell(60, 5, 'NPWP: ' . $rNpwp['npwp'], 0, 1, 'L');

		

		$this->SetFont('Arial', '', 6);

		

	}



	public function Footer()

	{

		$this->SetY(-15);

		$this->SetFont('Arial', 'I', 8);

		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');

	}
}


$table = $_GET['table'];

$column = $_GET['column'];

$where = $_GET['cond'];

$pdf = new PDF('P', 'mm', 'A4');

$pdf->AddPage();

$pdf->SetFont('courier', 'B', 8);
$pdf->SetX(10);

$pdf->Cell(10, 4, 'KEPADA YTH :', 0, 0, 'L');

//$pdf->Ln();
$pdf->SetX(135);

$pdf->Cell(35, 4, "Approval 1", 0, 0, 'L');

$pdf->Cell(40, 4, ': ' .$nama_persetujuan1, 0, 1, 'L');

$pdf->SetX(10);
$pdf->Cell(35, 4, $_SESSION['lang']['nm_perusahaan'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $nmSupplier , 0, 0, 'L');

$pdf->SetX(135);

$pdf->Cell(35, 4, "Waktu Approval 1", 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $waktu_persetujuan1, 0, 1, 'L');

$pdf->SetX(10);
$pdf->Cell(35, 4, $_SESSION['lang']['alamat'], 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $almtSupplier, 0, 0, 'L');

$pdf->SetX(135);

$pdf->Cell(35, 4, "Approval 2", 0, 0, 'L');

$pdf->Cell(40, 4, ': ' . $nama_persetujuan2 , 0, 1, 'L');


//$pdf->Cell(35, 4, $_SESSION['lang']['telp'], 0, 0, 'L');

//$pdf->Cell(40, 4, ': ' . $tlpSupplier, 0, 0, 'L');
$pdf->Cell(35, 4, '', 0, 0, 'L');

$pdf->Cell(40, 4, '' , 0, 0, 'L');

	$pdf->SetX(135);

	$pdf->Cell(35, 4, "Waktu Approval 2", 0, 0, 'L');

	$pdf->Cell(40, 4, ': ' . $waktu_persetujuan2 , 0, 1, 'L');


//$pdf->Cell(35, 4, $_SESSION['lang']['fax'], 0, 0, 'L');
//$pdf->Cell(40, 4, ': ' . $faxSupplier, 0, 1, 'L');

//$pdf->Cell(35, 4, $_SESSION['lang']['kota'], 0, 0, 'L');

//$pdf->Cell(40, 4, ': ' . $kota, 0, 1, 'L');

$pdf->SetFont('Arial', 'U', 10);

$ar = round($pdf->GetY());

$pdf->SetY($ar + 2);

$pdf->Cell(190, 5, strtoupper('Purchase Order'), 0, 1, 'C');

$pdf->SetY($ar + 10);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(10, 4, ' ', 0, 0, 'L');

$pdf->Cell(20, 4, '' , 0, 0, 'L');

$pdf->SetX(135);

$pdf->Cell(20, 4, $_SESSION['lang']['tanggal'] . ' PO.', 0, 0, 'L');

$pdf->Cell(20, 4, ': ' . $tglPo, 0, 0, 'L');

$pdf->SetY($ar + 14);

$pdf->SetFont('Arial', 'B', 8);

$pdf->SetFillColor(220, 220, 220);

$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);



$pdf->Cell(60, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);

$pdf->Cell(12, 5, $_SESSION['lang']['nopp'], 1, 0, 'C', 1);

$pdf->Cell(15, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);

$pdf->Cell(14, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);

$pdf->Cell(29, 5, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);

$pdf->Cell(26, 5, 'Total', 1, 1, 'C', 1);

$pdf->SetFillColor(255, 255, 255);

$pdf->SetFont('Arial', '', 8);

$str = 'select a.*,b.kodesupplier,b.subtotal,b.diskonpersen,b.tanggal,b.nilaidiskon,b.ppn,b.nilaipo,b.tanggalkirim,b.lokasipengiriman,b.uraian,b.matauang from ' . $dbname . '.log_podt a inner join ' . $dbname . '.log_poht b on a.nopo=b.nopo  where a.nopo=\'' . $_GET['column'] . '\'';

$re = mysql_query($str);

$no = 0;



while ($bar = mysql_fetch_object($re)) {

	$no += 1;

	$kodebarang = $bar->kodebarang;

	$jumlah = floatval($bar->jumlahpesan);

	$harga_sat = $bar->hargasbldiskon;

	$total = $jumlah * $harga_sat;

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



	$sSat = 'select satuan,namabarang from ' . $dbname . '.log_5masterbarang where kodebarang=\'' . $bar->kodebarang . '\'';



	#exit(mysql_error());

	($qSat = mysql_query($sSat)) || true;

	$rSat = mysql_fetch_assoc($qSat);

	$satuan = $rSat['satuan'];

	$namabarang = $rSat['namabarang'];

	++$i;



	if ($no != 1) {

		$pdf->SetY($akhirY);

	}



	$akhirY = $pdf->GetY();



	if (260 <= $akhirY) {

		$pdf->AddPage();

		$akhirY = $pdf->GetY();

	}



	$pdf->Cell(8, 4, $no, 0, 0, 'L', 0);

	$pdf->SetX($pdf->GetX());

	$posisiY = round($pdf->GetY());

	

	$pdf->MultiCell(60, 5, ' ' . $namabarang . "\n" . $spek . $bar->catatan, 0, 'J', 0);

	$akhirY = $pdf->GetY();

	$pdf->SetY($posisiY);

	$pdf->SetX($pdf->GetX() + 69);

	$pdf->Cell(12, 5, $nopp, 0, 0, 'C', 0);

	$pdf->Cell(14, 5, number_format($jumlah, 2, '.', ','), 0, 0, 'R', 0);

	$pdf->Cell(14, 5, $bar->satuan, 0, 0, 'C', 0);

	$pdf->Cell(29, 5, $bar->matauang . ' ' . number_format($harga_sat, 2, '.', ','), 0, 0, 'R', 0);

	$desiomal = 2;

	$pdf->Cell(26, 5, number_format($total, 2, '.', ','), 0, 1, 'R', 0);

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

$user_release = $rlopoht->persetujuan1;

$gr_total = ($sb_tot - $nil_diskon) + $nppn;



if (240 <= $akhirSubtot) {

	$pdf->AddPage();

	$akhirY = $pdf->GetY();

}



$pdf->MultiCell(134, 4, 'Keterangan :' . "\n" . $rlopoht->uraian, 'T', 1, 'J', 0);

$pdf->SetY($akhirY);

$pdf->SetX($pdf->GetX() + 110);

$pdf->Cell(29, 5, $_SESSION['lang']['subtotal'], 'T', 0, 'L', 1);

$pdf->Cell(26, 5, number_format($rlopoht->subtotal, 2, '.', ','), 'T', 1, 'R', 1);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 110);

$pdf->Cell(29, 5, 'Discount' . ' (' . $rlopoht->diskonpersen . '% )', 0, 0, 'L', 1);

$pdf->Cell(26, 5, number_format($rlopoht->nilaidiskon, $desiomal, '.', ','), 0, 1, 'R', 1);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 110);

$pdf->Cell(29, 5, 'PPh/PPn (10 %)', 0, 0, 'L', 1);

$pdf->Cell(26, 5, number_format($rlopoht->ppn, $desiomal, '.', ','), 0, 1, 'R', 1);

$pdf->SetFont('Arial', 'B', 8);

$pdf->SetY($pdf->GetY());

$pdf->SetX($pdf->GetX() + 110);

$pdf->Cell(29, 5, $_SESSION['lang']['grnd_total'], 0, 0, 'L', 1);

$pdf->Cell(26, 5, $rlopoht->matauang . ' ' . number_format($gr_total, $desiomal, '.', ','), 0, 1, 'R', 1);



if (620 < strlen($rlopoht->uraian)) {

	$tmbhBrs = 80;

	$tmbhBrs2 = 105;

	$tmbhBrs3 = 75;

	$tmbhBrs5 = 135;

}

else {

	$tmbhBrs = 45;

	$tmbhBrs2 = 65;

	$tmbhBrs3 = 55;

	$tmbhBrs5 = 95;

}



if (175 <= $akhirY) {

	$akhirY = 0;

	$pdf->AddPage();

}



$pdf->SetY($akhirY + 20);

$pdf->SetFont('Arial', '', 8);

$pdf->SetFont('Arial', '', 8);

$pdf->Cell(126, 5, 'Terbilang ' . terbilang($gr_total), 0, 1, 'L', 0);

//$pdf->Cell(126, 5, $_SESSION['lang']['almt_kirim'] . ': ' . $rlopoht->lokasipengiriman, 0, 1, 'L', 0);

//$pdf->Cell(126, 5, $_SESSION['lang']['syaratPem'] . ': ' . $rlopoht->syaratbayar, 0, 1, 'L', 0);

//$pdf->Cell(126, 5, $_SESSION['lang']['norekeningbank'] . ': ' . $norek_sup, 0, 1, 'L', 0);

//$pdf->Cell(126, 5, $_SESSION['lang']['npwp'] . ': ' . $npwp_sup, 0, 1, 'L', 0);

//$pdf->Cell(126, 5, $_SESSION['lang']['purchaser'] . ': ' . $nm_kary, 0, 1, 'L', 0);

//$pdf->Cell(190, 4, $namapt, 0, 0, 'R');

$//pdf->SetY($akhirY + 25);

$sPo = 'select persetujuan1,persetujuan2 from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';



#exit(mysql_error($conn));

($qPo = mysql_query($sPo)) || true;

$rPo = mysql_fetch_assoc($qPo);

//$pdf->SetFont('Arial', '', 8);

//$pdf->Cell(10, 4, strtoupper($_SESSION['lang']['purchaser']) . ': ' . strtoupper($nm_kary), 0, 0, 'L', 0);

$pdf->SetFont('Arial', '', 8);

$sql_kry = 'select namakaryawan, b.namajabatan from ' . $dbname . '.datakaryawan a inner join ' . $dbname . '.sdm_5jabatan b on a.kodejabatan=b.kodejabatan where a.karyawanid=\'' . $user_release . '\' ';



#exit(mysql_error());

($query_kry = mysql_query($sql_kry)) || true;

$resv = mysql_fetch_assoc($query_kry);

//$pdf->SetFont('Arial', 'U', 9);

//$pdf->Cell(180, 4, strtoupper($resv['namakaryawan']), 0, 0, 'R');

//$pdf->Ln();

//$pdf->SetFont('Arial', '', 9);

//$pdf->Cell(190, 4, $resv['namajabatan'], 0, 0, 'R');

$akrhr = $tmbhBrs5 + 5;

$pdf->SetY($akhirY +25);

$pdf->SetFont('Arial', 'I', 8);

$pdf->Cell(10, 4,  'NB: Form ini dicetak secara otomatis oleh sistem, tidak perlu tanda tangan', 0, 0, 'L', 0);

$pdf->Output();



?>

