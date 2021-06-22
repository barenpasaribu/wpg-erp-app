// JavaScript Document
function getData()
{
	prd=document.getElementById('deptId').options[document.getElementById('deptId').selectedIndex].value;
       
        param='proses=getData'+'&deptId='+prd;
        
	tujuan='sdm_slave_pemanggilantest.php';
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
        clsPdf()
	prd=document.getElementById('deptId').options[document.getElementById('deptId').selectedIndex].value;
	afdI=document.getElementById('nmLowongan').options[document.getElementById('nmLowongan').selectedIndex].value;
	periode=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        nmjbtn=document.getElementById('deptId').options[document.getElementById('deptId').selectedIndex].text;
        kondId=document.getElementById('kondId').options[document.getElementById('kondId').selectedIndex].value;
        umr=document.getElementById('umrNa').value;
        if(kondId!=''){
            if(umr==''){
                alert("Umur Tidak Boleh Kosong");
                return;
            }
        }
	param='proses=loadData'+'&periode='+periode+'&nmLowongan='+afdI;
	param+='&deptId='+prd+'&nbJbtn='+nmjbtn+'&kondId='+kondId+'&umrNa='+umr;
        //alert(param);
	tujuan='sdm_slave_pemanggilantest.php';
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
function ricekDt(id){
    
        eml=document.getElementById('emailDt_'+id).innerHTML;
        notrans=document.getElementById('nopermintaan').value;
        prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        chk=document.getElementById('pildt_'+id);
        if(chk.checked==true){
            param='proses=insrData';
        }else{
            param='proses=delData';
        }
	param+='&emailDt='+eml+'&nopermintaan='+notrans+'&periodetest='+prd;
        //alert(param);
	tujuan='sdm_slave_pemanggilantest.php';
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
function previewCv(fileTarget,passParam,idCont) {
    document.getElementById('tombolData').style.display='block';
    document.getElementById('printpdf').style.display='block';
    var cont = document.getElementById(idCont);
    var param = "proses=cvData&emailDt="+passParam;
    cont.innerHTML = "<iframe frameborder=0 style='width:790px;height:1020px' src='"+fileTarget+".php?"+param+"'></iframe>";
}
function clsPdf(){
    document.getElementById('printpdf').style.display='none';
    document.getElementById('tombolData').style.display='none';
    document.getElementById('printpdf').innerHTML='';
}
function tolakDt(id){
        eml=document.getElementById('emailDt_'+id).innerHTML;
        notrans=document.getElementById('nopermintaan').value;
        prd=document.getElementById('periodeTest').options[document.getElementById('periodeTest').selectedIndex].value;
        chk=document.getElementById('pildt_'+id);
        param='proses=insrData2';
	param+='&emailDt='+eml+'&nopermintaan='+notrans+'&periodetest='+prd;
        //alert(param);
	tujuan='sdm_slave_pemanggilantest.php';
        if(confirm("Anda Yakin Ingin Menolak!!")){
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