function cariBast(num)
{
        param='&page='+num;
        tujuan='pabrik_slave_hasil.php?proses=showHeadList';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('susunanDataInput').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function refresh()
{
    window.location.reload(); 
}      
function quickPosting()
{
	tanggal = document.getElementById('tanggal_quick_posting').value;
	param='proses=quickPostingSounding'+'&tanggal='+tanggal;
	tujuan='pabrik_slave_get_data.php';

	post_response_text(tujuan, param, respog);	
	function respog()
	{
		if(con.readyState==4)
		{
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else 
				{
                    alert(con.responseText);
                    document.location.reload();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}	
	}

}

function lihatPDF(row_id, ev = 'event') {
    tanggal = document.getElementById('tanggal_'+row_id).innerHTML;
    param = "proses=pdf&tanggal="+tanggal;
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
                " src='pabrik_slave_hasil_pdf.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '150px';
    dialog.style.left = '15%';
}

function postingData(notransaksi) {
    var param = "notransaksi="+notransaksi;
    //alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Posting Berhasil');
                    document.location.reload();
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    if(confirm('Apakah anda yakin untuk konfirmasi :'+notransaksi+
        '?\n data tidak akan bisa diedit')) {
        post_response_text('pabrik_slave_hasil.php?proses=posting', param, respon);
    }
}


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
    var where = '[["notransaksi","'+notrans.value+'"]]';
    
    goToPages(0,showPerPage,where);
}

/* Paging
 * Paging Data
 */

function defaultList() {
    goToPages(0,showPerPage);
}

function goToPages(page,shows,where) {
    if(typeof where != 'undefined') {
        var newWhere = where.replace(/'/g,'"');
    }
    var workField = document.getElementById('susunanDataInput');
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
    
    post_response_text('pabrik_slave_hasil.php?proses=showHeadList', param, respon);
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
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_hasil.php?proses=showAdd', param, respon);
}

function showEdit(notransaksi) {
    var workField = document.getElementById('workField');
    var param = "notransaksi="+notransaksi;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    workField.innerHTML = con.responseText;
                    document.getElementById('tanggal').disabled=true;
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_hasil.php?proses=showEdit', param, respon);
}

/* Manipulasi Data
 * add, edit, delete
 */
function addDataTable() {
    var param = "notransaksi="+getValue('notransaksi')+"&tanggal="+getValue('tanggal');
    param += "&kodeorg="+getValue('kodeorg')+"&kodetangki="+getValue('kodetangki');
    param += "&kuantitas="+getValue('kuantitas')+"&suhu="+getValue('suhu');
    param += "&cpoffa="+getValue('cpoffa');
//    param += "&cporendemen="+getValue('cporendemen')+"&cpoffa="+getValue('cpoffa');
    param += "&cpokdair="+getValue('cpokdair')+"&cpokdkot="+getValue('cpokdkot');
    param += "&kernelquantity="+getValue('kernelquantity');
//    param += "&kernelquantity="+getValue('kernelquantity')+"&kernelrendemen="+getValue('kernelrendemen');
    param += "&kernelkdair="+getValue('kernelkdair')+"&kernelkdkot="+getValue('kernelkdkot');
    param += "&kernelffa="+getValue('kernelffa')+"&tinggi="+getValue('tinggi');
    param += "&jam="+getValue('jam_jam')+"&jam_menit="+getValue('jam_menit');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('simpan berhasil');
                    defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_hasil.php?proses=add', param, respon);
}

function editDataTable() {
    var param = "notransaksi="+getValue('notransaksi')+"&tanggal="+getValue('tanggal');
    param += "&kodeorg="+getValue('kodeorg')+"&kodetangki="+getValue('kodetangki');
    param += "&kuantitas="+getValue('kuantitas')+"&suhu="+getValue('suhu');
    param += "&cpoffa="+getValue('cpoffa');
//    param += "&cporendemen="+getValue('cporendemen')+"&cpoffa="+getValue('cpoffa');
    param += "&cpokdair="+getValue('cpokdair')+"&cpokdkot="+getValue('cpokdkot');
    param += "&kernelquantity="+getValue('kernelquantity');
//    param += "&kernelquantity="+getValue('kernelquantity')+"&kernelrendemen="+getValue('kernelrendemen');
    param += "&kernelkdair="+getValue('kernelkdair')+"&kernelkdkot="+getValue('kernelkdkot');
    param += "&kernelffa="+getValue('kernelffa')+"&tinggi="+getValue('tinggi');
    param += "&jam="+getValue('jam_jam')+"&jam_menit="+getValue('jam_menit');
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    document.getElementById('tanggal').disabled=false;
                    defaultList();
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('pabrik_slave_hasil.php?proses=edit', param, respon);
}

function deleteData(notransaksi) {
    var param = "notransaksi="+notransaksi;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    alert('Delete Berhasil');
                    document.location.reload();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    if(confirm("Yakin akan hapus data ?")){
        post_response_text('pabrik_slave_hasil.php?proses=delete', param, respon);
    }
}

/* Update No Urut di halaman absensi
 */
function updNoUrut() {
    var tabBody = document.getElementById('mTabBody');
    var nourut = document.getElementById('nourut');
    var maxNum = 0;
    
    if(tabBody.childNodes.length>0) {
        for(i=0;i<tabBody.childNodes.length;i++) {
            var tmp = document.getElementById('nourut_'+i);
            if(tmp.innerHTML > maxNum) {
                maxNum = tmp.innerHTML;
            }
        }
    }
    nourut.value = parseInt(maxNum)+1;
}

function printPDF(ev) {
    // Prep Param
    param = "proses=pdf";
    
    showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px'"+
        " src='pabrik_slave_hasil_print.php?"+param+"'></iframe>",'800','400',ev);
    var dialog = document.getElementById('dynamic1');
    dialog.style.top = '50px';
    dialog.style.left = '15%';
}
function getVolCpo(){
    param = "kodetangki="+getValue('kodetangki')+"&suhu="+getValue('suhu');
    param +="&tinggi="+getValue('tinggi');
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    //=== Success Response
                    var hasil = JSON.parse(con.responseText);
                    console.log(hasil);
                    console.log(hasil.tipe);
                    console.log(hasil.tonase);
                    if (hasil.tipe == 'CPO') {
                        document.getElementById('kuantitas').disabled = false;
                        document.getElementById('kernelquantity').disabled = true;
                        document.getElementById('kuantitas').value = parseFloat(hasil.tonase).toFixed(2);
                        document.getElementById('kernelquantity').value = 0;
                    }
                    if (hasil.tipe == 'KERNEL') {
                        document.getElementById('kuantitas').disabled = true;
                        document.getElementById('kernelquantity').disabled = false;
                        document.getElementById('kuantitas').value = 0;
                        document.getElementById('kernelquantity').value = parseFloat(hasil.tonase).toFixed(2);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('pabrik_slave_hasil.php?proses=getVolume', param, respon);
}