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
                                                                document.getElementById('tgl_cari2').value='';
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
	tujuan='log_slave_2penerimaan.php';
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
								document.getElementById('kdGdng').value='';
                                                                document.getElementById('nmBrg').value='';
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
function cariData()
{
    
	kdGudang=document.getElementById('kdGdng').options[document.getElementById('kdGdng').selectedIndex].value;
        txtsearch=document.getElementById('txtsearch').value;
        nmBrg=document.getElementById('nmBrg').value;
        mnop=document.getElementById('txtsearch2').value;
        tgl_cari=document.getElementById('tgl_cari').value;
	met=document.getElementById('method');
	met=met.value='list_new_data';
	//met=trim(met);
	param='txtSearch='+txtsearch+'&tglCari='+tgl_cari+'&method='+met;
        param+='&kdGudang='+kdGudang+'&nmBrg='+nmBrg+'&nopp='+mnop;
	tujuan='log_slave_2penerimaan.php';
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
function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'log_slave_2penerimaan.php';
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
                kdGudang=document.getElementById('kdGdng').options[document.getElementById('kdGdng').selectedIndex].value;
                txtsearch=document.getElementById('txtsearch').value;
                nmBrg=document.getElementById('nmBrg').value;
                tgl_cari=document.getElementById('tgl_cari').value;
                met=document.getElementById('method');
                mnop=document.getElementById('txtsearch2').value;
                met=met.value='list_new_data';
                //met=trim(met);
                param='txtSearch='+txtsearch+'&tglCari='+tgl_cari+'&method='+met;
                param+='&kdGudang='+kdGudang+'&nmBrg='+nmBrg+'&nopp='+mnop;
		//param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
		param+='&page='+num;
		tujuan = 'log_slave_2penerimaan.php';
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
function previewBapb(notransaksi,ev)
{
        param='notransaksi='+notransaksi;
        tujuan = 'log_slave_print_bapb_pdf.php?'+param;	
        //display window
        title=notransaksi;
        width='700';
        height='400';
        content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
        showDialog1(title,content,width,height,ev);
   
}