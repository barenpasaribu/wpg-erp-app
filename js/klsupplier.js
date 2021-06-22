/**
 * @author antoniuslouis
 */
function getCodeNumber(tipe)
{
        param='tipe='+tipe;
        tujuan='log_slave_get_klsupplier_number.php';
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
                                                        document.getElementById('kodespl').value=trim(con.responseText);
                                                    getList(tipe);
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}

function saveKelSup()
{
        tipe		=trim(document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value);
        kodespl		=trim(document.getElementById('kodespl').value);
        kelompok	=trim(document.getElementById('kelompok').value);
        noakun		=trim(document.getElementById('akun').options[document.getElementById('akun').selectedIndex].value);
    method		=document.getElementById('method').value;
        param='tipe='+tipe+'&kode='+kodespl+'&kelompok='+kelompok+'&noakun='+noakun;
        param+='&method='+method;
        tujuan='log_slave_save_klsupplier.php';
        //alert(param);
        if (tipe == '' || kodespl == '' || kelompok == '') 
                alert('Data incomplete');
        else {
                if(confirm('Saving '+kelompok+', Are you sure..?'))
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
                                                   //clear form
                                                   cancelKelSup();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }    
}

function getList(tipe)
{
    param='tipe='+tipe;
        tujuan='log_slave_save_klsupplier.php';
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
function cancelKelSup()
{
        document.getElementById('kodespl').value='';
        getCodeNumber(trim(document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value));
        document.getElementById('kelompok').value='';
    document.getElementById('method').value='insert';	
}

function delKlSupplier(kode)
{
    param='kode='+kode+'&method=delete';
        tujuan='log_slave_save_klsupplier.php';
        if(confirm('Deleting '+kode+', Are you sure..?'))
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
                                                    //get list
                                                        getCodeNumber(trim(document.getElementById('tipe').options[document.getElementById('tipe').selectedIndex].value));
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}

function editKlSupplier(kode,kelompok,tipe,noakun)
{
        document.getElementById('method').value='update';
        optTipe=document.getElementById('tipe');
        for(x=0;x<optTipe.length;x++)
        {
                if(optTipe.options[x].value==tipe)
                   optTipe.options[x].selected=true;
        }
        document.getElementById('kodespl').value=kode;
        document.getElementById('kelompok').value=kelompok;
        optAkun=document.getElementById('akun');
        for(x=0;x<optAkun.length;x++)
        {
                if(optAkun.options[x].value==noakun)
                   optAkun.options[x].selected=true;		
        }
}
