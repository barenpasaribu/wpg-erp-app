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
    tujuan='sdm_slave_permintaankerja.php';
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
    tujuan='sdm_slave_permintaankerja.php';
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
                        loadData();
                        cancelIsi();
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
//    var currentTime = new Date()
//    var month = currentTime.getMonth() + 1
//    var day = currentTime.getDate()
//    var year = currentTime.getFullYear()
//    document.write(day + "-" + month + "-" + year)
            document.getElementById('notransaksi').value='';
            document.getElementById('kodeorg').value='';
            document.getElementById('departemen').disabled=false;
            document.getElementById('pendidikan').disabled=false;
            document.getElementById('kodeorg').disabled=false;
            document.getElementById('penempatan').disabled=false;
            document.getElementById('penempatan').value='';
            document.getElementById('departemen').value='';
            document.getElementById('kotapenempatan').value='';
            document.getElementById('pendidikan').value='';
            document.getElementById('jurusan').value='';
            document.getElementById('pengalaman').value='';
            document.getElementById('kompetensi').value='';
            document.getElementById('deskpekerjaan').value='';
            document.getElementById('maxumur').value='';
            document.getElementById('persetujuan1').value='';
            document.getElementById('persetujuan2').value='';
            document.getElementById('proses').value='insert';
            document.getElementById('persetujuanhrd').value='';
            document.getElementById('tgldibutuhkan').value='';
            document.getElementById('nmlowongan').value='';
            
           // document.getElementById('tanggal').value=currentTime;
}
 
function showEdit(notrans){
               
        param='proses=getData'+'&notransaksi='+notrans;
        fileTarget='sdm_slave_permintaankerja';
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
//                    document.getElementById('dataList').style.display='none';
//                    document.getElementById('formData').style.display='block';
                    cancelIsi();
//echo $rts['notransaksi']."###".$rts['kodeorg']."###".$rts['penempatan']."###".tanggalnormal($rts['tanggal'])."###".tanggalnormal($rts['tgldibutuhkan'])."###".$rts['kotapenempatan']."###".$rts['pendidikan']."###".$rts['jurusan']."###".$rts['pengalaman']."###".$rts['kompetensi']."###".$rts['deskpekerjaan']."###".$rts['maxumur']."###".$rts['persetujuan1']."###".$rts['persetujuan2']."###".$rts['persetujuanhrd'];
                    isiDt=con.responseText.split("###");
                    document.getElementById('notransaksi').value=isiDt[0];
                    document.getElementById('kodeorg').value=isiDt[1];
                    document.getElementById('penempatan').value=isiDt[2];
                    document.getElementById('departemen').value=isiDt[3];
                    document.getElementById('kotapenempatan').value=isiDt[4];
                    document.getElementById('tgldibutuhkan').value=isiDt[6];
                    document.getElementById('tanggal').value=isiDt[5];
                    document.getElementById('pendidikan').value=isiDt[7];
                    document.getElementById('jurusan').value=isiDt[8];
                    document.getElementById('pengalaman').value=isiDt[9];
                    document.getElementById('kompetensi').value=isiDt[10];
                    document.getElementById('deskpekerjaan').value=isiDt[11];
                    document.getElementById('maxumur').value=isiDt[12];
                    document.getElementById('persetujuan1').value=isiDt[13];
                    document.getElementById('persetujuan2').value=isiDt[14];
                    document.getElementById('persetujuanhrd').value=isiDt[15];          
                    document.getElementById('nmlowongan').value=isiDt[16];
                    document.getElementById('proses').value='update';
                    
                   
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
function deleteData(notrans){
    param='proses=delete'+'&notransaksi='+notrans;
    
    fileTarget='sdm_slave_permintaankerja.php';
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
function getAfdeling(kbn,afd,kary){
    if(kbn==0||afd==0||kary==0){
        dr=document.getElementById('kebundt').options[document.getElementById('kebundt').selectedIndex].value;
        kbn=dr;
        param='proses=getAfd'+'&kebun='+kbn;
    }
    else{
        param='proses=getAfd'+'&kebun='+kbn+'&afdeling='+afd;
        param+='&mandor='+kary;
    }
    
    
    fileTarget='sdm_slave_permintaankerja';
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                     dr=con.responseText.split("###");
                     document.getElementById('afdeling').innerHTML=dr[0];
                     document.getElementById('mandor').innerHTML=dr[1];
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
     post_response_text(fileTarget+'.php', param, respon);
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