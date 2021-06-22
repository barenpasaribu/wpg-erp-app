

function loadData(hal){
    
    if(hal=='1.1'){
       hal='1.1';
    }
    
    param='proses=loadData'+'&page='+hal;
     if(hal=='1.1'){
       hal=document.getElementById('pages').options[document.getElementById('pages').selectedIndex].value;
       param+='&page2='+hal;
    }
    
    tujuan='kebun_slave_taksasi.php';
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
                        cancelIsi();
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
    tujuan='kebun_slave_taksasi.php';
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
function saveData(fileTarget,passParam) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            if(getValue(passP[i])=='')
                alert('tanggal tidak boleh kosong.');
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
	//alert(param);
 
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
//                        loadData();
//                        cancelIsi();
                        selesaiIsi();
                        alert('Done.');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);

}

function cancelIsi(){
    //$arr="##tanggal##afdeling##blok##seksi##proses##hasisa##haesok##jmlhpokok##persenbuahmatang##jjgmasak##jjgoutput##hkdigunakan##bjr";
            document.getElementById('afdeling').value='';
            document.getElementById('tanggal').diabled=false;
            document.getElementById('sNoTrans').value='';
            document.getElementById('blok').value='';
            document.getElementById('seksi').value='';
            document.getElementById('hasisa').value='';
            document.getElementById('haesok').value='';
            document.getElementById('jmlhpokok').value='';
			document.getElementById('sph').value='';
            document.getElementById('persenbuahmatang').value='';
            document.getElementById('jjgmasak').value='';
            document.getElementById('jjgoutput').value='';
            document.getElementById('hkdigunakan').value='';
            document.getElementById('bjr').value='';
            document.getElementById('tanggal').disabled=false;
            document.getElementById('afdeling').disabled=false;
            document.getElementById('proses').value='insert';
            document.getElementById('kebundt').disabled=false;
			
//            document.getElementById('kebundt').value='';
//            document.getElementById('mandor').disabled=false;
//            document.getElementById('afdeling').innerHTML="";
//            document.getElementById('afdeling').innerHTML="<option value=''></option>";
//            document.getElementById('blok').innerHTML="";
//            document.getElementById('blok').innerHTML="<option value=''></option>";
//            document.getElementById('mandor').innerHTML="";
//            document.getElementById('mandor').innerHTML="<option value=''></option>";
            
}

function selesaiIsi(){
    //$arr="##tanggal##afdeling##blok##seksi##proses##hasisa##haesok##jmlhpokok##persenbuahmatang##jjgmasak##jjgoutput##hkdigunakan##bjr";
            document.getElementById('tanggal').disabled=true;
            document.getElementById('kebundt').disabled=true;
            document.getElementById('afdeling').disabled=true;
            document.getElementById('sNoTrans').value='';
            document.getElementById('blok').value='';
            document.getElementById('seksi').value='';
            document.getElementById('hasisa').value='';
            document.getElementById('haesok').value='';
            document.getElementById('jmlhpokok').value='';
            document.getElementById('persenbuahmatang').value='';
            document.getElementById('jjgmasak').value='';
            document.getElementById('jjgoutput').value='';
            document.getElementById('hkdigunakan').value='';
            document.getElementById('bjr').value='';            
}


function showAdd(){
    document.getElementById('dataList').style.display='none';
    document.getElementById('formData').style.display='block';
    cancelIsi();
}

