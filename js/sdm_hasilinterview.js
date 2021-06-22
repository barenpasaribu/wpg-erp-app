// JavaScript Document
function getNmLowongan()
{
	prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        param='proses=getData'+'&periodeTest='+prd;
        //alert(param);
	tujuan='sdm_slave_hasilinterview.php';
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
	tujuan='sdm_slave_hasilinterview.php';
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
						   
						  document.getElementById('sddataList').style.display='block';
                                                  document.getElementById('pdfDet').style.display="none";
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
        param+='&idKe='+id;
        //alert(param);
	tujuan='sdm_slave_hasilinterview.php';
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
                                                 document.getElementById('dtForm').style.display='block';
                                                 document.getElementById('formPen').style.display='none';
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
function getFormPen(karid,id){
        eml=document.getElementById('emailDt').value;
        nml=document.getElementById('namaDt_'+id).innerHTML;
	param='&emailDt='+eml+'&proses=getForm2'+'&karyId='+karid;
        param+='&namacalon='+nml;
        //alert(param);
	tujuan='sdm_slave_hasilinterview.php';
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
						 document.getElementById('dtForm').style.display='none';
                                                 document.getElementById('formPen').style.display='block';
                                                 document.getElementById('formPen').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	 	
}
function svPenilaian(karid,id,urut,jmlrow){
        eml=document.getElementById('emailDt_'+id).innerHTML;
        nml=document.getElementById('namaDt_'+id).innerHTML;
        intrvwr2=document.getElementById('nmLowongan');
        hslintr2=intrvwr2.options[intrvwr2.selectedIndex].value;
	param='emailDt='+eml+'&proses=updateSdmTest'+'&karyId='+karid;
        param+='&idPermintaan='+hslintr2;
        
        //alert(param);
	tujuan='sdm_slave_hasilinterview.php';
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
                                                    prevData();
                                                    for(dery=0;dery<=jmlrow;dery++){
                                                    if(dery!=urut){
                                                        document.getElementById('interviewFinal_'+dery).checked=false;
                                                    }
                                                    }
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	 	
}
function saveView(){
    eml=document.getElementById('emailDt').value;
    hsl=document.getElementById('hasilIntview');
    hsl=hsl.options[hsl.selectedIndex].value;
    intrvwr=document.getElementById('interview');
    hslintr=intrvwr.options[intrvwr.selectedIndex].value;
    intrvwr2=document.getElementById('nmLowongan');
    hslintr2=intrvwr2.options[intrvwr2.selectedIndex].value;
    cttn=document.getElementById('catatan').value;
    tglintr=document.getElementById('tglinterview').value;
    param='emailDt='+eml+'&proses=insrData'+'&hasilIntview='+hsl+'&idPermintaan='+hslintr2;
    param+='&cttn='+cttn+'&interviewer='+hslintr+'&tglInterview='+tglintr;
        //alert(param);
    tujuan='sdm_slave_hasilinterview.php';
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
                                            prevData();
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