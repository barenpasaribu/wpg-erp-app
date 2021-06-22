function login()
{
	uname   =document.getElementById('username').value;
	password=document.getElementById('password').value;
	lang=document.getElementById('language').options[document.getElementById('language').selectedIndex].value;
	
	if (uname == '' || password == '') {
		alert('Your UserName and Password are required');
		document.getElementById('username').focus();
	}
	else {
			param = 'uname=' + uname + '&password=' + password +'&language='+lang;
			post_response_text('slave_login.php', param, respog);
	   }
	   
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					if (con.responseText.lastIndexOf('Wrong') > -1) {
						document.getElementById('msg').innerHTML = con.responseText;
					}
					else {
						window.location = 'master.php';
						//alert(con.responseText);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
			resetf();//clear from	
		}
	}	   
}

function resetf()//clear from	
{
	document.getElementById('username').value='';
	document.getElementById('password').value='';	
}

function enter(e)
{
  key=getKey(e);
  if(key==13)
    {
		login();
	    return true;
	}	
  else
   	{
		return tanpa_kutip_dan_sepasi(e);
	}	
}
//disable right click====================================
document.oncontextmenu=new Function('return false')
