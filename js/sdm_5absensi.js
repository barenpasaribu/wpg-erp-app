/**
 * @author antoniuslouis
 */
function simpanJ()
{
	kode=document.getElementById('kode').value;
	keterangan=document.getElementById('keterangan').value;
	jumlahhk=remove_comma(document.getElementById('jumlahhk'));
	grup=document.getElementById('grup');
	grup=grup.options[grup.selectedIndex].value;	
	met=document.getElementById('method').value;
	if(trim(kode)=='' || jumlahhk=='' || keterangan=='')
	{
		alert('Each Field are obligatory');
		document.getElementById('kode').focus();
	}
	else
	{
		param='kode='+kode+'&keterangan='+keterangan+'&method='+met;
		param+='&jumlahhk='+jumlahhk+'&grup='+grup;
		tujuan='sdm_slave_save_5absensi.php';
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

function fillField(kode,keterangan,kelompok,nilai)
{
	document.getElementById('kode').value=kode;
    document.getElementById('kode').disabled=true;
	document.getElementById('keterangan').value=keterangan;
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
    document.getElementById('kode').disabled=false;
	document.getElementById('kode').value='';
	document.getElementById('keterangan').value='';
	document.getElementById('jumlahhk').value=0;
	grup=document.getElementById('grup');
	grup=grup.options[0].selected=true;
	document.getElementById('method').value='insert';		
}
