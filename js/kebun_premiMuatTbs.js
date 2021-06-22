function batal()
{
	document.location.reload();
}


maxf=0
sekarang=1;
function saveAll(maxRow)
{     

	document.getElementById('tPreview').disabled=true;
	document.getElementById('tBatal').disabled=true;
      	 maxf=maxRow;
	    loopsave(1,maxRow);
}





function loopsave(currRow,maxRow)
{
	per=document.getElementById('per').value;
	kodeorg=document.getElementById('kodeorg').value;
	karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
	premiinput=trim(document.getElementById('premiinput'+currRow).innerHTML);
	if(per=='' || karyawanid=='' || premiinput=='')
	{
		alert("Data tidak lengkap");return;
	}	
    else
	{  
	    param='per='+per+'&kodeorg='+kodeorg+'&karyawanid='+karyawanid+'&premiinput='+premiinput
		param+="&proses=savedata";
		
		//alert(param);
		tujuan = 'kebun_slave_premiMuatTbs.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='cyan';
		//lockScreen('wait');
	}
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                    if(currRow>maxRow)
					{
						alert('Done');
						document.location.reload();	
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopsave(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                               // document.getElementById('lanjut').style.display='';
				//unlockScreen();
			}
		}
	}		
	
}