<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/fpdf.php');

$proses = $_GET['proses'];
$comId=$_GET['pt'];
$kdVhc=$_GET['kdVhc'];
$jnsVhc=$_GET['jnsVhc'];
$period=$_GET['periode'];

$param = $_POST;
//$where=" kodeorg='".$kdOrg."' and tanggal like '%".$tngl."%'";


/** Report Prep **/
$title = $_SESSION['lang']['laporanPenggunaanKomponen'];
/** Output Format **/
switch($proses) {
    case 'pdf':
        class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $comId;
				global $kdVhc;
				global $jnsVhc;
				global $period;
				
                
                # Bulan
               // $optBulan = 
                
                # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 12;
                $path='images/logo.jpg';
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                    $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                $this->Ln();
                $this->SetFont('Arial','',8);
				if($comId!='')
				{
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodeorg'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$comId,'',0,'L');
				}
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['user'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(15/100*$width,$height, $_SESSION['standard']['username'],0,0,'L');
                $this->Ln();
				if($comId!='')
				{
                
				$query2 = selectQuery($dbname,'organisasi','namaorganisasi',
				"kodeorganisasi='".$comId."'");
				$orgData2 = fetchData($query2);
                $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$orgData2[0]['namaorganisasi'],'',0,'L');
				}
              	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['tanggal'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
               	$this->Cell(15/100*$width,$height,date('d-m-Y H:i:s'),'',1,'L');		
                if($period!='')
				{
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                $this->Cell(5,$height,':','',0,'L');
                $this->Cell(45/100*$width,$height,$period,'',0,'L');
				}
				
                $this->Ln();
                $this->SetFont('Arial','U',12);
                $this->Cell($width,$height,$title,0,1,'C');	
                $this->Ln();	
                 $this->SetFont('Arial','',8);
                $this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
				$this->Cell(13/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);
				$this->Cell(25/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['jumlah'],1,0,'C',1);
				$this->Cell(18/100*$width,$height,$_SESSION['lang']['keterangan'],1,1,'C',1);
				
			   // $this->Cell(10/100*$width,$height,'No',1,0,'C',1);
                /*foreach($colArr as $key=>$head) {
                    $this->Cell($length[$key]/100*$width,$height,$_SESSION['lang'][$head],1,0,'C',1);
                }*/
          
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
			if(($comId=='')&&($kdVhc=='')&&($period==''))
	{
		$where=" order by a.tanggal asc";
	}
	elseif(($comId!='')&&($kdVhc=='')&&($period==''))
	{
		$where=" where a.kodeorg='".$comId."' order by a.tanggal asc";
	}
	elseif(($comId!='')&&($kdVhc!='')&&($period==''))
	{
		$where=" where a.kodeorg='".$comId."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc!='')&&($period==''))
	{
		$where=" where a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
	}
	elseif(($comId!='')&&($kdVhc!='')&&($period!=''))
	{
		$where=" where  a.kodeorg='".$comId."' and a.kodevhc='".$kdVhc."' and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc=='')&&($period!=''))
	{
		$where=" where  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId!='')&&($kdVhc=='')&&($period!=''))
	{
		$where=" where  a.kodeorg='".$comId."'  and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	elseif(($comId=='')&&($kdVhc!='')&&($period!=''))
	{
		$where=" where a.kodevhc='".$kdVhc."' and  a.tanggal like '%".$period."%'  order by a.tanggal asc";	
	}
	$sql="select a.tanggal,a.kodevhc,b.* from ".$dbname.".vhc_penggantianht a left join ".$dbname.".vhc_penggantiandt b on a.notransaksi=b.notransaksi ".$where."";
	
	$qRvhc=mysql_query($sql) or die(mysql_error());
	$row=mysql_num_rows($qRvhc);
	if($row>1)
	{
		$no=0;
		while($rRvhc=mysql_fetch_assoc($qRvhc))
		{
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rRvhc['kodebarang']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);	
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(15/100*$width,$height,$rRvhc['notransaksi'],1,0,'L',1);
			$pdf->Cell(10/100*$width,$height,tanggalnormal($rRvhc['tanggal']),1,0,'C',1);
			$pdf->Cell(13/100*$width,$height,$rRvhc['kodevhc'],1,0,'C',1);
			$pdf->Cell(25/100*$width,$height,$rbrg['namabarang'],1,0,'L',1);
			$pdf->Cell(6/100*$width,$height,$rRvhc['satuan'],1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,$rRvhc['jumlah'],1,0,'R',1);
			$pdf->Cell(18/100*$width,$height,$rRvhc['keterangan'],1,1,'L',1);
			
		}
	}
	else
	{
		$pdf->Cell(83/100*$width,$height,'Not Found',1,1,'C',1);
	}
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>