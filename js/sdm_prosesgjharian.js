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

/* Show Data List */
function list() {
    var listCon = document.getElementById('listContainer');
    //var postBtn = document.getElementById('postBtn');
    var param = "periodegaji="+getValue('periodegaji');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    //postBtn.setAttribute('disabled','disabled');
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                     listCon.innerHTML=con.responseText;
                    /*
                    eval('var res ='+con.responseText);
                    listCon.innerHTML = res['list'];
                    if(res['neg']==false) {
                        postBtn.removeAttribute('disabled');
                    } else {
                        postBtn.setAttribute('disabled','disabled');
                        alert('Masih ada Karyawan dengan gaji kurang dari 0');
                    }
                    */
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_3prosesgjharian.php?proses=list', param, respon);
}

/* Post */
function post() {
    var param = "periodegaji="+getValue('periodegaji');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    alert('Posting Success');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_3prosesgjharian.php?proses=post', param, respon);
}