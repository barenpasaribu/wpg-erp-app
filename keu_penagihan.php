<?php

require_once 'master_validation.php';
include 'lib/eagrolib.php';
include_once 'lib/zLib.php';
include_once 'lib/devLibrary.php';
echo open_body();
include 'master_mainMenu.php';
OPEN_BOX('', '<b>'.strtoupper($_SESSION['lang']['penagihan']).'</b>');
echo "<link rel=stylesheet type=text/css href=\"style/zTable.css\">\r\n<script language=\"javascript\" src=\"js/zMaster.js\"></script>\r\n<script language=javascript src=js/zTools.js></script>\r\n<script type=\"text/javascript\" src=\"js/keu_penagihan.js\" /></script>\r\n\r\n\r\n\r\n";
echo "<table>\r\n     <tr valign=middle>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>\r\n\t   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>\r\n\t <td align=center style='width:100px;cursor:pointer;' onclick=loadData(0)>\r\n\t   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>\r\n\t <td><fieldset><legend>".$_SESSION['lang']['find'].'</legend>';

echo $_SESSION['lang']['noinvoice'].':<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>';
echo $_SESSION['lang']['tanggal'].':<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />';
echo '<button class=mybutton onclick=cariData(0)>'.$_SESSION['lang']['find'].'</button>';
echo "</fieldset></td>\r\n     </tr>\r\n\t </table> ";
CLOSE_BOX();
OPEN_BOX();
echo '<div id=listData>';
echo '<fieldset><legend>'.$_SESSION['lang']['data'].'</legend>';
echo '<table cellpading=1 cellspacing=1 border=0 class=sortable style=width:100%><thead>';
echo '<tr><td>'.$_SESSION['lang']['noinvoice'].'</td>';
echo '<td>'.$_SESSION['lang']['unit'].'</td>';
echo '<td>'.$_SESSION['lang']['tanggal'].'</td>';
echo '<td>'.$_SESSION['lang']['nodo'].'</td>';
echo '<td>'.$_SESSION['lang']['jumlah'].'</td>';
echo '<td>'.$_SESSION['lang']['keterangan'].'</td>';
echo '<td colspan=4>'.$_SESSION['lang']['action'].'</td>';
echo '</tr></thead><tbody id=continerlist><script>loadData(0)</script></tbody>';
$skeupenagih = 'select count(*) as rowd from '.$dbname.".keu_penagihanht where kodeorg='".$_SESSION['empl']['lokasitugas']."'";
$qkeupenagih = mysql_query($skeupenagih);
$rkeupenagih = mysql_num_rows($qkeupenagih);
$totrows = ceil($rkeupenagih / 10);
if (0 == $totrows) {
    $totrows = 1;
}

for ($er = 1; $er <= $totrows; ++$er) {
    @$isiRow .= "<option value='".$er."'>".$er.'</option>';
}
echo '<tfoot id=footData></tfoot></table></fieldset></div><input type=hidden id=proses value=insert />';
$whereJam = " kasbank=1 and detail=1 and (pemilik='".$_SESSION['empl']['tipelokasitugas']."' or pemilik='GLOBAL' or pemilik='".$_SESSION['empl']['lokasitugas']."' or pemilik='".$_SESSION['empl']['induklokasitugas']."')";
$sakun = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun \r\n        where  ".$whereJam.' order by namaakun asc';
$qakun = mysql_query($sakun);
while ($rakun = mysql_fetch_assoc($qakun)) {
    @$optAkun .= "<option value='".$rakun['noakun']."'>".$rakun['noakun'].'-'.$rakun['namaakun'].'</option>';
}

$sakun = "select kodecustomer,namacustomer, kelompok  from ".$dbname.".pmn_4customer  a left join ".$dbname.".pmn_4klcustomer b on a.klcustomer=b.kode order by namacustomer";
$qakun = mysql_query($sakun);
while ($rakun = mysql_fetch_assoc($qakun)) {
    @$optCust .= "<option value='".$rakun['kodecustomer']."'>".$rakun['kodecustomer'].'-'.$rakun['namacustomer']." (".$rakun['kelompok'].')</option>';
}
$sTTd = "select distinct a.namakaryawan,a.*  from ".$dbname.".datakaryawan  a , ".$dbname.".sdm_5golongan b where a.kodegolongan=b.kodegolongan and alias in ('ttdinv','po_sign') and a.isduplicate=0 and tanggalkeluar is null and kodeorganisasi='".$_SESSION['empl']['kodeorganisasi']."'";
$qTTd = mysql_query($sTTd);
while ($rTTd = mysql_fetch_assoc($qTTd)) {
    @$optTTd .= "<option value='".$rTTd['karyawanid']."'>".$rTTd['namakaryawan'].'</option>';
}
$sPrm = 'select * from '.$dbname.".keu_5parameterjurnal where jurnalid='INVPK1'";

