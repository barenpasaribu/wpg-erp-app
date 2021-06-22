/**
 * @author antoniuslouis
 */
function simpanJ()
{
        potongan=document.getElementById('potongan');
        potongan=potongan.options[potongan.selectedIndex].value;
        debet=document.getElementById('debet');
        debet=debet.options[debet.selectedIndex].value;
        kredit=document.getElementById('kredit');
        kredit=kredit.options[kredit.selectedIndex].value;        	
        met=document.getElementById('method').value;
        if(potongan=='' || debet==''  || kredit=='')
        {
                alert('Each Field are obligatory');
        }
        else
        {
                param='method='+met;
                param+='&potongan='+potongan+'&debet='+debet+'&kredit='+kredit;
                tujuan='keu_slave_save_5pengakuanPotongan.php';
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
                                                        //alert(con.responseText);
                                                        cancelJ();
                                                        loadData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function fillField(potonganA,debetA,kreditA)
{
        potongan=document.getElementById('potongan');
        for(x=0;x<potongan.length;x++)
        {
                if(potongan.options[x].value==potonganA)
                {
                        potongan.options[x].selected=true;
                }
        }
        
    debet=document.getElementById('debet');
    for(x=0;x<debet.length;x++)
    {
            if(debet.options[x].value==debetA)
            {
                    debet.options[x].selected=true;
            }
    }
    kredit=document.getElementById('kredit');
    for(x=0;x<kredit.length;x++)
    {
            if(kredit.options[x].value==kreditA)
            {
                    kredit.options[x].selected=true;
            }
    }        
    
    
    document.getElementById('method').value='update';
}

function delField(potonganA)
{
        param='method=delete';
        param+='&potongan='+potonganA;
        tujuan='keu_slave_save_5pengakuanPotongan.php';
        
        if(confirm(' Delete id '+potonganA+ '?')){
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
                                                        //alert(con.responseText);
                                                        cancelJ();
                                                        loadData();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }
}

function cancelJ()
{

        document.getElementById('potongan').options[0].selected=true;
        document.getElementById('debet').options[0].selected=true
        document.getElementById('kredit').options[0].selected=true
        document.getElementById('method').value='insert';		
}
function loadData()
{
        param='method=loadData'
        tujuan='keu_slave_save_5pengakuanPotongan.php';
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

