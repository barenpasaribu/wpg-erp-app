<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('lib/fpdf.php');  
$_POST['method']==''?$method=$_GET['method']:$method=$_POST['method'];
$_POST['ids']==''?$ids=$_GET['ids']:$ids=$_POST['ids'];
$sJabat="select * from ".$dbname.".sdm_5matriktraining ";
$qJabat=mysql_query($sJabat) or die(mysql_error());
while($rJabat=mysql_fetch_assoc($qJabat))
{
$kamusKategori[$rJabat['matrixid']]=$rJabat['kategori'];
$kamusTopik[$rJabat['matrixid']]=$rJabat['topik'];
}

//filter kondisi sesuai pusat/site ==Jo 31-03-2017==
if($_SESSION['empl']['pusat']==1){
	$optkodeorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$loktug="";
}
else {
	$loktug="and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}

$stats[0]=$_SESSION['lang']['wait_approval'];
$stats[1]=$_SESSION['lang']['disetujui'];
$stats[2]=$_SESSION['lang']['ditolak'];
//Untuk persetujuan
$sPrs="select value,bahasalegend from ".$dbname.".sdm_5persetujuan";
$qPrs=mysql_query($sPrs) or die(mysql_error());
while($rPrs=mysql_fetch_assoc($qPrs))
{
    $kamusPrs[$rPrs['value']]=$rPrs['bahasalegend'];
}

//ambil karyawan permanen yang belum keluar
$str="select namakaryawan,karyawanid from ".$dbname.".datakaryawan
      where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') order by namakaryawan";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $nam[$bar->karyawanid]=$bar->namakaryawan;
}  	
$statss[0]=$_SESSION['lang']['notproses'];
$statss[1]=$_SESSION['lang']['alertsdhproses'];
$limit=20;
$page=0;
if ($method==''){
	//========================
	//ambil jumlah baris dalam tahun ini
	  /*if(isset($_POST['pilihkaryawan']))
	  {
		$pilihkaryawan=$_POST['pilihkaryawan'];
	  }*/
	//Rubah Kueri jadi sesuai inputan namakaryawan ==Jo 06-12-2016==

	 if(isset($_POST['namakry']))
	  {
		$namakry=$_POST['namakry'];
	  }
	$str="select count(distinct a.id) as jlhbrs from ".$dbname.".sdm_matriktraininght a 
	left join ".$dbname.".sdm_matriktrainingdt b on a.id = b.headerid
	where b.karyawanid in (select karyawanid from ".$dbname.".datakaryawan
	where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') and namakaryawan like '%".$namakry."%' ".$loktug.") order by jlhbrs desc";
	$res=mysql_query($str);
	while($bar=mysql_fetch_object($res))
	{
		$jlhbrs=$bar->jlhbrs;
	}		
	//==================
			 
	  if(isset($_POST['page']))
		 {
			$page=$_POST['page'];
			if($page<0)
			  $page=0;
		 }
		 
	  
	  $offset=$page*$limit;
	  

	  $str="select distinct a.* from ".$dbname.".sdm_matriktraininght a  
	  left join ".$dbname.".sdm_matriktrainingdt b on a.id = b.headerid
	  where b.karyawanid in (select karyawanid from ".$dbname.".datakaryawan
	where (tanggalkeluar = '0000-00-00' or tanggalkeluar > '".date("Y-m-d")."') and namakaryawan like '%".$namakry."%' ".$loktug.") 
	  order by a.tanggaltraining desc  limit ".$offset.",20";
	  //echo "warning: ".$str;
	  $res=mysql_query($str);
	  $no=$page*$limit;
	  while($bar=mysql_fetch_object($res))
	  {

		$no+=1;
		echo"<tr class=rowcontent>
		  <td>".$no."</td>
		  <td>".$kamusTopik[$bar->matrikxid]."</td>
		  <td >".tanggalnormal_hrd($bar->tanggaltraining)."</td>	  
		  <td >".tanggalnormal_hrd($bar->sampaitanggal)."</td>
		  <td >".$nam[$bar->persetujuan1]."</td>
		  <td >".$statss[$bar->prs1]."</td>
		  <td >".$nam[$bar->persetujuanhrd]."</td>
		  <td >".$statss[$bar->prshrd]."</td>	 
		  <td >
				<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"previewPdf('".$bar->id."',event);\">";
		/*if((($bar->persetujuan1==$saya)and($bar->stpersetujuan1==0))or(($bar->persetujuanhrd==$saya)and($bar->sthrd==0)))
				 echo"<button class=mybutton onclick=tolak('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['tolak']."</button>
				 <button class=mybutton onclick=setuju('".$bar->kode."','".$bar->karyawanid."','".$sayaadalah."',event)>".$_SESSION['lang']['setuju']."</button>";*/
		  echo"</td>
		  </tr>";
	  }
	  echo"<tr><td colspan=11 align=center>
		   ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
		   <br>
		   <button class=mybutton onclick=cariPJD(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		   <button class=mybutton onclick=cariPJD(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		   </td>
		   </tr>";
}
else {
	switch($method){
		case'prevPdf':
		
			//=================================================
			class PDF extends FPDF {
		//        function Header() {
		//            global $jabatan;
		//            global $kriteria;
		//            $this->SetFont('Arial','B',11);
		//            $this->Cell(190,6,strtoupper($_SESSION['lang']['kriteria'].' '.$_SESSION['lang']['psikologi']),0,1,'C');
		//            $this->Ln();
		//            $this->SetFont('Arial','',10);
		//            $this->Cell(60,6,$_SESSION['lang']['jabatan'],1,0,'C');
		//            $this->Cell(30,6,$_SESSION['lang']['kriteria'],1,0,'C');	
		//            $this->Cell(100,6,$_SESSION['lang']['deskripsi'],1,0,'C');	
		//            $this->Ln();						
		//        }

				
			}
			//================================
			$pdf=new PDF('P','mm','A4');
			
			//funtion supaya multicell panjang kolomnya seragam ==Jo 05-07-2017==
			function SetWidths($w,$pdf)
			{
				//Set the array of column widths
				$pdf->widths=$w;
			}
			
			function NbLines($w,$txt,$pdf)
			{
				//Computes the number of lines a MultiCell of width w will take
				$cw=&$pdf->CurrentFont['cw'];
				if($w==0)
					$w=$pdf->w-$pdf->rMargin-$pdf->x;
				$wmax=($w-2*$pdf->cMargin)*1000/$pdf->FontSize;
				$s=str_replace("\r",'',$txt);
				$nb=strlen($s);
				if($nb>0 and $s[$nb-1]=="\n")
					$nb--;
				$sep=-1;
				$i=0;
				$j=0;
				$l=0;
				$nl=1;
				while($i<$nb)
				{
					$c=$s[$i];
					if($c=="\n")
					{
						$i++;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
						continue;
					}
					if($c==' ')
						$sep=$i;
					$l+=$cw[$c];
					if($l>$wmax)
					{
						if($sep==-1)
						{
							if($i==$j)
								$i++;
						}
						else
							$i=$sep+1;
						$sep=-1;
						$j=$i;
						$l=0;
						$nl++;
					}
					else
						$i++;
				}
				return $nl;
			}
			function Row($data,$pdf)
			{
				//Calculate the height of the row
				$nb=0;
				for($i=0;$i<count($data);$i++)
					$nb=max($nb,NbLines($pdf->widths[$i],$data[$i][0],$pdf));
				$h=5*$nb;
				
				//Draw the cells of the row
				for($i=0;$i<count($data);$i++)
				{
					$w=$pdf->widths[$i];
					$a=isset($data[$i][1]) ? $data[$i][1] : 'L';
					//Save the current position
					$x=$pdf->GetX();
					$y=$pdf->GetY();
					//Draw the border
					$pdf->Rect($x,$y,$w,$h);
					//Print the text
					$pdf->MultiCell($w,5,$data[$i][0],0,$a);
					//Put the position to the right of the cell
					$pdf->SetXY($x+$w,$y);
				}
				//Go to the next line
				$pdf->Ln($h);
			}
			
			$pdf->AddPage();
			
			$pdf->SetFont('Arial','',10);
			
			$pdf->Cell(185,6,$_SESSION['lang']['listtraining'],0,1,'C');
			$pdf->Ln();
			$pdf->SetFont('Arial','',8);
			/*$str="select * from ".$dbname.".datakaryawan 
				where karyawanid = '".$karyawanid."'
				";
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
				$pdf->Cell(50,6,$_SESSION['lang']['namakaryawan'],0,0,'L');                 $pdf->Cell(100,6,': '.$bar->namakaryawan,0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['jabatan'],0,0,'L');                      $pdf->Cell(100,6,': '.$kamusJabat[$bar->kodejabatan],0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['lokasitugas'],0,0,'L');                  $pdf->Cell(100,6,': '.$bar->lokasitugas,0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['tmk'],0,0,'L');                          $pdf->Cell(100,6,': '.puter_tanggal($bar->tanggalmasuk),0,1,'L');
				$pdf->Ln();
				
				$jabatanku=$bar->kodejabatan;
			}*/
			//Ganti jadi Header ==Jo 04-12-2016==
			$str="select * from ".$dbname.".sdm_matriktraininght where id='".$ids."'";
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
				$pdf->Cell(50,6,$_SESSION['lang']['topik'],0,0,'L');                 $pdf->Cell(100,6,': '.$kamusTopik[$bar->matrikxid],0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['tanggalmulai'],0,0,'L');                      $pdf->Cell(100,6,': '.$bar->tanggaltraining,0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['tanggalsampai'],0,0,'L');                  $pdf->Cell(100,6,': '.$bar->sampaitanggal,0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['atasan'],0,0,'L');                          $pdf->Cell(100,6,': '.$nam[$bar->persetujuan1],0,1,'L');
				$pdf->Cell(50,6,$_SESSION['lang']['hrd'],0,0,'L');                          $pdf->Cell(100,6,': '.$nam[$bar->persetujuanhrd],0,1,'L');
				$pdf->Ln();
				
			}
			
			$pdf->Ln();
			$pdf->Cell(185,6,$_SESSION['lang']['listemployee'],0,1,'L');
			$pdf->Cell(40,6,$_SESSION['lang']['namakaryawan'],1,0,'C');
			$pdf->Cell(30,6,$_SESSION['lang']['remark'],1,0,'C');
			$pdf->Cell(30,6,$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan'],1,0,'C');
			$pdf->Cell(30,6,$_SESSION['lang']['remark']." ".$_SESSION['lang']['atasan'],1,0,'C');
			$pdf->Cell(30,6,$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd'],1,0,'C');
			$pdf->Cell(30,6,$_SESSION['lang']['remark']." ".$_SESSION['lang']['hrd'],1,0,'C');
			$pdf->Ln();
			//Cari panjang karakter maksimum
					
					$str="select * from ".$dbname.".sdm_matriktrainingdt
						where headerid = '".$ids."'";
					$res=mysql_query($str);
					while($bar=mysql_fetch_object($res))
					{
						/*$pdf->Cell(40,$ht,$nam[$bar->karyawanid],1,0,'L');
						$start_x = $pdf->GetX();		
						$start_y = $pdf->GetY();
						if(strlen($bar->catatan)<=20){
							$hts=$ht;
						}
						else{
							$hts=$pjaw;
						}
						$pdf->MultiCell(30,$hts,$bar->catatan,1,'L');
						$pdf->setXY($start_x+30,$start_y);
						$pdf->Cell(30,$ht,$_SESSION['lang'][$kamusPrs[$bar->stpersetujuan1]],1,0,'L');
						$start_x = $pdf->GetX();		
						$start_y = $pdf->GetY();
						if(strlen($bar->catatan1)<=20){
							$hts=$ht;
						}
						else{
							$hts=$pjaw;
						}
						$pdf->MultiCell(30,$hts,$bar->catatan1,1,'L');
						$pdf->setXY($start_x+30,$start_y);
						$pdf->Cell(30,$ht,$_SESSION['lang'][$kamusPrs[$bar->sthrd]],1,0,'L');
						$start_x = $pdf->GetX();		
						$start_y = $pdf->GetY();
						if(strlen($bar->catatanhrd)<=20){
							$hts=$ht;
						}
						else{
							$hts=$pjaw;
						}
						$pdf->MultiCell(30,$hts,$bar->catatanhrd,1,'L');
						$pdf->setXY($start_x,$start_y);
						$pdf->Ln();*/
						
						SetWidths(array(40,30,30,30,30,30),$pdf);
						Row(array(
									array($nam[$bar->karyawanid]),
									array($bar->catatan),
									array($_SESSION['lang'][$kamusPrs[$bar->stpersetujuan1]]),
									array($bar->catatan1),
									array($_SESSION['lang'][$kamusPrs[$bar->sthrd]]),
									array($bar->catatanhrd)
						),$pdf);
						
					}
			
			
		 
			$pdf->Output();	
		break;
		default:
        break;
	}
}

	   

?>