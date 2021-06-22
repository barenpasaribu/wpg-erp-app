<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];

$optNmSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if($_SESSION['language']=='EN'){
    $dd='namaakun1';
}else{
    $dd='namaakun';
}
$optNmAkun=makeOption($dbname, 'keu_5akun', 'noakun,'.$dd);
if($kdUnit=='')
{
    exit("Error: Organizer code required");
}
if($kdUnit!='')
{
    $where="  and kodetraksi='".$kdUnit."'";
}
if($periode!='')
{
    $where.=" and periode='".$periode."'";
}
$sPeriode="select distinct tanggalmulai,tanggalsampai from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and kodeorg='".substr($kdUnit,0,4)."'";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
$rPeriode=mysql_fetch_assoc($qPeriode);

$sData="select distinct noakundebet,sampaidebet  from ".$dbname.".keu_5parameterjurnal where  jurnalid='LPVHC'";
//exit("error".$sData);
$qData=mysql_query($sData) or die(mysql_error());
$rList=mysql_fetch_assoc($qData);
//$sData2="select kodevhc,sum(jumlah) as jumlah,namaakun,noakun  from ".$dbname.".vhc_by_kendaraan where noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."' ".$where." group by noakun,kodevhc order by kodevhc asc ";
$sData2="select sum(debet) as jumlah, kodevhc,noakun from ".$dbname.".keu_jurnaldt_vw where
          kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".substr($kdUnit,0,4)."%')
          and tanggal>='".$rPeriode['tanggalmulai']."' and tanggal<='".$rPeriode['tanggalsampai']."' and nojurnal like '%".substr($kdUnit,0,4)."%'
          and (noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."')
                  and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)
                  group by kodevhc,noakun  order by kodevhc asc ";
// exit("error".$sData);
//echo $sData2;
$qData2=mysql_query($sData2) or die(mysql_error());
while($rData2=mysql_fetch_assoc($qData2))
{
    $totJumlah[$rData2['kodevhc']]+=$rData2['jumlah'];
    $listNamaakun[$rData2['kodevhc']][$rData2['noakun']]=$rData2['jumlah'];
}

//list data kode vhc
//$sData3="select distinct kodevhc  from ".$dbname.".vhc_by_kendaraan where noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."' ".$where." group by noakun,kodevhc order by kodevhc asc ";
$sData3="select distinct  kodevhc from ".$dbname.".keu_jurnaldt_vw where
          kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".substr($kdUnit,0,4)."%')
          and tanggal>='".$rPeriode['tanggalmulai']."' and tanggal<='".$rPeriode['tanggalsampai']."' and nojurnal like '%".substr($kdUnit,0,4)."%'
          and (noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."')
                  and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)
                  group by kodevhc  order by kodevhc asc";
//echo $sData3;
$qData3=mysql_query($sData3) or die(mysql_error());
while($rData3=mysql_fetch_assoc($qData3))
{
    $dtVhc[]=$rData3['kodevhc'];
}
//list data no akun
//$sData5="select distinct noakun,namaakun  from ".$dbname.".vhc_by_kendaraan where noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."' ".$where." group by noakun,kodevhc order by kodevhc asc ";
$sData5="select distinct  noakun from ".$dbname.".keu_jurnaldt_vw where
          kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodetraksi like '%".substr($kdUnit,0,4)."%')
          and tanggal>='".$rPeriode['tanggalmulai']."' and tanggal<='".$rPeriode['tanggalsampai']."' and nojurnal like '%".substr($kdUnit,0,4)."%'
          and (noakun between '".$rList['noakundebet']."' and '".$rList['sampaidebet']."')
                  and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)
                  group by noakun,kodevhc  order by kodevhc asc";
$qData5=mysql_query($sData5) or die(mysql_error());
while($rData5=mysql_fetch_assoc($qData5))
{
    $listNoakun[]=$rData5['noakun'];
   // $namaAkun[$rData5['noakun']]=$rData5['namaakun'];
}
$dataSemua=count($dtVhc);
if($dataSemua==0)
{
    exit("Error: No data found");
}
$brd=0;
$bgBelakang='';
if($proses=='excel')
{
    $brd=1;
    $bgBelakang="bgcolor=#00FF40 align=center";
    $tab="<table>
            <tr><td colspan=5 align=center>".$_SESSION['lang']['laporanByRinciPerKend']."</td></tr>
            <tr><td colspan=5 align=left>".$_SESSION['lang']['kodetraksi']." : ".$kdUnit." [".$optNm[$kdUnit]."]</td></tr>
            <tr><td colspan=5 align=left>".$_SESSION['lang']['periode']." : ".$periode."</td></tr>
            <tr><td colspan=5></td><td></td></tr>
            </table>";
}
$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>
<thead>
<tr class=rowheader>            
<td ".$bgBelakang.">".$_SESSION['lang']['kodevhc']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['total']."</td>";
foreach($listNoakun as $dafNoakun)
{
   $tab.=" <td ".$bgBelakang.">".$optNmAkun[$dafNoakun]."</td> ";
}
  
