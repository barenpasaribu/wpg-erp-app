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
    var nokonosemen = document.getElementById('snokonosemen'),
		where = '[["nokonosemen","'+nokonosemen.value+'"]]';
    
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
    param += "&shows="+shows+"&tipe=KB";
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
    
    post_response_text('log_slave_konosemen.php?proses=showHeadList', param, respon);
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
    
    post_response_text('log_slave_konosemen.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField'),
		param = "nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept');
    
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
    
    post_response_text('log_slave_konosemen.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField'),
		nokonosemen = document.getElementById('nokonosemen_'+num).getAttribute('value'),
		kodept = document.getElementById('kodept_'+num).getAttribute('value'),
		param = "nokonosemen="+nokonosemen+"&kodept="+kodept;
    
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
    
    post_response_text('log_slave_konosemen.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "nokonosemen="+getValue('nokonosemen')+"&nokonosemenexp="+getValue('nokonosemenexp')+"&kodept="+getValue('kodept')+"&kodeorg="+getValue('kodeorg');
	param += "&tanggal="+getValue('tanggal')+"&tanggalberangkat="+getValue('tanggalberangkat');
	param += "&tanggaltiba="+getValue('tanggaltiba');
    param += "&shipper="+getValue('shipper')+"&vessel="+getValue('vessel')+"&franco="+getValue('franco');
    param += "&asalbarang="+getValue('asalbarang')+"&pengirim="+getValue('pengirim');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_konosemen.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "nokonosemen="+getValue('nokonosemen')+"&nokonosemenexp="+getValue('nokonosemenexp')+"&kodept="+getValue('kodept')+"&kodeorg="+getValue('kodeorg');
	param += "&tanggal="+getValue('tanggal')+"&tanggalberangkat="+getValue('tanggalberangkat');
	param += "&tanggaltiba="+getValue('tanggaltiba');
    param += "&shipper="+getValue('shipper')+"&vessel="+getValue('vessel')+"&franco="+getValue('franco');
    param += "&asalbarang="+getValue('asalbarang')+"&pengirim="+getValue('pengirim');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_konosemen.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail(tipe) {
    var detailField = document.getElementById('detailField'),
		param = "nokonosemen="+getValue('nokonosemen');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    detailField.innerHTML = con.responseText;
                    
                    if (typeof tipe != 'undefined') {
                        if(tipe=='po') {
                            findPO();
                        } else if(tipe=='sj') {
                            findSJ();
                        } else {
							findMat();
						}
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('log_slave_konosemen_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var nokonosemen = document.getElementById('nokonosemen_'+num).innerHTML,
		param = "nokonosemen="+nokonosemen;
    
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
    
	if(confirm("Transaction: "+nokonosemen+" will be deleted.\nAre you sure?"))
		post_response_text('log_slave_konosemen.php?proses=delete', param, respon);
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_konosemen_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev) {
    // Prep Param
    var nokonosemen = document.getElementById('nokonosemen_'+numRow).getAttribute('value'),
		param = "proses=pdf&nokonosemen="+nokonosemen;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_konosemen_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

/**
 * showPO
 * Tampilkan box pencarian PO
 */
function showPO(ev) {
	var param = "tipe=PO&nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_konosemen_popup.php?'+param;
    
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
//	showDialog1('Find PO',"<iframe frameborder=0 style='width:795px;height:400px'"+
//        " src='log_slave_konosemen_popup.php?"+param+"'></iframe>",'800','400',ev);
}

/**
 * showSJ
 * Tampilkan box pencarian Surat Jalan
 */
function showSJ(ev) {
	var param = "tipe=SJ&nokonosemen="+getValue('nokonosemen')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_konosemen_popup.php?'+param;
    
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
                    showDialog1('Find Delivery Order',"<div id='popupCont'></div>",'800','400',ev);
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
//	showDialog1('Find Delivery Order',"<iframe frameborder=0 style='width:795px;height:400px'"+
//        " src='log_slave_konosemen_popup.php?"+param+"'></iframe>",'800','400',ev);
}

/**
 * showMaterial
 * Tampilkan box pencarian Material
 */
function showMaterial(ev) {
	var param = "tipe=M&nosj="+getValue('nosj')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_konosemen_popup.php?'+param;
    
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
                    showDialog1('Find Material',"<div id='popupCont'></div>",'800','400',ev);
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
}

/**
 * findPO
 * Find PO based on query
 */
function findPO() {
	var po = document.getElementById('po').value;
		param='po='+po+'&kodept='+getValue('kodept');
        tujuan='log_slave_konosemen_po.php';
	
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
					document.getElementById('hasilCari').innerHTML = con.responseText;
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * findSJ
 * Find Delivery Order based on query
 */
function findSJ() {
	var sj = document.getElementById('sj').value;
		param='sj='+sj+'&kodept='+getValue('kodept');
        tujuan='log_slave_konosemen_sj.php';
	
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
					document.getElementById('hasilCari').innerHTML = con.responseText;
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * findMat
 * Find Package List based on query
 */
function findMat() {
	var mat = document.getElementById('mat').value,
		nokonosemen = getById('nokonosemen').value,
		param='mat='+mat+'&kodept='+getValue('kodept')+'&nokonosemen='+nokonosemen,
        tujuan='log_slave_konosemen_mat.php';
	
	if(mat.length<3) {
		alert("Minimum character for search material is 3");
		return;
	}
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
					document.getElementById('hasilCari').innerHTML = con.responseText;
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * add2detail
 * Add Checklisted item to detail surat jalan
 */
function add2detail(tipe) {
	var nokonosemen = document.getElementById('nokonosemen').value,
		param='nokonosemen='+nokonosemen+'&kodept='+getValue('kodept')+'&jenis='+tipe,
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_konosemen_detail.php?proses=add2detail';
	
	var j=0;
	for(var i=0;i<body.rows.length;i++) {
		if(getById(tipe+'_'+i).checked) {
			if(document.getElementById('nosj_'+i)) {
				param+="&data["+j+"][nosj]="+getInner('nosj_'+i);
			}
            if(document.getElementById('nopo_'+i)) {
                param+="&data["+j+"][nopo]="+getInner('nopo_'+i);
            }
			if(document.getElementById('jenis_'+i)) {
				param+="&data["+j+"][jenis]="+getInner('jenis_'+i);
			}
			param+="&data["+j+"][kodebarang]="+getInner('kodebarang_'+i);
			param+="&data["+j+"][namabarang]="+getInner('namabarang_'+i);
            if(document.getElementById('nopp_'+i)) {
                param+="&data["+j+"][nopp]="+getInner('nopp_'+i);
            }
            if(document.getElementById('jumlah_'+i)) {
                param+="&data["+j+"][jumlah]="+getInner('jumlah_'+i);
            }
			param+="&data["+j+"][satuan]="+getInner('satuan_'+i);
			j++;
		}
	}
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
					//document.getElementById('detailField').innerHTML = con.responseText;
					showDetail(tipe);
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * changeBg
 * Change Background color
 */
function changeBg(obj, color) {
	obj.style.background = color;
}

/**
 * saveDetail
 * Update Detail Jumlah
 */
function saveDetail(num) {
	var nokonosemen = document.getElementById('nokonosemen').value,
		jml = getById('el_jumlah_'+num),
        elPo = getById('el_nopo_'+num),
		elPp = getById('el_nopp_'+num),
         nopo = getById('t_nopo_'+num),
		param='nokonosemen='+nokonosemen+'&kodept='+getValue('kodept')
			+'&kodebarang='+getInner('t_kodebarang_'+num)
			+'&nopo='+nopo.getAttribute('value')+'&jumlah='+jml.value,
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_konosemen_detail.php?proses=saveDetail';
	
    if(elPo) 
		param+='&newNopo='+elPo.value+'&newNopp='+elPp.value;
    
	jml.disabled = true;
    if(elPo) elPo.disabled = true;
	if(elPp) elPp.disabled = true;
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
					jml.disabled = false;
                    if(elPo) {
						elPo.disabled = false;
						nopo.setAttribute("value",elPo.value);
					}
					if(elPp) elPp.disabled = false;
					changeBg(getById('detRow_'+num),'#D7EBFA');
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * deleteDetail
 * Delete Data Detail
 */
function deleteDetail(num) {
	var nokonosemen = document.getElementById('nokonosemen').value,
		jml = getById('el_jumlah_'+num),
		param='nokonosemen='+nokonosemen+'&kodept='+getValue('kodept')
			+'&kodebarang='+getInner('t_kodebarang_'+num)
			+'&nopo='+getInner('t_nopo_'+num),
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_konosemen_detail.php?proses=deleteDetail';
	if(jml) {
		param+='&jumlah='+jml.value
		jml.disabled = true;
	}
	if(confirm("You're about to delete row number "+(parseInt(num)+1)+"\nAre you sure?"))
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
					showDetail();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * Posting Data
 */
function postingData(numRow) {
    var nokonosemen = document.getElementById('nokonosemen_'+numRow).getAttribute('value')
		param = "nokonosemen="+nokonosemen;
    
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    //alert('Posting Berhasil');
                    x=document.getElementById('tr_'+numRow);
					x.cells[7].innerHTML='';
					x.cells[8].innerHTML='';
                    x.cells[9].innerHTML="<img class=\"zImgOffBtn\" title=\"Posting\" src=\"images/skyblue/posted.png\">";
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Posting '+nokonosemen+'\nThis transaction will released. are you sure?')) {
        post_response_text('log_slave_konosemen.php?proses=posting', param, respon);
    }
}