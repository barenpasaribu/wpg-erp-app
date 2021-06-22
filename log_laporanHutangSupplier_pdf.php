<?php


require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$gudang = $_GET['gudang'];
$periode = $_GET['periode'];
$str = 'select distinct kodeorganisasi,namaorganisasi from ' . $dbname . '.organisasi' . "\r\n" . '      where kodeorganisasi=\'' . $gudang . '\'';
$res = mysql_query($str);

while ($bar = mysql_fetch_object($res)) {
	$namagudang = $bar->namaorganisasi;
}

$str = 'select distinct tanggalmulai, tanggalsampai from ' . $dbname . '.setup_periodeakuntansi' . "\r\n" . '      where kodeorg = \'' . $gudang . '\' and periode = \'' . $periode . '\'';
$res = mysql_query($str);

if ($periode == '') {
	echo 'Warning: silakan mengisi periode';
	exit();
}


while ($bar = mysql_fetch_object($res)) {
	$tanggalmulai = $bar->tanggalmulai;
	$tanggalsampai = $bar->tanggalsampai;
}

$str = 'select distinct kodebarang, namabarang from ' . $dbname . '.log_5masterbarang';
$res = mysql_query($str);
$optper = '';

while ($bar = mysql_fetch_object($res)) {
	$barang[$bar->kodebarang] = $bar->namabarang;
}

