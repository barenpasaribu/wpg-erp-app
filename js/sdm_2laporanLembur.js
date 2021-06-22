// JavaScript Document

function getTgl()
{
	periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
	kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
	//pilihan=document.getElementById('pilihan').options[document.getElementById('pilihan').selectedIndex].value;
        pilihan2=document.getElementById('pilihan2').options[document.getElementById('pilihan2').selectedIndex].value;
	param='periode='+periode+'&proses=getTgl'+'&kdUnit='+kdOrg;
        if(pilihan2!='')
            {
                param+='&pilihan2='+pilihan2;
            }
        //    alert(param);
	tujuan='sdm_slave_2laporanLembur';
	post_response_text(tujuan+'.php?proses=getTgl', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						ar=con.responseText.split("###");
						document.getElementById('tgl1').value=ar[0];
						document.getElementById('tgl2').value=ar[1];
						document.getElementById('tgl1').disabled=true;
						document.getElementById('tgl2').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}
function getTgl2()
{
	periode=document.getElementById('period').options[document.getElementById('period').selectedIndex].value;
	kdOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
	//pilihan=document.getElementById('pilihan').options[document.getElementById('pilihan').selectedIndex].value;
        pilihan2=document.getElementById('pilihan_2').options[document.getElementById('pilihan_2').selectedIndex].value;
	param='period='+periode+'&proses=getTgl'+'&kdUnit='+kdOrg;
        //alert(param);
	tujuan='sdm_slave_2laporanLembur_rekap';
	post_response_text(tujuan+'.php?proses=getTgl', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						ar=con.responseText.split("###");
						document.getElementById('tgl_1').value=ar[0];
						document.getElementById('tgl_2').value=ar[1];
						document.getElementById('tgl_1').disabled=true;
						document.getElementById('tgl_2').disabled=true;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
}
function  getPeriode()
{
    kdOrg=document.getElementById('kdOrg').options[document.getElementById('kdOrg').selectedIndex].value;
    //pilihan2=document.getElementById('pilihan2').options[document.getElementById('pilihan2').selectedIndex].value;
    tujuan='sdm_slave_2laporanLembur';
    param='kdOrg='+kdOrg;
    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);
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
							document.getElementById('periode').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}
function  getPeriode2()
{
    kdOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
    //pilihan2=document.getElementById('pilihan2').options[document.getElementById('pilihan2').selectedIndex].value;
    tujuan='sdm_slave_2laporanLembur_rekap';
    param='kdOrg='+kdOrg;
    post_response_text(tujuan+'.php?proses=getPeriode', param, respog);
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
							document.getElementById('period').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }  	
}
function getKry()
{
	kdeOrg=document.getElementById('kdeOrg').options[document.getElementById('kdeOrg').selectedIndex].value;
	param='kdeOrg='+kdeOrg;
	tujuan='sdm_slave_2laporanLembur';
	post_response_text(tujuan+'.php?proses=getKry', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						document.getElementById('idKry').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function Clear1()
{
	document.getElementById('tgl1').value='';
	document.getElementById('tgl2').value='';
	document.getElementById('tgl1').disabled=false;
	document.getElementById('tgl2').disabled=false;
	document.getElementById('kdOrg').value='';
	document.getElementById('periode').value='';
	document.getElementById('printContainer').innerHTML='';
}
function Clear2()
{
	document.getElementById('tgl_1').value='';
	document.getElementById('tgl_2').value='';
	document.getElementById('tgl_1').disabled=false;
	document.getElementById('tgl_2').disabled=false;
	document.getElementById('kdeOrg').value='';
	document.getElementById('period').value='';
	document.getElementById('idKry').innerHTML="<option value''></option>";
	document.getElementById('printContainer').innerHTML='';
}