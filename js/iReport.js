function iExcel(ev,tujuan,passParam)
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
	param+='&method=excel';
	//alert(param);
	printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function iPreview(fileTarget,passParam,idCont) {
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
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php?method=preview', param, respon);

}

function iPdf(fileTarget,passParam,idCont) {
    var cont = document.getElementById(idCont);
    var passP = passParam.split('##');
	
    var param = "method=pdf";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        param += "&"+passP[i]+"="+getValue(passP[i]);
    }
	//alert(param);
    cont.innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
}
