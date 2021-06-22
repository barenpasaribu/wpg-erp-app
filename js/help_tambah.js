// JavaScript Document


function cancelForm()
{
    document.getElementById('index').value='';
    document.getElementById('tentang').value='';
    document.getElementById('modul').value='';
    document.getElementById('isi').value='';
    document.getElementById('html').value='help/';
    var oEditor = FCKeditorAPI.GetInstance('isi');
    oEditor.SetData('');
}

function saveForm()
{

    index=document.getElementById('index').value;
    tentang=trim(document.getElementById('tentang').value);
    modul=trim(document.getElementById('modul').value);
    isi=trim(document.getElementById('isi').value);
    html=trim(document.getElementById('html').value);
    pros=document.getElementById('proses').value;
    var oEditor = FCKeditorAPI.GetInstance('isi');
    isi=oEditor.GetXHTML(true);
	
    param = "proses="+pros;
    param += "&index="+index;
    param += "&tentang="+tentang;
    param += "&modul="+modul;
    param += "&isi="+isi;
    param += "&html="+html;
//    alert(param);
	
    tujuan='help_slave_tambah.php';
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
                else {
                    loadNData(); 
                    alert('Done.');
                    document.getElementById('index').value='';
                    document.getElementById('tentang').value='';
                    document.getElementById('modul').value='';
                    oEditor.SetData('');
                    //document.getElementById('isi').value='';
                    document.getElementById('html').value='help/';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 
     
	
}

function loadNData()
{
    param='proses=loaddata';
    tujuan='help_slave_tambah.php';
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
        tujuan='help_slave_tambah.php';
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
                
    tujuan = 'help_slave_tambah.php';
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

function delData(index,tentang,modul,isi)
{
    param='index='+index+'&tentang='+tentang+'&modul='+modul+'&isi='+isi+'&proses=deletedata';
    tujuan='help_slave_tambah.php';
    if(confirm("Are You Sure Want Delete Data?"))
        post_response_text(tujuan, param, respog);
				
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    loadNData();
                    document.getElementById('index').value='';
                    document.getElementById('tentang').value='';
                    document.getElementById('modul').value='';
                    oEditor.SetData('');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function editRow(kode,tentang,modul,isi,html) {
    param='index='+kode+'&proses=getData';
    tujuan='help_slave_tambah.php';
   // alert(param);
        post_response_text(tujuan, param, respog);
				
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                document.getElementById('index').value=kode;
                document.getElementById('tentang').value=tentang; 
                document.getElementById('modul').value=modul;
//                document.getElementById('isi').value=isi;
                //alert(con.responseText);
                var oEditor = FCKeditorAPI.GetInstance('isi');
                oEditor.SetData(isi);
                document.getElementById('html').value=html;
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
  
    tujuan='help_slave_detailtambah.php'+"?"+param;  
    width='800';
    height='500';
// alert(param);
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('',content,width,height,ev); 
    
}
//------------- english --------------
function cancelForm_en()
{
    document.getElementById('index').value='';
    document.getElementById('tentang').value='';
    document.getElementById('modul').value='';
    document.getElementById('isi').value='';
    document.getElementById('html').value='help/en/';
    var oEditor = FCKeditorAPI.GetInstance('isi');
    oEditor.SetData('');
}

function saveForm_en()
{

    index=document.getElementById('index').value;
    tentang=trim(document.getElementById('tentang').value);
    modul=trim(document.getElementById('modul').value);
    isi=trim(document.getElementById('isi').value);
    html=trim(document.getElementById('html').value);
    pros=document.getElementById('proses').value;
    var oEditor = FCKeditorAPI.GetInstance('isi');
    isi=oEditor.GetXHTML(true);
	
    param = "proses="+pros;
    param += "&index="+index;
    param += "&tentang="+tentang;
    param += "&modul="+modul;
    param += "&isi="+isi;
    param += "&html="+html;
//    alert(param);
	
    tujuan='help_slave_tambah_en.php';
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
                else {
                    loadNData_en(); 
                    alert('Done.');
                    document.getElementById('index').value='';
                    document.getElementById('tentang').value='';
                    document.getElementById('modul').value='';
                    oEditor.SetData('');
                    //document.getElementById('isi').value='';
                    document.getElementById('html').value='help/en/';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }	
    } 
     
	
}

function loadNData_en()
{
    param='proses=loaddata';
    tujuan='help_slave_tambah_en.php';
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
        tujuan='help_slave_tambah_en.php';
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
                
    tujuan = 'help_slave_tambah_en.php';
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

function delData_en(index,tentang,modul,isi)
{
    param='index='+index+'&tentang='+tentang+'&modul='+modul+'&isi='+isi+'&proses=deletedata';
    tujuan='help_slave_tambah_en.php';
    if(confirm("Are You Sure Want Delete Data?"))
        post_response_text(tujuan, param, respog);
				
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                    loadNData_en();
                    document.getElementById('index').value='';
                    document.getElementById('tentang').value='';
                    document.getElementById('modul').value='';
                    oEditor.SetData('');
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function editRow_en(kode,tentang,modul,isi,html) {
    param='index='+kode+'&proses=getData';
    tujuan='help_slave_tambah_en.php';
   // alert(param);
        post_response_text(tujuan, param, respog);
				
    function respog(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                document.getElementById('index').value=kode;
                document.getElementById('tentang').value=tentang; 
                document.getElementById('modul').value=modul;
//                document.getElementById('isi').value=isi;
                //alert(con.responseText);
                var oEditor = FCKeditorAPI.GetInstance('isi');
                oEditor.SetData(isi);
                document.getElementById('html').value=html;
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
  
    tujuan='help_slave_detailtambah_en.php'+"?"+param;  
    width='800';
    height='500';
// alert(param);
    content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
    showDialog1('',content,width,height,ev); 
    
}