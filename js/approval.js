/**
 * @author antoniuslouis
 */
function simpanDep()
{
	kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
	app=document.getElementById('app').value;
	met=document.getElementById('method').value;
        karyawanid=document.getElementById('karyawanid').options[document.getElementById('karyawanid').selectedIndex].value;

        param='kodeorg='+kodeorg+'&app='+app+'&method='+met+'&karyawanid='+karyawanid;
        tujuan='setup_slave_save_approval.php';
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

function dellField(kodeorg,app,karyawanid)
{
         met='delete';
        param='kodeorg='+kodeorg+'&app='+app+'&method='+met+'&karyawanid='+karyawanid;
       // alert(param);
        tujuan='setup_slave_save_approval.php';
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

