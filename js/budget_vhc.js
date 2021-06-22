// JavaScript Document

function getKdvhc(kdtrk,kdvhc)
{
        if((kdtrk=='0')||(kdvhc=='0'))
        {
            kdTraksi=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
            param='kdTraksi='+kdTraksi+'&proses=getVhc';
        }
        else
        {
            kdTraksi=kdtrk;
            kdVhc=kdvhc;
            param='kdTraksi='+kdTraksi+'&proses=getVhc'+'&kdVhc='+kdVhc;
        }
        tujuan='budget_slave_vhc.php';
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
                                  document.getElementById('kodeVhc').innerHTML='';
                                  document.getElementById('kodeVhc').innerHTML=con.responseText;
                                  if((kdtrk!='0')||(kdvhc!='0'))
                                      {
                                          saveData();
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
function saveData()
{
        thnBdget=document.getElementById('thnBudget').value;
        kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
        tipeBudget=document.getElementById('tipeBudget').value;
        param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&proses=cekSave'+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget;
        tujuan='budget_slave_vhc.php';
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
                                                        ar=con.responseText.split("###");
                                                        document.getElementById('hkEfektif').value=ar[0];
                                                        //document.getElementById('kdWorkshop').innerHTML=ar[1];
                                                        document.getElementById('thnBudget').disabled=true;
                                                        document.getElementById('kdTraksi').disabled=true;
                                                        document.getElementById('kodeVhc').disabled=true;
                                                        document.getElementById('saveData').disabled=true;
                                                        document.getElementById('formIsian').style.display='block';
                                                        document.getElementById('listDatHeader').style.display='none';
                                                        loadDataSdm(1);
                                                        //loadDataSdm()
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  

}

function jumlahkan(x)
{
        thnBdget=document.getElementById('thnBudget').value;
        kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc;
           if(x==1)
            {
                personel=document.getElementById('jmlh_'+x).value;
                hkEfektip=document.getElementById('hkEfektif').value;
                kdGol=document.getElementById('kdBudget').options[document.getElementById('kdBudget').selectedIndex].value;
                param+='&proses=getUpah'+'&jmlhPerson='+personel+'&kdGol='+kdGol+'&hkEfektif='+hkEfektip;
            }
            if(x==2)
                {
                    kdBudget=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
                    kdBrg=document.getElementById('kdBarang').value;
                    jmlhBrg=document.getElementById('jmlh_'+x).value;
                    param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&proses=getHarga';
                }
        if(x==3)
        {
            kdBudgetS=document.getElementById('kdBudgetS').options[document.getElementById('kdBudgetS').selectedIndex].value;
            kdWorkshop=document.getElementById('kdWorkshop').options[document.getElementById('kdWorkshop').selectedIndex].value;
            jmlhJam=document.getElementById('jmlh_'+x).value;
            param+='&kdBudgetS='+kdBudgetS+'&kdWorkshop='+kdWorkshop+'&jmlhJam='+jmlhJam+'&proses=getBiayaService';
			//alert(param);
        }
        tujuan='budget_slave_vhc.php';
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
                                               if(x==1)
                                               {
                                                   document.getElementById('totBiaya').value=con.responseText;
                                               }
                                               if(x==2)
                                                   {
                                                       document.getElementById('totHarga').value=con.responseText;
                                                   }
                                                    if(x==3)
                                                   {
                                                       var dtFloat=parseFloat(con.responseText);
                                                       if(isNaN(dtFloat))
                                                           {
                                                               dtFloat=0;
                                                           }
                                                       document.getElementById('totHargaJam').value=dtFloat;
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
function saveBudget(x)
{
        thnBdget=document.getElementById('thnBudget').value;
        kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        tipeBudget=document.getElementById('tipeBudget').value;
        kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
        param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget+'&kdVhc='+kdVhc;
           if(x==1)
           {
                personel=document.getElementById('jmlh_'+x).value;
                hkEfektip=document.getElementById('hkEfektif').value;
                kdGol=document.getElementById('kdBudget').options[document.getElementById('kdBudget').selectedIndex].value;
                totBiaya=document.getElementById('totBiaya').value;
                param+='&proses=saveSdm'+'&jmlhPerson='+personel+'&kdGol='+kdGol+'&hkEfektif='+hkEfektip+'&totBiaya='+totBiaya;
           }
           if(x==2)
               {
                    kdBudget=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
                    kdBrg=document.getElementById('kdBarang').value;
                    jmlhBrg=document.getElementById('jmlh_'+x).value;
                    totHarga=document.getElementById('totHarga').value;
                    satuanBrg=document.getElementById('satuan').innerHTML;
                    param+='&kdBudget='+kdBudget+'&kdBrg='+kdBrg+'&jmlhBrg='+jmlhBrg+'&totHarga='+totHarga+'&proses=saveMat'+'&satuanBrg='+satuanBrg;
               }
               if(x==3)
               {
                    kdBudgetS=document.getElementById('kdBudgetS').options[document.getElementById('kdBudgetS').selectedIndex].value;
                    kdWorkshop=document.getElementById('kdWorkshop').value;
                    jmlhJam=document.getElementById('jmlh_'+x).value;
                    totHargaJam=document.getElementById('totHargaJam').value;
                    param+='&kdBudgetS='+kdBudgetS+'&kdWorkshop='+kdWorkshop+'&jmlhJam='+jmlhJam+'&proses=saveService'+'&totHargaJam='+totHargaJam;
               }
               if(x==4)
                   {
                        kdBudgetB=document.getElementById('kdBudgetB').options[document.getElementById('kdBudgetB').selectedIndex].value;
                        noAkun=document.getElementById('noAkun').options[document.getElementById('noAkun').selectedIndex].value;
                        totBiayaB=document.getElementById('totBiayaB').value;
                        param+='&kdBudgetB='+kdBudgetB+'&noAkun='+noAkun+'&totBiayaB='+totBiayaB+'&proses=saveLain';
                   }

        tujuan='budget_slave_vhc.php';
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
                                               if(x==1)
                                               {
                                                   clearSdm();
                                                   loadDataSdm();
                                               }
                                               if(x==2)
                                               {
                                                   clearMat();
                                                   loadDtMaterail();
                                               }
                                               if(x==4)
                                               {
                                                    clearLain();
                                                    loadDtLain();
                                               }
                                               if(x==3)
                                                   {
                                                       clearService();
                                                       loadDtService();
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
function clearSdm()
{
    document.getElementById('jmlh_1').value='';
    document.getElementById('kdBudget').value='';
    document.getElementById('totBiaya').value='0';
}
function getKlmpkbrg()
{
    klmpkBrg=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
    param='klmpkBrg='+klmpkBrg+'&proses=setKdBrg';
    tujuan='budget_slave_vhc.php';
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
                                            document.getElementById('kdBarang').value=con.responseText;
                                            document.getElementById('jmlh_2').value='';
                                            document.getElementById('namaBrg').innerHTML='';
                                            // document.getElementById('kdBarang').value='';
                                            // document.getElementById('kdBudgetM').value='';
                                            document.getElementById('totHarga').value='0';
                                            document.getElementById('satuan').innerHTML='';

                                              }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
         }  
}
function clearMat()
{
    document.getElementById('jmlh_2').value='';
    document.getElementById('namaBrg').innerHTML='';
    document.getElementById('kdBarang').value='';
    document.getElementById('kdBudgetM').value='';
    document.getElementById('totHarga').value='0';
    document.getElementById('satuan').innerHTML='';
}
function clearLain()
{
    document.getElementById('kdBudgetB').value='';
    document.getElementById('noAkun').value='';
    document.getElementById('totBiayaB').value='0';
}
function clearService()
{
     document.getElementById('jmlh_3').value='';
    document.getElementById('kdWorkshop').value='';
    document.getElementById('kdBudgetS').value='';
    document.getElementById('totHargaJam').value='0';
}
function newData()
{
    document.getElementById('formIsian').style.display='none';
    document.getElementById('hkEfektif').value='';
    document.getElementById('thnBudget').disabled=false;
    document.getElementById('kdTraksi').disabled=false;
    document.getElementById('kodeVhc').disabled=false;
    document.getElementById('saveData').disabled=false;
    dataHeader();

}
function deleteSdm(id,hal)
{
        param='idData='+id+'&proses=delData';
        tujuan='budget_slave_vhc.php';
        if(confirm("Delete, are you sure?"))
            {
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
                                 if(hal==1)
                                     {
                                         loadDataSdm();
                                     }
                                     if(hal==2)
                                     {
                                         loadDtMaterail();
                                     }
                                     if(hal==4)
                                       {
                                            loadDtLain();
                                       }
                                       if(hal==3)
                                           {
                                               loadDtService();
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
function searchBrg(title,content,ev)
{
        klmpk=document.getElementById('kdBudgetM').options[document.getElementById('kdBudgetM').selectedIndex].value;
        if(klmpk=='')
            {
                alert("Budget code required");
                return;
            }
            idKlmpk="<input type='hidden' id='idKlmpk' value='"+klmpk+"' />"
            content=content+idKlmpk;
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        findBrg();
        //alert('asdasd');
}
function findBrg()
{
    klmpkBrg=document.getElementById('idKlmpk').value;
    nmBrg=document.getElementById('nmBrg').value;
    kdBarang=document.getElementById('kdBarang').value;
    param='klmpkBrg='+klmpkBrg+'&nmBrg='+nmBrg+'&proses=getBarang';
    tujuan='budget_slave_vhc.php';
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
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
         }  

}
function setData(kdbrg,namaBarang,sat)
{
    document.getElementById('kdBarang').value=kdbrg;
    document.getElementById('namaBrg').innerHTML=namaBarang;
    document.getElementById('satuan').innerHTML=sat;
    closeDialog();
}
function loadDataSdm(b)
{
    thnBdget=document.getElementById('thnBudget').value;
    kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    tipeBudget=document.getElementById('tipeBudget').value;
    kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
    param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget+'&kdVhc='+kdVhc+'&proses=loadDataSdm';
    tujuan='budget_slave_vhc.php';
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
                                              document.getElementById('containDataSDM').innerHTML=con.responseText;
                                                if(b==1)
                                                {
                                                  loadDtMaterail(b);
                                                }
                                                else
                                                    {
                                                        getThnBudget();
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

function loadDtMaterail(c)
{
    thnBdget=document.getElementById('thnBudget').value;
    kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    tipeBudget=document.getElementById('tipeBudget').value;
    kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
    param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget+'&kdVhc='+kdVhc;
    param+='&proses=loadDataMat';
    tujuan='budget_slave_vhc.php';
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

                                              document.getElementById('containDataBrg').innerHTML=con.responseText;
                                              if(c==1)
                                              {loadDtService(c);}
                                               else
                                                    {
                                                        getThnBudget();
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
function loadDtService(d)
{
    thnBdget=document.getElementById('thnBudget').value;
    kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    tipeBudget=document.getElementById('tipeBudget').value;
    kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
    param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget+'&kdVhc='+kdVhc;
    param+='&proses=loadDtService';
    tujuan='budget_slave_vhc.php';
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

                                              document.getElementById('containDataSrvc').innerHTML=con.responseText;
                                              if(d==1)
                                                  {loadDtLain();}
                                                  else
                                                    {
                                                        getThnBudget();
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
function loadDtLain()
{
    thnBdget=document.getElementById('thnBudget').value;
    kdOrg=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    tipeBudget=document.getElementById('tipeBudget').value;
    kdVhc=document.getElementById('kodeVhc').options[document.getElementById('kodeVhc').selectedIndex].value;
    param='thnBudget='+thnBdget+'&kdOrg='+kdOrg+'&kdVhc='+kdVhc+'&tipeBudget='+tipeBudget+'&kdVhc='+kdVhc;
    param+='&proses=loadDtLain';
    tujuan='budget_slave_vhc.php';
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

                                              document.getElementById('containDataLain').innerHTML=con.responseText;
                                               getThnBudget();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
         }  
}
function dataHeader()
{
    param='proses=DataHeader';
    thnbgt=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value; 
    kdVhc=document.getElementById('kdVhcHead').options[document.getElementById('kdVhcHead').selectedIndex].value;

    if(thnbgt!='')
    {
    param+='&thnBudget='+thnbgt;
    }
    if(kdVhc!='')
    {
    param+='&kdVhc='+kdVhc;
    }
    tujuan='budget_slave_vhc.php';
   // alert(param);

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
                                          // alert(con.responseText);
                                              document.getElementById('listDatHeader').style.display='block';
                                              document.getElementById('formIsian').style.display='none';
                                              document.getElementById('listDatHeader2').innerHTML=con.responseText;
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
function filFieldHead(thnbdget,kdtrk,kdvhc)
{
    document.getElementById('thnBudget').value=thnbdget;
    document.getElementById('kdTraksi').value=kdtrk;
    document.getElementById('listDatHeader').style.display='none';
    document.getElementById('formIsian').style.display='block';
    getKdvhc(kdtrk,kdvhc);

}
function closeBudget()
{
    thnBudgetTtp=document.getElementById('thnBudgetTutup').options[document.getElementById('thnBudgetTutup').selectedIndex].value;
    param='proses=closeBudget'+'&thnBudget='+thnBudgetTtp;
    tujuan='budget_slave_vhc.php';
    if(confirm("Closing budget  "+thnBudgetTtp+"are you sure ?"))
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
                                                    newData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
}
function getThnBudget()
{
    param='proses=getThnBudget';
    tujuan='budget_slave_vhc.php';
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
                                                document.getElementById('thnBudgetTutup').innerHTML=con.responseText;
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
        thnbgt=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value; 
        kdVhc=document.getElementById('kdVhcHead').options[document.getElementById('kdVhcHead').selectedIndex].value;
        param='proses=DataHeader';
        param+='&page='+num;
        if(thnbgt!='')
        {
            param+='&thnBudget='+thnbgt;
        }
        if(kdVhc!='')
        {
            param+='&kdVhc='+kdVhc;
        }
        tujuan='budget_slave_vhc.php';
        post_response_text(tujuan, param, respog);			
        function respog()
        {
                if (con.readyState == 4) 
                {
                        if (con.status == 200) 
                        {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) 
                                {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else 
                                {
                                        document.getElementById('listDatHeader').style.display='block';
                                        document.getElementById('formIsian').style.display='none';
                                        document.getElementById('listDatHeader2').innerHTML=con.responseText;
                                        //loadData();
                                }
                        }
                        else 
                        {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}