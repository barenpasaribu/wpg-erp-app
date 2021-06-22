//JS 
//ind
function excel(ev,tujuan)
{
	per=document.getElementById('per');
	per=per.options[per.selectedIndex].value;
	kodeorg=document.getElementById('kodeorg').value;
                
	judul='Report Ms.Excel';	
	param='method=excel'+'&per='+per+'&kodeorg='+kodeorg;
	printFile(param,tujuan,judul,ev)	
}

function getKar()
{
	kodeorg=document.getElementById('kodeorg').value;
	jabatan=document.getElementById('jabatan').value;
	per=document.getElementById('per').value;
	if(kodeorg=='' || jabatan=='' || per=='')
	{	
		alert('Field Was Empty');return;
	}
	
	//if(jabatan=='Kerani')
	if(jabatan=='Kerani')
	{	
		document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='block';
		document.getElementById('hijau').style.display='none';
	}
	//else if(jabatan=='Mandor')
	else if(jabatan=='Mandor')
	{
		document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='none';
		document.getElementById('hijau').style.display='block';
	}
	else
	{
		document.getElementById('merah').style.display='none';
		document.getElementById('kuning').style.display='none';
		document.getElementById('hijau').style.display='none';
	}
	
	param='method=getKar'+'&kodeorg='+kodeorg+'&jabatan='+jabatan+'&per='+per;
	tujuan='kebun_slave_premimandorbaru.php';
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
						document.getElementById('kar').innerHTML=con.responseText;
						
						document.getElementById('pembagi').value=0;
						document.getElementById('bjr').value=0;
						document.getElementById('totpanen').value=0;
						document.getElementById('premi').value=0;
						//hijau
						document.getElementById('m1').value=0;
							document.getElementById('rpm1').value=0;
						document.getElementById('m2').value=0;
							document.getElementById('rpm2').value=0;
						document.getElementById('m3').value=0;
							document.getElementById('rpm3').value=0;
						document.getElementById('m4').value=0;
							document.getElementById('rpm4').value=0;
						document.getElementById('m5').value=0;
							document.getElementById('rpm5').value=0;
						document.getElementById('m6').value=0;
							document.getElementById('rpm6').value=0;
							
						//merah
						document.getElementById('r1').value=0;
							document.getElementById('rpr1').value=0;
						document.getElementById('r2').value=0;
							document.getElementById('rpr2').value=0;
						document.getElementById('r3').value=0;
							document.getElementById('rpr3').value=0;
						document.getElementById('r4').value=0;
							document.getElementById('rpr4').value=0;	
							
						//kuning	
						document.getElementById('k1').value=0;
							document.getElementById('rpk1').value=0;
						document.getElementById('k2').value=0;
							document.getElementById('rpk2').value=0;
						document.getElementById('k3').value=0;
							document.getElementById('rpk3').value=0;
						document.getElementById('k4').value=0;
							document.getElementById('rpk4').value=0;
						document.getElementById('k5').value=0;
							document.getElementById('rpk5').value=0;	
						
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}



