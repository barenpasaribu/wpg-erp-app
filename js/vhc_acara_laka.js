//JS 

function get_kd(notrans,kodevhc)
{
        //alert("test");
        if (kodevhc==''){
        kdtrs=document.getElementById('kodetraksi');
        kdtrs=kdtrs.options[kdtrs.selectedIndex].value;
        param='method=getkodefiled'+'&kdtrs='+kdtrs;
        }else{
            kdtrs=notrans;
            kdevhc=kodevhc;
            param='method=getkodefiled'+'&kdtrs='+kdtrs;
            param+='&kdevhc='+kdevhc;
        }
        
                
        tujuan='vhc_slave_acara_laka.php';
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
                                                  //	alert(con.responseText);
                                                        document.getElementById('kde_vhc').innerHTML=con.responseText;
                                                        //load_data_pekerjaan();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         post_response_text(tujuan, param, respog);	
}

function get_kendaraan(notrans)
{
        //alert("test");
        kdtrs=document.getElementById('kde_vhc');
        kdtrs=kdtrs.options[kdtrs.selectedIndex].value;
        
        param='method=getkode_vhc'+'&kd_vhc='+kdtrs;
        
        tujuan='vhc_slave_acara_laka.php';
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
                                                  //	alert(con.responseText);
                                                        document.getElementById('operator').innerHTML=con.responseText;
                                                       
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         post_response_text(tujuan, param, respog);	
}


function new_acara_laka(){
    
        param='method=baru';
        
        tujuan='vhc_slave_acara_laka.php';
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
                                                  //	alert(con.responseText);
                                                        document.getElementById('notransaksi').value=trim(con.responseText);
                                                        document.getElementById('tanggal').disabled=false;
														document.getElementById('tanggal').value='';
														var trk = document.getElementById('kodetraksi');
                                                        trk.disabled=false;
														if(trk.selectedIndex>-1) trk.selectedIndex=0;
														
														var kdVhc = document.getElementById('kde_vhc');
														kdVhc.disabled=false;
														kdVhc.options.length=0;
														kdVhc.innerHTML = '';
														
														var opr = document.getElementById('operator');
														opr.disabled=false;
														opr.options.length=0;
														opr.innerHTML = '';
														
														var security = document.getElementById('security');
                                                        security.disabled=false;
														if(security.selectedIndex>-1) security.selectedIndex=0;
														
														var karymekanik = document.getElementById('karymekanik');
                                                        karymekanik.disabled=false;
														if(karymekanik.selectedIndex>-1) karymekanik.selectedIndex=0;
														
														var managerunit = document.getElementById('managerunit');
                                                        managerunit.disabled=false;
														if(managerunit.selectedIndex>-1) managerunit.selectedIndex=0;
														
														var karyworkshop = document.getElementById('karyworkshop');
                                                        karyworkshop.disabled=false;
														if(karyworkshop.selectedIndex>-1) karyworkshop.selectedIndex=0;
														
                                                        document.getElementById('kronologiskejadian').disabled=false;
														document.getElementById('kronologiskejadian').value='';
                                                        document.getElementById('akibatkejadian').disabled=false;
														document.getElementById('akibatkejadian').value='';
                                                         cancel();
                                                       
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  
         
         post_response_text(tujuan, param, respog);	    
        
    
}

function simpan(){
    //alert('masuk simpan');
        notransaksi=trim(document.getElementById('notransaksi').value);
	tanggal=document.getElementById('tanggal').value;
        kodetraksi=document.getElementById('kodetraksi');
        kodetraksi=kodetraksi.options[kodetraksi.selectedIndex].value;
        
        kde_vhc=document.getElementById('kde_vhc');
        kde_vhc=kde_vhc.options[kde_vhc.selectedIndex].value;
        
        operator=document.getElementById('operator');
        operator=operator.options[operator.selectedIndex].value;
        
        security=document.getElementById('security');
        security=security.options[security.selectedIndex].value;
        
        karymekanik=document.getElementById('karymekanik');
        karymekanik=karymekanik.options[karymekanik.selectedIndex].value;
        
        managerunit=document.getElementById('managerunit');
        managerunit=managerunit.options[managerunit.selectedIndex].value;
        
        karyworkshop=document.getElementById('karyworkshop');
        karyworkshop=karyworkshop.options[karyworkshop.selectedIndex].value;
        
        kronologiskejadian=document.getElementById('kronologiskejadian').value;
        akibatkejadian=document.getElementById('akibatkejadian').value;
        	
	met=document.getElementById('method').value;
	param='notransaksi='+notransaksi+'&tanggal='+tanggal+'&kodetraksi='+kodetraksi+'&kd_vhc='+kde_vhc;
        param+='&operator='+operator+'&security='+security+'&karymekanik='+karymekanik+'&managerunit='+managerunit;
        param+='&karyworkshop='+karyworkshop+'&kronologiskejadian='+kronologiskejadian+'&akibatkejadian='+akibatkejadian;
        param+='&method='+met;
        //alert(param);
	tujuan='vhc_slave_acara_laka.php';
    post_response_text(tujuan, param, respog);		
	//}
	
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
							document.getElementById('notransaksi').disabled=true;
							document.getElementById('tanggal').disabled=true;
							document.getElementById('kodetraksi').disabled=true;
							document.getElementById('kde_vhc').disabled=true;
							document.getElementById('operator').disabled=true;
							document.getElementById('security').disabled=true;
							document.getElementById('karymekanik').disabled=true;
							document.getElementById('managerunit').disabled=true;
							document.getElementById('karyworkshop').disabled=true;
							document.getElementById('kronologiskejadian').disabled=true;
							document.getElementById('akibatkejadian').disabled=true;
                            loadData(0);
							cancel();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
}
					


