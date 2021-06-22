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
    var postBtn = document.getElementById('postBtn');
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
                   listCon.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_prosesgjbulanan.php?proses=list', param, respon);
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
    
    post_response_text('sdm_slave_prosesgjbulanan.php?proses=post', param, respon);
}