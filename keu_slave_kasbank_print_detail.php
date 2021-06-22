<?php

include_once 'master_validation.php';
include_once 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/formTable.php';
include_once 'lib/zPdfMaster.php';
include_once 'lib/terbilang.php';
include_once 'lib/devLibrary.php';
$proses = $_GET['proses'];
$param = $_GET;
$cols = [];
$whereH = " AND a.notransaksi='".$param['notransaksi']."' ";
$sql="SELECT a.*, b.kode, c.namaakun FROM keu_kasbankht a inner join keu_kasbankdt b ON a.notransaksi=b.notransaksi  inner join keu_5akun c ON a.noakun=c.noakun WHERE 1 ".$whereH." ";
$query=mysql_query($sql);
$kasbankht=mysql_fetch_assoc($query);

$sql2="SELECT * FROM keu_kasbankdt a WHERE 1 ".$whereH." ";
$query2=mysql_query($sql2);

switch ($proses) {
   case 'pdf':
       $pdf = new zPdfMaster('P', 'pt', 'A4');
//       $pdf->_noThead = true;
//       $pdf->setAttr1($title, $align, $length, []);
//       $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
       $height = 15;
       $pdf->AddPage();
       $pdf->SetFillColor(255, 255, 255);
       $pdf->SetFont('Arial', 'B', 9);
       $pdf->SetY(100);
       $pdf->Cell(500, $height, strtoupper($_SESSION['lang']['kasbank']), 0, 0, 'C', 1);
       $pdf->Ln();
$pdf->SetFont('Arial', 'B', 8);
       $pdf->Cell(350, $height, $_SESSION['lang']['notransaksi'].' : '.$kasbankht['kode'].'/'.$kasbankht['notransaksi'], 0, 0, 'L', 1);
       if (!empty($kasbankht['nobayar'])) {
           $nmbyr = $kasbankht['nobayar'];
       } else {
           $nmbyr = '';
       }

       $pdf->Cell($width, $height, 'No. Pembayaran : '.$nmbyr, 0, 1, 'L', 1);

       if ($kasbankht['tanggalposting'] == '0000-00-00') {
           $tglPost = '';
       } else {
           $tglPost = tanggalnormal($kasbankht['tanggalposting']);
       }

       $pdf->Cell($width, $height, $_SESSION['lang']['tanggalposting'].' : '.$tglPost, 0, 1, 'L', 1);
      
       if (!empty($kasbankht['nogiro'])) {
           $nogiro = ', '.$resH[0]['nogiro'];
       }

       if ($kasbankht['tipetransaksi'] == 'K') {
           $byr = $_SESSION['lang']['cgttu'];
       } else {
           $byr = 'Diterima Dengan';
       }

       $pdf->Cell($width, $height, $byr.' : '.$kasbankht['cgttu'].$nogiro, 0, 1, 'L', 1);
       $pdf->Ln();
$pdf->SetFont('Arial', '', 8);
       $pdf->Cell(250, $height, "Catatan : ".$kasbankht['keterangan'], 0, 0, $align[$i], 1);
      $pdf->Ln();
       $pdf->SetFillColor(220, 220, 220);
       $i = 1;

      $pdf->Cell(40, $height, "Nomor", 1, 0, $align[$i], 1);
      $pdf->Cell(60, $height, "Nomor Akun", 1, 0, $align[$i], 1);
      $pdf->Cell(280, $height, "Keterangan", 1, 0, $align[$i], 1);
      $pdf->Cell(80, $height, "Debet", 1, 0,'LR', 1);
      $pdf->Cell(80, $height, "Kredit", 1, 0,'LR', 1);
      $pdf->Ln();


      $pdf->SetFillColor(255, 255, 255);
      if($kasbankht['tipetransaksi']=="M"){
        $pdf->Cell(40, $height, $i, 1, 0, $align[$i], 1);
        $pdf->Cell(60, $height, $kasbankht['noakun'], 1, 0, $align[$i], 1);
        $pdf->Cell(280, $height, $kasbankht['namaakun'], 1, 0, $align[$i], 1);
        $pdf->Cell(80, $height, number_format($kasbankht['jumlah'],2), 1, 0,'R', 1);  
        $pdf->Cell(80, $height, "", 1, 0,'R', 1);
        $totdebet=$kasbankht['jumlah'];
        $pdf->Ln();
        $i = $i+1;
        }
        
      
      while($kasbankdt=mysql_fetch_array($query2)){

      $cellWidth=280;


      if($pdf->GetStringWidth($kasbankdt['keterangan2']) < $cellWidth){
        //jika tidak, maka tidak melakukan apa-apa
          $line=1;
      }else{

        //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
        //dengan memisahkan teks agar sesuai dengan lebar sel
        //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
        
        $textLength=strlen($kasbankdt['keterangan2']);    //total panjang teks
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
                $tmpString=substr($kasbankdt['keterangan2'],$startChar,$maxChar);
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

        $pdf->Cell(40, $height*$line, $i, 1, 0, $align[$i], 1);
        $pdf->Cell(60, $height*$line, $kasbankdt['noakun'], 1, 0, $align[$i], 1);
        $xPos=$pdf->GetX();
        $yPos=$pdf->GetY();
        $pdf->MultiCell(280, $height, $kasbankdt['keterangan2'], 1);
        if($kasbankdt['tipetransaksi']=="K"){
          $pdf->SetXY($xPos + $cellWidth , $yPos);  
          $pdf->Cell(80, $height*$line, number_format($kasbankdt['jumlah'],2), 1, 0,'R', 1);  
          $pdf->Cell(80, $height*$line, "", 1, 0,'R', 1);
          $totdebet=$totdebet+$kasbankdt['jumlah'];
        }else{
          $pdf->SetXY($xPos + $cellWidth , $yPos);
          $pdf->Cell(80, $height*$line, "", 1, 0,'R', 1);  
          $pdf->Cell(80, $height*$line, number_format($kasbankdt['jumlah'],2), 1, 0,'R', 1);
          $totkredit=$totkredit+$kasbankdt['jumlah'];
        }
        ++$i;
        $pdf->Ln();
       }
       if($kasbankht['tipetransaksi']=="K"){
        $pdf->Cell(40, $height, $i, 1, 0, $align[$i], 1);
        $pdf->Cell(60, $height, $kasbankht['noakun'], 1, 0, $align[$i], 1);
        $pdf->Cell(280, $height, $kasbankht['namaakun'], 1, 0, $align[$i], 1);
        $pdf->Cell(80, $height, "", 1, 0,'R', 1);  
        $pdf->Cell(80, $height, number_format($kasbankht['jumlah'],2), 1, 0,'R', 1);
        $totkredit=$kasbankht['jumlah'];
        $pdf->Ln();
        }
     
        $pdf->Cell(380, $height, "Total", 1, 0, 'LR', 1);
        $pdf->Cell(80, $height, number_format($totdebet,2), 1, 0, 'R', 1);
        $pdf->Cell(80, $height, number_format($totkredit,2), 1, 0, 'R', 1);

        $pdf->Ln();
        $optMt = makeOption($dbname, 'setup_matauang', 'kode,matauang');
        $sen=explode(".",number_format($totdebet,2));
        if($sen[1]>0){
          $nilaisen=terbilang($sen[1])." Sen ";
        }
        $pdf->MultiCell($width, $height, $_SESSION['lang']['terbilang'].' : '.terbilang($totdebet, 2).' Rupiah '.$nilaisen , 0);

               $pdf->Ln();
       
       $pdf->SetFillColor(220, 220, 220);
       if ($param['tipetransaksi'] == 'M') {
           $pdf->Cell(108, $height, 'Diterima Oleh', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Diperiksa Oleh', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Dibukukan Oleh', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Disetujui Oleh', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Diterima Dari', 1, 0, 'C', 1);
           $pdf->Ln();
           $pdf->SetFillColor(255, 255, 255);

           for ($i = 0; $i < 3; ++$i) {
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Ln();
           }
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);


       } else {
           $pdf->Cell(108, $height, 'Dibayarkan', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Diperiksa', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Dibukukan', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Disetujui', 1, 0, 'C', 1);
           $pdf->Cell(108, $height, 'Diterima', 1, 0, 'C', 1);
           $pdf->Ln();
           $pdf->SetFillColor(255, 255, 255);

           for ($i = 0; $i < 3; ++$i) {
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Cell(108, $height, '', 'LR', 0, 'C', 1);
               $pdf->Ln();
           }
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
           $pdf->Cell(108, $height, '', 'BLR', 0, 'C', 1);
       }

       $pdf->Output();

       break;
     }
/*


    case 'html':
//        $whereH = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $whereH = "notransaksi='".$param['notransaksi']."' ";//and kodeorg='".$param['kodeorg']."' and noakun='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $queryH = selectQuery($dbname, 'keu_kasbankht', '*', $whereH);
        $resH = fetchData($queryH);
        $userId = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', "karyawanid='".$resH[0]['userid']."'");
        $namaakunhutang = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', "noakun='".$resH[0]['noakunhutang']."'");
        $col1 = 'noakun,jumlah,noaruskas,matauang,kode,nik,keterangan2,kodesupplier';
        $cols = ['nomor', 'noakun', 'namaakun', 'matauang', 'keterangan', 'kodesupplier', 'debet', 'kredit'];
//        $where = "notransaksi='".$param['notransaksi']."' and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $where = "notransaksi='".$param['notransaksi']."' ";//and kodeorg='".$param['kodeorg']."' and noakun2a='".$param['noakun']."' and tipetransaksi='".$param['tipetransaksi']."'";
        $query = selectQuery($dbname, 'keu_kasbankdt', $col1, $where);
        $res = fetchData($query);
        $kary = $supp = [];
        foreach ($res as $row) {
            if (!empty($row['nik'])) {
                $kary[$row['nik']] = $row['nik'];
            }

            if (!empty($row['kodesupplier'])) {
                $supp[$row['kodesupplier']] = $row['kodesupplier'];
            }
        }
        if (empty($res)) {
            echo 'Data Empty';
            exit();
        }

        $whereAkun = 'noakun in (';
        $whereAkun .= "'".$resH[0]['noakun']."'";
        $whereAkun .= ",'".$resH[0]['noakunhutang']."'";
        $whereKary = $whereSupp = '';
        foreach ($res as $key => $row) {
            if (!empty($whereKary)) {
                $whereKary .= ',';
            }

            if (!empty($whereSupp)) {
                $whereSupp .= ',';
            }

            $whereAkun .= ",'".$row['noakun']."'";
            $whereKary .= "'".$row['nik']."'";
            $whereSupp .= "'".$row['kodesupplier']."'";
        }
        $whereAkun .= ')';
        $optKary = makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan', 'karyawanid in ('.$whereKary.')');
        $optSupp = makeOption($dbname, 'log_5supplier', 'supplierid,namasupplier', 'supplierid in ('.$whereSupp.')');
        $optAkun = makeOption($dbname, 'keu_5akun', 'noakun,namaakun', $whereAkun);
        $optHutangUnit = ['Tidak', 'Ya'];
        $data = [];
        $totalDebet = 0;
        $totalKredit = 0;
        $i = 1;
        $data[$i] = ['nomor' => $i, 'noakun' => $resH[0]['noakun'], 'namaakun' => $optAkun[$resH[0]['noakun']], 'matauang' => $resH[0]['matauang'], '-' => '', '- ' => '', 'debet' => 0, 'kredit' => 0];
        if ($param['tipetransaksi'] == 'M') {
            $data[$i]['debet'] = $resH[0]['jumlah'];
            $totalDebet += $resH[0]['jumlah'];
        } else {
            $data[$i]['kredit'] = $resH[0]['jumlah'];
            $totalKredit += $resH[0]['jumlah'];
        }

        $i++;
        foreach ($res as $row) {
            $data[$i] = ['nomor' => $i, 'noakun' => $row['noakun'], 'namaakun' => (isset($optAkun[$row['noakun']]) ? $optAkun[$row['noakun']] : ''), 'matauang' => $row['matauang'], 'keterangan2' => $row['keterangan2'], 'kodesupplier' => $row['kodesupplier'], 'debet' => 0, 'kredit' => 0];
            
            if ($param['tipetransaksi'] == 'M' && 0 < $row['jumlah'] ) {
                $data[$i]['kredit'] = $row['jumlah'];
                $totalKredit += $row['jumlah'];
            } else {
                if ($param['tipetransaksi'] == 'K' && $row['jumlah'] < 0) {
                    $data[$i]['kredit'] = $row['jumlah'] * -1;
                    $totalKredit += $row['jumlah'] * -1;
                } else {
                    if ($param['tipetransaksi'] == 'M' && $row['jumlah'] < 0) {
                        $data[$i]['debet'] = $row['jumlah'] * -1;
                        $totalDebet += $row['jumlah'] * -1;
                    } else {
                        $data[$i]['debet'] = $row['jumlah'];
                        $totalDebet += $row['jumlah'];
                    }
                }
            }

            $i++;
        }
        if (!empty($data)) {
            foreach ($data as $c => $key) {
                $sort_debet[] = $key['debet'];
                $sort_kredit[] = $key['kredit'];
            }
        }

        if (!empty($data)) {
            array_multisort($sort_debet, SORT_DESC, $sort_kredit, SORT_ASC, $data);
        }

        $align = explode(',', 'R,R,L,L,R,R');
        $length = explode(',', '7,12,35,10,18,18');
        $title = $_SESSION['lang']['kasbank'];
        $titleDetail = 'Detail';
        $tab .= '<link rel=stylesheet type=text/css href=style/generic.css>';
        $tab .= '<fieldset><legend>'.$title.'</legend>';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><tbody class=rowcontent>';
        $tab .= '<tr><td>'.$_SESSION['lang']['kodeorganisasi'].'</td><td> :</td><td> '.$_SESSION['empl']['lokasitugas'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['notransaksi'].'</td><td> :</td><td> '.$res[0]['kode'].'/'.$param['notransaksi'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['cgttu'].'</td><td> :</td><td> '.$resH[0]['cgttu'].'</td></tr>';
        $tab .= '<tr><td>'.$_SESSION['lang']['terbilang'].'</td><td> :</td><td> '.terbilang($resH[0]['jumlah'], 2).' rupiah'.'</td></tr>';
        if ($resH[0]['hutangunit'] == 1) {
            $tab .= '<tr><td>'.$_SESSION['lang']['hutangunit'].'</td><td> :</td><td> '.'Unit payable Account '.$resH[0]['pemilikhutang'].' : '.$namaakunhutang[$resH[0]['noakunhutang']].'</td></tr>';
        }

        $tab .= '</tbody></table><br />';
        $tab .= '<table cellpadding=1 cellspacing=1 border=0 width=100% class=sortable><thead><tr class=rowheader>';
        foreach ($cols as $column) {
            $tab .= '<td>'.$_SESSION['lang'][$column].'</td>';
        }
        $tab .= '</tr></thead><tbody class=rowcontent>';
        $nyomor = 0;
        foreach ($data as $key => $row) {
            ++$nyomor;
            $tab .= '<tr>';
            foreach ($row as $key => $cont) {
                if ($key == 'nomor') {
                    $tab .= '<td>'.$nyomor.'</td>';
                } else {
                    if ($key == 'kodesupplier') {
                        $tab .= '<td>'.$optSupp[$cont].'</td>';
                    } else {
                        if ($key == 'debet' || $key == 'kredit') {
                            $tab .= '<td align=right>'.number_format($cont, 0).'</td>';
                        } else {
                            $tab .= '<td>'.$cont.'</td>';
                        }
                    }
                }
            }
            $tab .= '</tr>';
        }
        $tab .= '<tr><td colspan=6 align=center>Total</td><td align=right>'.number_format($totalDebet, 0).'</td><td align=right>'.number_format($totalKredit, 0).'</td></tr>';
        $tab .= '</tbody></table> <br />';
        if ($proses=='html') {
            echo $tab;
        }
    // if ($proses=='pdf') {
    //     generateTablePDF($tab,true,'Legal','landscape');
    // }
        break;
    default:
        break;
}
*/
?>