<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');

?>
<script language=javascript src='js/pmn_kontrakjual.js'></script>
<script language="javascript" src="js/zMaster.js"></script>
<?php
OPEN_BOX('',$_SESSION['lang']['kontrakjual']);

$sBrg="select namabarang,kodebarang from ".$dbname.".log_5masterbarang where `kelompokbarang`='400'";
$qBrg=mysql_query($sBrg) or die(mysql_error());
while($rBrg=mysql_fetch_assoc($qBrg))
{
        $optBrg.="<option value=".$rBrg['kodebarang'].">".$rBrg['namabarang']."</option>";
}

$sCust="select kodecustomer,namacustomer  from ".$dbname.".pmn_4customer order by namacustomer";
$qCust=mysql_query($sCust) or die(mysql_error($sCust));
while($rCust=mysql_fetch_assoc($qCust))
{
        $optCust.="<option value=".$rCust['kodecustomer'].">".$rCust['namacustomer']."</option>";
}	
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='PT'"; //echo $sOrg;
$qOrg=mysql_query($sOrg) or die(mysql_error());
while($rOrg=mysql_fetch_assoc($qOrg))
{
        $optPt.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}
$arrKurs=array("IDR","USD");
foreach($arrKurs as $dt)
{
        $optKurs.="<option value=".$dt.">".$dt."</option>";
}
//=========================
$frm[0].="
<div style='height:480px;overflow:scroll;'>
<input type=hidden id=method name=method value='insert' />     <fieldset>
          <legend>".$_SESSION['lang']['form']."</legend>
          <fieldset>
                <legend>".$_SESSION['lang']['header']."</legend>
                <table cellspacing=1 border=0>
                        <tr>
            <td>".$_SESSION['lang']['namaorganisasi']."</td>
            <td><select id=kdPt name=kdPt><option value=''></option>".$optPt."</select></td>
                        <td>
                 ".$_SESSION['lang']['NoKontrak']."</td><td>
                 <input type=text class=myinputtext id=noKtrk name=noKtrk maxlength=100 onkeypress=\"return tanpa_kutip(event)\" style=\"width:150px;\" /></td>
                                        <td>&nbsp;</td> <td>".$_SESSION['lang']['tglKontrak']."</td><td align=right><input type=text id=tlgKntrk size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this) style=\"width:150px;\" /></td>
                        </tr>
                </table>
          </fieldset>
          <br />
          <fieldset>
                <legend>".$_SESSION['lang']['custInformation']."</legend>
                <table>
                        <tr> 	 
                                <td>".$_SESSION['lang']['nmcust']."</td>
                                <td>
                                <select id=custId name=custId style=\"width:150px;\" onchange=\"getDataCust(0)\"><option value=></option>".$optCust."</select></td>
                                <td><span id=nmPerson></span> <span id=fax></span></td>
                        </tr>
                </table>
          </fieldset><br />
          <fieldset>
                        <legend>".$_SESSION['lang']['orderInfor']."</legend>

                        <table cellspacing=1 border=0>
                        <thead>
                        <tr>
                        <td colspan=7>".$_SESSION['lang']['goodsDesc']."</td>
                        </tr>
                        <tr class=rowheader>
                                <td>".$_SESSION['lang']['namabarang']."</td>
                                <td>".$_SESSION['lang']['satuan']."</td>
                                <td>".$_SESSION['lang']['hargasatuan']."</td>
                                <td>".$_SESSION['lang']['matauang']."</td>
                                <td>".$_SESSION['lang']['jmlhBrg']."</td>
                                <td>".$_SESSION['lang']['total']."</td>
                                <td>".$_SESSION['lang']['terbilang']."</td>
                        </tr>
                        </thead>
                        <tbody>
                                <td><select id=kdBrg name=kdBrg onchange=\"getSatuan(0,0,0)\" style=\"width:150px;\"><option value=></option>".$optBrg."</select></td>
                                <td><select id=stn name=stn style=\"width:50px;\"><option value=''></option></select></td>
                                <td><input type=text class=myinputtextnumber  name=HrgStn id=HrgStn onkeypress=\"return angka_doang(event);\"  onchange=hitungx() style=\"width:100px;\" /></td>
                                <td><select id=kurs name=kurs style=\"width:50px;\">".$optKurs."</select></td>
                                <td><input type=text class=myinputtextnumber name=jmlh id=jmlh onkeypress=\"return angka_doang(event);\"  onchange=hitungx() style=\"width:100px;\" /></td>
                                <td><input type=text disabled=\"disabled\" class=myinputtextnumber name=total id=total onkeypress=\"return angka_doang(event);\"  style=\"width:100px;\" /></td>
                                <td width:350><span id=tBlg></span></td>
                        </tbody>
                        </table><br />
                        <table cellspacing=1 border=0>
                        <thead>
                        <tr>
                        <td colspan=2>".$_SESSION['lang']['penyerahan']."</td>
                        </tr>
                        <tr class=rowheader>
                                <td>".$_SESSION['lang']['tgl_kirim']."</td>
                                <td>".$_SESSION['lang']['toleransi']."</td>
                        </tr>
                        </thead>
                        <tbody>
                                <td> <input type=text id=tglKrm size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)> s.d.<input type=text id=tglSd size=10 maxlength=10 class=myinputtext onkeypress=\"return false;\" onmouseover=setCalendar(this)></td>
                                <td><input type=text class=myinputtextnumber name=tlransi id=tlransi style=\"width:150px;\" onkeypress=\"return angka_doang(event);\" />%</td>

                        </tbody>
                        </table><br />
                        <table border=0 cellspacing=1>
                        <tr>
                        <td>
                        <table cellspacing=1 border=0>
                        <thead>
                        <tr>
                        <td colspan=3 style=\"width:200px;\">".$_SESSION['lang']['timbangan']."</td>
                        </tr></thead>
                        <tbody>
                        <tr>
                        <td>".$_SESSION['lang']['infoTmbngn']."</td><td>:</td><td><textarea style=\"height: 75px; width: 170px;\" name=tmbngn id=tmbngn ></textarea></td></tr>
                        <tr><td>".$_SESSION['lang']['kualitas']."</td><td>:</td><td><textarea style=\"height: 75px; width: 170px;\" name=kualitas id=kualitas ></textarea></td></tr>
                        <tr><td>".$_SESSION['lang']['nodo']."</td><td>:</td><td><input type=text id=noDo name=noDo class=myinputtext style=\"width: 170px;\" /></td></tr>
                        </tbody>
                        </table>
                        </td><td valign=top>
                        <table cellspacing=1 border=0>
                        <thead>
                        <tr>
                        <td colspan=3 style=\"width:200px;\">".$_SESSION['lang']['syaratPem']."</td>
                        </tr></thead>
                        <tbody>
                        <tr>
                        <td>".$_SESSION['lang']['payment']."</td><td>:</td><td><textarea style=\"height: 90px; width: 170px;\" name=syrtByr id=syrtByr ></textarea></td></tr>
                        <tr>
                        <td>".$_SESSION['lang']['tndaTangan']."</td><td>:</td><td><input type=text name=tndtng id=tndtng class=myinputtext style=\"width: 170px;\" /></td></tr>
                        </tbody>
                        </table>
                        </td>
                        </tr>
                        </table>
          </fieldset>
          <br />
        <fieldset>
        <legend>".$_SESSION['lang']['catatan']."</legend>
     <table>
            <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatan']." 1</td><td><input type=text id=cttn1 name=cttn1 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
          <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatan']." 2</td><td><input type=text id=cttn2 name=cttn2 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
           <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatan']." 3</td><td><input type=text id=cttn3 name=cttn3 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
           <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatan']." 4</td><td><input type=text id=cttn4 name=cttn4 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
           <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatan']." 5</td><td><input type=text id=cttn5 name=cttn5 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
           <tr> 	 
                 <td style='valign:top'>".$_SESSION['lang']['catatanlain']." </td><td><input type=text id=othCttn name=othCttn class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=255 style=\"width:300px;\" /></td>
          </tr>
     </table>
        </fieldset>
         <center>
           <button class=mybutton onclick=saveKP()>".$_SESSION['lang']['save']."</button>
           <button class=mybutton onclick=copyFromLast()>".$_SESSION['lang']['copy']."</button>
           <button class=mybutton onclick=clearFrom()>".$_SESSION['lang']['new']."</button>

         </center>
         </fieldset>";

