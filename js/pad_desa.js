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
    tujuan='pad_slave_save_desa.php';
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
unit=document.getElementById('unit');
unit=unit.options[unit.selectedIndex].value;
namadesa=document.getElementById('desa').value;
kecamatan=document.getElementById('kecamatan').value;
kabupaten=document.getElementById('kabupaten').value;    
met=document.getElementById('method').value;
if(trim(namadesa)=='')
{
        alert('Desa is empty');
        document.getElementById('desa').focus();
}
else
{
        param='unit='+unit+'&desa='+namadesa+'&method='+met;
        param+='&kecamatan='+kecamatan+'&kabupaten='+kabupaten+'&unitbawah='+unitbawah;
        tujuan='pad_slave_save_desa.php';
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

function fillField(kode,nama,kecamatan,kabupaten)
{
    document.getElementById('desa').value=nama;
    document.getElementById('desa').disabled=true;
     x=document.getElementById('unit');
    for(y=0;y<x.length;y++){
        if(x.options[y].value==kode){
            x.options[y].selected=true;
        }
    }
    
    document.getElementById('kecamatan').value=kecamatan;
    document.getElementById('kabupaten').value=kabupaten;    
    document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('desa').disabled=false;
    document.getElementById('kecamatan').value='';
    document.getElementById('kabupaten').value='';
    document.getElementById('method').value='insert';		
}
