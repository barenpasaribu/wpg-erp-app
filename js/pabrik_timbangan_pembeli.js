// JavaScript Document

function cancelForm()
{
        document.getElementById('kdBrg').disabled=false;
        document.getElementById('custId').disabled=false;
        document.getElementById('noKontrak').disabled=false;
        document.getElementById('dtlFormAtas').disabled=false;
        document.getElementById('formInputan').style.display='none';
        document.getElementById('formTampil').innerHTML='';
}

function getCustomer(kdbrg,kdrkn,kntrk)
{
     if((kdbrg==0)||(kdrkn==0)||(kntrk==0))
     {
     kdBrg=document.getElementById('kdBrg').options[document.getElementById('kdBrg').selectedIndex].value;
     param='proses=getCustomer'+'&kdBrg='+kdBrg;
     }
     else
     {
         l=document.getElementById('kdBrg');

        for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdbrg)
                {
                    l.options[a].selected=true;
                }
        }
         param='proses=getCustomer'+'&kdBrg='+kdbrg;
         param+='&custId='+kdrkn;
     }
     tujuan='pabrik_slave_timbangan_pembeli.php';
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
                                                        document.getElementById('custId').innerHTML=con.responseText;
                                                        if(kntrk!=0)
                                                            {
                                                               getKontrak(kdrkn,kntrk);
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
function getKontrak(kdrkn,kntrkno)
{
     if((kntrkno==0)||(kdrkn==0))
     {
     custId=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
     param='proses=getKontrak'+'&custId='+custId;
     }
     else
     {
         param='proses=getKontrak'+'&custId='+kdrkn;
         param+='&noKontrak='+kntrkno;
     }
     tujuan='pabrik_slave_timbangan_pembeli.php';
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
                                                        document.getElementById('noKontrak').innerHTML=con.responseText;
                                                        if((kntrkno!=0)||(kdrkn!=0))
                                                        {
                                                            getForm();
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
function getForm()
{
        custId=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
        kdBrg=document.getElementById('kdBrg').options[document.getElementById('kdBrg').selectedIndex].value;
        noKontrak=document.getElementById('noKontrak').options[document.getElementById('noKontrak').selectedIndex].value;

        param="proses=getForm";
        param += "&custId="+custId;
        param += "&kdBrg="+kdBrg;
        param += "&noKontrak="+noKontrak;

        tujuan='pabrik_slave_timbangan_pembeli.php';
        //alert(param);
//	return;
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
                                                        document.getElementById('kdBrg').disabled=true;
                                                        document.getElementById('custId').disabled=true;
                                                        document.getElementById('noKontrak').disabled=true;
                                                        document.getElementById('dtlFormAtas').disabled=true;
                                                        document.getElementById('formInputan').style.display='block';
                                                        document.getElementById('formTampil').innerHTML=con.responseText;

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}


function loadNData()
{
        param='proses=loadData';
        tujuan='pabrik_slave_timbangan_pembeli.php';
        //alert(tujuan);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'pabrik_slave_timbangan_pembeli.php';
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
function fillField(kodebarang,koderekanan,nokontrak) 
{
   if((koderekanan=='')||(nokontrak==''))
       {
           alert("Error: No sales contract registred for this transaction");
           return;
       }
   getCustomer(kodebarang,koderekanan,nokontrak);
}
function displayList()
{
    document.getElementById('txtsearch').value='';
    document.getElementById('tgl_cari').value='';
    document.getElementById('txtsearchKntrk').value='';
    cancelForm();
    loadNData();
}
function cariTransaksi()
{
        txtSearch=document.getElementById('txtsearch').value;
        txtTgl=document.getElementById('tgl_cari').value;
        txtKntrk=document.getElementById('txtsearchKntrk').value;

        param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariTransaksi';
        param+='&txtKntrk='+txtKntrk;
        //alert(param);
        tujuan='pabrik_slave_timbangan_pembeli.php';
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
function cariBastTransaksi(num)
{
                txtKntrk=document.getElementById('txtsearchKntrk').value;
                param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cariTransaksi';
                param+='&txtKntrk='+txtKntrk;
                param+='&page='+num;
                tujuan = 'pabrik_slave_timbangan_pembeli.php';
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
function saveAll(x)
{
    noTiket=document.getElementById('notiket_'+x).innerHTML;
    noKontrak=document.getElementById('nokontrak2').value;
    kodeVhc=document.getElementById('kendaran_'+x).innerHTML;
    brtCust=document.getElementById('brtCust_'+x).value;
    totRow=document.getElementById('jmlhRow').value;
    param='proses=updateTimAll'+'&noTiket='+noTiket+'&noKontrak='+noKontrak+'&kodeVhc='+kodeVhc+'&brtCust='+brtCust;
    tujuan='pabrik_slave_timbangan_pembeli.php';
    if(x==1 && confirm('apakah anda yakin ?'))
    post_response_text(tujuan, param, respog);
    else
    post_response_text(tujuan, param, respog);
             document.getElementById('baris_'+x).style.backgroundColor='orange';
     function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                        document.getElementById('baris_'+x).style.backgroundColor='red';
                                }
                                else {
                                       // document.getElementById('contain').innerHTML=con.responseText;
                                         b=x;
                        row=x+1;
                        x=row;
                        if(x<=totRow)
                         {   
                             document.getElementById('baris_'+b).style.backgroundColor='green';
                             document.getElementById('brtCust_'+b).disabled=true;
                             document.getElementById('simTmbl2_'+b).disabled=true;
                             saveAll(x);
                         }
                         else
                         {
                             //displayList();
                             document.getElementById('baris_'+b).style.backgroundColor='green';
                             displayList();
                            // batal();
                           // alert('Done');
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
function saveForm(notiket,kdvhc,kntrkno,x)
{
    noTiket=notiket;
    noKontrak=kntrkno;
    kodeVhc=kdvhc;
    brtCust=document.getElementById('brtCust_'+x).value;
    param='proses=updateTimAll'+'&noTiket='+noTiket+'&noKontrak='+noKontrak+'&kodeVhc='+kodeVhc+'&brtCust='+brtCust;
    tujuan='pabrik_slave_timbangan_pembeli.php';
    post_response_text(tujuan, param, respog);
    document.getElementById('baris_'+x).style.backgroundColor='orange';
     function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                        document.getElementById('baris_'+x).style.backgroundColor='red';
                                }
                                else {
                                       // document.getElementById('contain').innerHTML=con.responseText;
                             if(confirm("melanjutkan untuk input ?"))
                             {
                             b=x;
                             document.getElementById('baris_'+b).style.backgroundColor='green';
                             document.getElementById('brtCust_'+b).disabled=true;
                             document.getElementById('simTmbl2_'+b).disabled=true;
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
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function locoData(kodebarang,koderekanan,nokontrak)
{
        param="proses=updateKgTimbangan";
        param += "&custId="+koderekanan;
        param += "&kdBrg="+kodebarang;
        param += "&noKontrak="+nokontrak;

        tujuan='pabrik_slave_timbangan_pembeli.php';
        if(confirm("Penggunaan locco, apakah anda yakin ?"))
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
                                                        loadNData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}
