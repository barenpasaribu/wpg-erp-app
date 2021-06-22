<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
?>
<script language=javascript1.2 src="js/generic.js"></script>
<script language=javascript1.2 src="js/vhc_2biayatotalperkendaraan.js"></script>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$kodevhc=$_GET['kodevhc'];
$tanggalmulai=$_GET['tanggalmulai'];
$tanggalsampai=$_GET['tanggalsampai'];
$unit=$_GET['unit'];
$periode=$_GET['periode'];

$noakunawal=$_GET['noakunawal'];
$noakunakhir=$_GET['noakunakhir'];

//=================================================

$stream="Vehicle Cost  :".$kodevhc." ".$_SESSION['lang']['tanggal']." :".$tanggalmulai." s.d.".$tanggalsampai."";
if($_GET['type']!='excel')
{
    echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailExcel(event)\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset><input type=hidden id=kodevhc value='".$kodevhc."' />
    <input type=hidden id=tanggalmulai value='".$tanggalmulai."' />
    <input type=hidden id=tanggalsampai value='".$tanggalsampai."' />
    <input type=hidden id=unit value='".$unit."' />
    <input type=hidden id=noakunawal value='".$noakunawal."' />
    <input type=hidden id=noakunakhir value='".$noakunakhir."' />";
    $stream.="<table class=sortable border=0 cellspacing=1>"; 
    
}
else
{
 echo"<fieldset><legend>Print Excel</legend>
    
     </fieldset>";
    $stream.="<table class=sortable border=1 cellspacing=1>";
}
$stream.="
      <thead>
        <tr class=rowcontent>
          <td bgcolor=#DEDEDE align=center>No.</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noakun']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namaakun']."</td>    
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['kodeblok']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
          ";
        $stream.="</tr>
      </thead>
      <tbody>";
	$str="select a.tanggal, a.noakun, a.keterangan, a.debet as jumlah, a.kodevhc,a.kodeblok,a.noreferensi,b.namaakun 
              from ".$dbname.".keu_jurnaldt_vw a left join ".$dbname.".keu_5akun b
              on a.noakun=b.noakun    
              where kodevhc = '".$kodevhc."'
              and tanggal>='".$tanggalmulai."' and tanggal<='".$tanggalsampai."' 
              and nojurnal like '%".$unit."%' and (a.noakun between '".$noakunawal."' and '".$noakunakhir."')
              and (noreferensi not like '%ALK_KERJA_AB%' or noreferensi is NULL)";
    // exit("Error:".$str);
    $res=mysql_query($str);
    $no=0;
    $total=0;
    while($bar= mysql_fetch_object($res))
    {
        $no+=1;
        if($bar->jumlah>0){
              $stream.="<tr class=rowcontent>
              <td align=right>".$no."</td>
              <td>".$bar->tanggal."</td>
              <td align=right>".$bar->noakun."</td>
              <td>".$bar->namaakun."</td>    
              <td>".$bar->keterangan."</td>
              <td align=right>".number_format($bar->jumlah)."</td>
              <td>".$bar->kodeblok."</td>
              <td>".$bar->noreferensi."</td>";
             $stream.="</tr>";
         $total+=$bar->jumlah;
        }          
    } 
    $stream.="<tr class=rowtitle>
              <td colspan=5 align=right>TOTAL :</td>
              <td align=right>".number_format($total)."</td>
              <td></td><td></td>";
         $stream.="</tr>";

   $stream.="</tbody></table>";
   if($_GET['type']=='excel')
   {
$nop_="Detail_BiayaPerKendaraan_".$kodevhc."_";
        if(strlen($stream)>0)
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
         if(!fwrite($handle,$stream))
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
   }
   else
   {
       echo $stream;
   }    
       
?>