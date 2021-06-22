// JavaScript Document
function add_new_data(){
    document.getElementById('headher').style.display="block";
    document.getElementById('listData').style.display="none";
    document.getElementById('detailEntry').style.display="none";
    document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlAbn onclick=saveHeader()>'+nmTmblSave+'</button><button class=mybutton id=cancelAbn onclick=cancelAbsn()>'+nmTmblCancel+'</button>';
    document.getElementById('tombol').innerHTML='';
    document.getElementById('contentDetail').innerHTML='';
    statFrm=0;
    status_inputan=0;
    bersihForm();	
    document.getElementById('proses').value="insert";
}
function cancelAbsn(){
        displayList();
}
function showDetail(kdBlok,ev){
        title=kdBlok;
        content="<fieldset><legend>"+kdBlok+"</legend>\n\
                 <div id=contDetail style='overflow:auto; width:320px; height:300px;' ></div>\n\
                  </fieldset>";
        width='350';
        height='350';
        showDialog1(title,content,width,height,ev);	
}
function getBlok(kdBlok,ev){
        showDetail(kdBlok,ev);
        kd=document.getElementById('divisiId');
        kd=kd.options[kd.selectedIndex].value;
        param='proses=getDetailPP'+'&kbnId='+kd;
        tujuan='kebun_slave_qcUlatApi.php';
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
                                                        document.getElementById('contDetail').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  

}
function displayList()
{
        document.getElementById('listData').style.display='block';
        document.getElementById('headher').style.display='none';
        document.getElementById('detailEntry').style.display='none';
        document.getElementById('kdOrgCari').value='';
        document.getElementById('tgl_cari').value='';
        loadData();
}

