/**
 * @author antoniuslouis
 */
function simpanJ()
{
	periodegaji=document.getElementById('periodegaji').options[document.getElementById('periodegaji').selectedIndex].value;
	idkaryawan=document.getElementById('idkaryawan').options[document.getElementById('idkaryawan').selectedIndex].value;
	upahpremi=document.getElementById('upahpremi').value;
	komponenpayroll=document.getElementById('komponenpayroll').options[document.getElementById('komponenpayroll').selectedIndex].value;
	met=document.getElementById('method').value;
	if(trim(periodegaji)=='' || idkaryawan=='' || upahpremi=='' || upahpremi=='0' || upahpremi=='0.00')
	{
		alert('Each Field are obligatory');
		document.getElementById('periodegaji').focus();
	}
	else
	{
		param='periodegaji='+periodegaji+'&idkaryawan='+idkaryawan+'&method='+met;
		param+='&upahpremi='+upahpremi+'&komponenpayroll='+komponenpayroll;
		tujuan='sdm_slave_rapel_kebun.php';
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
                                                        document.getElementById('idkaryawan').value='';
                                                        document.getElementById('upahpremi').value=0;
                                                       // showPremi1();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function showPremi1()
{
	periodegaji=document.getElementById('periodegaji').options[document.getElementById('periodegaji').selectedIndex].value;
	showPremi();
}

function showPremi2()
{
	periodegaji=document.getElementById('periodegaji').options[document.getElementById('periodegaji2').selectedIndex].value;
	showPremi();
}


function showPremi(){
	met='show';
		param='periodegaji='+periodegaji+'&method='+met;
		tujuan='sdm_slave_rapel_kebun.php';
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

function delPremi(periodegaji,idkaryawan,upahpremi,komponenpayroll)
{
	   	param='periodegaji='+periodegaji+'&idkaryawan='+idkaryawan+'&komponenpayroll='+komponenpayroll+'&method=delete';
		if (confirm('deleting, Are you sure..?')) {
			post_response_text('sdm_slave_rapel_kebun.php', param, respon);
		}	
   function respon(){
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


function cancelJ()
{
	document.getElementById('periodegaji').value='';
	document.getElementById('idkaryawan').value='';
	document.getElementById('upahpremi').value=0;
}
