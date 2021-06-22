<?php



require_once 'master_validation.php';
include 'lib/eagrolib.php';
include 'lib/zMysql.php';
include 'lib/zFunction.php';
echo open_body();
include 'master_mainMenu.php';
echo "<script>\r\ndcari='";
echo $_SESSION['lang']['find'];
echo "';    \r\nmed='";
echo $_SESSION['lang']['medicalId'];
echo "';    \r\nberobat='";
echo $_SESSION['lang']['yangberobat'];
echo "';    \r\n</script>\r\n<script language=javascript src='js/sdm_pengobatan.js'></script>\r\n<link rel=stylesheet type=text/css href=style/payroll.css>\r\n";
OPEN_BOX('', $_SESSION['lang']['adm_peng']);
$optthn="<option value=''></option>";
for($x=-1;$x<4;$x++)
{
	$optthn.="<option value='".(date('Y')-$x)."'>".(date('Y')-$x)."</option>";
}

$optperiode="<option value=''></option>";
for($x=0;$x<36;$x++)
{
    $t=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optperiode.="<option value='".date('Y-m',$t)."'>".date('m-Y',$t)."</option>";
}
//ambil data karyawan==========

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
	$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where
	      alokasi=1 order by namakaryawan";
}
else
{
	$str="select karyawanid,namakaryawan from ".$dbname.".datakaryawan where
	      alokasi=0 and lokasitugas='".$_SESSION['empl']['lokasitugas']."' order by namakaryawan";	
}
$optKar="<option value=''></option>";
$res=mysql_query($str) or die(mysql_error());
while($bar=mysql_fetch_object($res))
{
	$optKar.="<option value='".$bar->karyawanid."'>".$bar->namakaryawan."</option>";
}
//=============
//yang berobat

$optKel="<option value=0>Ybs/PIC</option>";

//=============
//ambil jenis pengobatan
$str="select * from ".$dbname.".sdm_5jenisbiayapengobatan order by nama";
$res=mysql_query($str) or die(mysql_error());
$optJns='';
while($bar=mysql_fetch_object($res))
{
	$optJns.="<option value='".$bar->kode."'>".$bar->nama."</option>";
}

//=============
//ambil rumah sakit
$str="select id,namars,kota from ".$dbname.".sdm_5rs order by namars";
$res=mysql_query($str) or die(mysql_error());
$optRs='';
while($bar=mysql_fetch_object($res))
{
	$optRs.="<option value='".$bar->id."'>".$bar->namars."[".$bar->kota."]</option>";
}
//================
//ambil list diagnosa
$optDiagnosa='';
$str="select * from ".$dbname.".sdm_5diagnosa order by diagnosa";
$res=mysql_query($str) or die(mysql_error());
while($bar=mysql_fetch_object($res))
{
	$optDiagnosa.="<option value='".$bar->id."'>".$bar->diagnosa."</option>";
}
//=================
//jenis klaim
$optklaim="<option value=0>".$_SESSION['lang']['karyawan']."</option>
          <option value=1>".$_SESSION['lang']['rumahsakit']."</option>
          <option value=2>".$_SESSION['lang']['internal']." Clinic</option>";

