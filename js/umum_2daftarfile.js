/**
 * @author antoniuslouis
 */
function simpanDep()
{
    kode=document.getElementById('kode').value;
    nama=document.getElementById('nama').value;
    met=document.getElementById('method').value;
    if(trim(kode)=='')
    {
            alert('Code is empty');
            document.getElementById('kode').focus();
    }
    else
    {
            kode=trim(kode);
            nama=trim(nama);
            param='kode='+kode+'&nama='+nama+'&method='+met;
            tujuan='rencana_slave_save_jenis.php';
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

function fillField(kode,nama)
{
    document.getElementById('kode').value=kode;
    document.getElementById('kode').disabled=true;
    document.getElementById('nama').value=nama;
    document.getElementById('method').value='update';
}

function cancelDep()
{
    document.getElementById('kode').disabled=false;
    document.getElementById('kode').value='';
    document.getElementById('nama').value='';
    document.getElementById('method').value='insert';		
}

function cancelPhoto()
{
    winForm.document.getElementById('frmUpload').reset();
}
function simpanPhoto()
{
    winForm.document.getElementById('frmUpload').submit();
}

function loadList()
{
    window.location='rencana_gis.php';
}

function delFile(unit,kode,namafile){
    tujuan='rencana_slave_gisHapusFile.php';
    param="namafile="+namafile;
    if(confirm('Anda yakin mengapus file '+namafile+' ?')){
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
                                                        window.location='rencana_gis.php';
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }
}

function download(namafile){
    frame.location='filegis/'+namafile;
}

function cariFile()
{
    kodeorg=document.getElementById('kodeorg1');
    kodeorg=kodeorg.options[kodeorg.selectedIndex].value;
    kode=document.getElementById('kode1');
    kode=kode.options[kode.selectedIndex].value;
    periode=document.getElementById('periode');
    periode=periode.options[periode.selectedIndex].value;    
    
    tujuan='rencana_slave_getGisFile.php';
    param='kodeorg='+kodeorg+'&periode='+periode+'&kode='+kode;
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
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
     }    
}