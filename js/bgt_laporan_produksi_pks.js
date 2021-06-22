/**
 * @author antoniuslouis
 */

function getProduksi()
{
	tahun =document.getElementById('tahun');
	pabrik =document.getElementById('pabrik');
		tahunV =tahun.options[tahun.selectedIndex].value;
		pabrikV	=pabrik.options[pabrik.selectedIndex].value;

	param='tahun='+tahunV+'&pabrik='+pabrikV;
//alert(param);
tujuan='bgt_slave_laporan_produksi_pks.php';
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

function produksiKeExcel(ev,tujuan)
{
	tahun =document.getElementById('tahun');
	pabrik =document.getElementById('pabrik');
		tahunV =tahun.options[tahun.selectedIndex].value;
		pabrikV	=pabrik.options[pabrik.selectedIndex].value;
                                
	judul='Report Ms.Excel';	
	param='tahun='+tahunV+'&pabrik='+pabrikV;
	printFile(param,tujuan,judul,ev)	
}

function produksiKePDF(ev,tujuan)
{
	tahun =document.getElementById('tahun');
	pabrik =document.getElementById('pabrik');
		tahunV =tahun.options[tahun.selectedIndex].value;
		pabrikV	=pabrik.options[pabrik.selectedIndex].value;
                                
	judul='PDF';	
	param='tahun='+tahunV+'&pabrik='+pabrikV;
	printFile(param,tujuan,judul,ev)	
}

function detail(what,tahun,kebun,tt,ev)
{
//   alert(what+tahun+kebun+tt);
   param='what='+what+'&tahun='+tahun+'&kebun='+kebun+'&tt='+tt;
   tujuan='bgt_slave_laporan_arealstatement_detail.php'+"?"+param;  
   width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Data '+what+' '+kebun+' '+tt,content,width,height,ev); 
}

function detailKeExcel(ev,tujuan)
{
    width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Data',content,width,height,ev); 
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
