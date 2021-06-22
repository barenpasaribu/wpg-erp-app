<?

require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
?>

<script type="text/javascript" src="js/vhc_5premiJarakDanBerat.js"></script>
<script type="text/javascript" src="js/zMaster.js"></script>
<?php
$soptOrg='';
$sorg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe in('HOLDING','KEBUN','KANWIL','PABRIK')
      and kodeorganisasi='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
      order by namaorganisasi";
$qorg=mysql_query($sorg) or die(mysql_error());
global $kd_org;
while($rorg=mysql_fetch_assoc($qorg))
{
	$kd_org=$rorg['kodeorganisasi'];
	$soptOrg.="<option '".($rorg['kodeorganisasi']==$rest['kodeorganisasi']?'selected=selected':'')."' value=".$rorg['kodeorganisasi']." >".$rorg['namaorganisasi']."</option>";
}
//----tab pertama...
$sjnskrj="select * from ".$dbname.".vhc_kegiatan";
$qjnskrj=mysql_query($sjnskrj) or die(mysql_error());
while($rjnskrj=mysql_fetch_assoc($qjnskrj))
{
	$optJnsKerja.="<option value=".$rjnskrj['kodekegiatan'].">".$rjnskrj['namakegiatan']."</option>";
}
$lokTugas=substr($_SESSION['empl']['lokasitugas'],0,4);
$optKeyCode='';
$sOptKey="select keycode from ".$dbname.".setup_mappremi where kodeorg='".$lokTugas."' and keycode like '%TRANS01%'"; //echo $sOptKey;
$qOptKey=mysql_query($sOptKey) or die(mysql_error());
while($rOptKey=mysql_fetch_assoc($qOptKey))
{
	$optKeyCode.="<option value=".$rOptKey['keycode']." >".$rOptKey['keycode']."</option>";
}
$arrPos=array("Sopir","Kondektur");
foreach($arrPos as $brs => $isi)
{
	$optPosition.="<option value=".$brs.">".$isi."</option>";
}

OPEN_BOX('',"<b>".$_SESSION['lang']['premiTransportJarak']."</b>");
$frm[0].="<fieldset><legend>".$_SESSION['lang']['entryForm']."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['keycode']."</td><td>:</td><td>
<select id=keyCode name=keyCode style=width:150px; onchange=cekNomor()>".$optKeyCode."</select>
<!--<input type=text id=keyCode name=keyCode class=myinputtext style=width:150px; />--></td></tr>
<tr><td>".$_SESSION['lang']['nomor']."</td><td>:</td><td>
<input type=text id=nomor name=nomor class=myinputtextnumber maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; disabled />
</td></tr>
<tr><td>".$_SESSION['lang']['vhc_posisi']."</td><td>:</td><td>
<select id=posisi name=posisi style=width:150px;>".$optPosition."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td><td>:</td><td>
<select id=tipeAnkg name=tipeAnkg style=width:150px; >".$optJnsKerja."</select></td></tr>
<tr><td>".$_SESSION['lang']['jarakdari']."</td><td>:</td><td>
<input type=text id=jrkDari name=jrkDari class=myinputtextnumber maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['jaraksampai']."</td><td>:</td><td>
<input type=text id=jrkSmp name=jrkSmp class=myinputtextnumber maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></td></tr>
<!--<tr><td>".$_SESSION['lang']['jumlahbasis']."</td><td>:</td><td>
<input type=text id=jmlhBasis name=jmlhBasis class=myinputtextnumber maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; />&nbsp;Ton</td></tr>-->
<tr><td>".$_SESSION['lang']['premilebihbasis']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=premiLbhBasis name=premiLbhBasis maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td colspan=3>
<button class=mybutton id=save_kepala name=save_kepala onclick=save_header()  >".$_SESSION['lang']['save']."</button><button class=mybutton id=cancel_kepala name=cancel_kepala onclick=clear_form()  >".$_SESSION['lang']['cancel']."</button>
<input type=hidden id=proses name=proses value=insert_header >
</td></tr></table>";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['keycode']."</td>
		<td>".$_SESSION['lang']['nomor']."</td>
		<td>".$_SESSION['lang']['jarakdari']."</td>
		<td>".$_SESSION['lang']['jaraksampai']."</td>
		<td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
		<td>".$_SESSION['lang']['premilebihbasis']."</td>
		<td>".$_SESSION['lang']['vhc_posisi']."</td>
		<td>Action</td>
		</tr></thead>
		<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('kebun_5ratetransport','','','vhc_5premiJarakDanBeratPdf',event);\">
		<tbody id=contain>
		<script>load_data()</script>
		";
$frm[0].="</tbody></table></fieldset>";

//Detail Pekerjaan
/*

$frm[1].="<fieldset><legend>".$_SESSION['lang']['detail']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['keycode']."</td><td>:</td><td>
<input type=text id=keyCodeDetail name=keyCodeDetail class=myinputtext style=width:150px; disabled /></td></tr>
<tr><td>".$_SESSION['lang']['nomor']."</td><td>:</td><td>
<input type=text id=nmrDetail name=nmrDetail class=myinputtextnumber maxlength=8  disabled style=width:150px;  />
</td></tr>
<tr><td>".$_SESSION['lang']['vhc_posisi']."</td><td>:</td><td>
<select id=posisi name=posisi style=width:150px;>".$optPosition."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['premilebihbasis']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=premiLbhBasis name=premiLbhBasis maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['proporsipenalty']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=penalty name=penalty maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td colspan=3>
<button class=mybutton onclick=saveDetail() id=saveDetail name=saveDetail disabled >".$_SESSION['lang']['save']."</button>
<button class=mybutton onclick=clearDetail() id=cancelDetail name=cancelDetail disabled >".$_SESSION['lang']['cancel']."</button>
<button class=mybutton id=done_entry name=done_entry onclick=doneEntry() disabled >".$_SESSION['lang']['done']."</button>
<input type=hidden id=prosesDetail name=prosesDetail value=insertDetail />
</table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['keycode']."</td>
		<td>".$_SESSION['lang']['vhc_posisi']."</td>
		<td>".$_SESSION['lang']['premilebihbasis']."</td>
		<td>".$_SESSION['lang']['proporsipenalty']."</td>
		<td>Action</td>
		</tr></thead>
		<tbody id=containDetail>
		";
$frm[1].="</tbody></table></fieldset>";*/

//karyawan


//========================
$hfrm[0]=$_SESSION['lang']['header'];
//$hfrm[1]=$_SESSION['lang']['detail'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>





















<?php
CLOSE_BOX();
echo close_body();
?>