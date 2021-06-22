<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
require_once 'lib/terbilang.php';

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
		global $optNmkry;
		global $kota;
		global $cp;
		global $nama_persetujuan1;
		global $waktu_persetujuan1;
		global $nama_persetujuan2;
		global $waktu_persetujuan2;
		$optNmkry = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', '', 11);
		$str = 'select kodeorg,kodesupplier,purchaser,nopo,tanggal,useridapprovepo as persetujuan1,useridreleasae as persetujuan2,tglapprovepo as tglp1,tglrelease as tglp2,wktapprovepo as wktp1,wktrelease as wktp2 from ' . $dbname . '.log_poht  where nopo=\'' . $_GET['column'] . '\'';
		$res = mysql_query($str);
		$bar = mysql_fetch_object($res);
		$str1 = 'select namaorganisasi,alamat,wilayahkota,telepon from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $bar->kodeorg . '\'';
		$res1 = mysql_query($str1);
		$bar1 = mysql_fetch_object($res1);
		$namapt = $bar1->namaorganisasi;
		$alamatpt = $bar1->alamat . ', ' . $bar1->wilayahkota;
		$telp = $bar1->telepon;
		$sNpwp = 'select npwp,alamatnpwp from ' . $dbname . '.setup_org_npwp where kodeorg=\'' . $bar->kodeorg . '\'';
		//cari approval 1
		$nama_persetujuan1 = "";
		$waktu_persetujuan1 = "";
		if( $bar->persetujuan1 != "" && (int)$bar->persetujuan1 > 0){
			$str2 = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->persetujuan1."'";
			$res2 = mysql_query($str2);
			$bar2 = mysql_fetch_object($res2);
			$nama_persetujuan1 = $bar2->namakaryawan;			
			$waktu_persetujuan1 = $bar->tglp1." ".$bar->wktp1;
		}
		//cari approval 2
		$nama_persetujuan2 = "";
		$waktu_persetujuan2 = "";
		if( $bar->persetujuan2 != "" && (int)$bar->persetujuan2 > 0 ){
			$str2 = "select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->persetujuan2."'";
			$res2 = mysql_query($str2);
			$bar2 = mysql_fetch_object($res2);
			$nama_persetujuan2 = $bar2->namakaryawan;
			$waktu_persetujuan2 = $bar->tglp2." ".$bar->wktp2; 
		}
		

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
		$cp = $res->kontakperson;
		$this->SetMargins(5, 10, 5);

		$path = $_SESSION['org']['logo'];

		//$this->Image($path, 15, 9, 35, 24);
		$this->SetFont('Arial', 'B', 9);
		$this->SetFillColor(255, 255, 255);
		$this->SetX(5);
		$this->Cell(60, 5, $namapt, 0, 0, 'L');

		$this->SetX(135);
		$this->Cell(30, 5, 'No. PO: ' . $nopo, 0, 1, 'L');

/*
		$this->SetX(55);
		$this->Cell(60, 5, $alamatpt, 0, 1, 'L');
		$this->SetX(55);
		$this->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
*/
//		$this->SetFont('Arial', 'B', 7);
		//$this->SetX(55);
		$this->Cell(60, 5, 'NPWP: ' . $rNpwp['npwp'], 0, 1, 'L');
/*
		$this->SetX(55);
		$this->Cell(60, 5, $_SESSION['lang']['alamat'] . ' NPWP: ' . $rNpwp['alamatnpwp'], 0, 1, 'L');
		$this->SetFont('Arial', 'B', 9);
		$this->Line(15, 35, 205, 35);

		$this->SetX(135);
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(30, 10, 'No. PO: ' . $nopo, 0, 1, 'L');
		$this->SetFont('Arial', 'B', 9);
*/
	}

	public function Footer()
	{
		/*
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(10, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
		$this->SetFont('Arial', '', 6);
		$this->SetX(163);
		$this->Cell(30, 10, 'PRINT TIME : ' . date('d-m-Y H:i:s') . ' ' . $nopo, 0, 1, 'L');
		*/
	}
}

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/zLib.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$pdf = new PDF('P', 'mm', 'A4');
//$pdf = new PDF('L', 'mm', 'A5');
$pdf->AddPage();
$pdf->SetFont('courier', 'B', 8);

if ($_SESSION['language'] == 'EN') {
	$pdf->Cell(30, 4, 'TO :', 0, 0, 'L');
}
else {
	$pdf->Cell(30, 4, 'KEPADA YTH :', 0, 0, 'L');
}
//
//$pdf->Ln();
$pdf->SetX(135);
$pdf->Cell(35, 4, "Approval 1", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nama_persetujuan1, 0, 1, 'L');

