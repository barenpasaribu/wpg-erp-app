function getKar()
{
	
	kdorg=document.getElementById('kdorg').value;
	param='proses=getKar'+'&kdorg='+kdorg;
	//alert(param);
	tujuan='log_slave_2peneluaranBrgPerKary_sch.php';
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
						document.getElementById('karyawanid').innerHTML=con.responseText;
						
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
		  }	
	}

}



function cariNoGudang (title,ev)
{
	content= "<div>";
	content+="<fieldset>No Transaksi Gudang:<input type=text id=schBarang class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariBarang()>Go</button> </fieldset>";
	content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
	
	title=title+' PO:';
	width='500';
	height='300';
	showDialog1(title,content,width,height,ev);	
}



function goCariBarang()
{

	schBarang=trim(document.getElementById('schBarang').value);
	param='proses=goCariBarang'+'&schBarang='+schBarang;
	tujuan = 'log_slave_2peneluaranBrgPerKary_sch.php';
	post_response_text(tujuan, param, respog);			

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


function batal()
{
	document.location.reload();
}



function goPickBarang(kodebarang,namabarang)
{
        document.getElementById('kodebarang').value=kodebarang;
		document.getElementById('namabarang').value=namabarang;
		closeDialog();
}