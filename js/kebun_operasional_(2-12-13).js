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
    var notrans = document.getElementById('sNoTrans');
    if(notrans.value=='') {
        var where = '[["'+tipe+'","'+tipeVal+'"]]';
    } else {
        var where = '[["notransaksi","'+notrans.value+'"],["'+tipe+'","'+tipeVal+'"]]';
    }
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList(tipe) {
    goToPages(1,showPerPage,'[["tipetransaksi","'+tipe+'"]]');
}

/*function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows;
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
    
    post_response_text('kebun_slave_operasional.php?proses=showHeadList', param, respon);
}*/

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd(tipe) {
    var workField = document.getElementById('workField');
    var param = "tipe="+tipe;
    
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
    
    post_response_text('kebun_slave_operasional.php?proses=showAdd', param, respon);
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
    
    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);
}

function showEdit(num,tipe) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi_'+num);
    var param = "numRow="+num+"&notransaksi="+trans.getAttribute('value');
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
    
    post_response_text('kebun_slave_operasional.php?proses=showEdit', param, respon);
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
                    document.getElementById('notransaksi').value = con.responseText;
                   // alert('Added Data Header');
                    showEditFromAdd(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional.php?proses=add&tipe='+tipe, param, respon);
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
    
    post_response_text('kebun_slave_operasional.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField');
    var notrans = document.getElementById('notransaksi').value;
    var afdeling = getValue('kodeorg');
    var param = "notransaksi="+notrans+"&afdeling="+afdeling;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                    initUmrIns();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('notransaksi_'+num).getAttribute('value');
    var param = "notransaksi="+notrans;
    
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


    if(confirm('Hapus transaksi '+notrans+'. Anda yakin?')) {
        post_response_text('kebun_slave_operasional.php?proses=delete', param, respon);
    }
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

/* Init Total HK, UMR dan Insentif
 */
function initUmrIns() {
    var numRow = 0;
    var totalPresHk = document.getElementById('totalPresHk');
    var totalAbsHk = document.getElementById('totalAbsHk');
    var totalPresUmr = document.getElementById('totalPresUmr');
    var totalAbsUmr = document.getElementById('totalAbsUmr');
    var totalPresIns = document.getElementById('totalPresIns');
    var totalAbsIns = document.getElementById('totalAbsIns');
    var idPres = 'ftPrestasi';var idAbs = 'ftAbsensi';
    
    // Init Total Value
    totalPresHk.setAttribute('realValue',0);
    totalPresHk.value = 0;
    totalAbsHk.setAttribute('realValue',0);
    totalAbsHk.value = 0;
    totalPresUmr.setAttribute('realValue',0);
    totalPresUmr.value = 0;
    totalAbsUmr.setAttribute('realValue',0);
    totalAbsUmr.value = 0;
    totalPresIns.setAttribute('realValue',0);
    totalPresIns.value = 0;
    totalAbsIns.setAttribute('realValue',0);
    totalAbsIns.value = 0;
    
    // Get Prestasi Value
    while(document.getElementById("tr_"+idPres+"_"+numRow)) {
        tmpHk = document.getElementById(idPres+"_jumlahhk_"+numRow).getAttribute('value');
        tmpUmr = document.getElementById(idPres+"_umr_"+numRow).getAttribute('value');
        tmpIns = document.getElementById(idPres+"_upahpremi_"+numRow).getAttribute('value');
        
        totalPresHk.setAttribute('realValue',(parseFloat(totalPresHk.getAttribute('realValue')) + parseFloat(tmpHk)));
        totalPresHk.value = totalPresHk.getAttribute('realValue');
        totalPresUmr.setAttribute('realValue',(parseFloat(totalPresUmr.getAttribute('realValue')) + parseFloat(tmpUmr)));
        totalPresUmr.value = totalPresUmr.getAttribute('realValue');
        totalPresIns.setAttribute('realValue',(parseFloat(totalPresIns.getAttribute('realValue')) + parseFloat(tmpIns)));
        totalPresIns.value = totalPresIns.getAttribute('realValue');
        change_number(totalPresHk);
        change_number(totalPresUmr);
        change_number(totalPresIns);
        numRow++;
    }
    
    numRow = 0;
    // Get Absensi Value
    while(document.getElementById("tr_"+idAbs+"_"+numRow)) {
        tmpHk = document.getElementById(idAbs+"_jhk_"+numRow).getAttribute('value');
        tmpUmr = document.getElementById(idAbs+"_umr_"+numRow).getAttribute('value');
        tmpIns = document.getElementById(idAbs+"_insentif_"+numRow).getAttribute('value');
        
        totalAbsHk.setAttribute('realValue',(parseFloat(totalAbsHk.getAttribute('realValue')) + parseFloat(tmpHk)));
        totalAbsHk.value = totalAbsHk.getAttribute('realValue');
        totalAbsUmr.setAttribute('realValue',(parseFloat(totalAbsUmr.getAttribute('realValue')) + parseFloat(tmpUmr)));
        totalAbsUmr.value = totalAbsUmr.getAttribute('realValue');
        totalAbsIns.setAttribute('realValue',(parseFloat(totalAbsIns.getAttribute('realValue')) + parseFloat(tmpIns)));
        totalAbsIns.value = totalAbsIns.getAttribute('realValue');
        change_number(totalAbsHk);
        change_number(totalAbsUmr);
        change_number(totalAbsIns);
        numRow++;
    }
}

/* Total HK, UMR atau Insentif
 */
function totalVal() {
    initUmrIns();
    
    var totalPresHk = document.getElementById('totalPresHk');
    var totalAbsHk = document.getElementById('totalAbsHk');
    var totalPresUmr = document.getElementById('totalPresUmr');
    var totalAbsUmr = document.getElementById('totalAbsUmr');
    var totalPresIns = document.getElementById('totalPresIns');
    var totalAbsIns = document.getElementById('totalAbsIns');
    var modeArr = new Array('Pres','Abs');
    var varArr = new Array('Hk','Umr','Ins');
    
    var PresHk = document.getElementById('ftPrestasi_jumlahhk').firstChild;
    var PresUmr = document.getElementById('ftPrestasi_umr').firstChild;
    var PresIns = document.getElementById('ftPrestasi_upahpremi').firstChild;
    var AbsHk = document.getElementById('ftAbsensi_jhk').firstChild;
    var AbsUmr = document.getElementById('ftAbsensi_umr').firstChild;
    var AbsIns = document.getElementById('ftAbsensi_insentif').firstChild;
    
    var cancelPres = document.getElementById('clearFTBtn_ftPrestasi');
    var cancelAbs = document.getElementById('clearFTBtn_ftAbsensi');
    
    var numRowPres = document.getElementById('ftPrestasi_numRow').value;
    var numRowAbs = document.getElementById('ftAbsensi_numRow').value;
    
    // if NaN
    if(isNaN(parseFloat(PresHk.value))) {PresHk.value = 0;}
    if(isNaN(parseFloat(PresUmr.value))) {PresUmr.value = 0;}
    if(isNaN(parseFloat(PresIns.value))) {PresIns.value = 0;}
    if(isNaN(parseFloat(AbsHk.value))) {AbsHk.value = 0;}
    if(isNaN(parseFloat(AbsUmr.value))) {AbsUmr.value = 0;}
    if(isNaN(parseFloat(AbsIns.value))) {AbsIns.value = 0;}
    
    // Prestasi
    if(cancelPres.style.display=='') {
        var tmpRowHk = document.getElementById('ftPrestasi_jumlahhk_'+numRowPres).getAttribute('value');
        var tmpRowUmr = document.getElementById('ftPrestasi_umr_'+numRowPres).getAttribute('value');
        var tmpRowIns = document.getElementById('ftPrestasi_upahpremi_'+numRowPres).getAttribute('value');
        
        var tmpValHk = parseFloat(totalPresHk.getAttribute('realValue'))-parseFloat(tmpRowHk)+parseFloat(PresHk.value);
        var tmpValUmr = parseFloat(totalPresUmr.getAttribute('realValue'))-parseFloat(tmpRowUmr)+parseFloat(PresUmr.value);
        var tmpValIns = parseFloat(totalPresIns.getAttribute('realValue'))-parseFloat(tmpRowIns)+parseFloat(PresIns.value);
    } else {
        var tmpValHk = parseFloat(totalPresHk.getAttribute('realValue'))+parseFloat(PresHk.value);
        var tmpValUmr = parseFloat(totalPresUmr.getAttribute('realValue'))+parseFloat(PresUmr.value);
        var tmpValIns = parseFloat(totalPresIns.getAttribute('realValue'))+parseFloat(PresIns.value);
    }
    
    totalPresHk.setAttribute('realValue',tmpValHk);totalPresHk.value = tmpValHk;
    totalPresUmr.setAttribute('realValue',tmpValUmr);totalPresUmr.value = tmpValUmr;
    totalPresIns.setAttribute('realValue',tmpValIns);totalPresIns.value = tmpValIns;
    
    change_number(totalPresHk);
    change_number(totalPresUmr);
    change_number(totalPresIns);
    
    // Absensi
    if(cancelAbs.style.display=='') {
        var tmpRowHk = document.getElementById('ftAbsensi_jhk_'+numRowAbs).getAttribute('value');
        var tmpRowUmr = document.getElementById('ftAbsensi_umr_'+numRowAbs).getAttribute('value');
        var tmpRowIns = document.getElementById('ftAbsensi_insentif_'+numRowAbs).getAttribute('value');
        
        var tmpValHk = parseFloat(totalAbsHk.getAttribute('realValue'))-parseFloat(tmpRowHk)+parseFloat(AbsHk.value);
        var tmpValUmr = parseFloat(totalAbsUmr.getAttribute('realValue'))-parseFloat(tmpRowUmr)+parseFloat(AbsUmr.value);
        var tmpValIns = parseFloat(totalAbsIns.getAttribute('realValue'))-parseFloat(tmpRowIns)+parseFloat(AbsIns.value);
    } else {
        var tmpValHk = parseFloat(totalAbsHk.getAttribute('realValue'))+parseFloat(AbsHk.value);
        var tmpValUmr = parseFloat(totalAbsUmr.getAttribute('realValue'))+parseFloat(AbsUmr.value);
        var tmpValIns = parseFloat(totalAbsIns.getAttribute('realValue'))+parseFloat(AbsIns.value);
    }
    totalAbsHk.setAttribute('realValue',tmpValHk);totalAbsHk.value = tmpValHk;
    totalAbsUmr.setAttribute('realValue',tmpValUmr);totalAbsUmr.value = tmpValUmr;
    totalAbsIns.setAttribute('realValue',tmpValIns);totalAbsIns.value = tmpValIns;
    
    change_number(totalAbsHk);
    change_number(totalAbsUmr);
    change_number(totalAbsIns);
}

/* Cek HK, UMR atau Insentif
 */
function cekVal(obj,mode,cekVar) {
    var totalPres = document.getElementById('totalPres'+cekVar);
    var totalAbs = document.getElementById('totalAbs'+cekVar);
    var tmpVar = document.getElementById('tmpVal'+cekVar).value;
    if(mode=='Abs' && cekVar=='Hk') {
        if(obj.value>1) {
            alert('Nilai Maksimum HK adalah 1');
            obj.value = 1;
        }
    }
    totalVal();
    
    var selisih = parseFloat(totalAbs.getAttribute('realValue')) - parseFloat(totalPres.getAttribute('realValue'));
    if(parseFloat(totalPres.getAttribute('realValue')) < parseFloat(totalAbs.getAttribute('realValue'))) {
        alert('Nilai Prestasi harus lebih besar dari nilai Absensi');
        if(mode=='Abs' && cekVar=='Hk' && (selisih>1)) {
            obj.value = 1;
        } else {
            obj.value = tmpVar;
        }
    }
    totalVal();
}

/* Posting Data
 */
function postingData(numRow) {
    var notrans = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    var param = "notransaksi="+notrans;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                   // alert('Posting Berhasil');
                   // javascript:location.reload(true);
                    x=document.getElementById('tr_'+numRow);
                    x.cells[8].innerHTML='';
                    x.cells[9].innerHTML='';
                    x.cells[10].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Are you sure confirm Transaction:'+notrans+
        '?\nOnce confirmed, the data can not be edited.')) {
        post_response_text('kebun_slave_operasional_posting.php', param, respon);
    }
}

