<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
require_once('lib/eksilib.php');
echo open_body_hrd();
?>

<script language=javascript1.2 src='js/sdm_log_datakaryawan.js'></script>

<?php		

include('master_mainMenu.php');

$jmtab=0;
//Pilihan untuk log data karyawan dan bagian-bagian terkait data karyawan
$optdatakrys="<option value=''></option>";

$slcdt = $eksi->sSQL("select id,bahasa_legend from sdm_5tab_datakaryawan");

foreach($slcdt as $bardt){
	$jmtab++;
	$optdatakrys.="<option value=".$bardt['id'].">".$_SESSION['lang'][$bardt['bahasa_legend']]."</option>";
}

/*$optdatakrys.="<option value=1>".$_SESSION['lang']['inputdatakaryawan']."</option>";
$optdatakrys.="<option value=2>".$_SESSION['lang']['pengalamankerja']."</option>";
$optdatakrys.="<option value=3>".$_SESSION['lang']['pendidikan']."</option>";
$optdatakrys.="<option value=4>".$_SESSION['lang']['kursus']."</option>";
$optdatakrys.="<option value=5>".$_SESSION['lang']['keluarga']."</option>";
$optdatakrys.="<option value=6>".$_SESSION['lang']['address']."</option>";
$optdatakrys.="<option value=7>".$_SESSION['lang']['mutasipromosidemosi']."</option>";*/

//Pilihan untuk pengguna yang merubah data 
$optuser="<option value=''>".$_SESSION['lang']['all']."</option>";

$slcuser=$eksi->sSQL("select b.namakaryawan,b.karyawanid,a.namauser from user a left join datakaryawan b on a.karyawanid=b.karyawanid");

foreach($slcuser as $baruser){
	$optuser.="<option value=".$baruser['karyawanid'].">".$baruser['namakaryawan']."(".$baruser['namauser'].")</option>";
}

//Pilihan untuk tahun data di edit
$opttahun ="<option value=''>".$_SESSION['lang']['all']."</option>";
for($x=-3;$x<=0;$x++){
	$year=date('Y')+$x;
	$opttahun.="<option value=".$year.">".$year."</option>";
}

//Pilihan untuk bulan data di edit
$optbln ="<option value=''>".$_SESSION['lang']['all']."</option>";
for($x=0;$x<12;$x++){
	$month=date('m')+$x;
	$optbln.="<option value=".$month.">".$month."</option>";
}


OPEN_BOX_HRD('',$_SESSION['lang']['logdatakaryawan']);


echo"<table>
     <tr valign=middle>
         <td><fieldset><legend>".$_SESSION['lang']['searchdata']."</legend>"; 
                       
						
						echo $_SESSION['lang']['jenisdata']." :<select id=schdata  style='width:150px' onchange=showHeader(this.options[this.selectedIndex].value);>".$optdatakrys."</select> &nbsp ";
						
						echo $_SESSION['lang']['diperbaruioleh']." :<select id=schuser  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optuser."</select> &nbsp ";
						
						echo $_SESSION['lang']['tahunedit']." :<select id=schtahun  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$opttahun."</select> &nbsp ";
						
						echo $_SESSION['lang']['bulanedit']." :<select id=schbulan  style='width:150px' onchange=changeCaption(this.options[this.selectedIndex].text);>".$optbln."</select> &nbsp ";
						
						echo "<input id=alfield type=hidden value='".$_SESSION['lang']['tipedataisi']."'> ";
						echo "<input id=jmtab type=hidden value=".$jmtab."> ";

                        echo"<button class=mybutton onclick=cariLogDataKaryawan(1)>".$_SESSION['lang']['find']."</button>";

						
echo"</fieldset></td>
	</tr>
</table> "; 

        echo" 
		<table id=1 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           <tr class=rowheader  >
             <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
                 <!--<td align=center>".$_SESSION['lang']['nokaryawan']."</td>-->
                 <td align=center>".$_SESSION['lang']['nik']."</td>
                 <td align=center>".$_SESSION['lang']['nama']."</td>
                 <td align=center>".$_SESSION['lang']['changing']."</td>
				 <td align=center>".$_SESSION['lang']['adding']."</td>
                 <td align=center>".$_SESSION['lang']['deleting']."</td>
                 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
                 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
		    
         </thead>

         <tbody id=searchplaceresult1>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn1 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn1 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
         
		 <table id=2 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
			 <td align=center>".$_SESSION['lang']['changing']."</td>
			 <td align=center>".$_SESSION['lang']['adding']."</td>
			 <td align=center>".$_SESSION['lang']['deleting']."</td>
			 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
			 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult2>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn2 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn2 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 
		<table id=3 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
             <td align=center>".$_SESSION['lang']['changing']."</td>
				 <td align=center>".$_SESSION['lang']['adding']."</td>
                 <td align=center>".$_SESSION['lang']['deleting']."</td>
                 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
                 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult3>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn3 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn3 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 
		<table id=4 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
             <td align=center>".$_SESSION['lang']['changing']."</td>
				 <td align=center>".$_SESSION['lang']['adding']."</td>
                 <td align=center>".$_SESSION['lang']['deleting']."</td>
                 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
                 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult4>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn4 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn4 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 
		<table id=5 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
             <td align=center>".$_SESSION['lang']['changing']."</td>
				 <td align=center>".$_SESSION['lang']['adding']."</td>
                 <td align=center>".$_SESSION['lang']['deleting']."</td>
                 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
                 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult5>
         </tbody>
         <tfoot>
         </tfoot> 
                 <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn5 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn5 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 
		<table id=6 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
             <td align=center>".$_SESSION['lang']['changing']."</td>
			 <td align=center>".$_SESSION['lang']['adding']."</td>
			 <td align=center>".$_SESSION['lang']['deleting']."</td>
			 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
			 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult6>
         </tbody>
         <tfoot>
         </tfoot> 
        <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn6 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn6 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 	 
		<table id=7 class=sortable border=0 cellspacing=1 style='display:none;'>
         <thead>
           
		    <tr class=rowheader  >
			 <td align=center>".substr($_SESSION['lang']['nomor'],0,2)."</td>
             <td align=center>".$_SESSION['lang']['nik']."</td>
             <td align=center>".$_SESSION['lang']['namakaryawan']."</td>
             <td align=center>".$_SESSION['lang']['changing']."</td>
			 <td align=center>".$_SESSION['lang']['adding']."</td>
			 <td align=center>".$_SESSION['lang']['deleting']."</td>
			 <td align=center>".$_SESSION['lang']['diperbaruioleh']."</td>
			 <td align=center>".$_SESSION['lang']['waktupembaruan']."</td>
           </tr>
         </thead>

         <tbody id=searchplaceresult7>
         </tbody>
         <tfoot>
         </tfoot> 
         <tr align=center><td colspan=9 align=center>
         <button align=center class=mybutton value=0 onclick=prefDatakaryawan1(this,this.value) id=prefbtn7 >< ".$_SESSION['lang']['pref']." </button> 
         &nbsp 
         <button align=center class=mybutton value=2 onclick=nextDatakaryawan1(this,this.value) id=nextbtn7 > ".$_SESSION['lang']['lanjut']." ></button>
        </td></tr>
         </table> 
	 
	 ";

CLOSE_BOX_HRD();
close_body_hrd('');
?>