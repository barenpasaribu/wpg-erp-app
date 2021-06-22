/**
 * 
 */
function showHeader(num){
	jmtab =document.getElementById('jmtab').value;	
	for(x=1;x<=jmtab;x++){
		if (x==num){
			document.getElementById(x).style.display='block';
		}
		else {
			document.getElementById(x).style.display='none';
		}
	}
	//document.getElementById(num).style.display='block';
}
 
 
function cariLogDataKaryawan(page)
{
        alfield =document.getElementById('alfield').value;	
        schdata =document.getElementById('schdata');	
        schdata=schdata.options[schdata.selectedIndex].value;
	
        schuser =document.getElementById('schuser');	
        schuser=schuser.options[schuser.selectedIndex].value;

        schtahun =document.getElementById('schtahun');	
        schtahun=schtahun.options[schtahun.selectedIndex].value;

        schbulan =document.getElementById('schbulan');	
        schbulan=schbulan.options[schbulan.selectedIndex].value;

		param='schdata='+schdata;
		param+='&schuser='+schuser;
		param+='&schtahun='+schtahun;
		param+='&schbulan='+schbulan;
		param+='&page='+page;

		//alert(param);
		tujuan = 'sdm_slave_load_log_datakaryawan.php';
		if (schdata==''){
			alert(alfield);
		}
		else {
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
													document.getElementById('searchplaceresult'+schdata).innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }	
}


function prefDatakaryawan1(btn,curval)
{
	schdata =document.getElementById('schdata');	
    schdata=schdata.options[schdata.selectedIndex].value;
	
    cariLogDataKaryawan(curval); 
        if(curval==0)
        {
        }
        else
          btn.value=parseInt(curval)-1;
   document.getElementById('nextbtn'+schdata).value=parseInt(btn.value)+2;	  

}
function nextDatakaryawan1(btn,curval)
{
	schdata =document.getElementById('schdata');	
    schdata=schdata.options[schdata.selectedIndex].value;
      cariLogDataKaryawan(curval);
          btn.value=parseInt(curval)+1;
          document.getElementById('prefbtn'+schdata).value=parseInt(btn.value)-2;	
}



