function ubahNilai(code) {
    var nilai = document.getElementById(code).value;
    var proses = "ubahNilai";

    param='code='+code;
    param+='&nilai='+nilai;
    param+='&proses='+proses;
    tujuan='pabrik_slave_5general';

    if (nilai == '') {
        Swal.fire('Oops...', 'Value tidak boleh kosong.', 'error');
        loadData();
    } else {
        post_response_text(tujuan+'.php', param, respon);
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if (con.responseText == "SIMPAN BERHASIL") {
                        Swal.fire(con.responseText);
                    } else {
                        Swal.fire('Oops...', con.responseText, 'error');
                        loadData();
                    }
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function simpanGeneral() {
    var code = document.getElementById("code").value;  
    var nilai = document.getElementById("nilai").value;
    var proses = document.getElementById("proses").value;

    param+='&code='+code;
    param+='&nilai='+nilai;
    param+='&proses='+proses;
    
    tujuan='pabrik_slave_5general';

    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert(con.responseText);
                    document.getElementById("code").value = '';
                    document.getElementById("nilai").value = '';
                    document.getElementById("proses").value = 'simpanGeneral';
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}


function loadData()
{
    param='proses=loadData';
    tujuan='pabrik_slave_5general';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('dataGeneral').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}