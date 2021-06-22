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
                    document.getElementById('excPrev').style.display="block";
                    document.getElementById('pdfData').style.display="none";
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
    post_response_text(fileTarget+'.php?proses=preview', param, respon);

}

/* Function zPdf
 * Fungsi untuk malihat report dalam bentuk pdf pada pop-up window
 * I : target file, parameter yang akan dilempar
 * O : report dalam bentuk PDF
 */
function zPdf(fileTarget,passParam,idCont) {
    var cont = document.getElementById(idCont);
    var passP = passParam.split('##');
	
    var param = "proses=pdf";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        param += "&"+passP[i]+"="+getValue(passP[i]);
    }
	//alert(param);
    document.getElementById('excPrev').style.display="none";
    document.getElementById('pdfData').style.display="block";
    cont.innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
}
function zPdfInputan(fileTarget,passParam,idCont) {
    var cont = document.getElementById(idCont);
    var passP = passParam.split('##');
	
    var param = "proses=pdf";
    for(i=0;i<passP.length;i++) {
       // var tmp = document.getElementById(passP[i]);
	   	a=i;
        param += "&"+passP[a]+"="+passP[i+1];
    }
	//alert(param);
	//return;
    cont.innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
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
        document.getElementById('excPrev').style.display="block";
        document.getElementById('pdfData').style.display="none";
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
function zDetail(ev,tujuan,passParam)
{
	var passP = passParam.split('##');
	var param = "";
	 for(i=0;i<passP.length;i++) {
       // var tmp = document.getElementById(passP[i]);
	   	a=i;
        param += "&"+passP[a]+"="+passP[i+1];
    }
	param+='&proses=getDetail';
	judul="Detail ";
	//alert(param);
	printFile(param,tujuan,judul,ev)
}