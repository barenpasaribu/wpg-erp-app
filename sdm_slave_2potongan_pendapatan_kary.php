<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');


$proses=$_GET['proses'];
//$periode=$_POST['periode'];
//$period=$_POST['period'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];

$kdOrg=$_POST['kdeOrg'];
if($kdOrg=='')$kdOrg=$_GET['kdeOrg'];
$tgl1=tanggalsystem_hrd($_POST['tgl1']);
if($tgl1=='') $tgl1=tanggalsystem_hrd($_GET['tgl1']);
$tgl2=tanggalsystem_hrd($_POST['tgl2']);
if($tgl2=='')$tgl2=tanggalsystem_hrd($_GET['tgl2']);
$tgl_1=tanggalsystem_hrd($_POST['tgl_1']);
if($tgl_1=='')$tgl_1=tanggalsystem_hrd($_GET['tgl_1']);
$tgl_2=tanggalsystem_hrd($_POST['tgl_2']);
if($tgl_2=='')$tgl_2=tanggalsystem_hrd($_GET['tgl_2']);
$periodeGaji=$_POST['period'];
if($periodeGaji=='')$periodeGaji=$_GET['period'];
$periode=explode('-',$_POST['periode']);

$idKry=$_POST['idKry'];
if($idKry=="")$idKry=$_GET['idKry'];

	if(($idKry=="")&&($idKry==""))
	{
		echo"warning: Mohon pilih karyawan";
		exit();
	}
/* 	
$ssql="SELECT b.tipe FROM ".$dbname.".organisasi a 
	   INNER JOIN ".$dbname.".organisasitipe b ON a.tipe=b.tipe
	   WHERE b.isHolding=1 LIMIT 1"; */	
$ssql="SELECT * FROM ".$dbname.".organisasi 
	   WHERE kodeorganisasi ='".$lksiTgs."'";
	   
