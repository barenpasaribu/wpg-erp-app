// JavaScript Document

function resetJurnal()
{
    //alert("masuk");
        kodePt=document.getElementById('kodePt').options[document.getElementById('kodePt').selectedIndex].value;
        
	param='proses=resetData'+'&kodePt='+kodePt;
	//alert(param);
	tujuan='keu_slave_5kelompokjurnal_reset.php';
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
						//	alert(con.responseText);
						//eval(con.responseText);
                                                if(con.responseText==1)
                                                    {
                                                        alert("Reset Berhasil");
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
