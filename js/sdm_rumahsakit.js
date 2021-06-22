/**
 * @author antoniuslouis
 */
function saveHospital()
{
	update	=trim(document.getElementById('update').value);
	hosid	=trim(document.getElementById('hosid').value);
	hosname	=trim(document.getElementById('hosname').value);
	hosadd	=trim(document.getElementById('hosadd').value);
	hoscity	=trim(document.getElementById('hoscity').value);
	hosphone=trim(document.getElementById('hosphone').value);
	hosmail =trim(document.getElementById('hosmail').value);
	status	=document.getElementById('status').options[document.getElementById('status').selectedIndex].value;
  if(hosname=='' || hoscity=='')
  {
  	alert('Hospital name and city is obligatoy');
  }	
  else
  {
  	param='name='+hosname+'&add='+hosadd+'&city='+hoscity+'&id='+hosid;
	param+='&phone='+hosphone+'&mail='+hosmail+'&status='+status;
	if(update=='yes')
	  param+='&update=yes';
   
   post_response_text('sdm_slaveSaveRumahSakit.php', param, respon);	
  }
		    function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							document.getElementById('tbody').innerHTML=con.responseText;
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    } 
}

function deleteHospital(id)
{
	param='id='+id+'&del=yes';
    if (confirm('Deleting,Are you sure..?')) {
		post_response_text('sdm_slaveSaveRumahSakit.php', param, respon);
	}	    function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							document.getElementById('tbody').innerHTML=con.responseText;
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    } 	
}

function editHospital(id,name,city,add,phone,mail,status)
{
	document.getElementById('update').value='yes';
	document.getElementById('label').innerHTML='Edit';
	document.getElementById('hosid').value=id;
	document.getElementById('hosname').value=name;
	document.getElementById('hosadd').value=add;
	document.getElementById('hoscity').value=city;
	document.getElementById('hosphone').value=phone;
	document.getElementById('hosmail').value=mail;
	if (status == '1') {
		document.getElementById('status').options[2]=new Option('Active',status,false,false);
		document.getElementById('status').selectedIndex=2;
	}
	else
	{
		document.getElementById('status').options[2]=new Option('Black List',status,false,false);
		document.getElementById('status').selectedIndex=2;		
	}
	
}
function cancelHospital()
{
  window.location.reload();
}

function saveDiagnosa()
{
	idx=document.getElementById('idx').value;
	name=document.getElementById('name').value;
	param='idx='+idx+'&name='+name;
	if (name != '') {
		if (confirm('Saving.., are you sure..?')) 
			post_response_text('sdm_slaveSaveMedicalDiagnosa.php', param, respon);
	}
	else
	{
		alert('Diagnosis undefinded');
	}	 
		 
   function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
					document.getElementById('idx').value='';
					document.getElementById('name').value='';
					document.getElementById('tbody').innerHTML=con.responseText;
				}
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
function editDiagnosa(id,name)
{
	document.getElementById('idx').value=id;
	document.getElementById('name').value=name;	
}