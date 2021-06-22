/**
 * @author antoniuslouis
 */

function fillField(satuan)
{
	document.getElementById('satuan').value=satuan;
	document.getElementById('old').innerHTML=satuan;
	document.getElementById('method').value='update';
	
}

function cancelSatuan(){

	document.getElementById('method').value='insert';
	document.getElementById('satuan').value='';
	document.getElementById('old').innerHTML='';
}

function saveSatuan()
{
	tujuan='log_slave_get_satuan.php';
	method=trim(document.getElementById('method').value);
	satuan=trim(document.getElementById('satuan').value);
	oldsatuan=trim(document.getElementById('old').innerHTML);
		
		
	param='method='+method+'&satuan='+satuan+'&oldsatuan='+oldsatuan;

   if(confirm('Saving/Simpan '+satuan+' .., Are you sure..?'))
   {
	 if(satuan=='')
	 	alert('UOM/Satuan is obligatory');
	 else
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
							document.getElementById('container').innerHTML=con.responseText;
						    alert('Done');
							cancelSatuan();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	

}

function delSatuan(satuan)
{
  tujuan='log_slave_get_satuan.php';
   param='satuan='+satuan+'&method=delete';
   if(confirm('Deleting '+satuan+' .., Are you sure..?'))
   {
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


