/**
 * @author antoniuslouis
 */

function pilihkaryawan()
{
    matrixid=document.getElementById('matrixid').options[document.getElementById('matrixid').selectedIndex].value;
        met='pilihkaryawan';
        param='matrixid='+matrixid+'&method='+met;
        tujuan='sdm_slave_matrixTraining.php';
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

function rilotlis()
{
        met='rilotlis';
        param='&method='+met;
        tujuan='sdm_slave_matrixTraining.php';
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
                    document.getElementById('icontainer').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
    
}

function klikbok(karyawanid)
{
    dicentang=document.getElementById('cek'+karyawanid).checked;
    catatan=document.getElementById('remark'+karyawanid).value;
    matrixid=document.getElementById('matrixid').options[document.getElementById('matrixid').selectedIndex].value;
    tanggal1=document.getElementById('tanggal1').value;
    tanggal2=document.getElementById('tanggal2').value;
    
    salahtanggal=false;
    
    if(dicentang==true){
        method='centang';
        if((tanggal1=='')||(tanggal2==''))salahtanggal=true;
    }else{
        method='uncentang';
    }
    
        
        param='matrixid='+matrixid+'&method='+method+'&karyawanid='+karyawanid+'&tanggal1='+tanggal1+'&tanggal2='+tanggal2+'&catatan='+catatan;
        tujuan='sdm_slave_matrixTraining.php';
        	
    if(salahtanggal==false)post_response_text(tujuan, param, respog);	
    else {
        alert('Silakan isi tanggal.');
        pilihkaryawan();
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
                    alert('Done.');
                    pilihkaryawan();
                    rilotlis();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
    
}
 
function lihatpdf(ev,tujuan,karyawanid)
{   
//    jabatan =document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    judul='Report PDF';	
    param='karyawanid='+karyawanid+'&method=pdf';
    printFile(param,tujuan,judul,ev)	        
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}







//function hapus(matrixid)
//{
//    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
//    if(confirm('Delete ?')){
//        met='delete';
//        param='matrixid='+matrixid+'&method='+met+'&jabatan2='+jabatan2;
//        tujuan='sdm_slave_5matrixTraining.php';
//        post_response_text(tujuan, param, respog);		
//    }else{
//        
//    }
//    
//    function respog()
//    {
//        if(con.readyState==4)
//        {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                }
//                else {
//                    //alert(con.responseText);
//                    document.getElementById('container').innerHTML=con.responseText;
//                    cancel();
//                }
//            }
//            else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }	
//    }
//    
//}
//
//function pilihjabatan(){        
//    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
//    param='jabatan2='+jabatan2+'&method=pilih';
//    tujuan='sdm_slave_5matrixTraining.php';
//    post_response_text(tujuan, param, respog);	
//        
//    function respog()
//    {
//        if(con.readyState==4)
//        {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                }
//                else {
//                    //alert(con.responseText);
//                    document.getElementById('container').innerHTML=con.responseText;
//                    cancel();
//                }
//            }
//            else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }	
//    }
//
//}
//
//function simpan()
//{
//    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
//    kategori=document.getElementById('kategori').options[document.getElementById('kategori').selectedIndex].value;
//    topik=document.getElementById('topik').value;
//    remark=document.getElementById('remark').value;
//    matrixid=document.getElementById('matrixid').value;
//    met=document.getElementById('method').value;
//    jabatan2=jabatan;
//    if((trim(jabatan)=='')||(trim(kategori)=='')||(trim(topik)=='')||(trim(remark)==''))
//    {
//        alert('Please fill all fields.');
//    }
//    else
//    {
//        param='jabatan='+jabatan+'&kategori='+kategori+'&method='+met+'&topik='+topik+'&remark='+remark+'&matrixid='+matrixid;;
//        tujuan='sdm_slave_5matrixTraining.php';
//        post_response_text(tujuan, param, respog);		
//    }
//
//    function respog()
//    {
//        if(con.readyState==4)
//        {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                }
//                else {
//                    //alert(con.responseText);
//                    document.getElementById('container').innerHTML=con.responseText;
//                    cancel();
//                }
//            }
//            else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }	
//    }
//}
//
//function fillField(jabatan,kategori,topik,remark,matrixid)
//{
//    l=document.getElementById('jabatan');
//    
//    for(a=0;a<l.length;a++)
//    {
//        if(l.options[a].value==jabatan)
//        {
//            l.options[a].selected=true;
//        }
//    }
//
//    k=document.getElementById('kategori');
//    
//    for(a=0;a<k.length;a++)
//    {
//        if(k.options[a].value==kategori)
//        {
//            k.options[a].selected=true;
//        }
//    }
//
//    document.getElementById('jabatan').disabled=true;
//    document.getElementById('kategori').disabled=true;
//    document.getElementById('topik').value=topik;
//    document.getElementById('remark').value=remark;
//    document.getElementById('method').value='update';
//    
//    document.getElementById('matrixid').value=matrixid;    
//}
//
//function cancel()
//{
//    document.getElementById('jabatan').disabled=false;
//    document.getElementById('kategori').disabled=false;
//    document.getElementById('jabatan').value='';
//    document.getElementById('kategori').value='';
//    document.getElementById('topik').value='';
//    document.getElementById('remark').value='';
//    document.getElementById('method').value='insert';		
//    document.getElementById('matrixid').value='';
//    
//    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
//    l=document.getElementById('jabatan');    
//    for(a=0;a<l.length;a++)
//    {
//        if(l.options[a].value==jabatan2)
//        {
//            l.options[a].selected=true;
//        }
//    }
//    
//}

//function lihat(jabatan,kriteria,ev)
//{
//   param='jabatan='+jabatan+'&kriteria='+kriteria+'&method=lihat';
//   tujuan='sdm_slave_5kriteriaPsy.php'+"?"+param;  
//   width='600';
//   height='250';
//  
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1('Deskripsi '+kriteria,content,width,height,ev); 	
//}
//
//function lihatpdf(ev,tujuan)
//{
//    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
//    judul='Report PDF';	
//    param='jabatan2='+jabatan2+'&method=pdf';
//    if(trim(jabatan2)=='')
//    {
//        alert('Please choose...');
//        document.getElementById('jabatan2').focus();
//    }
//    else{
//        printFile(param,tujuan,judul,ev)	        
//    }
//}
//
//function printFile(param,tujuan,title,ev)
//{
//   tujuan=tujuan+"?"+param;  
//   width='600';
//   height='250';
//   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   showDialog1(title,content,width,height,ev); 	
//}