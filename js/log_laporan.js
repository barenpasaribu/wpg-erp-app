/**
 * @author antoniuslouis
 */
function getLaporanFisik()
{
    pt		=document.getElementById('pt');
    //gudang  =document.getElementById('gudang');
    periode =document.getElementById('periode');
            pt		=pt.options[pt.selectedIndex].value;
    //	gudang	=gudang.options[gudang.selectedIndex].value;
            periode	=periode.options[periode.selectedIndex].value;

    param='pt='+pt+'&periode='+periode;
    tujuan='log_laporanPersediaanFisik.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                        //alert('ERROR TRANSACTION,\n' + con.responseText);
                                        showById('printPanel');
                                        hideById('printPanel3');
                                        hideById('printPanel2');
                                        document.getElementById('container').innerHTML = con.responseText;
                                    }
                                    else {
                                            showById('printPanel');
                                            hideById('printPanel3');
                                            hideById('printPanel2');
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
	//gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		pt		=pt.options[pt.selectedIndex].value;
		//gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+pt+'&periode='+periode;
	printFile(param,tujuan,judul,ev)	
}

function fisikKeExceltab0(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='pt='+pt+'&periode='+periode+'&gudang='+gudang;
	printFile(param,tujuan,judul,ev)	
}

function fisikKeExcelT1(ev,tujuan)
{
    // tahunan
	pt		=document.getElementById('pt1');
	gudang  =document.getElementById('gudang1');
	periode =document.getElementById('periode1');
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
function detailMutasiBarangHargaExcel(ev,pt,periode,gudang,kodebarang,namabarang,satuan,tujuan)
{
	pt      =document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
        
            pt		=pt.options[pt.selectedIndex].value;
            gudang	=gudang.options[gudang.selectedIndex].value;
            periode	=periode.options[periode.selectedIndex].value;

	judul='Detail Ms.Excel';	
	param='pt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
	printFile(param,tujuan,judul,ev)
                    
}
function fisikKePDFHarga(ev,tujuan)
{
	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
                    pt		=pt.options[pt.selectedIndex].value;
                    gudang	=gudang.options[gudang.selectedIndex].value;
                    periode	=periode.options[periode.selectedIndex].value;
	judul='Report PDF';	
	param='pt='+pt+'&periode='+periode+'&gudang='+gudang;
	printFile(param,tujuan,judul,ev)	
}
function fisikKePDF(ev,tujuan)
{
	pt		=document.getElementById('pt');
	//gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
                    pt		=pt.options[pt.selectedIndex].value;
                   // gudang	=gudang.options[gudang.selectedIndex].value;
                    periode	=periode.options[periode.selectedIndex].value;
	judul='Report PDF';	
	param='pt='+pt+'&periode='+periode;
	printFile(param,tujuan,judul,ev)	
}

function fisikKePDFT1(ev,tujuan)
{
	pt		=document.getElementById('pt1');
	gudang  =document.getElementById('gudang1');
	periode =document.getElementById('periode1');
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

function getLaporanFisikHarga1()
{
    pt=document.getElementById('pt1');
    gudang  =document.getElementById('gudang1');
    periode =document.getElementById('periode1');
    pt		=pt.options[pt.selectedIndex].value;
    gudang	=gudang.options[gudang.selectedIndex].value;
    periode	=periode.options[periode.selectedIndex].value;
    document.getElementById('orglegend1').innerHTML= pt+"-"+gudang;		
    param='pt='+pt+'&gudang='+gudang+'&periode='+periode;
    tujuan='log_laporanPersediaanFisikHargaTahunan.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            showById('printPanel1');
                                            document.getElementById('container1').innerHTML=con.responseText;
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
//	document.getElementById('orglegend').innerHTML= pt+"-"+gudang;		
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

function hutangSupplierKeExcel(ev,tujuan)
{
//	pt		=document.getElementById('pt');
	gudang  =document.getElementById('gudang');
	periode =document.getElementById('periode');
//		pt		=pt.options[pt.selectedIndex].value;
		gudang	=gudang.options[gudang.selectedIndex].value;
		periode	=periode.options[periode.selectedIndex].value;
	judul='Report Ms.Excel';	
	param='gudang='+gudang+'&periode='+periode;
//	alert(param);
	printFile(param,tujuan,judul,ev)	
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
function getGudangDt()
{
    unt=document.getElementById('unitDt').options[document.getElementById('unitDt').selectedIndex].value;
    param='unitDt='+unt+'&proses=getGudang';
    tujuan='log_slaveLaporanPersediaanFisikHargaUnit.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            document.getElementById('gudang2').innerHTML=con.responseText;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }
}
function getLaporanFisikHarga2()
{
    unitDt=document.getElementById('unitDt');   
    periode =document.getElementById('periode2');
    unitDt=unitDt.options[unitDt.selectedIndex].value;
    periode	=periode2.options[periode2.selectedIndex].value;
    document.getElementById('orglegend2').innerHTML= unitDt;		
    param='unitDt='+unitDt+'&periode='+periode;
    tujuan='log_LaporanPersediaanFisikHarga.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            showById('printPanel2');
                                            document.getElementById('container2').innerHTML=con.responseText;
                                    }
                            }
                            else {
                                    busy_off();
                                    error_catch(con.status);
                            }
                    }
            }		
}
function fisikKeExcelT2(ev,tujuan)
{
// tahunan
    unitDt=document.getElementById('unitDt');
    //gudang  =document.getElementById('gudang2');
    periode =document.getElementById('periode2');
            unitDt=unitDt.options[unitDt.selectedIndex].value;
            //gudang	=gudang2.options[gudang2.selectedIndex].value;
            periode	=periode2.options[periode2.selectedIndex].value;	
    judul='Report Ms.Excel';	
    param='unitDt='+unitDt+'&periode='+periode;
   // param+='&proses=excel';
    printFile(param,tujuan,judul,ev);	
}
function fisikKePDFT2(ev,tujuan)
{
    unitDt=document.getElementById('unitDt');
    //gudang  =document.getElementById('gudang2');
    periode =document.getElementById('periode2');
            unitDt=unitDt.options[unitDt.selectedIndex].value;
            //gudang	=gudang2.options[gudang2.selectedIndex].value;
            periode	=periode2.options[periode2.selectedIndex].value;
    judul='Report PDF';	
    param='unitDt='+unitDt+'&periode='+periode;
    param+='&proses=pdf';
    printFile(param,tujuan,judul,ev);	
}
function getLaporanFisik2()
{
    unitDt=document.getElementById('unitDt');
    gudang  =document.getElementById('gudang2');
    periode =document.getElementById('periode2');
    unitDt=unitDt.options[unitDt.selectedIndex].value;
    gudang	=gudang.options[gudang.selectedIndex].value;
    periode	=periode.options[periode.selectedIndex].value;
    param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
    param+='&proses=preview';
    tujuan='log_slaveLaporanPersediaanFisikUnit.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            //alert('ERROR TRANSACTION,\n' + con.responseText);
                                        showById('printPanel2');
                                        hideById('printPanel');
                                        hideById('printPanel3');
                                        document.getElementById('container').innerHTML = con.responseText;
                                    }
                                    else {
                                            showById('printPanel2');
                                            hideById('printPanel');
                                            hideById('printPanel3');
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

function fisikKeExcel2(ev,tujuan)
{
    unitDt=document.getElementById('unitDt');
    gudang  =document.getElementById('gudang2');
    periode =document.getElementById('periode2');
            unitDt=unitDt.options[unitDt.selectedIndex].value;
            gudang	=gudang2.options[gudang2.selectedIndex].value;
            periode	=periode2.options[periode2.selectedIndex].value;	
    judul='Report Ms.Excel';	
    param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
    param+='&proses=excel';
    printFile(param,tujuan,judul,ev);	
}
function fisikKePDF2(ev,tujuan)
{
    unitDt=document.getElementById('unitDt');
        gudang  =document.getElementById('gudang2');
        periode =document.getElementById('periode2');
                unitDt=unitDt.options[unitDt.selectedIndex].value;
                gudang	=gudang2.options[gudang2.selectedIndex].value;
                periode	=periode2.options[periode2.selectedIndex].value;
        judul='Report PDF';	
        param='unitDt='+unitDt+'&gudang='+gudang+'&periode='+periode;
        param+='&proses=pdf';
        printFile(param,tujuan,judul,ev)	
}
function detailMutasiBarang2(ev,pt,periode,gudang,kodebarang,namabarang,satuan)
{
	tujuan='log_slaveLaporanPersediaanFisikUnit.php';
	judul='Report PDF';	
	param='unitDt='+pt+'&gudang='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
        param+='&proses=detailData'
	printFile(param,tujuan,judul,ev);	
}
function getLaporanFisik3()
{
    unitDt=document.getElementById('unitDt2');
    periode =document.getElementById('periode3');
    unitDt=unitDt.options[unitDt.selectedIndex].value;
    periode	=periode.options[periode.selectedIndex].value;
    param='unitDt='+unitDt+'&periode='+periode;
    param+='&proses=preview';
    tujuan='log_slaveLaporanPersediaanFisikUnit2.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
                                            showById('printPanel3');
                                            hideById('printPanel');
                                            hideById('printPanel2');
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
function fisikKeExcel3(ev,tujuan)
{
	unitDt=document.getElementById('unitDt2');
        periode =document.getElementById('periode3');
        unitDt=unitDt.options[unitDt.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;
        param='unitDt='+unitDt+'&periode='+periode;
	judul='Report Ms.Excel';
        param+='&proses=excel';
	printFile(param,tujuan,judul,ev)
}
function fisikKePDF3(ev,tujuan)
{
	unitDt=document.getElementById('unitDt2');
        periode =document.getElementById('periode3');
        unitDt=unitDt.options[unitDt.selectedIndex].value;
        periode	=periode.options[periode.selectedIndex].value;
        param='unitDt='+unitDt+'&periode='+periode;
	judul='Report PDF';
	
        param+='&proses=pdf';
	printFile(param,tujuan,judul,ev)
}
function detailMutasiBarang3(ev,periode,gudang,kodebarang,namabarang,satuan)
{
	tujuan='log_slaveLaporanPersediaanFisikUnit2.php';
	judul='Report PDF';
	param='unitDt='+gudang+'&periode='+periode+'&namabarang='+namabarang+'&satuan='+satuan+'&kodebarang='+kodebarang;
        param+='&proses=detailData'
	printFile(param,tujuan,judul,ev);
}