/*function getWarna()
{
	
	jabatan=document.getElementById('jabatan').value;
	if(jabatan=='Mandor')
	{
		//merah
		r1=document.getElementById('r1').disabled=true;
		r2=document.getElementById('r2').disabled=true;
		r3=document.getElementById('r3').disabled=true;
		r4=document.getElementById('r4').disabled=true;
			
		//kuning	
		k1=document.getElementById('k1').disabled=true;
		k2=document.getElementById('k2').disabled=true;
		k3=document.getElementById('k3').disabled=true;
		k4=document.getElementById('k4').disabled=true;
		k5=document.getElementById('k5').disabled=true;
			
			
		//hijau
		m1=document.getElementById('m1').disabled=false;
			rpm1=document.getElementById('rpm1').disabled=false;
		m2=document.getElementById('m2').disabled=false;
			rpm2=document.getElementById('rpm2').disabled=false;
		m3=document.getElementById('m3').disabled=false;
			rpm3=document.getElementById('rpm3').disabled=false;
		m4=document.getElementById('m4').disabled=false;
			rpm4=document.getElementById('rpm4').disabled=false;
		m5=document.getElementById('m5').disabled=false;
			rpm5=document.getElementById('rpm5').disabled=false;
		m6=document.getElementById('m6').disabled=false;
			rpm6=document.getElementById('rpm6').disabled=false;			
			
	}
	else if(jabatan=='Kerani')
	{
		//hijau
		m1=document.getElementById('m1').disabled=true;
			rpm1=document.getElementById('rpm1').disabled=true;
		m2=document.getElementById('m2').disabled=true;
			rpm2=document.getElementById('rpm2').disabled=true;
		m3=document.getElementById('m3').disabled=true;
			rpm3=document.getElementById('rpm3').disabled=true;
		m4=document.getElementById('m4').disabled=true;
			rpm4=document.getElementById('rpm4').disabled=true;
		m5=document.getElementById('m5').disabled=true;
			rpm5=document.getElementById('rpm5').disabled=true;
		m6=document.getElementById('m6').disabled=true;
			rpm6=document.getElementById('rpm6').disabled=true;
		//merah
		r1=document.getElementById('r1').disabled=true;
		r2=document.getElementById('r2').disabled=true;
		r3=document.getElementById('r3').disabled=true;
		r4=document.getElementById('r4').disabled=true;
		
		//kuning	
		k1=document.getElementById('k1').disabled=false;
		k2=document.getElementById('k2').disabled=false;
		k3=document.getElementById('k3').disabled=false;
		k4=document.getElementById('k4').disabled=false;
		k5=document.getElementById('k5').disabled=false;
						
	}
	else if(jabatan=='RECORDER')
	{
		//hijau disabled
		m1=document.getElementById('m1').disabled=true;
			rpm1=document.getElementById('rpm1').disabled=true;
		m2=document.getElementById('m2').disabled=true;
			rpm2=document.getElementById('rpm2').disabled=true;
		m3=document.getElementById('m3').disabled=true;
			rpm3=document.getElementById('rpm3').disabled=true;
		m4=document.getElementById('m4').disabled=true;
			rpm4=document.getElementById('rpm4').disabled=true;
		m5=document.getElementById('m5').disabled=true;
			rpm5=document.getElementById('rpm5').disabled=true;
		m6=document.getElementById('m6').disabled=true;
			rpm6=document.getElementById('rpm6').disabled=true;
		
		//kuning disabled	
		k1=document.getElementById('k1').disabled=true;
			
		k2=document.getElementById('k2').disabled=true;
			
		k3=document.getElementById('k3').disabled=true;
			
		k4=document.getElementById('k4').disabled=true;
			
		k5=document.getElementById('k5').disabled=true;
			
		
		r1=document.getElementById('r1').disabled=false;
			
		r2=document.getElementById('r2').disabled=false;
			
		r3=document.getElementById('r3').disabled=false;
			
		r4=document.getElementById('r4').disabled=false;
				
			
								
	}
	else 
	{
		//hijau
		m1=document.getElementById('m1').disabled=true;
			rpm1=document.getElementById('rpm1').disabled=true;
		m2=document.getElementById('m2').disabled=true;
			rpm2=document.getElementById('rpm2').disabled=true;
		m3=document.getElementById('m3').disabled=true;
			rpm3=document.getElementById('rpm3').disabled=true;
		m4=document.getElementById('m4').disabled=true;
			rpm4=document.getElementById('rpm4').disabled=true;
		m5=document.getElementById('m5').disabled=true;
			rpm5=document.getElementById('rpm5').disabled=true;
		m6=document.getElementById('m6').disabled=true;
			rpm6=document.getElementById('rpm6').disabled=true;
			
		//merah
		r1=document.getElementById('r1').disabled=true;
			
		r2=document.getElementById('r2').disabled=true;
			
		r3=document.getElementById('r3').disabled=true;
			
		r4=document.getElementById('r4').disabled=true;
			
			
		//kuning	
		k1=document.getElementById('k1').disabled=true;
			
		k2=document.getElementById('k2').disabled=true;
			
		k3=document.getElementById('k3').disabled=true;
			
		k4=document.getElementById('k4').disabled=true;
			
		k5=document.getElementById('k5').disabled=true;
			
	}		
}*/
					
