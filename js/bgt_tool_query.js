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
                    document.getElementById('listData').style.display='block';
                    lockForm();
                    var res = document.getElementById('container');
                    res.innerHTML = con.responseText;	
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
function saveFranco2(fileTarget,passParam) {

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
                    document.getElementById('listData').style.display='block';
                    lockForm2();
                    var res = document.getElementById('container');
                    res.innerHTML = con.responseText;
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
function lockForm()
{
    //$arr="##thnBudget##unitId##klmpKeg##kegId##kdBgt##pilUn_1##method##actId";
    document.getElementById('klmpKeg').disabled=true;
    document.getElementById('pilUn_1').disabled=true;
    document.getElementById('unitId').disabled=true;
    document.getElementById('thnBudget').disabled=true;
    document.getElementById('tmblDt').disabled=true;
    document.getElementById('kegId').disabled=true;
    document.getElementById('kdBgt').disabled=true;
    document.getElementById('persenData').disabled=true;
    document.getElementById('sbUnit').disabled=true;
    document.getElementById('blokId').disabled=true;
    document.getElementById('kdBrgRev').disabled=true;
}
function unlockForm()
{
    //$arr="##listTransaksi##pilUn_1##unitId##periodeId##method";
    document.getElementById('klmpKeg').disabled=false;
    document.getElementById('pilUn_1').disabled=false;
    document.getElementById('unitId').disabled=false;
    document.getElementById('thnBudget').disabled=false;
    document.getElementById('blokId').disabled=false;
    document.getElementById('tmblDt').disabled=false;
    document.getElementById('kegId').disabled=false;
    document.getElementById('kdBgt').disabled=false;
    document.getElementById('persenData').disabled=false;
    document.getElementById('sbUnit').disabled=false;
    document.getElementById('kdBrgRev').disabled=true;
    document.getElementById('listData').style.display='none';
    var res = document.getElementById('container');
    res.innerHTML = "";
    document.getElementById('blokId').innerHTML =isi;
    document.getElementById('kegId').innerHTML = isi;
    document.getElementById('sbUnit').innerHTML = isi;
    document.getElementById('kdBrgRev').innerHTML=isi;
    document.getElementById('thnTnm').disabled=true;
}
function lockForm2()
{
    //##thnBudget2##unitId2##sbUnit2##blokId2##klmpKeg2##kegId2##kegIdR2##kdBgt2##pilUn_2##persenData2##method2";
    document.getElementById('klmpKeg2').disabled=true;
    document.getElementById('pilUn_2').disabled=true;
    document.getElementById('unitId2').disabled=true;
    document.getElementById('thnBudget2').disabled=true;
    document.getElementById('tmblDt2').disabled=true;
    document.getElementById('kegId2').disabled=true;
    document.getElementById('kdBgt2').disabled=true;
    document.getElementById('persenData2').disabled=true;
    document.getElementById('sbUnit2').disabled=true;
    document.getElementById('blokId2').disabled=true;
    document.getElementById('kegIdR2').disabled=true;
    document.getElementById('kdBgtR2').disabled=true;
    document.getElementById('kdBarang').disabled=true;
    document.getElementById('kdBrgLam').disabled=true;

}
function unlockForm2()
{
    //$arr="##listTransaksi##pilUn_1##unitId##periodeId##method";
    document.getElementById('klmpKeg2').disabled=false;
    document.getElementById('pilUn_2').disabled=false;
    document.getElementById('unitId2').disabled=false;
    document.getElementById('thnBudget2').disabled=false;
    document.getElementById('tmblDt2').disabled=false;
    document.getElementById('kegId2').disabled=false;
    document.getElementById('kdBgt2').disabled=false;
    document.getElementById('persenData2').disabled=false;
    document.getElementById('sbUnit2').disabled=false;
    document.getElementById('blokId2').disabled=false;
    document.getElementById('kegIdR2').disabled=true;
    document.getElementById('kdBarang').disabled=true;
    document.getElementById('kdBgtR2').disabled=true;
    document.getElementById('kegIdR2').value='';
    document.getElementById('kdBgtR2').value='';
    document.getElementById('kdBrgLam').disabled=true;
    document.getElementById('kdBrgLam').disabled=false;
    document.getElementById('kdBgtR2').innerHTML=isi;
    document.getElementById('listData').style.display='none';
    var res = document.getElementById('container');
    res.innerHTML = "";
    document.getElementById('kegIdR2').innerHTML =isi;
    document.getElementById('sbUnit2').innerHTML = isi;
    document.getElementById('kdBrgLam').innerHTML = isi
    document.getElementById('blokId2').innerHTML = isi
}

function getKegiatan()
{
        klmp=document.getElementById('klmpKeg').options[document.getElementById('klmpKeg').selectedIndex].value;
	param='method=getKeg'+'&klmpKeg='+klmp;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //alert(con.responseText);
                    var res = document.getElementById('kegId');
                    res.innerHTML = con.responseText;
                    
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getSubunit()
{
        klmp=document.getElementById('unitId').options[document.getElementById('unitId').selectedIndex].value;
        thn=document.getElementById('thnBudget').value;
	param='method=getSub'+'&unitId='+klmp+'&thnBudget='+thn;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                      //alert(con.responseText);
                     aret=con.responseText.split("###");
                     var res = document.getElementById('sbUnit');
                     res.innerHTML =aret[0];
                     if(aret[1]!="")
                         {
                             document.getElementById('thnTnm').disabled=false;
                             document.getElementById('thnTnm').innerHTML=aret[1];
                         }
                    

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getBlok()
{
        klmp=document.getElementById('sbUnit').options[document.getElementById('sbUnit').selectedIndex].value;
        thn=document.getElementById('thnBudget').value;
	param='method=getBlok'+'&sbUnit='+klmp+'&thnBudget='+thn;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                     //alert(con.responseText);
                    var res = document.getElementById('blokId');
                    res.innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getKegiatan2()
{
        klmp=document.getElementById('klmpKeg2').options[document.getElementById('klmpKeg2').selectedIndex].value;
	param='method=getKeg'+'&klmpKeg='+klmp;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    //alert(con.responseText);
                    var res = document.getElementById('kegId2');
                    res.innerHTML = con.responseText;
                    var res2 = document.getElementById('kegIdR2');
                    res2.innerHTML = res.innerHTML;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getSubunit2()
{
        klmp=document.getElementById('unitId2').options[document.getElementById('unitId2').selectedIndex].value;
        thn=document.getElementById('thnBudget2').value;
	param='method=getSub'+'&unitId='+klmp+'&thnBudget='+thn;
        
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                     //alert(con.responseText);
                    var res = document.getElementById('sbUnit2');
                    res.innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function getBlok2()
{
        klmp=document.getElementById('sbUnit2').options[document.getElementById('sbUnit2').selectedIndex].value;
        thn=document.getElementById('thnBudget2').value;
	param='method=getBlok'+'&sbUnit='+klmp+'&thnBudget='+thn;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                     //alert(con.responseText);
                    var res = document.getElementById('blokId2');
                    res.innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}
function getUnit()
{
        klmp=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
        thn=document.getElementById('thnBudget3').value;
	param='method=getUnit'+'&kdTraksi='+klmp+'&thnBudget='+thn;
	tujuan='bgt_slave_tool_query';
	post_response_text(tujuan+'.php', param, respon);
	function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                     //alert(con.responseText);
                    var res = document.getElementById('kdVhc');
                    res.innerHTML = con.responseText;

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
}

function revisi(maxRow)
{
//
	if(confirm('Anda Yakin Ingin Merevisi Data ..?'))
	{
		   loopClosingFisik(1,maxRow);
		   lockForm();
	}
	else
	{
		document.getElementById('revTmbl').disabled=false;
		return;
	}
}

function loopClosingFisik(currRow,maxRow)
{
    
        kegdt=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
        kdbuget=document.getElementById('kdBgt').options[document.getElementById('kdBgt').selectedIndex].value;
        pil=document.getElementById('pilUn_1').options[document.getElementById('pilUn_1').selectedIndex].value;
        thn=document.getElementById('thnBudget').value;
	index=document.getElementById('knci_'+currRow).innerHTML;
	jumRev=document.getElementById('jumRev_'+currRow).innerHTML;
	rupRev=document.getElementById('rupRev_'+currRow).innerHTML;
        volRev=document.getElementById('volRev_'+currRow).innerHTML;
        rotRev=document.getElementById('rotRev_'+currRow).innerHTML;
	
	param='method=saveRevisi';
	param+='&kegId='+kegdt+'&kdBgt='+kdbuget+'&pilUn_1='+pil+'&index='+index;
	param+='&jumRev='+jumRev+'&rupRev='+rupRev+'&rotRev='+rotRev+'&volRev='+volRev;
	tujuan = 'bgt_slave_tool_query.php';
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
						document.getElementById('revTmbl').disabled=false;
						tutupProses('simpan');
					}
					else
					{
						loopClosingFisik(currRow,maxRow);
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
function tutupProses(x)
{
	period=document.getElementById('revTmbl');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Terivisi");
			unlockForm();
			document.getElementById('container').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
}

function revisi2(maxRow)
{
//
	if(confirm('Anda Yakin Ingin Merevisi Data ..?'))
	{
		   loopClosingFisik2(1,maxRow);
		   lockForm2();
	}
	else
	{
		document.getElementById('revTmbl2').disabled=false;
		return;
	}
}

function loopClosingFisik2(currRow,maxRow)
{
//##thnBudget2##unitId2##sbUnit2##blokId2##klmpKeg2##kdBgt2##kegIdR2##kdBgt2##pilUn_2##persenData2##method2
        kegdt=document.getElementById('kdBgt2').options[document.getElementById('kdBgt2').selectedIndex].value;
        kdbuget=document.getElementById('kdBgt2').options[document.getElementById('kdBgt2').selectedIndex].value;
        pil=document.getElementById('pilUn_2').options[document.getElementById('pilUn_2').selectedIndex].value;
        unt=document.getElementById('unitId2').options[document.getElementById('unitId2').selectedIndex].value;
        thn=document.getElementById('thnBudget2').value;
	index=document.getElementById('knci_'+currRow).innerHTML;
	jumRev=document.getElementById('jumRev_'+currRow).innerHTML;
	rupRev=document.getElementById('rupRev_'+currRow).innerHTML;
        
        
        if(pil=='1')
        {
            volRev=document.getElementById('volRev_'+currRow).innerHTML;
        }
        if(pil=='9')
        {
            kdBgtRe=document.getElementById('kdBgtRe_'+currRow).innerHTML;
        }
        if(pil=='10')
        {
            kdbrgRev=document.getElementById('kdbrgRev_'+currRow).innerHTML;
        }
        if(pil=='2')
        {
           rotRev=document.getElementById('rotRev_'+currRow).innerHTML;
        }
        if(pil=='6')
        {
            revKeg=document.getElementById('revKeg_'+currRow).innerHTML;
        }
	param='method=saveRevisi2';
	param+='&kegId='+kegdt+'&kdBgt='+kdbuget+'&pilUn_1='+pil+'&index='+index+'&unitId2='+unt;
	param+='&jumRev='+jumRev+'&rupRev='+rupRev+'&thnBudget2='+thn;
        if(pil=='6')
        {
            param+='&revKeg='+revKeg;
        }
        if(pil=='1')
        {
            param+='&volRev='+volRev;
        }
        if(pil=='2')
        {
            param+='&rotRev='+rotRev;
        }
        if(pil=='10')
        {
            param+='&kdbrgRev='+kdbrgRev;
        }
         if(pil=='9')
        {
              param+='&kdBgtRe='+kdBgtRe;
        }
	tujuan = 'bgt_slave_tool_query.php';
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
						tutupProses2();
						//unlockScreen();
					}
					if(currRow>maxRow)
					{
						document.getElementById('revTmbl2').disabled=false;
						tutupProses2('simpan');
					}
					else
					{
						loopClosingFisik2(currRow,maxRow);
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
function tutupProses2(x)
{
	period=document.getElementById('revTmbl2');
	if(period.disabled!=true)
	{
		if (x == 'simpan') {
			unlockScreen();
			alert("Data Telah Terivisi");
			unlockForm2();
			document.getElementById('container').innerHTML='';
		}
		else
		{
			unlockScreen();
		}
	}
}
function pilGant()
{
    //$pil2=array("1"=>"VOLUME","2"=>"ROTASI","3"=>"FISIK","4"=>"RUPIAH","5"=>"HAPUS DATA","6"=>"KEGIATAN","7"=>"UNCLOSE BLOK","8"=>"Unclose Upah","9"=>"Kode Budget","10"=>"Material");
    pil=document.getElementById('pilUn_2').options[document.getElementById('pilUn_2').selectedIndex].value;
        document.getElementById('kegIdR2').disabled=true;
        document.getElementById('kdBgtR2').disabled=true;
        document.getElementById('kdBarang').disabled=true;
        document.getElementById('kdBrgLam').disabled=true;
    if(pil=='6')
    {
        document.getElementById('kegIdR2').disabled=false;
        document.getElementById('kdBgtR2').disabled=true;
        document.getElementById('kdBarang').disabled=true;
        document.getElementById('kdBrgLam').disabled=true;
    }
    if(pil=='9')
    {
        document.getElementById('kegIdR2').disabled=true;
        document.getElementById('kdBgtR2').disabled=false;
        document.getElementById('kdBarang').disabled=true;
        document.getElementById('kdBrgLam').disabled=true;
    }
    if(pil=='10')
    {
        document.getElementById('kdBrgLam').disabled=false;
        document.getElementById('kegIdR2').disabled=true;
        document.getElementById('kdBgtR2').disabled=true;
        document.getElementById('kdBgt2').disabled=true;
        document.getElementById('kdBarang').disabled=true;
        document.getElementById('kdBgtR2').value='';
        document.getElementById('kdBgt2').value='';
        document.getElementById('kdBgt2').value='';
    }
}
function searchBrg(title,content,ev)
{
        width='500';
	height='400';
	showDialog1(title,content,width,height,ev);
}
function findBrg()
{
    nmBrg=document.getElementById('nmBrg').value;
    kdBarang=document.getElementById('kdBarang').value;
    param='nmBrg='+nmBrg+'&method=getBarang';
    tujuan='bgt_slave_tool_query.php';
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
                                           document.getElementById('containerBarang').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }
	 }

}
function setData(kdbrg,namaBarang)
{
    document.getElementById('kdBarang').value=kdbrg;
    document.getElementById('namaBrg').innerHTML=namaBarang;
    closeDialog();
}
function apDate()
{
    thn=document.getElementById('thnBudget3').options[document.getElementById('thnBudget3').selectedIndex].value;
    kdv=document.getElementById('kdVhc').options[document.getElementById('kdVhc').selectedIndex].value;
    kdtr=document.getElementById('kdTraksi').options[document.getElementById('kdTraksi').selectedIndex].value;
    param='kdVhc='+kdv+'&method=updateVhc';
    param+='&kdTraksi='+kdtr+'&thnBudget3='+thn;
    tujuan='bgt_slave_tool_query.php';
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
                                          // document.getElementById('containerBarang').innerHTML=con.responseText;
                                          if(con.responseText==1)
                                              {
                                                  alert("Data Berhasil Di Update");
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
function getBarang()
{
    thn=document.getElementById('thnBudget2').options[document.getElementById('thnBudget2').selectedIndex].value;
    kdv=document.getElementById('unitId2').options[document.getElementById('unitId2').selectedIndex].value;
    kdtr=document.getElementById('kegId2').options[document.getElementById('kegId2').selectedIndex].value;
    param='thnBudget2='+thn+'&method=getBrg';
    param+='&unitId2='+kdv+'&kegId2='+kdtr;
    tujuan='bgt_slave_tool_query.php';
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
                                          document.getElementById('kdBrgLam').disabled=false;
                                          document.getElementById('kdBrgLam').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }
	 }
}

function getBarangRev()
{
    thn=document.getElementById('thnBudget').options[document.getElementById('thnBudget').selectedIndex].value;
    kdB=document.getElementById('kdBgt').options[document.getElementById('kdBgt').selectedIndex].value;
    kdtr=document.getElementById('kegId').options[document.getElementById('kegId').selectedIndex].value;
    kd=document.getElementById('unitId').options[document.getElementById('unitId').selectedIndex].value;
    param='thnBudget='+thn+'&method=getBrgRev';
    param+='&kdBgt='+kdB+'&kegId='+kdtr+'&unitId='+kd;
    tujuan='bgt_slave_tool_query.php';
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
                                          ader=con.responseText.split("###");
                                          document.getElementById('kdBrgRev').disabled=true;
                                          if(ader[0]>=1)
                                              {
                                                  document.getElementById('kdBrgRev').disabled=false;
                                              }
                                              document.getElementById('kdBrgRev').innerHTML=ader[1];
                                          
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
              }
	 }
}