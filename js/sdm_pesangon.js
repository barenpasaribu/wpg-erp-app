var showPerPage = 10;

/* Search
 * Filtering Data
 */
function searchTrans() {
    var nosj = document.getElementById('sKary'),
		where = '[["b.namakaryawan","'+nosj.value+'"]]';
    
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
                    //== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon.php?proses=showHeadList', param, respon);
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
                    //== Success Response
                    workField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon.php?proses=showAdd', param, respon);
}

function showEditFromAdd() {
    var workField = document.getElementById('workField'),
		param = "karyawanid="+getValue('karyawanid');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon.php?proses=showEdit', param, respon);
}

function showEdit(num) {
    var workField = document.getElementById('workField'),
		param = "karyawanid="+getValue('karyawanid_'+num);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    workField.innerHTML = con.responseText;
                    showDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "karyawanid="+getValue('karyawanid')+"&periodegaji="+getValue('periodegaji');
    param += "&tanggal="+getValue('tanggal')+"&nodok="+getValue('nodok');
	param += "&alasankeluar="+getValue('alasankeluar')+"&tanggalkeluar="+getValue('tanggalkeluar');
    param += "&masakerja="+getValue('masakerja')+"&lembur="+getValue('lembur');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    showEditFromAdd();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "karyawanid="+getValue('karyawanid')+"&periodegaji="+getValue('periodegaji');
    param += "&tanggal="+getValue('tanggal')+"&nodok="+getValue('nodok');
	param += "&alasankeluar="+getValue('alasankeluar')+"&tanggalkeluar="+getValue('tanggalkeluar');
    param += "&masakerja="+getValue('masakerja')+"&lembur="+getValue('lembur');
    param += "&pengganti="+getValue('pengganti')+"&penghargaan="+getValue('penghargaan');
    param += "&pesangon="+getValue('detailPesangon')+"&perusahaan="+getValue('perusahaan');
    param += "&kesalahanbiasa="+getValue('kesalahanbiasa')+"&kesalahanbesar="+getValue('kesalahanberat');
    param += "&uangpisah="+getValue('uangpisah')+"&detailDiterima="+getValue('detailDiterima');
    param += "&uangpisah="+getValue('uangpisah')+"&detailTotal="+getValue('detailTotal');
    param += "&pph="+getValue('pph');
	
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('sdm_slave_pesangon.php?proses=edit', param, respon);
}
function DataTable() {
    var param = "karyawanid="+getValue('karyawanid')+"&periodegaji="+getValue('periodegaji');
    param += "&tanggal="+getValue('tanggal')+"&nodok="+getValue('nodok');
	param += "&alasankeluar="+getValue('alasankeluar')+"&tanggalkeluar="+getValue('tanggalkeluar');
    param += "&masakerja="+getValue('masakerja')+"&lembur="+getValue('lembur');
    param += "&pengganti="+getValue('pengganti')+"&penghargaan="+getValue('penghargaan');
    param += "&pesangon="+getValue('detailPesangon')+"&perusahaan="+getValue('perusahaan');
    param += "&kesalahanbiasa="+getValue('kesalahanbiasa')+"&kesalahanbesar="+getValue('kesalahanbesar');
    param += "&uangpisah="+getValue('uangpisah')+"&detailTotal="+getValue('detailTotal');
    param += "&pph="+getValue('pph');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('sdm_slave_pesangon.php?proses=edit', param, respon);
}
/*
 * Detail
 */

function showDetail() {
    var detailField = document.getElementById('detailField'),
		param = "karyawanid="+getValue('karyawanid');
		param += "&tanggalkeluar="+getValue('tanggalkeluar');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    detailField.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('sdm_slave_pesangon_detail.php?proses=showDetail', param, respon);
}

