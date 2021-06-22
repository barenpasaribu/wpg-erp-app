/**
 * @author antoniuslouis
 */
function browseTable(tablename)
{
		txt=trim(document.getElementById('txtcari').value);
		if (txt == 'All') {
			txt = '';
		}
		ordr=document.getElementById('order1').options[document.getElementById('order1').selectedIndex].value+','+document.getElementById('order2').options[document.getElementById('order2').selectedIndex].value; 
		field=document.getElementById('field').options[document.getElementById('field').selectedIndex].value;
		periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
		param='tablename='+tablename+'&txttofind='+txt+'&field='+field+'&order='+ordr+'&periode='+periode;
		tujuan='slave_table_browser.php';
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

function navigatepage(tablename)
{
		txt=trim(document.getElementById('txtcari').value);
		if (txt == 'All') {
			txt = '';
		}		
		page=trim(document.getElementById('page').options[document.getElementById('page').selectedIndex].value);
		field=document.getElementById('field').options[document.getElementById('field').selectedIndex].value;
		ordr=document.getElementById('order1').options[document.getElementById('order1').selectedIndex].value+','+document.getElementById('order2').options[document.getElementById('order2').selectedIndex].value;
		param='tablename='+tablename+'&txttofind='+txt+'&field='+field+'&page='+page+'&order='+ordr;
		tujuan='slave_table_browser.php';
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

function checkThis(obj)
{
	if(trim(obj.value)=='')
	{
		obj.value='All';
	}
}
