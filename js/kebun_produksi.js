/**
 * @author {anthesis-team@mitraagroservindo.com}
 * jakarta indonesia
 */

function getLaporanPrdTbs()
{
	periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
	tampil=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].text;
    kebun=document.getElementById('kebun').options[document.getElementById('kebun').selectedIndex].text;
	param='periode='+periode+'&tampil='+tampil+'&kebun='+kebun;
    tujuan='kebun_slave_3produksiTbs.php';
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
						else {;
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


function laporanPDF(periode,tampil,pabrik,ev)
{
    param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
   tujuan = 'pabrik_slave_printProduksi_pdf.php?'+param;	
 //display window
   title=periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}

function grafikProduksi(periode,tampil,pabrik,ev)
{
   param='periode='+periode+'&tampil='+tampil+'&pabrik='+pabrik;
   //document.getElementById('container').innerHTML="<img src='pabrik_slave_grafikProduksi.php?"+param+"'>";		
   tujuan='pabrik_slave_grafikProduksi.php?'+param;
   title=periode;
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);
}