//================
//loaksi tugas
$strd="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where length(kodeorganisasi)=4 order by kodeorganisasi";
$resd=mysql_query($strd) or die(mysql_error());
$lokasitugas="<option value=''></option>";
while($bard=mysql_fetch_object($resd))
{
    $lokasitugas.="<option value='".$bard->kodeorganisasi."'>".$bard->namaorganisasi."</option>";
}
//
//option periode akuntansi
$optx='';
for($x=-1;$x<13;$x++)
{
	$dt=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optx.="<option value='".date('Y-m',$dt)."'>".date('m-Y',$dt)."</option>";
}
$arr[0]="<div style='width:900px;height:460px;overflow:scroll;'><fieldset><legend>".$_SESSION['lang']['form']."</legend>

        <table>
	 <tr>
	  <td>".$_SESSION['lang']['thnplafon']."</td>
	  <td><select id=thnplafon onchange=getTrxNumber(this.options[this.selectedIndex].value)>".$optthn."</select>
               ".$_SESSION['lang']['periode']."<select id=periode>".$optx."</select></td>
	  <td>".$_SESSION['lang']['notransaksi']."</td>
	  <td><input type=text class=myinputtext id=notransaksi maxlength=20 disabled></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
	  <td><select id=jenisbiaya  style='width:200px;'>".$optJns."</select> </td>	 
	  <td>".$_SESSION['lang']['lokasitugas']."</td>
	  <td><select id=lokasitugas onchange=loadOptkar(this.options[this.selectedIndex].value)>".$lokasitugas."</select></td>
        <td>".$_SESSION['lang']['namakaryawan']."</td>  
		  <td><select id=karyawanid style='width:200px;' onchange=\"getFamily(this.options[this.selectedIndex].value);\">".$optKar."</select>
	      <input type=hidden value=insert id=method></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['yangberobat']."</td>
	  <td><select id=ygberobat style='width:200px;'>".$optKel."</select> </td>	 
	  <td>".$_SESSION['lang']['rumahsakit']."</td>
	  <td><select id=rs style='width:200px;'>".$optRs."</select></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['diagnosa']."</td>
	  <td><select id=diagnosa style='width:200px;'>".$optDiagnosa."</select></td>	  	 
	  <td>".$_SESSION['lang']['klaim']."</td>
	  <td><select id=klaim style='width:200px;'>".$optklaim."</select></td>
	 </tr>
	 <tr>
	  <td>".$_SESSION['lang']['hariistirahat']."</td>
	  <td><input type=text class=myinputtext id=hariistirahat value=1 onkeypress=\"return angka_doang(event);\" maxlength=2 style='width:50px;'>
	      ".$_SESSION['lang']['tanggal']."<input type=text id=tanggal value='".date('d-m-Y')."' size=10 maxlength=10 onkeypress=\"return false\" onmouseover=setCalendar(this) class=myinputtext>".$_SESSION['lang']['tanggalselesai']."<input type=text id=tanggalselesai value='".date('d-m-Y')."' size=10 maxlength=10 onkeypress=\"return false\" onmouseover=setCalendar(this) class=myinputtext>
	  </td>	  	 
	  <td>".$_SESSION['lang']['keterangan']."</td>
	  <td><input type=text class=myinputtext id=keterangan onkeypress=\"return tanpa_kutip(event);\" style='width:200px;' ></td>
	 </tr>
	 </table>
	 <fieldset>
	  <legend>".$_SESSION['lang']['biayabiaya']."</legend>
	  <table>
	  <tr>
	    <td>".$_SESSION['lang']['biayars']."</td><td><input type=text id=byrs class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>
	    <td>".$_SESSION['lang']['biayapendaftaran']."</td><td><input type=text id=byadmin class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>		
	  </tr>
	  <tr>
	    <td>".$_SESSION['lang']['biayalab']."</td><td><input type=text id=bylab class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>
	    <td>".$_SESSION['lang']['biayaobat']."</td><td><input type=text id=byobat class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>		 
	  </tr>	
	  <tr>
	    <td>".$_SESSION['lang']['biayadr']."</td><td><input type=text id=bydr class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=10 value=0 onblur=\"change_number(this);calculateTotal();\"></td>	 
		<td>".$_SESSION['lang']['totalbiaya']."</td><td><input type=text id=total disabled class=myinputtextnumber onkeypress=\"return angka_doang(event);\" maxlength=15 value=0 onblur=\"change_number(this);calculateTotal();\"></td>	 
	  </tr>		  	   
	  </table>
	  <!--
	  <fieldset style='width:300px;'><legend>".$_SESSION['lang']['beban']."</legend>
                           <table>
                              <tr><td>".$_SESSION['lang']['perusahaan']."</td><td><input type=text class=myinputtextnumber id=bebanperusahaan onkeypress=\"return false;\" disabled sise=12 value=0></td></tr>
                              <tr><td>".$_SESSION['lang']['karyawan']."</td><td><input type=text class=myinputtextnumber id=bebankaryawan onkeypress=\"return angka_doang(event);\"  sise=12 value=0 onblur=kurangkanTotal(this)></td></tr>
                              <tr><td>Jamsostek</td><td><input type=text class=myinputtextnumber id=bebanjamsostek onkeypress=\"return angka_doang(event);\"  sise=12 value=0 onblur=kurangkanTotal(this)></td></tr>
                           </table>
                      </fieldset>
	 </fieldset> 
	 -->
	<input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=savePengobatan() id=mainsavebtn>
	<input type=button  class=mybutton value='".$_SESSION['lang']['new']."' onclick=clearForm();>
	</fieldset></div>";
