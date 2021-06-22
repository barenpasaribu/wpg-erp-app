<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body_hrd();
?>
<script language=javascript1.2 src='js/sdm_persetujuan_pelatihan_eksternal.js'></script>
<script>
    tolak="<? echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('','<b>'.strtoupper($_SESSION['lang']['list']." ".$_SESSION['lang']['kursus']).'</b>');

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
//$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where alokasi=1 order by namakaryawan asc";
$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}
              
echo"
     <img onclick=detailExcel(event,'sdm_slave_persetujuan_pelatihan_eksternal.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     &nbsp;".$_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=getCariDt()>".$optKary."</select>&nbsp;
     
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
         <div style='width:100%;height:600px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
             <thead>
                    <tr>
                         <td align=center>".$_SESSION['lang']['nourut']."</td>
						<!--<td align=center>".$_SESSION['lang']['budgetyear']."</td>-->
						<td align=center>".$_SESSION['lang']['employeename']."</td>
						<!--<td align=center>".$_SESSION['lang']['kodetraining']."</td>-->
						<td align=center>".$_SESSION['lang']['namatraining']."</td>
						<td align=center>".$_SESSION['lang']['levelpeserta']."</td>
						<td align=center>".$_SESSION['lang']['penyelenggara']."</td>
						<td align=center>".$_SESSION['lang']['hargaperpeserta']."</td>
						<td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
						<td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
						<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
						<td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td>    
                        </tr>  
                 </thead>
                 <tbody id=container><script>loadData()</script>
                 </tbody>

           </table>
     </div>";
CLOSE_BOX_HRD();
close_body_hrd();
?>