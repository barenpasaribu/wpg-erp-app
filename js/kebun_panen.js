var showPerPage = 10;
var rowData = {
    number: 0,
    tipe: '',
    notransaksi: ''
}

var headerElements = {
    notransaksi: '', nik: '', kodeorgafd: '', bjraktual: '', tahuntanam: '', tarif: '', turunhujan: '', norma: '', hasilkerja: '', brondolan: '', hasilkerjakg: '', jumlahlbhbasis: '', umr: '', premilebihbasis: '', premihadir: '', premirajin: '', premibrondol: '', upahpremi: '', upahkerja: '', penalti1: '', penalti2: '', penalti3: '', penalti4: '', penalti5: '', penalti6: '', penalti7: '', rupiahpenalty: '', luaspanen: ''
}  

function hitungDenda(obj) {
    var BM = document.getElementById('dendaBM'); var penalti1 = document.getElementById('penalti1');
    var BT = document.getElementById('dendaBT'); var penalti2 = document.getElementById('penalti2');
    var JT = document.getElementById('dendaJT'); var penalti3 = document.getElementById('penalti3');
    var PT = document.getElementById('dendaPT'); var penalti4 = document.getElementById('penalti4');
    var TD = document.getElementById('dendaTD'); var penalti5 = document.getElementById('penalti5');
    var TM = document.getElementById('dendaTM'); var penalti6 = document.getElementById('penalti6');
    var TP = document.getElementById('dendaTP'); var penalti7 = document.getElementById('penalti7');

    var dendaBM = (BM == null ? 0 : Number(BM.value.replace(/,/g, ''))) * Number(penalti1.value.replace(/,/g, ''));
    var dendaBT = (BT == null ? 0 : Number(BT.value.replace(/,/g, ''))) * Number(penalti2.value.replace(/,/g, ''));
    var dendaJT = (JT == null ? 0 : Number(JT.value.replace(/,/g, ''))) * Number(penalti3.value.replace(/,/g, ''));
    var dendaPT = (PT == null ? 0 : Number(PT.value.replace(/,/g, ''))) * Number(penalti4.value.replace(/,/g, ''));
    var dendaTD = (TD == null ? 0 : Number(TD.value.replace(/,/g, ''))) * Number(penalti5.value.replace(/,/g, ''));
    var dendaTM = (TM == null ? 0 : Number(TM.value.replace(/,/g, ''))) * Number(penalti6.value.replace(/,/g, ''));
    var dendaTP = (TP == null ? 0 : Number(TP.value.replace(/,/g, ''))) * Number(penalti7.value.replace(/,/g, ''));

    var totalDenda = dendaBM + dendaBT + dendaJT + dendaPT + dendaTD + dendaTM + dendaTP;
    document.getElementById('rupiahpenalty').value = Intl.NumberFormat().format(totalDenda);
}

