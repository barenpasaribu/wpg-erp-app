// JavaScript Document
function save_header()
{
        periode=document.getElementById('periode').value;
        kdOrg=document.getElementById('kdOrg').value;
        pros=document.getElementById('proses').value;
        param='periode='+periode+'&kdOrg='+kdOrg+'&proses='+pros;
        //alert(param);
        tujuan='pmn_slave_rencanajual.php';
        post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                alert("Succes, Please complete the detail in the next Tab");
                                                loadData();
                                                lockForm();
                                                document.getElementById('periodeDetail').value=periode;
                                                document.getElementById('kdOrgDetail').value=kdOrg;

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	

}
function loadData()
{
        param='proses=loadData';
        tujuan='pmn_slave_rencanajual.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                                //loadDetail();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'pmn_slave_rencanajual.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function loadDetail()
{
        periode=document.getElementById('periode').value;
        kdOrgDetail=document.getElementById('kdOrgDetail').value
        if((periode=='')&&(kdOrgDetail==''))
        {
                document.getElementById('containDetail').innerHTML='';
        }
        else if(periode!='')
        {
                param='proses=loadDetail'+'&periode='+periode+'&kdOrg='+kdOrgDetail;
                tujuan='pmn_slave_rencanajual.php';
                        function respog(){
                                if (con.readyState == 4) {
                                        if (con.status == 200) {
                                                busy_off();
                                                if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                                }
                                                else {
                                                        document.getElementById('containDetail').innerHTML=con.responseText;
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
}
function cariDetail(num)
{
                periode=document.getElementById('periode').value;
                kdOrgDetail=document.getElementById('kdOrgDetail').value
                if((periode=='')&&(kdOrgDetail==''))
                {
                document.getElementById('containDetail').innerHTML='';
                }
                else
                {
                        param='proses=loadDetail'+'&periode='+periode+'&kdOrg='+kdOrgDetail;
                }
                //param='proses=loadDetail';
                param+='&page='+num;
                tujuan = 'pmn_slave_rencanajual.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containDetail').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function lockForm()
{
        document.getElementById('periode').disabled=true;
        document.getElementById('kdOrg').disabled=true;
        document.getElementById('tmbLhead').innerHTML='';	
        document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=clear_save_form() >"+tmblDone+"</button>";	
}
function shwTmbl()
{
        document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=save_header() >"+tmblSave+"</button><button class=mybutton id=cancel_kepala name=cancel_kepala onclick=clear_save_form() >"+tmblCancel+"</button>";
}
function saveDetail()
{
        kdBrg=document.getElementById('kdBrg').value;
        periodeDetail=document.getElementById('periodeDetail').value;
        tglDetail=document.getElementById('tglDetail').value;
        jmlh=document.getElementById('jmlh').value;
        kdCustomer=document.getElementById('kdCust').value;
        lokasi=document.getElementById('lokasi').value;
        oldKdbrg=document.getElementById('oldKdbrg').value;
        oldCust=document.getElementById('oldCust').value;
        kdOrg=document.getElementById('kdOrgDetail').value;
        pros=document.getElementById('pros').value;
        param='kdBrg='+kdBrg+'&jmlh='+jmlh+'&oldKdbrg='+oldKdbrg+'&proses='+pros+'&periodeDetail='+periodeDetail+'&kdCustomer='+kdCustomer;
        param+='&lokasi='+lokasi+'&tglDetail='+tglDetail+'&kdOrg='+kdOrg;
        //alert(param);
        tujuan='pmn_slave_rencanajual.php';
        post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                clearDetail();
                                                loadDetail();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function fillField(period,kdorg)
{
        periode=period;
        kdOrg=kdorg;
        document.getElementById('periode').value=periode;
        document.getElementById('kdOrg').value=kdOrg;
        document.getElementById('periodeDetail').value=periode;
        document.getElementById('kdOrgDetail').value=kdOrg;
        document.getElementById('tmbLhead').innerHTML="<button class=mybutton id=save_kepala name=save_kepala onclick=clearForm() >"+tmblDone+"</button>";
        lockForm();
        loadDetail();
}
function fillFieldDetail(period,tgl,cust,kdbrg,lokPeng,jumlah)
{
        document.getElementById('kdBrg').value=kdbrg;
        document.getElementById('oldKdbrg').value=kdbrg;
        document.getElementById('tglDetail').value=tgl;
        document.getElementById('tglDetail').disabled=true;
        document.getElementById('kdBrg').disabled=true;
        document.getElementById('kdCust').value=cust;
        document.getElementById('oldCust').value=cust;
        document.getElementById('lokasi').value=lokPeng;
        document.getElementById('jmlh').value=jumlah;
        document.getElementById('periodeDetail').value=period;
        document.getElementById('pros').value='updateDetail';
}

function deldata(period,kdorgnsi)
{
        periode=period;
        kdOrg=kdorgnsi;
        param='periode='+periode+'&proses=delHeader'+'&kdOrg='+kdOrg;
        tujuan = 'pmn_slave_rencanajual.php';		
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                                loadData();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
                if(confirm("Delete header, are you sure..?"))
                post_response_text(tujuan, param, respog);	
}
function delDet(period,tgl,kodebrg,kodeOrg)
{
        periodeDetail=period;
        tglDetail=tgl;
        kdBrg=kodebrg;
        kdOrg=kodeOrg;
        param='periodeDetail='+periodeDetail+'&tglDetail='+tglDetail+'&proses=delDet'+'&kdBrg='+kdBrg+'&kdOrg='+kdOrg;
        tujuan = 'pmn_slave_rencanajual.php';		
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                                loadDetail();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
                if(confirm("Delete, Are you sure..?"))
                post_response_text(tujuan, param, respog);
}
function clear_save_form()
{

        document.getElementById('periode').disabled=false;
        document.getElementById('kdOrg').disabled=false;
        document.getElementById('containDetail').innerHTML='';
        document.getElementById('proses').value='insert';
        shwTmbl();
}
function clearDetail()
{
        document.getElementById('kdBrg').value='40000005';
        document.getElementById('jmlh').value='0';
        document.getElementById('tglDetail').value='';
        document.getElementById('lokasi').value='';
        document.getElementById('oldKdbrg').value='';
        document.getElementById('tglDetail').disabled=false;
        document.getElementById('kdBrg').disabled=false;
        document.getElementById('oldCust').value='';
        document.getElementById('lokasi').value='';
        document.getElementById('jmlh').value=0;
        document.getElementById('pros').value='insertDetail';
}
