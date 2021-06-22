// JavaScript Document
function saveFranco(fileTarget,passParam) {
	
    var passP = passParam.split('##');
    var param = "";
	
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    cariTransaksi();
//                        loadData();
                        cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
        param='proses=loadData';
        tujuan='sdm_slave_5jamkerja';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
	   document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cariBast(num)
{
        caritanggal=document.getElementById('caritanggal').value;
        carisupplier=document.getElementById('carisupplier').options[document.getElementById('carisupplier').selectedIndex].value;
        caripabrik=document.getElementById('caripabrik').options[document.getElementById('caripabrik').selectedIndex].value;
        param='proses=cariData'+'&caritanggal='+caritanggal+'&carisupplier='+carisupplier+'&caripabrik='+caripabrik;
        param+='&page='+num;
         tujuan='sdm_slave_5jamkerja.php';
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
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function cariTransaksi()
{
        caritanggal=document.getElementById('caritanggal').value;
        carisupplier=document.getElementById('carisupplier').options[document.getElementById('carisupplier').selectedIndex].value;
        caripabrik=document.getElementById('caripabrik').options[document.getElementById('caripabrik').selectedIndex].value;
        param='proses=cariData'+'&caritanggal='+caritanggal+'&carisupplier='+carisupplier+'&caripabrik='+caripabrik;
        tujuan='sdm_slave_5jamkerja';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
	  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cariTrans(num)
{
            tgl=document.getElementById('tglCri').value;
            kdbrg=document.getElementById('kdBrgCari').options[document.getElementById('kdBrgCari').selectedIndex].value;
            ipsd=document.getElementById('idPsrCari').options[document.getElementById('idPsrCari').selectedIndex].value;
            param='proses=cariData'+'&idPasar='+ipsd+'&kdBrgCari='+kdbrg+'&tglHarga='+tgl;
            param+='&page='+num;
             tujuan='sdm_slave_5jamkerja';
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
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }	
}
function fillField(pabrik,tanggal,supplier,hargab,hargas,hargak)
{
	document.getElementById('tanggal').value=tanggal;
        l=document.getElementById('pabrik');

        for(a=0;a<l.length;a++)
        {
        if(l.options[a].value==pabrik)
            {
                l.options[a].selected=true;
            }
        }

         dl=document.getElementById('supplier');

        for(a=0;a<dl.length;a++)
        {
        if(dl.options[a].value==supplier)
            {
                dl.options[a].selected=true;
            }
        }
	document.getElementById('hargab').value=hargab;
	document.getElementById('hargas').value=hargas;
	document.getElementById('hargak').value=hargak;
        document.getElementById('proses').value="update";
	document.getElementById('pabrik').disabled=true;
	document.getElementById('tanggal').disabled=true;
        document.getElementById('supplier').disabled=true;
	
}
function cancelIsi()
{
    //$arr="##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses";
	document.getElementById('pabrik').value='';
	document.getElementById('tanggal').value='';
	document.getElementById('supplier').value='';
	document.getElementById('hargab').value='';
	document.getElementById('hargas').value='';
	document.getElementById('hargak').value='';
        document.getElementById('proses').value="insert";
	document.getElementById('hargab').disabled=false;
	document.getElementById('hargas').disabled=false;
	document.getElementById('hargak').disabled=false;
	document.getElementById('pabrik').disabled=false;
	document.getElementById('supplier').disabled=false;
        document.getElementById('tanggal').disabled=false;
}
function delData(pabrik,tanggal,supplier)
{
	param='proses=delData'+'&pabrik='+pabrik+'&tanggal='+tanggal+'&supplier='+supplier;
	tujuan='sdm_slave_5jamkerja';
	if(confirm("Delete, are you sure?"))
        {
            post_response_text(tujuan+'.php', param, respon);
	}
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                            loadData();
                            cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function inputharga()
{
    hargab=document.getElementById('hargab').value;
    hargas=95 * +hargab / 100;
    hargak=85 * +hargab / 100;
    
    document.getElementById('hargas').value=hargas;
    document.getElementById('hargak').value=hargak;
}

function getSatuan()
{
    kdBar=document.getElementById('kdBarang').options[document.getElementById('kdBarang').selectedIndex].value;
    param='proses=getSatuan'+'&kdBarang='+kdBar;
    tujuan='sdm_slave_5jamkerja';
    post_response_text(tujuan+'.php', param, respon);
	
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById('satuan');
                    res.value = con.responseText;
					
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}