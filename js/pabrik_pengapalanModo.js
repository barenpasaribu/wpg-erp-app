function getCust()
{
	nokontrak=document.getElementById('nokontrak').value;	
	param='method=getCust'+'&nokontrak='+nokontrak;
	tujuan='pabrik_slave_pengapalanModo.php';
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
						document.getElementById('kdCust').innerHTML=ar[0];
						document.getElementById('kdbarang').innerHTML=ar[1];
						
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


function del(notran)
{
	
	param='method=delete'+'&notran='+notran;
	tujuan='pabrik_slave_pengapalanModo.php';
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




function edit(kodeorgEdit,tglEdit,produkEdit,barangEdit,inpEdit,idEdit)
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
	document.getElementById('idEdit').value=idEdit;
}

function simpan()
{
	notran=document.getElementById('notran').value;
	tgl=document.getElementById('tgl').value;
	kodeorg=document.getElementById('kodeorg').value;
	nokontrak=document.getElementById('nokontrak').value;
	nodo=document.getElementById('nodo').value;
	kdCust=document.getElementById('kdCust').value;
	kdbarang=document.getElementById('kdbarang').value;
	kdKapal=document.getElementById('kdKapal').value;
	transp=document.getElementById('transp').value;
	berat=document.getElementById('berat').value;
	
	param='method=saveData'+'&notran='+notran+'&tgl='+tgl+'&kodeorg='+kodeorg+'&nokontrak='+nokontrak+'&nodo='+nodo;
	param+='&kdCust='+kdCust+'&kdbarang='+kdbarang+'&kdKapal='+kdKapal+'&transp='+transp+'&berat='+berat;
	tujuan='pabrik_slave_pengapalanModo.php';
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





function cariBast(num)
{
		perSch=document.getElementById('perSch').value;
		
		param='method=loadData'+'&perSch='+perSch+'&page='+num;
		tujuan = 'pabrik_slave_pengapalanModo.php';
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
	perSch=document.getElementById('perSch').value;
	param='method=loadData'+'&perSch='+perSch;
	
	//alert(param);	
	tujuan='pabrik_slave_pengapalanModo.php';
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

















