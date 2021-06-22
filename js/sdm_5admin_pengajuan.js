/**
 * @author Developer e-Komoditi Solutions Indonesia
 */
 
function baru(){
	document.getElementById('form').style.display = '';
	document.getElementById('listdata').style.display = 'none';
}

function listdata(){
	document.getElementById('form').style.display = 'none';
	document.getElementById('listdata').style.display = '';
}

function simpanJ()
{
          
		cnsimpan = document.getElementById('cnfsmpn').value;
		dttdklkp = document.getElementById('tdklkp').value;
		dtsmpn = document.getElementById('dtsmpn').value;
		ids = document.getElementById('ids').value;
		org=document.getElementById('org');
        org=org.options[org.selectedIndex].value;
		
		kryw=document.getElementById('kryw');
        kryw=kryw.options[kryw.selectedIndex].value;		
		
		user=document.getElementById('user');
        user=user.options[user.selectedIndex].value;		
		  	
        met=document.getElementById('method').value;
        if(org=='' || kryw==''|| user=='')
        {
                alert(dttdklkp);
        }
        else
        {
			 if(confirm(cnsimpan)){
				param='method='+met+'&ids='+ids;
                param+='&org='+org+'&kryw='+kryw+'&user='+user;
                tujuan='sdm_slave_save_5adminpengajuan.php';
				post_response_text(tujuan, param, respog);
			 }
               		
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
														alert(dtsmpn);
                                                        cancelJ();
														document.getElementById('form').style.display = 'none';
														document.getElementById('listdata').style.display = '';
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

function getKaryawan(){
	org=document.getElementById('org');
    org=org.options[org.selectedIndex].value;
	
	param='method=getkry';
	param+='&org='+org;
	tujuan='sdm_slave_save_5adminpengajuan.php';
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
										document.getElementById('kryw').innerHTML=con.responseText;
										getUser();
									}
							}
							else {
									busy_off();
									error_catch(con.status);
							}
		  }	
	 }

}

function getUser(){
	org=document.getElementById('org');
    org=org.options[org.selectedIndex].value;
	
	param='method=getusr';
	param+='&org='+org;
	tujuan='sdm_slave_save_5adminpengajuan.php';
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
											document.getElementById('user').innerHTML=con.responseText;
									}
							}
							else {
									busy_off();
									error_catch(con.status);
							}
		  }	
	 }

}

function fillField(ids,loktugA,kryA,UsrA)
{
	document.getElementById('form').style.display = '';
	document.getElementById('listdata').style.display = 'none';
	loktug=document.getElementById('org');
	for(x=0;x<loktug.length;x++)
	{
			if(loktug.options[x].value==loktugA)
			{
					loktug.options[x].selected=true;
			}
	}
	
	kryw=document.getElementById('kryw');
	for(x=0;x<kryw.length;x++)
	{
			if(kryw.options[x].value==kryA)
			{
					kryw.options[x].selected=true;
			}
	}
        	
	user=document.getElementById('user');
	for(x=0;x<user.length;x++)
	{
			if(user.options[x].value==UsrA)
			{
					user.options[x].selected=true;
			}
	}
        
  
    document.getElementById('method').value='update';
    document.getElementById('ids').value=ids;
}


function delField(ids)
{
		cnhapus = document.getElementById('cnfhps').value;
        param='method=delete';
        param+='&ids='+ids;
        tujuan='sdm_slave_save_5adminpengajuan.php';
        
        if(confirm(cnhapus)){
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
                                                        cancelJ();
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

function cancelJ()
{

        document.getElementById('org').options[0].selected=true;
        document.getElementById('kryw').options[0].selected=true;
        document.getElementById('user').options[0].selected=true;
        document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='sdm_slave_save_5adminpengajuan.php';
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

function search(){
	filter  = document.getElementById('filter').value;
	keyword = document.getElementById('keyword').value;
	if(filter==''){
		document.getElementById('keyword').value='';
	}
	else {
		if(keyword=='') {
			alert(document.getElementById('findword').innerHTML + ' ' + document.getElementById('alertrequired').value);
		}
	}

	param='filter='+filter+'&keyword='+keyword+'&method=refresh_data';
	tujuan = 'sdm_slave_save_5adminpengajuan.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		  if(con.readyState==4){
					if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							alert('ERROR\n' + con.responseText);
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

