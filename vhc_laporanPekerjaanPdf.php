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
$cols = 'no,tanggal,pagi,sore,note';
$colArr = explode(',',$cols);

//$query = selectQuery($dbname,'kebun_curahhujan','kodeorg, tanggal, pagi, sore, catatan',$where);
//$data = fetchData($query);

$title = $_SESSION['lang']['laporanPekerjaan'];
$align = explode(",","L,L,R,R,L");
$length = explode(",","10,15,20,20,35");

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
                 $this->SetFont('Arial','',7);
                $this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
			
				/*$this->MultiCell(25/100*$width,$height-10,$_SESSION['lang']['header'],1,'C',1);
				$this->SetY($this->GetY());
				$this->SetX($this->GetX()+48);*/
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['notransaksi'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['vhc_kmhm_awal'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['vhc_kmhm_akhir'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['vhc_jenis_bbm'],1,0,'C',1);
				$this->Cell(5/100*$width,$height,$_SESSION['lang']['vhc_jumlah_bbm'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['vhc_jenis_pekerjaan'],1,0,'C',1);
				$this->Cell(5/100*$width,$height,$_SESSION['lang']['alokasibiaya'],1,0,'C',1);
				$this->Cell(5/100*$width,$height,$_SESSION['lang']['vhc_berat_muatan'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['jumlahrit'],1,0,'C',1);
				$this->Cell(4/100*$width,$height,$_SESSION['lang']['biaya'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['namakaryawan'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['vhc_posisi'],1,0,'C',1);
				$this->Cell(6/100*$width,$height,$_SESSION['lang']['upahkerja'],1,1,'C',1);
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
        $pdf=new PDF('L','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		if(($comId=='')&&($jnsVhc=='')&&($kdVhc=='')&&($period==''))
		{
			$where=" order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc=='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' order by a.tanggal asc";	
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.`jenisvhc`='".$jnsVhc."' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc=='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc!='')&&($period==''))
		{
			$where=" where a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";	
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' order by a.tanggal asc";	
		}
		elseif(($comId!='')&&($jnsVhc=='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' order by a.tanggal asc";
		}
		elseif(($comId=='')&&($jnsVhc!='')&&($kdVhc!='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.jenisvhc='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc=='')&&($period!=''))
		{
			$where=" where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' and a.jenisvhc='".$jnsVhc."' order by a.tanggal asc";
		}
		elseif(($comId!='')&&($jnsVhc!='')&&($kdVhc!='')&&($period!=''))
		{
			$where="where a.tanggal like '%".$period."%' and a.kodeorg='".$comId."' and a.`jenisvhc`='".$jnsVhc."' and a.kodevhc='".$kdVhc."' order by a.tanggal asc";
		}
			
		$sql="select a.*,b.*,c.idkaryawan,c.upah,c.posisi from ".$dbname.".vhc_runht a left join ".$dbname.".vhc_rundt b on a.notransaksi=b.notransaksi left join ".$dbname.".vhc_runhk c on b.notransaksi=c.notransaksi ".$where."";
		$arrPos=array("Sopir","Kondektur");
		$qRvhc=mysql_query($sql) or die(mysql_error());
		$no=0;
		while($res=mysql_fetch_assoc($qRvhc))
		{
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);
			
			$skry="select `namakaryawan` from ".$dbname.".datakaryawan where karyawanid='".$res['idkaryawan']."'";
			$qkry=mysql_query($skry) or die(mysql_error());
			$rkry=mysql_fetch_assoc($qkry);
			
			$sJns="select namakegiatan  from ".$dbname.".vhc_kegiatan where kodekegiatan='".$res['jenispekerjaan']."'";
			$qJns=mysql_query($sJns) or die(mysql_error());
			$rJns=mysql_fetch_assoc($qJns);
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
			$pdf->Cell(10/100*$width,$height,$res['notransaksi'],1,0,'L',1);
			$pdf->Cell(6/100*$width,$height,tanggalnormal($res['tanggal']),1,0,'l',1);
			$pdf->Cell(8/100*$width,$height,$res['kodevhc'],1,0,'L',1);
			$pdf->Cell(4/100*$width,$height,$res['satuan'],1,0,'L',1);
			$pdf->Cell(6/100*$width,$height,$res['kmhmawal'],1,0,'R',1);
			$pdf->Cell(6/100*$width,$height,$res['kmhmakhir'],1,0,'R',1);
			$pdf->Cell(8/100*$width,$height,$rbrg['namabarang'],1,0,'L',1);
			$pdf->Cell(5/100*$width,$height,$res['jlhbbm'],1,0,'R',1);
			$pdf->Cell(6/100*$width,$height,$res['jenispekerjaan'],1,0,'L',1);
			$pdf->Cell(5/100*$width,$height,$res['alokasibiaya'],1,0,'L',1);
			$pdf->Cell(5/100*$width,$height,$res['beratmuatan'],1,0,'R',1);
			$pdf->Cell(6/100*$width,$height,$res['jumlahrit'],1,0,'R',1);
			$pdf->Cell(4/100*$width,$height,$res['biaya'],1,0,'R',1);
			$pdf->Cell(6/100*$width,$height,$rkry['namakaryawan'],1,0,'L',1);
			$pdf->Cell(6/100*$width,$height,$arrPos[$res['posisi']],1,0,'L',1);
			$pdf->Cell(6/100*$width,$height,number_format($res['upah'],2),1,1,'R',1);
		}
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>