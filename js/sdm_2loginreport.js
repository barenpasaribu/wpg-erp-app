
function getUser()
{
    namauser=document.getElementById('namauser');
    namauserV=namauser.options[namauser.selectedIndex].value;
    param='namauser='+namauserV;
    tujuan='sdm_slave_2loginreport.php';
    
    if(namauserV==''){
        alert('Please choose...');
    }
    else
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//						showById('printPanel');
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