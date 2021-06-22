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

/* toggleActive
 * Fungsi untuk disabled enable suatu field
 */
function toggleActive(obj,idTarget) {
    var tmpTarget = document.getElementById(idTarget);
    if(obj.checked==false) {
        tmpTarget.setAttribute('disabled','disabled');
    } else {
        tmpTarget.removeAttribute('disabled');
    }
}

/** Print **/
function print(mode,primeField,advField,page) {
    var workField = document.getElementById('workField');
    var field = primeField+advField;
    var fieldArr = field.split('##');
    var param = "";
    for(i=1;i<fieldArr.length;i++) {
        if(i==1) {
            param += fieldArr[i]+"="+getValue(fieldArr[i]);
        } else {
            param += "&"+fieldArr[i]+"="+getValue(fieldArr[i]);
        }
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    switch(mode) {
                        case 'preview':
                            workField.innerHTML = con.responseText;
                            break;
                        default:
                            alert('Print Mode not defined');
                            break;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(page+'.php?mode='+mode, param, respon);
}