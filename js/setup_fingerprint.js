/**
 * @author antoniuslouis
 */
function simpanJ()
{
	pin=remove_comma(document.getElementById('pin'));
	karyawanid=document.getElementById('karyawanid');
	karyawanid=karyawanid.options[karyawanid.selectedIndex].value;	
	met=document.getElementById('method').value;
	if(pin=='' || karyawanid=='')
	{
		alert('Each Field are obligatory');
	}
	else
	{ 
		param='method='+met;
		param+='&pin='+pin+'&karyawanid='+karyawanid;
		tujuan='setup_slave_fingerprint.php';
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
                                                        cancelJ();
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function dedel(karyawanid,pin)
{
	met='delete';
		param='method='+met;
		param+='&pin='+pin+'&karyawanid='+karyawanid;
		tujuan='setup_slave_fingerprint.php';
                
        if(confirm('Apus?'))    
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
                                                        cancelJ();
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(kelompok,nilai)
{
	document.getElementById('pin').value=nilai;
	grup=document.getElementById('karyawanid');
        grup.value='';
	for(x=0;x<grup.length;x++)
	{
		if(grup.options[x].value==kelompok)
		{
			grup.options[x].selected=true;
		}
	}
        grup.disabled=true;
	document.getElementById('method').value='update';
}

function cancelJ()
{
    
	document.getElementById('pin').value='';
	grup=document.getElementById('karyawanid');
        grup.disabled=false;
	grup=grup.options[0].selected=true;
	document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='setup_slave_fingerprint.php';
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

