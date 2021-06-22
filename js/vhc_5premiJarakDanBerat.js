// JavaScript Document
function load_data()
{
	param='proses=loadData';
	tujuan='vhc_slave_5premiJarakDanBerat.php';
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
	nmr=document.getElementById('nmrDetail').value;
	param='pros=loadDataDetail'+'&keyCodeDtail='+keyCodeDtail+'&nmrDetail='+nmr;
	//alert(param);
	tujuan='vhc_slave_Detail5premiJarakDanBerat.php';
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
	jrkDari=document.getElementById('jrkDari').value;
	jrkSmp=document.getElementById('jrkSmp').value;
	tipeKerja=document.getElementById('tipeAnkg').value;
	pos=document.getElementById('posisi').value;
	premiLbh=document.getElementById('premiLbhBasis').value;
	//jmlhBasis=document.getElementById('jmlhBasis').value;
	pro=document.getElementById('proses');
	param='keycode='+keycode+'&nomor='+nomor+'&jrkDari='+jrkDari+'&jrkSmp='+jrkSmp;
	param+='&tipeKerja='+tipeKerja+'&proses='+pro.value+'&posisi='+pos+'&premiLbh='+premiLbh;
	//alert(param);
	tujuan='vhc_slave_5premiJarakDanBerat.php';
	
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
							/*if(pro.value=='insert_header')
							{
								lock_header_form();
							}
							else if(pro.value=='updateHeader')
							{
								clear_form();
							}*/
							clear_form();
							load_data();
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
function fillField(keyCode,nmr,jrkDri,jrkSmp,Krj,posi,rate)
{
	document.getElementById('keyCode').value=keyCode;
	//document.getElementById('keyCodeDetail').value=keyCode;
	document.getElementById('nomor').value=nmr;
	/*document.getElementById('nmrDetail').value=nmr;
	loadDataDetail();*/
	document.getElementById('jrkDari').value=jrkDri;
	document.getElementById('jrkSmp').value=jrkSmp;
	document.getElementById('tipeAnkg').value=Krj;
	document.getElementById('posisi').value=posi;
	document.getElementById('premiLbhBasis').value=rate;
	document.getElementById('proses').value='updateHeader';
	document.getElementById('keyCode').disabled=true;
	document.getElementById('nomor').disabled=true;
}
function clear_form()
{
	document.getElementById('jrkDari').value='';
	document.getElementById('jrkSmp').value='';
	document.getElementById('tipeAnkg').value='';
	document.getElementById('premiLbhBasis').value='';
	document.getElementById('keyCode').disabled=false;
	document.getElementById('nomor').value='';
	document.getElementById('keyCode').value='';
	//document.getElementById('keyCodeDetail').value='';
	document.getElementById('proses').value='insert_header';
	//loadDataDetail();
}
function lock_header_form()
{
	//alert("test");
	document.getElementById('save_kepala').disabled=true;
	document.getElementById('cancel_kepala').disabled=true;
	document.getElementById('keyCode').disabled=true;
	document.getElementById('done_entry').disabled=false;
	document.getElementById('saveDetail').disabled=false;
	document.getElementById('cancelDetail').disabled=false;
	document.getElementById('jrkDari').disabled=true;
	document.getElementById('jrkSmp').disabled=true;
	document.getElementById('tipeAnkg').disabled=true;
	document.getElementById('jmlhBasis').disabled=true;
	keyCode=document.getElementById('keyCode').value;
	document.getElementById('keyCodeDetail').value=keyCode;
	nmr=document.getElementById('nomor').value;
	document.getElementById('nmrDetail').value=nmr;
}
function unlock_head_form()
{
	document.getElementById('save_kepala').disabled=false;
	document.getElementById('cancel_kepala').disabled=false;
	document.getElementById('keyCode').disabled=false;
	document.getElementById('done_entry').disabled=true;
	document.getElementById('saveDetail').disabled=true;
	document.getElementById('cancelDetail').disabled=true;
	document.getElementById('jrkDari').disabled=false;
	document.getElementById('jrkSmp').disabled=false;
	document.getElementById('tipeAnkg').disabled=false;
	document.getElementById('jmlhBasis').disabled=false;
	document.getElementById('keyCode').value='';
	document.getElementById('keyCodeDetail').value='';
}
function saveDetail()
{
	keyCodeDtail=document.getElementById('keyCodeDetail').value;
	posi=document.getElementById('posisi').value;
	prmiLbhBasis=document.getElementById('premiLbhBasis').value;
	pinlty=document.getElementById('penalty').value;
	pros=document.getElementById('prosesDetail').value;
	nmr=document.getElementById('nmrDetail').value;
	param='keyCodeDtail='+keyCodeDtail+'&posi='+posi+'&prmLbhBasis='+prmiLbhBasis+'&pinalty='+pinlty+'&nmrDetail='+nmr;
	param+='&pros='+pros;
	tujuan='vhc_slave_Detail5premiJarakDanBerat.php';
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
function fillFieldDetail(code,posisi,premiLbh,pinalty)
{
	document.getElementById('keyCodeDetail').value=code;
	document.getElementById('posisi').value=posisi;
	document.getElementById('posisi').disabled=true;
	document.getElementById('premiLbhBasis').value=premiLbh;
	document.getElementById('penalty').value=pinalty;	
	document.getElementById('prosesDetail').value='updateDetail';
}
function clearFormDetail()
{
	document.getElementById('posisi').value='';
	document.getElementById('premiLbhBasis').value='';
	document.getElementById('premiLbhBasis').value='';
	document.getElementById('penalty').value='';
	document.getElementById('posisi').disabled=false;
}
function cariBast(num)
{
		param='proses=loadData';
		param+='&page='+num;
		tujuan = 'vhc_slave_5premiJarakDanBerat.php';
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
		tujuan = 'vhc_slave_Detail5premiJarakDanBerat.php';
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
	tujuan='vhc_slave_Detail5premiJarakDanBerat.php';			
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
	tujuan='vhc_slave_5premiJarakDanBerat.php';			
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
function cekNomor(trans)
{
	trp=document.getElementById('keyCode').value;
	//trp=trans;
	param='proses=cekNmr'+'&keycode='+trp;
	tujuan='vhc_slave_5premiJarakDanBerat.php';			
    //alert(param);
	post_response_text(tujuan, param, respog);
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
		
}