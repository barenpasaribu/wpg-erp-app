// JavaScript Document
function save_pil()
{
	document.getElementById('gudang_id').disabled=true;
	document.getElementById('period').disabled=true;
	document.getElementById('company_id').disabled=true;

	alert("Data telah disimpan");
}
function ganti_pil()
{
	document.getElementById('company_id').disabled=false;
	document.getElementById('gudang_id').disabled=false;
	document.getElementById('nm_goods').disabld=false;
	document.getElementById('period').disabled=false;
	document.getElementById('hasil_cari').style.display='none';
	document.getElementById('nm_goods').value='';

	alert("Data telah disimpan");
}
function cari_brng(title,content,ev)
{
	if(document.getElementById('gudang_id').disabled==true)
	{
		width='500';
		height='400';
		showDialog1(title,content,width,height,ev);
	}
	else
	{
		alert('Please Choose Storage');
	}
}
function findBrg()
{
		//kode_gudang=document.getElementById('gudang_id').value;
		txt_cari=document.getElementById('no_brg').value;
		param='txtcari='+txt_cari;
		tujuan='log_slave_cariBarangUmum.php';
		//alert(param);
		//tujuan='log_slave_2keluarmasukbrg.php';
		post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
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
function throwThisRow(kd_brg,nm_brg,satuan)
{
	document.getElementById('hasil_cari').style.display='block';
	//document.getElementById('printPanel').style.display='block';
	document.getElementById('nm_goods').value=nm_brg;
	document.getElementById('nm_goods').disabld=true;
	document.getElementById('nm_brg').innerHTML=nm_brg;
	document.getElementById('satuan_brg').innerHTML=satuan;
	document.getElementById('kd_brg').innerHTML=kd_brg;
	kd_gdng=document.getElementById('gudang_id').value;
	priode=document.getElementById('period').value;
	kode_pt=document.getElementById('company_id').value;
	param='kd_gudang='+kd_gdng+'&kodebarang='+kd_brg+'&periode='+priode+'&pt='+kode_pt+'&satuan='+satuan+'&namabarang='+nm_brg;
	//alert(param);
	tujuan='log_slave_2keluarmasukbrgPerBarang.php';
	post_response_text(tujuan, param, respog);
	function respog(){
		if (con.readyState == 4) {
			if (con.status == 200) {
				busy_off();
				if (!isSaveResponse(con.responseText)) {
					alert('ERROR TRANSACTION,\n' + con.responseText);
				}
				else {
					
					document.getElementById('contain').innerHTML=con.responseText;	
					closeDialog();				
				}
			}
			else {
				busy_off();
				error_catch(con.status);
			}
		}
	}	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function dataKeExcel(ev,tujuan)
{
	pt		=document.getElementById('company_id');
	gudang  =document.getElementById('gudang_id');
	periode =document.getElementById('period');
	namabrg=document.getElementById('nm_brg').innerHTML;
	satuan=document.getElementById('satuan_brg').innerHTML;
	kd_brg=document.getElementById('kd_brg').innerHTML;
	pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabrg+'&kodebarang='+kd_brg+'&satuan='+satuan;
	//alert(param);
	printFile(param,tujuan,judul,ev)	
}

function dataKePDF(ev)
{
	pt		=document.getElementById('company_id');
	gudang  =document.getElementById('gudang_id');
	periode =document.getElementById('period');
	namabrg=document.getElementById('nm_brg').innerHTML;
	satuan=document.getElementById('satuan_brg').innerHTML;
	kd_brg=document.getElementById('kd_brg').innerHTML;
	pt		=pt.options[pt.selectedIndex].value;
	gudang	=gudang.options[gudang.selectedIndex].value;
	periode	=periode.options[periode.selectedIndex].value;
	tujuan='log_laporanMutasiDetailPerBarang_pdf.php';
	judul='Report PDF';	
	//param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabrg+'&kodebarang='+kd_brg+'&satuan='+satuan;
	printFile(param,tujuan,judul,ev);		
}
