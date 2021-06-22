<?
require_once('config/connection.php');
require_once('master_validation.php');
include('lib/nangkoelib.php');
#showerror();
echo open_body_hrd();
include('master_mainMenu.php');
OPEN_BOX_HRD('','<b> Input '.$_SESSION['lang']['mastershift'].'</b>');
clearstatcache();
## GET LIST SHIFT ##
//tambah filter lokasi tugas ==Jo 06-05-2017==
if($_SESSION['empl']['pusat']==1){
	$whrorg="";
}
else{
	$whrorg="where kd_organisasi ='".$_SESSION['empl']['lokasitugas']."'";
}
$sShift	= "select * from ".$dbname.".sdm_shift ".$whrorg." order by kd_organisasi asc, nama asc";
$qShift	= mysql_query($sShift) or die(mysql_error($conn));
$nShift = 1;

function putertanggal($tanggal)
{
    $qwe=explode('-',$tanggal);
    return $qwe[2].'-'.$qwe[1].'-'.$qwe[0];
} 
?>
<script language=javascript1.2 src='js/sdm_entry_shift.js'></script>
<fieldset>
	<table>
		<tr>
			<td><? echo $_SESSION['lang']['namashift'] ?> <img src='images/obl.png'/></td>
			<td><input type=text class=myinputtext id=namashift size=20 maxlength=20></td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['keterangan'] ?> <img src='images/obl.png'/></td>
			<td><input type=text class=myinputtext id=kodeshift size=20 maxlength=20></td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['jammasukkeluar'] ?> <img src='images/obl.png'/></td>
			<td>
				<input name=jam_masuk type=text class=myinputtext id=jam_masuk size=9 maxlength=10>&nbsp;&nbsp;&nbsp;-
				<input name=jam_keluar type=text class=myinputtext id=jam_keluar size=9 maxlength=10>
			</td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['aktifshift'] ?> <img src='images/obl.png'/></td>
			<td><select class=myinputtext id=aktifshift style="width:100px;">
				<option value=''><? echo $_SESSION['lang']['pilihdata']?></option>
				<option value='Y'>Ya</option>
				<option value="N">Tidak</option>
			</select></td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['kd_organisasi'] ?> <img src='images/obl.png'/></td>
			<td>
				<input type=text class=myinputtext id=kd_organisasi size=20 maxlength=20>
				<img src=images/zoom.png title='<? echo $_SESSION['lang']['find'] ?>' class=resicon onclick=CariKdO('<? echo $_SESSION['lang']['find'] ?>',event)>
			</td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['kd_unit'] ?> <img src='images/obl.png'/></td>
			<td>
				<input type=text class=myinputtext id=kd_unit size=20 maxlength=20>
				<img src=images/zoom.png title='<? echo $_SESSION['lang']['find'] ?>' class=resicon onclick=CariKdU('<? echo $_SESSION['lang']['find'] ?>',event)>
			</td>
		</tr>
		<tr>
			<td><? echo $_SESSION['lang']['tglmulaiakhir'] ?> <img src='images/obl.png'/></td>
			<td>
				<input name=jam_masuk type=text class=myinputtext id=tgl_start onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>&nbsp;&nbsp;&nbsp;-
				<input name=jam_keluar type=text class=myinputtext id=tgl_end onmousemove=setCalendar(this.id); onkeypress=\"return false;\" size=9 maxlength=10>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<button class=mybutton onclick=sEntryShift()><? echo $_SESSION['lang']['save'] ?></button>
				<button class=mybutton onclick=cEntryShift()><? echo $_SESSION['lang']['cancel'] ?></button>		
			</td>
		</tr>
			 <input type=hidden class=myinputtext id=id value='0'>
	</table>
</fieldset>

