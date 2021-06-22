// JavaScript Document
function loadData()
{
	param='method=loadData';
	tujuan='slave_setup_keycode.php';
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
function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'slave_setup_keycode.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
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
function smpnKeycode()
{
	cde=document.getElementById('keyCode').value;
	ktrng=document.getElementById('ket').value;
	met=document.getElementById('method').value;
	oldCode=document.getElementById('oldCode');
	if(oldCode.value=='')
	{
		param='Code='+cde+'&ket='+ktrng+'&method='+met;
	}
	else if(oldCode.value!='')
	{
		param='Code='+cde+'&ket='+ktrng+'&method='+met+'&oldCode='+oldCode.value;
	}
	tujuan='slave_setup_keycode.php';
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
							document.getElementById('keyCode').value='';
							document.getElementById('ket').value='';
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
function fillField(cde,ktrng)
{
	document.getElementById('keyCode').value=cde;
	document.getElementById('oldCode').value=cde;
	document.getElementById('ket').value=ktrng;
	document.getElementById('method').value='updateCode';
}
function delCode(cde)
{
	code=cde;
	param='Code='+code+'&method=delData';
	tujuan='slave_setup_keycode.php';
	if(confirm("Are You Sure Want Delete This Data"))
	{post_response_text(tujuan, param, respog);}
	else
	{return;}
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
function cancelKeycode()
{
	document.getElementById('keyCode').value='';
	document.getElementById('ket').value='';
	document.getElementById('method').value='insert';
}