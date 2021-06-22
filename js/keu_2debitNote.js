function load_unit_kpd()
{
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    param='pt='+pt+'&proses=load_unit_kpd';
    tujuan='keu_slave_2debitNote.php';
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
                    pisah=con.responseText.split('###');
                    document.getElementById('unit').innerHTML=pisah[0];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}

function load_kpd()
{
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    param='unit='+unit+'&proses=load_kpd';
    tujuan='keu_slave_2debitNote.php';
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
                    pisah=con.responseText.split('###');
                    document.getElementById('kepada').innerHTML=pisah[0];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }			
}
