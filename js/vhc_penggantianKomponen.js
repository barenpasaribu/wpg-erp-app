// JavaScript Document

function cekMekanik(select){
    var value1 = select.value; 
    console.log(value1);
    var selects = document.querySelectorAll('[tipe="mechanic"]');
    for (var i=0;i<selects.length;i++){ 
        var value2 = selects[i].value; 
        if (selects[i]!=select){ 
                if (value1==value2){
                alert('Nama mekanik sudah dipilih');
                select.selectedIndex=0;
                select.focus();
                break;
                }
        }
    }
}

function cariNoGudang(title,ev)
{
                 // kosongkan();
                  //setSloc('simpan');
                  content= "<div>";
                  content+="<fieldset>No Transaksi Gudang:<input type=text id=noGudang class=myinputtext onkeypress=\"return tanpa_kutip(event);\" maxlength=25><button class=mybutton onclick=goCariGudang()>Go</button> </fieldset>";
                  content+="<div id=containercari style=\"height:250px;width:470px;overflow:scroll;\"></div></div>";
             //display window
                 title=title+' PO:';
                   width='500';
                   height='300';
                   showDialog1(title,content,width,height,ev);	
}



function goCariGudang()
{

                noGudang=trim(document.getElementById('noGudang').value);
                if(noGudang.length<4)
                   alert('Text too short');
                else
                {   
				param='proses=goCariGudang'+'&noGudang='+noGudang;
               
                tujuan = 'vhc_slave_save_penggantianKomponen.php';
                post_response_text(tujuan, param, respog);			
            }
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('containercari').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}


function goPickGudang(noGudang)
{
    document.getElementById('noTranGudang').value = noGudang;
    
		closeDialog();
}



function add_new_data()
{
        bersih_form();
        status_inputan=0;
        stat_inputc=0;
        document.getElementById('trans_no').value='';
        document.getElementById('tgl_ganti').value='';
        document.getElementById('list_ganti').style.display='none';
        document.getElementById('headher').style.display='block';
        document.getElementById('detail_ganti').style.display='none';
        document.getElementById('trans_no').disabled=true;
        document.getElementById('vhc_code').innerHTML='';
        document.getElementById('ppDetailTable').innerHTML='';
        document.getElementById('detail_kode').value='';
        ShowtmblHeder();
}
function ShowtmblHeder()
{
        document.getElementById('tmblHeader').innerHTML="<button class=mybutton id='dtl_pem' onclick='detailGanti()'>"+tmblSave+"</button><button class=mybutton id='cancel_gti' onclick='cancelGanti()'>"+tmblCancel+"</button>";
}
function getNotrans(notran,kdJenis)
{
        if((notran!=0)&&(kdJenis!=0))
        {
                kdOrg=document.getElementById('codeOrg').value;
                kdjenis=kdJenis;
                notrans=notran;
                param='proses=generate_no'+'&codeOrg='+kdOrg+'&kdjenis='+kdjenis+'&notrans='+notrans;
        }
        else
        {
                kdOrg=document.getElementById('codeOrg').value;
                param='proses=generate_no'+'&codeOrg='+kdOrg;
        }
        tujuan='vhc_slave_save_penggantianKomponen.php';
        post_response_text(tujuan, param, respon);
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                                ar=con.responseText.split("###");
                                                document.getElementById('trans_no').value = ar[1];
                                                document.getElementById('vhc_code').innerHTML=ar[0];
                     }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }


}
ard=0;
function cek_code_vhc(tgl)
{
        ar=document.getElementById('vhc_code').value;
        tgl_entry=tgl;
        dwnTime=document.getElementById('dwnTime').value;
        descDmg=document.getElementById('descDmg').value;
        codeOrg=document.getElementById('codeOrg').value;
		
		
		
		//indra
        param='kdjenis='+ar+'&tglGanti='+tgl_entry+'&proses=cek_entry_jenis_vhc'+'&dwnTime='+dwnTime+'&descDmg='+descDmg+'&codeOrg='+codeOrg;
        //alert(param);
        tujuan='vhc_slave_save_penggantianKomponen.php';
        if(confirm("Any additional material usage?"))
        {	
                ard=0;
                post_response_text(tujuan, param, respon);
        }
        else
        {
                alert("Successfull");
                ard=1;
                saveHeader();
                //post_response_text(tujuan, param+'&statInp='+ard, respon);
        }
         function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        if(ard==0)
                                        {
                                                kunci_form();
                                                document.getElementById('detail_ganti').style.display='block';
                                                add_detail();
                                                stat_inputb=1;
                                                document.getElementById('tmblHeader').innerHTML='';
                                        }

                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

}


