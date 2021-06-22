<?php 
require_once('master_validation.php');
//include('lib/nangkoelib_erpold.php');
include 'lib/eagrolib.php';
echo open_body();
?>
<script language='javascript' src='js/master_barang.js'></script>
<script language='javascript' src='js/jquery-1.4.2.min.js'></script>
<script>
function validate1() {
	var allowedFiles = [".jpg", ".jpeg", ".png"];
        var fileUpload = document.getElementById("file1");
        var lblError = document.getElementById("file_error1");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.value.toLowerCase())) {
            lblError.innerHTML = "Please upload files having extensions: <b>" + allowedFiles.join(', ') + "</b> only.";
            document.getElementById("mybtn").disabled = true;
			return false;
        }
        lblError.innerHTML = "";
		
	$('#file_error1').html('');
	var file_size1 = $('#file1')[0].files[0].size;
	if(file_size1>1000000) {
		$('#file_error1').html('<?php echo $_SESSION['lang']['tampakdepan'];?> max 1 MB');
		document.getElementById("mybtn").disabled = true;
		return false;
	} 
	document.getElementById("mybtn").disabled = false;	
	return true;	
}

function validate2() {
	var allowedFiles = [".jpg", ".jpeg", ".png"];
        var fileUpload = document.getElementById("file2");
        var lblError = document.getElementById("file_error2");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.value.toLowerCase())) {
            lblError.innerHTML = "Please upload files having extensions: <b>" + allowedFiles.join(', ') + "</b> only.";
			document.getElementById("mybtn").disabled = true;	
            return false;
        }
        lblError.innerHTML = "";
		
	$('#file_error2').html('');
	var file_size2 = $('#file2')[0].files[0].size;
	if(file_size2>1000000) {
		$('#file_error2').html('<?php echo $_SESSION['lang']['tampaksamping'];?> max 1 MB');
		document.getElementById("mybtn").disabled = true;
		return false;
	} 
	document.getElementById("mybtn").disabled = false;
	return true;	
}

function validate3() {
	var allowedFiles = [".jpg", ".jpeg", ".png"];
        var fileUpload = document.getElementById("file3");
        var lblError = document.getElementById("file_error3");
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
        if (!regex.test(fileUpload.value.toLowerCase())) {
            lblError.innerHTML = "Please upload files having extensions: <b>" + allowedFiles.join(', ') + "</b> only.";
			document.getElementById("mybtn").disabled = true;	
            return false;
        }
        lblError.innerHTML = "";
	
	$('#file_error3').html('');
	var file_size3 = $('#file3')[0].files[0].size;
	if(file_size3>1000000) {
		$('#file_error3').html('<?php echo $_SESSION['lang']['tampakatas'];?> max 1 MB');
		document.getElementById("mybtn").disabled = true;
		return false;
	} 
	document.getElementById("mybtn").disabled = false;
	return true;	
}
</script>

<?php 
include('master_mainMenu.php');
OPEN_BOX();
//pengambilan kelompok barang dari table kelompok barang
$strkel="select kode, kelompok from ".$dbname.".log_5klbarang order by kode asc";
$reskel=mysql_query($strkel);
$optkelompok="<option value=''></option>";
while($bar=mysql_fetch_object($reskel)){
	$optkelompok.="<option value='".$bar->kode."'>".$bar->kode." | ".$bar->kelompok."</option>";
}

//pengambilan satuan dari table setup_satuan
$str="select distinct satuan from ".$dbname.".setup_satuan order by satuan asc";
$res=mysql_query($str);
$optsatuan="<option value=''></option>";
while($bar=mysql_fetch_object($res)){
	$optsatuan.="<option value='".$bar->satuan."'>".$bar->satuan."</option>";
}

//pengambilan inventory code dari table setup 5 parameter
$strinv="select kode, nama from ".$dbname.".setup_5parameter where flag='inv'";
$resinv=mysql_query($strinv);
$optinv="<option value=''></option>";
while($bar=mysql_fetch_object($resinv)){
	$optinv.="<option value='".$bar->kode."'>".$bar->kode." - ".$bar->nama."</option>";
}
?>

