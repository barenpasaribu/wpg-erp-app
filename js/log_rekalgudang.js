
function getPeriode(){
	gdngId=document.getElementById('gdngId').options[document.getElementById('gdngId').selectedIndex].value;
	param='gdngId='+gdngId+'&proses=getPeriode';
	tujuan="log_slave_rekalgudang.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('periodeGdng').innerHTML=con.responseText;
                        document.getElementById('kmlpkBrg').innerHTML=dert;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);

}
function getPeriode2(){
	gdngId=document.getElementById('gdngId2').options[document.getElementById('gdngId2').selectedIndex].value;
	param='gdngId='+gdngId+'&proses=getPeriode';
	tujuan="log_slave_rekalgudang.php";
	 
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('periodeGdng2').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);

}
function getKlmpkBrg(){
        gdngId=document.getElementById('periodeGdng').options[document.getElementById('periodeGdng').selectedIndex].value;
        gdngId2=document.getElementById('gdngId').options[document.getElementById('gdngId').selectedIndex].value;
	param='periodeGdng='+gdngId+'&proses=getKlmmpkBrg'+'&gdngId='+gdngId2;
	tujuan="log_slave_rekalgudang.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('kmlpkBrg').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
 /* Function zPreview
 * Fungsi untuk preview sebuah report
 * I : target file, parameter yang akan dilempar, id container
 * O : report dalam bentuk HTML
 */
function zPreview(fileTarget,passParam,idCont) {
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
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                        dertf=con.responseText.split("#####");
                      if(dertf[1]=='1'){
                          alert("Done!!");
                          document.getElementById('periodeGdng').innerHTML=dert2;
                          document.getElementById('nmBrg').innerHTML="";
                          document.getElementById('kdBrg').value="";
                          var res = document.getElementById(idCont);
                          res.innerHTML = dertf[0];

                          
                      }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?proses=preview', param, respon);
}
function getKdBrg(title,content,ev){
    gdng=document.getElementById('gdngId');
    gdng=gdng.options[gdng.selectedIndex].value;
    prd=document.getElementById('periodeGdng');
    prd=prd.options[prd.selectedIndex].value;
    if((gdng=='')||(prd=='')){
        alert('Gudan dan periode tidak boleh kosong!!');
        return;
    }
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findBarang(){
    gdng=document.getElementById('gdngId');
    gdng=gdng.options[gdng.selectedIndex].value;
    prd=document.getElementById('periodeGdng');
    prd=prd.options[prd.selectedIndex].value;
    if((gdng=='')||(prd=='')){
        alert('Gudan dan periode tidak boleh kosong!!');
        return;
    }
    nmSupplier=document.getElementById('nmSupplier').value;
    param='proses=getNmbrg'+'&nmBarang='+nmSupplier;
    param+='&gdngId='+gdng+'&periodeGdng='+prd;
    tujuan='log_slave_rekalgudang.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerSupplier').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setData(kdbrg,nmbrg){
    document.getElementById('kdBrg').value=kdbrg;
    document.getElementById('nmBrg').innerHTML=nmbrg;
    closeDialog();
}
function zPreview2(fileTarget,passParam,idCont) {
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
  // alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {

                          var res = document.getElementById(idCont);
                          res.innerHTML = con.responseText;
                      
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?proses=preview2', param, respon);
}
function reklasDt(kdBrg,gdngid,periode,rowkbrp){
    param='kdBrg='+kdBrg+'&gdngId='+gdngid;
    param+='&periodeGdng='+periode+'&proses=reklasData';
    tujuan='log_slave_rekalgudang.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else { 
                                  isidt=con.responseText.split("####");
                                  document.getElementById('sawal_'+rowkbrp).innerHTML=isidt[3];
                                  document.getElementById('qtymsk_'+rowkbrp).innerHTML=isidt[1];
                                  document.getElementById('qtyklr_'+rowkbrp).innerHTML=isidt[2];
                                  document.getElementById('salak_'+rowkbrp).innerHTML=isidt[0];
                                  document.getElementById('guaikutaja_'+rowkbrp).style.background='green';
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}