// JavaScript Document
function load_data()
{
	//alert("masuk");
	param='proses=loadData';
	tujuan='vhc_slave_5premiJumlahTrip.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							document.getElementById('contain').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}	
function loadDataDetail()
{
	keyCodeDtail=document.getElementById('keyCodeDetail').value;
	nmr=document.getElementById('nomDetail').value;
	param='pros=loadDataDetail'+'&keyCodeDtail='+keyCodeDtail+'&nomDetail='+nmr;
	//alert(param);
	tujuan='vhc_slave_Detail5premiJumlahTrip.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							document.getElementById('containDetail').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}							
function save_header()
{
	keycode=document.getElementById('keyCode').value;
	nomor=document.getElementById('nomor').value;
	tipeKerja=document.getElementById('tipeAnkg').value;
	premi=document.getElementById('detPremi').value;
	jmlhTrip=document.getElementById('jmlhTrip').value;
	pos=document.getElementById('posisi').value;
	//totBasis=document.getElementById('jmlhBasis').value;
	pro=document.getElementById('proses');
	param='keycode='+keycode+'&nomor='+nomor;
	param+='&tipeKerja='+tipeKerja+'&proses='+pro.value+'&detPremi='+premi+'&jmlhTrip='+jmlhTrip;
	param+='&posisi='+pos;
	
	tujuan='vhc_slave_5premiJumlahTrip.php';
	if(confirm("Are You Sure Want Save This Data!!"))
	{
/*		alert(param);
		return;*/
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							load_data();
							clear_form();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}
function fillField(keyCode,nmr,Krj,pos,jmlhTrip,Rate)
{
	//alert("masuk");
	document.getElementById('keyCode').value=keyCode;
	document.getElementById('nomor').value=nmr;
	document.getElementById('tipeAnkg').value=Krj;
	document.getElementById('posisi').value=pos;
	document.getElementById('jmlhTrip').value=jmlhTrip;
	document.getElementById('detPremi').value=Rate;

	test=document.getElementById('proses');
	test.value='updateHeader';
	//alert(test.value);
	unlock_head_form();
	document.getElementById('keyCode').disabled=true;
}

function clear_form()
{
	document.getElementById('detPremi').value='';
	document.getElementById('tipeAnkg').value='';
	document.getElementById('jmlhTrip').value='';
	document.getElementById('keyCode').disabled=false;
	document.getElementById('nomor').value='';
	document.getElementById('keyCode').value='';
	document.getElementById('proses').value='insert_header';
}
function lock_header_form()
{
	key=document.getElementById('keyCode').value;
	nmr=document.getElementById('nomor').value;
	//alert(key+nmr);
	document.getElementById('save_kepala').disabled=true;
	document.getElementById('cancel_kepala').disabled=true;
	document.getElementById('keyCode').disabled=true;
	document.getElementById('done_entry').disabled=false;
	document.getElementById('saveDetail').disabled=false;
	document.getElementById('cancelDetail').disabled=false;
	document.getElementById('jrkDari').disabled=true;
	document.getElementById('jrkSmp').disabled=true;
	document.getElementById('tipeAnkg').disabled=true;
	document.getElementById('jmlhTrip').disabled=true;
	
}
function unlock_head_form()
{
	/*document.getElementById('save_kepala').disabled=false;
	document.getElementById('cancel_kepala').disabled=false;
	document.getElementById('keyCode').disabled=false;
	document.getElementById('done_entry').disabled=true;
	document.getElementById('saveDetail').disabled=true;
	document.getElementById('cancelDetail').disabled=true;
	document.getElementById('tipeAnkg').disabled=false;
	document.getElementById('jmlhTrip').disabled=false;*/
}
function saveDetail()
{
	keyCodeDtail=document.getElementById('keyCodeDetail').value;
	posi=document.getElementById('posisi').value;
	pinlty=document.getElementById('penalty').value;
	pros=document.getElementById('prosesDetail').value;
	nmr=document.getElementById('nomDetail').value;
	param='keyCodeDtail='+keyCodeDtail+'&posi='+posi+'&pinalty='+pinlty+'&nomDetail='+nmr;
	param+='&pros='+pros;
	tujuan='vhc_slave_Detail5premiJumlahTrip.php';
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							//alert(con.responseText);
							//document.getElementById('contain').value=con.responseText;
							clearFormDetail();
							loadDataDetail();

						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	 if(confirm("Are You Sure Want Save This Data!!"))
	{
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
}
function fillFieldDetail(code,posisi,pinalty)
{
	document.getElementById('keyCodeDetail').value=code;
	document.getElementById('posisi').value=posisi;
	document.getElementById('posisi').disabled=true;
	document.getElementById('penalty').value=pinalty;	
	document.getElementById('prosesDetail').value='updateDetail';
}
function clearFormDetail()
{
	document.getElementById('posisi').value='';
	document.getElementById('penalty').value='';
	document.getElementById('posisi').disabled=false;
	load_data();
}
function cariBast(num)
{
		param='proses=loadData';
		param+='&page='+num;
		tujuan = 'vhc_slave_5premiJumlahTrip.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function cariDet(num)
{
		keyCodeDtail=document.getElementById('keyCode').value;
		param='pros=loadDataDetail';
		param+='&page='+num;
		param+='&keyCodeDtail='+keyCodeDtail;
		tujuan = 'vhc_slave_Detail5premiJumlahTrip.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containDetail').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function delDetail(keycode,posisi)
{
	keyCodeDtail=keycode;
	posi=posisi;
	param='keyCodeDtail='+keyCodeDtail+'&posi='+posi+'&pros=delDetail';
	tujuan='vhc_slave_Detail5premiJumlahTrip.php';			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('containDetail').innerHTML=con.responseText;
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	if(confirm("Are You Sure Want Delete This Data"))
	{
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
}
function delHead(keycode,nmr)
{
	keyCodeDtail=keycode;
	nomor=nmr;
	param='keycode='+keyCodeDtail+'&nomor='+nomor+'&proses=delData';
	tujuan='vhc_slave_5premiJumlahTrip.php';			
//	alert(param);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('containDetail').innerHTML=con.responseText;
						load_data();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	if(confirm("Are You Sure Want Delete All Data"))
	{
		post_response_text(tujuan, param, respog);
	}
	else
	{
		return;
	}
}
function doneEntry()
{
	if(confirm("Are You Sure Already Done"))
	{
		clear_form();
		unlock_head_form();
		clearFormDetail();
	}
	else
	{
		return;
	}
}
function cekNomor()
{
	//alert(trans);
	trp=document.getElementById('keyCode').value;
	if(trp=='')
	{
		document.getElementById('nomor').value='';
		return;
	}
	else
	{
	//trp=trans;
	param='proses=cekNmr'+'&keycode='+trp;
	tujuan='vhc_slave_5premiJumlahTrip.php';			
    //alert(param);

		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('nomor').value=con.responseText;
						//load_data();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	post_response_text(tujuan, param, respog);
	}
}