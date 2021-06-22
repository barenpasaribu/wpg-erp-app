/**
 * @author antoniuslouis
 */
// dhyaz sep 22, 2011

function simpan()
{
    tahunbudget =document.getElementById('tahunbudget');
    departemen =document.getElementById('departemen');
    noakun =document.getElementById('noakun');
    keterangan =document.getElementById('keterangan');
    alokasi =document.getElementById('alokasi');
    jumlahbiaya =document.getElementById('jumlahbiaya');
    tahunbudgetV =tahunbudget.value;
    departemenV =departemen.options[departemen.selectedIndex].value;
    noakunV =noakun.options[noakun.selectedIndex].value;
    keteranganV =keterangan.value;
    alokasiV =alokasi.options[alokasi.selectedIndex].value;
    jumlahbiayaV	=jumlahbiaya.value;

    fisik   =document.getElementById('fisik').value;
    satuanf =document.getElementById('satuanf').value;

    if(tahunbudgetV==''){
        alert('Budget year is empty.');
        return;
    }
    if(departemenV==''){
        alert('Department is empty.');
        return;
    }
    if(noakunV==''){
        alert('Account No. is empty.');
        return;
    }
    if(keteranganV==''){
        alert('Description is empty.');
        return;
    }
    if(alokasiV==''){
        alert('Allocation is empty.');
        return;
    }
    if((jumlahbiayaV=='')||(parseFloat(jumlahbiayaV)==0)){
        alert('Amount is empty.');
        return;
    }
    param='cekapa=saveatas&tahunbudget='+tahunbudgetV+'&departemen='+departemenV+'&noakun='+noakunV;
    param+='&keterangan='+keteranganV+'&alokasi='+alokasiV+'&jumlahbiaya='+jumlahbiayaV;
    param+='&fisik='+fisik+'&satuanf='+satuanf;
    tujuan='bgt_slave_departemen.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        updateTab();    
//                        document.getElementById('kodebudget0').value='';
                        document.getElementById('noakun').value='';
                        document.getElementById('keterangan').value='';
                        document.getElementById('alokasi').value='';
                        document.getElementById('jumlahbiaya').value='';
                        alert('Done');
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function updateTab()
{
    pilihtahun0 =document.getElementById('pilihtahun0');
    pilihtahun0V	=pilihtahun0.options[pilihtahun0.selectedIndex].value;
    param='cekapa=tab&pilihtahun0='+pilihtahun0V;
    tujuan='bgt_slave_departemen.php'; 
//    alert(param);
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//                    alert(con.responseText);
                    document.getElementById('container0').innerHTML=con.responseText;
//                    if(apa=='all')updateTab1('all');
//                    updateTabs();
                    updateTahun(pilihtahun0V);
                    getThnBudget();
//                    alert(pilihtahun0V);
//                    document.getElementById('pilihtahun0').value=pilihtahun0V;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		
}

function deleteRow(kunci)
{
    {
        param='cekapa=delete&kunci='+kunci;
        tujuan='bgt_slave_departemen.php';
        if(confirm('Delete?'))post_response_text(tujuan, param, respog);		
    }

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
                    updateTab();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }		
}

function sebaran(kunci,ev)
{
   param='cekapa=sebaran&kunci='+kunci;
   tujuan='bgt_slave_departemen.php'+"?"+param;  
   width='300';
   height='350';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Sebaran '+kunci,content,width,height,ev); 

}

