function delRow(kodeorg, kodetangki){
    var param = "kodeorg="+kodeorg+"&kodetangki="+kodetangki;
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					alert(con.responseText);
					location.reload(true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    if(confirm("Yakin akan hapus data ?")) {
	    post_response_text('pabrik_slave_5tangki.php?proses=delete', param, respon);
	}
}

function addData(){
	kodeorg = document.getElementById('kodeorg').value;
	kodetangki = document.getElementById('kodetangki').value;
	keterangan = document.getElementById('keterangan').value;	
	komoditi = document.getElementById('komoditi').value;
	stsuhu = document.getElementById('stsuhu').value;
	luaspenampang = document.getElementById('luaspenampang').value;
	satuanpenampang = document.getElementById('satuanpenampang').value;
	volumekerucut = document.getElementById('volumekerucut').value;
	satuankerucut = document.getElementById('satuankerucut').value;
	
	var param = 'kodeorg='+kodeorg+'&kodetangki='+kodetangki+'&keterangan='+keterangan+'&komoditi='+komoditi+'&stsuhu='+stsuhu+'&luaspenampang='+luaspenampang+'&satuanpenampang='+satuanpenampang+'&volumekerucut='+volumekerucut+'&satuankerucut='+satuankerucut;
	
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					alert(con.responseText);
					location.reload(true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('pabrik_slave_5tangki.php?proses=add', param, respon);
}

function editRow(kodeorg,kodetangki,keterangan,komoditi,stsuhu,luaspenampang,satuanpenampang,volumekerucut,satuankerucut){
	document.getElementById('kodeorg').value = kodeorg;
	document.getElementById('kodetangki').value = kodetangki;
	document.getElementById('keterangan').value = keterangan;	
	document.getElementById('komoditi').value = komoditi;
	document.getElementById('stsuhu').value = stsuhu;
	document.getElementById('luaspenampang').value = luaspenampang;
	document.getElementById('satuanpenampang').value = satuanpenampang;
	document.getElementById('volumekerucut').value = volumekerucut;
	document.getElementById('satuankerucut').value = satuankerucut;
	document.getElementById('add').style.display = 'none';
	document.getElementById('edit').style.display = '';
	document.getElementById("kodeorg").disabled = true;
	document.getElementById("kodetangki").disabled = true;
}

function editData(){
	kodeorg = document.getElementById('kodeorg').value;
	kodetangki = document.getElementById('kodetangki').value;
	keterangan = document.getElementById('keterangan').value;	
	komoditi = document.getElementById('komoditi').value;
	stsuhu = document.getElementById('stsuhu').value;
	luaspenampang = document.getElementById('luaspenampang').value;
	satuanpenampang = document.getElementById('satuanpenampang').value;
	volumekerucut = document.getElementById('volumekerucut').value;
	satuankerucut = document.getElementById('satuankerucut').value;
	
	var param = 'kodeorg='+kodeorg+'&kodetangki='+kodetangki+'&keterangan='+keterangan+'&komoditi='+komoditi+'&stsuhu='+stsuhu+'&luaspenampang='+luaspenampang+'&satuanpenampang='+satuanpenampang+'&volumekerucut='+volumekerucut+'&satuankerucut='+satuankerucut;
	
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
					alert(con.responseText);
					location.reload(true);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('pabrik_slave_5tangki.php?proses=edit', param, respon);
	
}