$qOrg=mysql_query($ssql) or die(mysql_error($conn));
while($rOrg=mysql_fetch_object($qOrg))
{
	$HO=$rOrg->tipe;
}
//$ssql="SELECT b.tipe FROM ".$dbname.".organisasi a INNER JOIN ".$dbname.".organisasitipe b ON a.tipe=b.tipe WHERE b.isKanwil=1 LIMIT 1";
$ssql="SELECT a.tipe FROM ".$dbname.".organisasi a WHERE a.tipe='KANWIL'";
//echo $ssql;
$qOrg=mysql_query($ssql) or die(mysql_error($conn));
while($rOrg=mysql_fetch_object($qOrg))
{
	$kwl=$rOrg->tipe;
}
function dates_inbetween($date1, $date2){

    $day = 60*60*24;

    $date1 = strtotime($date1);
    $date2 = strtotime($date2);

    $days_diff = round(($date2 - $date1)/$day); // Unix time difference devided by 1 day to get total days in between

    $dates_array = array();

    $dates_array[] = date('Y-m-d',$date1);
   
    for($x = 1; $x < $days_diff; $x++){
        $dates_array[] = date('Y-m-d',($date1+($day*$x)));
    }

    $dates_array[] = date('Y-m-d',$date2);

    return $dates_array;
}
switch($proses)
{
	case'preview':
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		$tgl1=$tgl_1;
		$tgl2=$tgl_2;
	}
	
	$test = dates_inbetween($tgl1, $tgl2);
	if(($tgl2=="")&&($tgl1==""))
	{
		echo"warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong";
		exit();
	}

	$jmlHari=count($test);
	//cek max hari inputan
	if($jmlHari>31)
	{
		echo"warning:Range tanggal tidak valid";
		exit();
	}
	if($kdOrg!='')
	{
		$kodeOrg=$kdOrg;
			/*if(strlen($kdOrg)>4)
			{
				$where=" and subbagian='".$kdOrg."'";
			}
			else
			{
				$where=" and lokasitugas='".$kdOrg."' and (IFNULL(subbagian,'0')='0' OR IFNULL(subbagian,' ')=' ')";
			}*/
			//hilangkan subbagian==Jo 09-03-2017==
			$where=" and lokasitugas='".$kdOrg."'";
			$where2=" and kodeorg='".substr($kodeOrg,0,4)."'";
		
	}
	elseif($kdOrg=='')
	{
		echo"warning:Unit Tidak Boleh Kosong";
                exit();
	}
	$resData=array();
	$sGetKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where karyawanid=".$idKry." ".$where."   order by namakaryawan asc";
	//echo $sGetKary;
	//exit("Error".$sGetKary);
	$rGetkary=fetchData($sGetKary);
	//$resData[]=$rGetkary;
	foreach($rGetkary as $row => $kar)
    {
	  $resData[$kar['karyawanid']][]=$kar['karyawanid'];		
      $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    }  
	
	$tab.="<table cellspacing='1' border='0' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td>No</td>
	<td>".$_SESSION['lang']['nama']."</td>
	<td>".$_SESSION['lang']['keterangan']."</td>
	<td>".$_SESSION['lang']['jumlah']."</td></tr><tbody>";
	
	$i=0;	
	foreach($resData as $hslBrs => $hslAkhir)
	{
		
		/*echo"<pre>";
		print_r($hslAkhir);
		echo"</pre>";*/
		
		$sData="select  jumlahpotongan,nik,periodegaji as tanggal,keterangan from ".$dbname.".sdm_potongandt 
                    where periodegaji='".$periodeGaji."' ".$where2." and nik='".$hslAkhir[0]."' order by nik asc";	
		//exit("Error".$sData);
		//echo $sData;
		$qData=mysql_query($sData) or die(mysql_error());
		$row=mysql_num_rows($qData);
		$tmpRow=$row-1;
		$afdC=false;
		$blankC=false;
		$afdB=true;
        $baris=$row;
        $i=0;
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
                        $i+=1;
			$tab.="<tr class='rowcontent'>";
			if($afdC==false) {
				$tab .= "<td>".$no."</td>";
				$tab .= "<td>".$namakar[$hslAkhir[0]]."</td>";
				$afdC = true;
			} else {
				if($blankC==false) {
					$no-=$tmpRow;
					$tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
					$tab .= "<td  rowspan='".$tmpRow."'>&nbsp;</td>";
					$blankC = true;
				}
			}	
			$tab.="<td>".$rData['keterangan']."</td>";
			$tab.="<td align=\"right\">".number_format($rData['jumlahpotongan'],2)."</td>";
			$tab.="</tr>";
			$subtot[$hslAkhir[0]]+=$rData['jumlahpotongan'];
			if($i==$baris)
			{
				$tab.="<tr class='rowcontent'><td rowspan=1>&nbsp; </td><td colspan=2 align=right>Total</td><td align=\"right\">".number_format($subtot[$hslAkhir[0]],2)."</td></tr>";
			
			}
		}
		//$tab.="<tr><td colspan=2>test</td></tr>";
		//$i++;
	}
	$tab.="</tbody></table>";
	echo $tab;
	break;
	case'pdf':
	$kdOrg=$_GET['kdeOrg'];

	$tgl1=tanggalsystem_hrd($_GET['tgl1']);
	$tgl2=tanggalsystem_hrd($_GET['tgl2']);
	$tgl_1=tanggalsystem_hrd($_GET['tgl_1']);
	$tgl_2=tanggalsystem_hrd($_GET['tgl_2']);

        $periodeGaji=$_GET['period'];
	$idKry=$_GET['idKry'];
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		$tgl1=$tgl_1;
		$tgl2=$tgl_2;
	}
	
	$test = dates_inbetween($tgl1, $tgl2);
	if(($tgl2=="")&&($tgl1==""))
	{
		echo"warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong";
		exit();
	}

	$jmlHari=count($test);
	//cek max hari inputan
	if($jmlHari>31)
	{
		echo"warning:Range tanggal tidak valid";
		exit();
	}
	//ambil query untuk tanggal kehadiran
	

	//+++++++++++++++++++++++++++++++++++++++++++++++++++++
//create Header

