/**
 * @author {anthesis-team@mitraagroservindo.com}
 */
function enablecheck(val)
{
	if(val==0)
	{
		disable_on(document.getElementById('sendmail'));
		document.getElementById('sendmail').checked=false;
	}
	else
	{
		disable_off(document.getElementById('sendmail'));
	}
}
function resetf(){
	document.getElementById('uname').value='';
	document.getElementById('pwd1').value='';
	document.getElementById('pwd2').value='';	
	document.getElementById('userid').selectedIndex=document.getElementById('userid').options[0];
	enablecheck(0);
	}


function savef()
{
	active=0; 
	sendmail=0;
	uname=document.getElementById('uname').value;;
	pw1=trim(document.getElementById('pwd1').value);
	pw2=trim(document.getElementById('pwd2').value);	
	userid=document.getElementById('userid').options[document.getElementById('userid').selectedIndex].value;
	
	if(document.getElementById('radio').checked)
	    active=1;
	if(document.getElementById('sendmail').checked)	 
        sendmail=1;	
	if(uname.length>5)
	{
		if(pw1==pw2)
		{
			if(pw1.length>5)
			{
				if(confirm('Are you sure...?'))
				{
					param='uname='+uname+'&sendmail='+sendmail;
					param+='&pw='+pw1+'&userid='+userid;
					param+='&active='+active;
					//alert(param);
					post_response_text('slave_newUser.php', param, respog);					
				}
			}
			else
			 alert("Password min. 6 Char");
		}
		else
			alert("Password does not macth");		
	}
	else
	  alert('Username min. 6 Char');	

	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('temp').innerHTML+=con.responseText;
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

function getUserForActivation()
{
	x=trim(document.getElementById('uname').value);
	param='uname='+x;
	if(x.length>0)
        post_response_text('slave_getUserForActive.php', param, respog);	
	else
	   {
	   	alert('Please fill username');
		document.getElementById('uname').focus();
	   }
	   
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('result').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

function validat(ev)
{

  key=getKey(ev);
  if(key==13)
    getUserForActivation();
  else
  return tanpa_kutip_dan_sepasi(ev);	
	
}

function validat1(ev)
{

  key=getKey(ev);
  if(key==13)
    getUserForResetP();
  else
  return tanpa_kutip_dan_sepasi(ev);	
	
}

function setActivate(uname)
{
	obj=document.getElementById(uname);
	if(obj.checked)
	  {
	  	attr='Click to deActivate';
		param='uname='+uname+'&setstatus=1';
	  }
	else
	{
	    attr='Click to Activate';	
		param='uname='+uname+'&setstatus=0';
	} 
   
   post_response_text('slave_setUserActivation.php', param, respog);
			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					obj.removeAttribute('title');
					obj.setAttribute('title',attr);
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	 
}

function delUser(uname,userid)
{
	param='uname='+uname+'&userid='+userid;
	if(confirm('Are you sure deleting '+uname+'. account...?'))
	{
	      if(confirm('Are you sure...?'))
		  {
             post_response_text('slave_deleteUserAccount.php', param, respog);		  	
		  }	
	}
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					alert('Account '+uname+' has been deleted');
					document.getElementById('row'+uname).style.display='none';
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function getUserForResetP()
{
	x=trim(document.getElementById('uname').value);
	param='uname='+x;
	if(x.length>0)
        post_response_text('slave_getUserForResetP.php', param, respog);	
	else
	   {
	   	alert('Please fill username');
		document.getElementById('uname').focus();
	   }
	   
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('result').innerHTML=con.responseText;
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

function showDial(uname,userid,e,obj)
{
	document.getElementById('uid').value=userid;
	document.getElementById('un').innerHTML=uname;
	win1=document.getElementById('resetter');
    cord= new Array();
	cord=getMouseP(e);
	win1.style.display='';
	win1.style.top=(cord[1])+'px';
	win1.style.left=(cord[0])+'px';	
}

function saveNewPwd()
{
  pwd1=trim(document.getElementById('newpwd1').value);	
  pwd2=trim(document.getElementById('newpwd2').value); 
  userid=trim(document.getElementById('uid').value);  
  uname=trim(document.getElementById('un').innerHTML.toString());
  
  sendmail=0;
  if(document.getElementById('sendmail').checked)
    sendmail=1;
	
   if(pwd1!=pwd2)
   {
   	 alert('Password does not match');
	 document.getElementById('newpwd1').value='';	
     document.getElementById('newpwd2').value=''; 
	 document.getElementById('newpwd1').focus();
   }
   else if(pwd1.length<6)
   {
   	 alert('Password min.6 character length');
	 document.getElementById('newpwd1').value='';	
     document.getElementById('newpwd2').value='';
	 document.getElementById('newpwd1').focus();   	
   }
   else if(confirm('Are you sure changing '+uname+' password..?'))
   {
   	resetPassword(uname,userid,pwd1,sendmail);
   }
}

function hideSetter()
{
  document.getElementById('newpwd1').value='';	
  document.getElementById('newpwd2').value=''; 
  document.getElementById('uid').value='';  
  document.getElementById('un').innerHTML='';
  document.getElementById('sendmail').checked=false;
  hideObject(document.getElementById('resetter'));
}

function resetPassword(uname,uid,pwd,sendmail)
{
	param='uname='+uname+'&userid='+uid+'&password='+pwd+'&sendmail='+sendmail;
    post_response_text('slave_resetUserPassword.php', param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					alert('Password for '+uname+' has been changed successfully'+con.responseText);
				   hideSetter();	
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