$frm[1]="<div style='height:480px;overflow:scroll;'><fieldset>
           <legend>".$_SESSION['lang']['list']."</legend>
          <fieldset><legend></legend>
          ".$_SESSION['lang']['NoKontrak']."
          <input type=text id=txtnokntrk size=25 class=myinputtext onkeypress=\"return tanpa_kutip(event);\" >
          <button class=mybutton onclick=cariNoKntrk()>".$_SESSION['lang']['find']."</button>
          </fieldset>
          <table class=sortable cellspacing=1 border=0>
      <thead>
          <tr class=rowheader>
          <td>No.</td>
          <td>".$_SESSION['lang']['NoKontrak']."</td>
          <td>".$_SESSION['lang']['nm_perusahaan']."</td>
          <td>".$_SESSION['lang']['nmcust']."</td>
          <td>".$_SESSION['lang']['tglKontrak']."</td>
          <td>".$_SESSION['lang']['kodebarang']."</td>
          <td>".$_SESSION['lang']['produk']."</td>
          <td>".$_SESSION['lang']['tgl_kirim']."</td>
          <td>Action</td>
          </tr>
          </head>
           <tbody id=containerlist>
           <script>
           loadNewData();
           </script>
           </tbody>
           <tfoot>
           </tfoot>
           </table>
         </fieldset>";

$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['list'];

drawTab('FRM',$hfrm,$frm,100,900);
?>

<?
CLOSE_BOX();
echo close_body();
?>