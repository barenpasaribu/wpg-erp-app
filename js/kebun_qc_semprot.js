function cancel()
{
	document.location.reload();
}


function getAfd()
{
	
	kdDiv=document.getElementById('kdDiv').value;
	param='method=getAfd'+'&kdDiv='+kdDiv;
	//alert(param);
	tujuan='kebun_slave_qc_semprot.php';//alert(param);
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
	tujuan='kebun_slave_qc_semprot.php';
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
						for(i=1;i<=15;i++)
						{
							document.getElementById('karyawan'+i).innerHTML=ar[0];
						}
						document.getElementById('pengawas').innerHTML=ar[1];
						document.getElementById('asisten').innerHTML=ar[2];
						document.getElementById('mengetahui').innerHTML=ar[3];
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}

function del(tgl,kdBlok)
{
	
	param='method=delete'+'&tgl='+tgl+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_qc_semprot.php';
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
	tujuan='kebun_slave_qc_semprot.php';
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


function getData()
{
	kdBlok=document.getElementById('kdBlok').value;
	param='method=getData'+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_qc_semprot.php';
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
						ar=con.responseText.split("###");
						document.getElementById('luasAreal').value=ar[0];
						document.getElementById('jmlPkk').value=ar[1];
						
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
	kdDivSch=document.getElementById('kdDivSch').value;
	perSch=document.getElementById('perSch').value;
	
	param='method=loadData'+'&kdDivSch='+kdDivSch+'&perSch='+perSch+'&page='+num;
	tujuan = 'kebun_slave_qc_semprot.php';
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
	tujuan='kebun_slave_qc_semprot.php';
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
	kdDiv=document.getElementById('kdDiv').value;
	kdAfd=document.getElementById('kdAfd').value;
	kdBlok=document.getElementById('kdBlok').value;
	kdKeg=document.getElementById('kdKeg').value;
	
	dosis=document.getElementById('dosis').value;
	jenisgulma=document.getElementById('jenisgulma').value;
	kondisigulma=document.getElementById('kondisigulma').value;
	
	/*strUrl = '';
	for(i=1;i<=3;i++)
	{
		 strUrl+= +'&dosismaterial[]='+document.getElementById('dosismaterial'+i).value
		 +'&dosisjumlah[]='+document.getElementById('dosisjumlah'+i).value
	}*/

	dosismaterial1=document.getElementById('dosismaterial1').value;
		dosisjumlah1=document.getElementById('dosisjumlah1').value;
	dosismaterial2=document.getElementById('dosismaterial2').value;
		dosisjumlah2=document.getElementById('dosisjumlah2').value;
	dosismaterial3=document.getElementById('dosismaterial3').value;
		dosisjumlah3=document.getElementById('dosisjumlah3').value;
	
	//materialdiambil1=document.getElementById('materialdiambil1').value;
		jumlahdiambil1=document.getElementById('jumlahdiambil1').value;
//	materialdiambil2=document.getElementById('materialdiambil2').value;
		jumlahdiambil2=document.getElementById('jumlahdiambil2').value;
//	materialdiambil3=document.getElementById('materialdiambil3').value;
		jumlahdiambil3=document.getElementById('jumlahdiambil3').value;
		
//	materialdipakai1=document.getElementById('materialdipakai1').value;
		jumlahdipakai1=document.getElementById('jumlahdipakai1').value;
//	materialdipakai2=document.getElementById('materialdipakai2').value;
		jumlahdipakai2=document.getElementById('jumlahdipakai2').value;
//	materialdipakai3=document.getElementById('materialdipakai3').value;
		jumlahdipakai3=document.getElementById('jumlahdipakai3').value;
		
	karyawan1=document.getElementById('karyawan1').value;
		hasilkaryawan1=document.getElementById('hasilkaryawan1').value;
	karyawan2=document.getElementById('karyawan2').value;
		hasilkaryawan2=document.getElementById('hasilkaryawan2').value;
	karyawan3=document.getElementById('karyawan3').value;
		hasilkaryawan3=document.getElementById('hasilkaryawan3').value;
	karyawan4=document.getElementById('karyawan4').value;
		hasilkaryawan4=document.getElementById('hasilkaryawan4').value;
	karyawan5=document.getElementById('karyawan5').value;
		hasilkaryawan5=document.getElementById('hasilkaryawan5').value;
	
	karyawan6=document.getElementById('karyawan6').value;
		hasilkaryawan6=document.getElementById('hasilkaryawan6').value;
	karyawan7=document.getElementById('karyawan7').value;
		hasilkaryawan7=document.getElementById('hasilkaryawan7').value;
	karyawan8=document.getElementById('karyawan8').value;
		hasilkaryawan8=document.getElementById('hasilkaryawan8').value;
	karyawan9=document.getElementById('karyawan9').value;
		hasilkaryawan9=document.getElementById('hasilkaryawan9').value;
	karyawan10=document.getElementById('karyawan10').value;
		hasilkaryawan10=document.getElementById('hasilkaryawan10').value;
	
	karyawan11=document.getElementById('karyawan11').value;
		hasilkaryawan11=document.getElementById('hasilkaryawan11').value;
	karyawan12=document.getElementById('karyawan12').value;
		hasilkaryawan12=document.getElementById('hasilkaryawan12').value;
	karyawan13=document.getElementById('karyawan13').value;
		hasilkaryawan13=document.getElementById('hasilkaryawan13').value;
	karyawan14=document.getElementById('karyawan14').value;
		hasilkaryawan14=document.getElementById('hasilkaryawan14').value;
	karyawan15=document.getElementById('karyawan15').value;
		hasilkaryawan15=document.getElementById('hasilkaryawan15').value;
	
	keterangan=document.getElementById('keterangan').value;
	pengawas=document.getElementById('pengawas').value;
	asisten=document.getElementById('asisten').value;
	mengetahui=document.getElementById('mengetahui').value;
	
	
	if(tgl=='' || kdDiv=='' || kdAfd=='' || kdBlok=='' || kdKeg=='' || pengawas=='' || asisten=='' || mengetahui=='')
	{
		alert('Date, Divisi, Afddeling, Block, Activity, Suvervision, Assistant, Verify was empty');return;
	}

	
	
	
	param='method=saveData'+'&tgl='+tgl+'&kdBlok='+kdBlok+'&kdKeg='+kdKeg;
	param+='&dosis='+dosis+'&jenisgulma='+jenisgulma+'&kondisigulma='+kondisigulma;
	param+='&dosismaterial1='+dosismaterial1+'&dosismaterial2='+dosismaterial2+'&dosismaterial3='+dosismaterial3;
		param+='&dosisjumlah1='+dosisjumlah1+'&dosisjumlah2='+dosisjumlah2+'&dosisjumlah3='+dosisjumlah3;		
//	param+='&materialdiambil1='+materialdiambil1+'&materialdiambil2='+materialdiambil2+'&materialdiambil3='+materialdiambil3;
		param+='&jumlahdiambil1='+jumlahdiambil1+'&jumlahdiambil2='+jumlahdiambil2+'&jumlahdiambil3='+jumlahdiambil3;	
//	param+='&materialdipakai1='+materialdipakai1+'&materialdipakai2='+materialdipakai2+'&materialdipakai3='+materialdipakai3;
		param+='&jumlahdipakai1='+jumlahdipakai1+'&jumlahdipakai2='+jumlahdipakai2+'&jumlahdipakai3='+jumlahdipakai3;

	param+='&karyawan1='+karyawan1+'&hasilkaryawan1='+hasilkaryawan1;	
	param+='&karyawan2='+karyawan2+'&hasilkaryawan2='+hasilkaryawan2;	
	param+='&karyawan3='+karyawan3+'&hasilkaryawan3='+hasilkaryawan3;		
	param+='&karyawan4='+karyawan4+'&hasilkaryawan4='+hasilkaryawan4;	
	param+='&karyawan5='+karyawan5+'&hasilkaryawan5='+hasilkaryawan5;	
	
	param+='&karyawan6='+karyawan6+'&hasilkaryawan6='+hasilkaryawan6;	
	param+='&karyawan7='+karyawan7+'&hasilkaryawan7='+hasilkaryawan7;	
	param+='&karyawan8='+karyawan8+'&hasilkaryawan8='+hasilkaryawan8;		
	param+='&karyawan9='+karyawan9+'&hasilkaryawan9='+hasilkaryawan9;	
	param+='&karyawan10='+karyawan10+'&hasilkaryawan10='+hasilkaryawan10;	

	param+='&karyawan11='+karyawan11+'&hasilkaryawan11='+hasilkaryawan11;	
	param+='&karyawan12='+karyawan12+'&hasilkaryawan12='+hasilkaryawan12;	
	param+='&karyawan13='+karyawan13+'&hasilkaryawan13='+hasilkaryawan13;		
	param+='&karyawan14='+karyawan14+'&hasilkaryawan14='+hasilkaryawan14;	
	param+='&karyawan15='+karyawan15+'&hasilkaryawan15='+hasilkaryawan15;
	
	param+='&keterangan='+keterangan+'&pengawas='+pengawas+'&asisten='+asisten+'&mengetahui='+mengetahui;		
	//param+=strUrl;	
	
	//alert(param);
	tujuan='kebun_slave_qc_semprot.php';
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






function edit(tgl,kdDiv,kdBlok)
{
	document.getElementById('tgl').value=tgl;
	//document.getElementById('kdDiv').value=kdDiv;
	document.getElementById('kdDiv').value=kdDiv;
	document.getElementById('kdBlok').value=kdBlok;
	

	
	param='method=getForm'+'&tgl='+tgl+'&kdBlok='+kdBlok;
	//alert(param);
	tujuan='kebun_slave_qc_semprot.php';
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
						
						/*ar=con.responseText.split("###");
						
						
						document.getElementById('kdAfd').value=ar[0];//??
						document.getElementById('kdBlok').value=ar[1];//??
						document.getElementById('kdKeg').value=ar[2];
						document.getElementById('karyawan1').value=ar[3];
						document.getElementById('hasilkaryawan1').value=ar[4];*/
						
						
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}






