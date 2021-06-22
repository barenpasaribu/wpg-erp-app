/**
 * @author antoniuslouis
 */
//function tutupProses(x)
//{
//    gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
////set value display periode
//    tglstart=document.getElementById(gudang+'_start').value;
//    tglend=document.getElementById(gudang+'_end').value;
//    tglstart=tglstart.substr(6,2)+"-"+tglstart.substr(4,2)+"-"+tglstart.substr(0,4);
//    tglend=tglend.substr(6,2)+"-"+tglend.substr(4,2)+"-"+tglend.substr(0,4);
//    document.getElementById('displayperiod').innerHTML=tglstart+" - "+tglend;
//
//    if (gudang != '') {
//            if (x == 'simpan') {
//                    document.getElementById('sloc').disabled = true;
//                    document.getElementById('btnsloc').disabled = true;
//                    tujuan = 'log_slave_tutupBukuFisik.php';
//                    param = 'gudang=' + gudang+'&awal='+document.getElementById(gudang+'_start').value+'&akhir='+document.getElementById(gudang+'_end').value;
//                    if (confirm('Jika anda ingin menutup buku click OK\nJika hanya perhitungan sementara click Cancel dan lakukan Perhitungan harga.\n '+document.getElementById('displayperiod').innerHTML+', Tutup Buku..?')) {
//                            post_response_text(tujuan, param, respog);
//                    }
//                    else
//                    {
//                            unlockScreen();
//                    }
//
//            }
//            else {
//                    document.getElementById('sloc').disabled = false;
//                    document.getElementById('sloc').options[0].selected=true;
//                    document.getElementById('btnsloc').disabled = false;
//                    //kosongkan();
//            }	
//
//    }
//            function respog(){
//                    if (con.readyState == 4) {
//                            if (con.status == 200) {
//                                    busy_off();
//                                    if (!isSaveResponse(con.responseText)) {
//                                            document.getElementById('infoDisplay').innerHTML=con.responseText;
//                                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                                    }
//                                    else {
//                                            alert('Closing of '+ gudang + ' successful, please Relogin!');
//                                            logout();
//                                    }
//                            }
//                            else {
//                                    busy_off();
//                                    error_catch(con.status);
//                            }
//                    }
//            }		
//}
function setSloc(x)
{
	gudang = document.getElementById('sloc').options[document.getElementById('sloc').selectedIndex].value;
//set value display periode
//              tglstart=document.getElementById(gudang+'_start').value;
//	tglend=document.getElementById(gudang+'_end').value;
//	tglstart=tglstart.substr(6,2)+"-"+tglstart.substr(4,2)+"-"+tglstart.substr(0,4);
//	tglend=tglend.substr(6,2)+"-"+tglend.substr(4,2)+"-"+tglend.substr(0,4);
//	document.getElementById('displayperiod').innerHTML=tglstart+" - "+tglend;

if (gudang != '') {
        if (x == 'simpan') {
                document.getElementById('sloc').disabled = true;
                document.getElementById('btnsloc').disabled = true;
                tujuan = 'log_slave_getTutupBukuFisik.php';
//                param = 'gudang=' + gudang+'&awal='+document.getElementById(gudang+'_start').value+'&akhir='+document.getElementById(gudang+'_end').value;
                param='gudang='+gudang;
                post_response_text(tujuan, param, respog);
        }
        else {
                document.getElementById('sloc').disabled = false;
                document.getElementById('sloc').options[0].selected=true;
                document.getElementById('btnsloc').disabled = false;
                    document.getElementById('infoDisplay').innerHTML='';
                //kosongkan();
        }	

}
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        document.getElementById('infoDisplay').innerHTML=con.responseText;
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('infoDisplay').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }		
}
maxf=0
sekarang=1;
function saveSaldoFisik(maxRow,btn)
{
       btn.disabled=true;
       maxf=maxRow;
       if(confirm('Tutup buku gudang, anda yakin..?'))
          loopClosingFisik(1,maxRow);
}

function lanjut()
{
    loopClosingFisik(sekarang,maxf);
    //document.getElementById('lanjut').style.display='none';
}
function loopClosingFisik(currRow,maxRow)
{
//    tglstart=document.getElementById(gudang+'_start').value;
//    tglend=document.getElementById(gudang+'_end').value;
//    periode	=trim(document.getElementById('period'+currRow).innerHTML);
//    pt		=trim(document.getElementById('pt'+currRow).innerHTML);
//    gudang	=trim(document.getElementById('gudang'+currRow).innerHTML);
//    kodebarang	=trim(document.getElementById('kodebarang'+currRow).innerHTML);
//if(pt=='' || periode=='' || gudang=='' || kodebarang=='')
//{
//        alert("Data inconsistent");
//}	
//else
//{  
//    param='pt='+pt+'&periode='+periode+'&gudang='+gudang+'&kodebarang='+kodebarang;
//        param+='&awal='+tglstart+'&akhir='+tglend;
//        tujuan = 'log_slave_saveTutupBukuFisik.php';
//        post_response_text(tujuan, param, respog);
//        document.getElementById('row'+currRow).style.backgroundColor='orange';
//        lockScreen('wait');
//}
if(document.getElementById('pilihan'+currRow).checked==false){//jika tidak dipilih maka lanjut
    currRow+=1;
    if(currRow>maxRow)
    {
       //     tutupProses('simpan');//tutup periode dan pindah periode						
       // document.getElementById('infoDisplay').innerHTML='';
    alert('Done'); 
     unlockScreen();
      logout();
    }else{    
        loopClosingFisik(currRow,maxRow);
    }
}
else{
    periode	=trim(document.getElementById('periode'+currRow).innerHTML);
    gudang	=trim(document.getElementById('kodeorg'+currRow).innerHTML);
    tanggalmulai=trim(document.getElementById('tanggalmulai'+currRow).innerHTML);
    tanggalsampai=trim(document.getElementById('tanggalsampai'+currRow).innerHTML);    

        param='periode='+periode+'&gudang='+gudang+'&tanggalmulai='+tanggalmulai+'&tanggalsampai='+tanggalsampai;
        tujuan = 'log_slave_saveTutupBukuFisik.php';
        post_response_text(tujuan, param, respog);
        document.getElementById('row'+currRow).style.backgroundColor='orange';
        lockScreen('wait');
}    
function respog(){
    if (con.readyState == 4) {
        if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                    document.getElementById('row'+currRow).style.backgroundColor='red';
                    unlockScreen();
                //    document.getElementById('lanjut').style.display='';
                }
                else {
                        document.getElementById('row'+currRow).style.display='none';
                        currRow+=1;
                        sekarang=currRow;
                        if(currRow>maxRow)
                        {
                             //tutupProses('simpan');//tutup periode dan pindah periode						
                           // document.getElementById('infoDisplay').innerHTML='';
                           alert('Done');
                          unlockScreen();
                           logout();
                        }  
                        else
                        {
                                loopClosingFisik(currRow,maxRow);
                        }
                }
        }
        else {
                busy_off();
                error_catch(con.status);
       //         document.getElementById('lanjut').style.display='';
                unlockScreen();
        }
    }
}		

}
