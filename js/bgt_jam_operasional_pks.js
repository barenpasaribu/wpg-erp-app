function simpanpks()
{	
	tahunbudget=document.getElementById('tahunbudget').value;
	kodeorg=document.getElementById('kodeorg');
	kodeorg=kodeorg.options[kodeorg.selectedIndex].value;

	jamo=document.getElementById('jamo').value;	
	jamb=document.getElementById('jamb').value;
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
		tahunbudget=trim(tahunbudget);
		kodeorg=trim(kodeorg);
		jamo=trim(jamo);
		jamb=trim(jamb);
	
	param='tahunbudget='+tahunbudget+'&kodeorg='+kodeorg+'&jamo='+jamo+'&jamb='+jamb+'&method='+met;
	param+='&oldtahunbudget='+oldtahunbudget+'&oldkodeorg='+oldkodeorg;
		
	tujuan='bgt_slave_save_jam_operasional_pks.php';
	post_response_text(tujuan, param, respog);		
	}
	function respog()
	{
			  if(con.readyState==4)
			  {
					if (con.status == 200) 
					{
						busy_off();
						if (!isSaveResponse(con.responseText)) 
						{
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							batalpks();						
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

function loadData () 
{
	param='method=loadData';
	tujuan='bgt_slave_save_jam_operasional_pks.php';
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

function fillField(tahunbudget,millcode,jamolah,breakdown)
{
	document.getElementById('tahunbudget').value=tahunbudget;
	document.getElementById('oldtahunbudget').value=tahunbudget;
	document.getElementById('kodeorg').value=millcode;
	document.getElementById('oldkodeorg').value=millcode;
	
	document.getElementById('jamo').value=jamolah;
   // document.getElementById('').disabled=true;
	document.getElementById('jamb').value=breakdown;
	//document.getElementById('method').value='update';
}

function batalpks()
{
    //document.getElementById('').disabled=false;
	document.getElementById('tahunbudget').value='';
	document.getElementById('kodeorg').value='';
	document.getElementById('jamo').value='';
	document.getElementById('jamb').value='';
	document.getElementById('method').value='insert';			
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
function getHk()
{
		param = 'method=selectJam&thnbgt=';
		param = param + document.getElementById('tahunbudget').options[document.getElementById('tahunbudget').selectedIndex].value;
        tujuan='bgt_slave_save_jam_operasional_pks.php';
        post_response_text(tujuan, param, respog);
        function respog(){
              if(con.readyState==4){
                    if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                 //	alert(con.responseText);
                                  document.getElementById('jamo').value='';
                                  document.getElementById('jamo').value=con.responseText;
                                }
                        }
                        else {
                            busy_off();
                            error_catch(con.status);
                        }
              }	
         }  

}