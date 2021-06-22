/**
 * @author Developer
 */

// START AUTOCOMPLETE KODE ORGANISASI //
function CariKdO(title,ev){
	content= "<div>";
	content+="<fieldset>Kode Organisasi:<input type=text id=textkdo class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariKdO()>Go</button> </fieldset>";
	content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
	title=title+' Kode Organisasi:';
	width='500';
	height='300';
	showDialog1(title,content,width,height,ev);		   
}

function goCariKdO(){
	var KdO 	= trim(document.getElementById('textkdo').value); 
	var param	= 'KdO='+KdO;
	var tujuan 	= 'log_slave_cariKdO.php';
	post_response_text(tujuan, param, respog);			

	function respog(){
		if (con.readyState == 4) {
				if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
								document.getElementById('containercari').innerHTML=con.responseText;
						}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
		}
	}	
}

function goPickKdO(KdO){
	document.getElementById('kd_organisasi').value = KdO;
	closeDialog();
}
// END AUTOCOMPLETE KODE ORGANISASI //

// START AUTOCOMPLETE KODE UNIT //
function CariKdU(title,ev){
	content= "<div>";
	content+="<fieldset>Kode Unit:<input type=text id=textkdu class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariKdU()>Go</button> </fieldset>";
	content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
	title=title+' Kode Organisasi:';
	width='500';
	height='300';
	showDialog1(title,content,width,height,ev);		   
}

function goCariKdU(){
	var KdU 	= trim(document.getElementById('textkdu').value); 
	var param	= 'KdU='+KdU;
	var tujuan 	= 'log_slave_cariKdU.php';
	post_response_text(tujuan, param, respog);			

	function respog(){
		if (con.readyState == 4) {
				if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
								document.getElementById('containercari').innerHTML=con.responseText;
						}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
		}
	}	
}

function goPickKdU(KdU){
	document.getElementById('kd_unit').value = KdU;
	closeDialog();
}
// END AUTOCOMPLETE KODE UNIT //

