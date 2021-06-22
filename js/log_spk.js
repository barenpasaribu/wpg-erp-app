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
    var notrans = document.getElementById('sNoTrans');
    var where = '[["notransaksi","'+notrans.value+'"]]';
    
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
    
    post_response_text('log_slave_spk.php?proses=showHeadList', param, respon);
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
    
    post_response_text('log_slave_spk.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi');
    var kodeorg = document.getElementById('kodeorg');
    var param = "notransaksi="+trans.value+"&kodeorg="+kodeorg.options[kodeorg.selectedIndex].value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_spk.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+num).getAttribute('value');
    var param = "numRow="+num+"&notransaksi="+trans+"&kodeorg="+kodeorg;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_spk.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "kodeorg="+getValue('kodeorg')+"&notransaksi="+getValue('notransaksi');
    param += "&tanggal="+getValue('tanggal')+"&divisi="+getValue('divisi');
    param += "&koderekanan="+getValue('koderekanan')+"&nilaikontrak="+getValue('nilaikontrak');
    param += "&dari="+getValue('dari')+"&sampai="+getValue('sampai');
    param += "&keterangan="+getValue('keterangan');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Added Data Header');
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_spk.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "kodeorg="+getValue('kodeorg')+"&notransaksi="+getValue('notransaksi');
    param += "&tanggal="+getValue('tanggal')+"&divisi="+getValue('divisi');
    param += "&koderekanan="+getValue('koderekanan')+"&nilaikontrak="+getValue('nilaikontrak');
    param += "&dari="+getValue('dari')+"&sampai="+getValue('sampai');
    param += "&keterangan="+getValue('keterangan');
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
    
    post_response_text('log_slave_spk.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('notransaksi').value;
    var param = "notransaksi="+notrans+"&divisi="+getValue('divisi');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_spk_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+num).getAttribute('value');
    var param = "notransaksi="+notrans+"&kodeorg"+kodeorg;
    
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
    
	if(confirm("Anda akan menghapus SPK dengan No: "+notrans+"\nAnda yakin?"))
		post_response_text('log_slave_spk.php?proses=delete', param, respon);
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_spk_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '75px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var kodeorg = document.getElementById('kodeorg_'+numRow).getAttribute('value');
    var koderekanan = document.getElementById('koderekanan_'+numRow).getAttribute('value');
    param = "proses=pdf&notransaksi="+notransaksi+"&kodeorg="+kodeorg+
        "&koderekanan="+koderekanan;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_spk_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '100px';
    dialog.style.left = '15%';
}

function postingData(id)
{
    notrans=document.getElementById('notransaksi_'+id).getAttribute('value');
    param='proses=postingSpk'+'&noTrans='+notrans;
    tujuan='log_slave_spk_posting.php';
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
					alert('Success');
                    //javascript:location.reload(true);
                   defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm("Confirm ?"))
        post_response_text(tujuan, param, respon);
}

function updSub() {
    var kodeorg = getValue('kodeorg');
    var sub = document.getElementById('divisi');
    param='kodeorg='+kodeorg;
    sub.options.length=0;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval('var res = '+con.responseText);
                    for(i in res) {
                        sub.options[sub.options.length] = new Option(res[i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('log_slave_spk.php?proses=updSub', param, respon);
}

function updKegiatan() {
    var kodeblok = getValue('kodeblok');
    var kodekegiatan = document.getElementById('kodekegiatan');
    param='kodeblok='+kodeblok;
    kodekegiatan.options.length=0;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval('var res = '+con.responseText);
                    for(i in res) {
                        kodekegiatan.options[kodekegiatan.options.length] = new Option(res[i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('log_slave_spk_detail.php?proses=updKegiatan', param, respon);
}

function beforeEditMode(numRow,id,els,addEls,mode,freeze,numFormat) {
	var kodeblok = getValue('kodeblok');
    var kodekegiatan = document.getElementById('kodekegiatan');
    param='kodeblok='+kodeblok;
    kodekegiatan.options.length=0;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval('var res = '+con.responseText);
                    for(i in res) {
                        kodekegiatan.options[kodekegiatan.options.length] = new Option(res[i],i);
                    }
					theFT.editmodeFT(numRow,id,els,addEls,mode,freeze,numFormat);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('log_slave_spk_detail.php?proses=updKegiatan', param, respon);
}