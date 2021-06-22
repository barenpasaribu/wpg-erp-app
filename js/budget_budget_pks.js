/**
 * @author antoniuslouis
 */
// dhyaz sep 20, 2011



function ubah_list()
{
	tahunbudget=document.getElementById('tahunbudget').value;
	tipebudget=document.getElementById('tipebudget').value;
	
	budgetSort=document.getElementById('budgetSort').value;
	mesinSort=document.getElementById('mesinSort').value;
	akunSort=document.getElementById('akunSort').value;
	
	
	//thnbudgetHeader=document.getElementById('thnbudgetHeader').options[document.getElementById('thnbudgetHeader').selectedIndex].value;
		
	param='cekapa=tab4'+'&tahunbudget='+tahunbudget+'&tipebudget='+tipebudget+'&budgetSort='+budgetSort+'&mesinSort='+mesinSort+'&akunSort='+akunSort;
	//alert (param);
	tujuan='budget_slave_budget_pks.php';
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
										document.getElementById('container4').innerHTML=con.responseText;
								}
						}
						else {
								busy_off();
								error_catch(con.status);
						}
		}	
	 } 
}	




function load_mesin()
{
    station =document.getElementById('station');
    stationV =station.options[station.selectedIndex].value;
    param='cekapa=station&station='+stationV;
    tujuan='budget_slave_budget_pks.php'; 
    post_response_text(tujuan, param, respog);
		       
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        alert('No Machine unit on this station.')
                    }else
                    {
                        document.getElementById('mesin').innerHTML=con.responseText;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
}

//fixation
function prosesSimpan()
{
    tahunbudget=document.getElementById('tahunbudget');
    tahunbudgetV=tahunbudget.value;
    mesin =document.getElementById('mesin');
    mesinV=mesin.options[mesin.selectedIndex].value;
    if(tahunbudgetV==''){
        alert('Budget year required');
        return;
    }
    if(mesinV==''){
        alert('Machine is empty.');
        return;
    }
    document.getElementById('tahunbudget').disabled=true;
    document.getElementById('station').disabled=true;
    document.getElementById('mesin').disabled=true;
    document.getElementById('tab0').disabled=false;
    document.getElementById('tab1').disabled=false;
    document.getElementById('tab2').disabled=false;
    document.getElementById('tab3').disabled=false;
    document.getElementById('tab4').disabled=false;
    param='cekapa=kendaraan&tahunbudget='+tahunbudgetV;
    tujuan='budget_slave_budget_pks.php'; 
    post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
//                    alert(con.responseText);
                    document.getElementById('kodevhc3').innerHTML=con.responseText;
                    updateTab0('all');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		    
}

function updateTahuntutup()
{
    tahuntutup =document.getElementById('tahuntutup');
    tahuntutupV=tahuntutup.options[tahuntutup.selectedIndex].value;
    param='cekapa=updatetahuntutup';
    tujuan='budget_slave_budget_pks.php';
    post_response_text(tujuan, param, respon);
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
//                        document.getElementById('tahuntutup').innerHTML=con.responseText;
                    }else{
                        document.getElementById('tahuntutup').innerHTML=con.responseText;
                        document.getElementById('tahuntutup').value=tahuntutupV;
                    }
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    } 
}

function prosesTutup()
{
    pabrik =document.getElementById('pabrik');
    tahuntutup =document.getElementById('tahuntutup');
    pabrikV =pabrik.options[pabrik.selectedIndex].value;
    tahuntutupV =tahuntutup.options[tahuntutup.selectedIndex].value;

    if(pabrikV==''){
        alert('Mill code required.');
        return;
    }
    if(tahuntutupV==''){
        alert('Budget year required');
        return;
    }
    
    param='tab=tutup&pabrik='+pabrikV+'&tahuntutup='+tahuntutupV;
    tujuan='budget_slave_budget_pks_save.php';
    if(confirm('Closing budet, are you sure ?'))post_response_text(tujuan, param, respon);
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
                        document.getElementById('pabrik').value='';
                        document.getElementById('tahuntutup').value='';
                        prosesBaru();    
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
    //document.getElementById('tahunbudget').value='';
    document.getElementById('station').value='';
    document.getElementById('mesin').innerHTML='';
    document.getElementById('tahunbudget').disabled=false;
    document.getElementById('station').disabled=false;
    document.getElementById('mesin').disabled=false;
    
}

