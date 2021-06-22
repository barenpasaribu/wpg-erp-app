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
//                    var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
                        document.getElementById('reportcontainer').style.display='block';
                        document.getElementById('reportcontainer').innerHTML=con.responseText;
                        document.getElementById('lyrSatu').style.display='none';
                        document.getElementById('lyrDua').style.display='none';
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

function zExcel(ev,tujuan,passParam,pros)
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
	param+='&proses='+pros;
       
	printFile(param,tujuan,judul,ev)	
}
function zExcl(ev,tujuan,sbb,ssb2,periode,pros){
    judul='Report Excel';
    param='subbagian='+ssb2+'&periode='+periode;
    param+='&proses='+pros+'&noakun='+sbb;
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
function lempar(dest,title){
    	param='judul='+title;
	tujuan=dest+'.php';
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
                        document.getElementById('formcontainer').innerHTML=con.responseText;
                        document.getElementById('reportcontainer').innerHTML='';
                        document.getElementById('isiJdlBawah').innerHTML=title;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }        
}
function ubah(obj)
{
    if(obj.style.backgroundColor=='darkgreen'){
      obj.style.backgroundColor='#FFFFFF';
      obj.style.color='#000000';
      obj.style.fontWeight='normal';
    }
    else{
       obj.style.backgroundColor='darkgreen'; 
       obj.style.color='#FFFFFF';
       obj.style.fontWeight='bolder';
    }
}
function getDetail(sbb,periode,dest){
    param='subbagian='+sbb+'&periode='+periode;
    param+='&proses=getDetail';
    tujuan=dest+'.php';
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
                            
                            document.getElementById('reportcontainer').style.display='none';
                            document.getElementById('lyrSatu').innerHTML=con.responseText;
                            document.getElementById('lyrSatu').style.display='block';
                            document.getElementById('lyrDua').style.display='none';
                            
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }	
     }     
}
function getBack1(){
    document.getElementById('reportcontainer').style.display='block';
    document.getElementById('lyrSatu').style.display='none';
    document.getElementById('lyrDua').style.display='none';
}
function getDetail2(sbb,periode,lstnoakun,dest){
    param='subbagian='+sbb+'&periode='+periode;
    param+='&proses=getDetail2'+'&noakun='+lstnoakun;
    tujuan=dest+'.php';
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
                            
                            document.getElementById('reportcontainer').style.display='none';
                            document.getElementById('lyrDua').innerHTML=con.responseText;
                            document.getElementById('lyrSatu').style.display='none';
                            document.getElementById('lyrDua').style.display='block';
                            
                    }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }	
     }     
}
function getBack2(){
    document.getElementById('reportcontainer').style.display='none';
    document.getElementById('lyrSatu').style.display='block';
    document.getElementById('lyrDua').style.display='none';
}