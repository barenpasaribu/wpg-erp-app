maxf=0
sekarang=1;
function saveAll(maxRow)
{     

      	 maxf=maxRow;
	    loopsave(1,maxRow);
}


function batal()
{
	document.getElementById('per').value='';	
	document.getElementById('printContainer').innerHTML='';	
}


function loopsave(currRow,maxRow)
{
	periode=trim(document.getElementById('periode'+currRow).innerHTML);
	karyawanid=trim(document.getElementById('karyawanid'+currRow).innerHTML);
	premi=trim(document.getElementById('premi'+currRow).innerHTML);
	
	if(periode=='' || karyawanid=='' || premi=='')
	{
		alert("Data tidak lengkap");return;
	}	
    else
	{  
	    param='periode='+periode+'&karyawanid='+karyawanid+'&premi='+premi;
		param+="&proses=savedata";
		
		//alert(param);
		tujuan = 'sdm_slave_premiKehadiran.php';
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
						//document.getElementById('infoDisplay').innerHTML='';
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