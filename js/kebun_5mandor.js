// JavaScript Document

function tampilmandor()
{
    param='method=tampilmandor';
    tujuan='kebun_slave_5mandor';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('container').innerHTML=con.responseText;
                    pilihmandor();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function updatekaryawan()
{
    mandor=document.getElementById('mandor');
    mandor=mandor.options[mandor.selectedIndex].value;

    param='method=tampilkaryawan&mandor='+mandor;
    tujuan='kebun_slave_5mandor';
    if(mandor!='')post_response_text(tujuan+'.php', param, respon);
    else document.getElementById('karyawan').innerHTML='<option value=\'\'></option>';

    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('karyawan').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function pilihmandor(pilihanmandor)
{
    mandor=document.getElementById('mandor');
    mandor=mandor.options[mandor.selectedIndex].value;
    
    if(pilihanmandor!=null){
        mandor=pilihanmandor;
        document.getElementById('mandor').value=pilihanmandor;
    }
    
    param='method=pilihmandor&mandor='+mandor;
    tujuan='kebun_slave_5mandor';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('anggota').innerHTML=con.responseText;
                    updatekaryawan();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function tambahkaryawan()
{
    mandor=document.getElementById('mandor');
    mandor=mandor.options[mandor.selectedIndex].value;
    karyawan=document.getElementById('karyawan');
    karyawan=karyawan.options[karyawan.selectedIndex].value;
    urut=document.getElementById('urut').value;

    param='method=tambahkaryawan&mandor='+mandor+'&karyawan='+karyawan+'&urut='+urut;
    tujuan='kebun_slave_5mandor';
    
    if(karyawan=='' || urut==''){
        alert('Karyawan dan No. harus diisi');
    }else{
        post_response_text(tujuan+'.php', param, respon);
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
                    tampilmandor();
                    document.getElementById('urut').value='';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}

function hapuskaryawan(karyawan)
{
    mandor=document.getElementById('mandor');
    mandor=mandor.options[mandor.selectedIndex].value;

    param='method=hapuskaryawan&mandor='+mandor+'&karyawan='+karyawan;
    tujuan='kebun_slave_5mandor';
    
    if(confirm('OK delete?'))post_response_text(tujuan+'.php', param, respon);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
                    tampilmandor();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}

function hapusmandor(mandor)
{
    param='method=hapusmandor&mandor='+mandor;
    tujuan='kebun_slave_5mandor';
    
    if(confirm('OK delete?'))post_response_text(tujuan+'.php', param, respon);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    alert('Done.');
                    document.getElementById('mandor').value='';
                    tampilmandor();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}

function aktifkaryawan(karyawan,mandor,aktif)
{
    param='method=aktifkaryawan&mandor='+mandor+'&karyawan='+karyawan+'&aktif='+aktif;
    tujuan='kebun_slave_5mandor';
    
    if(confirm('OK modify?'))post_response_text(tujuan+'.php', param, respon);
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    tampilmandor();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	            
}