function printPDF(ev,tipe) {
    // Prep Param
    param = "proses=pdf&tipe="+tipe;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev,tipe) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    param = "proses=pdf&tipe="+tipe+"&notransaksi="+notransaksi;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailData(numRow,ev,tipe)
{
    var notransaksi = document.getElementById('notransaksi_'+numRow).getAttribute('value');
    param = "proses=html&tipe="+tipe+"&notransaksi="+notransaksi;
        title="Data Detail";
        showDialog1(title,"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='kebun_slave_operasional_print_detail.php?"+param+"'></iframe>",'800','400',ev);	
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '50px';
        dialog.style.left = '15%';
}

function changeOrg() {
    var prestasi = document.getElementById('ftPrestasi_kodeorg').firstChild;
    var material = document.getElementById('ftMaterial_kodeorg').firstChild;
    material.selectedIndex = prestasi.selectedIndex;
}

function updateUMR(obj) {
    var nik = obj.options[obj.selectedIndex].value;
    var umr = document.getElementById('ftAbsensi_umr').firstChild;
    var tanggal=document.getElementById('tanggal').value;
    var jhk=document.getElementById('jhk').value;
    var tahun=tanggal.substr(6, 4);
    var param = "nik="+nik+'&tahun='+tahun+'&jhk='+jhk;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    umr.value = con.responseText
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional_detail.php?proses=updateUMR', param, respon);
}

function updateUMR2() {
    var nik=document.getElementById('nik');
    nik = nik.options[nik.selectedIndex].value;
    var umr = document.getElementById('ftAbsensi_umr').firstChild;
    var tanggal=document.getElementById('tanggal').value;
    var jhk=document.getElementById('jhk').value;
    var tahun=tanggal.substr(6, 4);
    var param = "nik="+nik+'&tahun='+tahun+'&jhk='+jhk;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    umr.value = con.responseText
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('kebun_slave_operasional_detail.php?proses=updateUMR', param, respon);
}


function filterKaryawan(idkomponen,cek)
{
   try{  
     kodeorg=document.getElementById('ftPrestasi_kodeorg_0').getAttribute('value');
       if(cek.checked==true)
           param='kodeorg='+kodeorg+'&tipe=afdeling';
       else
           param='kodeorg='+kodeorg+'&tipe=unit';
       post_response_text('kebun_slave_operasional_detail.php?proses=gatKarywanAFD', param, respon);     
   }
   catch(err){
       alert('Simpan prestasi terlebih dahulu');
   }

     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById(idkomponen).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }   
}