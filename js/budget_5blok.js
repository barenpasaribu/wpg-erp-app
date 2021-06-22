// JavaScript Document

//blok lama
function cekData()
{
        
        thnAng=document.getElementById('thnAnggran').value;
        afdId=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;
	param='thnAngrn='+thnAng+'&afdId='+afdId+'&proses=cekData';
	tujuan='budget_slave_5blok.php';
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
                                  //	alert(con.responseText);
                                  document.getElementById('save_kepala').disabled=true;
                                  document.getElementById('idAfd').disabled=true;
                                  if(con.responseText>=1)
                                      {
                                         if(confirm("Data sudah pernah ada, anda mau edit..?\n Tekan ok untuk mengedit data yang sudah di simpan\n atau tekan cancel untuk mengulang dengan blok tahun lalu"))
                                             {
                                                oldData(con.responseText);        
                                             }
                                             else{
                                                 prevData();
                                             }

                                      }
                                      else
                                      {
                                            prevData();
                                      }
                                        //document.getElementById('isiContainer').innerHTML=con.responseText;

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
	
}
function prevData()
{
	
        thnAng=document.getElementById('thnAnggran').value;
        afdId=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;
	param='thnAngrn='+thnAng+'&afdId='+afdId+'&proses=getPreview'+'&jmlh='+0;
	tujuan='budget_slave_5blok.php';
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
						  //	alert(con.responseText);
                                                        document.getElementById('save_kepala').disabled=true;
                                                        document.getElementById('thnAnggran').disabled=true;
                                                        document.getElementById('idAfd').disabled=true;
							document.getElementById('isiContainer').innerHTML=con.responseText;
							document.getElementById('dataList').style.display='block';
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
		
}

