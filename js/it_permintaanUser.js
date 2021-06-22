function saveForm()
{
    jenislayanan=document.getElementById('jenislayanan').value;
    deskripsi=document.getElementById('deskripsi').value;
    atasan=document.getElementById('atasan').value;
    managerit=document.getElementById('managerit').value;
    param='jenislayanan='+jenislayanan+'&deskripsi='+deskripsi+'&atasan='+atasan+'&managerit='+managerit+'&proses=insert';
//    alert(param);
    if(jenislayanan==''){
        alert('Silahkan mengisi Jenis Layanan');
        return;
    }
    else if(deskripsi==''){
        alert('Silahkan mengisi Deskripsi/Keluhan');
        return;
    }
    else if(atasan==''){
        alert('Silahkan mengisi Atasan');
        return;
    }
    else if(managerit==''){
        alert('Silahkan mengisi Manager IT');
        return;
    }
    
    tujuan='it_slave_permintaanUser.php';
    if(confirm('Anda yakin untuk menyimpan data ?'))
    {
        if(confirm('Setelah submit, data tidak dapat diubah, Anda yakin ? ?'))
        {
            post_response_text(tujuan, param, respog);
        }   
    }
   
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();

                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    alert('Done.');
                    loaddata();
                    document.getElementById('jenislayanan').value='';
                    document.getElementById('deskripsi').value='';
                    document.getElementById('atasan').value='';
                    document.getElementById('managerit').value='';
                }
             }
             else {
                busy_off();
                error_catch(con.status);
             }
        }
    }	
}

function pages(num)
{
    param='proses=loaddata';
    param+='&page='+num;
                
    tujuan = 'it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function loaddata()
{
    param='proses=loaddata';
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function update_nilaihk(no)
{
    kepuasanuser=document.getElementById('kepuasanuser_'+no).options[document.getElementById('kepuasanuser_'+no).selectedIndex].value;
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='kepuasanuser='+kepuasanuser+'&notransaksi='+notransaksi+'&proses=update_nilaihk'; 
//    alert(param);
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loaddata();
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function update_nilaikom(no)
{
    nilaikomunikasi=document.getElementById('nilaikomunikasi_'+no).options[document.getElementById('nilaikomunikasi_'+no).selectedIndex].value;
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='nilaikomunikasi='+nilaikomunikasi+'&notransaksi='+notransaksi+'&proses=update_nilaikom'; 
//    alert(param);
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loaddata();
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function simpan(no)
{
    saranuser=document.getElementById('saranuser_'+no).value;
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='saranuser='+saranuser+'&notransaksi='+notransaksi+'&proses=update_saranuser';
//    alert(param);
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loaddata();
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function setuju(no)
{
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='notransaksi='+notransaksi+'&proses=setuju';
//    alert(param);
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loaddata();
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
function tolak(no)
{
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='notransaksi='+notransaksi+'&proses=formpenolakan';
    tujuan='it_slave_permintaanUser.php';
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
                else {
                    width='400';
                    height='200';
                    content="<div id=form_tolak></div>";
                    ev='event';
                    title="Form Komentar Penolakan";
                    showDialog1(title,content,width,height,ev);
                    //alert(con.responseText);
                    document.getElementById('form_tolak').innerHTML=con.responseText;
                    return con.responseText;
                    loaddata();
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }	
    }
}
function save(no)
{
    tolak=document.getElementById('tolak').value;
    transaksi=document.getElementById('no').value;
    param='tolak='+tolak+'&transaksi='+no+'&proses=update_statusatasan';
//    alert(param);
    tujuan='it_slave_permintaanUser.php';
    post_response_text(tujuan, param, respog);
    function respog() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    loaddata();
                    javascript:location.reload(true);
                   // defaultList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function view(no)
{
    notransaksi=document.getElementById('notransaksi_'+no).value;
    param='notransaksi='+notransaksi+'&proses=show';
    tujuan='it_slave_permintaanUser.php';
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
                else {
                    width='800';
                    height='400';
                    content="<div id=view></div>";
                    ev='event';
                    title="";
                    showDialog1(title,content,width,height,ev);
                    //alert(con.responseText);
                    document.getElementById('view').innerHTML=con.responseText;
                    return con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }	
    }
}