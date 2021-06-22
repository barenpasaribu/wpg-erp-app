// JavaScript Document
function save_header()
{
	thnAnggrn=document.getElementById('thnAnggaran').value;
	kdVhc=document.getElementById('kdvhc').value;
	jmlhari=document.getElementById('jmlhHari').value;
	pmaknHm=document.getElementById('pemakaianHm').value;
	jmlhHrTdkOpr=document.getElementById('jmlhHariTdk').value;
	pros=document.getElementById('proses').value;
	param='thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&jmlhHari='+jmlhari+'&pemakaianHm='+pmaknHm+'&jmlhHrTdkOpr='+jmlhHrTdkOpr+'&proses='+pros;
	//alert(param);
	tujuan='keu_slave_anggaranTraksi.php';
	post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('contain').innerHTML=con.responseText;
						alert("Save Succes, You can Add Detail in Other Tab");
						loadData();
						lockForm();
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	

}
function loadData()
{
	param='proses=loadData';
	tujuan='keu_slave_anggaranTraksi.php';
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
						//loadDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function cariBast(num)
{
		param='proses=loadData';
		param+='&page='+num;
		tujuan = 'keu_slave_anggaranTraksi.php';
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
function loadDetail()
{
	thnAnggrn=document.getElementById('thnAnggaran').value;
	kdVhc=document.getElementById('kdvhc').value;
	if((thnAnggrn=='')&&(kdVhc==''))
	{
		document.getElementById('containDetailTraksi').innerHTML='';
	}
	else if((thnAnggrn!='')&&(kdVhc!=''))
	{
		param='proses=loadDetail'+'&thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc;
		tujuan='keu_slave_anggaranTraksi.php';
			function respog(){
				if (con.readyState == 4) {
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							document.getElementById('containDetailTraksi').innerHTML=con.responseText;
							loadaLokasi();
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
function cariDetail(num)
{
		param='proses=loadDetail';
		param+='&page='+num;
		tujuan = 'keu_slave_anggaranTraksi.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containDetailTraksi').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function lockForm()
{
	document.getElementById('thnAnggaran').disabled=true;
	document.getElementById('kdvhc').disabled=true;
	document.getElementById('jmlhHari').disabled=true;
	document.getElementById('pemakaianHm').disabled=true;
	document.getElementById('jmlhHariTdk').disabled=true;
	document.getElementById('tmbLhead').innerHTML='';	
	document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=clear_save_form() >"+tmblDone+"</button>";	
}
function shwTmbl()
{
	document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=save_header() >"+tmblSave+"</button><button class=mybutton id=cancel_kepala name=cancel_kepala onclick=clear_save_form() >"+tmblCancel+"</button>";
}
function saveDetail()
{
	kdBrg=document.getElementById('kdBrg').value;
	jmlh=document.getElementById('jmlh').value;
   // document.getElementById('oldKdbrg').value;
	oldKdbrg=document.getElementById('oldKdbrg').value;
	thnAnggrn=document.getElementById('thnAnggaran').value;
	kdVhc=document.getElementById('kdvhc').value;
	pros=document.getElementById('pros').value;
	param='kdBrg='+kdBrg+'&jmlh='+jmlh+'&oldKdbrg='+oldKdbrg+'&proses='+pros+'&thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc;
	alert(param);
	tujuan='keu_slave_anggaranTraksi.php';
	post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('contain').innerHTML=con.responseText;
						document.getElementById('jmlh').value='0';
						loadDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function saveAlokasi()
{
	kdOrg=document.getElementById('kdOrg').value;
	jmlhMeter=document.getElementById('jmlhMeter').value;
	thnAnggrn=document.getElementById('thnAnggaran').value;
	kdVhc=document.getElementById('kdvhc').value;
	jmlhJan=document.getElementById('jmlhJan').value;
	jmlhFeb=document.getElementById('jmlhFeb').value;
	jmlhMar=document.getElementById('jmlhMar').value;
	jmlhApr=document.getElementById('jmlhApr').value;
	jmlhMei=document.getElementById('jmlhMei').value;
	jmlhJun=document.getElementById('jmlhJun').value;
	jmlhJul=document.getElementById('jmlhJul').value;
	jmlhAug=document.getElementById('jmlhAug').value;
	jmlhSep=document.getElementById('jmlhSep').value;
	jmlhNov=document.getElementById('jmlhNov').value;
	jmlhDes=document.getElementById('jmlhDes').value;
	jmlhOkt=document.getElementById('jmlhOkt').value;
	pros=document.getElementById('prosAlokasi').value;
	param='kdOrg='+kdOrg+'&jmlhMeter='+jmlhMeter+'&proses='+pros+'&thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc;
	param+='&jmlhJan='+jmlhJan+'&jmlhFeb='+jmlhFeb+'&jmlhMar='+jmlhMar+'&jmlhApr='+jmlhApr+'&jmlhMei='+jmlhMei+'&jmlhOkt='+jmlhOkt;
	param+='&jmlhJun='+jmlhJun+'&jmlhJul='+jmlhJul+'&jmlhAug='+jmlhAug+'&jmlhSep='+jmlhSep+'&jmlhNov='+jmlhNov+'&jmlhDes='+jmlhDes;
	tujuan='keu_slave_anggaranTraksi.php';
	post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('contain').innerHTML=con.responseText;
						loadaLokasi();
						document.getElementById('jmlhMeter').value='0';
						document.getElementById('jmlhJan').value='0';
						document.getElementById('jmlhFeb').value='0';
						document.getElementById('jmlhMar').value='0';
						document.getElementById('jmlhApr').value='0';
						document.getElementById('jmlhMei').value='0';
						document.getElementById('jmlhJun').value='0';
						document.getElementById('jmlhJul').value='0';
						document.getElementById('jmlhAug').value='0';
						document.getElementById('jmlhSep').value='0';
						document.getElementById('jmlhNov').value='0';
						document.getElementById('jmlhDes').value='0';
						document.getElementById('jmlhOkt').value='0';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function loadaLokasi()
{
	thnAnggrn=document.getElementById('thnAnggaran').value;
	kdVhc=document.getElementById('kdvhc').value;
	if((thnAnggrn=='')&&(kdVhc==''))
	{
		document.getElementById('containAlokasi').innerHTML='';
	}
	else if((thnAnggrn!='')&&(kdVhc!=''))
	{
		param='proses=loadaLokasi'+'&thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc;
		tujuan='keu_slave_anggaranTraksi.php';
			function respog(){
				if (con.readyState == 4) {
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							document.getElementById('containAlokasi').innerHTML=con.responseText;
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
function cariLokasi(num)
{
		param='proses=loadaLokasi';
		param+='&page='+num;
		tujuan = 'keu_slave_anggaranTraksi.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containAlokasi').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function fillField(thn,kodevhc)
{
	thnAnggrn=thn;
	kdVhc=kodevhc;
	param='thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&proses=getDataHeader'; 
	//alert(param);
	tujuan = 'keu_slave_anggaranTraksi.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('containDetailTraksi').innerHTML=con.responseText;
					//	alert(con.responseTex);
						ar=con.responseText.split("###");
						document.getElementById('thnAnggaran').value=ar[1];
						document.getElementById('kdvhc').value=ar[0];
						document.getElementById('jmlhHari').value=ar[2];
						document.getElementById('pemakaianHm').value=ar[3];
						document.getElementById('jmlhHariTdk').value=ar[4];
						document.getElementById('proses').value='update';
						document.getElementById('thnAnggaran').disabled=true;
						document.getElementById('kdvhc').disabled=true;
					/*	document.getElementById('tmbLhead').innerHTML='';
						document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=clearForm() >"+tmblDone+"</button>";*/	
						loadDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function fillFieldDetail(kodebrg,jumlah)
{
	kdBrg=kodebrg;
	jmlh=jumlah;
	document.getElementById('kdBrg').value=kdBrg;
	document.getElementById('oldKdbrg').value=kdBrg;
	document.getElementById('jmlh').value=jmlh;
	document.getElementById('pros').value='updateDetail';
}
function fillFieldAlokasi(thn,kodevhc,kodeorg)
{
	thnAnggrn=thn;
	kdVhc=kodevhc;
	kdOrg=kodeorg;
	param='proses=getDataAlokasi'+'&thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&kdOrg='+kdOrg;
	tujuan = 'keu_slave_anggaranTraksi.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						//document.getElementById('containDetailTraksi').innerHTML=con.responseText;
					//	alert(con.responseTex);
						ar=con.responseText.split("###");
						document.getElementById('kdOrg').value=ar[0];
						document.getElementById('jmlhMeter').value=ar[1];
						document.getElementById('thnAnggaran').value;
						document.getElementById('kdvhc').value;
						document.getElementById('jmlhJan').value=ar[2];
						document.getElementById('jmlhFeb').value=ar[3];
						document.getElementById('jmlhMar').value=ar[4];
						document.getElementById('jmlhApr').value=ar[5];
						document.getElementById('jmlhMei').value=ar[6];
						document.getElementById('jmlhJun').value=ar[7];
						document.getElementById('jmlhJul').value=ar[8];
						document.getElementById('jmlhAug').value=ar[9];
						document.getElementById('jmlhSep').value=ar[10];
						document.getElementById('jmlhNov').value=ar[12];
						document.getElementById('jmlhDes').value=ar[13];
						document.getElementById('jmlhOkt').value=ar[11];
						document.getElementById('prosAlokasi').value='updateAlokasi';
					
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function deldata(thn,kodevhc)
{
	thnAnggrn=thn;
	kdVhc=kodevhc;
	param='thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&proses=delHeader';
	tujuan = 'keu_slave_anggaranTraksi.php';		
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
		if(confirm("Are You Sure Want Delete All Data !!"))
		post_response_text(tujuan, param, respog);	
}
function deldataLok(thn,kodevhc,kodeorg)
{
	thnAnggrn=thn;
	kdVhc=kodevhc;
	kdOrg=kodeorg;
	param='thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&proses=delLoksi'+'&kdOrg='+kdOrg;
	tujuan = 'keu_slave_anggaranTraksi.php';		
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
						loadaLokasi();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
		if(confirm("Are You Sure Want Delete This Data !!"))
		post_response_text(tujuan, param, respog);
}
function deldataDet(thn,kodevhc,kodebrg)
{
	thnAnggrn=thn;
	kdVhc=kodevhc;
	kdBrg=kodebrg;
	param='thnAnggrn='+thnAnggrn+'&kdVhc='+kdVhc+'&proses=delDet'+'&kdBrg='+kdBrg;
	tujuan = 'keu_slave_anggaranTraksi.php';		
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
						loadDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
		if(confirm("Are You Sure Want Delete This Data !!"))
		post_response_text(tujuan, param, respog);
}
/*function clear_save_form()
{
	document.getElementById('thnAnggaran').disabled=false;
	document.getElementById('kdvhc').disabled=false;
	document.getElementById('thnAnggaran').disabled=true;
	document.getElementById('kdvhc').disabled=true;
	document.getElementById('jmlhHari').disabled=true;
	document.getElementById('pemakaianHm').disabled=true;
	document.getElementById('jmlhHariTdk').disabled=true;
	document.getElementById('thnAnggaran').value='';
	document.getElementById('kdvhc').value='';
	document.getElementById('jmlhHari').value='';
	document.getElementById('pemakaianHm').value='';
	document.getElementById('jmlhHariTdk').value='';
	document.getElementById('containAlokasi').innerHTML='';
	document.getElementById('containDetailTraksi').innerHTML='';
}*/
function clear_save_form()
{
	document.getElementById('thnAnggaran').disabled=false;
	document.getElementById('kdvhc').disabled=false;
	document.getElementById('jmlhHari').disabled=false;
	document.getElementById('pemakaianHm').disabled=false;
	document.getElementById('jmlhHariTdk').disabled=false;
	document.getElementById('thnAnggaran').value='';
	document.getElementById('kdvhc').value='';
	document.getElementById('jmlhHari').value='';
	document.getElementById('pemakaianHm').value='';
	document.getElementById('jmlhHariTdk').value='';
	document.getElementById('containAlokasi').innerHTML='';
	document.getElementById('containDetailTraksi').innerHTML='';
	shwTmbl();
}
function clearDetail()
{
	document.getElementById('kdBrg').value='36100118';
	document.getElementById('jmlh').value='0';
}
function clearAlokasi()
{
	document.getElementById('kdOrg').value='MJHO';
	document.getElementById('jmlhMeter').value='0';
	document.getElementById('jmlhJan').value='0';
	document.getElementById('jmlhFeb').value='0';
	document.getElementById('jmlhMar').value='0';
	document.getElementById('jmlhApr').value='0';
	document.getElementById('jmlhMei').value='0';
	document.getElementById('jmlhJun').value='0';
	document.getElementById('jmlhJul').value='0';
	document.getElementById('jmlhAug').value='0';
	document.getElementById('jmlhSep').value='0';
	document.getElementById('jmlhNov').value='0';
	document.getElementById('jmlhDes').value='0';
	document.getElementById('jmlhOkt').value='0';

}