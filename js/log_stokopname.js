/**
 * @author Developer
 */
function getGudangDt() {
    unt = document.getElementById('unitDt').options[document.getElementById('unitDt').selectedIndex].value;
    param = 'unitDt='+unt+'&proses=getGudang';
    tujuan ='log_slaveStokOpname.php';
    post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
								document.getElementById('gudang2').innerHTML=con.responseText;
						}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
			}
		}
}

function getPeriodeGudang() {
    gudang = document.getElementById('gudang2').options[document.getElementById('gudang2').selectedIndex].value;
    param = 'gudang='+gudang+'&proses=getPeriodeGudang';
    tujuan ='log_slaveStokOpname.php';
    post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
								alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
								document.getElementById('periode2').innerHTML=con.responseText;
						}
				}
				else {
						busy_off();
						error_catch(con.status);
				}
			}
		}
}
 
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
					if (!isSaveResponse2(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('buttoncari');
						//showById('printPanel2');
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
			'nmbarang' : document.getElementById('nmbarang'+c).value,
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
						showById('printPanel2');
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

function UpdateData(){
	// GET DATA HEADER
	var reffno		= document.getElementById('reffno').value;
	//alert(reffno); return;
	var tanggal 	= trim(document.getElementById('tgl01').value);
	var nostokopname= trim(document.getElementById('nostokopname').value);
	var note		= trim(document.getElementById('keterangan').value);
	var kdunit		= trim(document.getElementById('unitDt').value);
	var kdgudang	= trim(document.getElementById('gudang2').value);
	var periode		= trim(document.getElementById('periode2').value);
	var usrapv		= '';
	var usrapvdt	= '';
	var usrcrt		= '';
	var method		= 'update';
	
	// GET DATA DETAILS
	var TotalRecord = trim(document.getElementById('TotalData').value);
	var GetDetails = new Array(TotalRecord);
	
	for(var c = 1; c <= TotalRecord; c++) {		
		GetDetails[c-1] = {
			'seqno' : document.getElementById('seqno'+c).value,
			'kdbarang' : document.getElementById('kdbarang'+c).value,
			'nmbarang' : document.getElementById('nmbarang'+c).value,
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
		param='reffno='+reffno+'&tanggal='+tanggal+'&nostokopname='+nostokopname+'&note='+note;
		param+='&kdunit='+kdunit+'&kdgudang='+kdgudang+'&periode='+periode;	
		param+='&method='+method+'&details='+JSON.stringify(GetDetails);	
		tujuan='log_slave_save_stokopname.php';
		if(confirm('Update data for '+nostokopname+', are you sure ?')) {post_response_text(tujuan, param, respog);}		
		function respog() {
			if(con.readyState==4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {alert('ERROR TRANSACTION,\n' + con.responseText);}
					else {
						alert('Data berhasil di update');
						showById('printPanel2');
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

function CariSO(title,ev){
	noso 	= '';
	unitDt	= trim(document.getElementById('unitDt').value);
	gudang	= trim(document.getElementById('gudang2').value);
	periode = trim(document.getElementById('periode2').value);
	
	param='noso='+noso+'&unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
	tujuan = 'log_slave_cariSo.php';
	post_response_text(tujuan, param, respog);	
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					content= "<div>";
					content+="<fieldset>No Stok Opname:<input type=text id=textso class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariSO()>Go</button> </fieldset>";
					content+="<div id=containercari style=\"height:400px;width:600px;overflow:scroll;\">";
					content+="</div></div>";
					title=title+' SO:';
					width='600';
					height='400';
					showDialog1(title,content,width,height,ev);	
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

function goCariSO(){
	noso	= trim(document.getElementById('textso').value);
	unitDt	= trim(document.getElementById('unitDt').value);
	gudang	= trim(document.getElementById('gudang2').value);
	periode = trim(document.getElementById('periode2').value);
	
	//if(noso.length<0)
	//   alert('Text too short');
	//else{   
	param	= 'noso='+noso+'&unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
	tujuan  = 'log_slave_cariSo.php';
	post_response_text(tujuan, param, respog);			
	//}
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

function goPickSo(id){
	var method = "GetDataSO";
	periode = trim(document.getElementById('periode2').value);
	
	$.ajax({
		url 		: 'log_slave_save_stokopname.php',
		type 		: "POST",
		data 		: 'method='+method+'&id='+id,
		dataType	: 'json', 
		success	: function(data){
			document.getElementById('tgl01').value=data['ht'][0]['tanggal'];
			document.getElementById('nostokopname').value=data['ht'][0]['nostokopname'];
			document.getElementById('keterangan').value=data['ht'][0]['note'];
			document.getElementById('reffno').value=data['ht'][0]['reffno'];
			
			var TotalData = data['dt'].length;
			var Total = 0;
			var tab = '';
			
			for(var i = 1; i <= TotalData; i++) {
				tab+="<tr class=rowcontent>";
				tab+="<td><input type='hidden' id='seqno"+i+"' name='seqno"+i+"' value='"+i+"'>"+i+"</td>";
				tab+="<input type='hidden' id='iddt"+i+"' name='iddt"+i+"' value='"+data['dt'][i-1]['id']+"'>";
				tab+="<td>"+data['ht'][0]['kdunit']+"</td>";
				tab+="<td>"+data['ht'][0]['kdgudang']+"</td>";
				tab+="<td>"+periode+"</td>";
				tab+="<td><input type='hidden' id='kdbarang"+i+"' name='kdbarang"+i+"' value='"+data['dt'][i-1]['kdbarang']+"'>"+data['dt'][i-1]['kdbarang']+"</td>";
				tab+="<td><input type='hidden' id='nmbarang"+i+"' name='nmbarang"+i+"' value='"+data['dt'][i-1]['nmbarang']+"'>"+data['dt'][i-1]['nmbarang']+"</td>";
				tab+="<td><input type='hidden' id='kdsatuan"+i+"' name='kdsatuan"+i+"' value='"+data['dt'][i-1]['kdsatuan']+"'>"+data['dt'][i-1]['kdsatuan']+"</td>";
				tab+="<td><input type='hidden' id='qtysaldo"+i+"' name='qtysaldo"+i+"' value='"+data['dt'][i-1]['qtysaldo']+"'>"+data['dt'][i-1]['qtysaldo']+"</td>";//saldo akhir  	
				tab+="<td><input type='number' onkeyup='CalculateBalance("+i+")' name='unitso"+i+"' id='unitso"+i+"' value='"+data['dt'][i-1]['qtyso']+"'></td>";//SO
				tab+="<td align=right width='100'><span name='balance"+i+"' id='balance"+i+"' value='"+data['dt'][i-1]['qtybalance']+"'>"+data['dt'][i-1]['qtybalance']+"</span></td>";//saldo keluar
				tab+="<input type='hidden' id='saldo"+i+"' name='saldo"+i+"' value='"+data['dt'][i-1]['qtysaldo']+"'>";
				tab+="<input type='hidden' id='qtybalanced"+i+"' name='qtybalanced"+i+"' value='"+data['dt'][i-1]['qtybalance']+"'>";
				tab+="</tr>";
			}
			tab+="<tr><td colspan=10><button id='buttonsave' class=mybutton onclick=UpdateData()>Update Data</button></td></tr>";
			tab+="<input type='hidden' id='TotalData' name='TotalData' value='"+TotalData+"'>";
			 
			showById('printPanel2');
			document.getElementById('container').innerHTML = tab;
			closeDialog();
		},
		error: function(xhr, error, dat){
			alert('Error');
		}
	});
	//document.getElementById('nostokopname').value=noso;
	//closeDialog();
}

function Clean(){
	document.getElementById('unitDt').value= '';
	document.getElementById('gudang2').value= '';
	document.getElementById('periode2').value= '';
	document.getElementById('tgl01').value= '';
	document.getElementById('nostokopname').value= '';
	document.getElementById('keterangan').value= '';
	document.getElementById('reffno').value= '';
	hideById('printPanel2');
	document.getElementById('container').innerHTML = '';
}

function fisikKeExcel2(ev,tujuan) {
    var unitDt	= document.getElementById('unitDt');
    var gudang  = document.getElementById('gudang2');
    var periode = document.getElementById('periode2');
    unitDt	= unitDt.options[unitDt.selectedIndex].value;
	gudang	= gudang.options[gudang.selectedIndex].value;
	periode	= periode.options[periode.selectedIndex].value;	
	var NoStokOpname = document.getElementById('nostokopname').value;

	if(NoStokOpname == '' || NoStokOpname == null) {
		alert('No Stok Opname is obligatory');
	} else {	
	    judul='Report Stok Opname';	
	    param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode+'&nostokopname='+NoStokOpname;
	    param+='&proses=excel';
	    printFile(param,tujuan,judul,ev);
	}
}

function fisikKePDF2(ev,tujuan){
    var unitDt	= document.getElementById('unitDt');
    var gudang  = document.getElementById('gudang2');
    var periode = document.getElementById('periode2');
	unitDt	= unitDt.options[unitDt.selectedIndex].value;
	gudang	= gudang2.options[gudang2.selectedIndex].value;
	periode	= periode2.options[periode2.selectedIndex].value;
	var NoStokOpname = document.getElementById('nostokopname').value;
	
	if(NoStokOpname == '' || NoStokOpname == null) {
		alert('No Stok Opname is obligatory');
	} else {	
	    judul='Transaksi Stok Opname';	
	    param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode+'&nostokopname='+NoStokOpname;
	    param+='&proses=pdf';
	    printFile(param,tujuan,judul,ev);
	}
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}