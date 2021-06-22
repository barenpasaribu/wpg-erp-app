/* Function addNewRow
 * Fungsi untuk menambah row baru ke dalam table
 * I : id dari tbody tabel
 * P : Persiapan row dalam bentuk HTML
 * O : Tambahan row pada akhir tabel (append)
 */
function addNewRow(body,onDetail) {
    var tabBody = document.getElementById(body);
    if(onDetail) {
	var detail = onDetail;
    } else {
	var detail = false;
    }
    
    // Search Available numRow
    var numRow = 0;
    if(!detail) {
	while(document.getElementById('tr_'+numRow)) {
	    numRow++;
	}
    } else {
	while(document.getElementById('detail_tr_'+numRow)) {
	    numRow++;
	}
    }
    
    // Add New Row
    var newRow = document.createElement("tr");
    tabBody.appendChild(newRow);
    if(!detail) {
	newRow.setAttribute("id","tr_"+numRow);
    } else {
	newRow.setAttribute("id","detail_tr_"+numRow);
    }
    newRow.setAttribute("class","rowcontent");
    
    if(!detail) {
	newRow.innerHTML += "<td><input id='kode_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='matauang_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='simbol_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='kodeiso_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><img id='add_"+numRow+
	"' title='Tambah' class=zImgBtn onclick=\"addMain('"+numRow+"')\" src='images/plus.png'/>"+
	"&nbsp;<img id='delete_"+numRow+"' />"+
	"&nbsp;<img id='pass_"+numRow+"' />"+
	"</td>";
    } else {
	// Make Jam dan Menit
	var jam = "<select id='jam_"+numRow+"' style='width:40px'>";
	var menit = "<select id='menit_"+numRow+"' style='width:40px'>";
	// Isi Option Jam & Menit
	for(i=0;i<60;i++) {
	    var tmpNum = i;
	    if(i<10)
		i = '0'+i;
	    menit += "<option value='"+i+"'>"+i+"</option>";
	    if(i<24)
		jam += "<option value='"+i+"'>"+i+"</option>";
	}
	jam += "</select>";
	menit += "</select>";
	
	// Create Row
	newRow.innerHTML += "<td><input id='daritanggal_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onmousemove='setCalendar(this.id)' readonly='readonly' value='' /></td><td>"+
	jam+" : "+menit+"</td><td><input id='kurs_"+numRow+
	"' type='text' class='myinputtext' style='width:70px' onkeypress='return angka_doang(event)' value='' /></td><td><img id='detail_add_"+numRow+
	"' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/plus.png'/>"+
	"&nbsp;<img id='detail_delete_"+numRow+"' />"+
	"&nbsp;<img id='detail_pass_"+numRow+"' />"+
	"</td>";
    }
}

/* Function switchEditAdd
 * Fungsi untuk mengganti image add menjadi edit dan keroconya
 * I : id nomor row
 * P : Image Add menjadi Edit
 * O : Image Edit
 */