$qPrm = mysql_query($sPrm);
$rPrm = mysql_fetch_assoc($qPrm);
$kreditawal = $rPrm['noakunkredit'];
$kreditakhir = $rPrm['sampaikredit'];

//$sakundbt = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where char_length(noakun)>5  AND left(noakun,1)='5' or namaakun = 'PPN Keluaran' or noakun like '91104%'\r\n and char_length(noakun)>5       order by noakun asc";
$sakundbt = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where noakun between '".$kreditawal."' and '".$kreditakhir."' and detail=1  order by noakun asc";

$qakun = mysql_query($sakundbt);
while ($rakun = mysql_fetch_assoc($qakun)) {
    @$optKredit .= "<option value='".$rakun['noakun']."'>".$rakun['noakun'].'-'.$rakun['namaakun'].'</option>';
}
$sakundbt = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where noakun between '".$kreditawal."' and '".$kreditakhir."' and detail=1 order by noakun asc";
$qakun = mysql_query($sakundbt);
while ($rakun = mysql_fetch_assoc($qakun)) {
    @$optDebet .= "<option value='".$rakun['noakun']."'>".$rakun['noakun'].'-'.$rakun['namaakun'].'</option>';
}
$sakunUM = 'select distinct noakun,namaakun from '.$dbname.".keu_5akun where noakun between '".$kreditawal."' and '".$kreditakhir."' and detail=1 order by noakun asc";

$qakunUM = mysql_query($sakunUM);
$optUM =  "<option value=''>".'</option>';
while ($rakunUM = mysql_fetch_assoc($qakunUM)) {
    @$optUM .= "<option value='".$rakunUM['noakun']."'>".$rakunUM['noakun'].'-'.$rakunUM['namaakun'].'</option>';
}

$optTipe = "<option value='0'>Penagihan Biasa</option>";
$optTipe .= "<option value='1'>Uang Muka</option>";
//$optTipe .= "<option value='2'>Penagihan Biasa + Uang Muka</option>";
$optTipe .= "<option value='3'>Pelunasan</option>";

$optPph = getPPhOptions();
$optPPh2="";
foreach ($optPph as $key=>$value){
    $optPPh2.="<option value='$key'>$value</option>";
}

$arr = '##noinvoice##jatuhtempo##kodeorganisasi##nofakturpajak##tanggal##bayarke##proses';
//$arr .= '##kodecustomer##uangmuka##noorder##nilaippn##keterangan##nilaiinvoice##debet##kredit';
$arr .= '##kodecustomer##tipe##uangmuka##akunuangmuka##noorder##nilaippn##keterangan##dpp##debet##kredit##akunpph##nilaipph##potongsusutkgint##potongsusutjmlint##potongsusutkgext##potongsusutjmlext##potongmutuint##potongmutuext##noakunppn##ttd##tonase##hargasatuan';

echo '<div id=formInput style=display:none;>';
echo '<fieldset style=clear:both><legend>'.$_SESSION['lang']['form']."</legend>\r\n    <table>";
echo '<tr><td>'.$_SESSION['lang']['tipe'].'</td><td><select id=tipe  style=width:150px; onchange=\'getAkun()\'>'.$optTipe.'</select></td>';
echo '<td></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['noinvoice'].'</td><td><input type=text id=noinvoice class=myinputtext style=width:150px;  ></td>';
echo '<td>'.$_SESSION['lang']['jatuhtempo'].'</td><td><input type=text class=myinputtext id=jatuhtempo onmousemove=setCalendar(this.id) onkeypress=return false;   style=width:150px;  maxlength=10 /></td></tr>';
echo '<tr><td>'.$_SESSION['lang']['kodeorganisasi']."</td><td><input type=text id=kodeorganisasi class=myinputtext style=width:150px; readonly value='".$_SESSION['empl']['lokasitugas']."' /></td>";
echo '<td>'.$_SESSION['lang']['nofaktur']."</td><td><input type=text class=myinputtext id=nofakturpajak  style=width:150px;  onkeypress='return tanpa_kutip(event)' /></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['tanggal'].'</td><td><input type=text class=myinputtext id=tanggal onmousemove=setCalendar(this.id) onkeypress=return false; onchange=\'getNoT()\'  style=width:150px;  maxlength=10 /></td>';
echo '<td>'.$_SESSION['lang']['bayarke'].'</td><td><select id=bayarke  style=width:150px;>'.$optAkun.'</select></td></tr>';
echo "<tr><td colspan=4> </td></tr>";
echo "<tr><td colspan=4 bgcolor='FFFFFF'> <table>";

