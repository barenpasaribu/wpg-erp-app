/**
 * @author antoniuslouis
 */
// dhyaz sep 14, 2011

//bersih2 tampilan
function prosesBaru()
{
    document.getElementById('container0').innerHTML='';
    document.getElementById('container1').innerHTML='';
    document.getElementById('container2').innerHTML='';
    document.getElementById('container3').innerHTML='';
    document.getElementById('container4').innerHTML='';
    document.getElementById('tab0').disabled=true;
    document.getElementById('tab1').disabled=true;
    document.getElementById('tab2').disabled=true;
    document.getElementById('tab3').disabled=true;
    document.getElementById('tab4').disabled=true;
    document.getElementById('tahunbudget').disabled=false;
    document.getElementById('kodews').disabled=false;
    document.getElementById('kodebudget0').value='';
    document.getElementById('hkefektif0').value='';
    document.getElementById('jumlahpersonel0').value='';
    document.getElementById('totalbiaya0').value='';
    document.getElementById('kodebudget1').value='';
    document.getElementById('kodebarang1').value='';
    document.getElementById('namabarang1').value='';
    document.getElementById('satuan1').value='';
    document.getElementById('jumlah1').value='';
    document.getElementById('totalharga1').value='';
    document.getElementById('kodebudget2').value='';
    document.getElementById('kodebarang2').value='';
    document.getElementById('namabarang2').value='';
    document.getElementById('satuan2').value='';
    document.getElementById('jumlah2').value='';
    document.getElementById('totalharga2').value='';
    document.getElementById('kodebudget3').value='';
    document.getElementById('kodeakun3').value='';
    document.getElementById('totalbiaya3').value='';
    document.getElementById('tutup4').disabled=true;
    
}

//fixation
function prosesSimpan(){
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    if(tahunbudgetV==''){
        alert('Budget is empty.');
        return;
    }
    if(kodewsV==''){
        alert('Workshop is empty.');
        return;
    }
    
    param='cekapa=hkef&tahunbudget='+tahunbudgetV;
    param+='&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
		busy_off();
		if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
		} else {
//                    showById('printPanel');
                    if(con.responseText==''){
                        alert('HK Efektif(Effective working days) not found.\n Please provide it first');
                    }else
                    {
                        //."#####".
                        isidt=con.responseText.split("#####");
                        document.getElementById('hkefektif0').value=isidt[0];
                        //document.getElementById('kodebudget0').innerHTML=isidt[1];
                        document.getElementById('tahunbudget').disabled=true;
                        document.getElementById('kodews').disabled=true;
                        document.getElementById('tab0').disabled=false;
                        document.getElementById('tab1').disabled=false;
                        document.getElementById('tab2').disabled=false;
                        document.getElementById('tab3').disabled=false;
                        document.getElementById('tab4').disabled=false;
                        document.getElementById('tutup4').disabled=true;
                        updateTab0('all');
                    }
//                    alert(con.responseText);
		}
            } else {
		busy_off();
		error_catch(con.status);
            }
	}
    }		
}


function bersihkan(tab)
{
    if(tab==1){
        kodebudget1 =document.getElementById('kodebudget1');
        kodebudget1V =kodebudget1.options[kodebudget1.selectedIndex].value;
        if(kodebudget1V==''){
            document.getElementById('kodebarang1').disabled=true;
            document.getElementById('jumlah1').disabled=true;
            document.getElementById('search1').disabled=true;
            
        }else{ // ada kodebudget
            document.getElementById('kodebarang1').disabled=false;
            document.getElementById('jumlah1').disabled=true;
            document.getElementById('search1').disabled=false;
    //        document.getElementById('kodebarang1').value=Right(kodebudget1V,3);
            document.getElementById('kodebarang1').value=kodebudget1V.slice(2);
            
        }
        document.getElementById('jumlah1').value='';
        document.getElementById('totalharga1').value='';
        document.getElementById('namabarang1').innerHTML='';
        document.getElementById('satuan1').innerHTML='';
    }
    if(tab==2){
        kodebudget2 =document.getElementById('kodebudget2');
        kodebudget2V =kodebudget2.options[kodebudget2.selectedIndex].value;
        if(kodebudget2V==''){
            document.getElementById('kodebarang2').disabled=true;
            document.getElementById('jumlah2').disabled=true;
            document.getElementById('search2').disabled=true;
            
        }else{ // ada kodebudget
            document.getElementById('kodebarang2').disabled=false;
            document.getElementById('jumlah2').disabled=true;
            document.getElementById('search2').disabled=false;
    //        document.getElementById('kodebarang1').value=Right(kodebudget1V,3);
            
        }
        document.getElementById('jumlah2').value='';
        document.getElementById('totalharga2').value='';
        document.getElementById('namabarang2').innerHTML='';
        document.getElementById('satuan2').innerHTML='';
    }
}