function deleteData(num) {
    var karyawanid = document.getElementById('karyawanid_'+num),
		param = "karyawanid="+karyawanid.getAttribute('value');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //== Success Response
                    var tmp = document.getElementById('tr_'+num);
                    tmp.parentNode.removeChild(tmp);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
	if(confirm("Transaction: "+karyawanid.innerHTML+" will be deleted.\nAre you sure?"))
		post_response_text('sdm_slave_pesangon.php?proses=delete', param, respon);
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='sdm_slave_pesangon_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}

function detailPDF(numRow,ev) {
    // Prep Param
    var karyawanid = document.getElementById('karyawanid_'+numRow).getAttribute('value'),
		param = "proses=pdf&karyawanid="+karyawanid;
    
    showDialog1('Print Detail PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='sdm_slave_pesangon_print_detail.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '100px';
    dialog.style.left = '15%';
}

/**
 * calcTotal
 * Calculate Sub Total, PPh & Total
 */
function calcTotal() {
	var cont = document.getElementById('detailCont'),
		els = document.getElementsByTagName('input'),
		subTotal = document.getElementById('subTotal'),
		pph = document.getElementById('pph'),
		diterima = document.getElementById('detailDiterima'),
		tmpSubTotal=0,
		tmpPph=0,
		sisa=0,
		totalPot=0;
	
	// Sub Total & PPh
	for(var i=0;i<els.length;i++) {
		if(els[i].getAttribute('dototal')=='total-plus') {
			tmpSubTotal += parseFloat(remove_comma_var(els[i].value));
		} else if(els[i].getAttribute('dototal')=='total-min') {
			totalPot += parseFloat(remove_comma_var(els[i].value));
		}
	}
	
	
	if (tmpSubTotal <= 50000000) {
		tmpPph = 0;
	}else if (tmpSubTotal > 50000000 && tmpSubTotal <= 100000000) {
		tmpPph = (tmpSubTotal - 50000000) * 0.05;
	}else if (tmpSubTotal > 100000000 && tmpSubTotal <= 500000000) {
		tmpPph = (50000000 * 0.05) + ((tmpSubTotal - 100000000) * 0.15);
	}else if (tmpSubTotal > 500000000) {
		tmpPph = (50000000 * 0.05) + (100000000 * 0.15) + ((tmpSubTotal - 500000000) * 0.25);
	} 
	
	
	subTotal.value = tmpSubTotal.toFixed(0);
	pph.value = tmpPph.toFixed(0);
	diterima.value = parseFloat(tmpSubTotal.toFixed(0))-parseFloat(tmpPph.toFixed(0))-parseFloat(totalPot.toFixed(0));
	z.numberFormat('subTotal');
	z.numberFormat('pph');
	z.numberFormat('detailDiterima');
}

function calcGanti(num) {
	var total = getById('ganti_total_'+num),
		pengali = getById('ganti_pengali_'+num),
		rp = getById('ganti_rp_'+num),tmp1,$tmp2;
	total.value = parseFloat(remove_comma_var(pengali.value))*parseFloat(remove_comma_var(rp.value));
	z.numberFormat('ganti_total_'+num);
	z.numberFormat('ganti_pengali_'+num);
	z.numberFormat('ganti_rp_'+num);
	calcTotal();
}

function calcPotongan(num) {
	var total = getById('potongan_total_'+num);
	z.numberFormat('potongan_total_'+num);
	calcTotal();
}

/**
 * loadGanti
 * Load Tabel untuk Uang Ganti
 */
function loadGanti() {
	var param = "karyawanid="+getValue('karyawanid'),
        tujuan='sdm_slave_pesangon_ganti.php?proses=list';
    
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
                    var res = JSON.parse(con.responseText);
					getById('tBodyGanti').innerHTML = res.content;
					calcTotal();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/*
 * addGanti
 * Tambah rincian uang ganti
 */
function addGanti() {
	var param = "karyawanid="+getValue('karyawanid')+"&narasi="+getValue('ganti_narasi_add')+
			"&pengali="+remove_comma_var(getById('ganti_pengali_add').value)+
			"&rp="+remove_comma_var(getById('ganti_rp_add').value),
        tujuan='sdm_slave_pesangon_ganti.php?proses=add';
    
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
					getById('ganti_narasi_add').value = '';
					getById('ganti_pengali_add').value = '0';
					getById('ganti_rp_add').value = '0';
					getById('ganti_total_add').value = '0';
                    loadGanti();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * saveGanti
 * Update Detail Uang Ganti
 */
function saveGanti(num) {
	var param = "karyawanid="+getValue('karyawanid')+"&narasi="+getValue('ganti_narasi_'+num)+
			"&pengali="+remove_comma_var(getById('ganti_pengali_'+num).value)+
			"&rp="+remove_comma_var(getById('ganti_rp_'+num).value)+'&no='+num,
        tujuan='sdm_slave_pesangon_ganti.php?proses=save';
	
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
					changeBg(getById('ganti_'+num),'transparent');
					calcTotal();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * deleteGanti
 * Hapus Detail Uang Ganti
 */
function deleteGanti(num) {
	var param = "karyawanid="+getValue('karyawanid')+"&narasi="+'&no='+num,
        tujuan='sdm_slave_pesangon_ganti.php?proses=delete';
	
	if(confirm("You're about to delete row number "+num+"\nAre you sure?"))
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
					loadGanti();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * loadPotongan
 * Load Tabel untuk Potongan
 */
function loadPotongan() {
	var param = "karyawanid="+getValue('karyawanid'),
        tujuan='sdm_slave_pesangon_potongan.php?proses=list';
    
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
                    var res = JSON.parse(con.responseText);
					getById('tBodyPotongan').innerHTML = res.content;
					calcTotal();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * addPotongan
 * Tambah rincian potongan
 */
function addPotongan() {
	var param = "karyawanid="+getValue('karyawanid')+"&narasi="+getValue('potongan_narasi_add')+
			"&total="+remove_comma_var(getById('potongan_total_add').value),
        tujuan='sdm_slave_pesangon_potongan.php?proses=add';
    
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
					getById('potongan_narasi_add').value = '';
					getById('potongan_total_add').value = '0';
                    loadPotongan();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * savePotongan
 * Update Detail Potongan
 */
function savePotongan(num) {
	var param = "karyawanid="+getValue('karyawanid')+"&narasi="+getById('potongan_narasi_'+num).value+
			"&total="+remove_comma_var(getById('potongan_total_'+num).value)+'&no='+num,
        tujuan='sdm_slave_pesangon_potongan.php?proses=save';
	
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
					changeBg(getById('potongan_'+num),'transparent');
					calcTotal();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

/**
 * deletePotongan
 * Hapus Detail Potongan
 */
function deletePotongan(num) {
	var param = "karyawanid="+getValue('karyawanid')+'&no='+num,
        tujuan='sdm_slave_pesangon_potongan.php?proses=delete';
	
	if(confirm("You're about to delete row number "+num+"\nAre you sure?"))
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
					loadPotongan();
				}
			} else {
				busy_off();
				error_catch(con.status);
			}
		}
	}
}

function changeKary() {
	var param = "karyawanid="+getValue('karyawanid'),
        tujuan='sdm_slave_pesangon.php?proses=changeKary';
	
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
					var res = JSON.parse(con.responseText),
						period = getById('periodegaji');
					getById('masakerja').value = res.masakerja;
					getById('tanggalkeluar').value = res.tanggalkeluar;
					period.options.length = 0;
					for(i in res.period) {
						var sel = document.createElement('option');
						sel.value = i;
						sel.innerHTML = res.period[i];
						period.appendChild(sel);
					}
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