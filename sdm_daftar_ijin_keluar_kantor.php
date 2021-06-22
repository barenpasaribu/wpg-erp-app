<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body_hrd();
?>
<script language=javascript1.2 src='js/sdm_daftar_ijin_keluar_kantor.js'></script>
<script>
    tolak="<? echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?
include('master_mainMenu.php');
OPEN_BOX_HRD('','<b>'.strtoupper($_SESSION['lang']['daftarizincutidayoff']).'</b>');

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
//$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where alokasi=1 order by namakaryawan asc";

//tambah filter pilihan nama karyawan sesuai login ==Jo 02-05-2017==
if($_SESSION['empl']['pusat']==1){
	$whorg="";
}
else{
	$whorg="and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
}

//tambah untuk pencarian by lokasi tugas ==Jo 22-09-2017==
if ($_SESSION['empl']['pusat']==1){
	$optlktgs.="<option value=''>".$_SESSION['lang']['all']."</option>";
	$wherekdlk="";
}
else {
	$wherekdlk="and kodeorganisasi='".$_SESSION['empl']['lokasitugas']."'";
}

$slhrd="select hrd from sdm_ijin where hrd='".$_SESSION['standard']['userid']."'";
$jmhrd=$eksi->sSQLnum($slhrd);

$sladmin="select pengajuid from sdm_ijin where pengajuid='".$_SESSION['standard']['userid']."'";
$jmadmin=$eksi->sSQLnum($sladmin);

if($jmhrd>0){
	$sllktgs="select kodeorganisasi,namaorganisasi from organisasi where length(kodeorganisasi)=".$_SESSION['lang']['panjangorgnpwp']." ".$wherekdlk."";
}
else {
	$sllktgs="select kodeorganisasi,namaorganisasi from organisasi where length(kodeorganisasi)=".$_SESSION['lang']['panjangorgnpwp']." and induk='".$_SESSION['empl']['kodeorganisasi']."' ".$wherekdlk."";
}

$reslktgs=$eksi->sSQL($sllktgs);

foreach($reslktgs as $barlktgs){
	$optlktgs.="<option value=".$barlktgs['kodeorganisasi'].">".$barlktgs['namaorganisasi']."</option>";
}

$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where 1=1 ".$whorg." order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}
                //$arragama=getEnum($dbname,'sdm_ijin','jenisijin');
				$arragama = "select id, jenisizincuti from ".$dbname.".sdm_jenis_ijin_cuti where status = '1' order by id asc";
				$qarragama=mysql_query($arragama) or die(mysql_error());
				
				while($rarragama=mysql_fetch_assoc($qarragama))
				{
					$optJenis.="<option value=".$rarragama['id'].">".$rarragama['jenisizincuti']."</option>";
				}
                /*foreach($rarragama as $kei=>$fal)
                {
					$optJenis.="<option value='".$kei."'>".$fal."</option>";
                    if($_SESSION['language']=='ID'){
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";
                    }else{
                        switch($fal){
                            case '1':
                                $fal='Late for work';
                                break;
                            case '2':
                                $fal='Out of Office';
                                break;         
                            case '3':
                                $fal='Home early';
                                break;     
                            case '4':
                                $fal='Other purposes';
                                break;   
                            case '5':
                                $fal='Leave';
                                break;       
                            case '6':
                                $fal='Maternity';
                                break;           
                            default:
                                $fal='Wedding, Circumcision or Graduation';
                                break;                              
                        }
                        $optJenis.="<option value='".$kei."'>".$fal."</option>";       
                    }
					$optJenis.="<option value='".$kei."'>".$fal."</option>"; 
                } */
echo"
     <img onclick=detailExcel(event,'sdm_slave_daftar_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     &nbsp;".$_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=getCariDt()>".$optKary."</select>&nbsp;
     ".$_SESSION['lang']['jeniscuti'].": <select id=jnsCuti style=width:150px onchange=getCariDt()>".$optJenis."</select>&nbsp;
     ".$_SESSION['lang']['lokasitugas'].": <select id=lokasitugas style=width:150px onchange=getCariDt()>".$optlktgs."</select>&nbsp;
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
         <div style='width:100%;height:600px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
             <thead>
                    <tr>
                          <td align=center>No.</td>
                          <td align=center>".$_SESSION['lang']['tanggal']."</td>
                          <td align=center>".$_SESSION['lang']['nama']."</td>
                          <td align=center>".$_SESSION['lang']['keperluan']."</td>
                          <td align=center>".$_SESSION['lang']['jenisijin']."</td>  
                          
                          <td align=center>".$_SESSION['lang']['dari']."  ".$_SESSION['lang']['tanggal']."/".$_SESSION['lang']['jam']."</td>
                          <td align=center>".$_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['tanggal']."/".$_SESSION['lang']['jam']."</td>
                          <td align=center>".$_SESSION['lang']['jumlahhk']." ".$_SESSION['lang']['diambil']."</td>
                          <td align=center>".$_SESSION['lang']['cuti']." ".$_SESSION['lang']['sisa']."</td>
                          <td align=center>".$_SESSION['lang']['atasan']."</td>
                          <td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['atasan']."</td>
						  <td align=center>".$_SESSION['lang']['hrd']."</td>
                          <td align=center>".$_SESSION['lang']['status']." ".$_SESSION['lang']['hrd']."</td> 
						  <td align=center>".$_SESSION['lang']['keterangan']."</td> 
                          <td align=center>".$_SESSION['lang']['print']."</td>   
                        </tr>  
                 </thead>
                 <tbody id=container><script>loadData()</script>
                 </tbody>

           </table>
     </div>";
CLOSE_BOX_HRD();
close_body_hrd();
?>