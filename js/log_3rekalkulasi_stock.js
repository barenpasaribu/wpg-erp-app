/**
 * @author antoniuslouis
 */

function ambilPeriode(gudang)
{
	param='gudang='+gudang;
	tujuan='log_slave_getPeriode.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('periode').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
}

function getTransaksiGudang()
{
	unit =document.getElementById('unit');
//	periode =document.getElementById('periode');
		unit	=unit.options[unit.selectedIndex].value;
//		periode	=periode.options[periode.selectedIndex].value;
	param='unit='+unit;
//	param='unit='+unit+'&periode='+periode;
	tujuan='log_slave_3rekalkulasi_stock.php';
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

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function rekalkulasiStockKeExcel(ev,tujuan)
{
	unit =document.getElementById('unit');
//	periode =document.getElementById('periode');
		unit	=unit.options[unit.selectedIndex].value;
//		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
//	param='unit='+unit+'&periode='+periode+'&excel=excel';
	param='unit='+unit+'&excel=excel';
	printFile(param,tujuan,judul,ev)	
}

function ambilPeriode2(unit)
{
	param='unit='+unit;
	tujuan='sdm_slave_getPeriode.php';
	post_response_text(tujuan, param, respog);
	
		function respog(){
			if (con.readyState == 4) {
				if (con.status == 200) {
					busy_off();
					if (!isSaveResponse(con.responseText)) {
						alert('ERROR TRANSACTION,\n' + con.responseText);
					}
					else {
						document.getElementById('periode').innerHTML=con.responseText;
					}
				}
				else {
					busy_off();
					error_catch(con.status);
				}
			}
		}	
	
}
