
function getperiode()
{
    param='proses=getperiode';
    tujuan='kebun_slave_crossblock.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('periode1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function canceldata0()
{
    document.getElementById('proses0').value='savedata0';
    document.getElementById('tanggal').value='';
    document.getElementById('kodeorg').value='';
    document.getElementById('jabatan').value='';
    document.getElementById('karyawan').value='';
    document.getElementById('kelompok').value='';
    document.getElementById('cek').value='';
    document.getElementById('keterangan').value='';
    document.getElementById('container2').innerHTML='';
    document.getElementById('jumlahkegiatan').value='0';
}

function abissave0(jumlahkegiatan)
{
    document.getElementById('proses0').value='savedata0';
    document.getElementById('kodeorg').value='';
    document.getElementById('keterangan').value='';
    for(i=1;i<=jumlahkegiatan;i++)
    {
        try{
            document.getElementById('kegiatanvalue'+i).value=0;
        }
        catch(e){}
    }
    document.getElementById('container1').innerHTML='';
}

function savedata0()
{
    proses=document.getElementById('proses0').value;
    id=document.getElementById('id').value;
    tanggal=document.getElementById('tanggal').value;
    kodeorg=document.getElementById('kodeorg').options[document.getElementById('kodeorg').selectedIndex].value;
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    karyawan=document.getElementById('karyawan').options[document.getElementById('karyawan').selectedIndex].value;
    cek=document.getElementById('cek').options[document.getElementById('cek').selectedIndex].value;
    keterangan=document.getElementById('keterangan').value;
    jumlahkegiatan=document.getElementById('jumlahkegiatan').value;
    kegiatan='';
    for(i=1;i<=jumlahkegiatan;i++)
    {
        try{
            kegiatan+='&kegiatanid'+i+'='+document.getElementById('kegiatanid'+i).value;
            kegiatan+='&kegiatanvalue'+i+'='+document.getElementById('kegiatanvalue'+i).value;
        }
        catch(e){}
    }
    param='proses='+proses+'&id='+id+'&tanggal='+tanggal+'&kodeorg='+kodeorg
        +'&jabatan='+jabatan+'&karyawan='+karyawan+'&cek='+cek
        +'&keterangan='+keterangan+kegiatan+'&jumlahkegiatan='+jumlahkegiatan;
    tujuan='kebun_slave_crossblock.php';
    if((tanggal!='')&&(kodeorg!='')&&(jabatan!='')&&(karyawan!='')&&(cek!=''))
    post_response_text(tujuan, param, respog);			
    else alert('Please fill all fields.')
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    alert('Done.');
                    abissave0(jumlahkegiatan);
                    loaddata0();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}

function openkegiatan()
{
    id=document.getElementById('id').value;
    kelompok=document.getElementById('kelompok').options[document.getElementById('kelompok').selectedIndex].value;    
    param='proses=openkegiatan'+'&kelompok='+kelompok+'&id='+id;
    tujuan='kebun_slave_crossblock.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    hasil=con.responseText.split("####");
                    document.getElementById('container2').innerHTML=hasil[0];
                    document.getElementById('jumlahkegiatan').value=hasil[1];
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function loaddata0()
{    
    param='proses=loaddata0';
    tujuan='kebun_slave_crossblock.php';
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
                    document.getElementById('container0').innerHTML=con.responseText;
                    getperiode();
                }
            }
        }
        else {
            busy_off();
            error_catch(con.status);
        }
    }	      
}

function filldata0(id,tanggal,kodeorg,jabatan,karyawan,cek,keterangan)
{
    canceldata0();
    document.getElementById('id').value=id;
    document.getElementById('tanggal').value=tanggal;
    selectform=document.getElementById('kodeorg');
    for(along=0;along<selectform.length;along++)
        if(selectform.options[along].value==kodeorg)selectform.options[along].selected=true;
    selectform=document.getElementById('jabatan');
    for(along=0;along<selectform.length;along++)
        if(selectform.options[along].value==jabatan)selectform.options[along].selected=true;
//    getkaryawan(karyawan);
    selectform=document.getElementById('karyawan');
    for(along=0;along<selectform.length;along++)
        if(selectform.options[along].value==karyawan)selectform.options[along].selected=true;
    selectform=document.getElementById('cek');
    for(along=0;along<selectform.length;along++)
        if(selectform.options[along].value==cek)selectform.options[along].selected=true;
//    selectform=document.getElementById('qcid');
//    for(along=0;along<selectform.length;along++)
//        if(selectform.options[along].value==qcid)selectform.options[along].selected=true;
//    document.getElementById('jumlah').value=jumlah;
    document.getElementById('keterangan').value=keterangan;
    document.getElementById('proses0').value='editdata0';
}

function deldata0(id)
{
    param='proses=deldata0';
    param+='&id='+id;
    //alert(param);
    tujuan='kebun_slave_crossblock.php';
    if(confirm("Are you sure?"))
    {
        post_response_text(tujuan, param, respog);
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
                //	alert(con.responseText);
                    loaddata0();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

function exploredata(num)
{
    param='proses=loaddata0';
    param+='&page='+num;
    tujuan='kebun_slave_crossblock.php';

    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container0').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