function findOrg(){
        txt=trim(document.getElementById('fnOrg').value);
        if(txt==''){
                alert('Text is obligatory');
        }
        else if(txt.length<3){
                alert('Text too short');
        }
        else{
                param='txtfind='+txt+'&proses=cariOrg';
                tujuan='kebun_slave_qcUlatApi.php';
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
                                                        //alert(con.responseText);
                                                        document.getElementById('hasilpencarian').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function setOrg(kdOrg,nmOrg){
        document.getElementById('kodeBlok').value=kdOrg;
        document.getElementById('nmOrg').innerHTML=nmOrg;
        closeDialog();
}
function bersihForm(){
    document.getElementById('divisiId').value="";
    document.getElementById('nmOrg').innerHTML="";
    document.getElementById('kodeBlok').value="";
    document.getElementById('tglSensus').value="";
    document.getElementById('tglPengendalian').value="";
    document.getElementById('jenisId').value="";
    document.getElementById('catatan').value="";
	
	    document.getElementById('pengawasId').value="";
    document.getElementById('pendampingId').value="";
    document.getElementById('mengetahuiId').value="";
	
   // document.getElementById('pengawasId').innerHTML=pilBlok;
   // document.getElementById('pendampingId').innerHTML=pilBlok;
   // document.getElementById('mengetahuiId').innerHTML=pilBlok;
    document.getElementById('kodeBlok').disabled=false;
    document.getElementById('divisiId').disabled=false;
    document.getElementById('tglSensus').disabled=false;
}

function findOrg2()
{
        txt=trim(document.getElementById('crOrg').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Text too short');
        }
        else
        {
                param='txtfind='+txt+'&proses=cariOrg2';
                tujuan='kebun_slave_qcUlatApi.php';
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
                                                        //alert(con.responseText);
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
function setOrg2(kdOrg,nmOrg)
{
        document.getElementById('kodeBlok').value=kdOrg;
        document.getElementById('txtsearch').value=nmOrg;
        closeDialog();
}
function getKary(){
    kd=document.getElementById('divisiId');
    kd=kd.options[kd.selectedIndex].value;
    param='proses=getKary'+'&kbnId='+kd;
    tujuan='kebun_slave_qcUlatApi.php';
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
                                                    document.getElementById('pengawasId').innerHTML=con.responseText;
                                                    document.getElementById('pendampingId').innerHTML=con.responseText;
                                                    document.getElementById('mengetahuiId').innerHTML=con.responseText;
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }  
}
status_inputan=0;
function saveHeader(){
        kd=document.getElementById('divisiId');
        kd=kd.options[kd.selectedIndex].value;
        pengId=document.getElementById('pengawasId');
        pengId=pengId.options[pengId.selectedIndex].value;
        pendId=document.getElementById('pendampingId');
        pendId=pendId.options[pendId.selectedIndex].value;
        mengId=document.getElementById('mengetahuiId');
        mengId=mengId.options[mengId.selectedIndex].value;
        jnsid=document.getElementById('jenisId');
        jnsid=jnsid.options[jnsid.selectedIndex].value;
        kdblk=document.getElementById('kodeBlok').value;
        tglSns=document.getElementById('tglSensus').value;
        tglPend=document.getElementById('tglPengendalian').value;//
        cttn=document.getElementById('catatan').value;
        prs=document.getElementById('proses').value;
       
        param='proses='+prs+'&pengawasId='+pengId;
        param+='&pendampingId='+pendId+'&mengetahuiId='+mengId;
        param+='&jenisId='+jnsid+'&kodeBlok='+kdblk+'&cattn='+cttn;
        param+='&tglSensus='+tglSns+'&tglPengendalian='+tglPend;
        tujuan='kebun_slave_qcUlatApi.php';
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
                                                    
                                                    if(status_inputan==1){
                                                        document.getElementById('dtlAbn').disabled=true;
                                                    }else{
                                                        status_inputan=1;
                                                        document.getElementById('dtlAbn').disabled=true;
                                                        addDetail(kd,tglSns,'');
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
function fillField(kdorg,tgl,stat){
    document.getElementById('listData').style.display='none';
    document.getElementById('headher').style.display='block';
    document.getElementById('detailEntry').style.display='block';
    document.getElementById('tmbLheader').innerHTML='<button class=mybutton id=dtlAbn onclick=saveHeader()>'+nmTmblSave+'</button><button class=mybutton id=cancelAbn onclick=cancelAbsn()>'+nmTmblCancel+'</button>';
    param="proses=createTable"+'&status='+stat;
    param+='&kodeblok='+kdorg+'&tanggal='+tgl;
    tujuan='kebun_slave_qcUlatApi_detail.php';
    post_response_text(tujuan, param, respon);
            function respon(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    } else {
                                            // Success Response
                            //lockForm();
                            //alert(con.responseText);
                            status_inputan=1;
                            dtisi=con.responseText.split("####");
                            l=document.getElementById('divisiId');
                            for(a=0;a<l.length;a++){
                                    if(l.options[a].value==dtisi[1])
                                        {
                                            l.options[a].selected=true;
                                        }
                            }
                            document.getElementById('proses').value='updateData';
                            document.getElementById('nmOrg').innerHTML=dtisi[3];
                            document.getElementById('kodeBlok').value=dtisi[2];
                            document.getElementById('tglSensus').value=dtisi[4];
                            document.getElementById('tglPengendalian').value=dtisi[5];
                            document.getElementById('jenisId').value=dtisi[6];
                            document.getElementById('catatan').value=dtisi[7];
                            document.getElementById('pengawasId').innerHTML=dtisi[8];
                            document.getElementById('pendampingId').innerHTML=dtisi[9];
                            document.getElementById('mengetahuiId').innerHTML=dtisi[10];
                            document.getElementById('kodeBlok').disabled=true;
                            document.getElementById('divisiId').disabled=true;
                            document.getElementById('tglSensus').disabled=true;
                            var detailDiv = document.getElementById('detailIsi');
                            detailDiv.innerHTML = dtisi[0];
                            statFrm=1;
                            loadDetail(kdorg,tgl);

                                    }
                            } else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }
}

function addDetail(){
        param="proses=createTable";
        tujuan='kebun_slave_qcUlatApi_detail.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('detailEntry').style.display='block';
                                        document.getElementById('detailIsi').innerHTML=con.responseText;
                                       
                                        lockForm();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function lockForm(){
        document.getElementById('kodeBlok').disabled=true;
        document.getElementById('tglSensus').disabled=true;
        document.getElementById('divisiId').disabled=true;
}
function svDetail(){
        kdblk=document.getElementById('kodeBlok').value;
        tglSns=document.getElementById('tglSensus').value;
        pkk=document.getElementById('pkkId').value;
        lsPengmtn=document.getElementById('luasPengamatan').value;
        drnaTrim=document.getElementById('darnaTrima').value;
        asigna=document.getElementById('Asigna').value;
        nits=document.getElementById('Nitens').value;
		 Kantong=document.getElementById('Kantong').value;
        cttn=document.getElementById('ktrangan').value;
        nurut=document.getElementById('nourut').value;
        param='proses=insertDetail'+'&kodeBlok='+kdblk+'&tanggal='+tglSns;
        param+='&pkkId='+pkk+'&luasPengamatan='+lsPengmtn+'&darnaTrima='+drnaTrim;
        param+='&Asigna='+asigna+'&Nitens='+nits+'&Kantong='+Kantong+'&ktrangan='+cttn+'&nourut='+nurut;
        tujuan='kebun_slave_qcUlatApi_detail.php';
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
                                                    loadDetail(kdblk,tglSns);
                                                    clearData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}

function editDetail(ppk, luas, jlhdar, jlhsetothosea,jlhsetoranitens,jlhsetoranitens,jlhulatkantong, ket,nurut){
        document.getElementById('pkkId').value=ppk;
        document.getElementById('luasPengamatan').value=luas;
        document.getElementById('darnaTrima').value=jlhdar;
        document.getElementById('Asigna').value=jlhsetothosea;
        document.getElementById('Nitens').value=jlhsetoranitens;
		document.getElementById('Kantong').value=jlhulatkantong;
        document.getElementById('ktrangan').value=ket;
        document.getElementById('nourut').value=nurut;
}
function clearData(){
    document.getElementById('pkkId').value=0;
    document.getElementById('luasPengamatan').value=0;
    document.getElementById('darnaTrima').value=0;
    document.getElementById('Asigna').value=0;
    document.getElementById('Nitens').value=0;
	document.getElementById('Kantong').value=0;
    document.getElementById('ktrangan').value="";
    document.getElementById('nourut').value="";
}


function bersihFormDetail()
{
        document.getElementById('krywnId').value='';
        document.getElementById('krywnId').disabled=false;
        document.getElementById('shiftId').value='';
        document.getElementById('absniId').value='';
        document.getElementById('ktrng').value='';
        document.getElementById('proses').value='insert';
        document.getElementById('jmId').value='00';
        document.getElementById('mntId').value='00';
        document.getElementById('jmId2').value='00';
        document.getElementById('premiInsentif').value='';
        document.getElementById('insentif').value='';
        document.getElementById('dendakehadiran').value='0';
}
 
function loadDetail(kdorg,tgl){
       
        param='proses=loadDetail'+'&kodeBlok='+kdorg;
        param+='&tanggal='+tgl;
        tujuan='kebun_slave_qcUlatApi_detail.php';
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
                                                        document.getElementById('contentDetail').innerHTML=con.responseText;
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
        kbn=document.getElementById('kdOrgCari');
        kbn=kbn.options[kbn.selectedIndex].value;
        tgl=document.getElementById('tgl_cari').value;
        param='proses=loadNewData'+'&tanggal='+tgl;
        param+='&page='+num+'&divisiId='+kbn;
        tujuan='kebun_slave_qcUlatApi.php';
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
                                                        //return;
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
function cariBast(num)
{
                param='proses=loadNewData';
                param+='&page='+num;
                tujuan = 'kebun_slave_qcUlatApi.php';
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

function delDetail(kdorg,tgl,norut){
        param='kodeBlok='+kdorg+'&proses=delDetail'+'&tanggal='+tgl;
        param+='&nourut='+norut;
        tujuan='kebun_slave_qcUlatApi_detail.php';
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                         loadDetail(kdorg,tgl);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
        if(confirm("Deleting, are you sure..?"))
        post_response_text(tujuan, param, respog);
}
function delData(kdorg,tgl){
        param='kodeBlok='+kdorg+'&proses=delData'+'&tanggal='+tgl;
        tujuan='kebun_slave_qcUlatApi.php';
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
        if(confirm("Deleting, are you sure..?"))
        post_response_text(tujuan, param, respog);
}