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
    var nosj = document.getElementById('sNoSj'),
		where = '[["nosj","'+nosj.value+'"]]';
    
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
    
    post_response_text('log_slave_suratjalan.php?proses=showHeadList', param, respon);
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
    
    post_response_text('log_slave_suratjalan.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField'),
		param = "nosj="+getValue('nosj')+"&kodept="+getValue('kodept');
    
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
    
    post_response_text('log_slave_suratjalan.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField'),
		nosj = document.getElementById('nosj_'+num).getAttribute('value'),
		kodept = document.getElementById('kodept_'+num).getAttribute('value'),
		param = "nosj="+nosj+"&kodept="+kodept;
    
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
    
    post_response_text('log_slave_suratjalan.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "nosj="+getValue('nosj')+"&kodept="+getValue('kodept')+"&kodeorg="+getValue('kodeorg')+"&tanggal="+getValue('tanggal')+"&tanggalkirim="+getValue('tanggalkirim');
    param += "&expeditor="+getValue('expeditor')+"&nopol="+getValue('nopol');
    param += "&jeniskend="+getValue('jeniskend')+"&driver="+getValue('driver');
    param += "&hpdriver="+getValue('hpdriver')+"&pengirim="+getValue('pengirim');
    param += "&penerima="+getValue('penerima')+"&checkedby="+getValue('checkedby');
	param += "&franco="+getValue('franco')+"&transportasi="+getValue('transportasi');
    
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
    
    post_response_text('log_slave_suratjalan.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "nosj="+getValue('nosj')+"&kodept="+getValue('kodept')+"&kodeorg="+getValue('kodeorg')+"&tanggal="+getValue('tanggal')+"&tanggalkirim="+getValue('tanggalkirim');
    param += "&expeditor="+getValue('expeditor')+"&nopol="+getValue('nopol');
    param += "&jeniskend="+getValue('jeniskend')+"&driver="+getValue('driver');
    param += "&hpdriver="+getValue('hpdriver')+"&pengirim="+getValue('pengirim');
    param += "&penerima="+getValue('penerima')+"&checkedby="+getValue('checkedby');
	param += "&franco="+getValue('franco')+"&transportasi="+getValue('transportasi');
    
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
    
    post_response_text('log_slave_suratjalan.php?proses=edit', param, respon);
}

/*
 * Detail
 */

function showDetail(tipe) {
    var detailField = document.getElementById('detailField'),
		param = "nosj="+getValue('nosj')+"&kodept="+getValue('kodept');
    
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
                        } else if(tipe=='pl') {
                            findPL();
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
    
    post_response_text('log_slave_suratjalan_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var nosj = document.getElementById('nosj_'+num).innerHTML,
		param = "nosj="+nosj;
    
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
    
	if(confirm("Transaction: "+nosj+" will be deleted.\nAre you sure?"))
		post_response_text('log_slave_suratjalan.php?proses=delete', param, respon);
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_suratjalan_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function postingData(row) {
    var nosj=document.getElementById('nosj_'+row).innerHTML,
		tanggaltiba=document.getElementById('tanggaltiba_'+row+'_el').value,
		param='nosj='+nosj+'&tanggaltiba='+tanggaltiba;
        tujuan='log_slave_suratjalan.php?proses=posting';
	if(tanggaltiba=='') {
		alert('Arrival Date must be filled');
		return;
	}
	
	if(confirm('Anda yakin dokumen telah lengkap..?'))
		post_response_text(tujuan, param, respog);
	
	function respog() {
        if(con.readyState==4) {
            if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					x=document.getElementById('tr_'+row);
					x.cells[4].innerHTML=con.responseText;
					x.cells[5].childNodes[0].disabled = true;
					x.cells[8].innerHTML="<img class='zImgBtn' title=Lengkap' src='images/skyblue/posted.png'>";
					x.cells[6].childNodes[0].onclick='';
					x.cells[7].childNodes[0].onclick='';
				}
			} else {
				busy_off();
				error_catch(con.status);
            }
        }
	}
}

function detailPDF(numRow,ev) {
    // Prep Param
    var nosj = document.getElementById('nosj_'+numRow).getAttribute('value'),
		param = "proses=pdf&nosj="+nosj;
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='log_slave_suratjalan_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

/**
 * showPO
 * Tampilkan box pencarian PO
 */
function showPO(ev) {
	var param = "tipe=PO&nosj="+getValue('nosj')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_suratjalan_popup.php?'+param;
    
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
//        " src='log_slave_suratjalan_popup.php?"+param+"'></iframe>",'800','400',ev);
}

/**
 * showPL
 * Tampilkan box pencarian Package List
 */
function showPL(ev) {
	var param = "tipe=PL&nosj="+getValue('nosj')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_suratjalan_popup.php?'+param;
    
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
                    showDialog1('Find Package List',"<div id='popupCont'></div>",'800','400',ev);
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
 * showMaterial
 * Tampilkan box pencarian Material
 */
function showMaterial(ev) {
	var param = "tipe=M&nosj="+getValue('nosj')+"&kodept="+getValue('kodept'),
        tujuan='log_slave_suratjalan_popup.php?'+param;
    
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
	var po = document.getElementById('po').value,
        nosj = getById('nosj').value,
		param='po='+po+'&kodept='+getValue('kodept')+'&nosj='+nosj,
        tujuan='log_slave_suratjalan_po.php';
	
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
 * findPL
 * Find Package List based on query
 */
function findPL() {
	var pl = document.getElementById('pl').value,
		nosj = getById('nosj').value,
		param='pl='+pl+'&kodept='+getValue('kodept')+'&nosj='+nosj,
        tujuan='log_slave_suratjalan_pl.php';
	
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
		nosj = getById('nosj').value,
		param='mat='+mat+'&kodept='+getValue('kodept')+'&nosj='+nosj,
        tujuan='log_slave_suratjalan_mat.php';
	
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
	var nosj = document.getElementById('nosj').value,
		param='nosj='+nosj+'&kodept='+getValue('kodept')+'&jenis='+tipe,
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_suratjalan_detail.php?proses=add2detail';
	
	var j=0;
	for(var i=0;i<body.rows.length;i++) {
		if(getById(tipe+'_'+i).checked) {
			if (tipe=='po') {
				param+="&data["+j+"][nopo]="+getInner('nopo_'+i);
				param+="&data["+j+"][kodebarang]="+getInner('kodebarang_'+i);
				param+="&data["+j+"][namabarang]="+getInner('namabarang_'+i);
				param+="&data["+j+"][nopp]="+getInner('nopp_'+i);
				param+="&data["+j+"][jumlah]="+getInner('jumlah_'+i);
				param+="&data["+j+"][satuan]="+getInner('satuan_'+i);
			} else if (tipe=='pl') {
				param+="&data["+j+"][kodebarang]="+getInner('notransaksi_'+i);
			} else {
				param+="&data["+j+"][kodebarang]="+getInner('kodebarang_'+i);
				param+="&data["+j+"][namabarang]="+getInner('namabarang_'+i);
				param+="&data["+j+"][satuan]="+getInner('satuan_'+i);
			}
			
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
	var nosj = document.getElementById('nosj').value,
		jml = getById('el_jumlah_'+num),
		elPo = getById('el_nopo_'+num),
		elPp = getById('el_nopp_'+num),
        nopo = getById('t_nopo_'+num),
		param='nosj='+nosj+'&kodept='+getValue('kodept')
			+'&kodebarang='+getInner('t_kodebarang_'+num)
			+'&nopo='+nopo.getAttribute('value')+'&jumlah='+jml.value;
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_suratjalan_detail.php?proses=saveDetail';
	
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
                    nopo.setAttribute("value",elPo.value);
					if(elPo) elPo.disabled = false;
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
	var nosj = document.getElementById('nosj').value,
		jml = getById('el_jumlah_'+num),
		param='nosj='+nosj+'&kodept='+getValue('kodept')
			+'&kodebarang='+getInner('t_kodebarang_'+num)
			+'&nopo='+getInner('t_nopo_'+num),
		body = document.getElementById('bodySearch'),
        tujuan='log_slave_suratjalan_detail.php?proses=deleteDetail';
	if(jml) {
		param +='&jumlah='+jml.value;
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