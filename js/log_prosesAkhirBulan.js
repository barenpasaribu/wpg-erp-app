/**
 * @author antoniuslouis
 */
function setSloc(x)
{
	pt = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
	if (pt != '') {
		if (x == 'simpan') {
			period=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
			tujuan = 'log_slave_getTutupBukuHarga.php';			
			param = 'pt=' + pt+'&period='+period;
			document.getElementById('sloc').disabled = true;
			document.getElementById('btnsloc').disabled = true;
			document.getElementById('periode').disabled = true;
				post_response_text(tujuan, param, respog);

		}
		else {
			document.getElementById('sloc').disabled = false;
			document.getElementById('sloc').options[0].selected=true;
			document.getElementById('btnsloc').disabled = false;
			document.getElementById('periode').disabled = false;
			document.getElementById('infoDisplay').innerHTML='';
			//kosongkan();
		}	
		
	}
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						document.getElementById('infoDisplay').innerHTML=con.responseText;
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('infoDisplay').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}		
}
maxf=0
sekarang=1;
function saveSaldoHarga(maxRow)
{     
       maxf=maxRow;
	if(confirm('This will take some time, are you sure..?'))
	    loopClosingHarga(1,maxRow);
}
function lanjut()
{
    loopClosingHarga(sekarang,maxf);
    document.getElementById('lanjut').style.display='none';
}

function loopClosingHarga(currRow,maxRow)
{
	periode		=trim(document.getElementById('period'+currRow).innerHTML);
	pt		=trim(document.getElementById('pt'+currRow).innerHTML);
	kodebarang	=trim(document.getElementById('kodebarang'+currRow).innerHTML);
        awal		=trim(document.getElementById('start'+currRow).innerHTML);
	akhir		=trim(document.getElementById('end'+currRow).innerHTML);
	if(pt=='' || periode=='' || kodebarang=='' || awal=='' || akhir =='')
	{
		alert("Data inconsistent");
	}	
    else
	{  
	    param='pt='+pt+'&periode='+periode+'&kodebarang='+kodebarang;
		param+='&awal='+awal+'&akhir='+akhir;
		tujuan = 'log_slave_saveTutupBukuHarga.php';
		post_response_text(tujuan, param, respog);
		document.getElementById('row'+currRow).style.backgroundColor='orange';
		lockScreen('wait');
	}
	function respog(){
		if (con.readyState == 4) {
			
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row'+currRow).style.backgroundColor='red';
                                        document.getElementById('lanjut').style.display='';
				   unlockScreen();
				}
				else {
					document.getElementById('row'+currRow).style.display='none';
                    currRow+=1;
					sekarang=currRow;
                                        if(currRow>maxRow)
					{
						alert('Done');
						unlockScreen();
						document.getElementById('infoDisplay').innerHTML='';
					}  
					else
					{
						loopClosingHarga(currRow,maxRow);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
                                document.getElementById('lanjut').style.display='';
				unlockScreen();
			}
		}
	}		
	
}