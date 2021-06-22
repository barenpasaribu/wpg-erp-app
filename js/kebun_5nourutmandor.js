//JS 

function simpan()
{
	nm=document.getElementById('nm').value;
	nu=document.getElementById('nu').value;
	ki=document.getElementById('ki').value;
	st=document.getElementById('st').value;
	
	oldnm=document.getElementById('oldnm').value;
	oldnu=document.getElementById('oldnu').value;
	oldki=document.getElementById('oldki').value;
	
	method=document.getElementById('method').value;
	

	if(trim(nm)=='')
	{
		alert('Nik Mandor masih kosong');
		document.getElementById('nm').focus();
	}
	else if(trim(nu)=='')
	{
		alert('Nomor Urut masih kosong');
		document.getElementById('nu').focus();
	}
	else if(trim(ki)=='')
	{
		alert('Karyawan ID kosong');
		document.getElementById('ki').focus();
	}
	else if(trim(st)=='')
	{
		alert('Status masi kosong');
		document.getElementById('st').focus();
	}
	else
	{
		nm=trim(nm);
		nu=trim(nu);
		ki=trim(ki);
		st=trim(st);

	param='nm='+nm+'&nu='+nu+'&ki='+ki+'&st='+st+'&method='+method;
	param+='&oldnm='+oldnm+'&oldnu='+oldnu+'&oldki='+oldki;
	//alert(param);

	

	tujuan='slave_kebun_5nourutmandor.php';
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
							hapus();							
                            loadData();
							//document.getElementById('container').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}
					


function hapus()
{
	document.getElementById('nm').value='';
	document.getElementById('nu').value='';
	document.getElementById('ki').value='';
	document.getElementById('st').value='';	
	document.getElementById('method').value='insert';	
}


function loadData () 
{
	param='method=loadData';
	tujuan='slave_kebun_5nourutmandor.php';
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
                                    document.getElementById('containerData').innerHTML=con.responseText;	
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
}



function fillField(nm,nu,ki,st)
{
	document.getElementById('nm').value=nm;
	document.getElementById('oldnm').value=nm;
	document.getElementById('nu').value=nu;
	document.getElementById('oldnu').value=nu;
	
	document.getElementById('ki').value=ki;
	document.getElementById('oldki').value=ki;
	document.getElementById('st').value=st;
}



function Del(nm,nu,ki)
{
	param='method=delete'+'&nm='+nm+'&nu='+nu+'&ki='+ki;
	tujuan='slave_kebun_5nourutmandor.php';
	if(confirm("Anda yakin ingin menghapus"))
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
					else 
					{
						 document.getElementById('containerData').innerHTML=con.responseText;
						loadData();	
                                           
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

