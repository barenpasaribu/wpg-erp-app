/**
 * @author antoniuslouis
 */
  
function ambilbatch(kodeorg)
{
    param='kodeorg='+kodeorg;
    param+='&tipe=batch';
    tujuan='bibit_slave_getbatch.php';
    post_response_text(tujuan, param, respog);
	
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('kodebatch').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}