function cancel(){
	document.getElementById('tanggal').value="";
        document.getElementById('kodetraksi').value="";
	document.getElementById('kde_vhc').value="";
        
//	document.getElementById('operator').value="";
//        document.getElementById('security').value="";
//        document.getElementById('karymekanik').value="";
//        document.getElementById('managerunit').value="";
//        document.getElementById('karyworkshop').value="";
        
        document.getElementById('kronologiskejadian').value="";
        document.getElementById('akibatkejadian').value="";
}




function loadData (num) {
	noTransCr=document.getElementById('noTransCr').value;
	param='method=loadData';
        param+='&noTransCr='+noTransCr;
        param+='&page='+num;
	tujuan='vhc_slave_acara_laka.php';
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
                                   // alert(con.responseText);
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

//,tanggal,kodealat,operator,security,mekanik,managerunit,kaworkshop,kronologis,akibatkejadian
function edit(notransaksi){
        param='method=getData'+'&notransaksi='+notransaksi;
    	tujuan='vhc_slave_acara_laka.php';
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
                                    isidt=con.responseText.split("####");
    document.getElementById('notransaksi').value=isidt[0];
    document.getElementById('tanggal').value=isidt[1];
    document.getElementById('kodetraksi').value=isidt[2];

    document.getElementById('kde_vhc').innerHTML=isidt[3];
    document.getElementById('operator').innerHTML=isidt[4];

    document.getElementById('security').value=isidt[5];
    document.getElementById('karymekanik').value=isidt[6];
    document.getElementById('managerunit').value=isidt[7];

    document.getElementById('karyworkshop').value=isidt[8];
    document.getElementById('kronologiskejadian').value=isidt[9];
    document.getElementById('akibatkejadian').value=isidt[10];

    document.getElementById('method').value='update';
    document.getElementById('notransaksi').disabled=true;
    document.getElementById('tanggal').disabled=false;
    document.getElementById('kodetraksi').disabled=false;
    document.getElementById('kde_vhc').disabled=false;
    document.getElementById('operator').disabled=false;
    document.getElementById('security').disabled=false;
    document.getElementById('karymekanik').disabled=false;
    document.getElementById('managerunit').disabled=false;
    document.getElementById('karyworkshop').disabled=false;
    document.getElementById('kronologiskejadian').disabled=false;
    document.getElementById('akibatkejadian').disabled=false;

									
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  

}



function delData(notransaksi)
{
	param='method=delete'+'&notransaksi='+notransaksi;
	//alert(param);
	tujuan='vhc_slave_acara_laka.php';
        if(confirm('Anda yakin mau menghapus?')){
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
					else 
					{
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
function upGrade(){
    //kdkegiatanCrPrsn
        bsis=document.getElementById('bsisPrsn').value;
	hrgsat=document.getElementById('hrgStnPrsn').value;
        hrglbh=document.getElementById('hrgLbhBsisPrsn').value;
        hrgmngg=document.getElementById('hrgMnggPrsn').value;
        param='method=upGradeData'+'&bsisPrsn='+bsis+'&hrgStnPrsn='+hrgsat;
        param+='&hrgLbhBsisPrsn='+hrglbh+'&hrgMnggPrsn='+hrgmngg;
        
	//alert(param);
	tujuan='vhc_slave_acara_laka.php';
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
					else 
					{
						loadData(0);
                                                document.getlementById('bsisPrsn').value='';
                                                document.getlementById('hrgStnPrsn').value='';
                                                document.getlementById('hrgLbhBsisPrsn').value='';
                                                document.getlementById('hrgMnggPrsn').value='';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}
}



