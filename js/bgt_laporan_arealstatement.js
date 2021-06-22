/**
 * @author antoniuslouis
 */

function getAreal()
{
	tahun =document.getElementById('tahun');
	kebun =document.getElementById('kebun');
		tahunV =tahun.options[tahun.selectedIndex].value;
		kebunV	=kebun.options[kebun.selectedIndex].value;

	param='tahun='+tahunV+'&kebun='+kebunV;
//alert(param);
tujuan='bgt_slave_laporan_arealstatement.php';
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

function arealKeExcel(ev,tujuan)
{
	tahun =document.getElementById('tahun');
	kebun =document.getElementById('kebun');
		tahunV =tahun.options[tahun.selectedIndex].value;
		kebunV	=kebun.options[kebun.selectedIndex].value;
                                
	judul='Report Ms.Excel';	
	param='tahun='+tahunV+'&kebun='+kebunV;
	printFile(param,tujuan,judul,ev)	
}

function arealKePDF(ev,tujuan)
{
	tahun =document.getElementById('tahun');
	kebun =document.getElementById('kebun');
		tahunV =tahun.options[tahun.selectedIndex].value;
		kebunV	=kebun.options[kebun.selectedIndex].value;
                                
	judul='Report PDF';	
	param='tahun='+tahunV+'&kebun='+kebunV;
	printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function detail(what,tahun,kebun,tt,stat,ev)
{
//   alert(what+tahun+kebun+tt);
   param='what='+what+'&tahun='+tahun+'&kebun='+kebun+'&tt='+tt;
   param+='&statBlok='+stat;
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

function detailKePDF(ev,tujuan)
{
    width='700';
   height='400';
  
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Data',content,width,height,ev); 
}
