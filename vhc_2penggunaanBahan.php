<?



require_once('master_validation.php');

include('lib/nangkoelib.php');

include_once('lib/zLib.php');

echo open_body();

include('master_mainMenu.php');

OPEN_BOX();

?>

<?php

$optBatch="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')

{

    $sBatch="select distinct periode from ".$dbname.".setup_periodeakuntansi order by periode desc";

    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and tipe='TRAKSI' order by namaorganisasi asc";

}

else

{

    $sBatch="select distinct periode from ".$dbname.".setup_periodeakuntansi where kodeorg like '%".$_SESSION['empl']['lokasitugas']."%' order by periode desc";

    $sKodeorg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='TRAKSI' and kodeorganisasi like '%".$_SESSION['empl']['lokasitugas']."%' order by namaorganisasi asc";

}

$qBatch=mysql_query($sBatch) or die(mysql_error());

while($rBatch=mysql_fetch_assoc($qBatch))

{

    if(substr($rBatch['periode'],4,2)=='12')

    {

     $optBatch.="<option value='".substr($rBatch['periode'],0,4)."'>".substr($rBatch['periode'],0,4)."</option>";   

    }

    $optBatch.="<option value='".$rBatch['periode']."'>".$rBatch['periode']."</option>";

}



$optKodeorg="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";

$qKodeOrg=mysql_query($sKodeorg) or die(mysql_error());

while($rKodeorg=mysql_fetch_assoc($qKodeOrg))

{

    $optKodeorg.="<option value='".$rKodeorg['kodeorganisasi']."'>".$rKodeorg['namaorganisasi']."</option>";

}

$arr="##kdUnit##periode";

?>

<script language=javascript src=js/zTools.js></script>

<script language=javascript src='js/zReport.js'></script>

<script language="javascript">

function previewDetail(kdvhc,periode,status,kdbrg,kdunit,ev)

{

        showDetail(kdvhc,periode,status,ev);



        param='kodevhc='+kdvhc+'&periode='+periode+'&status='+status+'&proses=getDetail';

        param+='&kodebarang='+kdbrg+'&kdUnit='+kdunit;

        //alert(param);

        tujuan='vhc_2slave_penggunaanBahan.php';

        post_response_text(tujuan, param, respog);

        function respog()

        {

                      if(con.readyState==4)

                      {

                                if (con.status == 200) {

                                                busy_off();

                                                if (!isSaveResponse(con.responseText)) {

                                                        alert('ERROR TRANSACTION,\n' + con.responseText);

                                                }

                                                else {

                                                        //alert(con.responseText);

                                                        document.getElementById('contDetail').innerHTML=con.responseText;

                                                }

                                        }

                                        else {

                                                busy_off();

                                                error_catch(con.status);

                                        }

                      }	

         }  



}

function showDetail(kdvhc,periode,status,ev)

{

	title="Detail "+kdvhc;

	content="<fieldset><legend>"+kdvhc+"</legend><div id=contDetail ></div></fieldset>";

	width='800';

	height='600';

	showDialog1(title,content,width,height,ev);	

}

</script>

<link rel=stylesheet type=text/css href=style/zTable.css>

<div style="margin-bottom: 30px;">

<fieldset style="float: left;">

<legend><b><?php echo $_SESSION['lang']['laporanByBahan']?></b></legend>

<table cellspacing="1" border="0" >

<tr><td><label><?php echo $_SESSION['lang']['kodetraksi']?></label></td><td><select id="kdUnit" name="kdUnit" style="width:150px">

<?php echo $optKodeorg?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode" name="periode" style="width:150px">

<?php echo $optBatch?></select></td></tr>

<tr><td colspan="2"><button onclick="zPreview('vhc_2slave_penggunaanBahan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zPdf('vhc_2slave_penggunaanBahan','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'vhc_2slave_penggunaanBahan.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>

</fieldset>

</div>

<fieldset style='clear:both'><legend><b>Print Area</b></legend>

<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>



</div></fieldset>

<?php

CLOSE_BOX();

echo close_body();

?>