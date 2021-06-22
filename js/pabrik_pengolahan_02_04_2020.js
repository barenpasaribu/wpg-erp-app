function postingData(numRow) {
    var nopengolahan = document.getElementById('nopengolahan_'+numRow).getAttribute('value');
	//nopengolahan=trim(document.getElementById('nopengolahan'+numRow).innerHTML);
    var param = "nopengolahan="+nopengolahan;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Posting Berhasil');
                   // javascript:location.reload(true);
                    x=document.getElementById('tr_'+numRow);
                    x.cells[4].innerHTML='';
                    x.cells[5].innerHTML='';
                    x.cells[6].innerHTML = "<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                    x.cells[7].innerHTML = '';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Apakah anda yakin untuk konfirmasi transaksi :'+nopengolahan+
        '?\n data tidak akan bisa diedit')) {
        post_response_text('pabrik_slave_pengolahan.php?proses=posting', param, respon);
    }
}


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
    var where = '[["nopengolahan","'+notrans.value+'"]]';
    
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
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showHeadList', param, respon);
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
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('nopengolahan');
    var param = "nopengolahan="+trans.value;
    
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
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('nopengolahan_'+num);
    var param = "numRow="+num+"&nopengolahan="+trans.innerHTML;
    
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
    
    post_response_text('pabrik_slave_pengolahan.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var noP = document.getElementById('nopengolahan');
    var param = "kodeorg="+getValue('kodeorg')+"&nopengolahan="+getValue('nopengolahan');
    param += "&tanggal="+getValue('tanggal')+"&shift="+getValue('shift');
    param += "&jammulai="+getValue('jammulai_jam')+":"+getValue('jammulai_menit')+":00";
    param += "&jamselesai="+getValue('jamselesai_jam')+":"+getValue('jamselesai_menit')+":00";
    param += "&mandor="+getValue('mandor')+"&asisten="+getValue('asisten');
    param += "&jamdinasbruto="+getValue('jamdinasbruto')+"&jamstagnasi="+getValue('jamstagnasi');
    param += "&jumlahlori="+getValue('jumlahlori')+"&tbsdiolah="+getValue('tbsdiolah');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    // noP.value = con.responseText;
                    console.log(con.responseText);
                    
                    document.getElementById('nopengolahan').value = con.responseText;
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "kodeorg="+getValue('kodeorg')+"&nopengolahan="+getValue('nopengolahan');
    param += "&tanggal="+getValue('tanggal')+"&shift="+getValue('shift');
    param += "&jammulai="+getValue('jammulai_jam')+":"+getValue('jammulai_menit')+":00";
    param += "&jamselesai="+getValue('jamselesai_jam')+":"+getValue('jamselesai_menit')+":00";
    param += "&mandor="+getValue('mandor')+"&asisten="+getValue('asisten');
    param += "&jamdinasbruto="+getValue('jamdinasbruto')+"&jamstagnasi="+getValue('jamstagnasi');
    param += "&jumlahlori="+getValue('jumlahlori')+"&tbsdiolah="+getValue('tbsdiolah');
    
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
    
    post_response_text('pabrik_slave_pengolahan.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('nopengolahan').value;
    var param = "nopengolahan="+notrans+"&kodeorg="+getValue('kodeorg');
    
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
    
    post_response_text('pabrik_slave_pengolahan_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('nopengolahan_'+num).innerHTML;
    var param = "nopengolahan="+notrans;
    
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
		if(confirm("Yakin akan hapus data ?")){
			post_response_text('pabrik_slave_pengolahan.php?proses=delete', param, respon);
		}        
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_pengolahan_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function updMesin() {
    var mesin = document.getElementById('tahuntanam');
    var param = "station="+getValue('station');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval("var res = "+con.responseText+";");
                    mesin.options.length=0;
                    for(i in res) {
                        mesin.options[mesin.options.length] = new Option(res[i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan_detail.php?proses=updMesin', param, respon);
}

function updMandorAst(mode) {
    var mandor = document.getElementById('mandor');
    var asisten = document.getElementById('asisten');
    var shift = document.getElementById('shift');
    if(shift.selectedIndex==-1) {
        var shiftVal = 'empty';
    } else {
        var shiftVal = getValue('shift');
    }
    var param = "tanggal="+getValue('tanggal')+"&shift="+shiftVal+"&mode="+mode;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    eval("var res = "+con.responseText+";");
                    if(res['shift']!='empty') {
                        shift.options.length=0;
                        for(i in res['shift']) {
                            shift.options[shift.options.length] = new Option(res['shift'][i],i);
                        }
                    }
                    mandor.options.length=0;
                    for(i in res['mandor']) {
                        mandor.options[mandor.options.length] = new Option(res['mandor'][i],i);
                    }
                    asisten.options.length=0;
                    for(i in res['asisten']) {
                        asisten.options[asisten.options.length] = new Option(res['asisten'][i],i);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan.php?proses=updMandorAst', param, respon);
}

function detailPDF(numRow,ev) {
    // Prep Param
    var nopengolahan = document.getElementById('nopengolahan_'+numRow).getAttribute('value');
    param = "proses=pdf&nopengolahan="+nopengolahan;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_pengolahan_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function showMaterial(num,ev) {
    var station = document.getElementById('ftMesin_station_'+num).getAttribute('value');
    var mesin = document.getElementById('ftMesin_tahuntanam_'+num).getAttribute('value');
    
    var param = "nopengolahan="+getValue('nopengolahan')+
        "&kodeorg="+station+"&tahuntanam="+mesin+"&numRow="+num;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Edit Material',con.responseText,'800','300',ev);
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '10%';
                    dialog.style.left = '15%';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_pengolahan_material.php?proses=showMaterial', param, respon);
}