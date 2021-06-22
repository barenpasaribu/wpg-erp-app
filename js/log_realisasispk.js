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
    
    post_response_text('log_slave_realisasispk.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
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
    
    post_response_text('log_slave_realisasispk.php?proses=showEdit', param, respon);
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
    
    post_response_text('log_slave_realisasispk_detail.php?proses=showDetail', param, respon);
}
function getTiket(notiket,target,target2,numrow1,numrow2) {
   
   tanggal = "tanggal_"+numrow1+"_"+numrow2;
   console.log(tanggal)
   tgl = document.getElementById(tanggal).value;
    var param = "notiket="+notiket;
    param +="&tanggal="+tgl;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    ar= con.responseText.split("#");
                    target.value=ar[0];
                    target2.value=ar[1];
                    calJumlah(numrow1,numrow2);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_realisasispk_detail.php?proses=gettiket', param, respon);
}

function manageDetail(numRow) {
    var detailField = document.getElementById('detail_'+numRow);
    var notrans = document.getElementById('notransaksi').value;
    var koderekanan = document.getElementById('koderekanan').value;
    var kodeblok = document.getElementById('kodeblok_'+numRow).getAttribute('value');
    var kodekeg = document.getElementById('kodekegiatan_'+numRow).getAttribute('value');
    var param = "notransaksi="+notrans+"&kodeblok="+kodeblok+"&koderekanan="+koderekanan+"&numRow="+numRow;
    param += "&kodekegiatan="+kodekeg+"&divisi="+getValue('divisi');
    
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
    
    if(detailField.innerHTML=="") {

        post_response_text('log_slave_realisasispk_detail.php?proses=manageDetail', param, respon);
    } else {
        if(detailField.style.display=='none') {
            detailField.style.display="";
        } else {
            detailField.style.display="none";
        }
    }
}

function cekKP(numRow1,numRow2){
    kodeaplikasi = 'kosong';
    var keg = document.getElementById('kodekegiatan_'+numRow1).getAttribute('value');
    listkp=document.getElementById("listkp").value; // ada di log_realisasi.php
    var vba = listkp.split("####"); 
    for(var i = 0, len = vba.length; i < len; ++i) {
        if(keg==vba[i])kodeaplikasi='KP';
    }
    return kodeaplikasi;    
}

function addData(numRow1,numRow2,theme) {
//    if(cekReal(numRow1,numRow2)==false) {
//        alert('Realisasi HK, Hasil Kerja atau Jumlah melebihi kontrak');
//        return;
//    }
    if(cekKP(numRow1,numRow2)!='KP')
        if(cekReal(numRow1,numRow2)==false) {
            alert('Actual realization larger than contract volume');
            return;
        }

    var koderekanan = document.getElementById('koderekanan').value;
    var tbody = document.getElementById('detailBody_'+numRow1);
    var blok = document.getElementById('kodeblok_'+numRow1).getAttribute('value');
    var keg = document.getElementById('kodekegiatan_'+numRow1).getAttribute('value');
    var param = "notransaksi="+getValue('notransaksi')+"&kodeblok="+blok+
        "&kodekegiatan="+keg+'&divisi='+getValue('divisi')+'&tanggalSpk='+getValue('tanggal');
    param += "&blokalokasi="+getValue('blokalokasi_'+numRow1+'_'+numRow2);
    param += "&tanggal="+getValue('tanggal_'+numRow1+'_'+numRow2);
    param += "&hasilkerjarealisasi="+getValue('hasilkerjarealisasi_'+numRow1+'_'+numRow2);
    param += "&hkrealisasi="+getValue('hkrealisasi_'+numRow1+'_'+numRow2);
    param += "&notiket="+getValue('notiket_'+numRow1+'_'+numRow2);
    param += "&nodo="+getValue('nodo_'+numRow1+'_'+numRow2);
    param += "&jumlahrealisasi="+getValue('jumlahrealisasi_'+numRow1+'_'+numRow2);
    param += "&numRow1="+numRow1+"&numRow2="+numRow2;
    param += "&jjgkontanan="+getValue('jjgkontanan_'+numRow1+'_'+numRow2);
    //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    saveToAdd(numRow1,numRow2,theme);
                    var newRow = document.createElement("tr");
                    newRow.setAttribute('id','tr_'+numRow1+'_'+(numRow2+1));
                    newRow.setAttribute('class','rowcontent');
                    tbody.appendChild(newRow);
                    newRow.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_realisasispk_detail.php?proses=add', param, respon);
}

