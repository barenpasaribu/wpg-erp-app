<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
//require_once('lib/zFunction.php');
require_once('lib/fpdf.php');
include_once('lib/zMysql.php');

//=============

//create Header
class PDF extends FPDF
{
	
	function Header()
	{
 	global $conn;
	global $dbname;
    global $userid;

			   $str1="select * from ".$dbname.".organisasi where induk='MHO' and tipe='PT'"; 
			   $res1=mysql_query($str1);
			   while($bar1=mysql_fetch_object($res1))
			   {
			   	 $nama=$bar1->namaorganisasi;
				 $alamatpt=$bar1->alamat.", ".$bar1->wilayahkota;
				 $telp=$bar1->telepon;				 
			   }    

		$path='images/logo.jpg';
	    $this->Image($path,15,5,40);	
		$this->SetFont('Arial','B',10);
		$this->SetFillColor(255,255,255);	
		$this->SetX(55);   
	    $this->Cell(60,5,$nama,0,1,'L');	 
		$this->SetX(55); 		
	    $this->Cell(60,5,$alamatpt,0,1,'L');	
		$this->SetX(55); 			
		$this->Cell(60,5,"Tel: ".$telp,0,1,'L');	
		$this->Ln();
		$this->SetFont('Arial','B',8); 
		$this->Cell(20,5,$namapt,'',1,'L');
        $this->SetFont('Arial','',8);
		$this->Line(10,30,200,30);	
//			$this->Cell(35,5,$_SESSION['lang']['pabrik'],'',0,'L');
//			$this->Cell(2,5,':','',0,'L');
//			$this->Cell(80,5,$namapt,'',0,'L');		
//			$this->Cell(20,5,$_SESSION['lang']['statasiun'],'',0,'L');
//			$this->Cell(2,5,':','',0,'L');
//			$this->Cell(35,5,$rNm['namaorganisasi'],0,1,'L');
			//		$this->Cell(140,5,' ','',0,'R');	
			//			$this->Cell(140,5,' ','',0,'R');
				
		//	$this->Cell(35,5,'User','',0,'L');
//			$this->Cell(2,5,':','',0,'L');
//			$this->Cell(80,5,$_SESSION['standard']['username'],'',1,'L');

		
			//	        $this->Ln();	
     $this->Ln();
	 
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
			
	$pdf->Ln();
	
	$pdf->SetFont('Arial','U',15);
	$pdf->SetY(40);
	$pdf->Cell(190,5,$_SESSION['lang']['premiTransportJarak'],0,1,'C');	
	$pdf->Ln();	
	$pdf->SetFont('Arial','B',9);	
	$pdf->SetFillColor(220,220,220);
    $pdf->Cell(8,5,'No',1,0,'L',1);
	$pdf->Cell(20,5,$_SESSION['lang']['keycode'],1,0,'C',1);	
	$pdf->Cell(20,5,$_SESSION['lang']['nomor'],1,0,'C',1);
	$pdf->Cell(25,5,$_SESSION['lang']['vhc_jenis_pekerjaan'],1,0,'C',1);
	$pdf->Cell(25,5,$_SESSION['lang']['jarakdari'],1,0,'C',1);
	$pdf->Cell(25,5,$_SESSION['lang']['jaraksampai'],1,0,'C',1);		
	$pdf->Cell(25,5,$_SESSION['lang']['premilebihbasis'],1,0,'C',1);
	$pdf->Cell(20,5,$_SESSION['lang']['vhc_posisi'],1,0,'C',1);
	$pdf->Cell(20,5,$_SESSION['lang']['nilaipremi'],1,1,'C',1);
	
	
	//$pdf->Cell(25,5,'Total',1,1,'C',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
		$str="select * from ".$dbname.".kebun_5ratetransport where jumlahtrip='0' order by keycode desc";
		$re=mysql_query($str);
		$no=0;
		$arrPos=array("Sopir","Kondektur");		   	
		while($res=mysql_fetch_assoc($re))
		{
			$no+=1;

			$pdf->Cell(8,5,$no,1,0,'L',1);
			$pdf->Cell(20,5,$res['keycode'],1,0,'C',1);	
			$pdf->Cell(20,5,$res['nomor'],1,0,'C',1);
			$pdf->Cell(25,5,$res['tipeangkutan'],1,0,'C',1);
			$pdf->Cell(25,5,$res['jarakdari'],1,0,'C',1);
			$pdf->Cell(25,5,$res['jaraksampai'],1,0,'C',1);		
			$pdf->Cell(25,5,$res['jumlahbasis'],1,0,'C',1);
			$pdf->Cell(20,5,$arrPos[$res['jobposition']],1,0,'C',1);
			$pdf->Cell(20,5,$res['rate'],1,1,'C',1);
		}
		

	
	$pdf->Output();
?>
