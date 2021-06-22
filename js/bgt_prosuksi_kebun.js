function ambil_pokok()
{
        kdblok=document.getElementById('kdblok').options[document.getElementById('kdblok').selectedIndex].value;
        thnbgt=document.getElementById('thnbudget').value;
        param='kdblok='+kdblok+'&method=pokok'+'&thnbudget='+thnbgt;
        tujuan='bgt_slave_prosuksi_kebun.php';
    post_response_text(tujuan, param, respog);
        function respog()
        {
                  if(con.readyState==4)
                  {
                                if (con.status == 200) 
                                {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else 
                                        {
                                                dta=con.responseText.split("###");
                                                if(dta[1]=='' || dta[1]==0)
                                                {
                                                        alert('FFB avg weight(BJR) for '+ dta[2] +' does not exist, Please define via menu (Anggaran->Transaksi->Kebun->BJR)');
                                                        document.getElementById('pokprod').value=dta[0];	
                                                        document.getElementById('bjr').value=0;
                                                }
                                                else 
                                                {
                                                        document.getElementById('pokprod').value=dta[0];
                                                        document.getElementById('bjr').value=dta[1];
                                                        document.getElementById('pokprod').disabled=true;
                                                        document.getElementById('bjr').disabled=true;
                                                        jumlahkan();
                                                }
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

function ubah_list()
{
        thnbudgetHeader=document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value;
        kodeblokHeader=document.getElementById('kodeblokHeader').options[document.getElementById('kodeblokHeader').selectedIndex].value;
        param='method=loadData'+'&thnbudgetHeader='+thnbudgetHeader+'&kodeblokHeader='+kodeblokHeader;
        //alert (param);
        tujuan='bgt_slave_prosuksi_kebun.php';
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


function ubah_listdrsimpan()
{
        thnsave=document.getElementById('thnbudget').value;
        param='method=loadData'+'&thnbudget='+thnsave;
        tujuan='bgt_slave_prosuksi_kebun.php';
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

statC=0;

function jumlahkan()
{	
        //isi kolom total dengan jjg/pkk/thn*BJR*Pokok Produktif
        a=document.getElementById('pokprod').value;
        //b=document.getElementById('bjr').value;
        c=document.getElementById('jjg').value;
        //d=a*b*c;
        d=parseFloat(a)*parseFloat(c);
        if(isNaN(d))
        {
                d=0;
        }
        document.getElementById('total').value=d;
        met=document.getElementById('method').value;
        if(met=='update')
        {
            statC=1
        }
        if(statC==1)
        {
            loadFormData(0,0);
        }

}

function batal()
{	

        document.getElementById('thnbudget').disabled=false;
        document.getElementById('kdblok').disabled=false;	
        document.getElementById('pokprod').disabled=true;
        document.getElementById('bjr').disabled=true;
        document.getElementById('jjg').disabled=false;
        document.getElementById('saveDt').disabled=false;

        document.getElementById('method').value='saveData';	
        document.getElementById('pokprod').value='';
        document.getElementById('bjr').value='';
        document.getElementById('jjg').value='';	
        document.getElementById('total').value='';
        document.getElementById('printContainer').innerHTML='';	
       statC=0;
}


function saveHead()
{
        thnbudget=document.getElementById('thnbudget').value;
        pokprod=document.getElementById('pokprod').value;
        bjr=document.getElementById('bjr').value;
        jjg=document.getElementById('jjg').value;
        total=document.getElementById('total').value;
        thnttp=document.getElementById('thnttp').value;

        if(trim(thnbudget)=='')
        {
                alert('Budget year required');
                return;
        }	
        else if(thnbudget.length<4) 
    {
        alert('Budget year incorrect');
        return;
    }
        else if (thnbudget==thnttp)
        {
                alert('Budget year '+thnbudget+' for '+lkstgs+' has been closed');
                return;	
        }

        else if(trim(kdblok)=='')
        {
                alert('Block code required');
                return;
        }
        else if(trim(bjr)=='')
        {
                alert('FFB avg weight for  '+ dta[2] +' does not exist, Please define via menu (Anggaran->Transaksi->Kebun->BJR)');
                return;	
        }
        else if(trim(jjg)=='')
        {
                alert('FFB/tree/year required');
                return;
        }
        param='thnbudget='+thnbudget+'&kdblok='+kdblok+'&method=cek';
        //alert(param);
    tujuan='bgt_slave_prosuksi_kebun.php';
        post_response_text(tujuan, param, respog);

        function respog()
        {
                      if(con.readyState==4)
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

                                                        document.getElementById('thnbudget').disabled=true;
                                                        //document.getElementById('kdblok2').value=kdblok;
                                                        document.getElementById('kdblok').disabled=true;	
                                                        document.getElementById('pokprod').disabled=true;
                                                        document.getElementById('bjr').disabled=true;
                                                        //document.getElementById('jjg').disabled=true;
                                                        document.getElementById('total').disabled=true;
                                                        document.getElementById('saveDt').disabled=true;

                                                        document.getElementById('printContainer').style.display='block';	
                                                        //ubah_listdrsimpan();
                                                        loadFormData(0,0);	

                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 
}


function saveBrt(totRow)
{
        strUrl = '';
        thnbudget=document.getElementById('thnbudget').value;
        kdblok=document.getElementById('kdblok').options[document.getElementById('kdblok').selectedIndex].value;
        pokprod=document.getElementById('pokprod').value;
        bjr=document.getElementById('bjr').value;
        jjg=document.getElementById('jjg').value;
        total=document.getElementById('total').value;
        method=document.getElementById('method').value;
    for(i=1;i<=totRow;i++)
    {
        try
                {
                if(strUrl != '')
            {
                strUrl += '&arrBrt['+i+']='+document.getElementById('brt_x'+i).value;
                }
                else
            {
                strUrl += '&arrBrt['+i+']='+document.getElementById('brt_x'+i).value;
            }
        }
        catch(e)
                {
                }
    }
        param='kdblok='+kdblok+'&method='+method+'&thnbudget='+thnbudget+'&total='+total+'&jjg='+jjg+'&totRow='+totRow+'&bjr='+bjr+'&pokprod='+pokprod;
// alert(param);
 if(strUrl!='')
    {    
        param+=strUrl;
    }
//	alert(param);
    tujuan='bgt_slave_prosuksi_kebun.php';
        post_response_text(tujuan, param, respog);
        function respog()
        {
                if(con.readyState==4)
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
                                        loadData();
                                        batal();
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

function getkodeblokHeader()
{
        param='method=getkodeblokHeader';	
        //alert(param);
        tujuan='bgt_slave_prosuksi_kebun.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
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
                                                document.getElementById('kodeblokHeader').innerHTML=con.responseText;

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

function getthnbudgetHeader()
{
        param='method=getthnbudgetHeader';	
        //alert(param);
        tujuan='bgt_slave_prosuksi_kebun.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
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
                                                document.getElementById('thnbudgetHeader').innerHTML=con.responseText;
                                                getkodeblokHeader();
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

function getThn()
{
        param='method=getThn';	
        //alert(param);
        tujuan='bgt_slave_prosuksi_kebun.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
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
                                                document.getElementById('thnttp').innerHTML=con.responseText;
                                                getthnbudgetHeader()
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

function getOrg()
{
        param='method=getOrg';	
        //alert(param);
        tujuan='bgt_slave_prosuksi_kebun.php';
    post_response_text(tujuan, param, respog);

        function respog()
        {
                  if(con.readyState==4)
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
                                                document.getElementById('lkstgs').innerHTML=con.responseText;
                                                getThn();
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

function loadData()
{

        param='method=loadData';
        tujuan='bgt_slave_prosuksi_kebun.php';
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
                                    statC=0;
                                    document.getElementById('contain').innerHTML=con.responseText;
                                    getOrg();
                                         // loadFormData();
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


function Del(thnbudget,kdblok)
{
        param='thnbudget='+thnbudget+'&kdblok='+kdblok+'&method=delete';
        tujuan='bgt_slave_prosuksi_kebun.php';
        if(confirm('Deteting budget for block '+ kdblok +', are you sure ?'))
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
                                        else 
                                        {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                                loadData();	
                                                batal();
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
        thnbudgetHeader=document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value;
        kodeblokHeader=document.getElementById('kodeblokHeader').options[document.getElementById('kodeblokHeader').selectedIndex].value;
        if((thnbudgetHeader!='')||(kodeblokHeader!=''))
            {
                param='method=loadData'+'&thnbudgetHeader='+thnbudgetHeader+'&kodeblokHeader='+kodeblokHeader;
            }
        else
            {
                param='method=loadData';
            }
        param+='&page='+num;
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
                                        document.getElementById('contain').innerHTML=con.responseText;
                                        batal();
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



function fillField(tahunbudget,kodeblok,pokokproduktif,bjr,jjgperpkk,kgsetahun,jjg)
{	
        document.getElementById('printContainer').style.display='block';

        document.getElementById('thnbudget').value=tahunbudget;	
        //document.getElementById('kdblok').value=kodeblok;	
        document.getElementById('pokprod').value=pokokproduktif;	
        document.getElementById('bjr').value=bjr;	
        document.getElementById('jjg').value=jjgperpkk;	
        document.getElementById('total').value=kgsetahun;	

        document.getElementById('thnbudget').disabled=true;
        document.getElementById('kdblok').disabled=true;	
        document.getElementById('pokprod').disabled=true;
        document.getElementById('bjr').disabled=true;

        document.getElementById('method').value='update';
        document.getElementById('saveDt').disabled=true;
        loadFormData(tahunbudget,kodeblok);	
        document.getElementById('saveDt').disabled=true;
}

function loadFormData(tahunbudget,kodeblok,stat)
{
        tot=document.getElementById('total').value;
        total=document.getElementById('total').value;

        if(tahunbudget==0||kodeblok==0)
        {
                kdblok=document.getElementById('kdblok').options[document.getElementById('kdblok').selectedIndex].value;
                thnbudget=document.getElementById('thnbudget').value;
        }
        else
        {
                thnbudget=tahunbudget;
                kdblok=kodeblok;
        }
        param='thnbudget='+thnbudget+'&kdblok='+kdblok+'&method=getData';

        if (total!=0 || total!='')
        {
                param+='&total='+total;	
        }
        else if (tot!=0 || tot!='')
        {
                param+='&tot='+tot;	
        }

                if(statC==1)
                {
                    param+='&statInputan='+statC;
                }


        tujuan='bgt_slave_prosuksi_kebun.php';		
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

                                        met=document.getElementById('method').value;
                                        document.getElementById('printContainer').innerHTML=con.responseText;

                                        if(met=='update')
                                        {
                                            if(statC==0)
                                            {
                                                 getKodeblok(tahunbudget,kodeblok);
                                            }	  
                                        }


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



function closeBudget()
{

    thnttp=document.getElementById('thnttp').options[document.getElementById('thnttp').selectedIndex].value;
        lkstgs=document.getElementById('lkstgs').options[document.getElementById('lkstgs').selectedIndex].value;
        if(trim(thnttp)=='')
        {
                alert('Budget year required');
                return;
        }
        if(trim(lkstgs)=='')
        {
                alert('Org code required');
                return;
        }
        param='thnttp='+thnttp+'&lkstgs='+lkstgs+'&method=closeBudget';
    tujuan='bgt_slave_prosuksi_kebun.php';
    if(confirm("Close budget for "+lkstgs+"  budget year "+thnttp+" ?? Onces closed, data can not be chage"))
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
                                        else 
                                        {
                                                loadData();
                                                batal();
                                        }
                                }
                                else 
                                {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
         }
        // alert ('Proses Tutup Selesai');  
}

function carikebun()
{
        kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].value;

        param='kebun='+kebun+'&method=carikebun';
        //alert(param);
        tujuan='bgt_slave_prosuksi_kebun.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else 
                                        {
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


function ubahNilai(persen,total,source)
{
     comp='persenPrdksi';
 tot=0;
 for(x=1;x<13;x++)
     {
          if(document.getElementById(comp+x).value=='')
             document.getElementById(comp+x).value=0; 
         tot+=parseFloat(document.getElementById(comp+x).value);
         document.getElementById(source+x).value=0;
     }
 if(tot>0){     
  for(x=1;x<13;x++)
     {
         document.getElementById(source+x).value=0;
     }    
 }
 for(x=1;x<13;x++)
     {
         if(document.getElementById(comp+x).value!='' || document.getElementById(comp+x).value!=0)
            {
               z=parseFloat(document.getElementById(comp+x).value);
              if(tot>0)
               document.getElementById(source+x).value=((z/tot)*total).toFixed(2);
            }
     }  
}
function clearForm()
{ 
    if(confirm("Clear form ?"))
    {
     for(sr=1;sr<13;sr++)
     {
         document.getElementById('brt_x'+sr).value='';
         document.getElementById('persenPrdksi'+sr).value='';
     }
    }
    else
        {
            return;
        }
}

function getKodeblok(thn,kdblk)
{
        if((thn=='0')||(kdblk=='0'))
        {
            thn=document.getElementById('thnbudget').value;
            param='thnbudget='+thn+'&method=getBlok';
        }
        else
        {
            thnbudget=thn;
            kdblok=kdblk;
            param='thnbudget='+thnbudget+'&method=getBlok'+'&kdblok='+kdblok;
        }
        tujuan='bgt_slave_prosuksi_kebun.php';
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
                                  document.getElementById('kdblok').innerHTML='';
                                  document.getElementById('kdblok').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
         }  

}			