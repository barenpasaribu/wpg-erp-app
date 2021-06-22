/* tutupBuku
 * Fungsi untuk melakukan proses tutup buku bulanan
 */
function tutupBuku() {
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Proses Tutup Buku berhasil');
                    logout();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Close this period for '+getValue('kodeorg')+
        '\n are you sure?')) {
        post_response_text('keu_slave_3tutupbulanAudit.php?proses=tutupBuku', param, respon);
    }
}