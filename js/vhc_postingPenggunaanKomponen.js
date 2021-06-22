// JavaScript Document
function displayList()
{
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari').value='';
        //document.getElementById('proses').value='insert';
        load_new_data();
}
function load_new_data()
{
        param='proses=load_data';
        tujuan='vhc_slave_postingPenggunaanKomponen.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
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
                param='proses=load_data';
                param+='&page='+num;
                tujuan = 'vhc_slave_postingPenggunaanKomponen.php';
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
function cariTransaksi()
{
        txtSearch=document.getElementById('txtsearch').value;
        txtTgl=document.getElementById('tgl_cari').value;

        param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cari_transaksi';
        //alert(param);
        tujuan='vhc_slave_postingPenggunaanKomponen.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {						
                                                //load_new_data();
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
function cariData(num)
{
                txtSearch=document.getElementById('txtsearch').value;
                txtTgl=document.getElementById('tgl_cari').value;		
                param='txtSearch='+txtSearch+'&txtTgl='+txtTgl+'&proses=cari_transaksi';
                param+='&page='+num;
                tujuan = 'vhc_slave_postingPenggunaanKomponen.php';
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

function posting_data(notrans,kdvhc)
{
        no_trans=notrans;
        kdVhc=kdvhc;
        param='notrans='+no_trans+'&proses=postingData'+'&kdVhc='+kdVhc;
        tujuan='vhc_slave_postingPenggunaanKomponen.php';
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {						
                                                load_new_data();
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
                if(confirm("are you sure ?"))
                {
                        post_response_text(tujuan, param, respog);			
                }
                else
                { return; }
}