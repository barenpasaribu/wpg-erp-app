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
        } else if(tmp.getAttribute('type')=='text') {
            return tmp.value;
        } else {
            return tmp.innerHTML
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
    var isBulan = document.getElementById(idTarget+"_bulan");
    var isTahun = document.getElementById(idTarget+"_tahun");
    
    if(obj.checked==false) {
        if(isBulan) {
            isBulan.setAttribute('disabled','disabled');
            isTahun.setAttribute('disabled','disabled');
        } else {
            tmpTarget.setAttribute('disabled','disabled');
        }
    } else {
        if(isBulan) {
            isBulan.removeAttribute('disabled');
            isTahun.removeAttribute('disabled');
        } else {
            tmpTarget.removeAttribute('disabled');
        }
    }
}

/** Print **/
function formPrint(mode,level,primeField,advField,page,ev,popFrame) {
    if(typeof popFrame=='undefined') {
        popFrame = false;
    } else {
        popFrame = true;
    }
    var workField = document.getElementById('workField');
    var field = primeField+advField;
    var fieldArr = field.split('##');
    var param = "";
    for(i=1;i<fieldArr.length;i++) {
        // Cek if Range Periode
        var tmp = document.getElementById(fieldArr[i]+"_from");
        if(i==1) {
            if(tmp) {
                param += fieldArr[i]+"_from"+"="+getValue(fieldArr[i]+"_from");
				try{
					param += "&"+fieldArr[i]+"_until"+"="+getValue(fieldArr[i]+"_until");
				}
				catch(e){}
				
            } else {
                param += fieldArr[i]+"="+getValue(fieldArr[i]);
            }
        } else {
            if(tmp) {
                param += "&"+fieldArr[i]+"_from"+"="+getValue(fieldArr[i]+"_from");
				
				try{
				param += "&"+fieldArr[i]+"_until"+"="+getValue(fieldArr[i]+"_until");
				}
				catch(e){}
				
                
            } else {
                param += "&"+fieldArr[i]+"="+getValue(fieldArr[i]);
            }
        }
		//alert(param);
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
                        case 'excel':
                            window.location='tempExcel/'+con.responseText+'.xls';
                            break;
                        default:
                            workField.innerHTML = con.responseText;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    switch(mode) {
        case 'preview':
        case 'excel':
            post_response_text(page+'.php?mode='+mode+'&level='+level, param, respon);
            break;
        case 'pdf':
            // Prep Param
            param += '&mode='+mode+'&level='+level;
            if(popFrame==false) {
                workField.innerHTML = "<iframe frameborder=0 style='width:100%;height:400px;'"+
                    " src='"+page+".php?"+param+"'></iframe>";
            } else {
                showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
                    " src='"+page+".php?"+param+"'></iframe>",'800','400',ev);
                var dialog = document.getElementById('dynamic1');
                dialog.style.top = '50px';
                dialog.style.left = '15%';
            }
            break;
        default:
            alert('Print Mode not defined');
            break;
    }
}