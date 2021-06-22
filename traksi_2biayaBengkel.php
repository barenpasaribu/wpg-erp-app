<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>

<?php
$lksiTugas=substr($_SESSION['empl']['lokasitugas'],0,4);

$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optPt=$optPeriode;
$optStat="<option value=''>".$_SESSION['lang']['all']."</option>";

//semua pt
$sPt="select distinct kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe='TRAKSI' 
and kodeorganisasi like '".$lksiTugas."%'";

$qPt=mysql_query($sPt) or die(mysql_error($conn));
while($rPt=mysql_fetch_assoc($qPt))
{
    $optPt.="<option value='".$rPt['kodeorganisasi']."'>".$rPt['namaorganisasi']."</option>";
}

//periode akuntansi
$sPeriode="select distinct substr(periode,1,4) as tahun from ".$dbname.".setup_periodeakuntansi order by periode desc";
$qPeriode=mysql_query($sPeriode) or die(mysql_error());
while($rPeriode=mysql_fetch_assoc($qPeriode))
{
	$optPeriode.="<option value=".$rPeriode['tahun'].">".$rPeriode['tahun']."</option>";
}


$arr="##thnId##unitId##bulanId";
//$arrKry="##kdeOrg##period##idKry##tgl_1##tgl_2";
?>

<script language=javascript src='js/zTools.js'></script>
<script language=javascript src='js/zReport.js'></script>
<script>
function Clear1()
{
    document.getElementById('thnId').options[0].selected=true;
    document.getElementById('unitId').options[0].selected=true;
    document.getElementById('bulanId').options[0].selected=true;
}

function resetBulan()
{
    document.getElementById('bulanId').innerHTML="";
    document.getElementById('unitId').value="";
}

function getBulan()
{
    thnId=document.getElementById('thnId').options[document.getElementById('thnId').selectedIndex].value;
    unitId=document.getElementById('unitId').options[document.getElementById('unitId').selectedIndex].value;
    
    param='proses=bulanapaaja&thnId='+thnId+'&unitId='+unitId;
    tujuan='traksi_slave_2biayaBengkel.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else 
                { 
                    document.getElementById('bulanId').innerHTML=con.responseText;
                }
            }
            else 
            {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
    
}
function displayDetail(bulan,noakun,unit,ev){
   param='noakun='+noakun+'&periode='+bulan+'&periode1='+bulan;
   param+='&lmperiode='+bulan+'&gudang='+unit+'&revisi=0';
   tujuan='keu_slave_getBBDetail.php'+"?"+param;  
   width='700';
   height='400';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail transaction'+noakun,content,width,height,ev); 
}
</script>
<link rel=stylesheet type='text/css' href='style/zTable.css'>

<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['biayabengkel']; ?></b></legend>
<table cellspacing="1" border="0" >

<tr>
	<td><label><?php echo $_SESSION['lang']['tahun']?></label></td>
	<td><select id="thnId" name="thnId" style="width:150px" onchange="resetBulan()">
		<?php echo $optPeriode?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['traksi']?></label></td>
	<td><select id="unitId" name="unitId" style="width:150px" onchange="getBulan()">
		<?php echo $optPt?>
	</select></td>
</tr>
<tr>
	<td><label><?php echo $_SESSION['lang']['bulan']?></label></td>
	<td><select id="bulanId" name="bulanId" style="width:150px">
	</select></td>
</tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
	<button onclick="zPreview('traksi_slave_2biayaBengkel','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
	<!--<button onclick="zPdf('traksi_slave_2biayaBengkel','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
	<button onclick="zExcel(event,'traksi_slave_2biayaBengkel.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
	<button onclick="Clear1()" class="mybutton" name="btnBatal" id="btnBatal"><?php echo $_SESSION['lang']['cancel']?></button>
</td></tr>
</table>
</fieldset>
</div>


<div style="margin-bottom: 30px;">
</div>
<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:325px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>