function displayList()
{
        document.getElementById('list_ganti').style.display='block';
        document.getElementById('detail_ganti').style.display='none';
        document.getElementById('headher').style.display='none';
        document.getElementById('txtsearch').value='';
        document.getElementById('tgl_cari').value='';
        //document.getElementById('proses').value='insert';
        load_new_data();
}
function cancelGanti()
{
        displayList();
}

function detailGanti()
{
                ad=document.getElementById('tgl_ganti').value;
                cek_code_vhc(ad);
}
function kunci_form()
{
        //document.getElementById('trans_no').value = con.responseText;
        document.getElementById('trans_no').disabled=true;
        document.getElementById('vhc_code').disabled=true;
        document.getElementById('tgl_ganti').disabled=true;
        document.getElementById('codeOrg').disabled=true;
        document.getElementById('dwnTime').disabled=true;
        document.getElementById('descDmg').disabled=true;
		
		document.getElementById('tglMasuk').disabled=true;
		document.getElementById('jm1').disabled=true;
		document.getElementById('mn1').disabled=true;
		document.getElementById('tglSelesai').disabled=true;
		document.getElementById('jm2').disabled=true;
		document.getElementById('mn2').disabled=true;
		
	    document.getElementById('tglAmbil').disabled=true;
		document.getElementById('kmhmMasuk').disabled=true;
		document.getElementById('namaMekanik1').disabled=true;
		document.getElementById('namaMekanik2').disabled=true;
		document.getElementById('namaMekanik3').disabled=true;
		document.getElementById('namaMekanik4').disabled=true;
		document.getElementById('namaMekanik5').disabled=true;
		document.getElementById('noTranGudang').disabled=true;
		
		//document.getElementById('tmblCariNoGudang').disabled=true;
		
		
		
		
}
function bersih_form()
{
        document.getElementById('trans_no').disabled=false;
        document.getElementById('vhc_code').disabled=false;
        document.getElementById('tgl_ganti').disabled=false;
        document.getElementById('codeOrg').disabled=false;
        document.getElementById('dwnTime').disabled=false;
        document.getElementById('descDmg').disabled=false;
        document.getElementById('dwnTime').value='0';
        document.getElementById('trans_no').value='';
        document.getElementById('vhc_code').value='';
        document.getElementById('tgl_ganti').value='';
        document.getElementById('codeOrg').value='';
        document.getElementById('descDmg').value=''
		
		
		document.getElementById('tglMasuk').disabled=false;
		document.getElementById('jm1').disabled=false;
		document.getElementById('mn1').disabled=false;
		document.getElementById('tglSelesai').disabled=false;
		document.getElementById('jm2').disabled=false;
		document.getElementById('mn2').disabled=false;
	    document.getElementById('tglAmbil').disabled=false;
		document.getElementById('kmhmMasuk').disabled=false;
		document.getElementById('namaMekanik1').disabled=false;
		document.getElementById('namaMekanik2').disabled=false;
		document.getElementById('namaMekanik3').disabled=false;
		document.getElementById('namaMekanik4').disabled=false;
		document.getElementById('namaMekanik5').disabled=false;
		
		
	//	document.getElementById('tmblCariNoGudang').disabled=false;
		

		document.getElementById('tglMasuk').value=''
		document.getElementById('jm1').value='00'
		document.getElementById('mn1').value='00'
		document.getElementById('tglSelesai').value=''
		document.getElementById('jm2').value='00'
		document.getElementById('mn2').value='00'
	    document.getElementById('tglAmbil').value=''
		document.getElementById('kmhmMasuk').value=''
		document.getElementById('namaMekanik1').value=''
		document.getElementById('namaMekanik2').value=''
		document.getElementById('namaMekanik3').value=''
		document.getElementById('namaMekanik4').value=''
		document.getElementById('namaMekanik5').value=''
		document.getElementById('noTranGudang').value=''		
		
		
}
function add_detail()
{
        notran=document.getElementById('trans_no').value;
        document.getElementById('detail_kode').value=notran;
        //alert(notran);
        param='notransaksi='+notran;
        param+="&proses=createTable";
        //alert(param);
        tujuan='vhc_detail_penggantianKomponen.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('ppDetailTable').innerHTML=con.responseText;
                                        showTmblDetail();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
function showTmblDetail()
{
        document.getElementById('tmblDetail').innerHTML="<button class='mybutton' onclick='frm_aju()'>"+tmblDone+"</button><button class='mybutton' onclick='reset_data()'>"+tmblCancelDetail+"</button>";
}
function load_new_data()
{
        param='proses=load_data';
        tujuan='vhc_slave_save_penggantianKomponen.php';
        function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        document.getElementById('list_ganti').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
        post_response_text(tujuan, param, respon);
}
status_inputan=0;
function frm_aju()
{
        if(status_inputan==0)
        {
                if(confirm("are you sure?"))
                {
                        displayList();
                }
                else
                {
                        return;
                }
        }
        else if(status_inputan!=0)
        {
                notrans=document.getElementById('trans_no').value;
                tgl=document.getElementById('tgl_ganti').value;
                kdJenis=document.getElementById('vhc_code').value;
                param='tglGanti='+tgl+'&kdjenis='+kdJenis+'&proses=update_header'+'&notrans='+notrans;
                tujuan='vhc_slave_save_penggantianKomponen.php';
                function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                                        //alert(con.responseText);
                                        displayList();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
                if(confirm("Edit header?"))
                {
                        post_response_text(tujuan, param, respon);
                }
                else
                {
                        return;
                }
        }
}
stat_inputb=0;

function reset_data()
{
     if(status_inputan==0)
            {
                                no_trans = document.getElementById('detail_kode');
                                no_trans = no_trans.value;
                                param='notrans='+no_trans;
                                param+='&proses=delete';
                                //alert(param);
                                tujuan='vhc_slave_save_penggantianKomponen.php';
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
                                                                displayList();
                                                                //document.getElementById('contain').innerHTML=con.responseText;

                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                          }
                                 }
                                if(confirm("Are you sure?"))
                                {
                        post_response_text(tujuan, param, respog);
                                }
                                else
                                {
                                        return;
                                }
            }
            else
            {
                displayList();
            }

      
}
function clear_all_data()
{
        bersih_form();

        stat_inputb=0;
        stat_input=0;
        stat_inputc=0;
}
function searchBrg(title,content,ev)
{
    noGudangx = document.getElementById('noTranGudang').value;
    
    //document.getElementById('noGudang').value = noGudang;
    //content = "<div>";
    //content += "<fieldset><input type=text id=noGudang value=" + noGudangx + " class=myinputtext onkeypress=\"return tanpa_kutip(event);\" disabled maxlength=25></fieldset>";
    //content += "<div id=container></div></div>";
    //display window
    //title = title + ' PO:';
        width='500';
        height = '400';
        
        //alert(noGudang);
        
        showDialog1(title, content, width, height, ev);
        document.getElementById('no_brg').disabled = true;
        document.getElementById('no_brg').value = noGudangx;
}
function set_brg(id)
{
        txt=trim(document.getElementById('kd_brg_'+id).value);
        param='txtcari='+txt+'&proses=cari_barang';
        //alert(param);
        tujuan='vhc_slave_save_penggantianKomponen.php';
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
                                                        document.getElementById('sat_'+id).innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         }  	
}
function findBrg()
{
    txt = trim(document.getElementById('no_brg').value);
    noGudang=document.getElementById('noTranGudang').value;
        /*if(txt=='')
        {
                alert('Text is obligatory');
        }
        else if(txt.length<3)
        {
                alert('Too short');
        }
        else
        {
            param = 'txtcari=' + txt + '&proses=cari_barang' + '&noGudang=' + noGudang;
            //alert(param);
                tujuan='vhc_slave_save_penggantianKomponen.php';
                post_response_text(tujuan, param, respog);
        }*/
        param = 'txtcari=' + txt + '&proses=cari_barang' + '&noGudang=' + noGudang;
        //alert(param);
        tujuan = 'vhc_slave_save_penggantianKomponen.php';
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

function throwThisRow(no_brg,namabrg,satuan,nomor,jumlah)
{
         nomor=document.getElementById('nomor').value;
     document.getElementById('kd_brg_'+nomor).value=no_brg;
         document.getElementById('nm_brg_'+nomor).value=namabrg;
         document.getElementById('sat_' + nomor).value = satuan;
         //document.getElementById('jmlh_' + nomor).value = jumlah;
         closeDialog();
}

function addDetail(id) {

        crt=document.getElementById('proses');
//	alert(crt.value);
        var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
    var rsatuan = document.getElementById('sat_'+id);
    var rjmlhDiminta = document.getElementById('jmlh_' + id);
    var rjmlhRiil = document.getElementById('jmlhriil_' + id);
        var rket = document.getElementById('ket_'+id);
        var rdwnTime=document.getElementById('dwnTime').value;
        var rdescDmg=document.getElementById('descDmg').value;
        var kdOrg=document.getElementById('codeOrg').value;
        //var id_user = trim(document.getElementById('user_id').value);
        rtgl = trim(document.getElementById('tgl_ganti').value);
        rkd_jenis = trim(document.getElementById('vhc_code').value);
        if(stat_inputc==0)
        {
             
                if (confirm('Add detail, are you sure?'))
                {
                        cek_data();
                }
        }
        
        else
        {
        //alert('test');
                        param = "proses=detail_add";
                        param += "&notransaksi="+detKode.value;
                        param += "&kd_brg="+rkd_brg.value;
                        param += "&satuan="+rsatuan.value;
                        param += "&jmlh=" + rjmlhDiminta.value;
                        
                        param += "&ket="+rket.value;
                        param += "&tgl="+rtgl;
                        param += "&kd_jenis="+rkd_jenis;
                        param +='&codeOrg='+kdOrg+'&dwnTime='+rdwnTime+'&descDmg='+rdescDmg;
                        //param += "&user_id="+id_user;
                        tujuan='vhc_detail_penggantianKomponen.php';

                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                           //alert(con.responseText);
                                           ar=document.getElementById('kd_brg_'+id).value;
                                           document.getElementById('skd_brg_'+id).value=ar;
                                           switchEditAdd(id,'detail');
                                           addNewRow('detailBody',true);
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                
                post_response_text(tujuan, param, respon);
        }

}
/* Function editDetail(id,primField,primVal)
 * Fungsi untuk mengubah data Detail
 * I : id row (urutan row pada table Detail)
 * P : Mengubah data pada tabel Detail
 * O : Notifikasi data telah berubah
 */
function editDetail(id) {
//	alert('test');
    var detKode = document.getElementById('detail_kode');
    var rkd_brg = document.getElementById('kd_brg_'+id);
        var skd_brg = document.getElementById('skd_brg_'+id);
    var rsatuan = document.getElementById('sat_'+id);
    var rjmlhDiminta = document.getElementById('jmlh_' + id);
    //var rjmlhRiil = document.getElementById('jmlhriil_' + id);
    var rket = document.getElementById('ket_'+id);
    
    param = "proses=detail_edit";
    param += "&notransaksi="+detKode.value;
    param += "&kd_brg="+rkd_brg.value;
        param += "&dkd_brg="+skd_brg.value;
    param += "&satuan="+rsatuan.value;
    param += "&jmlhDiminta="+rjmlhDiminta.value;
    param += "&ket="+rket.value;
   //	alert(param);
    function respon(){
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                    alert('Edit Succeed');
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }

    post_response_text('vhc_detail_penggantianKomponen.php', param, respon);
    
}

/* Function deleteDelete(id)
 * Fungsi untuk menghapus data Detail
 * I : id row (urutan row pada table Detail)
 * P : Menghapus data pada tabel Detail
 * O : Menghapus baris pada tabel Detail
 */
function deleteDetail(id) {
    var detKode = document.getElementById('detail_kode');
        var rkd_brg = document.getElementById('kd_brg_'+id);


                param = "proses=detail_delete";
                param += "&notransaksi="+detKode.value;
                param += "&kd_brg="+rkd_brg.value;
                //alert(param);

                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                row = document.getElementById("detail_tr_"+id);
                                if(row) {
                                row.style.display="none";
                                } else {
                                alert("Row undetected");
                                }
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                        if(confirm('Delete, are you sure?'))
                        {
                                post_response_text('vhc_detail_penggantianKomponen.php', param, respon);	
                        }
                        else
                        {
                                return;
                        }
}
 /* Function addNewRow
 * Fungsi untuk menambah row baru ke dalam table
 * I : id dari tbody tabel
 * P : Persiapan row dalam bentuk HTML
 * O : Tambahan row pada akhir tabel (append)
 */
function addNewRow(body,onDetail) {
        //alert(body);
    var tabBody = document.getElementById(body);
    if(onDetail) {
        var detail = onDetail;

    } else {
        var detail = false;
    }

    // Search Available numRow
    var numRow = 0;
    if(!detail) {
        while(document.getElementById('tr_'+numRow)) {
            numRow++;
        }
    } else {
        while(document.getElementById('detail_tr_'+numRow)) {
            numRow++;
        }
    }

    // Add New Row
    var newRow = document.createElement("tr");
    tabBody.appendChild(newRow);
    if(!detail) {
        newRow.setAttribute("id","tr_"+numRow);
    } else {
        newRow.setAttribute("id","detail_tr_"+numRow);
    }
    newRow.setAttribute("class","rowcontent");

    if(!detail) {
        newRow.innerHTML += "<td><input id='kode_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='matauang_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='simbol_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><input id='kodeiso_"+numRow+
        "' type='text' class='myinputtext' style='width:70px' onkeypress='return tanpa_kutip(event)' value='' /></td><td><img id='add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addMain('"+numRow+"')\" src='images/plus.png'/>"+
        "&nbsp;<img id='delete_"+numRow+"' />"+
        "&nbsp;<img id='pass_"+numRow+"' />"+
        "</td>";
    } else
    {
          // Create Row
                newRow.innerHTML += "<td><input id='kd_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /><input type=hidden id=skd_brg_"+numRow+" name=skd_brg_"+numRow+" /></td><td>"+
            "<input id='nm_brg_"+numRow+"' type='text' class='myinputtext' style='width:120px' disabled='disabled' value='' /></td><td><input id='sat_"+numRow+
        "' type='text' class='myinputtext' style='width:70px'disabled='disabled' value='' /><img src=images/search.png class=dellicon title='"+jdl_ats_0+"' onclick=\"searchBrg('"+jdl_ats_1+"','"+content_0+"<input id=nomor type=hidden value="+numRow+" />',event)\";></td>"+"<td><input id='jmlh_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return angka_doang(event)' style='width:70px' value='' />"+"<td><input id='ket_"+numRow+"' type='text' class='myinputtext' style='width:130px' value='' onkeypress='return tanpa_kutip(event)' />"+"<td><img id='detail_add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+
        "&nbsp;<img id='detail_delete_"+numRow+"' />"+
        "&nbsp;<img id='detail_pass_"+numRow+"' />"+
        "</td>";
        /*newRow.innerHTML += "<td><select id='kd_brg_"+numRow+"' style='width:180px' onchange='set_brg("+numRow+")'>"+isi_barang+"</select><input type=hidden id=skd_brg_"+numRow+" name=skd_brg_"+numRow+" /></td><td>"+
            "<select id='sat_"+numRow+"'  style='width:70px'></select></td>"+"<td><input id='jmlh_"+numRow+"' type='text' class='myinputtextnumber' onkeypress='return amgka_doang(event)' style='width:70px' value='' />"+"<td><input id='ket_"+numRow+"' type='text' class='myinputtext' style='width:130px' value='' onkeypress='return tanpa_kutip(event)' />"+"<td><img id='detail_add_"+numRow+
        "' title='Tambah' class=zImgBtn onclick=\"addDetail('"+numRow+"')\" src='images/save.png'/>"+
        "&nbsp;<img id='detail_delete_"+numRow+"' />"+
        "&nbsp;<img id='detail_pass_"+numRow+"' />"+
        "</td>";*/
        }
}
/* Function switchEditAdd
 * Fungsi untuk mengganti image add menjadi edit dan keroconya
 * I : id nomor row
 * P : Image Add menjadi Edit
 * O : Image Edit
 */
function switchEditAdd(id,main) {

 if(main=='main') {
        var idField = document.getElementById('add_'+id);
        var delImg = document.getElementById('delete_'+id);
        var passImg = document.getElementById('pass_'+id);
        var kode = document.getElementById('kode_'+id);
    } else {
        //alert(id);
        var idField = document.getElementById('detail_add_'+id);
        var delImg = document.getElementById('detail_delete_'+id);
    }
    if(idField) {
        idField.removeAttribute('id');
        idField.removeAttribute('name');
        idField.removeAttribute('onclick');
        idField.removeAttribute('src');
        idField.removeAttribute('title');

        // Set Edit Image Attr
        idField.setAttribute('title','Edit');
        if(main=='main') {
            idField.setAttribute('id','edit_'+id);
            idField.setAttribute('name','edit_'+id);
            idField.setAttribute('onclick','editMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                idField.setAttribute('id','detail_edit_'+id);
                        idField.setAttribute('name','detail_edit_'+id);
            idField.setAttribute('onclick','editDetail(\''+id+'\')');
        }
        idField.setAttribute('src','images/001_45.png');

        // Set Delete Image Attr
                delImg.setAttribute('class','zImgBtn');
        delImg.setAttribute('title','Hapus');
        if(main=='main') {
                        delImg.setAttribute('name','delete_'+id);
            delImg.setAttribute('onclick','deleteMain(\''+id+'\',\'kode\',\''+kode.value+'\')');
        } else {
                        //alert(id);
                        delImg.setAttribute('name','detail_delete_'+id);
            delImg.setAttribute('onclick','deleteDetail(\''+id+'\')');
        }
        delImg.setAttribute('src','images/delete_32.png');

    } else {
        alert('DOM Definition Error');
    }
}
function saveHeader()
{
        no_trans=document.getElementById('trans_no').value;
        kd_jenis = trim(document.getElementById('vhc_code').value);
        rtgl = trim(document.getElementById('tgl_ganti').value);
        rdwnTime=document.getElementById('dwnTime').value;
        rdescDmg=document.getElementById('descDmg').value;
        kdOrg=document.getElementById('codeOrg').value;
        pros=document.getElementById('proses').value;
		
		
		tglMasuk=document.getElementById('tglMasuk').value;
		jm1=document.getElementById('jm1').value;
		mn1=document.getElementById('mn1').value;
		tglSelesai=document.getElementById('tglSelesai').value;
		jm2=document.getElementById('jm2').value;
		mn2=document.getElementById('mn2').value;
		
		tglAmbil=document.getElementById('tglAmbil').value;
		kmhmMasuk=document.getElementById('kmhmMasuk').value;
		namaMekanik1=document.getElementById('namaMekanik1').value;
		namaMekanik2=document.getElementById('namaMekanik2').value;
		namaMekanik3=document.getElementById('namaMekanik3').value;
		namaMekanik4=document.getElementById('namaMekanik4').value;
		namaMekanik5=document.getElementById('namaMekanik5').value;
		noTranGudang=document.getElementById('noTranGudang').value;
		
		
        param='notrans='+no_trans+'&tglGanti='+rtgl+'&kdjenis='+kd_jenis+'&proses='+pros+'&codeOrg='+kdOrg+'&dwnTime='+rdwnTime+'&descDmg='+rdescDmg;
		param+='&tglMasuk='+tglMasuk+'&tglSelesai='+tglSelesai+'&tglAmbil='+tglAmbil;
		param+='&jm1='+jm1+'&mn1='+mn1+'&jm2='+jm2+'&mn2='+mn2;
		param+='&kmhmMasuk='+kmhmMasuk+'&namaMekanik1='+namaMekanik1+'&namaMekanik2='+namaMekanik2+'&namaMekanik3='+namaMekanik3+'&noTranGudang='+noTranGudang;
		param+='&namaMekanik4='+namaMekanik4+'&namaMekanik5='+namaMekanik5;
//	alert(param);
//	return;
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
                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                        displayList();
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	

}
function cek_data()
{
        no_trans=document.getElementById('detail_kode').value;
        kd_jenis = trim(document.getElementById('vhc_code').value);
        rtgl = trim(document.getElementById('tgl_ganti').value);
        rdwnTime=document.getElementById('dwnTime').value;
        rdescDmg=document.getElementById('descDmg').value;
        kdOrg=document.getElementById('codeOrg').value;
		
		
		//js indra
		
		
		
		tglMasuk=document.getElementById('tglMasuk').value;
		jm1=document.getElementById('jm1').value;
		mn1=document.getElementById('mn1').value;
		tglSelesai=document.getElementById('tglSelesai').value;
		jm2=document.getElementById('jm2').value;
		mn2=document.getElementById('mn2').value;
		
		tglAmbil=document.getElementById('tglAmbil').value;
		kmhmMasuk=document.getElementById('kmhmMasuk').value;
		namaMekanik1=document.getElementById('namaMekanik1').value;
		namaMekanik2=document.getElementById('namaMekanik2').value;
		namaMekanik3=document.getElementById('namaMekanik3').value;
		namaMekanik4=document.getElementById('namaMekanik4').value;
		namaMekanik5=document.getElementById('namaMekanik5').value;
		noTranGudang=document.getElementById('noTranGudang').value;
	        //id_user = trim(document.getElementById('user_id').value);
        met=document.getElementById('proses').value='cek_data_header';
        var tbl = document.getElementById("ppDetailTable");
        var baris = tbl.rows.length;
        //alert(baris);
        //return;
        strUrl = '';
        for(i=0;i<baris;i++)
        {
                        try{
                                if(strUrl != '')
                                {
                                        strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                                          +'&satuan[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value))
                                                          +'&jmlhMinta[]='+encodeURIComponent(trim(document.getElementById('jmlh_'+i).value))
                                                          +'&ketrngn[]='+encodeURIComponent(trim(document.getElementById('ket_'+i).value));
                                }
                                else
                                {
                                        strUrl += '&kdbrg[]='+encodeURIComponent(trim(document.getElementById('kd_brg_'+i).value))
                                                          +'&satuan[]='+encodeURIComponent(trim(document.getElementById('sat_'+i).value))
                                                          +'&jmlhMinta[]='+encodeURIComponent(trim(document.getElementById('jmlh_'+i).value))
                                                          +'&ketrngn[]='+encodeURIComponent(trim(document.getElementById('ket_'+i).value));
                                }
                        }
                        catch(e){}
        }
		
		
		param='notrans='+no_trans+'&tglGanti='+rtgl+'&kdjenis='+kd_jenis+'&proses='+met+'&codeOrg='+kdOrg+'&dwnTime='+rdwnTime+'&descDmg='+rdescDmg;
		param+='&tglMasuk='+tglMasuk+'&tglSelesai='+tglSelesai+'&tglAmbil='+tglAmbil;
		param+='&jm1='+jm1+'&mn1='+mn1+'&jm2='+jm2+'&mn2='+mn2;
		param+='&kmhmMasuk='+kmhmMasuk+'&namaMekanik1='+namaMekanik1+'&namaMekanik2='+namaMekanik2+'&namaMekanik3='+namaMekanik3+'&noTranGudang='+noTranGudang;
		param+='&namaMekanik4='+namaMekanik4+'&namaMekanik5='+namaMekanik5;

		
		
        //param='notrans='+no_trans+'&tglGanti='+rtgl+'&kdjenis='+kd_jenis+'&proses='+met+'&codeOrg='+kdOrg+'&dwnTime='+rdwnTime+'&descDmg='+rdescDmg;
        param+=strUrl;
        tujuan='vhc_slave_save_penggantianKomponen.php';
        //alert(param);
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
                                                        //return;
                                                        var id=con.responseText;
                                                        id=id-1;
                                                        switchEditAdd(id,'detail');
                                                        addNewRow('detailBody',true);
                                                        stat_inputc=1;
                                                        //document.getElementById('contain').innerHTML=con.responseText;
                                                }
                                        }
                                        else {
                                                busy_off();
                                                error_catch(con.status);
                                        }
                      }	
         } 	


}
stat_input=0;
stat_inputc=0;
function edit_header()
  {
        stats=document.getElementById('method');
        if(stat_input==1)
        {

        //	alert('edit');
                rnopp = trim(document.getElementById('nopp').value);
                rtgl_pp = trim(document.getElementById('tgl_pp').value);
                rkd_bag = trim(document.getElementById('kd_bag').value);
                id_user = trim(document.getElementById('user_id').value);
                //rkd_org = trim(document.getElementById('kode_org').value);
                method=document.getElementById('method').value;
                param='rnopp='+rnopp+'&rtgl_pp='+rtgl_pp+'&rkd_bag='+rkd_bag+'&usr_id='+id_user; //+'&rkd_org='+rkd_org;
                param+='&method='+method;
                //param+=strUrl;
                tujuan='log_slave_save_log_pp.php';

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
                                                                        //alert('Saved succeed !!');
                                                                        clear_all_data();
                                                        }
                                                }
                                                else {
                                                        busy_off();
                                                        error_catch(con.status);
                                                }
                                  }	
                 } 	
                 //post_response_text(tujuan, param, respog);
                        var answer =confirm('Edit header, are you sure?');
                        if (answer){
                        post_response_text(tujuan, param, respog);
                        }
                        else{
                        clear_all_data();
                        }
        }
        else if(stat_input==0)
        {
                //alert('insert');
                if(stat_inputc==0)
                {
                        cek_data();
                }
                else
                {
                        displayList();
                }
        }
}

	function fillField(kodeorg,notrans,tanggal,kd_vhc,stat,dwntTime,Desc,tglMasuk,tglSelesai,tglAmbil,jm1,mn1,jm2,mn2,kmhmMasuk,namaMekanik1,namaMekanik2,namaMekanik3,namaMekanik4,namaMekanik5,noTranGudang){	
        if(stat>0)
        {
                alert('has been posted');
                return;
        }
        else
        {
                //bersih_form();
                document.getElementById('codeOrg').value=kodeorg;
                document.getElementById('codeOrg').disabled=true;
                document.getElementById('dwnTime').value=dwntTime;
                document.getElementById('descDmg').value=Desc;
                document.getElementById('trans_no').value=notrans;
                document.getElementById('vhc_code').value=kd_vhc;
                document.getElementById('tgl_ganti').value=tanggal;
				
				document.getElementById('tglMasuk').value=tglMasuk;
				document.getElementById('tglAmbil').value=tglAmbil
				document.getElementById('tglSelesai').value=tglSelesai
				
				
				document.getElementById('jm1').value=jm1;
				document.getElementById('mn1').value=mn1;
			
				document.getElementById('jm2').value=jm2;
				document.getElementById('mn2').value=mn2;
				
				document.getElementById('kmhmMasuk').value=kmhmMasuk;
				document.getElementById('namaMekanik1').value=namaMekanik1;
				document.getElementById('namaMekanik2').value=namaMekanik2;
				document.getElementById('namaMekanik3').value=namaMekanik3;
				document.getElementById('namaMekanik4').value=namaMekanik4;
				document.getElementById('namaMekanik5').value=namaMekanik5;
				document.getElementById('noTranGudang').value=noTranGudang;
								
                document.getElementById('list_ganti').style.display='none';
                document.getElementById('headher').style.display='block';
                document.getElementById('detail_ganti').style.display='block';
                document.getElementById('trans_no').disabled=true;
                document.getElementById('detail_kode').value=notrans;
                stat_input=1;
                status_inputan=1;
       			stat_inputb=0;
                stat_inputc=1;
                var notrans = notrans;
                param = "notransaksi="+notrans;
                param += "&proses=createTable";

                function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                document.getElementById('proses').value='update';
                                var detailDiv = document.getElementById('ppDetailTable');
                                detailDiv.innerHTML = con.responseText;
                                getNotrans(notrans,kd_vhc);
                                        document.getElementById('tmblDetail').innerHTML="<button class='mybutton' onclick='frm_aju()'>"+tmblDone+"</button>";
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                post_response_text('vhc_detail_penggantianKomponen.php', param, respon);
        }
}