<fieldset>
<legend><b><?php echo $_SESSION['lang']['materialmaster'];?></b></legend>	
<table>
<tr>
<td width='80px'><img class='delliconBig' onclick='baru();' src='images/newfile.png' title='<?php echo $_SESSION['lang']['new'];?>'></td>
<td><img class='delliconBig' onclick='listdata();' src='images/orgicon.png' title='<?php echo $_SESSION['lang']['list'];?>'></td>
</tr>
<tr>
<td><?php echo $_SESSION['lang']['new'];?></td>
<td><?php echo $_SESSION['lang']['list'];?></td>
</tr>
</table>		
</fieldset>

<fieldset id='form' style='display:none;'>
<legend><b>Form</b></legend>
<div style='float:left;'>
<table border="0" cellspacing="0">
  <tr>
    <td><span id='a2'><?php echo $_SESSION['lang']['materialgroupcode'];?></span>  <img src='images/obl.png'/>
    </td>    
	<td><select id="kelompokbarang" onchange='getMaterialNumber(this.options[this.selectedIndex].value)'><?php echo $optkelompok;?></select></td>	
  </tr>
  <tr>
    <td><span id='a3'><?php echo $_SESSION['lang']['materialcode'];?></span></td>
    <td><input type='text' class='myinputtext' id="kodebarang" disabled='disabled' size='20'></td>
  </tr>
  <tr>
    <td><span id='a4'><?php echo $_SESSION['lang']['materialname'];?></span> <img src='images/obl.png'/></td>
	<td><input type="text" style='text-transform:uppercase;' class='myinputtext' id="namabarang" size='45' maxlength='70' onkeypress="return tanpa_kutip(event)"></td>
  </tr>
  <tr>
    <td><span id='a5'><?php echo $_SESSION['lang']['satuan'];?></span> <img src='images/obl.png'/></td>
    <td><select id="satuan"><?php echo $optsatuan;?></select></td>
  </tr>
  <tr>
    <td><span id='a6'><?php echo $_SESSION['lang']['kodeorder'];?></span></td>
	<td><input type="text" class='myinputtext' id="kodeorder" size='45'></td>
  </tr>  
  <tr>
    <td><span id='a7'><?php echo $_SESSION['lang']['merk'];?></span></td>
	<td><input type="text" class='myinputtext' id="merk" size='45'></td>
  </tr> 
  <tr>
    <td><span id='a8'><?php echo $_SESSION['lang']['partnumber'];?></span></td>
	<td><input type="text" class='myinputtext' id="partnumber" size='45'></td>
  </tr>  
  <tr>
  <td></td>
  <td>
	<button class='mybutton' onclick='simpanBarangBaru()'><?echo $_SESSION['lang']['save'];?></button>
	<button class='mybutton' onclick='cancelBarang()'><?echo $_SESSION['lang']['cancel'];?></button>  
  </td>
  </tr>  
</table>
</div>
<div style='float:left;'>
<table border="0" cellspacing="0">
  <tr>
    <td><span id='a9'>Inventori</span></td>
	<td>
		<select id='invkode'><?php echo $optinv;?></select>
	</td>
  </tr>    
  <tr>
    <td><span id='a10'><?php echo $_SESSION['lang']['minstok'];?></span></td>
	<td><input type="text" class='myinputtextnumber' id="minstok" value=0 size=4 maxlength=4 onkeypress="return angka_doang(event)"></td>
  </tr>  
  <tr>
    <td><span id='a11'><?php echo $_SESSION['lang']['nokartubin'];?></span></td>
	<td><input type="text" class='myinputtext' id="nokartu" size=10 maxlength=10 onkeypress="return tanpa_kutip(event)"></td>
  </tr>  
  <tr>
    <td><span id='a12'><?php echo $_SESSION['lang']['keterangan'];?></span></td>
	<td><textarea id='keterangan' cols='30' rows='5'></textarea></td>
  </tr>    
  <input type='hidden' value='insert' id='method'>
  <input type='hidden' value='0' id='konversi'>
</table>

</div>
</fieldset>

<fieldset style='display:none;' id='upload'>
<legend><b>Upload</b></legend>
<span id='file_error1' style='color:blue;'></span>
<br/>
<span id='file_error2' style='color:blue;'></span>
<br/>
<span id='file_error3' style='color:blue;'></span>
<div style='float:left;'>
<table>
<tr>
	<td><span id='lbl_depan'><?php echo $_SESSION['lang']['tampakdepan'];?></span> <img src='images/obl.png'/></td>
	<td>:</td>
	<td>
		<input type='text' size='40' id='file1'/></textarea>
	</td>
