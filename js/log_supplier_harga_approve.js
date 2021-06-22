function loadData()
{
    param='proses=loadData';
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
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
        tujuan='log_slave_supplier_harga_approve';
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

function showForm() {
    param='proses=showForm';
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan + '.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('formInput').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function batal() {
    document.getElementById('formInput').innerHTML="";
}

function simpanSupplierHarga() {
    var tanggal = document.getElementById("tanggal").value;
    var kode_klsupplier = document.getElementById("kode_klsupplier").value;
    var operator_kenaikan = document.getElementById("operator_kenaikan").value;
    var harga = document.getElementById("harga").value;  
    var fluktuasi = document.getElementById("fluktuasi").value;  

    var supplier =  document.getElementsByName("supplier[]");
    var  supplier_length = supplier.length;

    if (supplier_length == 0) {
        alert("Gagal, Supplier harga harus diisi!");
    }

    param='proses=simpanSupplierHistory';
    param+='&kode_klsupplier='+kode_klsupplier;
    param+='&operator_kenaikan='+operator_kenaikan;
    param+='&harga='+harga;
    param+='&fluktuasi='+fluktuasi;
    param+='&tanggal='+tanggal;
  
    for(i = 0; i < supplier_length; i++)
    {
        if (supplier[i].checked) {
            param+='&arraySupplierStatus['+i+']=1';
        }else{
            param+='&arraySupplierStatus['+i+']=0';
        }
        param+='&arraySupplier['+i+']='+supplier[i].value;
    }

    var feeElement = document.getElementsByName('fee[]');
    for (var i = 0; i < feeElement.length; i++) {
        param+='&arrayFee['+i+']='+feeElement[i].value;
    }

        
    tujuan='log_slave_supplier_harga_approve';

    if(harga){
        post_response_text(tujuan+'.php', param, respon);
    }else{
        alert("Gagal, harga harus diisi!");
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById("menuPilihSupplier").style.display = "none";
                    document.getElementById('btnSimpan').disabled = true;
                    document.getElementById("kode_klsupplier").value = '';
                    document.getElementById("harga").value = '';
                    alert(con.responseText);
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function pilihSemua() {
    var checkall =  document.getElementById("checkall");
    var supplier =  document.getElementsByName("supplier[]");
    var supplier_length = supplier.length;
    if (checkall.checked) {
        for(i = 0; i < supplier_length; i++)
        {
            supplier[i].checked = true;
        }
    }else{
        for(i = 0; i < supplier_length; i++)
        {
            supplier[i].checked = false;
        }
    }
}

function showCheckbox() {
    var kode_klsupplier = document.getElementById("kode_klsupplier").value;
    var tanggal = document.getElementById("tanggal").value;  
    
    param='proses=showCheckbox';
    param+='&kode_klsupplier='+kode_klsupplier;
    param+='&tanggal='+tanggal;
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    
                    if(con.responseText){
                        document.getElementById("menuPilihSupplier").style.display = "block";
                        document.getElementById('btnSimpan').removeAttribute("disabled");
                        document.getElementById('listSupplier').innerHTML=con.responseText;
                    }else{
                        document.getElementById("menuPilihSupplier").style.display = "none";
                        document.getElementById('btnSimpan').disabled = true;
                    }
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function susunanData() {
    document.getElementById('formInput').innerHTML="";
}

function listData() {
    param='proses=listData';
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('listSupplier').innerHTML=con.responseText;   
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function reject(jenisApprove, id, karyawanid) {
    param='proses=reject';
    param+='&id='+id;
    param+='&jenisApprove='+jenisApprove;
    param+='&karyawanid='+karyawanid;

    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);   
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

function deleteData(id) {
    param='proses=deleteData';
    param+='&id='+id;

    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert(con.responseText);
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function approve(jenisApprove, id, karyawanid) {
    param='proses=approve';
    param+='&id='+id;
    param+='&jenisApprove='+jenisApprove;
    param+='&karyawanid='+karyawanid;
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // console.log(con.responseText);
                    loadData();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function showListApprove(id) {
    // Prep Param
    param = "proses=showListApprove&temporary_list_id="+id;
    
    showDialog1('List Approve',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_supplier_harga_approve.php?"+param+"'></iframe>",'800','400','event');
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '100px';
    dialog.style.left = '15%';
}

function ubahFee(id,tanggal, kodesupplier, no) {
    nilai = document.getElementById("fee_"+no).value;
    param='proses=updateFee';
    param+='&tanggal='+tanggal;
    param+='&kodesupplier='+kodesupplier;
    param+='&id='+id;
    param+='&nilai='+nilai;
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function ubahFluktuasi(id,tanggal, kodesupplier, no) {
    nilai = document.getElementById("harga_"+no).value;
    param='proses=updateFluktuasi';
    param+='&tanggal='+tanggal;
    param+='&kodesupplier='+kodesupplier;
    param+='&id='+id;
    param+='&nilai='+nilai;
    tujuan='log_slave_supplier_harga_approve';
    post_response_text(tujuan+'.php', param, respon);   
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}