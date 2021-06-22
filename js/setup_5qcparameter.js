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
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='setup_slave_5qcparameter';
    post_response_text(tujuan+'.php', param, respon);
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
					  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function getData()
{
    tp=document.getElementById('tipeDt').options[document.getElementById('tipeDt').selectedIndex].value;
    param='method=getData'+'&tipeDt='+tp;
    tujuan='setup_slave_5qcparameter';
    post_response_text(tujuan+'.php', param, respon);
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
                            if(tp=='')
                                {
                                    	  document.getElementById('idData').value='';
                                          document.getElementById('idData').disabled=false;
                                }
                                else
                                    {
                                          document.getElementById('idData').value=con.responseText;
                                          document.getElementById('idData').disabled=true;
                                    }
					
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function fillField(tipe,id,nama,kelompok,satuan)
{
    document.getElementById('tipeDt').value=tipe;
    document.getElementById('idData').value=id;
    document.getElementById('nmQc').value=nama;
    document.getElementById('klmpkQc').value=kelompok;
    document.getElementById('satuan').value=satuan;
    document.getElementById('tipeDt').disabled=true;
    document.getElementById('idData').disabled=true;
    document.getElementById('method').value='updateData';
}
function cancelIsi()
{
 document.getElementById('tipeDt').value='';
    document.getElementById('idData').value='';
    document.getElementById('nmQc').value='';
    document.getElementById('klmpkQc').value='';
    document.getElementById('satuan').value='';
    document.getElementById('tipeDt').disabled=false;
    document.getElementById('idData').disabled=false;
	document.getElementById('method').value="insert";

}
function delData(idFr)
{
	param='method=delData'+'&idFranco='+idFr;
	tujuan='setup_slave_5qcparameter';
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