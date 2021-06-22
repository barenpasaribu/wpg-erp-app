<?
require_once('master_validation.php');
require_once('config/connection.php');
include_once 'lib/eagrolib.php';
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

 
$table = $_GET['table'];
$column = $_GET['column'];
$where = $_GET['cond'];



//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
        global $userid;
	global $notransaksi;
	global $kodevhc;
	global $posting;
        global $bar;
	
			$test=explode(',',$_GET['column']);
			$notransaksi=$test[0];
			$kodevhc=$test[1];
			$str="select * from ".$dbname.".".$_GET['table']."  where notransaksi='".$notransaksi."' and kodevhc='".$kodevhc."'";
			//echo $str;exit();
			$res=mysql_query($str);
			$bar=mysql_fetch_object($res);
			$posting=$bar->posting;		
			//ambil nama pt
			   $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $namapt=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   }    
	   $sql2="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->updateby."'";
	   $query2=mysql_query($sql2) or die(mysql_error());
	   $res2=mysql_fetch_object($query2);
	   
	   $sql5="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$bar->postingby."'";
	   $query5=mysql_query($sql5) or die(mysql_error());
	   $res5=mysql_fetch_object($query5);
	   
	   $sql3="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$bar->kodeorg."'";
	   $query3=mysql_query($sql3) or die(mysql_error());
	   $res3=mysql_fetch_object($query3); 
	   
	   $sqlJnsVhc="select namajenisvhc from ".$dbname.".vhc_5jenisvhc where jenisvhc='".$bar->jenisvhc ."'";
	   $qJnsVhc=mysql_query($sqlJnsVhc) or die(mysql_error());
	   $rJnsVhc=mysql_fetch_assoc($qJnsVhc);
	   
	   $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar->jenisbbm."'";
	   $qBrg=mysql_query($sBrg) or die(mysql_error());
	   $rBrg=mysql_fetch_assoc($qBrg);
	
		$path='images/logo.jpg';
	    $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$namapt,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','U',15);
		$this->SetY(35);
		$this->Cell(190,5,$_SESSION['lang']['laporanPekerjaan'],0,1,'C');		
		$this->SetFont('Arial','',6); 
		$this->SetY(27);
		$this->SetX(163);
        $this->Cell(30,10,'PRINT TIME : '.date('d-m-Y H:i:s'),0,1,'L');		
		$this->Line(10,27,200,27);	
		$this->Ln();
		$this->SetFont('Arial','',9); 
		$this->Cell(30,4,$_SESSION['lang']['notransaksi'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->notransaksi,0,1,'L'); 				
		$this->Cell(30,4,$_SESSION['lang']['tanggal'],0,0,'L'); 
		$this->Cell(40,4,": ".tanggalnormal($bar->tanggal),0,1,'L'); 
		$this->Cell(30,4,$_SESSION['lang']['namaorganisasi'],0,0,'L'); 
		$this->Cell(40,4,": ".$res3->namaorganisasi." [".$bar->kodeorg."]",0,1,'L'); 
		$this->Cell(30,4,$_SESSION['lang']['jenisvch'],0,0,'L'); 
		$this->Cell(40,4,": ".$rJnsVhc['namajenisvhc'],0,1,'L'); 		  
		$this->Cell(30,4,$_SESSION['lang']['kodevhc'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->kodevhc,0,1,'L'); 
		/*$this->Cell(30,4,$_SESSION['lang']['vhc_kmhm_awal'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->kmhmawal,0,1,'L'); 
		$this->Cell(30,4,$_SESSION['lang']['vhc_kmhm_akhir'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->kmhmakhir,0,1,'L');
		$this->Cell(30,4,$_SESSION['lang']['satuan'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->satuan,0,1,'L');*/
		$this->Cell(30,4,$_SESSION['lang']['vhc_jenis_bbm'],0,0,'L'); 
		$this->Cell(40,4,": ".$rBrg['namabarang'],0,1,'L');
		$this->Cell(30,4,$_SESSION['lang']['vhc_jumlah_bbm'],0,0,'L'); 
		$this->Cell(40,4,": ".$bar->jlhbbm,0,1,'L');
		$this->Cell(30,4,$_SESSION['lang']['dbuat_oleh'],0,0,'L'); 
		$this->Cell(40,4,": ".$res2->namakaryawan,0,1,'L');  
		$this->Cell(30,4,$_SESSION['lang']['posted'],0,0,'L'); 
		$this->Cell(40,4,": ".$res5->namakaryawan,0,1,'L');  
	
	}
	
	
	function Footer()
	{
	    $this->SetY(-15);
	    $this->SetFont('Arial','I',8);
	    $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
	}

}

	$pdf=new PDF('P','mm','A4');
	$pdf->AddPage();
			
