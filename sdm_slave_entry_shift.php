<?php
require_once('master_validation.php');
include('config/connection.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses = isset($_GET['proses']) ? $_GET['proses'] : null;

//tambah filter lokasi tugas ==Jo 08-05-2017==
if($_SESSION['empl']['pusat']==1){
	$whrorg="";
}
else{
	$whrorg="where kd_organisasi ='".$_SESSION['empl']['lokasitugas']."'";
}
if($proses == 'excel') {
	## GET SHIFT ##
	$sData = "SELECT * FROM ".$dbname.".sdm_shift ".$whrorg." order by nama asc;";
	$qData = mysql_query($sData) or die(mysql_error());
	
	## SET EXCEL OUTPUT ##
	## Title ##
	$stream ="<table><tr><td colspan=10 align=center><b>".$_SESSION['lang']['mastershift']."</b></td><tr></table>";
	## Details ##
	$stream.="
		<table border=1>
		<tr>
			<td bgcolor=#DEDEDE align=center >No</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['namashift']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['jammasuk']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['jamkeluar']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['aktifshift']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['kd_organisasi']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['kd_unit']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['tgl_start']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['tgl_akhir']."</td>
			<td bgcolor=#DEDEDE align=center >".$_SESSION['lang']['keterangan']."</td>
		</tr>
	";
		
		while($res=mysql_fetch_assoc($qData)){
			$no+=1;
			$Aktif = ($res['aktif'] == 'Y') ? "Ya" : "Tidak";
			$stream.="
				<tr>
					<td>".$no."</td>
					<td>".$res['nama']."</td>
					<td>".$res['jam_masuk']."</td>
					<td>".$res['jam_keluar']."</td>
					<td>".$Aktif."</td>
					<td>".$res['kd_organisasi']."</td>
					<td>".$res['kd_unit']."</td>
					<td>".$res['tgl_start']."</td>
					<td>".$res['tgl_end']."</td>
					<td>".$res['kode']."</td>
				</tr>";
		}
	$stream.="</table>";
	$nop_="Laporan Master Shift_";
} 
else if($proses == 'pdf'){
	## GET SHIFT ##
	$sData = "SELECT * FROM ".$dbname.".sdm_shift ".$whrorg." order by nama asc;";
	$qData = mysql_query($sData) or die(mysql_error());
}
switch($proses)
{
    case'preview':
		echo $tab;
		break;
	case'excel':
		if(strlen($stream)>0) {
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {@unlink('tempExcel/'.$file);}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$stream)){
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			} else {
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}
		break;
	case'pdf':
		class PDF extends FPDF {
			function Header() {
		       	## Title ##
		        $this->SetFont('Arial','B',12);
				$this->Cell(190,5,strtoupper($_SESSION['lang']['mastershift']),0,1,'C');
		        
				## Header
		        $this->SetFont('Arial','',8);		
				$this->Cell(5,4,'No.',1,0,'C');			
				$this->Cell(30,4,$_SESSION['lang']['namashift'],1,0,'C');		
				$this->Cell(20,4,$_SESSION['lang']['jammasuk'],1,0,'L');	
				$this->Cell(20,4,$_SESSION['lang']['jamkeluar'],1,0,'C');		
				$this->Cell(12,4,$_SESSION['lang']['aktif'],1,0,'C');		
				$this->Cell(20,4,'Kd Organisasi',1,0,'C');
				$this->Cell(15,4,'Kd Unit',1,0,'C');
				$this->Cell(20,4,'Tgl Mulai',1,0,'C');
				$this->Cell(20,4,'Tgl Akhir',1,0,'C');		
				$this->Cell(20,4,$_SESSION['lang']['keterangan'],1,1,'C');				
		    }
		}
		$pdf=new PDF('P','mm','A4');
		$pdf->AddPage();
			while($res=mysql_fetch_assoc($qData)){
				$Aktif = ($res['aktif'] == 'Y') ? "Ya" : "Tidak";
				$no+=1;
	            $pdf->Cell(5,4,$no,1,0,'C');
	            $pdf->Cell(30,4,$res['nama'],1,0,'C');
	            $pdf->Cell(20,4,$res['jam_masuk'],1,0,'C');
	            $pdf->Cell(20,4,$res['jam_keluar'],1,0,'C');
	            $pdf->Cell(12,4,$Aktif,1,0,'C');
				$pdf->Cell(20,4,$res['kd_organisasi'],1,0,'C');
				$pdf->Cell(15,4,$res['kd_unit'],1,0,'C');
				$pdf->Cell(20,4,$res['tgl_start'],1,0,'C');
	            $pdf->Cell(20,4,$res['tgl_end'],1,0,'C');
				$pdf->Cell(20,4,$res['kode'],1,1,'C');	
			}
		$pdf->Output();	
   		break;
    default:
    break;
}
?>