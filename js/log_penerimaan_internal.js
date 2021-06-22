// JavaScript Document
function saveFranco(nomr) {
    //alert(passParam);
    
    tglKrm=document.getElementById('tglTrima').value;
    
    param='tglKrm='+tglKrm+'&method=update';
    param+='&idNomor='+nomr;
    
    fileTarget='log_slave_penerimaan_internal';
    post_response_text(fileTarget+'.php', param, respon);
//    alert(param);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
						loadData();
						//cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    

}
function loadData()
{
        txtSrc=document.getElementById('txtsearch').value;
        tglSrc=document.getElementById('tgl_cari').value;
        param='method=loadData';
        if(txtSrc!='')
        {
        param+='&txtSrc='+txtSrc;
        }
        if(tglSrc!='')
        {
        param+='&tglSrc='+tglSrc;
        }
        tujuan='log_slave_penerimaan_internal';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
					  document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function fillField(idFr)
{
	
	param='method=getData'+'&idNomor='+idFr;
	tujuan='log_slave_pengiriman_internal';
    post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					ar=con.responseText.split("###");
					document.getElementById('nomor_id').value=ar[0];
                                        ldata=document.getElementById('id_supplier');
                                        for(adatasup=0;adatasup<ldata.length;adatasup++)
                                        {
                                            if(ldata.options[adatasup].value==ar[1])
                                                {
                                                    ldata.options[adatasup].selected=true;
                                                }
                                        }
					document.getElementById('tglKrm').value=ar[2];
					document.getElementById('jlhKoli').value=ar[3];
                                        ldata2=document.getElementById('kpd');
                                        for(adata=0;adata<ldata2.length;adata++)
                                        {
                                            if(ldata2.options[adata].value==ar[4])
                                                {
                                                    ldata2.options[adata].selected=true;
                                                }
                                        }
					document.getElementById('lokPenerimaan').value=ar[5];
					document.getElementById('srtJalan').value=ar[6];
                                        document.getElementById('biaya').value=ar[7];
                                        document.getElementById('ket').value=ar[8];
					document.getElementById('method').value='update';
					
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}

function cancelIsi()
{
	document.getElementById('id_supplier').value='';
	document.getElementById('tglKrm').value='';
	document.getElementById('jlhKoli').value='';
	document.getElementById('kpd').value='';
        document.getElementById('ket').value='';
	document.getElementById('lokPenerimaan').value='';
        document.getElementById('srtJalan').value='';
        document.getElementById('biaya').value='';
	document.getElementById('method').value="insert";
}
function delData(idFr)
{
	param='method=delData'+'&idNomor='+idFr;
	tujuan='log_slave_pengiriman_internal';
	if(confirm("Anda yakin ingin menghapus"))
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
                    // Success Response
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
function searchSupplier(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function searchSupplier(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}

function cariBast(num)
{
            txtSrc=document.getElementById('txtsearch').value;
            tglSrc=document.getElementById('tgl_cari').value;
            param='method=loadData';
            if(txtSrc!='')
            {
            param+='&txtSrc='+txtSrc;
            }
            if(tglSrc!='')
            {
            param+='&tglSrc='+tglSrc;
            }
                param+='&page='+num;
                tujuan = 'log_slave_penerimaan_internal.php';
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