<fieldset>
	<legend>
		<? echo $_SESSION['lang']['listshift'] ?>
		<span id=printPanel2>
			<img onclick=fisikKeExcel2(event,'sdm_slave_entry_shift.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
			<img onclick=fisikKePDF2(event,'sdm_slave_entry_shift.php') title='PDF' class=resicon src=images/pdf.jpg>
		</span>
	</legend>
	<table class=sortable cellspacing=1 border=1 width=100%>
	<thead>
		<tr>
			<td align=center>No.</td>
			<td align=center><? echo $_SESSION['lang']['namashift'] ?></td>
			<td align=center><? echo $_SESSION['lang']['jammasuk'] ?></td>
			<td align=center><? echo $_SESSION['lang']['jamkeluar'] ?></td>
			<td align=center><? echo $_SESSION['lang']['aktifshift'] ?></td>
			<td align=center><? echo $_SESSION['lang']['kd_organisasi'] ?></td>
			<td align=center><? echo $_SESSION['lang']['kd_unit'] ?></td>
			<td align=center><? echo $_SESSION['lang']['tgl_start'] ?></td>
			<td align=center><? echo $_SESSION['lang']['tgl_akhir'] ?></td>
			<td align=center><? echo $_SESSION['lang']['keterangan'] ?></td>
			<td align=center colspan="2"><? echo $_SESSION['lang']['action'] ?></td>
		</tr>  
	</thead>
	<tbody id=container class=rowcontent> 
		<? while($rShift = mysql_fetch_assoc($qShift)){ 
		$Aktif = ($rShift['aktif'] == 'Y') ? "Ya" : "Tidak";
		?>
		<tr>
			<td align=center><? echo $nShift; ?></td>	
			<td align=center>
				<? echo $rShift['nama'] ?>
				<input type="hidden" id="nama<? echo $nShift ?>" value="<? echo $rShift['nama'] ?>" disabled>
			</td>
			<td align=center>
				<? echo $rShift['jam_masuk'] ?>
				<input type="hidden" id="jam_masuk<? echo $nShift ?>" value="<? echo $rShift['jam_masuk'] ?>">
			</td>
			<td align=center>
				<? echo $rShift['jam_keluar'] ?>
				<input type="hidden" id="jam_keluar<? echo $nShift ?>" value="<? echo $rShift['jam_keluar'] ?>">
			</td>
			<td align=center>
				<? echo $Aktif ?>
				<input type="hidden" id="aktif<? echo $nShift ?>" value="<? echo $rShift['aktif'] ?>">
			</td>
			<td align=center>
				<? echo $rShift['kd_organisasi'] ?>
				<input type="hidden" id="kd_organisasi<? echo $nShift ?>" value="<? echo $rShift['kd_organisasi'] ?>">
			</td>
			<td align=center>
				<? echo $rShift['kd_unit'] ?>
				<input type="hidden" id="kd_unit<? echo $nShift ?>" value="<? echo $rShift['kd_unit'] ?>">
			</td>
			<td align=center>
				<? $StartDate = putertanggal($rShift['tgl_start']); ?>
				<? echo $StartDate ?>
				<input type="hidden" id="tgl_start<? echo $nShift ?>" value="<? echo $StartDate ?>">
			</td>
			<td align=center>
				<? $EndDate = putertanggal($rShift['tgl_end']); ?>
				<? echo $EndDate ?>
				<input type="hidden" id="tgl_end<? echo $nShift ?>" value="<? echo $EndDate ?>">
			</td>
			<td align=center>
				<? echo $rShift['kode'] ?>
				<input type="hidden" id="kode<? echo $nShift ?>" value="<? echo $rShift['kode'] ?>" disabled>
			</td>
			<input type="hidden" id="id<? echo $nShift ?>" value="<? echo $rShift['id'] ?>">
			<td align=center><img id="editRow0" title="Edit" onclick="eEntryShift(<? echo $nShift; ?>)" class="zImgBtn" src="images/001_45.png"></td>
			<!--<td align=center><img id="delRow0" title="Hapus" onclick="dEntryShift(<? echo $nShift; ?>)" class="zImgBtn" src="images/delete_32.png"></td>-->
		</tr>
		<? $nShift++;} ?>
	</tbody>
	<tfoot>	
	</tfoot>		 
	</table>
</fieldset>
<?
CLOSE_BOX_HRD();
echo close_body_hrd();
?>
