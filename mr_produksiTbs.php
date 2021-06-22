<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
$optKlmpk="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT' order by namaorganisasi asc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optKlmpk.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$optKlmpk2="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg2="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' order by namaorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optKlmpk2.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}
$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriode="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error($conn));
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
    $optPeriode.="<option value='".$rPeriode['periode']."'>".$rPeriode['periode']."</option>";
}
$arr="##kdPt##kdUnit##periodeDt";

?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReportUpd.js'></script>

<script>
function getUnit()
{
	pt=document.getElementById('kdPt').options[document.getElementById('kdPt').selectedIndex].value;
        prd=document.getElementById('periodeDt').options[document.getElementById('periodeDt').selectedIndex].value;
	param='kdPt='+pt+'&proses=getData'+'&periodeDt='+prd;
	tujuan="mr_slaveproduksiTbs.php";
	 
         post_response_text(tujuan, param, respog);
	 function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    
                  	document.getElementById('kdUnit').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}


</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['prodTbs']?></b></legend>
<table cellspacing="1" border="0" >
    <tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periodeDt" style="width:150px"><?php echo $optPeriode;?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['pt']?></label></td><td><select id="kdPt" name="kdPt" style="width:150px" onchange="getUnit()"><?php echo $optKlmpk?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px"><? echo $optKlmpk2?></select></td></tr>



<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('mr_slaveproduksiTbs','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('mr_slaveproduksiTbs','<?php echo $arr?>','printContainer2')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'mr_slaveproduksiTbs.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>
<?php //CLOSE_BOX(); 

//OPEN_BOX('','Result:');?>

      <div id="excPrev">
<div style='width:1180px;display:fixed;'>
<table cellpadding=1 cellspacing=1 border=0 class=sortable width=1160px>
<thead><tr class=rowheader>
<td rowspan=4 align=center   style='width:40px;'><? echo $_SESSION['lang']['tahuntanam'] ?></td>
<td rowspan=4 align=center  style='width:40px;'><? echo $_SESSION['lang']['pt'] ?></td>
<td rowspan=4 align=center  style='width:40px;'><? echo $_SESSION['lang']['afdeling'] ?></td>
<td colspan=2  rowspan=2  align=center  style='width:100px;'>LUAS TM (Ha)</td>
<td colspan=5   align=center   style='width:270px;'>TOTAL PRODUKSI (KG)</td>
<td colspan=3   align=center   style='width:150px;'>KG/HA</td></tr>
<tr class=rowheader>
<td colspan=2  align=center style='width:50px;'>BULAN INI</td>
<td colspan=2  align=center style='width:50px;'>S.D. BULAN INI</td>
<td rowspan=3  align=center  style='width:70px;'>ANGGARAN TAHUNAN</td> 
<td align=center  style='width:40px;'>BI</td>
<td align=center  style='width:40px;'>SBI</td>
<td rowspan=3  align=center  style='width:70px;'>ANGGARAN TAHUNAN KG/HA</td></tr>
<tr class=rowheader>
<td align=center rowspan=2 style='width:50px;'>Realisasi</td>
<td align=center rowspan=2 style='width:50px;'>Anggaran</td>
<td align=center rowspan=2 style='width:50px;'>Realisasi</td>
<td align=center rowspan=2 style='width:50px;'>Anggaran</td>
<td align=center rowspan=2 style='width:50px;'>Realisasi</td>
<td align=center rowspan=2 style='width:50px;'>Anggaran</td>
<td align=center rowspan=2 style='width:40px;'>KG/HA</td>
<td align=center rowspan=2 style='width:40px;'>KG/HA</td></tr></thead><tbody>
</tbody></table></div>
        <div style='width:1180px;height:680px;overflow:scroll;'>
            <table cellpadding=1 cellspacing=1 border=0 class=sortable width=1160px>
        <thead><tr>  </tr>  </thead><tbody id="printContainer"> 
        </tbody>
            </table>
        </div>
</div>
      <div id="pdfData" style="display: none;">
        <div id='printContainer2' style='overflow:auto;height:350px;max-width:1220px'>

</div>
</div>

     
<?php
CLOSE_BOX();
echo close_body();
?>