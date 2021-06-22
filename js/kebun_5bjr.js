function loadData()
{ 
        thnProd=document.getElementById('thnProd').value;
	param='proses=loadData'+'&thnProd='+thnProd;
	tujuan='kebun_slave_5bjr';
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
                      document.getElementById('kdBlok').disabled=false;
                      document.getElementById('jmBjr').disabled=false;
                      document.getElementById('listThnProduksi').style.display='none';
                      document.getElementById('listDataBjr').style.display='block';
                                          
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
    document.getElementById('kdBlok').disabled=true;
    document.getElementById('jmBjr').disabled=true; 
    document.getElementById('container').innerHTML=isidata;
    document.getElementById('thnProd').value='';
    document.getElementById('listThnProduksi').style.display='block';
    document.getElementById('listDataBjr').style.display='none';
}
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
                        loadData();
                        document.getElementById('jmBjr').value='';
                        document.getElementById('kdBlok').disabled=false;
                        document.getElementById('kdBlok').value='';
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

function cariBast2(num)
{
		thnProd=document.getElementById('thnProd').value;
                param='proses=loadData'+'&thnProd='+thnProd
		param+='&page='+num;
		tujuan = 'kebun_slave_5bjr.php';
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
function fillField(kdorg,jmbjr)
{
    lLokasi=document.getElementById('kdBlok');
    for(ard=0;ard<lLokasi.length;ard++)
    {
        if(lLokasi.options[ard].value==kdorg)
            {
                lLokasi.options[ard].selected=true;
            }
    }
    document.getElementById('jmBjr').value=jmbjr;
    document.getElementById('kdBlok').disabled=true;
    document.getElementById('proses').value="update";
					
}
function delData(thnproduksi,kdorg)
{
	param='proses=delData'+'&thnProd='+thnproduksi;
        param+='&kdBlok='+kdorg;
	tujuan='kebun_slave_5bjr';
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
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}