var showPerPage = 10;

function getValue(id) {
    var tmp = document.getElementById(id);
    
    if(tmp) {
        if(tmp.options) {
            return tmp.options[tmp.selectedIndex].value;
        } else if(tmp.nodeType=='checkbox') {
            if(tmp.checked==true) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return tmp.value;
        }
    } else {
        return false;
    }
}

/* Search
 * Filtering Data
 */
function searchTrans() {
    var notrans = document.getElementById('sNoTrans').value;
    var tmpTgl = notrans.split('-');
    var tgl = tmpTgl[2]+tmpTgl[1]+tmpTgl[0];
    var where = '[["tanggal","'+tgl+'"]]';
    
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList() {
    goToPages(1,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows+"&tipe=KB";
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd() {
    var workField = document.getElementById('workField');
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=showAdd', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var tanggal = document.getElementById('tanggal_'+num).getAttribute('value');
    var orgpengirim = document.getElementById('kodeorgpengirim_'+num).getAttribute('value');
    var orgpenerima = document.getElementById('kodeorgpenerima_'+num).getAttribute('value');
    var nogiro = document.getElementById('nogiro_'+num).getAttribute('value');
    var param = "numRow="+num+"&tanggal="+tanggal+"&kodeorgpengirim="+orgpengirim+
        "&kodeorgpenerima="+orgpenerima+"&nogiro="+nogiro;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "tanggal="+getValue('tanggal')+"&kodeorgpengirim="+getValue('kodeorgpengirim');
    param += "&kodeorgpenerima="+getValue('kodeorgpenerima')+"&noakunpengirim="+getValue('noakunpengirim');
    param += "&noakunpenerima="+getValue('noakunpenerima')+"&jumlah="+getValue('jumlah');
    param += "&nogiro="+getValue('nogiro')+"&tglgiro="+getValue('tglgiro');
    param += "&tgljatuhtempo="+getValue('tgljatuhtempo');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Data Berhasil ditambah');
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "tanggal="+getValue('tanggal')+"&kodeorgpengirim="+getValue('kodeorgpengirim');
    param += "&kodeorgpenerima="+getValue('kodeorgpenerima')+"&noakunpengirim="+getValue('noakunpengirim');
    param += "&noakunpenerima="+getValue('noakunpenerima')+"&jumlah="+getValue('jumlah');
    param += "&nogiro="+getValue('nogiro')+"&tglgiro="+getValue('tglgiro');
    param += "&tgljatuhtempo="+getValue('tgljatuhtempo');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=edit', param, respon);
}

function deleteData(num) {
    var tanggal = document.getElementById('tanggal_'+num).getAttribute('value');
    var orgpengirim = document.getElementById('kodeorgpengirim_'+num).getAttribute('value');
    var orgpenerima = document.getElementById('kodeorgpenerima_'+num).getAttribute('value');
    var nogiro = document.getElementById('nogiro_'+num).getAttribute('value');
    var param = "numRow="+num+"&tanggal="+tanggal+"&kodeorgpengirim="+orgpengirim+
        "&kodeorgpenerima="+orgpenerima+"&nogiro="+nogiro;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_transferdana.php?proses=delete', param, respon);
}

/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;
    
    if(tabBody.childNodes.length>0) {
        for(i=0;i<tabBody.childNodes.length;i++) {
            var tmp = document.getElementById('nourut_'+i);
            if(tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum)+1;
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_transferdana_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

/* Posting Data
 */
function postingData(num) {
    var tanggal = document.getElementById('tanggal_'+num).getAttribute('value');
    var orgpengirim = document.getElementById('kodeorgpengirim_'+num).getAttribute('value');
    var orgpenerima = document.getElementById('kodeorgpenerima_'+num).getAttribute('value');
    var nogiro = document.getElementById('nogiro_'+num).getAttribute('value');
    var param = "numRow="+num+"&tanggal="+tanggal+"&kodeorgpengirim="+orgpengirim+
        "&kodeorgpenerima="+orgpenerima+"&nogiro="+nogiro;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Posting Berhasil');
                    javascript:location.reload(true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Akan dilakukan posting untuk transaksi tanggal '+tanggal+
        '\nKiriman '+orgpengirim+' untuk '+orgpenerima+
        '\nData tidak dapat diubah setelah ini. Anda yakin?')) {
        post_response_text('keu_slave_transferdana_posting.php', param, respon);
    }
}