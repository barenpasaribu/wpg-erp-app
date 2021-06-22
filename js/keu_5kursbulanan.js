//JS 

function cariBast(num)
{
		param='method=loadData';
		param+='&page='+num;
		tujuan = 'keu_slave_5kursbulanan.php';
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

function simpan(){
	prdDt=document.getElementById('periodeDt');
        prdDt=prdDt.options[prdDt.selectedIndex].value;
	mtUangdt=document.getElementById('mtUang');
        mtUangdt=mtUangdt.options[mtUangdt.selectedIndex].value;
	krs=document.getElementById('krsDt').value;
	method=document.getElementById('method').value;
        mtUng=document.getElementById('mtUangold').value;
        prdold=document.getElementById('periodeold').value;
	if(prdDt=='' || mtUangdt=='' || krs==''){
		alert("All field can't empty");
		return;
	}
	param='periodeDt='+prdDt+'&mtUang='+mtUangdt+'&krsDt='+krs+'&method='+method;
        param+='&mtUangold='+mtUng+'&periodeDtold='+prdold;
	tujuan='keu_slave_5kursbulanan.php';
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
	prdcr=document.getElementById('periodeCr').value;
	mtUng=document.getElementById('mtUangCr');
        mtUng=mtUng.options[mtUng.selectedIndex].value; 
	param='method=loadData';
        param+='&periode='+prdcr+'&mtUang='+mtUng;
        param+='&page='+num;
	tujuan='keu_slave_5kursbulanan.php';
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

function edit(prddt,matuang,krs){
	  kar=document.getElementById('periodeDt');
          for(x=0;x<kar.length;x++){
            if(kar.options[x].value==prddt){
                    kar.options[x].selected=true;
            }
          }
          karmtuang=document.getElementById('mtUang');
          for(x=0;x<karmtuang.length;x++){
            if(karmtuang.options[x].value==matuang){
                    karmtuang.options[x].selected=true;
            }
          }
	document.getElementById('periodeold').value=prddt;
	document.getElementById('mtUangold').value=matuang;
        document.getElementById('krsDt').value=krs;
	document.getElementById('method').value='update';
}



function del(prd,mtuang){
	param='method=delete'+'&periodeDt='+prd+'&mtUang='+mtuang;
	//alert(param);
	tujuan='keu_slave_5kursbulanan.php';
        if(confirm("Are you sure delete this data?")){
            post_response_text(tujuan, param, respog);	
        }
	
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
function bersihField(){
    document.getElementById('periodeCr').value="";
}