$tab.="</tr>
</thead><tbody id=containDataStock>";
foreach($dtVhc as $listVhc)
{
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$listVhc."</td>";
        $tab.="<td align=right>".number_format($totJumlah[$listVhc],0)."</td>";
        foreach($listNoakun as $dafNoakun)
        {
           $tab.=" <td align=right>".number_format($listNamaakun[$listVhc][$dafNoakun],0)."</td> ";
        }
        $tab.="</tr>";
}
 
$tab.="</tbody></table>";
switch($proses)
{
	case'preview':
          
        echo $tab;
	break;
	case'pdf':
	
        class PDF extends FPDF
       {
           function Header() {
               global $conn;
               global $dbname;
               global $align;
               global $length;
               global $colArr;
               global $title;
               global $kdUnit;
               global $periode;
               global $dtVhc;
               global $optNm;
               global $listNoakun;
               global $jmlhCols;
               global $optNmAkun;

                           # Alamat & No Telp
               $query = selectQuery($dbname,'organisasi','alamat,telepon',
                   "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
               $orgData = fetchData($query);

               $width = $this->w - $this->lMargin - $this->rMargin;
               $height = 15;
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

               $this->SetFont('Arial','B',12);
                       //	$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['laporanKendAb'],'',0,'L');
                       //	$this->Ln();
                               $this->SetFont('Arial','',8);
                               $this->SetFont('Arial','U',12);
                               $this->Cell($width,$height, $_SESSION['lang']['laporanByRinciPerKend'],0,1,'C');	
                               $this->Ln();	
                                $this->SetFont('Arial','B',6);
                                       $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['kodetraksi'],'',0,'L');
                                       $this->Cell(5,$height,':','',0,'L');
                                       $this->Cell(25/100*$width,$height,$kdUnit." [".$optNm[$kdUnit]."]",'',0,'L');
                                       $this->Ln();

                                       $this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
                                       $this->Cell(5,$height,':','',0,'L');
                                       $this->Cell(25/100*$width,$height,$periode,'',0,'L');
                                       $this->Ln();					
               $this->SetFont('Arial','B',7);	
               $this->SetFillColor(220,220,220);
               $this->Cell(8/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);	
               $this->Cell(10/100*$width,$height,$_SESSION['lang']['total'],1,0,'C',1);	
               $totalBaris=count($listNoakun);
               $jmlhCols=$totalBaris*10;
               $jmlhCols=18+$jmlhCols;
               foreach($listNoakun as $dafNoakun)
               {
                  $are++;
                  if($are<$totalBaris)
                  {
                   $this->Cell(10/100*$width,$height,$optNmAkun[$dafNoakun],1,0,'L',1);
                  }
                  else
                  {
                      $this->Cell(10/100*$width,$height,$optNmAkun[$dafNoakun],1,1,'L',1);
                  }
               }

           }

           function Footer()
           {
               $this->SetY(-15);
               $this->SetFont('Arial','I',8);
               $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
           }
       }
       $pdf=new PDF('L','pt','A4');
       $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
       $height = 12;
               $pdf->AddPage();
               $pdf->SetFillColor(255,255,255);
               $pdf->SetFont('Arial','',7);
               $no=1;
               foreach($dtVhc as $listDataVhc)
               {            

                   if($no==1)
                   {
                       $pdf->Cell(8/100*$width,$height,$listDataVhc,1,0,'L',1);		
                       $pdf->Cell(10/100*$width,$height,number_format($totJumlah[$listDataVhc],2),1,0,'R',1);	
                   }
                   else
                   {
                      $akhiry=$pdf->GetY();
                      $akhirx=$pdf->GetX();
                      $pdf->SetY($akhiry+12);
                      $pdf->SetX($akhirx-($jmlhCols/100*$width));
                      $pdf->Cell(8/100*$width,$height,$listDataVhc,1,0,'L',1);		
                      $pdf->Cell(10/100*$width,$height,number_format($totJumlah[$listDataVhc],2),1,0,'R',1);	
                   }
//                    $akhiry2=$pdf->GetY();
//                    $akhirx2=$pdf->GetX();
//                    $pdf->SetY($akhiry2+12);
//                    $pdf->SetX($akhirx2);   
                   foreach($listNoakun as $dafNoakun)
                   {
                        if($no==1)
                        {
                           $akhiry2=$pdf->GetY();
                           $akhirx2=$pdf->GetX();
                           $pdf->SetY($akhiry2);
                           $pdf->SetX($akhirx2); 
                            $pdf->Cell(10/100*$width,$height,number_format($listNamaakun[$listDataVhc][$dafNoakun],0),1,0,'R',1);
                        }
                        else
                        {
                           $akhiry2=$pdf->GetY();
                           $akhirx2=$pdf->GetX();
                           $pdf->SetY($akhiry2);
                           $pdf->SetX($akhirx2);
                           $pdf->Cell(10/100*$width,$height,number_format($listNamaakun[$listDataVhc][$dafNoakun],0),1,0,'R',1);
                        }
                   }
                   $no+=1;

               }

       $pdf->Output();
       break;
       case'excel':


                       //echo "warning:".$strx;
                       //=================================================
               $tab.="Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

                       $nop_="laporan_penggunaan_bahan_".$kdUnit;
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

       default:
       break;
}

?>