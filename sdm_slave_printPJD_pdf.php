<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
require_once 'lib/zLib.php';
$notransaksi = $_GET['notransaksi'];

class PDF extends FPDF
{
    public function Header()
    {
        global $conn;
        global $dbname;
        global $notransaksi;
        $induk = makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');
        $x = 'select kodeorg from '.$dbname.".sdm_pjdinasht  where notransaksi='".$notransaksi."'";
        $y = mysql_query($x);
        $z = mysql_fetch_assoc($y);
        $i = 'select namaorganisasi,alamat,wilayahkota,telepon,kodeorganisasi,logo from '.$dbname.".organisasi where kodeorganisasi='".$induk[$z['kodeorg']]."'";
        $n = mysql_query($i);
        $d = mysql_fetch_assoc($n);
        $namapt = $d['namaorganisasi'];
        $alamatpt = $d['alamat'].', '.$d['wilayahkota'];
        $telp = $d['telepon'];
        $logo = $d['logo'];
        if (isset($logo)) {
            $this->Image($logo, 15, 5, 25, 20);
        }
        
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(255, 255, 255);
        $this->SetX(40);
        $this->Cell(60, 5, $namapt, 0, 1, 'L');
        $this->SetX(40);
        $this->Cell(60, 5, $alamatpt, 0, 1, 'L');
        $this->SetX(40);
        $this->Cell(60, 5, 'Tel: '.$telp, 0, 1, 'L');
        $this->Line(15, 35, 205, 35);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$str = 'select * from '.$dbname.".sdm_pjdinasht where notransaksi='".$notransaksi."'";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $jabatan = '';
    $namakaryawan = '';
    $bagian = '';
    $karyawanid = '';
    $strc = "select a.namakaryawan,a.karyawanid,a.bagian,a.nik,b.namajabatan 
    from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b
    on a.kodejabatan=b.kodejabatan
    where a.karyawanid=".$bar->karyawanid;
    $resc = mysql_query($strc);
    while ($barc = mysql_fetch_object($resc)) {
        $jabatan = $barc->namajabatan;
        $namakaryawan = $barc->namakaryawan;
        $bagian = $barc->bagian;
        $karyawanid = $barc->karyawanid;
        $nik = $barc->nik;
    }
    $keterangan = $bar->keterangan;
    $tipe_dinas = $bar->tipe_perjalanan_dinas;
    $kodeorg = $bar->kodeorg;
    $persetujuan = $bar->persetujuan;
    $persetujuan2 = $bar->persetujuan2;
    $hrd = $bar->hrd;
    $tujuan3 = $bar->tujuan3;
    $tujuan2 = $bar->tujuan2;
    $tujuan1 = $bar->tujuan1;
    $tanggalperjalanan = tanggalnormal($bar->tanggalperjalanan);
    $tanggalkembali = tanggalnormal($bar->tanggalkembali);
    $uangmuka = $bar->uangmuka;
    $tugas1 = $bar->tugas1;
    $tugas2 = $bar->tugas2;
    $tugas3 = $bar->tugas3;
    $tujuanlain = $bar->tujuanlain;
    $tugaslain = $bar->tugaslain;
    $pesawat = $bar->pesawat;
    $darat = $bar->darat;
    $laut = $bar->laut;
    $dibayar = $bar->dibayar;
    $mess = $bar->mess;
    $hotel = $bar->hotel;
    $mobilsewa = $bar->mobilsewa;
    $mobildinas = $bar->mobildinas;
    $statushrd = $bar->statushrd;
    if (0 == $statushrd) {
        $statushrd = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statushrd) {
            $statushrd = $_SESSION['lang']['disetujui'];
        } else {
            $statushrd = $_SESSION['lang']['ditolak'];
        }
    }

