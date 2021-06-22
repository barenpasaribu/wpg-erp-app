/**
 * @author antoniuslouis
 */

function simpanheader()
{
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    item=document.getElementById('item').value;
    met=document.getElementById('method').value;
    if((trim(jabatan)=='')||(trim(jenis)=='')||(trim(item)==''))
    {
        alert('Please fill all fields.');
    }
    else
    {
        param='jabatan='+jabatan+'&jenis='+jenis+'&method='+met+'&item='+item;
        tujuan='sdm_slave_5matrixKompetensi.php';
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
                    kunciheader();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function simpandetail()
{
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    item=document.getElementById('item').value;
    nourut=document.getElementById('nourut').options[document.getElementById('nourut').selectedIndex].value;
    kompetensi=document.getElementById('kompetensi').value;
    perilaku=document.getElementById('perilaku').value;
    met=document.getElementById('method').value;
    item2=document.getElementById('item2').value;
    if((trim(jabatan)=='')||(trim(jenis)=='')||(trim(item)=='')||(trim(nourut)=='')||(trim(kompetensi)=='')||(trim(perilaku)==''))
    {
        alert('Please fill all fields.');
    }
    else
    {
        param='jabatan='+jabatan+'&jenis='+jenis+'&method='+met+'&item='+item+'&nourut='+nourut+'&kompetensi='+kompetensi+'&perilaku='+perilaku+'&item2='+item2;
        tujuan='sdm_slave_5matrixKompetensi.php';
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
                    kunciheader();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function kunciheader()
{
    document.getElementById('jabatan').disabled=true;
    document.getElementById('jenis').disabled=true;
    document.getElementById('item').disabled=true;
    document.getElementById('tombolsimpanheader').disabled=true;
    document.getElementById('tombolcancelheader').disabled=true;
    document.getElementById('method').value='insertdetail';		
    document.getElementById('detail').style.display='block';
    
    item2=document.getElementById('item').options[document.getElementById('item').selectedIndex].value;
    l=document.getElementById('item2');    
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==item2)
        {
            l.options[a].selected=true;
        }
    }    
    
}

function cancelheader()
{
    document.getElementById('jabatan').disabled=false;
    document.getElementById('jenis').disabled=false;
    document.getElementById('item').disabled=false;
    document.getElementById('tombolsimpanheader').disabled=false;
    document.getElementById('tombolcancelheader').disabled=false;
    document.getElementById('jabatan').value='';
    document.getElementById('jenis').value='';
    document.getElementById('item').value='';
    document.getElementById('method').value='insert';		
}

function canceldetail()
{
    document.getElementById('jabatan').disabled=false;
    document.getElementById('jenis').disabled=false;
    document.getElementById('item').disabled=false;
    document.getElementById('tombolsimpanheader').disabled=false;
    document.getElementById('tombolcancelheader').disabled=false;
    document.getElementById('jabatan').value='';
    document.getElementById('jenis').value='';
    document.getElementById('item').value='';
    document.getElementById('method').value='insert';		
    
    document.getElementById('nourut').value='';
    document.getElementById('kompetensi').value='';
    document.getElementById('perilaku').value='';
    
    document.getElementById('detail').style.display='none';

    param='jabatan='+jabatan+'&jenis='+jenis+'&item='+item;
    tujuan='sdm_slave_5matrixKompetensi.php';
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

function pilihitem(){        
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    item2=document.getElementById('item2').options[document.getElementById('item2').selectedIndex].value;
    item=document.getElementById('item2').options[document.getElementById('item2').selectedIndex].value;
    l=document.getElementById('item');    
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==item)
        {
            l.options[a].selected=true;
        }
    }    
    param='item='+item+'&method=pilih'+'&jabatan='+jabatan+'&jenis='+jenis+'&item2='+item2;
    tujuan='sdm_slave_5matrixKompetensi.php';
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
                    item2=document.getElementById('item').options[document.getElementById('item').selectedIndex].value;
                    l=document.getElementById('item2');    
                    for(a=0;a<l.length;a++)
                    {
                        if(l.options[a].value==item2)
                        {
                            l.options[a].selected=true;
                        }
                    }    
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }
}

function fillField(jabatan,jenis,item,nourut,kompetensi,perilaku)
{
    l=document.getElementById('jabatan');
    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==jabatan)
        {
            l.options[a].selected=true;
        }
    }
    k=document.getElementById('jenis');    
    for(a=0;a<k.length;a++)
    {
        if(k.options[a].value==jenis)
        {
            k.options[a].selected=true;
        }
    }
    i=document.getElementById('item');    
    for(a=0;a<i.length;a++)
    {
        if(i.options[a].value==item)
        {
            i.options[a].selected=true;
        }
    }
    n=document.getElementById('nourut');    
    for(a=0;a<n.length;a++)
    {
        if(n.options[a].value==nourut)
        {
            n.options[a].selected=true;
        }
    }
    
    kunciheader();
    pilihitem();

    document.getElementById('kompetensi').value=kompetensi;
    document.getElementById('perilaku').value=perilaku;
    document.getElementById('method').value='updatedetail';
}

function hapus(jabatan,jenis,item,nourut)
{
    item2=document.getElementById('item2').options[document.getElementById('item2').selectedIndex].value;
    if(confirm('Delete ?')){
        met='delete';
        param='jabatan='+jabatan+'&jenis='+jenis+'&method='+met+'&item2='+item2+'&item='+item+'&nourut='+nourut;
        tujuan='sdm_slave_5matrixKompetensi.php';
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
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }    
}

function lihatpdf(ev,tujuan)
{
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    item=document.getElementById('item').options[document.getElementById('item').selectedIndex].value;
    item2=document.getElementById('item2').options[document.getElementById('item2').selectedIndex].value;
    judul='Report PDF';	
    param='item2='+item2+'&method=pdf'+'&jabatan='+jabatan+'&jenis='+jenis+'&item='+item;
    if(trim(item2)=='')
    {
        alert('Please choose...');
        document.getElementById('item2').focus();
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

function cariBast(num)
{ 
    jabatan=document.getElementById('jabatan').options[document.getElementById('jabatan').selectedIndex].value;
    jenis=document.getElementById('jenis').options[document.getElementById('jenis').selectedIndex].value;
    item=document.getElementById('item').options[document.getElementById('item').selectedIndex].value;
    item2=document.getElementById('item2').options[document.getElementById('item2').selectedIndex].value;
    param='jabatan='+jabatan+'&jenis='+jenis+'&item='+item+'&page='+num+'&method=dummy';
    tujuan = 'sdm_slave_5matrixKompetensi.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container').innerHTML=con.responseText;
                    l=document.getElementById('item2');    
                    for(a=0;a<l.length;a++)
                    {
                        if(l.options[a].value==item2)
                        {
                            l.options[a].selected=true;
                        }
                    }    
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
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

