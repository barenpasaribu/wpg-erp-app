// JavaScript Document
function savePil()
{
	kdbrg=document.getElementById('kdBrg').value;
	kdPabrik=document.getElementById('kdPbrk').value;
	tgl=document.getElementById('tglTrans').value;
	
	if(tgl=='')
	{
		alert('Tanggal masih kosong');
		return;	
	}
	
	param='kdBrg='+kdbrg+'&kdPbrk='+kdPabrik+'&tgl='+tgl+'&proses=getData';
	tujuan='pabrik_slave_2timbangan.php';
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
							document.getElementById('kdBrg').disabled=true;
							document.getElementById('kdPbrk').disabled=true;
							document.getElementById('tglTrans').disabled=true;
							document.getElementById('contain').innerHTML=con.responseText;
						}
					}
					else {
						busy_off();
						error_catch(con.status);
					}
		      }	
	 } 	
	
}
function gantiPil()
{
	document.getElementById('kdBrg').disabled=false;
	document.getElementById('kdPbrk').disabled=false;
	document.getElementById('tglTrans').disabled=false;
	document.getElementById('kdBrg').value='0';
	document.getElementById('kdPbrk').value='';
	document.getElementById('tglTrans').value='';
	document.getElementById('contain').innerHTML='';
}
function dataKeExcel(ev,tujuan)
{
	kdBrg		=document.getElementById('kdBrg').value;
	kdPbrk  =document.getElementById('kdPbrk').value;
	tgl =document.getElementById('tglTrans').value;

	//gudang	=gudang.options[gudang.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='kdBrg='+kdBrg+'&kdPbrk='+kdPbrk+'&tgl='+tgl;
	//alert(param);
	printFile(param,tujuan,judul,ev)	
}
function dataKePDF(ev)
{
	kdBrg	=document.getElementById('kdBrg').value;
	kdPbrk  =document.getElementById('kdPbrk').value;
	tgl =document.getElementById('tglTrans').value;

	tujuan='pabrik_slaveLaporanTimbanganPdf.php';
	judul='Report PDF';		
	param='kdBrg='+kdBrg+'&kdPbrk='+kdPbrk+'&tgl='+tgl;
	//alert(param);
	printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}