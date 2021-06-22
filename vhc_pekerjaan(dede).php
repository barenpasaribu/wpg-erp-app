<?

require_once('master_validation.php');
include 'lib/eagrolib.php';
include('lib/zFunction.php');
echo open_body();
include('master_mainMenu.php');
$frm[0]='';
$frm[1]='';
$frm[2]='';

?>
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/vhc_pekerjaan.js"></script>
<script>
dataKdvhc="<?php echo $_SESSION['lang']['pilihdata']?>";
</script>
<?php
$soptOrg='';    
$sorg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where
      tipe in ('HOLDING','KEBUN','KANWIL','PABRIK')
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
$sjvch="select * from ".$dbname.".vhc_5jenisvhc order by namajenisvhc";
$qjvch=mysql_query($sjvch) or die(mysql_error());
while($rjvch=mysql_fetch_assoc($qjvch))
{
	$optJnsvhc.="<option value='".$rjvch['jenisvhc']."'>".$rjvch['jenisvhc']."-".$rjvch['namajenisvhc']."</option>";
}
$strak="select kodeorganisasi, namaorganisasi from ".$dbname.".organisasi where tipe = 'TRAKSI' order by namaorganisasi ";
$qtrak=mysql_query($strak) or die(mysql_error());
while($rtrak=mysql_fetch_assoc($qtrak))
{
	$optTraksi.="<option value=".$rtrak['kodeorganisasi'].">".$rtrak['kodeorganisasi']."-".$rtrak['namaorganisasi']."</option>";
}
$arrOpt=array("KM","HM");
foreach($arrOpt as $brs => $isi)
{
	$optSatuanvhc.="<option value=".$isi.">".$isi."</option>";
}
//$where=" `kelompokbarang` = '003'"; //Bensin, Pertalite, Solar
$sbrg="select a.kodebarang,a.namabarang from log_5masterbarang a left join log_5klbarang b on a.kelompokbarang = b.kode where b.alias='bbm'";
$qbrg=mysql_query($sbrg) or die(mysql_error());
while($rbrg=mysql_fetch_assoc($qbrg))
{
	$optJnsBBMvhc.="<option value=".$rbrg['kodebarang'].">".$rbrg['kodebarang']."-".$rbrg['namabarang']."</option>";
}
$arrPremi=array("Non Premi","Premi");
foreach($arrPremi as $brs => $isi)
{
	$optStatPremi.="<option value=".$brs.">".$isi."</option>";
}
$lksiTgs=substr($_SESSION['empl']['lokasitugas'],0,4);
for($x=0;$x<=3;$x++)
{
	$dt=mktime(0,0,0,0,15,date('Y')+$x);
	$optper.="<option value=".date("Y",$dt).">".date("Y",$dt)."</option>";
}
///<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td>
//<input type=text id=no_trans name=no_trans disabled=disabled class=myinputtext style=width:150px; /></td></tr>
$optOrg2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
//$sOrg2="select kodeorganisasi from ".$dbname.".organisasi where tipe in ('KEBUN','KANWIL','TRAKSI') order by kodeorganisasi asc";
$sOrg2="select kodeorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['empl']['lokasitugas']."' order by kodeorganisasi asc";
$qOrg2=mysql_query($sOrg2) or die(mysql_error());
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optOrg2.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['kodeorganisasi']."</option>";
}
$optStatusLst="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrStatus=array("0"=>"Belum Posting","1"=>"Sudah diposting");
foreach($arrStatus as $lstStatus=>$vwStatus)
{
    $optStatusLst.="<option value='".$lstStatus."'>".$vwStatus."</option>";
}
//<button class=mybutton id=create_new name=create_new onclick=createNew() >".$_SESSION['lang']['new']."</button>
OPEN_BOX('',"<b>".$_SESSION['lang']['vhc_pekerjaan']."</b>");
$frm[0].="<fieldset><legend>".$_SESSION['lang']['header']."".$thn_skrg."</legend>";
$frm[0].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td><select id=KbnId name=KbnId onchange=\"createNew()\">".$optOrg2."</select>
<input type=text id=no_trans name=no_trans disabled=disabled class=myinputtext style=width:150px; /></td></tr>
<!--<tr><td>".$_SESSION['lang']['thnKontrak']." </td><td>:</td><td>
<select id=thnKntrk name=thnKntrk style='width:150px;' onchange=\"getKntrk('','')\"><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optper."</select> </td></tr>
<tr><td>".$_SESSION['lang']['NoKontrak']."</td><td>:</td><td>
<select id=noKntrk name=noKntrk style='width:150px;'><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
<tr><td>".$_SESSION['lang']['statPremi']."</td><td>:</td><td>
<select id=premiStat name=premiStat style=width:150px;>".$optStatPremi."</select></td></tr>-->
<tr><td>".$_SESSION['lang']['jenisvch']."</td><td>:</td><td>
<select id=jns_vhc name=jns_vhc style=width:150px; onchange=\"get_kd('')\"><option value=>".$_SESSION['lang']['pilihdata']."</option>".$optJnsvhc."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodetraksi']."</td><td>:</td><td>
<select id=kodetraksi name=kodetraksi style=width:150px; onchange=\"get_kd('')\"><option value=>".$_SESSION['lang']['all']."</option>".$optTraksi."</select></td></tr>
<tr><td>".$_SESSION['lang']['kodevhc']."</td><td>:</td><td>
<select id=kde_vhc name=kde_vhc style=width:150px;><option value=''>".$_SESSION['lang']['pilihdata']."</option></select></td></tr>
<tr><td>".$_SESSION['lang']['tanggal']."</td><td>:</td><td>
<input type=text class=myinputtext id=tgl_pekerjaan name=tgl_pekerjaan onmousemove=\"setCalendar(this.id)\" onkeypress=\"return false\";   maxlength=10  style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['vhc_jenis_bbm']."</td><td>:</td><td>
<select id=jns_bbm name=jns_bbm style=width:150px;>".$optJnsBBMvhc."</select></td></tr>
<tr><td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=jmlh_bbm name=jmlh_bbm maxlength=60 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td colspan=3>
<button class=mybutton id=save_kepala name=save_kepala onclick=save_header() disabled >".$_SESSION['lang']['save']."</button><button class=mybutton id=cancel_kepala name=cancel_kepala onclick=cancel_kepala_form() disabled >".$_SESSION['lang']['cancel']."</button><button class=mybutton id=done_entry name=done_entry onclick=doneEntry() disabled >".$_SESSION['lang']['done']."</button>

