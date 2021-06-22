//JS 

function getBlok()
{
	
	divisi=document.getElementById('divisi').value;
	method=document.getElementById('method').value;
	param='method=getBlok'+'&divisi='+divisi;
	tujuan='vhc_slave_5jarakBlok.php';
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
							document.getElementById('kodeblok').innerHTML=con.responseText;
							
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
		divisiSch=document.getElementById('divisiSch').value;
		param='method=loadData'+'&divisiSch='+divisiSch+'&page='+num;		
		tujuan = 'vhc_slave_5jarakBlok.php';
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

function simpan()
{
	regional=document.getElementById('regional').value;
	divisi=document.getElementById('divisi').value;
	kodeblok=document.getElementById('kodeblok').value;
	jarak=document.getElementById('jarak').value;
	method=document.getElementById('method').value;
	
	if(regional=='' || divisi==''|| kodeblok==''|| jarak=='')
	{
		alert('Field was empty');
		return;
	}
	param='regional='+regional+'&divisi='+divisi+'&kodeblok='+kodeblok+'&jarak='+jarak+'&method='+method;
	tujuan='vhc_slave_5jarakBlok.php';
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
					


function cancel()
{
	document.location.reload();
}




function loadData () 
{
	
	divisiSch=document.getElementById('divisiSch').value;
	param='method=loadData'+'&divisiSch='+divisiSch;
	tujuan='vhc_slave_5jarakBlok.php';
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

function edit(regional,kdkegiatan,rp,insen,konversi)
{
	document.getElementById('regional').value=regional;
	document.getElementById('kdkegiatan').value=kdkegiatan;
	document.getElementById('kdkegiatan').disabled=true;
	document.getElementById('rp').value=rp;
	document.getElementById('insen').value=insen;
	if(konversi==1)
	{
		document.getElementById('konversi').checked=true;
	}
	else
	{
		document.getElementById('konversi').checked=false;
	}
	document.getElementById('method').value='update';
}



function del(kodeblok)
{
	param='method=delete'+'&kodeblok='+kodeblok;
	//alert(param);
	tujuan='vhc_slave_5jarakBlok.php';
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




