<?
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$method=$_POST['method'];

if($_GET['method']=='excel')
{
    $stream="<table><tr><td colspan=17 align=center><b>Master Vehicle</b></td></tr></table>";
    $stream.="<table cellspacing=1 border=1 width=100%>";
    $bg="bgcolor=#DEDEDE";
}else{
    $stream.="<table class=sortable cellspacing=1 border=0 width=100%>";
}
        
$stream.="<thead>
             <tr class=rowheader>
              <td>No</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodeorganisasi'])."</td>		 
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodekelompok'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jenkendabmes'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodenopol'])."</td>		
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['namabarang'])."</td>		
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tahunperolehan'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['beratkosong'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomorrangka'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomormesin'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['detail'])."</td>	   
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kepemilikan'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodetraksi'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tglakhirstnk'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tglakhirkir'])."</td>	   
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tglakhirijinbongkar'])."</td>
               <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tglakhirijinangkut'])."</td>                   
              </tr>
             </thead>
             <tbody id=container>";

$str1="select * from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSION['empl']['lokasitugas']."%' order by kodeorg,kodevhc";
$res1=mysql_query($str1);

$no=0;	 
while($bar1=mysql_fetch_object($res1))
{
    $no+=1;
    $str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";
    $res=mysql_query($str);
    $namabarang='';
    while($bar=mysql_fetch_object($res))
    {
            $namabarang=$bar->namabarang;
    }
    if($bar1->kepemilikan==1)
    {
  $dptk=$_SESSION['lang']['miliksendiri'];	
    }
    else
    {
            $dptk=$_SESSION['lang']['sewa'];
    }		
    $stream.="<tr class=rowcontent>
             <td align=center>".$no."</td>
             <td>".$bar1->kodeorg."</td>
             <td>".$bar1->kelompokvhc."</td>				 
             <td>".$bar1->jenisvhc."</td>			 		
             <td>".$bar1->kodevhc."</td>
             <td>".$namabarang."</td>
             <td>".$bar1->tahunperolehan."</td>
             <td>".$bar1->beratkosong."</td>
             <td>".$bar1->nomorrangka."</td> 
             <td>".$bar1->nomormesin."</td> 
             <td>".$bar1->detailvhc."</td> 	
             <td>".$dptk."</td>
             <td>".$bar1->kodetraksi."</td>
             <td>".$bar1->tglakhirstnk."</td>
             <td>".$bar1->tglakhirkir."</td>
             <td>".$bar1->tglakhirijinbm."</td> 
             <td>".$bar1->tglakhirijinang."</td>
             </tr>";
}	 
$stream.="	 
         </tbody>
         <tfoot>
         </tfoot>
         </table>";
if($_GET['method']=='excel')
{
    $stream.="<br>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	
    $nop_="mastervhc_".date('YmdHis');
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
?>
