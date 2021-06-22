/**
 * @author antoniuslouis
 */

function tampilkanBTLKebun()
{
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    if(kodeorg=='' || thnbudget=='')
        {
            alert('Tahun budget dan kodeorganisasi tidak boleh kosong');
        }
     else{   
        document.getElementById('tahun').innerHTML=thnbudget;
        document.getElementById('unit').innerHTML=kodeorg;
        document.getElementById('printPanel').style.display='';
        param='kodeorg='+kodeorg+'&thnbudget='+thnbudget;
        
        tujuan = 'bgt_slave_laporan_btl_kebun.php';	
	post_response_text(tujuan, param, respog);
        	        
     }  
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

function fisikKeExcel(ev,tujuan)
{
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');

    param='kodeorg='+kodeorg+'&thnbudget='+thnbudget;
	
		   	
	tujuan = tujuan+'?'+param;	
   title='Download';
   width='500';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);		
}

function fisikKePDF(ev,tujuan)
{
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');

    param='kodeorg='+kodeorg+'&thnbudget='+thnbudget;
	
		   	
	tujuan = tujuan+'?'+param;	
   title='Download';
   width='800';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);		
}

function tampilkanBTLPks(){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    if(kodeorg=='' || thnbudget=='')
        {
            alert('Tahun budget dan kodeorganisasi tidak boleh kosong');
        }
     else{   
        param='kodeorg='+kodeorg+'&thnbudget='+thnbudget;
        
        tujuan = 'bgt_slave_laporan_btl_pks.php';	
	post_response_text(tujuan, param, respog);
        	        
     }  
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

function tampilkanBLPks(){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    if(kodeorg=='' || thnbudget=='')
        {
            alert('Tahun budget dan kodeorganisasi tidak boleh kosong');
        }
     else{   
        param='kodeorg='+kodeorg+'&thnbudget='+thnbudget;
        
        tujuan = 'bgt_slave_laporan_biaya_pks.php';	
	post_response_text(tujuan, param, respog);
        	        
     }  
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

function showDt(station,kdbudget,tahun,ev){
   param='station='+station+'&kdbudget='+kdbudget+'&tahun='+tahun;
   tujuan='bgt_slave_laporan_pks_detail.php'+"?"+param;  
   width='1000';
   height='150';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detai Budget',content,width,height,ev); 
}

function tampilkanRPKGKebun(){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    jenis=getValue('jenis');
    if(kodeorg=='' || thnbudget=='' || jenis=='')
        {
            alert('Tahun budget,kodeorganisasi dan jenis biaya tidak boleh kosong');
        }
     else{   
        param='kodeorg='+kodeorg+'&thnbudget='+thnbudget+'&jenis='+jenis;
        
        tujuan = 'bgt_slave_laporan_RPKG_kebun.php';	
	post_response_text(tujuan, param, respog);
        	        
     }  
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

function tampilkanRPKGPks(){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    jenis=getValue('jenis');
    if(kodeorg=='' || thnbudget=='' || jenis=='')
        {
            alert('Tahun budget,kodeorganisasi dan jenis biaya tidak boleh kosong');
        }
     else{   
        param='kodeorg='+kodeorg+'&thnbudget='+thnbudget+'&jenis='+jenis;
        
        tujuan = 'bgt_slave_laporan_RPKG_pks.php';	
	post_response_text(tujuan, param, respog);
        	        
     }  
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
 
function fisikKeExcelRPKG(ev,tujuan){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    jenis=getValue('jenis');
    param='kodeorg='+kodeorg+'&thnbudget='+thnbudget+'&jenis='+jenis;
      tujuan = tujuan+'?'+param;	
   title='Download';
   width='500';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);    
}

function fisikKePDFRPKG(ev,tujuan){
    kodeorg=getValue('kodeunit');
    thnbudget=getValue('thnbudget');
    jenis=getValue('jenis');
    param='kodeorg='+kodeorg+'&thnbudget='+thnbudget+'&jenis='+jenis;
      tujuan = tujuan+'?'+param;	
   title='Download';
   width='800';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);    
}