function bindDataToElements(data, isreset = false) {
    var isreset__ = isreset;
    var keys = Object.keys(data);
    for (var i = 0; i <= keys.length - 1; i++) {
        var el = document.getElementById(keys[i]);
        var isarray = Array.isArray(data[keys[i]]);
        var value = (isarray ? '' : data[keys[i]] == null ? '' : data[keys[i]]);
        if (el != null) {
            if (keys[i]=='nik' || keys[i]=='tahuntanam') {
                el.value=value;
            }   else {  
                el.value = (isreset__ ? '' : isNaN(value)? value : Intl.NumberFormat().format(value) );
            }
        } 
    }
}
function showDetailData(data) {
    data.kodeorgafd = data.kodeorg; 
    bindDataToElements(data);
}
function deleteDetailData(notransaki,nik,kodeorg,index) {
    var index__ = index;
    if (confirm('Anda yakin ingin hapus data ini ?')) {
        var param = "notransaksi="+notransaki+"&nik="+nik+"&kodeorg="+kodeorg;
        function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        document.getElementById("prestasiTable").deleteRow(index__);
                        // window.location = 'setup_slave_blok_data.php' + (arrDest.length>0 ? '?'+arrDest[1]:'');
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
        post_response_text('kebun_slave_panen_detail.php?proses=delete', param, respon);
    }
}
function simpanData() {
    hitungDenda(null);
    var param = '';
    var keys = Object.keys(headerElements);
    for (var i = 0; i <= keys.length - 1; i++) {
        param = param + keys[i] + '=' + getValue(keys[i]) + (i == keys.length - 1 ? '' : '&');
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    // eval(con.responseText);
                    // showData();
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_panen_detail.php?proses=add', param, respon);
    // theFT.addFT('ftPrestasi','##nik##kodeorgafd##bjraktual##tahuntanam##tarif##turunhujan##norma##hasilkerja##brondolan##hasilkerjakg##jumlahlbhbasis##umr##premilebihbasis##premihadir##premirajin##premibrondol##upahpremi##upahkerja##penalti1##penalti2##penalti3##penalti4##penalti5##penalti6##penalti7##rupiahpenalty##luaspanen','##notransaksi','kebun_slave_panen_detail','##left##left##right##right##left##left##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right##right','Mode Ubah',false,'','##tahuntanam##bjraktual##upahkerja##upahpremi##rupiahpenalty##hasilkerjakg##norma##tarif##umr##jumlahlbhbasis##premirajin##premihadir##premibrondol##premilebihbasis##hasilkerja##turunhujan','##upahkerja=0','[]','','##');
}

// function getValue(id) {
//     var tmp = document.getElementById(id);

//     if(tmp) {

//         if(tmp.options) {
//             return tmp.options[tmp.selectedIndex].value;
//         } else if(tmp.nodeType=='checkbox') {
//             if(tmp.checked==true) {
//                 return 1;
//             } else {
//                 return 0;
//             }
//         } else {
//             return tmp.value;
//         }
//     } else {
//         return false;
//     }
// }

/* Search
 * Filtering Data
 */
function searchTrans(tipe, tipeVal) {
    var notrans = document.getElementById('sNoTrans');
    if (notrans.value == '') {
        var where = '[["' + tipe + '","' + tipeVal + '"]]';
    } else {
        var where = '[["notransaksi","' + notrans.value + '"],["' + tipe + '","' + tipeVal + '"]]';
    }
    goToPages(1, showPerPage, where);
}

/* Paging
 * Paging Data
 */
function defaultList(tipe) {
    tipe = 'PNN';
    goToPages(1, showPerPage, '[["tipetransaksi","' + tipe + '"]]');
}

function goToPages(page, shows, where) {
    if (typeof where != 'undefined') {
        var newWhere = where.replace(/'/g, '"');
    }
    var workField = document.getElementById('workField');
    var param = "page=" + page;
    param += "&shows=" + shows + "&tipe=PNN";
    if (typeof where != 'undefined') {
        param += "&where=" + newWhere;
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
}

function choosePage(obj, shows, where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal, shows, where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd(tipe) {
    var workField = document.getElementById('workField');
    var param = "tipe=" + tipe;

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
    var param = "notransaksi=" + trans.value;
    param += "&tipe=" + tipe;

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

function showEdit(num, tipe) {
    rowData.number = num;
    rowData.tipe = tipe;
    rowData.notransaksi = tipe;
    var workField = document.getElementById('workField');
    var trans = document.getElementById('notransaksi_' + num);
    var param = "numRow=" + num + "&notransaksi=" + trans.getAttribute('value');
    param += "&tipe=" + tipe;

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
    var param = "notransaksi=" + getValue('notransaksi') + "&kodeorg=" + getValue('kodeorg');
    param += "&tanggal=" + getValue('tanggal') + "&nikmandor=" + getValue('nikmandor');
    param += "&nikmandor1=" + getValue('nikmandor1') + "&nikasisten=" + getValue('nikasisten');
    param += "&keranimuat=" + getValue('keranimuat') + "&asistenpanen=" + getValue('asistenpanen');

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('notransaksi').value = con.responseText;
                    //alert('Added Data Header');
                    showEditFromAdd(tipe);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_operasional.php?proses=add&tipe=' + tipe, param, respon);
}

function editDataTable(tipe) {
    var param = "notransaksi=" + getValue('notransaksi') + "&kodeorg=" + getValue('kodeorg');
    param += "&tanggal=" + getValue('tanggal') + "&nikmandor=" + getValue('nikmandor');
    param += "&nikmandor1=" + getValue('nikmandor1') + "&nikasisten=" + getValue('nikasisten');
    param += "&keranimuat=" + getValue('keranimuat') + "&asistenpanen=" + getValue('asistenpanen');

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
    var tanggal = document.getElementById('tanggal').value;
    var param = "notransaksi=" + notrans + "&afdeling=" + afdeling + "&tanggal=" + tanggal;

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

    post_response_text('kebun_slave_panen_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var notrans = document.getElementById('notransaksi_' + num).getAttribute('value');
    var param = "notransaksi=" + notrans;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_' + num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_operasional.php?proses=delete', param, respon);
}

/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;

    if (tabBody.childNodes.length > 0) {
        for (i = 0; i < tabBody.childNodes.length; i++) {
            var tmp = document.getElementById('nourut_' + i);
            if (tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum) + 1;
}

function getLaporanPanen() {
    pt = document.getElementById('pt');
    gudang = document.getElementById('gudang');
    tgl1 = document.getElementById('tgl1').value;
    tgl2 = document.getElementById('tgl2').value;
    ptV = pt.options[pt.selectedIndex].value;
    gudangV = gudang.options[gudang.selectedIndex].value;

    param = 'pt=' + ptV + '&gudang=' + gudangV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    tujuan = 'kebun_laporanPanen.php';

    if (ptV == '') {
        alert('Company required');
        return;
    }

    else if (tgl1 == '' || tgl2 == '') {
        alert('Date required');
        return;
    }

    else if (tgl1.length != 10 || tgl2.length != 10) {
        alert('Date incorrect');
    }
    else
        post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel');
                    document.getElementById('container').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function dateExpired(d1) {//d1 is date to check in YYYY-MM-DD format setFullYear it is in YYYY,MM,DD. But it starts Months on 0 for Jan.
}

function days_between(tgl1, tgl2) {
    var x = tgl1.split("-");
    var y = tgl2.split("-");
    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24
    var date1 = new Date(x[2], (x[1] - 1), x[0]);

    var date2 = new Date(y[2], (y[1] - 1), y[0])

    // Calculate the difference in milliseconds
    var difference_ms = Math.abs(date1.getTime() - date2.getTime())

    // Convert back to days and return
    return Math.round(difference_ms / ONE_DAY)

}

function getLaporanPanen_1() {
    pt = document.getElementById('pt_1');
    unit = document.getElementById('unit_1');
    tgl1 = document.getElementById('tgl1_1').value;
    tgl2 = document.getElementById('tgl2_1').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    tujuan = 'kebun_laporanPanen_tanggal.php';

    jumlahhari = days_between(tgl1, tgl2);

    if (ptV == '') {
        alert('Comany required');
        return;
    }
    else if (tgl1 == '' || tgl2 == '') {
        alert('Date required');
        return;
    }
    else if (jumlahhari > 30) {
        alert('Number of days must less than 31 days');
        return;
    }
    else if (tgl1.length != 10 || tgl2.length != 10) {
        alert('Date incorrect');
        return;
    }
    else
        post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel_1');
                    document.getElementById('container_1').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getLaporanPanen_2() {
    pt = document.getElementById('pt_2');
    unit = document.getElementById('unit_2');
    pil = document.getElementById('pil_2');
    tgl1 = document.getElementById('tgl1_2').value;
    tgl2 = document.getElementById('tgl2_2').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;
    pilV = pil.options[pil.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2 + '&pil=' + pilV;
    tujuan = 'kebun_laporanPanen_orang.php';

    jumlahhari = days_between(tgl1, tgl2);

    if (ptV == '') {
        alert('Company required');
        return;
    }
    else if (tgl1 == '' || tgl2 == '') {
        alert('Date required');
        return;
    }
    else if (jumlahhari > 30) {
        alert('Number of days must less than 31 days');
        return;
    }
    else if (tgl1.length != 10 || tgl2.length != 10) {
        alert('Date incorrect');
        return;
    }
    else
        post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel_2');
                    document.getElementById('container_2').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getLaporanPanen_3() {
    pt = document.getElementById('pt_3');
    unit = document.getElementById('unit_3');
    tgl1 = document.getElementById('tgl1_3').value;
    tgl2 = document.getElementById('tgl2_3').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    tujuan = 'kebun_laporanPanen_spbwb.php';

    jumlahhari = days_between(tgl1, tgl2);

    if (ptV == '') {
        alert('Company required');
        return;
    }
    else if (tgl1 == '' || tgl2 == '') {
        alert('Date required');
        return;
    }
    //    else if(jumlahhari>30){
    //        alert('Jumlah hari lebih dari 31');
    //        return;
    //    }
    else if (tgl1.length != 10 || tgl2.length != 10) {
        alert('Tanggal salah');
        return;
    }
    else
        post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    showById('printPanel_3');
                    document.getElementById('container_3').innerHTML = con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function laporanKeExcel_1(ev, tujuan) {
    pt = document.getElementById('pt_1');
    unit = document.getElementById('unit_1');
    tgl1 = document.getElementById('tgl1_1').value;
    tgl2 = document.getElementById('tgl2_1').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    judul = 'Excel';
    printFile2(param, tujuan, judul, ev)
}

function laporanKeExcel_2(ev, tujuan) {
    pt = document.getElementById('pt_2');
    unit = document.getElementById('unit_2');
    tgl1 = document.getElementById('tgl1_2').value;
    tgl2 = document.getElementById('tgl2_2').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;
    pil = document.getElementById('pil_2');
    pilV = pil.options[pil.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2 + '&pil=' + pilV;
    judul = 'Excel';
    printFile2(param, tujuan, judul, ev)
}

function laporanKeExcel_3(ev, tujuan) {
    pt = document.getElementById('pt_3');
    unit = document.getElementById('unit_3');
    tgl1 = document.getElementById('tgl1_3').value;
    tgl2 = document.getElementById('tgl2_3').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    judul = 'Excel';
    printFile2(param, tujuan, judul, ev)
}

function laporanKePDF_1(ev, tujuan) {
    pt = document.getElementById('pt_1');
    unit = document.getElementById('unit_1');
    tgl1 = document.getElementById('tgl1_1').value;
    tgl2 = document.getElementById('tgl2_1').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    judul = 'Portable Document Format';
    printFile(param, tujuan, judul, ev)
}

function laporanKePDF_2(ev, tujuan) {
    pt = document.getElementById('pt_2');
    unit = document.getElementById('unit_2');
    tgl1 = document.getElementById('tgl1_2').value;
    tgl2 = document.getElementById('tgl2_2').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;
    pil = document.getElementById('pil_2');
    pilV = pil.options[pil.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    judul = 'Portable Document Format';
    if (pilV == 'fisik')
        printFile(param, tujuan, judul, ev)
    else alert('PDF report by labour only display result.')
}

function laporanKePDF_3(ev, tujuan) {
    pt = document.getElementById('pt_3');
    unit = document.getElementById('unit_3');
    tgl1 = document.getElementById('tgl1_3').value;
    tgl2 = document.getElementById('tgl2_3').value;
    ptV = pt.options[pt.selectedIndex].value;
    unitV = unit.options[unit.selectedIndex].value;

    param = 'pt=' + ptV + '&unit=' + unitV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    judul = 'Portable Document Format';
    printFile(param, tujuan, judul, ev)
}

function bersih_1() {
    document.getElementById('printPanel_1').style.display = 'none';
    document.getElementById('container_1').innerHTML = '';
}

function bersih_2() {
    document.getElementById('printPanel_2').style.display = 'none';
    document.getElementById('container_2').innerHTML = '';
}

function bersih_3() {
    document.getElementById('printPanel_3').style.display = 'none';
    document.getElementById('container_3').innerHTML = '';
}

function fisikKePDF(ev, tujuan) {
    pt = document.getElementById('pt');
    gudang = document.getElementById('gudang');
    tgl1 = document.getElementById('tgl1').value;
    tgl2 = document.getElementById('tgl2').value;
    pt = pt.options[pt.selectedIndex].value;
    gudang = gudang.options[gudang.selectedIndex].value;
    judul = 'Report PDF';
    param = 'pt=' + ptV + '&gudang=' + gudangV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    printFile(param, tujuan, judul, ev)
}
function fisikKeExcel(ev, tujuan) {
    pt = document.getElementById('pt');
    gudang = document.getElementById('gudang');
    tgl1 = document.getElementById('tgl1').value;
    tgl2 = document.getElementById('tgl2').value;
    pt = pt.options[pt.selectedIndex].value;
    gudang = gudang.options[gudang.selectedIndex].value;
    judul = 'Report Ms.Excel';
    param = 'pt=' + ptV + '&gudang=' + gudangV + '&tgl1=' + tgl1 + '&tgl2=' + tgl2;
    printFile(param, tujuan, judul, ev)
}
function fisikKeExcel2(ev, tujuan) {
    tgl = document.getElementById('tanggal').value;
    kdOrg = document.getElementById('kdOrg').value;
    judul = 'Report Ms.Excel';
    param = 'tgl=' + tgl + '&kdOrg=' + kdOrg + '&proses=excelDetail';
    printFile2(param, tujuan, judul, ev)
}
function printFile(param, tujuan, title, ev) {
    tujuan = tujuan + "?" + param;
    width = '700';
    height = '400';
    content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
    showDialog1(title, content, width, height, ev);
}


/* Posting Data
 */
function postingData(numRow) {
    var notrans = document.getElementById('notransaksi_' + numRow).getAttribute('value');
    var param = "notransaksi=" + notrans;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Posting Berhasil');
                    //javascript:location.reload(true);
                    x = document.getElementById('tr_' + numRow);
                    x.cells[8].innerHTML = '';
                    x.cells[9].innerHTML = '';
                    x.cells[10].innerHTML = "<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    if (confirm('Akan dilakukan posting untuk transaksi ' + notrans +
        '\nData tidak dapat diubah setelah ini. Anda yakin?')) {
        post_response_text('kebun_slave_panen_posting.php', param, respon);
    }
}

function updTahunTanam() {
    var tgl = document.getElementById('tanggal').value;
    var nik = document.getElementById('nik');
    var tahuntanam = document.getElementById('tahuntanam');
    var turunhujan = document.getElementById('turunhujan');
    var blk = document.getElementById('kodeorgafd');
    var norma = document.getElementById('norma').value;
    turunhujan = turunhujan.options[turunhujan.selectedIndex].value;
    var param = "kodeorg=" + blk.options[blk.selectedIndex].value + '&tanggal=' + tgl +'&karyawanid=' + nik.value + '&turunhujan=' + turunhujan;

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response

                    var res = con.responseText.split("####");
                    if ((res[4] < 5) && (res[4] > 0)) {
                        document.getElementById('turunhujan').disabled = false;
                    }
                    tahuntanam.value = res[0];
                    document.getElementById('bjraktual').value = res[1];
                    if (turunhujan == 'Tidak') {
                        document.getElementById('norma').value = res[2];
                        //document.getElementById('turunhujan').disabled = true;
                    }
                    else {
                        document.getElementById('norma').value = res[5];
                        //document.getElementById('turunhujan').disabled = false;
                    }
                    document.getElementById('hasilkerja').value = res[7];
                    document.getElementById('brondolan').value = res[8];
                    document.getElementById('penalti1').value = res[9];
                    document.getElementById('penalti2').value = res[10];
                    document.getElementById('penalti3').value = res[11];
                    document.getElementById('penalti5').value = res[12];
                    document.getElementById('penalti6').value = res[13];
                    //document.getElementById('norma').value = res[5];
                    if (norma != 0) {
                        blk.disabled = false;
                    }
                    updUpah();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_panen_detail.php?proses=updTahunTanam', param, respon);
}


function updBjr() {
    var nik = document.getElementById('ftPrestasi_nik').firstChild;
    var blk = document.getElementById('ftPrestasi_kodeorg').firstChild;
    var notransaksi = document.getElementById('notransaksi').value;
    var hasilkerja = document.getElementById('ftPrestasi_hasilkerja').firstChild.value;
    var hasilkerjakg = document.getElementById('ftPrestasi_hasilkerjakg').firstChild;
    var param = "kodeorg=" + blk.options[blk.selectedIndex].value + "&notransaksi=" + notransaksi + "&hasilkerja=" + hasilkerja;
    //    alert(param);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var res = con.responseText;
                    hasilkerjakg.value = res;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_panen_detail.php?proses=updBjr', param, respon);
}

function updUpah() {
    var nik = document.getElementById('nik');
    var umr = document.getElementById('umr');
    var upahpremi = document.getElementById('upahpremi').value;
    //var trf= document.getElementById('ftPrestasi_tarif').firstChild;
    var blk = document.getElementById('kodeorgafd');
    var tanggal = document.getElementById('tanggal').value;
    var bjrak = document.getElementById('bjraktual').value;
    var hslkrj = document.getElementById('hasilkerja').value;
    var tahuntanam = document.getElementById('tahuntanam').value;
    var hslkrjkg = document.getElementById('hasilkerjakg').value;
    var premihadir = document.getElementById('premihadir').value;
    var norma = document.getElementById('norma').value;
    var jumlahlbhbasis = document.getElementById('jumlahlbhbasis').value;
    var premilebihbasis = document.getElementById('premilebihbasis').value;
    var premirajin = document.getElementById('premirajin').value;
    var brondolan = document.getElementById('brondolan').value;
    var premibrondol = document.getElementById('premibrondol').value;
    var totalpremi = document.getElementById('upahpremi').value;
    var totupprem = document.getElementById('upahkerja').value;
    //var hslkrjkg = document.getElementById('ftPrestasi_hasilkerjakg').firstChild;
    var param = "nik=" + nik.options[nik.selectedIndex].value + '&tanggal=' + tanggal + '&hslkrjkg=' + hslkrjkg + '&jmlhJjg=' + hslkrj;
    param += "&bjraktual=" + bjrak + '&blok=' + blk.options[blk.selectedIndex].value + '&tahuntanam=' + tahuntanam + '&hslkrjkg=' + hslkrjkg + '&premihadir=' + premihadir + '&norma=' + norma + '&jumlahlbhbasis=' + jumlahlbhbasis + '&premilebihbasis=' + premilebihbasis + '&premirajin=' + premirajin + '&premibrondol=' + premibrondol + '&brondolan=' + brondolan + '&totalpremi=' + totalpremi + '&totupprem=' + totupprem;



    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    upahkerja.value = 0;
                    document.getElementById('norma').value = 0;
                    document.getElementById('upahpremi').value = 0;
                    document.getElementById('hasilkerjakg').value = 0;
                    document.getElementById('premihadir').value = 0;
                    document.getElementById('jumlahlbhbasis').value = 0;
                    document.getElementById('premirajin').value = 0;
                    document.getElementById('premibrondol').value = 0;
                    document.getElementById('upahpremi').value = 0;
                    document.getElementById('totupprem').value = 0;
                } else {
                    //=== Success Response
                    //alert(con.responseText);
                    var isi = con.responseText.split("####");
                    if (isi[5] <= 0) {
                        document.getElementById('jumlahlbhbasis').value = 0;
                    }
                    else {
                        document.getElementById('jumlahlbhbasis').value = Intl.NumberFormat().format(parseFloat(isi[5]).toFixed(0));
                    }
                    umr.value = Intl.NumberFormat().format(parseFloat(isi[0]).toFixed(2));
                    //upahkerja.value = parseFloat(isi[0]);
                    //document.getElementById('norma').value=isi[1];
                    //upahpremi.value = parseFloat(isi[2]).toFixed(2);
                    document.getElementById('premilebihbasis').value =  Intl.NumberFormat().format(parseFloat(isi[2]).toFixed(2));
                    //document.getElementById('hasilkerja').value = parseFloat(isi[3]).toFixed(0);

                    document.getElementById('hasilkerjakg').value = Intl.NumberFormat().format(parseFloat(isi[3]).toFixed(2));
                    document.getElementById('premihadir').value = Intl.NumberFormat().format(parseFloat(isi[4]).toFixed(2));
                    //document.getElementById('jumlahlbhbasis').value = parseFloat(isi[5]).toFixed(0);
                    document.getElementById('premirajin').value = Intl.NumberFormat().format(parseFloat(isi[6]).toFixed(2));
                    document.getElementById('premibrondol').value = Intl.NumberFormat().format(parseFloat(isi[7]).toFixed(2));
                    document.getElementById('upahpremi').value = Intl.NumberFormat().format(parseFloat(isi[8]).toFixed(2));
                    document.getElementById('upahkerja').value = Intl.NumberFormat().format(parseFloat(isi[9]).toFixed(2));
                    // if (norma!=0){
                    //     blk.disabled = true;
                    // }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_panen_detail.php?proses=updUpah', param, respon);
}
function updDenda(tipDenda) {
    strUrl2 = '';
    var tanggal = document.getElementById('tanggal').value;
    var bjrak = document.getElementById('bjraktual').value;
    var blk = document.getElementById('ftPrestasi_kodeorg').firstChild;
    var trf = document.getElementById('ftPrestasi_tarif').firstChild;
    var dend = document.getElementById('rupiahpenalty').value;
    var param = 'tanggal=' + tanggal + '&tipeDenda=' + tipDenda + '&totDenda=' + dend + '&blok=' + blk.options[blk.selectedIndex].value;
    param += "&tarif=" + trf.options[trf.selectedIndex].value + '&bjraktual=' + bjrak;
    for (d = 1; d < 8; d++) {
        try {
            if (strUrl2 != '') {
                strUrl2 += '&isiDt[' + d + ']=' + document.getElementById('penalti' + d).value;
            } else {
                strUrl2 += '&isiDt[' + d + ']=' + document.getElementById('penalti' + d).value;
            }
        }
        catch (e) { }
    }
    param += strUrl2;
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('rupiahpenalty').value = parseFloat(con.responseText).toFixed(2);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('kebun_slave_panen_detail.php?proses=updDenda', param, respon);
}
function detailPDF(numRow, ev) {
    // Prep Param
    var notransaksi = document.getElementById('notransaksi_' + numRow).getAttribute('value');
    param = "proses=pdf&tipe=PNN" + "&notransaksi=" + notransaksi;

    showDialog1('Print PDF', "<iframe frameborder=0 style='width:795px;height:400px'" +
        " src='kebun_slave_operasional_print_detail_panen.php?" + param + "'></iframe>", '800', '400', ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}


function printPDF(ev, tipe) {
    // Prep Param
    param = "proses=pdf&tipe=PNN";

    showDialog1('Print PDF', "<iframe frameborder=0 style='width:795px;height:400px'" +
        " src='kebun_slave_operasional_print.php?" + param + "'></iframe>", '800', '400', ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function getKbn() {
    //alert('masuk');
    pt = document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    param = 'pt=' + pt + '&proses=getKbn';
    tujuan = 'kebun_slave_2panen.php';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('gudang').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text(tujuan, param, respon);

}

function getKbn_1() {
    //    alert('masuk');
    pt = document.getElementById('pt_1').options[document.getElementById('pt_1').selectedIndex].value;
    param = 'pt=' + pt + '&proses=getKbn';
    tujuan = 'kebun_slave_2panen.php';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('unit_1').innerHTML = con.responseText;
                    bersih_1();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respon);
}

function getKbn_2() {
    //    alert('masuk');
    pt = document.getElementById('pt_2').options[document.getElementById('pt_2').selectedIndex].value;
    param = 'pt=' + pt + '&proses=getKbn';
    tujuan = 'kebun_slave_2panen.php';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('unit_2').innerHTML = con.responseText;
                    bersih_2();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text(tujuan, param, respon);

}

function getKbn_3() {
    //    alert('masuk');
    pt = document.getElementById('pt_3').options[document.getElementById('pt_3').selectedIndex].value;
    param = 'pt=' + pt + '&proses=getKbn';
    tujuan = 'kebun_slave_2panen.php';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('unit_3').innerHTML = con.responseText;
                    bersih_3();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text(tujuan, param, respon);

}

function zDetail(ev, tujuan, passParam) {
    var passP = passParam.split('##');
    var param = "";
    for (i = 0; i < passP.length; i++) {
        // var tmp = document.getElementById(passP[i]);
        a = i;
        param += "&" + passP[a] + "=" + passP[i + 1];
    }
    param += '&proses=getDetail';
    judul = "Detail ";
    //alert(param);
    printFile(param, tujuan, judul, ev)
}
function printFile(param, tujuan, title, ev) {
    tujuan = tujuan + "?" + param;
    width = '800';
    height = '550';
    content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
    showDialog1(title, content, width, height, ev);
}
function printFile2(param, tujuan, title, ev) {
    tujuan = tujuan + "?" + param;
    width = '450';
    height = '350';
    content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
    showDialog2(title, content, width, height, ev);
}

function filterKaryawan(val) {

    if (val != 'null')
        param = 'afd=' + val + '&tipe=afdeling';
    else {
        val = document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
        param = 'afd=' + val + '&tipe=unit';
    }
    post_response_text('kebun_slave_panen_detail.php?proses=gatKarywanAFD', param, respon);


    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('nik').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

/**
 * allPtKaryawan
 * Show semua karyawan di dalam 1 PT
 * @param	string	idElement	ID dari element yang akan direplace optionnya
 * @param	object	check		Object Checkbox
 */
function allPtKaryawan(idElement, check) {
    kodeorg = document.getElementById('kodeorg').value;
    if (check.checked == true) {
        param = 'kodeorg=' + kodeorg + '&tipe=all';
    } else {
        param = 'kodeorg=' + kodeorg + '&tipe=default';
    }
    post_response_text('kebun_slave_operasional_karyawan.php?proses=getAllPt', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById(idElement).innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}