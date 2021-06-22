<?


require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/rTable.php');
echo open_body();
?>
<script language=javascript1.2 src='js/mr_biayaProduksiTbs.js'></script>
<script language=javascript src='js/zReport.js'></script>
<?

include('master_mainMenu.php');
OPEN_BOX('', '<b>' . strtoupper($_SESSION['lang']['managerialreport']) . ': ' . strtoupper($_SESSION['lang']['biaya'].' '.$_SESSION['lang']['produksitbs']) . '</b>');

$arr = "##pt##unit##afdeling##periode";
$arr1 = "##pt1##unit1##afdeling1##periode1";

//get existing period
$str = "select distinct periode from " . $dbname . ".setup_periodeakuntansi
    order by periode desc";
$res = mysql_query($str);
while ($bar = mysql_fetch_object($res)) {
    $optper.="<option value='" . $bar->periode . "'>" . $bar->periode . "</option>";
}

$str = "select kodeorganisasi,namaorganisasi from " . $dbname . ".organisasi
    where tipe='PT'
    order by namaorganisasi";
$res = mysql_query($str);
$optpt = "<option value=''>" . $_SESSION['lang']['all'] . "</option>";
while ($bar = mysql_fetch_object($res)) {
    $optpt.="<option value='" . $bar->kodeorganisasi . "'>" . $bar->namaorganisasi . "</option>";
}

$optunit = "<option value=''></option>";
$optafd = "<option value=''></option>";
 
$frm[0].="<fieldset><legend>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['produksitbs']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
    <tr><td>".$_SESSION['lang']['perusahaan']."</td><td>:</td><td>
    <select id=pt name=pt style=width:200px; onchange=getkebun()>".$optpt."</select>
    </td></tr>
    <tr><td>".$_SESSION['lang']['unit']."</td><td>:</td><td>
    <select id=unit name=unit style=width:150px; onchange=getafdeling()>".$optunit."</select>
    </td></tr>
    <tr><td>".$_SESSION['lang']['afdeling']."</td><td>:</td><td>
    <select id=afdeling name=afdeling style=width:150px; onchange=bersih()>".$optafd."</select>
    </td></tr>
    <tr><td>s/d ".$_SESSION['lang']['periode']."</td><td>:</td><td>
    <select id=periode name=periode style=width:150px; onchange=bersih()>".$optper."</select>
    </td></tr>
    <tr><td>Inti/Plasma</td><td>:</td><td>
    <select id=inti name=inti style=width:150px; onchange=bersih()>
        <option value=''>".$_SESSION['lang']['all']."</option>
        <option value='inti'>Inti</option>
        <option value='plasma'>Plasma</option>
    </select>
    </td></tr>
    <tr><td colspan=3>
        <button onclick=\"getpreview()\" class=\"mybutton\" name=\"preview\" id=\"preview\">" . $_SESSION['lang']['preview'] . "</button>
        <button onclick=\"getexcel(event,'mr_slave_biayaProduksiTbs.php')\" class=\"mybutton\" name=\"excel\" id=\"excel\">" . $_SESSION['lang']['excel'] . "</button>    
        <!--<button onclick=\"getpdf(event,'mr_slave_biayaTm.php')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">" . $_SESSION['lang']['pdf'] . "</button>-->
        <!--<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">" . $_SESSION['lang']['cancel'] . "</button>-->
    <input type=hidden name=hidden id=hidden value=hiddenvalue />
    </td></tr>
</table>"; 
$frm[0].="<div id='container'>
     </div></fieldset>"; 

$frm[1].="<fieldset><legend>".$_SESSION['lang']['biaya']." ".$_SESSION['lang']['produksitbs']." per PT</legend>";
$frm[1].="<table cellspacing=1 border=0>
    <!--<tr><td>" . $_SESSION['lang']['perusahaan'] . "</td><td>:</td><td>
    <select id=pt1 name=pt1 style=width:200px; onchange=getkebun1()>" . $optpt . "</select>
    </td></tr>
    <tr><td>" . $_SESSION['lang']['unit'] . "</td><td>:</td><td>
    <select id=unit1 name=unit1 style=width:150px; onchange=getafdeling1()>" . $optunit . "</select>
    </td></tr>
    <tr><td>" . $_SESSION['lang']['afdeling'] . "</td><td>:</td><td>
    <select id=afdeling1 name=afdeling1 style=width:150px; onchange=bersih1()>" . $optafd . "</select>
    </td></tr>-->
    <tr><td>s/d " . $_SESSION['lang']['periode'] . "</td><td>:</td><td>
    <select id=periode1 name=periode1 style=width:150px; onchange=bersih1()>" . $optper . "</select>
    </td></tr>
    <tr><td colspan=3>
        <button onclick=\"getpreview1()\" class=\"mybutton\" name=\"preview\" id=\"preview\">" . $_SESSION['lang']['preview'] . "</button>
        <button onclick=\"getexcel1(event,'mr_slave_biayaProduksiTbsPT.php')\" class=\"mybutton\" name=\"excel\" id=\"excel\">" . $_SESSION['lang']['excel'] . "</button>    
        <!--<button onclick=\"getpdf(event,'mr_slave_biayaTm.php')\" class=\"mybutton\" name=\"pdf\" id=\"pdf\">" . $_SESSION['lang']['pdf'] . "</button>-->
        <!--<button onclick=\"batal()\" class=\"mybutton\" name=\"btnBatal\" id=\"btnBatal\">" . $_SESSION['lang']['cancel'] . "</button>-->
    <input type=hidden name=hidden1 id=hidden1 value=hiddenvalue1 />
    </td></tr>
</table>"; 
$frm[1].="<div id='container1' style='overflow:auto;height:350px;max-width:1000px'>
     </div></fieldset>";

//========================
$hfrm[0] = $_SESSION['lang']['biaya']." ".$_SESSION['lang']['produksitbs'];
$hfrm[1] = $_SESSION['lang']['biaya']." ".$_SESSION['lang']['produksitbs']." per PT";
//$hfrm[1] = $_SESSION['lang']['biayatbm'] . " " . $_SESSION['lang']['detail'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM', $hfrm, $frm, 200, 900);
//===============================================
CLOSE_BOX();
close_body();
?>