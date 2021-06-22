function cancel()
{
	document.location.reload();
}


function getAfd()
{
	kdDiv=document.getElementById('kdDiv').value;
	param='method=getAfd'+'&kdDiv='+kdDiv;
	
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						//alert(con.responseText);
						document.getElementById('kdAfd').innerHTML=con.responseText;
						getKar();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function getKar()
{
	
	kdDiv=document.getElementById('kdDiv').value;
	param='method=getKar'+'&kdDiv='+kdDiv;
	//alert(param);
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						//alert(con.responseText);
						ar=con.responseText.split("###");
						//pengawas asisten diket
						document.getElementById('diperiksa').innerHTML=ar[1];
						document.getElementById('pendamping').innerHTML=ar[0];
						document.getElementById('mengetahui').innerHTML=ar[1];
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function del(tanggalcek,kdBlok)
{
	
	param='method=delete'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						cancel();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}




function getBlok()
{
	kdAfd=document.getElementById('kdAfd').value;
	param='method=getBlok'+'&kdAfd='+kdAfd;
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						document.getElementById('kdBlok').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}









function edit()
{

}






function cariBast(num)
{
	kdDivSch=document.getElementById('kdDivSch').value;
	perSch=document.getElementById('perSch').value;
	
	param='method=loadData'+'&kdDivSch='+kdDivSch+'&perSch='+perSch+'&page='+num;
	tujuan = 'kebun_slave_qc_panen.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//displayList();
					
					document.getElementById('container').innerHTML=con.responseText;
					//loadData();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function loadData () 
{
	kdDivSch=document.getElementById('kdDivSch').value;
	perSch=document.getElementById('perSch').value;
	param='method=loadData'+'&kdDivSch='+kdDivSch+'&perSch='+perSch;
	//alert(param);	
	tujuan='kebun_slave_qc_panen.php';
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
                                   // alert(con.responseText);
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




function lockHeader()
{
	document.getElementById('saveHeader').disabled=true;
	document.getElementById('cancelHeader').disabled=true;
	document.getElementById('tanggalcek').disabled=true;
	document.getElementById('kdDiv').disabled=true;
	document.getElementById('kdAfd').disabled=true;
	document.getElementById('kdBlok').disabled=true;
	document.getElementById('pusingan').disabled=true;
	document.getElementById('tanggalpanen').disabled=true;
	document.getElementById('diperiksa').disabled=true;
	document.getElementById('pendamping').disabled=true;
	document.getElementById('mengetahui').disabled=true;	
}


function clearDetail()
{	
	document.getElementById('nopokok').value='';
	document.getElementById('jjgpanen').value='';
	document.getElementById('jjgtdkpanen').value='';
	document.getElementById('jjgtdkkumpul').value='';
	document.getElementById('jjgmentah').value='';
	document.getElementById('jjggantung').value='';
	document.getElementById('brdtdkdikutip').value='';
	
	document.getElementById('rumpukan').value='';
	document.getElementById('piringan').value='';
	document.getElementById('jalurpanen').value='';
	document.getElementById('tukulan').value='';
	//document.getElementById('rumpukan').checked==false;
}


function saveHeader()
{
	tanggalcek=document.getElementById('tanggalcek').value;
	kdDiv=document.getElementById('kdDiv').value;
	kdAfd=document.getElementById('kdAfd').value;
	
	kdBlok=document.getElementById('kdBlok').value;
	pusingan=document.getElementById('pusingan').value;
	tanggalpanen=document.getElementById('tanggalpanen').value;
	diperiksa=document.getElementById('diperiksa').value;
	pendamping=document.getElementById('pendamping').value;
	mengetahui=document.getElementById('mengetahui').value;
	
	if(tanggalcek=='' || kdDiv=='' || kdAfd=='' || kdBlok=='' || tanggalpanen=='' || diperiksa=='' || pendamping=='' || mengetahui=='')
	{
		alert('Date Check, Divisi, Afddeling, Block, Date Harvest, Checked By, Companion, Verify was empty');return;
	}
	
	param='method=saveHeader'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok+'&pusingan='+pusingan;
	param+='&tanggalpanen='+tanggalpanen+'&diperiksa='+diperiksa+'&pendamping='+pendamping+'&mengetahui='+mengetahui;		

	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						
						lockHeader();
						document.getElementById('detailForm').style.display='block';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



function saveDetail()
{
	tanggalcek=document.getElementById('tanggalcek').value;
	kdBlok=document.getElementById('kdBlok').value;
	
	nopokok=document.getElementById('nopokok').value;
	jjgpanen=document.getElementById('jjgpanen').value;
	jjgtdkpanen=document.getElementById('jjgtdkpanen').value;
	jjgtdkkumpul=document.getElementById('jjgtdkkumpul').value;
	
	jjgmentah=document.getElementById('jjgmentah').value;
	jjggantung=document.getElementById('jjggantung').value;
	brdtdkdikutip=document.getElementById('brdtdkdikutip').value;
	
	if(document.getElementById('rumpukan').checked==true)
	   rumpukan=1;
	else
	   rumpukan=0; 
	if(document.getElementById('piringan').checked==true)
	   piringan=1;
	else
	   piringan=0; 
	if(document.getElementById('jalurpanen').checked==true)
	   jalurpanen=1;
	else
	   jalurpanen=0; 
	if(document.getElementById('tukulan').checked==true)
	   tukulan=1;
	else
	   tukulan=0;
	   
	 
	 if(nopokok=='' || nopokok=='0')
	 {
		 alert('Incorect number of Tree');return; 
	 }
	    	   	   

	param='method=saveDetail'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok;
	
	param+='&nopokok='+nopokok+'&jjgpanen='+jjgpanen+'&jjgtdkpanen='+jjgtdkpanen+'&jjgtdkkumpul='+jjgtdkkumpul
	param+='&jjgmentah='+jjgmentah+'&jjggantung='+jjggantung+'&brdtdkdikutip='+brdtdkdikutip;
	
	param+='&rumpukan='+rumpukan+'&piringan='+piringan+'&jalurpanen='+jalurpanen+'&tukulan='+tukulan;		

	//alert(param);
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						
						//bersihDetail();
						clearDetail();
						loadDataDetail();
						
						
						//lockHeader();
						//document.getElementById('detailForm').style.display='block';
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
	//alert('masuk');
	tanggalcek=document.getElementById('tanggalcek').value;
	kdBlok=document.getElementById('kdBlok').value;
	param='method=loadDetail'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok;
	//alert(param);
	tujuan='kebun_slave_qc_panen.php';
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
							//return;
							//document.getElementById('contentDetail').innerHTML=con.responseText;
							document.getElementById('containList').style.display='block';
							document.getElementById('containList').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}





function DelDetail(tanggalcek,kdBlok,nopokok)
{
	param='method=deleteDetail'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok+'&nopokok='+nopokok;
	//alert(param);
	tujuan='kebun_slave_qc_panen.php';
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
					else 
					{
						clearDetail();
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}
	//alert("Data telah terhapus !!!");	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='300';
    height='100';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2(title,content,width,height,ev); 	
}
function datakeExcel(ev,tanggalcek,kdBlok)
{
        param='method=printExcel'+'&tanggalcek='+tanggalcek+'&kdBlok='+kdBlok;
        //alert(param);
        tujuan='kebun_slave_qc_panen.php';
        judul='RFQ convert spreadsheet';		
        printFile(param,tujuan,judul,ev)	
}


