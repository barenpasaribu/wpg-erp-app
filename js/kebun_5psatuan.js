//JS 

function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'kebun_slave_5psatuan.php';
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

function simpan()
{
	regional=document.getElementById('regional').value;
	kdkegiatan=document.getElementById('kdkegiatan').value;
	rp=document.getElementById('rp').value;
	insen=document.getElementById('insen').value;
	if(document.getElementById('konversi').checked==true)
	   konversi=1;
	else
	   konversi=0; 
	
	method=document.getElementById('method').value;
	

	
	if(regional=='' || kdkegiatan=='')
	{
		alert('Fild masih kosong');
		return;
	}
/*	else
	{
		
		kdorg=trim(kdorg);
		kdkegiatan=trim(kdkegiatan);
		rp=trim(rp);
		insen=trim(insen);*/

	param='regional='+regional+'&kdkegiatan='+kdkegiatan+'&rp='+rp+'&insen='+insen+'&konversi='+konversi+'&method='+method;
	
	//alert(param);

	tujuan='kebun_slave_5psatuan.php';
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
					


function cancel()
{
	document.location.reload();
}




function loadData (num) {
	kdkegiatan=document.getElementById('kdkegiatanCr').value;
	rp=document.getElementById('rpCr').value;
	insen=document.getElementById('insenCr').value;
	if(document.getElementById('konversiCr').checked==true)
	   konversi=1;
	else
	   konversi=0; 
	param='method=loadData';
        param+='&kdkegiatan='+kdkegiatan+'&rp='+rp+'&insen='+insen+'&konversi='+konversi;
        param+='&page='+num;
	tujuan='kebun_slave_5psatuan.php';
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

function edit(regional,kdkegiatan,rp,insen,konversi)
{
	document.getElementById('regional').value=regional;
	document.getElementById('kdkegiatan').value=kdkegiatan;
	document.getElementById('kdkegiatan').disabled=true;
	document.getElementById('rp').value=rp;
	document.getElementById('insen').value=insen;
	if(konversi==1)
	{
		document.getElementById('konversi').checked=true;
	}
	else
	{
		document.getElementById('konversi').checked=false;
	}
	document.getElementById('method').value='update';
}



function del(kdorg,kdkegiatan)
{
	param='method=delete'+'&kdorg='+kdorg+'&kdkegiatan='+kdkegiatan;
	//alert(param);
	tujuan='kebun_slave_5psatuan.php';
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
        noakun=document.getElementById('kdkegiatanCrPrsn');
        noakun=noakun.options[noakun.selectedIndex].value;
        rp=document.getElementById('prsnrpCr').value;
	insen=document.getElementById('prsninsenCr').value;
        param='method=upGradeData'+'&prsnrpCr='+rp+'&prsninsenCr='+insen;
        param+='&noakun='+noakun;
	//alert(param);
	tujuan='kebun_slave_5psatuan.php';
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