class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $period;
				global $periode;
				global $kdOrg;
				global $kdeOrg;
				global $tgl1;
				global $tgl2;
				global $where;
				global $jmlHari;
				global $test;
				global $klmpkAbsn;
				global $periodeGaji;
				
				$jmlHari=$jmlHari*1.5;
				$cols=247.5;
			    # Alamat & No Telp
                $query = selectQuery($dbname,'organisasi','alamat,telepon',
                    "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                $orgData = fetchData($query);
                
                $width = $this->w - $this->lMargin - $this->rMargin;
                $height = 20;
                $path='images/logo.jpg';
				//hide logo ==Jo 03-05-2017==
                //$this->Image($path,$this->lMargin,$this->tMargin,70);	
                $this->SetFont('Arial','B',9);
                $this->SetFillColor(255,255,255);	
                $this->SetX(100);   
                //$this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                $this->SetX(100); 		
                //$this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                $this->SetX(100); 			
                //$this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
                $this->Ln();
                
                $this->SetFont('Arial','B',10);
				//$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['lapPotongan'],'',0,'L');
				$this->Cell((20/100*$width)-5,$height,'','',0,'L');
				$this->Ln();
				$this->Ln();
				
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['lapPotongan']),'',0,'C');
				$this->Ln();
				$this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal_hrd($tgl1)." s.d. ". tanggalnormal_hrd($tgl2),'',0,'C');
				$this->Ln();
				$this->Ln();
              	$this->SetFont('Arial','B',8);
                $this->SetFillColor(220,220,220);
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
				$this->Cell(13/100*$width,$height,$_SESSION['lang']['nama'],1,0,'C',1);		
				$this->Cell(25/100*$width,$height,$_SESSION['lang']['keterangan'],1,0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','Legal');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',7);
		$subtot=array();
		if($kdOrg!='')
	{
		$kodeOrg=$kdOrg;
			if(strlen($kdOrg)>4)
			{
				$where=" and subbagian='".$kdOrg."'";
			}
			else
			{
				$where=" and lokasitugas='".$kdOrg."' and (IFNULL(subbagian,'0')='0' OR IFNULL(subbagian,' ')=' ')";
			}
			$where2=" and kodeorg='".substr($kodeOrg,0,4)."'";
		
	}
	elseif($kdOrg=='')
	{
		echo"warning:Unit Tidak Boleh Kosong";
                exit();
	}
	$resData=array();
	$sGetKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where  karyawanid=".$idKry."   ".$where."   order by namakaryawan asc";
	//exit("Error".$sGetKary);
	$rGetkary=fetchData($sGetKary);
	//$resData[]=$rGetkary;
	foreach($rGetkary as $row => $kar)
    {
	  $resData[$kar['karyawanid']][]=$kar['karyawanid'];		
      $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    }  
	
	
	$i=0;	
	foreach($resData as $hslBrs => $hslAkhir)
	{
		
		/*echo"<pre>";
		print_r($hslAkhir);
		echo"</pre>";*/
		
		$sData="select  jumlahpotongan,nik,keterangan from ".$dbname.".sdm_potongandt 
                    where periodegaji='".$periodeGaji."' ".$where2." and nik='".$hslAkhir[0]."' order by nik asc";	
		//exit("Error".$sData);
		$qData=mysql_query($sData) or die(mysql_error());
		$row=mysql_num_rows($qData);
		$tmpRow=$row-1;
		$afdC=false;
		$blankC=false;
		$afdB=true;
                $baris=$row;
                $i=0;
		while($rData=mysql_fetch_assoc($qData))
		{					
			$no+=1;
			$i+=1;
			if($afdC==false) {
				$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
				$pdf->Cell(13/100*$width,$height,$namakar[$hslAkhir[0]],1,0,'L',1);	
				$afdC = true;
			} else {
				if($blankC==false) {
					$no-=1;
					$pdf->Cell(3/100*$width,$height,'',1,0,'L',1);
					$pdf->Cell(13/100*$width,$height,'',1,0,'L',1);	
				}
                                if($i==$baris)
                                {
                                    $blankC = true;
                                }
			}	
			$pdf->Cell(25/100*$width,$height,$rData['keterangan'],1,0,'L',1);	
			$pdf->Cell(10/100*$width,$height,number_format($rData['jumlahpotongan'],2),1,1,'R',1);
			$subtot[$hslAkhir[0]]+=$rData['jumlahpotongan'];
			if($i==$baris)
			{
				$pdf->Cell(41/100*$width,$height,$_SESSION['lang']['total'],1,0,'R',1);
				$pdf->Cell(10/100*$width,$height,number_format($subtot[$hslAkhir[0]],2),1,1,'R',1);
				$afdB=true;
			}
		}
		//$tab.="<tr><td colspan=2>test</td></tr>";
		//$i++;
	}
	$pdf->Output();

	break;
	case'excel':
	$kdOrg=$_GET['kdeOrg'];

	$tgl1=tanggalsystem_hrd($_GET['tgl1']);
	$tgl2=tanggalsystem_hrd($_GET['tgl2']);
	$tgl_1=tanggalsystem_hrd($_GET['tgl_1']);
	$tgl_2=tanggalsystem_hrd($_GET['tgl_2']);
	$period=explode('-',$_GET['period']);
	$periode=explode('-',$_GET['periode']);
        $periodeGaji=$_GET['period'];
	$idKry=$_GET['idKry'];
	if(($tgl_1!='')&&($tgl_2!=''))
	{
		$tgl1=$tgl_1;
		$tgl2=$tgl_2;
	}
	
	$test = dates_inbetween($tgl1, $tgl2);
	if(($tgl2=="")&&($tgl1==""))
	{
		echo"warning: Tanggal Mulai dan Tanggal Sampai tidak boleh kosong";
		exit();
	}

	$jmlHari=count($test);
	//cek max hari inputan
	if($jmlHari>31)
	{
		echo"warning:Range tanggal tidak valid";
		exit();
	}
	if($kdOrg!='')
	{
		$kodeOrg=$kdOrg;
			if(strlen($kdOrg)>4)
			{
				$where=" and subbagian='".$kodeOrg."'";
			}
			else
			{
				$where=" and lokasitugas='".$kodeOrg."' and (IFNULL(subbagian,'0')='0' OR IFNULL(subbagian,' ')=' ')";
			}
			$where2=" and kodeorg='".substr($kodeOrg,0,4)."'";
		
	}
	elseif($kdOrg=='')
	{
		echo"warning:Unit Tidak Boleh Kosong";
                exit();
	}
	$resData=array();
	$sGetKary="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where  karyawanid=".$idKry." ".$where."   order by namakaryawan asc";
	//exit("Error".$sGetKary);
	$rGetkary=fetchData($sGetKary);
	//$resData[]=$rGetkary;
	foreach($rGetkary as $row => $kar)
    {
	  $resData[$kar['karyawanid']][]=$kar['karyawanid'];		
      $namakar[$kar['karyawanid']]=$kar['namakaryawan'];
    }  
	$tab.="<table border='0'><tr><td colspan='4' align=center>".strtoupper($_SESSION['lang']['lapPotongan'])."</td></tr>
	<tr><td colspan='4' align=center>".strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal_hrd($tgl1)." s.d. ". tanggalnormal_hrd($tgl2)."</td></tr><tr><td colspan='".$colatas."'>&nbsp;</td></tr></table>";
	$tab.="<table cellspacing='1' border='1' class='sortable'>
	<thead class=rowheader>
	<tr>
	<td bgcolor=#DEDEDE align=center>No</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nama']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
	<td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td></tr><tbody>";
	
	foreach($resData as $hslBrs => $hslAkhir)
	{
		
		/*echo"<pre>";
		print_r($hslAkhir);
		echo"</pre>";*/
		
		$sData="select  jumlahpotongan,nik,keterangan from ".$dbname.".sdm_potongandt 
                    where periodegaji='".$periodeGaji."'  ".$where2." and nik='".$hslAkhir[0]."' order by nik asc";	
		//exit("Error".$sData);
		$qData=mysql_query($sData) or die(mysql_error());
		$row=mysql_num_rows($qData);
		$tmpRow=$row-1;
		$afdC=false;
		$blankC=false;
		$afdB=true;
                $baris=$row;
                $i=0;
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
                        $i+=1;
			$tab.="<tr class='rowcontent'>";
			if($afdC==false) {
				$tab .= "<td>".$no."</td>";
				$tab .= "<td>".$namakar[$hslAkhir[0]]."</td>";
				$afdC = true;
			} else {
				if($blankC==false) {
					$no-=$tmpRow;
					$tab .= "<td rowspan='".$tmpRow."'>&nbsp;</td>";
					$tab .= "<td  rowspan='".$tmpRow."'>&nbsp;</td>";
					$blankC = true;
					$afdB=false;
				}
			}	
			$tab.="<td>".$rData['keterangan']."</td>";
			$tab.="<td align=\"right\">".number_format($rData['jumlahpotongan'],2)."</td>";
			$tab.="</tr>";
			$subtot[$hslAkhir[0]]+=$rData['jumlahpotongan'];
			if($i==$baris)
			{
				$tab.="<tr class='rowcontent'><td rowspan=1>&nbsp; </td><td colspan=2 align=right>Total</td><td align=\"right\">".number_format($subtot[$hslAkhir[0]],2)."</td></tr>";
				$afdB=true;
			}
		}
		//$tab.="<tr><td colspan=2>test</td></tr>";
		//$i++;
	}
	$tab.="</tbody></table>";
