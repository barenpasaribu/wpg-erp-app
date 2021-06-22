//created by anthesis-team@mitraagroservindo.com

function tampilkanCatu(){
    kodeorg=document.getElementById('kodeorg').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    harga=document.getElementById('harga').value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&harga='+harga+'&aksi=display';
    tujuan='sdm_slave_pembagianCatu.php';
    if(harga=='' || harga==0)
        {
            alert('Price can not be 0');
        }
    else{    
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
                            if(trim(con.responseText)=='1')
                               alert('Provision of natura on this period has been posted')
                            else   
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
function simpanCatu(){
    kodeorg=document.getElementById('kodeorg').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    harga=document.getElementById('harga').value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&harga='+harga+'&aksi=simpan';
    tujuan='sdm_slave_pembagianCatu.php';
    if(harga=='' || harga==0)
        {
            alert('Price can not be 0');
        }
    else{    
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
                            if(trim(con.responseText)=='0')
                                {
                                    if(confirm('Provision of natura on the same period already exist, do you want to replace..?'))
                                        {
                                            replaceCatu();
                                        }
                                }
                             else if(trim(con.responseText)=='1')
                                 {
                                   alert('Provision of natura on this period has been posted');   
                                 }
                             else{                           
                                document.getElementById('container').innerHTML="Finish, Please confirm your work in Tab List";
                                updateDaftar();
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

function replaceCatu(){
    kodeorg=document.getElementById('kodeorg').value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;
    harga=document.getElementById('harga').value;
    param='kodeorg='+kodeorg+'&periode='+periode+'&harga='+harga+'&aksi=replace';
    tujuan='sdm_slave_pembagianCatu.php';
    if(harga=='' || harga==0)
        {
            alert('Price can not be 0');
        }
    else{    
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
                                document.getElementById('container').innerHTML="Finish, Please confirm your work in Tab List";
                                updateDaftar();
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }  
}

function updateDaftar()
{
 tujuan='sdm_slave_getDaftarCatu.php';
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
                            document.getElementById('containerlist').innerHTML=con.responseText;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }     
}

function getExcel(ev,tujuan,kdorg,prd)
{
    width='250';
    height='180';
    content="<iframe frameborder=0 width=100% height=280 src='"+tujuan+"?aksi=excel&kodeorg="+kdorg+"&periode="+prd+"'></iframe>"
    showDialog1('Excel ',content,width,height,ev); 
}

function postingCatu(kodeorg,periode,jumlah)
{
    param='kodeorg='+kodeorg+'&periode='+periode+'&jumlah='+jumlah+'&aksi=posting';
    tujuan='sdm_slave_pembagianCatu.php';    
    if(confirm('Posting period '+periode+' , Are you sure ..?'))
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
                                alert('Done');
                                updateDaftar();
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }      
}