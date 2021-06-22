/**
 * @author antoniuslouis
 */
function simpanJ()
{
	jumlahhk=remove_comma(document.getElementById('jumlahhk'));
	grup=document.getElementById('grup');
	grup=grup.options[grup.selectedIndex].value;	
	met=document.getElementById('method').value;
	if(jumlahhk=='' || grup=='')
	{
		alert('Each Field are obligatory');
	}
	else
	{
		param='method='+met;
		param+='&jumlahhk='+jumlahhk+'&grup='+grup;
		tujuan='keu_slave_save_5akunbank.php';
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

function fillField(kelompok,nilai)
{
	document.getElementById('jumlahhk').value=nilai;
	grup=document.getElementById('grup');
	for(x=0;x<grup.length;x++)
	{
		if(grup.options[x].value==kelompok)
		{
			grup.options[x].selected=true;
		}
	}
	document.getElementById('method').value='update';
}

function cancelJ()
{
    
	document.getElementById('jumlahhk').value='';
	grup=document.getElementById('grup');
	grup=grup.options[0].selected=true;
	document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='keu_slave_save_5akunbank.php';
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

