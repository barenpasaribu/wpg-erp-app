<?
require_once('master_validation.php');
//include('lib/nangkoelib.php');
include('lib/eagrolib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>

<script language='javascript' src='js/vhc.js'></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['jenkendabmes']."</b>");
//get enum untuk kelompok vhc;
		/*
        $optklvhc="";
        $arrklvhc=getEnum($dbname,'vhc_5master','kelompokvhc');
        foreach($arrklvhc as $kei=>$fal)
        {
                switch($kei)
                {
                             case 'AB':
                                  $_SESSION['language']!='EN'?$fal='Alat Berat':$fal='Heavy Equipment';
                             break;
                             case 'KD':                            
                                 $_SESSION['language']!='EN'?$fal='Kendaraan':$fal='Vehicle';
                             break;
                             case 'MS':
                                 $_SESSION['language']!='EN'? $fal='Mesin':$fal='Machinery';
                             break;
                }
                $optklvhc.="<option value='".$kei."'>".$fal."</option>";
        } 
		*/
	$optklvhc="<option value=''></option>";
	
	$flag='ikVHC';
	if ($_SESSION['language']=='EN'){
		$flag='EkVHC';		
	}
	$rs=mysql_query("SELECT kode,nama FROM ".$dbname.".setup_5parameter 
					 WHERE flag='".$flag."'");
	while($rst=mysql_fetch_array($rs)){
		$optklvhc.="<option value='".$rst['kode']."'>".$rst['nama']."</option>";
	}
		
echo"<fieldset style='width:500px;'><table>
<tr><td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=kelompokvhc>".$optklvhc."</select></td></tr>     
         <tr><td>".$_SESSION['lang']['tipe']."</td><td><input type=text id=jenisvhc size=5 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=5></td></tr>
         <tr><td>".$_SESSION['lang']['namajenisvhc']."</td><td><input type=text id=namajenisvhc size=40 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=45></td></tr>
         <tr><td>".$_SESSION['lang']['akunservice']."</td><td><input type=text id=noakun size=16 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=16></td></tr>
     </table>
         <input type=hidden id=method value='insert'>
         <button class=mybutton onclick=simpanVhc()>".$_SESSION['lang']['save']."</button>
         <button class=mybutton onclick=cancelVhc()>".$_SESSION['lang']['cancel']."</button>
         </fieldset>";
//echo open_theme($_SESSION['lang']['availvhc']);
echo "<div style='height:360px;overflow:scroll;'><div>";
        $str1="select * from ".$dbname.".vhc_5jenisvhc order by jenisvhc";
        $res1=mysql_query($str1);
        echo"<table class=sortable cellspacing=1 border=0 style='width:500px;'>
             <thead>
                 <tr class=rowheader>
                   <td style='width:150px;'>".$_SESSION['lang']['tipe']."</td>
                   <td>".$_SESSION['lang']['kodekelompok']."</td>
                   <td>".$_SESSION['lang']['namajenisvhc']."</td>
                   <td>".$_SESSION['lang']['akunservice']."</td>		   
                   <td style='width:30px;'>".$_SESSION['lang']['action']."</td></tr>
                 </thead>
                 <tbody id=container>";
        while($bar1=mysql_fetch_object($res1))
        {
                echo"<tr class=rowcontent><td align=center>".$bar1->jenisvhc."</td>
                     <td>".$bar1->kelompokvhc."</td>
                         <td>".$bar1->namajenisvhc."</td>
                         <td>".$bar1->noakun."</td>
                         <td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->jenisvhc."','".$bar1->namajenisvhc."','".$bar1->noakun."','".$bar1->kelompokvhc."');\"></td></tr>";
        }	 
        echo"	 
                 </tbody>
                 <tfoot>
                 </tfoot>
                 </table>";
echo "</div></div>";
//echo close_theme();
CLOSE_BOX();
echo close_body();
?>