<input type=hidden id=proses name=proses value=insert_header >
</td></tr></table>";
$frm[0].="</fieldset>";
$frm[0].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend>
<fieldset style=\"float: left;\"><legend>".$_SESSION['lang']['find']." Data</legend>
    <table cellspacing=\"1\" border=\"0\"><tr>
        <td>".$_SESSION['lang']['notransaksi']."</td>
        <td><input type=\"text\" id='txtCari' name='txtCari' style='width:150px' class=myinputtext />
        &nbsp;".$_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />
        &nbsp;".$_SESSION['lang']['status'].":<select id=statusInputan>".$optStatusLst."</select>
        <button class=mybutton id=cariTransaksi name=cariTransaksi onclick=cariDataTransaksi()  >".$_SESSION['lang']['find']."</button><button class=mybutton  onclick=load_data()  >".$_SESSION['lang']['cancel']."</button>
</td></tr></table></fieldset>
<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['notransaksi']."</td>
		<td>".$_SESSION['lang']['jenisvch']."</td>
		<td>".$_SESSION['lang']['kodevhc']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['vhc_jenis_bbm']."</td>
		<td>".$_SESSION['lang']['vhc_jumlah_bbm']."</td>
		<td>Action</td>
		</tr></thead><tbody id=contain>
		<script>load_data()</script>
		";
$frm[0].="</tbody></table></fieldset>";
/*
if($_SESSION['empl']['lokasitugas']=='SSRO' || $_SESSION['empl']['lokasitugas']=='BMIE' || $_SESSION['empl']['lokasitugas']=='BJUE'){
	$reg = 'KALTENG';
}elseif($_SESSION['empl']['lokasitugas']=='BMRO' || $_SESSION['empl']['lokasitugas']=='BNME'){
	$reg = 'SUMUT';
}elseif($_SESSION['empl']['lokasitugas']=='HSRO' || $_SESSION['empl']['lokasitugas']=='HSGE'){
	$reg = 'SUMUT';
}elseif($_SESSION['empl']['lokasitugas']=='MSRO' || $_SESSION['empl']['lokasitugas']=='AEKE'){
	$reg = 'SUMUT';
}
*/
$reg = 'RIAU';

