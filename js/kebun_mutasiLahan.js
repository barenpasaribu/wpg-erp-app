// JavaScript Document

function add_new_data()
{
	param='proses=CekData';
	//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
						//	alert(con.responseText);
						document.getElementById('headher').style.display='block';
						document.getElementById('listData').style.display='none';
						bersih();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	 	
}

function bersih()
{
	document.getElementById('kodeOrg').value='';
	document.getElementById('kodeAfdeling').innerHTML='';
	document.getElementById('kodeBlok').innerHTML='';
	document.getElementById('kodeOrg').disabled=false;
	document.getElementById('kodeAfdeling').disabled=false;
	document.getElementById('kodeBlok').disabled=false;
	document.getElementById('periodetm').value='00-0000';
}
function cancelSave()
{
	bersih();
	displayList();
}
function loadData()
{
	param='proses=LoadData';
	tujuan='kebun_slave_mutasiLahan.php';
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
function cariBast(num)
{
		param='proses=LoadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_mutasiLahan.php';
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
function getAfdeling(kdkebun,kdafdeling)
{
	if((kdkebun=='0')&&(kdafdeling=='0'))
	{
		kdKbn=document.getElementById('kodeOrg').value;
		param='kdKbn='+kdKbn+'&proses=getAfdeling';
	}
	else
	{
		kdafdel=kdafdeling.substr(0,6);
		param='kdKbn='+kdkebun+'&kdAfdeling='+kdafdel+'&proses=getAfdeling';
	}
//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
						   document.getElementById('kodeAfdeling').innerHTML='';
 						   document.getElementById('kodeAfdeling').innerHTML=con.responseText;
						   getBlok(kdafdel,kdafdeling);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function getBlok(kdafdeling,kdblok)
{
	if((kdblok=='0')&&(kdafdeling=='0'))
	{
		kdafdeling=document.getElementById('kodeAfdeling').value;
		param='kdAfdeling='+kdafdeling+'&proses=getBlok';
	}
	else
	{
		param='kdAfdeling='+kdafdeling+'&kdBlok='+kdblok+'&proses=getBlok';
	}
	//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
							document.getElementById('kodeBlok').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function saveData()
{
	kdBlok=document.getElementById('kodeBlok').value;
	kdKbn=document.getElementById('kodeOrg').value;
	kdAfdeling=document.getElementById('kodeAfdeling').value;
	kdBlok=document.getElementById('kodeBlok').value;
	periodetm=document.getElementById('periodetm').value;
	pros=document.getElementById('proses').value;
	param='kdBlok='+kdBlok+'&periodetm='+periodetm+'&proses='+pros+'&kdAfdeling='+kdAfdeling+'&kdKbn='+kdKbn+'&kdBlok='+kdBlok;
	//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
							loadData();
							bersih();
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function fillField(kdkbn,kdapdeling,kdblok,period)
{
	document.getElementById('kodeOrg').value=kdkbn;
	document.getElementById('kodeOrg').disabled=true;
	getAfdeling(kdkbn,kdblok);
	document.getElementById('kodeAfdeling').disabled=true;
	document.getElementById('kodeBlok').disabled=true;
	document.getElementById('periodetm').value=period;
}
function deldata(kdblok)
{
	kdBlok=kdblok;
	param='kdBlok='+kdBlok+'&proses=delData';
	//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
						//	alert(con.responseText);
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	 if(confirm("Are You Sure Want Delete This Data"))
	 	post_response_text(tujuan, param, respog);
}
function printPDF(kdorg,tgl,ev) {
    // Prep Param
	kdORg=kdorg;
	daTtgl=tgl;
	param='kdOrg='+kdORg+'&daTtgl='+daTtgl;
    param += "&proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_curahHujanPdf.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function cariCurah()
{
	kdOrg=document.getElementById('unitOrg').value;
	daTtgl=document.getElementById('tgl_cari').value;
	param='kdOrg='+kdOrg+'&daTtgl='+daTtgl+'&proses=cariData';
	//alert(param);
	tujuan='kebun_slave_mutasiLahan.php';
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
						//	alert(con.responseText);
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
