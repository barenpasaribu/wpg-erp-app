/**
 * @author antoniuslouis
 */
function simpanPendidikan()
{
	edulevel=trim(document.getElementById('edulevel').value);
	eduname=trim(document.getElementById('eduname').value);
	edugroup=trim(document.getElementById('edugroup').value);
	eduid=trim(document.getElementById('eduid').value);
	met=document.getElementById('method').value;
	if(edulevel=='' || eduname=='')
	{
		alert('Level & Name is empty');
		document.getElementById('edulevel').focus();
	}
	else
	{
		param='edulevel='+edulevel+'&eduname='+eduname+'&edugroup='+edugroup+'&eduid='+eduid+'&method='+met;
		tujuan='sdm_slave_save_5pendidikan.php';
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

function fillField(level,namex,group,eduid)
{
	document.getElementById('edulevel').value=level;
	document.getElementById('eduname').value=namex;
	document.getElementById('edugroup').value=group;
	document.getElementById('eduid').value=eduid;		
	document.getElementById('method').value='update';
}

function cancelJabatan()
{
	document.getElementById('edulevel').value='';
	document.getElementById('eduname').value='';
	document.getElementById('edugroup').value='';
	document.getElementById('eduid').value='';
	document.getElementById('method').value='insert';		
}

function delPendidikan(eduid)
{

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
		param='eduid='+eduid+'&method=delete';
		tujuan='sdm_slave_save_5pendidikan.php';
        if(confirm('You Are deleting Education List, Are you Sure..?'))
		     post_response_text(tujuan, param, respog);	
}
