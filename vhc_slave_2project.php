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

$optSupp=makeOption($dbname, 'log_5supplier','supplierid,namasupplier');
$optNmOrg=makeOption($dbname, 'organisasi','kodeorganisasi,namaorganisasi');

$_POST['kdOrg']==''?$kdOrg=$_GET['kdOrg']:$kdOrg=$_POST['kdOrg'];
$_POST['thnId']==''?$thnId=$_GET['thnId']:$thnId=$_POST['thnId'];
$_POST['kdProj']==''?$kdProj=$_GET['kdProj']:$kdProj=$_POST['kdProj'];

$unitId=$_SESSION['lang']['all'];
$dktlmpk=$_SESSION['lang']['all'];

if($proses=='preview'||$proses=='excel')
{


$brdr=0;
$bgcoloraja='';
if($proses=='excel')
{
    $bgcoloraja="bgcolor=#DEDEDE align=center";
    $brdr=1;
    $tab.="
    <table>
    <tr><td colspan=11 align=left><b><font size=5>Payment History</font></b></td></tr>
    <tr><td colspan=11 align=left>".$_SESSION['lang']['pt']." : ".$unitId."</td></tr>
    <tr><td colspan=11 align=left>".$_SESSION['lang']['periode']." : ".$periode."</td></tr>
    </table>";
}

	$tab.="<table cellspacing=1 border=".$brdr." class=sortable>
	<thead class=rowheader>
	<tr>
        <td ".$bgcoloraja." align=center>No.</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['kodeorg']."</td>
        <td ".$bgcoloraja." align=center>Project Code</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['nama']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['tanggalmulai']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['tanggalselesai']."</td>
        <td ".$bgcoloraja." align=center>".$_SESSION['lang']['biaya']."</td></tr>";
        $tab.="</tr></thead><tbody>";
        $sData="select distinct * from ".$dbname.".project
        where substr(tanggalmulai,1,4)='".$thnId."' and kodeorg='".$kdOrg."'";
        $qData=mysql_query($sData) or die(mysql_error($conn));
        while($rData=  mysql_fetch_assoc($qData))
        {
           $nor+=1;
           $sBiaya="select distinct sum(jumlah) as biaya from ".$dbname.".keu_jurnaldt
                   where kodeasset='".$rData['kode']."'";
           $qBiaya=mysql_query($sBiaya) or die(mysql_error($conn));
           $rBiaya=mysql_fetch_assoc($qBiaya);
           $tab.="<tr class=rowcontent style='cursor:pointer;' onclick=getDetail('".$rData['kode']."')><td align=center>".$nor."</td>";
           $tab.="<td>".$rData['kodeorg']."</td>";
           $tab.="<td>".$rData['kode']."</td>";
           $tab.="<td>".$rData['nama']."</td>";
           $tab.="<td>".$rData['tanggalmulai']."</td>";
            $tab.="<td>".$rData['tanggalselesai']."</td>";
           $tab.="<td  align=right>".number_format($rBiaya['biaya'],0)."</td>";
        
        }
        $tab.="</tbody></table>";
}
        
switch($proses)
{
	case'getPt':
	//echo "warning:masuk";
	$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
	$sOrg="select distinct kodeorg  from ".$dbname.".log_po_vw where substr(tanggal,1,7)='".$periode."'";
        //exit("Error:".$sOrg);
	$qOrg=mysql_query($sOrg) or die(mysql_error());
	while($rOrg=mysql_fetch_assoc($qOrg))
	{
		$optorg.="<option value=".$rOrg['kodeorg'].">".$optNmOrg[$rOrg['kodeorg']]."</option>";
	}
	echo $optorg;
	break;
        
	case'preview':
	echo $tab;
	break;
    
    case'excel':

        $tab.="Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
        $dte=date("Hms");
        $nop_="riwayat_pembayaran_".$dte;
         $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
         gzwrite($gztralala, $tab);
         gzclose($gztralala);
         echo "<script language=javascript1.2>
            window.location='tempExcel/".$nop_.".xls.gz';
            </script>";
			
	break;
       
        case'getDetail':
            $shead="select distinct nama from ".$dbname.".project
                   where kode='".$kdProj."'";
            $qhead=mysql_query($shead) or die(mysql_error($conn));
            $rhead=mysql_fetch_assoc($qhead);
            $tab.="<table cellpadding=1 cellspacing=1 border=0><tr><td>Kode Project</td><td>:</td>";
            $tab.="<td>".$kdProj."</td></tr>";
            $tab.="<tr><td>Nama Project</td><td>:</td><td>".$rhead['nama']."</td></tr></table>";
            $tab.="<table cellpadding=1 cellspacing=1 border=0 class=sortable><thead><tr>";
            $tab.="<td>No.</td>";
            $tab.="<td>".$_SESSION['lang']['tanggal']."</td>";
            $tab.="<td>".$_SESSION['lang']['nojurnal']."</td>";
            $tab.="<td>".$_SESSION['lang']['keterangan']."</td>";
            $tab.="<td>".$_SESSION['lang']['noreferensi']."</td>";
            $tab.="<td>".$_SESSION['lang']['debet']."</td>";
            $tab.="<td>".$_SESSION['lang']['kredit']."</td></tr></thead><tbody>";
            $sDetail="select distinct keterangan,noreferensi,nojurnal,tanggal
                     from ".$dbname.".keu_jurnaldt where kodeasset='".$kdProj."'";
            //exit("Error:".$sDetail);
            $qDetail=mysql_query($sDetail) or die(mysql_error($conn));
            $row=mysql_num_rows($qDetail);
            if($row!=0)
            {
                while($rDetail=  mysql_fetch_assoc($qDetail))
                {
                    $nor+=1;
                    $sMin="select distinct sum(jumlah) as debet from ".$dbname.".keu_jurnaldt
                          where nojurnal='".$rDetail['nojurnal']."' and jumlah<0";
                    $qMin=mysql_query($sMin) or die(mysql_error($conn));
                    $rMin=mysql_fetch_assoc($qMin);

                    $sPlus="select distinct sum(jumlah) as kredit from ".$dbname.".keu_jurnaldt
                          where nojurnal='".$rDetail['nojurnal']."' and jumlah>0";
                    $qPlus=mysql_query($sPlus) or die(mysql_error($conn));
                    $rPlus=mysql_fetch_assoc($qPlus);

                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$nor."</td>";
                    $tab.="<td>".$rDetail['tanggal']."</td>";
                    $tab.="<td>".$rDetail['nojurnal']."</td>";
                    $tab.="<td>".$rDetail['keterangan']."</td>";
                    $tab.="<td>".$rDetail['noreferensi']."</td>";
                    $tab.="<td>".$rMin['debet']."</td>";
                    $tab.="<td>".$rPlus['kredit']."</td></tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent><td colspan=7>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="<tr><td colspan=7><button class=mybutton onclick=kembaliAja()>Back</button></td></tr>";
            $tab.="</tbody></table>";
           
            echo $tab;
        break;
	
	default:
	break;
}
?>