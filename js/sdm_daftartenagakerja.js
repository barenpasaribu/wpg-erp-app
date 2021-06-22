function loadData(hal){
    
    if(hal=='1.1'){
       hal='1.1';
    }
    param='proses=loadData'+'&page='+hal;
     if(hal=='1.1'){
       hal=document.getElementById('pages').options[document.getElementById('pages').selectedIndex].value;
       param+='&page2='+hal;
    }
    thn=document.getElementById('thnPeriode').options[document.getElementById('thnPeriode').selectedIndex].value;
    if(thn!=''){
         param+='&tahun='+thn;
    }
    tujuan='sdm_slave_daftartenagakerja.php';
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
                        
                        document.getElementById('containerData').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
     
    
}
function cariData(hal){
    if(hal=='1.1'){
       hal=document.getElementById('pages').options[document.getElementById('pages').selectedIndex].value;
        
    }
    valtxt=document.getElementById('sNoTrans').value;
    param='proses=cariData'+'&page='+hal;
    if(valtxt!=''){
        param+='&sNoTrans='+valtxt;
    }
    tujuan='sdm_slave_daftartenagakerja.php';
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
                        document.getElementById('dataList').style.display='block';
                        document.getElementById('formData').style.display='none';
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

function masterPDF(table,column,cond,page,event) {
	// Prep Param
       
	param = "table="+table;
	param += "&column="+column;
	
	// Prep Condition
	param += "&cond="+cond;
	
	// Post to Slave
	if(page==null) {
		page = 'null';
	}
	if(page=='null') {
		page = "slave_master_pdf";
	}
	
	showDialog1('Print PDF',"<iframe frameborder=0 style='width:795px;height:400px' src='"+page+".php?proses=pdfDt&"+param+"'></iframe>",'800','400',event);
	var dialog = document.getElementById('dynamic1');
	dialog.style.top = '50px';
	dialog.style.left = '15%';
}
function procDt(notrans,urut){
    param='proses=update'+'&urut='+urut+'&notransaksi='+notrans;
    tujuan='sdm_slave_daftartenagakerja.php';
    if(confirm("Anda Yakin Ingin Mengkonfirmasi")){
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
                        loadData(0);
                        
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}
function procDt2(notrans){
    tgl=document.getElementById('tglsmp_'+notrans).value;
    if(tgl==""){
        alert("Tanggal Terakhir Display tidak boleh kosong!!");
        return;
    }
    param='proses=updateDt'+'&notransaksi='+notrans;
    param+='&tglTakhir='+tgl;
    tujuan='sdm_slave_daftartenagakerja.php';
    if(confirm("Anda Yakin Ingin Mengkonfirmasi")){
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
                        loadData(0);
                        
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
      }
     }
}