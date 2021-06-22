// JavaScript Document

var sta=0;
function loadTimer()
{
        t=setInterval('periksaChat()',20000);//15 second
}	
x=0;
function periksaChat()
{
	kodebarang=document.getElementById('kodebarang').value;
	nopp=document.getElementById('nopp').value;
	param='nopp='+nopp+'&kodebarang='+kodebarang;
	tujuan='log_slave_saveChatPP.php';
	if (sta==0) {
		post_response_text(tujuan, param, respog);
	    sta=1;
	}
	function respog()
	{
			  if(coss.readyState==4)
		      {	
				    if (coss.status == 200) {
						busy_off();
						if (!isSaveResponse(coss.responseText)) {
							alert('ERROR TRANSACTION,\n' + coss.responseText);
						}
						else {
							sta=0;
							document.getElementById('container').innerHTML=coss.responseText;
						}
					}
					else {
						busy_off();
						error_catch(coss.status);
					}
		      }	
	 } 		
}	
function savePPChat(nopp,kodebarang)
{
	pesan=document.getElementById('pesan').value;
	if(pesan!='')
	  param='nopp='+nopp+'&kodebarang='+kodebarang+'&pesan='+pesan;
	else
	  {}
	tujuan='log_slave_saveChatPP.php';
	post_response_text(tujuan, param, respog);
	function respog()
	{
		      if(coss.readyState==4)
		      {
			        if (coss.status == 200) {
						busy_off();
						if (!isSaveResponse(coss.responseText)) {
							alert('ERROR TRANSACTION,\n' + coss.responseText);
						}
						else {
							document.getElementById('container').innerHTML=coss.responseText;
						    document.getElementById('pesan').value='';
						}
					}
					else {
						busy_off();
						error_catch(coss.status);
					}
		      }	
	 } 		
}
loadTimer();