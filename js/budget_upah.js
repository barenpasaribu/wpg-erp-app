/**
 * @author antoniuslouis
 */
// dhyaz aug 10, 2011

//menampilkan tab list data

//manampilkan list upah yang belum ditutup untuk diedit
function prosesUpah()
{
    tahunbudget =document.getElementById('tahunbudget');
    kodeorg =document.getElementById('kodeorg');
    kodeorgV =kodeorg.options[kodeorg.selectedIndex].value;
    tahunbudgetV	=tahunbudget.value;
    param='tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV;
    param2='what=closing&tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV;
    tujuan='budget_slave_upah.php';
    tujuancek='budget_slave_upah_cek.php';
//pertama, cek apakah sudah di-closing?
    post_response_text(tujuancek, param2, respogcek2);
    function respogcek2(){
	if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
		if (!isSaveResponse(con.responseText)) {
		} else {
                    if(con.responseText){
                        alert(con.responseText);
                        post_response_text(tujuan, param+'&what=closed', respog);	
                    }else{
//kalo belon di-closing, display data
                        post_response_text(tujuan, param, respog);	
                    }
		}
            } else {
		busy_off();
		error_catch(con.status);
            }
	}
    }		
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
		busy_off();
		if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
		} else {
//                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
		}
            } else {
		busy_off();
		error_catch(con.status);
            }
	}
    }		
}

//menampilkan list upah yang belum ditutup untuk ditutup
function prosesTutupUpah()
{
    tahunbudget =document.getElementById('tahunbudget2');
    tahunbudgetV	=tahunbudget.options[tahunbudget.selectedIndex].value;
    kodeorg =document.getElementById('kodeorg2');
    kodeorgV	=kodeorg.innerHTML;
    param='tahunbudget='+tahunbudgetV;
    param2='what=closing&tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV;
    tujuan='budget_slave_upah_maututup.php';
    tujuancek='budget_slave_upah_cek.php';
//pertama, cek apakah sudah di-closing?
    post_response_text(tujuancek, param2, respogcek2);
    function respogcek2(){
	if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
		if (!isSaveResponse(con.responseText)) {
		} else {
                    if(con.responseText){
                        alert(con.responseText);
                    document.getElementById('container2').innerHTML='';
                    }else{
//kalo belon di-closing, display data
//                        alert(con.responseText);
                        post_response_text(tujuan, param, respog);	
                    }
		}
            } else {
		busy_off();
		error_catch(con.status);
            }
	}
    }		
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
		busy_off();
		if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
		} else {
//                    showById('printPanel');
                    document.getElementById('container2').innerHTML=con.responseText;
		}
            } else {
		busy_off();
		error_catch(con.status);
            }
	}
    }		
}

//update tab tutup saat save
function updateTahun()
{
    param='pam=1';
    tujuan='budget_slave_upah_update.php';
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('tahunbudget2').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }	
	
}

//modif dari angka_doang + minus enable
function angka_doangsamaminus(e)//only numeric e is event
{
    key=getKey(e);
    if((key<48 || key>57) && (key!=8 && key != 45 && key != 150 && key!=46  && key!=127 && key!=true))
        return false;
    else
    {
        return true;
    }
}

//tampilan angka dengan desimal (tanpa pemisah ribuan)
function display_format(angka,desimal)
{
    qwe=angka.toFixed(desimal);
    return qwe;
}

