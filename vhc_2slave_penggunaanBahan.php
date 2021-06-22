<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$_GET['proses']==''?$proses=$_POST['proses']:$proses=$_GET['proses'];
$_POST['kdUnit']==''?$kdUnit=$_GET['kdUnit']:$kdUnit=$_POST['kdUnit'];
$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$_POST['status']==''?$status=$_GET['status']:$status=$_POST['status'];
$_POST['kodevhc']==''?$kodevhc=$_GET['kodevhc']:$kodevhc=$_POST['kodevhc'];
$kodebarang=$_POST['kodebarang'];

$optNmSat=makeOption($dbname, 'log_5masterbarang', 'kodebarang,satuan');
$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
if($kdUnit=='')
{
    exit("Error: Organizer core required");
}
if($kdUnit!='')
{
    $where="  kodetraksi='".$kdUnit."'";
}
if($periode!='')
{
    $where.=" and tanggal like '".$periode."%'";
}
// list kendaraan
$sData="select distinct kodevhc from ".$dbname.".log_bahan_kendaraan_vw where ".$where." order by kodevhc asc";
//exit("error".$sData);
$qData=mysql_query($sData) or die(mysql_error());
while($rList=mysql_fetch_assoc($qData))
{
    $dataVhc[]=$rList['kodevhc'];
}
// jumlah pakai
$sJmlh="select a.kodevhc,jenisbbm,sum(a.jlhbbm) as jlbbm from  ".$dbname.". vhc_runht a 
        left join ".$dbname.".vhc_5master b on a.kodevhc=b.kodevhc
        where ".$where." group by a.kodevhc,jenisbbm";
//echo $sJmlh;
$qJmlh=mysql_query($sJmlh) or die(mysql_error($conn));
while($rJmlh=mysql_fetch_assoc($qJmlh))
{
  $dtJlhPakai[$rJmlh['kodevhc']][$rJmlh['jenisbbm']]+= $rJmlh['jlbbm'];
  $dtBarang[$rJmlh['kodevhc']]=$rJmlh['jenisbbm'];
}

// cari induk (dz mar 26, 2012)
$sinduk="select induk from  ".$dbname.".organisasi 
        where kodeorganisasi = '".$kdUnit."' order by kodeorganisasi desc limit 1";
$qinduk=mysql_query($sinduk) or die(mysql_error($conn));
while($rinduk=mysql_fetch_assoc($qinduk))
{
  $induk= $rinduk['induk'];
}
if(strlen($induk)==4){
    $sinduk2="select induk from  ".$dbname.".organisasi 
            where kodeorganisasi = '".$induk."'";
    $qinduk2=mysql_query($sinduk2) or die(mysql_error($conn));
    while($rinduk2=mysql_fetch_assoc($qinduk2))
    {
      $induk= $rinduk2['induk'];
    }
}
//echo $induk;

// cari harga rata
$sinduk="select hargarata,kodebarang from  ".$dbname.".log_5saldobulanan 
        where kodeorg = '".$induk."' and periode = '".$periode."'";
//echo $induk;
$qinduk=mysql_query($sinduk) or die(mysql_error($conn));
while($rinduk=mysql_fetch_assoc($qinduk))
{
  $hargarata[$rinduk['kodebarang']]= $rinduk['hargarata'];
}
//echo $induk;

