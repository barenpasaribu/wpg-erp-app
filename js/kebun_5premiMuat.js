//JS 





function cariBast(num)
{
		
		param='method=loadData'+'&page='+num;		
		tujuan = 'kebun_slave_5premiMuat.php';
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
	kodekegiatan=document.getElementById('kodekegiatan').value;
	volume=document.getElementById('volume').value;
	rupiah=document.getElementById('rupiah').value;
	tipe=document.getElementById('tipe').value;
	jumlahhari=document.getElementById('jumlahhari').value;
		
	method=document.getElementById('method').value;

	if(regional=='' || kodekegiatan==''|| volume==''|| rupiah==''|| tipe==''|| jumlahhari=='')
	{
		alert('Field was empty');
		return;
	}
	param='regional='+regional+'&kodekegiatan='+kodekegiatan+'&volume='+volume+'&rupiah='+rupiah+'&method='+method+'&tipe='+tipe+'&jumlahhari='+jumlahhari;
	tujuan='kebun_slave_5premiMuat.php';
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
	tujuan='kebun_slave_5premiMuat.php';
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










function edit(regional,kodekegiatan,volume,rupiah,tipe,jumlahhari)
{
	
	document.getElementById('kodekegiatan').disabled=true;
	document.getElementById('volume').disabled=true;
	document.getElementById('regional').value=regional;
	document.getElementById('kodekegiatan').value=kodekegiatan;
	document.getElementById('volume').value=volume;
	document.getElementById('rupiah').value=rupiah;
	document.getElementById('tipe').value=tipe;
	document.getElementById('jumlahhari').value=jumlahhari;
	document.getElementById('method').value='update';
	
	/*param='regional='+regional+'&kodekegiatan='+kodekegiatan+'&volume='+volume+'&rupiah='+rupiah+'&method='+update+'&tipe='+tipe+'&jumlahhari='+jumlahhari;
	//alert(param);
	tujuan='kebun_slave_5premiMuat.php';
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
	}*/

}





function del(kodekegiatan,volume)
{
	param='method=delete'+'&kodekegiatan='+kodekegiatan+'&volume='+volume;
	//alert(param);
	tujuan='kebun_slave_5premiMuat.php';
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




