// JavaScript Document
function saveFranco(fileTarget,passParam) {
	statFr=document.getElementById('statFr');
    var passP = passParam.split('##');
    var param = "";
	if(statFr.checked==true)
	{
		statFr.value=0;
	}
	else
	{
		statFr.value=1;
	}
	param='statFr='+statFr.value+'&';
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	//alert(param);
  //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        loadData();
                        cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='sdm_slave_prasarana';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cariBast2(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'sdm_slave_prasarana.php';
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
function fillField(idFr)
{
	
	param='method=getData'+'&idData='+idFr;
	tujuan='sdm_slave_prasarana';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //$arr="##kdOrg##idKlmpk##idJenis##idLokasi##jmlhSarana##method##thnPerolehan##blnPerolehan##statFr";
					ar=con.responseText.split("###");
                                        
					document.getElementById('thnPerolehan').value=ar[0];
					document.getElementById('blnPerolehan').value=ar[1];
					document.getElementById('jmlhSarana').value=ar[2];
                                        l=document.getElementById('idKlmpk');
                                        for(a=0;a<l.length;a++)
                                        {
                                            if(l.options[a].value==ar[3])
                                                {
                                                    l.options[a].selected=true;
                                                }
                                        }
					
					if(ar[4]==1)
					{
						document.getElementById('statFr').checked=true;
					}
                                        lLokasi=document.getElementById('idLokasi');
                                        for(ard=0;ard<lLokasi.length;ard++)
                                        {
                                            if(lLokasi.options[ard].value==ar[5])
                                                {
                                                    lLokasi.options[ard].selected=true;
                                                }
                                        }
                                        document.getElementById('idData').value=ar[7];
                                        getJenis(ar[3],ar[6]);
					document.getElementById('method').value="update";
					
					 // document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function cancelIsi()
{
	document.getElementById('idKlmpk').value='';
	document.getElementById('idJenis').innerHTML="<option value=''>"+pilih+"</option>";
	document.getElementById('idLokasi').value='';
        document.getElementById('jmlhSarana').value='';
        document.getElementById('thnPerolehan').value='';
        document.getElementById('blnPerolehan').value='';
	document.getElementById('satuan').innerHTML='';
	document.getElementById('method').value="insert";
	document.getElementById('statFr').checked=false;
	
}
function getJenis(klmpk,jns)
{
    if(klmpk==0||jns==0)
        {
            idKlmpk=document.getElementById('idKlmpk').options[document.getElementById('idKlmpk').selectedIndex].value;
        }
        else
        {
            idKlmpk=klmpk;
            idJenis=jns;
        }
        param='method=getJenis'+'&idKlmpk='+idKlmpk;
        if(jns!=0)
        {
           param+='&idJenis='+idJenis;
        }
        tujuan='sdm_slave_prasarana';
        post_response_text(tujuan+'.php', param, respon);
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                var res = document.getElementById('idJenis');
                res.innerHTML = con.responseText;
		if(jns!=0)
                    {
                        getSatuan(jns);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
}
function getSatuan(jns)
{
    if(jns==0)
        {
            idJenis=document.getElementById('idJenis').options[document.getElementById('idJenis').selectedIndex].value;
            
        }
        else
        {
            idJenis=jns;
        }
        param='method=getSatuan'+'&idJenis='+idJenis;
        tujuan='sdm_slave_prasarana';
        post_response_text(tujuan+'.php', param, respon);
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                var res = document.getElementById('satuan');
                res.innerHTML = con.responseText;
		
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
}
function delData(idFr)
{
	param='method=delData'+'&idData='+idFr;
	tujuan='sdm_slave_prasarana';
	if(confirm("Anda yakin ingin menghapus"))
    {
		post_response_text(tujuan+'.php', param, respon);
	}
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  loadData();
					  cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}