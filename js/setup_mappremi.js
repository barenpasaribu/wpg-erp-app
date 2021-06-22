// JavaScript Document
function loadData()
{
	param='method=loadNewData';
	tujuan='slave_setup_mappremi.php';
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
function cariBast(num)
{
		param='method=loadNewData';
		param+='&page='+num;
		tujuan = 'slave_setup_mappremi.php';
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
	kdOrg=document.getElementById('optOrg').value;
	tipePremi=document.getElementById('tipePremi').value;
	kyCode=document.getElementById('keyCode').value;
	met=document.getElementById('method');
	old=document.getElementById('oldKey');
	if(old.value=='')
	{
		param='kdOrg='+kdOrg+'&tipePremi='+tipePremi+'&kyCode='+kyCode+'&method='+met.value;
	}
	else if(old.value!='')
	{
		oldTipePremi=document.getElementById('oldtipePremi').value;
		param='kdOrg='+kdOrg+'&tipePremi='+tipePremi+'&kyCode='+kyCode+'&method='+met.value+'&oldData='+old.value+'&oldTipePremi='+oldTipePremi;
	}
	//alert(param);
	tujuan = 'slave_setup_mappremi.php';
	if(confirm("Are You Sure Want Save This Data"))
	{post_response_text(tujuan, param, respog);			}
	else
	{return;}
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						clearForm();
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

function fillField(kdOrg,tipePremi,key)
{	
	document.getElementById('optOrg').value=kdOrg;
	document.getElementById('tipePremi').value=tipePremi;
	document.getElementById('keyCode').value=key;
	document.getElementById('oldKey').value=key;
	document.getElementById('oldtipePremi').value=tipePremi;
	document.getElementById('method').value='updateData';
	document.getElementById('optOrg').disabled=true;
//	document.getElementById('tipePremi').disabled=true;
}
function cancelKeycode()
{
	document.getElementById('optOrg').disabled=false;
	document.getElementById('tipePremi').disabled=false;
}
function delCode(kdOrg,tipePremi,key)
{
	orgCode=kdOrg;
	premi=tipePremi;
	kyCode=key;
	met=document.getElementById('method');
	met.value='deleteData';
	param='kdOrg='+kdOrg+'&tipePremi='+tipePremi+'&kyCode='+kyCode+'&method='+met.value;
	tujuan = 'slave_setup_mappremi.php';
	if(confirm("Are You Sure Want Delete This Data"))
	{post_response_text(tujuan, param, respog);			}
	else
	{return;}
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
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
function clearForm()
{
	document.getElementById('optOrg').disabled=false;
	//document.getElementById('tipePremi').disabled=false;
	document.getElementById('method').value='insert';
}