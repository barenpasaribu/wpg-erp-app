// JavaScript Document
function loadData()
{
	param='proses=loadData';
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('list_ganti').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cariBast(num)
{
		param='proses=loadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_rekomendasiPupuk.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('list_ganti').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
}
function add_new_data()
{
	document.getElementById('list_ganti').style.display='none';
	document.getElementById('headher').style.display='block';
	document.getElementById('proses').value='insert';
	
	//document.getElementById('trans_no').disabled= true;
	bersihForm();
}
function bersihForm()
{
	document.getElementById('periode').disabled=false;
	document.getElementById('idKbn').disabled=false;
	document.getElementById('thnTnm').disabled=false;
	document.getElementById('jnsPpk').value='';
	document.getElementById('dosis').value='0';
	//document.getElementById('dosis2').value='0';
	//document.getElementById('dosis3').value='0';
	document.getElementById('idKbn').value='';
	document.getElementById('idBlok').innerHTML='';
	document.getElementById('thnTnm').value='';
	
}
function getBlok(kdafd,kdblk)
{
	if((kdafd=='0')&&(kdblk=='0'))
	{
		kdAfd=document.getElementById('idKbn').options[document.getElementById('idKbn').selectedIndex].value;
		param='kdAfd='+kdAfd+'&proses=getBlok';
	}
	else
	{
		kdAfd=kdafd;
		kdBlok=kdblk;
		param='kdAfd='+kdAfd+'&proses=getBlok'+'&kdBlok='+kdBlok;
	}
	//alert(param);
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('idBlok').innerHTML=con.responseText;
					if(kdblk!='0')
					{
						getThn();
					}
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getThn()
{

	kdblok=document.getElementById('idBlok').options[document.getElementById('idBlok').selectedIndex].value;
	param='kdBlok='+kdblok+'&proses=getThn';	
	//alert(param);
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					//document.getElementById('thnTnm').disabled=true;
                    //document.getElementById('thnTnm').value = con.responseText;
                    //document.getElementById('thnTnm').options[document.getElementById('thnTnm').selectedIndex].value = con.responseText;
                    //document.getElementById('thnTnm').options[document.getElementById('thnTnm').selectedIndex].text = con.responseText;
                    document.getElementById('thnTnm').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function displayList()
{
	document.getElementById('list_ganti').style.display='block';
	document.getElementById('headher').style.display='none';
	loadData();
	document.getElementById('crKbn').value='';
	document.getElementById('crPeriode').value='';
}
function getSatuan()
{
	kdBrg=document.getElementById('jnsPpk').options[document.getElementById('jnsPpk').selectedIndex].value;
	param='kdBrg='+kdBrg+'&proses=getSatuan';
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					document.getElementById('satuan').innerHTML=con.responseText;
					document.getElementById('satuan2').innerHTML=con.responseText;
					document.getElementById('satuan3').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	
}
function cancelSave()
{
	prose=document.getElementById('proses').value;
	if(prose=='insert')
	{
		prode=document.getElementById('periode').value;
		thntnm=document.getElementById('thnTnm').value;
		idkbn=document.getElementById('idKbn').options[document.getElementById('idKbn').selectedIndex].value;
		kdblk=document.getElementById('idBlok').options[document.getElementById('idBlok').selectedIndex].value;
		delDataC(prode,idkbn,thntnm,kdblk);
		//displayList();
	}
	else if(prose=='update')
	{
		displayList();
	}
}
function saveData()
{
	prode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
	idkbn=document.getElementById('idKbn').options[document.getElementById('idKbn').selectedIndex].value;
	kdBlok=document.getElementById('idBlok').options[document.getElementById('idBlok').selectedIndex].value;
	thntnm=document.getElementById('thnTnm').value;
	jndppk=document.getElementById('jnsPpk').options[document.getElementById('jnsPpk').selectedIndex].value;

	dsis=document.getElementById('dosis').value;
	//dsis2=document.getElementById('dosis2').value;
	//dsis3=document.getElementById('dosis3').value;
	jnsbibit=document.getElementById('jnsBibit').value;
	satn=document.getElementById('satuan').innerHTML;
	prose=document.getElementById('proses').value;
	if(prose=='update')
	{
		oldBlok=document.getElementById('oldBlok').value;		
	    //param='periode='+prode+'&idKbn='+idkbn+'&thnTnm='+thntnm+'&jnsPpk='+jndppk+'&dosis='+dsis+'&dosis2='+dsis2+'&dosis3='+dsis3+'&jnsBibit='+jnsbibit;
		param = 'periode=' + prode + '&idKbn=' + idkbn + '&thnTnm=' + thntnm + '&jnsPpk=' + jndppk + '&dosis=' + dsis + '&jnsBibit=' + jnsbibit;
		param +='&satuan='+satn+'&proses='+prose+'&kdBlok='+kdBlok+'&oldBlok='+oldBlok;
	}
	else
	{
	    //param='periode='+prode+'&idKbn='+idkbn+'&thnTnm='+thntnm+'&jnsPpk='+jndppk+'&dosis='+dsis+'&dosis2='+dsis2+'&dosis3='+dsis3+'&jnsBibit='+jnsbibit;
	    param = 'periode=' + prode + '&idKbn=' + idkbn + '&thnTnm=' + thntnm + '&jnsPpk=' + jndppk + '&dosis=' + dsis + '&jnsBibit=' + jnsbibit;
		param +='&satuan='+satn+'&proses='+prose+'&kdBlok='+kdBlok;
	}
	//alert(param);
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					//alert(con.responseText);
					displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function fillField(period,kdorg,thntnm,blk)
{
	idKbn=kdorg;
	thnTnm=thntnm;
	periode=period;
	kdBlok=blk;
	param='idKbn='+idKbn+'&proses=getData'+'&thnTnm='+thnTnm+'&periode='+periode+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_rekomendasiPupuk.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
					document.getElementById('proses').value='update';
					ar=con.responseText.split("###");
					document.getElementById('periode').value=ar[4];
					document.getElementById('idKbn').value=ar[0];
					document.getElementById('jnsPpk').value=ar[1];
					document.getElementById('dosis').value=ar[2];
					//document.getElementById('dosis2').value=ar[7];
					//document.getElementById('dosis3').value=ar[8];
					document.getElementById('jnsBibit').value=ar[5];
					document.getElementById('satuan').innerHTML=ar[3];
					document.getElementById('list_ganti').style.display='none';
					document.getElementById('headher').style.display='block';
					document.getElementById('periode').disabled=true;
					document.getElementById('idKbn').disabled=true;
					//document.getElementById('thnTnm').disabled=true;
					document.getElementById('oldBlok').value=ar[6];
					document.getElementById('thnTnm').value=ar[7];
					getBlok(ar[0],ar[6]);
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	post_response_text(tujuan, param, respon);
	
}
function delData(period,kdorg,thntnm,kdblk)
{
	idKbn=kdorg;
	thnTnm=thntnm;
	kdBlok=kdblk;
	periode=period;
	param='idKbn='+idKbn+'&proses=delData'+'&thnTnm='+thnTnm+'&periode='+periode+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_rekomendasiPupuk.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						displayList();
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Are You Sure Want Delete This Data !!!"))
	post_response_text(tujuan, param, respon);
	
}

function delDataC(period,kdorg,thntnm,kdblk)
{
	idKbn=kdorg;
	thnTnm=thntnm;
	periode=period;
	kdBlok=kdblk;
	param='idKbn='+idKbn+'&proses=delData'+'&thnTnm='+thnTnm+'&periode='+periode+'&kdBlok='+kdBlok;
	tujuan='kebun_slave_rekomendasiPupuk.php';
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						displayList();
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
	if(confirm("Are You Sure Want Delete This Data !!!"))
	post_response_text(tujuan, param, respon);
}
function cariData()
{
	periode=document.getElementById('crPeriode').value;
	idKbn=document.getElementById('crKbn').options[document.getElementById('crKbn').selectedIndex].value;
	param='periode='+periode+'&idKbn='+idKbn+'&proses=cariData';
	tujuan='kebun_slave_rekomendasiPupuk.php';
	post_response_text(tujuan, param, respon);
	function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
						//displayList();
						document.getElementById('list_ganti').innerHTML=con.responseText;
						
	             }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function cariHasil(num)
{
                periode=document.getElementById('crPeriode').value;
                idKbn=document.getElementById('crKbn').options[document.getElementById('crKbn').selectedIndex].value;
                param='periode='+periode+'&idKbn='+idKbn+'&proses=cariData';
		param+='&page='+num;
		tujuan = 'kebun_slave_rekomendasiPupuk.php';
		post_response_text(tujuan, param, respog);			
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('list_ganti').innerHTML=con.responseText;
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
	judul=jdlExcel;
	//alert(param);	
	param='';
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