    $statuspersetujuan = $bar->statuspersetujuan;
    if (0 == $statuspersetujuan) {
        $perstatus = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statuspersetujuan) {
            $perstatus = $_SESSION['lang']['disetujui'];
        } else {
            $perstatus = $_SESSION['lang']['ditolak'];
        }
    }

    $statuspersetujuan2 = $bar->statuspersetujuan2;
    if (0 == $statuspersetujuan2) {
        $perstatus2 = $_SESSION['lang']['wait_approval'];
    } else {
        if (1 == $statuspersetujuan2) {
            $perstatus2 = $_SESSION['lang']['disetujui'];
        } else {
            $perstatus2 = $_SESSION['lang']['ditolak'];
        }
    }

    $perjabatan = '';
    $perbagian = '';
    $pernama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join
    ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan
    where karyawanid=".$persetujuan;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $perjabatan = $barf->namajabatan;
        $perbagian = $barf->bagian;
        $pernama = $barf->namakaryawan;
    }
    $perjabatan2 = '';
    $perbagian2 = '';
    $pernama2 = '';
    $strf2 = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n\t   ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n\t\t   where karyawanid=".$persetujuan2;
    $resf2 = mysql_query($strf2);
    while ($barf2 = mysql_fetch_object($resf2)) {
        $perjabatan2 = $barf2->namajabatan;
        $perbagian2 = $barf2->bagian;
        $pernama2 = $barf2->namakaryawan;
    }
    $hjabatan = '';
    $hbagian = '';
    $hnama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n                   where karyawanid=".$hrd;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $hjabatan = $barf->namajabatan;
        $hbagian = $barf->bagian;
        $hnama = $barf->namakaryawan;
    }
}
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetFont('Arial', 'B', 14);
$pdf->AddPage();
$pdf->SetY(40);
$pdf->SetX(20);
$pdf->SetFillColor(255, 255, 255);
$pdf->Cell(175, 5, strtoupper($_SESSION['lang']['spdinas']), 0, 1, 'C');
$pdf->SetX(20);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(175, 5, 'NO : '.$notransaksi, 0, 1, 'C');
$pdf->Ln();
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['nik'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$nik, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$namakaryawan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, "Tipe", 0, 0, 'L');
if ($tipe_dinas == "D1") {
    $pdf->Cell(50, 5, ' : Dinas Dalam Kota', 0, 1, 'L');
}
if ($tipe_dinas == "D2") {
    $pdf->Cell(50, 5, ' : Dinas Luar Kota', 0, 1, 'L');
}

