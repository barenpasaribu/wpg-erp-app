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
$i = 'select *,(select namasupplier from log_5supplier a where supplierid=kodesupplier) as namasupplier from ' . $dbname . '.keu_tagihanht where noinvoice=\'' . $_GET['column'] . '\' ';

($n = mysql_query($i)) || true;
$d = mysql_fetch_assoc($n);



// sementara belum dipakai dimana mana
if ($d['kodeorg'] == 'SSP') {
	$harga1 = 'PKS PT. Semunai Sawit Perkasa';
	$pelmuat = '';
	$lokasiTtd = 'Pekanbaru';
	$nmPt = 'PT. Semunai Sawit Perkasa';
	$jbtnTdd = 'Kuasa Direksi';
}
else if ($d['kodeorg'] == 'LSP') {
	$harga1 = 'PKS PT. Libo Sawit Perkasa';
	$pelmuat = '';
	$lokasiTtd = 'Pekanbaru';
	$nmPt = 'PT. Libo Sawit Perkasa';
	$jbtnTdd = 'Kuasa Direksi';
}
$pel = explode('.', $pelmuat);



$str1 = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodeorg'] . '\'';
$res1 = mysql_query($str1);
$bar1 = mysql_fetch_object($res1);
$namapt = $bar1->namaorganisasi;
$alamatpt = $bar1->alamat;
$kotapt= $bar1->wilayahkota;
$telp = $bar1->telepon;
$logo = $bar1->logo;
$kotatelp = $kotapt."    Telp. ".$telp;

// if(!empty($logo)){
// 	$pdf->Image($logo, 15, 5, 35, 20);
// }

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetX(10);

