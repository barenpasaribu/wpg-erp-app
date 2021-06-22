/**
 * @author antoniuslouis
 */

function cariDt()
{
        notrans=document.getElementById('notransaksi').value;
	param ='notransaksi='+notrans+'&proses=getData';
	tujuan = 'sdm_slave_3revisipjd.php';
	post_response_text(tujuan, param, respog);
        //	tabAction(document.getElementById('tabFRM0'),1,'FRM',0);
         //document.getElementById('notransaksi').value=notrans; 
	
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

function getNotrans()
{
    kdorg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    notrans=document.getElementById('notransaksi').value;
    param ='kodeOrg='+kdorg+'&proses=getNotrans'+'&notransaksi='+notrans;
    tujuan = 'sdm_slave_3revisipjd.php';
    post_response_text(tujuan, param, respog);
    //	tabAction(document.getElementById('tabFRM0'),1,'FRM',0);
     //document.getElementById('notransaksi').value=notrans; 

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('isiNotrans').style.display="block";
                                    document.getElementById('notransaksi2').value=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }
}
function saveNotrans(){
    kdorg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    notrans=document.getElementById('notransaksi').value;
    notrans2=document.getElementById('notransaksi2').value;
    param ='kodeOrg='+kdorg+'&proses=saveNotrans'+'&notransaksi='+notrans+'&notransaksi2='+notrans2;
    tujuan = 'sdm_slave_3revisipjd.php';
    if(confirm("Anda Yaking Ingin Mengganti Notransaks?"))
    post_response_text(tujuan, param, respog);
    //	tabAction(document.getElementById('tabFRM0'),1,'FRM',0);
     //document.getElementById('notransaksi').value=notrans; 

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('isiNotrans').style.display="none";
                                    document.getElementById('notransaksi').value=con.responseText;
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

function saveApprvPJD(jenisby,notransaksi,tanggal,jumlah,no)
{
	jumlahhrd= remove_comma(document.getElementById('jumlahhrd'+jenisby+no));
	if(jumlahhrd=='')
	{
		alert('Amount is empty');
	}
	else
	{
			param ='notransaksi='+notransaksi+'&jenisby='+jenisby; 
			param +='&jumlahhrd='+jumlahhrd+'&method=update&tanggal='+tanggal+'&jumlah='+jumlah;
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
//					  loadList();
//					  tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
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
	
}
function loadList()
{num=0;
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



