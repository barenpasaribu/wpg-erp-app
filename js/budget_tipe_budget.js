/**
 * @author antoniuslouis
 */
function simpanDep()
{
	kode=document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
	nama=document.getElementById('nama').value;
	met=document.getElementById('method').value;
	if(trim(kode)=='')
	{
		alert('Code is empty');
		document.getElementById('tipe').focus();
	}
	if(trim(nama)=='')
	{
		alert('Name is empty');
		document.getElementById('nama').focus();
	}
	else
	{
		kode=trim(kode);
		nama=trim(nama);
		param='kode='+kode+'&nama='+nama+'&method='+met;
		tujuan='budget_slave_tipe_budget.php';
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
                                                        cancelDep();
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
    document.getElementById('tipe').value=kode;
    document.getElementById('tipe').disabled=true;
    document.getElementById('nama').value=nama;
    document.getElementById('method').value='update';
}

function cancelDep()
{
        document.getElementById('tipe').disabled=false;
	document.getElementById('tipe').value='';
	document.getElementById('nama').value='';
	document.getElementById('method').value='insert';		
}
