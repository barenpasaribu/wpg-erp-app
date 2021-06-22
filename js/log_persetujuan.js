// JavaScript Document

function show_list()
{
        document.getElementById('persetujuan').style.display='none';
        document.getElementById('rejected_form').style.display='none'
        document.getElementById('list_pp_verication').style.display='block';
        document.getElementById('nopp').value='';
        document.getElementById('stat_hasil').value='';
        refresh_data();
}
function agree_pp()
{
        width='400';
        height='200';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Rejection  Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}
function agree()
{
        width='300';
        height='300';
        //nopp=document.getElementById('nopp_'+id).value;
        content="<div id=container></div>";
        ev='event';
        title="Approval Form";
        showDialog1(title,content,width,height,ev);
        //get_data_pp();	
}

function reject_some_pp()
{
        width='850';
        height='450';
        content="<div id=container></div>";
        ev='event';
        title="Rejection Form";
        showDialog1(title,content,width,height,ev);
}
function get_data_pp(id,kolom)
{
        agree();
/*	document.getElementById('Ajukan').disabled=false;
        document.getElementById('Tutup').disabled=false;*/
        met=document.getElementById('method').value;
        rnopp=id;

        met='get_form_approval';
        param='method='+met+'&nopp='+rnopp+'&kolom='+kolom;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
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
                                                                                document.getElementById('container').innerHTML='You are not registred in the list';
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
function forward_pp()
{

        kolom=document.getElementById('kolom').value;
        nik=document.getElementById('user_id').value;
        cmnt_hsl=document.getElementById('comment_fr').value;
        rnopp=document.getElementById('nopp').value;
        met=document.getElementById('method');
        if(cmnt_hsl=='')
        {
                alert('Please write a note!');
                return;
        }
        document.getElementById('Ajukan').disabled=true;
        //document.getElementById('Tutup').disabled=true;
        met=met.value='insert_forward_pp';
        param='userid='+nik+'&cm_hasil='+cmnt_hsl+'&method='+met+'&nopp='+rnopp+'&kolom='+kolom;
        tujuan='log_slave_persetujuan.php';
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
                                                            //document.getElementById('contain').innerHTML=con.responseText;
                                                                closeDialog();
                                                                refresh_data();
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
function close_pp()
{
    kolom=document.getElementById('kolom').value;
        rnopp=trim(document.getElementById('rnopp').value);
        met=document.getElementById('method');
        met=met.value='insert_close_pp';
        comment_cls=trim(document.getElementById('note').value);
        usr_id=document.getElementById('user_login').value;
        //if(comment_cls=='')
//	{
//		alert('Please Write a Note (Catatan) For Approved');
//	}
//	else
//	{
                param='nopp='+rnopp+'&method='+met+'&cmnt='+comment_cls+'&userid='+usr_id+'&kolom='+kolom;
                tujuan='log_slave_persetujuan.php';
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
                                                                        displayList();
                                                                        closeDialog();

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
//}
function rejected_pp(id,kolom)
{

        //alert(id);return;
        agree_pp();
        met=document.getElementById('method').value;
        rnopp=id;

        //rnopp=document.getElementById('td_').innerHTML;
        met='get_form_rejected';
        param='method='+met+'&nopp='+rnopp+'&kolom='+kolom;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
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
                                                                        document.getElementById('container').innerHTML="<input type=hidden id=kolom value="+kolom+">"+con.responseText;
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
function rejected_some_proses(id,kolom)
{
        reject_some_pp();
        met=document.getElementById('method').value;
        rnopp=id;
        met='get_form_rejected_some';
        param='method='+met+'&nopp='+rnopp+'&kolom='+kolom;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
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
                                                                        document.getElementById('container').innerHTML="<input type=hidden id=kolom value="+kolom+">"+con.responseText;
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
function rejected_some(id,no,kolom)
{
        rnopp=id;
        kode_brg=document.getElementById('kd_brg_'+no).innerHTML;
        user_login=document.getElementById('user_id').value;
        alsn=document.getElementById('alsnDtolak_'+no).value;
        //kolom=document.getElementById('kolom').value;
        /*alert(nopp);
        return;*/
        met=document.getElementById('method').value;
        met='rejected_some_input';
        param='nopp='+rnopp+'&kd_brg='+kode_brg+'&method='+met+'&userid='+user_login+'&kolom='+kolom+'&alsnDtolk='+alsn;
        tujuan='log_slave_persetujuan.php';
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
                                                        //alert('Berhasil');
                                                        id=document.getElementById('rnopp').value;
                                                        rejected_some_proses(id);
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
function rejected_some_done(id,kolom,totRow)
{

    dnopp=id;
    strUrl2 = '';
        for(i=1;i<=totRow;i++)
        {
            try{
                ckh=document.getElementById('tolak_chk_'+i);
                if(ckh.checked==true)
                {
                     if(strUrl2 != '')
                    {					
                        strUrl2 +='&kode_brg[]='+trim(document.getElementById('kd_brg_'+i).innerHTML)
                        +'&alsan[]='+encodeURIComponent(trim(document.getElementById('alsnDtolak_'+i).value))
                        +'&jml_asal[]='+encodeURIComponent(trim(document.getElementById('jmlh_'+i).value))
                        +'&jml_appr[]='+encodeURIComponent(trim(document.getElementById('jmlh_approve_'+i).value));

                    }
                    else
                    {
                        strUrl2 +='&kode_brg[]='+trim(document.getElementById('kd_brg_'+i).innerHTML)
                        +'&alsan[]='+encodeURIComponent(trim(document.getElementById('alsnDtolak_'+i).value))
                        +'&jml_asal[]='+encodeURIComponent(trim(document.getElementById('jmlh_'+i).value))
                        +'&jml_appr[]='+encodeURIComponent(trim(document.getElementById('jmlh_approve_'+i).value));
                    }
                }

             }
        catch(e){}

        }
        if(strUrl2=='')
            {
                alert("please choose one item of material");
                return;
            }
        param='nopp='+dnopp+'&method=tolakBeberapa'+'&kolom='+kolom;
        param+=strUrl2;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
		
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
                                        get_data_pp(id,kolom);
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
function rejected_pp_proses(klm)
{
        rnopp=trim(document.getElementById('rnopp').value);
        met=document.getElementById('method');
        met=met.value='rejected_pp_ex';
        kolom=klm;
        comment=trim(document.getElementById('cmnt_tolak').value);
        usr_id=document.getElementById('user_login').value;
        if(comment=='')
        {
                alert('Please leave a note');	
        }	
        else
        {
                param='nopp='+rnopp+'&method='+met+'&comment='+comment+'&userid='+usr_id+'&kolom='+kolom;
                tujuan='log_slave_persetujuan.php';
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
}

function get_data_detail(id)
{
        rnopp=document.getElementById('nopp_'+id).value;
        met=document.getElementById('method');
        met=met.value='detail_pp';
        param='nopp='+rnopp+'&method='+met;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
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
        //return 'test';	
}

function detailPP(title,ev,nopp)
{
        width='490';
        height='1000';
        document.getElementById('nopp').value=nopp
        content="<div id=container></div>";
        showDialog1(title,content,width,height,ev);
        get_data_detail();
}

function cek_status_pp(id)
{
        var stat_pp=id;
        if(stat_pp==''||stat_pp==0)
        {
                alert('No decision');

                return;
        }
        else if(stat_pp==1)
        {
                alert('Approved');
                return;		
        }
        else if(stat_pp==3)
        {
                alert('Rejected');
                return;
        }
}
function close_form_pp()
{
        document.getElementById('test').style.display='none';
        document.getElementById('approve').style.display='block';
}
function cancel_pp()
{
        closeDialog();
}
function cariNopp()
{
        txtSearch=trim(document.getElementById('txtsearch').value);
        tglCari=trim(document.getElementById('tgl_cari').value);
        nmbrg=document.getElementById('txtnmbrg').value;
        buat=document.getElementById('pembuatPP').options[document.getElementById('pembuatPP').selectedIndex].value;
        met=document.getElementById('method');
        met=met.value='cari_pp';
        met=trim(met);
        param='txtSearch='+txtSearch+'&tglCari='+tglCari+'&method='+met;
        param+='&nmbrg='+nmbrg+'&pembuat='+buat;
        tujuan='log_get_detail_pp_persetujuan_pp.php';
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
function refresh_data()
{
        param='method=data_refresh';
        tujuan='log_slave_persetujuan.php';
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
                param='method=data_refresh';
                param+='&page='+num;
                tujuan = 'log_slave_persetujuan.php';
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
function displayList()
{
        document.getElementById('txtsearch').value='';	
        document.getElementById('tgl_cari').value='';
  refresh_data();
}

function previewDetail(nopP,ev)
{
        showDetail(nopP,ev);
        rnopp=nopP;
        param='rnopp='+rnopp+'&method=getDetailPP';
        tujuan='log_slave_save_log_pp.php';
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

function showDetail(noPP,ev)
{
        title="Purchase Request detail";
        content="<fieldset><legend>"+noPP+"</legend><div id=contDetail ></div></fieldset><input type=hidden id=datPP name=datPP value="+noPP+" />";
        width='800';
        height='400';
        showDialog1(title,content,width,height,ev);	
}

function checkAlasan(bars)
{
    txtstr=document.getElementById('alsnDtolak_'+bars).value;
    chkdt=document.getElementById('tolak_chk_'+bars);
    if(txtstr=='')
        {
            alert("Reason for rejection is obligatory!!");
            chkdt.checked=false;
            return;
        }
}