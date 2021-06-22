// JavaScript Document

function add_new_data()
{
	param='proses=CekData';
	//alert(param);
	tujuan='kebun_slave_curahHujan.php';
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
function displayList()
{
	document.getElementById('headher').style.display='none';
	document.getElementById('listData').style.display='block';
	document.getElementById('unitOrg').value='';
	document.getElementById('tgl_cari').value='';
	loadData();
}
function bersih()
{
	document.getElementById('kodeOrg').value='';
	document.getElementById('tgl').value='';
	document.getElementById('cttn').value='';
	document.getElementById('kodeOrg').disabled=false;
	document.getElementById('tgl').disabled=false;
	document.getElementById('pg').value='0';
	document.getElementById('sr').value='0';
}
function cancelSave()
{
	bersih();
	displayList();
}
function loadData()
{
	param='proses=LoadData';
	tujuan='kebun_slave_curahHujan.php';
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
		tujuan = 'kebun_slave_curahHujan.php';
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
function saveData()
{
	kdorg=document.getElementById('kodeOrg').value;
	daTpagi=document.getElementById('pg').value;
	daTsore=document.getElementById('sr').value;
	note=document.getElementById('cttn').value;
	jmlbayar=document.getElementById('jmlbayar').value;
	daTtgl=document.getElementById('tgl').value;
	pros=document.getElementById('proses').value;
	
	jmp=document.getElementById('jmp').value;
	mmp=document.getElementById('mmp').value;
	
	jsp=document.getElementById('jsp').value;
	msp=document.getElementById('msp').value;
	
	jms=document.getElementById('jms').value;
	mms=document.getElementById('mms').value;
	
	jss=document.getElementById('jss').value;
	mss=document.getElementById('mss').value;
	
/*	jmp mmp
jsp msp

jms mms
jss mss*/
	
	
	param='kdOrg='+kdorg+'&daTpagi='+daTpagi+'&daTsore='+daTsore+'&note='+note+'&daTtgl='+daTtgl+'&proses='+pros;
	param+='&jmp='+jmp+'&mmp='+mmp+'&jsp='+jsp+'&msp='+msp;
	param+='&jms='+jms+'&mms='+mms+'&jss='+jss+'&mss='+mss+'&jmlbayar='+jmlbayar;
	//alert(param);exit();
	tujuan='kebun_slave_curahHujan.php';
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
							displayList();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
}
function fillField(kdorg,tgl)
{
	kdORg=kdorg;
	daTtgl=tgl;
	document.getElementById('proses').value='update';
	param='kdOrg='+kdORg+'&daTtgl='+daTtgl+'&proses=showData';
	//alert(param);
	tujuan='kebun_slave_curahHujan.php';
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
							document.getElementById('headher').style.display='block';
							document.getElementById('listData').style.display='none';
							ar=con.responseText.split("###");
							//alert(ar[0]+ar[1]+ar[2]);
							document.getElementById('kodeOrg').value=kdorg;
							document.getElementById('tgl').value=daTtgl;
							document.getElementById('cttn').value=ar[0];
							document.getElementById('kodeOrg').disabled=true;
							document.getElementById('tgl').disabled=true;
							document.getElementById('pg').value=ar[1];
							document.getElementById('sr').value=ar[2];
							
							document.getElementById('jmp').value=ar[3];
							document.getElementById('mmp').value=ar[4];
							document.getElementById('jsp').value=ar[5];
							document.getElementById('msp').value=ar[6];
                            if (ar[7] == '00') {
                                ar[7] = '13';
                            }
                            if (ar[9] == '00') {
                                ar[9] = '13';
                            }
							document.getElementById('jms').value=ar[7];
							document.getElementById('mms').value=ar[8];
							document.getElementById('jss').value=ar[9];
							document.getElementById('mss').value=ar[10];
							

							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	
}
function deldata(kdorg,tgl)
{
	kdORg=kdorg;
	daTtgl=tgl;
	param='kdOrg='+kdORg+'&daTtgl='+daTtgl+'&proses=delData';
	//alert(param);
	tujuan='kebun_slave_curahHujan.php';
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
							displayList();
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
	tujuan='kebun_slave_curahHujan.php';
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