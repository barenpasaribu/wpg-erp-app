//---------------------------------------- FORM 1 --------------------------------------------------------
//---------------------------------------- FORM 1 --------------------------------------------------------
//---------------------------------------- FORM 1 --------------------------------------------------------





function bagi()
{	
        a=document.getElementById('totjamthn').value;
        d=parseFloat(a)/12;
        for(i=1;i<=12;i++)
        {
                document.getElementById('jam_x'+i).value=d;
        }
}



//untuk ambil data kodews dari kodetraksi
function getws(kodeOrg,kodeWs) {

        if((kodeOrg==0)||(kodeWs==0))
        {
                kdorg=document.getElementById('kdorg').options[document.getElementById('kdorg').selectedIndex].value;
                param='kdorg='+kdorg+'&method=getws';
        }
        else
        {
                kdorg=kodeOrg;
                kodews=kodeWs;
                param='kdorg='+kdorg+'&method=getws'+'&kodews='+kodews;
        }
        tujuan='budget_slave_traksi_total_jam_bengkel.php';
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
                                    document.getElementById('kdtrak').innerHTML=con.responseText;

                                                                                if(kodeOrg!=''||kodeWs!='')
                                        {
                                                getData();    
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


function saveHead()
{
        thnbudget=document.getElementById('thnbudget').value;
/*	kdorg=document.getElementById('kdorg');
        kdorg=kdorg.options[kdorg.selectedIndex].value;
        kdtrak=document.getElementById('kdtrak');
        kdtrak=kdtrak.options[kdtrak.selectedIndex].value;*/
        totjamthn=document.getElementById('totjamthn').value;

        kdorg=document.getElementById('kdorg').options[document.getElementById('kdorg').selectedIndex].value;
        kdtrak=document.getElementById('kdtrak').options[document.getElementById('kdtrak').selectedIndex].value;

        if(trim(thnbudget)=='')
        {
                alert('Tahun masih kosong');
                return;
                //document.getElementById('thnbudget').focus();
        }	
        else if(thnbudget.length<4) 
    {
        alert('Budget yesr incorrect');
        return;
    }
        else if(trim(kdorg)=='')
        {
                alert('Working unit required');
                return;
        }
        else if(trim(kdtrak)=='')
        {
                alert('Workshop code required');
                return;
        }
        else if(trim(totjamthn)=='')
        {
                alert('Jam / Tahun masi kosong');
                return;
        }

        param='kdorg='+kdorg+'&method=cekHead'+'&thnbudget='+thnbudget+'&totjamthn='+totjamthn+'&kdtrak='+kdtrak;
    tujuan='budget_slave_traksi_total_jam_bengkel.php';
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
                                                        document.getElementById('kdorg').disabled=true;
                                                        document.getElementById('kdtrak').disabled=true;
                                                        document.getElementById('totjamthn').disabled=true;
                                                        document.getElementById('saveDt').disabled=true;
                                                        document.getElementById('printContainer').style.display='block';	
                                                        b=1;
                                                        for(a=0;a<=11;a++)
                                                        {
                                                                 document.getElementById('jam_x'+b).disabled=true;
                                                                 document.getElementById('jam_x'+b).value=con.responseText; 
                                                                 document.getElementById('jam_x'+b).disabled=false;
                                                                 b++;
                                                        }							//loadFormData(0,0);														
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}


function getData()
{

    document.getElementById('printContainer').style.display='block';
    thnbudget=document.getElementById('thnbudget').value;
    kdorg=document.getElementById('kdorg').options[document.getElementById('kdorg').selectedIndex].value;
    kdtrak=document.getElementById('kdtrak').options[document.getElementById('kdtrak').selectedIndex].value;
        //totjamthn=document.getElementById('totjamthn').value;

        param='kdorg='+kdorg+'&method=getDataEdit'+'&thnbudget='+thnbudget+'&kdtrak='+kdtrak;

    tujuan='budget_slave_traksi_total_jam_bengkel.php';
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
                                                        b=1;
                                                        ar=con.responseText.split("###");
                                                        for(a=0;a<=11;a++)
                                                        {
                                                            document.getElementById('jam_x'+b).disabled=true;
                                                             document.getElementById('jam_x'+b).value=ar[a]; 
                                                             document.getElementById('jam_x'+b).disabled=false;
                                                             b++;
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



function saveHead2()
{	
    thnbudget=document.getElementById('thnbudget').value;
    totjamthn=document.getElementById('totjamthn').value;
    kdorg=document.getElementById('kdorg').options[document.getElementById('kdorg').selectedIndex].value;
    kdtrak=document.getElementById('kdtrak').options[document.getElementById('kdtrak').selectedIndex].value;
    method=document.getElementById('method').value;

    param='kdorg='+kdorg+'&method='+insert+'&thnbudget='+thnbudget+'&totjamthn='+totjamthn+'&kdtrak='+kdtrak;
    tujuan='budget_slave_traksi_total_jam_bengkel.php';
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

function batal()
{
    document.getElementById('kdtrak').innerHTML="<option value=''>Pilih Data</option>";
    document.getElementById('totjamthn').value='';
   // document.getElementById('thnbudget').value='';
    document.getElementById('kdorg').value='';

    document.getElementById('thnbudget').disabled=false;
    document.getElementById('totjamthn').disabled=false;
    document.getElementById('kdorg').disabled=false;
    document.getElementById('kdtrak').disabled=false;
        document.getElementById('method').value='saveData';
        //document.getElementById('method').value='insert';	


        for(i=1;i<=12;i++)
        {
                document.getElementById('jam_x'+i).value='';
        }


    document.getElementById('printContainer').style.display='none';
    document.getElementById('tmblSave').innerHTML="";
    document.getElementById('tmblSave').innerHTML="<button onclick='saveHead()' class='mybutton' name='saveDt' id='saveDt'>Simpan</button>&nbsp;<button onclick='batal()' class='mybutton' name='btl' id='btl'>Batal</button>"; 

}

function saveJam(totRow)
{
        strUrl = '';
    thnbudget=document.getElementById('thnbudget').value;
    totjamthn=document.getElementById('totjamthn').value;
        kdorg=document.getElementById('kdorg').options[document.getElementById('kdorg').selectedIndex].value;
    kdtrak=document.getElementById('kdtrak').options[document.getElementById('kdtrak').selectedIndex].value;
        //document.getElementById('method').value='insert';
    method=document.getElementById('method').value;

    for(i=1;i<=totRow;i++)
    {
        try
                {
                if(strUrl != '')
            {
                strUrl += '&arrJam['+i+']='+document.getElementById('jam_x'+i).value;
                }
                else
            {
                strUrl += '&arrJam['+i+']='+document.getElementById('jam_x'+i).value;
            }
        }
        catch(e)
                {
                }
    }
        param='kdtrak='+kdtrak+'&method='+method+'&thnbudget='+thnbudget+'&totjamthn='+totjamthn+'&kdorg='+kdorg+'&totRow='+totRow;
    if(strUrl!='')
    {    
        param+=strUrl;
    }
        tujuan='budget_slave_traksi_total_jam_bengkel.php';
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

function loadData()
{
        param='method=loadData';
        tujuan='budget_slave_traksi_total_jam_bengkel.php';
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


function fillField(tahunbudget,kodetraksi,kodews,jampertahun)
{
     getws(kodetraksi,kodews);

    document.getElementById('thnbudget').disabled=true;
    document.getElementById('kdorg').disabled=true;
    document.getElementById('kdtrak').disabled=true;
        document.getElementById('totjamthn').disabled=true;
        document.getElementById('kdorg').value=kodetraksi;
    document.getElementById('thnbudget').value=tahunbudget;
    document.getElementById('totjamthn').value=jampertahun;
        document.getElementById('kdtrak').value=kodews;

        document.getElementById('saveDt').disabled=true;
        document.getElementById('tmblSave').disabled=true;
        document.getElementById('method').value='update';
}







function cariBast(num)
{
        param='method=loadData'+'&page='+num;
        //param='method=loadData';
        //param+='&page='+num;
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