// JavaScript Document

function searchBrg(tab,title,content,ev)
{
    if(tab=='1'){
        qwe=document.getElementById('namabarang');        
    }
    qweV=qwe.value;
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
    if(qweV==''){
    }else{
        if(tab=='1'){
            document.getElementById('no_brg').value=qweV;
        }
        findBrg(tab);
    }
}

function findBrg(tab)
{
    if(tab=='1'){
        txt=trim(document.getElementById('no_brg').value);        
    }
    if(txt=='')
    {
        alert('Text is obligatory');
    }
    else if(txt.length<3)
    {
        alert('Please input up to 3 characters');
    }
    else
    {
        if(tab=='1'){
            param='tab=1&txtfind='+txt;
        }
        tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
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
                } else {
                    if(tab=='1')
                        document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
}

function setBrg(tab,no_brg,namabrg,satuan)
{
    if(tab=='1'){
        document.getElementById('kodebarang').value=no_brg;
        document.getElementById('namabarang').value=namabrg;
        document.getElementById('satuan').innerHTML=satuan;
    }
    closeDialog();
}

function carispk()
{
    txt=trim(document.getElementById('nospk').value);        
    if(txt=='')
    {
        alert('Text is obligatory');
    }
    else
    {
        param='tab=9&spkfind='+txt;
        param2='tab=8&spkfind='+txt;
        tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
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
                } else {
                    document.getElementById('kegiatan').innerHTML=con.responseText;
                    post_response_text(tujuan, param2, respog2);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
    function respog2()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('blok').innerHTML=con.responseText;
                    caritanggal();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
}

function caritanggal()
{
    nospk=document.getElementById('nospk').value;
    kegiatan=document.getElementById('kegiatan').options[document.getElementById('kegiatan').selectedIndex].value;
    blok=document.getElementById('blok').options[document.getElementById('blok').selectedIndex].value;
    param='tab=7&nospk='+nospk+'&kegiatan='+kegiatan+'&blok='+blok;
    tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
    post_response_text(tujuan, param, respog);
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tanggal').value=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
}

function cancelForm()
{
    document.getElementById('nospk').value='';
    document.getElementById('kegiatan').innerHTML='<option value=\'\'>[ no SPK data ]</option>';
    document.getElementById('blok').innerHTML='<option value=\'\'>[ no SPK data ]</option>';
    document.getElementById('tanggal').value='';
    document.getElementById('kodebarang').value='';
    document.getElementById('satuan').innerHTML='';
    document.getElementById('namabarang').value='';
    document.getElementById('jumlah').value='';
}

function resetkobar()
{
    document.getElementById('kodebarang').value='';
    document.getElementById('satuan').innerHTML='';
}

function saveForm()
{
    nospk=document.getElementById('nospk').value;
    kegiatan=document.getElementById('kegiatan').options[document.getElementById('kegiatan').selectedIndex].value;
    blok=document.getElementById('blok').options[document.getElementById('blok').selectedIndex].value;
    tanggal=document.getElementById('tanggal').value;
    kodebarang=document.getElementById('kodebarang').value;
    jumlah=document.getElementById('jumlah').value;
    pros=document.getElementById('proses').value;
	
    param = "proses="+pros;
    param += "&nospk="+nospk;
    param += "&kegiatan="+kegiatan;
    param += "&blok="+blok;
    param += "&tanggal="+tanggal;
    param += "&kodebarang="+kodebarang;
    param += "&jumlah="+jumlah;        
	
    tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
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
                    loadNData(); 
                    alert('Done.');
                    document.getElementById('kodebarang').value='';
                    document.getElementById('satuan').innerHTML='';
                    document.getElementById('jumlah').value='';
                    document.getElementById('namabarang').value='';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 	
	
}

function loadNData()
{
    param='proses=loaddata';
    tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
    function respon(){
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
    post_response_text(tujuan, param, respon);
}

function cariBast(num)
{
    param='proses=loaddata';
    param+='&page='+num;
                
    tujuan = 'kebun_slave_pemakaianMaterialSPK_barang.php';
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

function delData(nospk,kegiatan,blok,tanggal,kodebarang)
{
    param='nospk='+nospk+'&kegiatan='+kegiatan+'&blok='+blok+'&tanggal='+tanggal+'&kodebarang='+kodebarang+'&proses=deletedata';
    tujuan='kebun_slave_pemakaianMaterialSPK_barang.php';
    if(confirm("Are You Sure Want Delete Data?"))
        post_response_text(tujuan, param, respog);
				
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    loadNData();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