function simpansebaran(kunci,total,ev)
{
    jan4 =document.getElementById('rupiah1');
    jan4V =parseFloat(jan4.value);
    feb4 =document.getElementById('rupiah2');
    feb4V =parseFloat(feb4.value);
    mar4 =document.getElementById('rupiah3');
    mar4V =parseFloat(mar4.value);
    apr4 =document.getElementById('rupiah4');
    apr4V =parseFloat(apr4.value);
    may4 =document.getElementById('rupiah5');
    may4V =parseFloat(may4.value);
    jun4 =document.getElementById('rupiah6');
    jun4V =parseFloat(jun4.value);
    jul4 =document.getElementById('rupiah7');
    jul4V =parseFloat(jul4.value);
    aug4 =document.getElementById('rupiah8');
    aug4V =parseFloat(aug4.value);
    sep4 =document.getElementById('rupiah9');
    sep4V =parseFloat(sep4.value);
    oct4 =document.getElementById('rupiah10');
    oct4V =parseFloat(oct4.value);
    nov4 =document.getElementById('rupiah11');
    nov4V =parseFloat(nov4.value);
    dec4 =document.getElementById('rupiah12');
    dec4V =parseFloat(dec4.value);
    total4V =total;

    totalan4V=jan4V+feb4V+mar4V+apr4V+may4V+jun4V+jul4V+aug4V+sep4V+oct4V+nov4V+dec4V;
//        alert(totalan4V);
      gf=parseInt(total4V);
      gt=parseInt(totalan4V);
    if(gt>gf){
        alert('Distribution lager than total a year. '+totalan4V+' > '+total4V);
        return;
    }

    param='cekapa=simpansebaran&kunci='+kunci+'&d01='+jan4V+'&d02='+feb4V+'&d03='+mar4V+'&d04='+apr4V+'&d05='+may4V+'&d06='+jun4V+'&d07='+jul4V+'&d08='+aug4V+'&d09='+sep4V+'&d10='+oct4V+'&d11='+nov4V+'&d12='+dec4V;
    tujuan='bgt_slave_departemen.php';
//    alert(param);
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
//                        document.getElementById('kodebudget0').value='';
//                        document.getElementById('jumlahpertahun0').value='';
                        parent.updateTab();    
                        parent.closeDialog();
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function angka_doangsamaminus(e)//only numeric e is event
{
    key=getKey(e);
//    if((key<48 || key>57) && (key!=8 && key != 45 && key != 150 && key!=46  && key!=127 && key!=true)) // 45 hypen
    if((key<48 || key>57) && (key!=8 && key != 150 && key!=46  && key!=127 && key!=true))
        return false;
    else
    {
        return true;
    }
}

function updateTahun(tahun)
{
    hidden0 =document.getElementById('hidden0');
    hidden0V =hidden0.value;
    param='cekapa=updatetahun';
    tujuan='bgt_slave_departemen.php';
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
//                        alert('Done');
                    }else{

                            document.getElementById('pilihtahun0').innerHTML=con.responseText;
                            document.getElementById('pilihtahun0').value=tahun;                            
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function closeBudget()
{
    thnBudgetTtp=document.getElementById('thnBudgetTutup').options[document.getElementById('thnBudgetTutup').selectedIndex].value;
    param='cekapa=closeBudget'+'&tahunbudget='+thnBudgetTtp;
    tujuan='bgt_slave_departemen.php';
    if(confirm("Close budget "+thnBudgetTtp+", are you sure ?"))
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

                                                     updateTab();  
                                                     alert("done");
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
    param='cekapa=getThnBudget';
    tujuan='bgt_slave_departemen.php';
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

function ubahNilai(total)
{
 tot=0;
 for(x=1;x<13;x++)
     {
          if(document.getElementById('persen'+x).value=='')
             document.getElementById('persen'+x).value=0; 
        tot+=parseFloat(document.getElementById('persen'+x).value);
     }
   if(tot>0){    
   for(x=1;x<13;x++)
     {
         document.getElementById('rupiah'+x).value=0;
     }
    }
 for(x=1;x<13;x++)
     {
         if(document.getElementById('persen'+x).value!='' || document.getElementById('persen'+x).value!=0)
            {
               z=parseFloat(document.getElementById('persen'+x).value);
               if(tot>0)
               document.getElementById('rupiah'+x).value=((z/tot)*total).toFixed(2);
            }
     }  
}