if ($periode == '') {
	$str = 'select a.notransaksi,a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan ' . "\r\n\t\t" . '  from ' . $dbname . '.log_transaksi_vw a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5supplier b on a.idsupplier=b.supplierid' . "\r\n\t\t" . '  where a.kodegudang=\'' . $gudang . '\' and a.tipetransaksi=1 ' . "\r\n\t\t" . '  order by a.tanggal';
}
else {
	$str = 'select a.notransaksi,a.tanggal as tanggal, a.kodebarang as kodebarang, a.satuan as satuan, a.jumlah as jumlah, a.idsupplier as idsupplier, b.namasupplier as namasupplier, a.hargasatuan as hargasatuan ' . "\r\n\t\t" . '  from ' . $dbname . '.log_transaksi_vw a' . "\r\n\t\t" . '  left join ' . $dbname . '.log_5supplier b on a.idsupplier=b.supplierid' . "\r\n\t\t" . '  where a.kodegudang=\'' . $gudang . '\' and a.tanggal>=\'' . $tanggalmulai . '\' and a.tanggal<=\'' . $tanggalsampai . '\' and a.tipetransaksi=1 ' . "\r\n\t\t" . '  order by a.tanggal';
}
class PDF extends FPDF
{
	public function Header()
	{
		global $namagudang;
		global $periode;
		global $gudang;
		$this->SetFont('Arial', 'B', 12);
		$this->Cell(190, 5, strtoupper($_SESSION['lang']['hutangsupplierbpb']), 0, 1, 'C');
		$this->SetFont('Arial', '', 8);
		$this->Cell(20, 5, $namagudang, '', 0, 'L');
		$this->Cell(120, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['tanggal'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, date('d-m-Y H:i'), 0, 1, 'L');
		$this->Cell(10, 5, 'Periode ', '', 0, 'L');
		$this->Cell(20, 5, ' :  ' . $periode, '', 0, 'L');
		$this->Cell(110, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, $_SESSION['lang']['page'], '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $this->PageNo(), '', 1, 'L');
		$this->Cell(140, 5, ' ', '', 0, 'R');
		$this->Cell(15, 5, 'User', '', 0, 'L');
		$this->Cell(2, 5, ':', '', 0, 'L');
		$this->Cell(35, 5, $_SESSION['standard']['username'], '', 1, 'L');
		$this->Ln();
		$this->SetFont('Arial', '', 7);
		$this->Cell(5, 5, 'No.', 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['tanggal'], 1, 0, 'C');
		$this->Cell(35, 5, 'No. Transaksi', 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['kodebarang'], 1, 0, 'C');
		$this->Cell(65, 5, $_SESSION['lang']['namabarang'], 1, 0, 'C');
		$this->Cell(15, 5, $_SESSION['lang']['jumlah'], 1, 0, 'C');
		$this->Cell(10, 5, $_SESSION['lang']['satuan'], 1, 0, 'C');
		$this->Cell(20, 5, $_SESSION['lang']['kodesupplier'], 1, 0, 'C');
		$this->Cell(50, 5, $_SESSION['lang']['namasupplier'], 1, 0, 'C');
		$this->Cell(20, 5, $_SESSION['lang']['hargasatuan'], 1, 0, 'C');
		$this->Cell(25, 5, $_SESSION['lang']['total'], 1, 0, 'C');
		$this->Ln();
	}
}

$res = mysql_query($str);
$no = 0;

if (mysql_num_rows($res) < 1) {
	echo $_SESSION['lang']['tidakditemukan'];
}
else {
	$pdf = new PDF('L', 'mm', 'A4');
	$pdf->AddPage();

	while ($bar = mysql_fetch_object($res)) {

	$cellWidth=65;


    if($pdf->GetStringWidth($barang[$bar->kodebarang]) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
        $line=1;
    }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($barang[$bar->kodebarang]);    //total panjang teks
        $errMargin=5;       //margin kesalahan lebar sel, untuk jaga-jaga
        $startChar=0;       //posisi awal karakter untuk setiap baris
        $maxChar=0;         //karakter maksimum dalam satu baris, yang akan ditambahkan nanti
        $textArray=array(); //untuk menampung data untuk setiap baris
        $tmpString="";      //untuk menampung teks untuk setiap baris (sementara)
        
        while($startChar < $textLength){ //perulangan sampai akhir teks
            //perulangan sampai karakter maksimum tercapai
            while( 
            $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
            ($startChar+$maxChar) < $textLength ) {
                $maxChar++;
                $tmpString=substr($barang[$bar->kodebarang],$startChar,$maxChar);
            }
            //pindahkan ke baris berikutnya
            $startChar=$startChar+$maxChar;
            //kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
            array_push($textArray,$tmpString);
            //reset variabel penampung
            $maxChar=0;
            $tmpString='';
            
        }
        //dapatkan jumlah baris
        $line=count($textArray);


    }


		$no += 1;
		$total = 0;
		$total = $bar->jumlah * $bar->hargasatuan;
		$totalall += $total;
		$pdf->Cell(5, 4*$line, $no, 0, 0, 'R');
		$pdf->Cell(15, 4*$line, tanggalnormal($bar->tanggal), 0, 0, 'L');
		$pdf->Cell(35, 4*$line, $bar->notransaksi, 0, 0, 'L');
		$pdf->Cell(15, 4*$line, $bar->kodebarang, 0, 0, 'R');
		$xPos=$pdf->GetX();
		$yPos=$pdf->GetY();
		$pdf->MultiCell(65, 4, $barang[$bar->kodebarang]);
		$pdf->SetXY($xPos + 65 , $yPos);
		$pdf->Cell(15, 4*$line, number_format($bar->jumlah), 0, 0, 'R');
		$pdf->Cell(10, 4*$line, $bar->satuan, 0, 0, 'L');
		$pdf->Cell(20, 4*$line, $bar->idsupplier, 0, 0, 'R');
		$pdf->Cell(50, 4*$line, $bar->namasupplier, 0, 0, 'L');
		$pdf->Cell(20, 4*$line, number_format($bar->hargasatuan), 0, 0, 'R');
		$pdf->Cell(25, 4*$line, number_format($total), 0, 1, 'R');
	}

	$pdf->Cell(250, 4, 'TOTAL', 0, 0, 'R');
	$pdf->Cell(25, 4, number_format($totalall), 0, 1, 'R');
	$pdf->Output();
}

?>
