/**
 * @author antoniuslouis
 */
function simpanJ()
{
	kodeorg=document.getElementById('kodeorg');
	kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
	tipelembur=document.getElementById('tipelembur');
	tipelembur=tipelembur.options[tipelembur.selectedIndex].value;
	
	jamaktual=remove_comma(document.getElementById('jamaktual'));
	jamlembur=remove_comma(document.getElementById('jamlembur'));	
	met=document.getElementById('method').value;
	if(trim(kodeorg)=='' || tipelembur=='' || jamaktual=='0' || jamlembur=='0')
	{
		alert('Each Field are obligatory');
		document.getElementById('kodeorg').focus();
	}
	else
	{
		param='kodeorg='+kodeorg+'&tipelembur='+tipelembur+'&method='+met;
		param+='&jamaktual='+jamaktual+'&jamlembur='+jamlembur;
		tujuan='sdm_slave_save_5lembur.php';
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

function fillField(kodeorg,tipelembur,jamaktual,jamlembur)
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
	
	jk=document.getElementById('tipelembur');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==tipelembur)
		{
			jk.options[x].selected=true;
		}
	}
	document.getElementById('tipelembur').disabled=true;
		
	document.getElementById('jamaktual').value=jamaktual;
    document.getElementById('jamaktual').disabled=true;
	document.getElementById('jamlembur').value=jamlembur;
	
	document.getElementById('method').value='update';
}

function cancelJ()
{
    document.getElementById('kodeorg').disabled=false;
	document.getElementById('tipelembur').disabled=false;
	document.getElementById('jamlembur').value=0;
	document.getElementById('jamaktual').disabled=false;
	document.getElementById('jamaktual').value=0;
	document.getElementById('method').value='insert';		
}