function delData(notrans,kdJenis)
{
        param='notrans='+notrans+'&kdjenis='+kdJenis+'&proses=delete_all';
        tujuan='vhc_slave_save_penggantianKomponen.php';
        //alert(param);
        function respon(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        } else {
                                                // Success Response
                                                displayList();
                                        }
                                } else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }
                if(confirm("Delete, are you sure..?"))
                {
                        post_response_text(tujuan, param, respon);
                }
                else
                {
                        return;
                }

}
function cariBast(num)
{
                param='proses=load_data';
                param+='&page='+num;
                tujuan = 'vhc_slave_save_penggantianKomponen.php';
                post_response_text(tujuan, param, respog);			
                function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('list_ganti').innerHTML=con.responseText;
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
        tujuan='vhc_slave_save_penggantianKomponen.php';
        post_response_text(tujuan, param, respog);			
        function respog(){
                        if (con.readyState == 4) {
                                if (con.status == 200) {
                                        busy_off();
                                        if (!isSaveResponse(con.responseText)) {
                                                alert('ERROR TRANSACTION,\n' + con.responseText);
                                        }
                                        else {
                                                document.getElementById('list_ganti').style.display='block';
                                                document.getElementById('headher').style.display='none';
                                                document.getElementById('detail_ganti').style.display='none';
                                                document.getElementById('list_ganti').innerHTML=con.responseText;
                                        }
                                }
                                else {
                                        busy_off();
                                        error_catch(con.status);
                                }
                        }
                }	
}
function dataKePDF(notrans,ev)
{
        noTrans	= notrans;
        tujuan='vhc_DetailPenggantianKomponen_pdf.php';
        judul= noTrans;		
        param='noTrans='+noTrans;
        //alert(param);
        printFile(param,tujuan,judul,ev)		
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='700';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
}
