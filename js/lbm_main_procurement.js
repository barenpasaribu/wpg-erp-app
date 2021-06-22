/**
 * @author antoniuslouis
 */
function zPreview(fileTarget,passParam,idCont) {
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=preview'; 
     post_response_text(fileTarget+'.php', param, respon);
    function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    var res = document.getElementById(idCont);
                    res.innerHTML = con.responseText;
                    document.getElementById('mainPrint').style.display='block';
                    document.getElementById('lyrPertama').style.display='none';
                    document.getElementById('lyrKedua').style.display='none';
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);


}
function zPdf(fileTarget,passParam,idCont) {
    var cont = document.getElementById(idCont);
    var passP = passParam.split('##');

    var param = "proses=pdf";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        param += "&"+passP[i]+"="+getValue(passP[i]);
    }
        //alert(param);
    cont.innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='250';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
function zExcel(ev,tujuan,passParam)
{
        judul='Report Excel';
        //alert(param);	
        var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
        param+='&proses=excel';
        //alert(param);
        printFile(param,tujuan,judul,ev)	
}
function getDetailNonKap(passParam){
        //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetailNonKap';
        tujuan='log_slave_proc_brg_detail_kap.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //alert(con.responseText);
                                                document.getElementById('reportcontainer1').innerHTML=con.responseText;
                                                document.getElementById('mainPrint').style.display='none';
                                                document.getElementById('lyrPertama').style.display='block';

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}

function getDetailKap(passParam){
        //alert("kedua");
        var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
        param+='&proses=getDetailKap';
        tujuan='log_slave_proc_brg_detail_kap2.php';
         post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //alert(con.responseText);
                                                art=con.responseText.split("###");
                                                document.getElementById('reportcontainer1').innerHTML=art[0];
                                                document.getElementById('isiJdlBawah1').innerHTML=art[1];
                                                document.getElementById('mainPrint').style.display='none';
                                                document.getElementById('lyrPertama').style.display='block';
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function getDet(tujun,klmpk,prd)
{
    param='periode='+prd+'&klmpkBrg='+klmpk+'&proses=getDetail';
    tujuan=tujun+'.php';

    post_response_text(tujuan, param, respog);
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                //alert(con.responseText);
                            art=con.responseText.split("###");
                            document.getElementById('reportcontainer1').innerHTML=art[0];
                            document.getElementById('isiJdlBawah1').innerHTML=art[1];
                            document.getElementById('mainPrint').style.display='none';
                            document.getElementById('lyrPertama').style.display='block';
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }
}
function getDetPt(tujun,klmpk,prd,pt)
{
    param='periode='+prd+'&klmpkBrg='+klmpk+'&proses=getDetail'+'&idPt='+pt;
    tujuan=tujun+'.php';

    post_response_text(tujuan, param, respog);
    function respog(){
            if (con.readyState == 4) {
                    if (con.status == 200) {
                            busy_off();
                            if (!isSaveResponse(con.responseText)) {
                                    alert('ERROR TRANSACTION,\n' + con.responseText);
                            }
                            else {
                                //alert(con.responseText);
                            art=con.responseText.split("###");
                            document.getElementById('reportcontainer1').innerHTML=art[0];
                            document.getElementById('isiJdlBawah1').innerHTML=art[1];
                            document.getElementById('mainPrint').style.display='none';
                            document.getElementById('lyrPertama').style.display='block';
                            }
                    }
                    else {
                            busy_off();
                            error_catch(con.status);
                    }
            }
    }
}
function zBack()
{
    document.getElementById('mainPrint').style.display='block';
    document.getElementById('lyrPertama').style.display='none';
}
function zBack2()
{
    document.getElementById('lyrPertama').style.display='block';
    document.getElementById('lyrKedua').style.display='none';
}

function getDetBrg(klmpkBrg,passParam)
{
       //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetBarang'+'&klmpkbrg='+klmpkBrg;
    tujuan='log_slave_proc_brg_detail_kap.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            arg=con.responseText.split("###");
                            document.getElementById('reportcontainer2').innerHTML=arg[0];
                            document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='none';
                            document.getElementById('lyrKedua').style.display='block';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}

