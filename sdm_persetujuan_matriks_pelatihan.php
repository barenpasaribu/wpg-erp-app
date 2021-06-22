<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body_hrd();
?>
<script language=javascript1.2 src='js/sdm_persetujuan_matriks_pelatihan.js'></script>
<script>
    tolak="<? echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('','<b>'.strtoupper($_SESSION['lang']['list']." ".$_SESSION['lang']['matrikstraining']).'</b>');

$sGol="select distinct * from ".$dbname.".sdm_5golongan where 1";
$qGol=mysql_query($sGol) or die(mysql_error());
while($rGol=mysql_fetch_assoc($qGol))
{
    $kamusGol[$rGol['kodegolongan']]=$rGol['namagolongan'];
}

$sDep="select distinct * from ".$dbname.".sdm_5departemen where kode<>'---'";
$qDep=mysql_query($sDep) or die(mysql_error());
while($rDep=mysql_fetch_assoc($qDep))
{
    $kamusDep[$rDep['kode']]=$rDep['nama'];
}


$optTrn="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
//$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where alokasi=1 order by namakaryawan asc";
$sTrn="select kodegolongan,kodedepartemen,matrixid,topik from ".$dbname.".sdm_5matriktraining ";
$qTrn=mysql_query($sTrn) or die(mysql_error($sTrn));
while($rTrn=mysql_fetch_assoc($qTrn))
{
    $optTrn.="<option value='".$rTrn['matrixid']."'>".$rTrn['topik']." ( ".$kamusGol[$rTrn['kodegolongan']]." - ".$kamusDep[$rTrn['kodedepartemen']].")</option>";
}
              
echo"
     <img onclick=detailExcel(event,'sdm_slave_persetujuan_matriks_pelatihan.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     &nbsp;".$_SESSION['lang']['namatraining'].": <select id=TrnidCari style=width:150px onchange=getCariDt()>".$optTrn."</select>&nbsp;
     
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
         <div style='width:100%;height:600px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
             <thead>
                    <tr>
                        <td align=center>".$_SESSION['lang']['nourut']."</td>
						<td align=center>".$_SESSION['lang']['namatraining']."</td>
						<td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
						<td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
						<td align=center>".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['atasan']."</td>
						<td align=center>".$_SESSION['lang']['persetujuan']." ".$_SESSION['lang']['hrd']."</td>  
						<td align=center></td>
                        </tr>  
                 </thead>
                 <tbody id=container><script>loadData()</script>
                 </tbody>

           </table>
     </div>";
CLOSE_BOX_HRD();
close_body_hrd();
?>