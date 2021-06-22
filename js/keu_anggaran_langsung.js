/* Function getKegiatan
 * Fungsi untuk kegiatan sesuai dengan kelompok
 * I : element kelompok, id target kegiatan diisi
 * O : List Kegiatan terupdate
 */
function getKegiatan(obj,idTarget) {
    var klp = obj.options[obj.selectedIndex].value;
    var keg = document.getElementById(idTarget);
    keg.options.length = 0;
    
    var param = "klp="+klp;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval(con.responseText);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('slave_link_klp_kegiatan.php', param, respon);
}

/* Function addHeader
 * Fungsi untuk menampilkan pop up form tambah header
 * I : event
 * O : Pop up form tambah header
 */
function addHeader(event) {
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Tambah Header',con.responseText,'500','300',event);
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
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=addHeader', param, respon);
}

/* Function editHeader
 * Fungsi untuk menampilkan pop up form edit header
 * I : event
 * O : Pop up form edit header
 */
function editHeader(event,field,val) {
    var fieldJs = field.split('##');
    var valueJs = val.split('##');
    
    var param = "";
    for(i=1;i<fieldJs.length;i++) {
        if(i==1) {
            param += fieldJs[i]+"="+valueJs[i];
        } else {
            param += "&"+fieldJs[i]+"="+valueJs[i];
        }
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //showDialog1('Edit Header',con.responseText,'500','300',event);
                    var dialogCon = document.getElementById('dialogCon');
                    var dialog = document.getElementById('dynamic1');
                    var title = dialog.firstChild;
                    title.innerHTML = "Edit Header";
                    dialogCon.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=editHeader', param, respon);
}

/* Function showHeadList
 * Fungsi untuk menampilkan pop up daftar header anggaran
 * I : event
 * O : Pop up daftar header
 */
function showHeadList(event) {
    var param = "";
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var dialogCon = document.getElementById('dialogCon');
                    if(dialogCon) {
                        var dialog = document.getElementById('dynamic1');
                        var title = dialog.firstChild;
                        title.innerHTML = "Daftar Tabel";
                        dialogCon.innerHTML = con.responseText
                    } else {
                        showDialog1('Daftar Tabel',"<div id='dialogCon'>"+
                            con.responseText+"</div>",'700','300',event);
                        var dialog = document.getElementById('dynamic1');
                        dialog.style.top = '10%';
                        dialog.style.left = '15%';
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=showList', param, respon);
}

/* Function addDataHeader
 * Fungsi untuk melakukan proses tambah header anggaran
 * I :
 * P : Proses tambah data header
 * O : Data ditampilkan di fieldset header, tampilkan daftar detail pada fieldset detail
 */
function addDataHeader(field) {
    var fieldJs = field.split('##');
    
    // Setting Param
    var kodeorg = document.getElementById('kodeorg');
    var tutup = document.getElementById('tutup');
    var param = "nameOrg="+kodeorg.options[kodeorg.selectedIndex].text;
    for(i=1;i<fieldJs.length;i++) {
        var tmp = document.getElementById(fieldJs[i]);
        var value = "";
        
        // Get Value
        if(!tmp) {
            alert('DOM Definition Error : ID = '+fieldJs[i]);
            return;
        }
        
        if(tmp.getAttribute('type')=='checkbox') {
            if(tmp.checked) {
                value = 1;
            } else {
                value = 0;
            }
        } else {
            value = tmp.value;
        }
        
        // Valid Data
        if(value==='') {
            alert('Data '+fieldJs[i]+' tidak boleh kosong');
            return;
        }
        
        param += "&"+fieldJs[i]+"="+value;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval(con.responseText);
                    closeDialog();
                    if(tutup.checked==true) {
                        showDetail(field,1);
                    } else {
                        showDetail(field,0);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=add', param, respon);
}

/* Function editDataHeader
 * Fungsi untuk melakukan proses edit header anggaran
 * I :
 * P : Proses edit data header
 * O : Data ditampilkan di fieldset header, tampilkan daftar detail pada fieldset detail
 */
function editDataHeader(field) {
    var fieldJs = field.split('##');
    
    // Setting Param
    var kodeorg = document.getElementById('kodeorg');
    var tutup = document.getElementById('tutup');
    var param = "nameOrg="+kodeorg.options[kodeorg.selectedIndex].text;
    for(i=1;i<fieldJs.length;i++) {
        var tmp = document.getElementById(fieldJs[i]);
        var value = "";
        
        // Get Value
        if(tmp.getAttribute('type')=='checkbox') {
            if(tmp.checked) {
                value = 1;
            } else {
                value = 0;
            }
        } else {
            value = tmp.value;
        }
        
        // Valid Data
        if(value==='') {
            alert('Data '+fieldJs[i]+' tidak boleh kosong');
            return;
        }
        
        param += "&"+fieldJs[i]+"="+value;
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval(con.responseText);
                    closeDialog();
                    if(tutup.checked==true) {
                        showDetail(field,1);
                    } else {
                        showDetail(field,0);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=edit', param, respon);
}

/* Function deleteHeader
 * Fungsi untuk menghapus data header beserta detailnya
 * I : baris pada tabel, field, nilai field
 * O : row terhapus dari tabel
 */
function deleteHeader(num,field,val) {
    if(confirm('Menghapus header akan menghapus keseluruhan detail\nAnda yakin ?')) {
        var kodeorg = document.getElementById('col_kodeorg_'+num).innerHTML;
        var kodeanggaran = document.getElementById('col_kodeanggaran_'+num).innerHTML;
        var tahun = document.getElementById('col_tahun_'+num).innerHTML;
        
        var param = "kodeorg="+kodeorg+"&kodeanggaran="+kodeanggaran+
            "&tahun="+tahun;
        
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        // Success Response
                        var detailC = document.getElementById('detailContainer');
                        var tabTr = document.getElementById('edit_tr_'+num);
                        tabTr.style.display='none';
                        clearHeadForm();
                        detailC.innerHTML = '';
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        
        post_response_text('keu_slave_anggaran_langsung_header.php?proses=delete', param, respon);
    }
}

/* Function clearHeadForm
 * Fungsi untuk mengosongkan form header
 * I : 
 * O : Tampilan header kosong
 */
function clearHeadForm() {
    var field = new Array();
    field.push('kodeorg','nameorg','kodeanggaran','keterangan','tipeanggaran');
    field.push('tahun','matauang','jumlah','revisi');
    
    for(i=0;i<field.length;i++) {
        document.getElementById('main_'+field[i]).value = '';
    }
    document.getElementById('main_tutup').checked=false;
}

/* Function showingDetail
 * Fungsi untuk menampilkan header dalam bentuk disabled dan tampilkan detail
 * I : list field dari header, list nilai dari header
 * O : Tampilan header dan detail
 */
function showingDetail(field,val,closed) {
    var fieldJs = field.split('##');
    var valueJs = val.split('##');
    
    var param = "";
    for(i=1;i<fieldJs.length;i++) {
        if(i==1) {
            param += fieldJs[i]+"="+valueJs[i];
        } else {
            param += "&"+fieldJs[i]+"="+valueJs[i];
        }
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval(con.responseText);
                    closeDialog();
                    showDetail(field,closed);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_header.php?proses=showHead', param, respon);
}

/* Function showDetail
 * Fungsi untuk menampilkan detail
 * I : list field dari header
 * O : Tampilan detail
 */
function showDetail(field,closed) {
    if(closed=='undefined') {
        closed = 0;
    }
    var kodeorg = document.getElementById('main_kodeorg').value;
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var tahun = document.getElementById('main_tahun').value;
    var param ="closed="+closed+"&kodeorg="+kodeorg;
    param += "&kodeanggaran="+kodeanggaran;
    param += "&tahun="+tahun;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var detailCon = document.getElementById('detailContainer');
                    detailCon.innerHTML = con.responseText;
                    
                    // Enable Proses
                    document.getElementById('prosesKelompok').removeAttribute('disabled');
                    document.getElementById('prosesKodekegiatan').removeAttribute('disabled');
                    document.getElementById('autoFillBtn').removeAttribute('disabled');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=showDetail', param, respon);
}

/* Function addDetail
 * Fungsi untuk menampilkan form tambah detail
 * I : 
 * O : Tampilan form header
 */
function addDetail(event) {
    var kodeorg = document.getElementById('main_kodeorg');
    var tipeanggaran = document.getElementById('main_tipeanggaran').value;
    var param ="kodeorg="+kodeorg.value+"&tipeanggaran="+tipeanggaran;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var dialogConDet = document.getElementById('dialogConDet');
                    if(dialogConDet) {
                        var dialog = document.getElementById('dynamic1');
                        var title = dialog.firstChild;
                        title.innerHTML = "Tambah Detail";
                        dialogConDet.innerHTML = con.responseText;
                    } else {
                        showDialog1('Tambah Detail',"<div id='dialogConDet'>"+
                            con.responseText+"</div>",'770','310',event);
                        var dialog = document.getElementById('dynamic1');
                        dialog.style.top = '10%';
                        dialog.style.left = '15%';
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=addDetail', param, respon);
}

/* Function editDetail
 * Fungsi untuk menampilkan form edit detail
 * I : 
 * O : Tampilan form edit detail
 */
function editDetail(num,event,field,val) {
    var kodeorg = document.getElementById('main_kodeorg').value;
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var tipeanggaran = document.getElementById('main_tipeanggaran').value;
    var tahun = document.getElementById('main_tahun').value;
    var fieldJs = field.split("##");
    var valJs = val.split('##');
    
    var param = "numRow="+num+"&kodeorg="+kodeorg+"&kodeanggaran="+kodeanggaran+
        "&tahun="+tahun+"&tipeanggaran="+tipeanggaran;
    for(i=1;i<fieldJs.length;i++) {
        param += "&"+fieldJs[i]+"="+valJs[i];
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var dialogConDet = document.getElementById('dialogConDet');
                    if(dialogConDet) {
                        var dialog = document.getElementById('dynamic1');
                        var title = dialog.firstChild;
                        title.innerHTML = "Edit Detail";
                        dialogConDet.innerHTML = con.responseText;
                    } else {
                        showDialog1('Edit Detail',"<div id='dialogConDet'>"+
                            con.responseText+"</div>",'770','310',event);
                        var dialog = document.getElementById('dynamic1');
                        dialog.style.top = '10%';
                        dialog.style.left = '15%';
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=editDetail', param, respon);
}

/* Function addDataDetail
 * Fungsi untuk menambah data detail
 * I : 
 * O : Tampilan form header
 */
function addDataDetail() {
    var kodeorg = document.getElementById('main_kodeorg').value;
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var tahun = document.getElementById('main_tahun').value;
    var kodebagian = document.getElementById('kodebagian').value;
    var kodekegiatan = document.getElementById('kodekegiatan').value;
    var kelompok = document.getElementById('kelompok').value;
    var noaruskas = document.getElementById('noaruskas').value;
    var kodebarang = document.getElementById('kodebarang').value;
    var revisi = document.getElementById('main_revisi').value;
    var hargasatuan = document.getElementById('hargasatuan').value;
    var jumlah = document.getElementById('jumlah').value;
    var jan = document.getElementById('jan').value;
    var peb = document.getElementById('peb').value;
    var mar = document.getElementById('mar').value;
    var apr = document.getElementById('apr').value;
    var mei = document.getElementById('mei').value;
    var jun = document.getElementById('jun').value;
    var jul = document.getElementById('jul').value;
    var agt = document.getElementById('agt').value;
    var sep = document.getElementById('sep').value;
    var okt = document.getElementById('okt').value;
    var nov = document.getElementById('nov').value;
    var dec = document.getElementById('dec').value;
    
    if(kodebarang=='') {
        alert('Kode Barang harus diisi');
        return;
    }
    
    var numRow = 0;
    while(document.getElementById('detail_tr_'+numRow)) {
        numRow++;
    }
    
    var param = "numRow="+numRow;
    param += "&kodeorg="+kodeorg;param += "&kodeanggaran="+kodeanggaran;
    param += "&tahun="+tahun;
    param += "&kodebagian="+kodebagian;param += "&kodekegiatan="+kodekegiatan;
    param += "&kelompok="+kelompok;param += "&noaruskas="+noaruskas;param += "&kodebarang="+kodebarang;
    param += "&revisi="+revisi;param += "&hargasatuan="+hargasatuan;
    param += "&jumlah="+jumlah;
    param += "&jan="+jan;param += "&peb="+peb;param += "&mar="+mar;
    param += "&apr="+mar;param += "&mei="+mei;param += "&jun="+jun;
    param += "&jul="+jul;param += "&agt="+agt;param += "&sep="+sep;
    param += "&okt="+okt;param += "&nov="+nov;param += "&dec="+dec;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    closeDialog();
                    var tBody = document.getElementById('bodyDetail');
                    tBody.innerHTML += con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=add', param, respon);
}

/* Function editDataDetail
 * Fungsi untuk mengubah data detail
 * I : 
 * O : Tampilan form header
 */
function editDataDetail(num) {
    var kodeorg = document.getElementById('main_kodeorg').value;
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var tahun = document.getElementById('main_tahun').value;
    var kodebagian = document.getElementById('kodebagian').value;
    var kodekegiatan = document.getElementById('kodekegiatan').value;
    var kelompok = document.getElementById('kelompok').value;
    var noaruskas = document.getElementById('noaruskas').value;
    var kodebarang = document.getElementById('kodebarang').value;
    var revisi = document.getElementById('main_revisi').value;
    var hargasatuan = document.getElementById('hargasatuan').value;
    var jumlah = document.getElementById('jumlah').value;
    var jan = document.getElementById('jan').value;
    var peb = document.getElementById('peb').value;
    var mar = document.getElementById('mar').value;
    var apr = document.getElementById('apr').value;
    var mei = document.getElementById('mei').value;
    var jun = document.getElementById('jun').value;
    var jul = document.getElementById('jul').value;
    var agt = document.getElementById('agt').value;
    var sep = document.getElementById('sep').value;
    var okt = document.getElementById('okt').value;
    var nov = document.getElementById('nov').value;
    var dec = document.getElementById('dec').value;
    
    var param = "numRow="+num;
    param += "&kodeorg="+kodeorg;param += "&kodeanggaran="+kodeanggaran;
    param += "&tahun="+tahun;
    param += "&kodebagian="+kodebagian;param += "&kodekegiatan="+kodekegiatan;
    param += "&kelompok="+kelompok;param += "&noaruskas="+noaruskas;param += "&kodebarang="+kodebarang;
    param += "&revisi="+revisi;param += "&hargasatuan="+hargasatuan;
    param += "&jumlah="+jumlah;
    param += "&jan="+jan;param += "&peb="+peb;param += "&mar="+mar;
    param += "&apr="+apr;param += "&mei="+mei;param += "&jun="+jun;
    param += "&jul="+jul;param += "&agt="+agt;param += "&sep="+sep;
    param += "&okt="+okt;param += "&nov="+nov;param += "&dec="+dec;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    closeDialog();
                    var tabTr = document.getElementById('detail_tr_'+num);
                    tabTr.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=edit', param, respon);
}

/* Function deleteDetail
 * Fungsi untuk menghapus data detail
 * I : 
 * O : Tampilan form header
 */
function deleteDetail(num,field,val) {
    var kodeorg = document.getElementById('main_kodeorg').value;
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var tahun = document.getElementById('main_tahun').value;
    var fieldJs = field.split("##");
    var valJs = val.split('##');
    
    var param = "kodeorg="+kodeorg+"&kodeanggaran="+kodeanggaran+"&tahun="+tahun;
    for(i=1;i<fieldJs.length;i++) {
        param += "&"+fieldJs[i]+"="+valJs[i];
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var tabTr = document.getElementById('detail_tr_'+num);
                    tabTr.style.display='none';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Data akan dihapus\nAnda yakin?')) {
        post_response_text('keu_slave_anggaran_langsung_detail.php?proses=delete', param, respon);
    }
}

/* Function updateQty
 * Fungsi untuk update total kuantitas
 */
function updateQty(obj) {
    var field = new Array();
    field.push('jan','peb','mar','apr','mei','jun');
    field.push('jul','agt','sep','okt','nov','dec');
    
    var jumlah = document.getElementById('jumlah');
    var jml = 0;
    for(i=0;i<field.length;i++) {
        tmp = document.getElementById(field[i]).value;
        jml += Number(tmp);
    }
    if(jumlah.value < jml) {
        alert('Jumlah melebihi jumlah maksimal');
        obj.value=0;
    }
}

/* function autoFill
 * Fungsi untuk secara otomatis mengisi detail dengan kode barang yang sesuai
 */
function autoFill() {
    var kodeanggaran = document.getElementById('main_kodeanggaran').value;
    var kodeorg = document.getElementById('main_kodeorg').value;
    var revisi = document.getElementById('main_revisi').value;
    var matauang = document.getElementById('main_matauang').value;
    var tahun = document.getElementById('main_tahun').value;
    var prosesKel = document.getElementById('prosesKelompok');
    var prosesKodekegiatan = document.getElementById('prosesKodekegiatan');
    var body = document.getElementById('bodyDetail');
    var autoFillBtn = document.getElementById('autoFillBtn');
    var numRow = 0;
    while(document.getElementById('detail_tr_'+numRow)) {
        numRow++;
    }
    
    // Disable Button
    autoFillBtn.setAttribute('disabled','disabled');
    
    var param = "kodeanggaran="+kodeanggaran+"&kodebagian="+kodeorg;
    param += "&kodeorg="+kodeorg;
    param += "&kodekegiatan="+prosesKodekegiatan.options[prosesKodekegiatan.selectedIndex].value;
    param += "&kelompok="+prosesKel.options[prosesKel.selectedIndex].value;
    param += "&revisi="+revisi;
    param += "&matauang="+matauang+"&tahun="+tahun+"&numRow="+numRow;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    if(con.responseText!='') {
                        body.innerHTML += con.responseText;
                    } else {
                        alert('Tidak ada Norma untuk Kegiatan tersebut');
                    }
                }
                autoFillBtn.removeAttribute('disabled');
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_detail.php?proses=autoFill', param, respon);
}

/* Function updBarang
 * Update Barang yang sesuai dengan kode VHC
 */
function updBarang() {
    var vhc = document.getElementById('kodevhc');
    var barang = document.getElementById('kodebarang');
    var hargasatuan = document.getElementById('hargasatuan');
    
    var param = "kodevhc="+vhc.options[vhc.selectedIndex].value;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    eval('var res = '+con.responseText);
                    barang.value = res['vhc'];
                    hargasatuan.value = res['harga'];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_anggaran_langsung_vhcbarang.php', param, respon);
}