$arte = '';
$pdf->Cell(35, 4, $_SESSION['lang']['nm_perusahaan'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nmSupplier . $arte, 0, 0, 'L');
//
$pdf->SetX(135);
$pdf->Cell(35, 4, "Waktu Approval 1", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $waktu_persetujuan1, 0, 1, 'L');

$n= 50;
$lengthx= strlen($almtSupplier);
if ($lengthx < 50){
	$n=$lengthx;
}
$pdf->Cell(35, 4, $_SESSION['lang']['alamat'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . substr($almtSupplier,0,$n), 0, 0, 'L');
//
$pdf->SetX(135);
$pdf->Cell(35, 4, "Approval 2", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nama_persetujuan2 , 0, 1, 'L');

$sqlx = 'select * from ' . $dbname . '.log_poht where nopo=\'' . $_GET['column'] . '\'';
($sqlx1 = mysql_query($sqlx)) || true;
$rsqlx1 = mysql_fetch_object($sqlx1);

// kalau char alamat lebih dari 50, sambung dibawahnya
if ($lengthx > 50) {
	$pdf->Cell(35, 4, '', 0, 0, 'L');
	$pdf->Cell(40, 4, ' '.substr($almtSupplier,50,$lengthx-50), 0, 0, 'L');
	//
	$pdf->SetX(135);
	$pdf->Cell(35, 4, "Waktu Approval 2", 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $waktu_persetujuan2 , 0, 1, 'L');

	$pdf->Cell(35, 4, $rsqlx1->matauang . ' / '. $rsqlx1->statusbayar . ' ' . $rsqlx1->syaratbayar, 0, 1, 'L');
} else {
	$pdf->Cell(35, 4, $rsqlx1->matauang . ' / '. $rsqlx1->statusbayar . ' ' . $rsqlx1->syaratbayar, 0, 0, 'L');
	//
	$pdf->SetX(135);
	$pdf->Cell(35, 4, "Waktu Approval 2", 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $waktu_persetujuan2 , 0, 1, 'L');
}

/*
if ($cp != '') {
	$pdf->Cell(35, 4, $_SESSION['lang']['cperson'], 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $cp, 0, 0, 'L');
}
*/

//$pdf->Cell(35, 4, " ", 0, 0, 'L'); //kasi jarak
//$pdf->Cell(40, 4, ' ', 0, 1, 'L'); //kasi jarak

/*
$pdf->Cell(35, 4, "Approval 1", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nama_persetujuan1, 0, 1, 'L');
$pdf->Cell(35, 4, "Waktu Approval 1", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $waktu_persetujuan1, 0, 1, 'L');
$pdf->Cell(35, 4, "Approval 2", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $nama_persetujuan2 , 0, 1, 'L');
$pdf->Cell(35, 4, "Waktu Approval 2", 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $waktu_persetujuan2 , 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['npwp'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $npwp_sup, 0, 1, 'L');
$pdf->Cell(35, 4, $_SESSION['lang']['kota'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . $kota, 0, 1, 'L');
*/

$pdf->SetFont('Arial', 'U', 12);
$ar = round($pdf->GetY());
//$pdf->SetY($ar + 5);
$pdf->SetY($ar + 5);
$pdf->Cell(190, 5, strtoupper('Purchase Order'), 0, 1, 'C');
$pdf->SetY($ar + 12);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(10, 4, '', 0, 0, 'L');
$pdf->Cell(20, 4, ' ', 0, 0, 'L');
$pdf->SetX(135);
$pdf->Cell(20, 4, $_SESSION['lang']['tanggal'], 0, 0, 'L');
$pdf->Cell(20, 4, ': ' . $tglPo, 0, 0, 'L');
$pdf->SetY($ar + 17);
$pdf->SetFont('Arial', 'B', 8);
//$pdf->SetFillColor(220, 220, 220);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(8, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(60, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C', 1);
$pdf->Cell(36, 5, $_SESSION['lang']['nopp'], 1, 0, 'C', 1);
$pdf->Cell(14, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C', 1);
$pdf->Cell(14, 5, $_SESSION['lang']['satuan'], 1, 0, 'C', 1);
$pdf->Cell(29, 5, $_SESSION['lang']['hargasatuan'], 1, 0, 'C', 1);
$pdf->Cell(26, 5, 'Total', 1, 1, 'C', 1);
$pdf->SetFillColor(255, 255, 255);

$pdf->SetFont('Arial', '', 9);
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
	$nopp = $bar->nopp;
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
	$pdf->SetX($pdf->GetX() + 70);
	$pdf->Cell(36, 5, $nopp, 0, 0, 'L', 0);
	$pdf->Cell(14, 5, number_format($jumlah, 2, '.', ','), 0, 0, 'C', 0);
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
$user_release = $rlopoht->useridreleasae;
$gr_total = ($sb_tot - $nil_diskon) + $nppn;

if (240 <= $akhirSubtot) {
	$pdf->AddPage();
	$akhirY = $pdf->GetY();
}

$pdf->MultiCell(134, 4, $_SESSION['lang']['keterangan'] . ':' . "\n" . $rlopoht->uraian, 'T', 1, 'J', 0);
$akhirKet = $pdf->SetY($akhirY);
$pdf->SetY($akhirY);
$pdf->SetX($pdf->GetX() + 134);
$pdf->Cell(29, 5, $_SESSION['lang']['subtotal'], 'T', 0, 'L', 1);
$pdf->Cell(26, 5, number_format($rlopoht->subtotal, 2, '.', ','), 'T', 1, 'R', 1);
$pdf->SetY($pdf->GetY());
$pdf->SetX($pdf->GetX() + 134);
if( $rlopoht->miscppn > 0){
	$pdf->Cell(29, 5, 'Misc Dgn PPN', '', 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->miscppn, $desiomal, '.', ','), '', 1, 'R', 1);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if( $rlopoht->nilaidiskon > 0){
	$pdf->Cell(29, 5, 'Discount' . ' (' . $rlopoht->diskonpersen . '% )', 0, 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->nilaidiskon, $desiomal, '.', ','), 0, 1, 'R', 1);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if( $rlopoht->ppn > 0){
	$pdf->Cell(29, 5, 'PPN (10 %)', 0, 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->ppn, $desiomal, '.', ','), 0, 1, 'R', 1);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if( $rlopoht->pph > 0){
	$pdf->Cell(29, 5, 'PPh', 0, 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->pph, $desiomal, '.', ','), 0, 1, 'R', 1);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if( $rlopoht->ongkosangkutan > 0){
	$pdf->Cell(29, 5, 'Ongkos Kirim', '', 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->ongkosangkutan, $desiomal, '.', ','), '', 1, 'R', 1);
	$persenPpnOngkir = ($rlopoht->ongkirimppn / $rlopoht->ongkosangkutan) * 100;
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if( $rlopoht->ongkirimppn > 0){
	$pdf->Cell(35, 5, 'PPN OngKir (' . $persenPpnOngkir . '%)', '', 0, 'L', 1);
	$pdf->Cell(20, 5, number_format($rlopoht->ongkirimppn, $desiomal, '.', ','), '', 1, 'R', 1);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
if(!empty($rlopoht->misc)){
	$pdf->Cell(29, 5, 'Pembulatan', '', 0, 'L', 1);
	$pdf->Cell(26, 5, number_format($rlopoht->misc, $desiomal, '.', ','), '', 1, 'R', 1);
	$pdf->SetFont('Arial', 'B', 8);
	$pdf->SetY($pdf->GetY());
	$pdf->SetX($pdf->GetX() + 134);
}
$pdf->Cell(29, 5, $_SESSION['lang']['grnd_total'], 0, 0, 'L', 1);
$pdf->Cell(26, 5, $rlopoht->matauang . ' ' . number_format($rlopoht->nilaipo, $desiomal, '.', ','), 0, 1, 'R', 1);
//terbilang($totalAll['totalSemua'], 2)
$pdf->Cell(35, 4, 'Terbilang: '.terbilang($rlopoht->nilaipo,2), 0, 1, 'L');

$pdf->Cell(35, 4, '', 0, 1, 'L');

if (is_null($rlopoht->idFranco) || ($rlopoht->idFranco == '') || ($rlopoht->idFranco == 0)) {
	$pdf->Cell(40, 4, 'Lokasi & Tanggal Kirim: ' . $rlopoht->lokasipengiriman.' / '.tanggalnormal($rlopoht->tanggalkirim), 0, 1, 'L');
} else {
	$sFr = 'select * from ' . $dbname . '.setup_franco where id_franco=\'' . $rlopoht->idFranco . '\'';
	#exit(mysql_error());
	($qFr = mysql_query($sFr)) || true;
	$rFr = mysql_fetch_assoc($qFr);
	$pdf->Cell(35, 4, 'Lokasi & Tanggal Kirim: ' . $rFr['franco_name'].' '. $rFr['alamat'].' / '.tanggalnormal($rlopoht->tanggalkirim), 0, 1, 'L');
}

$pdf->Cell(35, 4, 'NB: Form ini dicetak secara otomatis oleh sistem, tidak perlu tanda tangan', 0, 1, 'L');

if (616 < strlen($rlopoht->uraian)) {
	$tmbhBrs = 70;
	$tmbhBrs2 = 95;
	$tmbhBrs3 = 65;
	$tmbhBrs5 = 125;
}
else {
	$tmbhBrs = 35;
	$tmbhBrs2 = 55;
	$tmbhBrs3 = 45;
	$tmbhBrs5 = 85;
}

if (275 <= $akhirY + $tmbhBrs5) {
	$akhirY = 0;
	$pdf->AddPage();
}

$pdf->SetY($akhirY + $tmbhBrs + 10);

$pdf->SetFont('Arial', '', 8);

//$pdf->Cell(35, 4, $_SESSION['lang']['kurs'], 0, 0, 'L');
//$pdf->Cell(40, 4, ':  ' . $rlopoht->kurs, 0, 1, 'L');

/*
$pdf->Cell(35, 4, 'Mata Uang', 0, 0, 'L');
$pdf->Cell(40, 4, ':  ' . $rlopoht->matauang, 0, 1, 'L');
$pdf->Cell(35, 4, $_SESSION['lang']['syaratPem'], 0, 0, 'L');
$pdf->Cell(40, 4, ':  ' . $rlopoht->statusbayar . ' : ' . $rlopoht->syaratbayar, 0, 1, 'L');
$pdf->Cell(35, 4, $_SESSION['lang']['tgl_kirim'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . tanggalnormald($rlopoht->tanggalkirim), 0, 1, 'L');

$pdf->Cell(35, 4, $_SESSION['lang']['tgl_kirim'], 0, 0, 'L');
$pdf->Cell(40, 4, ': ' . tanggalnormald($rlopoht->tanggalkirim), 0, 0, 'L');
$pdf->SetX(130);
*/
/*
if (is_null($rlopoht->idFranco) || ($rlopoht->idFranco == '') || ($rlopoht->idFranco == 0)) {
	$pdf->Cell(35, 4, $_SESSION['lang']['almt_kirim'], 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $rlopoht->lokasipengiriman, 0, 1, 'L');
} else {
	$sFr = 'select * from ' . $dbname . '.setup_franco where id_franco=\'' . $rlopoht->idFranco . '\'';

	#exit(mysql_error());
	($qFr = mysql_query($sFr)) || true;
	$rFr = mysql_fetch_assoc($qFr);
	$pdf->Cell(35, 4, $_SESSION['lang']['almt_kirim'], 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $rFr['alamat'], 0, 1, 'L');
	$pdf->Cell(35, 4, ' ', 0, 0, 'L');
	$pdf->Cell(40, 4, ' ', 0, 1, 'L');
	$pdf->Cell(35, 4, 'NB: Form ini dicetak secara otomatis oleh sistem, tidak perlu tanda tangan', 0, 0, 'L');
	$pdf->Cell(40, 4, ': ' . $rFr['contact'], 0, 1, 'L');
}
*/

$pdf->SetY($akhirY + $tmbhBrs2);
//$pdf->Cell(185, 4, $nm_pt, 0, 0, 'R');
$pdf->SetY($akhirY + $tmbhBrs3);
$pdf->SetY($akhirY + $tmbhBrs5);
$sPo = 'select persetujuan1,updateby,purchaser from ' . $dbname . '.log_poht where nopo=\'' . $nopo . '\'';

#exit(mysql_error($conn));
($qPo = mysql_query($sPo)) || true;
$rPo = mysql_fetch_assoc($qPo);
$pdf->SetFont('Arial', '', 8);
//$pdf->Cell(20, 4, "AA ".strtoupper($_SESSION['lang']['purchaser']) . ': ' . strtoupper($optNmkry[$rPo['purchaser']]), 0, 0, 'L', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('', 'U');
//$pdf->Cell(158, 4, "SS ".strtoupper($optNmkry[$rPo['persetujuan1']]), 0, 0, 'R', 0);
$akrhr = $tmbhBrs5 + 5;
$pdf->SetY($akhirY + $akrhr);
$pdf->SetFont('Arial', 'I', 8);
//$pdf->Cell(10, 4, $_SESSION['lang']['fyiGudang'], 0, 0, 'L', 0);

//$pdf->Cell(10, 4, "NB: Form ini dicetak secara otomatis oleh sistem, tidak perlu tanda tangan", 0, 0, 'L', 0);

/*
$pdf->SetFont('', '');
$pdf->Cell(170, 4, 'Manager Procurement', 0, 0, 'R', 0);
*/
$pdf->Output();

?>
