/**
 * @author antoniuslouis
 */
function simpanDep()
{
	var namapasar=document.getElementById('namapasar').value,
		id = document.getElementById('idPasar').value,
		method = document.getElementById('mode').value,
        param='id='+id+'&namapasar='+namapasar+'&method='+method;
        tujuan='pmn_slave_5pasar.php';
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

function editField(id,nama)
{
    var elNamapasar=document.getElementById('namapasar'),
		elId = document.getElementById('idPasar'),
		method = document.getElementById('mode'),
		modeStr = document.getElementById('modeStr'),
		addModeBtn = document.getElementById('addModeBtn');
	method.value = 'update';
	modeStr.innerHTML = ': Edit Mode';
	elId.value = id;
	elNamapasar.value = nama;
	addModeBtn.disabled = false;
}

function addMode() {
	var elNamapasar=document.getElementById('namapasar'),
		elId = document.getElementById('idPasar'),
		method = document.getElementById('mode'),
		modeStr = document.getElementById('modeStr'),
		addModeBtn = document.getElementById('addModeBtn');
	method.value = 'insert';
	modeStr.innerHTML = ': Add Mode';
	elId.value = '';
	elNamapasar.value = '';
	addModeBtn.disabled = true;
}