//save upah ke table budget - semua row
function simpanHarga(row)
{
    tahunbudget =document.getElementById('tahunbudget');
    kodeorg =document.getElementById('kodeorg');
    kodegolongan =document.getElementById('kodegolongan_'+row);
    upah =document.getElementById('upah_'+row);
    tahunbudgetV	=tahunbudget.value;
    kodeorgV =kodeorg.options[kodeorg.selectedIndex].value;
    kodegolonganV=kodegolongan.innerHTML;
    upahV =upah.value;
    param='tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV+'&kodegolongan='+kodegolonganV+'&upah='+upahV;
    tujuan='budget_slave_5upah_save.php';
//template dari script tutup buku
    post_response_text(tujuan, param, respon);
    document.getElementById('baris_'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris_'+row).style.backgroundColor='red';
                } else {
//tidak ada error, hilangkan baris                    
                    document.getElementById('baris_'+row).style.backgroundColor='';
                    document.getElementById('baris_'+row).style.display='none';
                    try{
//coba, apakah baris terakhir
                        x=row+1;
                        if(document.getElementById('baris_'+x))
                        {
//kalo bukan, looping ke awal fungsi                            
                            row=x;
                            simpanHarga(row);
                        } else {
//baris terakhir, hapus header, berikan pesan DONE                            
                            document.getElementById('simpan').disabled=true;
                            alert('Done');
                            updateTahun();
//                            prosesUpah();
                        }
                    }
                    catch(e)
                    {
//baris terakhir, hapus header, berikan pesan DONE                            
                        document.getElementById('simpan').disabled=true;
                        alert('Done');
                        updateTahun();
//                        prosesUpah();
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

//save upah ke table budget - hanya satu row
function simpanHargasatusatu(row)
{
    tahunbudget =document.getElementById('tahunbudget');
    kodeorg =document.getElementById('kodeorg');
    kodegolongan =document.getElementById('kodegolongan_'+row);
    upah =document.getElementById('upah_'+row);
    tahunbudgetV	=tahunbudget.value;
    kodeorgV =kodeorg.options[kodeorg.selectedIndex].value;
    kodegolonganV=kodegolongan.innerHTML;
    upahV =upah.value;
//    rataV =remove_comma_var(rataV);
    param='tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV+'&kodegolongan='+kodegolonganV+'&upah='+upahV;
    tujuan='budget_slave_5upah_save.php';
//template dari script tutup buku
    post_response_text(tujuan, param, respon);
    document.getElementById('baris_'+row).style.backgroundColor='orange';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris_'+row).style.backgroundColor='red';
                } else {
//tidak ada error, hilangkan baris                    
                        document.getElementById('baris_'+row).style.backgroundColor='';
                        alert('Done');
                        updateTahun();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

//close upah
function tutupHarga(row)
{
    if(row==1){
        if(confirm('Bila sudah di Tutup, data tidak dapat diganti lagi. Yakin?'))
        {
//    document.getElementById('close_'+row).disabled=true;
        } else {
//    document.getElementById('close_'+row).disabled=false;
            return;
        }
    }
    tahunbudget =document.getElementById('tahunbudget2');
    tahunbudgetV	=tahunbudget.options[tahunbudget.selectedIndex].value;
    kodeorg =document.getElementById('kodeorg2_'+row);
    kodeorgV	=kodeorg.innerHTML;
    kodegolongan =document.getElementById('kodegolongan2_'+row);
    kodegolonganV	=kodegolongan.innerHTML;
    upah =document.getElementById('upah2_'+row);
    upahV	=upah.innerHTML;
    param='tahunbudget='+tahunbudgetV+'&kodeorg='+kodeorgV+'&kodegolongan='+kodegolonganV+'&upah='+upahV;
    tujuan='budget_slave_upah_close.php';
    post_response_text(tujuan, param, responl);
    document.getElementById('baris2_'+row).style.backgroundColor='orange';
    function responl() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris2_'+row).style.backgroundColor='red';
                } else {
                    document.getElementById('baris2_'+row).style.display='none';
                    try{
                        x=row+1;
                        if(document.getElementById('baris2_'+x))
                        {   
                            row=x;
                            tutupHarga(row);
                        } else {
//                            document.getElementById('barisl_0').style.display='none';
                        document.getElementById('tutup').disabled=true;
                            alert('Done');
                    document.getElementById('container').innerHTML='';
			}
                    }
                        catch(e)
                    {
//                        document.getElementById('barisl_0').style.display='none';
                        document.getElementById('tutup').disabled=true;
                        alert('Done');
                    document.getElementById('container').innerHTML='';
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function resetcontainer2()
{
                    document.getElementById('container2').innerHTML='';    
}

function resetcontainer()
{
                    document.getElementById('container').innerHTML='';    
}