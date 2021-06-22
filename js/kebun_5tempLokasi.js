//JS 





function cariBast(num)
{
		
		param='method=loadData'+'&page='+num;		
		tujuan = 'setup_slave_tempLokasi.php';
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
	kar=document.getElementById('kar').value;
	kdorg=document.getElementById('kdorg').value;
	method=document.getElementById('method').value;

	if(kar=='' || kdorg=='')
	{
		alert('Field was empty');
		return;
	}
	param='kar='+kar+'&kdorg='+kdorg+'&method='+method;
	tujuan='setup_slave_tempLokasi.php';
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
	method=document.getElementById('method').value;
	document.getElementById('kar').disabled=false;
	document.getElementById('kar').value='';
	document.getElementById('kdorg').value='';
	
	loadData();
	//document.location.reload();
}




function loadData () 
{
	
	
	param='method=loadData';
	tujuan='setup_slave_tempLokasi.php';
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










function edit(kar,kdorg)
{
	document.getElementById('kar').disabled=true;
	document.getElementById('kar').value=kar;
	document.getElementById('kdorg').value=kdorg;
	document.getElementById('method').value='update';
}





function del(kar)
{
	param='method=delete'+'&kar='+kar;
	//alert(param);
	tujuan='setup_slave_tempLokasi.php';
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




