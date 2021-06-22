//JS 
function simpan(){
	regional=document.getElementById('regional').value;
        kdkeg=document.getElementById('kdKegiatan').value;
        nmkeg=document.getElementById('nmKegiatan').value;
	sat=document.getElementById('satuan').value;
	noakn=document.getElementById('noakun');
        noakn=noakn.options[noakn.selectedIndex].value;
        bsis=document.getElementById('basis').value;
        hrgSat=document.getElementById('hrgSatuan').value;
        hrgMing=document.getElementById('hrgHrMngg').value;
        hrgLbh=document.getElementById('hrgLbhBasis').value;
	at=document.getElementById('auto');
        at=at.options[at.selectedIndex].value;
	method=document.getElementById('method').value;
	param='regional='+regional+'&kdkegiatan='+kdkeg+'&nmKegiatan='+nmkeg;
        param+='&satuan='+sat+'&noakun='+noakn+'&method='+method+'&auto='+at;
        param+='&basis'+bsis+'&hrgSatuan='+hrgSat+'&hrgHrMngg='+hrgMing+'&hrgLbhBasis='+hrgLbh;
	tujuan='vhc_slave_5jenisKegiatan.php';
    post_response_text(tujuan, param, respog);		
	//}
	
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
                                                        loadData(0);
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
					


function cancel(){
	document.getElementById('kdKegiatan').value="";
        document.getElementById('nmKegiatan').value="";
	document.getElementById('satuan').value="";
	document.getElementById('noakun').value="";
        document.getElementById('basis').value="";
        document.getElementById('hrgSatuan').value="";
        document.getElementById('hrgHrMngg').value="";
        document.getElementById('hrgLbhBasis').value="";
        document.getElementById('method').value="insert";
        document.getElementById('auto').value="";
}




function loadData (num) {
	noakundt=document.getElementById('noakunCr');
        noakundt=noakundt.options[noakundt.selectedIndex].value;
        auto=document.getElementById('autoCr');
        auto=auto.options[auto.selectedIndex].value;
	nmKeg=document.getElementById('nmKegiatanCr').value;
	param='method=loadData';
        param+='&noakunCr='+noakundt+'&nmKegiatanCr='+nmKeg;
        param+='&page='+num+'&autoCr='+auto;
	tujuan='vhc_slave_5jenisKegiatan.php';
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

function edit(kdkegiatan,nmkeg,sat,nakun,bsis,hrglbhbsis,hrgmnggu,konversi,hrgsat){
	document.getElementById('kdKegiatan').value=kdkegiatan;
        document.getElementById('nmKegiatan').value=nmkeg;
	document.getElementById('satuan').value=sat;
	//document.getElementById('noakun').value=nakun;
        document.getElementById('basis').value=bsis;
        document.getElementById('hrgLbhBasis').value=hrglbhbsis;
        document.getElementById('hrgSatuan').value=hrgsat;
        document.getElementById('hrgHrMngg').value=hrgmnggu;
        jk=document.getElementById('noakun');
	for(x=0;x<jk.length;x++)
	{
		if(jk.options[x].value==nakun)
		{
			jk.options[x].selected=true;
		}
	}
        jkd=document.getElementById('auto');
	for(x=0;x<jkd.length;x++)
	{
		if(jkd.options[x].value==konversi)
		{
			jkd.options[x].selected=true;
		}
	}
	 
	document.getElementById('method').value='update';
        document.getElementById('kdKegiatan').disabled=true;
}



function del(kdorg,kdkegiatan)
{
	param='method=delete'+'&kdorg='+kdorg+'&kdkegiatan='+kdkegiatan;
	//alert(param);
	tujuan='vhc_slave_5jenisKegiatan.php';
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
						loadData(0);
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}
function upGrade(){
    //kdkegiatanCrPrsn
        bsis=document.getElementById('bsisPrsn').value;
	hrgsat=document.getElementById('hrgStnPrsn').value;
        hrglbh=document.getElementById('hrgLbhBsisPrsn').value;
        hrgmngg=document.getElementById('hrgMnggPrsn').value;
        param='method=upGradeData'+'&bsisPrsn='+bsis+'&hrgStnPrsn='+hrgsat;
        param+='&hrgLbhBsisPrsn='+hrglbh+'&hrgMnggPrsn='+hrgmngg;
        
	//alert(param);
	tujuan='vhc_slave_5jenisKegiatan.php';
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
						loadData(0);
                                                document.getlementById('bsisPrsn').value='';
                                                document.getlementById('hrgStnPrsn').value='';
                                                document.getlementById('hrgLbhBsisPrsn').value='';
                                                document.getlementById('hrgMnggPrsn').value='';
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}
}



