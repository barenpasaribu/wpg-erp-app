// JavaScript Document
function saveFranco(fileTarget,passParam) {

    var passP = passParam.split('##');
    var param = "";

    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('listData').style.display='block';
                    //lockForm();
                    var res = document.getElementById('container');
                    res.innerHTML = con.responseText;	
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
    //alert(param);
    post_response_text(fileTarget+'.php', param, respon);

}
function saveFranco2(fileTarget,passParam) {

    var passP = passParam.split('##');
    var param = "";

    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('listData').style.display='block';                  
                    var res = document.getElementById('container');
                    res.innerHTML = con.responseText;	
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
    //alert(param);
    post_response_text(fileTarget+'.php', param, respon);

}
function lockForm()
{
    //$arr="##listTransaksi##pilUn_1##unitId##periodeId##method";
    document.getElementById('listTransaksi').disabled=true;
    document.getElementById('pilUn_1').disabled=true;
    document.getElementById('unitId').disabled=true;
   // document.getElementById('periodeId').disabled=true;
    document.getElementById('tmblDt').disabled=true;
}
function unlockForm()
{
    //$arr="##listTransaksi##pilUn_1##unitId##periodeId##method";
    document.getElementById('listTransaksi').disabled=false;
    document.getElementById('listReset').disabled=false;
    document.getElementById('listData').style.display='none';
    var res = document.getElementById('container');
    res.innerHTML = "";
}
function checkAll()
{
    drt=document.getElementById('allCheck');
    if(drt.checked==true)
        {
            chk=true;
        }
        else
            {
                chk=false;
            }
    var tbl = document.getElementById("dataIsi");
    var row = tbl.rows.length;
     row=row-1;
    
    for(i=1;i<=row;i++)
    {
        document.getElementById('act_'+i).checked=chk;
    }
}
function checkAll2()
{
    drt=document.getElementById('allCheck2');
    if(drt.checked==true)
        {
            chk=true;
        }
        else
            {
                chk=false;
            }
    var tbl = document.getElementById("dataIsi2");
    var row = tbl.rows.length;
    // row=row-1;
    
    for(i=1;i<=row;i++)
    {
        document.getElementById('trans_'+i).checked=chk;
    }
}
function resetDt()
{
    var tbl = document.getElementById("dataIsi");
    var row = tbl.rows.length;
//    row=row-1;
     strUrl = '';
    for(i=1;i<=row;i++)
    {
       
                try{
                    if(strUrl != '')
                    {
                            strUrl +='&notransaksi[]='+trim(document.getElementById('notransaksi_'+i).innerHTML)
                                   +'&jnsPekerjaan[]='+trim(document.getElementById('jnsPekerjaan_'+i).innerHTML)
                                   +'&alokasiBiaya[]='+trim(document.getElementById('alokasiBiya_'+i).innerHTML)
                                   +'&kmhmap[]='+trim(document.getElementById('kmhmAkhirup_'+i).innerHTML);
                    }
                    else
                    {
                           strUrl +='&notransaksi[]='+trim(document.getElementById('notransaksi_'+i).innerHTML)
                                   +'&jnsPekerjaan[]='+trim(document.getElementById('jnsPekerjaan_'+i).innerHTML)
                                   +'&alokasiBiaya[]='+trim(document.getElementById('alokasiBiya_'+i).innerHTML)
                                   +'&kmhmap[]='+trim(document.getElementById('kmhmAkhirup_'+i).innerHTML);
                    }
                }
                 catch(e){}
            
    }
    trans=document.getElementById('listTransaksi').value;
    trans2=document.getElementById('listReset').value;
    param='method=resetDt'+'&listTransaksi='+trans+'&listReset='+trans2;
    param+=strUrl;
    tujuan='tool_slave_reset';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		//unlockForm();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
    
}
function unpostingGudang(){
    var tbl = document.getElementById("dataIsi2");
    var row = tbl.rows.length;
    //row=row-1;
     strUrl = '';
    for(i=1;i<=row;i++)
    {
        ard=document.getElementById('trans_'+i);
        if(ard.checked==true)
            {
                try{
                    if(strUrl != '')
                    {
                            strUrl +='&notransaksi[]='+trim(document.getElementById('notransaks_'+i).innerHTML)
                                   +'&nojurnal[]='+trim(document.getElementById('nojurnal_'+i).innerHTML)
                                   +'&tanggal[]='+trim(document.getElementById('tgl_'+i).innerHTML);
                    }
                    else
                    {
                           strUrl +='&notransaksi[]='+trim(document.getElementById('notransaks_'+i).innerHTML)
                                   +'&nojurnal[]='+trim(document.getElementById('nojurnal_'+i).innerHTML)
                                   +'&tanggal[]='+trim(document.getElementById('tgl_'+i).innerHTML);
                    }
                }
                 catch(e){}
            }
    }
    pil=document.getElementById('pilUn_5').options[document.getElementById('pilUn_5').selectedIndex].value;
    if(pil=='')
        {
            alert("jenis tidak boleh kosong");
            return;
        }
    param='method=unpostingGudang'+'&pilUn_5='+pil;
    param+=strUrl;
    tujuan='tool_slave_admin';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
		unlockForm();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    
}
function fillField(idFr)
{
	
	param='method=getData'+'&idFranco='+idFr;
	
    

}
function cancelIsi()
{
	document.getElementById('idFranco').value='';
	document.getElementById('nmFranco').value='';
	document.getElementById('almtFranco').value='';
	document.getElementById('cntcPerson').value='';
	document.getElementById('hdnPhn').value='';
	document.getElementById('method').value="insert";
	document.getElementById('statFr').checked=false;
	document.getElementById('nmFranco').disabled=false;
}
function delData(idFr)
{
	param='method=delData'+'&idFranco='+idFr;
	tujuan='log_slave_5masterfranco';
	if(confirm("Anda yakin ingin menghapus"))
    {
		post_response_text(tujuan+'.php', param, respon);
	}
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  loadData();
					  cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getBlok(kebun)
{
    param='unitId='+kebun+'&method=getBlok';
    tujuan='tool_slave_admin';
    post_response_text(tujuan+'.php', param, respon);
function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                  document.getElementById('bloklama').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function updateBlokBaru(bloklama)
{
    document.getElementById('blokbaru').value=bloklama.substr(0, 6)+'XXXX';
     document.getElementById('blokbaru').focus();
}
function getInfo(){
    pil=document.getElementById('pilUn_1').options[document.getElementById('pilUn_1').selectedIndex].value;
    var dar
    dar="<h3>How Unposting Works</h3><ul><li>";
    dar+="Enter Transaction number to Unpost.</li><li>";
    dar+="Can be used for more than one transaction, by providing sign ',' (read 'comma').<br /> ex. 2013001/BKM/999,2013001/BKM/888,2013001/BKM/444</li><li>";
    dar+="If it turns out there are the same number of transactions, may occur in cash/bank transaction, then do sort by entering the option unit</li>";
    dar+="<li>Important Note<ol><li>";
    dar+="Make sure the transaction in the current accounting period</li>";
    dar+="<li>Select the type of transaction in accordance with</li>";
    dar+="<li>By doing unposting, means canceling journals preconceived</li></ol></li></ul>";
    dar+="<h3>How to Change Block Code</h3><ul><li>";
    dar+="Select the unit you want to change the blocks</li><li>";
    dar+="After a old block appears, enter a new block in the desired</li><li>";
    dar+="Note!! If the old block code change data in the old transaction will be transferred to the new block</li></ul>";
    dar+="<h3>Open / Close Accounting Period</h3><ul><li>";
    dar+="Choose corresponding Unit will display option period</li><li>";
 //   dar+="Pemilihan periode terbalik tidak menjadi masalah</li><li>";
    dar+="When OPEN, the period of the unit will return to the smallest period selected</li><li>";
    dar+="When CLOSE, the period corresponding unit will be the greatest period selected</li></ul>";    
//    alert(pil);
//    return;
    //$pil=array("1"=>"Kas bank","3"=>"SPK","4"=>"Perawatan Dgn Material","5"=>"Traksi");
    switch(pil)
    {
        case'1':
        dar="";
        dar="<h3>Unpost cash / bank only applies to transactions that are not connected with the Auto Jurnal on Unit</h3><ol><li>";
        dar+="Displays the  cash transaction number  with the journal reference number</li>";
        //dar+="<li>Menampilkan notransaksi dan nojurnal</li>";
        dar+="<li>Removing the journal and change the flag post in keu_kasbankht</li></ol>"
        break;
        case'3':
        dar="";
        dar="<h3>Unposting Contract Transaction</h3><ol><li>";
        dar+="Showing Journal Transaction Number from table  keu_jurnalht where cash/bank transaction number=reference number in table  keu_jurnalht</li>";
        dar+="<li>Showing Journal Transaction Number and Transaction Number</li>";
        dar+="<li>Removing the journal and change the flag post posting=0, statusjurnal=0, in table log_baspk</li></ol>"
        break;
        case'4':
        dar="";
        dar="<h3>Unposting Immature and Mature Transaction with material Usage</h3><ol><li>";
        dar+="Showing journal number from table keu_jurnalht where cash/bank Transaction number=reference number in keu_jurnalht and journal transaction number not like '%M0%'</li>";
        dar+="<li>Showing journal number and transaction number</li>";
        dar+="<li>Delete corresponding journal</li>";
        dar+="<li>Delete Good Issue from table log_transaksiht,(if exist)</li>";
        dar+="<li>Update jurnal=0 table kebun_aktifitas</li>";
        dar+="<li>Restore previous material balance on table log_5saldobulanan.</li>"; 
        dar+="<li>Update value `saldo akhir` and  `qtykeluar harga`</li>"; 
        dar+="<li>Update log_5masterbarangdt base on material balance</li></ul>"; 
        break;
        case'5':
        dar="";
        dar="<h3>Unposting Vehicle Runn Transaction</h3><ol><li>";
        dar+="Showing Transaction Detail</li>";
        dar+="<li>Change flag posting=0 on vhc_runht</li></ol>";
        break;
    }

    document.getElementById('infoTip').innerHTML=dar;
}
function getInfo2(){
    pil=document.getElementById('pilUn_5').options[document.getElementById('pilUn_5').selectedIndex].value;
    var dar
    dar="<h3>How Unposting Works</h3><ul><li>";
    dar+="Enter Transaction number to Unpost.</li><li>";
    dar+="Can be used for more than one transaction, by providing sign ',' (read 'comma').<br /> ex. 2013001/BKM/999,2013001/BKM/888,2013001/BKM/444</li><li>";
    dar+="If it turns out there are the same number of transactions, may occur in cash/bank transaction, then do sort by entering the option unit</li>";
    dar+="<li>Important Note<ol><li>";
    dar+="Make sure the transaction in the current accounting period</li>";
    dar+="<li>Select the type of transaction in accordance with</li>";
    dar+="<li>By doing unposting, means canceling journals preconceived</li></ol></li></ul>";
    dar+="<h3>How to Change Block Code</h3><ul><li>";
    dar+="Select the unit you want to change the blocks</li><li>";
    dar+="After a old block appears, enter a new block in the desired</li><li>";
    dar+="Note!! If the old block code change data in the old transaction will be transferred to the new block</li></ul>";
    dar+="<h3>Open / Close Accounting Period</h3><ul><li>";
    dar+="Choose corresponding Unit will display option period</li><li>";
 //   dar+="Pemilihan periode terbalik tidak menjadi masalah</li><li>";
    dar+="When OPEN, the period of the unit will return to the smallest period selected</li><li>";
    dar+="When CLOSE, the period corresponding unit will be the greatest period selected</li></ul>"; 
//    alert(pil);
//    return;
    //$pil=array("1"=>"Kas bank","3"=>"SPK","4"=>"Perawatan Dgn Material","5"=>"Traksi");
    switch(pil)
    {
        case'1':
        dar="";
        dar="<h3>Unposting Good Receipt</h3><ol><li>";
        dar+="Menampilkan nojurnal dari keu_jurnalht dimana notransaksi penerimaan gudang=norefrensi di keu_jurnalht</li>";
        dar+="<li>Menampilkan notransaksi dan nojurnal</li>";
        dar+="<li>Mengecek Periode Akuntansi</li>"
        dar+="<li>Update saldo akhir,nilai saldo akhir,qty masuk dan harga qty masuk pada table log_5saldobulanan.</li>"
        dar+="<li>Update saldo qty masuk pada table log_5masterbarangdt.</li>"
        dar+="<li>Update status saldo pada table log_transaksidt.</li>"
        dar+="<li>Update status jurnal dan post pada table log_transaksiht.</li>"
        dar+="<li>Menghapus jurnal pada table keu_jurnalht.</li></ol>"
        break;
        case'5':
        dar="";
        dar="<h3>Unposting Pengeluaran Gudang</h3><ol><li>";
        dar+="Menampilkan nojurnal dari keu_jurnalht dimana notransaksi penerimaan gudang=norefrensi di keu_jurnalht</li>";
        dar+="<li>Menampilkan notransaksi dan nojurnal</li>";
        dar+="<li>Mengecek Periode Akuntansi</li>"
        dar+="<li>Update saldo akhir,nilai saldo akhir,qty keluar dan harga qty keluar pada table log_5saldobulanan.</li>"
        dar+="<li>Update saldo qty masuk pada table log_5masterbarangdt.</li>"
        dar+="<li>Update status saldo pada table log_transaksidt.</li>"
        dar+="<li>Update status jurnal dan post pada table log_transaksiht.</li>"
        dar+="<li>Menghapus jurnal pada table keu_jurnalht.</li></ol>"
        break;
        case'3':
        dar="";
        dar="<h3>Unposting Penerimaan Mutasi Gudang</h3><ol><li>";
        dar+="Menampilkan nojurnal dari keu_jurnalht dimana notransaksi penerimaan gudang=norefrensi di keu_jurnalht</li>";
        dar+="<li>Menampilkan notransaksi dan nojurnal</li>";
        dar+="<li>Mengecek Periode Akuntansi</li>"
        dar+="<li>Update saldo akhir,nilai saldo akhir,qty masuk dan harga qty masuk pada table log_5saldobulanan.</li>"
        dar+="<li>Update saldo qty masuk pada table log_5masterbarangdt.</li>"
        dar+="<li>Update status saldo pada table log_transaksidt.</li>"
        dar+="<li>Update status jurnal dan post pada table log_transaksiht.</li>"
        dar+="<li>Menghapus jurnal pada table keu_jurnalht.</li></ol>"
        break;
        case'7':
         dar="";
        dar="<h3>Unposting Pengeluaran Gudang</h3><ol><li>";
        dar+="Menampilkan nojurnal dari keu_jurnalht dimana notransaksi penerimaan gudang=norefrensi di keu_jurnalht</li>";
        dar+="<li>Menampilkan notransaksi dan nojurnal</li>";
        dar+="<li>Mengecek Periode Akuntansi</li>"
        dar+="<li>Mengecek Penerimaan Mutasi sudah di Posting atau belum, jika sudah maka harus di lakukan unposting terlebih dahulu</li>"
        dar+="<li>Update saldo akhir,nilai saldo akhir,qty keluar dan harga qty keluar pada table log_5saldobulanan.</li>"
        dar+="<li>Update saldo qty masuk pada table log_5masterbarangdt.</li>"
        dar+="<li>Update status saldo pada table log_transaksidt.</li>"
        dar+="<li>Update status jurnal dan post pada table log_transaksiht.</li>"
        dar+="<li>Menghapus jurnal pada table keu_jurnalht.</li></ol>"
        break;
    }

    document.getElementById('infoTip').innerHTML=dar;
}

function getPeriode(unit)
{
    param='unit='+unit+'&method=getPeriodeOClose';
    tujuan='tool_slave_admin';
    post_response_text(tujuan+'.php', param, respon);
    function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                      document.getElementById('periodeopenclose').innerHTML=con.responseText;
                      document.getElementById('buttonDong').style.display='';
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }   
}

function prosesDong()
{
    tipe=document.getElementById('openclose');
    tipe=tipe.options[tipe.selectedIndex].value;
    unitopenclose=document.getElementById('unitopenclose');
    unitopenclose=unitopenclose.options[unitopenclose.selectedIndex].value;
    dariperiode    =document.getElementById('dariperiode');
    dariperiode    =dariperiode.options[dariperiode.selectedIndex].value;    
    sampaiperiode    =document.getElementById('sampaiperiode');
    sampaiperiode    =sampaiperiode.options[sampaiperiode.selectedIndex].value;    
    
    if(dariperiode>sampaiperiode){
        x=dariperiode;
        dariperiode=sampaiperiode;
        sampaiperiode=x;
    }
   if(unitopenclose=='')
       alert('Pilih unit!');
    else if(confirm('Anda yakin melakukan '+tipe+' untuk '+unitopenclose+'?')){
       if(confirm('Anda yakin..?')){
           param='tipe='+tipe+'&unitopenclose='+unitopenclose+'&dariperiode='+dariperiode+'&sampaiperiode='+sampaiperiode+'&method=openCloseMethod';
            tujuan='tool_slave_admin';
            post_response_text(tujuan+'.php', param, respon);           
       }
   }
function respon() {
            if (con.readyState == 4) {
                if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                    } else {
                           if(tipe=='OPEN')
                               alert('Done');
                           else
                               doClose(con.responseText,unitopenclose,0);
                    }
                } else {
                    busy_off();
                    error_catch(con.status);
                }
            }
        }        
}
function doClose(listPeriod,unit,i)
{
        arrperiode=listPeriod.split('#');
        param='kodeorg='+unit+'&periode='+arrperiode[i];
       //alert(arrperiode[i]);
       post_response_text('keu_slave_3tutupbulan.php?proses=tutupBuku', param, respon);         
        function respon() {
                    if (con.readyState == 4) {
                        if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                            } else {
                               i++;//penambahan index array 
                               if(i<arrperiode.length){
                                   if(confirm('Bulan '+arrperiode[i]+' sudah selesai tutup buku, lanjut ?')){
                                   doClose(listPeriod,unit,i);
                                   }
                                   else
                                       {
                                           logout();
                                       }
                               }
                               else
                                   {
                                       alert('Done');
                                       logout();
                                   }
                            }
                        } else {
                            busy_off();
                            error_catch(con.status);
                        }
                    }
                } 
}