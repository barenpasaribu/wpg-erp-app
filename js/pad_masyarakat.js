/**
 * @author antoniuslouis
 */

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='400';
   height='200';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function desaexcel(ev,tujuan)
{
    unitbawah=document.getElementById('unitbawah');
    unitbawah=unitbawah.options[unitbawah.selectedIndex].value;
    
    method='excel';

    param='unitbawah='+unitbawah+'&method='+method;

    judul='Report Ms.Excel';	
    printFile(param,tujuan,judul,ev)	
}

function gantikebun()
{
    unitbawah=document.getElementById('unitbawah');
    unitbawah=unitbawah.options[unitbawah.selectedIndex].value;
    param='unitbawah='+unitbawah+'&method=gantikebun';
    tujuan='pad_slave_save_masyarakat.php';
    post_response_text(tujuan, param, respog);		
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) 
            {
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

function simpanJabatan()
{
    unitbawah=document.getElementById('unitbawah');
    unitbawah=unitbawah.options[unitbawah.selectedIndex].value;
pid=document.getElementById('mid').value;
nama=document.getElementById('nama').value
alamat=document.getElementById('alamat').value
desa=document.getElementById('desa');
desa=desa.options[desa.selectedIndex].value;
kecamatan=document.getElementById('kecamatan');
kecamatan=kecamatan.options[kecamatan.selectedIndex].value;
kabupaten=document.getElementById('kabupaten');
kabupaten=kabupaten.options[kabupaten.selectedIndex].value;
ktp=document.getElementById('ktp').value;
hp=document.getElementById('hp').value;    
met=document.getElementById('method').value;
if(trim(nama)=='' || alamat=='' || desa=='')
{
        alert('Nama,Alamat,Desa are oblogatory');
        document.getElementById('nama').focus();
}
else
{
        param='pid='+pid+'&nama='+nama+'&method='+met;
        param+='&alamat='+alamat+'&kecamatan='+kecamatan+'&desa='+desa;
        param+='&kabupaten='+kabupaten+'&ktp='+ktp+'&hp='+hp+'&unitbawah='+unitbawah;       
        tujuan='pad_slave_save_masyarakat.php';
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
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
 }

}

function fillField(pid,nama,alamat,desa,kecamatan,kabupaten,ktp,hp)
{
    document.getElementById('mid').value=pid;
     document.getElementById('nama').value=nama;  
     document.getElementById('alamat').value=alamat;       
     x=document.getElementById('desa');
    for(y=0;y<x.length;y++){
        if(x.options[y].value==desa){
            x.options[y].selected=true;
        }
    }
     x=document.getElementById('kabupaten');
    for(y=0;y<x.length;y++){
        if(x.options[y].value==kabupaten){
            x.options[y].selected=true;
        }
    }    
     x=document.getElementById('kecamatan');
    for(y=0;y<x.length;y++){
        if(x.options[y].value==kecamatan){
            x.options[y].selected=true;
        }
    }   
   document.getElementById('ktp').value=ktp;
    document.getElementById('hp').value=hp;    
    document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('mid').value='';
    document.getElementById('nama').value='';
    document.getElementById('alamat').value='';
    document.getElementById('desa').value='';
    document.getElementById('kecamatan').value=''; 
    document.getElementById('kabupaten').value='';     
    document.getElementById('ktp').value='';     
     document.getElementById('hp').value='';    
    document.getElementById('method').value='insert';		
}
