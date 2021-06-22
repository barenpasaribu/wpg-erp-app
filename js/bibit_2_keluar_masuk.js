// JavaScript Document

function getkebun()
{
    pt1=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
    param='proses=getkebun'+'&pt1='+pt1;
    tujuan='bibit_2_slave_keluar_masuk2.php';
    console.log(param);
    post_response_text(tujuan, param, respog);	

    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('kebun1').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	    
}

function previewdata1()
{
    tanggal1=document.getElementById('tanggal1').value;
    kebun1=document.getElementById('kebun1').options[document.getElementById('kebun1').selectedIndex].value;
    pt1=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
    param='proses=preview1&tanggal1='+tanggal1+'&kebun1='+kebun1
        +'&pt1='+pt1;
    tujuan='bibit_2_slave_keluar_masuk2.php';
    if((tanggal1!='')&&(pt1!='')&&(kebun1!=''))
    post_response_text(tujuan, param, respog);			
    else alert('Please fill all fields.')
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('container1').innerHTML=con.responseText;                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}

function exceldata1(ev,tujuan)
{
    tanggal1=document.getElementById('tanggal1').value;
    kebun1=document.getElementById('kebun1').options[document.getElementById('kebun1').selectedIndex].value;
    pt1=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
    judul='Report Ms.Excel';	
    param='tanggal1='+tanggal1+'&kebun1='+kebun1+'&pt1='+pt1+'&proses=excel1';
    printFile(param,tujuan,judul,ev)	
}

function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='600';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}

function ambilbatch(kodeorg)
{
    param='kodeorg='+kodeorg;
    param+='&tipe=batch';
    tujuan='bibit_slave_getbatch.php';
    post_response_text(tujuan, param, respog);
	
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('kodebatch').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}
 
function previewdata2()
{
    kebun1=document.getElementById('kodeunit').options[document.getElementById('kodeunit').selectedIndex].value;
    pt1=document.getElementById('kodebatch').options[document.getElementById('kodebatch').selectedIndex].value;
    param='proses=preview&kodebatch='+pt1+'&kodeunit='+kebun1;
    tujuan='bibit_slave_2kartu.php';
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('printContainer3').innerHTML=con.responseText;                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	        
}
function exceldata2(ev,tujuan)
{
    kebun1=document.getElementById('kodeunit').options[document.getElementById('kodeunit').selectedIndex].value;
    pt1=document.getElementById('kodebatch').options[document.getElementById('kodebatch').selectedIndex].value;
    judul='Report Ms.Excel';	
    param='proses=excel&kodebatch='+pt1+'&kodeunit='+kebun1;
    printFile(param,tujuan,judul,ev)	
}