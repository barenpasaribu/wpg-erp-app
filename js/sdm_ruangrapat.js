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
 met=document.getElementById('method').value;
 if(met=='updateData')
     {
         id=document.getElementById('idData').value;
         param+='&idData='+id;
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
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
	param='method=loadData';
	tujuan='sdm_slave_ruangrapat';
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
function cariBast(num)
{
    param='method=loadData';
    param+='&page='+num;
    tujuan = 'sdm_slave_ruangrapat.php';
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

function loadData2()
{
    val=document.getElementById('tglCari').value;
	param='method=loadData2';
        if(val!='')
        {
            param+='&tglCari='+val;
        }
	tujuan='sdm_slave_ruangrapat';
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
function cariBast2(num)
{
    val=document.getElementById('tglCari').value;
    param='method=loadData2';
    if(val!='')
    {
        param+='&tglCari='+val;
    }
    
    param+='&page='+num;
    tujuan = 'sdm_slave_ruangrapat.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('container2').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}


function fillField(id,tgl,roomdt,drjam,smpjam,agnd,pice)
{
    //$arr="##tanggalDt##tglAwal##tglEnd##method##agenda##room##pic##jam1##mnt1##jam2##mnt2";
    
    jm1=drjam.split(" ");
    tlgAwal=jm1[0].split("-");
    tglAwal1=tlgAwal[2]+"-"+tlgAwal[1]+"-"+tlgAwal[0];
    jmDari=jm1[1].split(":");


    jm2=smpjam.split(" ");
    tlgAkhir=jm2[0].split("-");
    tlgAkhir1=tlgAkhir[2]+"-"+tlgAkhir[1]+"-"+tlgAkhir[0];
    jamSmp=jm2[1].split(":");
    q=document.getElementById('jam1');
      for(a=0;a<q.length;a++)
      {
            if(q.options[a].value==jmDari[0])
                {
                    q.options[a].selected=true;
                }
       }
       q2=document.getElementById('mnt1');
      for(a2=0;a2<q2.length;a2++)
      {
            if(q2.options[a2].value==jmDari[1])
                {
                    q2.options[a2].selected=true;
                }
       }

       q3=document.getElementById('jam2');
      for(a3=0;a3<q3.length;a3++)
      {
            if(q.options[a3].value==jamSmp[0])
                {
                    q3.options[a3].selected=true;
                }
       }
       qakr=document.getElementById('mnt2');
      for(a5=0;a5<qakr.length;a5++)
      {
            if(qakr.options[a5].value==jamSmp[1])
                {
                    qakr.options[a5].selected=true;
                }
       }
    document.getElementById('room').value=roomdt;
    document.getElementById('tanggalDt').value=tgl;
    document.getElementById('agenda').value=agnd;
    document.getElementById('pic').value=pice;
    document.getElementById('tglAwal').value=tglAwal1;
    document.getElementById('tglEnd').value=tlgAkhir1;
    document.getElementById('idData').value=id;
    document.getElementById('method').value='updateData';
}
function cancelIsi()
{
    
    document.getElementById('tanggalDt').value='';
    document.getElementById('tglAwal').value='';
    document.getElementById('tglEnd').value='';
    document.getElementById('agenda').value='';
    document.getElementById('room').value='';
    document.getElementById('pic').value='';
    document.getElementById('jam1').value='00';
    document.getElementById('jam2').value='00';
    document.getElementById('mnt1').value='00';
    document.getElementById('mnt2').value='00';
    document.getElementById('method').value="insert";

}
function kancel(id)
{
    param='method=kancelDat'+'&idData='+id;
	tujuan='sdm_slave_ruangrapat';
	if(confirm("Anda yakin ingin cancel data ini"))
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
function delData(idFr)
{
	param='method=delData'+'&idFranco='+idFr;
	tujuan='sdm_slave_ruangrapat';
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