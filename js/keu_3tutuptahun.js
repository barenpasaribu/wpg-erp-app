function postingData() {
    var btnPost = document.getElementById('btnPost');
    var param = "kodeorg="+getValue('kodeorg')+"&tahun="+getValue('tahun');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Posting Berhasil dilakukan');
                    btnPost.setAttribute('disabled','disabled');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Akan dilakukan proses penjurnalan untuk tutup tahun '+getValue('tahun')+
        '\nAnda yakin?')) {
        post_response_text('keu_slave_3tutuptahun.php', param, respon);
    }
}