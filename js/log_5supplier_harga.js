function setOptionSupplier() {
    var kode_klsupplier = document.getElementById("kode_klsupplier").value;  
    // var kode_supplier = document.getElementById("kode_supplier").value;  
    
    param='proses=getOptionSupplier';
    param+='&kode_klsupplier='+kode_klsupplier;
    tujuan='log_slave_5supplier_harga';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('kode_supplier').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function simpanSupplierHarga() {
    var tanggal = document.getElementById("tgl1_1").value;  
    var kode_klsupplier = document.getElementById("kode_klsupplier").value;  
    var kode_supplier = document.getElementById("kode_supplier").value;  
    var harga = document.getElementById("harga").value;  

    param='proses=simpanSupplierHarga';
    param+='&kode_klsupplier='+kode_klsupplier;
    param+='&kode_supplier='+kode_supplier;
    param+='&harga='+harga;
    param+='&tanggal='+tanggal;
    tujuan='log_slave_5supplier_harga';

    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById("kode_klsupplier").value = '';
                    document.getElementById("kode_supplier").value = '';
                    document.getElementById("harga").value = '';
                    document.getElementById("tgl1_1").value = '';
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clone() {
    param='proses=clone';
    tujuan='log_slave_5supplier_harga';

    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById("kode_klsupplier").value = '';
                    document.getElementById("kode_supplier").value = '';
                    document.getElementById("harga").value = '';
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
    tujuan='log_slave_5supplier_harga';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('loadDataTable').innerHTML='';
                    document.getElementById('loadDataTable').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function cariBast(num)
{
        param='proses=loadData';
        param+='&page='+num;
        tujuan='log_slave_5supplier_harga';
        post_response_text(tujuan + '.php', param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('loadDataTable').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}

function deleteSupplierHarga(kode_klsupplier, kode_supplier) {
    param='proses=deleteSupplierHarga';
    param+='&kode_klsupplier='+kode_klsupplier;
    param+='&kode_supplier='+kode_supplier;
    tujuan='log_slave_5supplier_harga';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}