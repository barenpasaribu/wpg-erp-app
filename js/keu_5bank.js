/**
 * @author Developer
 */
function simpanJ()
{
	jumlahhk=remove_comma(document.getElementById('jumlahhk'));
	sandibank=document.getElementById('sandibank').value;
	grup=document.getElementById('grup').value;	
	met=document.getElementById('method').value;
	if(jumlahhk=='' || grup=='')
	{
		alert('Each Field are obligatory');
	}
	else
	{
		param='method='+met;
		param+='&jumlahhk='+jumlahhk+'&grup='+grup+'&sandibank='+sandibank;
		tujuan='keu_slave_save_5bank.php';
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
                            cancelJ();
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kelompok,nilai,sandibank)
{
	document.getElementById('jumlahhk').value=nilai;
	document.getElementById('sandibank').value=sandibank;
	document.getElementById('grup').value=kelompok;
	document.getElementById('grup').disabled = true;
	document.getElementById('method').value='update';
}

function cancelJ()
{
    
	document.getElementById('jumlahhk').value='';
	document.getElementById('sandibank').value='';
	document.getElementById('grup').value='';
	document.getElementById('grup').disabled = false;
	document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='keu_slave_save_5bank.php';
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

