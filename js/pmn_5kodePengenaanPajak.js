// JavaScript Document


function cancelForm()
{
    document.getElementById('kode').value='';
    document.getElementById('nama').value='';
}

function saveForm()
{
    kode=document.getElementById('kode').value;
    nama=trim(document.getElementById('nama').value);
    pros=document.getElementById('proses').value;
	
    param = "proses="+pros;
    param += "&kode="+kode;
    param += "&nama="+nama;
	
    tujuan='pmn_slave_5kodePengenaanPajak.php';
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
                    document.getElementById('kode').value='';
                    document.getElementById('nama').value='';
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
    tujuan='pmn_slave_5kodePengenaanPajak.php';
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


function cariBast(num)
{
    param='proses=loaddata';
    param+='&page='+num;
                
    tujuan = 'pmn_slave_5kodePengenaanPajak.php';
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

function delData(kode,nama)
{
    param='kode='+kode+'&nama='+nama+'&proses=deletedata';
    tujuan='pmn_slave_5kodePengenaanPajak.php';
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
                    document.getElementById('kode').value='';
                    document.getElementById('nama').value='';
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
}

function editRow(kode,nama) {
    param='kode='+kode;
    tujuan='pmn_slave_5kodePengenaanPajak.php';
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
                document.getElementById('kode').value=kode;
                document.getElementById('nama').value=nama; 
                }
            }
            else {
                busy_off();
                error_catch(con.status);
            }
        }
    }		
    
}