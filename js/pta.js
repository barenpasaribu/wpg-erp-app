function kunciForm()
{
    dtVal=document.getElementById('jenis_pta').options[document.getElementById('jenis_pta').selectedIndex].value;
    if(dtVal=='HK')
    {
        document.getElementById('kegId').disabled=false;
        document.getElementById('alokasi').disabled=false;
        document.getElementById('vol_pekerjaan').disabled=false;
        document.getElementById('satuan_vol').disabled=false;
        document.getElementById('kode_vhc').disabled=true;
        document.getElementById('kdbrng').disabled=true;
        document.getElementById('nmbrng').disabled=true;
    }
    if(dtVal=='MATERIAL')
    {
        document.getElementById('kegId').disabled=false;
        document.getElementById('alokasi').disabled=false;
        document.getElementById('vol_pekerjaan').disabled=false;
        document.getElementById('satuan_vol').disabled=false;
        document.getElementById('kdbrng').disabled=false;
        document.getElementById('nmbrng').disabled=false;
        document.getElementById('kode_vhc').disabled=true;
    }
    if(dtVal=='HM')
    {
        document.getElementById('kegId').disabled=false;
        document.getElementById('alokasi').disabled=false;
        document.getElementById('vol_pekerjaan').disabled=false;
        document.getElementById('satuan_vol').disabled=false;
        document.getElementById('kode_vhc').disabled=false;
        document.getElementById('kdbrng').disabled=true;
        document.getElementById('nmbrng').disabled=true;
    }
    if(dtVal=='UMUM')
    {
        document.getElementById('kegId').disabled=true;
        document.getElementById('alokasi').disabled=true;
        document.getElementById('kode_vhc').disabled=true;
        document.getElementById('kdbrng').disabled=true;
        document.getElementById('nmbrng').disabled=true;
        document.getElementById('vol_pekerjaan').disabled=true;
        document.getElementById('satuan_vol').disabled=true;
    }

}
function createNew()
{
    kdKeg=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    param='kdKeg='+kdKeg+'&method=getsatuan';
    tujuan='pta_slave_buat.php';
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
                    document.getElementById('satuan_vol').value=con.responseText;
                    document.getElementById('satuan_vol').disabled=true;
            }
        }
        else {
                busy_off();
                error_catch(con.status);
        }
      }	
     }  	
}

function searchBrg(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
}