function saveData(numRow1,numRow2) {
if(cekKP(numRow1,numRow2)!='KP')//tambahan dz, cek apakah kontrak plus?
    if(cekReal(numRow1,numRow2)==false) {
        alert('Actual realization larger than contract volume');
        return;
    }
    
    var blok = document.getElementById('kodeblok_'+numRow1).getAttribute('value');
    var keg = document.getElementById('kodekegiatan_'+numRow1).getAttribute('value');
    var param = "notransaksi="+getValue('notransaksi')+'&tanggalSpk='+getValue('tanggal')+"&kodeblok="+blok+"&kodekegiatan="+keg;
    param += "&blokalokasi="+getValue('blokalokasi_'+numRow1+'_'+numRow2);
    param += "&tanggal="+getValue('tanggal_'+numRow1+'_'+numRow2);
    param += "&hasilkerjarealisasi="+getValue('hasilkerjarealisasi_'+numRow1+'_'+numRow2);
    param += "&hkrealisasi="+getValue('hkrealisasi_'+numRow1+'_'+numRow2);
    param += "&jumlahrealisasi="+getValue('jumlahrealisasi_'+numRow1+'_'+numRow2);
    param += "&jjgkontanan="+getValue('jjgkontanan_'+numRow1+'_'+numRow2);

    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Data changed');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_realisasispk_detail.php?proses=edit', param, respon);
}

function saveToAdd(numRow1,numRow2,theme) {
    var btn = document.getElementById('btn_'+numRow1+'_'+numRow2);
    var btnDel = document.getElementById('btnDel_'+numRow1+'_'+numRow2);
    var btnPost = document.getElementById('btnPost_'+numRow1+'_'+numRow2);
    var tanggal = document.getElementById('tanggal_'+numRow1+'_'+numRow2);
    var blok = document.getElementById('blokalokasi_'+numRow1+'_'+numRow2);
    
    // Change btn
    btn.removeAttribute('src');
    btn.removeAttribute('onclick');
    btn.setAttribute('src','images/'+theme+'/save.png');
    btn.setAttribute('onclick','saveData('+numRow1+','+numRow2+')');
    btnDel.style.display = "";
    btnPost.style.display = "";
    tanggal.setAttribute('disabled','disabled');
    blok.setAttribute('disabled','disabled');
}

function deleteData(numRow1,numRow2) {
    var tr = document.getElementById('tr_'+numRow1+'_'+numRow2);
    var blok = document.getElementById('kodeblok_'+numRow1).getAttribute('value');
    var keg = document.getElementById('kodekegiatan_'+numRow1).getAttribute('value');
    var param = "notransaksi="+getValue('notransaksi')+"&kodeblok="+blok+"&kodekegiatan="+keg;
    param += "&tanggal="+getValue('tanggal_'+numRow1+'_'+numRow2);
    param += "&blokalokasi="+getValue('blokalokasi_'+numRow1+'_'+numRow2);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    tr.parentNode.removeChild(tr);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_realisasispk_detail.php?proses=delete', param, respon);
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
        " src='log_slave_realisasispk_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '100px';
    dialog.style.left = '15%';
}

/* Posting Data
 */