function oldData(x)
{
	
        thnAng=document.getElementById('thnAnggran').value;
        afdId=document.getElementById('idAfd').options[document.getElementById('idAfd').selectedIndex].value;
	param='thnAngrn='+thnAng+'&afdId='+afdId+'&proses=getPreview'+'&jmlh='+x;
	tujuan='budget_slave_5blok.php';
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
                                          //	alert(con.responseText);
                                                document.getElementById('save_kepala').disabled=true;
                                                document.getElementById('thnAnggran').disabled=true;
                                                document.getElementById('isiContainer').innerHTML=con.responseText;
                                                document.getElementById('dataList').style.display='block';
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function editData(x,thn,afd,smbr){
        thnAng=thn;
        afdId=afd;
        sumber=smbr;
        document.getElementById('thnAnggran').value=thn;
        document.getElementById('idAfd').value=afd;
	param='thnAngrn='+thnAng+'&afdId='+afdId+'&proses=getPreview'+'&jmlh='+x+'&sumber='+sumber;
	tujuan='budget_slave_5blok.php';
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
                                          //	alert(con.responseText);
                                                document.getElementById('save_kepala').disabled=true;
                                                document.getElementById('thnAnggran').disabled=true;
                                                document.getElementById('idAfd').disabled=true;
                                                document.getElementById('isiContainer').innerHTML=con.responseText;
                                                document.getElementById('dataList').style.display='block';
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}

function batal()
{
    document.getElementById('save_kepala').disabled=false;
    document.getElementById('thnAnggran').disabled=false;  
    document.getElementById('idAfd').disabled=false;
    document.getElementById('idAfd').value='';  
    document.getElementById('thnAnggran').value='';  
    document.getElementById('dataList').style.display='none';
    document.getElementById('isiContainer').innerHTML='';
}

function saveAll(x)
{
 
	thnAng=document.getElementById('thnAnggran').value;
	kBlok=document.getElementById('kdBlok_'+x).innerHTML;
	haThnLalu=document.getElementById('luas_'+x).innerHTML;
        haThnIni=document.getElementById('hathnIni_'+x).value;
        pkkThnLalu=document.getElementById('pkk_'+x).innerHTML;
        pokokThnIni=document.getElementById('pokokThnINi_'+x).value;
        statBlok=document.getElementById('statBlok_'+x).options[document.getElementById('statBlok_'+x).selectedIndex].value;
        topoGrafi=document.getElementById('topoGrafi_'+x).innerHTML;
        thnTmn=document.getElementById('thnTmn_'+x).innerHTML;
        lcThnini=document.getElementById('lcThn_'+x).value;
        haNon=document.getElementById('haNon_'+x).value;
        pkkProduktif=document.getElementById('pkkProduk_'+x).value;
        totRow=document.getElementById('jmlhRow').value;
        plsma=document.getElementById('statPlasma_'+x);
        
        ar=topoGrafi.split("-");
	param='proses=insertAll'+'&thnAngrn='+thnAng+'&haThnLalu='+haThnLalu+'&kdBlok='+kBlok;
	param+='&haThnIni='+haThnIni+'&pkkThnLalu='+pkkThnLalu+'&pokokThnIni='+pokokThnIni+'&lcThnini='+lcThnini;
        param+='&statBlok='+statBlok+'&topoGrafi='+ar[0]+'&thnTmn='+thnTmn+'&haNon='+haNon+'&pkkProduktif='+pkkProduktif;
        if(plsma.checked==true)
        {
            param+='&statPlsma=P';
        }
	//alert(param);
	tujuan='budget_slave_5blok.php';
	if(x==1 && confirm('Anda Yakin Melakukan Proses Ini?'))
        post_response_text(tujuan, param, respog);
        else
        post_response_text(tujuan, param, respog);
		 document.getElementById('rew_'+x).style.backgroundColor='orange';
	function respog()
    {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
							document.getElementById('rew_'+x).style.backgroundColor='red';
//                                                        alert("Lanjut");
//                                                        saveAll(x);
                    }
                    else {
                           // alert(con.responseText);
                          //document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                        b=x;
                        row=x+1;
                        x=row;
                        if(x<=totRow)
                         {   
			     document.getElementById('rew_'+b).style.backgroundColor='green';
                             saveAll(x);
                         }
                         else
                         {
                             //displayList();
                             document.getElementById('rew_'+b).style.backgroundColor='green';
                             loadDataLama();
                             batal();
                           // alert('Done');
                         }
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    

}
function getDatab(c)
{
    isi=document.getElementById('pkk_'+c).innerHTML;
    document.getElementById('pokokThnINi_'+c).value=isi;
}
function getData(b)
{
    isi=document.getElementById('luas_'+b).innerHTML;
    document.getElementById('hathnIni_'+b).value=isi;
}

//blok baru
function cekDataBr()
{
        thnAngBr=document.getElementById('thnAnggranBr').value;
        afdIdBr=document.getElementById('idAfdBr').options[document.getElementById('idAfdBr').selectedIndex].value;
       // jmlh=document.getElementById('jmlhRow').value;
	param='thnAngBr='+thnAngBr+'&afdIdBr='+afdIdBr+'&proses=cekDataBr'+'&jmlh=1';
        //alert(param);
	tujuan='budget_slave_5blok.php';
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
                                    //ar=con.responseText.split("###");
                                    document.getElementById('isiContainerBr').innerHTML=con.responseText;
                                    //document.getElementById('isiContainerBr').innerHTML=ar[0];
                                    document.getElementById('dataListBr').style.display='block';
                                    document.getElementById('save_kepalaBr').disabled=true;
                                    document.getElementById('thnAnggranBr').disabled=true;
                                    document.getElementById('idAfdBr').disabled=true;
                                    
                                  //	alert(con.responseText);
                                 
//                                  if(con.responseText>=1)
//                                      {
//                                         if(confirm("Data sudah pernah ada, anda mau edit..?\n Tekan ok untuk mengedit data yang sudah di simpan\n atau tekan cancel untuk mengulang dengan blok tahun lalu"))
//                                             {
//                                                oldDataBr(con.responseText);        
//                                             }
//                                             else{
//                                                 prevDataBr();
//                                             }
//
//                                      }
//                                      else
//                                      {
//                                            prevDataBr();
//                                      }
                                        //document.getElementById('save_kepalaBr').disabled=true;
                                        //document.getElementById('isiContainer').innerHTML=con.responseText;

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
	
}
function prevDataBr()
{
	
        thnAngBr=document.getElementById('thnAnggranBr').value;
        afdIdBr=document.getElementById('idAfdBr').options[document.getElementById('idAfdBr').selectedIndex].value;
	param='thnAngBr='+thnAngBr+'&afdIdBr='+afdIdBr+'&proses=getPreviewBr'+'&jmlh='+0;
	tujuan='budget_slave_5blok.php';
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
						  //	alert(con.responseText);
							document.getElementById('isiContainerBr').innerHTML=con.responseText;
							document.getElementById('dataListBr').style.display='block';
                                                        document.getElementById('save_kepalaBr').disabled=true;
                                                        document.getElementById('thnAnggranBr').disabled=true;
                                                        document.getElementById('idAfdBr').disabled=true;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  
		
}

function oldDataBr(x)
{
        thnAngBr=document.getElementById('thnAnggranBr').value;
        afdIdBr=document.getElementById('idAfdBr').options[document.getElementById('idAfdBr').selectedIndex].value;
	param='thnAngBr='+thnAngBr+'&afdIdBr='+afdIdBr+'&proses=getPreviewBr'+'&jmlh='+x;
	tujuan='budget_slave_5blok.php';
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
                                          //	alert(con.responseText);
                                               
                                                    document.getElementById('isiContainerBr').innerHTML=con.responseText;
                                                    document.getElementById('dataListBr').style.display='block';
                                                    document.getElementById('save_kepalaBr').disabled=true;
                                                    document.getElementById('thnAnggranBr').disabled=true;
                                                    document.getElementById('idAfdBr').disabled=true;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function loadData()
{
    afdIdBr=document.getElementById('idAfdBr').options[document.getElementById('idAfdBr').selectedIndex].value;
    thnAngBr=document.getElementById('thnAnggranBr').value;
    param='proses=loadData'+'&afdIdBr='+afdIdBr+'&thnAngBr='+thnAngBr;
    tujuan='budget_slave_5blok.php';
    post_response_text(tujuan, param, respog);
    function respog()
        {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
							document.getElementById('rew_'+x).style.backgroundColor='red';
                    }
                    else {
                           // alert(con.responseText);
                          document.getElementById('containDetail').innerHTML=con.responseText;
                          getThnBudgt();
                       
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}

function batalBr()
{
    document.getElementById('save_kepalaBr').disabled=false;
    document.getElementById('thnAnggranBr').disabled=false;  
    document.getElementById('idAfdBr').disabled=false;
    document.getElementById('idAfdBr').value='';  
    document.getElementById('thnAnggranBr').value='';
    document.getElementById('dataListBr').style.display='none';
    document.getElementById('isiContainerBr').innerHTML='';
   // document.getElementById('containDetail').innerHTML='';
}

function saveAllBr(x){
	thnAngBr=document.getElementById('thnAnggranBr').value;
        idAfdBr=document.getElementById('idAfdBr').options[document.getElementById('idAfdBr').selectedIndex].value;
	kBlokBr=document.getElementById('kdBlokBr_'+x).value;
        kBlokBr2=idAfdBr+kBlokBr;
        haThnIniBr=document.getElementById('hathnIniBr_'+x).value;
        pokokThnIniBr=document.getElementById('pokokThnINiBr_'+x).value;
        statBlokBr=document.getElementById('statBlokBr_'+x).options[document.getElementById('statBlokBr_'+x).selectedIndex].value;
        topoGrafiBr=document.getElementById('topoGrafiBr_'+x).options[document.getElementById('topoGrafiBr_'+x).selectedIndex].value;
        thnTmnBr=document.getElementById('thnTmnBr_'+x).value;
       lcThnBr=document.getElementById('lcThnBr_'+x).value;
        haNonBr=document.getElementById('haNonBr_'+x).value;
        pkkProdukBr=document.getElementById('pkkProdukBr_'+x).value;
        totRow=document.getElementById('jmlhRow').value;
        thnAngrnOld=document.getElementById('thnAngrnOld').value;
        oldBlok=document.getElementById('oldBlok').value;
        plsma=document.getElementById('statPlasmaBr_'+x);
        topoGrafOld=document.getElementById('topoGrafOld').value;
        if(topoGrafOld=='')
        {
            topoGrafOld=topoGrafiBr;
        }
        if(thnAngrnOld=='')
        {
            thnAngrnOld=thnAngBr;
        }
        if(oldBlok=='')
        {
            oldBlok=kBlokBr;
        }
	param='proses=insertAllBr'+'&thnAngBr='+thnAngBr+'&kdBlokBr='+kBlokBr2;
	param+='&haThnIniBr='+haThnIniBr+'&pokokThnIniBr='+pokokThnIniBr+'&lcThnBr='+lcThnBr;
        param+='&statBlokBr='+statBlokBr+'&topoGrafiBr='+topoGrafiBr+'&thnTmnBr='+thnTmnBr+'&haNonBr='+haNonBr;
        param+='&thnAngrnOld='+thnAngrnOld+'&oldBlok='+oldBlok+'&topoGrafOld='+topoGrafOld+'&pkkProdukBr='+pkkProdukBr;
        if(plsma.checked==true)
            {
                param+='&statPlasmaBr=P';
            }
	//alert(param);
	tujuan='budget_slave_5blok.php';
	if(x==1 && confirm('Anda Yakin Melakukan Proses Ini?'))
        {post_response_text(tujuan, param, respog);}
        else if(x!=1)
        {post_response_text(tujuan, param, respog);}
		// document.getElementById('rewBr_'+x).style.backgroundColor='orange';
	function respog()
        {
          if(con.readyState==4)
          {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
							//document.getElementById('rew_'+x).style.backgroundColor='red';
                    }
                    else {
                           // alert(con.responseText);
                          //document.getElementById('container').innerHTML=con.responseText;
                            //return con.responseText;
                        b=x;
                        row=x+1;
                        x=row;
                        if(x<=totRow)
                         {   
			     document.getElementById('rewBr_'+b).style.backgroundColor='green';
                             saveAllBr(x);
                         }
                         else
                         {
                             //displayList();
                             //document.getElementById('rewBr_'+b).style.backgroundColor='green';
                           
                            // batalBr();
                            document.getElementById('thnAngrnOld').value='';
                            document.getElementById('oldBlok').value='';
                            document.getElementById('topoGrafOld').value='';
                            document.getElementById('thnTmnBr_'+b).value='';
                            document.getElementById('kdBlokBr_'+b).value='';
                            document.getElementById('hathnIniBr_'+b).value='0';
                            document.getElementById('hathnIniBr_'+b).value='0';
                            document.getElementById('pokokThnINiBr_'+b).value='0';
                            document.getElementById('statBlokBr_'+b).value='TM';
                            document.getElementById('topoGrafiBr_'+b).value='B1';
                            document.getElementById('pkkProdukBr_'+b).value='0';
                            loadData();
                           // alert('Done');
                         }
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    

}
function fillField(thnbdgt,blokKd,smbr,tpgrafi)
{
    afdeling=blokKd.substring(0,6);
    afd=blokKd;
    param='proses=getData'+'&kdBlokBr='+blokKd+'&thnAngBr='+thnbdgt+'&sumber='+smbr;
    param+='&topoGrafiBr='+tpgrafi+'&jmlh='+0+'&afdIdBr='+afd;
    //alert(param);
    tujuan='budget_slave_5blok.php';
    
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
                            document.getElementById('thnAnggranBr').value=thnbdgt;
                            document.getElementById('idAfdBr').value=afdeling;
                           
                            document.getElementById('isiContainerBr').innerHTML=con.responseText;
                            document.getElementById('dataListBr').style.display='block';
                            document.getElementById('save_kepalaBr').disabled=true;
                            document.getElementById('thnAnggranBr').disabled=true;
                            document.getElementById('idAfdBr').disabled=true;
                            //return con.responseText;
                         }
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
      	    
    
}
function prosesClose()
{
    thnBudget=document.getElementById('thnBudget').options[document.getElementById('thnBudget').selectedIndex].value;
    param='thnAngBr='+thnBudget+'&proses=prosesClose';
    tujuan='budget_slave_5blok.php';
    if(confirm("Anda Yakin Melakukan Proses Ini \n Setelah Ini Data Tidak Dapat Di Ubah"))
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
                           if(con.responseText==1)
                            {
                                alert("Done: Berhasil");
                             document.getElementById('thnBudget').value='';
                             idtFrm=document.getElementById('tabFRM0');
                             tabAction(idtFrm,0,'FRM',2);
                             loadDataLama();
                            }
                         }
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
    
}
function addDetail(b)
{
    saveAllBr(b);
}
function cariBast(num)
{
        param='proses=loadData';
        param+='&page='+num;
        tujuan='budget_slave_5blok.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('containDetail').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function loadDataLama()
{
    param='proses=loadDataLama';
    tujuan='budget_slave_5blok.php';
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
                          document.getElementById('containData').innerHTML=con.responseText;
                          loadData();
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
     } 	    
}
function cariLoad(num)
{
        param='proses=loadDataLama';
        param+='&page='+num;
        tujuan='budget_slave_5blok.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('containData').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
    width='300';
    height='100';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function datakeExcel(ev,thndget,blkid,smbr)
{
        param='proses=printExcel'+'&thnAngrn='+thndget+'&sumber='+smbr+'&afdId='+blkid;
        //alert(param);
	tujuan='budget_slave_5blok.php';
	judul='List Data';		
	printFile(param,tujuan,judul,ev)	
}
function datakeExcel2(ev,thndget,blkid,smbr)
{
        param='proses=printExcel2'+'&thnAngrn='+thndget+'&sumber='+smbr+'&afdId='+blkid;
        //alert(param);
	tujuan='budget_slave_5blok.php';
	judul='List Data';		
	printFile(param,tujuan,judul,ev)	
}
function cekThis(j)
{
    
    pkkThnIni=document.getElementById('pokokThnINi_'+j).value;
    pkkProduktip=document.getElementById('pkkProduk_'+j).value;
    //alert("test__"+pkkProduktip+"__"+pkkThnIni);
    if(parseFloat(pkkProduktip)>parseFloat(pkkThnIni))
    {
        alert("Pokok Produktif Tidak Boleh Lebih Besar Dari Pokok Tahun Ini");
        document.getElementById('pkkProduk_'+j).value=pkkThnIni;
        document.getElementById('pkkProduk_'+j).focus();
        return;
    }
}
function cekThis(s)
{
    
    pkkThnIni=document.getElementById('hathnIniBr_'+s).value;
    pkkProduktip=document.getElementById('pkkProdukBr_'+s).value;
    //alert("test__"+pkkProduktip+"__"+pkkThnIni);
    if(parseFloat(pkkProduktip)>parseFloat(pkkThnIni))
    {
        alert("Pokok Produktif Tidak Boleh Lebih Besar Dari Pokok Tahun Ini");
        document.getElementById('pkkProdukBr_'+s).value=pkkThnIni;
        document.getElementById('pkkProdukBr_'+s).focus();
        return;
    }
}
function getThnBudgt()
{
    param='proses=getThnBudgt';
    tujuan='budget_slave_5blok.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                if (con.readyState == 4) {
                        if (con.status == 200) {
                                busy_off();
                                if (!isSaveResponse(con.responseText)) {
                                        alert('ERROR TRANSACTION,\n' + con.responseText);
                                }
                                else {
                                        document.getElementById('thnBudget').innerHTML=con.responseText;
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
                }
        }
}