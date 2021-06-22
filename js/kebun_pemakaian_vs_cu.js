
function showAplikasi(ev,kdbrng,tgla,tglb,kdorg)
{
    param='proses=getForm'+'&kdbrng='+kdbrng+'&tgla='+tgla+'&tglb='+tglb+'&kdorg='+kdorg;
//    alert(param);
    tujuan='kebun_slave_pemakaian_vs_cu.php'+"?"+param;  
    width='800';
    height='500';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('',content,width,height,ev); 
}

function showCu(ev,kdbrng,tgla,tglb,kdorg)
{
    param='proses=getFormCu'+'&kdbrng='+kdbrng+'&tgla='+tgla+'&tglb='+tglb+'&kdorg='+kdorg;
//    alert(param);
    tujuan='kebun_slave_pemakaian_vs_cu.php'+"?"+param;  
    width='800';
    height='500';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('',content,width,height,ev); 
}
/* Function zPreview
 * Fungsi untuk preview sebuah report
 * I : target file, parameter yang akan dilempar, id container
 * O : report dalam bentuk HTML
 */
function zPrevi(fileTarget,passParam,idCont) {
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
                    var res = document.getElementById(idCont);
                    dtPeriode=con.responseText.split('###');
                    
                    res.innerHTML = dtPeriode[0];
                    document.getElementById('tgl_1').innerHTML=dtPeriode[1];
                     document.getElementById('tgl_2').innerHTML=dtPeriode[2];
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


