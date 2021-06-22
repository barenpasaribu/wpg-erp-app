<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<link rel=stylesheet type='text/css' href='style/generic.css'>
<?
   
$noakun=$_GET['noakun'];
$pt=$_GET['pt'];
$unit=$_GET['unit'];
$periode=$_GET['periode'];
$tt=$_GET['tt'];

// default: pt - unit
$kodeorg=" like '".$unit."%' ";

// kalo pt doang, dapatkan unit-unitnya
$unitunit="('')";
if($unit==''){
    $unitunit="(";
    $str="select kodeorganisasi from ".$dbname.".organisasi 
        where induk='".$pt."' and tipe='KEBUN'";
    $query=mysql_query($str) or die(mysql_error($conn));
    while($res=  mysql_fetch_assoc($query))
    {
        $unitunit.="'".$res['kodeorganisasi']."',";
    }    
    $unitunit=substr($unitunit,0,-1);
    $unitunit=$unitunit.")";
    $kodeorg=" in ".$unitunit;
}

// ambil tt
//$str="SELECT tahuntanam, sum(luasareaproduktif) as luas FROM ".$dbname.".setup_blok
//    WHERE statusblok in('TBM','TB') and substr(kodeorg,1,4) ".$kodeorg." group by tahuntanam order by tahuntanam";
$str="SELECT tahuntanam, sum(luasareaproduktif) as luas FROM ".$dbname.".setup_blok
    WHERE substr(kodeorg,1,4) ".$kodeorg." group by tahuntanam order by tahuntanam";
$tahuntahun="(";
$query=mysql_query($str) or die(mysql_error($conn));
while($res=mysql_fetch_assoc($query))
{
    if($res['tahuntanam']>0)
    $tahuntahun.="'".$res['tahuntanam']."',";
}   
$tahuntahun=substr($tahuntahun,0,-1);
$tahuntahun.=")";
$tahuntanam="(b.tahuntanam not in ".$tahuntahun." or b.tahuntanam is null)";

if($tt>0){
    $tahuntanam="b.tahuntanam = '".$tt."'";
}

//ambil namapt
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$pt."'";
$namapt='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namapt=strtoupper($bar->namaorganisasi);
}

//ambil namagudang
$str="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$unit."'";
$namaunit='';
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namaunit=strtoupper($bar->namaorganisasi);
}

//ambil mutasi-----------------------
$str="select a.*, b.tahuntanam
    from ".$dbname.".keu_jurnaldt_vw a
    left join ".$dbname.".setup_blok b on a.kodeblok = b.kodeorg
    where a.kodeorg ".$kodeorg." and a.periode <= '".$periode."' and a.noakun like '".$noakun."%' and ".$tahuntanam."";
 
//=================================================
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"parent.detailKeExcel(event,'mr_slave_biayaTbm_detail.php?type=excel&noakun=".$noakun."&pt=".$pt."&unit=".$unit."&periode=".$periode."&tt=".$tt."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']=='excel')$border=1; else $border=0;
$stream="<table class=sortable border=".$border." cellspacing=1>
    <thead>
    <tr class=rowcontent>
        <td>No</td>
        <td>No.Transaksi</td>
        <td>Tanggal</td>
        <td>No.Akun</td>
        <td>Keterangan</td>
        <td>Debet</td>
        <td>Kredit</td>
        <td>Karyawan</td>
        <td>Mesin</td>
        <td>Blok</td>
        <td>Tahun Tanam</td>
    </tr>
    </thead>
    <tbody>";
$res=mysql_query($str);
$no=0;
$tdebet=0;
$tkredit=0;
while($bar= mysql_fetch_object($res))
{
    $no+=1;
    $debet=0;
    $kredit=0;
    if($bar->jumlah>0)
         $debet= $bar->jumlah;
    else
         $kredit= $bar->jumlah*-1;

    $noref=$bar->noreferensi;
    if(trim($noref)=='')$noref=$bar->nojurnal;
    $stream.="<tr class=rowcontent>
           <td>".$no."</td>
           <td>".$noref."</td>               
           <td>".tanggalnormal($bar->tanggal)."</td>    
           <td>".$noakun."</td>    
           <td>".$bar->keterangan."</td>
           <td align=right>".number_format($debet)."</td>
           <td align=right>".number_format($kredit)."</td>  
           <td align=right>".$bar->karyawanid."</td>
           <td align=right>".$bar->kodevhc."</td>
           <td align=right>".$bar->kodeblok."</td>  
           <td align=right>".$bar->tahuntanam."</td>  
        </tr>";
    $tdebet+=$debet;
    $tkredit+=$kredit;    
} 
$stream.="<tr class=rowcontent>
    <td colspan=5>TOTAL</td>
    <td align=right>".number_format($tdebet)."</td>
    <td align=right>".number_format($tkredit)."</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>";  
$stream.="</tbody><tfoot></tfoot></table>";
if($_GET['type']=='excel')
{
    $stream.="Print Time:".date('Y-m-d H:i:s')."<br />By:".$_SESSION['empl']['name'];
    $nop_="Detail_jurnal_".$_GET['gudang']."_".$_GET['periode'];
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