function getDetBrgKap(klmpkBrg,passParam)
{
       //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetBrgKap'+'&klmpkbrg='+klmpkBrg;
    tujuan='log_slave_proc_brg_detail_kap2.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            arg=con.responseText.split("###");
                            document.getElementById('reportcontainer2').innerHTML=arg[0];
                            document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='none';
                            document.getElementById('lyrKedua').style.display='block';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}
function getDet2(tujn,pt,klmpkbrg,mtuang,prde,kdbrg)
{
    param='proses=getDetPt'+'&klmpkBrg='+klmpkbrg+'&mtuang='+mtuang+'&pt='+pt;
    param+='&periode='+prde+'&kdBarang='+kdbrg;
    tujuan=tujn+'.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            arg=con.responseText.split("###");
                            document.getElementById('reportcontainer2').innerHTML=arg[0];
                            document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='none';
                            document.getElementById('lyrKedua').style.display='block';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}
function zExcel2(ev,tujn,pros,pt,klmpkbrg,mtuang,prde,kdbr)
{
    judul='Report Excel';
    param='proses='+pros+'&klmpkBrg='+klmpkbrg+'&mtuang='+mtuang+'&pt='+pt;
    param+='&periode='+prde+'&kdBarang='+kdbr;
    printFile(param,tujn,judul,ev)	
}

function zExcelDet(ev,tujuan,passParam,klmpkBrg)
{
        judul='Report Excel';
        //alert(param);	
        var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
        param+='&proses=exceLgetDetBarang'+'&klmpkbrg='+klmpkBrg;
        //alert(param);
        printFile(param,tujuan,judul,ev)	
}
function zExcelDet2(ev,tujuan,passParam,klmpkBrg)
{
        judul='Report Excel';
        //alert(param);	
        var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
        param+='&proses=exceLgetDetBarang'+'&klmpkbrg='+klmpkBrg;
        //alert(param);
        printFile(param,tujuan,judul,ev)	
}
 

function bukaPil(totRpw)
{
    ardt=document.getElementById('kursPil');
    if(ardt.checked==true)
    {
        document.getElementById('statKurs').value=1;
        for(are=1;are<=totRpw;are++)
        {
        document.getElementById('kurs_'+are).disabled=false;
        }
    }
    else
        {
            document.getElementById('statKurs').value=0;
            for(are=1;are<=totRpw;are++)
            {
                document.getElementById('kurs_'+are).disabled=true;
            }
        }


}
function getTransaksiGudang()
{
unit =document.getElementById('unit');
unit =unit.options[unit.selectedIndex].value;
tahun =document.getElementById('tahun');
tahun =tahun.options[tahun.selectedIndex].value;
kelompok =document.getElementById('kelompok');
kelompok =kelompok.options[kelompok.selectedIndex].value;
pilih =document.getElementById('pilih');
pilih =pilih.options[pilih.selectedIndex].value;
param='unit='+unit+'&tahun='+tahun+'&kelompok='+kelompok+'&pilih='+pilih;
tujuan='log_slave_2kalkulasi_stock.php';
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
width='400';
height='200';
content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
showDialog1(title,content,width,height,ev);
}

function rekalkulasiStockKeExcel(ev,tujuan)
{
unit =document.getElementById('unit');
unit =unit.options[unit.selectedIndex].value;
tahun =document.getElementById('tahun');
tahun =tahun.options[tahun.selectedIndex].value;
kelompok =document.getElementById('kelompok');
kelompok =kelompok.options[kelompok.selectedIndex].value;
pilih =document.getElementById('pilih');
pilih =pilih.options[pilih.selectedIndex].value;
param='unit='+unit+'&tahun='+tahun+'&kelompok='+kelompok+'&pilih='+pilih+'&excel=excel';
judul='Report Ms.Excel';
printFile(param,tujuan,judul,ev)
}

function getDetailGudangKeExcel(ev,tujuan,pilih,barang,periode)
{
param='pilih='+pilih+'&barang='+barang+'&periode='+periode+'&unit='+unit+'&excel=excel';
judul='Report Ms.Excel';
printFile(param,tujuan,judul,ev)
}