$brd=0;
$bgBelakang='';
if($proses=='excel')
{
    $brd=1;
    $bgBelakang="bgcolor=#00FF40 align=center";
    $tab="<table>
            <tr><td colspan=5 align=center>".$_SESSION['lang']['laporanByBahan']."</td></tr>
            <tr><td colspan=5 align=left>".$_SESSION['lang']['kodetraksi']." : ".$kdUnit." [".$optNm[$kdUnit]."]</td></tr>
            <tr><td colspan=5 align=left>".$_SESSION['lang']['periode']." : ".$periode."</td></tr>
            <tr><td colspan=5></td><td></td></tr>
            </table>";
}
if($proses!='getDetail')
{
    // header
$tab.="<table cellpadding=1 cellspacing=1 border=".$brd." class=sortable>
<thead>
<tr class=rowheader>            
<td ".$bgBelakang.">".$_SESSION['lang']['kodevhc']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['namabarang']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['jumlahkeluargudang']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['jmlhPakai']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['satuan']."</td>
<td ".$bgBelakang.">HM/KM</td>
<td ".$bgBelakang.">".$_SESSION['lang']['satuan']."</td>
<td ".$bgBelakang.">".$_SESSION['lang']['hargagudang']."</td>   
</tr>
</thead><tbody id=containDataStock>";
$grandTotal=0;
$cekDt=count($dataVhc);
if($cekDt!=0)
{
    // hm/km
    $sJmlh="select distinct sum(a.jumlah) as totjmhm,a.satuan,b.kodevhc from ".$dbname.".vhc_rundt a
            left join ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
	    left join ".$dbname.".vhc_5master c on b.kodevhc=c.kodevhc
            where substr(tanggal,1,7)='".$periode."' and kodetraksi like '%".substr($kdUnit,0,4)."%' group by b.kodevhc";
			//echo $sJmlh;
    $qJmlh=mysql_query($sJmlh) or die(mysql_error($conn));
    while($rJmlh=mysql_fetch_assoc($qJmlh))
    {
        $dtJmlh[$rJmlh['kodevhc']]=$rJmlh['totjmhm'];
        $dtSat[$rJmlh['kodevhc']]=$rJmlh['satuan'];
    }
foreach($dataVhc as $listDataVhc)
{
    $hrgSemua=0;
    // jumlah keluar gudang + harga gudang
    $sData="select sum(jumlah) as jumlah,sum(hargatotal) as hrgTotal,kodevhc,namabarang,kodebarang from ".$dbname.".log_bahan_kendaraan_vw 
        where kodevhc='".$listDataVhc."' and substr(tanggal,1,7)='".$periode."' group by kodevhc,kodebarang ";
//    if($listDataVhc=='BG8401FM')echo $sData;
    // exit("error".$sData);
    $qData=mysql_query($sData) or die(mysql_error());
    while($rData=mysql_fetch_assoc($qData))
    {
        $no+=1;
        
        $tab.="<tr class=rowcontent>";
        $tab.="<td align=right>".$rData['kodevhc']."</td>";
        $tab.="<td>".$rData['namabarang']."</td>";
        $arrd="##kodevhc_".$no."##periode_".$no."##status_".$no."";
        $arrb="##kodevhc_".$no."##periode_".$no."##statusb_".$no."";
        if($proses!='excel')
        {
            //$tab.="<td align=right onclick=\"previewDetail('".$rData['kodevhc']."','".$periode."','0','".$rData['kodebarang']."','".$kdUnit."','event')\" style='cursor:pointer;' title='get detail jlh keluar dari gudang'>".number_format($rData['jumlah'],0)."</td>";
            //$tab.="<td align=right onclick=\"previewDetail('".$rData['kodevhc']."','".$periode."','1','".$rData['kodebarang']."','".$kdUnit."','event')\" style='cursor:pointer;' title='get detail pemakaian'>".$dtJlhPakai[$rData['kodevhc']][$rData['kodebarang']]."</td>";
            $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
            $tab.="<td align=right>".$dtJlhPakai[$rData['kodevhc']][$rData['kodebarang']]."</td>";			
        }
        else
        {
            $tab.="<td align=right>".number_format($rData['jumlah'],0)."</td>";
            $tab.="<td>".$dtJlhPakai[$rData['kodevhc']][$rData['kodebarang']]."</td>";
        }
        $tab.="<td>".$optNmSat[$rData['kodebarang']]."</td>";
        $tab.="<td align=right>".number_format($dtJmlh[$rData['kodevhc']])."</td>";
        $tab.="<td>".$dtSat[$rData['kodevhc']]."</td>";
//        $tab.="<td align=right>".number_format($rData['hrgTotal'],0)."</td>";
        $hrgTotal=$rData['jumlah']*$hargarata[$rData['kodebarang']];
        $tab.="<td align=right>".number_format($hrgTotal,0)."</td>";
        $tab.="</tr>";
        $hrgSemua+=$hrgTotal;
    }
    $tab.="<tr class=rowcontent>";
    $tab.="<td colspan=6 align=right>".$_SESSION['lang']['subtotal']." ".$listDataVhc."</td>";
    $tab.="<td align=right colspan=2>".number_format($hrgSemua,0)."</td>";
    $tab.="</tr>";
    $grandTotal+=$hrgSemua;
}
$tab.="<tr class=rowcontent>";
$tab.="<td colspan=7 align=right>".$_SESSION['lang']['grnd_total']."</td>";
$tab.="<td align=right>".number_format($grandTotal,0)."</td>";
$tab.="</tr>";
}
else
{
    $tab.="<tr class=rowcontent><td colspan=8>".$_SESSION['lang']['dataempty']."</td></tr>";
}
$tab.="</tbody></table>";
}
switch($proses)
{
	case'preview':
        echo $tab;
	break;
        case'getDetail':
              $sData="select distinct * from ".$dbname.".log_bahan_kendaraan_vw where kodevhc='".$kodevhc."' and tanggal like '".$periode."%' 
                    and kodebarang='".$kodebarang."'";
             //exit("error".$sData);
            if($status==0)
            {
                $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader><td>".$_SESSION['lang']['notransaksi']."</td><td>".$_SESSION['lang']['kodeabs']."</td>";
                $tab.="<td>".$_SESSION['lang']['tanggal']."</td><td>".$_SESSION['lang']['jumlah']."</td></tr></thead><tbody>";
                $sData="select distinct * from ".$dbname.".log_bahan_kendaraan_vw where kodevhc='".$kodevhc."' and tanggal like '".$periode."%' 
                    and kodebarang='".$kodebarang."'";
                //echo $sData;
                //exit("error".$sData);
                $qData=mysql_query($sData) or die(mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$rData['notransaksi']."</td>";
                    $tab.="<td>".$rData['kodegudang']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td  align=right>".$rData['jumlah']."</td>";
                    $tab.="</tr>";
                }
                $tab.="</tbody></table>";
                echo $tab;
            }
            elseif($status==1)
            {
                 $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable>";
                $tab.="<thead><tr class=rowheader><td>".$_SESSION['lang']['notransaksi']."</td><td>".$_SESSION['lang']['tanggal']."</td>";
                $tab.="<td>".$_SESSION['lang']['jumlah']."</td><td>".$_SESSION['lang']['satuan']."</td><td>".$_SESSION['lang']['jumlah']." BBM</td></tr></thead><tbody>";
                $sData="select distinct * from ".$dbname.".vhc_runht  where kodevhc='".$kodevhc."' and tanggal like '".$periode."%' 
                    and jenisbbm='".$kodebarang."'";
               // echo $sData;
                $qData=mysql_query($sData) or die(mysql_error($conn));
                while($rData=mysql_fetch_assoc($qData))
                {
                    $sGet="select distinct sum(jumlah) as jumlah,satuan from ".$dbname.".vhc_rundt where notransaksi='".$rData['notransaksi']."' 	
                           group by notransaksi";
                    $qGet=mysql_query($sGet) or die(mysql_error($conn));
                    $rGet=mysql_fetch_assoc($qGet);
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$rData['notransaksi']."</td>";
                    $tab.="<td>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=right>".$rGet['jumlah']."</td>";
                    $tab.="<td>".$rGet['satuan']."</td>";
                    $tab.="<td align=right>".$rData['jlhbbm']."</td>";
                    $tab.="</tr>";
                }
                $tab.="</tbody></table>";
                echo $tab;
                
            }
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
                global $rData;
                global $optNm;
                global $dataVhc;
				
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
				$this->Cell($width,$height, $_SESSION['lang']['laporanByBahan'],0,1,'C');	
				$this->Ln();	
				$this->SetFont('Arial','B',6);
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['unit'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(25/100*$width,$height,$optNm[$kdUnit],'',0,'L');
				$this->Ln();
				$this->Cell((20/100*$width)-5,$height,$_SESSION['lang']['periode'],'',0,'L');
				$this->Cell(5,$height,':','',0,'L');
				$this->Cell(25/100*$width,$height,$periode,'',0,'L');
				$this->Ln();					
                $this->SetFont('Arial','B',7);	
                $this->SetFillColor(220,220,220);
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['kodevhc'],1,0,'C',1);	
                $this->Cell(17/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);		
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['jumlahkeluargudang'],1,0,'C',1);
                $this->Cell(10/100*$width,$height,$_SESSION['lang']['jmlhPakai'],1,0,'C',1);
                $this->Cell(11/100*$width,$height,$_SESSION['lang']['satuan'],1,0,'C',1);		
                $this->Cell(8/100*$width,$height,$_SESSION['lang']['hargagudang'],1,1,'C',1);
                				
            }
                
            function Footer(){
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
                foreach($dataVhc as $listDataVhc){ 
                       $hrgSemua=0;
                       $sData="select sum(jumlah) as jumlah,sum(hargatotal) as hrgTotal,kodevhc,namabarang,kodebarang from ".$dbname.".log_bahan_kendaraan_vw where kodevhc='".$listDataVhc."' group by kodevhc,kodebarang";
                        $qData=mysql_query($sData) or die(mysql_error());
                        while($rData=mysql_fetch_assoc($qData)){                           
                            $no+=1;	
                            $pdf->Cell(8/100*$width,$height,$rData['kodevhc'],1,0,'C',1);		
                            $pdf->Cell(17/100*$width,$height,$rData['namabarang'],1,0,'L',1);		
                            $pdf->Cell(10/100*$width,$height,number_format($rData['jumlah'],0),1,0,'R',1);
                            $pdf->Cell(10/100*$width,$height,number_format($dtJlhPakai[$rData['kodevhc']][$rData['kodebarang']],0),1,0,'R',1);
                            $pdf->Cell(11/100*$width,$height,$optNmSat[$rData['kodebarang']],1,0,'C',1);
                            $hrgTotal=$rData['jumlah']*$hargarata[$rData['kodebarang']];
                            $pdf->Cell(8/100*$width,$height,number_format($hrgTotal,0),1,1,'R',1);	
                            $hrgSemua+=$hrgTotal;
                        }
                        $pdf->Cell(56/100*$width,$height,$_SESSION['lang']['subtotal']."  ".$listDataVhc,1,0,'R',1);
                        $pdf->Cell(8/100*$width,$height,number_format($hrgSemua,0),1,1,'R',1);
                        $grandTotal+=$hrgSemua;
                }
                $pdf->Cell(56/100*$width,$height,$_SESSION['lang']['grnd_total'],1,0,'R',1);
                $pdf->Cell(8/100*$width,$height,number_format($grandTotal,0),1,1,'R',1);
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