// START SAVE DATA ENTRY SHIFT //
function sEntryShift(){
	// GET DATA SHIFT ENTRY
	var kode 			= trim(document.getElementById('kodeshift').value);
	var nama 			= trim(document.getElementById('namashift').value);
	var jam_masuk 		= trim(document.getElementById('jam_masuk').value);
	var jam_keluar 		= trim(document.getElementById('jam_keluar').value);
	var aktif 			= document.getElementById('aktifshift');
	aktif				= aktif.options[aktif.selectedIndex].value;
	var kd_organisasi	= trim(document.getElementById('kd_organisasi').value);
	var kd_unit 		= trim(document.getElementById('kd_unit').value);
	var tgl_start 		= trim(document.getElementById('tgl_start').value);
	var tgl_end 		= trim(document.getElementById('tgl_end').value);
	var id				= trim(document.getElementById('id').value);
	if(id == 0){
		var method		= 'insert';
	} else {
		var method		= 'update';
	}
	
	
	if(kode == '' || kode == null) {
		alert('Kode Shift is obligatory');
		return false;
	} else if(nama == '' || nama == null){
		alert('Nama Shift is obligatory');
		return false;
	} else if(jam_masuk == '' || jam_masuk == null){
		alert('Jam Masuk is obligatory');
		return false;
	} else if(jam_keluar == '' || jam_keluar == null){
		alert('Jam Keluar is obligatory');
		return false;
	} else if(aktif == '' || aktif == null){
		alert('Aktive Shift is obligatory');
		return false;
	} else if(kd_organisasi == '' || kd_organisasi == null){
		alert('Kode Organisasi is obligatory');
		return false;
	} else if(kd_unit == '' || kd_unit == null){
		alert('Kode Unit is obligatory');
		return false;
	} else if(tgl_start == '' || tgl_start == null){
		alert('Tanggal Start is obligatory');
		return false;
	} else if(tgl_end == '' || tgl_end == null){
		alert('Tanggal End is obligatory');
		return false;
	}
	
	var param = 'kode='+kode+'&nama='+nama+'&jam_masuk='+jam_masuk;
	param+='&jam_keluar='+jam_keluar+'&aktif='+aktif+'&kd_organisasi='+kd_organisasi;	
	param+='&kd_unit='+kd_unit+'&tgl_start='+tgl_start+'&tgl_end='+tgl_end+'&method='+method+'&id='+id;	
	tujuan='sdm_slave_save_entry_shift.php';
	if(confirm('Apakah Anda yakin akan menyimpan data ini ?')) {post_response_text(tujuan, param, respog);}		
	function respog() {
		if(con.readyState==4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
				else {
					alert('Selesai');
					//alert(con.responseText);
					location.reload();
					//pendi();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}	
}
// END SAVE DATA ENTRY SHIFT //

// START EDIT DATA ENTRY SHIFT //
function eEntryShift(no){
	document.getElementById("kodeshift").value = trim(document.getElementById('kode'+no).value);
	document.getElementById("namashift").value = trim(document.getElementById('nama'+no).value);
	document.getElementById("jam_masuk").value = trim(document.getElementById('jam_masuk'+no).value);
	document.getElementById("jam_keluar").value = trim(document.getElementById('jam_keluar'+no).value);
	document.getElementById("aktifshift").value = trim(document.getElementById('aktif'+no).value);
	document.getElementById("kd_organisasi").value = trim(document.getElementById('kd_organisasi'+no).value);
	document.getElementById("kd_unit").value = trim(document.getElementById('kd_unit'+no).value);
	document.getElementById("tgl_start").value = trim(document.getElementById('tgl_start'+no).value);
	document.getElementById("tgl_end").value = trim(document.getElementById('tgl_end'+no).value);
	document.getElementById("id").value = trim(document.getElementById('id'+no).value);
	document.getElementById('namashift').disabled = true;
}
// END EDIT DATA ENTRY SHIFT //

// START CANCEL DATA ENTRY SHIFT //
function cEntryShift(){
	document.getElementById("kodeshift").value = "";
	document.getElementById("namashift").value = "";
	document.getElementById("jam_masuk").value = "";
	document.getElementById("jam_keluar").value = "";
	document.getElementById("aktifshift").value = "";
	document.getElementById("kd_organisasi").value = "";
	document.getElementById("kd_unit").value = "";
	document.getElementById("tgl_start").value = "";
	document.getElementById("tgl_end").value = "";
	document.getElementById("id").value = "0";
}
// END CANCEL DATA ENTRY SHIFT //

// START DELETE DATA ENTRY SHIFT //
function dEntryShift(no){
	var kode 	= trim(document.getElementById('kode'+no).value);
	var id 		= trim(document.getElementById('id'+no).value);
	var method	= 'delete';
	
	var param = 'method='+method+'&id='+id;	
	tujuan='sdm_slave_save_entry_shift.php';
	if(confirm('Delete data '+kode+', are you sure ?')) {post_response_text(tujuan, param, respog);}	
	function respog() {
		if(con.readyState==4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
				else {
					alert('Suksess');
					//alert(con.responseText);
					location.reload();
					//pendi();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}
}
// END DELETE DATA ENTRY SHIFT //




 
function getLaporanFisik2(){
    unitDt=document.getElementById('unitDt');
    gudang  =document.getElementById('gudang2');
    periode =document.getElementById('periode2');
    unitDt=unitDt.options[unitDt.selectedIndex].value;
    gudang	=gudang.options[gudang.selectedIndex].value;
    periode	=periode.options[periode.selectedIndex].value;
	if(periode == '') {
		alert('Periode is obligatory');
	} else {
		param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
		param+='&proses=preview';
		tujuan='log_slaveStokOpname.php';
		post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel2');
						document.getElementById('container').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	}		
}

function CalculateBalance(no) {
    var SO = document.getElementById("unitso"+no).value;
	var Saldo = document.getElementById("saldo"+no).value;
	var Balance = Number(Saldo) - Number(SO);
	document.getElementById("balance"+no).innerHTML = Balance.toFixed(2);
	document.getElementById("qtybalanced"+no).value = Balance;
}

function SaveData(){
	// GET DATA HEADER
	var tanggal 	= trim(document.getElementById('tgl01').value);
	var nostokopname= trim(document.getElementById('nostokopname').value);
	var note		= trim(document.getElementById('keterangan').value);
	var kdunit		= trim(document.getElementById('unitDt').value);
	var kdgudang	= trim(document.getElementById('gudang2').value);
	var periode		= trim(document.getElementById('periode2').value);
	var usrapv		= '';
	var usrapvdt	= '';
	var usrcrt		= '';
	var method		= 'insert';
	
	// GET DATA DETAILS
	var TotalRecord = trim(document.getElementById('TotalData').value);
	var GetDetails = new Array(TotalRecord);
	
	for(var c = 1; c <= TotalRecord; c++) {		
		GetDetails[c-1] = {
			'seqno' : document.getElementById('seqno'+c).value,
			'kdbarang' : document.getElementById('kdbarang'+c).value,
			'kdsatuan' : document.getElementById('kdsatuan'+c).value,
			'qtysaldo' : document.getElementById('qtysaldo'+c).value,
			'qtyso' : document.getElementById('unitso'+c).value,
			'qtybalance' : document.getElementById('qtybalanced'+c).value
		}
	}
	
	if(tanggal == '') {
		alert('Tanggal is obligatory');
	} else if(nostokopname == '' || nostokopname == null) {
		alert('No Stokopname is obligatory');
	} else if(periode == ''){
		alert('Periode is obligatory');
	}
	else {
		param='tanggal='+tanggal+'&nostokopname='+nostokopname+'&note='+note;
		param+='&kdunit='+kdunit+'&kdgudang='+kdgudang+'&periode='+periode;	
		param+='&method='+method+'&details='+JSON.stringify(GetDetails);	
		tujuan='log_slave_save_stokopname.php';
		if(confirm('Saving data for '+nostokopname+', are you sure ?')) {post_response_text(tujuan, param, respog);}		
		function respog() {
			if(con.readyState==4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
					else {
						alert('Suksess');
						//alert(con.responseText);
						document.getElementById("buttonsave").disabled = true;
						//pendi();
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}	
		}	
	}
}

function fisikKeExcel2(ev,tujuan) {
	judul='Report Entry Shift';	
	param='proses=excel';
	printFile(param,tujuan,judul,ev);
}

function fisikKePDF2(ev,tujuan){
	judul='Report Entry Shift';	
	param='proses=pdf';
	printFile(param,tujuan,judul,ev);
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}