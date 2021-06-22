function simpanbor()
{
	tahunbudget=document.getElementById('tahunbudget').value;
	kodeorg=document.getElementById('kodeorg');
	kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
	
	
	sb=document.getElementById('sb').value;	
	lb=document.getElementById('lb').value;
	met=document.getElementById('method').value;
	
	oldtahunbudget=document.getElementById('oldtahunbudget').value;
	oldkodeorg=document.getElementById('oldkodeorg').value;
	
	
	if(trim(tahunbudget)=='')
	{
		alert('Tahun masih kosong');
		return;
		//document.getElementById('thnbudget').focus();
	}	
	else if(tahunbudget.length<4) 
    {
        alert('Karakter Tahun Budget Tidak Tepat');
        return;
    }
	else if(trim(kodeorg)=='')
	{
		alert('Kode Afdeling masi kosong');
		return;
	}
	else if(trim(sb)=='')
	{
		alert('Siap Borong Masi Kosong');
		return;
	}
	else if(trim(lb)=='')
	{
		alert('Lebih Borong Masi Kosong');
		return;
	}
	else
	{
		tahunbudget=trim(tahunbudget);
		kodeorg=trim(kodeorg);
		sb=trim(sb);
		lb=trim(lb);
		
		
	param='tahunbudget='+tahunbudget+'&kodeorg='+kodeorg+'&sb='+sb+'&lb='+lb+'&method='+met;
	param+='&oldtahunbudget='+oldtahunbudget+'&oldkodeorg='+oldkodeorg;
		
		tujuan='bgt_slave_save_borong_panen.php';
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
							batalbor();							
                            loadData();
							//document.getElementById('container').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}
	



function fillField(tahunbudget,kodeorg,siapborong,lebihborong)
{
	document.getElementById('tahunbudget').value=tahunbudget;
	document.getElementById('oldtahunbudget').value=tahunbudget;
	document.getElementById('kodeorg').value=kodeorg;
	document.getElementById('oldkodeorg').value=kodeorg;
	
	document.getElementById('sb').value=siapborong;
   // document.getElementById('').disabled=true;
	document.getElementById('lb').value=lebihborong;
	//document.getElementById('method').value='update';
}








function batalbor()
{
    //document.getElementById('').disabled=false;
	document.getElementById('tahunbudget').value='';
	document.getElementById('kodeorg').value='';
	document.getElementById('sb').value='';
	document.getElementById('lb').value='';
	document.getElementById('method').value='insert';		
}


function loadData () 
{
	param='method=loadData';
	tujuan='bgt_slave_save_borong_panen.php';
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
                                   // alert(con.responseText);
                                    document.getElementById('containerData').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
}



function angka (b,ainput)
{
	var goodInput = ainput;
	var evt = (b)?b:window.event;
	var key_code = (document.all)?evt.keyCode:evt.which;
	if (key_code == 0 || key_code == 8) return true;
	if (goodInput.indexOf(String.fromCharCode(key_code)) == -1)
	{
		return false;
	}
	else
	return true;
} 