/**
 * @author antoniuslouis
 */
function getLaporanFisik()
{
        pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                pt		=pt.options[pt.selectedIndex].value;
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;

        param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
        tujuan='log_laporanPersediaanFisik.php';
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
        pt		=document.getElementById('pt');
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

function getHutangSupplier()
{
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        param='gudang='+gudang+'&periode='+periode;
        tujuan='log_laporanHutangSupplier.php';
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

function hutangSupplierKePDF(ev,tujuan)
{
//	pt		=document.getElementById('pt');
        gudang  =document.getElementById('gudang');
        periode =document.getElementById('periode');
//		pt		=pt.options[pt.selectedIndex].value;
                gudang	=gudang.options[gudang.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        judul='Report PDF';	
        param='gudang='+gudang+'&periode='+periode;
//	alert(param);
        printFile(param,tujuan,judul,ev)	
}


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

function getAlokasiGaji()
{
        unit =document.getElementById('unit');
        periode =document.getElementById('periode');
                unit	=unit.options[unit.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        param='unit='+unit+'&periode='+periode;
        tujuan='sdm_laporanAlokasiGaji.php';
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

function alokasiGajiKeExcel(ev,tujuan)
{
//	pt		=document.getElementById('pt');
        unit  =document.getElementById('unit');
        periode =document.getElementById('periode');
//		pt		=pt.options[pt.selectedIndex].value;
                unit	=unit.options[unit.selectedIndex].value;
                periode	=periode.options[periode.selectedIndex].value;
        judul='Report Ms.Excel';	
        param='unit='+unit+'&periode='+periode;
//	alert(param);
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
