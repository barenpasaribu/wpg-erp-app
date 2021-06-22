function cancel()
{
	document.location.reload();
}


function getAfd()
{
	kdDiv=document.getElementById('kdDiv').value;
	param='method=getAfd'+'&kdDiv='+kdDiv;
	
	tujuan='kebun_slave_qc_hamaUlat.php';
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
					else 
					{
						//alert(con.responseText);
						document.getElementById('kdAfd').innerHTML=con.responseText;
						getKar();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}


function getKar()
{
	
	kdDiv=document.getElementById('kdDiv').value;
	param='method=getKar'+'&kdDiv='+kdDiv;
	//alert(param);
	tujuan='kebun_slave_qc_hamaUlat.php';
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
					else 
					{
						//alert(con.responseText);
						ar=con.responseText.split("###");
						//pengawas asisten diket
						document.getElementById('pengawas').innerHTML=ar[0];
						document.getElementById('asisten').innerHTML=ar[1];
						document.getElementById('mengetahui').innerHTML=ar[2];
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function del(tgl,kdBlok,userId)
{
	
	param='method=delete'+'&tgl='+tgl+'&kdBlok='+kdBlok+'&userId='+userId;
	tujuan='kebun_slave_qc_hamaUlat.php';
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
					else 
					{
						cancel();
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
	kdAfd=document.getElementById('kdAfd').value;
	param='method=getBlok'+'&kdAfd='+kdAfd;
	tujuan='kebun_slave_qc_hamaUlat.php';
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
					else 
					{
						document.getElementById('kdBlok').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}









function edit()
{

}






function cariBast(num)
{
	kdDivSch=document.getElementById('kdDivSch').value;
	perSch=document.getElementById('perSch').value;
	
	param='method=loadData'+'&kdDivSch='+kdDivSch+'&perSch='+perSch+'&page='+num;
	tujuan = 'kebun_slave_qc_hamaUlat.php';
	post_response_text(tujuan, param, respog);			
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					//displayList();
					
					document.getElementById('container').innerHTML=con.responseText;
					//loadData();
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}

function loadData () 
{
	kdDivSch=document.getElementById('kdDivSch').value;
	perSch=document.getElementById('perSch').value;
	param='method=loadData'+'&kdDivSch='+kdDivSch+'&perSch='+perSch;
	//alert(param);	
	tujuan='kebun_slave_qc_hamaUlat.php';
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






function simpan()
{
	
	tgl=document.getElementById('tgl').value;
	kdBlok=document.getElementById('kdBlok').value;
	tenagakerja=document.getElementById('tenagakerja').value;
	jm1=document.getElementById('jm1').value;
	jm2=document.getElementById('jm2').value;
	mn1=document.getElementById('mn1').value;
	mn2=document.getElementById('mn2').value;
	
	alat=document.getElementById('alat').value;
	dosis1=document.getElementById('dosis1').value;
	bahan1=document.getElementById('bahan1').value;
	dosis2=document.getElementById('dosis2').value;
	bahan2=document.getElementById('bahan2').value;
	dosis3=document.getElementById('dosis3').value;
	bahan3=document.getElementById('bahan3').value;
	pokok=document.getElementById('pokok').value;
	bensin=document.getElementById('bensin').value;
	oli=document.getElementById('oli').value;
	
	catatan=document.getElementById('catatan').value;
	pengawas=document.getElementById('pengawas').value;
	asisten=document.getElementById('asisten').value;
	mengetahui=document.getElementById('mengetahui').value;
	
	if(tgl=='' || kdDiv=='' || kdAfd=='' || kdBlok=='' ||  pengawas=='' || asisten=='' || mengetahui=='')
	{
		alert('Date, Divisi, Afddeling, Block, Suvervision, Assistant, Verify was empty');return;
	}
	
	param='method=saveData'+'&tgl='+tgl+'&kdBlok='+kdBlok+'&tenagakerja='+tenagakerja;
	param+='&jm1='+jm1+'&jm2='+jm2+'&mn1='+mn1+'&mn2='+mn2+'&alat='+alat;
	param+='&bahan1='+bahan1+'&bahan2='+bahan2+'&bahan3='+bahan3;
	param+='&dosis1='+dosis1+'&dosis2='+dosis2+'&dosis3='+dosis3;
	param+='&pokok='+pokok+'&bensin='+bensin+'&oli='+oli;
	param+='&catatan='+catatan+'&pengawas='+pengawas+'&asisten='+asisten+'&mengetahui='+mengetahui;		

	tujuan='kebun_slave_qc_hamaUlat.php';
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
					else 
					{
						cancel();
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}











