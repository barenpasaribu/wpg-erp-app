// JavaScript Document

function searchSupplier(title,content,ev)
{
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
	//alert('asdasd');
}
function findSupplier()
{
    nmSupplier=document.getElementById('nmSupplier').value;
    param='proses=getSupplierNm'+'&nmSupplier='+nmSupplier;
    tujuan='log_slave_save_po_lokal.php';
    post_response_text(tujuan, param, respog);			

    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                  document.getElementById('containerSupplier').innerHTML=con.responseText;
                        }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }	
}
function setData(kdSupp)
{
    l=document.getElementById('supplier_id');
    
    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }
       closeDialog();
}
function cancelData1()
{
        document.getElementById('batch').value='';
        document.getElementById('kodeorgBibitan').value='';
        document.getElementById('kodeorgBibitan').disabled=false;
        document.getElementById('tglTnm').disabled=false;
        document.getElementById('jmlhBibitan').value='0';
        document.getElementById('afkirKcmbh').value='0';
        document.getElementById('jmlhTrima').value='0';
        document.getElementById('jmlh').value='0';
        document.getElementById('nodo').value='';
        document.getElementById('ket').value='';
        document.getElementById('tglTnm').value='';
        document.getElementById('jnsBibitan').value='';
        document.getElementById('supplier_id').value='';
        document.getElementById('tgl2').value='';
        document.getElementById('proses1').value='saveTab1';
}
function cancelData2()
{
        document.getElementById('batchTp').value='';
        document.getElementById('batchTp').disabled=false;
        document.getElementById('kodeOrgTp').value='';
        document.getElementById('kodeOrgTp').disabled=false;
        document.getElementById('tglTp').disabled=false;
        document.getElementById('tglTp').value='';
        document.getElementById('jmlhTpBbtn').value='0';
        document.getElementById('tglTp').value='';
        document.getElementById('kodeOrgTjnTp').value='';
        document.getElementById('kodeOrgTjnTp').disabled=false;
        document.getElementById('supplier_id').value='';
        document.getElementById('ketTp').value='';
        document.getElementById('proses1').value='saveTab2';
}
function cancelData3()
{
       
        document.getElementById('batchAfk').value='';
        document.getElementById('batchAfk').disabled=false;
        document.getElementById('kdOrgAfk').value='';
        document.getElementById('kdOrgAfk').disabled=false;
        document.getElementById('tglAfkirBibit').disabled=false;
        document.getElementById('jmlhAfk').value='0';
        document.getElementById('ketAfk').value='';
        document.getElementById('tglAfkirBibit').value='';
        document.getElementById('proses3').value='saveTab3';
}
function cancelData5()
{
       
        document.getElementById('batchDbt').value='';
        document.getElementById('batchDbt').disabled=false;
        document.getElementById('kdOrgDbt').value='';
        document.getElementById('kdOrgDbt').disabled=false;
        document.getElementById('tglAfkirBibit').disabled=false;
        document.getElementById('jmlhDbt').value='0';
        document.getElementById('ketDbt').value='';
        document.getElementById('tglDbt').value='';
        document.getElementById('proses5').value='saveTab5';
}
function cancelData7()
{
        document.getElementById('batchPnb').value='';
        document.getElementById('batchPnb').disabled=false;
        document.getElementById('kdOrgPnb').value='';
        document.getElementById('kdOrgPnb').disabled=false;
        document.getElementById('tglPnb').disabled=false;
        document.getElementById('jmlhPnb').value='0';
        document.getElementById('ketPnb').value='';
        document.getElementById('tglPnb').value='';
        document.getElementById('kdvhc').value='';
        document.getElementById('nmSupir').value='';
        document.getElementById('intexDt').value='';
        document.getElementById('custId').innerHTML="<option value=''>"+pilh+"</option>";
        document.getElementById('kdAfdeling').innerHTML="<option value=''>"+pilh+"</option>";
        document.getElementById('detPeng').value='';
        document.getElementById('assistenPnb').value='';
        document.getElementById('kegId').value='';
        
        document.getElementById('proses7').value='saveTab7';
}
function saveData(sTab)
{
        if(sTab=='1')
        {
           kodeTrans=document.getElementById('kdTransaksi').value;
           batchVar=document.getElementById('batch').value;
           kdOrg=document.getElementById('kodeorgBibitan').options[document.getElementById('kodeorgBibitan').selectedIndex].value;
           jmlhBibitan=document.getElementById('jmlhBibitan').value;
           ket=trim(document.getElementById('ket').value);
           tglTnm=document.getElementById('tglTnm').value;
           jnsBibitan=document.getElementById('jnsBibitan').options[document.getElementById('jnsBibitan').selectedIndex].value;
           supplierid=document.getElementById('supplier_id').options[document.getElementById('supplier_id').selectedIndex].value;
           tglProduksi=document.getElementById('tgl2').value;
           proses1=document.getElementById('proses1').value;
           oldJenisBibit=document.getElementById('oldJnsbibit').value;
           nodo=document.getElementById('nodo').value;
           jmlhdDo=document.getElementById('jmlh').value;
           jmlhTrima=document.getElementById('jmlhTrima').value;
           afkirKcmbh=document.getElementById('afkirKcmbh').value;
           param='kodeTrans='+kodeTrans+'&batchVar='+batchVar+'&kdOrg='+kdOrg+'&jmlhBibitan='+jmlhBibitan+'&tglTnm='+tglTnm;
           param+='&ket='+ket+'&jnsBibitan='+jnsBibitan+'&supplierid='+supplierid+'&tglProduksi='+tglProduksi+'&proses='+proses1;
           param+='&jmlhTrima='+jmlhTrima+'&nodo='+nodo+'&afkirKcmbh='+afkirKcmbh+'&jmlhdDo='+jmlhdDo;
           if(oldJenisBibit!='')
           {
               param+='&oldJenisBibit='+oldJenisBibit;
           }
        }
        else if(sTab=='2')
        {
           kodeTrans=document.getElementById('kdTransaksiTp').value;
           batchVar=document.getElementById('batchTp').options[document.getElementById('batchTp').selectedIndex].value;
           kdOrg=document.getElementById('kodeOrgTp').options[document.getElementById('kodeOrgTp').selectedIndex].value;
           kdOrgTjn=document.getElementById('kodeOrgTjnTp').options[document.getElementById('kodeOrgTjnTp').selectedIndex].value;
           jmlhBibitan=document.getElementById('jmlhTpBbtn').value;
           ket=document.getElementById('ketTp').value;
           tglTnm=document.getElementById('tglTp').value;
           proses2=document.getElementById('proses2').value;
           param='kodeTrans='+kodeTrans+'&batchVar='+batchVar+'&kdOrg='+kdOrg+'&jmlhBibitan='+jmlhBibitan+'&tglTnm='+tglTnm;
           param+='&ket='+ket+'&kdOrgTjn='+kdOrgTjn+'&proses='+proses2;
        }
         else if(sTab=='3')
        {
           kodeTrans=document.getElementById('kdTransAfk').value;
           batchVar=document.getElementById('batchAfk').options[document.getElementById('batchAfk').selectedIndex].value;
           kdOrg=document.getElementById('kdOrgAfk').options[document.getElementById('kdOrgAfk').selectedIndex].value;
           jmlhBibitan=document.getElementById('jmlhAfk').value;
           ket=document.getElementById('ketAfk').value;
           tglTnm=document.getElementById('tglAfkirBibit').value;
           proses3=document.getElementById('proses3').value;
           param='kodeTrans='+kodeTrans+'&batchVar='+batchVar+'&kdOrg='+kdOrg+'&jmlhBibitan='+jmlhBibitan+'&tglTnm='+tglTnm;
           param+='&ket='+ket+'&proses='+proses3;
        }
        else if(sTab=='5')
        {
           kodeTrans=document.getElementById('kdTransaksiDbt').value;
           batchVar=document.getElementById('batchDbt').options[document.getElementById('batchDbt').selectedIndex].value;
           kdOrg=document.getElementById('kdOrgDbt').options[document.getElementById('kdOrgDbt').selectedIndex].value;
           jmlhBibitan=document.getElementById('jmlhDbt').value;
           ket=document.getElementById('ketDbt').value;
           tglTnm=document.getElementById('tglDbt').value;
           proses5=document.getElementById('proses5').value;
           param='kodeTrans='+kodeTrans+'&batchVar='+batchVar+'&kdOrg='+kdOrg+'&jmlhBibitan='+jmlhBibitan+'&tglTnm='+tglTnm;
           param+='&ket='+ket+'&proses='+proses5;
        }
        else if(sTab=='7')
        {
           kodeTrans=document.getElementById('kdTransPnb').value;
           batchVar=document.getElementById('batchPnb').options[document.getElementById('batchPnb').selectedIndex].value;
           kdOrg=document.getElementById('kdOrgPnb').options[document.getElementById('kdOrgPnb').selectedIndex].value;
           jmlhBibitan=document.getElementById('jmlhPnb').value;
           ket=document.getElementById('ketPnb').value;
           tglTnm=document.getElementById('tglPnb').value;
           kdvhc=trim(document.getElementById('kdvhc').value);
           nmSupir=document.getElementById('nmSupir').value;
           intexDt=document.getElementById('intexDt').options[document.getElementById('intexDt').selectedIndex].value;
           custId=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
           detPeng=document.getElementById('detPeng').value;
           assistenPnb=document.getElementById('assistenPnb').options[document.getElementById('assistenPnb').selectedIndex].value;
           KegiatanId=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
           kodeAfd=document.getElementById('kdAfdeling').options[document.getElementById('kdAfdeling').selectedIndex].value;
           jmlRit=trim(document.getElementById('jmlRit').value);
           proses7=document.getElementById('proses7').value;
           param='kodeTrans='+kodeTrans+'&batchVar='+batchVar+'&kdOrg='+kdOrg+'&jmlhBibitan='+jmlhBibitan+'&tglTnm='+tglTnm;
           param+='&kdvhc='+kdvhc+'&nmSupir='+nmSupir+'&intexDt='+intexDt+'&detPeng='+detPeng+'&assistenPnb='+assistenPnb;
           param+='&ket='+ket+'&proses='+proses7+'&custId='+custId+'&kodeAfd='+kodeAfd+'&KegiatanId='+KegiatanId;
           param+='&jmlRit='+jmlRit;
          }
	tujuan='bibit_slave_keluar_masuk.php';
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
                                  if(sTab=='1')
                                  {
                                      loadData1(2);
                                      cancelData1();
                                  }
                                  else if(sTab=='2')
                                  {
                                      loadData2(2);
                                      cancelData2();
                                  }
                                  else if(sTab=='3')
                                  {
                                      loadData3(2);
                                      cancelData3();
                                  }
                                  else if(sTab=='5')
                                  {
                                      loadData5(2);
                                      cancelData5();
                                  }
                                  else if(sTab=='7')
                                  {
                                      loadData7(2);
                                      cancelData7();
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
function getBatchForAll()
{
    param='proses=getBatch';
    tujuan='bibit_slave_keluar_masuk.php';
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
                            
                                        document.getElementById('batchTp').innerHTML=con.responseText;
                                        document.getElementById('batchAfk').innerHTML=con.responseText;
                                        document.getElementById('batchDbt').innerHTML=con.responseText
                                        document.getElementById('batchPnb').innerHTML=con.responseText;

                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }	
     }   
}