//	echo $tab;
	
	
	
	
	
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			if($period!='')
			{
				$art=$period;
				$art=$art[1].$art[0];
			}
			if($periode!='')
			{
				$art=$periode;
				$art=$art[1].$art[0];
			}
			if($kdeOrg!='')
			{
				$kodeOrg=$kdeOrg;
			}
			if($kdOrg!='')
			{
				$kodeOrg=$kdOrg;
			}
			$nop_="RekapPotongan".$art."__".$kodeOrg;
			if(strlen($tab)>0)
			{
			if ($handle = opendir('tempExcel')) {
			while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
			@unlink('tempExcel/'.$file);
			}
			}	
			closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$tab))
			{
			echo "<script language=javascript1.2>
			parent.window.alert('Can't convert to excel format');
			</script>";
			exit;
			}
			else
			{
			echo "<script language=javascript1.2>
			window.location='tempExcel/".$nop_.".xls';
			</script>";
			}
			closedir($handle);
			}
	break;
	case'getTgl':
	if($periode!='')
	{
		$tgl=$periode;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
	elseif($period!='')
	{
		$tgl=$period;
		$tanggal=$tgl[0]."-".$tgl[1];
	}
	$sTgl="select distinct tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji where kodeorg='".$_SESSION['empl']['lokasitugas']."' and periode='".$tanggal."' ";
	//echo"warning".$sTgl;
	$qTgl=mysql_query($sTgl) or die(mysql_error());
	$rTgl=mysql_fetch_assoc($qTgl);
	echo tanggalnormal_hrd($rTgl['tanggalmulai'])."###".tanggalnormal_hrd($rTgl['tanggalsampai']);
	break;
	case'getKry':
	if(strlen($kdeOrg)>4)
	{
		$where=" subbagian='".$kdeOrg."'";
	}
	else
	{
		$where=" lokasitugas='".$kdeOrg."'";
	}
	$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
	$qKry=mysql_query($sKry) or die(mysql_error());
	while($rKry=mysql_fetch_assoc($qKry))
	{
		$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
	}
	echo $optKry;
	break;

case'getPeriode2':
         if(strlen($kdeOrg)>4)
	{
		$where=" subbagian='".$kdOrg."'";
	}
	else
	{
		$where=" lokasitugas='".$kdOrg."'";
	}
        $optKry="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sKry="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where ".$where." order by namakaryawan asc";
        //exit("Error".$sKry);
	$qKry=mysql_query($sKry) or die(mysql_error());
	while($rKry=mysql_fetch_assoc($qKry))
	{
		$optKry.="<option value=".$rKry['karyawanid'].">".$rKry['namakaryawan']."</option>";
	}
        $optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sPeriode="select distinct periode from ".$dbname.".sdm_5periodegaji where kodeorg='".$kdOrg."'";
	$qPeriode=mysql_query($sPeriode) or die(mysql_error());
	while($rPeriode=mysql_fetch_assoc($qPeriode))
	{
		$optPeriode.="<option value=".$rPeriode['periode'].">".substr(tanggalnormal_hrd($rPeriode['periode']),1,7)."</option>";
	}
	echo $optPeriode."###".$optKry;
    break;
	default:
	break;
}

