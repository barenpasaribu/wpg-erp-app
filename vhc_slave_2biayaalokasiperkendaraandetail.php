<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
?>
<script language='javascript1.2' src="js/generic.js"></script>
<script language='javascript1.2' src="js/vhc_2biayatotalperkendaraan.js"></script>
<link rel='stylesheet' type='text/css' href="style/generic.css" />

<?      

$param=$_GET;
  $str="select a.notransaksi,a.alokasibiaya,a.keterangan,a.jumlah,b.tanggal from ".$dbname.".vhc_rundt a left join
        ".$dbname.".vhc_runht b on a.notransaksi=b.notransaksi
        where kodevhc='".$param['kodevhc']."' 
        and tanggal between '".tanggalsystem($param['tglAwal'])."' and '".tanggalsystem($param['tglAkhir'])."'";
$res=mysql_query($str);
echo"<fieldset><legend>Print Excel</legend>
     <img onclick=\"detailData(event,'vhc_slave_2biayaalokasiperkendaraandetail.php?type=excel&kodevhc=".$param['kodevhc']."&tglAwal=".$param['tglAwal']."&tglAkhir=".$param['tglAkhir']."&hrgaSatuan=".$param['hrgaSatuan']."')\" src=images/excel.jpg class=resicon title='MS.Excel'>
     </fieldset>";
if($_GET['type']!='excel')
$stream="Detail Activity :".$param['kodevhc']." ".$_SESSION['lang']['tanggal'].":".$param['tglAwal']." - ".$param['tglAkhir'].";
      <table class=sortable cellspacing=1 border=0>";
else
$stream="Detail Activity :".$param['kodevhc']." ".$_SESSION['lang']['tanggal'].":".$param['tglAwal']." - ".$param['tglAkhir'].";
      <table class=sortable cellspacing=1 border=1>";
$stream.="<thead>
      <tr class=rowheader><td bgcolor=#DEDEDE align=center>No</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['tanggal']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['notransaksi']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['alokasibiaya']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['keterangan']."</td>
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['jumlah']."(HM/KM)</td> 
          <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['harga']."</td>
      </tr>
      </thead>
      <tbody>";
$no=0;
$ttl=0;
while($bar=mysql_fetch_object($res))
{
    $hrg=$param['hrgaSatuan']*$bar->jumlah;
   $no+=1;
    $stream.="<tr class=rowcontent>
          <td>".$no."</td>
          <td>".tanggalnormal($bar->tanggal)."</td>   
          <td>".$bar->notransaksi."</td>
          <td>".$bar->alokasibiaya."</td>
          <td>".$bar->keterangan."</td>    
          <td align=right>".number_format($bar->jumlah,2)."</td>
          <td align=right>".number_format($hrg,2)."</td>
      </tr>";  
    $ttl+=$bar->jumlah;
    $total+=$hrg;
}
    $stream.="<tr class=rowcontent>
          <td colspan=5 align=center>Total</td> 
          <td align=right>".$ttl."</td>
          <td align=right>".number_format($total,2)."</td>
      </tr>"; 
$stream.="</tbody><tfoot></tfoot></table>";
 if($_GET['type']=='excel')
   {
$nop_="Detail_BiayaAlokasi_".$param['kodevhc'];
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