function getPembagi()
{
	kodeorg=document.getElementById('kodeorg').value;
	per=document.getElementById('per').value;
	kar=document.getElementById('kar').value;
	jabatan=document.getElementById('jabatan').value;
	param='method=getPembagi'+'&jabatan='+jabatan+'&kar='+kar+'&per='+per+'&kodeorg='+kodeorg;
	tujuan='kebun_slave_premimandorbaru.php';
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
						ar=con.responseText.split("###");
						vPembagi=ar[0];
						vJabatan=ar[1];
						vBjr=ar[2];
						vHasilKerja=ar[3];
						if(vJabatan=='Mandor Satu')
						{
							//alert('asd');
							document.getElementById('pembagi').value=0;
							document.getElementById('pembagi').disabled=true;	
							document.getElementById('bjr').value=0;
							document.getElementById('bjr').disabled=true;
							document.getElementById('totpanen').value=0;
							document.getElementById('totpanen').disabled=true;
							document.getElementById('premi').value=0;
							document.getElementById('premi').disabled=true;
							
						}
						else
						{
							
							//document.getElementById('pembagi').disabled=false;	
							document.getElementById('pembagi').value=remove_comma_var(vPembagi);
							//document.getElementById('bjr').disabled=false;	
							document.getElementById('bjr').value=remove_comma_var(vBjr);
							//document.getElementById('totpanen').disabled=false;
							document.getElementById('totpanen').value=remove_comma_var(vHasilKerja);
							
							//document.getElementById('premi').disabled=false;
							
						}
						getPremi();
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);  
                }
      }	
     } 
}




