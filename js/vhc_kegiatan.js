/**
 * @author Developer
 */
function simpanKegiatan()
{
        kodekegiatan=document.getElementById('kodekegiatan').value;
        namakegiatan=document.getElementById('namakegiatan').value;
        noakun       =document.getElementById('noakun');
        noakun=noakun.options[noakun.selectedIndex].value;
        met=document.getElementById('method').value;
        if(trim(kodekegiatan)=='')
        {
                alert('Code is empty');
                document.getElementById('kodekegiatan').focus();
        }
        else
        {
                kodekegiatan=trim(kodekegiatan);
                namakegiatan=trim(namakegiatan);
                param='kodekegiatan='+kodekegiatan+'&namakegiatan='+namakegiatan+'&method='+met+'&noakun='+noakun;
                tujuan='vhc_slave_save_5jenisKegiatan.php';
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
                                                        document.getElementById('kodekegiatan').value='';
                                                        document.getElementById('namakegiatan').value='';
                                                        document.getElementById('kodekegiatan').disabled=false;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }

}

function fillField(kode,nama,noakun)
{
        document.getElementById('kodekegiatan').value=kode;
        document.getElementById('kodekegiatan').disabled=true;
        document.getElementById('namakegiatan').value=nama;
        x=document.getElementById('noakun');
        for(y=0;y<x.length;y++)
           {
               if(x.options[y].value==noakun)
                   x.options[y].selected=true;
           } 
        document.getElementById('method').value='update';
}

function cancelKegiatan()
{
    document.getElementById('kodekegiatan').disabled=false;
        document.getElementById('kodekegiatan').value='';
        document.getElementById('namakegiatan').value='';
        document.getElementById('method').value='insert';		
}
