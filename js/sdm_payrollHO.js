/**
 * @author antoniuslouis
 */


function getKar() {
    kdOrg = document.getElementById('kdOrg').value;
    param = 'method=getKar' + '&kdOrg=' + kdOrg;
    //alert(param);
    tujuan = 'sdm_slave_angsuran.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    document.getElementById('userid').innerHTML = con.responseText;
                    loadData();
                    //getKar();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}




function loadData() {
    kdOrg = document.getElementById('kdOrg').value;
    param = 'method=loadData' + '&kdOrg=' + kdOrg;
    //alert(param);
    tujuan = 'sdm_slave_angsuran.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // alert(con.responseText);
                    //document.getElementById('container').innerHTML=con.responseText;
                    document.getElementById('tbody').innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}





function validatepayroll() {
    pw = document.getElementById('pypwd').value;
    param = 'pwd=' + pw;
    post_response_text('hr_slaveValidatePayroll.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    window.location.reload();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function editComp(id, name, jenis, type, lock) {

    document.getElementById('comp').value = name;
    document.getElementById('compid').value = id;

    jk1 = document.getElementById('plus');
    for (x = 0; x < jk1.length; x++) {
        if (jk1.options[x].value == jenis) {
            jk1.options[x].selected = true;
        }
    }

    jk2 = document.getElementById('type');
    for (x = 0; x < jk2.length; x++) {
        if (jk2.options[x].value == type) {
            jk2.options[x].selected = true;
        }
    }

    jk3 = document.getElementById('lock');
    for (x = 0; x < jk3.length; x++) {
        if (jk3.options[x].value == lock) {
            jk3.options[x].selected = true;
        }
    }
    document.getElementById('legend').innerHTML = '<b>Edit Form (id=' + id + ')</b>';
}

function cancelComp() {
    document.getElementById('legend').innerHTML = '<b>New Form</b>';
    document.getElementById('compid').value = '';
    document.getElementById('comp').value = '';
}

function saveComp() {
    name = document.getElementById('comp').value;
    id = document.getElementById('compid').value;
    plus = document.getElementById('plus').options[document.getElementById('plus').selectedIndex].value;
    type = document.getElementById('type').options[document.getElementById('type').selectedIndex].value;
    lock = document.getElementById('lock').options[document.getElementById('lock').selectedIndex].value;
    if (trim(name).length > 0 && confirm('Are you sure..')) {
        param = 'name=' + name + '&id=' + id + '&plus=' + plus + '&type=' + type + '&lock=' + lock;
        post_response_text('sdm_slaveSavePayrollHOComponent.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tablebody').innerHTML = con.responseText;
                    cancelComp();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function delComp(id, name) {
    if (confirm('Delete component: ' + name + '\nAre you sure?')) {
        param = 'id=' + id;
        post_response_text('sdm_slaveDelPayrollHOComponent.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tablebody').innerHTML = con.responseText;
                    cancelComp();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function checkAll(obj, max) {
    for (x = 1; x <= max; x++) {
        if (obj.checked)
            document.getElementById('chk' + x).checked = true;
        else
            document.getElementById('chk' + x).checked = false;
    }
}
var jumldata = 0;

function sync(juml) {
    //start from begining again
    jumldata = 0;
    dosync(juml);
}

function dosync(juml) {
    jumldata += 1;
    if (jumldata <= juml) {
        if (document.getElementById('chk' + jumldata).checked) {
            userid = document.getElementById('userid' + jumldata).innerHTML;
            nama = document.getElementById('nama' + jumldata).innerHTML;
            mstatus = document.getElementById('mstatus' + jumldata).innerHTML;
            start = document.getElementById('start' + jumldata).innerHTML;
            resign = document.getElementById('resign' + jumldata).innerHTML;
            npwp = document.getElementById('npwp' + jumldata).innerHTML;
            document.getElementById('stpbutton').disabled = false;

            if (jumldata == 1) {

                if (confirm('Are you sure?')) {
                    param = 'userid=' + userid + '&nama=' + nama + '&mstatus=' + mstatus + '&start=' + start + '&resign=' + resign + '&npwp=' + npwp;
                    document.getElementById('row' + jumldata).style.backgroundColor = 'orange';
                    post_response_text('sdm_slaveSyncronizePYHO.php', param, respon);
                }
                document.getElementById('synbutton').disabled = true;
            } else {
                param = 'userid=' + userid + '&nama=' + nama + '&mstatus=' + mstatus + '&start=' + start + '&resign=' + resign + '&npwp=' + npwp;
                document.getElementById('row' + jumldata).style.backgroundColor = 'orange';
                post_response_text('sdm_slaveSyncronizePYHO.php', param, respon);
            }
        } else {
            dosync(juml);
        }
    } else {
        alert('Finish');
        document.getElementById('stpbutton').disabled = true;
        document.getElementById('synbutton').disabled = false;
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row' + jumldata).style.backgroundColor = 'red';
                    document.getElementById('synbutton').disabled = true;
                } else {
                    document.getElementById('row' + jumldata).style.backgroundColor = 'green';
                    dosync(juml);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function stopSync(x) {
    //this will stop synchronization
    jumldata = x;
    document.getElementById('synbutton').disabled = false;
    document.getElementById('stpbutton').disabled = true
}

function vLine(obj, no) {
    if (obj.checked) {
        document.getElementById('userid' + no).disabled = false;
        document.getElementById('bank' + no).disabled = false;
        document.getElementById('bankac' + no).disabled = false;
        document.getElementById('jms' + no).disabled = false;
        document.getElementById('jmsstartbl' + no).disabled = false;
        document.getElementById('jmsstartth' + no).disabled = false;
        document.getElementById('firstbl' + no).disabled = false;
        document.getElementById('firstth' + no).disabled = false;
        document.getElementById('firstvol' + no).disabled = false;
        document.getElementById('lastbl' + no).disabled = false;
        document.getElementById('lastth' + no).disabled = false;
        document.getElementById('lastvol' + no).disabled = false;
        document.getElementById('butt' + no).disabled = false;
    } else {
        document.getElementById('userid' + no).disabled = true;
        document.getElementById('bank' + no).disabled = true;
        document.getElementById('bankac' + no).disabled = true;
        document.getElementById('jms' + no).disabled = true;
        document.getElementById('jmsstartbl' + no).disabled = true;
        document.getElementById('jmsstartth' + no).disabled = true;
        document.getElementById('firstbl' + no).disabled = true;
        document.getElementById('firstth' + no).disabled = true;
        document.getElementById('firstvol' + no).disabled = true;
        document.getElementById('lastbl' + no).disabled = true;
        document.getElementById('lastth' + no).disabled = true;
        document.getElementById('lastvol' + no).disabled = true;
        document.getElementById('butt' + no).disabled = true;
    }
}

function saOneLine(no) {
    userid = trim(document.getElementById('userid' + no).innerHTML);
    bank = document.getElementById('bank' + no).value;
    bankac = document.getElementById('bankac' + no).value;
    jms = document.getElementById('jms' + no).value;
    jmsstartbl = document.getElementById('jmsstartbl' + no).options[document.getElementById('jmsstartbl' + no).selectedIndex].value;
    jmsstartth = document.getElementById('jmsstartth' + no).options[document.getElementById('jmsstartth' + no).selectedIndex].value;
    jmsperiod = jmsstartth + "-" + jmsstartbl;
    firstbl = document.getElementById('firstbl' + no).options[document.getElementById('firstbl' + no).selectedIndex].value;
    firstth = document.getElementById('firstth' + no).options[document.getElementById('firstth' + no).selectedIndex].value;
    firstperiod = firstth + "-" + firstbl;
    firstvol = document.getElementById('firstvol' + no).value;
    lastbl = document.getElementById('lastbl' + no).options[document.getElementById('lastbl' + no).selectedIndex].value;
    lastth = document.getElementById('lastth' + no).options[document.getElementById('lastth' + no).selectedIndex].value;
    lastperiod = lastth + "-" + lastbl;

    lastvol = document.getElementById('lastvol' + no).value;
    if (trim(lastperiod).length != 7)
        lastperiod = '';
    if (trim(firstperiod).length != 7)
        firstperiod = '';
    if (lastvol == '')
        lastvol = 0;
    if (firstvol == '')
        firstvol = 0;
    bankAccountColor(no, 'orange');
    if (trim(bankac) != '' && trim(bank) != '' && trim(firstperiod).length == 7) {
        param = 'userid=' + userid + '&bank=' + bank + '&bankac=' + bankac;
        param += '&jms=' + jms + '&firstperiod=' + firstperiod;
        param += '&firstvol=' + firstvol + '&lastperiod=' + lastperiod + '&lastvol=' + lastvol + '&jmsperiod=' + jmsperiod;
        post_response_text('sdm_slaveSavePyHOEmployeeData.php', param, respon);
    } else {
        alert('Bank account,BPJS Start & first payment are obligatory');
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    bankAccountColor(no, 'red');
                } else {
                    bankAccountColor(no, '#E8F2FC');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function bankAccountColor(no, color) {
    document.getElementById('bank' + no).style.backgroundColor = color;
    document.getElementById('bankac' + no).style.backgroundColor = color;
    document.getElementById('jms' + no).style.backgroundColor = color;
    document.getElementById('jmsstartbl' + no).style.backgroundColor = color;
    document.getElementById('jmsstartth' + no).style.backgroundColor = color;
    document.getElementById('firstbl' + no).style.backgroundColor = color;
    document.getElementById('firstth' + no).style.backgroundColor = color;
    document.getElementById('firstvol' + no).style.backgroundColor = color;
    document.getElementById('lastbl' + no).style.backgroundColor = color;
    document.getElementById('lastth' + no).style.backgroundColor = color;
    document.getElementById('lastvol' + no).style.backgroundColor = color;
}

function saveAll(max) {
    jumldata = 0;
    dosaveAll(max);
}

function dosaveAll(max) {
    jumldata += 1;
    if (jumldata <= max) {
        if (document.getElementById('check' + jumldata).checked) {
            userid = trim(document.getElementById('userid' + jumldata).innerHTML);
            bank = document.getElementById('bank' + jumldata).value;
            bankac = document.getElementById('bankac' + jumldata).value;
            jms = document.getElementById('jms' + jumldata).value;
            jmsstartbl = document.getElementById('jmsstartbl' + jumldata).options[document.getElementById('jmsstartbl' + jumldata).selectedIndex].value;
            jmsstartth = document.getElementById('jmsstartth' + jumldata).options[document.getElementById('jmsstartth' + jumldata).selectedIndex].value;
            jmsperiod = jmsstartth + "-" + jmsstartbl;
            firstbl = document.getElementById('firstbl' + jumldata).options[document.getElementById('firstbl' + jumldata).selectedIndex].value;
            firstth = document.getElementById('firstth' + jumldata).options[document.getElementById('firstth' + jumldata).selectedIndex].value;
            firstperiod = firstth + "-" + firstbl;
            firstvol = document.getElementById('firstvol' + jumldata).value;
            lastbl = document.getElementById('lastbl' + jumldata).options[document.getElementById('lastbl' + jumldata).selectedIndex].value;
            lastth = document.getElementById('lastth' + jumldata).options[document.getElementById('lastth' + jumldata).selectedIndex].value;
            lastperiod = lastth + "-" + lastbl;
            lastvol = document.getElementById('lastvol' + jumldata).value;
            if (trim(lastperiod).length != 7)
                lastperiod = '';
            if (trim(firstperiod).length != 7)
                firstperiod = '';
            if (lastvol == '')
                lastvol = 0;
            if (firstvol == '')
                firstvol = 0;
            bankAccountColor(jumldata, 'orange');
            if (trim(bankac) != '' && trim(bank) != '' && trim(firstperiod).length == 7 && jmsperiod.length == 7) {
                param = 'userid=' + userid + '&bank=' + bank + '&bankac=' + bankac;
                param += '&jms=' + jms + '&firstperiod=' + firstperiod;
                param += '&firstvol=' + firstvol + '&lastperiod=' + lastperiod + '&lastvol=' + lastvol + '&jmsperiod=' + jmsperiod;
                post_response_text('sdm_slaveSavePyHOEmployeeData.php', param, respon);
            } else {
                alert('Bank account,JMS Start & first payment are obligatory');
            }
        } else {
            dosaveAll(max);
        }
    } else {
        alert('Finish');
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    bankAccountColor(jumldata, 'red');
                } else {
                    bankAccountColor(jumldata, '#E8F2FC');
                    dosaveAll(max);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveOperator(no) {
    userid = document.getElementById('user' + no).innerHTML;
    operator = document.getElementById('operator' + no).options[document.getElementById('operator' + no).selectedIndex].value;
    param = 'userid=' + userid + '&operator=' + operator;
    if (userid != '' && operator != '')
        post_response_text('sdm_slaveSaveHOPyOperator.php', param, respon);
    else
        alert('Data not valid');

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('operator' + no).style.backgroundColor = 'red';
                } else {
                    document.getElementById('operator' + no).style.backgroundColor = '#E8F2FC';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function savePyUser() {
    user = document.getElementById('user').value;
    type = document.getElementById('type').options[document.getElementById('type').selectedIndex].value;
    param = 'user=' + user + '&type=' + type;
    if (user != '' && type != '')
        post_response_text('sdm_slaveSavePyHOUser.php', param, respon);
    else
        alert('Data Not valid');

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tablebody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function delPyUser(uname) {
    param = 'uname=' + uname;
    if (confirm('Deleting ' + uname + ', are you sure ..?')) {
        post_response_text('sdm_slaveDeletePyHOUser.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tablebody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function calculcateTotal(maxC, userid) {
    limit = parseInt(maxC);
    total = 0.00;
    for (d = 0; d <= limit; d++) {
        sign = document.getElementById('plus' + userid + d).value;
        val = document.getElementById('value' + userid + d).value;
        while (val.indexOf(",") > -1) {
            val = val.replace(",", "");
        }
        val = parseFloat(val);
        if (sign == '1')
            total = total + val;
        else
            total = total - val;
    }
    Tl = document.getElementById('total' + userid);
    Tl.value = total;
    change_number(Tl);
}

function calculatePayroll(object, maxC, userid) {
    calculcateTotal(maxC, userid);
    change_number(object);
    fillTerbilang(userid);
}

function change_number(object) {
    while (object.value.indexOf(",") > -1) {
        object.value = object.value.replace(",", "");
    }
    //number format cleared and verified
    str = object.value.replace(".", "");
    rex = /[^0-9]/;
    if ((!str.match(rex)) || (parseFloat(str) == 0.00)) {
        try {
            object.value = _formatted(object);
        } catch (ex) {
            alert(ex.toString());
        }
    } else {
        if (object.value.length > 0) {
            alert('Wrong number');
            object.focus();
        }
    }
}

function saveBSalary(userid, compId, valObj) {
    value = document.getElementById(valObj).value;
    while (value.indexOf(",") > -1) {
        value = value.replace(",", "");
    }
    param = 'userid=' + userid + '&component=' + compId + '&value=' + value;
    document.getElementById(valObj).style.backgroundColor = '#FFA500';
    post_response_text('sdm_slaveSaveHOBSalary.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById(valObj).style.backgroundColor = '#FF4444';
                } else {
                    document.getElementById(valObj).style.backgroundColor = '#EDEDED';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function editAngsuran(karyawanid, jenis, total, xstart, jlhbln, aktf) {
    userid = document.getElementById('userid');
    idx = document.getElementById('idx');
    start = document.getElementById('start');
    active = document.getElementById('active');

    document.getElementById('idx').disabled = true;

    for (x = 0; x < userid.length; x++) {
        if (userid.options[x].value == karyawanid)
            userid.options[x].selected = true;
    }

    for (x = 0; x < idx.length; x++) {
        if (idx.options[x].value == jenis)
            idx.options[x].selected = true;
    }

    for (x = 0; x < start.length; x++) {
        if (start.options[x].value == xstart)
            start.options[x].selected = true;
    }
    for (x = 0; x < active.length; x++) {
        if (active.options[x].value == aktf)
            active.options[x].selected = true;
    }

    document.getElementById('method').value = 'update';
    lama = document.getElementById('lama').value = jlhbln;
    total = document.getElementById('total').value = total;
}


function delAngsuran(karyawanid, jenis) {
    param = 'userid=' + karyawanid + '&idx=' + jenis + '&method=delete';
    if (confirm('deleting, Are you sure..?')) {
        post_response_text('sdm_slaveSaveAngsuran.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tbody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function cancelAngsuran() {
    document.getElementById('lama').value = 0;
    document.getElementById('total').value = 0;

    //document.getElementById('userid').value='';
    document.getElementById('idx').value = '9';
    document.getElementById('idx').disabled = false;

    document.getElementById('method').value = 'insert';
}

function saveAngsuran() {
    userid = document.getElementById('userid').options[document.getElementById('userid').selectedIndex].value;
    idx = document.getElementById('idx').options[document.getElementById('idx').selectedIndex].value;
    total = document.getElementById('total').value;
    start = document.getElementById('start').options[document.getElementById('start').selectedIndex].value;
    lama = document.getElementById('lama').value;
    active = document.getElementById('active').options[document.getElementById('active').selectedIndex].value;
    method = document.getElementById('method').value;
    while (total.indexOf(",") > -1) {
        total = total.replace(",", "");
    }
    f = total;
    total = parseFloat(total);
    g = lama;
    lama = parseInt(lama);
    if (total < 100.00 || f == '' || lama < 1 || g == '') {
        alert('Total installment must greater than 0');
    } else {
        param = 'userid=' + userid + '&idx=' + idx + '&total=' + total;
        param += '&start=' + start + '&lama=' + lama + '&active=' + active;
        param += '&method=' + method;
        if (confirm('Saving, Are you sure..?')) {
            post_response_text('sdm_slaveSaveAngsuran.php', param, respon);
        }
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('method').value = 'insert';
                    document.getElementById('tbody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function setPayrollPeriod() {
    period = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    periodTx = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;
    param = 'period=' + period;
    if (confirm('Setting Period to ' + periodTx + ', Are you sure..?')) {
        post_response_text('sdm_slaveSavePayrollHOPeriod.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    window.location = 'sdm_mainCreatePayrollHODetail.php';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function setBonusPeriod() {
    period = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    periodTx = document.getElementById('periodegaji').options[document.getElementById('periodegaji').selectedIndex].value;
    param = 'period=' + period + '&periodegaji=' + periodTx;
    if (confirm('Setting Period to ' + periodTx + ', Are you sure..?')) {
        post_response_text('sdm_slaveSaveHOBonusPeriod.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    window.location = 'sdm_mainCreateBonusHODetail.php';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function setTHRPeriod() {
    period = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tglthr = document.getElementById('tglthr').value;
    param = 'period=' + period + '&tglthr=' + tglthr;

    post_response_text('sdm_slaveSaveTHRHOPeriod.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    window.location = 'sdm_mainCreateTHRHO.php';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function fillTerbilang(userid) {
    obj = document.getElementById('total' + userid);
    rupiahkan(obj, 'terbilang' + userid);
}

maxComponent = 0;
itemComponent = -1; //id component start from 0 (begining of an array
replace = 'no';

function saveMonthlySalary(userid, rcount) {
    replace = 'no';
    itemComponent = -1;
    maxComponent = rcount;
    fillTerbilang(userid);
    if (confirm('Saving, Are you sure..?')) {
        document.getElementById('btn' + userid).disabled = true;
        doPostMsalary(userid);
    }
}

function doPostMsalary(userid) {
    itemComponent += 1;
    val = document.getElementById('value' + userid + itemComponent).value;
    component = document.getElementById('component' + userid + itemComponent).value;
    plus = document.getElementById('plus' + userid + itemComponent).value;
    terbilang = document.getElementById('terbilang' + userid).innerHTML;
    while (val.indexOf(",") > -1) {
        val = val.replace(",", "");
    }
    param = 'val=' + val + '&component=' + component + '&plus=' + plus + '&userid=' + userid + '&terbilang=' + terbilang;
    post_response_text('sdm_slaveMonthlyPayrollHO.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById(userid).style.backgroundColor = 'red';
                    //aktifkan button ketika response selesai
                    document.getElementById('btn' + userid).disabled = false;
                } else {
                    if (con.responseText.lastIndexOf('Double') > -1) {
                        param = 'val=' + val + '&component=' + component + '&plus=' + plus + '&userid=' + userid + '&replace=yes&terbilang=' + terbilang;
                        if (replace == 'no') {
                            if (confirm('Data already exist, replace..?')) {
                                replace = 'yes';
                                post_response_text('sdm_slaveMonthlyPayrollHO.php', param, respon);
                                document.getElementById('btn' + userid).disabled = true;
                            }
                        } else {
                            post_response_text('sdm_slaveMonthlyPayrollHO.php', param, respon);
                            document.getElementById('btn' + userid).disabled = true;
                        }
                    } else {
                        if (maxComponent == itemComponent) {
                            document.getElementById(userid).style.backgroundColor = 'green';
                            //aktifkan button ketika response selesai
                            document.getElementById('btn' + userid).disabled = false;
                        } else {
                            doPostMsalary(userid);
                        }
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function setJmsPorsi() {
    karyawan = trim(document.getElementById('karyawan').value);
    // beban = trim(document.getElementById('beban').value);
    perusahaan = (document.getElementById('perusahaan').value);
    usiapensiun = (document.getElementById('usiapensiun').value);
    jhtkar = trim(document.getElementById('jhtkar').value);
    jpkar = trim(document.getElementById('jpkar').value);
    bpjskar = trim(document.getElementById('bpjskar').value);
    jhtpt = trim(document.getElementById('jhtpt').value);
    jppt = trim(document.getElementById('jppt').value);
    jkkpt = trim(document.getElementById('jkkpt').value);
    jkmpt = trim(document.getElementById('jkmpt').value);
    bpjspt = trim(document.getElementById('bpjspt').value);

    jmpk = trim(document.getElementById('jmpk').value);
    jmppt = trim(document.getElementById('jmppt').value);
    bpjsmk = trim(document.getElementById('bpjsmk').value);
    bpjsmpt = trim(document.getElementById('bpjsmpt').value);

    lokres = document.getElementById('lokres').options[document.getElementById('lokres').selectedIndex].value;
    if (karyawan == '' || karyawan == 0 || perusahaan == '' || perusahaan == 0 || usiapensiun == '' || usiapensiun == 0 || jhtkar == '' || jhtkar == 0 || jpkar == '' || jpkar == 0 || bpjskar == '' || bpjskar == 0 || jhtpt == '' || jhtpt == 0 || jppt == '' || jppt == 0 || jkkpt == '' || jkkpt == 0 || jkmpt == '' || jkmpt == 0 || bpjspt == '' || bpjspt == 0) {
        alert('Portion can not be 0 or empty');
    } else {
        param = 'karyawan=' + karyawan + '&perusahaan=' + perusahaan + '&usiapensiun=' + usiapensiun;
        param += '&jhtkar=' + jhtkar + '&jpkar=' + jpkar + '&bpjskar=' + bpjskar;
        param += '&jmpk=' + jmpk + '&jmppt=' + jmppt + '&bpjsmk=' + bpjsmk + '&bpjsmpt=' + bpjsmpt;
        param += '&jhtpt=' + jhtpt + '&jppt=' + jppt + '&jkkpt=' + jkkpt + '&jkmpt=' + jkmpt + '&bpjspt=' + bpjspt + '&lokres=' + lokres + '&proses=updbpjs';
        // alert(param);
        if (confirm('Saving, Are you sure..?')) {
            post_response_text('sdm_slaveSaveJmsHOSetting.php', param, respon);
        }
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('karyawan').disabled = true;
                    document.getElementById('perusahaan').disabled = true;
                    document.getElementById('jhtkar').disabled = true;
                    document.getElementById('jpkar').disabled = true;
                    document.getElementById('bpjskar').disabled = true;
                    document.getElementById('jhtpt').disabled = true;
                    document.getElementById('jppt').disabled = true;
                    document.getElementById('jkkpt').disabled = true;
                    document.getElementById('jkmpt').disabled = true;
                    document.getElementById('bpjspt').disabled = true;
                    document.getElementById('usiapensiun').disabled = true;
                    alert('Saved');
                    document.getElementById('updBPJS').disabled = false;
                    document.getElementById('saveBPJS').disabled = true;
                    // document.getElementById('beban').disabled = true;
                    document.getElementById('lokres').disabled = true;
                    document.getElementById('jmpk').disabled = true;
                    document.getElementById('jmppt').disabled = true;
                    document.getElementById('bpjsmk').disabled = true;
                    document.getElementById('bpjsmpt').disabled = true;
                    document.getElementById('listdata').innerHTML = '';
                    document.getElementById('listdata').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function updateJmsPorsi() {
    //document.getElementById('karyawan').disabled = false;
    //document.getElementById('perusahaan').disabled = false;
    // document.getElementById('beban').disabled = false;
    document.getElementById('lokres').disabled = false;
    document.getElementById('jhtkar').disabled = false;
    document.getElementById('jpkar').disabled = false;
    document.getElementById('bpjskar').disabled = false;
    document.getElementById('jhtpt').disabled = false;
    document.getElementById('jppt').disabled = false;
    document.getElementById('jkkpt').disabled = false;
    document.getElementById('jkmpt').disabled = false;
    document.getElementById('usiapensiun').disabled = false;
    document.getElementById('bpjspt').disabled = false;
    document.getElementById('updBPJS').disabled = true;
    document.getElementById('saveBPJS').disabled = false;
    document.getElementById('jmpk').disabled = false;
    document.getElementById('jmppt').disabled = false;
    document.getElementById('bpjsmk').disabled = false;
    document.getElementById('bpjsmpt').disabled = false;
}

function hitungjhtKar() {
    jhtkar = document.getElementById('jhtkar').value;
    jpkar = document.getElementById('jpkar').value;
    bpjskar = document.getElementById('bpjskar').value;
    totkar = parseFloat(jhtkar) + parseFloat(jpkar) + parseFloat(bpjskar);
    ttlkar = document.getElementById('karyawan');
    ttlkar.value = parseFloat(totkar).toFixed(2);
    document.getElementById('karyawan').innerHTML = parseFloat(ttlkar).toFixed(2);
}

function hitungjhtPT() {
    jhtpt = document.getElementById('jhtpt').value;
    jppt = document.getElementById('jppt').value;
    jkkpt = document.getElementById('jkkpt').value;
    jkmpt = document.getElementById('jkmpt').value;
    bpjspt = document.getElementById('bpjspt').value;
    totpt = parseFloat(jhtpt) + parseFloat(jppt) + parseFloat(jkkpt) + parseFloat(jkmpt) + parseFloat(bpjspt);
    ttlpt = document.getElementById('perusahaan');
    ttlpt.value = parseFloat(totpt).toFixed(2);
    document.getElementById('perusahaan').innerHTML = parseFloat(ttlpt).toFixed(2);
}

function showAngsuran(val) {
    switch (trim(val)) {
        case 'lunas':
            document.getElementById('bln').options[0].selected = true;
            document.getElementById('caption').innerHTML = 'Settled';
            break;
        case 'blmlunas':
            document.getElementById('caption').innerHTML = 'Not yet settled';
            document.getElementById('bln').options[0].selected = true;
            break;
        case 'active':
            document.getElementById('caption').innerHTML = 'Active';
            document.getElementById('bln').options[0].selected = true;
            break;
        case 'notactive':
            document.getElementById('caption').innerHTML = 'Tidak Active';
            document.getElementById('bln').options[0].selected = true;
            break;
        default:
            document.getElementById('caption').innerHTML = 'Period ' + val.substr(5, 2) + '-' + val.substr(0, 4);
            document.getElementById('lunas').options[0].selected = true;
    }
    param = 'string=' + val;
    document.getElementById('val').value = val;
    post_response_text('sdm_slaveShowAngsuran.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tbody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getJmsvalue(val) {
    document.getElementById('caption').innerHTML = val.substr(5, 2) + '-' + val.substr(0, 4);
    if (trim(val) == '') {} else {
        param = 'val=' + trim(val);
        post_response_text('sdm_slaveShowJamsostekHO.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tbody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function convertJmsExcel() {
    val = document.getElementById('caption').innerHTML;
    document.getElementById('ifrm').src = 'sdm_svlaveJmsHOToExcel.php?periode=' + val;
}

function replaceComma(val) {
    while (val.indexOf(",") > -1) {
        val = val.replace(",", "");
    }
    return val;
}

function savePTKP() {
    single = document.getElementById('ptkps').value;
    single = replaceComma(single);
    k0 = document.getElementById('ptkp0').value;
    k0 = replaceComma(k0);
    k1 = document.getElementById('ptkp1').value;
    k1 = replaceComma(k1);
    k2 = document.getElementById('ptkp2').value;
    k2 = replaceComma(k2);
    k3 = document.getElementById('ptkp3').value;
    k3 = replaceComma(k3);
    param = 'single=' + single + '&k0=' + k0 + '&k1=' + k1 + '&k2=' + k2 + '&k3=' + k3
    //alert(param);
    post_response_text('sdm_slaveSavePph21HOptkp.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Saved');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function saveKontribusi() {
    r0 = document.getElementById('range0').value;
    r0 = replaceComma(r0);
    r1 = document.getElementById('range1').value;
    r1 = replaceComma(r1);
    r2 = document.getElementById('range2').value;
    r2 = replaceComma(r2);
    r3 = document.getElementById('range3').value;
    r3 = replaceComma(r3);
    r4 = document.getElementById('range4').value;
    r4 = replaceComma(r4);
    p0 = document.getElementById('percent0').value;
    p0 = replaceComma(p0);
    p1 = document.getElementById('percent1').value;
    p1 = replaceComma(p1);
    p2 = document.getElementById('percent2').value;
    p2 = replaceComma(p2);
    p3 = document.getElementById('percent3').value;
    p3 = replaceComma(p3);
    p4 = document.getElementById('percent4').value;
    p4 = replaceComma(p4);
    s0 = document.getElementById('sign0').options[document.getElementById('sign0').selectedIndex].value;
    s1 = document.getElementById('sign1').options[document.getElementById('sign1').selectedIndex].value;
    s2 = document.getElementById('sign2').options[document.getElementById('sign2').selectedIndex].value;
    s3 = document.getElementById('sign3').options[document.getElementById('sign3').selectedIndex].value;
    s4 = document.getElementById('sign4').options[document.getElementById('sign4').selectedIndex].value;

    param = 'r0=' + r0 + '&r1=' + r1 + '&r2=' + r2 + '&r3=' + r3 + '&r4=' + r4;
    param += '&p0=' + p0 + '&p1=' + p1 + '&p2=' + p2 + '&p3=' + p3 + '&p4=' + p4;
    param += '&s0=' + s0 + '&s1=' + s1 + '&s2=' + s2 + '&s3=' + s3 + '&s4=' + s4;
    //alert(param);
    post_response_text('sdm_slaveSavePph21HOKontribusi.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Saved');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function savePph21ByJabatan() {
    persen = document.getElementById('persen').value;
    persen = replaceComma(persen);
    max = document.getElementById('max').value;
    max = replaceComma(max);
    param = 'persen=' + persen + '&max=' + max;
    if (max == '' || persen == '') {
        alert('Data inconsistent');
    } else {
        if (confirm('Are you sure..?'))
            post_response_text('sdm_slaveSavePph21HOByJabatan.php', param, respon);
    }

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Saved');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}

function savePPh21Component(obj, id) {
    if (obj.checked)
        to = 1;
    else
        to = 0;
    param = 'to=' + to + '&idx=' + id;
    post_response_text('sdm_slaveSavePph21HOComponen.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Saved');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function showPPh21Monthly() {
    //get Options
    regular = 'yes';
    thr = 'yes';
    jaspro = 'yes';
    jmsperusahaan = 'yes';
    if (document.getElementById('regular').checked == false)
        regular = 'no';
    if (document.getElementById('thr').checked == false)
        thr = 'no';
    if (document.getElementById('jaspro').checked == false)
        jaspro = 'no';
    if (document.getElementById('jmsperusahaan').checked == false)
        jmsperusahaan = 'no';
    periode = document.getElementById('bulanan').options[document.getElementById('bulanan').selectedIndex].value;
    kodeorg = document.getElementById('kodeorg1').options[document.getElementById('kodeorg1').selectedIndex].value;

    param = 'periode=' + periode + '&kodeorg='+ kodeorg +'&regular=' + regular + '&thr=' + thr + '&jaspro=' + jaspro + '&jmsperusahaan=' + jmsperusahaan;
    post_response_text('sdm_slaveShowPph21HOBulanan.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tbody').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function showPPh21Yearly() {
    //get Options
    regular = 'yes';
    thr = 'yes';
    jaspro = 'yes';
    jmsperusahaan = 'yes';
    if (document.getElementById('regular').checked == false)
        regular = 'no';
    if (document.getElementById('thr').checked == false)
        thr = 'no';
    if (document.getElementById('jaspro').checked == false)
        jaspro = 'no';
    if (document.getElementById('jmsperusahaan').checked == false)
        jmsperusahaan = 'no';
    periode = document.getElementById('tahun').options[document.getElementById('tahun').selectedIndex].value;

    param = 'periode=' + periode + '&regular=' + regular + '&thr=' + thr + '&jaspro=' + jaspro + '&jmsperusahaan=' + jmsperusahaan;
    post_response_text('sdm_slaveShowPph21HOYearly.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tbodyYear').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function convertPPh21Excel(jenis) {
    //get Options
    regular = 'yes';
    thr = 'yes';
    jaspro = 'yes';
    jmsperusahaan = 'yes';
    if (document.getElementById('regular').checked == false)
        regular = 'no';
    if (document.getElementById('thr').checked == false)
        thr = 'no';
    if (document.getElementById('jaspro').checked == false)
        jaspro = 'no';
    if (document.getElementById('jmsperusahaan').checked == false)
        jmsperusahaan = 'no';
    if (jenis == 'bulan') {
        jenis = 'bulanan';
        periode = document.getElementById('bulanan').options[document.getElementById('bulanan').selectedIndex].value;
        param = 'periode=' + periode + '&regular=' + regular + '&thr=' + thr + '&jaspro=' + jaspro + '&jmsperusahaan=' + jmsperusahaan + '&jenis=' + jenis;
        document.getElementById('ifrm').src = 'sdm_slavePPh21HOToExcel.php?' + param;
    } else {
        jenis = 'tahunan';
        periode = document.getElementById('tahun').options[document.getElementById('tahun').selectedIndex].value;
        param = 'periode=' + periode + '&regular=' + regular + '&thr=' + thr + '&jaspro=' + jaspro + '&jmsperusahaan=' + jmsperusahaan + '&jenis=' + jenis;
        document.getElementById('ifrm1').src = 'sdm_slavePPh21HOToExcel.php?' + param;
    }
}

function pyPreview() {
    periode = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tipe = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
    operator = document.getElementById('user').options[document.getElementById('user').selectedIndex].value;
    if (operator == '')
        username = '';
    else
        username = operator;
    param = 'periode=' + periode + '&tipe=' + tipe + '&username=' + username;
    //alert(param);
    post_response_text('sdm_slaveShowPayrollHOPrint.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('output').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function pyPreviewExcel() {
    periode = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tipe = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
    operator = document.getElementById('user').options[document.getElementById('user').selectedIndex].value;
    if (operator == '')
        username = '';
    else
        username = operator;
    param = 'periode=' + periode + '&tipe=' + tipe + '&username=' + username;

    document.getElementById('output').innerHTML = "<iframe src=\"sdm_slaveShowPayrollHOPrintExcel.php?" + param + "\" frameborder=0></iframe>";
}

function pPDF(event) {
    periode = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tipe = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
    operator = document.getElementById('user').options[document.getElementById('user').selectedIndex].value;
    if (operator == '')
        username = '';
    else
        username = operator;
    printPyPDF(periode, tipe, event, username);
}

function angsuranPDF(ev) {
    val = document.getElementById('val').value;
    param = 'string=' + val;
    tujuan = 'sdm_slave_print_angsuran_pdf.php?' + param;
    //display window
    title = 'Angsusan';
    width = '700';
    height = '400';
    content = "<iframe frameborder=0 width=100% height=100% src='" + tujuan + "'></iframe>"
    showDialog1(title, content, width, height, ev);
}

function printPyPDF(periode, tipe, evt, username) {
    pos = new Array();
    pos = getMouseP(evt);
    win = "<img src=images/closebig.gif align=right onclick=hideById('pdf'); title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
    win += "<iframe height=450px width=100%  frameborder=0 src='sdm_slavePrintPayrollHOPDF.php?periode=" + periode + "&tipe=" + tipe + "&username=" + username + "'></iframe>"
    document.getElementById('pdf').innerHTML = win;
    document.getElementById('pdf').style.top = pos[1] + 'px';
    document.getElementById('pdf').style.left = '75px';
    document.getElementById('pdf').style.display = '';
}



function printBank(event, bank) {
    periode = document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    tipe = document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value;
    operator = document.getElementById('user').options[document.getElementById('user').selectedIndex].value;
    if (operator == '')
        username = '';
    else
        username = operator;
    printBankExcel(periode, event, bank, username)
}

function printBankExcel(periode, event, bank, username) {
    pos = new Array();
    pos = getMouseP(event);
    tgltrf = prompt("Transfer date:");
    while (trim(tgltrf) == '') {
        tgltrf = prompt("Transfer date:");
    }
    win = "<img src=images/closebig.gif align=right onclick=hideById('pdf'); title='Close detail' class=closebtn onmouseover=\"this.src='images/closebigon.gif';\" onmouseout=\"this.src='images/closebig.gif';\"><br><br>";
    win += "<iframe height=450px width=100%  frameborder=0 src='sdm_slavePrintPayrollHOBank.php?periode=" + periode + "&tipe=" + tipe + "&tanggaltrf=" + tgltrf + "&username=" + username + "'></iframe>"
    document.getElementById('pdf').innerHTML = win;
    document.getElementById('pdf').style.top = pos[1] + 'px';
    document.getElementById('pdf').style.left = '75px';
    document.getElementById('pdf').style.display = '';
}

function thrSetup(obj, val) {
    if (obj.checked) {
        param = 'action=insert&id=' + val;
    } else {
        param = 'action=delete&id=' + val;
    }

    post_response_text('sdm_slaveTHRHOSetup.php', param, respon);

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

curRow = 0;

function saveTHR(x) {
    curRow = 0;
    if (confirm('Saving THR, are you sure..?'))
        loopTHR(x);
}

function saveBonus(x) {
    curRow = 0;
    if (confirm('Saving Bonus, are you sure..?'))
        loopBonus(x);
}

function loopTHR(x) {
    curRow += 1;
    loadTerbilang(document.getElementById('thr' + curRow), curRow, '');
    userid = document.getElementById('userid' + curRow).innerHTML;
    val = document.getElementById('thr' + curRow).value;
    terbil = document.getElementById('terbilang' + curRow).innerHTML;
    while (val.indexOf(",") > -1) {
        val = val.replace(",", "");
    }
    param = 'userid=' + userid + '&val=' + val + '&terbilang=' + terbil;
    post_response_text('sdm_slaveSaveTHRHO.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('thr' + curRow).style.backgroundColor = 'red';
                } else {
                    document.getElementById('thr' + curRow).style.backgroundColor = 'lightblue';
                    if (curRow <= x) {
                        loopTHR(x);
                    } else {
                        alert('All Saved');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function loopBonus(x) {
    curRow += 1;
    loadTerbilang(document.getElementById('bns' + curRow), curRow, '');
    userid = document.getElementById('userid' + curRow).innerHTML;
    val = document.getElementById('bns' + curRow).value;
    terbil = document.getElementById('terbilang' + curRow).innerHTML;
    while (val.indexOf(",") > -1) {
        val = val.replace(",", "");
    }
    param = 'userid=' + userid + '&val=' + val + '&terbilang=' + terbil;
    post_response_text('sdm_slaveSaveBonusHO.php', param, respon);

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('bns' + curRow).style.backgroundColor = 'red';
                } else {
                    document.getElementById('bns' + curRow).style.backgroundColor = 'lightblue';
                    if (curRow <= x) {
                        loopBonus(x);
                    } else {
                        alert('All Saved');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function bonusSetup(obj, val) {
    if (obj.checked) {
        param = 'action=insert&id=' + val;
    } else {
        param = 'action=delete&id=' + val;
    }

    post_response_text('sdm_slaveBonusHOSetup.php', param, respon);

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

function loadTerbilang(obj, row, value) {
    rupiahkan(obj, 'terbilang' + row);
}

function hitungKar() {

}

function ambilpersen() {
    // beban = document.getElementById('beban').options[document.getElementById('beban').selectedIndex].value;
    lokres = document.getElementById('lokres').options[document.getElementById('lokres').selectedIndex].value;
    param = '&lokres=' + lokres + '&proses=getPersentase';
    tujuan = 'sdm_slaveSaveJmsHOSetting.php';
    post_response_text(tujuan, param, respog);

    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //alert(con.responseText);
                    isipersen = JSON.parse(con.responseText);
                    // console.log(isipersen[0].value);

                    // Karyawan
                    document.getElementById('jhtkar').value = isipersen[0].jhtkar;
                    document.getElementById('jpkar').value = isipersen[0].jpkar;
                    document.getElementById('bpjskar').value = isipersen[0].bpjskar;
                    document.getElementById('karyawan').value = isipersen[0].karyawan;
                    document.getElementById('bpjsmk').value = isipersen[0].bpjsmk;
                    document.getElementById('jmpk').value = isipersen[0].jmpk;

                    // Perusahaan
                    document.getElementById('jhtpt').value = isipersen[1].jhtpt;
                    document.getElementById('jppt').value = isipersen[1].jppt;
                    document.getElementById('jkkpt').value = isipersen[1].jkkpt;
                    document.getElementById('jkmpt').value = isipersen[1].jkmpt;
                    document.getElementById('bpjspt').value = isipersen[1].bpjspt;
                    document.getElementById('perusahaan').value = isipersen[1].perusahaan;
                    document.getElementById('jmppt').value = isipersen[1].jmppt;                    
                    document.getElementById('bpjsmpt').value = isipersen[1].bpjsmpt;
                    
                    // Usia Pensiun
                    document.getElementById('usiapensiun').value = isipersen[2].value;
                    
                    hitungjhtPT();
                    hitungjhtKar();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function clearJmsPorsi() {
    //document.getElementById('beban').disabled = false;
    //document.getElementById('lokres').disabled = true;
    window.location.reload();
}

function cekDateUnder(no) {
    firstbl = document.getElementById('firstbl' + no).options[document.getElementById('firstbl' + no).selectedIndex].value;
    firstth = document.getElementById('firstth' + no).options[document.getElementById('firstth' + no).selectedIndex].value;
    lastbl = document.getElementById('lastbl' + no).options[document.getElementById('lastbl' + no).selectedIndex].value;
    lastth = document.getElementById('lastth' + no).options[document.getElementById('lastth' + no).selectedIndex].value;

    var a = false;

    if (firstth != '' && lastth != '' && firstbl != '' && lastbl != '') {
        if (firstth > lastth) {
            a = true;
            document.getElementById('firstth' + no).selectedIndex = 0;
        }

        if (lastth < firstth) {
            a = true;
            document.getElementById('lastth' + no).selectedIndex = 0;
        }

        if (firstth == lastth && firstbl > lastbl) {
            a = true;
            document.getElementById('firstbl' + no).selectedIndex = 0;
        }

        if (lastth == firstth && lastbl < firstbl) {
            a = true;
            document.getElementById('firstbl' + no).selectedIndex = 0;
        }

        if (a) {
            alert('Last payment tidak boleh lebih kecil dari 1st payment');
        }
    }
}