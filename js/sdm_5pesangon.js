function getEls() {
	return document.getElementsByTagName('input');
}

function setZero(cond) {
	var elInput = getEls();
	if(typeof cond=='undefined') {
		cond = false;
	}
	for(var i=0;i<elInput.length;i++) {
		var tmp = getById(elInput[i].id);
		if(cond==false) {
			if(tmp.value=='') {
				tmp.value=0;
			}
		} else {
			tmp.value=0;
		}
	}
}

function add() {
	var elInput = getEls(),param='';
	setZero();
	
	for(var i=0;i<elInput.length;i++) {
		if(param!='') {param+='&';}
		param += elInput[i].id+'='+elInput[i].value;
	}
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    cancel();
					list();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_5pesangon.php?proses=add', param, respon);
}

function edit() {
	var elInput = getEls(),param='';
	setZero();
	
	for(var i=0;i<elInput.length;i++) {
		if(param!='') {param+='&';}
		param += elInput[i].id+'='+elInput[i].value;
		if(elInput[i].id!='rowNum') {
			param += '&ref['+elInput[i].id+']='+getById(elInput[i].id+'_'+elInput['rowNum'].value).getAttribute('value');
		}
	}
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
					list();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_5pesangon.php?proses=edit', param, respon);
}

function deleteData(row) {
	var elInput = getEls(),param='';
	setZero();
	
	for(var i=0;i<elInput.length;i++) {
		if(param!='') {param+='&';}
		if(elInput[i].id=='rowNum') {
			param += elInput[i].id+'='+row;
		} else {
			param += elInput[i].id+'='+getById(elInput[i].id+'_'+row).getAttribute('value');
		}
	}
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    cancel();
					var tr = getById('masakerja_'+row).parentNode;
					tr.parentNode.removeChild(tr);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
	if(confirm("Anda akan menghapus data masa kerja "+getById('masakerja_'+row).innerHTML+"?"))
		post_response_text('sdm_slave_5pesangon.php?proses=delete', param, respon);
}

function editMode(row) {
	var elInput = getEls();
	document.getElementById('addBtn').style.display='none';
	document.getElementById('editBtn').style.display='';
	document.getElementById('rowNum').value=row;
	for(var i=0;i<elInput.length;i++) {
		if(document.getElementById(elInput[i].id+'_'+row)) {
			elInput[i].value = getById(elInput[i].id+'_'+row).getAttribute('value');
		}
	}
}

function cancel() {
	document.getElementById('addBtn').style.display='';
	document.getElementById('editBtn').style.display='none';
	document.getElementById('rowNum').value='';
	setZero(true);
}

function list() {
	var param = '';
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    getById('tBody').innerHTML  = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
	post_response_text('sdm_slave_5pesangon.php?proses=list', param, respon);
}