// JavaScript Document
function getFreeQuery()
{
    thnbudget   =document.getElementById('thnbudget');
    thnbudget   =thnbudget.options[thnbudget.selectedIndex].value;
    kodeorg     =document.getElementById('kodeorg');
    kodeorg     =kodeorg.options[kodeorg.selectedIndex].value;
    kegiatan     =document.getElementById('kegiatan');
    kegiatan     =kegiatan.options[kegiatan.selectedIndex].value;
    param='thnbudget='+thnbudget+'&kodeorg='+kodeorg+'&kegiatan='+kegiatan+'&proses=preview';
    tujuan='bgt_slave_freeQuery.php';
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

function printExcel(param,tujuan,title,ev)
{
    thnbudget   =document.getElementById('thnbudget');
    thnbudget   =thnbudget.options[thnbudget.selectedIndex].value;
    kodeorg     =document.getElementById('kodeorg');
    kodeorg     =kodeorg.options[kodeorg.selectedIndex].value;
    kegiatan     =document.getElementById('kegiatan');
    kegiatan     =kegiatan.options[kegiatan.selectedIndex].value;
    param='thnbudget='+thnbudget+'&kodeorg='+kodeorg+'&kegiatan='+kegiatan+'&proses='+param;    
   tujuan=tujuan+"?"+param;  
   width='300';
   height='200';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}