function getPremi()
{
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	kar=document.getElementById('kar').value;
	pembagi=document.getElementById('pembagi').value;
	bjr=document.getElementById('bjr').value;
	totpanen=document.getElementById('totpanen').value;
	jabatan=document.getElementById('jabatan').value;
	
	param='method=getPremi'+'&pembagi='+pembagi+'&bjr='+bjr+'&totpanen='+totpanen+'&kodeorg='+kodeorg+'&kar='+kar+'&jabatan='+jabatan+'&per='+per;
	//alert(param);
	tujuan='kebun_slave_premimandorbaru.php';
	
	if(jabatan=='Mandor Satu')
	{
		//if(confirm("pastikan semuna premi mandor sudah disimpan"))
		if(confirm("Please check all premi mandor has ready input"))
		{
			
			post_response_text(tujuan, param, respog);
		}
	}
	else
	{
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
							document.getElementById('premi').value=remove_comma_var(con.responseText);
							document.getElementById('pterima').value=remove_comma_var(con.responseText);
							getHijau();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}


function getHijau()
{
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	kar=document.getElementById('kar').value;
	//jabatan=document.getElementById('jabatan').value;
	
	param='method=getHijau'+'&per='+per+'&kodeorg='+kodeorg+'&kar='+kar;
	tujuan='kebun_slave_premimandorbaru.php';
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
							
							ar=con.responseText.split("###");
							document.getElementById('m1').value=remove_comma_var(ar[0]);
							document.getElementById('m2').value=remove_comma_var(ar[1]);
							document.getElementById('m3').value=remove_comma_var(ar[2]);
							document.getElementById('m4').value=remove_comma_var(ar[3]);
							document.getElementById('m5').value=remove_comma_var(ar[4]);
							document.getElementById('m6').value=remove_comma_var(ar[5]);
							tHijau();
							//getPremiDitCon();
							
							//document.getElementById('premi').value=remove_comma_var(con.responseText);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}

/*function getPremiDitCon()
{
	
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	jabatan=document.getElementById('jabatan').value;
	kar=document.getElementById('kar').value;
	param='method=getPremiDitCon'+'&per='+per+'&kodeorg='+kodeorg+'&kar='+kar+'&jabatan='+jabatan;
	tujuan='kebun_slave_premimandorbaru.php';
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
							
							//ar=con.responseText.split("###");
							
							document.getElementById('pterima').value=remove_comma_var(con.responseText);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}*/


function tKuning()
{
	premi=document.getElementById('premi').value;
	jabatan=document.getElementById('jabatan').value;
	
	k1=document.getElementById('k1').value;
		rpk1=document.getElementById('rpk1').value;
	k2=document.getElementById('k2').value;
		rpk2=document.getElementById('rpk2').value;
	k3=document.getElementById('k3').value;
		rpk3=document.getElementById('rpk3').value;
	k4=document.getElementById('k4').value;
		rpk4=document.getElementById('rpk4').value;
	k5=document.getElementById('k5').value;
		rpk5=document.getElementById('rpk5').value;
	
	param='method=tKuning'+'&premi='+premi+'&jabatan='+jabatan;
	param+='&k1='+k1+'&k2='+k2+'&k3='+k3+'&k4='+k4+'&k5='+k5;
	param+='&rpk1='+rpk1+'&rpk2='+rpk2+'&rpk3='+rpk3+'&rpk4='+rpk4+'&rpk5='+rpk5;
	//alert(param);
	tujuan='kebun_slave_premimandorbaru.php';
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
							
							ar=con.responseText.split("###");
							document.getElementById('rpk1').value=remove_comma_var(ar[0]);
							document.getElementById('rpk2').value=remove_comma_var(ar[1]);
							document.getElementById('rpk3').value=remove_comma_var(ar[2]);
							document.getElementById('rpk4').value=remove_comma_var(ar[3]);
							document.getElementById('rpk5').value=remove_comma_var(ar[4]);
							document.getElementById('pterima').value=remove_comma_var(ar[5]);
							//getPremiDitCon();
							
							//document.getElementById('premi').value=remove_comma_var(con.responseText);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}


function tHijau()
{
	premi=document.getElementById('premi').value;
	jabatan=document.getElementById('jabatan').value;
	
	m1=document.getElementById('m1').value;
		rpm1=document.getElementById('rpm1').value;
	m2=document.getElementById('m2').value;
		rpm2=document.getElementById('rpm2').value;
	m3=document.getElementById('m3').value;
		rpm3=document.getElementById('rpm3').value;
	m4=document.getElementById('m4').value;
		rpm4=document.getElementById('rpm4').value;
	m5=document.getElementById('m5').value;
		rpm5=document.getElementById('rpm5').value;
	m6=document.getElementById('m6').value;
		rpm6=document.getElementById('rpm6').value;
	
	param='method=tHijau'+'&premi='+premi+'&jabatan='+jabatan;
	param+='&m1='+m1+'&m2='+m2+'&m3='+m3+'&m4='+m4+'&m5='+m5+'&m6='+m6;
		param+='&rpm1='+rpm1+'&rpm2='+rpm2+'&rpm3='+rpm3+'&rpm4='+rpm4+'&rpm5='+rpm5+'&rpm6='+rpm6;
	//alert(param);
	tujuan='kebun_slave_premimandorbaru.php';
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
							
							ar=con.responseText.split("###");
							document.getElementById('rpm1').value=remove_comma_var(ar[0]);
							document.getElementById('rpm2').value=remove_comma_var(ar[1]);
							document.getElementById('rpm3').value=remove_comma_var(ar[2]);
							document.getElementById('rpm4').value=remove_comma_var(ar[3]);
							document.getElementById('rpm5').value=remove_comma_var(ar[4]);
							document.getElementById('rpm6').value=remove_comma_var(ar[5]);
							document.getElementById('pterima').value=remove_comma_var(ar[6]);
							//getPremiDitCon();
							
							//document.getElementById('premi').value=remove_comma_var(con.responseText);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}



function tMerah()
{
	premi=document.getElementById('premi').value;
	jabatan=document.getElementById('jabatan').value;
	
	r1=document.getElementById('r1').value;
		rpr1=document.getElementById('rpr1').value;
	r2=document.getElementById('r2').value;
		rpr2=document.getElementById('rpr2').value;
	r3=document.getElementById('r3').value;
		rpr3=document.getElementById('rpr3').value;
	r4=document.getElementById('r4').value;
		rpr4=document.getElementById('rpr4').value;	
	
	param='method=tMerah'+'&premi='+premi+'&jabatan='+jabatan;
	param+='&r1='+r1+'&r2='+r2+'&r3='+r3+'&r4='+r4;	
	param+='&rpr1='+rpr1+'&rpr2='+rpr2+'&rpr3='+rpr3+'&rpr4='+rpr4;	
	//alert(param);
	tujuan='kebun_slave_premimandorbaru.php';
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
							
							ar=con.responseText.split("###");
							document.getElementById('rpr1').value=remove_comma_var(ar[0]);
							document.getElementById('rpr2').value=remove_comma_var(ar[1]);
							document.getElementById('rpr3').value=remove_comma_var(ar[2]);
							document.getElementById('rpr4').value=remove_comma_var(ar[3]);
							document.getElementById('pterima').value=remove_comma_var(ar[4]);
							//getPremiDitCon();
							
							//document.getElementById('premi').value=remove_comma_var(con.responseText);
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
	
}

function simpan()
{
	method=document.getElementById('method').value;
	
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	jabatan=document.getElementById('jabatan').value;
	kar=document.getElementById('kar').value;
	pembagi=document.getElementById('pembagi').value;
	bjr=document.getElementById('bjr').value;
	totpanen=document.getElementById('totpanen').value;
	premi=document.getElementById('premi').value;
	
	//hijau
	m1=document.getElementById('m1').value;
		rpm1=document.getElementById('rpm1').value;
	m2=document.getElementById('m2').value;
		rpm2=document.getElementById('rpm2').value;
	m3=document.getElementById('m3').value;
		rpm3=document.getElementById('rpm3').value;
	m4=document.getElementById('m4').value;
		rpm4=document.getElementById('rpm4').value;
	m5=document.getElementById('m5').value;
		rpm5=document.getElementById('rpm5').value;
	m6=document.getElementById('m6').value;
		rpm6=document.getElementById('rpm6').value;
		
	//merah
	r1=document.getElementById('r1').value;
		rpr1=document.getElementById('rpr1').value;
	r2=document.getElementById('r2').value;
		rpr2=document.getElementById('rpr2').value;
	r3=document.getElementById('r3').value;
		rpr3=document.getElementById('rpr3').value;
	r4=document.getElementById('r4').value;
		rpr4=document.getElementById('rpr4').value;	
		
	//kuning	
	k1=document.getElementById('k1').value;
		rpk1=document.getElementById('rpk1').value;
	k2=document.getElementById('k2').value;
		rpk2=document.getElementById('rpk2').value;
	k3=document.getElementById('k3').value;
		rpk3=document.getElementById('rpk3').value;
	k4=document.getElementById('k4').value;
		rpk4=document.getElementById('rpk4').value;
	k5=document.getElementById('k5').value;
		rpk5=document.getElementById('rpk5').value;
	
	pterima=document.getElementById('pterima').value;		
	

	if(per=='' || kodeorg=='' || jabatan=='' || kar=='' || pembagi=='' || bjr=='' || totpanen=='' || premi=='')
	{	
		alert('Field Was Empty');return;
	}
	
	param='per='+per+'&kodeorg='+kodeorg+'&jabatan='+jabatan+'&kar='+kar+'&method='+method;
	param+='&pembagi='+pembagi+'&bjr='+bjr+'&totpanen='+totpanen+'&premi='+premi;
	param+='&m1='+m1+'&m2='+m2+'&m3='+m3+'&m4='+m4+'&m5='+m5+'&m6='+m6;
		param+='&rpm1='+rpm1+'&rpm2='+rpm2+'&rpm3='+rpm3+'&rpm4='+rpm4+'&rpm5='+rpm5+'&rpm6='+rpm6;
	param+='&r1='+r1+'&r2='+r2+'&r3='+r3+'&r4='+r4;	
		param+='&rpr1='+rpr1+'&rpr2='+rpr2+'&rpr3='+rpr3+'&rpr4='+rpr4;	
	param+='&k1='+k1+'&k2='+k2+'&k3='+k3+'&k4='+k4+'&k5='+k5;
		param+='&rpk1='+rpk1+'&rpk2='+rpk2+'&rpk3='+rpk3+'&rpk4='+rpk4+'&rpk5='+rpk5;
	param+='&pterima='+pterima;
	
	tujuan='kebun_slave_premimandorbaru.php';
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


function cariBast(num)
{
		per=document.getElementById('per').value;
		kodeorg=document.getElementById('kodeorg').value;
		param='method=loadData'+'&per='+per+'&kodeorg='+kodeorg+'&page='+num;
		tujuan = 'kebun_slave_premimandorbaru.php';
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


function cancel()
{
	document.location.reload();
}

function loadData () 
{
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	param='method=loadData'+'&per='+per+'&kodeorg='+kodeorg;
	
	tujuan='kebun_slave_premimandorbaru.php';
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


