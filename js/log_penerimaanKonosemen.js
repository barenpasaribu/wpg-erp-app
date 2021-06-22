
function posting(notran)
{
	
	param='method=posting'+'&notran='+notran;
	//alert(param);
	tujuan='log_slave_penerimaanKonosemen.php';
	if(confirm('Posting??'))
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
						closeDialog();
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

function savePenerimaan(notran,kodebarang,no)
{
	//jumlah=trim(document.getElementById('jumlah'+no).innerHTML);
	jumlah=document.getElementById('jumlah'+no).innerHTML;
	jumlahditerima=document.getElementById('jumlahditerima'+no).value;
	
	
	if(parseFloat(jumlahditerima)>parseFloat(jumlah))
	{
		alert('amount received is greater than the amount sent');return;
	}
	
	param='method=savePenerimaan'+'&notran='+notran+'&kodebarang='+kodebarang+'&jumlahditerima='+jumlahditerima;
	//alert(param);
	tujuan='log_slave_penerimaanKonosemen.php';
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
						getIsi(con.responseText);
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



function listBarang(notran,title,ev)
{
	//alert(ev);
	content= "<div>";
	// content+="<fieldset>posting</fieldset>";
	content+="<div id=isiData style=\"height:300px;width:735px;overflow:scroll;\"></div></div>";
	//display window
	title=title+' PO:';
	width='750';
	height='350';
	showDialog1(title,content,width,height,ev);	
	getIsi(notran);
}

function getIsi(notran)
{
	
		param='method=getIsi'+'&notran='+notran;
		//alert(param);
		tujuan = 'log_slave_penerimaanKonosemen.php';
		post_response_text(tujuan, param, respog);			
	
	//alert(param);
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
									document.getElementById('isiData').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	}	
}



function goCariPo()
{
	tampung=document.getElementById('tampung').value;
	noPo=trim(document.getElementById('noPo').value);
	pt=document.getElementById('pt').value;
	if(noPo.length<4)
	{   
		alert('Text too short');
		return;
	}
	else
	{   
		param='method=goCariPo'+'&noPo='+noPo+'&pt='+pt+'&tampung='+tampung;
		tujuan = 'log_slave_packing.php';
		post_response_text(tujuan, param, respog);			
	}
	//alert(param);
	function respog(){
			if (con.readyState == 4) {
					if (con.status == 200) {
							busy_off();
							if (!isSaveResponse(con.responseText)) {
									alert('ERROR TRANSACTION,\n' + con.responseText);
							}
							else {
									document.getElementById('containercari').innerHTML=con.responseText;
							}
					}
					else {
							busy_off();
							error_catch(con.status);
					}
			}
	}	
}







function cari()
{
	txt=trim(document.getElementById('txt').value);
	param='txt='+txt+'&method=loadData';
	//param='txt='+txt+'&tgl='+tgl+'&method=loadData';
	tujuan='log_slave_penerimaanKonosemen.php';
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
		 post_response_text(tujuan, param, respog);
}







function cariBast(num)
{
		txt=trim(document.getElementById('txt').value);
		
	
		
		param='method=loadData';
		param+='&page='+num+'&txt='+txt;
		
		//param='txt='+txt+'&tgl='+tgl+'&status='+status+'&method=loadData';
		
		
		tujuan = 'log_slave_penerimaanKonosemen.php';
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
	param='method=loadData';
	tujuan='log_slave_penerimaanKonosemen.php';
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



