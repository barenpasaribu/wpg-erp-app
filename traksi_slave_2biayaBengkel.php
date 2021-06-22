<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}
//$arr="##thnId##unitId";

$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Oct","11"=>"Nov","12"=>"Dec");
$arrBln1=array("1"=>"Januari","2"=>"Februari","3"=>"March","4"=>"April","5"=>"Mei","6"=>"Juni","7"=>"July","8"=>"August","9"=>"September","10"=>"October","11"=>"November","12"=>"December");
$optNmBrg=makeOption($dbname, 'log_5masterbarang', 'kodebarang,namabarang');
$optInduk=makeOption($dbname, 'organisasi','kodeorganisasi,induk');
$arrDt=array("0"=>"Head Office","1"=>"Local");

$_POST['unitId']==''?$unitId=$_GET['unitId']:$unitId=$_POST['unitId'];
$_POST['thnId']==''?$thnId=$_GET['thnId']:$thnId=$_POST['thnId'];
$_POST['bulanId']==''?$bulanId=$_GET['bulanId']:$bulanId=$_POST['bulanId'];
//

if($proses=='bulanapaaja')
{
    //periode akuntansi
    $sPeriode="select distinct substr(tanggal,6,2) as bulan from ".$dbname.".vhc_penggantianht where tanggal like '".$thnId."%' order by tanggal desc";
    $qPeriode=mysql_query($sPeriode) or die(mysql_error());
    while($rPeriode=mysql_fetch_assoc($qPeriode))
    {
        $optPeriode.="<option value=".$rPeriode['bulan'].">".$arrBln1[intval($rPeriode['bulan'])]."</option>";
    }    
    echo $optPeriode;
}else{
    
if($unitId==''||$thnId=='')
{
    exit("Error: Fields are required");
}
    
//get data akun
if($_SESSION['language']=='EN'){
    $dd="namaakun1 as namaakun";
}else{
    $dd="namaakun";
}
$sAkun="select distinct noakun ,".$dd." from ".$dbname.".keu_5akun where noakun like '41101%' and noakun !='4110199' and CHAR_LENGTH(noakun)='7' order by noakun asc";
//exit("Error".$sAkun);
$qAkun=mysql_query($sAkun) or die(mysql_error($conn));
while($rAkun=mysql_fetch_assoc($qAkun))
{
    $lstAkun[]=$rAkun['noakun'];
    $dtNamAkun[$rAkun['noakun']]=$rAkun['namaakun'];
}
//biaya budget
$sBudget="select distinct noakun,sum(rp01) as rp01,sum(rp02) as rp02,sum(rp03) as rp03,sum(rp04) as rp04,sum(rp05) as rp05,sum(rp06) as rp06,
          sum(rp07) as rp07,sum(rp01) as rp08,sum(rp09) as rp09,sum(rp12) as rp12,sum(rp11) as rp11,sum(rp12) as rp12
          from ".$dbname.".bgt_ws_vw where tahunbudget='".$thnId."' and kodeorg like '".substr($unitId,0,4)."%' group by tahunbudget,noakun";
// exit("Error".$sBudget);
$qBudget=mysql_query($sBudget) or die(mysql_error($conn));
while($rBudget=mysql_fetch_assoc($qBudget))
{
    for($bln=1;$bln<13;$bln++)
    {
        $sdt=$bln;
        if($bln<10)
        {
          $sdt="0".$bln;
        }
        $dtRupBudget[$rBudget['noakun']][$bln]=$rBudget['rp'.$sdt];
        $totBudget[$bln]+=$dtRupBudget[$rBudget['noakun']][$bln];
    }
}
//realisai  atau aktual
for($ngulang=1;$ngulang<=12;$ngulang++)
{
      $sdt=$ngulang;
     if($ngulang<10)
        {
          $sdt="0".$ngulang;
        }
        $sAktual="select distinct sum(jumlah) as jumlah,noakun,periode from ".$dbname.".keu_jurnaldt_vw 
                  where noakun like '41101%' and noakun != '4110199' and periode='".$thnId."-".$sdt."' and kodeorg like '".substr($unitId,0,4)."%' group by periode,noakun";
        //echo $sAktual;
        $qAktual=mysql_query($sAktual) or die(mysql_error($conn));
        while($rAktual=mysql_fetch_assoc($qAktual))
        {
            
            //mengantisipasi jika datanya min yang seharusnya di ambil di kredit
            if($rAktual['jumlah']<1)
            {
                $rAktual['jumlah']=$rAktual['jumlah']*-1;
            }
           $dtRupAktual[$rAktual['noakun']][$ngulang]=$rAktual['jumlah'];
           $totAktual[$ngulang]+=$dtRupAktual[$rAktual['noakun']][$ngulang];
        }
}

//jam budget
$sJmBudget="select distinct * from ".$dbname.".bgt_ws_jam where tahunbudget='".$thnId."' and kodetraksi like '".substr($unitId,0,4)."%'";
//exit("Error".$sJmBudget);
$qJmBudget=mysql_query($sJmBudget) or die(mysql_error($conn));
$rJmBudget=mysql_fetch_assoc($qJmBudget);
for($ngulanglg=1;$ngulanglg<13;$ngulanglg++)
{
    $sdt=$ngulanglg;
    if($ngulanglg<10)
    {
        $sdt="0".$ngulanglg;
    }
   
    $lstJmBudget[$ngulanglg]=$rJmBudget['jam'.$sdt];
}

//jam aktual adalah sum downtime grup by kodeorg,left(tanggal,7)




$sJam="select tanggal ,downtime as jmlhjam, namamekanik1, namamekanik2, namamekanik3, namamekanik4, namamekanik5 from ".$dbname.".vhc_penggantianht where
       kodeorg like '".substr($unitId,0,4)."%' and left(tanggal,4)='".$thnId."' ";
$qJam=mysql_query($sJam) or die(mysql_error($conn));
while($rJam=  mysql_fetch_assoc($qJam)){
        $a=$b=$c=$d=$e=0;
        if($rJam['namamekanik1']!='0000000000'){
            $a=1;
        }
        if($rJam['namamekanik2']!='0000000000'){
            $b=1;
        }
        if($rJam['namamekanik3']!='0000000000'){
            $c=1;
        }
        if($rJam['namamekanik4']!='0000000000'){
            $d=1;
        }
        if($rJam['namamekanik5']!='0000000000'){
            $e=1;
        }
        $jmlmekanik=$a+$b+$c+$d+$e;

//    $bln=substr($rJam['periode'],-2,2);
    $bubulan=intval(substr($rJam['tanggal'],5,2));
    $dtJmAk[$bubulan]+=($rJam['jmlhjam']*$jmlmekanik);
}

//exit("error:".$bln."__".$rJam['periode']."__".$sJam);

$brsdt=count($data);
$brdr=0;
$bgcoloraja='';
//
if($proses=='excel')
{
      $bgcoloraja="bgcolor=#DEDEDE ";
      $brdr=1;
}
        //biaya bengkel
        $tab.="<p>".$_SESSION['lang']['biayabengkel']." : ".$unitId.", ".$_SESSION['lang']['tahun']." : ".$thnId."</p>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr>";
        $tab.="<td ".$bgcoloraja."  rowspan=2>No.</td>";
        $tab.="<td ".$bgcoloraja."  rowspan=2>".$_SESSION['lang']['noakun']."</td>";
        $tab.="<td ".$bgcoloraja."  rowspan=2>".$_SESSION['lang']['namaakun']."</td>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
           $tab.="<td ".$bgcoloraja." colspan=2 align=center >".$isiBLn." (Rp) (000)</td>";
        }
        $tab.="</tr><tr>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td ".$bgcoloraja."  align=center>Budget</td>";
            $tab.="<td ".$bgcoloraja."  align=center>Actual</td>";
        } 
        $tab.="</tr></thead><tbody>";
        foreach($lstAkun as $dtNoakun)
        {
                $no+=1;
                $tab.="<tr class=rowcontent>";
                $tab.="<td>".$no."</td>";
                $tab.="<td>".$dtNoakun."</td>";
                $tab.="<td>".$dtNamAkun[$dtNoakun]."</td>";
                foreach($arrBln as $listBulan =>$isiBLn)
                {
                    if($listBulan<10){
                        $zz="0".$listBulan;
                    }
                    else{
                        $zz=$listBulan;
                    }
                    @$dtRupBudget[$dtNoakun][$listBulan]=$dtRupBudget[$dtNoakun][$listBulan]/1000;
                    @$dtRupAktual[$dtNoakun][$listBulan]=$dtRupAktual[$dtNoakun][$listBulan]/1000;
                    $tab.="<td align=right>".number_format($dtRupBudget[$dtNoakun][$listBulan],2)."</td>";
                    $tab.="<td align=right style='cursor:pointer;'  title='Click for detail' onclick=displayDetail('".$thnId."-".$zz."','".$dtNoakun."','".substr($unitId,0,4)."',event)>".number_format($dtRupAktual[$dtNoakun][$listBulan],2)."</td>";
                    
                    
                    
                }
                $tab.="</tr>";
        }
        $tab.="<tr class=rowcontent>";
        $tab.="<td colspan=3>".$_SESSION['lang']['total']."</td>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            @$totBagiBudget[$listBulan]=$totBudget[$listBulan]/1000;
            @$totBagiAktual[$listBulan]=$totAktual[$listBulan]/1000;
            $tab.="<td align=right>".number_format($totBagiBudget[$listBulan],2)."</td>";
            $tab.="<td align=right>".number_format($totBagiAktual[$listBulan],2)."</td>";
        }
        $tab.="</tr>";
        $tab.="</tbody></table><br />";
        
        //jam bengkel
        $tab.="<p>".$_SESSION['lang']['jambengkel']." : ".$unitId.", ".$_SESSION['lang']['tahun']." : ".$thnId."</p>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr><td>&nbsp;</td>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td ".$bgcoloraja."  rowspan=2  align=center>".$isiBLn." ".$_SESSION['lang']['jmlhJam']."</td>";
        }
        $tab.="</tr></thead><tbody>";
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$_SESSION['lang']['anggaran']."</td>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td  align=right>".number_format($lstJmBudget[$listBulan],1)."</td>";
        }
        $tab.="</tr>";
        $tab.="<tr class=rowcontent>";
        $tab.="<td>".$_SESSION['lang']['realisasi']."</td>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td  align=right>".number_format($dtJmAk[$listBulan],1)."</td>";
        }
        $tab.="</tr>";
        $tab.="</tbody></table>";
        
        //rupiah/jam
        $tab.="<p>Cost/Hour  : ".$unitId.", ".$_SESSION['lang']['tahun']." : ".$thnId."</p>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td ".$bgcoloraja." colspan=2  align=center>".$isiBLn."  (Rp) (000)</td>";
        }
        $tab.="</tr><tr>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            $tab.="<td ".$bgcoloraja."  align=center>Budget</td>";
            $tab.="<td ".$bgcoloraja."  align=center>Actual</td>";
        }
        $tab.="</tr></thead><tbody><tr class=rowcontent>";
        foreach($arrBln as $listBulan =>$isiBLn)
        {
            @$hslBagiBudget[$listBulan]=($totBudget[$listBulan]/$lstJmBudget[$listBulan])/1000;
            @$hslBagiAktual[$listBulan]=($totAktual[$listBulan]/$dtJmAk[$listBulan])/1000;
            $tab.="<td ".$bgcoloraja." align=right>".number_format($hslBagiBudget[$listBulan],2)."</td>";
            $tab.="<td ".$bgcoloraja." align=right>".number_format($hslBagiAktual[$listBulan],2)."</td>";
        }
        $tab.="</tr></tbody></table>";
        
        $tab.="<p>".$_SESSION['lang']['jambengkel']." : ".$unitId.", ".$_SESSION['lang']['bulan']." : ".$bulanId."</p>";
	$tab.="<table cellspacing=1 cellpadding=1 border=".$brdr." class=sortable>
	<thead class=rowheader>";
        $tab.="<tr>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['nourut']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['kodeabs']." ".$_SESSION['lang']['kendaraan']."</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['tanggal']."</td>";
            $tab.="<td ".$bgcoloraja.">Jml Mekanik</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['downtime']."</td>";
            $tab.="<td ".$bgcoloraja.">Total Downtime</td>";
            $tab.="<td ".$bgcoloraja.">".$_SESSION['lang']['keterangan']."</td>";
        $tab.="</tr></thead><tbody>";

        $sJam="select * from ".$dbname.".vhc_penggantianht where
            kodeorg like '".substr($unitId,0,4)."%' and tanggal like '".$thnId."-".$bulanId."%'
                order by tanggal, kodevhc";
        $qJam=mysql_query($sJam) or die(mysql_error($conn));
        $nonono=0;
        $tototaljam=0;
        

        while($rJam=  mysql_fetch_assoc($qJam)){
        $a=$b=$c=$d=$e=0;
        if($rJam['namamekanik1']!='0000000000'){
            $a=1;
        }
        if($rJam['namamekanik2']!='0000000000'){
            $b=1;
        }
        if($rJam['namamekanik3']!='0000000000'){
            $c=1;
        }
        if($rJam['namamekanik4']!='0000000000'){
            $d=1;
        }
        if($rJam['namamekanik5']!='0000000000'){
            $e=1;
        }
        $jmlmekanik=$a+$b+$c+$d+$e;

            $nonono+=1;
            $bln=substr($rJam['periode'],-2,2);
            $dtJmAk[$bln]+=$rJam['jmlhjam'];
                $tab.="<tr class=rowcontent>";
                    $tab.="<td align=right>".$nonono."</td>";
                    $tab.="<td>".$rJam['kodevhc']."</td>";
                    $tab.="<td>".$rJam['tanggal']."</td>";
                    $tab.="<td align=right>".number_format($jmlmekanik)."</td>";
                    $tab.="<td align=right>".number_format($rJam['downtime'],1)."</td>";
                    $tab.="<td align=right>".number_format($jmlmekanik*$rJam['downtime'],1)."</td>";
                    $tab.="<td>".$rJam['kerusakan']."</td>";
                $tab.="</tr>";
                $tototaljam+=($jmlmekanik*$rJam['downtime']);
        }
                $tab.="<tr class=rowtitle>";
                    $tab.="<td colspan=5 ".$bgcoloraja.">Total</td>";
                    $tab.="<td align=right ".$bgcoloraja.">".number_format($tototaljam,1)."</td>";
                    $tab.="<td ".$bgcoloraja."></td>";
                $tab.="</tr>";
        $tab.="</tbody></table>";   
        
} // end of else bulan apa aja

        
switch($proses)
{
	case'preview':
	echo $tab;
	break;
        case'excel':
        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="biayabengkel_".$dte;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";	
	break;
        case'pdf':
      
           class PDF extends FPDF {
           function Header() {
            global $periode;
            global $kdUnit;
            global $optNmOrg;  
            global $dbname;
            global $thn;
            global $tot;

                
          }
              function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
            }
            //================================

            $pdf=new PDF('L','pt','A4');
            $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
            $height = 10;
            $tnggi=$jmlHari*$height;
            $pdf->AddPage();
            $pdf->SetFillColor(220,220,220);
            //$pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','B',4);
            $i=0;
            $pdf->Cell(15,$height,"No.",TLR,0,'C',1);
            $pdf->Cell(38,$height,$_SESSION['lang']['noakun'],TLR,0,'C',1);
            $pdf->Cell(70,$height,$_SESSION['lang']['noakun'],TLR,0,'C',1);
            foreach($arrBln as $listBulan =>$isiBLn)
            {
               if($listBulan!=12)
               {
                $pdf->Cell(56,$height,$isiBLn,TLR,0,'C',1);
               }
               else
               {
                   $pdf->Cell(56,$height,$isiBLn,TLR,1,'C',1);
               }
            }
            $pdf->Cell(15,$height," ",BLR,0,'C',1);
            $pdf->Cell(38,$height," ",BLR,0,'C',1);
            $pdf->Cell(70,$height," ",BLR,0,'C',1);
             foreach($arrBln as $listBulan =>$isiBLn)
            {
               if($listBulan!=12)
               {
                $pdf->Cell(28,$height,"Budget",TBLR,0,'C',1);
                $pdf->Cell(28,$height,"Aktual",TBLR,0,'C',1);
               }
               else
               {
                $pdf->Cell(28,$height,"Budget",TBLR,0,'C',1);
                $pdf->Cell(28,$height,"Aktual",TBLR,1,'C',1);
               }
            }
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',3.2);
            foreach($lstAkun as $dtNoakun)
            {
                    $no3+=1;
                    $pdf->Cell(15,$height,$no3,TBLR,0,'C',1);
                    $pdf->Cell(38,$height,$dtNoakun,TBLR,0,'L',1);
                    $pdf->Cell(70,$height,$dtNamAkun[$dtNoakun],TBLR,0,'L',1);
                   
                    foreach($arrBln as $listBulan =>$isiBLn)
                    {
                        if($listBulan!=12)
                        {
                        $pdf->Cell(28,$height,number_format($dtRupBudget[$dtNoakun][$listBulan],2),TBLR,0,'R',1);
                        $pdf->Cell(28,$height,number_format($dtRupAktual[$dtNoakun][$listBulan],2),TBLR,0,'R',1);
                        }
                        else
                        {
                        $pdf->Cell(28,$height,number_format($dtRupBudget[$dtNoakun][$listBulan],2),TBLR,0,'R',1);
                        $pdf->Cell(28,$height,number_format($dtRupAktual[$dtNoakun][$listBulan],2),TBLR,1,'R',1);
                        }
                        $totBudget[$listBulan]+=$dtRupBudget[$dtNoakun][$listBulan];
                        $totAktual[$listBulan]+=$dtRupAktual[$dtNoakun][$listBulan];
                    }
                    $tab.="</tr>";
            }

            $pdf->Output();
            break;
	
            
	
	default:
	break;
}
      
?>