$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['bagian'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$bagian, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['functionname'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$jabatan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['tanggaldinas'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$tanggalperjalanan, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['tanggalkembali'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$tanggalkembali, 0, 1, 'L');
$pdf->Ln();

$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['tujuandantugas']), 0, 1, 'L');

$pdf->SetX(30);
$pdf->Cell(7, 5, strtoupper($_SESSION['lang']['nourut']), 1, 0, 'C');
$pdf->Cell(50, 5, strtoupper($_SESSION['lang']['tujuan']), 1, 0, 'C');
$pdf->Cell(100, 5, strtoupper($_SESSION['lang']['tugas']), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);

/*
$pdf->SetX(30);
$pdf->Cell(7, 5, '1', 1, 0, 'L');
$pdf->Cell(30, 5, $tujuan1, 1, 0, 'L');
$pdf->Cell(120, 5, $tugas1, 1, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(7, 5, '2', 1, 0, 'L');
$pdf->Cell(30, 5, $tujuan2, 1, 0, 'L');
$pdf->Cell(120, 5, $tugas2, 1, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(7, 5, '3', 1, 0, 'L');
$pdf->Cell(30, 5, $tujuan3, 1, 0, 'L');
$pdf->Cell(120, 5, $tugas3, 1, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(7, 5, '4', 1, 0, 'L');
$pdf->Cell(30, 5, $tujuanlain, 1, 0, 'L');
$pdf->Cell(120, 5, $tugaslain, 1, 1, 'L');
*/

$cellWidth=100;


//TUGAS 1

    if($pdf->GetStringWidth($tugas1) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
        $line=1;
    }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($tugas1);    //total panjang teks
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
                $tmpString=substr($tugas1,$startChar,$maxChar);
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


$pdf->SetX(30);
$pdf->Cell(7, (5*$line), '1', 1, 0, 'C', true);
$pdf->Cell(50, (5*$line), $tujuan1, 1, 0);
$xPos=$pdf->GetX();
$yPos=$pdf->GetY();
$pdf->MultiCell(100, 5, $tugas1, 1,1);

//TUGAS 2

    if($pdf->GetStringWidth($tugas2) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
        $line=1;
    }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($tugas2);    //total panjang teks
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
                $tmpString=substr($tugas2,$startChar,$maxChar);
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


$pdf->SetX(30);
$pdf->Cell(7, (5*$line), '2', 1, 0, 'C', true);
$pdf->Cell(50, (5*$line), $tujuan2, 1, 0);
$xPos=$pdf->GetX();
$yPos=$pdf->GetY();
$pdf->MultiCell(100, 5, $tugas2, 1,1);


//TUGAS 3
    if($pdf->GetStringWidth($tugas3) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
        $line=1;
    }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($tugas3);    //total panjang teks
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
                $tmpString=substr($tugas3,$startChar,$maxChar);
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


$pdf->SetX(30);
$pdf->Cell(7, (5*$line), '3', 1, 0, 'C', true);
$pdf->Cell(50, (5*$line), $tujuan3, 1, 0);
$xPos=$pdf->GetX();
$yPos=$pdf->GetY();
$pdf->MultiCell(100, 5, $tugas3, 1,1);


//TUGAS LAINYA
    if($pdf->GetStringWidth($tugaslain) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
        $line=1;
    }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($tugaslain);    //total panjang teks
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
                $tmpString=substr($tugaslain,$startChar,$maxChar);
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

if(($pdf->GetStringWidth($tujuanlain) > 50) && $line<2){ 
    $line=2;    
}

$pdf->SetX(30);
$pdf->Cell(7, (5*$line), '4', 1, 0, 'C', true);
$xPos=$pdf->GetX();
$yPos=$pdf->GetY();

if($pdf->GetStringWidth($tujuanlain) < 50){
$pdf->Cell(50,(5*$line), $tujuanlain, 1);
} else {
$pdf->MultiCell(50,5, $tujuanlain,1);
}

$pdf->SetXY($xPos + 50 , $yPos);
$xPos=$pdf->GetX();
$yPos=$pdf->GetY();

if($pdf->GetStringWidth($tugaslain) < $cellWidth){
$pdf->Cell(100,(5*$line), $tugaslain, 1,1);
}else{
$pdf->MultiCell(100, 5, $tugaslain, 1,1);
}
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['transportasi'].'/'.$_SESSION['lang']['akomodasi']), 0, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['pesawatudara']), 1, 0, 'C');
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['transportasidarat']), 1, 0, 'C');
$pdf->Cell(20, 5, strtoupper($_SESSION['lang']['transportasiair']), 1, 0, 'C');
$pdf->Cell(15, 5, strtoupper($_SESSION['lang']['mess']), 1, 0, 'C');
$pdf->Cell(15, 5, strtoupper($_SESSION['lang']['hotel']), 1, 0, 'C');
$pdf->Cell(20, 5, strtoupper('Mobil Sewa'), 1, 0, 'C');
$pdf->Cell(20, 5, strtoupper('Mobil Dinas'), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->SetX(30);
$pdf->Cell(30, 5, (1 == $pesawat ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(30, 5, (1 == $darat ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(20, 5, (1 == $laut ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(15, 5, (1 == $mess ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(15, 5, (1 == $hotel ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(20, 5, (1 == $mobilsewa ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(20, 5, (1 == $mobildinas ? 'X' : ''), 1, 1, 'C');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['approval_status']), 0, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['bagian']), 1, 0, 'C');
$pdf->Cell(50, 5, strtoupper($_SESSION['lang']['namakaryawan']), 1, 0, 'C');
$pdf->Cell(50, 5, strtoupper($_SESSION['lang']['functionname']), 1, 0, 'C');
$pdf->Cell(35, 5, strtoupper($_SESSION['lang']['keputusan']), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->SetX(30);
$pdf->Cell(30, 5, $hbagian, 1, 0, 'L');
$pdf->Cell(50, 5, $hnama, 1, 0, 'L');
$pdf->Cell(50, 5, $hjabatan, 1, 0, 'L');
$pdf->Cell(35, 5, $statushrd, 1, 1, 'L');
$pdf->SetX(30);
if ('' == $perbagian) {
} else {
    $pdf->Cell(30, 5, $perbagian, 1, 0, 'L');
    $pdf->Cell(50, 5, $pernama, 1, 0, 'L');
    $pdf->Cell(50, 5, $perjabatan, 1, 0, 'L');
    $pdf->Cell(35, 5, $perstatus, 1, 1, 'L');
}

$pdf->SetX(30);
$pdf->Cell(30, 5, $perbagian2, 1, 0, 'L');
$pdf->Cell(50, 5, $pernama2, 1, 0, 'L');
$pdf->Cell(50, 5, $perjabatan2, 1, 0, 'L');
$pdf->Cell(35, 5, $perstatus2, 1, 1, 'L');
$pdf->Ln();
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['uangmuka']), 0, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['diajukan']), 1, 0, 'C');
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['disetujui']), 1, 1, 'C');
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetX(30);
$pdf->Cell(30, 5, number_format($uangmuka, 2, '.', ','), 1, 0, 'R');
$pdf->Cell(30, 5, number_format($dibayar, 2, '.', ','), 1, 1, 'R');
$pdf->Ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['keterangan'], 0, 0, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(150, 5, $keterangan, 0, 'L');
$pdf->Ln();
$pdf->SetX(150);
$pdf->Cell(50, 5, 'Receipt By,', 0, 1, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetX(150);
$pdf->Cell(50, 5, $namakaryawan, 0, 1, 'C');
$pdf->Ln();
$pdf->Output();

?>