$arr[1]="<fieldset>
	  <legend>".$_SESSION['lang']['obatobat']."</legend>
	  <table>
	  <tr>
	    <td>".$_SESSION['lang']['namaobat']."</td><td><input type=text id=namaobat class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=45></td>	  </tr>
                         <td>".$_SESSION['lang']['jenis']."</td><td><select id=jenisobat><option value=Paten>Paten</option><option value=Generic>Generic</option></select></td>	  </tr>
	  </table>
	  <input type=button class=mybutton value='".$_SESSION['lang']['save']."' onclick=saveObat()>
	  <fieldset>
	    <legend>".$_SESSION['lang']['list']."</legend>
		<div>
		 <table class=sortable cellspacing=1 border=0>
		  <thead>
		   <tr class=rowheader>
		    <td>No.</td>
			<td>".$_SESSION['lang']['notransaksi']."</td>
			<td>".$_SESSION['lang']['namaobat']."</td>
                                                            <td>".$_SESSION['lang']['jenis']."</td>
			<td></td>
		   </tr>
		  </thead>
		  <tbody id=container1>
		  </tbody>
		  <tfoot>
		  </tfoot>
		 </table>
		</div>
	  </fieldset>
	 </fieldset> 	 
	 ";
//ambil daftar pengobatan dengan tahun sekarang
if($_SESSION['empl']['lokasitugas']=='MJHO'){
    $str2="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, a.notransaksi as notransaksi,
          a.karyawanid as karyawanid,a.kodebiaya as kodebiaya,a.keterangan as keterangan,
          c.lokasitugas as lokasitugas,a.tahunplafon as thnplafon,a.periode as periode,
          b.id as rs,a.jasars as byrs,a.jasadr as bydr, a.jasalab as bylab,a.byobat as byobat,
          a.bypendaftaran as byadmin,a.ygsakit as ygsakit,a.tanggal as tanggal,a.totalklaim as totalklaim,
          a.jlhhariistirahat as istirahat,a.bebankaryawan as bebankaryawan,a.bebanjamsostek as bebanjamsostek,
          a.bebanperusahaan as bebanperusahaan,a.diagnosa as diagnosa,a.klaimoleh as klaim
          from ".$dbname.".sdm_pengobatanht a 
          left join ".$dbname.".sdm_5rs b on a.rs=b.id 
          left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
          left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id
          where a.periode='".date('Y-m')."'  
          and (c.tipekaryawan in ('0','7','8') or c.alokasi=1)
          order by a.updatetime desc, a.tanggal desc";
//    where a.periode='".date('Y-m')."' and (c.tanggalkeluar = '0000-00-00' or c.tanggalkeluar > '".date("Y-m-d")."') 
}
else{
    $str2="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag, a.notransaksi as notransaksi,
          a.karyawanid as karyawanid,a.kodebiaya as kodebiaya,a.keterangan as keterangan,
          c.lokasitugas as lokasitugas,a.tahunplafon as thnplafon,a.periode as periode,
          b.id as rs,a.jasars as byrs,a.jasadr as bydr, a.jasalab as bylab,a.byobat as byobat,
          a.bypendaftaran as byadmin,a.ygsakit as ygsakit,a.tanggal as tanggal,a.totalklaim as totalklaim,
          a.jlhhariistirahat as istirahat,a.bebankaryawan as bebankaryawan,a.bebanjamsostek as bebanjamsostek,
          a.bebanperusahaan as bebanperusahaan,a.diagnosa as diagnosa,a.klaimoleh as klaim
          from ".$dbname.".sdm_pengobatanht a 
          left join ".$dbname.".sdm_5rs b on a.rs=b.id 
          left join ".$dbname.".datakaryawan c on a.karyawanid=c.karyawanid
          left join ".$dbname.".sdm_5diagnosa d on a.diagnosa=d.id
          where a.periode='".date('Y-m')."' 
          and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
          order by a.updatetime desc, a.tanggal desc";
}
//exit("error: ".$str2);
$res2=mysql_query($str2) or die(mysql_error());

