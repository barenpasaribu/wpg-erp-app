
function loadNData()
{
    param='proses=loaddata';
//    alert(param);
    tujuan='help_slave_bantuan.php';
//    alert(tujuan);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
   post_response_text(tujuan, param, respon);
}


function cariHelp()
{
    find=trim(document.getElementById('cariindex').value);        
    if(find=='')
    {
        alert('Isi pencarian');
    }
    else
    {
        param="proses=cariindex";
        param +="&cariindex="+find;
        tujuan='help_slave_bantuan.php';
        post_response_text(tujuan,param, respog);
        
    }  
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('contain').innerHTML=con.responseText;
//                    post_response_text(tujuan, param, respog);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
  	
}

function cariBast(num)
{
    param='proses=loaddata';
    param+='&page='+num;
//    alert(param);              
    tujuan='help_slave_bantuan.php';
//    alert(tujuan);
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function detailHelp(ev,index,modul)
{
    param = "index="+index;
    param += "&modul="+modul;
   
    tujuan='help_slave_detailbantuan.php'+"?"+param;  
    width='800';
    height='500';
//    alert(param);
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog2('',content,width,height,ev); 
	
}

function loadNData_en()
{
    param='proses=loaddata';
//    alert(param);
    tujuan='help_slave_bantuan_en.php';
//    alert(tujuan);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
   post_response_text(tujuan, param, respon);
}


function cariHelp_en()
{
    find=trim(document.getElementById('cariindex').value);        
    if(find=='')
    {
        alert('Isi pencarian');
    }
    else
    {
        param="proses=cariindex";
        param +="&cariindex="+find;
        tujuan='help_slave_bantuan_en.php';
        post_response_text(tujuan,param, respog);
        
    }  
    function respog()
    {
        if(con.readyState==4)
        {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    document.getElementById('contain').innerHTML=con.responseText;
//                    post_response_text(tujuan, param, respog);
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }	
    }  	
  	
}

function cariBast_en(num)
{
    param='proses=loaddata';
    param+='&page='+num;
//    alert(param);              
    tujuan='help_slave_bantuan_en.php';
//    alert(tujuan);
    post_response_text(tujuan, param, respog);			
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    document.getElementById('contain').innerHTML=con.responseText;
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }	
}

function detailHelp_en(ev,index,modul)
{
    param = "index="+index;
    param += "&modul="+modul;
   
    tujuan='help_slave_detailbantuan_en.php'+"?"+param;  
    width='800';
    height='500';
//    alert(param);
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog2('',content,width,height,ev); 
	
}