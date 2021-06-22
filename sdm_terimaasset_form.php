<?php

	// date_default_timezone_set("Asia/Bangkok");
    require_once 'master_validation.php';
    require_once 'config/connection.php';
    require_once 'lib/eagrolib.php';
    require_once 'lib/fpdf.php';
    require_once 'lib/zLib.php';

    session_start();
    $adata      = $_SESSION['list_terima_asset'];

    $namapt = $adata[0];
    $karyawanid = $adata[1];
    $nik = $adata[2];
    $namakaryawan = $adata[3];
    $tglterima = $adata[4];
    $tglterimaX = tanggalsystem($adata[4]);

    // $str =  "SELECT a.namaorganisasi FROM datakaryawan b LEFT JOIN organisasi a ".
    //         "ON b.kodeorganisasi=a.kodeorganisasi WHERE b.karyawanid='".$karyawanid."'";
    // $res = mysql_query($str);
    // while ($bar = mysql_fetch_object($res)) {
    //     $namapt = $bar->namaorganisasi;
    // }

    class PDF extends FPDF
    {
        public function Header()
        {
            global $namapt;
            $path = '';
            //$this->Image($path, 15, 2, 40);
            $this->SetFont('Arial', 'B', 10);
            $this->SetFillColor(255, 255, 255);
            $this->SetY(22);
            $this->Cell(60, 5, $namapt,0, 1, 'L');
            $this->SetFont('Arial', '', 15);
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Ln();
            $this->Cell(190, 5, 'DAFTAR SERAH TERIMA ASSET',0, 1,  'C');
            $this->SetFont('Arial', '', 6);
            $this->Ln();
            $this->SetY(50);
            $this->SetX(163);
            $this->Cell(30, 10, 'PRINT TIME : '.date('d-m-Y H:i:s'), 0, 1, 'L');
            $this->Line(10, 42, 200, 42);
            $this->Ln();
        }

        public function Footer()
        {
            $this->SetFont('Arial', '', 8);
            $this->SetY(-50);
            $this->SetX(25);
            $this->Cell(30,  10, 'Yang Menerima', 0, 0, 'L');
            $this->SetX(162);
            $this->Cell(30, 10, 'Yang Menyerahkan', 0, 0, 'L');
            $this->SetY(-20);
            $this->SetX(20);
            $this->Cell(10, 10, '(', 0, 0, 'L');
            $this->SetX(50);
            $this->Cell(10, 10, ')', 0, 0, 'L');
            $this->SetX(160);
            $this->Cell(30, 10, '(', 0, 0, 'L');
            $this->SetX(190);
            $this->Cell(30, 10, ')', 0, 0, 'L');
            $this->SetY(-15);
            // $this->SetFont('Arial', 'I', 8);
            // $this->Cell(10, 10, 'Page '.$this->PageNo(), 0, 0, 'C');            
        }
  
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();

    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln();
    $pdf->Cell(30, 5, 'NIK', 0, 0, 'L');
    $pdf->Cell(100, 5, ': '.$nik, 0, 1, 'L');
    $pdf->Cell(30, 5, 'Nama Karyawan', 0, 0, 'L');
    $pdf->Cell(100, 5, ': '.$namakaryawan, 0, 1, 'L');
    $pdf->Cell(30, 5, 'Tanggal Terima', 0, 0, 'L');
    $pdf->Cell(100, 5, ': '.$tglterima, 0, 1, 'L');
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(6,  6, 'NO.', 1, 0, 'L', 1);
    $pdf->Cell(25, 6, 'KODE ASSET', 1, 0, 'C', 1);
    $pdf->Cell(40, 6, 'NAMA ASSET', 1, 0, 'C', 1);
    $pdf->Cell(48, 6, 'KETERANGAN ASSET', 1, 0, 'C', 1);
    $pdf->Cell(48, 6, 'KETERANGAN', 1, 0, 'C', 1);
    $pdf->Cell(28, 6, 'TGL. PENGEMBALIAN', 1, 0, 'C', 1);

    $pdf->ln();
    $str = "SELECT * FROM v_sdm_terimaasset WHERE karyawanid=".$karyawanid." AND tglterima='".$tglterimaX."'";
    $res = mysql_query($str);

    $no = 0;

    while($hasil=mysql_fetch_array($res)){
        ++$no;
        $cellWidth=48; //lebar sel
        $cellHeight=5; //tinggi sel satu baris normal

        //periksa apakah teksnya melibihi kolom?
        $ncol = 0;
        $ctemp = "";
        $line = 0;
        $line1 = 0;
        $keteranganasset = trim($hasil['keteranganasset']);
        $keterangan = trim($hasil['keterangan']);
        $ctemp1 = $keteranganasset;
        $ctemp2 = $keterangan;
    
        //-- Periksa column yang mau diwrap paling panjang jumlah characternya
        if ($pdf->GetStringWidth($keteranganasset) > $pdf->GetStringWidth($keterangan) ) {
            $ctemp = $keteranganasset;
            $ncol=1;
        }else {
            $ctemp = $keterangan;
            $ncol=2;
        }

        if($pdf->GetStringWidth($ctemp) < $cellWidth){
            //jika tidak, maka tidak melakukan apa-apa
            $line=1;
        }else{
            //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
            //dengan memisahkan teks agar sesuai dengan lebar sel
            //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
            
            $textLength=strlen($ctemp);	//total panjang teks
            $errMargin=5;		//margin kesalahan lebar sel, untuk jaga-jaga
            $startChar=0;		//posisi awal karakter untuk setiap baris
            $maxChar=0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
            $textArray=array();	//untuk menampung data untuk setiap baris
            $tmpString="";		//untuk menampung teks untuk setiap baris (sementara)
            
            while($startChar < $textLength){ //perulangan sampai akhir teks
                //perulangan sampai karakter maksimum tercapai
                while( 
                $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                ($startChar+$maxChar) < $textLength ) {
                    $maxChar++;
                    $tmpString=substr($ctemp,$startChar,$maxChar);
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

        if($pdf->GetStringWidth($ctemp1) < $cellWidth){
            //jika tidak, maka tidak melakukan apa-apa
            $line1=1;
        }else{
            //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
            //dengan memisahkan teks agar sesuai dengan lebar sel
            //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
            
            $textLength=strlen($ctemp1);	//total panjang teks
            $errMargin=5;		//margin kesalahan lebar sel, untuk jaga-jaga
            $startChar=0;		//posisi awal karakter untuk setiap baris
            $maxChar=0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
            $textArray=array();	//untuk menampung data untuk setiap baris
            $tmpString="";		//untuk menampung teks untuk setiap baris (sementara)
            
            while($startChar < $textLength){ //perulangan sampai akhir teks
                //perulangan sampai karakter maksimum tercapai
                while( 
                $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                ($startChar+$maxChar) < $textLength ) {
                    $maxChar++;
                    $tmpString=substr($ctemp1,$startChar,$maxChar);
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
            $line1=count($textArray);
        }        
        //tulis selnya
        // $pdf->SetFillColor(255,255,255);
        $pdf->Cell(6,($line * $cellHeight),$no,1,0,'R'); //sesuaikan ketinggian dengan jumlah garis
        $pdf->Cell(25,($line * $cellHeight),$hasil['kodeasset'],1,0); //sesuaikan ketinggian dengan jumlah garis
        $pdf->Cell(40,($line * $cellHeight),$hasil['namaasset'],1,0); //sesuaikan ketinggian dengan jumlah garis

        if ($ncol==1) {
            //memanfaatkan MultiCell sebagai ganti Cell
            //atur posisi xy untuk sel berikutnya menjadi di sebelahnya.
            //ingat posisi x dan y sebelum menulis MultiCell
            $xPos=$pdf->GetX();
            $yPos=$pdf->GetY();
            // $pdf->MultiCell($cellWidth,$cellHeight,$hasil['keteranganasset'],1);
            $pdf->MultiCell($cellWidth,$cellHeight,$ctemp1,1);

            //kembalikan posisi untuk sel berikutnya di samping MultiCell 
            //dan offset x dengan lebar MultiCell
            $pdf->SetXY($xPos + $cellWidth , $yPos);

            // $pdf->Cell(50,($line * $cellHeight),$hasil['keterangan'],1,0); //sesuaikan ketinggian dengan jumlah garis
            $pdf->Cell(48,($line * $cellHeight),$ctemp2,1,0); //sesuaikan ketinggian dengan jumlah garis
            $pdf->Cell(28,($line * $cellHeight),$hasil['tglberakhir'],1,1); //sesuaikan ketinggian dengan jumlah garis

        }else {

            
            $ntemp = abs($line - $line1);
            $ctemp3 = "";
            if (strlen($ctemp1)<= $cellWidth ) {
                $ctemp3 = str_repeat(" ",$cellWidth-strlen($ctemp1));
            }

            for ($i=0;$i<$ntemp;$i++) {
                $ctemp3 .= str_repeat(" ",$cellWidth);
            }

            $xPos=$pdf->GetX();
            $yPos=$pdf->GetY();
            $pdf->MultiCell($cellWidth,$cellHeight,$ctemp1.$ctemp3,1);
            $pdf->SetXY($xPos + $cellWidth , $yPos);


            $xPos=$pdf->GetX();
            $yPos=$pdf->GetY();
            $pdf->MultiCell($cellWidth,$cellHeight,$ctemp2,1);
            $pdf->SetXY($xPos + $cellWidth , $yPos);

            $pdf->Cell(28,($line * $cellHeight),$hasil['tglberakhir'],1,1); //sesuaikan ketinggian dengan jumlah garis
        }    
    }    


    //--- l
    // while ($bar = mysql_fetch_object($res)) {        
    //     $keterangan = ($bar->keterangan === '' ? ' &nbsp' : $bar->keterangan);
    //     $tglberakhir = ($bar->tglberakhir === '' ? ' &nbsp' : tanggalnormal($bar->tglberakhir));
    //     ++$no;
    //     // $pdf->Cell(6, 4, $no.".", 1, 0, 'R', 0);
    //     // // $pdf->Cell(20, 4, tanggalnormal($bar->tglterima), 1, 0, 'C', 0);
    //     // $pdf->Cell(25, 4, $bar->kodeasset, 1, 0, 'L', 0);
    //     // $pdf->Cell(40, 4, $bar->namaasset, 1, 0, 'L', 0);
    //     // $pdf->Cell(55, 4, $bar->keteranganasset, 1, 0, 'L', 0);
    //     // $pdf->Cell(30, 4, $bar->keterangan, 1, 0, 'L', 0);
    //     // $pdf->Cell(28, 4, $tglberakhir, 1, 0, 'C', 0);

    //     $cellWidth=50;

    //     if($pdf->GetStringWidth($bar->keteranganasset) || $pdf->GetStringWidth($bar->keterangan) < $cellWidth){
    //         $lLine=1;
    //     }else{
    //         $lLine=2;
    //     }

    //     $height = 5*$lLine;

    //     $pdf->Cell(6, $height, $no.".", 1, 0, 'R');
    //     $pdf->Cell(25, $height, $bar->kodeasset, 1, 0, 'L');
    //     $pdf->Cell(40, $height, $bar->namaasset, 1,0, 'L');
    //     $xx = $pdf->GetX();
    //     $yy = $pdf->GetY();
    //     $pdf->SetXY($xx,$yy);
    //     $pdf->MultiCell(50, $height, $bar->keteranganasset, 1);
    //     $xx += 50; 
    //     $pdf->SetXY($xx,$yy);
    //     $pdf->MultiCell(50, $height, $bar->keterangan, 1);
    //     $xx += 50;
    //     $pdf->SetXY($xx,$yy);
    //     $pdf->MultiCell(26, $height, $bar->tglberakhir, 1);
    //     $xx += 26;
    //     $pdf->SetXY($xx,$yy);
                
    //     $pdf->ln();
    // }

    $pdf->Output();

?>
