/**
 * @author Developer
 */

function getlaporanGaji()
{
        unit =document.getElementById('unit');
        unit	=unit.options[unit.selectedIndex].value;

        subunit =document.getElementById('subunit');
        subunit    =subunit.options[subunit.selectedIndex].value;

        periode =document.getElementById('periode');
        periode	=periode.options[periode.selectedIndex].value;

        jenis =document.getElementById('jenis');
        jenis =jenis.options[jenis.selectedIndex].value;

        dept =document.getElementById('dept');
        dept =dept.options[dept.selectedIndex].value;

        gol =document.getElementById('gol');
        gol =gol.options[gol.selectedIndex].value;

        //param='unit='+unit+'&periode='+periode;
        param='unit='+unit+'&periode='+periode+'&jenis='+jenis+'&dept='+dept+'&gol='+gol+'&subunit='+subunit;
        //tujuan='vhc_slave_2biayatotalperkendaraan.php';
        tujuan='sdm_slave_2laporanGaji.php';
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

function laporanGajiKeExcel(ev,tujuan)
{
        unit =document.getElementById('unit');
        unit    =unit.options[unit.selectedIndex].value;

        subunit =document.getElementById('subunit');
        subunit    =subunit.options[subunit.selectedIndex].value;

        periode =document.getElementById('periode');
        periode =periode.options[periode.selectedIndex].value;

        jenis =document.getElementById('jenis');
        jenis =jenis.options[jenis.selectedIndex].value;

        dept =document.getElementById('dept');
        dept =dept.options[dept.selectedIndex].value;

        gol =document.getElementById('gol');
        gol =gol.options[gol.selectedIndex].value;

        //param='unit='+unit+'&periode='+periode;
        param='unit='+unit+'&periode='+periode+'&jenis='+jenis+'&dept='+dept+'&gol='+gol+'&subunit='+subunit;


        judul='Report Ms.Excel';	
        //param='unit='+unit+'&periode='+periode;
//        param='unit='+unit+'&periode='+periode+'&jenis='+jenis+'&dept='+dept;
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
