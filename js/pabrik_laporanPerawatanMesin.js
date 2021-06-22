// JavaScript Document
function getStation(shft,stat)
{
	if((shft!=0)||(stat!=0))
	{
		kdOrg=document.getElementById('pbrkId').value;
		sht=shft;
		statid=stat;
		param='kdOrg='+kdOrg+'&proses=GetStat'+'&shft='+sht+'&statid='+statid;
	}
	else
	{
		kdOrg=document.getElementById('pbrkId').value;
		param='kdOrg='+kdOrg+'&proses=GetStat';
	}
	//alert(param);
	tujuan='pabrikPemeliharaanmesin_slave.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Respons
						//document.getElementById('trans_no').value = con.responseText;
						as=con.responseText.split("###");
						document.getElementById('statId').innerHTML=as[0];
						getMesin(stat);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function getMesin(stat)
{
	if(stat!=0)
	{
		statId=stat;
	}
	else
	{
		statId=document.getElementById('statId').value;
	}
	param='statId='+statId+'&proses=GetMsn';
	//alert(param);
	tujuan='pabrikPemeliharaanmesin_slave.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Respons
						//document.getElementById('trans_no').value = con.responseText;
						//document.getElementById('msnId').innerHTML="<option value=></option>"+con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}
function save_pil()
{
	
	//document.getElementById('nm_goods').disabled= false;
	//document.getElementById('msnId').disabled= false;
/*	document.getElementById('kd_br').value='';
	document.getElementById('msnId').value='';
*/	
	pbrkId=document.getElementById('pbrkId').value;
	statid=document.getElementById('statId').value;
	periode=document.getElementById('period').value;
	
	
	
	
	if(pbrkId=='')
	{
		alert('Pabrik masih kosong');
		return;
		//document.getElementById('thnbudget').focus();
	}
	else if(statid=='')
	{
		alert('Station masih kosong');
		return;	
	}
	
	document.getElementById('pbrkId').disabled= true;
	document.getElementById('statId').disabled= true;
	document.getElementById('period').disabled= true;
	
	//else {
		
	param='pbrkId='+pbrkId+'&statId='+statid+'&periode='+periode+'&proses=getData';
	//alert(param);
	tujuan='pabrik_laporanPerawatanMesinSlave.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('hasil_cari').style.display='block';
						document.getElementById('contain').innerHTML=con.responseText;					
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		//}	
	
}

}

function ganti_pil()
{
	document.getElementById('pbrkId').disabled= false;
	document.getElementById('statId').disabled= false;
	document.getElementById('period').disabled= false;
	
	document.getElementById('pbrkId').value='';
	document.getElementById('statId').value='';
	document.getElementById('period').value='';
	
	document.getElementById('hasil_cari').style.display='none';
	
}
function cari_brng(title,content,ev)
{
	if(document.getElementById('pbrkId').disabled==true)
	{
		width='500';
		height='400';
		showDialog1(title,content,width,height,ev);
	}
	else
	{
		alert('Please Choose Company');
	}
}
function findBrg()
{
		//kode_gudang=document.getElementById('gudang_id').value;
		txt_cari=document.getElementById('no_brg').value;
		param='txtcari='+txt_cari;
		tujuan='log_slave_cariBarangUmum.php';
		//alert(param);
		//tujuan='log_slave_2keluarmasukbrg.php';
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
function throwThisRow(kd_brg,nm_brg,satuan)
{
	document.getElementById('nm_goods').value=nm_brg;
	document.getElementById('nm_goods').disabled=true;
	//document.getElementById('msnId').value='';
	//document.getElementById('msnId').disabled=true;
	document.getElementById('kd_br').disabled=true;
	document.getElementById('msnId').disabled=false;
	pabrik=document.getElementById('pbrkId').value;
	statid=document.getElementById('statId').value;
	periode=document.getElementById('period').value;
	document.getElementById('kd_br').value=kd_brg;		
	document.getElementById('msnId').value='';
	param='proses=get_result_cari';
	param+='&kdBrg='+kd_brg+'&pbrkId='+pabrik+'&statId='+statId+'&periode='+periode;
	//alert(param);
	tujuan='pabrik_laporanPerawatanMesinSlave.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					document.getElementById('contain').innerHTML=con.responseText;	
					closeDialog();	
						
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
function getDataMsn()
{
	if((document.getElementById('pbrkId').disabled!=true)||(document.getElementById('statId').disabled!=true)||(document.getElementById('period').disabled!=true))
	{
		alert("Please Lock Your Choose");
		return;
	}
	
	document.getElementById('kd_br').value='';
	document.getElementById('nm_goods').disabled=false;
	document.getElementById('msnId').disabled=true;
	msn=document.getElementById('msnId').value;
	pbrkId=document.getElementById('pbrkId').value;
	statid=document.getElementById('statId').value;
	periode=document.getElementById('period').value;
	
	
	param='pbrkId='+pbrkId+'&statId='+statid+'&periode='+periode;
	param+='&msnId='+msn+'&proses=GetDataMsn';
	tujuan='pabrik_laporanPerawatanMesinSlave.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('hasil_cari').style.display='block';
						//document.getElementById('nm_goods').disabled=true;
						document.getElementById('contain').innerHTML=con.responseText;					
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function dataKeExcel(ev,tujuan)
{
	pabrik	=document.getElementById('pbrkId');
	statId  =document.getElementById('statId').value;
	periode =document.getElementById('period');
	pabrik	=pabrik.options[pabrik.selectedIndex].value;
	//gudang	=gudang.options[gudang.selectedIndex].value;
	periode	=periode.options[periode.selectedIndex].value;
	judul='Perawatan Mesin Report Ms.Excel';	
	
	/*if((document.getElementById('msnId')!=null)&&(document.getElementById('msnId').value!=''))
	{
		//kd_brg=document.getElementById('kd_br').value;
		msnId=document.getElementById('msnId').value;
		param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode+'&msnId='+msnId;
	}
	else if((document.getElementById('kd_br')!=null)&&(document.getElementById('kd_br').value!=''))
	{
		kd_brg=document.getElementById('kd_br').value;
		//msnId=document.getElementById('msnId').value;
		param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode+'&kdBrg='+kd_brg;
	}
	else if((document.getElementById('kd_br')==null)||(document.getElementById('msnId')==null))
	{
*/		
	param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode;

	//}

	//alert(param);
	printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
	
	pabrik	=document.getElementById('pbrkId');
	statId  =document.getElementById('statId').value;
	periode =document.getElementById('period');
	pabrik	=pabrik.options[pabrik.selectedIndex].value;
	periode	=periode.options[periode.selectedIndex].value;
	judul='Perawatan Mesin Report';	
	tujuan='pabrikPemeliharaanMesinPdf.php';
	/*if((document.getElementById('msnId')!=null)&&(document.getElementById('msnId').value!=''))
	{
		//kd_brg=document.getElementById('kd_br').value;
		
		msnId=document.getElementById('msnId').value;
		param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode+'&msnId='+msnId;
	}
	else if((document.getElementById('kd_br')!=null)&&(document.getElementById('kd_br').value!=''))
	{
		
		kd_brg=document.getElementById('kd_br').value;
		//msnId=document.getElementById('msnId').value;
		param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode+'&kdBrg='+kd_brg;
	}
	else if((document.getElementById('kd_br').value=='')||(document.getElementById('msnId').value==''))
	{
		document.getElementById('kd_br').disabled=false;
		document.getElementById('msnId').disabled=false;
*/		
	param='pabrik='+pabrik+'&statId='+statId+'&periode='+periode;
	//}
	//alert(param);
	printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}