function showEdit(notrans,tgl,blok){//ind
               
        param='proses=getData'+'&afdeling='+notrans+'&tanggal='+tgl;
        param+='&blok='+blok
        fileTarget='kebun_slave_taksasi';
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    document.getElementById('dataList').style.display='none';
                    document.getElementById('formData').style.display='block';
                    cancelIsi();
					
				//alert(con.responseText);
				   //BJUE04###16-06-2017###BJUE04B004###A###10###15###250###39###456###678###25###9.76######BJUE###BJUE04B004	
					
					/*H01E05###22-03-2014###H01E05H050######7.43###0###0###23###0###0###0###0###*/
					
//echo $rts['afdeling']."###".tanggalnormal($rts['tanggal'])."###".$rts['blok']."###".$rts['seksi']."###".$rts['hasisa']."###".$rts['haesok']."###".$rts['jmlhpokok']."###".$rts['persenbuahmatang']."###".$rts['jjgmasak']."###".$rts['jjgoutput']."###".$rts['hkdigunakan']."###".$rts['bjr'];                    
                    isiDt=con.responseText.split("###");
                    //document.getElementById('afdeling').value=isiDt[0];
                    //document.getElementById('afdeling').innerHTML = isiDt[0];
                    document.getElementById('tanggal').value=isiDt[1];
                    document.getElementById('blok').value = isiDt[14];
                    //document.getElementById('blok').innerHTML = isiDt[14];
                    document.getElementById('seksi').value=isiDt[3];
                    document.getElementById('hasisa').value=isiDt[4];
                    document.getElementById('haesok').value=isiDt[5];
                    document.getElementById('jmlhpokok').value=isiDt[6];
                    document.getElementById('persenbuahmatang').value=isiDt[7];
                    document.getElementById('jjgmasak').value=isiDt[8];
                    document.getElementById('jjgoutput').value=isiDt[9];
                    document.getElementById('hkdigunakan').value=isiDt[10];
                    document.getElementById('bjr').value=isiDt[11];                   
                    //document.getElementById('proses').value='update';
                    document.getElementById('tanggal').disabled=true;
                    document.getElementById('afdeling').disabled=true;
                    document.getElementById('kebundt').disabled=true;
//                     document.getElementById('mandor').disabled=true;

					
					//a=isiDt[0];
					
				//	kbn=substring(a,0,4);
                   //kbn=a.substring(0,4);
					
					
					//kbn=13
					//blok=14
					
					kbn=isiDt[13];
                    document.getElementById('kebundt').value=kbn;
					afd=isiDt[0]
					blok=isiDt[14];
					//alert(kbn);
                    getAfdeling(kbn, afd, blok);
					//getAfdeling(kbn);
                    //getBlok(afd, blok);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
    post_response_text(fileTarget+'.php', param, respon);
                    
}
function deleteData(notrans,tgl,blok){
    param='proses=delete'+'&afdeling='+notrans+'&tanggal='+tgl;
    param+='&blok='+blok
    fileTarget='kebun_slave_taksasi.php';
    if(confirm("Anda Yakin Ingin Menghapus Data Ini?")){
        post_response_text(fileTarget, param, respon);
    }
    
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    loadData(0);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getAfdeling(kbn,afd,blok){
//function getAfdeling(kbn) {
    if(kbn==0||afd==0){
    //if (kbn == 0) {
        dr=document.getElementById('kebundt').options[document.getElementById('kebundt').selectedIndex].value;//ind
		
        kbn=dr;
        param='proses=getAfd'+'&kebun='+kbn;
    }
    else{
        param='proses=getAfd'+'&kebun='+kbn+'&afdeling='+afd+'&blok='+blok;
        //param = 'proses=getAfd' + '&kebun=' + kbn;
       // param+='&mandor='+kary;
    }
    //alert(param);
    
    fileTarget='kebun_slave_taksasi';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//dr=con.responseText.split("####");
					//document.getElementById('afdeling').innerHTML=dr[0];
					//document.getElementById('blok').innerHTML=dr[1];
                    //document.getElementById('sph').value=dr[2];
                    document.getElementById('afdeling').innerHTML = con.responseText;
                    getBlok(kbn, afd, blok);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
     post_response_text(fileTarget+'.php', param, respon);
}
function getBlok(kbn, afd, blok) {
//function getBlok() {
    if (kbn == 0 || afd == 0) {
        dr = document.getElementById('kebundt').options[document.getElementById('kebundt').selectedIndex].value;//ind

        kbn = dr;
        param = 'proses=getBlok' + '&kebun=' + kbn;
    }
    else {
        param = 'proses=getBlok' + '&kebun=' + kbn + '&afdeling=' + afd + '&blok=' + blok;
        //  param = 'proses=getBlok' + '&afdeling=' + afd + '&blok=' + blok;
        // param+='&mandor='+kary;
    }
     //alert(param);

    fileTarget = 'kebun_slave_taksasi';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    dr=con.responseText.split("####");
                    //document.getElementById('afdeling').innerHTML=dr[0];
                    document.getElementById('blok').innerHTML = dr[0];
                    document.getElementById('sph').value=dr[1];
                    //document.getElementById('afdeling').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text(fileTarget + '.php', param, respon);
}
function getSPH() {
    //param='proses=getSph'+'&blok='+getValue('blok');
    param = 'proses=getSph' + '&blok=' + getValue('blok') + '&tanggal=' + getValue('tanggal');
    //alert(param);
    
    fileTarget='kebun_slave_taksasi';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    dr = con.responseText.split("####");
                    //document.getElementById('sph').value=con.responseText;
                    document.getElementById('sph').value = dr[0];
                    document.getElementById('bjr').value = dr[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    post_response_text('kebun_slave_taksasi.php', param, respon);
}