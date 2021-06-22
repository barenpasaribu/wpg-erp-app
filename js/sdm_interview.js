// JavaScript Document
function getNmLowongan()
{
	prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        param='proses=getData'+'&periodeTest='+prd;
	tujuan='sdm_slave_interview.php';
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
						  //	alert(con.responseText);
							document.getElementById('nmLowongan').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	 post_response_text(tujuan, param, respog);	
}
function prevData()
{
    
	prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
	afdI=document.getElementById('nmLowongan').options[document.getElementById('nmLowongan').selectedIndex].value;
	param='proses=loadData'+'&periodeTest='+prd+'&nmLowongan='+afdI;
        //alert(param);
	tujuan='sdm_slave_interview.php';
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
						   document.getElementById('pdfDet').style.display="none";
						  document.getElementById('sddataList').style.display='block';
						  document.getElementById('dataList').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
	 post_response_text(tujuan, param, respog);	
}
function getFormPenilaian(id){
    
        eml=document.getElementById('emailDt_'+id).innerHTML;
        nml=document.getElementById('namaDt_'+id).innerHTML;
	param='&emailDt='+eml+'&proses=getForm'+'&nmcalon='+nml;
        //alert(param);
	tujuan='sdm_slave_interview.php';
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
						  //	alert(con.responseText);
                                                   document.getElementById('pdfDet').style.display="none";
						 document.getElementById('dtForm').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	 	
}
function closeForm(){
    document.getElementById('dtForm').innerHTML="";
}
function saveView(id){
    eml=document.getElementById('emailDt').value;
    tglIntr=document.getElementById('tglInter').value;
    hsl=document.getElementById('periodeTest');
    hsl=hsl.options[hsl.selectedIndex].value;
//    intrvwr=document.getElementById('interview');
//    hslintr=intrvwr.options[intrvwr.selectedIndex].value;
//    cttn=document.getElementById('catatan').value;
//    param='emailDt='+eml+'&proses=insrData'+'&hasilIntview='+hsl;
//    param+='&cttn='+cttn+'&interviewer='+hslintr;
    kary=document.getElementById('karyId_'+id).value;
    if(kary==''){
        alert("Namakaryawan Kosong!!"+kary);
        document.getElementById('interview_'+id).checked=false;
        return;
    }
    der=document.getElementById('interview_'+id);
    if(der.checked==true){
        param='proses=insrData';
    }else{
        document.getElementById('interview_'+id).checked=false;
        param='proses=delData';
    }
    param+='&karyId='+kary+'&emailDt='+eml;
    param+='&tglInterv='+tglIntr+'&periode='+hsl;
        //alert(param);
    tujuan='sdm_slave_interview.php';
    if(confirm("Anda Yakin ??")){
        post_response_text(tujuan, param, respog);
    }
    
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
                                              //	alert(con.responseText);
                                            //prevData();
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }  	 	
}
function zPdf(fileTarget,passParam,idke,idCont) {
    document.getElementById('pdfDet').style.display="block";
    document.getElementById('dtForm').innerHTML="";
    var cont = document.getElementById(idCont);
    var passP = passParam.split('##');
	
    var param = "proses=zpdf"+'&idKebrp='+idke;
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        param += "&"+passP[i]+"="+getValue(passP[i]);
    }
    
	//alert(param);
    cont.innerHTML = "<iframe frameborder=0 style='width:100%;height:780px' src='"+fileTarget+".php?"+param+"'></iframe>";
}