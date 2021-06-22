function getNoakun(){
    kdorg=document.getElementById('kodeorg');
    kdorg=kdorg.options[kdorg.selectedIndex].value;
    param='proses=getNoakun&kodeorg='+kdorg;
    tujuan='keu_slave_2kasHarianv2.php';
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
                          document.getElementById('noakun').innerHTML=con.responseText;
                          document.getElementById('noakunsmp').innerHTML=con.responseText;
                      }
                    }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }	
    }       
}