//ambil kelengkapan

	$pdf->Ln();
	if($posting<1)
	{
		$pdf->SetY(80);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(190,5,$_SESSION['lang']['belumposting'],0,0,'C');
	}
	$pdf->SetFont('Arial','U',10);
	$pdf->SetY(90);
	$pdf->Cell(190,5,$_SESSION['lang']['vhc_detail_pekerjaan'],0,1,'L');	
	$pdf->Ln(5);	
	$pdf->SetFont('Arial','B',7);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    $pdf->Cell(25,5,$_SESSION['lang']['vhc_jenis_pekerjaan'],1,0,'L',1);
    $pdf->Cell(20,5,$_SESSION['lang']['alokasibiaya'],1,0,'L',1);		
    $pdf->Cell(20,5,$_SESSION['lang']['vhc_kmhm_awal'],1,0,'L',1);	
	$pdf->Cell(20,5,$_SESSION['lang']['vhc_kmhm_akhir'],1,0,'L',1);	
  	$pdf->Cell(25,5,$_SESSION['lang']['vhc_berat_muatan'],1,0,'L',1);	
	$pdf->Cell(15,5,$_SESSION['lang']['jumlahrit'],1,0,'C',1);
	$pdf->Cell(60,5,$_SESSION['lang']['keterangan'],1,1,'C',1);
	//$pdf->Cell(25,5,'Total',1,1,'C',1);

		$pdf->SetFillColor(255,255,255);
	    $pdf->SetFont('Arial','',7);
		
		$str="select * from ".$dbname.".vhc_rundt   where notransaksi='".$notransaksi."'"; //echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($res=mysql_fetch_assoc($re))
		{
			$no+=1;
		   	
			$pdf->Cell(8,5,$no,1,0,'L',1);
			$pdf->Cell(25,5,$res['jenispekerjaan'],1,0,'L',1);
			$pdf->Cell(20,5,$res['alokasibiaya'],1,0,'L',1);		
			$pdf->Cell(20,5,$res['kmhmawal'],1,0,'R',1);
			$pdf->Cell(20,5,$res['kmhmakhir'],1,0,'R',1);	
			$pdf->Cell(25,5,$res['beratmuatan'],1,0,'R',1);	
			$pdf->Cell(15,5,$res['jumlahrit'],1,0,'R',1);
			$pdf->Cell(60,5,$res['keterangan'],1,1,'L',1);
    	   
		}
	$ary=$pdf->GetY();
	$pdf->SetFont('Arial','U',10);
	$pdf->SetY($ary+25);
	$pdf->Cell(190,5,$_SESSION['lang']['vhc_detail_operator'],0,1,'L');	
	$pdf->Ln();	
	$pdf->SetFont('Arial','B',7);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
    $pdf->Cell(40,5,$_SESSION['lang']['namakaryawan'],1,0,'L',1);
    $pdf->Cell(25,5,$_SESSION['lang']['vhc_posisi'],1,0,'L',1);		
    $pdf->Cell(25,5,$_SESSION['lang']['upahpremi']." hi",1,0,'L',1);		
    $pdf->Cell(25,5,$_SESSION['lang']['upahpremi']." sdi",1,1,'L',1);		
   
	//$pdf->Cell(25,5,'Total',1,1,'C',1);

		$pdf->SetFillColor(255,255,255);
	    $pdf->SetFont('Arial','',6);
		$arrPos=array("Sopir","Kondektur");
		$str="select * from ".$dbname.".vhc_runhk  where notransaksi='".$notransaksi."'"; //echo $str;exit();
		$re=mysql_query($str);
		$no=0;
		while($res=mysql_fetch_assoc($re))
		{
			$no+=1;
			$sql5="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";
			$query5=mysql_query($sql5) or die(mysql_error());
			$res5=mysql_fetch_object($query5);
                        $strd="select sum(premi) as premisdi from ".$dbname.". vhc_runhk_vw    
                               where idkaryawan='".$res['idkaryawan']."' and tanggal like '".substr($bar->tanggal,0,7)."%'";
                        //exit("error".$strd);
                        $qstrd=mysql_query($strd) or die(mysql_error($conn));
                        $rstrd=mysql_fetch_assoc($qstrd);
			$pdf->Cell(8,5,$no,1,0,'L',1);
			$pdf->Cell(40,5,$res5->namakaryawan,1,0,'L',1);
                        $pdf->Cell(25,5,$arrPos[$res['posisi']],1,0,'L',1);	
                        $pdf->Cell(25,5,number_format($res['premi'],0),1,0,'R',1);
                        $pdf->Cell(25,5,number_format($rstrd['premisdi'],0),1,1,'R',1);
				
		}
			
//footer================================
 
	
	$pdf->Output();
?>
