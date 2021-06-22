function simpanpks()
{
	thnbudget=document.getElementById('thnbudget').value;
	kdpks=document.getElementById('kdpks').value;
	jamo=document.getElementById('jamo').value;	
	jamb=document.getElementById('jamb').value;
	met=document.getElementById('method').value;
	
	if(trim(thnbudget)=='')
	{
		alert('Tahun masih kosong');
		return;
		//document.getElementById('thnbudget').focus();
	}	
	else if(thnbudget.length<4) 
    {
        alert('Karakter Tahun Budget Tidak Tepat');
        return;
    }
	else if(trim(kdpks)=='')
	{
		alert('Kode PKS masi kosong');
		return;
	}
	else if(trim(jamo)=='')
	{
		alert('Jam Olah/Tahun Masi Kosong');
		return;
	}
	else if(trim(jamb)=='')
	{
		alert('Jam Breakdown/Tahun Masi Kosong');
		return;
	}
	else
	{
		thnbudget=trim(thnbudget);
		kdpks=trim(kdpks);
		jamo=trim(jamo);
		jamb=trim(jamb);
		
		param='thnbudget='+thnbudget+'&kdpks='+kdpks+'&jamo='+jamo+'&jamb='+jamb+'&method='+met;
		tujuan='bgt_slave_save_jam_oprasional_pks.php';
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
                            batalpks();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(tahunbudget,millcode,jamolah,breakdown)
{
	document.getElementById('thnbudget').value=tahunbudget;
	document.getElementById('kdpks').value=millcode;
	document.getElementById('jamo').value=jamolah;
   // document.getElementById('').disabled=true;
	document.getElementById('jamb').value=breakdown;
	document.getElementById('method').value='update';
}

function batalpks()
{
    //document.getElementById('').disabled=false;
	document.getElementById('thnbudget').value='';
	document.getElementById('kdpks').value='';;
	document.getElementById('jamo').value='';;
	document.getElementById('jamb').value='';;
	document.getElementById('method').value='insert';		
}
