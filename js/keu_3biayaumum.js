/* listPosting
 * Fungsi untuk men-generate list dari transaksi yang dapat di posting
 */
function listPosting() {
    var listPost = document.getElementById('listPosting');
    var param = "kodeorg="+getValue('kodeorg')+"&periode="+getValue('periode');
    
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
    
    post_response_text('keu_slave_3biayaumum.php', param, respon);
}

/* Status tiap Posting, jika 0 berarti ada data yang belum lolos posting
 */
var stat = {};
stat['masukBarang'] = 1;
stat['keluarBarang'] = 1;

/* post
 * Proses Posting
 */
function post(id,num,field,theme) {
    var imgTr = document.getElementById('status_'+id+'_'+num);
    var btnPost = document.getElementById('btnPosting_'+id);
    var fieldJs = field.split('##');
    var param = "param_kodeorg="+getValue('kodeorg')+"&param_periode="+getValue('periode');
    for(i=1;i<fieldJs.length;i++) {
        param += '&'+fieldJs[i]+'='+getValue(id+'_'+fieldJs[i]+'_'+num);
    }
    
    // Disable Post Button
    //btnPost.setAttribute('disabled','disabled');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    imgTr.removeAttribute('src');
                    imgTr.setAttribute('src','images/'+theme+'/posting.png');
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+id+'_'+(parseInt(num)+1));
                    // Change Image
                    if(con.responseText=='1') {
                        imgTr.removeAttribute('src');
                        imgTr.setAttribute('src','images/'+theme+'/posted.png');
                    } else {
                        imgTr.removeAttribute('src');
                        imgTr.setAttribute('src','images/'+theme+'/posting.png');
                        stat[id] = 0;
                    }
                    // Process Next Row or Stop
                    if(tmp) {
                        post(id,parseInt(num)+1,field,theme);
                    } else {
                        if(stat[id]==0) {
                            btnPost.removeAttribute('disabled');
                        }
                        stat[id]=1;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(imgTr.getAttribute('src')=='images/'+theme+'/posting.png') {
        if(post_response_text('keu_slave_3biayaumum_posting.php', param, respon)) {
            // Change Images
            imgTr.removeAttribute('src');
            imgTr.setAttribute('src','images/'+theme+'/progress1.gif');
        }
    }
}