</tr>	
<tr>	   
	<td><?php echo $_SESSION['lang']['tampaksamping'];?></td>
	<td>:</td>
	<td>
	   <input type='text' size='40' id='file2'/>
	</td>
</tr>	
<tr>
	<td><?php echo $_SESSION['lang']['tampakatas'];?></td>
	<td>:</td>
	<td>
	   <input type='text' size='40' id='file3'/>
	   <input type='hidden' name='kdbrg' id='kodebarangx'>
	   <input type='hidden' name='statimg' id='statimg'>
	</td>						
</tr>
<tr>
<td><?php echo $_SESSION['lang']['spesifikasi'];?></td>
<td>:</td>
<td><textarea name='spec' id='spec' cols='20' rows='3' onkeypress='return parent.tanpa_kutip(event)'></textarea></td>
</tr>
<tr>
<td></td>
<td></td>
<td>&nbsp;&nbsp;<button id="mybtn" onclick='saveimage()'><?php echo $_SESSION['lang']['save'];?></button></td>
</tr>
</table>
</div>
<div style='float:left;'>
<table>
<tr> 
	<td>Link URL 1</td>
	<td>:</td>
	<td><input type='text' size='40' id='link1'/></td>
</tr>	   
<tr> 
	<td>Link URL 2</td>
	<td>:</td>
	<td><input type='text' size='40' id='link2'/></textarea></td>
</tr>	   
<tr> 
	<td>Link URL 3</td>
	<td>:</td>
	<td><input type='text' size='40' id='link3'/></td>
</tr>	   
<tr> 
	<td>Link URL 4</td>
	<td>:</td>
	<td><input type='text' size='40' id='link4'/></td>
</tr>	
</table>
</div>
</fieldset>

<fieldset id='listdata'>
<legend><b><?php echo $_SESSION['lang']['list']; ?></b></legend>
<?php echo $_SESSION['lang']['find'];?> :
<select id='filter'>
<option value='namabarang'><?php echo $_SESSION['lang']['materialname'];?></option>
<option value='kelompokbarang'><?php echo $_SESSION['lang']['materialgroupcode'];?></option>
<option value='kodebarang'><?php echo $_SESSION['lang']['materialcode'];?></option>
<option value='satuan'><?php echo $_SESSION['lang']['satuan'];?></option>
<option value='kodeorder'><?php echo $_SESSION['lang']['kodeorder'];?></option>
<option value='merk'><?php echo $_SESSION['lang']['merk'];?></option>
<option value='partnumber'><?php echo $_SESSION['lang']['partnumber'];?></option>
</select>
<?php echo $_SESSION['lang']['keyword'];?> : <input type='text' id='txtcari' class='myinputtext' size='40'>
<button class='mybutton' onclick='cariBarang()'><?php echo $_SESSION['lang']['find'];?></button>
<br/><br/>
      <table cellspacing='1' border='0' class='sortable'>
      <thead>
	  <tr class='rowheader'>
	  <td>No</td>
	  <td align='center'><?php echo $_SESSION['lang']['materialgroupcode'];?></td>
	  <td><?php echo $_SESSION['lang']['materialcode'];?></td>
	  <td><?php echo $_SESSION['lang']['materialname'];?></td>
	  <td><?php echo $_SESSION['lang']['satuan'];?></td>
	  <td><?php echo $_SESSION['lang']['kodeorder'];?></td>
	  <td><?php echo $_SESSION['lang']['merk'];?></td>
	  <td><?php echo $_SESSION['lang']['partnumber'];?></td>
	  <td><?php echo $_SESSION['lang']['keterangan'];?></td>
	  <td align='center'><?php echo $_SESSION['lang']['minstok'];?></td>
	  <td align='center'><?php echo $_SESSION['lang']['nokartubin'];?></td>
	  <td>Inventori</td>
	  <td><?php echo $_SESSION['lang']['tidakaktif'];?></td> 
	  <td><?php echo $_SESSION['lang']['action'];?></td>
	  </tr>
	  </thead>
	  <tbody id='contain'>
	  <script>loadData();</script>
	  </tbody>
	  <tfoot>
	  </tfoot>
	  </table>	
</fieldset>
<?php 
CLOSE_BOX();
echo CLOSE_BODY();
?>