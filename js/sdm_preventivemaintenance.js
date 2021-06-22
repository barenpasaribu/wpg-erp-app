// JavaScript Document

function tambahdata()
{
    resetheader();
    document.getElementById('header').style.display='block';
    document.getElementById('listdata').style.display='none';
    tampilsave();
    document.getElementById('detailtable2').style.display='none';
}

function resetheader()
{
    document.getElementById('jenis').disabled=false;
    document.getElementById('mesin').disabled=false;
    document.getElementById('satuan').disabled=false;
    document.getElementById('atas').disabled=false;
    document.getElementById('peringatan').disabled=false;
    document.getElementById('tanggal').disabled=false;
    document.getElementById('tugas').disabled=false;
    document.getElementById('keterangan').disabled=false;
    document.getElementById('email').disabled=false;    
    document.getElementById('id').value='';
    document.getElementById('jenis').value='';
    loadkodemesin();
    document.getElementById('satuan').value='';
    document.getElementById('atas').value='';
    document.getElementById('resetHmkm').value='';
    document.getElementById('peringatan').value='';
    document.getElementById('tanggal').value='';
    document.getElementById('tugas').value='';
    document.getElementById('keterangan').value='';
    document.getElementById('email').value='';    
}

function kunciheader()
{
    document.getElementById('jenis').disabled=true;
    document.getElementById('mesin').disabled=true;
    document.getElementById('satuan').disabled=true;
    document.getElementById('atas').disabled=true;
    document.getElementById('peringatan').disabled=true;
    document.getElementById('tanggal').disabled=true;
    document.getElementById('tugas').disabled=true;
    document.getElementById('keterangan').disabled=true;
    document.getElementById('email').disabled=true;    
}

function tampildata()
{
    param='proses=load_data';
    tujuan='sdm_slave_preventivemaintenance.php';
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //alert(con.responseText);
                    document.getElementById('listdata').innerHTML=con.responseText;
                    document.getElementById('header').style.display='none';
                    document.getElementById('listdata').style.display='block';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respon);
}

function loadkodemesin()
{
    id=document.getElementById('id').value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    param='proses=load_mesin'+'&jenis='+jenis+'&id='+id;
    tujuan='sdm_slave_preventivemaintenance.php';
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //alert(con.responseText);
                    document.getElementById('mesin').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(tujuan, param, respon);
    if(jenis=='UMUM'){
        document.getElementById('atas').disabled=true;
        document.getElementById('peringatan').disabled=true;
    }else{
        document.getElementById('atas').disabled=false;
        document.getElementById('peringatan').disabled=false;        
    }
}

function tampilsave()
{
    document.getElementById('tombolsave').style.display='block';
    document.getElementById('tombolsave').innerHTML="<button class=mybutton id='simpan' onclick='simpanheader()'>"+tombolsimpan+"</button>\n\
        <button class=mybutton id='batal' onclick='batalheader()'>"+tombolbatal+"</button>";
}

function tampilsave2()
{
    document.getElementById('tombolselesai').innerHTML="<button class=mybutton id='selesai' onclick='tampildata()'>"+tomboldone+"</button>";
}


function cekperingatan()
{
    peringatan=document.getElementById('peringatan').value;
    if(peringatan!=0){
        document.getElementById('tanggal').disabled=true;        
    }else{
        document.getElementById('tanggal').disabled=false;        
    }
    document.getElementById('tanggal').value='';
}

function batalheader()
{
    tampildata();
}

