<?



require_once('master_validation.php');

include('lib/nangkoelib.php');

include_once('lib/zLib.php');

echo open_body();

include('master_mainMenu.php');

OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['laporanPenggunaanKomponen']).'</b>'); //1 O

?>



<script type="text/javascript" src="js/vhc_laporanPenggunaanKomponen.js" /></script>

<div id="action_list">

<?php



$optOrg=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');

$sOrg="select distinct kodetraksi from ".$dbname.".vhc_5master where left(kodetraksi,3)='".$_SESSION['empl']['kodeorganisasi']."' order by kodetraksi asc";

$qOrg=fetchData($sOrg);

foreach($qOrg as $brsOrg)

{

    $optPt.="<option value=".$brsOrg['kodetraksi'].">".$optOrg[$brsOrg['kodetraksi']]."</option>";

}

$optJns="<option value=>".$_SESSION['lang']['all']."</option>";

$sJvhc="select distinct jenisvhc,namajenisvhc from ".$dbname.".vhc_5jenisvhc order by namajenisvhc asc";

$qJvhc=mysql_query($sJvhc) or die(mysql_error($sJvhc));

while($rJvhc=mysql_fetch_assoc($qJvhc))

{

    $optJns.="<option value=".$rJvhc['jenisvhc'].">".$rJvhc['namajenisvhc']."</option>";

}



$optper="<option value=''>".$_SESSION['lang']['all']."</option>";

$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_penggantianht order by tanggal desc";

$qTgl=mysql_query($sTgl) or die(mysql_error());

while($rTgl=mysql_fetch_assoc($qTgl))

{

   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";

}



echo"<table>

     <tr valign=moiddle>

		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 

			echo $_SESSION['lang']['unit'].":<select id=company_id name=company_id onChange=get_kdVhc() style=width:200px><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optPt."</select>&nbsp;"; 

                        echo $_SESSION['lang']['jenisvch'].":<select id=jnsVhc name=jnsVhc onChange=get_sortVhc()>".$optJns."</select>&nbsp;";

			echo $_SESSION['lang']['kodevhc'].":<select id=kdVhc name=kdVhc style=width:100px><option value=''>".$_SESSION['lang']['all']."</option></select>&nbsp;";

			echo $_SESSION['lang']['tgldari'].":<input type=\"text\" class=\"myinputtext\" id=\"tglAwal\" name=\"tglAwal\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />";

                        echo $_SESSION['lang']['tglsmp'].":<input type=\"text\" class=\"myinputtext\" id=\"tglAkhir\" name=\"tglAkhir\" onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false;\"  maxlength=\"10\" style=\"width:100px;\" />";

			echo"<button class=mybutton onclick=save_pil()>".$_SESSION['lang']['save']."</button>

			     <button class=mybutton onclick=ganti_pil()>".$_SESSION['lang']['ganti']."</button>";

echo"</fieldset></td>

     </tr>

	 </table> "; 

?>

</div>

<?php 

//CLOSE_BOX();

//OPEN_BOX();



?>

<div style='height:430px;overflow:scroll;'>

<div id="cari_barang" name="cari_barang">

<div id="hasil_cari" name="hasil_cari">

    <fieldset>

    <legend><?php echo $_SESSION['lang']['result']?></legend>

     <img onclick=dataKeExcel(event,'vhc_slave_laporanPenggunaanKomponen.php') src=images/excel.jpg class=resicon title='MS.Excel'> 

	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

<div id="contain">

</div>

    </fieldset>

    </div>

</div>  </div>

<?php

CLOSE_BOX();

?>

<?php

echo close_body();

?>