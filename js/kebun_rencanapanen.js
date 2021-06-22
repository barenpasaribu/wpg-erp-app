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
function searchTrans(tipe,tipeVal) {
    var afdl = document.getElementById('sAfdeling').options[document.getElementById('sAfdeling').selectedIndex].value;
    var bln = document.getElementById('sBulan').options[document.getElementById('sBulan').selectedIndex].value;
    var tahun = document.getElementById('sTahun').value;
    var where='';
   // where='[["'+tipe+'","'+tipeVal+'"]';
    if(tahun.length!=4)
    {
        alert("Format Tahun Salah");
        return;
    }
   
    if(afdl!=''){
       where+='[["kodeorg","'+afdl+'"]';
    }
    if(bln!=''&&tahun!=''){
        if(bln.length<2)
            {
                ard=tahun+"-"+"0"+bln;
            }
            else
            {
                ard=tahun+"-"+bln;
            }
       where+=',["substr(tanggal,1,7)","'+ard+'"]]';
    }
 
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
    
    post_response_text('kebun_slave_rencanapanen.php?proses=showHeadList', param, respon);
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
    
    post_response_text('kebun_slave_rencanapanen.php?proses=showAdd', param, respon);
}

function showEditFromAdd(tipe) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi');
    var param = "notransaksi="+trans.value;
    param+="&tipe="+tipe;
    
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
    
    post_response_text('kebun_slave_rencanapanen.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var kodeblok = document.getElementById('kodeblok_'+num);
    var bulan = document.getElementById('bulan_'+num);
    var tahun = document.getElementById('tahun_'+num);
    var param = "numRow="+num+"&kodeblok="+kodeblok.innerHTML+
        "&bulan="+bulan.innerHTML+"&tahun="+tahun.innerHTML;
    
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
    
    post_response_text('kebun_slave_rencanapanen.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable(tipe) {
    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');
    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');
    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');
    param += "&keranimuat="+getValue('keranimuat');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Added Data Header');
                    showEditFromAdd(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_rencanapanen.php?proses=add&tipe='+tipe, param, respon);
}

function editDataTable(tipe) {
    var param = "notransaksi="+getValue('notransaksi')+"&kodeorg="+getValue('kodeorg');
    param += "&tanggal="+getValue('tanggal')+"&nikmandor="+getValue('nikmandor');
    param += "&nikmandor1="+getValue('nikmandor1')+"&nikasisten="+getValue('nikasisten');
    param += "&keranimuat="+getValue('keranimuat');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    defaultList(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_rencanapanen.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var bulan = getValue('bulan');
    var tahun = getValue('tahun');
    var afdeling = getValue('kodeorg');
    var param = "bulan="+bulan+"&tahun="+tahun+"&afdeling="+afdeling;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                    document.getElementById('bulan').setAttribute('disabled','disabled');
                    document.getElementById('tahun').setAttribute('disabled','disabled');
                    document.getElementById('kodeorg').setAttribute('disabled','disabled');
                    document.getElementById('showDetBtn').setAttribute('disabled','disabled');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_rencanapanen_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var kodeorg = document.getElementById('kodeorg_'+num);
    var kodeblok = document.getElementById('kodeblok_'+num);
    var bulan = document.getElementById('bulan_'+num);
    var tahun = document.getElementById('tahun_'+num);
    var param = "numRow="+num+"&kodeorg="+kodeorg.innerHTML+"&kodeblok="+kodeblok.innerHTML+
        "&bulan="+bulan.innerHTML+"&tahun="+tahun.innerHTML;
    
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
    
    post_response_text('kebun_slave_rencanapanen.php?proses=delete', param, respon);
}

function printPDF(ev,tipe) {
    // Prep Param
    param = "proses=pdf&tipe="+tipe;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_rencanapanen_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}