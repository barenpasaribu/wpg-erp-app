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

function biPrint(level,page,targetId,field,rowNum) {
    var target = document.getElementById(targetId);
    if(typeof rowNum=='undefined') {
        rowNum = 'none';
    }
    
    var fieldArr = field.split('##');
    var param = "";
    for(i=1;i<fieldArr.length;i++) {
        if(i==1) {
            if(rowNum=='none') {
                param += fieldArr[i]+"="+getValue(fieldArr[i]);
            } else {
                param += fieldArr[i]+"="+getValue(fieldArr[i]+"_"+rowNum);
            }
        } else {
            if(rowNum=='none') {
                param += "&"+fieldArr[i]+"="+getValue(fieldArr[i]);
            } else {
                param += "&"+fieldArr[i]+"="+getValue(fieldArr[i]+"_"+rowNum);
            }
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
                    target.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text(page+'.php?level='+level, param, respon);
}