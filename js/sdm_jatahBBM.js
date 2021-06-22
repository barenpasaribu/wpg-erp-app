/**
 * @author antoniuslouis
 */

 function saveJatah(karid)
 {
 	val=document.getElementById(karid).value;
	if(val=='')
	 alert('Value is empty');
	 else if (val==0)
	 alert('Value is 0');
	else
	{
		param='val='+val+'&karyawanid='+karid;
	    tujuan='sdm_slaveSaveJatahBBM.php';
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
							document.getElementById(karid).style.backgroundColor='#E8F4F4';
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
 }

 function getNotransaksi(periode)
 {
 		param='periode='+periode;
	    tujuan='sdm_slave_getBBMNumber.php';
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
							document.getElementById('notransaksi').value=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
 }
 
 function saveBBM()
 {
 	periode		=document.getElementById('periode');
	karyawanid	=document.getElementById('karyawanid');
	pt			=document.getElementById('pt');
	
	periode		=periode.options[periode.selectedIndex].value;
	karyawanid	=karyawanid.options[karyawanid.selectedIndex].value;
	pt			=pt.options[pt.selectedIndex].value;		
	notransaksi =document.getElementById('notransaksi').value;
	keterangan 	=document.getElementById('keterangan').value;
	bytransport =remove_comma(document.getElementById('bytransport'));
	byperawatan =remove_comma(document.getElementById('byperawatan'));
	bytoll 		=remove_comma(document.getElementById('bytoll'));
	bylain 		=remove_comma(document.getElementById('bylain'));
	total		=remove_comma(document.getElementById('total'));
	method=document.getElementById('method').value;
//=============
   if(periode=='' || notransaksi=='')
   {
   	alert('Transaction number is obligatory');
   }
   else if(total=='' || parseFloat(total)==0.00)
   {
   	alert('Please Enter Cost');
   }
   else
   {
   	param='periode='+periode+'&karyawanid='+karyawanid+'&pt='+pt;
	param+='&notransaksi='+notransaksi+'&keterangan='+keterangan;
	param+='&bytransport='+bytransport+'&byperawatan='+byperawatan;
	param+='&bytoll='+bytoll+'&bylain='+bylain+'&total='+total;
	param+='&method='+method;
	
	if (confirm('Saving..?')) {
		tujuan = 'sdm_slave_penggantianBBM.php';
		post_response_text(tujuan, param, respog);
	}
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
							document.getElementById('savebtn').disabled=true;
							document.getElementById('periode').disabled=true;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	  
 }
 
function deleteBBM(notransaksi)
{
	periode		=document.getElementById('periox').options[document.getElementById('periox').selectedIndex].value;
	param='method=delete&notransaksi='+notransaksi+'&periode='+periode;
	if (confirm('Deleting '+notransaksi +', are you sure..?')) {
		tujuan = 'sdm_slave_penggantianBBM.php';
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
							document.getElementById('savebtn').disabled=true;
							document.getElementById('periode').disabled=true;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
} 


function previewBBM(notransaksi,ev)
{
	
	tujuan='sdm_laporanPenggantianTransport_pdf.php';
	title='Report PDF';	
	param=tujuan+'?notransaksi='+notransaksi;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+param+"'></iframe>"
   showDialog1(title,content,width,height,ev);		
}

function previewBBMPeriode(ev)
{
	periode		=document.getElementById('periox').options[document.getElementById('periox').selectedIndex].value;
	tujuan='sdm_laporanPenggantianTransportPeriode_pdf.php';
	title='Report PDF';	
	param=tujuan+'?periode='+periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+param+"'></iframe>"
   showDialog1(title,content,width,height,ev);		
}
 function calculateTotal()
 {
	bytransport =remove_comma(document.getElementById('bytransport'));
	byperawatan =remove_comma(document.getElementById('byperawatan'));
	bytoll 		=remove_comma(document.getElementById('bytoll'));
	bylain 		=remove_comma(document.getElementById('bylain'));
	total		= parseFloat(bytransport)+parseFloat(byperawatan)+parseFloat(bytoll)+parseFloat(bylain);
	document.getElementById('total').value=total;
	change_number(document.getElementById('total'));
 }

function cancelBBM()
{
	document.getElementById('periode').disabled=false;
	document.getElementById('savebtn').disabled=false;		
	document.getElementById('keterangan').value='';
	document.getElementById('bytransport').value=0;
	document.getElementById('byperawatan').value=0;
	document.getElementById('bytoll').value=0;
	document.getElementById('bylain').value=0;
	document.getElementById('total').value=0;
	getNotransaksi(periode);	
	document.getElementById('containerSolar').innerHTML='';
}
function getData(periode)
{
	param='periode='+periode;
		tujuan = 'sdm_slave_penggantianBBM.php';
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

function saveLitre()
{
	notransaksi =document.getElementById('notransaksi').value;
	tanggal=document.getElementById('tanggal').value;
	totalharga=document.getElementById('totalharga').value;
	jlhbbm=document.getElementById('jlhbbm').value;
	if(jlhbbm=='')
	  jlhbbm=0;
	if(totalharga=='')
	  totalharga=0;  
	if (tanggal.length != 10 || jlhbbm == 0 || totalharga==0) {
		alert('Date,price and volume are obligatory');
	}
	else {
		param = 'notransaksi=' + notransaksi + '&tanggal=' + tanggal + '&jlhbbm=' + jlhbbm;
		param+='&method=insert&totalharga='+totalharga;
		tujuan = 'sdm_slave_saveJlhBBM.php';
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
							curr_total=parseFloat(remove_comma_var(document.getElementById('total').value));
							totalharga=parseFloat(totalharga); 
							document.getElementById('total').value=	curr_total+totalharga;
							change_number(document.getElementById('total'));
							document.getElementById('containerSolar').innerHTML=con.responseText;
							document.getElementById('tanggal').value='';
							document.getElementById('jlhbbm').value=0;							
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }		
}
function deleteSolar(notransaksi,tanggal,idcell)
{
	param='method=delete&notransaksi='+notransaksi+'&tanggal='+tanggal;

	nilaicell=parseFloat(remove_comma_var(document.getElementById(idcell).innerHTML));
	
	tujuan = 'sdm_slave_saveJlhBBM.php';
	if(confirm('Deleting are you sure..?')){	
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
							curr_total=parseFloat(remove_comma_var(document.getElementById('total').value));
							document.getElementById('total').value=	curr_total-nilaicell;	
							change_number(document.getElementById('total'));							
							document.getElementById('containerSolar').innerHTML=con.responseText;						
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}
function saveBBMClaim(no,notransaksi)
{
	bayar=remove_comma(document.getElementById('bayar'+no));
	tglbayar=remove_comma(document.getElementById('tglbayar'+no));
	
	if(notransaksi=='' || bayar=='' || tglbayar.length!=10)
	{
		alert('Data incomplete');
	}
	else if(bayar==0.00)
	{
		alert('Payment can not be 0');
	}
	else
	{
		param='notransaksi='+notransaksi+'&bayar='+bayar+'&tglbayar='+tglbayar;
		if(confirm('Saving payment '+notransaksi+', Are you sure..?'))
		tujuan='sdm_simpanPembayaranBBM.php';
		post_response_text(tujuan, param, respog);
	}
	function respog()
	{
		      if(con.readyState==4)
		      {
			        if (con.status == 200) {
						busy_off();
						if (!isSaveResponse(con.responseText)) {
							document.getElementById('bayar'+no).style.backgroundColor='red';
							alert('ERROR TRANSACTION,\n' + con.responseText);
						}
						else {
							document.getElementById('bayar'+no).style.backgroundColor='#C3DAF9';
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 }	
}