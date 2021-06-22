// JavaScript Documentz

function tampilpeta(kodeorg)
{
    param='unit='+kodeorg+'&proses=pdf';
    tujuan='lbm_slave_peta_kebun.php';
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
                    document.body.innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}

function gantul(kodeorg) 
{
//    document.getElementById('legend').innerHTML=kodeorg;
    param='unit='+kodeorg+'&proses=legend';
    tujuan='lbm_slave_peta_kebun.php';
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
                    document.getElementById('legend').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}






//function batal()
//{
//    document.getElementById('unit').value='';
//    document.getElementById('periode').value='';
//}
//
//function saveHeader()
//{
//         kaliKan(); 
//    tahunbudget=getValue('tahunbudget');
//   // thnBdget=document.getElementById('thnBudgetKap').value;
//     kodeorg=getValue('kodeorg');
//   //kodeOrg=document.getElementById('kodeOrg').options[document.getElementById('kodeOrg').selectedIndex].value;
//   jeniskapital=getValue('jeniskapital');
//   //jns=document.getElementById('jnsKapital').options[document.getElementById('jnsKapital').selectedIndex].value;
//    keterangan=getValue('keterangan');
//    //ket=document.getElementById('ket').value;
//    jumlah=getValue('jumlah');
//    //jmlhKap=document.getElementById('jmlhKap').value;
//    harga=getValue('harga');
//    //hrgSatuan=document.getElementById('hrgSatuanKap').value;
//    total=getValue('totalrp');
//    lokasi=getValue('lokasi');
//    //totKap=document.getElementById('totKap').value;  
//    param='proses=simpanHeader';
//    param+='&tahunbudget='+tahunbudget+'&kodeorg='+kodeorg+'&jeniskapital='+jeniskapital+'&keterangan='+keterangan;
//    param+='&jumlah='+jumlah+'&harga='+harga+'&total='+total+'&lokasi='+lokasi;
//
//	tujuan='bgt_slave_kapital.php';
//     if(tahunbudget=='' || kodeorg=='' || jeniskapital=='' || keterangan=='' || jumlah=='' || jumlah==0 || harga==0 || harga=='' || total==0)
//        alert('Data harus lengkap');
//     else
//        post_response_text(tujuan, param, respog);
//    function respog()
//    {
//      if(con.readyState==4)
//      {
//            if (con.status == 200) {
//                    busy_off();
//                    if (!isSaveResponse(con.responseText)) {
//                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                    }
//                    else {
//                            document.getElementById('container1').innerHTML=con.responseText;
//                            document.getElementById('keterangan').value='';
//                            document.getElementById('jumlah').value=''
//                            document.getElementById('harga').value=''
//                      }
//                    }
//            else {
//                    busy_off();
//                    error_catch(con.status);
//            }
//      }	
//    }  
//      
//}
//
//function deleteData(kunci)
//{
//    param='proses=delete&kunci='+kunci;
//    	tujuan='bgt_slave_kapital.php';
//    if(confirm('Anda yakin menghapus..?'))
//        {
//          post_response_text(tujuan, param, respog);           
//        }
//
//    function respog()
//    {
//      if(con.readyState==4)
//      {
//            if (con.status == 200) {
//                    busy_off();
//                    if (!isSaveResponse(con.responseText)) {
//                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                    }
//                    else {
//                          document.getElementById('container1').innerHTML=con.responseText;
//                      }
//                    }
//            else {
//                    busy_off();
//                    error_catch(con.status);
//            }
//      }	
//    }       
//}
//
//function kaliKan()
//{
//        harga=getValue('harga');
//        jumlah=getValue('jumlah');
//        document.getElementById('totalrp').value=(harga*jumlah);
//}
//
//function sebaran(kunci,ev)
//{
//    param='proses=sebaran&kunci='+kunci;
//   tujuan='bgt_slave_kapital.php';
//          post_response_text(tujuan, param, respog);           
//
//    function respog()
//    {
//      if(con.readyState==4)
//      {
//            if (con.status == 200) {
//                    busy_off();
//                    if (!isSaveResponse(con.responseText)) {
//                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                    }
//                    else {
//                          tabAction(document.getElementById('tabFRM1'),1,'FRM',2);
//                          document.getElementById('detailDataSebaran').innerHTML=con.responseText;
//                      }
//                    }
//            else {
//                    busy_off();
//                    error_catch(con.status);
//            }
//      }	
//    }     
//}
//
//function clearForm()
//{ 
//    if(confirm("Anda yakin ingin mengkosongkan form??"))
//    {
//     for(sr=1;sr<13;sr++)
//     {
//         document.getElementById('k'+sr).value='';
//         document.getElementById('persen'+sr).value='';
//     }
//    }
//    else
//        {
//            return;
//        }
//}
//function simpanSebaran(total,kunci)
//{
//  param='proses=updatesebaran&kunci='+kunci+'&total='+total;
//  k01=document.getElementById('k1').value;
//  k02=document.getElementById('k2').value;
//  k03=document.getElementById('k3').value;
//  k04=document.getElementById('k4').value;
//  k05=document.getElementById('k5').value;
//  k06=document.getElementById('k6').value;
//  k07=document.getElementById('k7').value;
//  k08=document.getElementById('k8').value;
//  k09=document.getElementById('k9').value;
//  k10=document.getElementById('k10').value;
//  k11=document.getElementById('k11').value;
//  k12=document.getElementById('k12').value;
//  param+='&k01='+k01;
//  param+='&k02='+k02;
//  param+='&k03='+k03;
//  param+='&k04='+k04;
//  param+='&k05='+k05;
//  param+='&k06='+k06;
//  param+='&k07='+k07;
//  param+='&k08='+k08;
//  param+='&k09='+k09;
//  param+='&k10='+k10;
//  param+='&k11='+k11;
//  param+='&k12='+k12;
//  param+='&total='+total;
//   tujuan='bgt_slave_kapital.php';
//   post_response_text(tujuan, param, respog);           
//
//    function respog()
//    {
//      if(con.readyState==4)
//      {
//            if (con.status == 200) {
//                    busy_off();
//                    if (!isSaveResponse(con.responseText)) {
//                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                    }
//                    else {
//                          tabAction(document.getElementById('tabFRM0'),0,'FRM',1);
//                         
//                      }
//                    }
//            else {
//                    busy_off();
//                    error_catch(con.status);
//            }
//      }	
//    }    
//}
//
//function closeBudget()
//{
//    thn=getValue('thnBudgetTutup');
//    param='proses=tutup&tahun='+thn;
//    if(confirm('Anda yakin mau tutup budget '+thn+'..?\nSetelah tutup data tidak dapat diubah'));
//    {
//        tujuan='bgt_slave_kapital.php';
//       post_response_text(tujuan, param, respog);   
//    }
//function respog()
//    {
//      if(con.readyState==4)
//      {
//            if (con.status == 200) {
//                    busy_off();
//                    if (!isSaveResponse(con.responseText)) {
//                            alert('ERROR TRANSACTION,\n' + con.responseText);
//                    }
//                    else {
//                          window.location.reload();
//                         
//                      }
//                    }
//            else {
//                    busy_off();
//                    error_catch(con.status);
//            }
//      }	
//    }    
//}
//
//function ubahNilai(total)
//{
// tot=0;
// for(x=1;x<13;x++)
//     {
//          if(document.getElementById('persen'+x).value=='')
//             document.getElementById('persen'+x).value=0; 
//        tot+=parseFloat(document.getElementById('persen'+x).value);
//     }
//   if(tot>0){    
//   for(x=1;x<13;x++)
//     {
//         document.getElementById('k'+x).value=0;
//     }
//    }
// for(x=1;x<13;x++)
//     {
//         if(document.getElementById('persen'+x).value!='' || document.getElementById('persen'+x).value!=0)
//            {
//               z=parseFloat(document.getElementById('persen'+x).value);
//               if(tot>0)
//               document.getElementById('k'+x).value=((z/tot)*total).toFixed(2);
//            }
//     }  
//}