//Detail Pekerjaan
$sjnskrj="select * from ".$dbname.".vhc_kegiatan where  regional = '".$reg."' order by noakun asc";
$qjnskrj=mysql_query($sjnskrj) or die(mysql_error());
while($rjnskrj=mysql_fetch_assoc($qjnskrj))
{
	$optJnsKerja.="<option value=".$rjnskrj['kodekegiatan'].">".$rjnskrj['noakun']." - ".$rjnskrj['namakegiatan']."</option>";
}
$slokTgs="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where `tipe` NOT
IN ('PT', 'BLOK', 'HOLDING', 'STATION', 'STENGINE','AFDELING')";
$qlokTgs=mysql_query($slokTgs) or die(mysql_error());
while($rlokTgs=mysql_fetch_assoc($qlokTgs))
{
	$optLokTugas.="<option value=".$rlokTgs['kodeorganisasi'].">".$rlokTgs['namaorganisasi']."</option>";
}

$frm[1].="<fieldset><legend>".$_SESSION['lang']['vhc_detail_pekerjaan']."</legend>";
$frm[1].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td>
<input type=text id=no_trans_pekerjaan name=no_trans_pekerjaan disabled=disabled class=myinputtext style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td><td>:</td><td>
<select id=jns_kerja name=jns_kerja  style=width:150px;><option value=></option>".$optJnsKerja."</select>
<input type=hidden name=old_jnskerja id=old_jnskerja />
</td></tr>
<tr><td>".$_SESSION['lang']['alokasibiaya']."</td><td>:</td><td>
<select id=lokasi_kerja name=lokasi_kerja  style=width:150px; onchange=\"getBlok('','')\"><option value=''>".$_SESSION['lang']['pilihdata']."</option>".$optLokTugas."</select> 
<input type=hidden name=old_lokkerja id=old_lokkerja />
</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>
<select id=blok name=blok  style=width:150px; ><option value=''>".$_SESSION['lang']['pilihdata']."</option></select> (* Obligatory if activity location on estate/Jika pekerjaan dilakukan di Kebun
<input type=hidden name=old_blok id=old_blok />
</td></tr>
<tr><td>".$_SESSION['lang']['prestasi']."</td><td>:</td>
<td><input type=text class=myinputtextnumber id=brt_muatan name=brt_muatan maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /> &nbsp;Unit/Mtr/Ha/Ton</td> </tr>
<tr><td>".$_SESSION['lang']['jumlahrit']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=jmlh_rit name=jmlh_rit maxlength=5 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['vhc_kmhm_awal']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=kmhm_awal name=kmhm_awal maxlength=8 onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=kmhm_akhir name=kmhm_akhir maxlength=8  onkeypress=\"return angka_doang(event);\" style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['satuan']."</td><td>:</td><td><select id=stn name=stn style=width:150px;>".$optSatuanvhc."</select></td></tr>
<tr><td>".$_SESSION['lang']['biaya']."</td><td>:</td><td>
<input type=text class=myinputtextnumber id=biaya name=biaya maxlength=45 onkeypress=\"return angka_doang(event);\" style=width:150px; /> Rp</td></tr>

<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td>
<input type=text class=myinputtext id=ket name=ket maxlength=45 onkeypress=\"return tanpa_kutip(event);\" style=width:150px; /></td></tr>

<tr><td colspan=3>
<button class=mybutton onclick=save_pekerjaan() >".$_SESSION['lang']['save']."</button>
<button class=mybutton onclick=bersih_form_pekerjaan() >".$_SESSION['lang']['cancel']."</button>
<input type=hidden id=proses_pekerjaan name=proses_pekerjaan value=insert_pekerjaan />
</table>";

$frm[1].="</fieldset>";
$frm[1].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['notransaksi']."</td>
		<td>".$_SESSION['lang']['vhc_jenis_pekerjaan']."</td>
		<td>".$_SESSION['lang']['alokasibiaya']."</td>
		<td>".$_SESSION['lang']['jumlahrit']."</td>
		<td>".$_SESSION['lang']['vhc_muatan']." (Ton)</td>
                <td>".$_SESSION['lang']['vhc_kmhm_awal']."</td>
		<td>".$_SESSION['lang']['vhc_kmhm_akhir']."</td>
		<td>".$_SESSION['lang']['satuan']."</td>
		<td>".$_SESSION['lang']['biaya']." (Rp.)</td>
		<td>Action</td>
		</tr></thead><tbody id=containPekerja>
		";
$frm[1].="</tbody></table></fieldset>";

//karyawan
$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$arrPos=array("Sopir","Kondektur", "Operator");
foreach($arrPos as $brs => $isi)
{
	$optPosition.="<option value=".$brs.">".$isi."</option>";
}
$frm[2].="<fieldset><legend>".$_SESSION['lang']['vhc_detail_operator']."</legend>";
$frm[2].="<table cellspacing=1 border=0>
<tr><td>".$_SESSION['lang']['notransaksi']."</td><td>:</td><td>
<input type=text id=no_trans_opt name=no_trans_opt disabled=disabled class=myinputtext style=width:150px; /></td></tr>
<tr><td>".$_SESSION['lang']['namakaryawan']."</td><td>:</td><td>
<select id=kode_karyawan name=kode_karyawan style=width:150px; onchange=\"getUmr()\">".$optKary."</select></select>
</td></tr>
<tr><td>".$_SESSION['lang']['vhc_posisi']."</td><td>:</td><td>
<select id=posisi name=posisi style=width:150px;>".$optPosition."</select></select>
&nbsp;&nbsp;&nbsp; 
Set premi ke <input type='text' id=persen onkeypress='return angka_doang(event)' maxlength=3 style=width:50px; value=100 onchange='ubahpremi()'/> %
<input type=hidden id=premidasar />
</td>
</tr>
<tr><td>Premi (Rp)</td><td>:</td><td>
<input type=text id=uphOprt name=uphOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' /></td></tr>
<tr><td>Premi Luar Jam Kerja</td><td>:</td><td>
<input type=text id=prmiOprt name=prmiOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' value=0 /></td></tr>
<tr><td>".$_SESSION['lang']['rupiahpenalty']."</td><td>:</td><td>
<input type=text id=pnltyOprt name=pnltyOprt class=myinputtextnumber style=width:150px; onkeypress='return angka_doang(event)' value=0 /></td></tr>
<tr><td colspan=3>
<button class=mybutton onclick=save_operator() >".$_SESSION['lang']['save']."</button>
<button class=mybutton onclick=clear_operator() >".$_SESSION['lang']['cancel']."</button>
<input type=hidden name=prosesOpt id=prosesOpt value=insert_operator />

</td></tr>
</table>";

$frm[2].="</fieldset>";
$frm[2].="<fieldset><legend>".$_SESSION['lang']['datatersimpan']."</legend><table cellspacing=1 border=0>
		<thead>
		<tr class=\"rowheader\">
		<td>No.</td>
		<td>".$_SESSION['lang']['notransaksi']."</td>
		<td>".$_SESSION['lang']['namakaryawan']."</td>
		<td>".$_SESSION['lang']['vhc_posisi']."</td>
		<td>".$_SESSION['lang']['upahkerja']."</td>
		<td>".$_SESSION['lang']['upahpremi']."</td>
		<td>".$_SESSION['lang']['rupiahpenalty']."</td>
		<td>Action</td>
		</tr></thead><tbody id=containOperator>
		<script>//load_data_operator()</script>
		";
$frm[2].="</tbody></table></fieldset>";

//========================
$hfrm[0]=$_SESSION['lang']['header'];
$hfrm[1]=$_SESSION['lang']['vhc_detail_pekerjaan'];
$hfrm[2]=$_SESSION['lang']['vhc_detail_operator'];
//$hfrm[1]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$frm,100,900);
//===============================================	
?>


<?php
CLOSE_BOX();
echo close_body();
?>