var showPerPage = 10;

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

/* Search
 * Filtering Data
 */
function searchTrans() {
    var notrans = document.getElementById('sNoTrans');
    var tanggal = getValue('sTanggal');
    if(tanggal!='') {
        var tmpTanggal = tanggal.split('-');
        var tanggalR = tmpTanggal[2]+"-"+tmpTanggal[1]+"-"+tmpTanggal[0];
    } else {
        var tanggalR = '';
    }
    var where = '[["novp","'+notrans.value+'"],["tanggal","'+tanggalR+'"]]';
    
    goToPages(1,showPerPage,where);
}

/* Paging
 * Paging Data
 */
function defaultList() {
    goToPages(1,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('workField');
    var param = "page="+page;
    param += "&shows="+shows;
    if(typeof where != 'undefined') {
        param+="&where="+newWhere;
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
    
    post_response_text('keu_slave_vp.php?proses=showHeadList', param, respon);
}

function choosePage(obj,shows,where) {
    var pageVal = obj.options[obj.selectedIndex].value;
    goToPages(pageVal,shows,where);
}

/* Halaman Manipulasi Data
 * Halaman add, edit, delete
 */
function showAdd() {
    var workField = document.getElementById('workField');
    var param = "";
    
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
    
    post_response_text('keu_slave_vp.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('novp');
    var param = "novp="+trans.value+"&kodeorg="+getValue('kodeorg')+
        "&noakun="+getValue('noakun2a')+"&tipetransaksi="+getValue('tipetransaksi');
    
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
    
    post_response_text('keu_slave_vp.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField');
    var trans = document.getElementById('novp_'+num).getAttribute('value');
    var param = "numRow="+num+"&novp="+trans;
    
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
    
    post_response_text('keu_slave_vp.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "novp="+getValue('novp')+"&tanggal="+getValue('tanggal'),
		listInv = getById('listInvoice').childNodes.length;
	param += "&tanggalterima="+getValue('tanggalterima')+"&tanggalbayar="+getValue('tanggalbayar')+
		"&tanggaljatuhtempo="+getValue('tanggaljatuhtempo');
    param += "&nopo="+getValue('nopo')+"&penjelasan="+getValue('penjelasan');
    for(var i=0;i<listInv;i++){
		param += "&noinv[]="+getById('noinv_'+i).innerHTML;
	}
	
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Added Data Header');
					document.getElementById('novp').value = con.responseText;
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_vp.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "novp="+getValue('novp')+"&tanggal="+getValue('tanggal');
	param += "&tanggalterima="+getValue('tanggalterima')+"&tanggalbayar="+getValue('tanggalbayar')+
		"&tanggaljatuhtempo="+getValue('tanggaljatuhtempo');
    param += "&nopo="+getValue('nopo')+"&penjelasan="+getValue('penjelasan');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert(con.responseText);
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('keu_slave_vp.php?proses=edit', param, respon);
}

/*
 * Detail
 */
function showDetail() {
    var detailField = document.getElementById('detailField'),
        novp = document.getElementById('novp').value,
        param = "novp="+novp;
    
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
    
    post_response_text('keu_slave_vp_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var novp = document.getElementById('novp_'+num).getAttribute('value'),
        param = "novp="+novp;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
	if(confirm("Anda akan menghapus data transaksi "+novp+"\nAnda yakin?"))
		post_response_text('keu_slave_vp.php?proses=delete', param, respon);
}

/* Posting Data
 */
function postingData(numRow) {
    var novp = document.getElementById('novp_'+numRow).getAttribute('value'),
		param = "novp="+novp;
    
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    x=document.getElementById('tr_'+numRow);
					x.cells[4].innerHTML='';
					x.cells[5].innerHTML='';
                    x.cells[6].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Posting '+novp+'\nThis transaction will released. are you sure?')) {
        post_response_text('keu_slave_vp_posting.php', param, respon);
    }
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_vp_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev) {
    // Prep Param
    var novp = document.getElementById('novp_'+numRow).getAttribute('value');
    param = "proses=pdf&novp="+novp;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='keu_slave_vp_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

/**
 * getPO
 * Show dialog for get nomor
 */
function getPO(ev) {
	var param = "tipe=PO&nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept'),
	    tujuan='keu_slave_vp_popup.php?'+param;
    
    post_response_text(tujuan, param, respog);
	
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    showDialog1('Find PO',"<div id='popupCont'></div>",'800','400',ev);
					document.getElementById('popupCont').innerHTML = con.responseText;
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '50px';
                    dialog.style.left = '15%';
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
	// showDialog1('Find PO',"<iframe frameborder=0 style='width:795px;height:400px'"+
        // " src='keu_slave_vp_popup.php?"+param+"'></iframe>",'800','400',ev);
}

/**
 * findPO
 * Display List of PO
 */
function findPO() {
	var po = document.getElementById('po').value,
		param='po='+po+'&tipe='+getValue('tipe'),
        tujuan='keu_slave_vp_po.php?proses=po';
	
	post_response_text(tujuan, param, respog);
	
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    var contPO = document.getElementById('hasilPO'),
                        contInvoice = document.getElementById('hasilInvoice');
                    contPO.innerHTML = con.responseText;
                    contPO.style.display = "";
                    contInvoice.style.display = "none";
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * findInvoice
 * Display Invoice List of PO
 */
function findInvoice(obj) {
	var param='po='+obj.getAttribute('nopo')+'&tipe='+obj.getAttribute('tipe');
        tujuan='keu_slave_vp_po.php?proses=invoice';
	
	post_response_text(tujuan, param, respog);
	
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    var contPO = document.getElementById('hasilPO'),
                        contInvoice = document.getElementById('hasilInvoice');
                    contInvoice.innerHTML = con.responseText;
                    contInvoice.style.display = "";
                    contPO.style.display = "none";
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * setPoInv
 * Set No PO dan Invoice
 */
function setPoInv() {
    var tbody = getById('t_inv_body'),
        nopo = getInner('t_inv_nopo'),
        rows = tbody.childNodes.length,
		listInv = getById('listInvoice'),
		totalRp = getById('totalRpInv'),
        res = {};
    
    // Get Result
    res.nopo = nopo;
    res.invoice = new Array();
	var totalInvoice = 0;
    for (var i=0;i<rows;i++) {
        var tmp = getById('el_inv_'+i),
            tmp2 = getById('t_noinvoice_'+i),
			tmp3 = getById('t_nilaiinvoice_'+i).getAttribute('value');
			
        if (tmp.checked) {
            res.invoice.push(tmp2.innerHTML);
			totalInvoice += parseFloat(tmp3);
        }
    }
    if (res.invoice.length>4) {
        alert("Maximum Invoice selected is 4");return;
    }
    
    // Set Result
    document.getElementById('nopo').value = res.nopo;
	totalRp.value = totalInvoice;
	var tmpInv = "";
    for (i in res.invoice) {
		tmpInv += "<div id='noinv_"+i+"'>"+res.invoice[i]+"</div>";
    }
    listInv.innerHTML = tmpInv;
    closeDialog();
}

/**
 * selAll
 * Select All untuk list PO/Kontrak/SJ/Kono
 */
function selAll() {
	var tbodyLen = getById('t_inv_body').childNodes.length;
	for(var i=0;i<tbodyLen;i++) {
		getById('el_inv_'+i).setAttribute('checked',true);
	}
}

/**
 * zoom
 * Show List Invoice
 */
function zoom(numRow,ev) {
	var novp = document.getElementById('novp_'+numRow).getAttribute('value'),
		tujuan='keu_slave_vp.php?proses=showInvoice';
    
	param = "proses=pdf&novp="+novp;
    post_response_text(tujuan, param, respog);
	
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				} else {
                    showDialog1('Invoice List',"<div id=listInvoiceHeader style='overflow: auto;max-height: 100px;'>"+
						con.responseText+"</div>",'250','100',ev);
					var dialog = document.getElementById('dynamic1');
					dialog.style.top = dialog.style.top+15;
					dialog.style.left = '75%';
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}