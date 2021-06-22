// JavaScript Document
function refresh_data_release_po()
{
        param='method=list_new_data_release_po';
        tujuan='log_slave_release_po.php';
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
                                                                document.getElementById('txtsearch_rpo').value='';
                                                                document.getElementById('tgl_cari_rpo').value='';
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
function cariBast2(num)
{
                param='method=list_new_data_release_po';
                param+='&page='+num;
                tujuan = 'log_slave_release_po.php';
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
function cariRpo()
{
        txtSearchrpo=trim(document.getElementById('txtsearch_rpo').value);
        tglCarirpo=trim(document.getElementById('tgl_cari_rpo').value);
        typePo=trim(document.getElementById('typepo').value);
        met=document.getElementById('method');
        met=met.value='cari_rpo';
        //met=trim(met);
        param='txtSearchrpo='+txtSearchrpo+'&tglCarirpo='+tglCarirpo+'&method='+met+'&typepo='+typePo;
        tujuan='log_slave_release_po.php';
		
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
function cariPage(num)
{
        txtSearchrpo=trim(document.getElementById('txtsearch_rpo').value);
        tglCarirpo=trim(document.getElementById('tgl_cari_rpo').value);
        met=document.getElementById('method');
        met=met.value='cari_rpo';
        param='txtSearchrpo='+txtSearchrpo+'&tglCarirpo='+tglCarirpo+'&method='+met;
                param+='&page='+num;
                tujuan = 'log_slave_release_po.php';
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
function approve_po(id)
{
        rnopo=id;
        id_user=document.getElementById('user_login').value;
        param='nopo='+rnopo+'&id_user='+id_user+'&method=approve_po';
        tujuan='log_slave_release_po.php';
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
                                                                refresh_data_release_po();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 a =confirm("Are you sure want to approve this PO:"+id)
                 if(a)
                 {
                        post_response_text(tujuan, param, respog);
                 }
                 else
                 {
                         return;
                 }
}

function release_po(id)
{
        rnopo=id;
        id_user=document.getElementById('user_login').value;
        param='nopo='+rnopo+'&id_user='+id_user+'&method=release_po';
        tujuan='log_slave_release_po.php';
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
                                                                refresh_data_release_po();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 a =confirm("Are you sure want to release this PO:"+id)
                 if(a)
                 {
                        post_response_text(tujuan, param, respog);
                 }
                 else
                 {
                         return;
                 }
}
function un_release_po(id,tanggal)
{
        rnopo=id;
        tglR=tanggal;
        id_user=document.getElementById('user_login').value;
        param='nopo='+rnopo+'&id_user='+id_user+'&method=un_release_po';
        tujuan='log_slave_release_po.php';
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
                                                                refresh_data_release_po();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 }
                 a =confirm("Are you sure want Unrelease this PO:."+id)
                 if(a)
                 {
                        post_response_text(tujuan, param, respog);
                 }
                 else
                 {
                         return;
                 }
}
function tolakPo()
{
        rnopo=document.getElementById('rnopo').value;
        ketrngan=document.getElementById('ket').value
        param='nopo='+rnopo+'&ket='+ketrngan+'&method=tolakPo';
        tujuan='log_slave_release_po.php';
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
                                                                        /*alert(con.responseText);
                                                                        return;*/
                                                                        refresh_data_release_po();
                                                                        closeDialog();
                                                                        //document.getElementById('container').innerHTML=con.responseText;				
                                                                }
                                                        }
                                                        else {
                                                                busy_off();
                                                                error_catch(con.status);
                                                        }
                                          }	
                         } 	

}
function agree_po()
{
        width='300';
        height='130';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Persetujuan Atau Penolakan Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function get_data_po(rnopo)
{
        agree_po();
        met=document.getElementById('method').value;
        met='getFormTolak';
        param='method='+met+'&nopo='+rnopo;
        tujuan='log_slave_release_po.php';
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

                                                                                document.getElementById('container').innerHTML=con.responseText;
//										//return con.responseText;
//									
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
function saveKoreksi(id)
{
        texkKrsi=document.getElementById('krksiText_'+id).value;
        if(texkKrsi=="")
        {
                alert("Enter a note");
                return;
        }
        else
        {
        nop=document.getElementById('td_'+id).innerHTML;
        met=document.getElementById('method').value;
        met='insertKoreksi';
        param='method='+met+'&texkKrsi='+texkKrsi+'&nopo='+nop;
        tujuan='log_slave_release_po.php';

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
                                                        refresh_data_release_po();
                                                        document.getElementById('btnSave_'+id).disabled=true;
                                                        document.getElementById('krksiText_'+id).disabled=true;							
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
}
function undisable(id)
{
        document.getElementById('btnSave_'+id).disabled=false;
        document.getElementById('krksiText_'+id).disabled=false;	
}
function printFile2(title,npo,ev){
    title=title+" "+npo;
    width='250';
    height='100';
    content="<div id=closeForm></div>";
    showDialog1(title,content,width,height,ev); 	
}
function closeedPo(jdl,nop,ev){
        printFile2(jdl,nop,ev);
        param='method=closeForm'+'&nopo='+nop;
        tujuan='log_slave_release_po.php';
        //if(confirm("Are you sure want to close this Nopo :"+nop)){
            post_response_text(tujuan, param, respog);
        //}
        function respog(){
          if(con.readyState==4){
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                        }
                        else {
                            document.getElementById('closeForm').innerHTML=con.responseText;				
                        }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }	
        } 	
}
function tutpDt(npo){
    pil=document.getElementById('pilId');
    pil=pil.options[pil.selectedIndex].value;
    ket=document.getElementById('ketClose').value;
    if(confirm("Are you sure close this PO "+npo)){
        param='method=tutupData'+'&nopo='+npo+'&pilDt='+pil+'&ketClose='+ket;//pilId
        tujuan='log_slave_release_po.php';
        post_response_text(tujuan, param, respog);   
    }
    
        function respog(){
          if(con.readyState==4){
                if (con.status == 200) {
                        busy_off();
                        if (!isSaveResponse(con.responseText)) {
                                alert('ERROR TRANSACTION,\n' + con.responseText);
                        }
                        else {
                            document.getElementById('closeForm').innerHTML=con.responseText;	
                            closeDialog();
                            refresh_data_release_po();
                        }
                }
                else {
                        busy_off();
                        error_catch(con.status);
                }
          }	
        } 	
}

function validat(ev)
{
  key=getKey(ev);
  if(key==13){
    cariRpo();
  } else {
  return tanpa_kutip_dan_sepasi(ev);	
  }	
}