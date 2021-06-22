/*@anthesis-team@mitraagroservindo.com
 * 
 */
function hitungTotal(max)
{
    var z=0;
    for(x=1;x<=max;x++){
        g=document.getElementById('jumlah'+x).value;
        if(g=='')
           { g=0;}
            z+=parseFloat(g);
    }

 document.getElementById('total').value=z;   
}
function alokasiKan(row)
{
    tujuan='keu_slave_3alokasiByRo.php';
    
    pt=document.getElementById('pt'+row).options[0].value;
    jumlah=document.getElementById('jumlah'+row).value;
    periode=document.getElementById('periode').value;
    kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
    if(jumlah=='0' || jumlah==''){
        alert('Jumlah salah');
    }
    else if (confirm('Anda yakin..?')){
    param='periode='+periode+'&pt='+pt+'&jumlah='+jumlah+'&kodeorg='+kodeorg;
    post_response_text(tujuan, param, respog);
    }
            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            alert('Done');
                                            document.getElementById('pt'+row).disabled=true;
                                            document.getElementById('jumlah'+row).disabled=true;
                                             document.getElementById('button'+row).disabled=true;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }
    
}