function findBrg()
{
    txt=trim(document.getElementById('no_brg').value);
    if(txt=='')
    {
            alert('Text is obligatory');
    }
    else if(txt.length<3)
    {
            alert('Min 3 chars');
    }
    else
    {
            param='txtfind='+txt+'&method=cariBarangDlmDtBs';
            tujuan='pta_slave_buat.php';
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

function setBrg(no_brg,namabrg)
{
    document.getElementById('kdbrng').value=no_brg;
    document.getElementById('nmbrng').value=namabrg;
    document.getElementById('nmbrng').disabled=true;
    closeDialog();
}
function cancelForm()
{
    document.getElementById('nopta').value='';
    document.getElementById('kelompok').value='';
    document.getElementById('tgl').value='';
    document.getElementById('penjelasan').value='';
    document.getElementById('tipe_pta').value='';
    document.getElementById('jenis_pta').value='';
    document.getElementById('noakunData').value='';
    document.getElementById('kegId').value='';
    document.getElementById('alokasi').value='';
    document.getElementById('kode_vhc').value='';
    document.getElementById('kdbrng').value='';
    document.getElementById('vol_pekerjaan').value='0';
    document.getElementById('satuan_vol').value='';
    document.getElementById('jml').value='0';
    document.getElementById('satuan_jml').value='';
    document.getElementById('jml_rp').value='0';

    document.getElementById('kegId').disabled=false;
    document.getElementById('alokasi').disabled=false;
    document.getElementById('vol_pekerjaan').disabled=false;
    document.getElementById('satuan_vol').disabled=false;
    document.getElementById('kode_vhc').disabled=false;
    document.getElementById('kdbrng').disabled=false;
    document.getElementById('nmbrng').disabled=false;
    }

function saveForm()
{
    nopta=document.getElementById('nopta').value;
    klmpk=trim(document.getElementById('kelompok').value);
    tgl=document.getElementById('tgl').value;
    penjelasan=trim(document.getElementById('penjelasan').value);
    tipepta=document.getElementById('tipe_pta').value;
    jnpta=document.getElementById('jenis_pta').value;
    akun=document.getElementById('noakunData').value;
    kegId=document.getElementById('kegId').value;
    alokasi=document.getElementById('alokasi').value;
    kdvhc=document.getElementById('kode_vhc').value;
    kdbrng=document.getElementById('kdbrng').value;
    vol=document.getElementById('vol_pekerjaan').value;
    satv=document.getElementById('satuan_vol').value;
    jml=document.getElementById('jml').value;
    satj=document.getElementById('satuan_jml').value;
    rp=document.getElementById('jml_rp').value;
    dtr=document.getElementById('method').value;

    param = "nopta="+nopta;
    param +="&kelompok="+klmpk;
    param +="&tgl="+tgl;
    param +="&penjelasan="+penjelasan;
    param +="&tipe_pta="+tipepta;
    param +="&jenis_pta="+jnpta;
    param +="&noakunData="+akun;
    param +="&kegId="+kegId;
    param +="&alokasi="+alokasi;
    param +="&kode_vhc="+kdvhc;
    param +="&kdbrng="+kdbrng;
    param +="&vol_pekerjaan="+vol;
    param +="&satuan_vol="+satv;
    param +="&jml="+jml;
    param +="&satuan_jml="+satj;
    param +="&jml_rp="+rp;
    param +='&method='+dtr;

//    alert(param);
    tujuan='pta_slave_buat.php';
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
                    alert('Done.');
                    loaddata();
                    document.getElementById('nopta').value=notransaksi;
                    document.getElementById('kelompok').value=kelompok;
                    document.getElementById('tgl').value=tanggal;
                    document.getElementById('penjelasan').value=penjelasan;



                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 


}

function loaddata()
{
    notrans=document.getElementById('nopta').value;
    param='method=loaddata';
    param +="&notransaksi="+notrans;
//    alert(param);
    tujuan='pta_slave_buat.php';
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    ard=con.responseText.split("###");
                    document.getElementById('contain').innerHTML=ard[0];
                    document.getElementById('tgl').disabled=false;
                    document.getElementById('method').value='add'; 
                    if(ard.length=='2')
                        {
                            document.getElementById('nopta').value=ard[1];
                            document.getElementById('kegId').disabled=false;
                            document.getElementById('alokasi').disabled=false;
                            document.getElementById('vol_pekerjaan').disabled=false;
                            document.getElementById('satuan_vol').disabled=false;
                            document.getElementById('kode_vhc').disabled=false;
                            document.getElementById('kdbrng').disabled=false;
                            document.getElementById('nmbrng').disabled=false;


                            document.getElementById('tipe_pta').value='';
                            document.getElementById('jenis_pta').value='';
                            document.getElementById('noakunData').value='';
                            document.getElementById('kegId').value='';
                            document.getElementById('alokasi').value='';
                            document.getElementById('kode_vhc').value='';
                            document.getElementById('kdbrng').value='';
                            document.getElementById('vol_pekerjaan').value='0';
                            document.getElementById('satuan_vol').value='';
                            document.getElementById('jml').value='0';
                            document.getElementById('satuan_jml').value='';
                            document.getElementById('jml_rp').value='0'; 
                        }
                        else
                        {
                            document.getElementById('nopta').value=ard[1];
                            document.getElementById('tgl').value=ard[2];
                            document.getElementById('penjelasan').value=ard[3];
                        }
                   loadDaftar();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function loadDaftar()
{
    param='method=daftarData';
    tujuan='pta_slave_buat.php';
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {

                    document.getElementById('daftarData').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getKegiatan()
{
    nakun=document.getElementById('noakunData').options[document.getElementById('noakunData').selectedIndex].value;
    param='method=getKegiatan'+'&noakun='+nakun;
    tujuan='pta_slave_buat.php';
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {

                    document.getElementById('kegId').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function previewPdf(notrans,ev)
{
        notransaksi=notrans;
        param='proses=prevPdf'+'&notransaksi='+notransaksi;
        tujuan = 'pta_slave_persetujuan.php?'+param;	
 //display window
   title='Print PDF';
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}
function editData(npta)
{
    param='method=getData'+'&notransaksi='+npta;
    tujuan='pta_slave_buat.php';
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    ard=con.responseText.split("###");
                    document.getElementById('contain').innerHTML=ard[0];
                    document.getElementById('nopta').value=trim(ard[1]);
                    document.getElementById('tgl').value=ard[2];
                    document.getElementById('penjelasan').value=ard[3];
                            document.getElementById('kegId').disabled=false;
                            document.getElementById('alokasi').disabled=false;
                            document.getElementById('vol_pekerjaan').disabled=false;
                            document.getElementById('satuan_vol').disabled=false;
                            document.getElementById('kode_vhc').disabled=false;
                            document.getElementById('kdbrng').disabled=false;
                            document.getElementById('nmbrng').disabled=false;
                            document.getElementById('tgl').disabled=true;

                            document.getElementById('tipe_pta').value='';
                            document.getElementById('jenis_pta').value='';
                            document.getElementById('noakunData').value='';
                            document.getElementById('kegId').value='';
                            document.getElementById('alokasi').value='';
                            document.getElementById('kode_vhc').value='';
                            document.getElementById('kdbrng').value='';
                            document.getElementById('vol_pekerjaan').value='0';
                            document.getElementById('satuan_vol').value='';
                            document.getElementById('jml').value='0';
                            document.getElementById('satuan_jml').value='';
                            document.getElementById('jml_rp').value='0'; 
                            document.getElementById('method').value='editData'; 
                            $test=document.getElementById('tabFRM0');
                            //tabAction($test,0,'FRM',2);
                            tabAction($test,0,'FRM',1);

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }


}
function deleteData(nopta)
{
    param='method=deleteData'+'&notransaksi='+nopta;
    tujuan='pta_slave_buat.php';
    if(confirm("Delete, are you sure?"))
    post_response_text(tujuan, param, respon);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   loadDaftar();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function delData(nopta,jnspta,alokasi,kdvhc,kodebarang,noakun)
{
    param='method=delete'+'&nopta='+nopta+'&jnspta='+jnspta+'&alokasi='+alokasi;
    param+='&kdvhc='+kdvhc+'&kodebarang='+kodebarang+'&noakun='+noakun;

    tujuan='pta_slave_buat.php';
//    alert(tujuan);
    if(confirm("Are You Sure Want Delete Data?"))
        post_response_text(tujuan, param, respog);

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    loaddata();
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function appAjukan(ev)
{
        title="Form  Approval";
        content="<div id=isiAjukan></div>";
        width='320';
        height='150';
        showDialog1(title,content,width,height,ev);	
}
function showAjukan()
{
    appAjukan('event');
    notrans=document.getElementById('nopta').value;
    param='method=getForm'+'&notransaksi='+notrans;
    tujuan = 'pta_slave_buat.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            document.getElementById('isiAjukan').innerHTML=con.responseText;

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function saveAjukan()
{
    notransaksi=trim(document.getElementById('nopta').value);
    krywnId=document.getElementById('dtKary').options[document.getElementById('dtKary').selectedIndex].value;
    kt=document.getElementById('koments').value;
    param='method=appSetuju'+'&notransaksi='+notransaksi+'&krywnId='+krywnId+'&ket='+kt;
    tujuan = 'pta_slave_buat.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            document.getElementById('isiAjukan').innerHTML=con.responseText;
                                            alert("Done");
                                            closeDialog();
                                            loaddata();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}