//1625
function getAfd(){
        divId=document.getElementById('kbnId');
        divId=divId.options[divId.selectedIndex].value;
        param='divisiId='+divId+'&proses=getAfd';
        tujuan='log_slave_pengeluaran_mris.php';
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
                                              document.getElementById('afdId').innerHTML=con.responseText;
                                              document.getElementById('periodeId').innerHTML="";
                                              document.getElementById('periodeId').innerHTML=pild;
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
         }  	
}
function getPrd(){
        divId=document.getElementById('afdId');
        divId=divId.options[divId.selectedIndex].value;
        param='afdId='+divId+'&proses=getPrd';
        tujuan='log_slave_pengeluaran_mris.php';
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
                                              document.getElementById('periodeId').innerHTML=con.responseText;
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
         }  	
}
function prevData(){
        nom=document.getElementById('crDataMris').value;
        kbnId=document.getElementById('kbnId');
        kbnId=kbnId.options[kbnId.selectedIndex].value;
        divId=document.getElementById('afdId');
        divId=divId.options[divId.selectedIndex].value;
        prdId=document.getElementById('periodeId');
        prdId=prdId.options[prdId.selectedIndex].value;
        param='afdId='+divId+'&proses=getHeader'+'&periode='+prdId;
        param+='&nomris='+nom+'&kbnId='+kbnId;
        tujuan='log_slave_pengeluaran_mris.php';
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
                                              document.getElementById('formPertama').style.display='block';
                                              document.getElementById('formKedua').style.display='none';
                                              document.getElementById('detailContainer').innerHTML=con.responseText;
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
         }  	
}
function hapusForm(){
        document.getElementById('formPertama').style.display='none';
        document.getElementById('formKedua').style.display='none';
        document.getElementById('detailContainer').innerHTML="";
        document.getElementById('crDataMris').value="";
        document.getElementById('kbnId').value="";
        document.getElementById('afdId').value="";
        document.getElementById('periodeId').value="";
}
function getDetail(notrans){
        param='notransaksi='+notrans+'&proses=getDetail';
        tujuan='log_slave_pengeluaran_mris.php';
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
                                              //alert(con.responseText)
                                              //
                                              hasilajax=con.responseText.split("####");
                                              document.getElementById('formKedua').style.display='block';
                                              document.getElementById('detailContainer2').innerHTML=hasilajax[0];
                                              document.getElementById('kbnId2').innerHTML=hasilajax[1];
                                              document.getElementById('tglPermintaan').innerHTML=hasilajax[2];
                                              document.getElementById('nomris').innerHTML=hasilajax[3];
                                              document.getElementById('gudangId').innerHTML=hasilajax[4];
                                              document.getElementById('periodeStr').innerHTML=hasilajax[5];
                                              document.getElementById('periodeEnd').innerHTML=hasilajax[6];
                                              document.getElementById('tglMulai').value=hasilajax[7];
                                              document.getElementById('tglSelesai').value=hasilajax[8];
                                              document.getElementById('tglKeluar').value=hasilajax[9];
                                              
                                            }
                                    }
                                    else {
                                            busy_off();
                                            error_catch(con.status);
                                    }
                  }	
         }  
}
function donePengeluaran(){
    document.getElementById('formKedua').style.display='none';
    document.getElementById('detailContainer2').innerHTML="";
    document.getElementById('kbnId2').innerHTML="";
    document.getElementById('tglPermintaan').innerHTML="";
    document.getElementById('periodeStr').innerHTML="";
    document.getElementById('periodeEnd').innerHTML="";
    document.getElementById('gudangId').innerHTML="";
    document.getElementById('nomris').innerHTML="";
    document.getElementById('tglMulai').value="";
    document.getElementById('tglSelesai').value="";
    document.getElementById('tglKeluar').value="";
    cariBast();
}
function cekIsi(rowKe){
    jmlhpengeluran=document.getElementById('jmlhPengeluara_'+rowKe).value;
    jmlhPengeluaraPosting=document.getElementById('jmlhPengeluaranPosting_'+rowKe).value;
    jmlhPermintaan=document.getElementById('jmlh_'+rowKe).innerHTML;
    jmlhReal=document.getElementById('realisasiSblm_'+rowKe).innerHTML;
    totRealisasi=parseFloat(jmlhPengeluaraPosting)+parseFloat(jmlhpengeluran);
   if(totRealisasi>jmlhPermintaan ){
        alert("Jumlah pengeluaran lebih besar dari permintaan barang! Atau Realisasi tidak boleh Nol atau kosong");
        document.getElementById('jmlhPengeluara_'+rowKe).value=0;
        return;
    }
}
function saveDt(rowKe,notrans){
    tgl=document.getElementById('tglKeluar').value;
    if(tgl==''){
        alert("Date is obligatory");
        return;
    }
    x=tgl;
    _start=document.getElementById('tglMulai').value;
    _end=document.getElementById('tglSelesai').value;
    while (x.lastIndexOf("-") > -1) {
            x = x.replace("-", "");
    }
    while (x.lastIndexOf("-") > -1) {
        x=x.replace("/","");
    }

    curdateY=x.substr(4,4).toString();
    curdateM=x.substr(2,2).toString();
    curdateD=x.substr(0,2).toString();
    curdate=curdateY+curdateM+curdateD;	
    curdate=parseInt(curdate);
    if (curdate < parseInt(_start) || curdate > parseInt(_end)) {
            alert('Date out of range');
            return;
    }
  
    cekIsi(rowKe);


    kegDt=document.getElementById('kegId_'+rowKe).value;
    kdbrg=document.getElementById('kdBrg_'+rowKe).innerHTML;
    satbrg=document.getElementById('satBrg_'+rowKe).innerHTML;
    kdblk=document.getElementById('kdBlok_'+rowKe).innerHTML;
    kdmesin=document.getElementById('kdMesin_'+rowKe).innerHTML;
     
    
    afd=document.getElementById('kbnId2').innerHTML;
    param='notransaksi='+notrans+'&proses=saveData';
    param+='&kdBarag='+kdbrg+'&satuan='+satbrg+'&afdeling='+afd;
    param+='&tanggal='+tgl+'&kdMesin='+kdmesin+'&kdblok='+kdblk;
    param+='&kegiatan='+kegDt+'&jmlhKeluar='+jmlhpengeluran;
    tujuan='log_slave_pengeluaran_mris.php';
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
                                          //alert(con.responseText)
                                           getPost();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
     }  
   
}
function cariBast(num){
    notr=document.getElementById('txtbabp').value;
    kdGnd=document.getElementById('gdngCr');
    kdGnd=kdGnd.options[kdGnd.selectedIndex].value;
    param='proses=detailLog';
    param+='&page='+num+'&tex='+notr+'&kdGudng='+kdGnd;
    tujuan='log_slave_pengeluaran_mris.php';
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
                                          //alert(con.responseText)
                                          document.getElementById('containerlist').innerHTML=con.responseText;

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
     }  
}
function delXBapb(nodok,nomri)
{
	if(confirm('Deleting Doc: '+nodok+', Are sure..?'))
	{
		param='notransaksi='+nodok+'&proses=delData';
                param+='&nomris='+nomri;
		tujuan='log_slave_pengeluaran_mris.php';//file ini berfungsi untuk penerimaan dan pengeluaran
	   if(confirm('All data in this document will be removed. Continue ?'))
	   {
	   	 post_response_text(tujuan, param, respog);
	   }   
	}
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						cariBast();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}		
}
function previewBast(notransaksi,ev){
   	param='notransaksi='+notransaksi;
	tujuan = 'log_slave_print_bastMris_pdf.php?'+param;	
 //display window
   title=notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
   
}
function getPost(){
    notrns=document.getElementById('nomris').innerHTML;
    tgl=document.getElementById('tglKeluar').value;
    param='notransaksi='+notrns+'&proses=getPostDt';
    param+='&tanggal='+tgl;
    tujuan='log_slave_pengeluaran_mris.php';
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
                                          //alert(con.responseText)
                                          document.getElementById('detailContainer2').innerHTML=con.responseText;
                                          

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
     }  
}