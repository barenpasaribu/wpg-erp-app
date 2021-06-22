/**
 * @author {anthesis-team@mitraagroservindo.com}
 * jakarta indonesia
 */

function cariNama()
{
	txtnama=document.getElementById('txtnama').value;
	nik=document.getElementById('nik').value;
	if(txtnama=='' || txtnama.length<5)
	{
		 alert('Min. 5 char');
	}
	else
	{
		param='txtnama='+txtnama+'&nik='+nik;
		tujuan='sdm_slave_cariSecurity.php';
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
							//alert(con.responseText);
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

function setKarTo(karid)
{
	pt=document.getElementById('tujuan'+karid).options[document.getElementById('tujuan'+karid).selectedIndex].value;
	lokasitugas=document.getElementById('tujuan'+karid).options[document.getElementById('tujuan'+karid).selectedIndex].text;
   if(confirm('Are you sure,?') && pt!='*')
   {
		param='karyawanid='+karid+'&pt='+pt+'&lokasitugas='+lokasitugas;
		//alert(param);return;
		tujuan='sdm_slave_save_rotasiSecurity.php';
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
							document.getElementById('tujuan'+karid).options[0].selected=true;
						}
						else {
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
