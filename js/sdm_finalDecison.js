// JavaScript Document
function getNmLowongan()
{
	prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        param='proses=getData'+'&periodeTest='+prd;
	tujuan='sdm_slave_finalDecison.php';
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
	tujuan='sdm_slave_finalDecison.php';
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
        der=document.getElementById('nmLowongan');
        der=der.options[der.selectedIndex].value;
	param='&emailDt='+eml+'&proses=getForm'+'&namacalon='+nml;
        param+='&idPermintaan='+der+'&idKe='+id;
        //alert(param);
	tujuan='sdm_slave_finalDecison.php';
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
function bersihData(){
    document.getElementById('dtForm').innerHTML="";
}
function saveView(){
    eml=document.getElementById('emailDt').value;
    tglspk=document.getElementById('tglSpk').value;
    jam=document.getElementById('hasilAkhir');
    hslakhir=jam.options[jam.selectedIndex].value;
    cttnakhir=document.getElementById('catatanakhir').value;
    idperm=document.getElementById('nmLowongan');
    idperm=idperm.options[idperm.selectedIndex].value;
    param='emailDt='+eml+'&proses=insrData'+'&tglSpk='+tglspk;
    param+='&hslAkhir='+hslakhir+'&cttnAkhir='+cttnakhir+'&idpermintaan='+idperm;
        //alert(param);
    tujuan='sdm_slave_finalDecison.php';
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