var mulai=1;
function loadData1(stat)
{
    statCar=document.getElementById('statCari2').options[document.getElementById('statCari2').selectedIndex].value;
    batchCar=document.getElementById('batchCari2').value;
    tglCar=document.getElementById('tglCari2').value;
    
    param='proses=loadData1';
    param+='&statCari2='+statCar+'&batchCari2='+batchCar+'&tglCari2='+tglCar;
//    alert(param);
    tujuan='bibit_slave_keluar_masuk.php';
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
                             if(stat==2)
                                 {
                                      document.getElementById('containData1').innerHTML=con.responseText;
                                      getBatchForAll();
                                 }
                                 else
                                 {

                                    if(mulai==1)
                                    {
                                      document.getElementById('containData1').innerHTML=con.responseText;
                                      loadData2(mulai);
                                      mulai=0;
                                    }
                                    else
                                    {
                                        document.getElementById('containData1').innerHTML=con.responseText;
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
}
function cariBast(num)
{
                statCar=document.getElementById('statCari2').options[document.getElementById('statCari2').selectedIndex].value;
                batchCar=document.getElementById('batchCari2').value;
                tglCar=document.getElementById('tglCari2').value;
                param='proses=loadData1';
                param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
		param+='&page='+num;
                tujuan='bibit_slave_keluar_masuk.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containData1').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//load data no 1

//load data no 2
function loadData2(stat)
{
    statCar=document.getElementById('statCari3').options[document.getElementById('statCari3').selectedIndex].value;
    batchCar=document.getElementById('batchCari3').value;
    tglCar=document.getElementById('tglCari3').value;
    param='proses=loadData2';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
    tujuan='bibit_slave_keluar_masuk.php';
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
                             if(stat==1)
                                 {
                                     document.getElementById('containData2').innerHTML=con.responseText;
                                     loadData3(stat);
                                 }
                                 else if(stat==2)
                                 {
                                     document.getElementById('containData2').innerHTML=con.responseText;
                                     loadDataStock(2);
                                 }
                                 else
                                     {
                                         document.getElementById('containData2').innerHTML=con.responseText;
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


function cariBast2(num)
{
                statCar=document.getElementById('statCari3').options[document.getElementById('statCari3').selectedIndex].value;
                batchCar=document.getElementById('batchCari3').value;
                tglCar=document.getElementById('tglCari3').value;
                param='proses=loadData2';
                param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
		param+='&page='+num;
                tujuan='bibit_slave_keluar_masuk.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containData2').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//end load data no 2
//load data no 3
function loadData3(stat)
{
    statCar=document.getElementById('statCari4').options[document.getElementById('statCari4').selectedIndex].value;
    batchCar=document.getElementById('batchCari4').value;
    tglCar=document.getElementById('tglCari4').value;
    param='proses=loadData3';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
    tujuan='bibit_slave_keluar_masuk.php';
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
                             if(stat==1)
                                 {
                                     document.getElementById('containData3').innerHTML=con.responseText;
                                     loadData7(stat);
                                 }
                                 else if(stat==2)
                                 {
                                     document.getElementById('containData3').innerHTML=con.responseText;
                                     loadDataStock();
                                 }
                                 else
                                     {
                                         document.getElementById('containData3').innerHTML=con.responseText;
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
function cariBast3(num)
{
    statCar=document.getElementById('statCari4').options[document.getElementById('statCari4').selectedIndex].value;
    batchCar=document.getElementById('batchCari4').value;
    tglCar=document.getElementById('tglCari4').value;
    param='proses=loadData3';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
    param+='&page='+num;
    tujuan='bibit_slave_keluar_masuk.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containData3').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//end load data no 3
//load data tab no 4
function loadData7(stat)
{
    statCar=document.getElementById('statCari7').options[document.getElementById('statCari7').selectedIndex].value;
    batchCar=document.getElementById('batchCari7').value;
    tglCar=document.getElementById('tglCari7').value;
    param='proses=loadData7';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
    tujuan='bibit_slave_keluar_masuk.php';
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
                            
                               if(stat==1)
                                 {
                                     document.getElementById('containData7').innerHTML=con.responseText;
                                     loadData5(stat);
                                 }
                                 else if(stat==2)
                                 {
                                    document.getElementById('containData7').innerHTML=con.responseText;
                                    loadDataStock();
                                 }
                                 else
                                     {
                                         document.getElementById('containData7').innerHTML=con.responseText;
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

function cariBast7(num)
{
    statCar=document.getElementById('statCari7').options[document.getElementById('statCari7').selectedIndex].value;
    batchCar=document.getElementById('batchCari7').value;
    tglCar=document.getElementById('tglCari7').value;
    param='proses=loadData7';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
		
		param+='&page='+num;
                tujuan='bibit_slave_keluar_masuk.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containData7').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//end load data no 4
//load data tab no 5
function loadData5(stat)
{
    statCar=document.getElementById('statCari5').options[document.getElementById('statCari5').selectedIndex].value;
    batchCar=document.getElementById('batchCari5').value;
    tglCar=document.getElementById('tglCari5').value;
    param='proses=loadData5';
    param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
    tujuan='bibit_slave_keluar_masuk.php';
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
                             if(stat==1)
                                 {
                                     document.getElementById('containData5').innerHTML=con.responseText;
                                     loadDataStock(stat);
                                 }
                                 else if(stat==2)
                                 {
                                    document.getElementById('containData5').innerHTML=con.responseText;
                                    loadDataStock();
                                 }
                                 else
                                 {
                                     document.getElementById('containData5').innerHTML=con.responseText;
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
function cariBast5(num)
{
                statCar=document.getElementById('statCari2').options[document.getElementById('statCari2').selectedIndex].value;
                batchCar=document.getElementById('batchCari2').value;
                tglCar=document.getElementById('tglCari2').value;
                param='proses=loadData5';
                param+='&statCari='+statCar+'&batchCari='+batchCar+'&tglCari='+tglCar;
		param+='&page='+num;
                tujuan='bibit_slave_keluar_masuk.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('containData5').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
//end load data no 5

//load data stock
function loadDataStock(ygnke)
 {
     
    param='proses=loadDataStock';
    tujuan='bibit_slave_keluar_masuk.php';
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
                             if(ygnke==2)
                                 {
                                     document.getElementById('containDataStock').innerHTML=con.responseText;
                                     loadData1(2);
                                 }
                                 else
                                     {
                                        document.getElementById('containDataStock').innerHTML=con.responseText; 
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
 
 
function filFieldHead(kodetrans,btch,kdeorg,jmlah,tgltnm,jnsbibit,supplerid,tglprodsi,nod,jmlpddo,dtrma,afkri)
{
    

     param='kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&proses=getKet';
     tujuan='bibit_slave_keluar_masuk.php';
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
                                    
                                    document.getElementById('kdTransaksi').value=kodetrans;
                                    document.getElementById('batch').value=btch;
                                    document.getElementById('kodeorgBibitan').disabled=true;
                                    document.getElementById('tglTnm').disabled=true;
                                    l=document.getElementById('kodeorgBibitan');
                                    for(a=0;a<l.length;a++)
                                    {
                                    if(l.options[a].value==kdeorg)
                                    {
                                    l.options[a].selected=true;
                                    }
                                    }
                                    document.getElementById('jmlhBibitan').value=jmlah;
                                    document.getElementById('tglTnm').value=tgltnm;
                                    lrd=document.getElementById('jnsBibitan');

                                    for(ard=0;ard<lrd.length;ard++)
                                    {
                                        if(lrd.options[ard].value==jnsbibit)
                                        {
                                            lrd.options[ard].selected=true;
                                        }
                                    }
                                    lrd2=document.getElementById('supplier_id');

                                    for(ard2=0;ard2<lrd2.length;ard2++)
                                    {
                                        if(lrd2.options[ard2].value==supplerid)
                                        {
                                         lrd2.options[ard2].selected=true;
                                        }
                                    }
                                    document.getElementById('oldJnsbibit').value=jnsbibit;
                                    document.getElementById('tgl2').value=tglprodsi;
                                    document.getElementById('ket').value=con.responseText;
                                    document.getElementById('nodo').value=nod;
                                    document.getElementById('jmlh').value=jmlpddo;
                                    document.getElementById('jmlhTrima').value=dtrma;
                                    document.getElementById('afkirKcmbh').value=afkri;
                                    document.getElementById('proses1').value='update1';

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function filField2(kodetrans,btch,kdeorg,tujn,tgltnm,jmlh)//'".$rData['kodetransaksi']."','".$rData['batch']."','".$rData['kodeorg']."','".$rData['tujuan']."','".tanggalnormal($rData['tanggal'])."','".$rData['jumlah']."'
{
     param='kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&proses=getKet'+'&kdOrgTjn='+tujn;
     tujuan='bibit_slave_keluar_masuk.php';
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
                                    
                                    document.getElementById('kdTransaksiTp').value=kodetrans;
                                    //document.getElementById('batch').value=btch;
                                    longdt=document.getElementById('batchTp');
                                    for(along=0;along<longdt.length;along++)
                                    {
                                        if(longdt.options[along].value==btch)
                                        {
                                          longdt.options[along].selected=true;
                                        }
                                    }
                                    document.getElementById('batchTp').disabled=true;
                                    longdt2=document.getElementById('kodeOrgTp');
                                    for(along2=0;along2<longdt2.length;along2++)
                                    {
                                        if(longdt2.options[along2].value==kdeorg)
                                        {
                                          longdt2.options[along2].selected=true;
                                        }
                                    }
                                    document.getElementById('kodeOrgTp').disabled=true;
                                    document.getElementById('tglTp').value=tgltnm;
                                    longdt25=document.getElementById('kodeOrgTjnTp');
                                    for(along25=0;along25<longdt25.length;along25++)
                                    {
                                        if(longdt25.options[along25].value==tujn)
                                        {
                                          longdt25.options[along25].selected=true;
                                        }
                                    }
                                    
                                    document.getElementById('kodeOrgTjnTp').disabled=true;
                                    document.getElementById('tglTp').disabled=true;
                                    document.getElementById('jmlhTpBbtn').value=jmlh;
                                    
                                    document.getElementById('ketTp').value=con.responseText;
                                    //document.getElementById('proses1').value='update1';

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function filField3(kodetrans,btch,kdeorg,tgltnm,jmlh)
{
     param='kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&proses=getKet';
     tujuan='bibit_slave_keluar_masuk.php';
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
                                    
                                    document.getElementById('kdTransAfk').value=kodetrans;
                                    //document.getElementById('batch').value=btch;
                                    longdt=document.getElementById('batchAfk');
                                    for(along=0;along<longdt.length;along++)
                                    {
                                        if(longdt.options[along].value==btch)
                                        {
                                          longdt.options[along].selected=true;
                                        }
                                    }
                                    document.getElementById('batchAfk').disabled=true;
                                    longdt2=document.getElementById('kdOrgAfk');
                                    for(along2=0;along2<longdt2.length;along2++)
                                    {
                                        if(longdt2.options[along2].value==kdeorg)
                                        {
                                          longdt2.options[along2].selected=true;
                                        }
                                    }
                                    document.getElementById('kdOrgAfk').disabled=true;
                                    document.getElementById('tglAfkirBibit').value=tgltnm;
                                    document.getElementById('tglAfkirBibit').disabled=true;
                                    document.getElementById('jmlhAfk').value='';
                                    document.getElementById('jmlhAfk').value=jmlh;
                                    
                                    document.getElementById('ketAfk').value=con.responseText;
                                    //document.getElementById('proses1').value='update1';

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function filField5(kodetrans,btch,kdeorg,tgltnm,jmlh)
{
     param='kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&proses=getKet';
     tujuan='bibit_slave_keluar_masuk.php';
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
                                    
                                    document.getElementById('kdTransaksiDbt').value=kodetrans;
                                    //document.getElementById('batch').value=btch;
                                    longdt=document.getElementById('batchDbt');
                                    for(along=0;along<longdt.length;along++)
                                    {
                                        if(longdt.options[along].value==btch)
                                        {
                                          longdt.options[along].selected=true;
                                        }
                                    }
                                    document.getElementById('batchDbt').disabled=true;
                                    longdt2=document.getElementById('kdOrgDbt');
                                    for(along2=0;along2<longdt2.length;along2++)
                                    {
                                        if(longdt2.options[along2].value==kdeorg)
                                        {
                                          longdt2.options[along2].selected=true;
                                        }
                                    }
                                    document.getElementById('kdOrgDbt').disabled=true;
                                    document.getElementById('tglDbt').value=tgltnm;
                                    document.getElementById('tglDbt').disabled=true;
                                    document.getElementById('jmlhDbt').value='';
                                    document.getElementById('jmlhDbt').value=jmlh;
                                    
                                    document.getElementById('ketDbt').value=con.responseText;
                                    //document.getElementById('proses1').value='update1';

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function filField7(kodetrans,btch,kdeorg,tgltnm,jmlh,kdVhc,nmsopir,inTex,kdcust,lokPeng,assist,afd,kegiatanid)
{
     param='kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&proses=getKet';
     tujuan='bibit_slave_keluar_masuk.php';
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
                                    
                                    document.getElementById('kdTransPnb').value=kodetrans;
                                    //document.getElementById('batch').value=btch;
                                    longdt=document.getElementById('batchPnb');
                                    for(along=0;along<longdt.length;along++)
                                    {
                                        if(longdt.options[along].value==btch)
                                        {
                                          longdt.options[along].selected=true;
                                        }
                                    }
                                    document.getElementById('batchPnb').disabled=true;
                                    longdt2=document.getElementById('kdOrgPnb');
                                    for(along2=0;along2<longdt2.length;along2++)
                                    {
                                        if(longdt2.options[along2].value==kdeorg)
                                        {
                                          longdt2.options[along2].selected=true;
                                        }
                                    }
                                    longdt5=document.getElementById('kegId');
                                    for(along5=0;along5<longdt5.length;along5++)
                                    {
                                        if(longdt5.options[along5].value==kegiatanid)
                                        {
                                          longdt5.options[along5].selected=true;
                                        }
                                    }
                                 
                                    document.getElementById('kdOrgPnb').disabled=true;
                                    document.getElementById('tglPnb').value=tgltnm;
                                    document.getElementById('jmlhPnb').value='';
                                    document.getElementById('jmlhPnb').value=jmlh;
                                    document.getElementById('kdvhc').value=kdVhc;
                                    document.getElementById('nmSupir').value=nmsopir;
                                    document.getElementById('detPeng').value=lokPeng;
                                    longdt25=document.getElementById('assistenPnb');
                                    for(along25=0;along25<longdt25.length;along25++)
                                    {
                                        if(longdt25.options[along25].value==assist)
                                        {
                                          longdt25.options[along25].selected=true;
                                        }
                                    }
                                    document.getElementById('ketPnb').value=con.responseText;
                                    longdt28=document.getElementById('intexDt');
                                    for(along28=0;along28<longdt28.length;along28++)
                                    {
                                        if(longdt28.options[along28].value==inTex)
                                        {
                                          longdt28.options[along28].selected=true;
                                        }
                                    }
                                    getCustdata(inTex,kdcust,afd);

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function delFieldHead(tanggal,kodetrans,btch,kdeorg,tgltnm,jnsbibit)
{
        param='proses=delData';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&oldJenisBibit='+jnsbibit+'&tglTnm='+tgltnm;
        param+='&tanggal='+tanggal;    
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Delete, are you sure"))
            {
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
                                 //	alert(con.responseText);
                                  loadData1(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function delField2(tanggal,kodetrans,btch,kdeorg,tjan)
{
        param='proses=delData2';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&kdOrgTjn='+tjan+'&tanggal='+tanggal;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Delete, are you sure"))
            {
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
                                 //	alert(con.responseText);
                                  loadData2(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function delField3(tanggal,kodetrans,btch,kdeorg,tjan)
{
        param='proses=delData3';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&kdOrgTjn='+tjan+'&tanggal='+tanggal;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Delete, are you sure"))
            {
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
                                 //	alert(con.responseText);
                                  loadData3(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function delField5(tanggal,kodetrans,btch,kdeorg,tjan)
{
        param='proses=delData3';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&kdOrgTjn='+tjan+'&tanggal='+tanggal;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Delete, are you sure?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData5(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function delField7(tanggal,kodetrans,btch,kdeorg,rit,kodevhc)
{
        param='proses=delData7';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&rit='+rit+'&tanggal='+tanggal+'&kodevhc='+kodevhc;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Delete, are you sure"))
            {
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
                                 //	alert(con.responseText);
                                  loadData7(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function postingData(kodetrans,btch,kdeorg,tgltnm)
{
        param='proses=postData';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&tglTnm='+tgltnm;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Are you sure?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData1(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function postingData2(tanggal,kodetrans,btch,kdeorg,kdOrgTjn,jmlhBibitan)
{
        param='proses=postData2';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&kdOrgTjn='+kdOrgTjn+'&jmlhBibitan='+jmlhBibitan;
        param+='&tanggal='+tanggal;
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Are you sure?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData2(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function postingData3(tanggal,kodetrans,btch,kdeorg,jmlhBibitan)
{
        param='proses=postData3';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&tanggal='+tanggal+'&jmlhBibitan='+jmlhBibitan;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Are you sure?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData3(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}

function postingData5(tanggal,kodetrans,btch,kdeorg,tujuan,jmlhBibitan)
{
        param='proses=postData5';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&tanggal='+tanggal+'&jmlhBibitan='+jmlhBibitan;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Are you sure.?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData5(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function postingData7(tanggal,kodetrans,btch,kdeorg,rit,kodevhc,jmlhBibitan)
{
        param='proses=postData7';
        param+='&kodeTrans='+kodetrans+'&batchVar='+btch+'&kdOrg='+kdeorg+'&jmlhBibitan='+jmlhBibitan;
        param+='&jmlRit='+rit+'&kdvhc='+kodevhc+'&tanggal='+tanggal;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        if(confirm("Are you sure..?"))
            {
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
                                 //	alert(con.responseText);
                                  loadData7(2);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}


function getKodeorg()
{
     //alert("masuk");
        btch=document.getElementById('batchTp').options[document.getElementById('batchTp').selectedIndex].value;
        param='proses=getKodeorg';
        param+='&batchVar='+btch;
      //  alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        
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
                                     if(con.responseText!='')
                                     {
                                        document.getElementById('kodeOrgTp').innerHTML=con.responseText;
                                        //document.getElementById('kodeOrgTp').disabled=true;
                                        document.getElementById('kodeOrgTjnTp').value='';
                                     }
                                     else
                                     {
                                            document.getElementById('kodeOrgTp').innerHTML="<option value=''>"+pilh+"</option>";
                                            document.getElementById('kodeOrgTjnTp').value='';
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

function getKodeorgN()
{
    btch=document.getElementById('batchPnb').options[document.getElementById('batchPnb').selectedIndex].value;
    param='proses=getKodeorg';
    param+='&batchVar='+btch;
    //alert(param);
    tujuan='bibit_slave_keluar_masuk.php';

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
                    if(con.responseText!='')
                    {
                        document.getElementById('kdOrgPnb').innerHTML=con.responseText;
                        //document.getElementById('kdOrgAfk').disabled=true;
                    }
                    else
                    {
                        document.getElementById('kdOrgPnb').innerHTML="<option value=''>"+pilh+"</option>";
                        document.getElementById('kdOrgPnb').disabled=false;
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
function getKodeorg2()
{
        btch=document.getElementById('batchAfk').options[document.getElementById('batchAfk').selectedIndex].value;
        param='proses=getKodeorg';
        param+='&batchVar='+btch;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        
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
                                     if(con.responseText!='')
                                     {
                                        document.getElementById('kdOrgAfk').innerHTML=con.responseText;
                                        //document.getElementById('kdOrgAfk').disabled=true;
                                     }
                                     else
                                     {
                                            document.getElementById('kdOrgAfk').innerHTML="<option value=''>"+pilh+"</option>";
                                            document.getElementById('kdOrgAfk').disabled=false;
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
function getKodeorg3()
{
        btch=document.getElementById('batchDbt').options[document.getElementById('batchDbt').selectedIndex].value;
        param='proses=getKodeorg';
        param+='&batchVar='+btch;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        
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
                                     if(con.responseText!='')
                                     {
                                        document.getElementById('kdOrgDbt').innerHTML=con.responseText;
                                        //document.getElementById('kdOrgDbt').disabled=true;
                                     }
                                     else
                                     {
                                            document.getElementById('kdOrgDbt').innerHTML="<option value=''>"+pilh+"</option>";
                                            document.getElementById('kdOrgDbt').disabled=false;
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
function getKodeorg7()
{
        btch=document.getElementById('batchPnb').options[document.getElementById('batchPnb').selectedIndex].value;
        param='proses=getKodeorg';
        param+='&batchVar='+btch;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        
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
                                     if(con.responseText!='')
                                     {
                                        document.getElementById('kdOrgPnb').innerHTML=con.responseText;
                                        //document.getElementById('kdOrgPnb').disabled=true;
                                     }
                                     else
                                     {
                                            document.getElementById('kdOrgPnb').innerHTML="<option value=''>"+pilh+"</option>";
                                            document.getElementById('kdOrgPnb').disabled=false;
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
function cekSamaGak()
{
        kdOrg=document.getElementById('kodeOrgTjnTp').options[document.getElementById('kodeOrgTjnTp').selectedIndex].value;
        kodeOrgTp=document.getElementById('kodeOrgTp').options[document.getElementById('kodeOrgTp').selectedIndex].value;
        if(kdOrg==kodeOrgTp)
            {
              document.getElementById('kodeOrgTjnTp').options[0].selected=true;  
            }
    /*
        btch=document.getElementById('batchTp').options[document.getElementById('batchTp').selectedIndex].value;
        param='proses=cekSmGak';
        param+='&kdOrg='+kdOrg;
        param+='&batchVar='+btch;
        //alert(param);
	tujuan='bibit_slave_keluar_masuk.php';
        
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
                                 if(con.responseText=='1')
                                     {
                                         alert("Kode Organisasi Bibitan Tujuan Sama Dengan Kode Organisasi Sumber");
                                         document.getElementById('kodeOrgTjnTp').value='';
                                     }
                                 
                                  
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  */
}

function getCustdata(intx,kdorg,afd)
{
        if((intx==0)||(kdorg==0)||(afd==0))
            {
                intexDt=document.getElementById('intexDt').options[document.getElementById('intexDt').selectedIndex].value;
                param='proses=getCust';
                param+='&intexDt='+intexDt;
            }
            else
            {
                intexDt=intx;
                kdOrg=kdorg;
                if(intexDt=='')
                {
                    intexDt=document.getElementById('intexDt').options[document.getElementById('intexDt').selectedIndex].value;
                }
                param='proses=getCust';
                param+='&intexDt='+intexDt;
                param+='&kdOrg='+kdOrg;
            }
           // alert(param);
           if(intexDt>0)
           {
               document.getElementById('kdAfdeling').disabled=false;
           }
           else
           {
                document.getElementById('kdAfdeling').disabled=true;
                document.getElementById('kdAfdeling').innerHTML="<option value=''>"+plh+"</option>";
           }
           
	tujuan='bibit_slave_keluar_masuk.php';
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
                                    document.getElementById('custId').innerHTML= con.responseText
                                    if(afd!=0)
                                    {
                                        getKodeorg(kdorg,afd);
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

function getKodeorgAfd(kbnid,afdid)
{
    if((kbnid==0)||(afdid==0))
    {
        kdKbn=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
        param='proses=getAfd';
        param+='&kdOrg='+kdKbn;
    }
    else
    {
        kdKbn=kbnid;
        kodeAfd=afdid;
        param='proses=getAfd';
        param+='&kdOrg='+kdKbn+'&kodeAfd='+kodeAfd;
    }
    tujuan='bibit_slave_keluar_masuk.php';
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
                                document.getElementById('kdAfdeling').innerHTML= con.responseText
                                

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
}

function getKodeorgBlok()
{
    kdKbn=document.getElementById('custId').options[document.getElementById('custId').selectedIndex].value;
    param='proses=getBlok';
    param+='&kdOrg='+kdKbn;
    tujuan='bibit_slave_keluar_masuk.php';
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
                    document.getElementById('kdAfdeling').innerHTML= con.responseText
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  
}