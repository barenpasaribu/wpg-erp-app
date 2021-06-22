/**
 * @author antoniuslouis
 */

 function addNewLanguage()
 {
   nlang=document.getElementById('lang').value;
   langname=document.getElementById('langname').value;
   deflang=document.getElementById('def').options[document.getElementById('def').selectedIndex].value;
   if (trim(nlang) == '') {
   	alert('New language is empty');
   }
   else {
   	param = 'newlang=' + trim(nlang)+'&def='+deflang+'&langname='+trim(langname);
   	post_response_text('slaveSaveNewLanguage.php', param, respon);
   }
   function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							document.getElementById('avlanguage').innerHTML=con.responseText;
							document.getElementById('lang').value='';
   							document.getElementById('langname').value='';
							alert('New language has been added, please configure..');
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }		
 }
 
function loadLang(langname)
 {
    param = 'langname='+langname;
   	post_response_text('slaveDetaillangConfiguration.php', param, respon);

   function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							document.getElementById('langdetailconf').innerHTML=con.responseText;
						    document.getElementById('defaultfind').innerHTML=langname;
							document.getElementById('defaultfind1').innerHTML=langname;
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }			
 }

function duaevent(event)
{
	kibod=getKey(event);
	if(kibod==13)
	{
		findComp();
	}
	else
	{
		return tanpa_kutip(event);
	}
}
 function findComp()
 {
   
  langnamex=document.getElementById('defaultfind').innerHTML;
   searclangx=document.getElementById('searclang').value;
   z=searclangx.replace(/^\s+|\s+$/g, '')
   if(z!='')
   {
    param = 'langname='+langnamex+'&findlang='+z;
   	post_response_text('slaveDetaillangConfiguration.php', param, respon);   	
   }

   function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							document.getElementById('langdetailconf').innerHTML=con.responseText;
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }		   	
 }

function updateCaption(index,objloc,objcap,langname)
{
	newloc=trim(document.getElementById(objloc).value);
	newcap=trim(document.getElementById(objcap).value);
	rownum=index;
	if(newloc!='' && newcap !='')
	{
		param='idx='+rownum+'&newcap='+newcap+'&newloc='+newloc+'&langname='+langname;
		//alert(index);
		post_response_text('slave_saveLlangConfiguration.php', param, respon);
	    document.getElementById(objcap).style.backgroundColor='orange';
		document.getElementById(objloc).style.backgroundColor='orange';
	}

   function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							alert('Saved');
							document.getElementById(objcap).style.backgroundColor='#dedede';
							document.getElementById(objloc).style.backgroundColor='#dedede';
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }	
} 

function saveNewCaption(num)
{
	arg='';
	cont='';
	legend	=trim(document.getElementById('legend').value);
	loc     =trim(document.getElementById('location').value);
	for(x=1;x<=num;x++)
	{
		if (x == 1) {
			arg +=trim(document.getElementById('hidden' + x).value);
			cont +=trim(document.getElementById('lang' + x).value);		
		}
		else {
			arg += "##" + trim(document.getElementById('hidden' + x).value);
			cont += "##" + trim(document.getElementById('lang' + x).value);
		}
	}
	if(legend!='' && location !='')
	{
		param='legend='+legend+'&location='+loc+'&arg='+arg+'&cont='+cont;
		//alert(param);
		post_response_text('slave_saveNewLanguageCaption.php', param, respon);
	}
   function respon(){
		        if (con.readyState == 4) {
		            if (con.status == 200) {
		                busy_off();
		                if (!isSaveResponse(con.responseText)) {
		                    alert('ERROR TRANSACTION,\n' + con.responseText);
		                }
		                else {
							alert('Saved');
							document.getElementById('legend').value='';
							document.getElementById('location').value='';
							for(x=1;x<=num;x++)
							{
								document.getElementById('lang'+x).value='';
							}							
						}
		            }
		            else {
		                busy_off();
		                error_catch(con.status);
		            }
		        }
		    }	
}
