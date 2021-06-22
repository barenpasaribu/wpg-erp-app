/**
 * @author antoniuslouis
 */

function getBiayaTotalPerKendaraan()
{
        unit =document.getElementById('unit');
        unit	=unit.options[unit.selectedIndex].value;
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
        param='unit='+unit+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl;
        tujuan='log_slave_laporanRealisasiSPK.php?proses=html';
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

function biayaLaporanRealisasiKeExcel(ev,tujuan)
{
        unit  =document.getElementById('unit');
        tglAwl=document.getElementById('tglAwal').value;
        tglAkhr=document.getElementById('tglAkhir').value;
//	periode =document.getElementById('periode');
        unit	=unit.options[unit.selectedIndex].value;
//        periode	=periode.options[periode.selectedIndex].value;
        judul='Report Ms.Excel';	
        //param='unit='+unit+'&periode='+periode;
        param='proses=excel&unit='+unit+'&tglAkhir='+tglAkhr+'&tglAwal='+tglAwl;
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