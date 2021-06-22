// JavaScript Document
//Jamhari....kelompok Pelanggan, Pemasaran>Setup>Kelompok Pelanggan

function searchAkun(title,content,ev)
{
        width='500';
        height='400';
        showDialog1(title,content,width,height,ev);
        //alert('asdasd');
}
function findAkun()
{
        txt=trim(document.getElementById('no_akun').value);
        if(txt=='')
        {
                alert('Text is obligatory');
        }
        else
        {
                param='txtfind='+txt;
                tujuan='log_slave_get_akun.php';
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
function setNoakun(no_akun,namaakun)
{
         document.getElementById('nama_akun').value=namaakun;
         document.getElementById('akun_cust').value=no_akun;
         closeDialog();
}
function fillField(kode,kelompok,noakun,namaakun)
{
        kode_grp_cus		=document.getElementById('kode_grp_cus');
        kode_grp_cus.value	=kode;
        kode_grp_cus.disabled=true;
        klmpk_cust			=document.getElementById('klmpk_cust');
        klmpk_cust.value	=kelompok;
        akun_cust		    =document.getElementById('akun_cust');
        akun_cust.value=noakun;
        nama_akun			=document.getElementById('nama_akun');
        nama_akun.value=namaakun;

        document.getElementById('method').value='update';
}
function batalKlmpkplgn()
{
        document.getElementById('kode_grp_cus').value='';
        document.getElementById('kode_grp_cus').disabled=false;
        document.getElementById('klmpk_cust').value='';
        document.getElementById('nama_akun').value='';
        document.getElementById('akun_cust').value='';
}
//test
function simpanKlmpkplgn()
{
        kode=trim(document.getElementById('kode_grp_cus').value);
        kelompok=trim(document.getElementById('klmpk_cust').value);
        noakun=trim(document.getElementById('akun_cust').value);
        method=document.getElementById('method').value;
                param='kode='+kode+'&kelompok='+kelompok+'&noakun='+noakun+'&method='+method;
                tujuan='log_slave_save_akun_cust.php';
        if (kode == '' || kelompok == '') {
                alert('Data inconsistent');
        }
        else {
                if(confirm('Are you sure?'))
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
                                                        document.getElementById('containersatuan').innerHTML=con.responseText;
                                                        batalKlmpkplgn();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}


function delKlmpkplgn(kode,noakun)
{
        param='kode='+kode+'&noakun='+noakun;
                param+='&method=delete';
                tujuan='log_slave_save_akun_cust.php';
                if(confirm('Deleting, Are you sure?'))
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
                                                        document.getElementById('containersatuan').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	
}

