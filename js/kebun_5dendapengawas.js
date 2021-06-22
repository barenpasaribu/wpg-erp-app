//JS 

function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_5dendapengawas.php';
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
	kode=document.getElementById('kode').value;
	nama=document.getElementById('nama').value;
	jabatan=document.getElementById('jabatan').value;
	denda=document.getElementById('denda').value;	
	method=document.getElementById('method').value;

	if(kode=='' || nama=='' || jabatan=='')
	{
		alert('Field Was Empty');
		return;
	}
	
	param='kode='+kode+'&nama='+nama+'&jabatan='+jabatan+'&denda='+denda+'&method='+method;
	tujuan='kebun_slave_5dendapengawas.php';
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
	param='method=loadData';
	tujuan='kebun_slave_5dendapengawas.php';
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

function edit(kode,nama,jabatan,denda)
{
	document.getElementById('kode').value=kode;
	document.getElementById('kode').disabled;
	document.getElementById('nama').value=nama;
	document.getElementById('jabatan').value=jabatan;
	document.getElementById('denda').value=denda;
	document.getElementById('method').value='update';
}



function del(kode)
{
	param='method=delete'+'&kode='+kode;
	tujuan='kebun_slave_5dendapengawas.php';
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




