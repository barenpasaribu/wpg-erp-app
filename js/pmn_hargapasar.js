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
                        loadData();
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
        tujuan='pmn_slave_hargapasar';
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
        param='proses=loadData';
        param+='&page='+num;
         tujuan='pmn_slave_hargapasar';
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
        tgl=document.getElementById('tglCri').value;
        kdbrg=document.getElementById('kdBrgCari').options[document.getElementById('kdBrgCari').selectedIndex].value;
        ipsd=document.getElementById('idPsrCari').options[document.getElementById('idPsrCari').selectedIndex].value;
        param='proses=cariData'+'&idPasar='+ipsd+'&kdBrgCari='+kdbrg+'&tglHarga='+tgl;
        tujuan='pmn_slave_hargapasar';
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
             tujuan='pmn_slave_hargapasar';
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
function fillField(tgl,kdbrg,sat,psdr,idmtuang,hrga,status,catatan)
{
	document.getElementById('tglHarga').value=tgl;
        l=document.getElementById('kdBarang');

        for(a=0;a<l.length;a++)
        {
        if(l.options[a].value==kdbrg)
            {
                l.options[a].selected=true;
            }
        }
	//document.getElementById('kdBarang').value='';
	document.getElementById('satuan').value=sat;
	document.getElementById('idPasar').value='';
    dl=document.getElementById('idPasar');
	for(a=0;a<dl.length;a++)
	{
	if(dl.options[a].value==psdr)
		{
			dl.options[a].selected=true;
		}
	}
	
	sts=document.getElementById('status');
	for(a=0;a<sts.length;a++)
	{
	if(sts.options[a].value==status)
		{
			sts.options[a].selected=true;
		}
	}
	
	document.getElementById('catatan').value=catatan;
	document.getElementById('idMatauang').value=idmtuang;
	document.getElementById('hrgPasar').value=hrga;
	document.getElementById('proses').value="update";
	document.getElementById('tglHarga').disabled=true;
	document.getElementById('kdBarang').disabled=true;
    document.getElementById('idPasar').disabled=true;
}
function cancelIsi()
{
    //$arr="##tglHarga##kdBarang##satuan##idPasar##idMatauang##hrgPasar##proses";
	document.getElementById('tglHarga').value='';
	document.getElementById('kdBarang').value='';
	document.getElementById('satuan').value='';
	document.getElementById('idPasar').value='';
	document.getElementById('idMatauang').value='';
        document.getElementById('hrgPasar').value='';
		document.getElementById('status').selectedIndex=0;
		document.getElementById('catatan').value='';
        document.getElementById('proses').value="insert";
	document.getElementById('tglHarga').disabled=false;
	document.getElementById('kdBarang').disabled=false;
        document.getElementById('idPasar').disabled=false;
}
function delData(tgl,kdbrg,psdr)
{
	param='proses=delData'+'&kdBarang='+kdbrg+'&tglHarga='+tgl+'&idPasar='+psdr;
	tujuan='pmn_slave_hargapasar';
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
function getSatuan()
{
    kdBar=document.getElementById('kdBarang').options[document.getElementById('kdBarang').selectedIndex].value;
    param='proses=getSatuan'+'&kdBarang='+kdBar;
    tujuan='pmn_slave_hargapasar';
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

function ApproveTBS(approve_no,tanggal)
{
	konfirmasi = confirm("Anda yakin ?");
	if( konfirmasi ){
		param='proses=approveHargaTbs&approve_no='+approve_no+'&tanggal='+tanggal;
		tujuan='pmn_slave_hargapasar';
		post_response_text(tujuan+'.php', param, respon);
		function respon() {
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					} else {
						if(con.responseText == "success"){
							loadData();
						}else{
							alert(con.responseText);
						}						
					}
				} else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
	}
	
}