<?



require_once('master_validation.php');

include('lib/nangkoelib.php');

include_once('lib/zLib.php');

echo open_body();

include('master_mainMenu.php');

OPEN_BOX();

?>

<script language="javascript" src="js/zMaster.js"></script>

<script>

    function getDetail(kdProj)

    {

        param='kdProj='+kdProj+'&proses=getDetail';

	tujuan='vhc_slave_2project.php';

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

                                    

                                    document.getElementById('detailData').style.display='block';

                                    document.getElementById('isiData').innerHTML=con.responseText;

                                    document.getElementById('awal').style.display='none';

                            }

                        }

                        else {

                                busy_off();

                                error_catch(con.status);

                        }

              }

	 }  	

    }

    function kembaliAja()

    {

        document.getElementById('detailData').style.display='none';

        document.getElementById('awal').style.display='block';

    }

    /* Function zPreview

 * Fungsi untuk preview sebuah report

 * I : target file, parameter yang akan dilempar, id container

 * O : report dalam bentuk HTML

 */

function zPreview(fileTarget,passParam,idCont) {

    var passP = passParam.split('##');

    var param = "";

    for(i=1;i<passP.length;i++) {

        var tmp = document.getElementById(passP[i]);

        if(i==1) {

            param += passP[i]+"="+getValue(passP[i]);

        } else {

            param += "&"+passP[i]+"="+getValue(passP[i]);

        }

    }

  // alert(param);

    function respon() {

        if (con.readyState == 4) {

            if (con.status == 200) {

                busy_off();

                if (!isSaveResponse(con.responseText)) {

                    alert('ERROR TRANSACTION,\n' + con.responseText);

                } else {

                    // Success Response

                    var res = document.getElementById(idCont);

                    res.innerHTML = con.responseText;

                    document.getElementById('detailData').style.display='none';

                    document.getElementById('awal').style.display='block';

                }

            } else {

                busy_off();

                error_catch(con.status);

            }

        }

    }

    //

  //  alert(fileTarget+'.php?proses=preview', param, respon);

    post_response_text(fileTarget+'.php?proses=preview', param, respon);



}

function zExcel(ev,tujuan,passParam)

{

	judul='Report Excel';

	//alert(param);

	var passP = passParam.split('##');

    var param = "";

    for(i=1;i<passP.length;i++) {

        var tmp = document.getElementById(passP[i]);

        if(i==1) {

            param += passP[i]+"="+getValue(passP[i]);

        } else {

            param += "&"+passP[i]+"="+getValue(passP[i]);

        }

    }

	param+='&proses=excel';

	//alert(param);

	printFile(param,tujuan,judul,ev)

}

function printFile(param,tujuan,title,ev)

{

   tujuan=tujuan+"?"+param;

   width='700';

   height='250';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"

   showDialog1(title,content,width,height,ev);

}

</script>

<?php



$optKelompok=makeOption($dbname, 'log_5klbarang', 'kode,kelompok');

$optNmOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');



$optOrg=$optPeriode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";



$sPeriodeCari="select distinct substr(periode,1,4) as tahun from ".$dbname.".setup_periodeakuntansi order by substr(periode,1,4) desc";

$qPeriodeCari=mysql_query($sPeriodeCari) or die(mysql_error());

while($rPeriodeCari=mysql_fetch_assoc($qPeriodeCari))

{

   $optPeriode.="<option value='".$rPeriodeCari['tahun']."'>".$rPeriodeCari['tahun']."</option>";

}

 

$sOrg="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where

      left(kodeorganisasi,3)='".$_SESSION['empl']['kodeorganisasi']."' and char_length(kodeorganisasi)='4' order by namaorganisasi asc";

$qOrg=mysql_query($sOrg) or die(mysql_error($conn));

while($rOrg=mysql_fetch_assoc($qOrg))

{

    $optOrg.="<option value='".$rOrg['kodeorganisasi']."'>".$rOrg['namaorganisasi']."</option>";

}

$arr="##kdOrg##thnId";

?>

<script language=javascript src=js/zTools.js></script>





<link rel=stylesheet type=text/css href=style/zTable.css>

<div>

<fieldset style="float: left;">

<legend><b><?php echo $_SESSION['lang']['lapproject']?></b></legend>

<table cellspacing="1" border="0" >

<tr><td><label><?php echo $_SESSION['lang']['kodeorg']?></label></td><td><select id="kdOrg" name="kdOrg" style="width:150px"><? echo $optOrg?></select></td></tr>

<tr><td><label><?php echo $_SESSION['lang']['tahun']?></label></td><td><select id="thnId" name="thnId" style="width:150px" ><? echo $optPeriode?></select></td></tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>

<tr><td colspan="2"><button onclick="zPreview('vhc_slave_2project','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button><button onclick="zExcel(event,'log_slave_2project.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>

</fieldset>

</div>





<fieldset style='clear:both'><legend><b>Print Area</b></legend>

<div id=awal>

    <div id='printContainer' style='overflow:auto;height:350px;max-width:1220px;'>



    </div>

</div>

<div id=detailData style=display:none>

<div id=isiData>

</div>

</div>

</fieldset>



<?php

CLOSE_BOX();

echo close_body();

?>