//$pdf->SetX(55);
//$pdf->Cell(60, 5, 'Tel: ' . $telp, 0, 1, 'L');
$pdf->SetFont('Arial', '', 15);

		
$pdf->Ln(20);
$pdf->SetFont('Arial', 'BU', '14');
//$pdf->Cell(200, 5, 'SURAT PERJANJIAN JUAL BELI', 0, 1, 'C');
$pdf->Cell(200, 5, 'INVOICE', 0, 1, 'C');
$pdf->SetFont('Arial', '', '14');
$pdf->Cell(200, 5, 'No. ' . $d['noinvoice'], 0, 1, 'C');
$pdf->Ln();
$x = 'select * from ' . $dbname . '.organisasi where kodeorganisasi=\'' . $d['kodept'] . '\'';
$y = mysql_query($x);
$z = mysql_fetch_assoc($y);
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(40, 5, 'Tipe Invoice', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
if($d['tipeinvoice'] =='o'){
	$tipe = 'Lainya';
} else if($d['tipeinvoice'] =='p'){
	$tipe = 'PO';
}else if($d['tipeinvoice'] =='s'){
	$tipe = 'Surat Jalan';
}else if($d['tipeinvoice'] =='k'){
	$tipe = 'Kontrak';
}else if($d['tipeinvoice'] =='f'){
	$tipe = 'fee';
}else if($d['tipeinvoice'] =='n'){
	$tipe = 'Konosemen';
}else {
	$tipe = '';
}
$pdf->Cell(10, 5, $tipe, 0, 1, 'L');
$pdf->Cell(40, 5, 'Tanggal', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $d['tanggal'], 0, 1, 'L');
$pdf->Cell(40, 5, 'Suplier', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $d['namasupplier'], 0, 1, 'L');
$pdf->Cell(40, 5, 'No Invoice Sup', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $d['noinvoicesupplier'], 0, 1, 'L');
$pdf->Cell(40, 5, 'NO. PO', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $d['nopo'], 0, 1, 'L');
$pdf->Cell(40, 5, 'Jatuh Tempo', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->SetFont('Arial', '', '10');
$pdf->Cell(10, 5, $d['jatuhtempo'], 0, 1, 'L');
$pdf->Cell(40, 5, 'No Faktur Pajak', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, $d['nofp'], 0, 1, 'L');
$pdf->Cell(40, 5, 'Nilai Invoice', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format($d['nilaiinvoice']), 0, 1, 'L');
$pdf->Cell(40, 5, 'Potongan Susut', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format($d['potsusutjml']), 0, 1, 'L');
$pdf->Cell(40, 5, 'Potongan Mutu', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format($d['potmutujml']), 0, 1, 'L');
$pdf->Cell(40, 5, 'Nilai Ppn', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format($d['nilaippn']), 0, 1, 'L');
$pdf->Cell(40, 5, 'Tipe PPh', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, $d['pph'], 0, 1, 'L');
$pdf->Cell(40, 5, 'Nilai PPh', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format($d['perhitunganpph']), 0, 1, 'L');
$pdf->Cell(40, 5, 'Nilai Invoice Akhir', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, number_format((($d['nilaiinvoice']-($d['potsusutjml']+d['potmutujml']))+$d['nilaippn'])-$d['perhitunganpph']), 0, 1, 'L');


$pdf->Cell(40, 5, 'Tiket Timbangan ', 0, 0, 'L');
$pdf->Cell(5, 5, ':', 0, 0, 'L');
$pdf->Cell(10, 5, '', 0, 1, 'L');
$pdf->Cell(10, 5, 'No'.'.', 0, 0, 'L');
$pdf->Cell(40, 5, 'No Tiket', 0, 0, 'L');

$pdf->Cell(20, 5, 'Tonase ', 0, 0, 'L');
$pdf->Cell(30, 5, 'Harga', 0, 0, 'L');
$pdf->Cell(5, 5, '', 0, 0, 'L');
$pdf->Cell(30, 5, 'Total', 0, 0, 'L');
$pdf->Cell(30, 5, 'Kendaraan', 0, 1, 'L');


 $sCob = 'select a.*,b.beratnormal as beratnormal,date(b.tanggal) as tanggal_,b.nokendaraan as nokendaraan , nospb from '.$dbname.".keu_tagihandt a,pabrik_timbangan b, keu_tagihanht c where a.notiket=b.notransaksi and left(b.millcode,3)=c.kodeorg and a.noinvoice=c.noinvoice and  a.noinvoice='".$d['noinvoice']."'  order by a.notiket ASC";

        $res = mysql_query($sCob);
        $row = mysql_num_rows($res);
while ($hsl = mysql_fetch_assoc($res)) {

        $tglTimbangan = $hsl['tanggal_'];
		$kodesupplier = $d['kodesupplier'];

		$nopo = $hsl['nospb'];
        $sSPBantrian = 'select * from '.$dbname.".pabrik_antriantb where  nospb = '".$nopo."' ";
		$resSPBantrian = mysql_query($sSPBantrian);
        $hslAntrian = mysql_fetch_assoc($resSPBantrian);
        $tglAntrian = $hslAntrian['tanggal'];

        if($tglAntrian != ''){
        	$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglAntrian."' ";
        
	
        	$resSPB = mysql_query($sSPB);
       		 $hsil = mysql_fetch_assoc($resSPB);
       		 if($tipe=='fee'){
       		 	$harga = $hsil['fee'];
       		 }else{
       		 	$harga = $hsil['harga_akhir'];
       		 }
        	
        	
        }else{
        		if($tglTimbangan != ''){
        				$sSPB = 'select * from '.$dbname.".log_supplier_harga_history where  kode_supplier = '".$kodesupplier."' and tanggal_akhir = '".$tglTimbangan."' ";
        
		 
        				$resSPB = mysql_query($sSPB);
       		 			$hsil = mysql_fetch_assoc($resSPB);
        				 if($tipe=='fee'){
		       		 	 $harga = $hsil['fee'];
		       		 }else{
		       		 	 $harga = $hsil['harga_akhir'];
		       		 }
		        				
        				
        			}else{
        			 if($harga ==''){
        	
        			 $harga = '0';
        		
       			  }
        		}
        }

$no++;
$pdf->Cell(10, 5, $no.'.', 0, 0, 'L');
$pdf->Cell(40, 5, $hsl['notiket'], 0, 0, 'L');

$pdf->Cell(20, 5, number_format($hsl['beratnormal']).' Kg', 0, 0, 'R');
$pdf->Cell(30, 5, 'Rp. '.number_format($harga).'/'.'Kg', 0, 0, 'R');
$pdf->Cell(5, 5, 'Rp. ', 0, 0, 'L');
$pdf->Cell(30, 5, number_format($harga*$hsl['beratnormal']), 0, 0, 'R');
$pdf->Cell(30, 5, $hsl['nokendaraan'], 0, 1, 'L');

$totberatnormal+=$hsl['beratnormal'];
$totharga+=($harga*$hsl['beratnormal']);
}
$pdf->Cell(50, 5, 'T O T A L', 0, 0, 'L');

$pdf->Cell(20, 5, number_format($totberatnormal).' Kg', 0, 0, 'R');
$pdf->Cell(30, 5, '', 0, 0, 'R');
$pdf->Cell(5, 5, 'Rp. ', 0, 0, 'L');
$pdf->Cell(30, 5, number_format($totharga), 0, 0, 'R');
$pdf->Cell(30, 5, '', 0, 1, 'L');

$pdf->Output();

?>
