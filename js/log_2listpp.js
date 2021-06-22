// JavaScript Document
function getPt()
{
        prd=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
	param='proses=getPt'+'&periode='+prd;
	tujuan='log_slave_2pembayaran.php';
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
                                    document.getElementById('kdUnit').innerHTML=con.responseText;
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
              }
         }
		
}
function getNopo()
{
     prd=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
     if(prd=='')
         {
             alert("Period required!");
             document.getElementById('jenisId').value='';
             return;
         }
     jns=document.getElementById('jenisId').options[document.getElementById('jenisId').selectedIndex].value;
    param='proses=getJenis'+'&periode='+prd+'&jenisId='+jns;
    tujuan='log_slave_2pembayaran.php';
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
                                aer=con.responseText.split("###");
                                document.getElementById('lstPo').innerHTML=aer[0];
                                document.getElementById('suppId').innerHTML=aer[1];
                        }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }
     }
}
function getAll()
{
    prd=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    unt=document.getElementById('kdUnit').options[document.getElementById('kdUnit').selectedIndex].value;
    jns=document.getElementById('jenisId').options[document.getElementById('jenisId').selectedIndex].value;
    param='proses=getJenis'+'&periode='+prd+'&jenisId='+jns+'&kdUnit='+unt;
    tujuan='log_slave_2pembayaran.php';
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
                                document.getElementById('lstPo').innerHTML=con.responseText;
                        }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }
     }
}
function searchPO(title,content,ev)
{
    width='500';
    height='400';
    showDialog1(title,content,width,height,ev);
    //alert('asdasd');
}
function setDataPO(nopo)
{
    // l=document.getElementById('cariNopo').options[document.getElementById('cariNopo').selectedIndex].value;

    l=document.getElementById('txtNopp');

    for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==nopo)
        {
            l.options[a].selected=true;
            break;
        }
    }

    closeDialog();
}
function findPO()
{
    nmPO=document.getElementById('nmPO').value;
    param='proses=getPO'+'&nmPO='+nmPO;
    tujuan='log_slave_2listpp.php';
    console.log(param);
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('containerPO').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
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
    tujuan='log_slave_2listpp.php';
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
    l=document.getElementById('suppId2');

    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==kdSupp)
                {
                    l.options[a].selected=true;
                }
        }

       closeDialog();
	   get_supplier();
}