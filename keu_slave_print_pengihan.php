<?php

require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
require_once 'lib/fpdf.php';
include_once 'lib/zMysql.php';
include_once 'lib/devLibrary.php';
require_once 'lib/terbilang.php';
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];
$optnmcust = makeOption($dbname, 'pmn_4customer', 'kodecustomer,namacustomer');
$optnmakun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun');

$sql = "SELECT p.namacustomer,o.namaorganisasi,a3.namaakun,a1.namaakun AS akunkredit,a2.namaakun AS akundebet,k.*
from  keu_penagihanht k
LEFT OUTER JOIN organisasi o ON o.kodeorganisasi=k.kodeorg
LEFT OUTER JOIN pmn_4customer p ON p.kodecustomer=k.kodecustomer
LEFT OUTER JOIN keu_5akun a1 ON a1.noakun=k.kredit
LEFT OUTER JOIN keu_5akun a2 ON a2.noakun=k.debet
LEFT OUTER JOIN keu_5akun a3 ON a3.noakun=k.bayarke
where noinvoice='".$column."' ";
//$str = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";
$res = mysql_query($sql);
$bar = mysql_fetch_assoc($res);


// Gak dipakai
class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $userid;
        global $column;
        global $optnmakun;
        global $optnmcust;
        global $bar;
        global $namapt;
        $test = explode(',', $_GET['column']);
        list($notransaksi, $kodevhc) = $test;
        $str = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";
        $res = mysql_query($str);
        $bar = mysql_fetch_object($res);
        $posting = $bar->posting;
        $str0 = 'select * from '.$dbname.".organisasi where tipe IN('holding','tipe') and kodeorganisasi='".$bar->kodeorg."'";
        $res0 = mysql_query($str0);
         while ($bar0 = mysql_fetch_object($res0)) {
            $induk = $bar0->induk;
           
        }
      
        $str1 = 'select * from '.$dbname.".organisasi where tipe IN ('holding','pt') and kodeorganisasi='".$induk."'";
        $res1 = mysql_query($str1);
        while ($bar1 = mysql_fetch_object($res1)) {
            $namapt = $bar1->namaorganisasi;
            $alamatpt = $bar1->alamat.', '.$bar1->wilayahkota;
            $telp = $bar1->telepon;
            $kodeorganisasi = $bar1->kodeorganisasi;
        }
           $strNpwp = 'select * from '.$dbname.".setup_org_npwp where  kodeorg='".$kodeorganisasi."'";
        $resNpwp = mysql_query($strNpwp);
         while ($barNpwp = mysql_fetch_object($resNpwp)) {
            $npwp = $barNpwp->npwp;
           
        }

        $sql2 = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$bar->updateby."'";
        $query2 = mysql_query($sql2);
        $res2 = mysql_fetch_object($query2);
        $sql5 = 'select namakaryawan from '.$dbname.".datakaryawan where karyawanid='".$bar->postingby."'";
        $query5 = mysql_query($sql5);
        $res5 = mysql_fetch_object($query5);
        $sqlJnsVhc = 'select namajenisvhc from '.$dbname.".vhc_5jenisvhc where jenisvhc='".$bar->jenisvhc."'";
        $qJnsVhc = mysql_query($sqlJnsVhc);
        $rJnsVhc = mysql_fetch_assoc($qJnsVhc);
        $sBrg = 'select namabarang from '.$dbname.".log_5masterbarang where kodebarang='".$bar->jenisbbm."'";
        $qBrg = mysql_query($sBrg);
        $rBrg = mysql_fetch_assoc($qBrg);
        $strcust = 'select b.*,date(a.tanggal) as tgl from '.$dbname.'.'.$_GET['table']." as a ,  pmn_4customer as b  where a.kodecustomer=b.kodecustomer  and a.noinvoice='".$column."'";
 $rescust = mysql_query($strcust);
