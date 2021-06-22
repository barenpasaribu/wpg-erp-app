/**
 * @author antoniuslouis
 */
// dhyaz sep 22, 2011

function getlevel0()
{
    tanggal =document.getElementById('tanggal').value;
    param='proses=preview&tanggal='+tanggal;
    tujuan='sdm_slave_2summarykaryawan.php'; 
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('printContainer0').innerHTML=con.responseText;
                    document.getElementById('printContainer1').innerHTML='';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function getlevel1(tanggal,region)
{
    param='proses=level1&tanggal='+tanggal+'&region='+region;
    tujuan='sdm_slave_2summarykaryawan.php'; 
//    alert(param);
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('printContainer1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function level1excel(ev,tujuan,tanggal,region)
{
    param='proses=excel&tanggal='+tanggal+'&region='+region;

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}
function getUnit2(){
    pro=document.getElementById('ptId2');
    prod=pro.options[pro.selectedIndex].value;
    param='proses=getUnit'+'&ptId2='+prod;
    tujuan='log_slave_2gdangAccounting2.php';
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
                               document.getElementById('unitId2').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }	
	 }  
}
function getlevel2(tanggal,region){
    param='proses=level1&prdIdDr2='+tanggal+'&region='+region;
    tujuan='sdm_slave_2summarykaryawan2.php'; 
//    alert(param);
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {                     
                    document.getElementById('printContainer5').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
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
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;
                    document.getElementById('printContainer5').innerHTML="";
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
function zExcel(ev,tujuan,passParam)
{
	judul='Report Excel';
	//alert(param);	
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
	param+='&proses=excel';
	//alert(param);
	printFile(param,tujuan,judul,ev)	
}
function level2excel(ev,tujuan,tanggal,region){
    param='proses=excel&prdIdDr2='+tanggal+'&region='+region;

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}