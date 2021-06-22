function getLaporanJurnal()
{
	pt=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
        
            ptV         =pt.options[pt.selectedIndex].value;
            gudangV	=gudang.options[gudang.selectedIndex].value;
            periodeV    =periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_laporanJurnal.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getUsiaHutang()
{
	pt=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_laporanUsiaHutang.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanBukuBesar()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV	=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2bukubesar.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanNeracaCoba()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_laporanNeracaCoba.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getMesinLaporan()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2mesinlaporan.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanNeraca()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2neraca.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanRugiLaba()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2rugilaba.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanArusKas()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2aruskas.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function getLaporanArusKasLangsung()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		ptV		=pt.options[pt.selectedIndex].value;
		gudangV	=gudang.options[gudang.selectedIndex].value;
		periodeV	=periode.options[periode.selectedIndex].value;

	param='pt='+ptV+'&gudang='+gudangV+'&periode='+periodeV;
	tujuan='keu_slave_2aruskasLangsung.php';
	post_response_text(tujuan, param, respog);
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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
function fisikKeExcel(ev,tujuan)
{
	pt	=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
	printFile(param,tujuan,judul,ev)	
}

function detailMutasiBarang(ev,pt,periode,gudang,kodebarang,namabarang,satuan)
{
	tujuan='log_laporanMutasiDetailPerBarang_pdf.php';
	judul='Report PDF';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
	printFile(param,tujuan,judul,ev);	
}
function detailMutasiBarangHarga(ev,pt,periode,gudang,kodebarang,namabarang,satuan)
{
	tujuan='log_laporanMutasiDetailPerBarangHarga_pdf.php';
	judul='Report PDF';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
	printFile(param,tujuan,judul,ev);	
}

function fisikKePDF(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report PDF';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
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

function getLaporanFisikHarga()
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	document.getElementById('orglegend').innerHTML= pt+"-"+gudang;		
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
	tujuan='log_laporanPersediaanFisikHarga.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						showById('printPanel');
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

function ambilAnak(pt)
{
	param='pt='+pt;
	tujuan='keu_slave_getUnit.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('gudang').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
}

function showDetail(nourut,keterangan,ev)
{
	judul='Detail '+keterangan;	
	param='nourut='+nourut;
	printFile(param,'keu_slave_2neraca_detail.php',judul,ev)	
}

function viewDetail(nopengolahan,tanggal,kodeorg,periode_tahun,periode_bulan,ev)
{
   param='nopengolahan='+nopengolahan+'&tanggal='+tanggal+'&kodeorg='+kodeorg+'&periode_tahun='+periode_tahun+'&periode_bulan='+periode_bulan;
   tujuan='pabrik_slave_2pengolahandetail.php'+"?"+param;  
   width='700';
   height='150';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Processing Detail '+tanggal,content,width,height,ev); 
	
}

function browsemesin(nopengolahan,tanggal,kodeorg,periode_tahun,periode_bulan,ev)
{
   param='nopengolahan='+nopengolahan+'&tanggal='+tanggal+'&kodeorg='+kodeorg+'&periode_tahun='+periode_tahun+'&periode_bulan='+periode_bulan;
   tujuan='pabrik_slave_2pengolahanmesin.php'+"?"+param;  
   width='800';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2('Processing Detail (Machine) '+nopengolahan,content,width,height,ev); 
	
}

function browsebarang(nopengolahan,tanggal,kodeorg,periode_tahun,periode_bulan,ev)
{
   param='nopengolahan='+nopengolahan+'&tanggal='+tanggal+'&kodeorg='+kodeorg+'&periode_tahun='+periode_tahun+'&periode_bulan='+periode_bulan;
   tujuan='pabrik_slave_2pengolahanbarang.php'+"?"+param;  
   width='800';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog2('Processing Detail (Material) '+nopengolahan,content,width,height,ev); 
	
}


function lihatDetail(noakun,periode,lmperiode,pt,gudang,ev)
{
   param='noakun='+noakun+'&periode='+periode;
   param+='&lmperiode='+lmperiode+'&pt='+pt+'&gudang='+gudang;
   tujuan='keu_slave_getBBDetail.php'+"?"+param;  
   width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal'+noakun,content,width,height,ev); 
}

function lihatDetail(noakun,periode,lmperiode,pt,gudang,ev)
{
   param='noakun='+noakun+'&periode='+periode;
   param+='&lmperiode='+lmperiode+'&pt='+pt+'&gudang='+gudang;
   tujuan='keu_slave_getBBDetail.php'+"?"+param;  
   width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal'+noakun,content,width,height,ev); 
}

function detailKeExcel(ev,tujuan)
{
    width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Jurnal',content,width,height,ev); 
}

function detailExcel(ev,tujuan)
{
    width='500';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Processing Detail in Excel',content,width,height,ev); 
}