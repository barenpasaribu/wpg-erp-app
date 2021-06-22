/**
 * @author Developer
 */
function simpan()
{
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    aset=document.getElementById('aset').options[document.getElementById('aset').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    nama=trim(document.getElementById('nama').value);
    tanggalmulai=trim(document.getElementById('tanggalmulai').value);
    tanggalselesai=trim(document.getElementById('tanggalselesai').value);
    method=document.getElementById('method').value;	
    kode=document.getElementById('kode').value;	
    
    if(unit=='')            { alert('Please fill UNIT'); exit(); }
    if(aset=='')            { alert('Please fill ASET'); exit(); }
    if(nama=='')            { alert('Please fill NAMA'); exit(); }
    if(tanggalmulai=='')    { alert('Please fill TANGGAL MULAI'); exit(); }
    if(tanggalselesai=='')  { alert('Please fill TANGGAL SELESAI'); exit(); }
    
    param='unit='+unit+'&aset='+aset+'&jenis='+jenis;
    param+='&nama='+nama+'&tanggalmulai='+tanggalmulai+'&tanggalselesai='+tanggalselesai+'&kode='+kode;
    param+='&method='+method;
    if(confirm('Save/Simpan?'))
    {
        tujuan = 'vhc_slave_project.php';
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
                    //alert(con.responseText);
                    alert('Done.');
                    //document.getElementById('container').innerHTML=con.responseText;
                    loadData();
                    batal();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }	
}

function batal()
{
    var d = new Date();
    var curr_date = d.getDate();
    var curr_month = d.getMonth() + 1; //Months are zero based
    var curr_year = d.getFullYear();
    d1=curr_date + "-" + curr_month + "-" + curr_year;

    document.getElementById('unit').value='';
    document.getElementById('aset').value='';
    document.getElementById('jenis').value='AK';
    document.getElementById('nama').value='';
    document.getElementById('tanggalmulai').value=d1;
    document.getElementById('tanggalselesai').value=d1;
    document.getElementById('method').value='insert';
    document.getElementById('kode').value='';
    
    document.getElementById('unit').disabled=false;
    document.getElementById('aset').disabled=false;
    document.getElementById('jenis').disabled=false;
}

function fillField(unit,aset,jenis,nama,tanggalmulai,tanggalselesai,method,kode)
{
    document.getElementById('unit').value=unit;
    document.getElementById('aset').value=aset;
    document.getElementById('jenis').value=jenis;
    document.getElementById('nama').value=nama;
    document.getElementById('tanggalmulai').value=tanggalmulai;
    document.getElementById('tanggalselesai').value=tanggalselesai;
    document.getElementById('method').value=method;
    document.getElementById('kode').value=kode;
    
    document.getElementById('unit').disabled=true;
    document.getElementById('aset').disabled=true;
    document.getElementById('jenis').disabled=true;
    

}
function detailForm(unit,aset,jenis,nama,tanggalmulai,tanggalselesai,method,kode)
{
    document.getElementById('unit').value=unit;
    document.getElementById('aset').value=aset;
    document.getElementById('jenis').value=jenis;
    document.getElementById('nama').value=nama;
    document.getElementById('tanggalmulai').value=tanggalmulai;
    document.getElementById('tanggalselesai').value=tanggalselesai;
    document.getElementById('method').value='insertDetail';
    document.getElementById('kode').value=kode;
    document.getElementById('kdProj').value=kode;
    document.getElementById('unit').disabled=true;
    document.getElementById('aset').disabled=true;
    document.getElementById('jenis').disabled=true;
    document.getElementById('tanggalselesai').disabled=true;
    document.getElementById('tanggalmulai').disabled=true;
    document.getElementById('nama').disabled=true;
    param='method='+method+'&kode='+kode;
    tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                   document.getElementById('detailInput').style.display='block';
                   document.getElementById('dataDisimpan').style.display='none';
                   document.getElementById('printDat').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function doneSlsi()
{
    //waktu=date('d-m-Y');
    document.getElementById('unit').value='';
    document.getElementById('aset').value='';
    document.getElementById('jenis').value='';
    document.getElementById('nama').value='';
    document.getElementById('method').value='insert';
    document.getElementById('kode').value='';
    document.getElementById('kdProj').value='';
    document.getElementById('unit').disabled=false;
    document.getElementById('aset').disabled=false;
    document.getElementById('jenis').disabled=false;
    document.getElementById('tanggalselesai').disabled=false;
    document.getElementById('tanggalmulai').disabled=false;
    document.getElementById('nama').disabled=false;
    document.getElementById('detailInput').style.display='none';
    document.getElementById('dataDisimpan').style.display='block';
    document.getElementById('printDat').innerHTML='';
    //document.getElementById('tanggalmulai').value=waktu;
    //document.getElementById('tanggalselesai').value=waktu;
}
function editDet(tanggalmulai,tanggalselesai,method,kode,knci,nmkeg)
{
    document.getElementById('kdProj').value=kode;
    document.getElementById('namaKeg').value=nmkeg;
    document.getElementById('tanggalMulai').value=tanggalmulai;
    document.getElementById('tanggalSampai').value=tanggalselesai;
    document.getElementById('kegId').value=knci;
    document.getElementById('method').value=method;
}
function hapus(kode)
{
    document.getElementById('method').value='hapus';
    param='kode='+kode+'&method=delete';
    if(confirm('Delete/Hapus '+kode+'?'))
    {
        tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                    alert('Done.');
                    //document.getElementById('container').innerHTML=con.responseText;
                    loadData();
                    batal();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }		
}
function loadData()
{
    param='method=loadData';
    tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                    document.getElementById('container').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}
function postIni(kd)
{
    param='method=postingData'+'&kode='+kd;
    tujuan='vhc_slave_project.php';
    if(confirm("Anda Yakin Ingin Memposting Kode :"+kd))
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
                    //alert(con.responseText);
                   loadData();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
function addDetail()
{
    kd=document.getElementById('kdProj').value;
    nmKeg=document.getElementById('namaKeg').value;
    tglMul=document.getElementById('tanggalMulai').value;
    tglSmp=document.getElementById('tanggalSampai').value;
    knci=document.getElementById('kegId').value;
    met=document.getElementById('method').value;
    param='&kode='+kd+'&nmKeg='+nmKeg+'&tglMul='+tglMul+'&tglSmp='+tglSmp;
    param+='&index='+knci+'&method='+met;
    tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                   // document.getElementById('container').innerHTML=con.responseText;
                   loadDetail();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function loadDetail()
{
    kd=document.getElementById('kdProj').value;
    param='method=detail'+'&kode='+kd;
    tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                    document.getElementById('printDat').innerHTML=con.responseText;
                    document.getElementById('method').value='insertDetail';
                    document.getElementById('namaKeg').value='';
                    document.getElementById('tanggalMulai').value=date('d-m-Y');
                    document.getElementById('tanggalSampai').value=date('d-m-Y');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function hapusData(kode)
{
    param='index='+kode+'&method=hpsDetail';
    if(confirm('Delete/Hapus Detail ?'))
    {
        tujuan='vhc_slave_project.php';
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
                    //alert(con.responseText);
                   loadDetail();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}