<?
require_once('master_validation.php');
require_once('config/connection.php');
include('lib/nangkoelib.php');

//+++++++++++++++++++++++++++++++++++++++++++++
$val=trim($_GET['string']);
$prd=trim($_GET['prd']);
$loktug=$_GET['loktug'];
if ($prd!='' && $prd!='-'){
	$where="and (a.`start`<='".$prd."' AND a.`end`>='".$prd."')";
}
else {
	$where="";
}
$pt=$_SESSION['empl']['lokasitugas'];

/*$str="select * from ".$dbname.".sdm_ho_component
      where name like '%Angs%'";*/
//Rubah kueri menjadi lihat berdasarkan flag isAngsuran
$str="select * from ".$dbname.".sdm_ho_component
      where isAngsuran=1";
$res=mysql_query($str,$conn);
$arr=Array();
$opt='';
while($bar=mysql_fetch_object($res))
{
        $arr[$bar->id]=$bar->name;
}
$valstat=$val;

if($loktug==''){
	$filter="";			
}
else {
	$filter="and u.lokasitugas='".$loktug."'";
}
switch ($val){
        case 'lunas':
                         $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                         ".$filter."
                                      and a.totalbayar=a.total ".$where."
                                          order by namakaryawan";		
        break;
        case 'blmlunas':
                        $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          ".$filter."
                                      and a.totalbayar<a.total ".$where."
                                          order by namakaryawan";
                         
        break;
        case 'active':
                        $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          ".$filter."
                                      and `active`=1 ".$where."
                                          order by namakaryawan";
        break;
        case 'notactive':
                        $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          ".$filter."
                                      and `active`=0 ".$where."
                                          order by namakaryawan";
        break;
        case '':
                        $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid
                                          ".$filter." ".$where."
                                      order by namakaryawan";
                break;	
        default:
                        /*if($_SESSION['org']['pusat']==1)
                        {			    
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid and
                                          ((u.tanggalkeluar = '0000-00-00' or u.tanggalkeluar > '".date("Y-m-d")."') and u.tipekaryawan in ('0','7','8') or u.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";
                        }
                        else
                        {
                                $str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid 
                                          and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
                                          and (`start`<='".$val."' AND `end`>='".$val."')
                                          order by namakaryawan";		
                        }*/	  					  					  			  
                        $str="select a.*,u.namakaryawan, u.lokasitugas from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
                                      where a.karyawanid=u.karyawanid 
                                          ".$filter."
                                          order by namakaryawan";
		break;
}	


date_default_timezone_set('Asia/Jakarta');
$head=$_SESSION['lang']['laporanangsuran']. "
		<br>
		".$_SESSION['lang']['tanggal'].": ".date('d-m-Y H:i')."
        <br>
		".$_SESSION['lang']['unit'].": ".$loktug."
		<br>
		".$_SESSION['lang']['status'].": ".$valstat."
 		<br>
		".$_SESSION['lang']['username'].": ".$_SESSION['standard']['username']."
        <table border=1>
        <thead>
		
		<tr>
	   	<td class=firsttd>".$_SESSION['lang']['no']."</td>
	   	<td class=firsttd>".$_SESSION['lang']['nokaryawan']."</td>
		<td align=center>".$_SESSION['lang']['namakaryawan']."</td>
		<td align=center>".$_SESSION['lang']['jennisangsuran']."</td>
		<td align=center>".$_SESSION['lang']['jumlah']."</td>
		<td align=center>".$_SESSION['lang']['total']." ".$_SESSION['lang']['dibayar']."</td>
		<td align=center>".$_SESSION['lang']['bulanawal']."</td>
		<td align=center>".$_SESSION['lang']['tglcutisampai']."</td>
		<td align=center>".$_SESSION['lang']['jumlahbulan']."</td>
		<td align=center>".$_SESSION['lang']['jumlahbulan']." ".$_SESSION['lang']['dibayar']."</td>
		<td align=center>".$_SESSION['lang']['angsuranperbulan']."</td>			
		<td align=center>".$_SESSION['lang']['status']."</td>";


	   
$res1=mysql_query($str);
$no=0;
while($bar=mysql_fetch_object($res1))
{
	$no++;
//assign to string
$head.="<tr>
        <td class=firsttd>".$no."</td>
		<td>".$bar->karyawanid."</td>
		<td>".$bar->namakaryawan." (".$bar->lokasitugas.")</td>
		<td>".$arr[$bar->jenis]."</td>	
		<td>".number_format($bar->total,2,'.',',')."</td>		
		<td>".number_format($bar->totalbayar,2,'.',',')."</td>
		<td>".$bar->start."</td>
		<td>".$bar->end."</td>
		<td>".$bar->jlhbln."</td>
		<td>".$bar->jlhblnbayar."</td>
		<td>".number_format($bar->bulanan,2,'.',',')."</td>
		<td>".($bar->active==1?$_SESSION['lang']['aktif']:$_SESSION['lang']['tidakaktif'])."</td>";
	
$head.="</tr>";
//add terbilang below value row
/*
$terbilang='-';
$str3="select terbilang from ".$dbname1.".payrollterbilang
        where userid=".$bar1[0]." and `type`='".$tipe."'
		and periode='".$periode."' limit 1";	
$res3=mysql_query($str3);
while($bar3=mysql_fetch_object($res3))
{
	$terbilang=$bar3->terbilang;
}		
$head.="<tr><td bgcolor=#ffffff colspan='".(count($arrVal)+1)."'>".$terbilang."</td></tr>"; 
*/
$grandTotal+=$total; 		   
}	     
$head.="</tbody><tfoot>
        
		</tfoot></table>";
		
$stream=$head;		
$nop_="".$_SESSION['lang']['laporanangsuran'];
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
?>