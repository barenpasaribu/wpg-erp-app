/**
 * @author antoniuslouis
 */

function batalPJD(notransaksi)
{      
	param = 'proses=batalPerjalananDinas';
	param += '&notransaksi='+notransaksi;
	tujuan = 'sdm_slave_get_save_data.php'; 
	if(confirm('Are you sure cancel ' + notransaksi+' ?'))
		post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					alert(con.responseText);
					window.location.reload();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}				
}

function approvePJD(notransaksi,karyawanid,status,kolom)
{      
	 	param='notransaksi='+notransaksi+'&karyawanid='+karyawanid+'&status='+status+'&kolom='+kolom;
		tujuan = 'sdm_slave_PJDinasApproval.php';
		if(status==1)
		   comment=' Approve ';
		else
		   comment=' Reject ';   
		if(confirm('Are you sure '+comment+' '+notransaksi+' ?'))
		     post_response_text(tujuan, param, respog);
			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					alert('Done');
					window.location.reload();
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
		tujuan = 'sdm_slave_getPJDListApproval.php';
		
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

function previewPJD(notransaksi,ev)
{
   	param='notransaksi='+notransaksi;
	tujuan = 'sdm_slave_getPersetujuanPJDPreview.php?'+param;	
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						   title=notransaksi;
						   width='600';
						   height='300';
						   content=con.responseText;
						   showDialog1(title,content,width,height,ev);					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
}

function saveUpdateValPJD()
{
	newVal=document.getElementById('newvalpjd').value;
	notransaksi=document.getElementById('nitransaksipjd').value;
	
	param='newvalpjd='+newVal+'&notransaksi='+notransaksi;
	tujuan = 'sdm_slave_saveUpdateValPJD.php?'+param;	
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
                          document.getElementById('oldval').innerHTML=con.responseText;
						  alert('Done');					
				 }
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}		
	
}

function previewPJDPDF(nosk,ev)
{
   	param='notransaksi='+nosk;
	tujuan = 'sdm_slave_printPJD_pdf.php?'+param;	
 //display window
   title=nosk;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}
