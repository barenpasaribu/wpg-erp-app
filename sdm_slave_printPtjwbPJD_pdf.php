<?php



require_once 'master_validation.php';
require_once 'config/connection.php';
require_once 'lib/eagrolib.php';
require_once 'lib/fpdf.php';
$notransaksi = $_GET['notransaksi'];

class PDF extends FPDF
{
    public function Header()
    {
        global $namapt;
        if ('SSP' == $_SESSION['org']['kodeorganisasi']) {
            $path = 'images/SSP_logo.jpg';
        } else {
            if ('MJR' == $_SESSION['org']['kodeorganisasi']) {
                $path = 'images/MI_logo.jpg';
            } else {
                if ('HSS' == $_SESSION['org']['kodeorganisasi']) {
                    $path = 'images/HS_logo.jpg';
                } else {
                    if ('BNM' == $_SESSION['org']['kodeorganisasi']) {
                        $path = 'images/BM_logo.jpg';
                    }
                }
            }
        }

        //$this->Image($path, 15, 2, 40);
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(255, 255, 255);
        $this->SetY(22);
        $this->Cell(60, 5, strtoupper($namapt), 0, 1, 'C');
        $this->SetFont('Arial', '', 15);
        $this->Cell(190, 5, '', 0, 1, 'C');
        $this->SetFont('Arial', '', 6);
        $this->SetY(30);
        $this->SetX(163);
        $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
        $this->Line(10, 32, 200, 32);
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
$closure = 'Open';
while ($bar = mysql_fetch_object($res)) {
    $jabatan = '';
    $namakaryawan = '';
    $bagian = '';
    $karyawanid = '';
    $strc = "select a.namakaryawan,a.karyawanid,a.bagian,b.namajabatan \r\n                    from ".$dbname.'.datakaryawan a left join  '.$dbname.".sdm_5jabatan b\r\n                        on a.kodejabatan=b.kodejabatan\r\n                        where a.karyawanid=".$bar->karyawanid;
    $resc = mysql_query($strc);
    while ($barc = mysql_fetch_object($resc)) {
        $jabatan = $barc->namajabatan;
        $namakaryawan = $barc->namakaryawan;
        $bagian = $barc->bagian;
        $karyawanid = $barc->karyawanid;
    }
    $strw = 'select a.namaorganisasi from '.$dbname.'.datakaryawan b left join '.$dbname.".organisasi a \r\n          on b.kodeorganisasi=a.kodeorganisasi where b.karyawanid=".$karyawanid;
    $resw = mysql_query($strw);
    while ($barw = mysql_fetch_object($resw)) {
        $namapt = $barw->namaorganisasi;
    }
    $kodeorg = $bar->kodeorg;
    $persetujuan = $bar->persetujuan;
    $hrd = $bar->hrd;
    $tujuan3 = $bar->tujuan3;
    $tujuan2 = $bar->tujuan2;
    $tujuan1 = $bar->tujuan1;
    $tanggalperjalanan = tanggalnormal($bar->tanggalperjalanan);
    $tanggalkembali = tanggalnormal($bar->tanggalkembali);
    $tanggal_penyelesaian2 = tanggalnormal($bar->tanggalkembali);
    $uangmuka = $bar->uangmuka;
    $dibayar = $bar->dibayar;
    $tugas1 = $bar->tugas1;
    $tugas2 = $bar->tugas2;
    $tugas3 = $bar->tugas3;
    $tujuanlain = $bar->tujuanlain;
    $tugaslain = $bar->tugaslain;
    $pesawat = $bar->pesawat;
    $darat = $bar->darat;
    $laut = $bar->laut;
    $mess = $bar->mess;
    $hotel = $bar->hotel;
    $tanggal_penyelesaian = $bar->tanggal_penyelesaian;
    if ($bar->tanggal_penyelesaian != "0000-00-00") {
        $closure = 'Closed';
    }

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

    $perjabatan = '';
    $perbagian = '';
    $pernama = '';
    $strf = 'select a.bagian,b.namajabatan,a.namakaryawan from '.$dbname.".datakaryawan a left join\r\n               ".$dbname.".sdm_5jabatan b on a.kodejabatan=b.kodejabatan\r\n                   where karyawanid=".$persetujuan;
    $resf = mysql_query($strf);
    while ($barf = mysql_fetch_object($resf)) {
        $perjabatan = $barf->namajabatan;
        $perbagian = $barf->bagian;
        $pernama = $barf->namakaryawan;
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
$pdf->Cell(30, 5, $_SESSION['lang']['nokaryawan'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$karyawanid, 0, 1, 'L');
$pdf->SetX(20);
$pdf->Cell(30, 5, $_SESSION['lang']['namakaryawan'], 0, 0, 'L');
$pdf->Cell(50, 5, ' : '.$namakaryawan, 0, 1, 'L');
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
$pdf->Ln();
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
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['transportasiair']), 1, 0, 'C');
$pdf->Cell(30, 5, strtoupper($_SESSION['lang']['mess']), 1, 0, 'C');
$pdf->Cell(37, 5, strtoupper($_SESSION['lang']['hotel']), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->SetX(30);
$pdf->Cell(30, 5, (1 == $pesawat ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(30, 5, (1 == $darat ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(30, 5, (1 == $laut ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(30, 5, (1 == $mess ? 'X' : ''), 1, 0, 'C');
$pdf->Cell(37, 5, (1 == $hotel ? 'X' : ''), 1, 1, 'C');
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
$pdf->SetX(20);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(172, 5, strtoupper($_SESSION['lang']['pertanggungjawabandinas']), 0, 1, 'L');
$pdf->SetX(30);
$pdf->Cell(10, 5, 'NO', 1, 0, 'C');
$pdf->Cell(45, 5, strtoupper($_SESSION['lang']['jenisbiaya']), 1, 0, 'C');
$pdf->Cell(19, 5, strtoupper($_SESSION['lang']['tanggal']), 1, 0, 'C');
$pdf->Cell(56, 5, strtoupper($_SESSION['lang']['keterangan']), 1, 0, 'C');
$pdf->Cell(20, 5, strtoupper($_SESSION['lang']['jumlah']), 1, 0, 'C');
$pdf->Cell(20, 5, strtoupper($_SESSION['lang']['disetujui']), 1, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$str = 'select a.*,b.keterangan as jns from '.$dbname.".sdm_pjdinasdt a\r\n      left join ".$dbname.".sdm_5jenisbiayapjdinas b on a.jenisbiaya=b.id\r\n          where a.notransaksi='".$notransaksi."' order by tanggal asc";
$res = mysql_query($str);
$no = 0;
$total = 0;
$total1 = 0;
while ($bar = mysql_fetch_object($res)) {
    ++$no;
    $pdf->SetX(30);
    $pdf->Cell(10, 5, $no, 1, 0, 'L');
    $pdf->Cell(45, 5, $bar->jns, 1, 0, 'L');
    $pdf->Cell(19, 5, tanggalnormal($bar->tanggal), 1, 0, 'L');
    $pdf->Cell(56, 5, $bar->keterangan, 1, 0, 'L');
    $pdf->Cell(20, 5, number_format($bar->jumlah, 2, '.', '.'), 1, 0, 'R');
    $pdf->Cell(20, 5, number_format($bar->jumlahhrd, 2, '.', '.'), 1, 1, 'R');
    $total += $bar->jumlah;
    $total1 += $bar->jumlahhrd;
}
$balance = $dibayar - $total1;
$pdf->SetX(30);
$pdf->Cell(130, 5, 'TOTAL', 1, 0, 'C');
$pdf->Cell(20, 5, number_format($total, 2, '.', '.'), 1, 0, 'R');
$pdf->Cell(20, 5, number_format($total1, 2, '.', '.'), 1, 1, 'R');
$pdf->Ln();
$pdf->SetX(30);
$pdf->Cell(50, 5, $_SESSION['lang']['saldo'].': '.number_format($balance, 2, '.', '.').' *['.$closure.']', 0, 1, 'L');

if ($tanggal_penyelesaian != "0000-00-00") {
    // print_r($tanggal_penyelesaian);
    // die();
    $pdf->SetX(30);
    $pdf->Cell(50, 5, 'Tanggal: '.$tanggal_penyelesaian2, 0, 1, 'L');
    $pdf->Ln(); 
}

$pdf->Ln();
$pdf->Cell(63, 5, $_SESSION['lang']['dibuat'], 0, 0, 'C');
$pdf->Cell(63, 5, $_SESSION['lang']['verifikasi'], 0, 0, 'C');
$pdf->Cell(63, 5, $_SESSION['lang']['dstujui_oleh'], 0, 1, 'C');
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(63, 5, $namakaryawan, 0, 0, 'C');
$pdf->Cell(63, 5, $hnama, 0, 0, 'C');
$pdf->Cell(63, 5, $pernama, 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(63, 5, $_SESSION['lang']['karyawan'], 0, 0, 'C');
$pdf->Cell(63, 5, 'HRD', 0, 0, 'C');
$pdf->Cell(63, 5, $_SESSION['lang']['atasan'], 0, 1, 'C');
$pdf->Output();

?>