function getDetailGudang(pilih,barang,periode,ev)
{
unit = document.getElementById('unit');
unit = unit.options[unit.selectedIndex].value;
param='pilih='+pilih+'&barang='+barang+'&periode='+periode+'&unit='+unit;
tujuan='log_slave_2kalkulasi_stock_detail.php'+"?"+param;
width='800';
height='400';

content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
showDialog1('Detail Transaksi '+pilih+' '+barang+' '+periode,content,width,height,ev);

}
function getDetailPP(pur,bln,thn)
{
    blnthn=thn+'-'+bln;
    param='proses=getDetPP'+'&purchaser='+pur+'&bln='+blnthn;
    tujuan='lbm_slave_proc_ppblmrealisasi.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            //arg=con.responseText.split("###");
                            document.getElementById('reportcontainer1').innerHTML=con.responseText;
                            //document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='block';
                            document.getElementById('mainPrint').style.display='none';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}
function getDtPt()
{
    dert=document.getElementById('regDt').options[document.getElementById('regDt').selectedIndex].value;
    param='proses=getDetPt'+'&regional='+dert;
    tujuan='lbm_slave_proc_brg_kap_nonkapital.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            //arg=con.responseText.split("###");
                            document.getElementById('kdPt').innerHTML=con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}

function getDetailNonKap2(passParam){
        //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetailNonKap';
        tujuan='log_slave_proc_brg_detail_fis_kap.php';
        post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //alert(con.responseText);
                                                document.getElementById('reportcontainer1').innerHTML=con.responseText;
                                                document.getElementById('mainPrint').style.display='none';
                                                document.getElementById('lyrPertama').style.display='block';

                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}

function getDetailKap2(passParam){
        //alert("kedua");
        var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
        param+='&proses=getDetailKap';
        tujuan='log_slave_proc_brg_detail_fis_kap2.php';
         post_response_text(tujuan, param, respog);
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                //alert(con.responseText);
                                                art=con.responseText.split("###");
                                                document.getElementById('reportcontainer1').innerHTML=art[0];
                                                document.getElementById('isiJdlBawah1').innerHTML=art[1];
                                                document.getElementById('mainPrint').style.display='none';
                                                document.getElementById('lyrPertama').style.display='block';
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
}
function getDetBrg2(klmpkBrg,passParam)
{
       //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetBarang'+'&klmpkbrg='+klmpkBrg;
    tujuan='log_slave_proc_brg_detail_fis_kap.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            arg=con.responseText.split("###");
                            document.getElementById('reportcontainer2').innerHTML=arg[0];
                            document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='none';
                            document.getElementById('lyrKedua').style.display='block';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}
function getDetBrgKap2(klmpkBrg,passParam)
{
       //alert("pertama");
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=getDetBrgKap'+'&klmpkbrg='+klmpkBrg;
    tujuan='log_slave_proc_brg_detail_fis_kap2.php';
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            arg=con.responseText.split("###");
                            document.getElementById('reportcontainer2').innerHTML=arg[0];
                            document.getElementById('isiJdlBawah2').innerHTML=arg[1];
                            document.getElementById('lyrPertama').style.display='none';
                            document.getElementById('lyrKedua').style.display='block';
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}
function showDetail(kdBrg,ev){
        title="Detail "+kdBrg;
        content="<fieldset><legend>"+title+"</legend><div id=contDetail style='overflow:auto; width:450px; height:290px;' ></div></fieldset>";
        width='500';
        height='350';
        showDialog1(title,content,width,height,ev);	
}

function detData(ev,tjn,passParam,kdBrg,stat){
       //alert("pertama");
       showDetail(kdBrg,ev);
    var passP = passParam.split('##');
    var param = "";
    for(i=1;i<passP.length;i++) {
        var tmp = document.getElementById(passP[i]);
        if(i==1) {
            param += passP[i]+"="+getValue(passP[i]);
        } else {
            param += "&"+passP[i]+"="+getValue(passP[i]);
        }
    }
    param+='&proses=excelDetailBrg'+'&kodeBarang='+kdBrg;
    param+='&pilihan='+stat;
    tujuan=tjn;
    post_response_text(tujuan, param, respog);
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                    busy_off();
                    if (!isSaveResponse(con.responseText)) {
                            alert('ERROR TRANSACTION,\n' + con.responseText);
                    }
                    else {
                            //alert(con.responseText);
                            document.getElementById('contDetail').innerHTML=con.responseText;
                    }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
        }
    }
}