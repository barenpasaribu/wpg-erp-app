//1625
function getKary(unitdt,idkar){
        if((unitdt!='')||(idkar!='')){
            param='kodeOrg='+unitdt+'&proses=getKary';
            param+='&karyId='+idkar;
        }else{
            unit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
            param='kodeOrg='+unit+'&proses=getKary';
        }
        tujuan='sdm_slave_idpengobatan.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('karyawanId').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
    }
    
 function simpanDt(){
     kdorg=document.getElementById('kdUnit');
     kdorg=kdorg.options[kdorg.selectedIndex].value;
     karyId=document.getElementById('karyawanId');
     karyId=karyId.options[karyId.selectedIndex].value;
     medId=document.getElementById('medicalId').value;
     
      param='kodeOrg='+kdorg+'&proses=instData';
      param+='&karyId='+karyId+'&medicalId='+medId;
        tujuan='sdm_slave_idpengobatan.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        loadData(0);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
 }
 function loadData(num){
        nik=document.getElementById('nikcar').value;
        nm=document.getElementById('namacar').value;
        param='page='+num+'&proses=loadData';
        if(nik!=''){
            param+='&nikcari='+nik;
        }
        if(nm!=''){
            param+='&namakary='+nm;
        }
        tujuan='sdm_slave_idpengobatan.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                    dert=con.responseText.split("####");
                                        document.getElementById('containData').innerHTML=dert[0];
                                        document.getElementById('dtKaki').innerHTML=dert[1];
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
 }
 function addFamily(karyId){
        param='karyawanId='+karyId+'&proses=getForm';
        tujuan='sdm_slave_idpengobatan.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                    document.getElementById('showForm').style.display='block';
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
 function saveDtKlrg(nurut,karyid,id){
        medid=document.getElementById('medicalId_'+id).value;
        param='karyawanId='+karyid+'&proses=smpnData';
        param+='&nourut='+nurut+'&medicalId='+medid;
        tujuan='sdm_slave_idpengobatan.php';
        post_response_text(tujuan, param, respog);	
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                     
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
 }
 function tutupForm(){
    document.getElementById('showForm').style.display="none";
    document.getElementById('container').innerHTML="";    
 }
function fillField(unit,idmed,idKary){
        st=document.getElementById('kdUnit');
        for(x=0;x<st.length;x++)
        {
                if(st.options[x].value==unit)
                {
                        st.options[x].selected=true;
                }
        }	
        document.getElementById('medicalId').value=idmed;
        document.getElementById('karyawanId').disabled=true;
        document.getElementById('kdUnit').disabled=true;
        getKary(unit,idKary);
}
function batalDt(){
    document.getElementById('karyawanId').disabled=false;
    document.getElementById('kdUnit').disabled=false;
    document.getElementById('medicalId').value="";
    document.getElementById('kdUnit').value="";
    document.getElementById('karyawanId').innerHTML="";
    document.getElementById('karyawanId').innerHTML="<option value=''>"+fild+"</option>";
}