echo '<tr><td>'.$_SESSION['lang']['nodo']."</td><td colspan=3><input type=text id=noorder class=myinputtext style=width:300px; readonly onclick=\"searchNosibp('".$_SESSION['lang']['find'].' '.$_SESSION['lang']['nosipb']."','<div id=formPencariandata></div>',event)\" /></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['kodecustomer'].'</td><td colspan=3><select id=kodecustomer style=width:300px>'.$optCust.'</select></td></tr>';
echo "<tr><td>No Kontrak</td><td colspan=3><input type=text id=nokontrak class=myinputtext  style=width:300px;' readonly/></td> </tr>";

echo "<tr><td colspan=4><table border=1 cellpading=0 cellspacing=0><tr><td>Tonase</td><td>Harga/Kg</td><td>Jumlah</td></tr>";
echo "<tr><td><input type=text id=tonase class=myinputtextnumber style=width:150px; onkeyup=\"hitungakhir()\"'></td><td><input type=text id=hargasatuan class=myinputtextnumber style=width:150px; onkeyup=\"hitungakhir()\"></td><td><input type=text id=jumlah class=myinputtextnumber style=width:150px;></td></tr>";
echo "<tr><td colspan=2>Sub Total</td><td><input type=text id=nilaiinvoice class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' readonly /></td></tr>";

echo '<tr><td><label id=lbluangmuka>Uang Muka</label></td><td><select id=akunuangmuka style=width:150px;>'.$optUM.'</select></td><td><input type=hidden id=uangmuka class=myinputtextnumber style=width:150px; /></td><tr>';

echo "<tr><td colspan=2>Potongan Susut Internal</td><td><input type=text id=potongsusutkgint  style=width:55px; onkeyup=\"hitungPotSusutInt()\"'/>Kg<input type=text id=potongsusutjmlint  style=width:75px; onclick=\"hitungPotSusutInt()\"'/></td></tr>";
echo "<tr><td colspan=2>Potongan Susut External</td><td><input type=text id=potongsusutkgext  style=width:55px; onkeyup=\"hitungPotSusutExt()\"'/>Kg<input type=text id=potongsusutjmlext  style=width:75px; onclick=\"hitungPotSusutExt()\"'/></td></tr>";
echo '<tr><td colspan=2>Potongan Mutu Internal'."</td><td><input type=text id=potongmutuint class=myinputtext style=width:150px; onkeypress='return tanpa_kutip(event)'  onkeyup=\"hitungakhir()\"/></td><tr>";
echo '<tr><td colspan=2>Potongan Mutu External'."</td><td><input type=text id=potongmutuext class=myinputtext style=width:150px; onkeypress='return tanpa_kutip(event)'  onkeyup=\"hitungakhir()\"/></td></tr>";

echo "<tr><td colspan=2>NILAI INVOICE</td><td><input type=text id=dpp class=myinputtextnumber  style=width:150px; onkeypress='return angka_doang(event)' value='0' onclick='hitungakhir()' /></td></tr>";

echo '<tr><td colspan=2>'.$_SESSION['lang']['nilaippn']."</td><td><input type=checkbox id=cekppn onchange=\"GetPPN()\"' /><input type=text id=nilaippn class=myinputtextnumber style=width:130px; onkeypress='return angka_doang(event)' /><input type=hidden id=noakunppn class=myinputtextnumber style=width:130px; value=2120108 /></td></tr>";

echo "<tr><td>Nilai Pph</td><td><select id=akunpph onchange=\"GetPPH()\"' style=width:150px;>".$optPPh2."</select></td><td><input type=text id=nilaipph class=myinputtextnumber  style=width:150px; onkeypress='return angka_doang(event)' value='0'/></td></tr>";

echo "</table></td></tr>";

echo "<td>Penanda Tangan</td><td colspan=3><select id=ttd  style=width:150px;>".$optTTd."</select></td></tr>";
echo '<tr><td>'.$_SESSION['lang']['keterangan']."</td><td colspan=3><input type=text id=keterangan class=myinputtext style=width:300px; onkeypress='return tanpa_kutip(event)'  /></td>";

echo '<tr><td>'.$_SESSION['lang']['debet'].'</td><td><select id=debet style=width:150px;>'.$optDebet.'</select></td>';
echo '<td>'.$_SESSION['lang']['kredit'].'</td><td><select id=kredit style=width:150px;>'.$optKredit.'</select></td></tr>';

echo "</table></td></tr>";
echo "<tr><td colspan=4><button class=mybutton onclick=saveData('keu_slave_penagihan','".$arr."')>".$_SESSION['lang']['save']."</button>&nbsp;\r\n         <button class=mybutton onclick=cancelData()>".$_SESSION['lang']['cancel'].'</button></td></tr>';
echo '</table></fieldset></div>';

echo "<div id=printArea style=display:none;><fieldset style='clear:both'><legend><b>Data Timbangan</b></legend>\r\n<div id='printContainer'>\r\n\r\n</div></fieldset></div>\r\n";
CLOSE_BOX();
echo close_body();

?>