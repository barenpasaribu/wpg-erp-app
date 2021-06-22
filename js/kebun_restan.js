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
        document.getElementById('kdBlokRestan').value='';
        document.getElementById('kdBlokRestan').disabled=false;
        document.getElementById('tglRestan').disabled=false;
        document.getElementById('jjgKrm').value='0';
        document.getElementById('umrRestan').value='0';
        document.getElementById('jjgPanen').value='0';
        document.getElementById('tglRestan').value='';
        document.getElementById('proses1').value='saveTab1';
        document.getElementById('cttn').value='';
}

function saveData(sTab)
{
        if(sTab=='1')
        {
           jjgKrm=document.getElementById('jjgKrm').value;
           umrRestan=document.getElementById('umrRestan').value;
           kdBlokRestan=document.getElementById('kdBlokRestan').options[document.getElementById('kdBlokRestan').selectedIndex].value;
           jjgPanen=document.getElementById('jjgPanen').value;
           cttn=document.getElementById('cttn').value;
           tglRestan=document.getElementById('tglRestan').value;
           proses1=document.getElementById('proses1').value;
           param='jjgKrm='+jjgKrm+'&umrRestan='+umrRestan+'&kdBlokRestan='+kdBlokRestan+'&jjgPanen='+jjgPanen+'&cttn='+cttn;
           param+='&tglRestan='+tglRestan+'&proses='+proses1;
        }
       //alert(param);
	tujuan='kebun_slave_restan.php';
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
                                 

                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
	
}

function loadData1(stat)
{
    
    param='proses=loadData1';
    tujuan='kebun_slave_restan.php';
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
                            
                                      document.getElementById('containData1').innerHTML=con.responseText;
                                    
                                 }
                             
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
          }	
      
}
function cariBast(num)
{
		param='proses=loadData1';
		param+='&page='+num;
                tujuan='kebun_slave_restan.php';
		
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



 
function filFieldHead(tgl,kdeorg,jjgpnen,jjgkrm,umrrestan,cttan)
{
   // alert(tgl);
    document.getElementById('tglRestan').value=tgl;
    document.getElementById('kdBlokRestan').disabled=true;
    document.getElementById('tglRestan').disabled=true;
    longdt=document.getElementById('kdBlokRestan');
    for(along=0;along<longdt.length;along++)
    {
        if(longdt.options[along].value==kdeorg)
        {
          longdt.options[along].selected=true;
        }
    }
    document.getElementById('jjgPanen').value=jjgpnen;
    document.getElementById('jjgKrm').value=jjgkrm;
    document.getElementById('umrRestan').value=umrrestan;
    document.getElementById('cttn').value=cttan;
    document.getElementById('proses1').value='update1';
}

function delFieldHead(tgl,kdorg)
{
        param='proses=delData';
        param+='&tglRestan='+tgl+'&kdBlokRestan='+kdorg;
        //alert(param);
	tujuan='kebun_slave_restan.php';
        if(confirm("Anda yaking ingin menghapus"))
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
                                  loadData1(1);
                                }
                        }
                        else {
                                busy_off();
                                error_catch(con.status);
                        }
              }	
	 }  
    
}
function getCari()
{
    kdBlokCari=document.getElementById('kdBlokCari').options[document.getElementById('kdBlokCari').selectedIndex].value;
    periodeCari=document.getElementById('periodeCari').options[document.getElementById('periodeCari').selectedIndex].value;
    param='proses=getCariData'+'&periodeCari='+periodeCari+'&kdBlokCari='+kdBlokCari;
		
                tujuan='kebun_slave_restan.php';
		
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
function cariBastCr(num)
{
            kdBlokCari=document.getElementById('kdBlokCari').options[document.getElementById('kdBlokCari').selectedIndex].value;
            periodeCari=document.getElementById('periodeCari').options[document.getElementById('periodeCari').selectedIndex].value;
            param='proses=getCariData'+'&periodeCari='+periodeCari+'&kdBlokCari='+kdBlokCari
		param+='&page='+num;
                tujuan='kebun_slave_restan.php';
		
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
function getAfd()
{
    kdUnit=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    param='proses=getAfd'+'&kdUnit='+kdUnit;
                tujuan='kebun_slave_restan.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('afdId').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function getBlok()
{
     afdId=document.getElementById('afdId').options[document.getElementById('afdId').selectedIndex].value;
    param='proses=getBlok'+'&afdId='+afdId;
                tujuan='kebun_slave_restan.php';
		
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('BlokId').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}
    
}