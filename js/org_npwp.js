/**
 * @author antoniuslouis
 */
function delnpwp(org)
{
	param='kodeorg='+org+'&switch=delete';
	tujuan='slave_save_org_npwp.php';
	if(confirm('Delete/hapus ..?'))
	{
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

function savenpwp()
{
	org				=document.getElementById('org').options[document.getElementById('org').selectedIndex].value;
	npwp			=document.getElementById('npwp').value;
	alamatnpwp	 	=document.getElementById('alamatnpwp').value;
	alamatdomisili 	=document.getElementById('alamatdomisili').value;
   
	param='kodeorg='+org+'&npwp='+npwp+'&alamatnpwp='+alamatnpwp;
	param+='&alamatdom='+alamatdomisili;
	tujuan='slave_save_org_npwp.php';
	if(confirm('Saving/Simpan ..?'))
	{
       //alert(param);
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

function cancelnpwp()
{
	document.getElementById('npwp').value='';
	document.getElementById('alamatnpwp').value='';
	document.getElementById('alamatdomisili').value='';	
}
