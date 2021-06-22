
function simpanJabatan()
{
        kodejabatan=document.getElementById('kode').value;
        namajabatan=document.getElementById('nama').value;
        namajabatan1=document.getElementById('nama1').value;
        satuan=document.getElementById('satuan').value;
        met=document.getElementById('method').value;
        if(trim(kodejabatan)=='')
        {
                alert('Code is empty');
                document.getElementById('kode').focus();
        }
        else
        {
                kodejabatan=trim(kodejabatan);
                namajabatan=trim(namajabatan);
                param='kode='+kodejabatan+'&nama='+namajabatan+'&method='+met+'&satuan='+satuan+'&nama1='+namajabatan1;
                tujuan='pabrik_slave_save_5fraksi.php';
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

function fillField(kode,nama,satuan,nama1)
{
        document.getElementById('kode').value=kode;
    document.getElementById('kode').disabled=true;
        document.getElementById('nama').value=nama;
        document.getElementById('nama1').value=nama1;
        document.getElementById('satuan').value=satuan;
        document.getElementById('method').value='update';
}

function cancelJabatan()
{
    document.getElementById('kode').disabled=false;
        document.getElementById('kode').value='';
        document.getElementById('nama').value='';
        document.getElementById('nama1').value='';        
        document.getElementById('satuan').value='';
        document.getElementById('method').value='insert';		
}
