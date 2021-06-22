// JavaScript Document 
function addData(tGl,noTkt)
{
        if((tGl=='0')&&(noTkt=='0'))
        {
                tgl=document.getElementById('tgl').value;
                param='proses=createTable'+'&tgl='+tgl;
        }
        else
        {
                noTiket=noTkt;
                tgl=tGl;
                param='proses=createTable'+'&noTiket='+noTkt+'&tgl='+tgl;
        }
        //alert(param);
        tujuan='pabrik_slave_sortasi.php';
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
                                                        //document.getElementById('noTiket').innerHTML=con.responseText;
                                document.getElementById('tgl').disabled=true;
                                document.getElementById('tanggalForm').innerHTML=tgl;
                                document.getElementById('tmblPilih').innerHTML='<button class="mybutton" id="cancelAbn" onclick="cancelForm()" >'+canForm+'</button>';
                                //document.getElementById('formInput').style.display='block';
                                document.getElementById('listData').style.display='none';
                                document.getElementById('formDetail').innerHTML=con.responseText;
                                //document.getElementById('noTiket').disabled=true;
                                //document.getElementById('cancelAbn').disabled=false;
                                document.getElementById('showFormBwh').style.display="block";
                                if(a==0)
                                    {
                                        loadDataDetail()
                                    }
                                                       // getForm(noTkt);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
function add_new_data()
{
        document.getElementById('headher').style.display='block';
        document.getElementById('listData').style.display='none';
        document.getElementById('formInput').style.display='none';
        document.getElementById('showFormBwh').style.display="none";
        document.getElementById('formDetail').innerHTML='';
        bersih();
}
function displayList()
{
        document.getElementById('headher').style.display='none';
        document.getElementById('listData').style.display='block';
        document.getElementById('noTiketcr').value='';
        //document.getElementById('noTiketcr').value='';
        loadData();
}
function bersih()
{
        document.getElementById('tgl').value='';
        document.getElementById('tgl').disabled=false;
        document.getElementById('tmblPilih').innerHTML="<button class=mybutton id=dtlAbn onclick=addData('0','0')>"+tmblPilih+"</button>";
        document.getElementById('proses').value='insert';
}
function cancelSave()
{
        bersih();
        displayList();
}
function loadData()
{
        param='proses=LoadData';
        tujuan='pabrik_slave_sortasi.php';
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
                param='proses=LoadData';
                param+='&page='+num;
                tujuan = 'pabrik_slave_sortasi.php';
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
function saveData()
{
        noTiket=document.getElementById('noTiket').options[document.getElementById('noTiket').selectedIndex].value;
        kdFraksi=document.getElementById('kdFraksi').options[document.getElementById('kdFraksi').selectedIndex].value;
        jmlh=document.getElementById('jmlh').value;
        pros=document.getElementById('proses').value;
        param='noTiket='+noTiket+'&kdFraksi='+kdFraksi+'&jmlh='+jmlh+'&proses='+pros
        //alert(param);
        tujuan='pabrik_slave_sortasi.php';
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
                                                        if(confirm("Lanjut input ?"))
                                                        {
                                                                document.getElementById('noTiket').disabled=false;
                                                                document.getElementById('kdFraksi').disabled=false
                                                                //document.getElementById('noTiket').value='';
                                                                document.getElementById('kdFraksi').value='';
                                                                document.getElementById('jmlh').value='';
                                                                document.getElementById('proses').value="insert";								
                                                                //addData('0','0');
                                                        }
                                                        else
                                                        {
                                                                displayList();
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
function fillField(id)
{
        ar=id.split("##");
        noTiket=ar[1];
        kdFraksi=ar[0];
        param='noTiket='+noTiket+'&kdFraksi='+kdFraksi+'&proses=getData';
        tujuan='pabrik_slave_sortasi.php';
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

                                                        ar=con.responseText.split("###");
                                                        document.getElementById('tmblPilih').innerHTML='';
                                                        document.getElementById('formInput').style.display='block';
                                                        document.getElementById('headher').style.display='block';
                                                        document.getElementById('listData').style.display='none';
                                                        document.getElementById('tgl').value=ar[3];
                                                        document.getElementById('tgl').disabled=true;
                                                        document.getElementById('kdFraksi').value=ar[1];
                                                        document.getElementById('noTiket').disabled=true;
                                                        document.getElementById('kdFraksi').disabled=true;
                                                        document.getElementById('jmlh').value=ar[2];
                                                        document.getElementById('proses').value='update';
                                                        addData(ar[3],ar[0]);

                                                        }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  


        }
function deldata(notiket,kdfraksi)
{
        noTiket=notiket;
        kdFraksi=kdfraksi;
        param='noTiket='+noTiket+'&kdFraksi='+kdFraksi+'&proses=delData';
        //alert(param);
        tujuan='pabrik_slave_sortasi.php';
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
                                                        displayList();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         if(confirm("bersihkan grading untuk : "+noTiket+", Apakah anda yakin ?"))
                post_response_text(tujuan, param, respog);
}
function delDet(notiket,kdfraksi)
{
        noTiket=notiket;
        kdFraksi=kdfraksi;
        param='noTiket='+noTiket+'&kdFraksi='+kdFraksi+'&proses=delData';
        //alert(param);
        tujuan='pabrik_slave_sortasi.php';
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
                                                        loadDataDetail();

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         if(confirm("bersihkan grading untuk : "+noTiket+", apakah anda yakin ?"))
                post_response_text(tujuan, param, respog);
}
function printPDF(kdorg,tgl,ev) {
    // Prep Param
        kdORg=kdorg;
        daTtgl=tgl;
        param='kdOrg='+kdORg+'&daTtgl='+daTtgl;
    param += "&proses=pdf";

    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_curahHujanPdf.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function cariTiket()
{
        document.getElementById('headher').style.display='none';
        document.getElementById('listData').style.display='block';
        noTiket=document.getElementById('noTiketcr').value;
        param='noTiket='+noTiket+'&proses=cariData';
        //alert(param);
        tujuan='pabrik_slave_sortasi.php';
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
function cariData(num)
{
                noTiket=document.getElementById('noTiketcr').value;
                param='noTiket='+noTiket+'&proses=cariData';
                param+='&page='+num;
                tujuan = 'pabrik_slave_sortasi.php';
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

a=0;
function getForm()
{
    //notkt=document.getElementById('noTiket').options[document.getElementById('noTiket').selectedIndex].value;
    tngl=document.getElementById('tgl').value;
    document.getElementById('tanggalForm').innerHTML=tngl;
    param='proses=createTable'+'&noTiket='+notkt;
    tujuan = 'pabrik_slave_sortasi.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                               // alert(con.responseText);

                                document.getElementById('formDetail').innerHTML=con.responseText;
                                //document.getElementById('noTiket').disabled=true;
                                //document.getElementById('cancelAbn').disabled=false;
                                document.getElementById('showFormBwh').style.display="block";
                                if(a==0)
                                    {
                                        loadDataDetail()
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
function cancelForm()
{
    document.getElementById('formDetail').innerHTML='';
    //document.getElementById('noTiket').value='';
    //document.getElementById('noTiket').disabled=false;
    //document.getElementById('cancelAbn').disabled=true;
    document.getElementById('showFormBwh').style.display="none";
    displayList();

}
function loadDataDetail()
{
    //a=1;
    tngl=document.getElementById('tgl').value;
    param='proses=loadDataDetail'+'&tgl='+tngl;
    tujuan = 'pabrik_slave_sortasi.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                               // alert(con.responseText);

                                document.getElementById('isiDetail').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }
}
function addDetail(brs)
{
    baris=brs;
    row=baris;
    strUrl = '';
    for(i=1;i<=row;i++)
    {
    try{
        if(strUrl != '')
        {
            Fraksi=document.getElementById('fraksi_'+i).getAttribute('value');
            strUrl += '&isiData['+Fraksi+']='+encodeURIComponent(trim(document.getElementById('inputan_'+i).value))
                   +'&kdFraksi[]='+Fraksi;
        }
        else
        {
            Fraksi=document.getElementById('fraksi_'+i).getAttribute('value');
            strUrl += '&isiData['+Fraksi+']='+encodeURIComponent(trim(document.getElementById('inputan_'+i).value))
                   +'&kdFraksi[]='+Fraksi;
        }
    }
    catch(e){}
    }
    noTkt=document.getElementById('noTkt').value;
    jmlh=document.getElementById('jmlhJJg').value;
    //prsn=document.getElementById('persenBrnd').value
    kgPtngan=document.getElementById('kgPtngan').value
    pros=document.getElementById('proses').value;
    param="proses="+pros+"&noTiket="+noTkt+"&jmlhJJg="+jmlh+'&kgPtngan='+kgPtngan;
    param+=strUrl;
    fileTarget='pabrik_slave_sortasi.php';
   // alert(param);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    loadDataDetail();
                    bersihForm();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);

    post_response_text(fileTarget, param, respon);
}
function bersihForm()
{
    row=document.getElementById('jmlhBaris').value;
//    for(i=1;i<=row;i++)
//    {
//        document.getElementById('inputan_'+i).value='';
//    }
    for(d=1;d<=row;d++)
    {
        document.getElementById('inputan_'+d).value='0';
    }

    document.getElementById('jmlhJJg').value=0;
    //document.getElementById('persenBrnd').value=0;
    document.getElementById('kgPtngan').value=0;
    document.getElementById('noTkt').disabled=false;
    document.getElementById('noTkt').value='';
    document.getElementById('proses').value='insert';
}
function editDet(nTk,tanggal)
{
    notkt=nTk;
    tngl=tanggal;
    param='noTiket='+notkt+'&proses=EditData'+'&tgl='+tngl;
    fileTarget='pabrik_slave_sortasi.php';
    document.getElementById('formDetail').innerHTML='';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                   // alert(con.responseText);
                   document.getElementById('formDetail').innerHTML=con.responseText;
                   document.getElementById('proses').value='update';
                   document.getElementById('noTkt').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);

    post_response_text(fileTarget, param, respon);
}
function editDetHead(nTk,tanggal)
{
    notkt=nTk;
    tngl=tanggal;
    param='noTiket='+notkt+'&proses=EditData'+'&tgl='+tngl;
    fileTarget='pabrik_slave_sortasi.php';
    document.getElementById('formDetail').innerHTML='';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                   // alert(con.responseText);
                   document.getElementById('formDetail').innerHTML=con.responseText;
                   document.getElementById('proses').value='update';
                   document.getElementById('tgl').value=tanggal;
                   document.getElementById('noTkt').disabled=true;
                   document.getElementById('tgl').disabled=true;
                   document.getElementById('tanggalForm').innerHTML=tanggal;
                   document.getElementById('tmblPilih').innerHTML='<button class="mybutton" id="cancelAbn" onclick="cancelForm()" >'+canForm+'</button>';
                   document.getElementById('formInput').style.display='block';
                   document.getElementById('listData').style.display='none';
                   document.getElementById('formDetail').innerHTML=con.responseText;
                   //document.getElementById('isiDetail').innerHTML='';
                    //document.getElementById('noTiket').disabled=true;
                    //document.getElementById('cancelAbn').disabled=false;
                    document.getElementById('headher').style.display='block';
                   document.getElementById('showFormBwh').style.display="block";
                   loadDataDetail();

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);

    post_response_text(fileTarget, param, respon);
}


function getNetto(noticket)
{
    param='noticket='+noticket+'&proses=getNetto';
    fileTarget='pabrik_slave_sortasi.php';
    post_response_text(fileTarget, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   var isi = con.responseText.split("####");
                   //document.getElementById('nettox').innerHTML=con.responseText;
                   document.getElementById('nettox').innerHTML = isi[0];
                   //document.getElementById('bjrx').innerHTML='';
                   document.getElementById('bjrx').innerHTML = isi[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }   
}

function hitungPotongan(val, kodefraksi, field) {
    if (document.getElementById('bjrx').innerHTML == '' || document.getElementById('bjrx').innerHTML == 0)
        alert(' Please insert bunch');
    else {
        bjr = document.getElementById('bjrx').innerHTML;
        bjr = parseFloat(bjr);
        totpot = 0;
        if (kodefraksi == 'BRD') {

        }
        else{
        tmv = document.getElementById('pot' + kodefraksi).innerHTML;
        tmv = parseFloat(tmv);
        //alert(tmv);
        }
        for (x = 1; x <= field; x++) {
            if (kodefraksi == 'BRD') {
           
            }
            else {
                tm = document.getElementById('fraksi_' + x).getAttribute('value');
                tm1 = document.getElementById('inputan_' + x).value;
                //tmf = parseFloat(tm1);
                totpot += bjr * tm1 * tmv;
            }
        }
        //        brd=document.getElementById('potBRD').innerHTML;
        //        brd=parseFloat(brd);
        nettox = document.getElementById('nettox').innerHTML;
        nettox = parseFloat(nettox);
    }
    document.getElementById('kgPtngan').value = totpot.toFixed(2);
}

function hitungBJR(d, val) {
    nettox = document.getElementById('nettox').innerHTML;
    nettox = parseFloat(nettox);
    document.getElementById('bjrx').innerHTML = (nettox / d).toFixed(2).toString();
    hitungPotongan(0, 'BRD', val);
}
