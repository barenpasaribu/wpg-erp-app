/**
 * @author antoniuslouis
 */
function simpanGolongan()
{
	kodegolongan=document.getElementById('kodegolongan').value;
	namagolongan=document.getElementById('namagolongan').value;
	met=document.getElementById('method').value;
	if(trim(kodegolongan)=='')
	{
		alert('Code is empty');
		document.getElementById('kodegolongan').focus();
	}
	else
	{
		kodegolongan=trim(kodegolongan);
		namagolongan=trim(namagolongan);
		param='kodegolongan='+kodegolongan+'&namagolongan='+namagolongan+'&method='+met;
		tujuan='sdm_slave_save_golongan.php';
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

function fillField(kode,nama)
{
	document.getElementById('kodegolongan').value=kode;
    document.getElementById('kodegolongan').disabled=true;
	document.getElementById('namagolongan').value=nama;
	document.getElementById('method').value='update';
}

function DeleteField(kode,nama)
{
	param='kodegolongan='+kode+'&namagolongan='+nama+'&method=delete';
	tujuan='sdm_slave_save_golongan.php';
	if(confirm("Anda yakin ingin menghapus")){
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

function cancelGolongan()
{
    document.getElementById('kodegolongan').disabled=false;
	document.getElementById('kodegolongan').value='';
	document.getElementById('namagolongan').value='';
	document.getElementById('method').value='insert';		
}
