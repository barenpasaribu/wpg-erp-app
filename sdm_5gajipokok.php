<?
require_once('master_validation.php');
include('lib/nangkoelib_erp1.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body_hrd();
?>
<script language=javascript src='js/zMaster.js'></script>
<script language=javascript src='js/zTools.js'></script>
<script language=javascript1.2 src='js/sdm_5gajipokok.js'></script>
<?
$optTipe="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTp="select id,name from ".$dbname.".sdm_ho_component where type='basic' and `lock`=1 order by name";
$qTp=mysql_query($sTp) or die(mysql_error($conn));
while($rTp=mysql_fetch_assoc($qTp)){
    $optTipe.="<option value='".$rTp['id']."'>".$rTp['name']."</option>";
}
$karyPdf="karyawanid in (";
$optTipe2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTp2="select distinct nik,namakaryawan,karyawanid from ".$dbname.".datakaryawan where 
       lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan asc";
$qTp2=mysql_query($sTp2) or die(mysql_error($conn));
while($rTp=mysql_fetch_assoc($qTp2)){
    $ader+=1;
    $optTipe2.="<option value='".$rTp['karyawanid']."'>".$rTp['nik']."-".$rTp['namakaryawan']."</option>";
    if($ader==1){
        $karyPdf.=$rTp['karyawanid'];
    }else{
         $karyPdf.=",".$rTp['karyawanid'];
    }
}
$karyPdf.=") and tahun=".date('Y')."";
$optTipe3="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sTp2="select distinct id,tipe from ".$dbname.".sdm_5tipekaryawan where 
       id!=0 order by tipe asc";
$qTp2=mysql_query($sTp2) or die(mysql_error($conn));
while($rTp=mysql_fetch_assoc($qTp2)){
    $optTipe3.="<option value='".$rTp['id']."'>".$rTp['tipe']."</option>";
}
$arrd=array("0"=>"Per Orang/Per Person","1"=>$_SESSION['lang']['all']);
foreach($arrd as $rwdd=>$lstarr){
     
     $optTipe5.="<option value='".$rwdd."'>".$lstarr."</option>";
}
$arr="##thn##pilInp##karyawanId##idKomponen##jmlhDt##method##tpKary";
include('master_mainMenu.php');
OPEN_BOX_HRD();

echo"<fieldset style='width:380px;float:left;'>
     <legend><b>".$_SESSION['lang']['gajipokok']."</b></legend>
	 <table>
	 <tr>
	   <td>".$_SESSION['lang']['tahun']."</td>
	   <!--<td><input type=text class=myinputtextnumber id=thn name=thn  onkeypress=\"return angka_doang(event);\" style=\"width:50px;\" maxlength='4' value='".date('Y')."'></td>-->
	   <td><input type=text class=myinputtextnumber id=thn name=thn  onkeypress=\"return angka_doang(event);\" style=\"width:50px;\" maxlength='4'></td>
	 </tr>
           <tr>
	   <td>".$_SESSION['lang']['tipekaryawan']." </td>
	    <td><select id=tpKary style=width:150px;>".$optTipe3."</select></td>
	 </tr>	
         <tr>
	   <td>".$_SESSION['lang']['pilih']." </td>
	    <td><select id=pilInp style=width:150px;>".$optTipe5."</select></td>
	 </tr>	
	 <tr>
	   <td>".$_SESSION['lang']['namakaryawan']." </td>
	   <td><select id=karyawanId style=width:150px;>".$optTipe2."</select></td>
	 </tr>	 
          <tr>
	   <td>".$_SESSION['lang']['idkomponen']." </td>
	    <td><select id=idKomponen style=width:150px;>".$optTipe."</select></td>
	 </tr>	
         <tr>
	   <td>".$_SESSION['lang']['jumlah']."</td>
	   <td><input type=text class=myinputtextnumber id=jmlhDt name=jmlhDt  onkeypress=\"return angka_doang(event);\" style=\"width:150px;\" maxlength='8' /></td>
	 </tr>	
	 </table>
	 <input type=hidden value=insert id=method>
	 <button class=mybutton onclick=saveFranco('sdm_slave_5gajipokok','".$arr."')>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelIsi()>".$_SESSION['lang']['cancel']."</button>
     </fieldset>";

for($x=-2;$x<=8;$x++)
{
    $opttahun1.="<option value='".(date('Y')+$x)."'>".(date('Y')+$x)."</option>";
}
echo"<fieldset style='width:350px;'><legend>Copy</legend>".
    $_SESSION['lang']['dari']." ".$_SESSION['lang']['tahun'].":<select id=tahun1>".$opttahun1."</select>
    ke Tahun:<select id=tahun2>".$opttahun1."</select>
    <button onclick=copyTahun() class=mybutton>".$_SESSION['lang']['proses']."</button>    
    <hr>
    ID:Copy gaji pokok dari konfigurasi gaji tahun tertentu ke tahun tertentu<br>
    ID:Copy basic salary from previous year to this year<br>
    </fieldset>";
//CLOSE_BOX_HRD();
//OPEN_BOX_HRD();
for($x=2;$x>=-10;$x--)
{
    if((date('Y')+$x)==date('Y'))
    $opttahun.="<option value='".(date('Y')+$x)."' selected>".(date('Y')+$x)."</option>";
     else    
    $opttahun.="<option value='".(date('Y')+$x)."'>".(date('Y')+$x)."</option>";
}
echo"<fieldset  style=width:750px;><legend>".$_SESSION['lang']['find']." ".$_SESSION['lang']['data']."</legend>
    <div style=float:left;><img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."' onclick=displatList() ></div>
     <table border=0>
     <tr>
     <td>".$_SESSION['lang']['tahun']."</td><td><select id=opttahun onchange=loadGaji(this.options[this.selectedIndex].value)>".$opttahun."</select></td>
     <td>".$_SESSION['lang']['namakaryawan']."</td><td><input type=text class=myinputtext  id=nmKar name=nmKar  onkeypress=\"return tanpa_kutip(event);\" style=\"width:150px;\" /></td>
     </tr>
     <tr>
     <td>".$_SESSION['lang']['tipekaryawan']."</td><td><select id=tpKaryCr style=width:150px;>".$optTipe3."</select></td>
     <td>".$_SESSION['lang']['idkomponen']."</td><td><select id=idKomponenCr style=width:150px;>".$optTipe."</select></td>
     </tr>
     <tr>
     <td colspan4><button onclick=loadData() class=mybutton>".$_SESSION['lang']['find']."</button>  </td>
     </tr>
     </table></fieldset>";
echo"<fieldset  style=width:750px;><legend>".$_SESSION['lang']['list']."</legend>
     <img onclick=\"masterPDF('sdm_5gajipokok','','".$karyPdf."','sdm_slave_5gajipokok_pdf',event)\"  class=\"resicon\" style=\"cursor:pointer\" title=\"PDF Format\" src=\"images/pdf.jpg\">
     <img onclick=\"dataKeExcel(event)\" src=\"images/excel.jpg\" class=\"resicon\" title=\"MS.Excel\">
     
     <table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
           <td>".$_SESSION['lang']['tahun']."</td>
           <td>".$_SESSION['lang']['namakaryawan']."</td>
	   <td>".$_SESSION['lang']['tipekaryawan']."</td>
           <td>".$_SESSION['lang']['idkomponen']."</td>
           <td>".$_SESSION['lang']['jumlah']."</td>
           <td>".$_SESSION['lang']['action']."</td>    
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";

echo"</tbody>
     <tfoot>
     
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX_HRD();
echo close_body_hrd();
?>