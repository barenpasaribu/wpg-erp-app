maxf=0
sekarang=1;
function editAll(maxRow)
{     
	maxf=maxRow;
	loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
	notranDet=trim(document.getElementById('notranDet'+currRow).innerHTML);
	nobpbDet=trim(document.getElementById('nobpbDet'+currRow).innerHTML);
	nopoDet=trim(document.getElementById('nopoDet'+currRow).innerHTML);
	kodebarangDet=trim(document.getElementById('kodebarangDet'+currRow).innerHTML);
	jumlah=trim(document.getElementById('jumlah'+currRow).value);
	
	param='notranDet='+notranDet+'&nobpbDet='+nobpbDet+'&nopoDet='+nopoDet+'&kodebarangDet='+kodebarangDet+'&jumlah='+jumlah;
	param+="&method=updateAll";
	
	//alert(param);
	tujuan = 'log_slave_packing.php';
	post_response_text(tujuan, param, respog);
	document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//document.getElementById('row'+currRow).style.display='none';//ini untuk menghilangkan/memunculkan data telah tersimpan
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						alert('Done');
						loadDataDetail();
						//document.location.reload();
						//document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsave(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}



function saveDetail(notran,nobpb,nopo,nopp,kodebarang,jumlah,satuanpo,matauang,kurs,hargasatuan,keteranganpp)
{
	if(notran=='')
	{
		alert('Some field was empty');return;
	}
	
	param='method=saveDetail'+'&notran='+notran+'&nobpb='+nobpb+'&nopo='+nopo;
	param+='&nopp='+nopp+'&kodebarang='+kodebarang+'&jumlah='+jumlah+'&satuanpo='+satuanpo;		
	param+='&matauang='+matauang+'&kurs='+kurs+'&hargasatuan='+hargasatuan+'&keteranganpp='+keteranganpp;	
	//alert(param);
	tujuan='log_slave_packing.php';
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
						
						//lockHeader();
						//document.getElementById('detailForm').style.display='block';
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



/////////////////////////////////find barang di input barang


function cariBarang(title,ev)
{
	 // kosongkan();
	  //setSloc('simpan');
	  content= "<div>";
	  content+="<fieldset>Barang:<input type=text id=txtBarang class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
	  content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
 //display window
	 title=title+' PO:';
	   width='500';
	   height='300';
	   showDialog2(title,content,width,height,ev);	
}



function goCariBarang()
{

	txtBarang=trim(document.getElementById('txtBarang').value);
	if(txtBarang.length<4)
	   alert('Text too short');
	else
	{   
	param='method=goCariBarang'+'&txtBarang='+txtBarang;
   
	tujuan = 'log_slave_packing.php';
	post_response_text(tujuan, param, respog);			
	}
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


function goPickBarang(kodebarang,namabarang,satuan)
{
	document.getElementById('kodebarang').value=kodebarang;
	document.getElementById('namabarang').value=namabarang;
	document.getElementById('satuan').value=satuan;
	closeDialog2();
	//document.getElementById('').innerHTML=con.responseText;
	//document.getElementById('listCariBarang').style.display='none';
}





function cancelFormBarang()
{
	document.getElementById('nobpb').value='';
	document.getElementById('nopo').value='';
	document.getElementById('nopp').value='';
	document.getElementById('kodebarang').value='';
	document.getElementById('kurs').value='';
	document.getElementById('namabarang').value='';
	document.getElementById('jumlah').value='';
	document.getElementById('satuan').value='';
	document.getElementById('matauang').value='IDR';
	document.getElementById('hargasatuan').value='';
	
}


function saveFormBarang()
{

	//alert('MASUK');
	notran=document.getElementById('notran').value;
	
	nobpb=document.getElementById('nobpb').value;
	nopo=document.getElementById('nopo').value;
	nopp=document.getElementById('nopp').value;
	kodebarang=document.getElementById('kodebarang').value;
	jumlah=document.getElementById('jumlah').value;
	satuan=document.getElementById('satuan').value;
	matauang=document.getElementById('matauang').value;
	hargasatuan=document.getElementById('hargasatuan').value;
	method=document.getElementById('method').value;

	//param='kodeproject='+kodeproject+'&kodekegiatan='+kodekegiatan+'&kodeBarangForm='+kodeBarangForm+'&jumlahBarangForm='+jumlahBarangForm+'&method='+saveFormBarang;
	param='method=saveFormBarang'+'&notran='+notran+'&nobpb='+nobpb+'&nopo='+nopo+'&nopp='+nopp;
	param+='&kodebarang='+kodebarang+'&jumlah='+jumlah+'&satuan='+satuan+'&matauang='+matauang;		
	param+='&hargasatuan='+hargasatuan+'&kodebarang='+kodebarang+'&jumlah='+jumlah+'&satuan='+satuan;		
	
	tujuan = 'log_slave_packing.php';
	
	//alert(tujuan);
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
							//alert(con.responseText
							cancelFormBarang();
							loadDataDetail();
							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}

//////////////////////////////




//////////////////////////////////////////////////////////////INPUT BARANG

function inputBarang(title,ev)
{
	notran=document.getElementById('notran').value;
	content= "<div id=formBarang style=\"height:450px;width:800px;overflow:scroll;\">";
	content+="<input type=hidden id=tampung  value="+ notran +" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25>";
	//content+="<div id=formCariBarang></div>";
	title='notran : '+notran;
	width='800';
	height='450';
	showDialog1(title,content,width,height,ev);	
	getFormBarang(notran);
}

function getFormBarang(notran)
{
	param='method=getFormBarang'+'&notran='+notran;
	//alert(param);
	tujuan = 'log_slave_packing.php';
	post_response_text(tujuan, param, respog);		
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
								//alert(con.responseText);
									document.getElementById('formBarang').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	} 
		
}


/*function cariBarang(title,ev)
{
	notran=document.getElementById('notran').value;
	content= "<div>";
	content+="<fieldset>Barang:<input type=text id=txtBarang class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariBarang()>Go</button>";
	content+="<input type=hidden id=tampung  value="+ notran +" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25> </fieldset>";
	content+="<div id=containercari style=\"height:300px;width:735px;overflow:scroll;\"></div></div>";
	title=title+' Barang:';
	width='750';
	height='350';
	showDialog1(title,content,width,height,ev);	
}

function goCariBarang()
{
	//alert('a');
	tampung=document.getElementById('tampung').value;
	txtBarang=trim(document.getElementById('txtBarang').value);
	kdOrg=document.getElementById('kdOrg').value;
	pt=document.getElementById('pt').value;
	if(txtBarang.length<4)
	{  
		alert('Text too short');
		return;
	}
	else
	{   
		param='method=goCariBarang'+'&pt='+pt+'&tampung='+tampung+'&kdOrg='+kdOrg+'&txtBarang='+txtBarang;
		tujuan = 'log_slave_packing.php';
		post_response_text(tujuan, param, respog);			
	}
	//alert(param);
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
}*/




//////////////////////////////////////////////////////////////PO


function cariNoPo(title,ev)
{
	notran=document.getElementById('notran').value;
	content= "<div>";
	content+="<fieldset><legend><b>Find No. PO</b></legend>No PO:<input type=text id=noPo class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariPo()>Go</button>";
	content+="<input type=hidden id=tampung  value="+ notran +" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25> ";
	content+="<fieldset><legend>Note</legend>Jika field jumlah berwarna orange, maka barang tersebut tidak bisa diinput dikarenakan jumlah barang tersebut sudah dikirim melalui PL lain</fieldset></fieldset>";
	content+="<input type=hidden id=tampung  value="+ notran +" class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25> </fieldset>";
	content+="<div id=containercari style=\"height:300px;width:1175px;overflow:scroll;\"></div></div>";
	title=title+' PO:';
	width='1200';
	height='400';
	showDialog1(title,content,width,height,ev);	
}

function goCariPo()
{
	//alert('a');
	tampung=document.getElementById('tampung').value;
	noPo=trim(document.getElementById('noPo').value);
	pt=document.getElementById('pt').value;
	if(noPo.length<4)
	{   
		alert('Text too short');
		return;
	}
	else
	{   
		param='method=goCariPo'+'&noPo='+noPo+'&pt='+pt+'&tampung='+tampung;
		tujuan = 'log_slave_packing.php';
		post_response_text(tujuan, param, respog);			
	}
	//alert(param);
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
		

function loadDataDetail()
{
	//alert('masuk');
	notran=document.getElementById('notran').value;
	param='method=loadDetail'+'&notran='+notran;
	//alert(param);
	tujuan='log_slave_packing.php';
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

		
function saveDetail(notran,nobpb,nopo,nopp,kodebarang,jumlah,satuanpo,matauang,kurs,hargasatuan,keteranganpp)
{
	if(notran=='')
	{
		alert('Some field was empty');return;
	}
	
	param='method=saveDetail'+'&notran='+notran+'&nobpb='+nobpb+'&nopo='+nopo;
	param+='&nopp='+nopp+'&kodebarang='+kodebarang+'&jumlah='+jumlah+'&satuanpo='+satuanpo;		
	param+='&matauang='+matauang+'&kurs='+kurs+'&hargasatuan='+hargasatuan+'&keteranganpp='+keteranganpp;	
	//alert(param);
	tujuan='log_slave_packing.php';
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
						
						//lockHeader();
						//document.getElementById('detailForm').style.display='block';
						
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function updateDetail(notran,nobpb,nopo,kodebarang,no,jumbpb,jumterkirim)
{
	
	jumlah=parseFloat(document.getElementById('jumlah'+no).value);
	jbpb=parseFloat(jumbpb);
	jkirim=parseFloat(jumterkirim);
	
	
	
	if((jumlah+jkirim)>jbpb)
	{
		alert('jumlah melebihi');return;
	}
	
	param='method=updateDetail'+'&notran='+notran+'&nobpb='+nobpb+'&nopo='+nopo+'&kodebarang='+kodebarang+'&jumlah='+jumlah;
	//alert(param);
	tujuan='log_slave_packing.php';
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
						
						//lockHeader();
						//document.getElementById('detailForm').style.display='block';
						loadDataDetail();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}





function cancel()
{
	document.location.reload();
}


/*function cancelDetail()
{
	tabAction(document.getElementById('tabFRM0'),0,'FRM',1);	
}
*/

function edit(notran,pt,tgl,peti,ket,serah,terima)
{
	tabAction(document.getElementById('tabFRM0'),0,'FRM',1);	
	
	
	//document.getElementById('nampung').value=notran;		
	//document.getElementById('listData').style.display='none';
	document.getElementById('header').style.display='block';
	document.getElementById('notran').value=notran;
	document.getElementById('pt').value=pt;
	document.getElementById('tgl').value=tgl;
	document.getElementById('peti').value=peti;
	document.getElementById('ket').value=ket;
	document.getElementById('serah').value=serah;
	document.getElementById('terima').value=terima;
	document.getElementById('method').value='update';
	//lockHeader();
	document.getElementById('detailForm').style.display='block';
	//document.getElementById('notranDet').value=notran;
	loadDataDetail();	
}

function saveHeader()
{
	notran=document.getElementById('notran').value;
	pt=document.getElementById('pt').value;
	tgl=document.getElementById('tgl').value;
	ket=document.getElementById('ket').value;
	peti=document.getElementById('peti').value;
	serah=document.getElementById('serah').value;
	terima=document.getElementById('terima').value;
	method=document.getElementById('method').value;
	
	
	if(notran=='' || pt=='' || tgl=='')
	{
		alert('Some field was empty');return;
	}
	
	param='&notran='+notran+'&pt='+pt+'&tgl='+tgl;
	param+='&ket='+ket+'&peti='+peti+'&serah='+serah+'&terima='+terima+'&method='+method;	
		//alert(param);

	tujuan='log_slave_packing.php';
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
						
						//lockHeader();
						document.getElementById('detailForm').style.display='block';
					//	document.getElementById('notranDet').value=notran;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}




function posting(notran)
{
	
	param='method=posting'+'&notran='+notran;
	//alert(param);
	tujuan='log_slave_packing.php';
	if(confirm('Are you sure posting this transaction,'+notran+'??'))
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
						loadData();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function delHead(notran)
{
	
	param='method=delHead'+'&notran='+notran;
	//alert(param);
	tujuan='log_slave_packing.php';
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
						loadData();
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
	kdPtSch=document.getElementById('kdPtSch').value;
	perSch=document.getElementById('perSch').value;
	notrn=document.getElementById('notransCari').value;
	param='method=loadData'+'&kdPtSch='+kdPtSch+'&perSch='+perSch+'&page='+num;
        param+='&notransCari='+notrn;
	tujuan = 'log_slave_packing.php';
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
	kdPtSch=document.getElementById('kdPtSch').value;
	perSch=document.getElementById('perSch').value;
        notrn=document.getElementById('notransCari').value;
	param='method=loadData'+'&kdPtSch='+kdPtSch+'&perSch='+perSch;
        param+='&notransCari='+notrn;
	//alert(param);	
	tujuan='log_slave_packing.php';
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
	document.getElementById('notran').disabled=true;
	document.getElementById('pt').disabled=true;
	document.getElementById('tgl').disabled=true;
	document.getElementById('ket').disabled=true;
	document.getElementById('peti').disabled=true;
	document.getElementById('serah').disabled=true;
	document.getElementById('terima').disabled=true;
	
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











// onclick=\"DelDetail('".$c['notransaksi']."','".$c['nobpb']."','".$c['kodebarang']."');\" ></td></tr>";
function DelDetail(notran,nobpb,nopo,kodebarang)
{
	param='method=deleteDetail'+'&notran='+notran+'&nobpb='+nobpb+'&nopo='+nopo+'&kodebarang='+kodebarang;
	//alert(param);
	tujuan='log_slave_packing.php';
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
						//clearDetail();
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



