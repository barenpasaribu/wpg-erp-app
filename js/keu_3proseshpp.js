/* listPosting
 * Fungsi untuk men-generate list dari transaksi yang dapat di posting
 */
function listPosting() {
    var listPost = document.getElementById('listPosting');
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode')+"&jenisdata="+getValue('jenisData');
    document.getElementById('listPosting').style.display;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    listPost.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
         post_response_text('keu_slave_3proseshpp.php', param, respon);
}

function prosesJurnalHPP(){
  var hargapemakaiantbs = document.getElementById('hargapemakaiantbs').value;
  var produksicpo = document.getElementById('produksicpo').value;
  var produksipk = document.getElementById('produksipk').value;
  var penjualancpo = document.getElementById('penjualancpo').value;
  var penjualanpk = document.getElementById('penjualanpk').value;

    param="kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode')+'&hargapemakaiantbs='+hargapemakaiantbs+'&produksicpo='+produksicpo+'&produksipk='+produksipk+'&penjualancpo='+penjualancpo+'&penjualanpk='+penjualanpk+'&method=post';
 //   alert(hargapemakaiantbs);
    tujuan='keu_slave_3jurnalhpp.php';
     if(confirm('Anda yakin melakukan proses ini?'))
          post_response_text(tujuan, param, respon);

        function respon() {
              if (con.readyState == 4) {
                  if (con.status == 200) {
                      busy_off();
                      if (!isSaveResponse(con.responseText)) {
                          alert(' Error:,\n' + con.responseText);
                      } else {
                          alert('Done');
                          document.getElementById('btnproses').disabled=true;
                      }
                  } else {
                      busy_off();
                      error_catch(con.status);
                  }
              }
          }  
}
