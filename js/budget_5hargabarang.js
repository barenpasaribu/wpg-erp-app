/**
 * @author antoniuslouis
 */
// dhyaz aug 10, 2011

//menampilkan tab list data
function displayList()
{
    document.getElementById('frminput').style.display='none';
    document.getElementById('frmlist').style.display='';
    document.getElementById('container2').innerHTML='';
    document.getElementById('container3').innerHTML='';
    tujuan='budget_slave_5hargabarang_listhead.php';
    param='';
    post_response_text(tujuan, param, respog); 
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    showById('printPanel2');
                    document.getElementById('container3').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function buatbaru(tahun,regional,kelompok)
{
    displayFormInput();
    document.getElementById('tahunbudget').value=tahun;
    document.getElementById('regional').value=regional;
    document.getElementById('kelompokbarang').value=kelompok;
}

//menampilkan tab form input
function displayFormInput()
{
    document.getElementById('frminput').style.display='';
    document.getElementById('frmlist').style.display='none';	
    document.getElementById('tahunbudget').value='';
    document.getElementById('regional').value='';
    document.getElementById('tahunbudget').disabled=false;
    document.getElementById('regional').disabled=false;
    document.getElementById('sumberharga').disabled=false;
    document.getElementById('legendinput').innerHTML='New';
    document.getElementById('hiddenprocess').value='';
    document.getElementById('sumberharga').style.display='';
    document.getElementById('sumberharga').value='';
    document.getElementById('kelompokbarang').value='';
    document.getElementById('container').innerHTML='';
}

//tampilkan form harga baru                                                     GANTI REGIONALNYA NIH!!!
//function tampolkanHarga(row)
function tampolkanHarga(tahun,regional)
{
    param='what=editing&tahunbudget='+tahun+'&regional='+regional+'';
    tujuan='budget_slave_5hargabarang_cek.php';
    tujuancek='budget_slave_5hargabarang_cek.php';
    param2='what=closing&tahunbudget='+tahun+'&regional='+regional+'';
    post_response_text(tujuancek, param2, respogcek3);
    function respogcek3(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                } else {
                    if(con.responseText){
                        alert(con.responseText);
                    }else{
//kalo belon close, display deh
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                    document.getElementById('tahunbudget').value=tahun;
                    document.getElementById('regional').value=regional;
                    document.getElementById('tahunbudget').disabled=true;
                    document.getElementById('regional').disabled=true;
                    document.getElementById('sumberharga').disabled=true;
					document.getElementById('sumberharga').value = regional;
                    document.getElementById('legendinput').innerHTML='Edit';
                    document.getElementById('kelompokbarang').value='';
                    document.getElementById('hiddenprocess').value='edit';
                    document.getElementById('frminput').style.display='';
                    document.getElementById('frmlist').style.display='none';	

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
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                    document.getElementById('tahunbudget').value=tahun;
                    document.getElementById('regional').value=regional;
                    document.getElementById('kelompokbarang').value='';
                    document.getElementById('hiddenprocess').value='edit';
                    document.getElementById('frminput').style.display='';
                    document.getElementById('frmlist').style.display='none';	
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		    
}

//tampilkan harga baru (diambil dari masterbarang)
function tampolHarga2(tahun,regional)
{
    kelompokbarang =document.getElementById('kelompokbarang');
    kelompokbarangV	=kelompokbarang.options[kelompokbarang.selectedIndex].value;
    if(kelompokbarangV==''){
        alert('Material group required');
        return;
    }

    param='what=editing&tahunbudget='+tahun+'&regional='+regional+'&kelompokbarang='+kelompokbarangV+'';
    tujuan='budget_slave_5hargabarang_cek.php';
//pertama, cek apakah data sudah di-closing?    
    tujuancek='budget_slave_5hargabarang_cek.php';
    param2='what=closing&tahunbudget='+tahun+'&regional='+regional+'';
    post_response_text(tujuancek, param2, respogcek3);
    function respogcek3(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                } else {
                    if(con.responseText){
                        alert(con.responseText);
                    }else{
//kalo belon close, display deh
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;

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
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
                  showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                } else {
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		    
}

//tampilkan harga baru (diambil dari masterbarang)
function tampolHarga()
{
    tahunbudget =document.getElementById('tahunbudget');
    regional =document.getElementById('regional');
    sumberharga =document.getElementById('sumberharga');
    kelompokbarang =document.getElementById('kelompokbarang');
    sumberhargaV =sumberharga.options[sumberharga.selectedIndex].value;
    kelompokbarangV	=kelompokbarang.options[kelompokbarang.selectedIndex].value;
    tahunbudgetV	=tahunbudget.value;
    regionalV	=regional.options[regional.selectedIndex].value;

    hiddenprocess =document.getElementById('hiddenprocess').value;
    if(hiddenprocess=='edit'){
        tampolHarga2(tahunbudgetV,regionalV);
        return;
    }

    param='what=adadata&tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&sumberharga='+sumberhargaV+'&kelompokbarang='+kelompokbarangV;
    param2='what=closing&tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&sumberharga='+sumberhargaV+'&kelompokbarang='+kelompokbarangV;
    tujuan='budget_slave_5hargabarang.php';
    tujuancek='budget_slave_5hargabarang_cek.php';
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
                    }else{
//kalo belon di-closing, cek apakah sudah ada data?
                        post_response_text(tujuancek, param, respogcek);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
    function respogcek(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                } else {
                    if(con.responseText){
//kasih peringatan sudah ada data                        
                        if(confirm(con.responseText))
                        post_response_text(tujuan, param, respog);	
                    }else{
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
                    showById('printPanel');
                    document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

//tampilkan harga dari list
function deleteHarga(tahun,regional)
{
    if(confirm('Delete?'))
    {

    }else
    {
        return
    }
    var ada=0;
    param='what=delete&tahunbudget='+tahun+'&regional='+regional+'';
    tujuan='budget_slave_5hargabarang_cek.php';
//pertama, cek apakah data sudah di-closing?    
    tujuancek='budget_slave_5hargabarang_cek.php';
    param2='what=closing&tahunbudget='+tahun+'&regional='+regional+'';
    post_response_text(tujuancek, param2, respogcek3);
    function respogcek3(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                } else {
                    if(con.responseText){
                        alert(con.responseText);
                    }else{
//kalo belon close, apus deh
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
                    alert('Done.');
                    displayList();
                    // berhasil dihapus
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

//tampilkan harga dari list
function listHarga(row)
{
    tahunbudget =document.getElementById('tahun2_'+row);
    tahunbudget=tahunbudget.innerHTML;
    regional =document.getElementById('reg2_'+row);
    regional=regional.innerHTML;
    var ada=0;
    param2='what=closing&tahunbudget='+tahunbudget+'&regional='+regional;
    param='tahunbudget='+tahunbudget+'&regional='+regional;
    tujuancek='budget_slave_5hargabarang_cek.php';
    tujuan='budget_slave_5hargabarang_list.php';
//pertama, cek apakah data sudah di-closing?    
    post_response_text(tujuancek, param2, respogcek3);
    function respogcek3(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                } else {
                    if(con.responseText){
                        alert(con.responseText);
                        post_response_text(tujuan, param, respog);
                    }else{
//kalo belon close, tampilin deh tuh + enable button edit n close
                        post_response_text(tujuan, param, respog);
                        document.getElementById('edit_'+row).removeAttribute('disabled');
                        document.getElementById('close_'+row).removeAttribute('disabled');
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
                    showById('printPanel');
                    document.getElementById('container2').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

//hitung harga berdasarkan harga awal + varian
function hitungharga(harga,varian,no)
{
    if(varian=='')varian='0';
    if(varian=='-')varian='0';
    harga=parseFloat(harga);
    varian=parseFloat(varian);
    hargasetelah=harga+(harga*varian/100);
    hargasetelah=hargasetelah.toFixed(2);
    if(hargasetelah=='-0.00')hargasetelah='0.00';
    document.getElementById('harga_'+no).value=hargasetelah;
}

//hitung varian berdasarkan harga awal + akhir
function hitungpersen(harga,hargasetelah,no)
{
    if(hargasetelah=='')hargasetelah='0';
    if(hargasetelah=='-')hargasetelah='0';
    harga=parseFloat(harga);
    hargasetelah=parseFloat(hargasetelah);
    varian=(hargasetelah-harga)/harga*100;
    varian=varian.toFixed(2);
    if(varian=='-0.00')varian='0.00';
    document.getElementById('varian_'+no).value=varian;
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

//klik proses (varian)
function updateHargaall()
{
    varianall =document.getElementById('varianall');
    varianallV =varianall.value;
    var tbl = document.getElementById("container9");
    var row = tbl.rows.length;
    for(i=1;i<row;i++)
    {
        document.getElementById('varian_'+i).value=varianallV;
        harga = document.getElementById('rata_'+i);
        hargaV=harga.innerHTML;
        hargaV=remove_comma_var(hargaV);
        if(varianall=='')varianall='0';
        if(varianall=='-')varianall='0';
        hargaV=parseFloat(hargaV);
        varianall=parseFloat(varianall);
        hargasetelah=hargaV+(hargaV*varianallV/100);
        hargasetelah=hargasetelah.toFixed(2);
        if(hargasetelah=='-0.00')hargasetelah='0.00';
        document.getElementById('harga_'+i).value=hargasetelah;
    }
}

//save harga barang ke table budget
function simpanHarga(row)
{
    tahunbudget =document.getElementById('tahunbudget');
    regional =document.getElementById('regional');
    sumberharga =document.getElementById('sumberharga');
    kode =document.getElementById('kode_'+row);
    rata =document.getElementById('rata_'+row);
    varian =document.getElementById('varian_'+row);
    harga =document.getElementById('harga_'+row);
    tahunbudgetV	=tahunbudget.value;
//    regionalV	=regional.value;
    regionalV =regional.options[regional.selectedIndex].value;
    sumberhargaV =sumberharga.options[sumberharga.selectedIndex].value;
    kodeV=kode.innerHTML;
    rataV=rata.innerHTML;
    varianV =varian.value;
    hargaV =harga.value;
    rataV =remove_comma_var(rataV);
    param='tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&kodebarang='+kodeV+'&hargasatuan='+hargaV+'&sumberharga='+sumberhargaV+'&variant='+varianV+'&hargalalu='+rataV;
    tujuan='budget_slave_5hargabarang_save.php';
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
                            document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                            alert('Done');
                        }
                    }
                    catch(e)
                    {
//baris terakhir, hapus header, berikan pesan DONE                            
                        document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function updateHarga(row)
{
    tahunbudget =document.getElementById('tahunbudget');
    regional =document.getElementById('regional');
    sumberharga =document.getElementById('sumber_'+row);
    kode =document.getElementById('kode_'+row);
    rata =document.getElementById('rata_'+row);
    varian =document.getElementById('varian_'+row);
    harga =document.getElementById('harga_'+row);
    tahunbudgetV	=tahunbudget.value;
//    regionalV	=regional.value;
    regionalV =regional.options[regional.selectedIndex].value;
    sumberhargaV =sumberharga.innerHTML;
    kodeV=kode.innerHTML;
    rataV=rata.innerHTML;
    varianV =varian.value;
    hargaV =harga.value;
    rataV =remove_comma_var(rataV);
//    param='tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&kodebarang='+kodeV+'&hargasatuan='+hargaV+'&sumberharga='+sumberhargaV+'&variant='+varianV+'&hargalalu='+rataV;
    param='what=update&tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&kodebarang='+kodeV+'&hargasatuan='+hargaV+'&sumberharga='+sumberhargaV+'&variant='+varianV+'&hargalalu='+rataV;
    tujuan='budget_slave_5hargabarang_save.php';
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
                            document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                            alert('Done');
                        }
                    }
                    catch(e)
                    {
//baris terakhir, hapus header, berikan pesan DONE                            
                        document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                        alert('Done');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

//close harga
function TutupHarga(row,row2)
{
    if(row==1){
        if(confirm('When it is closed, the data can not be modify. are you sure?'))
        {
    document.getElementById('close_'+row2).disabled=true;
        } else {
    document.getElementById('close_'+row2).disabled=false;
            return;
        }
    }
    tahunbudget =document.getElementById('tahun_'+row);
    regional =document.getElementById('reg_'+row);
    kode =document.getElementById('kode_'+row);
    tahunbudgetV	=tahunbudget.innerHTML;
    regionalV	=regional.innerHTML;
    kodeV=kode.innerHTML;
    param='tahunbudget='+tahunbudgetV+'&regional='+regionalV+'&kodebarang='+kodeV;
    tujuan='budget_slave_5hargabarang_close.php';
    post_response_text(tujuan, param, responl);
//    document.getElementById('barisl_'+row).style.backgroundColor='orange';
    function responl() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                    document.getElementById('barisl_'+row).style.backgroundColor='red';
                } else {
                        alert('Done');
//                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function hargaKeExcel(ev,row)
{
    tahunbudget =document.getElementById('tahun2_'+row);
    tahunbudget=tahunbudget.innerHTML;
    regional =document.getElementById('reg2_'+row);
    regional=regional.innerHTML;
    param='tahunbudget='+tahunbudget+'&regional='+regional;
    tujuan='budget_5hargabarang_Excel.php';
    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function resetcontainer()
{
                    document.getElementById('container').innerHTML='';    
}

function searchBrg(tab,title,content,ev){
    isi=document.getElementById('regional1');
    isi=isi.options[isi.selectedIndex].value;
    thn=document.getElementById('tahunbudget1').value;
    if(tab=='1'){
        qwe=document.getElementById('kodebarang1');        
    }
    //alert(isi+"___"+thn);
    //return;
    qweV=qwe.value;
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
    document.getElementById('regbrg').value=isi;
    document.getElementById('thnbgtbrg').value=thn;

    if(qweV==''){
    }else{
        if(tab=='1'){alert("dada"+isi);
            document.getElementById('no_brg').value=qweV;
           
            }
//        if(tab=='2'){
//            document.getElementById('no_brg2').value=qweV;
//        }
        findBrg(tab);
    
    }
}

function findBrg(tab){
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
            reg=document.getElementById('regbrg').value;
            tahun=document.getElementById('thnbgtbrg').value;
            param='tab=1&txtfind='+txt+'&awalan=';
            param+='&thnbgt='+tahun+'&regional='+reg;
        }
        if(tab=='2'){
            param='tab=2&txtfind='+txt+'&awalan=';
        }
        tujuan='budget_slave_budget_pks_barang.php'; // dari BUDGET WORKSHOP
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
                    document.getElementById('containerq').innerHTML=con.responseText;
//                    if(tab=='2')
//                    document.getElementById('containerx').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
}

function setBrg(tab,no_brg,namabrg,satuan,nomor)
{
    if(tab=='1'){
        document.getElementById('kodebarang1').value=no_brg;
        document.getElementById('namabarang1').innerHTML=namabrg;
        document.getElementById('satuan1').innerHTML=' ('+satuan+')';
        document.getElementById('buttonedit').disabled=false;
//        document.getElementById('jumlah1').disabled=false;
    }
    closeDialog();
}

function editHarga()
{
    tahunbudget1 =document.getElementById('tahunbudget1');
    regional1 =document.getElementById('regional1');
    kodebarang1 =document.getElementById('kodebarang1');
    namabarang1 =document.getElementById('namabarang1');
    hargasatuan1 =document.getElementById('hargasatuan1');
//    regionalV	=regional.value;
    regional1V =regional1.options[regional1.selectedIndex].value;
    tahunbudget1V =tahunbudget1.value;
    kodebarang1V =kodebarang1.value;
    namabarang1V =namabarang1.innerHTML;
    hargasatuan1V =hargasatuan1.value;

    if(tahunbudget1V==''){
        alert('Budget year required');
        return;
    }
    if(regional1V==''){
        alert('Budget year required');
        return;
    }
    if(namabarang1V==''){
        alert('Click Lens Icon for material code confirmation.');
        return;
    }
    if(hargasatuan1V==''){
        alert('Material price required');
        return;
    }


    param='what=edit&tahunbudget='+tahunbudget1V+'&regional='+regional1V+'&kodebarang='+kodebarang1V+'&hargasatuan='+hargasatuan1V;
    tujuan='budget_slave_5hargabarang_save.php';
//template dari script tutup buku
    post_response_text(tujuan, param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('tahunbudget1').value='';
                    document.getElementById('regional1').value='';
                    document.getElementById('kodebarang1').value='';
                    document.getElementById('hargasatuan1').value='';
                    document.getElementById('namabarang1').innerHTML='';
                    document.getElementById('satuan1').innerHTML='';
                    document.getElementById('buttonedit').disabled=true;
                    alert('Done.');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

