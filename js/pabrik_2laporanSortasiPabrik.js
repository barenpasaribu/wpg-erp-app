// JavaScript Document
/*function getPemilik()
{
	intexId=document.getElementById('statBuah').options[document.getElementById('statBuah').selectedIndex].value;
	param='proses=getPemilik'+'&intextId='+intexId;
	tujuan='pabrik_slave_2laporanSortasiPabrik.php';
	alert(param);
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					alert(con.responseText);
					document.getElementById('idGab').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}*/
function getKbn()
{
	buahStat=document.getElementById('statBuah').options[document.getElementById('statBuah').selectedIndex].value;
        kdPbrk=document.getElementById('kdPbrk').options[document.getElementById('kdPbrk').selectedIndex].value;
	if(buahStat=='5')
	{
		document.getElementById('kdOrg').disabled=false;
		document.getElementById('suppId').disabled=false;
		document.getElementById('suppId').innerHTML='';
		document.getElementById('kdOrg').innerHTML='';
		document.getElementById('kdOrg').innerHTML=optInt;
		document.getElementById('suppId').innerHTML=optExt;
		return;
	}
	param='proses=getkbn'+'&BuahStat='+buahStat+'&kdPbrk='+kdPbrk;		
		//alert(param);
	tujuan='pabrik_slave_2laporanSortasiPabrik.php';
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
							//alert("Save Data Success !!!");
							ar=con.responseText.split("###")
							if(ar[1]==0)
							{
								document.getElementById('suppId').innerHTML=ar[0];
								document.getElementById('kdOrg').disabled=true;
								document.getElementById('suppId').disabled=false;
							}
							else if(ar[1]!=0)
							{
								document.getElementById('suppId').disabled=true;
								document.getElementById('kdOrg').disabled=false;
                                                                document.getElementById('kdOrg').innerHTML=ar[0];
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