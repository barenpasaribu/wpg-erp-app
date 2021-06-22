/**

 * @author antoniuslouis

 */

function loadData(num){
	ktrng=document.getElementById('ktrnganCr').value;
	tglcr=document.getElementById('tgl_cari').value;
	param='method=loadData';
	if(tglcr!=''){
		param+='&tgl_cari='+tglcr;    
	}

	if(ktrng!=''){
		param+='&ktrnganCr='+ktrng;
	}

	param+='&page='+num;
	tujuan='sdm_slave_save_5harilibur.php';
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
	if(document.getElementById('ishariraya-a').checked){
		ishariraya = document.getElementById("ishariraya-a").value;
	}
	
	if(document.getElementById('ishariraya-b').checked){
		ishariraya = document.getElementById("ishariraya-b").value;
	}

	if(tglis==''){
		alert('Date is obligatory');
		return;
	}
	
	param='tanggal='+tglis+'&ket='+ktrngan+'&method='+met+'&regId='+reg+'&ishariraya='+ishariraya;

	if(met=='update'){
		tgllm=document.getElementById('tglOld').value;
		param+='&tglOld='+tgllm;
	}

	tujuan='sdm_slave_save_5harilibur.php';
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



function fillField(tnggl,ktrngnf,ishariraya){
	document.getElementById('tgl').value=tnggl;
	document.getElementById('tglOld').value=tnggl;
	document.getElementById('ktrngan').value=ktrngnf;
	
	if(ishariraya == 1){
		document.getElementById('ishariraya-a').checked = true;
	}else{
		document.getElementById('ishariraya-b').checked = true;
	}

	document.getElementById('method').value='update';
}



function cancelJ(){
	document.getElementById('tgl').value='';
	document.getElementById('ktrngan').value='';
	document.getElementById('tglOld').value='';
	document.getElementById('ishariraya-a').checked = false;
	document.getElementById('ishariraya-b').checked = false;
	
	document.getElementById('method').value='insert';		
}

