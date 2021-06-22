/**
 * @author antoniuslouis
 */
function simpanJ()
{
	kodeorg=document.getElementById('kodeorg');
	kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
	metodepenggajian=document.getElementById('metodepenggajian');
	metodepenggajian=metodepenggajian.options[metodepenggajian.selectedIndex].value;
	
	periode=document.getElementById('periode').value;
	tanggalmulai=document.getElementById('tanggalmulai').value;	
	tanggalsampai=document.getElementById('tanggalsampai').value;	
	tutup=document.getElementById('tutup');
	if(tutup.checked==true)
	   tutup=1;
	else
	   tutup=0;   
	met=document.getElementById('method').value;
	
	if(trim(kodeorg)=='' || periode=='' || tanggalmulai=='' || tanggalsampai=='')
	{
		alert('Each Field are obligatory');
		document.getElementById('kodeorg').focus();
	}
	else
	{
		param='kodeorg='+kodeorg+'&metodepenggajian='+metodepenggajian+'&method='+met;
		param+='&periode='+periode+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai;
		param+='&tutup='+tutup;
		
		tujuan='sdm_slave_save_5periodeGaji.php';
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
							cancelJ();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kodeorg,jenisgaji,periode,tanggalmulai,tanggalsampai,sudahproses)
{
	jk=document.getElementById('kodeorg');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==kodeorg)
		{
			jk.options[x].selected=true;
		}
	}
	document.getElementById('kodeorg').disabled=true;
	
	jk=document.getElementById('metodepenggajian');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==jenisgaji)
		{
			jk.options[x].selected=true;
		}
	}
	document.getElementById('metodepenggajian').disabled=true;
	
	document.getElementById('periode').value=periode;
	document.getElementById('periode').disabled=true;
	if(sudahproses=='1')
	   document.getElementById('tutup').checked=true;
	else
	   document.getElementById('tutup').checked=false;
		
	document.getElementById('tanggalmulai').value=tanggalmulai;
	document.getElementById('tanggalsampai').value=tanggalsampai;	
	document.getElementById('method').value='update';
}

function cancelJ()
{
    document.getElementById('kodeorg').disabled=false;
	document.getElementById('metodepenggajian').disabled=false;
	document.getElementById('tutup').checked=false;
	document.getElementById('periode').disabled=false;	
	document.getElementById('periode').value='';	
	document.getElementById('tanggalmulai').value='';
	document.getElementById('tanggalsampai').value='';
	
	document.getElementById('method').value='insert';		
}
