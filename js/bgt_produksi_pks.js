function bagi()
{	
        a=document.getElementById('kgtbs').value;
        d=parseFloat(a)/12;

        for(i=1;i<=12;i++)
        {
                if(isNaN(d))
                {
                d=0;
                }
                document.getElementById('brt_x'+i).value=d;
        }
}

function batal()
{
        document.getElementById('thnbudget').disabled=false;
        document.getElementById('kdpks').disabled=false;
        document.getElementById('ktbs').disabled=false;
        //document.getElementById('thnbudget').value='';
        //document.getElementById('kdpks').value='';
        document.getElementById('ktbs').value='';

        document.getElementById('saveDt').disabled=false;
        document.getElementById('kdsup').disabled=false;
        document.getElementById('kdsup').innerHTML="<option value=''></option>";	
        document.getElementById('kgtbs').value='';
        document.getElementById('oerc').value='';
        document.getElementById('oerk').value='';
        for(i=1;i<=12;i++)
        {
                document.getElementById('brt_x'+i).value='';
        }
        document.getElementById('printContainer').style.display='none';	
}

function saveBrt(totRow)
{
        strUrl = '';
        thnbudget=document.getElementById('thnbudget').options[document.getElementById('thnbudget').selectedIndex].value;
        kdpks=document.getElementById('kdpks').options[document.getElementById('kdpks').selectedIndex].value;
        //ktbs=document.getElementById('ktbs').options[document.getElementById('ktbs').selectedIndex].value;

        kdsup=document.getElementById('kdsup').options[document.getElementById('kdsup').selectedIndex].value;
        kgtbs=document.getElementById('kgtbs').value;
        oerc=document.getElementById('oerc').value;
        oerk=document.getElementById('oerk').value;

        brt_x1=document.getElementById('brt_x1').value;
        brt_x2=document.getElementById('brt_x2').value;
        brt_x3=document.getElementById('brt_x3').value;
        brt_x4=document.getElementById('brt_x4').value;
        brt_x5=document.getElementById('brt_x5').value;
        brt_x6=document.getElementById('brt_x6').value;
        brt_x7=document.getElementById('brt_x7').value;
        brt_x8=document.getElementById('brt_x8').value;
        brt_x9=document.getElementById('brt_x9').value;
        brt_x10=document.getElementById('brt_x10').value;
        brt_x11=document.getElementById('brt_x11').value;
        brt_x12=document.getElementById('brt_x12').value;

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
        //validasi untuk form 2 yang input text dan number kecuali sebaran bulan 
        if(trim(kdsup)=='')
        {
                alert('Supplier code required');
                return;
        }	
        else if(trim(kgtbs)=='')
        {
                alert('FFB');
                return;	
        }
        else if(trim(oerc)=='')
        {
                alert('OER (CPO) required');
                return;	
        }
        else if(oerc > 100)
        {
                alert('OER (CPO) maximum 100');
                return;		
        }
        else if(oerk > 100)
        {
                alert('KER (Kernel) maximum 100');
                return;		
        }


        else if(trim(oerk)=='')
        {
                alert('KER (Kernel) required');
                return;	
        }
        else if(trim(brt_x1)=='')
        {
                alert('Distribution on jan is empty');
                return;	
        }
        else if(trim(brt_x2)=='')
        {
                alert('Distribution on feb is empty');
                return;	
        }
        else if(trim(brt_x3)=='')
        {
                alert('Distribution on mar is empty');
                return;	
        }
        else if(trim(brt_x4)=='')
        {
                alert('Distribution on apr is empty');
                return;	
        }
        else if(trim(brt_x5)=='')
        {
                alert('Distribution on mei is empty');
                return;	
        }
        else if(trim(brt_x6)=='')
        {
                alert('Distribution on jum is empty');
                return;	
        }
        else if(trim(brt_x7)=='')
        {
                alert('Distribution on jul is empty');
                return;	
        }
        else if(trim(brt_x8)=='')
        {
                alert('Distribution on aug is empty');
                return;	
        }
        else if(trim(brt_x9)=='')
        {
                alert('Distribution on sep is empty');
                return;	
        }
        else if(trim(brt_x10)=='')
        {
                alert('Distribution on oct is empty');
                return;	
        }
        else if(trim(brt_x11)=='')
        {
                alert('Distribution on nov is empty');
                return;	
        }
        else if(trim(brt_x12)=='')
        {
                alert('Distribution on dec is empty');
                return;	
        }

        param='thnbudget='+thnbudget+'&kdpks='+kdpks+'&ktbs='+ktbs+'&method='+method+'&kdsup='+kdsup+'&kgtbs='+kgtbs+'&oerc='+oerc+'&oerk='+oerk+'&totRow='+totRow;
		//alert(param);
    if(strUrl!='')
    {    
        param+=strUrl;
    }
    tujuan='bgt_slave_produksi_pks.php';
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
                                        batal();
                                        loadData();	
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
        tujuan='bgt_slave_produksi_pks.php';
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
                                            ar=con.responseText.split("###");
                                                document.getElementById('lkstgs').innerHTML=ar[0];
                                                document.getElementById('thnttp').innerHTML=ar[1];

                                                  getthnbudgetHeader();

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
        tujuan='bgt_slave_produksi_pks.php';
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
        param='method=loadData'+'&thnbudgetHeader='+thnbudgetHeader;
        //alert (param);
        tujuan='bgt_slave_produksi_pks.php';
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

thn=0;	
brd=0;
function loadData()
{
        param='method=loadData';
        tujuan='bgt_slave_produksi_pks.php';
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
                                        getOrg();
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

function savehead(kdorg)
{

        thnbudget=document.getElementById('thnbudget').options[document.getElementById('thnbudget').selectedIndex].value;
        kdpks=document.getElementById('kdpks').options[document.getElementById('kdpks').selectedIndex].value;
        ktbs=document.getElementById('ktbs').options[document.getElementById('ktbs').selectedIndex].value;


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

        else if(trim(kdpks)=='')
        {
                alert('Mill code required');
                return;	
        }
        else if(ktbs=='')
        {
                alert('Supplier code required');	
                return;
        }


        param='thnbudget='+thnbudget+'&kdpks='+kdpks+'&method=cekclose'+'&ktbs='+ktbs;
        if((kdorg!='')||(kdorg!=0))
        {
            param+='&kdunit='+kdorg;
        }
        //alert (param);
        tujuan='bgt_slave_produksi_pks.php';
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
                                //alert(con.responseText);
                                if((kdorg=='')||(kdorg==0))
                                    {
                                        document.getElementById('kdsup').innerHTML='';
                                        document.getElementById('kdsup').innerHTML=con.responseText;
                                    }
                                    else
                                    {
                                        ar=con.responseText.split("###");
                                        document.getElementById('kdsup').innerHTML='';
                                        document.getElementById('kdsup').innerHTML= ar[0];
                                        for(awl=1;awl<13;awl++)
                                        {
                                            document.getElementById('brt_x'+awl).value=ar[awl];
                                        }
                                    }


                                document.getElementById('thnbudget').disabled=true;
                                document.getElementById('kdpks').disabled=true;
                                document.getElementById('ktbs').disabled=true;
                                document.getElementById('saveDt').disabled=true;
                                document.getElementById('printContainer').style.display='block';

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
function cariBast(num)
{
        param='method=loadData'+'&page='+num;
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



function Del(kunci)
{
        param='method=delete'+'&kunci='+kunci;
        tujuan='bgt_slave_produksi_pks.php';
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
                                        else 
                                        {
                                                document.getElementById('contain').innerHTML=con.responseText;
                                                loadData();	
                                                thn=1;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                  }	
        }
}

function closepks()
{

    thnttp=document.getElementById('thnttp').options[document.getElementById('thnttp').selectedIndex].value;
        lkstgs=document.getElementById('lkstgs').options[document.getElementById('lkstgs').selectedIndex].value;
        if(trim(thnttp)=='')
        {
                alert('Year required');
                return;
        }
        if(trim(lkstgs)=='')
        {
                alert('Location required');
                return;
        }
        param='thnttp='+thnttp+'&lkstgs='+lkstgs+'&method=closepks';
    tujuan='bgt_slave_produksi_pks.php';
    if(confirm("Close production budget "+lkstgs+" year "+thnttp+" ?"))
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

function fillField(thnbdget,kdpbrik,kodeunit,totalKg,oercpo,oerkrnl,intex)
{
    document.getElementById('thnbudget').options[document.getElementById('lkstgs').selectedIndex].value=thnbdget;
    document.getElementById('kdpks').value=kdpbrik;
    document.getElementById('kgtbs').value=totalKg;
    document.getElementById('oerc').value=oercpo;
    document.getElementById('oerk').value=oerkrnl;
    document.getElementById('ktbs').value=intex;
    document.getElementById('kdsup').disabled=true;
    document.getElementById('method').value='update';
    savehead(kodeunit);
}