function switchEditAdd(id,main) {
    if(main=='main') {
	var idField = document.getElementById('add_'+id);
	var delImg = document.getElementById('delete_'+id);
	var passImg = document.getElementById('pass_'+id);
	var kode = document.getElementById('kode_'+id);
    } else {
	var idField = document.getElementById('detail_add_'+id);
	var delImg = document.getElementById('detail_delete_'+id);
	var detTgl = document.getElementById('daritanggal_'+id);
	var detJam = document.getElementById('jam_'+id);
	var detMenit = document.getElementById('menit_'+id);
    }
    if(idField) {
        idField.removeAttribute('id');
        idField.removeAttribute('name');
        idField.removeAttribute('onclick');
        idField.removeAttribute('src');
        idField.removeAttribute('title');
        
	// Set Edit Image Attr
	idField.setAttribute('title','Edit');
        if(main=='main') {
	    idField.setAttribute('id','edit_'+id);
	    idField.setAttribute('name','edit_'+id);
            idField.setAttribute('onclick','editMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
	    idField.setAttribute('id','detail_edit_'+id);
	    idField.setAttribute('name','detail_edit_'+id);
            idField.setAttribute('onclick','editDetail(\''+id+'\')');
        }
        idField.setAttribute('src','images/001_45.png');
	
	// Set Delete Image Attr
	delImg.setAttribute('class','zImgBtn');
        delImg.setAttribute('title','Hapus');
        if(main=='main') {
	    delImg.setAttribute('name','delete_'+id);
            delImg.setAttribute('onclick','deleteMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
	    delImg.setAttribute('name','detail_delete_'+id);
            delImg.setAttribute('onclick','deleteDetail(\''+id+'\')');
        }
        delImg.setAttribute('src','images/delete_32.png');
	
	if(main=='main') {
	    // Set Pass Image Attr
	    passImg.setAttribute('name','pass_'+id);
	    passImg.setAttribute('class','zImgBtn');
	    passImg.setAttribute('title','Lihat Detail');
	    passImg.setAttribute('onclick','pass2detail(\''+id+'\')');
	    passImg.setAttribute('src','images/nxbtn.png');
	} else {
	    // Disabled various field
	    detTgl.setAttribute('disabled','disabled');
	    detJam.setAttribute('disabled','disabled');
	    detMenit.setAttribute('disabled','disabled');
	    detTgl.removeAttribute('onmousemove');
	}
    } else {
        alert('DOM Definition Error');
    }
}

/* Function addMain(id)
 * Fungsi untuk menambah data Main
 * I : id row (urutan row pada table Main)
 * P : Menambah data pada tabel Main
 * O : Menambah baris pada tabel Main
 */
function addMain(id) {
    var kode = document.getElementById('kode_'+id);
    var matauang = document.getElementById('matauang_'+id);
    var simbol = document.getElementById('simbol_'+id);
    var kodeiso = document.getElementById('kodeiso_'+id);
    
    param = "proses=main_add";
    param += "&kode="+kode.value;
    param += "&matauang="+matauang.value;
    param += "&simbol="+simbol.value;
    param += "&kodeiso="+kodeiso.value;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    switchEditAdd(id,'main');
                    addNewRow('mainBody');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang.php', param, respon);
}

/* Function editMain(id,primField,primVal)
 * Fungsi untuk mengubah data Main
 * I : id row (urutan row pada table Main), nama primary key, nilai dari primary key
 * P : Mengubah data pada tabel Main
 * O : Notifikasi data telah berubah
 */
function editMain(id,primField,primVal) {
    var kode = document.getElementById('kode_'+id);
    var matauang = document.getElementById('matauang_'+id);
    var simbol = document.getElementById('simbol_'+id);
    var kodeiso = document.getElementById('kodeiso_'+id);
    var edit = document.getElementById('edit_'+id);
    
    param = "proses=main_edit";
    param += "&primField="+primField;
    param += "&primVal="+primVal;
    param += "&kode="+kode.value;
    param += "&matauang="+matauang.value;
    param += "&simbol="+simbol.value;
    param += "&kodeiso="+kodeiso.value;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    alert("Baris dengan "+primField+" = "+primVal+" berhasil diubah");
		    edit.removeAttribute('onclick');
		    edit.setAttribute('onclick',"editMain('"+id+"','"+primField+"','"+kode.value+"')");
		    var detKode = document.getElementById('detail_kode');
		    if(detKode) {
			detKode.value = kode.value;
		    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang.php', param, respon);
}

/* Function deleteMain(id,primField,primVal)
 * Fungsi untuk menghapus data Main
 * I : id row (urutan row pada table Main), nama primary key, nilai dari primary key
 * P : Menghapus data pada tabel Main
 * O : Menghapus baris pada tabel Main
 */
function deleteMain(id,primField,primVal) {
    param = "proses=main_delete";
    param += "&primField="+primField;
    param += "&primVal="+primVal;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    row = document.getElementById("tr_"+id);
		    if(row) {
			row.style.display="none";
		    } else {
			alert("Row undetected");
		    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang.php', param, respon);
}

/* Function pass2detail
 * Fungsi untuk menampilkan tabel detail dari tabel Main yang dimaksud
 * I : numRow dari tabel Main
 * P : Ajax untuk extract data dan persiapan tabel dalam bentuk HTML
 * O : Tampilan tabel detail
 */
function pass2detail(id) {
    var kode = document.getElementById('kode_'+id);
    param = "id="+kode.value;
    param += "&proses=createTable";
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    var detailDiv = document.getElementById('detailTable');
		    detailDiv.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('setup_slave_matauang_detail.php', param, respon);
}

/* Function addDetail(id)
 * Fungsi untuk menambah data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menambah data pada tabel Detail
 * O : Menambah baris pada tabel Detail
 */
function addDetail(id) {
    var detKode = document.getElementById('detail_kode');
    var tanggal = document.getElementById('daritanggal_'+id);
    var jam = document.getElementById('jam_'+id);
    var menit = document.getElementById('menit_'+id);
    var kurs = document.getElementById('kurs_'+id);
    
    param = "proses=detail_add";
    param += "&kode="+detKode.value;
    param += "&daritanggal="+tanggal.value;
    param += "&jam="+jam.value;
    param += "&menit="+menit.value;
    param += "&kurs="+kurs.value;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    switchEditAdd(id,'detail');
                    addNewRow('detailBody',true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang_detail.php', param, respon);
}

/* Function editDetail(id,primField,primVal)
 * Fungsi untuk mengubah data Detail
 * I : id row (urutan row pada table Detail)
 * P : Mengubah data pada tabel Detail
 * O : Notifikasi data telah berubah
 */
function editDetail(id) {
    var detKode = document.getElementById('detail_kode');
    var tanggal = document.getElementById('daritanggal_'+id);
    var jam = document.getElementById('jam_'+id);
    var menit = document.getElementById('menit_'+id);
    var kurs = document.getElementById('kurs_'+id);
    
    param = "proses=detail_edit";
    param += "&kode="+detKode.value;
    param += "&daritanggal="+tanggal.value;
    param += "&jam="+jam.value;
    param += "&menit="+menit.value;
    param += "&kurs="+kurs.value;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    alert("Baris dengan tanggal "+tanggal.value+" dan jam "+jam.value+" berhasil diubah");
		}
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang_detail.php', param, respon);
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
    var detKode = document.getElementById('detail_kode');
    var tanggal = document.getElementById('daritanggal_'+id);
    var jam = document.getElementById('jam_'+id);
    var menit = document.getElementById('menit_'+id);
    
    param = "proses=detail_delete";
    param += "&kode="+detKode.value;
    param += "&daritanggal="+tanggal.value;
    param += "&jam="+jam.value;
    param += "&menit="+menit.value;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    row = document.getElementById("detail_tr_"+id);
		    if(row) {
			row.style.display="none";
		    } else {
			alert("Row undetected");
		    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_matauang_detail.php', param, respon);
}