/**
 * @author antoniuslouis
 */
function preview()
{
	dari=document.getElementById('dari').value;
	sampai=document.getElementById('sampai').value;
        unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
	if(trim(dari)=='' || trim(sampai)=='' || trim(unit)=='' )
	{
		alert('Code is empty');
	}
	else
	{
            param='dari='+dari+'&sampai='+sampai+'&unit='+unit;
            tujuan='keu_getLpj.php';
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
function fisikKeExcel(ev)
{
	dari=document.getElementById('dari').value;
	sampai=document.getElementById('sampai').value;
        unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
	if(trim(dari)=='' || trim(sampai)=='' || trim(unit)=='' )
	{
		alert('Code is empty');
	}
	else
            {
                
          param='dari='+dari+'&sampai='+sampai+'&unit='+unit+'&excel=excel';
	   tujuan = 'keu_getLpjExcel.php?'+param;	
           title='Download';
           width='500';
           height='400';
           content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
           showDialog1(title,content,width,height,ev);		
	}
	
		
}

function showByDetail(afd,dari,sampai,akundari,akunsampai,tipe,ev,unit)
{
           param='afd='+afd+'&dari='+dari+'&sampai='+sampai+'&akundari='+akundari+'&akunsampai='+akunsampai+'&tipe='+tipe+'&unit='+unit;
	   tujuan = 'keu_getLpjDetail.php?'+param;	
           title='Download';
           width='700';
           height='400';
           content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
           showDialog1(title,content,width,height,ev);    
}