function jumlahkan0()
{
    kodebudget0 =document.getElementById('kodebudget0');
    kodebudget0V =kodebudget0.options[kodebudget0.selectedIndex].value;
    hkefektif0 =document.getElementById('hkefektif0');
    hkefektif0V =hkefektif0.value;
    kodews =document.getElementById('kodews').value;
    jumlahpersonel0 =document.getElementById('jumlahpersonel0');
    jumlahpersonel0V =jumlahpersonel0.value;
//    kodebudget0 =document.getElementById('kodebudget0');
    param='cekapa=upah&kodebudget0='+kodebudget0V+'&kodews='+kodews;
//        alert(param);
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    
//    alert(hkefektif0V+" "+jumlahpersonel0V);
		       
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        document.getElementById('kodebudget0').value='';
                        alert('Daily salary(Upah harian) not yet provided or not yet posted');
                    }else
                    {
                        upah=con.responseText
                        jumlah=hkefektif0V*upah*jumlahpersonel0V;
                        document.getElementById('totalbiaya0').value=jumlah;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function jumlahkan1()
{
    kodebarang1 =document.getElementById('kodebarang1');
    kodebarang1V =kodebarang1.value;
    jumlah1 =document.getElementById('jumlah1');
    jumlah1V =jumlah1.value;
    kodews =document.getElementById('kodews');
    kodewsV =kodews.options[kodews.selectedIndex].value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV =tahunbudget.value;
    param='cekapa=regional&kodews='+kodewsV;
    param2='cekapa=barang&tahunbudget='+tahunbudgetV+'&kodebarang1='+kodebarang1V;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
		        
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                    document.getElementById('jumlah1').value='';
                    alert('This workshop does not assingned to any regional.\n Please assign it first');
                }else
                {
                    document.getElementById('regional1').value=con.responseText;
                    param2=param2+'&regional='+con.responseText;
                    post_response_text(tujuan, param2, respog2);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    function respog2(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        document.getElementById('jumlah1').value='';
                        alert('Material price not found');
                    }else{
                        harga=con.responseText;
                        jumlah=harga*jumlah1V;
                        document.getElementById('totalharga1').value=jumlah;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function jumlahkan2()
{
    kodebarang2 =document.getElementById('kodebarang2');
    kodebarang2V =kodebarang2.value;
    jumlah2 =document.getElementById('jumlah2');
    jumlah2V =jumlah2.value;
    kodews =document.getElementById('kodews');
    kodewsV =kodews.options[kodews.selectedIndex].value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV =tahunbudget.value;
    param='cekapa=regional&kodews='+kodewsV;
    param2='cekapa=barang&tahunbudget='+tahunbudgetV+'&kodebarang1='+kodebarang2V;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
		        
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        document.getElementById('jumlah2').value='';
                        alert('This workshop does not assingned to any regional.\n Please assign it first');
                    }else
                    {
                        document.getElementById('regional2').value=con.responseText;
                        param2=param2+'&regional='+con.responseText;
                        post_response_text(tujuan, param2, respog2);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    function respog2(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        document.getElementById('jumlah2').value='';
                        alert('Material price not found');
                    }else
                    {
                        harga=con.responseText;
                        jumlah=harga*jumlah2V;
                        document.getElementById('totalharga2').value=jumlah;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function simpan0()
{
    kodebudget0 =document.getElementById('kodebudget0');
    hkefektif0 =document.getElementById('hkefektif0');
    jumlahpersonel0 =document.getElementById('jumlahpersonel0');
    totalbiaya0 =document.getElementById('totalbiaya0');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    kodews =document.getElementById('kodews');
    kodebudget0V =kodebudget0.options[kodebudget0.selectedIndex].value;
    hkefektif0V	=hkefektif0.value;
    jumlahpersonel0V =jumlahpersonel0.value;
    totalbiaya0V =totalbiaya0.value;
    tipebudgetV =tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    kodewsV =kodews.options[kodews.selectedIndex].value;

    if(kodebudget0V==''){
        alert('Kode is empty.');
        return;
    }
    if(jumlahpersonel0V==''){
        alert('Personel is empty.');
        return;
    }

    param='tab=0&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV+'&kodebudget0='+kodebudget0V+'&hkefektif0='+hkefektif0V+'&jumlahpersonel0='+jumlahpersonel0V+'&totalbiaya0='+totalbiaya0V;
    tujuan='budget_slave_ws_biaya_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV;
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        post_response_text(tujuan, param, respon);
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
                        document.getElementById('kodebudget0').value='';
                        document.getElementById('jumlahpersonel0').value='';
                        document.getElementById('totalbiaya0').value='';
                        updateTab0();    
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function updateTab0(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    param='cekapa=tab0&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    param1='cekapa=tab1&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
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
                    if(apa=='all')updateTab1('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		
}

function updateTab1(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    param='cekapa=tab1&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container1').innerHTML=con.responseText;
                    if(apa=='all')updateTab2('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function updateTab2(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    param='cekapa=tab2&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container2').innerHTML=con.responseText;
                    if(apa=='all')updateTab3('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function updateTab3(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    param='cekapa=tab3&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container3').innerHTML=con.responseText;
//                    if(apa=='all')updateTab4('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function persiapantutup4()
{
    updateTab4();
    document.getElementById('tutup4').disabled=false;
    
}

function updateTab4(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    kodews =document.getElementById('kodews');
    kodewsV	=kodews.options[kodews.selectedIndex].value;
    param='cekapa=tab4&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&kodews='+kodewsV;
    tujuan='budget_slave_ws_biaya_hkef.php'; 
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container4').innerHTML=con.responseText;
//                    if(apa=='all')updateTab5('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function tutup4(row)
{
    kunci =document.getElementById('kunci_'+row).value;
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    kodews =document.getElementById('kodews');
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    kodewsV =kodews.options[kodews.selectedIndex].value;
    param='tab=4&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV+'&kunci='+kunci;
    tujuan='budget_slave_ws_biaya_save.php';
    if(confirm('Close?\nOnces closed, after the confirmation of the data can not be changed .'))post_response_text(tujuan, param, respon);
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
                            tutup4(row);
                        } else {
//baris terakhir, hapus header, berikan pesan DONE                            
                            alert('Done');
                            document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                            updateTab0('all');
                        }
                    }
                    catch(e)
                    {
//baris terakhir, hapus header, berikan pesan DONE                            
//                        alert('Done');
                        document.getElementById('baris_0').style.display='none';
//                            document.getElementById('proses').disable=true;
//                            document.getElementById('simpan').disable=true;
                        updateTab0('all');
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
}


function simpan1()
{
    satuan1 =document.getElementById('satuan1');
    jumlah1 =document.getElementById('jumlah1');
    regional1 =document.getElementById('regional1');
    kodebarang1 =document.getElementById('kodebarang1');
    totalharga1 =document.getElementById('totalharga1');
    kodebudget1 =document.getElementById('kodebudget1');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    kodews =document.getElementById('kodews');

    satuan1V=satuan1.innerHTML;
    jumlah1V=jumlah1.value;
    regional1V=regional1.value;
    kodebarang1V=kodebarang1.value;
    totalharga1V=totalharga1.value;
    kodebudget1V=kodebudget1.options[kodebudget1.selectedIndex].value;
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    kodewsV =kodews.options[kodews.selectedIndex].value;

    if(jumlah1V==''){
        alert('Ammount is empty.');
        return;
    }
    param='tab=1&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV+'&kodebudget1='+kodebudget1V+'&totalharga1='+totalharga1V+'&kodebarang1='+kodebarang1V+'&regional1='+regional1V+'&jumlah1='+jumlah1V+'&satuan1='+satuan1V;
    tujuan='budget_slave_ws_biaya_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV;
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        post_response_text(tujuan, param, respon);
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
                        document.getElementById('satuan1').innerHTML='';
                        document.getElementById('jumlah1').value='';
                        document.getElementById('namabarang1').innerHTML='';
                        document.getElementById('kodebarang1').value='';
                        document.getElementById('totalharga1').value='';
                        document.getElementById('kodebudget1').value='';
                        updateTab1();    
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function simpan2()
{
    satuan2 =document.getElementById('satuan2');
    jumlah2 =document.getElementById('jumlah2');
    regional2 =document.getElementById('regional2');
    kodebarang2 =document.getElementById('kodebarang2');
    totalharga2 =document.getElementById('totalharga2');
    kodebudget2 =document.getElementById('kodebudget2');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    kodews =document.getElementById('kodews');

    satuan2V=satuan2.innerHTML;
    jumlah2V=jumlah2.value;
    regional2V=regional2.value;
    kodebarang2V=kodebarang2.value;
    totalharga2V=totalharga2.value;
    kodebudget2V=kodebudget2.options[kodebudget2.selectedIndex].value;
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    kodewsV =kodews.options[kodews.selectedIndex].value;

    if(jumlah2V==''){
        alert('Ammount is empty.');
        return;
    }
    param='tab=2&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV+'&kodebudget2='+kodebudget2V+'&totalharga2='+totalharga2V+'&kodebarang2='+kodebarang2V+'&regional2='+regional2V+'&jumlah2='+jumlah2V+'&satuan2='+satuan2V;
    tujuan='budget_slave_ws_biaya_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV;
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        post_response_text(tujuan, param, respon);
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
                        document.getElementById('satuan2').innerHTML='';
                        document.getElementById('jumlah2').value='';
                        document.getElementById('namabarang2').innerHTML='';
                        document.getElementById('kodebarang2').value='';
                        document.getElementById('totalharga2').value='';
                        document.getElementById('kodebudget2').value='';
                        updateTab2();    
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function simpan3()
{
    kodeakun3 =document.getElementById('kodeakun3');
    totalbiaya3 =document.getElementById('totalbiaya3');
    kodebudget3 =document.getElementById('kodebudget3');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    kodews =document.getElementById('kodews');

    kodeakun3V=kodeakun3.options[kodeakun3.selectedIndex].value;
    totalbiaya3V=totalbiaya3.value;
    kodebudget3V=kodebudget3.options[kodebudget3.selectedIndex].value;
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    kodewsV =kodews.options[kodews.selectedIndex].value;

    if(kodebudget3V==''){
        alert('Please fill budget code.');
        return;
    }
    if(kodeakun3V==''){
        alert('Please fil budget account.');
        return;
    }
    if(totalbiaya3V==''){
        alert('Cost required.');
        return;
    }
    param='tab=3&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV+'&kodebudget3='+kodebudget3V+'&totalbiaya3='+totalbiaya3V+'&kodeakun3='+kodeakun3V;
    tujuan='budget_slave_ws_biaya_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&kodews='+kodewsV;
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        post_response_text(tujuan, param, respon);
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
//tidak ada error, cek response                    
                    if(con.responseText==''){
                        alert('Done');
                        document.getElementById('kodeakun3').value='';
                        document.getElementById('totalbiaya3').value='';
                        document.getElementById('kodebudget3').value='';
                        updateTab3();    
                    }else{
                        alert(con.responseText);
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function deleteRow(tab,kunci)
{
    {
        param='cekapa=delete0&kunci='+kunci;
        tujuan='budget_slave_ws_biaya_hkef.php';
        if(confirm('Delete?'))post_response_text(tujuan, param, respog);		
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
                    alert('Done.');
                    if(tab=='0')updateTab0();
                    if(tab=='1')updateTab1();
                    if(tab=='2')updateTab2();
                    if(tab=='3')updateTab3();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }		
}

function searchBrg(tab,title,content,ev)
{
    if(tab=='1'){
        qwe=document.getElementById('kodebarang1');        
    }
    if(tab=='2'){
        qwe=document.getElementById('kodebarang2');        
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
        if(tab=='2'){
            document.getElementById('no_brg2').value=qweV;
        }
        findBrg(tab);
    }
}

function findBrg(tab)
{
    if(tab=='1'){
        kodebudget1 =document.getElementById('kodebudget1');
        kodebudget1V	=kodebudget1.options[kodebudget1.selectedIndex].value;
        kodebudget1V=kodebudget1V.slice(2);
	txt=trim(document.getElementById('no_brg').value);        
    }
    if(tab=='2'){
	txt=trim(document.getElementById('no_brg2').value);        
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
            param='tab=1&txtfind='+txt+'&awalan='+kodebudget1V;
        }
        if(tab=='2'){
            param='tab=2&txtfind='+txt+'&awalan=';
        }
        tujuan='budget_slave_ws_biaya_barang.php';
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
                    if(tab=='2')
                    document.getElementById('containerx').innerHTML=con.responseText;
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
        document.getElementById('jumlah1').value='';
        document.getElementById('totalharga1').value='';
        document.getElementById('kodebarang1').value=no_brg;
        document.getElementById('namabarang1').innerHTML=namabrg;
        document.getElementById('satuan1').innerHTML=satuan;
        document.getElementById('jumlah1').disabled=false;
    }
    if(tab=='2'){
        document.getElementById('jumlah2').value='';
        document.getElementById('totalharga2').value='';
        document.getElementById('kodebarang2').value=no_brg;
        document.getElementById('namabarang2').innerHTML=namabrg;
        document.getElementById('satuan2').innerHTML=satuan;
        document.getElementById('jumlah2').disabled=false;
    }
    closeDialog();
}