function simpanheader()
{
    id=document.getElementById('id').value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    mesin=document.getElementById('mesin').options[document.getElementById('mesin').selectedIndex].value;
    satuan=document.getElementById('satuan').options[document.getElementById('satuan').selectedIndex].value;
    atas=document.getElementById('atas').value;
    rset=document.getElementById('resetHmkm').value;
    peringatan=document.getElementById('peringatan').value;
    sekali=document.getElementById('sekali').value;
    tanggal=document.getElementById('tanggal').value;
    tugas=document.getElementById('tugas').value;
    keterangan=document.getElementById('keterangan').value;
    email=document.getElementById('email').value;
     
    tujuan='sdm_slave_preventivemaintenance.php';
    param='proses=simpan_header'+'&id='+id+'&jenis='+jenis+'&mesin='+mesin+'&satuan='+satuan+'&atas='+atas+'&peringatan='+peringatan+
        '&tanggal='+tanggal+'&tugas='+tugas+'&keterangan='+keterangan+'&email='+email+'&sekali='+sekali+'&resetHmkm='+rset;
//        post_response_text(tujuan, param, respon);
    if((jenis!='')&&(mesin!='')&&(satuan!='')&&(tugas!='')&&(email!='')){
        post_response_text(tujuan, param, respon);
    }else{
        alert("warning: Please Complete Your Form");
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(id=='')document.getElementById('id').value=con.responseText;
                    // Success Response
                    document.getElementById('detailtable2').style.display='block';
                    add_detail();
                    document.getElementById('tombolsave').innerHTML='';
                    kunciheader();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function hapusdetail(kodebarang)
{
    id=document.getElementById('id').value;
    tujuan='sdm_slave_preventivemaintenance.php';
    param='proses=hapus_detail'+'&id='+id+'&kodebarang='+kodebarang;
    if(confirm("Delete "+id+" "+kodebarang+"?"))
    {	
        post_response_text(tujuan, param, respon);
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    add_detail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function hapusheader(id)
{
    tujuan='sdm_slave_preventivemaintenance.php';
    param='proses=hapus_header'+'&id='+id;
    if(confirm("Delete "+id+"?"))
    {	
        post_response_text(tujuan, param, respon);
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    tampildata();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function simpandetail()
{
    id=document.getElementById('id').value;
    kodebarang=document.getElementById('kodebarang').value;
    jumlahbarang=document.getElementById('jumlahbarang').value;

    tujuan='sdm_slave_preventivemaintenance.php';
    param='proses=simpan_detail'+'&id='+id+'&kodebarang='+kodebarang+'&jumlahbarang='+jumlahbarang;
    
    if((id!='')&&(kodebarang!='')&&(jumlahbarang!='')){
        post_response_text(tujuan, param, respon);
    }else{
        alert("warning: Please Complete Your Form");
    }
    
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    add_detail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function browsedata(num)
{
    param='proses=load_data';
    param+='&page='+num;
    tujuan = 'sdm_slave_preventivemaintenance.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('listdata').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function isiheader(id,jenis,mesin,satuan,atas,peringatan,tanggal,tugas,keterangan,email,skli,rset)
{
    resetheader();
    document.getElementById('id').value=id;
    document.getElementById('jenis').value=jenis;
    loadkodemesin();
    document.getElementById('satuan').value=satuan;
    document.getElementById('atas').value=atas;
    document.getElementById('peringatan').value=peringatan;
    document.getElementById('tanggal').value=tanggal;
    document.getElementById('tugas').value=tugas;
    document.getElementById('keterangan').value=keterangan;
    document.getElementById('email').value=email;
    document.getElementById('sekali').value=skli;
    document.getElementById('resetHmkm').value=rset;
    document.getElementById('header').style.display='block';
    document.getElementById('listdata').style.display='none';
    tampilsave();
    document.getElementById('detailtable2').style.display='none';
}

function lihatdetail(id,jenis,mesin,satuan,atas,peringatan,tanggal,tugas,keterangan,email,skli,rset)
{
    resetheader();
    document.getElementById('id').value=id;
    document.getElementById('jenis').value=jenis;
    loadkodemesin();
    document.getElementById('satuan').value=satuan;
    document.getElementById('atas').value=atas;
    document.getElementById('peringatan').value=peringatan;
    document.getElementById('tanggal').value=tanggal;
    document.getElementById('tugas').value=tugas;
    document.getElementById('keterangan').value=keterangan;
    document.getElementById('email').value=email;
    document.getElementById('sekali').value=skli;
    document.getElementById('resetHmkm').value=rset;
    document.getElementById('header').style.display='block';
    document.getElementById('listdata').style.display='none';
    kunciheader();
    document.getElementById('tombolsave').style.display='none';
    add_detail();
    document.getElementById('detailtable2').style.display='block';
}

function add_detail()
{
    id=document.getElementById('id').value;
//    document.getElementById('detailid').value=id;
    param='id='+id;
    param+="&proses=tambahdetail";
    //alert(param);
    tujuan='sdm_slave_preventivemaintenance.php';
    function respon(){
    if (con.readyState == 4) {
        if (con.status == 200) {
            busy_off();
            if (!isSaveResponse(con.responseText)) {
                alert('ERROR TRANSACTION,\n' + con.responseText);
            } else {
                // Success Response
                                    //alert(con.responseText);
                                    document.getElementById('detailisi').innerHTML=con.responseText;
                                    tampilsave2();
            }
        } else {
            busy_off();
            error_catch(con.status);
        }
    }
}
    post_response_text(tujuan, param, respon);
}

function searchBrg(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
}

function findBrg()
{
    txt=trim(document.getElementById('no_brg').value);
    if(txt=='')
    {
        alert('Text is obligatory');
    }
    else if(txt.length<3)
    {
        alert('Too Short Words');
    }
    else
    {
        param='txtcari='+txt+'&proses=cari_barang';
        tujuan='sdm_slave_preventivemaintenance.php';
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

function throwThisRow(no_brg,namabrg,satuan)
{
    document.getElementById('kodebarang').value=no_brg;
    document.getElementById('namabarang').value=namabrg;
    document.getElementById('satuanbarang').value=satuan;
    closeDialog();
}

function lihatpdf(id,ev)
{
    tujuan='sdm_slave_preventivemaintenance_pdf.php';
    judul= 'Preventive Maintenance/Scheduler '+id;		
    param='id='+id;
    param+="&proses=lihat_pdf";
    printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function overdueData()
{
    param='proses=getOverDue';
    tujuan='sdm_slave_preventivemaintenance.php';
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
                    document.getElementById('listdata').innerHTML=con.responseText;
                    document.getElementById('header').style.display='none';
                    document.getElementById('listdata').style.display='block';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function upStat(di,tgl)
{
        param='proses=upDate'+'&idStat='+di+'&tgl='+tgl;
        tujuan='sdm_slave_preventivemaintenance.php';
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
                     overdueData();
                    }
                }
                else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }
}