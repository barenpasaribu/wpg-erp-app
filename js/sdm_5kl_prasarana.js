/**
 * @author antoniuslouis
 */
function simpanJabatan()
{
	kodejabatan=document.getElementById('kodejabatan').value;
	namajabatan=document.getElementById('namajabatan').value;
	met=document.getElementById('method').value;
	if(trim(kodejabatan)=='')
	{
		alert('Code is empty');
		document.getElementById('kodejabatan').focus();
	}
	else
	{
		kodejabatan=trim(kodejabatan);
		namajabatan=trim(namajabatan);
		param='kodejabatan='+kodejabatan+'&namajabatan='+namajabatan+'&method='+met;
		tujuan='sdm_slave_save_5kl_prasarana.php';
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
	document.getElementById('kodejabatan').value=kode;
    document.getElementById('kodejabatan').disabled=true;
	document.getElementById('namajabatan').value=nama;
	document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('kodejabatan').disabled=false;
	document.getElementById('kodejabatan').value='';
	document.getElementById('namajabatan').value='';
	document.getElementById('method').value='insert';		
}
