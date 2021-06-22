function getkebun()
{
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    param='pt='+pt+'&proses=getkebun';
    tujuan='mr_slave_biayaProduksiTbs.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('unit').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}

function getkebun1()
{
    pt=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
    param='pt='+pt+'&proses=getkebun';
    tujuan='mr_slave_biayaProduksiTbsPT.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('unit1').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}

function getafdeling()
{
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    param='unit='+unit+'&proses=getafdeling';
    tujuan='mr_slave_biayaProduksiTbs.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('afdeling').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}

function getafdeling1()
{
    unit=document.getElementById('unit1').options[document.getElementById('unit1').selectedIndex].value;
    param='unit='+unit+'&proses=getafdeling';
    tujuan='mr_slave_biayaProduksiTbsPT.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('afdeling1').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}

function bersih()
{
    document.getElementById('container').innerHTML = '';
}

function bersih1() 
{
    document.getElementById('container1').innerHTML = '';
}

function getpreview(){
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    afdeling=document.getElementById('afdeling').options[document.getElementById('afdeling').selectedIndex].value;
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    inti=document.getElementById('inti').options[document.getElementById('inti').selectedIndex].value;
    param='pt='+pt+'&unit='+unit+'&afdeling='+afdeling+'&periode='+periode+'&inti='+inti+'&proses=preview';
    tujuan='mr_slave_biayaProduksiTbs.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   document.getElementById('container').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
} 

function getpreview1(){
//    pt=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
//    unit=document.getElementById('unit1').options[document.getElementById('unit1').selectedIndex].value;
//    afdeling=document.getElementById('afdeling1').options[document.getElementById('afdeling1').selectedIndex].value;
    periode=document.getElementById('periode1').options[document.getElementById('periode1').selectedIndex].value;
//    param='pt='+pt+'&unit='+unit+'&afdeling='+afdeling+'&periode='+periode+'&proses=preview';
    param='periode='+periode+'&proses=preview';
    tujuan='mr_slave_biayaProduksiTbsPT.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   document.getElementById('container1').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}


function getpreview2(){
	   pt=document.getElementById('pt2').options[document.getElementById('pt2').selectedIndex].value;
//    unit=document.getElementById('unit1').options[document.getElementById('unit1').selectedIndex].value;
//    afdeling=document.getElementById('afdeling1').options[document.getElementById('afdeling1').selectedIndex].value;
    periode=document.getElementById('periode2').options[document.getElementById('periode2').selectedIndex].value;
//    param='pt='+pt+'&unit='+unit+'&afdeling='+afdeling+'&periode='+periode+'&proses=preview';
    param='periode='+periode+'&pt='+pt+'&proses=preview';
	//alert(param);//return;
    tujuan='mr_slave_biayaProduksiTbsDivisi.php';
     function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                   document.getElementById('container2').innerHTML = con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }    
    post_response_text(tujuan, param, respon);    
}







function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='200';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
//   document.getElementById('container').innerHTML = "<iframe frameborder=0 style='width:100%;height:99%' src='"+fileTarget+".php?"+param+"'></iframe>";
   showDialog1(title,content,width,height,ev); 	
}
 
function getexcel(ev,tujuan){
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    judul='Report Ms.Excel';	
    param='pt='+pt+'&unit='+unit+'&periode='+periode+'&proses=excel';
    printFile(param,tujuan,judul,ev)    
} 

function getexcel1(ev,tujuan){
//    pt=document.getElementById('pt1').options[document.getElementById('pt1').selectedIndex].value;
//    unit=document.getElementById('unit1').options[document.getElementById('unit1').selectedIndex].value;
    periode=document.getElementById('periode1').options[document.getElementById('periode1').selectedIndex].value;
    judul='Report Ms.Excel';	
//    param='pt='+pt+'&unit='+unit+'&periode='+periode+'&proses=excel';
    param='periode='+periode+'&proses=excel';
    printFile(param,tujuan,judul,ev)    
} 

function getpdf(ev,tujuan)
{
    pt=document.getElementById('pt').options[document.getElementById('pt').selectedIndex].value;
    unit=document.getElementById('unit').options[document.getElementById('unit').selectedIndex].value;
    periode=document.getElementById('periode').options[document.getElementById('periode').selectedIndex].value;
    judul='Report PDF';	
    param='pt='+pt+'&unit='+unit+'&periode='+periode+'&proses=pdf';
    printFile(param,tujuan,judul,ev)	
}