function simpan0()
{
    kodebudget0 =document.getElementById('kodebudget0');
    jumlahpertahun0 =document.getElementById('jumlahpertahun0');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    mesin =document.getElementById('mesin');
    kodebudget0V =kodebudget0.options[kodebudget0.selectedIndex].value;
    jumlahpertahun0V	=jumlahpertahun0.value;
    tipebudgetV =tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    mesinV =mesin.options[mesin.selectedIndex].value;

    if(jumlahpertahun0V==''){
        alert('Amount per year required');
        return;
    }
    param='tab=0&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&mesin='+mesinV+'&kodebudget0='+kodebudget0V+'&jumlahpertahun0='+jumlahpertahun0V;
    tujuan='budget_slave_budget_pks_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV;
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
//                        alert(con.responseText);
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
                        document.getElementById('jumlahpertahun0').value='';
                        updateTab0('4');    
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
    mesin =document.getElementById('mesin');
    mesinV	=mesin.options[mesin.selectedIndex].value;
    param='cekapa=tab0&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&mesin='+mesinV;
    tujuan='budget_slave_budget_pks.php'; 
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
                    if(apa=='4')updateTab4();
                }
            }
            else {
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
        tujuan='budget_slave_budget_pks.php';
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
                    if(tab=='0')updateTab0();
                    if(tab=='1')updateTab1();
                    if(tab=='2')updateTab2();
                    if(tab=='3')updateTab3();
                    alert('Done.');
                    updateTab4();
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
    mesin =document.getElementById('mesin');
    mesinV =mesin.options[mesin.selectedIndex].value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV =tahunbudget.value;
    param='cekapa=regional&mesin='+mesinV;
    param2='cekapa=barang&tahunbudget='+tahunbudgetV+'&kodebarang1='+kodebarang1V;
    tujuan='budget_slave_budget_pks.php'; 
    post_response_text(tujuan, param, respog);
		        
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
//                    document.getElementById('jumlah1').focus();
                    alert('Working unit not listed on regional list');
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
                        alert('Price on material budget not found');
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

function bersihkan(tab)
{
    if(tab==1){
        kodebudget1 =document.getElementById('kodebudget1');
        kodebudget1V =kodebudget1.options[kodebudget1.selectedIndex].value;
        if(kodebudget1V==''){
            document.getElementById('kodebarang1').disabled=true;
            document.getElementById('jumlah1').disabled=true;
            document.getElementById('search1').disabled=true;
            document.getElementById('anggaranKd').disabled=true;
            
        }else{ // ada kodebudget
            document.getElementById('kodebarang1').disabled=false;
            document.getElementById('jumlah1').disabled=true;
            document.getElementById('search1').disabled=false;
            document.getElementById('anggaranKd').disabled=false;
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
            
        }
        document.getElementById('jumlah2').value='';
        document.getElementById('totalharga2').value='';
        document.getElementById('namabarang2').innerHTML='';
        document.getElementById('satuan2').innerHTML='';
    }
}

function simpan1()
{
    satuan1 =document.getElementById('satuan1');
    jumlah1 =document.getElementById('jumlah1');
    regional1 =document.getElementById('regional1');
    kodebarang1 =document.getElementById('kodebarang1');
    jenis1 =document.getElementById('jenis1');
    totalharga1 =document.getElementById('totalharga1');
    kodebudget1 =document.getElementById('kodebudget1');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    mesin =document.getElementById('mesin');
    anggaranKd=document.getElementById('anggaranKd');
    satuan1V=satuan1.innerHTML;
    jumlah1V=jumlah1.value;
    regional1V=regional1.value;
    kodebarang1V=kodebarang1.value;
    jenis1V=jenis1.options[jenis1.selectedIndex].value;
    anggaranKd=anggaranKd.options[anggaranKd.selectedIndex].value;
    totalharga1V=totalharga1.value;
    kodebudget1V=kodebudget1.options[kodebudget1.selectedIndex].value;
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    mesinV =mesin.options[mesin.selectedIndex].value;

    if(jumlah1V==''){
        alert('Volume is empty.\nTo be able to fill the volume, please fill budget code and material code first.');
        return;
    }
    if(jenis1V==''){
        alert('Material type is empty.');
        return;
    }
    if((parseFloat(totalharga1V)==0)||(totalharga1V=='')){
        alert('Total  is empty.\nTo get the total price, please fill out the amount and price of the goods in advance.');
        return;
    }
    param='tab=1&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&mesin='+mesinV+'&kodebudget1='+kodebudget1V+'&jenis1='+jenis1V+'&totalharga1='+totalharga1V;
    param+='&kodebarang1='+kodebarang1V+'&regional1='+regional1V+'&jumlah1='+jumlah1V+'&satuan1='+satuan1V+'&anggaranKd='+anggaranKd;
    tujuan='budget_slave_budget_pks_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV;
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
//                        alert(con.responseText);
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
                        document.getElementById('anggaranKd').value='';
                        document.getElementById('totalharga1').value='';
                        document.getElementById('kodebudget1').value='';
                        document.getElementById('jenis1').value='';
                        updateTab1('4');    
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

function updateTab1(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    mesin =document.getElementById('mesin');
    mesinV	=mesin.options[mesin.selectedIndex].value;
    param='cekapa=tab1&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&mesin='+mesinV;
    tujuan='budget_slave_budget_pks.php'; 
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
                    if(apa=='4')updateTab4();
                }
            }
            else {
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
        //findBrg(tab);
    }
}

