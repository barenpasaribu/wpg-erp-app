// JavaScript Document

function getDt()
{
	idRem=document.getElementById('lksiServer').value;
	param='idRemote='+idRem+'&proses=getDataLokasi';
//	alert(param);
	tujuan='pabrik_slave_3uploadDataVendor.php';
	post_response_text(tujuan, param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  //$arr="##dbnm##prt##pswrd##ipAdd#period##kdBrg";
				 // alert(con.responseText);
					ar= con.responseText.split("###");
					document.getElementById('ipAdd').value=ar[0];
					document.getElementById('prt').value=ar[1];
					document.getElementById('dbnm').value=ar[2];
					document.getElementById('usrName').value=ar[3];
					document.getElementById('pswrd').value=ar[4];
					document.getElementById('lksiServer').disabled=true;
					passprm="##ipAdd"+"##"+"prt"+"##"+"dbnm"+"##"+"usrName"+"##"+"pswrd";
                                        document.getElementById('nmTable').disabled=false;
					//getTable(passprm);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function getTable(passParam)
{
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
	param+='&proses=getTable';
	//alert(param);
	tujuan = 'pabrik_slave_3uploadDataVendor.php';
	post_response_text(tujuan, param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  //$arr="##dbnm##prt##pswrd##ipAdd#period##kdBrg";
				 // alert(con.responseText);
				 document.getElementById('nmTable').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function lockForm()
{
	document.getElementById('preview').disabled=true;
	document.getElementById('lksiServer').disabled=true;
}
function uploadData(maxRow,varIsi)
{
//
	if(confirm('Apakah anda yakin ?'))
	{
		   loopClosingFisik(1,maxRow,varIsi);
		   	lockForm();
	}
	else
	{
		document.getElementById('preview').disabled=false;
		return;
	}
}

function loopClosingFisik(currRow,maxRow,passParam)
{
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
	kdTimbangan=document.getElementById('kdTimbangan_'+currRow).innerHTML;
	nmCust=document.getElementById('nmCust_'+currRow).innerHTML;
	param+='&proses=uploadData';
	param+='&kdTimbangan='+kdTimbangan+'&nmCust='+nmCust;
	tujuan = 'pabrik_slave_3uploadDataVendor.php';
	post_response_text(tujuan, param, respog);
	document.getElementById('row_'+currRow).style.backgroundColor='orange';
	lockScreen('wait');
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
					if(con.responseText==1)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='green';
						currRow+=1;
					}
					else if(con.responseText==0)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='red';
						currRow+=1;
					}
					else
					{
						alert("Error");
						tutupProses();
						//unlockScreen();
						
					}
					if(currRow>maxRow)
					{
						document.getElementById('preview').disabled=false;
						tutupProses('simpan');//tutup periode dan pindah periode							
					}  
					else
					{
						loopClosingFisik(currRow,maxRow,dtAll);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}		
	
}
function unLockForm()
{
	//##dbnm##prt##pswrd##ipAdd##period##kdBrg##usrName##lksiServer
	document.getElementById('preview').disabled=false;
	document.getElementById('lksiServer').disabled=false;
	document.getElementById('dbnm').value='';
	document.getElementById('prt').value='';
	document.getElementById('pswrd').value='';
	document.getElementById('ipAdd').value='';
	document.getElementById('usrName').value='';
	document.getElementById('lksiServer').value='';
	document.getElementById('printContainer').innerHTML='';
}

function tutupProses(x)
{
	period=document.getElementById('preview');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Terupload");
			unLockForm();
			document.getElementById('printContainer').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}

}
function uploadData2(maxRow,varIsi)
{
//
	if(confirm('Apakah anda yakin ?'))
	{
		   loopClosingFisik2(1,maxRow,dtAll);
		   lockForm();
	}
	else
	{
		document.getElementById('preview').disabled=false;
		return;
	}
}

function loopClosingFisik2(currRow,maxRow,passParam)
{
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
	kntrk=document.getElementById('kontrak_'+currRow).innerHTML;
	nosipb=document.getElementById('sipb_'+currRow).innerHTML;
        tglsibp=document.getElementById('tgl_sipb_'+currRow).innerHTML;
        kdbrg=document.getElementById('kdbrg_'+currRow).innerHTML;
        trpcode=document.getElementById('trpcod_'+currRow).innerHTML;
        trpname=document.getElementById('trp_nm_'+currRow).innerHTML;
        ketdt=document.getElementById('ket_'+currRow).innerHTML;
        
	param+='&proses=uploadData2';
	param+='&kntrk='+kntrk+'&nosipb='+nosipb+'&tglsibp='+tglsibp;
        param+='&kdbrg='+kdbrg+'&trpcode='+trpcode+'&trpname='+trpname+'&ketdt='+ketdt;
	tujuan = 'pabrik_slave_3uploadDataVendor.php';
	post_response_text(tujuan, param, respog);
	document.getElementById('row_'+currRow).style.backgroundColor='orange';
	lockScreen('wait');
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
					document.getElementById('row_'+currRow).style.backgroundColor='red';
				   unlockScreen();
				}
				else {
					//alert(con.responseText);
					//return;
					if(con.responseText==1)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='green';
						currRow+=1;
					}
					else if(con.responseText==0)
					{
						document.getElementById('row_'+currRow).style.backgroundColor='red';
						currRow+=1;
					}
					else
					{
						alert("Error");
						tutupProses();
						//unlockScreen();
						
					}
					if(currRow>maxRow)
					{
						document.getElementById('preview').disabled=false;
						tutupProses('simpan');//tutup periode dan pindah periode							
					}  
					else
					{
						loopClosingFisik2(currRow,maxRow,dtAll);
					}
				}
			}
			else {
				busy_off();
				error_catch(con.status);
				unlockScreen();
			}
		}
	}		
	
}