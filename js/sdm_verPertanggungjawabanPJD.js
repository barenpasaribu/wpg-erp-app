/**
 * @author antoniuslouis
 */

function editPPJD(notransaksi)
{
	param ='notransaksi='+notransaksi;
	tujuan = 'sdm_slave_saveVerPrtjwbPJDinas.php';
	post_response_text(tujuan, param, respog);
	tabAction(document.getElementById('tabFRM0'),1,'FRM',0);
    document.getElementById('notransaksi').value=notransaksi; 
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('innercontainer').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function previewPJD(nosk,ev)
{
   	param='notransaksi='+nosk;
	tujuan = 'sdm_slave_printPtjwbPJD_pdf.php?'+param;	
 //display window
   title=nosk;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}

function saveApprvPJD(jenisby,notransaksi,tanggal,jumlah,keterangan,no)
{
	jumlahhrd= remove_comma(document.getElementById('jumlahhrd'+jenisby+no));
	if(jumlahhrd=='')
	{
		alert('Amount is empty');
	}
	else
	{
			param ='notransaksi='+notransaksi+'&jenisby='+jenisby; 
			param +='&jumlahhrd='+jumlahhrd+'&method=update&tanggal='+tanggal+'&jumlah='+jumlah+'&keterangan='+keterangan;
			if (confirm('Saving, are you sure..?')) {
				tujuan = 'sdm_slave_saveVerPrtjwbPJDinas.php';
				post_response_text(tujuan, param, respog);
			}
		}
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('jumlahhrd'+jenisby).style.backgroundColor='#dedede';
					alert('Saved');
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
		
}

function selesai()
{
  notransaksi=document.getElementById('notransaksi').value;
  tujuan = 'sdm_slave_saveVerPrtjwbPJDinas.php';
  param='notransaksi='+notransaksi+'&method=finish';
  post_response_text(tujuan, param, respog);
	
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					  document.getElementById('innercontainer').innerHTML='';
					  document.getElementById('notransaksi').value='';
					  loadList();
					  tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	 
 	
}
function batalkan()
{
	  document.getElementById('innercontainer').innerHTML='';
	  document.getElementById('notransaksi').value='';
	  loadList();
	  tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
	
}
function loadList()
{      num=0;
	 	param='&page='+num;
		tujuan = 'sdm_getVerPJDinasPertgjwbList.php';
		post_response_text(tujuan, param, respog);
			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('containerlist').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}				
}
					
function cariPJD(num)
{
	tex=trim(document.getElementById('txtbabp').value);
		param='&page='+num;
		if(tex!='')
			param+='&tex='+tex;
		tujuan = 'sdm_getVerPJDinasPertgjwbList.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containerlist').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}