function findBrg(tab){
    thn=document.getElementById('tahunbudget').value;
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
            param+='&thnbgt='+thn;
        }
        if(tab=='2'){
            param='tab=2&txtfind='+txt+'&awalan=';
            param+='&thnbgt='+thn;
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

function simpan2()
{
    kodebudget2 =document.getElementById('kodebudget2');
    jumlahpertahun2 =document.getElementById('jumlahpertahun2');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    mesin =document.getElementById('mesin');
    kodebudget2V =kodebudget2.value;
    jumlahpertahun2V	=jumlahpertahun2.value;
    tipebudgetV =tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    mesinV =mesin.options[mesin.selectedIndex].value;

    if(jumlahpertahun2V==''){
        alert('Volume per year is empty.');
        return;
    }
    param='tab=2&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&mesin='+mesinV+'&kodebudget2='+kodebudget2V+'&jumlahpertahun2='+jumlahpertahun2V;
    tujuan='budget_slave_budget_pks_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV;
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
//                        alert(con.responseText);
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
//                        document.getElementById('kodebudget0').value='';
                        document.getElementById('jumlahpertahun2').value='';
                        updateTab2('4');    
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

function updateTab2(apa)
{
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    mesin =document.getElementById('mesin');
    mesinV	=mesin.options[mesin.selectedIndex].value;
    param='cekapa=tab2&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&mesin='+mesinV;
    tujuan='budget_slave_budget_pks.php'; 
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
                    if(apa=='4')updateTab4();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
        }
        }
    }		
}

