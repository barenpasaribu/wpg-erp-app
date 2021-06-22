/**
 * @author {anthesis-team@mitraagroservindo.com}
 * jakarta indonesia
 */

function simpan()
{
    karyawanid  	=document.getElementById('karyawanid').value;
    tahunbudget  	=document.getElementById('tahunbudget').value;
    kodetraining	=document.getElementById('kodetraining').value;
    namatraining	=document.getElementById('namatraining').value;
    levelpeserta 	=document.getElementById('levelpeserta').options[document.getElementById('levelpeserta').selectedIndex].value;
    penyelenggara 	=document.getElementById('penyelenggara').options[document.getElementById('penyelenggara').selectedIndex].value;
    hargaperpeserta	=document.getElementById('hargaperpeserta').value;	
    tanggal1            =document.getElementById('tanggal1').value;	
    tanggal2            =document.getElementById('tanggal2').value;	
    persetujuan         =document.getElementById('persetujuan').value;	
    hrd                 =document.getElementById('hrd').value;	
    deskripsitraining	=document.getElementById('deskripsitraining').value;	
    hasildiharapkan	=document.getElementById('hasildiharapkan').value;	
    if(tahunbudget=='' ||  kodetraining==''  || namatraining=='' || hargaperpeserta=='' || levelpeserta=='' || penyelenggara=='' || tanggal1=='' || tanggal2=='' || persetujuan=='' || hrd=='')
    {
        alert('Field harus diisi.'); 
    }
    else
    {
        if(document.getElementById('kodetraining').disabled){
            param='kamar=edit&tahunbudget='+tahunbudget+'&kodetraining='+kodetraining;
        }else{
            param='kamar=save&tahunbudget='+tahunbudget+'&kodetraining='+kodetraining;
        }
        param+='&karyawanid='+karyawanid+'&namatraining='+namatraining+'&levelpeserta='+levelpeserta;
        param+='&penyelenggara='+penyelenggara+'&hargaperpeserta='+hargaperpeserta;
        param+='&tanggal1='+tanggal1+'&tanggal2='+tanggal2;
        param+='&persetujuan='+persetujuan+'&hrd='+hrd;
        param+='&deskripsitraining='+deskripsitraining+'&hasildiharapkan='+hasildiharapkan;
        tujuan='sdm_slave_5rencanatraining.php';
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
                }else {
            //							document.getElementById('container').innerHTML=con.responseText;
                    bersihkanForm();
                    document.getElementById('kodetraining').disabled=false;
//                    updateTahun();
                    alert('Done.');
                    displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 		
}

//function updateTahun()
//{
//    listtahunz = document.getElementById('pilihantahun').value;
//    param='kamar=tahun';
//    tujuan='sdm_slave_5rencanatraining.php';
//    post_response_text(tujuan, param, respog);
//    function respog()
//    {
//        if(con.readyState==4)
//        {
//            if (con.status == 200) {
//                busy_off();
//                if (!isSaveResponse(con.responseText)) {
//                    alert('ERROR TRANSACTION,\n' + con.responseText);
//                } else {
//                    document.getElementById('listtahun').innerHTML=con.responseText;
//                               //bersihkanForm();
//                               //alert('Done.');
//                }
//            } else {
//                busy_off();
//                error_catch(con.status);
//            }
//        }	
//    }   
//    document.getElementById('listtahun').value=listtahunz;     
//}

function displayList()
{
//    listtahun 	=document.getElementById('listtahun').options[document.getElementById('listtahun').selectedIndex].value;
//    document.getElementById('pilihantahun').value=listtahun;
    karyawanid  	=document.getElementById('karyawanid').value;
    param='kamar=list&karyawanid='+karyawanid;
    tujuan='sdm_slave_5rencanatraining.php';
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
                    document.getElementById('container').innerHTML=con.responseText;
                   //bersihkanForm();
                   //alert('Done.');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }     
}

function bersihkanForm()
{
    document.getElementById('tahunbudget').value='';
    document.getElementById('kodetraining').value='';

    document.getElementById('namatraining').value='';
    document.getElementById('levelpeserta').value='';
    document.getElementById('penyelenggara').value='';
    document.getElementById('hargaperpeserta').value='';

    document.getElementById('tanggal1').value='';
    document.getElementById('tanggal2').value='';

    document.getElementById('persetujuan').value='';
    document.getElementById('hrd').value='';

    document.getElementById('deskripsitraining').value='';
    document.getElementById('hasildiharapkan').value='';
}

function batal()
{
//    document.getElementById('tahunbudget').value='';
    bersihkanForm();
    document.getElementById('kodetraining').disabled=false;
}

function deletetraining(kode)
{
    karyawanid  	=document.getElementById('karyawanid').value;
    param='kamar=delete&kodetraining='+kode+'&karyawanid='+karyawanid;
    if (confirm('Delete ..?')) {
        tujuan='sdm_slave_5rencanatraining.php';
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
//							document.getElementById('container').innerHTML=con.responseText;
                    bersihkanForm();
//                    updateTahun();
                    alert('Done.');
                    displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }     
}

function desctraining(kode,ev)
{
    param='kamar=desc&kodetraining='+kode;
    tujuan = 'sdm_slave_5rencanatraining.php?'+param;	
    //display window
    title='Desc '+kode;
    width='400';
    height='200';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1(title,content,width,height,ev);
}

function edittraining(tahunbudget,kodetraining,namatraining,levelpeserta,penyelenggara,hargaperpeserta,tanggal1,tanggal2,persetujuan,hrd,deskripsitraining,hasildiharapkan)
{
    document.getElementById('tahunbudget').value=tahunbudget;
    document.getElementById('kodetraining').value=kodetraining;
    document.getElementById('namatraining').value=namatraining;
    
    v=document.getElementById('levelpeserta');    
    for(a=0;a<v.length;a++)
    {
        if(v.options[a].value==levelpeserta)
        {
            v.options[a].selected=true;
        }
    }
    g=document.getElementById('penyelenggara');    
    for(a=0;a<g.length;a++)
    {
        if(g.options[a].value==penyelenggara)
        {
            g.options[a].selected=true;
        }
    }
    
    document.getElementById('hargaperpeserta').value=hargaperpeserta;

    document.getElementById('tanggal1').value=tanggal1;
    document.getElementById('tanggal2').value=tanggal2;
    
    l=document.getElementById('persetujuan');    
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==persetujuan)
        {
            l.options[a].selected=true;
        }
    }
    h=document.getElementById('hrd');    
    for(a=0;a<h.length;a++)
    {
        if(h.options[a].value==hrd)
        {
            h.options[a].selected=true;
        }
    }
 
    document.getElementById('deskripsitraining').value=deskripsitraining;
    document.getElementById('hasildiharapkan').value=hasildiharapkan;

    document.getElementById('kodetraining').disabled=true;

}

function lihatpdf(ev,tujuan,kode)
{
    karyawanid  	=document.getElementById('karyawanid').value;
    judul='Report PDF';	
    param='karyawanid='+karyawanid+'&kamar=pdf'+'&kodetraining='+kode;
//    if(trim(jabatan2)=='')
//    {
//        alert('Please choose...');
//        document.getElementById('jabatan2').focus();
//    }
//    else{
        printFile(param,tujuan,judul,ev)	        
//    }
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
