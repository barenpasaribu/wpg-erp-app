/**
 * @author antoniuslouis
 */
function simpanPlafon()
{
	kodegolongan=document.getElementById('kodegolongan').value;
	persen=document.getElementById('prsn').value;
	met=document.getElementById('method').value;
	jenisbiaya=document.getElementById('jenisbiaya');
	jenisbiaya=jenisbiaya.options[jenisbiaya.selectedIndex].value;
	if(trim(kodegolongan)=='')
	{
		alert('Code is empty');
		document.getElementById('kodegolongan').focus();
	}
	else
	{
		kodegolongan=trim(kodegolongan);
		persen=trim(persen);
		param='kodegolongan='+kodegolongan+'&persen='+persen+'&method='+met+'&jenisbiaya='+jenisbiaya;
		tujuan='sdm_slave_save_setup_plafond.php';
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
							cancelPlafon();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kode,nama,jenisbiaya)
{
	x=document.getElementById('kodegolongan');
	for(z=0;z<x.length;z++)
	{
		if(x.options[z].value==kode)
		x.options[z].selected=true;
	}
    document.getElementById('kodegolongan').disabled=true;
	x=document.getElementById('jenisbiaya');
	for(z=0;z<x.length;z++)
	{
		if(x.options[z].value==jenisbiaya)
		x.options[z].selected=true;
	}
    document.getElementById('jenisbiaya').disabled=true;	
	document.getElementById('prsn').value=nama;
	document.getElementById('method').value='update';
}

function cancelPlafon()
{
    document.getElementById('kodegolongan').disabled=false;
	document.getElementById('kodegolongan').value='';
    document.getElementById('jenisbiaya').disabled=false;
	document.getElementById('jenisbiaya').value='';	
	document.getElementById('prsn').value='';
	document.getElementById('method').value='insert';		
}