/*if(($tgl2!='')&&($tgl1!=''))
	{
		$where=" a.tanggal between '".$tgl1."' and '".$tgl2."'";
	}
	
	elseif($kdOrg=='')
	{
		if($_SESSION['org']['tipelokasitugas']!='HOLDING')
		{
			$kodeOrg=$_SESSION['empl']['lokasitugas'];
		
		}
		else
		{
			$sKebun="select kodeorganisasi from organisasi where tipe in ('KEBUN','PABRIK','KANWIL') ";
			$qKebun=mysql_query($sKebun) or die(mysql_error());
			while($rKebun=mysql_fetch_assoc($qKebun))
			{
				$kodeOrg="'".$rKebun['kodeorganisasi']."'";
				$kodeOrg.=",'".$rKebun['kodeorganisasi']."'";
			}
			$where.=" and a.kodeorg in(".$kodeOrg.")";		
			$where2.=" and substr(unit,1,4) in (".$kodeOrg.")";	
			$abs.=" and kodeorg like '%".$kodeOrg."%'";	
		}
		
	}
	if($kdeOrg!="")
	{
		$where.=" and a.kodeorg='".$kdeOrg."'";
	}
	
	if($idKry!='')
	{
		$where.=" and a.karyawanid='".$idKry."'";
	}
*/?>