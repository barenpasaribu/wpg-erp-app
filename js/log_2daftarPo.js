// JavaScript Document
function refresh_data()
{
	param='method=list_new_data';
	tujuan='log_slave_2daftarPo.php';
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
								document.getElementById('contain').innerHTML=con.responseText;
								document.getElementById('txtsearch').value='';
								document.getElementById('tgl_cari').value='';
								//alert('Berhasil');
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 } 	
		 post_response_text(tujuan, param, respog);	
}
function loadData()
{
	param='method=loadData';
	tujuan='log_slave_2daftarPo.php';
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
								document.getElementById('contain').innerHTML=con.responseText;
								document.getElementById('txtsearch').value='';
								document.getElementById('tgl_cari').value='';
								//alert('Berhasil');
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 } 	
		 post_response_text(tujuan, param, respog);	
}
function cariPo()
{
	txtSearch=trim(document.getElementById('txtsearch').value);
	tglCari=trim(document.getElementById('tgl_cari').value);
	met=document.getElementById('method');
	met=met.value='list_new_data';
	//met=trim(met);
	param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
	tujuan='log_slave_2daftarPo.php';
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
								document.getElementById('contain').innerHTML=con.responseText;
								
							}
						}
						else {
							busy_off();
							error_catch(con.status);
						}
				  }	
		 }
		 post_response_text(tujuan, param, respog);
}
function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'log_slave_2daftarPo.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function cariPage(num)
{
		txtSearch=trim(document.getElementById('txtsearch').value);
		tglCari=trim(document.getElementById('tgl_cari').value);
		met=document.getElementById('method');
		met=met.value='list_new_data';
		//met=trim(met);
		param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
		param+='&page='+num;
		tujuan = 'log_slave_2daftarPo.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('contain').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}