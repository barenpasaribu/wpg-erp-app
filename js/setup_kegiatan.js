/* Function showNorma
 * Fungsi untuk pop up form norma
 * I : id table, primary key table
 * P : Ajax menyiapkan keseluruhan halaman norma
 * O : Halaman edit norma
 */
function showNorma(num,idStr,event) {
    var IDs = idStr.split('##');
    
    for(i=1;i<IDs.length;i++) {
        tmp = document.getElementById(IDs[i]+"_"+num);
        if(i==1) {
            var param = IDs[i]+"="+tmp.innerHTML;
        } else {
            param += "&"+IDs[i]+"="+tmp.innerHTML;
        }
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    showDialog1('Edit Norma',con.responseText,'800','300',event);
                    var dialog = document.getElementById('dynamic1');
                    dialog.style.top = '10%';
                    dialog.style.left = '15%';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_kegiatan.php', param, respon);
}

/* Function getInv
 * Override dari zSearch.js
 * Fungsi untuk pop up window, untuk memilih barang
 * I : id target
 * O : Pop up window untuk pencarian barang
 */
function getInv(event,num) {
    var cont = "<fieldset><legend><b>Search</b></legend>";
    cont += "<input id='invSearch' type='text' onkeypress=\"if(getKey(event)==13){searchInv('invSearch','"+num+"');} else {return tanpa_kutip(event)}\" />";
    cont += "<img src='images/search.png' onclick=\"searchInv('invSearch','"+num+"')\" style='cursor:pointer'>";
    cont += "</fieldset>";
    
    cont += "<fieldset><legend><b>Result</b></legend><div id='sResult' style='height:315px;overflow:auto'>";
    cont += "</div></fieldset><input id='currNum' type='hidden' value='"+num+"' />";
    showDialog2('Search Inventory',cont,'500','400',event);
}

/* Function searchInv
 * Override dari zSearch.js
 * Fungsi untuk mencari barang
 * I : id search text, id target
 * O : Tampilkan hasil pencarian
 */

function ambilkegiatan(){
    noakun=document.getElementById('noakun');
    noakun=noakun.options[noakun.selectedIndex].value;
    param="ngapain=ambilkegiatan";
    param+="&noakun="+noakun;
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('kodekegiatan');
                    if(res.value === ""){
                        res.value = con.responseText;
                    }else{
                        res.disabled = true;
                    }
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text('setup_slave_kegiatan_kegiatan.php', param, respon);
}

function searchInv(id,targetId) {
    var sText = document.getElementById(id);
    
    if(sText.value=='' || sText.value.length<3) {
        alert('Min 3 Char');
        exit;
    }
    
    var param = "keyword="+sText.value;
    param += "&target="+targetId;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('sResult');
                    res.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_kegiatan_barang.php', param, respon);
}

/* Function passInvValue
 * Fungsi untuk mengirim nilai ke element tertentu
 * I : nilai, id target
 * O : nilai terupdate
 */
function passValue(kode,nama,satuan) {
    var num = document.getElementById('currNum').value;
    var tKode = document.getElementById('kodebarang_'+num);
    var tNama = document.getElementById('namabarang_'+num);
    var tSatuan = document.getElementById('uom1_'+num);
    
    tKode.value = kode;
    tNama.value = nama;
    tSatuan.innerHTML = satuan;
    closeDialog2();
}

/* Function addNewRow
 * Fungsi untuk menambah row baru ke dalam table
 * I : id dari tbody tabel
 * P : Persiapan row dalam bentuk HTML
 * O : Tambahan row pada akhir tabel (append)
 */
function addNewRow(body,primary,field) {
    var tabBody = document.getElementById(body);
    
    // Search Available numRow
    var numRow = 0;
    while(document.getElementById('detail_tr_'+numRow)) {
	numRow++;
    }
    
    // Add New Row
    var newRow = document.createElement("tr");
    tabBody.appendChild(newRow);
    newRow.setAttribute("id","detail_tr_"+numRow);
    newRow.setAttribute("class","rowcontent");
    
    var param = "proses=addRow";
    param += "&numRow="+numRow;
    param += "&primary="+primary;
    param += "&field="+field;
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    newRow.innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_kegiatan_norma.php', param, respon);
}

/* Function switchEditAdd
 * Fungsi untuk mengganti image add menjadi edit dan keroconya
 * I : id nomor row
 * P : Image Add menjadi Edit
 * O : Image Edit
 */
function switchEditAdd(id,primary,field,theme) {
    var idField = document.getElementById('addNorma_'+id);
    var delImg = document.getElementById('deleteNorma_'+id);
    var invBtn = document.getElementById('getInvBtn_'+id);
    var primaryJs = primary.split('##');
    primaryJs.push('namabarang');
    
    if(idField) {
        idField.removeAttribute('id');
        idField.removeAttribute('name');
        idField.removeAttribute('onclick');
        idField.removeAttribute('src');
        idField.removeAttribute('title');
        
	// Set Edit Image Attr
	idField.setAttribute('title','Edit');
	idField.setAttribute('id','editNorma_'+id);
	idField.setAttribute('name','editNorma_'+id);
	idField.setAttribute('onclick','editNorma(\''+id+'\',\''+primary+'\',\''+field+'\')');
        idField.setAttribute('src','images/'+theme+'/save.png');
	
	// Set Delete Image Attr
	delImg.setAttribute('class','zImgBtn');
        delImg.setAttribute('title','Hapus');
	delImg.setAttribute('name','deleteNorma_'+id);
	delImg.setAttribute('onclick','deleteNorma(\''+id+'\',\''+primary+'\',\''+field+'\')');
        delImg.setAttribute('src','images/'+theme+'/delete.png');
	
	// Disabled various field
	for(i=1;i<primaryJs.length;i++) {
	    tmp = document.getElementById(primaryJs[i]+'_'+id);
	    if(tmp) {
		tmp.setAttribute('disabled','disabled');
	    }
	}
	invBtn.setAttribute('disabled','disabled');
    } else {
        alert('DOM Definition Error');
    }
}

/* Function addNorma(id,field)
 * Fungsi untuk menambah data Detail
 * I : id row (urutan row pada table Detail), field yang berhubungan
 * P : Menambah data pada tabel Detail
 * O : Menambah baris pada tabel Detail
 */
function addNorma(id,primary,field) {
    var fieldJs = field.split('##');
    var kodeorg = document.getElementById('kodeorg_norma').value;
    var kodekegiatan = document.getElementById('kodekegiatan_norma').value;
    var kelompok = document.getElementById('kelompok_norma').value;
    
    param = "proses=add";
    param += "&kodeorg="+kodeorg;
    param += "&kodekegiatan="+kodekegiatan;
    param += "&kelompok="+kelompok;
    for(i=1;i<fieldJs.length;i++) {
        tmp = document.getElementById(fieldJs[i]+"_"+id);
        param += "&"+fieldJs[i]+"="+tmp.value;
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    var theme = con.responseText;
                    switchEditAdd(id,primary,field,theme);
                    addNewRow('normaBody',primary,field);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_kegiatan_norma.php', param, respon);
}

/* Function editNorma(id,primary,field)
 * Fungsi untuk mengubah data Detail
 * I : id row (urutan row pada table Detail),primary key, semua field
 * P : Mengubah data pada tabel Detail
 * O : Notifikasi data telah berubah
 */
function editNorma(id,primary,field) {
    var fieldJs = field.split('##');
    var primJs = primary.split('##');
    
    param = "proses=edit";
    param += "&primary="+primary;
    param += "&primVal=";
    for(i=1;i<primJs.length;i++) {
        tmp = document.getElementById(primJs[i]+"_norma");
        if(!tmp) {
            tmp = document.getElementById(primJs[i]+"_"+id);
        }
        param += "##"+tmp.value;
    }
    
    for(i=1;i<fieldJs.length;i++) {
        tmp = document.getElementById(fieldJs[i]+"_"+id);
        param += "&"+fieldJs[i]+"=";
        if(tmp.options) {
            param += tmp.options[tmp.options.selectedIndex].value;
        } else {
            param += tmp.value;
        }
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		    alert('Data Saved');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    post_response_text('setup_slave_kegiatan_norma.php', param, respon);
}

/* Function deleteNorma(id,primary,field)
 * Fungsi untuk menghapus data norma
 * I : id row (urutan row pada table norma), primary field, semua field
 * P : Menghapus data pada tabel norma
 * O : Menghapus baris pada tabel norma
 */
function deleteNorma(id,primary,field) {
    var fieldJs = field.split('##');
    var primJs = primary.split('##');
    
    param = "proses=delete";
    param += "&primary="+primary;
    param += "&primVal=";
    for(i=1;i<primJs.length;i++) {
        tmp = document.getElementById(primJs[i]+"_norma");
        if(!tmp) {
            tmp = document.getElementById(primJs[i]+"_"+id);
        }
        param += "##"+tmp.value;
    }
    
    for(i=1;i<fieldJs.length;i++) {
        tmp = document.getElementById(fieldJs[i]+"_"+id);
        param += "&"+fieldJs[i]+"=";
        if(tmp.options) {
            param += tmp.options[tmp.selectedIndex].value;
        } else {
            param += tmp.value;
        }
    }
    
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
    
    post_response_text('setup_slave_kegiatan_norma.php', param, respon);
}