$arr[2]="<fieldset>
	  <legend>".$_SESSION['lang']['list']."</legend>
      <div style='width:900px;height:350px;overflow:scroll;'>
	  ".$_SESSION['lang']['periode'].":<select id=optplafon onchange=loadPengobatan(this.options[this.selectedIndex].value)>".$optperiode."</select>
                       <img src='images/excel.jpg' onclick='printRekapKlaim()' class='resicon'>
	  <table class=sortable cellspacing=1 border=0 width=1500px>
	  <thead>
	    <tr class=rowheader>
                  <td align=center width=55>".$_SESSION['lang']['action']."</td>
		  <td align=center>No</td>
		  <td align=center width=100>".$_SESSION['lang']['notransaksi']."</td>
		  <td align=center width=50>".$_SESSION['lang']['periode']."</td>
		  <td align=center width=30>".$_SESSION['lang']['tanggal']."</td>
		  <td align=center width=200>".$_SESSION['lang']['namakaryawan']."</td>
		  <td align=center width=150>".$_SESSION['lang']['rumahsakit']."</td>
		  <td align=center width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		  <td align=center width=90>".$_SESSION['lang']['nilaiklaim']."</td>
                  <td align=center width=90>".$_SESSION['lang']['dibayar']."</td>
                  <td align=center width=90>".$_SESSION['lang']['perusahaan']."</td>
                  <td align=center width=90>".$_SESSION['lang']['karyawan']."</td>
                  <td align=center width=90>Jamsostek</td>    
		  <td align=center>".$_SESSION['lang']['diagnosa']."</td>
		  <td align=center>".$_SESSION['lang']['keterangan']."</td>
		</tr>
	  </thead>
	  <tbody id='container'>";
	  $no=0;
	  while($bar2=mysql_fetch_object($res2))
	  {
	   $no+=1;
	   $arr[2].="<tr class=rowcontent>
	   <td align=center>";
	   
	   if($bar2->posting==0)
	   {
               $ket=rawurlencode($bar2->keterangan);
               $arr[2].="<img src=images/edit.png title='edit' class=resicon onclick=editPengobatan('".$bar2->notransaksi."','".$bar2->karyawanid."','".$bar2->kodebiaya."','".$bar2->lokasitugas."','".$bar2->thnplafon."','".$bar2->periode."','".$bar2->rs."','".$bar2->byrs."','".$bar2->bydr."','".$bar2->bylab."','".$bar2->byobat."','".$bar2->byadmin."','".$bar2->ygsakit."','".$bar2->diagnosa."','".$bar2->tanggal."','".$bar2->totalklaim."','".$bar2->istirahat."','".$bar2->bebankaryawan."','".$bar2->bebanjamsostek."','".$bar2->bebanperusahaan."','".$bar2->klaim."','".$ket."')>";
               $arr[2].="&nbsp<img src=images/close.png class=resicon  title='delete' onclick=deletePengobatan('".$bar2->notransaksi."')>";
           }
	     $arr[2].="&nbsp<img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan('".$bar2->notransaksi."',event)>";
	   
	   $arr[2].="</td><td>".$no."</td>
		  <td>".$bar2->notransaksi."</td>
		  <td>".substr($bar2->periode,5,2)."-".substr($bar2->periode,0,4)."</td>
		  <td>".tanggalnormal($bar2->tanggal)."</td>
		  <td>".$bar2->namakaryawan."</td>
		  <td>".$bar2->namars."[".$bar2->kota."]"."</td>
		  <td>".$bar2->kodebiaya."</td>
		  <td align=right>".number_format($bar2->totalklaim,2,'.',',')."</td>
		  <td align=right>".number_format($bar2->jlhbayar,2,'.',',')."</td>
                  <td align=right>".number_format($bar2->bebanperusahaan,2,'.',',')."</td>
                  <td align=right>".number_format($bar2->bebankaryawan,2,'.',',')."</td>
                  <td align=right>".number_format($bar2->bebanjamsostek,2,'.',',')."</td>    
		  <td>".$bar2->ketdiag."</td>
		  <td>".$bar2->keterangan."</td>
		</tr>";	  	
	  }
$arr[2].="</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </div>
	 </fieldset><iframe id=frmku frameborder=0 style='width:0px;height:0px;'></iframe>	 
	 ";	 
$hfrm[0]=$_SESSION['lang']['form'];
$hfrm[1]=$_SESSION['lang']['obatobat'];
$hfrm[2]=$_SESSION['lang']['list'];
//draw tab, jangan ganti parameter pertama, krn dipakai di javascript
drawTab('FRM',$hfrm,$arr,100,900);

?>