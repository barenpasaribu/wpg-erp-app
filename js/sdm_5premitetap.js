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
function loadData(){
        idSupplier=document.getElementById('tpTransaksi2').options[document.getElementById('tpTransaksi2').selectedIndex].value;
        param='method=loadData'+'&tpTransaksi='+idSupplier;
        tujuan='sdm_slave_5premitetap';
        post_response_text(tujuan+'.php', param, respon);
        function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                                          document.getElementById('container').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function searchNopo(title,content,ev){
	width='350';
	height='320';
	showDialog1(title,content,width,height,ev);
        getForminvoice();
}
function getForminvoice(){
        idSupplier=document.getElementById('tpTransaksi').options[document.getElementById('tpTransaksi').selectedIndex].value;
        if(idSupplier==''){
            alert("Tipe Transaksi Tidak Boleh Kosong");
            closeDialog();
            return;
        }
        param='method=getForm'+'&tpTransaksi='+idSupplier;
        tujuan='sdm_slave_5premitetap.php';
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
							document.getElementById('formPencariandata').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
} 
function cariTipe()
{
	txt=trim(document.getElementById('no_brg').value);
        idSupplier=document.getElementById('tptrans').value;
	param='txtfind='+txt+'&tpTransaksi='+idSupplier+'&method=cariTipe';
        tujuan='sdm_slave_5premitetap.php';
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
							document.getElementById('container2').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }
	 }
}
function setPo(id){
    l=document.getElementById('pilInp');
    for(a=0;a<l.length;a++)
        {
            if(l.options[a].value==id)
                {
                    l.options[a].selected=true;
                }
        }
  
    closeDialog();
}
function cancelIsi(passParam){
   document.getElementById('tpTransaksi').disabled=false;
   document.getElementById('pilInp').disabled=false;
   document.getElementById('tpTransaksi').value='';
   document.getElementById('pilInp').value='';
   document.getElementById('premiIns').value='';
}

 
function fillField(tptrns,tipkary,jmlh){
l=document.getElementById('tpTransaksi');
for(a=0;a<l.length;a++)
    {
        if(l.options[a].value==tptrns)
            {
                l.options[a].selected=true;
            }
    }
l.disabled=true;
getDt(tptrns,tipkary);
document.getElementById('premiIns').value=jmlh;
document.getElementById('method').value='insert';

}

function getDt(tptrns,tipkary){
    if((tptrns==0)&&(tipkary==0)){
        tipeTrk=document.getElementById('tpTransaksi');
        tipeTrk=tipeTrk.options[tipeTrk.selectedIndex].value;
        param='method=getTipe'+'&tpTransaksi='+tipeTrk;
    }else{
        param='method=getTipe'+'&tpTransaksi='+tptrns+'&kdjbn='+tipkary;
    }
    
    tujuan='sdm_slave_5premitetap.php';
    post_response_text(tujuan, param, respon); 
    function respon(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            } else {
                                 document.getElementById('pilInp').innerHTML=con.responseText;
                                 if(tipkary!=0){
                                     pill=document.getElementById('pilInp');
                                     pill.disabled=true;
                                 }
                            }
                    } else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }   
}
 