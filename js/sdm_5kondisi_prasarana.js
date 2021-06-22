// JavaScript Document
function saveFranco(fileTarget,passParam) {
	
    var passP = passParam.split('##');
    var param = "";
	
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
	tujuan='sdm_slave_5kondisi_prasarana';
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
		tujuan = 'sdm_slave_5kondisi_prasarana.php';
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
function fillField(idFr,tgl)
{
	
	param='method=getData'+'&kdSarana='+idFr+'&tglKonSarana='+tgl;
	tujuan='sdm_slave_5kondisi_prasarana';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    
					ar=con.responseText.split("###");
                                        
                                        l=document.getElementById('kdSarana');
                                        for(a=0;a<l.length;a++)
                                        {
                                            if(l.options[a].value==idFr)
                                                {
                                                    l.options[a].selected=true;
                                                }
                                        }
                                        document.getElementById('jmlhSarana').value=ar[0];
                                        document.getElementById('tglKonSarana').value=tgl;
                                        document.getElementById('tglKonSarana').disabled=true;
                                        document.getElementById('kdSarana').disabled=true;
					lLokasi2=document.getElementById('kondId');
                                        for(ard2=0;ard2<lLokasi2.length;ard2++)
                                        {
                                            if(lLokasi2.options[ard2].value==ar[1])
                                                {
                                                    lLokasi2.options[ard2].selected=true;
                                                }
                                        }
                                        lLokasi=document.getElementById('idProgress');
                                        for(ard=0;ard<lLokasi.length;ard++)
                                        {
                                            if(lLokasi.options[ard].value==ar[2])
                                                {
                                                    lLokasi.options[ard].selected=true;
                                                }
                                        }
                                        
                                        getSatuan(idFr);
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
	document.getElementById('kdSarana').value='';
        document.getElementById('kdSarana').disabled=false;
	document.getElementById('tglKonSarana').value='';
        document.getElementById('tglKonSarana').disabled=false;
	document.getElementById('kondId').value='';
        document.getElementById('idProgress').value='';
	document.getElementById('satuan').innerHTML='';
	document.getElementById('method').value="insert";
}

function getSatuan(jns)
{
    if(jns==0)
        {
            kdSarana=document.getElementById('kdSarana').options[document.getElementById('kdSarana').selectedIndex].value;
        }
        else
        {
            kdSarana=jns;
        }
        param='method=getSatuan'+'&kdSarana='+kdSarana;
        //alert(param);
        tujuan='sdm_slave_5kondisi_prasarana';
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
function delData(idFr,tgl)
{
	param='method=delData'+'&kdSarana='+idFr+'&tglKonSarana='+tgl;
	tujuan='sdm_slave_5kondisi_prasarana';
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