// JavaScript Document
function btlTblbdya ()
{
	document.getElementById('kd_org').value='';
	document.getElementById('kd_budidaya').disabled=false;
	document.getElementById('kd_budidaya').value='';
	document.getElementById('ket').value='';
}
function simpanTblbudaya()
{
	kode=trim(document.getElementById('kd_budidaya').value);
	kodeorg=trim(document.getElementById('kd_org').value);
	budidaya=trim(document.getElementById('ket').value);
	method=trim(document.getElementById('method').value);
	//param='kode='+kode+'&kodeorg='+kodeorg+'&budaya='+budaya+'&method='+method;
	param='kode='+kode+'&kodeorg='+kodeorg+'&budidaya='+budidaya+'&method='+method;
	tujuan='log_slave_tbl_budaya.php';
	if(kode=='' || budidaya=='')
	{
		alert('Data inconsistent');
	}
	else {
		if(confirm('Are you sure?'))
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
						else {
							//alert(con.responseText);
							document.getElementById('container').innerHTML=con.responseText;
							btlTblbdya();
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
}
function fillField (kode,kodeorg,budidaya)
{
	kd_budidaya=document.getElementById('kd_budidaya');
	kd_budidaya.value=kode;
	kd_budidaya.disabled=true;
	kd_org=document.getElementById('kd_org');
	kd_org.value=kodeorg;
	ket=document.getElementById('ket');
	ket.value=budidaya;
	document.getElementById('method').value='update';
		
}

function delTbldya(kode)
{
        param='kode='+kode;
		param+='&method=delete';
		tujuan='log_slave_tbl_budaya.php';
		if(confirm('Deleting, Are you sure?'))
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