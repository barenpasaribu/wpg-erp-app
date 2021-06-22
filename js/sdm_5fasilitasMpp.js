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
	//alert(param);
  //alert(param);
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
    post_response_text(fileTarget+'.php', param, respon);

}
function loadData()
{
    
	param='method=loadData';
        thnBudget=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value;
        kdGol=document.getElementById('kdJabtanHead').options[document.getElementById('kdJabtanHead').selectedIndex].value;
       
        if(thnBudget!='')
        {
        param+='&thnBudget='+thnBudget;
        }
        if(kdGol!='')
        {
        param+='&kdJabatan='+kdGol;
        }
      
	tujuan='sdm_slave_5fasilitasMpp';
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
                                          cancelIsi();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function fillField(thnbdget,kdjbtn,brg)
{
	
	param='method=getData';
        param+='&thnBudget='+thnbdget+'&kdJabatan='+kdjbtn+'&oldKdBrg='+brg;
	tujuan='sdm_slave_5fasilitasMpp';
        post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //$arr="##thnBudget##kdGol##region##tktPes##tksi##airport##visa##byaLain##method";
			            ar=con.responseText.split("###");
                                    document.getElementById('thnBudget').value=ar[0];
                                    l=document.getElementById('kdJabatan');
                                    pnjng=document.getElementById('kdBarang');
                                    for(a=0;a<l.length;a++)
                                    {
                                        if(l.options[a].value==ar[1])
                                        {
                                        l.options[a].selected=true;
                                        }
                                    }
                                    
                                    for(abc=0;abc<pnjng.length;abc++)
                                    {
                                        if(pnjng.options[abc].value==ar[2])
                                        {
                                        pnjng.options[abc].selected=true;
                                        }
                                    }
				
					document.getElementById('hrgSat').value=ar[3];
					document.getElementById('sat').value=ar[4];
                                        document.getElementById('jmlhBrng').value=ar[5];
                                        document.getElementById('totBrg').value=ar[6];
                                        document.getElementById('oldKdBrg').value=ar[2];
					
					document.getElementById('method').value="update";
					document.getElementById('thnBudget').disabled=true;
                                        document.getElementById('kdJabatan').disabled=true;
                                        //document.getElementById('region').disabled=true;
					 // document.getElementById('container').innerHTML=con.responseText;
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
    
	document.getElementById('kdBarang').value='';
	document.getElementById('hrgSat').value='0';
	document.getElementById('sat').value='';
	document.getElementById('jmlhBrng').value='0';
        document.getElementById('totBrg').value='0';
	document.getElementById('method').value="insert";
	document.getElementById('thnBudget').disabled=false;
	document.getElementById('kdJabatan').disabled=false;
        
}
function delData(thnbdget,kdjbtn,brg)
{
    //thnBudget##kdJabatan##kdBarang##hrgSat##sat##jmlhBrng##method##totBrg";
	param='method=delData';
        param+='&thnBudget='+thnbdget+'&kdJabatan='+kdjbtn+'&oldKdBrg='+brg;
	tujuan='sdm_slave_5fasilitasMpp';
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
                    //var res = document.getElementById(idCont);
//                    res.innerHTML = con.responseText;
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
function cariPage(num)
{
                
		param='method=loadData';
		param+='&page='+num;
                thnBudget=document.getElementById('thnBudgetHead').options[document.getElementById('thnBudgetHead').selectedIndex].value;
                kdGol=document.getElementById('kdJabtanHead').options[document.getElementById('kdJabtanHead').selectedIndex].value;

                if(thnBudget!='')
                {
                param+='&thnBudget='+thnBudget;
                }
                if(kdGol!='')
                {
                param+='&kdJabatan='+kdGol;
                }
                tujuan='sdm_slave_5fasilitasMpp.php';
		
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
function searchBrg(title,content,ev)
{
        
	width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
        
	//alert('asdasd');
}
function findBrg()
{
    nmBrg=document.getElementById('nmBrg').value;
    param='nmBrg='+nmBrg+'&method=getBarang';
    tujuan='sdm_slave_5fasilitasMpp.php';
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
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }	
	 }  
		
}
function setData(kdbrg,sat)
{
    pnjng=document.getElementById('kdBarang');
   
    for(a=0;a<pnjng.length;a++)
    {
        if(pnjng.options[a].value==kdbrg)
        {
            pnjng.options[a].selected=true;
        }
    }
    //document.getElementById('namaBrg').innerHTML=namaBarang;
    document.getElementById('sat').value=sat;
    closeDialog();
}
function kalikan()
{
    hrgSat=document.getElementById('hrgSat').value;
    jmlh=document.getElementById('jmlhBrng').value;
    hsil=hrgSat*jmlh;
    if(isNaN(hsil))
    {
        hsil=0;
    }
    document.getElementById('totBrg').value=hsil;
}
function getSatuan()
{
    kdBrg=document.getElementById('kdBarang').options[document.getElementById('kdBarang').selectedIndex].value;
    param='kdBarang='+kdBrg+'&method=getSatuan';
    tujuan='sdm_slave_5fasilitasMpp.php';
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
                                      document.getElementById('sat').value=con.responseText;
                                      document.getElementById('oldKdBrg').value=kdBrg;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
          }	
     }  
}