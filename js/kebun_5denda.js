/**
 * @author antoniuslouis
 */
function loadData(num){
        param='method=loadData';
        param+='&page='+num;
	tujuan='kebun_slave_save_5denda.php';
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
function simpanJ(){
	met=document.getElementById('method').value;
        tglis=document.getElementById('tgl').value;
        reg=document.getElementById('regId').value;
        ktrngan=document.getElementById('ktrngan').value;
	if(regId==''){
		alert('Code is obligatory');
                return;
	}
        if(ktrngan==''){
            	alert('Jumlah is obligatory');
                return;
        }
            param='tanggal='+tglis+'&ket='+ktrngan+'&method='+met+'&regId='+reg;
            if(met=='update'){
                tgllm=document.getElementById('tglOld').value;
                param+='&tglOld='+tgllm;
            }
            tujuan='kebun_slave_save_5denda.php';
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
                                                        cancelJ();
							loadData();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }
		
}

function fillField(tnggl,ktrngnf,jmlh){
    document.getElementById('tgl').value=ktrngnf;
    
    document.getElementById('ktrngan').value=jmlh;
    document.getElementById('regId').value=tnggl;
    document.getElementById('regId').disabled=true;
    document.getElementById('method').value='update';
}

function cancelJ(){
	document.getElementById('tgl').value='';
	document.getElementById('ktrngan').value='';
	document.getElementById('regId').value='';
	document.getElementById('method').value='insert';		
        document.getElementById('regId').disabled=false;
}
