/**
 * @author antoniuslouis
 */

function hapus(jabatan,kriteria)
{
    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
    if(confirm('Delete ?')){
        met='delete';
        param='jabatan='+jabatan+'&kriteria='+kriteria+'&method='+met+'&jabatan2='+jabatan2;
        tujuan='sdm_slave_5kriteriaPsy.php';
        post_response_text(tujuan, param, respog);		
    }else{
        
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
                    cancel();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
    
}

function pilihjabatan(){        
    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
    param='jabatan2='+jabatan2+'&method=pilih';
    tujuan='sdm_slave_5kriteriaPsy.php';
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
                    cancel();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }

}

function simpan()
{
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    kriteria=document.getElementById('kriteria').options[document.getElementById('kriteria').selectedIndex].value;
    deskripsi=document.getElementById('deskripsi').value;
    met=document.getElementById('method').value;
    jabatan2=jabatan;
    if((trim(jabatan)=='')||(trim(kriteria)=='')||(trim(deskripsi)==''))
    {
        alert('Please fill all fields.');
    }
    else
    {
        param='jabatan='+jabatan+'&kriteria='+kriteria+'&method='+met+'&jabatan2='+jabatan2;
        param+='&deskripsi='+deskripsi;
        tujuan='sdm_slave_5kriteriaPsy.php';
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
                    cancel();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function fillField(jabatan,kriteria,deskripsi)
{
    l=document.getElementById('jabatan');
    
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==jabatan)
        {
            l.options[a].selected=true;
        }
    }

    k=document.getElementById('kriteria');
    
    for(a=0;a<k.length;a++)
    {
        if(k.options[a].value==kriteria)
        {
            k.options[a].selected=true;
        }
    }

    document.getElementById('jabatan').disabled=true;
    document.getElementById('kriteria').disabled=true;
    document.getElementById('deskripsi').value=deskripsi;
    document.getElementById('method').value='update';
}

function cancel()
{
    document.getElementById('jabatan').disabled=false;
    document.getElementById('kriteria').disabled=false;
    document.getElementById('jabatan').value='';
    document.getElementById('kriteria').value='';
    document.getElementById('deskripsi').value='';
    document.getElementById('method').value='insert';		
    
    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
    l=document.getElementById('jabatan');    
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==jabatan2)
        {
            l.options[a].selected=true;
        }
    }
    
}

function lihat(jabatan,kriteria,ev)
{
   param='jabatan='+jabatan+'&kriteria='+kriteria+'&method=lihat';
   tujuan='sdm_slave_5kriteriaPsy.php'+"?"+param;  
   width='600';
   height='250';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Deskripsi '+kriteria,content,width,height,ev); 	
}

function lihatpdf(ev,tujuan)
{
    jabatan2=document.getElementById('jabatan2').options[document.getElementById('jabatan2').selectedIndex].value;
    judul='Report PDF';	
    param='jabatan2='+jabatan2+'&method=pdf';
    if(trim(jabatan2)=='')
    {
        alert('Please choose...');
        document.getElementById('jabatan2').focus();
    }
    else{
        printFile(param,tujuan,judul,ev)	        
    }
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}