function postingData(numRow1,numRow2,theme) {
    var blok = document.getElementById('kodeblok_'+numRow1).getAttribute('value');
    var keg = document.getElementById('kodekegiatan_'+numRow1).getAttribute('value');
    var hasilkerjarealisasi = document.getElementById('hasilkerjarealisasi_'+numRow1+'_'+numRow2);
    var hkrealisasi = document.getElementById('hkrealisasi_'+numRow1+'_'+numRow2);
    var jumlahrealisasi = document.getElementById('jumlahrealisasi_'+numRow1+'_'+numRow2);
    var btn = document.getElementById('btn_'+numRow1+'_'+numRow2);
    var btnDel = document.getElementById('btnDel_'+numRow1+'_'+numRow2);
    var btnPost = document.getElementById('btnPost_'+numRow1+'_'+numRow2);
    
    var param = "kodeorg="+getValue('kodeorg')+"&koderekanan="+getValue('koderekanan');
    param += "&notransaksi="+getValue('notransaksi')+"&kodeblok="+blok+"&kodekegiatan="+keg;
    param += "&blokalokasi="+getValue('blokalokasi_'+numRow1+'_'+numRow2);
    param += "&tanggal="+getValue('tanggal_'+numRow1+'_'+numRow2);
    //param += "&hasilkerjarealisasi="+getValue('hasilkerjarealisasi_'+numRow1+'_'+numRow2);
    //param += "&hkrealisasi="+getValue('hkrealisasi_'+numRow1+'_'+numRow2);
	// diedit oleh louis menambahkan remove comma yang sebelumnya getValue
    param += "&jumlahrealisasi="+remove_comma(document.getElementById('jumlahrealisasi_'+numRow1+'_'+numRow2));
    
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    hasilkerjarealisasi.setAttribute('disabled','disabled');
                    hkrealisasi.setAttribute('disabled','disabled');
                    jumlahrealisasi.setAttribute('disabled','disabled');
                    btn.style.display = 'none';
                    btnDel.style.display = 'none';
                    btnPost.removeAttribute('onclick');
                    btnPost.removeAttribute('src');
                    btnPost.setAttribute('src','images/'+theme+'/posted.png');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Akan dilakukan posting untuk sub unit '+
        getValue('blokalokasi_'+numRow1+'_'+numRow2)+
        ' pada tanggal '+getValue('tanggal_'+numRow1+'_'+numRow2)+
        '\nOnces posted the data can not be changed, are you sure?')) {
      //alert(param);
        post_response_text('log_slave_realisasispk_posting.php', param, respon);
    }
}

function cekReal(numRow1,numRow2) {
    var tbody = document.getElementById('detailBody_'+numRow1);
    var hk = document.getElementById('hk_'+numRow1).getAttribute('value');
    var hasil = document.getElementById('hasilkerjajumlah_'+numRow1).getAttribute('value');
    var jumlah = document.getElementById('jumlahrp_'+numRow1).getAttribute('value');
    
    var sumHk=0;var sumHasil=0;var sumJumlah=0;
    for(i in tbody.childNodes) {
        if(document.getElementById('hkrealisasi_'+numRow1+'_'+i)) {
            var tmpHk = document.getElementById('hkrealisasi_'+numRow1+'_'+i).value;
            var tmpHasil = document.getElementById('hasilkerjarealisasi_'+numRow1+'_'+i).value;
            var tmpJumlah = document.getElementById('jumlahrealisasi_'+numRow1+'_'+i).value;
            tmpJumlah = tmpJumlah.replace(",","");
            sumHk+=parseInt(tmpHk);sumHasil+=parseInt(tmpHasil);sumJumlah+=parseInt(tmpJumlah);
        }
    }
    
    var res = true;
    if(sumHk>hk) {
        document.getElementById('hkrealisasi_'+numRow1+'_'+numRow2).value = 0;
        res = false;
    }
    if(sumHasil>hasil) {
        document.getElementById('hasilkerjarealisasi_'+numRow1+'_'+numRow2).value = 0;
        res = false;
    }
    if(sumJumlah>jumlah) {
        document.getElementById('jumlahrealisasi_'+numRow1+'_'+numRow2).value = 0;
        res = false;
    }
    
    return res;
}

function calJumlah(numRow1,numRow2) {
    var hasilH = document.getElementById('hasilkerjajumlah_'+numRow1).getAttribute('value');
    var jumlahH = document.getElementById('jumlahrp_'+numRow1).getAttribute('value');
    var hasil = document.getElementById('hasilkerjarealisasi_'+numRow1+'_'+numRow2).value;
    var jumlah = document.getElementById('jumlahrealisasi_'+numRow1+'_'+numRow2);
    
    if(jumlahH>0 && parseFloat(hasilH)!=0) {
        jumlah.value = (parseFloat(hasil)/parseFloat(hasilH))*parseFloat(jumlahH);
        jumlah.value = _formatted(jumlah);
    } else {
        jumlah.value = 0;
    }
}
function lihatDataTimbangan(isi,numRow1,numRow2) {
    
   

    // Post to Slave
    if(isi=='' || isi==null ) {
        alert("Tiket belum dipilih !");
    }else{
        var isia = isi;
        param = "isi="+isia;
         param += "&nodo="+getValue('nodo_'+numRow1+'_'+numRow2);
        showDialog1('Data Timbangan',"<iframe frameborder=0 style='width:795px;height:400px' src='log_slave_realisasipk_detail_timbangan.php?"+param+"'></iframe>",'800','400','event');
        var dialog = document.getElementById('dynamic1');
        dialog.style.top = '100px';
        dialog.style.left = '15%';
    }

    
}