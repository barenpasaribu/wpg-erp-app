/**
 * @author antoniuslouis
 */
function simpanJabatan()
{
	idKlmpk=document.getElementById('idKlmpk').options[document.getElementById('idKlmpk').selectedIndex].value;
        kodejabatan=document.getElementById('kodejabatan').value;
	namajabatan=document.getElementById('namajabatan').value;
        satuan=document.getElementById('satuan').value;
	met=document.getElementById('method').value;
	if((trim(kodejabatan)=='')||(trim(idKlmpk)==''))
        {
		alert('Code is empty');
		document.getElementById('kodejabatan').focus();
	}
	else
	{
		kodejabatan=trim(kodejabatan);
		namajabatan=trim(namajabatan);
                satuan=trim(satuan);
                idKlmpk=trim(idKlmpk);
		param='kodejabatan='+kodejabatan+'&namajabatan='+namajabatan+'&method='+met;
                param+='&satuan='+satuan+'&idKlmpk='+idKlmpk;
		tujuan='sdm_slave_save_5jenis_prasarana.php';
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
                                                        cancelJabatan();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(idklmk,kode,sat,nama)
{
    l=document.getElementById('idKlmpk');
    
    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==idklmk)
                {
                    l.options[a].selected=true;
                }
        }
    document.getElementById('kodejabatan').value=kode;
    document.getElementById('kodejabatan').disabled=true;
    document.getElementById('idKlmpk').disabled=true;
    document.getElementById('namajabatan').value=nama;
    document.getElementById('satuan').value=sat;
    document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('kodejabatan').disabled=false;
    document.getElementById('idKlmpk').disabled=false;
    document.getElementById('idKlmpk').value='';
    document.getElementById('kodejabatan').value='';
    document.getElementById('namajabatan').value='';
    document.getElementById('satuan').value='';
    document.getElementById('method').value='insert';		
}
