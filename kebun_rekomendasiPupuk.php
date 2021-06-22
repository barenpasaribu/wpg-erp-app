<?php
require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.$_SESSION['lang']['rekomendasiPupuk'].'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">
<script language=\"javascript\" src=\"js/zMaster.js\"></script>
<script>jdlExcel='";
echo $_SESSION['lang']['rekomendasiPupuk'];
echo "';tmblDone='";
echo $_SESSION['lang']['done'];
echo "';tmblCancelDetail='";
echo $_SESSION['lang']['cancel'];
echo "';</script>
<script type=\"application/javascript\" src=\"js/kebun_rekomendasiPupuk.js\"></script>
<input type=\"hidden\" id=\"proses\" name=\"proses\" value=\"insert\"/>
<div id=\"action_list\">";
$lksi = substr($_SESSION['empl']['lokasitugas'], 0, 4);
$sKbn = 'select namaorganisasi,kodeorganisasi from '.$dbname.".organisasi where tipe='AFDELING' and induk='".$lksi."'";
$qKbn = mysql_query($sKbn) ;
while ($rKbn = mysql_fetch_assoc($qKbn)) {
    $optKbn .= '<option value='.$rKbn['kodeorganisasi'].'>'.$rKbn['namaorganisasi'].'</option>';
}
for ($x = 0; $x <= 24; ++$x) {
    $dt = mktime(0, 0, 0, date('m') + $x, 15, date('Y'));
    $optPeriode .= '<option value='.date('Y-m', $dt).'>'.date('Y-m', $dt).'</option>';
}
echo "<table>
<tr valign=middle>
<td align=center style='width:100px;cursor:pointer;' onclick=add_new_data()><img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
<td align=center style='width:100px;cursor:pointer;' onclick=displayList()><img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
<td>
<fieldset><legend>".$_SESSION['lang']['find'].'</legend>';
echo $_SESSION['lang']['kebun'].":<select id=crKbn name=crKbn><option value=''></option>".$optKbn.'</select>&nbsp;';
echo $_SESSION['lang']['tahunpupuk'].":<select id=crPeriode nama=crPeriode style='width:150px;'><option value=''></option>".$optPeriode.'</select>';
echo '<button class=mybutton onclick=cariData()>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset>
</td>
</tr>
</table>
</div>";
CLOSE_BOX();
echo "<div id=\"list_ganti\">
<script>loadData();</script>
</div>
<div id=\"headher\" style=\"display:none\">";
OPEN_BOX();
$thn = (int) (date('Y'));
for ($i = 1988; $i <= $thn; ++$i) {
    $optThn .= '<option value='.$i.' '.(($i === $thn ? 'selected' : '')).'>'.$i.'</option>';
}
$sKlmpkBrg = 'select kode from '.$dbname.".log_5klbarang where kelompok like '%PUPUK%'";
$qKlmpkBrg = mysql_query($sKlmpkBrg) ;
$rKlmpkBrg = mysql_fetch_assoc($qKlmpkBrg);
$skdBrg = 'select kodebarang, namabarang, satuan from '.$dbname.".log_5masterbarang where kelompokbarang='".$rKlmpkBrg['kode']."'";
$qkdBrg = mysql_query($skdBrg) ;
while ($rkdBrg = mysql_fetch_assoc($qkdBrg)) {
    $optBrg .= '<option value='.$rkdBrg['kodebarang'].'>'.$rkdBrg['namabarang'].'</option>';
}
$sBibit = 'select jenisbibit  from '.$dbname.'.setup_jenisbibit order by jenisbibit  asc';
$qBibit = mysql_query($sBibit) ;
while ($rBibit = mysql_fetch_assoc($qBibit)) {
    $optBibit .= "<option value='".$rBibit['jenisbibit']."'>".$rBibit['jenisbibit'].'</option>';
}
echo "<fieldset><legend>";echo $_SESSION['lang']['form'];echo "</legend>
<table cellspacing=\"1\" border=\"0\">
<tr><td>";echo $_SESSION['lang']['tahunpupuk'];echo "</td>
<td>:</td>
<td><select id=\"periode\" nama=\"periode\" style=\"width:150px;\">";
echo $optPeriode;
echo "</select>
</td>
</tr>

<tr>
<td>";echo $_SESSION['lang']['afdeling'];echo "</td>
<td>:</td>
<td><select id=\"idKbn\" name=\"idKbn\"  onchange=\"getBlok('0','0')\"><option value=\"\"></option>";echo $optKbn;echo "</select></td>
</tr>

<tr>
<td>"; echo $_SESSION['lang']['blok'];echo "</td>
<td>:</td>
<td><select id=\"idBlok\" name=\"idBlok\" onchange=\"getThn()\"></select><input type=\"hidden\" id=\"oldBlok\" name=\"oldBlok\" /></td>
</tr>

<tr>
<td>"; echo $_SESSION['lang']['tahuntanam'];echo "</td>
<td>:</td>
<td><select id=\"thnTnm\" name=\"thnTnm\"></select></td>
</tr>

<tr>
<td>";
echo $_SESSION['lang']['jenisPupuk'];echo "</td>
<td>:</td>
<td><select id=\"jnsPpk\" name=\"jnsPpk\"  onchange=\"getSatuan()\"><option value=\"\"></option>";echo $optBrg; echo "</select></td>
</tr>

<tr>
<td>";echo $_SESSION['lang']['dosis'];echo "</td>
<td>:</td>
<td><input type=\"text\" id=\"dosis\" name=\"dosis\" class=\"myinputtextnumber\"   onkeypress=\"return angka_doang(event)\" value=\"0\" />&nbsp;<span id=\"satuan\"></span></td>
</tr>

<tr>
<td>Jenis Pupuk Ekstra"; echo "</td>
<td>:</td>
<td><select id=\"dosis3\" name=\"dsis3\"  onchange=\"getSatuanx()\">";echo $optBrg;echo "</select></td>
</tr>

<tr>
<td> Dosis Ekstra"; echo "</td>
<td>:</td>
<td><input type=\"text\" id=\"dosis2\" name=\"dosis\" class=\"myinputtextnumber\"   onkeypress=\"return angka_doang(event)\" value=\"0\" />&nbsp;<span id=\"satuan2\"></span></td>
</tr>

<tr>
<td>";echo $_SESSION['lang']['jenisbibit'];echo "</td>
<td>:</td>
<td><select id=\"jnsBibit\" name=\"jnsBibit\">";echo $optBibit;echo "</select></td>
</tr>

<tr>
<td></td>
<td></td>
<td id=\"tmblHeader\">
<button class=mybutton id='dtl_pem' onclick='saveData()'>";
echo $_SESSION['lang']['save'];
echo "</button><button class=mybutton id='cancel_gti' onclick='cancelSave()'>";
echo $_SESSION['lang']['cancel'];
echo "</button>
</td>
</tr>
</table>
</fieldset>";
CLOSE_BOX();
echo "</div>";
echo close_body();

?>