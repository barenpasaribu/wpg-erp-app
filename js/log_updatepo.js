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
                      //alert(con.responseText);
                      document.getElementById('printContainer').innerHTML=con.responseText; 
                      
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
function showDetail(ev){
        title="Get Material Code";
        content="<div id=contDetail style='overflow:auto; width:385px; height:350px;' ></div>";
        width='450';
        height='500';
        showDialog1(title,content,width,height,ev);	
}
function getKdBarang(idke,ev){
    showDetail(ev);
    param='proses=getForm'+'&rowKe='+idke;
    tujuan='log_slave_updatepo.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('contDetail').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}

function findBrg(idke){
        txt=trim(document.getElementById('no_brg').value);
        if(txt==''){
                alert('Text is obligatory');
        }
        else if(txt.length<3){
                alert('Too short words');
        }
        else{
                param='txtfind='+txt+'&proses=cariBarangDlmDtBs';
                param+='&rowKe='+idke;
                tujuan='log_slave_updatepo.php';
                post_response_text(tujuan, param, respog);
        }
        function respog(){
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
function setBrg(kdbrg,nmbrg,sat,idke){
    param='proses=satDt'+'&kdBarang='+kdbrg;//
    param+='&satuan='+sat+'&rowke='+idke;
    tujuan='log_slave_updatepo.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('kdBrgBaru_'+idke).value=kdbrg;
                                    document.getElementById('nmBrgBaru_'+idke).innerHTML=nmbrg;
                                    document.getElementById('satBaru_'+idke).innerHTML=con.responseText;
                                    closeDialog();
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
    
}
function updatePo(idke){
    kdbrg=document.getElementById('kdBrgBaru_'+idke).value;
    kdbrglm=document.getElementById('kdBrg_'+idke).innerHTML;
    nopp=document.getElementById('nopp_'+idke).innerHTML;
    nmbrg=document.getElementById('nmBrgBaru_'+idke).innerHTML;
    sat=document.getElementById('satUpdate_'+idke);
    sat=sat.options[sat.selectedIndex].value;
    np=document.getElementById('nopoUp').value;
    param+='&proses=updateDt'+'&kdBarang='+kdbrg+'&oldKdBrg='+kdbrglm;
    param+='&nopp='+nopp+'&satuanbr='+sat+'&nopo='+np;
    param+='&rowke='+idke;
    tujuan='log_slave_updatepo.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                    document.getElementById('kdBrg_'+idke).innerHTML=kdbrg;
                                    document.getElementById('nmBrg_'+idke).innerHTML=nmbrg;
                                    document.getElementById('Sat_'+idke).innerHTML=sat;
                                    
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
    
}