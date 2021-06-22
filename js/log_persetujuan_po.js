// JavaScript Document
function agree_po()
{
        width='400';
        height='200';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Approval Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function get_data_po(id,kolom)
{
        agree_po();
        met=document.getElementById('method').value;
        rnopo=id;
        met='get_form_approval';
        param='method='+met+'&nopo='+rnopo+'&kolom='+kolom;
        tujuan='log_persetujuan_po_get_data.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/
                                                                        if(con.responseText=='')
                                                                        {
                                                                        /*	con.responseText=;
                                                                                alert(con.responseText);*/
                                                                                document.getElementById('container').innerHTML='You are not in the list';
                                                                                //return con.responseText;
                                                                        }
                                                                        else
                                                                        {
                                                                                document.getElementById('container').innerHTML="<input type=hidden id=kolom value="+kolom+">"+con.responseText;
                                                                                return con.responseText;
                                                                        }
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	
        post_response_text(tujuan, param, respog);	

}
function forward_po()
{

        kolom=document.getElementById('kolom').value;
        nik=document.getElementById('id_user').value;
        rnopo=document.getElementById('nopo').value;
        met=document.getElementById('method');
        met=met.value='insert_forward_po';
        param='id_user_frwd='+nik+'&method='+met+'&nopo='+rnopo+'&kolom='+kolom;
        tujuan='log_slave_persetujuan_po.php';
        //alert(param);
        /*return;*/
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
                                                                //alert(con.responseText);
                                                                closeDialog();
                                                                refresh_data();
                                                                //document.getElementById('contain').innerHTML=con.responseText;

                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 post_response_text(tujuan, param, respog);	
}
function cancel_po()
{
        closeDialog();
}
function close_form_po()
{
        document.getElementById('test').style.display='none';
        document.getElementById('approve').style.display='block';
}
function close_po()
{
        rnopo=trim(document.getElementById('rnopo').value);
        met=document.getElementById('method');
        met=met.value='insert_close_po';
        usr_id=document.getElementById('user_id').value;

                param='nopo='+rnopo+'&method='+met+'&id_user='+usr_id;
                tujuan='log_slave_persetujuan_po.php';
                //alert(param);
                /*alert(param);
                return;*/
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
                                                                        //alert(con.responseText);
                                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                                        closeDialog();
                                                                        refresh_data();
                                                                        //alert('Berhasil');
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	
                         post_response_text(tujuan, param, respog);	
}
function rejected_po(id,kolom)
{

        //alert(id);return;
        agree_po();
        met=document.getElementById('method').value;
        rnopo=id;

        //rnopp=document.getElementById('td_').innerHTML;
        met='get_form_rejected';
        param='method='+met+'&nopo='+rnopo;
        tujuan='log_persetujuan_po_get_data.php';
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
                                                                        //alert(con.responseText);
                                                                        document.getElementById('container').innerHTML=con.responseText;
                                                                        //show_list();
                                                                        //alert('Berhasil');
                                                                        return con.responseText;
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	
        post_response_text(tujuan, param, respog);		
}
function rejected_po_proses()
{
        rnopo=trim(document.getElementById('rnopo').value);
        met=document.getElementById('method');
        met=met.value='rejected_pp_ex';
        usr_id=document.getElementById('user_id').value;
        param='nopo='+rnopo+'&method='+met+'&id_user='+usr_id;
        tujuan='log_slave_persetujuan_po.php';
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
                                                                //alert(con.responseText);
                                                                //document.getElementById('contain').innerHTML=con.responseText;
                                                                closeDialog();
                                                                refresh_data();
                                                                //alert('Berhasil');
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 post_response_text(tujuan, param, respog);	
}
function refresh_data()
{
        param='method=list_new_data';
        tujuan='log_slave_persetujuan_po.php';
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
                                                                //alert(con.responseText);
                                                                document.getElementById('contain').innerHTML=con.responseText;
                                                                document.getElementById('txtsearch').value='';
                                                                document.getElementById('tgl_cari').value='';
                                                                //alert('Berhasil');
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 post_response_text(tujuan, param, respog);	
}

function cariNopo()
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        met=document.getElementById('method');
        met=met.value='cari_po';
        //met=trim(met);
        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
        tujuan='log_persetujuan_po_get_data.php';
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
                                                                //alert(con.responseText);
                                                                document.getElementById('contain').innerHTML=con.responseText;
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 post_response_text(tujuan, param, respog);
}


function cariBast(num)
{
                param='method=list_new_data_release_po';
                param+='&page='+num;
                tujuan = 'log_slave_persetujuan_po.php';
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