function jumlahkan3()
{
    kodebudget3 =document.getElementById('kodebudget3');
    kodebudget3V =kodebudget3.options[kodebudget3.selectedIndex].value;
    kodevhc3 =document.getElementById('kodevhc3');
    kodevhc3V =kodevhc3.options[kodevhc3.selectedIndex].value;
    jumlahjam3 =document.getElementById('jumlahjam3');
    jumlahjam3V =jumlahjam3.value;
    mesin =document.getElementById('mesin');
    mesinV =mesin.options[mesin.selectedIndex].value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV =tahunbudget.value;
    param2='cekapa=vhc&tahunbudget='+tahunbudgetV+'&kodevhc3='+kodevhc3V;
    tujuan='budget_slave_budget_pks.php'; 
    post_response_text(tujuan, param2, respog2);
		        
    function respog2(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    if(con.responseText==''){
                        document.getElementById('jumlah3').value='';
                        alert('Data Alat Berat / Kendaraaan belum memiliki biaya.\nSilakan menghubungi Traksi.');
                    }else{
                        harga=con.responseText;
                        jumlah=harga*jumlahjam3V;
                        document.getElementById('totalbiaya3').value=jumlah;
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
    kodevhc3 =document.getElementById('kodevhc3');
    jumlahjam3 =document.getElementById('jumlahjam3');
    satuan3 =document.getElementById('satuan3');
    totalbiaya3 =document.getElementById('totalbiaya3');
    kodebudget3 =document.getElementById('kodebudget3');
    tipebudget =document.getElementById('tipebudget');
    tahunbudget =document.getElementById('tahunbudget');
    mesin =document.getElementById('mesin');

    kodevhc3V=kodevhc3.options[kodevhc3.selectedIndex].value;
    jumlahjam3V=jumlahjam3.value;
    satuan3V=satuan3.value;
    totalbiaya3V=totalbiaya3.value;
    kodebudget3V=kodebudget3.options[kodebudget3.selectedIndex].value;
    tipebudget=tipebudget.value;
    tahunbudgetV =tahunbudget.value;
    mesinV =mesin.options[mesin.selectedIndex].value;

    if(kodebudget3V==''){
        alert('Budget code required.');
        return;
    }
    if(kodevhc3V==''){
        alert('Vehicle code required');
        return;
    }
    if(jumlahjam3V==''){
        alert('Working hour required.');
        return;
    }
    if((totalbiaya3V=='')||(totalbiaya3V=='0')){
        alert('The total cost is zero, please fill out the data first. If you already filled but still zero, please contact Vehicle organizer.');
        return;
    }
    param='tab=3&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV+'&mesin='+mesinV+'&kodebudget3='+kodebudget3V+'&kodevhc3='+kodevhc3V+'&jumlahjam3='+jumlahjam3V+'&satuan3='+satuan3V+'&totalbiaya3='+totalbiaya3V;
    tujuan='budget_slave_budget_pks_save.php';
    param2='tab=cekclose&tahunbudget='+tahunbudgetV+'&tipebudget='+tipebudgetV;
    post_response_text(tujuan, param2, respon2);
    function respon2() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
//ada error                    
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                        post_response_text(tujuan, param, respon);
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
                        document.getElementById('kodevhc3').value='';
                        document.getElementById('jumlahjam3').value='';
                        document.getElementById('totalbiaya3').value='';
                        document.getElementById('kodebudget3').value='';
                        updateTab3('4');    
                }
            } else {
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
    mesin =document.getElementById('mesin');
    mesinV	=mesin.options[mesin.selectedIndex].value;
    param='cekapa=tab3&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&mesin='+mesinV;
    tujuan='budget_slave_budget_pks.php'; 
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
                    if(apa=='all')updateTab4('all');
                    if(apa=='4')updateTab4();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function updateTab4(apa)
{

	
    tipebudget =document.getElementById('tipebudget');
    tipebudgetV	=tipebudget.value;
    tahunbudget =document.getElementById('tahunbudget');
    tahunbudgetV	=tahunbudget.value;
    mesin =document.getElementById('mesin');
    mesinV	=mesin.options[mesin.selectedIndex].value;
    param='cekapa=tab4&tipebudget='+tipebudgetV+'&tahunbudget='+tahunbudgetV+'&mesin='+mesinV;
    tujuan='budget_slave_budget_pks.php'; 
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
                    updateTahuntutup();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }			
}

function bersihkanDonk()
{
    for(zx=1;zx<13;zx++)
        {
            document.getElementById('ss'+zx).value=0;
        }
}

function sebarkanBoo(kunci,baris,obj,rupe,fis)
{
    document.getElementById('baris'+baris).style.backgroundColor='orange';
    var1=parseInt(document.getElementById('ss1').value);
    var2=parseInt(document.getElementById('ss2').value);
    var3=parseInt(document.getElementById('ss3').value);
    var4=parseInt(document.getElementById('ss4').value);
    var5=parseInt(document.getElementById('ss5').value);
    var6=parseInt(document.getElementById('ss6').value);
    var7=parseInt(document.getElementById('ss7').value);
    var8=parseInt(document.getElementById('ss8').value);
    var9=parseInt(document.getElementById('ss9').value);
    var10=parseInt(document.getElementById('ss10').value);
    var11=parseInt(document.getElementById('ss11').value);
    var12=parseInt(document.getElementById('ss12').value);
    zz=var1+var2+var3+var4+var5+var6+var7+var8+var9+var10+var11+var12;
    if(zz && zz>0)
    {
    param='cekapa=sebarDoong&kunci='+kunci;
    param+='&var1='+(var1/zz)+'&var2='+(var2/zz)+'&var3='+(var3/zz)+'&var4='+(var4/zz)+'&var5='+(var5/zz);
    param+='&var6='+(var6/zz)+'&var7='+(var7/zz)+'&var8='+(var8/zz)+'&var9='+(var9/zz)+'&var10='+(var10/zz);
    param+='&var11='+(var11/zz)+'&var12='+(var12/zz)+'&rupe='+rupe+'&fis='+fis;
    tujuan='budget_slave_budget_pks.php';
    if(obj.checked)
        post_response_text(tujuan, param, respog);            
    }
    else
    {
        alert('Distribution invalid');
    }

    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('baris'+baris).style.backgroundColor='red';
                }
                else {
//                    updateTab4();
                    document.getElementById('baris'+baris).style.backgroundColor='green';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
                document.getElementById('baris'+baris).style.backgroundColor='red';
            }
        }	
    } 
} 

