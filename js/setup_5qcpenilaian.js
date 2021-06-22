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
    dr=document.getElementById('method').value;
    if(dr=='updateData')
        {
            idData=document.getElementById('idData2').value;
            param+='&idData2='+idData;
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
	tujuan='setup_slave_5qcpenilaian';
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
                                          loadData2();
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
    tujuan='setup_slave_5qcpenilaian';
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
////$arr="##tipeDt##maxData##nilData##method";
function fillField(tipe,mx,nil)
{
    document.getElementById('tipeDt').value=tipe;
    document.getElementById('idData2').value=mx;
    document.getElementById('nilData').value=nil;
    document.getElementById('maxData').value=mx;
    document.getElementById('tipeDt').disabled=true;
    document.getElementById('method').value='updateData';
}
function cancelIsi()
{
    document.getElementById('tipeDt').value='';
    document.getElementById('idData2').value='';
    document.getElementById('nilData').value='';
    document.getElementById('maxData').value='';
    document.getElementById('tipeDt').disabled=false;
    document.getElementById('method').value="insert";

}
function delData(idFr)
{
	param='method=delData'+'&idFranco='+idFr;
	tujuan='setup_slave_5qcpenilaian';
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


///part 2
// JavaScript Document
function saveFranco2(fileTarget,passParam) {

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
    dr=document.getElementById('method2').value;
    if(dr=='updateData')
        {
            idData=document.getElementById('idData3').value;
            param+='&idData2='+idData;
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
						loadData2();
						cancelIsi2();
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
function loadData2()
{
	param='method2=loadData';
	tujuan='setup_slave_5qcpenilaian2';
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
					  document.getElementById('container2').innerHTML=con.responseText;
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
    tujuan='setup_slave_5qcpenilaian2';
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
////$arr="##tipeDt##maxData##nilData##method";
function fillField2(kd,nm,mx,nil)
{
    document.getElementById('kdData').value=kd
    document.getElementById('nmData').value=nm;
    document.getElementById('idData3').value=mx;
    document.getElementById('nilData2').value=nil;
    document.getElementById('maxData2').value=mx;
    document.getElementById('kdData').disabled=true;
    document.getElementById('method2').value='updateData';
}
function cancelIsi2()
{
    document.getElementById('kdData').value='';
    document.getElementById('nmData').value='';
    document.getElementById('idData3').value='';
    document.getElementById('nilData2').value='';
    document.getElementById('maxData2').value='';
    document.getElementById('idData3').value='';
    document.getElementById('kdData').disabled=false;
    document.getElementById('method').value="insert";

}
function delData(idFr)
{
	param='method=delData'+'&idFranco='+idFr;
	tujuan='setup_slave_5qcpenilaian2';
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