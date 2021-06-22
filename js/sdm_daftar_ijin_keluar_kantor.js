/**
 * @author Developer
 */

function loadData()
{
        param='proses=loadData';
        tujuan='sdm_slave_daftar_ijin_meninggalkan_kantor.php';
        post_response_text(tujuan, param, respog);

                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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

function cariBast(num)
{
                param='proses=loadData';
                param+='&page='+num;
                tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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

function cariBastCr(num,karyidCari,jnsCuti,lokasitugas)
{
                param='proses=cariData';
                param+='&page='+num+'&karyidCari='+karyidCari+'&jnsCuti='+jnsCuti+'&lokasitugas='+lokasitugas;
                tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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
function dtReset()
{
    document.getElementById('karyidCari').value='';
    document.getElementById('jnsCuti').value='';
    document.getElementById('lokasitugas').value='';
    loadData();

}
function getCariDt()
{
    kary=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
    jnsCut=document.getElementById('jnsCuti').options[document.getElementById('jnsCuti').selectedIndex].value;
    lokasitugas=document.getElementById('lokasitugas').options[document.getElementById('lokasitugas').selectedIndex].value;
    param='proses=cariData'+'&jnsCuti='+jnsCut+'&karyidCari='+kary+'&lokasitugas='+lokasitugas;
    tujuan='sdm_slave_daftar_ijin_meninggalkan_kantor.php';
    post_response_text(tujuan, param, respog);

            function respog(){
                    if (con.readyState == 4) {
                            if (con.status == 200) {
                                    busy_off();
                                    if (!isSaveResponse(con.responseText)) {
                                            alert('ERROR TRANSACTION,\n' + con.responseText);
                                    }
                                    else {
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
function cariData(num)
{
                kary=document.getElementById('karyidCari').options[document.getElementById('karyidCari').selectedIndex].value;
                jnsCut=document.getElementById('jnsCuti').options[document.getElementById('jnsCuti').selectedIndex].value;
				lokasitugas=document.getElementById('lokasitugas').options[document.getElementById('lokasitugas').selectedIndex].value;
                param='proses=cariData'+'&jnsCuti='+jnsCut+'&karyidCari='+kary+'&lokasitugas='+lokasitugas;
                param+='&page='+num;
                tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
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



function showAppForward(tgl,karywn,ev)
{
        title="Forward Approval";
        content="<div id=contentForm></div>";
        width='350';
        height='110';
        showDialog1(title,content,width,height,ev);	
}
function showAppForw(tgl,karywn,ev)
{
    showAppForward(tgl,karywn,ev)
    tglijin=tgl;
    krywnId=karywn;

    param='proses=formForward'+'&tglijin='+tglijin+'&krywnId='+krywnId;
    tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            document.getElementById('contentForm').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }

}
function AppForw()
{
    krywnId=document.getElementById('karyaid').value;
    tglijin=document.getElementById('tglIjin').value;
    ats=document.getElementById('karywanId').options[document.getElementById('karywanId').selectedIndex].value;
    param='proses=forwardData'+'&tglijin='+tglijin+'&krywnId='+krywnId+'&atasan='+ats;
    tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php';

                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                            alert("Done");
                                            closeDialog();
                                            loadData();
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
//function previewPdf(tgl,karywn,ev)
//rubah jadi id ==jo 23-05-2017==
function previewPdf(id,ev)
{
        
        param='proses=prevPdf'+'&ids='+id;
        tujuan = 'sdm_slave_daftar_ijin_meninggalkan_kantor.php?'+param;	
 //display window
   title='Print PDF';
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev);

}

function detailExcel(ev,tujuan)
{
	lokasitugas=document.getElementById('lokasitugas').options[document.getElementById('lokasitugas').selectedIndex].value;
    width='300';
    height='100';
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+'?proses=getExcel&lokasitugas='+lokasitugas+"'></iframe>"
    showDialog1('Print Excel',content,width,height,ev); 
}

function detailData(ev,tujuan)
{
    width='300';
   height='100';

   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1('Detail Allocation',content,width,height,ev); 
}