function sebaran(kunci,ev)
{
   param='cekapa=sebaran4&kunci='+kunci;
   tujuan='budget_slave_budget_pks.php'+"?"+param;  
   width='400';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Sebaran '+kunci,content,width,height,ev); 
	
}

function simpansebaran(kunci,ev)
{
    jan4 =document.getElementById('jan4');
    jan4V =parseFloat(jan4.value);
    feb4 =document.getElementById('feb4');
    feb4V =parseFloat(feb4.value);
    mar4 =document.getElementById('mar4');
    mar4V =parseFloat(mar4.value);
    apr4 =document.getElementById('apr4');
    apr4V =parseFloat(apr4.value);
    may4 =document.getElementById('may4');
    may4V =parseFloat(may4.value);
    jun4 =document.getElementById('jun4');
    jun4V =parseFloat(jun4.value);
    jul4 =document.getElementById('jul4');
    jul4V =parseFloat(jul4.value);
    aug4 =document.getElementById('aug4');
    aug4V =parseFloat(aug4.value);
    sep4 =document.getElementById('sep4');
    sep4V =parseFloat(sep4.value);
    oct4 =document.getElementById('oct4');
    oct4V =parseFloat(oct4.value);
    nov4 =document.getElementById('nov4');
    nov4V =parseFloat(nov4.value);
    dec4 =document.getElementById('dec4');
    dec4V =parseFloat(dec4.value);
    total4 =document.getElementById('total4');
    total4V =parseFloat(total4.value);

    totalan4V=jan4V+feb4V+mar4V+apr4V+may4V+jun4V+jul4V+aug4V+sep4V+oct4V+nov4V+dec4V;
    
    jan4fis =document.getElementById('jan4fis');
    jan4fisV =parseFloat(jan4fis.value);
    feb4fis =document.getElementById('feb4fis');
    feb4fisV =parseFloat(feb4fis.value);
    mar4fis =document.getElementById('mar4fis');
    mar4fisV =parseFloat(mar4fis.value);
    apr4fis =document.getElementById('apr4fis');
    apr4fisV =parseFloat(apr4fis.value);
    may4fis =document.getElementById('may4fis');
    may4fisV =parseFloat(may4fis.value);
    jun4fis =document.getElementById('jun4fis');
    jun4fisV =parseFloat(jun4fis.value);
    jul4fis =document.getElementById('jul4fis');
    jul4fisV =parseFloat(jul4fis.value);
    aug4fis =document.getElementById('aug4fis');
    aug4fisV =parseFloat(aug4fis.value);
    sep4fis =document.getElementById('sep4fis');
    sep4fisV =parseFloat(sep4fis.value);
    oct4fis =document.getElementById('oct4fis');
    oct4fisV =parseFloat(oct4fis.value);
    nov4fis =document.getElementById('nov4fis');
    nov4fisV =parseFloat(nov4fis.value);
    dec4fis =document.getElementById('dec4fis');
    dec4fisV =parseFloat(dec4fis.value);
    total4fis =document.getElementById('total4fis');
    total4fisV =parseFloat(total4fis.value);

    totalan4fisV=jan4fisV+feb4fisV+mar4fisV+apr4fisV+may4fisV+jun4fisV+jul4fisV+aug4fisV+sep4fisV+oct4fisV+nov4fisV+dec4fisV;    
    
//        alert(totalan4V);
    if(totalan4V>total4V){
        alert('Distribution larger than total. '+totalan4V+' > '+total4V);
        return;
    }
    if(totalan4fisV>total4fisV){
        alert('Fisical distribution larger than total. '+totalan4fisV+' > '+total4fisV);
        return;
    }
        
    param='tab=9&kunci='+kunci+'&rp01='+jan4V+'&rp02='+feb4V+'&rp03='+mar4V+'&rp04='+apr4V+'&rp05='+may4V+'&rp06='+jun4V+'&rp07='+jul4V+'&rp08='+aug4V+'&rp09='+sep4V+'&rp10='+oct4V+'&rp11='+nov4V+'&rp12='+dec4V+
        '&fis01='+jan4fisV+'&fis02='+feb4fisV+'&fis03='+mar4fisV+'&fis04='+apr4fisV+'&fis05='+may4fisV+'&fis06='+jun4fisV+'&fis07='+jul4fisV+'&fis08='+aug4fisV+'&fis09='+sep4fisV+'&fis10='+oct4fisV+'&fis11='+nov4fisV+'&fis12='+dec4fisV;
    tujuan='budget_slave_budget_pks_save.php';
    post_response_text(tujuan, param, respon);
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
//                        document.getElementById('kodebudget0').value='';
//                        document.getElementById('jumlahpertahun0').value='';
                        parent.updateTab4();    
                        parent.closeDialog();
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

function jumlahkan7()
{
    totalrupiah=document.getElementById('hiddenrupiah').value;
    totaljumlah=document.getElementById('hiddenjumlah').value;
    jan4 =1*document.getElementById('jan4').value;
    feb4 =1*document.getElementById('feb4').value;
    mar4 =1*document.getElementById('mar4').value;
    apr4 =1*document.getElementById('apr4').value;
    may4 =1*document.getElementById('may4').value;
    jun4 =1*document.getElementById('jun4').value;
    jul4 =1*document.getElementById('jul4').value;
    aug4 =1*document.getElementById('aug4').value;
    sep4 =1*document.getElementById('sep4').value;
    oct4 =1*document.getElementById('oct4').value;
    nov4 =1*document.getElementById('nov4').value;
    dec4 =1*document.getElementById('dec4').value;
    totalan=jan4+feb4+mar4+apr4+may4+jun4+jul4+aug4+sep4+oct4+nov4+dec4;
//    alert (mar4+' '+totaljumlah+' '+totalan);
//    if(totalan>totaljumlah){
//        alert('Jumlah melebihi total.'); return;
//    }
    document.getElementById('jan4fis').value=(jan4*totaljumlah/totalan).toFixed(2);
    document.getElementById('feb4fis').value=(feb4*totaljumlah/totalan).toFixed(2);
    document.getElementById('mar4fis').value=(mar4*totaljumlah/totalan).toFixed(2);
    document.getElementById('apr4fis').value=(apr4*totaljumlah/totalan).toFixed(2);
    document.getElementById('may4fis').value=(may4*totaljumlah/totalan).toFixed(2);
    document.getElementById('jun4fis').value=(jun4*totaljumlah/totalan).toFixed(2);
    document.getElementById('jul4fis').value=(jul4*totaljumlah/totalan).toFixed(2);
    document.getElementById('aug4fis').value=(aug4*totaljumlah/totalan).toFixed(2);
    document.getElementById('sep4fis').value=(sep4*totaljumlah/totalan).toFixed(2);
    document.getElementById('oct4fis').value=(oct4*totaljumlah/totalan).toFixed(2);
    document.getElementById('nov4fis').value=(nov4*totaljumlah/totalan).toFixed(2);
    document.getElementById('dec4fis').value=(dec4*totaljumlah/totalan).toFixed(2);
}

function jumlahkan7a()
{
    totalrupiah=document.getElementById('hiddenrupiah').value;
    totaljumlah=document.getElementById('hiddenjumlah').value;
    jan4fis =1*document.getElementById('jan4fis').value;
    feb4fis =1*document.getElementById('feb4fis').value;
    mar4fis =1*document.getElementById('mar4fis').value;
    apr4fis =1*document.getElementById('apr4fis').value;
    may4fis =1*document.getElementById('may4fis').value;
    jun4fis =1*document.getElementById('jun4fis').value;
    jul4fis =1*document.getElementById('jul4fis').value;
    aug4fis =1*document.getElementById('aug4fis').value;
    sep4fis =1*document.getElementById('sep4fis').value;
    oct4fis =1*document.getElementById('oct4fis').value;
    nov4fis =1*document.getElementById('nov4fis').value;
    dec4fis =1*document.getElementById('dec4fis').value;
    totalan=jan4fis+feb4fis+mar4fis+apr4fis+may4fis+jun4fis+jul4fis+aug4fis+sep4fis+oct4fis+nov4fis+dec4fis;
//    alert (mar4+' '+totaljumlah+' '+totalan);
//    if(totalan>totaljumlah){
//        alert('Jumlah melebihi total.'); return;
//    } 
    document.getElementById('jan4').value=Math.floor(jan4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('feb4').value=Math.floor(feb4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('mar4').value=Math.floor(mar4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('apr4').value=Math.floor(apr4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('may4').value=Math.floor(may4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('jun4').value=Math.floor(jun4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('jul4').value=Math.floor(jul4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('aug4').value=Math.floor(aug4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('sep4').value=Math.floor(sep4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('oct4').value=Math.floor(oct4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('nov4').value=Math.floor(nov4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
    document.getElementById('dec4').value=Math.floor(dec4fis*totalrupiah*100/totalan)*0.01.toFixed(2);
}

function sapusebaran()
{
    document.getElementById('all4per').value='';
    document.getElementById('jan4per').value='';
    document.getElementById('feb4per').value='';
    document.getElementById('mar4per').value='';
    document.getElementById('apr4per').value='';
    document.getElementById('may4per').value='';
    document.getElementById('jun4per').value='';
    document.getElementById('jul4per').value='';
    document.getElementById('aug4per').value='';
    document.getElementById('sep4per').value='';
    document.getElementById('oct4per').value='';
    document.getElementById('nov4per').value='';
    document.getElementById('dec4per').value='';
    document.getElementById('jan4').value='';
    document.getElementById('feb4').value='';
    document.getElementById('mar4').value='';
    document.getElementById('apr4').value='';
    document.getElementById('may4').value='';
    document.getElementById('jun4').value='';
    document.getElementById('jul4').value='';
    document.getElementById('aug4').value='';
    document.getElementById('sep4').value='';
    document.getElementById('oct4').value='';
    document.getElementById('nov4').value='';
    document.getElementById('dec4').value='';
    document.getElementById('jan4fis').value='';
    document.getElementById('feb4fis').value='';
    document.getElementById('mar4fis').value='';
    document.getElementById('apr4fis').value='';
    document.getElementById('may4fis').value='';
    document.getElementById('jun4fis').value='';
    document.getElementById('jul4fis').value='';
    document.getElementById('aug4fis').value='';
    document.getElementById('sep4fis').value='';
    document.getElementById('oct4fis').value='';
    document.getElementById('nov4fis').value='';
    document.getElementById('dec4fis').value='';
}

function kalikan4(bulan,total)
{
    totalrupiah=document.getElementById('hiddenrupiah').value;
    totaljumlah=document.getElementById('hiddenjumlah').value;
    jan4per =1*document.getElementById('jan4per').value;
    feb4per =1*document.getElementById('feb4per').value;
    mar4per =1*document.getElementById('mar4per').value;
    apr4per =1*document.getElementById('apr4per').value;
    may4per =1*document.getElementById('may4per').value;
    jun4per =1*document.getElementById('jun4per').value;
    jul4per =1*document.getElementById('jul4per').value;
    aug4per =1*document.getElementById('aug4per').value;
    sep4per =1*document.getElementById('sep4per').value;
    oct4per =1*document.getElementById('oct4per').value;
    nov4per =1*document.getElementById('nov4per').value;
    dec4per =1*document.getElementById('dec4per').value;
    if((jan4per==0)&&(feb4per==0)&&(mar4per==0)&&(apr4per==0)&&(may4per==0)&&(jun4per==0)&&(jul4per==0)&&(aug4per==0)&&(sep4per==0)&&(oct4per==0)&&(nov4per==0)&&(dec4per==0))
    {
        return;
    }
    totalper=jan4per+feb4per+mar4per+apr4per+may4per+jun4per+jul4per+aug4per+sep4per+oct4per+nov4per+dec4per;
    if(totalper>100){
        alert('Persentase > 100%'); return;
    }
    document.getElementById('jan4').value=(jan4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('feb4').value=(feb4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('mar4').value=(mar4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('apr4').value=(apr4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('may4').value=(may4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('jun4').value=(jun4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('jul4').value=(jul4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('aug4').value=(aug4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('sep4').value=(sep4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('oct4').value=(oct4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('nov4').value=(nov4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('dec4').value=(dec4per*totalrupiah/totalper).toFixed(2);
    document.getElementById('jan4fis').value=(jan4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('feb4fis').value=(feb4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('mar4fis').value=(mar4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('apr4fis').value=(apr4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('may4fis').value=(may4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('jun4fis').value=(jun4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('jul4fis').value=(jul4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('aug4fis').value=(aug4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('sep4fis').value=(sep4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('oct4fis').value=(oct4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('nov4fis').value=(nov4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('dec4fis').value=(dec4per*totaljumlah/totalper).toFixed(2);
    document.getElementById('all4per').value=totalper;
    
}

function angka_doangsamaminus(e)//only numeric e is event
{
    key=getKey(e);
//    if((key<48 || key>57) && (key!=8 && key != 45 && key != 150 && key!=46  && key!=127 && key!=true)) // 45 hypen
    if((key<48 || key>57) && (key!=8 && key != 150 && key!=46  && key!=127 && key!=true))
        return false;
    else
    {
        return true;
    }
}