$barcust = mysql_fetch_object($rescust);


        $path = $_SESSION['org']['logo'];

        //$this->Image($path, 15, 5, 35, 20);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(10);
        $this->Cell(80, 5, $namapt, 0, 0, 'L');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(100, 5, 'INVOICE', 0, 1, 'R');
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 9);
        $PecahStr = explode(",", $alamatpt);
        for ( $i = 0; $i < count( $PecahStr ); $i++ ) {
        $this->Cell(60, 5,trim($PecahStr[$i]), 0, 1, 'L');
             }
       
        $this->SetX(10);
        $this->Cell(60, 5, 'NPWP: '.$npwp, 0, 1, 'L');

        $this->Cell(60, 5, '', 0, 1, 'L');
        $this->Cell(80, 5, 'Kepada :', 0, 0, 'L');

         $this->Cell(100, 5,'No Invoice :'.$bar->noinvoice, 0, 1, 'R');
         $this->Cell(80, 5, $barcust->namacustomer, 0, 0, 'L');
        $this->SetFont('Arial', '', 9);
        $tgl = explode("-",$barcust->tgl);
        $year= $tgl[0];
        $monthh = $tgl[1];
        $date = $tgl[2];
          if($monthh == '01'){
            $month = 'Januari';
        }else if($monthh == '02'){
            $month = 'Februari';
        }else if($monthh == '03'){
            $month = 'Maret';
        }else if($monthh == '04'){
            $month = 'April';
        }else if($monthh == '05'){
            $month = 'Mei';
        }else if($monthh == '06'){
            $month = 'Juni';
        }else if($monthh == '07'){
            $month = 'Juli';
        }else if($monthh == '08'){
            $month = 'Agustus';
        }else if($monthh == '09'){
            $month = 'September';
        }else if($monthh == '10'){
            $month = 'Oktober';
        }else if($monthh == '11'){
            $month = 'November';
        }else{
            $month = 'Desember';
        }
        $this->Cell(100, 5,'Tanggal :'. $date.' '.$month.' '.$year, 0, 1, 'R');
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 9);
        $PecahStr = explode(",", $barcust->alamat.','.$barcust->kota);
        for ( $i = 0; $i < count( $PecahStr ); $i++ ) {
        $this->Cell(60, 5,trim($PecahStr[$i]), 0, 1, 'L');
             }
       
        $this->SetX(10);
       // $this->Cell(60, 5, 'Tel: '.$barcust->telepon, 0, 1, 'L');
        $this->Ln();
        $this->SetFont('Arial', 'U', 9);
        $this->SetY(40);
        $this->Cell(190, 5, '', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $this->SetY(27);
        $this->SetX(163);
        $this->Cell(30, 10, '', 0, 1, 'L');
        
        $this->Ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$sql3 = 'select namaorganisasi from '.$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
$query3 = mysql_query($sql3);
$res3 = mysql_fetch_object($query3);
$pdf->SetFont('Arial', 'B', 9);
 $pdf->SetFillColor(255, 255, 255);
  $pdf->SetY(50);
 $pdf->Cell(1, 5, '', 0, 1, 'L', 1);
$pdf->Cell(1, 5, '', 0, 1, 'L', 1);
$pdf->Cell(1, 5, '', 0, 1, 'L', 1);
 $pdf->Cell(1, 5, '', 0, 1, 'L', 1);
 $pdf->Cell(1, 5, '', 0, 1, 'L', 1);
$pdf->Cell(12, 5, 'No', 1, 0, 'L', 1);
$pdf->Cell(60, 5, 'Keterangan', 1, 0, 'C', 1);
$pdf->Cell(36, 5, 'Quantity', 1, 0, 'C', 1);
$pdf->Cell(36, 5, 'Harga Satuan', 1, 0, 'C', 1);
$pdf->Cell(40, 5, 'Total', 1, 1, 'C', 1);
$pdf->Line(10,85,10,170);
$pdf->Line(194,85,194,170);
$pdf->Line(22,78,22,170);
 $pdf->Line(82,78,82,170);
$pdf->Line(118,85,118,170);
$pdf->Line(154,85,154,170);
$pdf->Line(10,170,194,170);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetFont('Arial', '', 9);
$no=0;
 $strInv = 'select * from '.$dbname.'.'.$_GET['table']."  where noinvoice='".$column."'";

 $resInv = mysql_query($strInv);
while ($barInv = mysql_fetch_object($resInv)) {
    $no++;
   $nodo =  $barInv->noorder;
   $tipe =  $barInv->tipe;
$tb_timbangan= 'pabrik_timbangan';
$strTonase = 'select sum(b.beratnormal) as netto from '.$dbname.'.'."keu_penagihandt as a,pabrik_timbangan as b  where b.nosipb='".$nodo."' and a.notiket=b.notransaksi AND a.nodo=b.nosipb AND noinvoice='".$column."' group by a.nodo";

$resTonase = mysql_query($strTonase);
$barTonase = mysql_fetch_object($resTonase);
$tb_kj= 'pmn_kontrakjual';
$strHarga = 'select * from '.$dbname.'.'.$tb_kj."  where nodo='".$nodo."'";

$resHarga = mysql_query($strHarga);
$barHarga = mysql_fetch_object($resHarga);
$kodebarang = $barHarga->kodebarang;
$nokontrak = $barHarga->nokontrak;
$strProduk = 'select * from '.$dbname.".log_5masterbarang  where kodebarang='".$kodebarang."'";
$resProduk = mysql_query($strProduk);
$barProduk = mysql_fetch_object($resProduk);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(12, 5, $no, LR, 0, 'C', 1);

if($tipe == '0'){
$pdf->Cell(60, 5, ' PENJUALAN ', 0, 0, 'L', 0);
$pdf->Cell(36, 5, number_format($barTonase->netto,0,",",".").' '.$barProduk->satuan, LR, 0, 'C', 1);
$pdf->Cell(36, 5,'Rp '. number_format($barHarga->hargasatuan,0,",","."), LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barTonase->netto*$barHarga->hargasatuan,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '. $barProduk->namabarang, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '.$nokontrak, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->MultiCell(60, 5, ' '.$barInv->keterangan, LR, 1, 'L', 1);

$pdf->SetFillColor(255, 255, 255);
$nilai += ($barTonase->netto*$barHarga->hargasatuan) ;
}else if($tipe =='1'){
$pdf->Cell(60, 5, ' UANG MUKA PENJUALAN ', 0, 0, 'L', 0);
$pdf->Cell(36, 5, ' ', LR, 0, 'C', 1);
$pdf->Cell(36, 5, ' ', LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barInv->nilaiinvoice,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '. $barProduk->namabarang, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '.$nokontrak, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->MultiCell(60, 5, ' '.$barInv->keterangan, LR, 1, 'L', 1);

$pdf->SetFillColor(255, 255, 255);
$nilai += $barInv->nilaiinvoice ;
}else if( $tipe == '2'){
$pdf->Cell(60, 5, ' PENJUALAN', 0, 0, 'L', 0);
$pdf->Cell(36, 5,number_format($barTonase->netto,0,",",".").' '.$barProduk->satuan, LR, 0, 'C', 1);
$pdf->Cell(36, 5,'Rp '. number_format($barHarga->hargasatuan,0,",","."), LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barTonase->netto*$barHarga->hargasatuan,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5, '', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' UANG MUKA', LR, 0, 'L', 1);
$pdf->Cell(36, 5, ' ', LR, 0, 'C', 1);
$pdf->Cell(36, 5,' ', LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barInv->uangmuka,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '. $barProduk->namabarang, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '.$nokontrak, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->MultiCell(60, 5, ' '.$barInv->keterangan, LR, 1, 'L', 1);

$pdf->SetFillColor(255, 255, 255);
$nilai += ($barTonase->netto*$barHarga->hargasatuan);

}else if($tipe =='3'){
$pdf->Cell(60, 5, ' PENJUALAN ', 0, 0, 'L', 0);
$pdf->Cell(36, 5,number_format($barTonase->netto,0,",",".").' '.$barProduk->satuan, LR, 0, 'C', 1);
$pdf->Cell(36, 5,'Rp '. number_format($barHarga->hargasatuan,0,",","."), LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barTonase->netto*$barHarga->hargasatuan,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '. $barProduk->namabarang, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '.$nokontrak, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->MultiCell(60, 5, ' '.$barInv->keterangan, LR, 1, 'L', 1);
$pdf->SetFillColor(255, 255, 255);
$nilai += ($barTonase->netto*$barHarga->hargasatuan) ;
}else{
 
$pdf->Cell(60, 5, ' PENJUALAN ', 0, 0, 'L', 0);
$pdf->Cell(36, 5,number_format($barTonase->netto,0,",",".").' '.$barProduk->satuan, LR, 0, 'C', 1);
$pdf->Cell(36, 5,'Rp '. number_format($barHarga->hargasatuan,0,",","."), LR, 0, 'C', 1);
$pdf->Cell(40, 5,'Rp '. number_format($barTonase->netto*$barHarga->hargasatuan,0,",",".") , LR, 1, 'C', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '. $barProduk->namabarang, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->Cell(60, 5, ' '.$nokontrak, LR, 1, 'L', 1);

$pdf->Cell(12, 5,'', LR, 0, 'C', 1);
$pdf->MultiCell(60, 5, ' '.$barInv->keterangan, LR, 1, 'L', 1);
$pdf->SetFillColor(255, 255, 255);
$nilai += ($barTonase->netto*$barHarga->hargasatuan) ; 
}

$potsusutjml += $barInv->potongsusutjmlint+$barInv->potongsusutjmlext;
$potmutuint += ($barInv->potongmutuint);
$potmutuext += ($barInv->potongmutuext);
$nilaippn += ($barInv->nilaippn);
$nilaipph += ($barInv->nilaipph);
$kodekaryawan = $barInv->namattd ;
    }
    $potTotal = (($potsusutjml)+($potmutuint)+($potmutuext))+($nilaipph);

$pdf->SetY(175);

$pdf->Cell(134, 4, 'Total', 0, 0, 'L');
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4,  number_format($nilai,0,",","."), 0, 1, 'R');

if($bar->uangmuka>0){
$pdf->Cell(134, 4, 'Uang Muka ', 0, 0, 'L');
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format($bar->uangmuka,0,",","."), 0, 1, 'R');
}

if($potsusutjml>0){
$pdf->Cell(134, 4, 'Potongan Kesusutan', 0, 0, 'L');
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4,number_format($potsusutjml,0,",","."), 0, 1, 'R');
}

if(($potmutuint+$potmutuext)>0){
$pdf->Cell(134, 4, 'Potongan Mutu ', 0, 0, 'L');
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format($potmutuint+$potmutuext,0,",","."), 0, 1, 'R');
}

$pdf->SetFont('Arial', 'B', 9);
if( $tipe == '0' || $tipe == '3'){
$pdf->Cell(134, 4, 'Sub Total', 0, 0, 'L', 1);
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format(($nilai)-($potTotal)-($bar->uangmuka),0,",","."), 0, 1, 'R', 0);
}
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(134, 4, 'Nilai PPN', 0, 0, 'L', 1);
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format(($nilaippn),0,",","."), 0, 1, 'R', 0);
if($nilaipph>0){
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(134, 4, 'Nilai PPh ', 0, 0, 'L');
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format($nilaipph,0,",","."), 0, 1, 'R');
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(134, 4, 'Total Akhir', 0, 0, 'L', 1);
$pdf->Cell(10, 4, 'Rp', 0, 0, 'L', 1);
$pdf->Cell(40, 4, number_format((($nilai)-($potTotal))+($nilaippn)-($bar->uangmuka),0,",","."), TB, 1, 'R', 0);
$pdf->Cell(30, 4, '', 0, 0, 'L');
$pdf->Cell(40, 4, ' ', 0, 1, 'L');
$pdf->Cell(30, 4, '', 0, 0, 'L');
$pdf->Cell(40, 4, ' ', 0, 1, 'L');
$pdf->MultiCell(100, 4, 'Terbilang: '.terbilang((($nilai)-($potTotal))+($nilaippn)-($bar->uangmuka),2).' Rupiah', 0, 1, 'L');
$pdf->Cell(30, 4, '', 0, 0, 'L');
$pdf->Cell(40, 4, ' ', 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(130, 4, ' ', 0, 0, 'L');
$pdf->Cell(40, 4, 'Hormat Kami', 0, 1, 'L');
$pdf->Cell(40, 4, 'Keterangan :', 0, 0, 'L');
$pdf->Cell(40, 4, ' ', 0, 1, 'L');
$pdf->Cell(40, 4, 'Pembayaran di transfer ke Rekening', 0, 1, 'L');
$sakun = "select distinct noakun,namaakun from ".$dbname.".keu_5akun  where noakun=".$bar->bayarke." order by namaakun asc";
$qakun = mysql_query($sakun);
$rakun = mysql_fetch_assoc($qakun);
$pdf->Cell(40, 4, $rakun['namaakun'], 0, 1, 'L');
$pdf->Cell(40, 4, "A/N ". $namapt, 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(130, 4, ' ', 0, 0, 'L');
$strKar = 'select namakaryawan from '.$dbname.'.'."datakaryawan where karyawanid='".$kodekaryawan."'";
$resKar = mysql_query($strKar);
$barKar = mysql_fetch_object($resKar);

$pdf->Cell(40, 4, $barKar->namakaryawan, 0, 1, 'L');

$pdf->Output();

?>