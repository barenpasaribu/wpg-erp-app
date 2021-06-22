function batal()
{
	document.getElementById('kodeorgLap').value='';
	document.getElementById('tglLap').value='';	
	document.getElementById('produkLap').value='';	
	document.getElementById('printContainer').innerHTML='';	
}


function cancel()
{
	document.location.reload();
}
function totS($urut)
{
	s1inp=parseFloat(document.getElementById('1inp'+$urut).value);
	s2inp=parseFloat(document.getElementById('2inp'+$urut).value);
	hsl = (s1inp+s2inp);
	hslper = hsl/2;
	console.log(s1inp);
	console.log(s2inp);
	console.log(hsl);
	hasil = hslper.toFixed(2);
	document.getElementById('inp'+$urut).value = hasil;

}

function getForm()
{
	document.getElementById('editForm').style.display='none';
	kodeorg=document.getElementById('kodeorg').value;
	produk=document.getElementById('produk').value;
	
	
	if(produk=='')
	{
		//document.getElementById('form').style.display='none';
		cancel();
	}
	
		/*document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='none';
		document.getElementById('hijau').style.display='none';*/
	
	param='method=getForm'+'&kodeorg='+kodeorg+'&produk='+produk;
	tujuan='pabrik_slave_kelengkapanloses.php';
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
						document.getElementById('form').style.display='block';
						document.getElementById('isi').innerHTML=con.responseText;
						//document.getElementById('kar').innerHTML=							
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}
function getFormAnalisa()
{
	document.getElementById('editForm').style.display='none';
	tipe=document.getElementById('tipeanalisa').value;
	subunit=document.getElementById('subunit').value;
	
	
	if(subunit=='0' || tipe=='0' )
	{
		//document.getElementById('form').style.display='none';
		cancel();
	}
	
		/*document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='none';
		document.getElementById('hijau').style.display='none';*/
	
	param='method=getForm'+'&tipe='+tipe+'&subunit='+subunit;
	tujuan='pabrik_slave_analisa.php';
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
						document.getElementById('form').style.display='block';
						document.getElementById('isi').innerHTML=con.responseText;
						//document.getElementById('kar').innerHTML=							
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}
function getFormAnalisaExternal()
{
	document.getElementById('editForm').style.display='none';
	sumber=document.getElementById('sumber').value;
	lab=document.getElementById('lab').value;
	bulan=document.getElementById('bulan').value;
	tahun=document.getElementById('tahun').value;
	
	if(sumber=='' || lab=='' || bulan=='0' || tahun=='0'  )
	{
		//document.getElementById('form').style.display='none';

		alert('Mohon isi dengan lengkap !!');
		cancel();

	}else{
		param='method=getForm';
	tujuan='pabrik_slave_analisa_external.php';
    post_response_text(tujuan, param, respog);  
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
						
						//alert(con.responseText);
						document.getElementById('form').style.display='block';
						document.getElementById('isi').innerHTML=con.responseText;
						//document.getElementById('kar').innerHTML=							
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}





function saveAll(maxRow)
{     
	maxf=maxRow;
	loopsave(1,maxRow);
}

function loopsave(currRow,maxRow)
{
	kodeorg=document.getElementById('kodeorg').value;
	tgl=document.getElementById('tgl').value;
	//produk=document.getElementById('produk').value;
	inp=document.getElementById('inp'+currRow).value;
	inp1=document.getElementById('1inp'+currRow).value;
	inp2=document.getElementById('2inp'+currRow).value;
	shift1=document.getElementById('shift1').value;
	shift2=document.getElementById('shift2').value;
	id=document.getElementById('id'+currRow).value;
	//method=document.getElementById('method').value;
	//imp=trim(document.getElementById('premi'+currRow).innerHTML);
	
	if(kodeorg=='' || tgl=='' || produk=='')
	{
		alert("Field Empty");return;
	}	
    else
	{  
	    param='kodeorg='+kodeorg+'&tgl='+tgl+'&inp='+inp+'&id='+id+'&inp1='+inp1+'&inp2='+inp2+'&shift1='+shift1+'&shift2='+shift2;
		param+="&method=savedata";
		
		//alert(param);
		tujuan = 'pabrik_slave_kelengkapanloses.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	}
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
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						//alert('Done');
						//unlockScreen();
						cancel();
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
function saveAllAnalisa(maxRow)
{     
	maxf=maxRow;
	loopsaveanalisa(1,maxRow);
}

function loopsaveanalisa(currRow,maxRow)
{
	tipeanalisa=document.getElementById('tipeanalisa').value;
	subunit=document.getElementById('subunit').value;
	tanggal=document.getElementById('tanggal').value;
	//produk=document.getElementById('produk').value;
	inp=document.getElementById('inp'+currRow).value;
	id=document.getElementById('id'+currRow).value;
	//method=document.getElementById('method').value;
	//imp=trim(document.getElementById('premi'+currRow).innerHTML);
	
	if(tipeanalisa=='0' || subunit=='' )
	{
		alert("Field Empty");return;
	}	
    else
	{  
	    param='tipeanalisa='+tipeanalisa+'&subunit='+subunit+'&tanggal='+tanggal+'&inp='+inp+'&id='+id;
		param+="&method=savedata";
		
		//alert(param);
		tujuan = 'pabrik_slave_analisa.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	}
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
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						//alert('Done');
						//unlockScreen();
						cancel();
						//document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsaveanalisa(currRow,maxRow);
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


function del(kodeorg,tgl,id)
{
	
	param='method=delete'+'&kodeorg='+kodeorg+'&tgl='+tgl+'&id='+id;
	tujuan='pabrik_slave_kelengkapanloses.php';
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
function delAnalisa(id)
{
	
	param='method=delete'+'&id='+id;
	tujuan='pabrik_slave_analisa.php';
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
function delAnalisaExternal(id)
{
	
	param='method=delete'+'&notransaksi='+id;
	tujuan='pabrik_slave_analisa_external.php';
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

function quickPosting()
{
	tanggal = document.getElementById('tanggal_quick_posting').value;
	param='proses=quickPosting'+'&tanggal='+tanggal;
	tujuan='pabrik_slave_get_data.php';

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
					alert(con.responseText);
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
function quickPostingAnalisa()
{
	tanggal = document.getElementById('tanggal_quick_posting').value;
	param='method=quickPosting'+'&tanggalPosting='+tanggal;
	tujuan='pabrik_slave_analisa.php';

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
					alert(con.responseText);
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

function posting(kodeorg,tgl,id)
{
	
	param='method=posting'+'&kodeorg='+kodeorg+'&tgl='+tgl+'&id='+id;
	tujuan='pabrik_slave_kelengkapanloses.php';
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
function postingAnalisa(id)
{
	
	param='method=posting'+'&id='+id;
	tujuan='pabrik_slave_analisa.php';
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
function postingAnalisaExternal(id)
{
	
	param='method=posting'+'&notransaksi='+id;
	tujuan='pabrik_slave_analisa_external.php';
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


function edit(kodeorgEdit,tglEdit,produkEdit,barangEdit,inpEdit,idEdit,inpEdit1,inpEdit2,shift1,shift2)
{
	document.getElementById('tgl').value='';
	document.getElementById('produk').value='';
	document.getElementById('editForm').style.display='block';
	document.getElementById('form').style.display='none';
	document.getElementById('kodeorgEdit').value=kodeorgEdit;
	document.getElementById('tglEdit').value=tglEdit;

	document.getElementById('produkEdit').value=produkEdit;
	document.getElementById('barangEdit').value=barangEdit;
	document.getElementById('inpEdit').value=inpEdit;
	document.getElementById('inpEdit1').value=inpEdit1;
	document.getElementById('inpEdit2').value=inpEdit2;
	document.getElementById('idEdit').value=idEdit;
}
function editAnalisa(kodeorgEdit,tglEdit,subUnitEdit,parameterEdit,inpEdit,idEdit,tipeEdit)
{
	
	document.getElementById('editForm').style.display='block';
	document.getElementById('form').style.display='none';
	document.getElementById('tanggal').value=tglEdit;
	document.getElementById('tipeanalisa').value=tipeEdit;
	document.getElementById('parameterEdit').value=parameterEdit;
	document.getElementById('subunit').value=subUnitEdit;
	document.getElementById('inpEdit').value=inpEdit;
	document.getElementById('idEdit').value=idEdit;
}
function editAnalisaExternal(notransaksi,sumber,lab,parameter,nilai,id,bulan,tahun)
{
	
	document.getElementById('editForm').style.display='block';
	document.getElementById('form').style.display='none';
	document.getElementById('notransaksi').value=notransaksi;
	document.getElementById('sumber').value=sumber;
	document.getElementById('lab').value=lab;
	document.getElementById('bulan').value=bulan;
	document.getElementById('tahun').value=tahun;
	document.getElementById('parameterEdit').value=parameter;
	document.getElementById('inpEdit').value=nilai;
	document.getElementById('idEdit').value=id;
}
function saveEdit ()
{
	
	kodeorgEdit=document.getElementById('kodeorgEdit').value;
	tglEdit=document.getElementById('tglEdit').value;
	produkEdit=document.getElementById('produkEdit').value;
	barangEdit=document.getElementById('barangEdit').value;
	inpEdit=document.getElementById('inpEdit').value;
	inpEdit1=document.getElementById('inpEdit1').value;
	inpEdit2=document.getElementById('inpEdit2').value;
	idEdit=document.getElementById('idEdit').value;
	console.log(inpEdit);
	console.log(inpEdit1);
	console.log(inpEdit2);
	param='method=update'+'&kodeorgEdit='+kodeorgEdit+'&tglEdit='+tglEdit+'&idEdit='+idEdit+'&inpEdit='+inpEdit+'&inpEdit1='+inpEdit1+'&inpEdit2='+inpEdit2;
	tujuan='pabrik_slave_kelengkapanloses.php';
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
function saveEditAnalisa ()
{
	
	
	inpEdit=document.getElementById('inpEdit').value;
	idEdit=document.getElementById('idEdit').value;
	
	param='method=update'+'&idEdit='+idEdit+'&inpEdit='+inpEdit;
	tujuan='pabrik_slave_analisa.php';
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
function saveEditAnalisaExternal ()
{
	
	
	inpEdit=document.getElementById('inpEdit').value;
	idEdit=document.getElementById('idEdit').value;
	
	param='method=update'+'&idEdit='+idEdit+'&inpEdit='+inpEdit;
	tujuan='pabrik_slave_analisa_external.php';
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

function SaveAnalisaExternal (maxRow)
{
	
	
	sumber=document.getElementById('sumber').value;
	lab=document.getElementById('lab').value;
	bulan=document.getElementById('bulan').value;
	tahun=document.getElementById('tahun').value;
	notransaksi=document.getElementById('notransaksi').value;
	
	param='method=savedataht'+'&sumber='+sumber+'&lab='+lab+'&bulan='+bulan+'&tahun='+tahun+'&notransaksi='+notransaksi;
	tujuan='pabrik_slave_analisa_external.php';
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
						loopsaveanalisaExternal(1,maxRow);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function loopsaveanalisaExternal(currRow,maxRow)
{
	notransaksi=document.getElementById('notransaksi').value;
	subunit="10";
	//produk=document.getElementById('produk').value;
	inp=document.getElementById('inp'+currRow).value;
	id=document.getElementById('id'+currRow).value;
	//method=document.getElementById('method').value;
	//imp=trim(document.getElementById('premi'+currRow).innerHTML);
	

		
		
  
	    param='notransaksi='+notransaksi+'&subunit='+subunit+'&inp='+inp+'&id='+id;
		param+="&method=savedata";
		
		//alert(param);
		tujuan = 'pabrik_slave_analisa_external.php';
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
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						//alert('Done');
						//unlockScreen();
						cancel();
						//document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsaveanalisaExternal(currRow,maxRow);
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



function cariBast(num)
{
		tglsch=document.getElementById('tglsch').value;
		kodeorg=document.getElementById('kodeorg').value;
		param='method=loadData'+'&tglsch='+tglsch+'&kodeorg='+kodeorg+'&page='+num;
		tujuan = 'pabrik_slave_kelengkapanloses.php';
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
	tglsch=document.getElementById('tglsch').value;
	kodeorg=document.getElementById('kodeorg').value;
	param='method=loadData'+'&tglsch='+tglsch+'&kodeorg='+kodeorg;
	//alert(param);	
	tujuan='pabrik_slave_kelengkapanloses.php';
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
function loadDataAnalisa () 
{
	tglsch=document.getElementById('tglsch').value;
	tipeanalisa=document.getElementById('tipeanalisasch').value;
	param='method=loadData'+'&tglsch='+tglsch+'&tipeanalisasch='+tipeanalisa;
	//alert(param);	
	tujuan='pabrik_slave_analisa.php';
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
function loadDataAnalisaExternal () 
{
	bulan=document.getElementById('bulansch').value;
	tahun=document.getElementById('tahunsch').value;
	param='method=loadData'+'&tahunsch='+tahun+'&bulansch='+bulan;
	//alert(param);	
	tujuan='pabrik_slave_analisa_external.php';
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
















