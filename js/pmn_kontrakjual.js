function getCatatan() { 
    komoditi        = document.getElementById('kdBrg').value;
    ppn             = document.getElementById('ppn').value;
    isPPn           = 0;
    if (ppn > 0) {
        isPPn = 1;
    }
    param =     'proses=getCatatan';
    param +=    '&komoditi=' + kdBrg;
    param +=    '&isPPn=' + isPPn;
    tujuan =    'pmn_slave_get_save_data.php';

    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    data = JSON.parse(con.responseText);
                    if (data == "kosong") {
                        document.getElementById('cttn1').value = "";
                        document.getElementById('cttn2').value = "";
                        document.getElementById('cttn3').value = "";
                        document.getElementById('cttn4').value = "";
                        document.getElementById('cttn5').value = "";
                        document.getElementById('cttn6').value = "";
                        document.getElementById('cttn7').value = "";
                        document.getElementById('cttn8').value = "";
                        document.getElementById('cttn9').value = "";
                        document.getElementById('cttn10').value = "";
                        document.getElementById('cttn11').value = "";
                        document.getElementById('cttn12').value = "";
                        document.getElementById('cttn13').value = "";
                        document.getElementById('cttn14').value = "";
                        document.getElementById('cttn15').value = "";
                    }else{
                        document.getElementById('cttn1').value = data.catatan1;
                        document.getElementById('cttn2').value = data.catatan2;
                        document.getElementById('cttn3').value = data.catatan3;
                        document.getElementById('cttn4').value = data.catatan4;
                        document.getElementById('cttn5').value = data.catatan5;
                        document.getElementById('cttn6').value = data.catatan6;
                        document.getElementById('cttn7').value = data.catatan7;
                        document.getElementById('cttn8').value = data.catatan8;
                        document.getElementById('cttn9').value = data.catatan9;
                        document.getElementById('cttn10').value = data.catatan10;
                        document.getElementById('cttn11').value = data.catatan11;
                        document.getElementById('cttn12').value = data.catatan12;
                        document.getElementById('cttn13').value = data.catatan13;
                        document.getElementById('cttn14').value = data.catatan14;
                        document.getElementById('cttn15').value = data.catatan15;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function genNoDO() { 
    kdPt        = document.getElementById('kdPt').value;
    tlgKntrk    = document.getElementById('tlgKntrk').value;
    kdBrg       = document.getElementById('kdBrg').value;
    noDo        = document.getElementById('noDo').value;
    
    param =     'method=getNoDO';
    param +=    '&kdBrg=' + kdBrg;
    param +=    '&kdPt=' + kdPt;
    param +=    '&tlgKntrk=' + tlgKntrk;
    param +=    '&noDo=' + noDo;
    tujuan =    'pmn_kontrakjual_slave.php';

    if(noDo){
        post_response_text(tujuan, param, respog);
    }

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    nomordo = con.responseText;
                    document.getElementById('noDo').value = trim(nomordo);
                    getSatuanBarang();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getSatuanBarang(){
    kdBrg = document.getElementById('kdBrg').value;
    param = 'kdBrg=' + kdBrg + '&method=getSatuan';

    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('stn').innerHTML = con.responseText;
                   // getCatatan();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

// JavaScript Document
function hitungx() {
    document.getElementById('tBlg').innerHTML = "";
    document.getElementById('total').innerHTML = 0;
    harga = parseInt(document.getElementById('HrgStn').value);
    ppn = parseFloat(document.getElementById('ppn').value);
    jml = parseInt(document.getElementById('jmlh').value);

    if (ppn == '0' && ppn == '') {
        document.getElementById('total').value = harga * jml;
        document.getElementById('total').innerHTML = harga * jml;
        document.getElementById('keterangan').innerHTML = 'Exclude PPN';
    } else {
        document.getElementById('total').value = (harga + (harga * ppn / 100)) * jml;
        document.getElementById('total').innerHTML = (harga + (harga * ppn / 100)) * jml;
        document.getElementById('keterangan').innerHTML = 'Include PPN';
    }
    param = 'method=perkalian';
    if (ppn == '0' && ppn == '') {
        param += '&nilai=' + (harga * jml);
    } else {
        param += '&nilai=' + (harga + (harga * ppn / 100)) * jml;
    }
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('tBlg').innerHTML = con.responseText;
                    //getCatatan();
                    copyFromLast();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loadNewData() {
    param = 'method=LoadNew';
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('containerlist').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function setStatus(nokontrak, status) {
    var status__= (status=='Aktif' ? 'menon aktifkan' : 'mengaktifkan');
    if (confirm('Anda yakin ingin ' + status__ + ' kontrak ini ?')) {
        param = 'method=setStatus&noKntrk=' + nokontrak + '&status=' + status;
        tujuan = 'pmn_kontrakjual_slave.php';
        post_response_text(tujuan, param, respog);

        function respog() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                        //alert(con.responseText);
                        //document.getElementById('containerlist').innerHTML=con.responseText;
                        do_load('pmn_kontrakjual');
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
    }
}

function cekDate() {
    tglKrm = document.getElementById('tglKrm').value;
    tglSd = document.getElementById('tglSd').value;
    param = 'method=cekDate' + '&tglKrm=' + tglKrm + '&tglSd=' + tglSd;
    //alert(param);
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    if (con.responseText == 'a') {
                        alert('Date not valid');
                        document.getElementById('tglSd').value = '';
                    }

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}



function posting(noKntrk) {
    param = 'method=posting' + '&noKntrk=' + noKntrk;
    tujuan = 'pmn_kontrakjual_slave.php';
    if (confirm('Posting??'))
        post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Posting Contract Number & DO Number To PC eWeighbridge in Mill already completed.');
                    loadNewData()
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}

function loadNewData() {
    param = 'method=LoadNew';
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('containerlist').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function cariBast(num) {
    param = 'method=LoadNew';
    param += '&page=' + num;
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('containerlist').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveKP() {
    noKntrk = document.getElementById('noKtrk').value;
    custid = document.getElementById('custId').value;
    tglkntr = document.getElementById('tlgKntrk').value;
    kdbrg = document.getElementById('kdBrg').value;
    satuan = document.getElementById('stn').value;
    HrgStn = document.getElementById('HrgStn').value;
    tBlg = document.getElementById('tBlg').innerHTML;
    qty = document.getElementById('jmlh').value;
    tglKrm = document.getElementById('tglKrm').value;
    tglSd = document.getElementById('tglSd').value;
    tlransi = document.getElementById('tlransi').value;
    noDo = document.getElementById('noDo').value;
    kualitas = document.getElementById('kualitas').value;
    syrtByr = document.getElementById('syrtByr').value;
    syrtByr2 = document.getElementById('syrtByr2').value;
    tndtng = document.getElementById('tndtng').value;
    tanda_tangan_pembeli = document.getElementById('tanda_tangan_pembeli').value;
    tmbngn = document.getElementById('tmbngn').value;
    cttn1 = document.getElementById('cttn1').value;
/*    cttn2 = document.getElementById('cttn2').value;
    cttn3 = document.getElementById('cttn3').value;
    cttn4 = document.getElementById('cttn4').value;
    cttn5 = document.getElementById('cttn5').value;
    cttn6 = document.getElementById('cttn6').value;
    cttn7 = document.getElementById('cttn7').value;
    cttn8 = document.getElementById('cttn8').value;
    cttn9 = document.getElementById('cttn9').value;
    cttn10 = document.getElementById('cttn10').value;
    cttn11 = document.getElementById('cttn11').value;
    cttn12 = document.getElementById('cttn12').value;
    cttn13 = document.getElementById('cttn13').value;
    cttn14 = document.getElementById('cttn14').value;
    cttn15 = document.getElementById('cttn15').value;
    othCttn = document.getElementById('othCttn').value;
*/    lamamuat = document.getElementById('lamamuat').value;
    tipemuat = document.getElementById('tipemuat').value;
    keterangan_muat = document.getElementById('keterangan_muat').value;
    pelabuhan = document.getElementById('pelabuhan').value;
    demurage = document.getElementById('demurage').value;
    kdPt = document.getElementById('kdPt').value;
    kurs = document.getElementById('kurs').value
    ppn = document.getElementById('ppn').value
    total = document.getElementById('total').value
    met = document.getElementById('method').value;
    transporter = document.getElementById('transporter').value;

    var reskualitas = kualitas.replace("&", "dan");
    var rescat1 = cttn1.replace(/& /g, "dan ");
/*    var rescat2 = cttn2.replace("&", "dan");
    var rescat3 = cttn3.replace("&", "dan");
    var rescat4 = cttn4.replace("&", "dan");
    var rescat5 = cttn5.replace("&", "dan");
    var rescat6 = cttn6.replace("&", "dan");
    var rescat7 = cttn7.replace("&", "dan");
    var rescat8 = cttn8.replace("&", "dan");
    var rescat9 = cttn9.replace("&", "dan");
    var rescat10 = cttn10.replace("&", "dan");
    var rescat11 = cttn11.replace("&", "dan");
    var rescat12 = cttn12.replace("&", "dan");
    var rescat13 = cttn13.replace("&", "dan");
    var rescat14 = cttn14.replace("&", "dan");
    var rescat15 = cttn15.replace("&", "dan");
*/
    var resbyr1 = syrtByr.replace("&", "dan");
    var resbyr2 = syrtByr2.replace("&", "dan");

    var restimb = tmbngn.replace("&", "dan");

    param = 'noKntrk=' + noKntrk + '&custId=' + custid + '&tlgKntrk=' + tglkntr + '&kdBrg=' + kdbrg + '&HrgStn=' + HrgStn;
    param += '&satuan=' + satuan + '&tBlg=' + tBlg + '&qty=' + qty + '&tglKrm=' + tglKrm + '&tglSd=' + tglSd;
    param += '&kualitasxx=' + reskualitas + '&syrtByr=' + resbyr1 + '&tmbngn=' + restimb;
    param += '&syrtByr2=' + resbyr2;
    param += '&lamamuat=' + lamamuat;
    param += '&tipemuat=' + tipemuat;
    param += '&keterangan_muat=' + keterangan_muat;
    param += '&pelabuhan=' + pelabuhan;
    param += '&demurage=' + demurage;
    param += '&cttn1=' + rescat1 ;
    param += '&method=' + met + '&tndtng=' + tndtng + '&tanda_tangan_pembeli=' + tanda_tangan_pembeli + '&noDo=' + noDo + '&tlransi=' + tlransi + '&kdPt=' + kdPt + '&kurs=' + kurs + '&ppn=' + ppn + '&total=' + total +'&transporter='+ transporter;
    tujuan = 'pmn_kontrakjual_slave.php';
    //alert(param);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    //document.getElementById('stn').innerHTML=con.responseText;
                    alert('Berhasil Tersimpan');
                    loadNewData();
                    clearFrom();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if (confirm("Are you sure?")) {
        post_response_text(tujuan, param, respog);
    }

}


function clearFrom() {
    document.getElementById('noKtrk').value = '';
    document.getElementById('custId').value = '';
    document.getElementById('tlgKntrk').value = '';
    document.getElementById('kdBrg').value = '';
    document.getElementById('HrgStn').value = '';

    document.getElementById('jmlh').value = '';
    document.getElementById('tglKrm').value = '';
    document.getElementById('tglSd').value = '';
    document.getElementById('tlransi').value = '';
    document.getElementById('noDo').value = '';
    document.getElementById('kualitas').value = '';
    document.getElementById('syrtByr').value = '';
    document.getElementById('syrtByr2').value = '';
    document.getElementById('tBlg').innerHTML = '';
    document.getElementById('keterangan').innerHTML = '';
    document.getElementById('tndtng').value = '';
    document.getElementById('tanda_tangan_pembeli').value = '';
    document.getElementById('tmbngn').value = '';
    document.getElementById('cttn1').value = '';
    document.getElementById('cttn2').value = '';
    document.getElementById('cttn3').value = '';
    document.getElementById('cttn4').value = '';
    document.getElementById('cttn5').value = '';
    document.getElementById('cttn6').value = '';
    document.getElementById('cttn7').value = '';
    document.getElementById('cttn8').value = '';
    document.getElementById('cttn9').value = '';
    document.getElementById('cttn10').value = '';
    document.getElementById('cttn11').value = '';
    document.getElementById('cttn12').value = '';
    document.getElementById('cttn13').value = '';
    document.getElementById('cttn14').value = '';
    document.getElementById('cttn15').value = '';
    document.getElementById('lamamuat').value = '';
    document.getElementById('tipemuat').value = 'Loco';
    document.getElementById('pelabuhan').value = '';
    document.getElementById('demurage').value = '';
    document.getElementById('othCttn').value = '';
    document.getElementById('kdPt').value = '';
    document.getElementById('method').value = 'insert';
    //document.getElementById('noKtrk').disabled=false;
    document.getElementById('nmPerson').innerHTML = '';
    document.getElementById('fax').innerHTML = '';
    document.getElementById('stn').value = '';
    document.getElementById('tBlg').value = '';
    document.getElementById('total').value = '';
    document.getElementById('ppn').value = '0';
    document.getElementById('tlgKntrk').disabled = false;
    document.getElementById('custId').disabled = false;
    document.getElementById('kdPt').disabled = false;
}

function getSatuan(kdbrg, cust, sat) {
    if ((kdbrg == 0) || (cust == 0) || (sat == 0)) {
        kdBrg = document.getElementById('kdBrg').value;
        param = 'kdBrg=' + kdBrg + '&method=getSatuan';
        //alert(param);
    } else {
        kdBrg = kdbrg;
        satuan = sat;
        param = 'kdBrg=' + kdBrg + '&method=getSatuan' + '&satuan=' + satuan;
    }

    /*
        var dateObj = new Date();
        var month = dateObj.getUTCMonth() + 1; //months from 1-12
        var day = dateObj.getUTCDate();
        var year = dateObj.getUTCFullYear();

        newdate = day + "-" + month + "-" + year;
        */


    if (document.getElementById('tlgKntrk').value == '') {
            // FA 2018-01-05
    var now = new Date();
    var yyyy = now.getFullYear();
    var m = now.getMonth() + 1;
    var d = now.getDate();
    var mm = m < 10 ? '0' + m : m;
    var dd = d < 10 ? '0' + d : d;
    newdate = dd + "-" + mm + "-" + yyyy;
        document.getElementById('tlgKntrk').value = newdate;
    }

    tlgKntrk = document.getElementById('tlgKntrk').value;
    param += '&tlgKntrk=' + tlgKntrk;

  //  alert(param);
    tujuan = 'pmn_kontrakjual_slave.php';

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('stn').innerHTML = con.responseText;
                    getDataCust(cust);
                    getNoCtr(kdBrg);
                    //genNoDO();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function getSatuanX(kdbrg, cust, sat) {
    if ((kdbrg == 0) || (cust == 0) || (sat == 0)) {
        kdBrg = document.getElementById('kdBrg').value;
        param = 'kdBrg=' + kdBrg + '&method=getSatuan';
        //alert(param);
    } else {
        kdBrg = kdbrg;
        satuan = sat;
        param = 'kdBrg=' + kdBrg + '&method=getSatuan' + '&satuan=' + satuan;
    }

    //alert(param);
    tujuan = 'pmn_kontrakjual_slave.php';

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('stn').innerHTML = con.responseText;
                    getDataCust(cust);
                    //getNoCtr(kdBrg);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function getNoCtr(kdBrg) {
    kdPt = document.getElementById('kdPt').value;
    tlgKntrk = document.getElementById('tlgKntrk').value;
    noKtrk = document.getElementById('noKtrk').value;
 
    param = 'kdBrg=' + kdBrg + '&method=getNoCtr' + '&kdPt=' + kdPt + '&tlgKntrk=' + tlgKntrk+ '&noKtrk=' + noKtrk;
//    alert(param);
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    isiKontDo = con.responseText.split("###");
                    //document.getElementById('noKtrk').value = trim(con.responseText);
                    document.getElementById('noKtrk').value = isiKontDo[0];
                    //document.getElementById('noDo').value = trim(con.responseText);
                    document.getElementById('noDo').value = isiKontDo[1];
                    //document.getElementById('dtl_pem').disabled = false;
                    document.getElementById('tlgKntrk').disabled = false;
                    //document.getElementById('noDo').disabled = true;
                    document.getElementById('custId').disabled = false;
                    // document.getElementById('noKtrk').disabled = true;
                    // document.getElementById('kdPt').disabled = true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function copyFromLast() {
    var custId=document.getElementById('custId').value;
    param = 'method=getLastData' + '&custId=' + custId;
    tujuan = 'pmn_kontrakjual_slave.php';
//alert(param);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    // document.getElementById('noKtrk').disabled=false;
                    ar = con.responseText.split("###");
                //    document.getElementById('noKtrk').value = ar[0];
                //    document.getElementById('custId').value = ar[1];
                //    document.getElementById('tlgKntrk').value = ar[2];
                //    document.getElementById('kdBrg').value = ar[3];
                //    document.getElementById('HrgStn').value = ar[4];
                //    document.getElementById('tBlg').value = ar[5];
                //    document.getElementById('total').value = ar[29];
                //    document.getElementById('jmlh').value = ar[6];
                //    document.getElementById('tglKrm').value = ar[7];
                //    document.getElementById('tglSd').value = ar[8];
                    document.getElementById('tlransi').value = ar[9];
                //    document.getElementById('noDo').value = ar[10];
                    document.getElementById('kualitas').value = ar[11];
                    document.getElementById('syrtByr').value = ar[12];
                    document.getElementById('tndtng').value = ar[13];
                    document.getElementById('tmbngn').value = ar[14];
                    document.getElementById('cttn1').value = ar[15];
                //    document.getElementById('cttn2').value = ar[16];
                //    document.getElementById('cttn3').value = ar[17];
                //    document.getElementById('cttn4').value = ar[18];
                //    document.getElementById('cttn5').value = ar[19];
                //    document.getElementById('othCttn').value = ar[20];
                //    getSatuan(ar[3], ar[1], ar[21]);
                //    document.getElementById('kdPt').value = ar[22];
                document.getElementById('syrtByr2').value = ar[23];
                document.getElementById('tanda_tangan_pembeli').value = ar[34];

                    //document.getElementById('stn').value;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function getDataCust(dt) {
    if (dt == 0) {
        custId = document.getElementById('custId').value;
    } else {
        custId = dt;
    }
    param = 'method=getCust' + '&custId=' + custId;
    tujuan = 'pmn_kontrakjual_slave.php';

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    ar = con.responseText.split("###");
                    document.getElementById('nmPerson').innerHTML = "Contact Person : &nbsp;" + ar[0];
                    document.getElementById('fax').innerHTML = ", Fax No.: &nbsp;" + ar[1];
                    copyFromLast();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function fillField(nokntrk) {
    noKntrk = nokntrk;
    param = 'method=getEditData' + '&noKntrk=' + noKntrk;
    tujuan = 'pmn_kontrakjual_slave.php';
    tabAction(document.getElementById('tabFRM0'), 0, 'FRM', 1);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    ar = con.responseText.split("###");
                   // alert(ar);
                    document.getElementById('noKtrk').value = ar[0];
                    document.getElementById('custId').value = ar[1];
                    document.getElementById('tlgKntrk').value = ar[2];
                    document.getElementById('kdBrg').value = ar[3];
                    document.getElementById('HrgStn').value = ar[4];
                    document.getElementById('tBlg').innerHTML = ar[5];
                    document.getElementById('jmlh').value = ar[6];
                    document.getElementById('tglKrm').value = ar[7];
                    document.getElementById('tglSd').value = ar[8];
                    document.getElementById('tlransi').value = ar[9];
                    document.getElementById('noDo').value = ar[10];
                    document.getElementById('kualitas').value = ar[11];
                    document.getElementById('syrtByr').value = ar[12];
                    document.getElementById('tndtng').value = ar[13];
                    document.getElementById('tmbngn').value = ar[14];
                    document.getElementById('cttn1').value = ar[15];
                    document.getElementById('transporter').value = ar[16];
                //    document.getElementById('cttn3').value = ar[17];
                //    document.getElementById('cttn4').value = ar[18];
                //    document.getElementById('cttn5').value = ar[19];
                //    document.getElementById('othCttn').value = ar[20];
                    document.getElementById('kdPt').value = ar[22];
                    document.getElementById('kurs').value = ar[23];
                    document.getElementById('ppn').value = ar[24];
                    document.getElementById('lamamuat').value = ar[25];
                    document.getElementById('pelabuhan').value = ar[26];
                    document.getElementById('demurage').value = ar[27];
                    document.getElementById('syrtByr2').value = ar[28];
                    document.getElementById('total').value = ar[29];
                //    document.getElementById('cttn6').value = ar[30];
                //    document.getElementById('cttn7').value = ar[31];
                //    document.getElementById('cttn8').value = ar[32];
                //    document.getElementById('cttn9').value = ar[33];
                //    document.getElementById('cttn10').value = ar[34];
                //    document.getElementById('cttn11').value = ar[35];
                //    document.getElementById('cttn12').value = ar[36];
                //    document.getElementById('cttn13').value = ar[37];
                //    document.getElementById('cttn14').value = ar[38];
                //    document.getElementById('cttn15').value = ar[39];
                    document.getElementById('tipemuat').value = ar[40];
                    document.getElementById('keterangan_muat').value = ar[41];
                    document.getElementById('tanda_tangan_pembeli').value = ar[42];
                    //getSatuan(ar[3],ar[1],ar[21]);
                    getSatuanX(ar[3], ar[1], ar[21]);
                    //document.getElementById('stn').value;
                    // document.getElementById('noKtrk').disabled=true;
                    document.getElementById('method').value = 'update';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respog);
}

function delData(nokontrk) {
    noKntrk = nokontrk;
    param = 'method=dataDel' + '&noKntrk=' + noKntrk;
    // alert(param);
    tujuan = 'pmn_kontrakjual_slave.php';

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    //document.getElementById('stn').innerHTML=con.responseText;
                    //clearFrom();
                    //tabAction(document.getElementById('tabFRM0'),0,'FRM',1);

                    document.getElementById('method').value = 'insert';
                    loadNewData();

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if (confirm("Are you sure?")) {
        post_response_text(tujuan, param, respog);
    }

}

function cariNoKntrk() {
    txtSearch = document.getElementById('txtnokntrk').value;
    param = 'txtSearch=' + txtSearch + '&method=cariNokntrk';
    tujuan = 'pmn_kontrakjual_slave.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    //document.getElementById('stn').innerHTML=con.responseText;
                    //clearFrom();
                    //tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
                    //tabAction(document.getElementById('tabFRM1'),0,'FRM',1);
                    document.getElementById('containerlist').innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getZerox() {
    HrgStn = document.getElementById('HrgStn');
    if (HrgStn.value == "") {
        HrgStn.value = 0;
    }
    jmlh = document.getElementById('jmlh');
    if (jmlh.value == "") {
        jmlh.value = 0;
    }
    total = document.getElementById('total');
    //angdis.value=remove_comma(angdis);
    if (total.value == "") {
        total.value = 0;
    }
}

function fillTerbilang() {
    obj = document.getElementById('total');
    rupiahkan(obj, 'tBlg');
}

function calculate_tanpappn() {
    HrgStn = document.getElementById('HrgStn');
    HrgStn.innerHTML = remove_comma_var(HrgStn.innerHTML);
    HrgStn = document.getElementById('HrgStn').value;
    jmlh = document.getElementById('jmlh');
    jmlh.innerHTML = remove_comma_var(jmlh.innerHTML);
    jmlh = document.getElementById('jmlh').value;
    ppn = document.getElementById('ppn').options[document.getElementById('ppn').selectedIndex].value;
    if ((HrgStn == 0) || (jmlh == 0)) {
        document.getElementById('HrgStn').disabled = false;
        document.getElementById('jmlh').disabled = false;
    }
    //document.getElementById('HrgStn').disabled = true;
    //grandtot = Number(HrgStn * jmlh).toFixed(2);
    if (ppn == 0) {
        grandtot = HrgStn * jmlh;
        nilaigrandtot = document.getElementById('total');
        nilaigrandtot.value = grandtot;
        obj = document.getElementById('total');
        rupiahkan(obj, 'tBlg');
    } else {
        totall = (HrgStn * jmlh);
        totppn = (parseFloat(totall) * 10) / 100;
        grandtot = parseFloat(totall) + parseFloat(totppn);
        nilaigrandtot = document.getElementById('total');
        nilaigrandtot.value = grandtot;
        obj = document.getElementById('total');
        rupiahkan(obj, 'tBlg');
    }
}